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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_verif_message.js"></script>
<script type="text/javascript" src="./librairie_js/ajax-messagerie.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script type="text/javascript" src="./FCKeditor/fckeditor.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_actualisepiecejointe.js"></script>
<script type="text/javascript">
window.onload = function()
{
	var oFCKeditor = new FCKeditor('resultat','99%','480','messagerie','') ;
	//oFCKeditor.Config['CustomConfigurationsPath'] = './fckeditor/myconfig.js'
	oFCKeditor.BasePath = './FCKeditor/' ;
	oFCKeditor.ReplaceTextarea() ;
}

function _(el) {
  return document.getElementById(el);
}

function uploadFile(idpiecejointe) {
        var file = _("Filedata").files[0];
        // alert(file.name+" | "+file.size+" | "+file.type);
        var formdata = new FormData();
        formdata.append("Filedata", file);
        var ajax = new XMLHttpRequest();
        ajax.upload.addEventListener("progress", progressHandler, false);
        ajax.addEventListener("load", completeHandler, false);
        ajax.addEventListener("error", errorHandler, false);
        ajax.addEventListener("abort", abortHandler, false);
        ajax.open("POST", "uploadmessagerie.php?idpiecejointe="+idpiecejointe); // http://www.developphp.com/video/JavaScript/File-Upload-Progress-Bar-Meter-Tutorial-Ajax-PHP
        //use file_upload_parser.php from above url
        ajax.send(formdata);
}

function progressHandler(event) {
        _("loaded_n_total").innerHTML = "tï¿½lï¿½chargement " + event.loaded + " bytes sur " + event.total;
        var percent = (event.loaded / event.total) * 100;
        _("progressBar").value = Math.round(percent);
        _("status").innerHTML = Math.round(percent) + "% tï¿½lï¿½chargement... attendre S.V.P";
}

function completeHandler(event) {
        _("status").innerHTML = event.target.responseText;
        _("progressBar").value = 0; //wil clear progress bar after successful upload
        _("loaded_n_total").innerHTML = "";
        updatefichier("ok");
}

function errorHandler(event) {
        _("status").innerHTML = "Tï¿½lï¿½chargement erreur";
}

function abortHandler(event) {
        _("status").innerHTML = "Tï¿½lï¿½chargement Abandonnï¿½";
}

function ajoutDestinataire() {
        var indexdest=document.getElementById('saisie_destinataire');
        var valeurdest = indexdest.options[indexdest.selectedIndex].value;
        var textdest = indexdest.options[indexdest.selectedIndex].text;
        if (valeurdest != '0') {
                document.getElementById('liste_destinataire').innerHTML+=textdest+", ";
                document.getElementById('liste_destinataireValue').value+=valeurdest+",";
        }
}


function annulDestinataire() {
        document.getElementById('liste_destinataire').innerHTML="Liste des destinataires : ";
        document.getElementById('liste_destinataireValue').value="";
        document.getElementById("saisie_destinataire").selectedIndex=0;
}

/*
function updatefichier(item) {
        if (item == "ok") {
                ajaxActualisePieceJointe('<?php print $idpiecejointe ?>','listingpiecejointe');
        }
}
*/

</script>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once('librairie_php/db_triade.php');
$cnx=cnx();
?>
<!-- // fin du texte   -->
<div align='center'>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr id='cadreCentral0'>
<td valign='top' >
<!-- // fin  -->
<?php
$data=affichage_messagerie_message($_GET["saisie_id_message"]);
// $data : tab bidim - soustab 3 champs
if ($_SESSION["navigateur"] == "IE") {
	$action="./messagerie_enr.php";
}else{
	$action="./messagerie_enr_firefox.php";
}

for($i=0;$i<count($data);$i++) {
	$qui_envoi=$data[$i][9];
	$number=$data[$i][10];
	if ((trim($data[$i][7]) == "ADM")||(trim($data[$i][7]) == "ENS")||(trim($data[$i][7]) == "MVS")||(trim($data[$i][7]) == "TUT")) {
	    $destinataire=recherche_personne($data[$i][2]);
	}else{
	    $destinataire=recherche_eleve($data[$i][2]);
	}
?>
<form name="formulaire" method=post onsubmit='return verif_message_envoi()' action='<?php print $action ?>' target='_parent'>
<BR>
<font class=T2>&nbsp;&nbsp;<?php print ucwords(LANGTE3)?> : 
<input type=text name="saisie_emetteur" value="<?php print "$_SESSION[nom] $_SESSION[prenom]" ?>" onfocus=this.blur() maxlength='40' size=25 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<br /><br />
&nbsp;&nbsp;<?php print ucwords(LANGTE5)?> : <input type=text name="saisie_objet" size=50 maxlength=50 value="<?php print trunchaine($data[$i][8],50)?>" >
<BR><BR>
&nbsp;&nbsp;<?php print LANGTE6?> : <input type=text name="saisie_destinataire" onfocus="this.blur()" size=30 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" value="<?php print $destinataire?>" >
</font>
<input type=hidden name="saisie_destinataire"  value="<?php print $data[$i][2]?>" >

<BR><BR><BR>
<?php
$messageencours.=stripslashes(stripslashes(Decrypte($data[$i][3],$number)));
?>
<?php 
if (($_SESSION["navigateur"] != "IE") || ($_SESSION["navigateur"] != "MO")) {
?>
&nbsp;<textarea name="resultat" id="editor" cols=150 rows=25>

<?php print $messageencours?></textarea><br /><br />
<?php
}else{
?>
<textarea name="resultat" id="editor"><?php print  $messageencours ?></textarea>
<?php } ?>

<?php 
$idpiecejointe=md5($_SESSION["membre"].$_SESSION["id_pers"].date("YMDHms"));
?>
<input type="hidden" name="saisie_type_personne_dest" value="<?php print $qui_envoi?>" >
<input type="hidden" name="idpiecejoint" value="<?php print $idpiecejointe ?>" >
<input type="hidden" name="brouillon" value="0" >
<input type="hidden" name="idsuppbrouillon" value="<?php print $data[$i][0] ?>" >

<div  style="position:absolute; top:700 ;left:100" >
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit2('<?php print LANGBT4?>','rien','<?php print LANGBT5 ?>');</script>
</td></tr></table>
<br><br>
</div>

</form>

<div id="fjoint" style="position:absolute; top:625 ;left:10 "  >
<form method="post" enctype="multipart/form-data" action="messagerie_envoi_fichier.php"  target="UploadTarget" >
<?php
$taille="2Mo";
$maxsize="2000000";
if (UPLOADIMG == "oui") { $taille="8Mo"; $maxsize="8000000"; }
?>
<table><tr><td>

<form id="upload_form" enctype="multipart/form-data" method="post">
<input type="file" name="Filedata" id="Filedata" onChange="uploadFile('<?php print trim($idpiecejointe) ?>')"> <a href='#'  onMouseOver="AffBulle('Fichier Taille Max : <?php print $taille ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a><br><br>
<progress id="progressBar" value="0" max="100" style="width:300px;">
</progress>
<h3 id="status"></h3>
<p id="loaded_n_total"></p>
<br>
</form>

</td></tr></table>
</div>

<iframe src="vide.html" name="UploadTarget" style="visibility:hidden" width=5 height=5 ></iframe>
<!-- // fin  -->
</td></tr></table>
<?php
}
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT> 
</BODY></HTML>
