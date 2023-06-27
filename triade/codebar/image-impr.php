<?php
// Define variable to prevent hacking
define('IN_CB',true);

// Including all required classes
require('class/index.php');
require('class/Font.php');
require('class/FColor.php');
require('class/BarCode.php');
require('class/FDrawing.php');


$texte=$_GET["text"];
$code=$_GET["code"];

// Loading Font
$font = new Font('./class/font/Arial.ttf', 6);

// Creating some Color (arguments are R, G, B)
$color_black = new FColor(0,0,0);
$color_white = new FColor(255,255,255);

/* Here is the list of the arguments:
1 - Thickness
2 - Color of bars
3 - Color of spaces
4 - Resolution
5 - Text
6 - Text Font */
if ($code == "code39") {
	include('class/code39.barcode.php');
	$code = new code39(50,$color_black,$color_white,3,$texte,$font);
}
if ($code == "EAN13-ISBN") {
	include('class/ean13.barcode.php');
	$code = new ean13(30,$color_black,$color_white,2,$texte,$font);
}

if ($code == "codabar") {
	include('class/codabar.barcode.php');
	$code = new codabar(30,$color_black,$color_white,2,$texte,$font);
}

/* Here is the list of the arguments
1 - Filename (empty : display on screen)
2 - Background color */
$drawing = new FDrawing('',$color_white);
$drawing->setBarcode($code);
$drawing->draw();

// Header that says it is an image (remove it if you save the barcode to a file)
header('Content-Type: image/png');

// Draw (or save) the image into PNG format.
$drawing->finish(IMG_FORMAT_PNG);

?>
