<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {
    header("./index.php");
    exit;
}
$md5=md5(time());
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2024
 *   copyright            : (C) 2000 E. TAESCH -
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
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<script language="JavaScript" src="./librairie_js/docopy.js"></script>
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION['nom']).' '.htmlspecialchars($_SESSION['prenom']) ?></title>
<script type="text/javascript">
function alertjs(item) { alert(item); }

function _(el) {
    return document.getElementById(el);
}

function uploadFile(name) {
    var file = _("Filedata").files[0];
    var formdata = new FormData();
    formdata.append("md5", name);
    formdata.append("Filedata", file);
    var ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    ajax.addEventListener("error", errorHandler, false);
    ajax.addEventListener("abort", abortHandler, false);
    ajax.open("POST", "IAupload.php");
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
    _("progressBar").value = 0;
    _("loaded_n_total").innerHTML = "";
    _("status").innerHTML = "Image transmise pour analyse, poser votre question à l'IA concernant l'image";
    document.getElementById('imggo').value = 'ok';
}

function errorHandler(event) {
    _("status").innerHTML = "Téléchargement erreur";
}

function abortHandler(event) {
    _("status").innerHTML = "Téléchargement Abandonné";
}
</script>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php
if (in_array($_SESSION['membre'], ["menuadmin","menuprof","menuscolaire","menupersonnel","menuparent"])) {
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="750">
<tr id='coulBar0'>
<td height="2"><b><font id='menumodule1'><?php print "TRIADE-COPILOT </b> - <i>L'intelligence artificielle à portée de main !</i>" ?></font></td>
</tr>
<tr id='cadreCentral0'>
<td valign='top'>
<?php
if (file_exists("./common/config-ia.php")) {
    include_once("common/productId.php");
    include_once("common/config-ia.php");
    $productID = PRODUCTID;
    $iakey = IAKEY;
    $iakeypers = recupkeytriade($_SESSION["membre"], $_SESSION["id_pers"], $_SESSION["idparent"]);
    $lienIA = "ajaxCopilot(document.getElementById('question').value,'$productID','$iakey','afficheretour',document.getElementById('imggo').value,document.getElementById('img').value,document.getElementById('searchweb').checked,'$iakeypers')";
} else {
    $lienIA = "alert('Votre Triade n\'est pas configuré pour utiliser l\'IA. Contacter votre administrateur Triade')";
}

include_once("./common/config6.inc.php");
$taille = "2Mo";
$maxsize = "2000000";
if ((defined('MAXUPLOAD')) && (MAXUPLOAD == "oui")) { $taille = "8Mo"; $maxsize = "8000000"; }
?>

<div class="bda-chat-wrap">

  <form id="upload_form" enctype="multipart/form-data" method="post">
    <div class="bda-upload-row">
      <span class="bda-upload-lbl">Transmettre une image à analyser :</span>
      <input type="file" name="Filedata" id="Filedata"
             onchange="uploadFile('<?php print $md5 ?>')" accept="image/jpeg"
             class="bda-file-input">
      <span class="bda-upload-hint">(Max : <?php print $taille ?>)</span>
    </div>
    <div class="bda-progress-wrap">
      <progress id="progressBar" value="0" max="100" class="bda-progress"></progress>
      <span id="loaded_n_total" class="bda-progress-text"></span>
    </div>
    <div id="status" class="bda-status"></div>
  </form>

  <div style="margin-bottom:8px;">
    <script language="JavaScript">buttonMagic2("AGENT - TRIADE-COPILOT",'agent_copilot.php','_self','','0')</script>
  </div>

  <div class="bda-question-row">
    <input type="text" name="question" id="question"
           placeholder="Poser votre question."
           size="65" maxlength="300" class="bda-question">
    <div class="bda-send-col">
      <input type="button" value="Envoyer" class="bda-send-btn"
             onClick="<?php print $lienIA ?>">
      <label class="bda-check-row">
        <input type="checkbox" name="searchweb" id="searchweb" value="1">
        Recherche Web
      </label>
    </div>
  </div>

  <div class="bda-disclaimer">
    TRIADE-COPILOT peut afficher des informations inexactes ou choquantes qui ne représentent pas l'opinion de Triade.
  </div>

  <div id="afficheretour" class="bda-result"></div>
  <div id="afficheToken"></div>

  <input type="hidden" name="imggo" id="imggo" value="ko">
  <input type="hidden" name="img" id="img" value="<?php print $md5 ?>">

</div>

</td></tr></table>
<br>

<?php } ?>

<?php
if (in_array($_SESSION['membre'], ["menuadmin","menuprof","menuscolaire","menupersonnel","menuparent"])) {
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'>
<td height="2"><b><font id='menumodule1'>Online Assistance</font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign='top'>

<div class="bda-assist-card">
  <div class="bda-assist-intro">
    <img src="image/commun/assisante.gif" alt="assistance" class="bda-assist-img">
    <span class="bda-assist-text">Disposer d'un service d'assistance en ligne.</span>
  </div>
  <div class="bda-assist-actions">
    <script language="JavaScript">buttonMagic2("TRIADE-PRESENTATION",'acces2.php?aidenew','_self','','')</script>
    <script language="JavaScript">buttonMagic2("TRIADE-CLIENT",'https://www.triade-educ.org/fr/espace-client.php','_blank','','0')</script>
    <script language="JavaScript">buttonMagic2("TRIADE-DOC",'https://www.triade-educ.org/fr/documentation.php','_blank','','0')</script>
    <script language="JavaScript">buttonMagic2("TRIADE-DISCORD",'https://www.triade-educ.org/fr/discord.php','_blank','','0')</script>
  </div>
</div>

</td></tr></table>
<?php } ?>

<?php
if (in_array($_SESSION["membre"], ["menuadmin","menuscolaire"])) {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}
?>

<!-- Brevo Conversations {literal} -->
<script>
    window.BrevoConversationsSetup = {
        colors: {
            buttonText: '#FFFFFF',
            buttonBg: '#080A66'
        }
    };
</script>
<script>
    (function(d, w, c) {
        w.BrevoConversationsID = '64baa5aa2041cf06f4299bfc';
        w[c] = w[c] || function() {
            (w[c].q = w[c].q || []).push(arguments);
        };
        var s = d.createElement('script');
        s.async = true;
        s.src = 'https://conversations-widget.brevo.com/brevo-conversations.js';
        if (d.head) d.head.appendChild(s);
    })(document, window, 'BrevoConversations');
</script>
<!-- /Brevo Conversations {/literal} -->

</BODY></HTML>
