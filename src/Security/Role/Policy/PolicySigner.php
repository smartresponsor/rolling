<?php
declare(strict_types=1);

namespace src\Security\Role\Policy;

use RuntimeException;
use src\Security\Role\Util\Base64Url;

/**
 *
 */

/**
 *
 */
final class PolicySigner
{
    /**
     * @param string $kid
     * @param string $privatePem
     * @param string $alg
     */
    public function __construct(private readonly string $kid, private readonly string $privatePem, private readonly string $alg = 'RS256')
    {
    }

    /**
     * @param string $docJson
     * @return array
     */
    public function signJson(string $docJson): array
    {
        $docJsonNorm = self::normalize($docJson);
        $hash = hash('sha256', $docJsonNorm);
        $ts = time();
        $payload = $this->alg . '|' . $this->kid . '|' . $ts . '|' . $hash;
        $sig = '';
        if (!openssl_sign($payload, $sig, $this->privatePem, OPENSSL_ALGO_SHA256)) throw new RuntimeException('policy_sign_failed');
        $doc = json_decode($docJsonNorm, true);
        if (!is_array($doc)) throw new RuntimeException('policy_doc_invalid_json');
        return ['alg' => $this->alg, 'kid' => $this->kid, 'ts' => $ts, 'hash' => 'sha256:' . $hash, 'sig' => Base64Url::enc($sig), 'doc' => $doc];
    }

    /**
     * @param string $json
     * @return string
     */
    private static function normalize(string $json): string
    {
        $v = json_decode($json, true);
        if ($v === null) return $json;
        return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }
}
