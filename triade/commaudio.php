<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script>
window.alert = function(msg) { if (msg) alertify.error(msg); };
function valideAudio() {
    var titre   = document.querySelector('[name=saisie_titre]').value.trim();
    var fichier = document.getElementById('Filedata').value;
    if (titre === '' && !fichier) {
        alertify.error('Veuillez saisir un titre et sélectionner un fichier audio.');
        return false;
    }
    if (titre === '') {
        alertify.error('Veuillez saisir un titre.');
        return false;
    }
    if (!fichier) {
        alertify.error('Veuillez sélectionner un fichier audio.');
        return false;
    }
    return true;
}
</script>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onunload="attente_close()" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
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
<?php
$fic = "./data/parametrage/audio.txt";
if (file_exists("./common/config-ia.php")) {
    include_once("common/productId.php");
    include_once("common/config-ia.php");
    $productID = PRODUCTID;
    $iakey     = IAKEY;
    $lienIA    = "apitext2speak(document.getElementById('texte').value);";
} else {
    $lienIA = "alertify.warning('Votre Triade n\\'est pas configuré pour utiliser l\\'IA. Contacter votre administrateur Triade')";
}
?>

<div class="audio-wrap">

    <!-- ── Section 1 : Upload ── -->
    <div class="audio-card">
        <div class="audio-card-header">
            <img src="./image/commun/son.gif" style="height:16px"> <?php print LANGAUDIO4 ?>
        </div>
        <div class="audio-card-body">
        <form method="post" name="formulaire" enctype="multipart/form-data" onsubmit="return valideAudio()">
            <div class="audio-field">
                <span class="audio-label"><?php print LANGMESS241 ?></span>
                <input type="text" class="audio-input" name="saisie_titre" maxlength="28">
            </div>
            <div class="audio-field">
                <span class="audio-label"><?php print LANGMESS242 ?></span>
                <div style="flex:1">
                    <input type="file" name="Filedata" id="Filedata" onchange="uploadFile()" style="font-size:12px">
                    <a href="#" onclick="document.getElementById('help-upload').style.display=(document.getElementById('help-upload').style.display=='none'?'block':'none'); return false;">
                        <img src="./image/help.gif" border="0" style="width:15px;height:15px;vertical-align:middle;margin-top:4px">
                    </a>
                    <div id="help-upload" style="display:none; margin-top:6px; padding:10px 14px; background:#fffbe6; border:1px solid #f5c842; border-radius:6px; font-size:12px; color:#5a4000; line-height:1.6;">
                        Taille maximale autorisée : <strong><?php print preg_replace('/000000/', 'M', $taille)."o" ?></strong>.<br>
                        Sélectionnez un fichier MP3 — l'envoi démarre automatiquement.
                    </div>
                    <div class="audio-progress-wrap">
                        <progress id="progressBar" value="0" max="100"></progress>
                        <div class="audio-status" id="status"></div>
                        <div class="audio-status" id="loaded_n_total"></div>
                    </div>
                </div>
            </div>
            <div class="audio-submit-row">
                <script language="JavaScript">buttonMagicSubmit3("<?php print LANGAUDIO4 ?>","create","onclick='attente();'");</script>
            </div>
        </form>
        </div>
    </div>

    <!-- ── Section 2 : Annonce en cours ── -->
    <?php if (file_exists($fic)) {
        $fichier = fopen($fic, "r");
        $donnee  = fread($fichier, 90000);
        $tab     = explode("#||#", $donnee);
        fclose($fichier);
    ?>
    <div class="audio-card">
        <div class="audio-card-header">
            <img src="./image/commun/son.gif" style="height:16px"> <?php print LANGAUDIO1 ?>
        </div>
        <div class="audio-card-body">
            <div class="audio-player-wrap">
                <div class="audio-info-badge">
                    <img src="./image/commun/son.gif" style="height:16px;vertical-align:middle">
                    <?php print LANGAUDIO1 ?>
                </div>
                <audio src="./data/audio/actu.mp3" controls style="flex:1;min-width:200px"></audio>
            </div>
            <a href="#" onclick="document.getElementById('help-audio').style.display=(document.getElementById('help-audio').style.display=='none'?'block':'none'); return false;" style="font-size:11px;color:#080A66;text-decoration:none;">
                <img src="./image/help.gif" border="0" style="width:15px;height:15px;vertical-align:middle"> Informations
            </a>
            <div id="help-audio" style="display:none; margin-top:6px; padding:10px 14px; background:#fffbe6; border:1px solid #f5c842; border-radius:6px; font-size:12px; color:#5a4000; line-height:1.6;">
                <?php print $tab[0]; ?>
            </div>
            <form method="post">
                <div class="audio-submit-row">
                    <span style="font-size:13px;font-weight:600;color:#444"><?php print LANGAUDIO6 ?> :</span>
                    <button type="submit" name="supp" class="audio-delete-btn"><?php print LANGBT50 ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php } ?>

    <!-- ── Section 3 : Enregistrement oral ── -->
    <div class="audio-card">
        <div class="audio-card-header">
            🎙 Enregistrer votre annonce oralement
        </div>
        <div class="audio-card-body">
            <div class="audio-hint">Minimum 15 secondes — Maximum 45 secondes</div>
            <select id="encodingTypeSelect" style="display:none">
                <option value="mp3">MP3 (MPEG-1 Audio Layer III) (.mp3)</option>
            </select>
            <div class="audio-record-btns">
                <button id="recordButton">Enregistrer</button>
                <button id="stopButton" disabled>Arrêter</button>
            </div>
            <div id="formats" style="display:none"></div>
            <pre id="log" style="display:none"></pre>
            <ol id="recordingsList" class="audio-recordings"></ol>
            <p style="font-size:12px;color:#555;margin-top:8px">
                Sauvegardez votre message MP3 puis ajoutez-le à l'annonce ci-dessus.
            </p>
        </div>
    </div>

    <!-- ── Section 4 : Enregistrement écrit (IA) ── -->
    <div class="audio-card">
        <div class="audio-card-header">
            ✍ Enregistrer votre annonce par écrit
        </div>
        <div class="audio-card-body">
            <textarea id="texte" class="audio-textarea" rows="6" placeholder="Saisissez votre texte ici..."></textarea>
            <div class="audio-submit-row">
                <script language="JavaScript">buttonMagicSubmit4("Enregistrer le message","btmp3","<?php print $lienIA ?>");</script>
            </div>
            <div id="retourmp3" style="margin-top:8px;font-size:12px;text-align:center"></div>
        </div>
    </div>

</div><!-- /.audio-wrap -->

<!-- inserting these scripts at the end to be able to use all the elements in the DOM -->
<script src="js/WebAudioRecorder.min.js"></script>
<script src="js/app.js"></script>
<script>
function apitext2speak(message) {
    ajaxAudioMessageFichier(message,'<?php print $productID ?>','<?php print $iakey ?>');
}
</script>


<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
</body>
</html>
