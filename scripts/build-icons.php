<?php

// Raster exports of the repo-native favicon-v2.svg geometry, using PHP GD.
$root = dirname(__DIR__).'/public';
$scale = 16;
$canvas = imagecreatetruecolor(1024, 1024);
imagealphablending($canvas, false);
imagesavealpha($canvas, true);
$clear = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
$orange = imagecolorallocate($canvas, 241, 90, 36);
$white = imagecolorallocate($canvas, 255, 255, 255);
imagefill($canvas, 0, 0, $clear);
$radius = 14 * $scale;
imagefilledrectangle($canvas, $radius, 0, 1023 - $radius, 1023, $orange);
imagefilledrectangle($canvas, 0, $radius, 1023, 1023 - $radius, $orange);
foreach ([[$radius, $radius], [1023 - $radius, $radius], [$radius, 1023 - $radius], [1023 - $radius, 1023 - $radius]] as [$x, $y]) {
    imagefilledellipse($canvas, $x, $y, $radius * 2, $radius * 2, $orange);
}
$ring = [];
foreach ([[20, range(45, 315)], [14, range(315, 45)]] as [$radius, $angles]) {
    foreach ($angles as $angle) {
        $ring[] = (int) round((27 + $radius * cos(deg2rad($angle))) * $scale);
        $ring[] = (int) round((32 + $radius * sin(deg2rad($angle))) * $scale);
    }
}
imagefilledpolygon($canvas, $ring, $white);
imagesetthickness($canvas, 6 * $scale);
foreach ([[39, 44], [39, 20]] as [$x, $y]) {
    imagefilledellipse($canvas, $x * $scale, $y * $scale, 6 * $scale, 6 * $scale, $white);
}
foreach ([[34, 14, 50, 14], [50, 14, 50, 30], [49, 15, 31, 33]] as [$x1, $y1, $x2, $y2]) {
    imageline($canvas, $x1 * $scale, $y1 * $scale, $x2 * $scale, $y2 * $scale, $white);
    foreach ([[$x1, $y1], [$x2, $y2]] as [$x, $y]) {
        imagefilledellipse($canvas, $x * $scale, $y * $scale, 6 * $scale, 6 * $scale, $white);
    }
}
$pngs = [];
foreach ([16, 32, 48, 180, 192, 512] as $size) {
    $image = imagecreatetruecolor($size, $size);
    imagealphablending($image, false);
    imagesavealpha($image, true);
    imagecopyresampled($image, $canvas, 0, 0, 0, 0, $size, $size, 1024, 1024);
    ob_start();
    imagepng($image, null, 9);
    $pngs[$size] = ob_get_clean();
}
file_put_contents($root.'/assets/favicon-32.png', $pngs[32]);
file_put_contents($root.'/apple-touch-icon.png', $pngs[180]);
file_put_contents($root.'/assets/icon-192.png', $pngs[192]);
file_put_contents($root.'/assets/icon-512.png', $pngs[512]);
$entries = '';
$payload = '';
$offset = 6 + 16 * 3;
foreach ([16, 32, 48] as $size) {
    $entries .= pack('CCCCvvVV', $size, $size, 0, 0, 1, 32, strlen($pngs[$size]), $offset);
    $payload .= $pngs[$size];
    $offset += strlen($pngs[$size]);
}
file_put_contents($root.'/favicon.ico', pack('vvv', 0, 1, 3).$entries.$payload);
echo "Built ICO, PNG, and Apple touch icons.\n";
