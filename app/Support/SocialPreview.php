<?php

namespace App\Support;

class SocialPreview
{
    public static function image(?string $image): array
    {
        $fallback = '/assets/social/compyvila-v3.jpg';
        if (! $image || ! preg_match('~^/(?:uploads|assets/articles)/[a-zA-Z0-9_.-]+$~D', $image)) {
            return self::metadata($fallback);
        }
        $source = public_path(ltrim($image, '/'));
        if (! is_file($source)) {
            return self::metadata($fallback);
        }
        $fingerprint = substr(hash_file('sha256', $source), 0, 20);
        $bundled = '/assets/social/article-'.$fingerprint.'.jpg';
        if (is_file(public_path(ltrim($bundled, '/')))) {
            return self::metadata($bundled);
        }
        $preview = '/uploads/social/article-'.$fingerprint.'.jpg';
        $target = public_path(ltrim($preview, '/'));
        if (! is_file($target)) {
            $dimensions = @getimagesize($source);
            if (! function_exists('imagecreatefromstring') || ! $dimensions
                || ! in_array($dimensions[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_WEBP], true)
                || $dimensions[0] * $dimensions[1] > 16000000) {
                return self::metadata($fallback);
            }
            $original = @imagecreatefromstring(file_get_contents($source));
            if (! $original) {
                return self::metadata($fallback);
            }
            $canvas = imagecreatetruecolor(1200, 630);
            imagefill($canvas, 0, 0, imagecolorallocate($canvas, 248, 247, 242));
            $scale = min(1200 / $dimensions[0], 630 / $dimensions[1]);
            $width = (int) round($dimensions[0] * $scale);
            $height = (int) round($dimensions[1] * $scale);
            imagecopyresampled($canvas, $original, (int) ((1200 - $width) / 2), (int) ((630 - $height) / 2), 0, 0, $width, $height, $dimensions[0], $dimensions[1]);
            // Atomic replacement prevents crawlers reading a partially written image.
            if (! is_dir(dirname($target)) && ! @mkdir(dirname($target), 0755, true) && ! is_dir(dirname($target))) {
                return self::metadata($fallback);
            }
            $temporary = tempnam(dirname($target), '.preview-');
            if ($temporary === false) {
                return self::metadata($fallback);
            }
            try {
                for ($quality = 82; $quality >= 42; $quality -= 10) {
                    imagejpeg($canvas, $temporary, $quality);
                    clearstatcache(true, $temporary);
                    if (filesize($temporary) < 280000) break;
                }
                chmod($temporary, 0644);
                rename($temporary, $target);
            } finally {
                if (is_file($temporary)) unlink($temporary);
            }
        }

        return self::metadata($preview);
    }

    private static function metadata(string $path): array
    {
        return ['url'=>rtrim(config('app.url'), '/').$path, 'type'=>'image/jpeg', 'width'=>1200, 'height'=>630];
    }
}
