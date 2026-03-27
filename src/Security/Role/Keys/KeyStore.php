<?php

declare(strict_types=1);

namespace src\Security\Role\Keys;

use RuntimeException;

/**
 *
 */

/**
 *
 */
final class KeyStore
{
    /**
     * @param string $dir
     */
    public function __construct(private readonly string $dir = __DIR__ . '/../../../../var/keys')
    {
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0775, true);
        }
        $this->ensureInitial();
    }

    /**
     * @param string $bin
     * @return string
     */
    private static function b64u(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

    /**
     * @return void
     */
    private function ensureInitial(): void
    {
        $active = $this->path('active.pem');
        $next = $this->path('next.pem');
        if (!file_exists($active)) {
            $this->generate('active');
        }
        if (!file_exists($next)) {
            $this->generate('next');
        }
        $this->writeJwks();
    }

    /**
     * @param string $name
     * @return string
     */
    private function path(string $name): string
    {
        return rtrim($this->dir, '/') . '/' . $name;
    }

    /**
     * @param string $slot
     * @return string
     */
    private function kidPath(string $slot): string
    {
        return $this->path($slot . '.kid');
    }

    /**
     * @param string $slot
     * @return string
     */
    private function pubPath(string $slot): string
    {
        return $this->path($slot . '.pub');
    }

    /** @return array{private:string, public:string, kid:string} */
    public function getSlot(string $slot): array
    {
        return [
            'private' => (string) @file_get_contents($this->path($slot . '.pem')),
            'public' => (string) @file_get_contents($this->pubPath($slot)),
            'kid' => trim((string) @file_get_contents($this->kidPath($slot))),
        ];
    }

    /** @return array{n:string,e:string,kty:string,alg:string,use:string,kid:string} */
    private function pemToJwkPublic(string $pem, string $kid): array
    {
        $res = openssl_pkey_get_public($pem);
        if ($res === false) {
            return [];
        }
        $det = openssl_pkey_get_details($res);
        $n = $det['rsa']['n'] ?? null;
        $e = $det['rsa']['e'] ?? null;
        return [
            'kty' => 'RSA',
            'alg' => 'RS256',
            'use' => 'sig',
            'kid' => $kid,
            'n' => self::b64u($n),
            'e' => self::b64u($e),
        ];
    }

    /**
     * @param string $slot
     * @return void
     */
    private function generate(string $slot): void
    {
        $cfg = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        $res = openssl_pkey_new($cfg);
        if ($res === false) {
            throw new RuntimeException('openssl_pkey_new failed');
        }
        openssl_pkey_export($res, $priv);
        $det = openssl_pkey_get_details($res);
        $pub = $det['key'];
        try {
            $kid = substr(hash('sha256', $pub . random_bytes(8)), 0, 16);
        } catch (\Exception $e) {
        }
        file_put_contents($this->path($slot . '.pem'), $priv);
        file_put_contents($this->pubPath($slot), $pub);
        file_put_contents($this->kidPath($slot), $kid);
    }

    /**
     * @return void
     */
    public function writeJwks(): void
    {
        $a = $this->getSlot('active');
        $n = $this->getSlot('next');
        $jwks = ['keys' => []];
        $jwks['keys'][] = $this->pemToJwkPublic($a['public'], $a['kid']);
        $jwks['keys'][] = $this->pemToJwkPublic($n['public'], $n['kid']);
        file_put_contents($this->path('jwks.json'), json_encode($jwks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * @return array
     */
    public function rotate(): array
    {
        // promote next -> active; new next
        $aPriv = $this->path('active.pem');
        $aPub = $this->pubPath('active');
        $aKid = $this->kidPath('active');
        $nPriv = $this->path('next.pem');
        $nPub = $this->pubPath('next');
        $nKid = $this->kidPath('next');

        // replace active with next
        copy($nPriv, $aPriv);
        copy($nPub, $aPub);
        copy($nKid, $aKid);

        // new next
        $this->generate('next');
        $this->writeJwks();
        $a = $this->getSlot('active');
        $n = $this->getSlot('next');
        return ['active_kid' => $a['kid'], 'next_kid' => $n['kid']];
    }

    /**
     * @return array[]
     */
    public function jwks(): array
    {
        $raw = (string) @file_get_contents($this->path('jwks.json'));
        $data = json_decode($raw, true);
        return is_array($data) ? $data : ['keys' => []];
    }
}
