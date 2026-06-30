<?php

function makeIcon(int $size, string $path, bool $maskable = false): void
{
    $im = imagecreatetruecolor($size, $size);
    imagesavealpha($im, true);

    $bg = imagecolorallocate($im, 13, 110, 253); // Bootstrap primary blue
    imagefill($im, 0, 0, $bg);

    // For maskable icons, keep content inside the safe zone (center ~80%).
    $pad = $maskable ? (int) ($size * 0.1) : 0;
    $inner = $size - ($pad * 2);

    $white = imagecolorallocate($im, 255, 255, 255);

    // Simple "S" wordmark-ish shape: draw bold letters "SH" centered.
    $fontSize = (int) ($inner * 0.42);
    $text = 'SH';

    $font = __DIR__.'/../public/fonts/dejavu-sans-bold.ttf';
    if (file_exists($font)) {
        $bbox = imagettfbbox($fontSize, 0, $font, $text);
        $textWidth = abs($bbox[4] - $bbox[0]);
        $textHeight = abs($bbox[5] - $bbox[1]);
        $x = (int) (($size - $textWidth) / 2);
        $y = (int) (($size + $textHeight) / 2);
        imagettftext($im, $fontSize, 0, $x, $y, $white, $font, $text);
    } else {
        // Fallback: built-in GD font, scaled via simple block rendering.
        $gdFont = 5;
        $textWidth = imagefontwidth($gdFont) * strlen($text);
        $textHeight = imagefontheight($gdFont);
        $scale = max(1, (int) ($inner / 100));
        $tmp = imagecreatetruecolor($textWidth, $textHeight);
        imagesavealpha($tmp, true);
        $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
        imagefill($tmp, 0, 0, $transparent);
        $tmpWhite = imagecolorallocate($tmp, 255, 255, 255);
        imagestring($tmp, $gdFont, 0, 0, $text, $tmpWhite);
        imagecopyresampled(
            $im, $tmp,
            (int) (($size - $textWidth * $scale) / 2), (int) (($size - $textHeight * $scale) / 2),
            0, 0,
            $textWidth * $scale, $textHeight * $scale,
            $textWidth, $textHeight
        );
        imagedestroy($tmp);
    }

    imagepng($im, $path);
    imagedestroy($im);
}

$dir = __DIR__.'/../public/images/icons';
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}

makeIcon(192, $dir.'/icon-192.png');
makeIcon(512, $dir.'/icon-512.png');
makeIcon(192, $dir.'/icon-maskable-192.png', true);
makeIcon(512, $dir.'/icon-maskable-512.png', true);

echo "Icons generated in {$dir}\n";
