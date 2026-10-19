<?php
// Native typographic social card; requires GD and a local bold TrueType font.
$font=$argv[1]??'/System/Library/Fonts/Supplemental/Arial Bold.ttf';
if(!is_file($font))throw new RuntimeException('Supply a bold TrueType font.');
$root=dirname(__DIR__);$im=imagecreatetruecolor(1200,630);imageantialias($im,true);
$paper=imagecolorallocate($im,248,247,242);$ink=imagecolorallocate($im,32,35,31);$green=imagecolorallocate($im,219,234,200);$orange=imagecolorallocate($im,241,90,36);$purple=imagecolorallocate($im,117,83,183);
imagefill($im,0,0,$paper);imagefilledrectangle($im,790,0,1199,629,$green);
$logo=imagecreatefrompng($root.'/public/assets/compyvila-logo-v3.png');imagecopyresampled($im,$logo,64,44,0,0,250,45,imagesx($logo),imagesy($logo));
imagettftext($im,15,0,64,156,$orange,$font,'THE COMPYVILA FUNDING TRACKER');
imagettftext($im,58,0,60,270,$ink,$font,'Crypto');imagettftext($im,58,0,60,355,$ink,$font,'Funding.');
imagettftext($im,20,0,64,428,$ink,$font,'Startup rounds. Investors. Original sources.');
imagettftext($im,16,0,64,494,$purple,$font,'2025-2026 ANNOUNCEMENTS');
imagettftext($im,16,0,64,574,$ink,$font,'compyvila.com/funding');
foreach([[844,328,80,'SEED'],[949,260,148,'SERIES A'],[1054,177,231,'GROWTH']]as[$x,$y,$height,$label]){imagefilledrectangle($im,$x,$y,$x+68,408,$purple);imagettftext($im,10,0,$x,448,$ink,$font,$label);}
imagettftext($im,13,0,835,540,$ink,$font,'FOLLOW THE CAPITAL.');imagettftext($im,13,0,835,566,$ink,$font,'UNDERSTAND THE CONTEXT.');
$path=$root.'/public/assets/social/funding-v1.jpg';imagejpeg($im,$path,90);if(filesize($path)>100000)imagejpeg($im,$path,82);echo filesize($path)." bytes\n";
