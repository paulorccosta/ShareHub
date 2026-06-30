<?php

function hex(int $r, int $g, int $b): array
{
    return [$r, $g, $b];
}

function lerp(array $a, array $b, float $t): array
{
    return [
        (int) ($a[0] + ($b[0] - $a[0]) * $t),
        (int) ($a[1] + ($b[1] - $a[1]) * $t),
        (int) ($a[2] + ($b[2] - $a[2]) * $t),
    ];
}

function roundedRect($im, int $x0, int $y0, int $x1, int $y1, int $r, int $color): void
{
    imagefilledrectangle($im, $x0 + $r, $y0, $x1 - $r, $y1, $color);
    imagefilledrectangle($im, $x0, $y0 + $r, $x1, $y1 - $r, $color);
    imagefilledellipse($im, $x0 + $r, $y0 + $r, $r * 2, $r * 2, $color);
    imagefilledellipse($im, $x1 - $r, $y0 + $r, $r * 2, $r * 2, $color);
    imagefilledellipse($im, $x0 + $r, $y1 - $r, $r * 2, $r * 2, $color);
    imagefilledellipse($im, $x1 - $r, $y1 - $r, $r * 2, $r * 2, $color);
}

function drawIcon(int $size, string $path, bool $maskable = false): void
{
    $S = 1024; // render at high res, then downscale
    $im = imagecreatetruecolor($S, $S);
    imagesavealpha($im, true);
    imagealphablending($im, true);

    // Background: diagonal gradient blue -> indigo, rounded square (or full bleed for maskable).
    $top = hex(43, 108, 247);    // #2b6cf7
    $bottom = hex(99, 60, 219);  // #633cdb

    for ($y = 0; $y < $S; $y++) {
        $t = $y / $S;
        [$r, $g, $b] = lerp($top, $bottom, $t);
        $color = imagecolorallocate($im, $r, $g, $b);
        imageline($im, 0, $y, $S, $y, $color);
    }

    if (! $maskable) {
        // Mask corners to rounded square by painting transparent corners.
        $mask = imagecreatetruecolor($S, $S);
        imagesavealpha($mask, true);
        $transparent = imagecolorallocatealpha($mask, 0, 0, 0, 127);
        imagefill($mask, 0, 0, $transparent);
        $white = imagecolorallocate($mask, 255, 255, 255);
        roundedRect($mask, 0, 0, $S - 1, $S - 1, (int) ($S * 0.22), $white);

        for ($x = 0; $x < $S; $x += 2) {
            for ($y = 0; $y < $S; $y += 2) {
                $alpha = imagecolorat($mask, $x, $y);
                $a = ($alpha >> 24) & 0x7F;
                if ($a === 127) {
                    imagesetpixel($im, $x, $y, imagecolorallocatealpha($im, 0, 0, 0, 127));
                    imagesetpixel($im, $x + 1, $y, imagecolorallocatealpha($im, 0, 0, 0, 127));
                    imagesetpixel($im, $x, $y + 1, imagecolorallocatealpha($im, 0, 0, 0, 127));
                    imagesetpixel($im, $x + 1, $y + 1, imagecolorallocatealpha($im, 0, 0, 0, 127));
                }
            }
        }
        imagedestroy($mask);
    }

    // Safe zone center for maskable icons (~66% of canvas).
    $cx = $S / 2;
    $cy = $S / 2;
    $scale = $maskable ? 0.62 : 0.78;
    $u = ($S * $scale) / 240; // unit scale, design drawn on a 240x240 grid

    $white = imagecolorallocatealpha($im, 255, 255, 255, 0);
    $whiteSoft = imagecolorallocatealpha($im, 255, 255, 255, 35);

    // Three overlapping "people" nodes forming a share/group cluster.
    $nodes = [
        ['dx' => -52, 'dy' => 18, 'r' => 40],
        ['dx' => 30, 'dy' => -42, 'r' => 34],
        ['dx' => 46, 'dy' => 40, 'r' => 30],
    ];

    // Connecting lines first (behind nodes), thick white with soft alpha.
    imagesetthickness($im, (int) (6 * $u));
    for ($i = 0; $i < count($nodes); $i++) {
        for ($j = $i + 1; $j < count($nodes); $j++) {
            $x1 = $cx + $nodes[$i]['dx'] * $u;
            $y1 = $cy + $nodes[$i]['dy'] * $u;
            $x2 = $cx + $nodes[$j]['dx'] * $u;
            $y2 = $cy + $nodes[$j]['dy'] * $u;
            imageline($im, (int) $x1, (int) $y1, (int) $x2, (int) $y2, $whiteSoft);
        }
    }

    // Draw nodes as filled circles with a subtle outline.
    foreach ($nodes as $n) {
        $x = $cx + $n['dx'] * $u;
        $y = $cy + $n['dy'] * $u;
        $r = $n['r'] * $u;
        imagefilledellipse($im, (int) $x, (int) $y, (int) ($r * 2), (int) ($r * 2), $white);
    }

    // Coin badge with "$" on the largest node to signal money/expenses.
    $coinX = $cx + $nodes[0]['dx'] * $u;
    $coinY = $cy + $nodes[0]['dy'] * $u;
    $coinR = $nodes[0]['r'] * $u * 0.62;
    $coinColor = imagecolorallocate($im, 43, 108, 247);
    imagefilledellipse($im, (int) $coinX, (int) $coinY, (int) ($coinR * 2), (int) ($coinR * 2), $coinColor);

    $font = __DIR__.'/../public/fonts/dejavu-sans-bold.ttf';
    if (file_exists($font)) {
        $fontSize = (int) ($coinR * 1.15);
        $bbox = imagettfbbox($fontSize, 0, $font, '$');
        $tw = abs($bbox[4] - $bbox[0]);
        $th = abs($bbox[5] - $bbox[1]);
        imagettftext(
            $im, $fontSize, 0,
            (int) ($coinX - $tw / 2),
            (int) ($coinY + $th / 2),
            $white, $font, '$'
        );
    } else {
        // Fallback: render "$" with the built-in GD font at native size, then
        // upscale via resampling so it stays crisp at the target icon size.
        $gdFont = 5;
        $charW = imagefontwidth($gdFont);
        $charH = imagefontheight($gdFont);
        $tmp = imagecreatetruecolor($charW, $charH);
        imagesavealpha($tmp, true);
        $tmpTransparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
        imagefill($tmp, 0, 0, $tmpTransparent);
        $tmpWhite = imagecolorallocate($tmp, 255, 255, 255);
        imagestring($tmp, $gdFont, 0, 0, '$', $tmpWhite);

        $destH = (int) ($coinR * 1.3);
        $destW = (int) ($destH * $charW / $charH);
        imagealphablending($im, true);
        imagecopyresampled(
            $im, $tmp,
            (int) ($coinX - $destW / 2), (int) ($coinY - $destH / 2),
            0, 0,
            $destW, $destH,
            $charW, $charH
        );
        imagedestroy($tmp);
    }

    // Downscale to target size with smooth resampling.
    $out = imagecreatetruecolor($size, $size);
    imagesavealpha($out, true);
    imagealphablending($out, false);
    $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
    imagefill($out, 0, 0, $transparent);
    imagealphablending($out, true);
    imagecopyresampled($out, $im, 0, 0, 0, 0, $size, $size, $S, $S);

    imagepng($out, $path);
    imagedestroy($im);
    imagedestroy($out);
}

$dir = __DIR__.'/../public/images/icons';
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}

drawIcon(192, $dir.'/icon-192.png');
drawIcon(512, $dir.'/icon-512.png');
drawIcon(192, $dir.'/icon-maskable-192.png', true);
drawIcon(512, $dir.'/icon-maskable-512.png', true);

echo "Icons generated in {$dir}\n";
