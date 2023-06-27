<?php
session_start();
$idpiecejointe=md5($_SESSION["membre"].$_SESSION["id_pers"].date("YMDHms").rand(0,9999));
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_actualisepiecejointe.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script type="text/javascript" src="./ckeditor/ckeditor.js"></script>
<script>
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
        _("loaded_n_total").innerHTML = "téléchargement " + event.loaded + " bytes sur " + event.total;
        var percent = (event.loaded / event.total) * 100;
        _("progressBar").value = Math.round(percent);
        _("status").innerHTML = Math.round(percent) + "% téléchargement... attendre S.V.P";
}

function completeHandler(event) {
        _("status").innerHTML = event.target.responseText;
        _("progressBar").value = 0; //wil clear progress bar after successful upload
        _("loaded_n_total").innerHTML = "";
        updatefichier("ok");
}

function errorHandler(event) {
        _("status").innerHTML = "Téléchargement erreur";
}

function abortHandler(event) {
        _("status").innerHTML = "Téléchargement Abandonné";
}



function updatefichier(item) { 
	if (item == "ok") {
		ajaxActualisePieceJointe('<?php print $idpiecejointe ?>','listingpiecejointe');
	}
}
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
<td valign='top' align='left'>
<!-- // fin  -->
<?php
$data=affichage_messagerie_message($_GET["saisie_id_message"]);
// $data : tab bidim - soustab 3 champs
if ( ($_SESSION["navigateur"] == "IE") || ($_GET["et"] == "1"))  {
	if ($_COOKIE["messmodelecture"] == "classic") {
		$action="./messagerie_enr_firefox.php";
	}else{
		$action="./messagerie_enr.php";
	}
}else{
	$action="./messagerie_enr_firefox.php";
}
for($i=0;$i<count($data);$i++) {
	$emetteur=$data[$i][1];
	$qui_envoi=$data[$i][7];
	$number=$data[$i][10];
	if ((trim($data[$i][7]) == "ADM")||(trim($data[$i][7]) == "ENS")||(trim($data[$i][7]) == "MVS")||(trim($data[$i][7]) == "TUT")) {
	    $destinataire=recherche_personne($data[$i][1]);
	}else{
	    $destinataire=recherche_eleve($data[$i][1]);
	}
?>
<form name="formulaire" method=post onsubmit='return verif_message_envoi()' action='<?php print $action ?>' target='_parent'><br />
<font class=T2>&nbsp;&nbsp;<?php print ucwords(LANGTE3)?> : <input type=text name="saisie_emetteur" value="<?php print "$_SESSION[nom] $_SESSION[prenom]" ?>" onfocus=this.blur() maxlength='40' size=25 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<br /><br />
&nbsp;&nbsp;<?php print ucwords(LANGTE5)?> : <input type=text name="saisie_objet" size=50 maxlength=50 value="<?php print trunchaine("RE:".stripslashes($data[$i][8]),50)?>" >
<BR><BR>
&nbsp;&nbsp;<?php print LANGTE6?> : <input type=text name="saisie_destinataire" onfocus="this.blur()" size=30 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" value="<?php print $destinataire?>" >
</font>
<input type=hidden name="saisie_destinataire_value"  value="<?php print $data[$i][1]?>" >

<BR><BR><BR>
<?php
$messageencours="<br><br><hr>";
$messageencours.="> <i>".LANGMESS31.": $destinataire</i><br>";
$messageencours.="> <i>".LANGTE12." ".dateForm($data[$i][4])." ".LANGTE13." ".$data[$i][5]."</i>";
$message=Decrypte($data[$i][3],$number);
$message=stripslashes($message);
$message=preg_replace('/\r\n/','',$message);
$message=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$message);
$messageencours.=stripslashes($message);
?>


<textarea id="editor" name="resultat" ><?php print $messageencours ?></textarea>
<script type="text/javascript">
var colorGRAPH='<?php print GRAPH ?>';
//<![CDATA[
CKEDITOR.replace( 'editor', {
	height: '300px' , language:'<?php print ($_SESSION["langue"] == "fr") ? "fr" : "en";  ?>'
	} );
//]]>

</script>


<input type="hidden" name=saisie_type_personne_dest value="<?php print $qui_envoi?>" >
<input type="hidden" name="idpiecejoint" value="<?php print $idpiecejointe ?>" >

<br><br>
<div  style="position:absolute; top:780 ;left:100 "  >
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit2('<?php print LANGBT4?>','rien','<?php print LANGBT5 ?>'); //text,nomInput</script>
</td></tr></table>
<br><br>
</div>

</form>
<br><br>
<div id="fjoint" style="position:absolute; top:625 ;left:10 "  >
<form method="post" >
<?php
$taille="2Mo";
$maxsize="2000000";
if (UPLOADIMG == "oui") { $taille="8Mo"; $maxsize="8000000"; }
?>
<table><tr><td valign=top><br/>
<!--
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
</object></td> <td valign='top'><br> <a href='#'  onMouseOver="AffBulle('Fichier Taille Max : <?php print $taille ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
-->

<input type="file" name="Filedata" id="Filedata" onChange="uploadFile('<?php print trim($idpiecejointe) ?>')"> <a href='#'  onMouseOver="AffBulle('Fichier Taille Max : <?php print $taille ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a><br><br>
<progress id="progressBar" value="0" max="100" style="width:300px;">
</progress>
<h3 id="status"></h3>
<p id="loaded_n_total"></p>
<br>
</td></tr></table>
</div>
<br>
<br /><br /><br /><br />
<div id="listingpiecejointe" style=width:100%;height:50;overflow:auto ></div>
<br />
<br />
<br><br>
<!-- // fin  -->
</td></tr></table>
<?php
}
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT> 
</BODY></HTML>
