<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if (empty($_SESSION["nom"]))  { exit; }

// Trombino pour les élèves
if (isset($_GET['idE'])) {
	include_once("./common/config.inc.php");
	include_once("./common/config2.inc.php");
	include_once("./librairie_php/db_triade.php");
	$cnx=cnx();
	$idEleve=$_GET["idE"];
	if(!is_numeric($idEleve)) exit;
	$photoLocal=recherche_photo_eleve($idEleve);
	Pgclose();

	

	// Définition de la largeur et de la hauteur maximale
	$width='120';
	$height='120';


	$fic="./data/image_eleve/$photoLocal";
	if ((file_exists($fic)) && (trim($photoLocal) !=  "")) {
		$filename = stripslashes(basename($fic));
		switch(strrchr(basename($filename), ".")) {
			case ".png": $type = "image/png"; break;
			case ".gif": $type = "image/gif"; break;
			case ".jpg": $type = "image/jpeg"; break;
			case ".jpeg": $type = "image/jpeg"; break;
		}
		$source=$fic;
		if( !( list($width_orig, $height_orig) = @getimagesize($source))) {
    			return false;
  		}
		if ($width_orig > 100) {
			$ratio_orig = $width_orig/$height_orig;
			if ($width/$height > $ratio_orig) {
		   		$width = $height*$ratio_orig;
			} else {
				$height = $width/$ratio_orig;
			}
			$image  = imagecreatetruecolor($width, $height);
			$source_image = imagecreatefromstring(file_get_contents($source));
			imagecopyresampled($image, $source_image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);	
			$sortie=$fic;
			if (strlen($sortie) > 0 and @touch($sortie)) {
				switch(strrchr(basename($filename), ".")) {
                        		case ".jpg": 
	                        	case ".jpeg": 
						imagejpeg($image,$sortie);
		 				break;
		                        case ".gif": 
						imagegif($image,$sortie);
						break;
					case ".png": 
						imagepng($image,$sortie);
						break;
				}
			}
			imagedestroy($image);
  			imagedestroy($source_image);
		}
		header("Content-type: $type");
		readfile($fic);
		exit;
	}else{
		header("Content-type: image/jpeg");
		readfile("./image/commun/photo_vide.jpg");
		exit;
	}
}


if (isset($_GET['idP'])) {
	$idPers=$_GET["idP"];
	include_once("./common/config.inc.php");
	include_once("./common/config2.inc.php");
	include_once("./librairie_php/db_triade.php");
	$cnx=cnx();
	$photoLocal=recherche_photo_pers($idPers);
	Pgclose();
	// Définition de la largeur et de la hauteur maximale
	$width='120';
	$height='120';
	$fic="./data/image_pers/$photoLocal";
	if ((file_exists($fic)) && (trim($photoLocal) !=  "")) {
		$filename = stripslashes(basename($fic));
		switch(strrchr(basename($filename), ".")) {
			case ".png": $type = "image/png"; break;
			case ".gif": $type = "image/gif"; break;
			case ".jpg": $type = "image/jpeg"; break;
			case ".jpeg": $type = "image/jpeg"; break;
		}
		$source=$fic;
		if( !(list($width_orig,$height_orig)=@getimagesize($source))) {
    			return false;
  		}
		if ($width_orig > 100) {
			$ratio_orig = $width_orig/$height_orig;
			if ($width/$height > $ratio_orig) {
		   		$width = $height*$ratio_orig;
			} else {
				$height = $width/$ratio_orig;
			}
			$image=imagecreatetruecolor($width, $height);
			$source_image=imagecreatefromstring(file_get_contents($source));
			imagecopyresampled($image, $source_image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);	
			$sortie=$fic;
			if (strlen($sortie) > 0 and @touch($sortie)) {
				switch(strrchr(basename($filename), ".")) {
                        		case ".jpg": 
	                        	case ".jpeg": 
						imagejpeg($image,$sortie);
		 				break;
		                        case ".gif": 
						imagegif($image,$sortie);
						break;
					case ".png": 
						imagepng($image,$sortie);
						break;
				}
			}
			imagedestroy($image);
  			imagedestroy($source_image);
		}
		header("Content-type: $type");
		readfile($fic);
		exit;
	}else{
		header("Content-type: image/jpeg");
		readfile("./image/commun/photo_vide.jpg");
		exit;
	}
}



if (isset($_GET['edt'])) {
	$idclasse=$_GET['idclasse'];
	$fic="./data/image_pers/${idclasse}_edt.jpg";
	if (file_exists($fic)) {
		$filename = stripslashes(basename($fic));
	}else{
		$fic="./data/image_pers/${idclasse}_edt.png";
		$filename = stripslashes(basename($fic));
	}
	switch(strrchr(basename($filename), ".")) {
		case ".jpg": $type = "image/jpeg"; break;
		case ".jpeg": $type = "image/jpeg"; break;
	}
	header("Content-type: $type");
	readfile($fic);
	exit;
}


?>
