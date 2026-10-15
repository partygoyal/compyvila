<?php
// Code-drawn editorial graphics. Supply a locally installed bold TrueType font.
$font = $argv[1] ?? '';
if (! is_file($font)) throw new RuntimeException('Provide a bold TrueType font path.');
$root = dirname(__DIR__);
$articles = json_decode(file_get_contents($root.'/content/news-2026-10-15.json'), true, flags: JSON_THROW_ON_ERROR);
$themes = [[246,91,45],[111,77,239],[32,166,131],[63,111,236],[191,63,174]];
foreach ($articles as $i => $article) {
    $im = imagecreatetruecolor(1200,630);
    imageantialias($im,true);
    $ink=imagecolorallocate($im,25,31,49);
    $paper=imagecolorallocate($im,248,247,242);
    $accent=imagecolorallocate($im,...$themes[$i]);
    $bright=imagecolorallocate($im,232,255,137);
    imagefill($im,0,0,$paper);
    imagefilledrectangle($im,790,0,1199,629,$accent);
    imagefilledellipse($im,1170,25,480,480,$bright);
    imagefilledellipse($im,1200,610,470,470,$ink);
    imagettftext($im,28,0,60,78,$ink,$font,'compyvila.');
    imagettftext($im,15,0,60,142,$accent,$font,$article['cover_kicker']);
    foreach ($article['cover_lines'] as $j=>$line) {
        imagettftext($im,43,0,60,292+$j*75,$ink,$font,$line);
    }
    imagefilledrectangle($im,60,441,126,447,$accent);
    imagettftext($im,15,0,60,508,$ink,$font,'THE DAILY EDITION / 15 OCT 2026');
    imagettftext($im,13,0,60,562,$ink,$font,'INDEPENDENT THINKING. CLEARER CRYPTO.');
    // Repeated geometric units express networks, finance, and collateral without news photographs.
    imagesetthickness($im,8);
    foreach ([[846,201],[1070,210],[838,421],[1069,430]] as [$x,$y]) {
        imageline($im,$x,$y,955,310,$paper);
        imagefilledellipse($im,$x,$y,45,45,$bright);
        imagefilledellipse($im,$x,$y,17,17,$ink);
    }
    imagefilledellipse($im,955,310,260,260,$paper);
    imagefilledellipse($im,955,310,220,220,$ink);
    $labels=['BTC','BTC','ETH','EU','BTC'];
    $label=$labels[$i];$box=imagettfbbox(39,0,$font,$label);
    imagettftext($im,39,0,(int)(955-($box[2]-$box[0])/2),328,$paper,$font,$label);
    $path=$root.'/public'.$article['image'];
    imagejpeg($im,$path,90);
    // Social previews use the same compact 1200x630 export, with the content fingerprint expected by Laravel.
    $hash=substr(hash_file('sha256',$path),0,20);
    copy($path,$root.'/public/assets/social/article-'.$hash.'.jpg');
    echo $article['slug'].': '.filesize($path)." bytes\n";
}
