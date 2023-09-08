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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./framaplayer/framaplayer.js"></script>
<script>
function _(el) {
  return document.getElementById(el);
}

function uploadFile() {
        var file = _("Filedata").files[0];
        // alert(file.name+" | "+file.size+" | "+file.type);
        var formdata = new FormData();
        formdata.append("Filedata", file);
        var ajax = new XMLHttpRequest();
        ajax.upload.addEventListener("progress", progressHandler, false);
        ajax.addEventListener("load", completeHandler, false);
        ajax.addEventListener("error", errorHandler, false);
        ajax.addEventListener("abort", abortHandler, false);
        ajax.open("POST", "uploadaudio.php"); // http://www.developphp.com/video/JavaScript/File-Upload-Progress-Bar-Meter-Tutorial-Ajax-PHP
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
        _("status").innerHTML = "Fichier Transmis";
}

function errorHandler(event) {
        _("status").innerHTML = "Téléchargement erreur";
}

function abortHandler(event) {
        _("status").innerHTML = "Téléchargement Abandonné";
}

</script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title> </head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onunload="attente_close()" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "News audio" ?></font></b></td>
<?php
include_once("./librairie_php/db_triade.php");
validerequete("2");

$taille=2000000;
$taille2="2Mo";

include_once("librairie_php/lib_get_init.php");
include_once("common/config6.inc.php");

if (MAXUPLOAD == "oui") {
	$id=php_ini_get("safe_mode");
	if ($id != 1) {
		//ini_set('memory_limit', 8000000); // en octets
		set_time_limit(3000); // en secondes
		$taille=8000000;
		$taille2="8Mo";
	}
}

if (isset($_POST["create"])) {
	/*
	$fichier=$_FILES['fichier']['name'];
	$titre=$_POST["saisie_titre"];
	$type=$_FILES['fichier']['type'];
	$tmp_name=$_FILES['fichier']['tmp_name'];
	$size=$_FILES['fichier']['size'];
	if ( (!empty($fichier)) &&  ($size <= $taille) && (($type=="audio/mpeg") || ($type=="audio/x-mpeg")) ) {
		// supprimer l'ancien
		$fichier="actu.mp3";
        	$f=fopen("./data/parametrage/audio.txt","r");
		$donnee=fread($f,900000);
	    	$tab=explode("#||#",$donnee);
	    	fclose($f);
		@unlink("./data/parametrage/audio.txt");
		@unlink("./data/audio/actu.mp3");
		// nouveau
		move_uploaded_file($tmp_name,"./data/audio/actu.mp3");
	*/
        	$today=dateDMY();
	   	$titre=strip_tags($_POST["saisie_titre"]);
        	$f=fopen("./data/parametrage/audio.txt","w");
        	fwrite($f,"<font size=1>".LANGAUDIO2."$today,</font> <br><font class=T1>$titre</font>#||#$fichier");
        	fclose($f);
	   	$cnx = cnx();
		$audiook="oui";

	   	history_cmd($_SESSION["nom"],"COMMUNIQUER","Audio");
}


if (isset($_POST["supp"])) {
    	$f=fopen("./data/parametrage/audio.txt","r");
	$donnee=fread($f,90000);
    	$tab=explode("#||#",$donnee);
    	fclose($f);
	@unlink("./data/parametrage/audio.txt");
	@unlink("./data/audio/actu.mp3");
}
?>
<tr id='cadreCentral0'>
<td >
<br />
<form method="post"   name=formulaire ENCTYPE="multipart/form-data">
<table  width=100%  border="0" align="center" >
<tr>
<td align="right"><font class="T2"><?php print LANGMESS241 ?></font></TD>
<TD align="left"><input type="text" name="saisie_titre" size=30 maxlength=28 ></td>
</tr>
<tr>
<td align="right"  valign=top ><font class="T2"><?php print LANGMESS242 ?></font></TD>
<TD  align="left" >
<input type="file" name="Filedata" id="Filedata" onChange="uploadFile()"> <a href='#'  onMouseOver="AffBulle('Fichier Taille Max : <?php print preg_replace('/000000/','M',$taille)."o" ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a><br><br>
<progress id="progressBar" value="0" max="100" style="width:300px;">
</progress>
<h3 id="status"></h3>
<p id="loaded_n_total"></p>
<br>
</td>
</tr>
</table>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGAUDIO4 ?>","create","onclick='attente();'"); //text,nomInput</script>
<A href='#' onMouseOver="AffBulle3('Information','./image/commun/info.jpg','<font face=Verdana size=1><B><font color=red><?php print LANGAUDIO3?></font></B><?php print LANGAUDIO3bis." <b>$taille2</b> . </font>" ?> '); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A> <br /><br />
</td></tr></table>
</form>
<?php
$fic="./data/parametrage/audio.txt";
if (file_exists($fic)) {
	$fichier=fopen("./data/parametrage/audio.txt","r");
	$donnee=fread($fichier,90000);
	$tab=explode("#||#",$donnee);
	fclose($fichier);
?>
<!-- <input type="button" value="Stop" id="btnPlayStop" onclick="Playa.doPlayStop();" /> -->
<center><a href='#'  onMouseOver="AffBulle3('Information','./image/commun/info.jpg','<?php print $tab[0]; ?>');"  onMouseOut="HideBulle()";><img src="./image/commun/son.gif" border=0 align=center></a> : <font class=T1 color=#000000><b><?php print LANGAUDIO1 ?></b></font>
<br><br>
<audio src="./data/audio/actu.mp3" controls ></audio>
<br><br>
<form method=post>
<font class=T1><?php print LANGAUDIO6 ?> :</font> <input name="supp" type=submit value="<?php print LANGBT50?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</center>
</form>
<?php
}
?>

<br><br><hr><br><br>
<center>
<?php
echo "<font class='T2 shadow'><b>Enregistrer votre annonce !!!</b></font>";
echo "<br/>";
echo "<br/>";
echo "<center>";
echo "<br><b><font color='red'>Mimimun 15 secondes - Maximum 45 secondes</b></font>";
?>
<br><br>

<div style="max-width: 28em;">
                <select id="encodingTypeSelect" style='display:none' >
                  <option value="mp3">MP3 (MPEG-1 Audio Layer III) (.mp3)</option>
                </select>
                <div id="controls">
                        <button id="recordButton" >Enregistrer</button>
                        <button id="stopButton" disabled >Arreter</button>
                </div>
                <div id="formats" style='display:none'  ></div>
                <pre style='display:none'  >Log</pre>
                <pre id="log" style='display:none'  ></pre>

                <ol id="recordingsList"></ol>
</div>

<!-- inserting these scripts at the end to be able to use all the elements in the DOM -->
<script src="js/WebAudioRecorder.min.js" ></script>
<script src="js/app.js" ></script>

<br>
<font color='blue'>&nbsp;Sauvegarder votre message MP3, et ajouter le fichier &agrave; l'annonce.</font></center>
<br><br>



</td>
</tr></table>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
</body>
</html>
