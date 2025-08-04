<?php
session_start();
$random = rand(1, 9) . rand(1, 9) . rand(1, 9);

// Set the value in the session
$_SESSION['nomreh_captcha_value'] = $random;

$captcha = imagecreatefromjpeg("captcha-bg.jpg");
$color = imagecolorallocate($captcha, 248, 92, 81);
$font = __DIR__ . '/code.otf';
$fontSize = 22;
$angle = 1;

// Get the text dimensions
$bbox = imagettfbbox($fontSize, $angle, $font, $random);
$textWidth = abs($bbox[4] - $bbox[0]);
$textHeight = abs($bbox[1] - $bbox[5]);

// Calculate safe boundaries for random positioning
$maxX = 200 - $textWidth - 10; // Image width minus text width and padding
$minX = 10; // Minimum padding from left
$x = rand($minX, max($minX, $maxX));

$maxY = 40 - ($textHeight/4); // Image height minus some padding
$minY = $textHeight + 5; // Minimum padding from top
$y = rand($minY, max($minY, $maxY));

imagettftext($captcha, $fontSize, $angle, $x, $y, $color, $font, $random);
header('Content-Type: image/png');
imagepng($captcha);
imagedestroy($captcha);
?>