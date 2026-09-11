<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Consistency;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 3).'/tools/qa/source-revision.php';

final class SourceRevisionResolverTest extends TestCase
{
    /** @var list<string> */
    private array $temporaryDirectories = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryDirectories as $directory) {
            $this->removeDirectory($directory);
        }

        $this->temporaryDirectories = [];
    }

    public function testResolvesCiCommitBeforeRepositoryMetadata(): void
    {
        $root = $this->newRepositoryRoot();
        $sha = str_repeat('a', 40);

        self::assertSame($sha, \rollingResolveSourceRevision($root, ['GITHUB_SHA' => strtoupper($sha)]));
    }

    public function testResolvesDetachedHead(): void
    {
        $root = $this->newRepositoryRoot();
        $sha = str_repeat('b', 40);
        file_put_contents($root.'/.git/HEAD', $sha.PHP_EOL);

        self::assertSame($sha, \rollingResolveSourceRevision($root, []));
    }

    public function testResolvesGitfileWorktree(): void
    {
        $root = $this->newRepositoryRoot(false);
        $sha = str_repeat('e', 40);
        mkdir($root.'/.git-worktree', 0777, true);
        file_put_contents($root.'/.git', "gitdir: .git-worktree\n");
        file_put_contents($root.'/.git-worktree/HEAD', $sha.PHP_EOL);

        self::assertSame($sha, \rollingResolveSourceRevision($root, []));
    }

    public function testResolvesSymbolicLooseRef(): void
    {
        $root = $this->newRepositoryRoot();
        $sha = str_repeat('c', 40);
        mkdir($root.'/.git/refs/heads', 0777, true);
        file_put_contents($root.'/.git/HEAD', "ref: refs/heads/master\n");
        file_put_contents($root.'/.git/refs/heads/master', $sha.PHP_EOL);

        self::assertSame($sha, \rollingResolveSourceRevision($root, []));
    }

    public function testResolvesSymbolicPackedRef(): void
    {
        $root = $this->newRepositoryRoot();
        $sha = str_repeat('d', 40);
        file_put_contents($root.'/.git/HEAD', "ref: refs/heads/master\n");
        file_put_contents($root.'/.git/packed-refs', "# pack-refs with: peeled fully-peeled\n{$sha} refs/heads/master\n");

        self::assertSame($sha, \rollingResolveSourceRevision($root, []));
    }

    public function testReturnsNullWhenRevisionCannotBeResolved(): void
    {
        $root = $this->newRepositoryRoot(false);

        self::assertNull(\rollingResolveSourceRevision($root, []));
    }

    private function newRepositoryRoot(bool $withGitDirectory = true): string
    {
        $root = sys_get_temp_dir().'/rolling-source-revision-'.bin2hex(random_bytes(8));
        mkdir($root, 0777, true);
        if ($withGitDirectory) {
            mkdir($root.'/.git', 0777, true);
        }

        $this->temporaryDirectories[] = $root;

        return $root;
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (new \FilesystemIterator($directory) as $item) {
            if ($item->isDir() && !$item->isLink()) {
                $this->removeDirectory($item->getPathname());
                continue;
            }

            unlink($item->getPathname());
        }

        rmdir($directory);
    }
}
