<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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
function connexpersistance($style) {
	// $style="color:red;font-weight:bold;font-size:11px;text-align: center;";
	print "<script type='text/javascript' src='./librairie_js/ajax-time.js'></script>";
	print "<span name='info-time' id='info-time' style='$style' ></span>";
	print "<script type='text/javascript' >";
	print "function boucle(){";
	print "		ConnexPersistant();";
	print "	setTimeout('boucle()',300000);";  // toutes les 5 minutes
	print "}";
	print "boucle();";
	print "</script>";
}
?>
