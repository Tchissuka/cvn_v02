<?php
// Gera CSS/JS minificado apenas em ambiente de desenvolvimento (host local/rede interna)
$host = strtolower($_SERVER['HTTP_HOST'] ?? '');
$isLocalHost = strpos($host, 'localhost') !== false
    || strpos($host, '127.0.0.1') !== false
    || strpos($host, '192.168.') === 0
    || str_ends_with($host, '.local');

if (!function_exists('collectAssetFiles')) {
    function collectAssetFiles(string $directory, string $extension): array
    {
        $files = [];
        foreach (scandir($directory) as $file) {
            $path = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_file($path) && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === $extension) {
                $files[] = $path;
            }
        }

        sort($files);
        return $files;
    }
}

if (!function_exists('assetNeedsRebuild')) {
    function assetNeedsRebuild(array $sources, string $target): bool
    {
        if (!is_file($target) || !filesize($target)) {
            return true;
        }

        $targetMTime = filemtime($target) ?: 0;
        foreach ($sources as $source) {
            if (is_file($source) && (filemtime($source) ?: 0) > $targetMTime) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('writeMinifiedAsset')) {
    function writeMinifiedAsset(callable $builder, string $target): void
    {
        $targetDir = dirname($target);
        $temporary = tempnam($targetDir, 'min_');

        if ($temporary === false) {
            return;
        }

        try {
            $builder($temporary);

            if (!is_file($temporary) || !filesize($temporary)) {
                @unlink($temporary);
                return;
            }

            if (is_file($target)) {
                @unlink($target);
            }

            @rename($temporary, $target);
        } finally {
            if (is_file($temporary)) {
                @unlink($temporary);
            }
        }
    }
}

if ($isLocalHost) {
    $sharedCss = __DIR__ . "/../../shared/styles/all.min.css";
    $themeCssFiles = collectAssetFiles(__DIR__ . "/../../themes/assets/css", 'css');
    $cssSources = array_merge([$sharedCss], $themeCssFiles);
    $cssTarget = __DIR__ . "/../../themes/assets/style.css";

    /**
     * CSS geral
     */
    if (assetNeedsRebuild($cssSources, $cssTarget)) {
        writeMinifiedAsset(function (string $temporaryTarget) use ($cssSources): void {
            $minCSS = new MatthiasMullie\Minify\CSS();
            foreach ($cssSources as $cssFile) {
                $minCSS->add($cssFile);
            }

            $minCSS->minify($temporaryTarget);
        }, $cssTarget);
    }

    $sharedJs = __DIR__ . "/../../shared/scripts/jquery.min.js";
    $themeJsFiles = collectAssetFiles(__DIR__ . "/../../themes/assets/js", 'js');
    $jsSources = array_merge([$sharedJs], $themeJsFiles);
    $jsTarget = __DIR__ . "/../../themes/assets/scripts.js";

    /**
     * JS
     */
    if (assetNeedsRebuild($jsSources, $jsTarget)) {
        writeMinifiedAsset(function (string $temporaryTarget) use ($jsSources): void {
            $minJS = new MatthiasMullie\Minify\JS();
            foreach ($jsSources as $jsFile) {
                $minJS->add($jsFile);
            }

            $minJS->minify($temporaryTarget);
        }, $jsTarget);
    }
}
