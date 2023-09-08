<?php
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
include_once("./common/choixlangue.php");  // fichier sur la langue a utiliser
include_once('./librairie_pdf/php.arabe/Arabic.php');
// --------------------------------------
// debut pour le franais
// --------------------------------------
if ( LANGUE == "french" ) {
	include_once("langue-text-fr.php");
}
// --------------------------------------
// debut pour l'arabe
// --------------------------------------
if ( LANGUE == "arabe" ) {
	include_once("langue-text-arabe.php");
}
// --------------------------------------
// debut pour l'italien
// --------------------------------------
if ( LANGUE == "italien" ) {
	include_once("langue-text-it.php");
}
// --------------------------------------
// debut pour l'anglais
// --------------------------------------
if ( LANGUE == "anglais" ) {
	include_once("langue-text-en.php");
}
// --------------------------------------
// debut pour l'espagnol
// --------------------------------------
if ( LANGUE == "espagnol" ) {
	include_once("langue-text-es.php");
}
// --------------------------------------
// debut pour breton
// --------------------------------------
if ( LANGUE == "breton" ) {
	include_once("langue-text-bret.php");
}
//--------------------------------------
//--------------------------------------
?>
