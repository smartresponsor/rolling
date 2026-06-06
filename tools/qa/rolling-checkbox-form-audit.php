<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$formDir = $root.'/src/Form';
$testDir = $root.'/tests/Role/Form';

$files = [];
if (is_dir($formDir)) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($formDir, FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || 'php' !== $file->getExtension()) {
            continue;
        }

        $path = $file->getPathname();
        $rel = str_replace($root.'/', '', $path);
        $content = (string) file_get_contents($path);
        $checkboxCount = preg_match_all('/CheckboxType::class/', $content);
        $requiredFalseCount = preg_match_all("/'required'\s*=>\s*false/", $content);
        $hasTransformer = str_contains($content, 'CallbackTransformer');

        if (0 < $checkboxCount) {
            $files[] = [
                'file' => $rel,
                'checkbox_fields' => $checkboxCount,
                'required_false_options' => $requiredFalseCount,
                'uses_callback_transformer' => $hasTransformer,
            ];
        }
    }
}

$tests = [];
if (is_dir($testDir)) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($testDir, FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || 'php' !== $file->getExtension()) {
            continue;
        }

        $path = $file->getPathname();
        $rel = str_replace($root.'/', '', $path);
        $content = (string) file_get_contents($path);
        if (str_contains($content, 'Checkbox') || str_contains($content, 'checkbox')) {
            $tests[] = $rel;
        }
    }
}

$checkboxFields = array_sum(array_column($files, 'checkbox_fields'));
$requiredFalse = array_sum(array_column($files, 'required_false_options'));
$missingRequiredFalse = max(0, $checkboxFields - $requiredFalse);

$out = [
    'summary' => [
        'form_files_with_checkbox' => count($files),
        'checkbox_fields' => $checkboxFields,
        'checkbox_fields_missing_required_false' => $missingRequiredFalse,
        'checkbox_semantics_tests' => count($tests),
    ],
    'forms' => $files,
    'tests' => $tests,
];

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;

exit(0 === $missingRequiredFalse && 0 < count($tests) ? 0 : 1);
