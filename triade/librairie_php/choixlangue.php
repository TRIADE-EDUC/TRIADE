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
if ($_SESSION["langue"] == "fr") {
	define("LANGUE","french");	
}elseif($_SESSION["langue"] == "en") {
	define("LANGUE","anglais");	
}elseif($_SESSION["langue"] == "es") {
	define("LANGUE","espagnol");	
}elseif($_SESSION["langue"] == "bret") {
	define("LANGUE","breton");	
}elseif($_SESSION["langue"] == "arabe") {
	define("LANGUE","arabe");	
}elseif($_SESSION["langue"] == "it") {
	define("LANGUE","italien");
}elseif($_SESSION["langue"] == "occitan") {
	define("LANGUE","occitan");
}else {
	define("LANGUE","french");
}
?>
