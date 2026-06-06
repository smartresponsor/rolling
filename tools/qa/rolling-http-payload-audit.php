<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$httpDir = $root.'/src/Service/Http';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($httpDir, FilesystemIterator::SKIP_DOTS));

$files = [];
foreach ($rii as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile() || 'php' !== $file->getExtension()) {
        continue;
    }
    $path = $file->getPathname();
    $rel = str_replace($root.'/', '', $path);
    $content = (string) file_get_contents($path);
    $manualJson = preg_match_all('/json_decode\s*\(.*?getContent\s*\(/s', $content);
    if ('src/Service/Http/Request/JsonPayloadReader.php' === $rel) {
        $manualJson = 0;
    }
    $requestFileAccess = preg_match('/->files\b|UploadedFile::class|UploadedFile\b/', $content) ? 1 : 0;
    $checkboxRisk = preg_match('/checkbox|\bbool\b|\(bool\)|false|null|not submitted/i', $content) ? 1 : 0;
    $usesPayloadReader = str_contains($content, 'JsonPayloadReader');
    $usesPayloadDto = str_contains($content, 'DTO\\Http\\Role');

    if ($manualJson || $requestFileAccess || $checkboxRisk || $usesPayloadReader || $usesPayloadDto) {
        $files[] = [
            'file' => $rel,
            'manual_get_content_json_decode' => $manualJson,
            'upload_candidate' => $requestFileAccess,
            'checkbox_null_candidate' => $checkboxRisk,
            'uses_json_payload_reader' => $usesPayloadReader,
            'uses_http_payload_dto' => $usesPayloadDto,
        ];
    }
}

$manual = array_sum(array_column($files, 'manual_get_content_json_decode'));
$uploads = array_sum(array_column($files, 'upload_candidate'));
$checkbox = array_sum(array_column($files, 'checkbox_null_candidate'));
$reader = count(array_filter($files, static fn (array $row): bool => $row['uses_json_payload_reader']));
$dto = count(array_filter($files, static fn (array $row): bool => $row['uses_http_payload_dto']));

$out = [
    'summary' => [
        'http_files_scanned' => iterator_count(new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($httpDir, FilesystemIterator::SKIP_DOTS)), '/\.php$/')),
        'manual_get_content_json_decode_count' => $manual,
        'upload_candidate_count' => $uploads,
        'checkbox_null_candidate_count' => $checkbox,
        'files_using_json_payload_reader' => $reader,
        'files_using_http_payload_dto' => $dto,
    ],
    'files' => $files,
];

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;

exit(0);
