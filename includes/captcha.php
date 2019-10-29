<?php

//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: captcha.php                                               #\\
//# Copyright: Christopher Schiffner, Some Rights Reserved              #\\
//# Description: Image gallery software, view readme for more info.     #\\
//#                                                                     #\\
//# License: This software is free to use for personal applications.    #\\
//#          There is a small registration fee for commercial           #\\
//#          applications.  Please contact chris@schiffner.com if       #\\
//#          you wish to use this program on a commercial website.      #\\
//#######################################################################\\

include_once '../conf/config.php';
@session_name($core_settings['session_identifier']);
@session_start();

$captchaCodeLength=6;

//dimensions 
$width = 125;
$height = 30;
        
// colors
$bgR = mt_rand(128, 255);
$bgG = mt_rand(128, 255);
$bgB = mt_rand(128, 255);
        
$txtR=$bgR - 128;
$txtG=$bgG - 128;
$txtB=$bgB - 128;

$gridXincrement=24;
$gridYincrement=19;
$angleX=5;
$angleY=5;

/* Random Character String Code 
   omiiting common problematic characters
*/
$characterPool = 'ABCDEFGHJKMNPQRSTUV12345689';
$captchaString = '';
for($x = 0; $x < $captchaCodeLength; $x++){
   $randomPosition = mt_rand(0, strlen($characterPool)-1);
   $captchaString .= substr($characterPool, $randomPosition, 1)." ";
}

//store captcha session variable
$_SESSION['captcha']=str_replace(" ", "", $captchaString);

// create new image with specified dimensions
$image = imagecreate($width, $height);

// generate color
$bgColor = imagecolorallocate($image, $bgR, $bgG, $bgB);
$txtColor = imagecolorallocate($image, $txtR, $txtG, $txtB);

// apply the background
imagefill($image, 0, 0, $bgColor);
 
// Generate a grid to help distort image
for($gridX = 0; $gridX <= $width; $gridX+=$gridXincrement){
    $gridXRAND=mt_rand($gridX, $gridX+3);
    imageline($image, $gridXRAND, 0, $gridXRAND + mt_rand(0, $angleX), $height, $txtColor);
}
for($gridY = 0; $gridY <= $height; $gridY+=$gridYincrement){
    $gridYRAND = mt_rand($gridY, $gridY + 6);
    imageline($image, 0, $gridYRAND, $width, $gridYRAND + mt_rand(0, $angleY), $txtColor);
}

// write the random string to the image
$x=mt_rand(8, 15);
$y=mt_rand(4, 10);
imagestring  ( $image  , 5 , $x , $y, $captchaString, $txtColor );

// put a border on the box
imagerectangle($image, 0, 0, $width - 1, $height - 1, $txtColor);

// set headers to prevent image cache, and identify as gif, output image, clean obj
header('Expires: Tue, 08 Oct 1991 00:00:00 GMT');
header('Cache-Control: no-cache, must-revalidate');
header("Content-Type: image/gif");
imagegif($image);
imagedestroy($image); 
?>
