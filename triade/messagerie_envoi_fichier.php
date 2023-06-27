<?php
session_start();
error_reporting(0);
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
/*

$fichier=$_FILES['fichierjoint']['name'];
$type=$_FILES['fichierjoint']['type'];
$tmp_name=$_FILES['fichierjoint']['tmp_name'];
$size=$_FILES['fichierjoint']['size'];
$idpiecejointe=$_POST["idpiecejoint"];

include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");

if (UPLOADIMG == "oui") {
	$taille=8000000;
}else{
	$taille=2000000;
}
$fichierMD5='';



if ( (!empty($fichier)) &&  ($size <= $taille)) {


	$cnx=cnx();

	if  ( 	(preg_match('/text/i',$type))  ||
		(peg_match('/pdf/i',$type))   ||
		(peg_match('/msword/i',$type)) ||
		(peg_match('/stuffit/i',$type)) ||
		(peg_match('/ms-excel/i',$type)) ||
		(peg_match('/zip/i',$type)) || 
		(peg_match('/opendocument/i',$type)) ||
		(peg_match('/.txt$/i',$fichier)) ||
		(peg_match('/.pdf$/i',$fichier)) ||
		(peg_match('/.doc$/i',$fichier)) ||
		(peg_match('/.xls$/i',$fichier)) ||
		(peg_match('/.zip$/i',$fichier)) || 
		(peg_match('/.ods$/i',$fichier)) ||
		(peg_match('/.odt$/i',$fichier)) ||
		(peg_match('/.odg$/i',$fichier))

       		) {

	
		//print "Nom du fichier :".$fichier." ".$type." ".$size." ".$tmp_name." ";
		$fichier=str_replace(" ","_",$fichier);
		$fichier=str_replace("'","_",$fichier);
		$fichier=str_replace("\\","",$fichier);
		$fichier=TextNoAccent($fichier);

		$nommage=$fichier.date("YmdHms");
		$fichierMD5=md5($nommage);

		if (!is_dir("./data/fichiersj")) { mkdir("./data/fichiersj"); }
		move_uploaded_file($tmp_name,"data/fichiersj/$fichierMD5");

		create_piecejointe($fichier,$fichierMD5,$idpiecejointe,'1');
	}else{
		create_piecejointe($type,$fichierMD5,$idpiecejointe,'0');				
	}
}else{
	create_piecejointe($fichier,$fichierMD5,$idpiecejointe,'2');
}
PgClose();
 */
?>
