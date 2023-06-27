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
	<a href="http://www.xiti.com/xiti.asp?s=150072" TARGET="_top"><script language="JavaScript1.1" type="text/javascript">
	<!--
	var version="<?php print VERSION?>";
	Xt_param = 's=150072&p=Admin_Triade_'+version;
	Xt_r = document.referrer;
	Xt_h = new Date();
	Xt_i = '<img width="39" height="25" border="0" id="mailing" class="blk_nav" onmouseover="resetit(this.id)" onmouseout="unfadeimg(this.id)"  style="filter:alpha(opacity=25);-moz-opacity:0.25" ';
	Xt_i += 'src="http://logv24.xiti.com/hit.xiti?'+Xt_param;
	Xt_i += '&hl='+Xt_h.getHours()+'x'+Xt_h.getMinutes()+'x'+Xt_h.getSeconds();
	if(parseFloat(navigator.appVersion)>=4) {
		Xt_s=screen;Xt_i+='&r='+Xt_s.width+'x'+Xt_s.height+'x'+Xt_s.pixelDepth+'x'+Xt_s.colorDepth;
	}
	document.write(Xt_i+'&ref='+Xt_r.replace(/[<>"]/g, '').replace(/&/g, '$')+'" title="Analyse d\'audience">');
	//-->
	</script></a></div>
<?php } ?>
<!-- audience Xiti -->
</BODY>
</HTML>
