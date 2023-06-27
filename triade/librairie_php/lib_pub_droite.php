<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.org
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

include_once("../common/config2.inc.php");
include_once("../common/productId.php");

if (LAN == "oui") {
	if (HTTPS == "oui") {
		$url="https://www.triade-educ.org/sponsor/pub-online-d.php&https=".HTTPS."&productid=".PRODUCTID;
		print "<script src='http://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
		print "<script>";
		print "if (ok2) {";
		print "document.write(\"<IFRAME NAME=pubtri SRC='$url' width=120 height=600 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";
		print "}";
		print "</script>";
	}else{
		$url="http://www.triade-educ.org/sponsor/pub-online-d.php&https=".HTTPS."&productid=".PRODUCTID;
		print "<script src='http://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
		print "<script>";
		print "if (ok2) {";
		print "document.write(\"<IFRAME NAME=pubtri SRC='$url' width=120 height=600 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";
		print "}";
		print "</script>";
	}
}else {
    print "";
}
?>
					       

