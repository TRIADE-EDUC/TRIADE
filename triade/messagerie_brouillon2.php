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
 **************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript"  src="./librairie_js/function.js"></script>
<script language="JavaScript"  src="./librairie_js/lib_css.js"></script>
<script language="JavaScript"  src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript"  src="./librairie_js/lib_verif_message.js"></script>
<script type="text/javascript" src="./FCKeditor/fckeditor.js"></script>
<script type="text/javascript" src="./librairie_js/ajax-messagerie.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_proto_mail.js"></script>
<script type="text/javascript">
window.onload = function()
{
	// Automatically calculates the editor base path based on the _samples directory.
	// This is usefull only for these samples. A real application should use something like this:
	// oFCKeditor.BasePath = '/fckeditor/' ;	// '/fckeditor/' is the default value.
	//var sBasePath = document.location.pathname.substring(0,document.location.pathname.lastIndexOf('_samples')) ;
	<?
	include_once("./common/config2.inc.php");
	if ((UPLOADIMG == "oui") && ($_SESSION["membre"] == "menuparent") ){
		$typedefen="messagerie_img_non";
	}else {
		$typedefen="messagerie";
	}
	?>

	var oFCKeditor = new FCKeditor('resultat','99%','480','<?php print $typedefen?>','') ;
	//oFCKeditor.Config['CustomConfigurationsPath'] = './fckeditor/myconfig.js'
	oFCKeditor.BasePath = './FCKeditor/' ;
	oFCKeditor.ReplaceTextarea() ;
}
</script>

</head>
<body  id='bodyfond3' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr id='cadreCentral0'>
<td valign=top >
<!-- // fin  -->
<?php
if (isset($_GET["erreur"])) {
	$erreur="<script language=javascript>alert(langfunc16)</script>";
}
?>
<form name="formulaire" method=post action='./messagerie_enr_brouillon.php' target='_parent' onsubmit='return verif_message_envoi()'>
<BR>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_GET["saisie_envoi"] == "mailexterne") {
	if(isset($_SESSION["id_suppleant"])) {
		$id_pers=$_SESSION["id_suppleant"];
	}else{
		$id_pers=$_SESSION["id_pers"];
	}
	$source=mess_mail_forward($_SESSION["nom"],$_SESSION["prenom"],$id_pers,$_SESSION["membre"]); 
}else{
	$source=$_SESSION["nom"]." ".$_SESSION["prenom"];
}
?>
	<font class=T2><?php print ucwords(LANGTE3)?> : <input type=text name="saisie_emetteur" value="<?php print stripslashes($source) ?>" onfocus=this.blur() readonly="readonly" size=40 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br /><br />
<?php print LANGTE5?> : <input type=text name="saisie_objet"  autocomplete="off"  size=50 maxlength=50 value="<?php print stripslashes(stripcslashes($_GET["saisie_objet"]))?>">
<BR> <BR></font>
<font class=T2>
<?php print ucfirst($langt6)?> : </font>
<?php 
	select_personne_messagerie($qui_envoi); // creation des options
?>
<BR><BR><BR>
<?php 
if (($_SESSION["navigateur"] != "IE") || ($_SESSION["navigateur"] != "MO")) {
?>
<textarea name="resultat" id="editor" cols=110 rows=38>
</textarea><br /><br />
<?php
}else{
?>
<textarea name="resultat" id="editor">
</textarea>
<?php } ?>
<br />
<?php 
$idpiecejointe=md5($_SESSION["membre"].$_SESSION["id_pers"].date("YMDHms").rand(0,9999));
?>
<input type="hidden" name="saisie_type_personne_dest" value="<?php print $qui_envoi?>" >
<input type="hidden" name="saisie_envoi" value="<?php print $_GET["saisie_envoi"]?>" >
<input type="hidden" name="saisie_classe" value="<?php print $_GET["saisie_classe"]?>" >
<input type="hidden" name="idpiecejoint" value="<?php print $idpiecejointe ?>" >



<div  style="position:absolute; top:720 ;left:100 "  >
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGBT3?>","acces2.php","_parent","","")</script>
<script language=JavaScript>buttonMagicSubmit2('<?php print LANGBT4?>','rien','<?php print LANGBT5?>'); //text,nomInput</script>&nbsp;&nbsp;
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</td></tr></table>
</div>
</form>


<?php 
if ($_GET["saisie_envoi"] != "mailexterne") {
?>

<div id="fjoint2" style="position:absolute; top:650 ;left:10;visibility:hidden "><font class='T2'><?php print LANGBT5 ?> </font> <img src='./image/commun/indicator.gif' align='center' /></div>

<div id="infofichierjoint" style="position:absolute; top:680 ;left:10;visibility:hidden "></div>

<div id="fjoint" style="position:absolute; top:650 ;left:10 "  ><form method="post" name="form2" enctype="multipart/form-data" action="messagerie_envoi_fichier.php"  target="UploadTarget" ><!-- <font class=T2>Fichier  : <input type="file" name="fichierjoint" /> -->
<?php
$taille="2Mo";
$maxsize="2000000";
if (UPLOADIMG == "oui") { $taille="8Mo"; $maxsize="8000000"; }
?>
<table><tr><td>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="292" height="54" id="fileUpload" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="librairie_php/fileUpload.swf" />
<param name="quality" value="high" />
<param name="wmode" value="transparent">
<param name=FlashVars value="idpiecejoint=<?php print trim($idpiecejointe) ?>&maxsize=<?php print trim($maxsize) ?>&idsession=<?php print session_id()?>">
<?php $couleur=couleurFont(GRAPH); ?>
<param name="bgcolor" value="<?php print $couleur ?>" />
<embed src="librairie_php/fileUpload.swf" quality="high" bgcolor="<?php print $couleur ?>"  wmode="transparent"
       width="292" height="54" name="fileUpload" align="middle" allowScriptAccess="sameDomain" 
       type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="idpiecejoint=<?php print trim($idpiecejointe) ?>&maxsize=<?php print trim($maxsize) ?>&idsession=<?php print session_id()?>" />
</object></td> <td valign='top'> <a href='#'  onMouseOver="AffBulle('Fichier Taille Max : <?php print $taille ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a></td></tr></table>

<!-- 
 <input type="text" name="idpiecejoint" value="<?php print $idpiecejointe ?>" />
 <input type="submit" value="Joindre le fichier" class="bouton2" onClick="chargement();PieceJointe('<?php print $idpiecejointe ?>');"  /></font> -->

</form></div>
<br />
<br />

<script type="text/javascript" >
function chargement() {
	document.getElementById('fjoint').style.visibility="hidden";
	document.getElementById('fjoint2').style.visibility="visible";
}
</script>
<!-- // fin  -->
<iframe src="vide.html" name="UploadTarget" style="visibility:hidden" width=5 height=5 ></iframe>
<?php } ?>


</td></tr></table>
<?php Pgclose(); ?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT> 
</BODY></HTML>
