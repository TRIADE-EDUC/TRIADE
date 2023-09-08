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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK REL="SHORTCUT ICON" href="./favicon.ico">
<TITLE>TRIADE</TITLE>
<SCRIPT LANGUAGE=JavaScript>
//######### click droit ###########//
function clicie() {
        // Fonction de détection pour Internet Explorer
        if (event.button==2) {
                alert("Respectez les droits d'auteur.");
        }
}
function clicns(e){
        // Fonction pour Netscape
        if(e.which==3){
                alert("Respectez les droits d'auteur.");
                return false;
        }
}
if (document.all) {        document.onmousedown=clicie;}
if (document.layers) {document.captureEvents(Event.MOUSEDOWN); document.onmousedown = clicns;}

//################################//
function ouvert() {
        location.href="index_acces.php"
}

</script>
</HEAD>
<BODY  background="image/attente.jpg" OnLoad="ouvert();">
<!-- audience Xiti -->
<?php
error_reporting(0);
include_once("./librairie_php/lib_error.php");
include_once("../common/congi2.inc.php");
if ( LAN == "oui") {
	include_once("../common/version.php");
?>
	<div align=left>
	</div>
<?php } ?>
<!-- audience Xiti -->
</BODY>
</HTML>
