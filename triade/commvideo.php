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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./framaplayer/framaplayer.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script>
window.alert = function(msg) { if (msg) alertify.error(msg); };
function valideVideo() {
    var titre      = document.querySelector('[name=saisie_titre]').value.trim();
    var lien       = document.querySelector('[name=saisie_lien]').value.trim();
    var youtube    = document.querySelector('[name=saisie_lien_youtube]').value.trim();
    var lienVide   = (lien === '' || lien === 'http://');
    var ytVide     = (youtube === '' || youtube === 'http://');
    if (titre === '' && lienVide && ytVide) {
        alertify.error('Veuillez saisir un titre et un lien vidéo ou YouTube.');
        return false;
    }
    if (titre === '') {
        alertify.error('Veuillez saisir un titre.');
        return false;
    }
    if (lienVide && ytVide) {
        alertify.error('Veuillez saisir un lien vidéo ou un lien YouTube.');
        return false;
    }
    attente();
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
$lienvideo = "http://";
?>

<form method="post" name="formulaire" action="acces2.php" onsubmit="return valideVideo()">
<div class="audio-wrap">
    <div class="audio-card">
        <div class="audio-card-header">
            🎬 <?php print "Ajouter une vidéo" ?>
        </div>
        <div class="audio-card-body">
            <div class="audio-field">
                <span class="audio-label"><?php print LANGMESST702 ?> :</span>
                <input type="text" class="audio-input" name="saisie_titre" maxlength="30">
            </div>
            <div class="audio-field">
                <span class="audio-label"><?php print LANGMESS301 ?></span>
                <div style="flex:1">
                    <input type="text" class="audio-input" name="saisie_lien"
                           value="<?php print $lienvideo ?>"
                           onclick="this.value=''; this.form.saisie_lien_youtube.value='';">
                    <div style="font-size:11px;color:#888;margin-top:3px"><i>(format mp4 ou webm)</i></div>
                </div>
            </div>
            <div class="audio-field">
                <span class="audio-label"><?php print LANGMESS302 ?></span>
                <div style="flex:1">
                    <input type="text" class="audio-input" name="saisie_lien_youtube"
                           value="<?php print $lienvideo ?>"
                           onclick="this.value=''; this.form.saisie_lien.value='';">
                    <a href="#" onclick="document.getElementById('help-yt').style.display=(document.getElementById('help-yt').style.display=='none'?'block':'none'); return false;">
                        <img src="./image/help.gif" border="0" style="width:15px;height:15px;vertical-align:middle;margin-top:4px">
                    </a>
                    <div id="help-yt" style="display:none; margin-top:6px; padding:10px 14px; background:#fffbe6; border:1px solid #f5c842; border-radius:6px; font-size:12px; color:#5a4000; line-height:1.6;">
                        <?php print LANGMESST703 ?> YouTube (http://www.youtube.com/watch?v=xxxxxx)
                    </div>
                </div>
            </div>
            <br>
            <div class="audio-submit-row">
                <script language="JavaScript">buttonMagicSubmit3("<?php print LANGENR ?>","createvideo","");</script>
            </div>
            <br>
        </div>
    </div>
</div><!-- /.audio-wrap -->
</form>
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
