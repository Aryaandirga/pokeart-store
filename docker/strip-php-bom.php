<?php

declare(strict_types=1);

/** @var non-empty-string $bom */
$bom = "\xEF\xBB\xBF";
$directories = ['app', 'bootstrap', 'config', 'database', 'routes', 'tests'];

foreach ($directories as $directory) {
    if (! is_dir($directory)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
    );

    /** @var SplFileInfo $file */
    foreach ($iterator as $file) {
        if (! $file->isFile()) {
            continue;
        }

        if (! str_ends_with(strtolower($file->getFilename()), '.php')) {
            continue;
        }

        $path = $file->getPathname();
        $contents = file_get_contents($path);

        if ($contents === false || ! str_starts_with($contents, $bom)) {
            continue;
        }

        file_put_contents($path, substr($contents, strlen($bom)));
    }
}
