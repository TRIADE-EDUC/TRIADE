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
 //
 ***************************************************************************/
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();


$data=activite_liste();
for($i=0;$i<count($data);$i++) {
//	print "<option value='".$data[$i][0]."'>".trunchaine($data[$i][0],40)."</option>";
//	print "document.formulaire.activite.options[document.formulaire.activite.options.length] = new Option('dmc','cmd');";
	print trunchaine($data[$i][0],40)."#";	
}


?>
