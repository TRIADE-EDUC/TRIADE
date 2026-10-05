<?php
session_start();
if ((empty($_SESSION["nom"])) && (empty($_SESSION["membre"]))) {
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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="javascript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<script language="JavaScript" src="./librairie_js/docopy.js"></script>
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION["nom"])." ".htmlspecialchars($_SESSION["prenom"]) ?></title>
<script type="text/javascript">
function alertjs(item) { alert(item); }
function _(el) { return document.getElementById(el); }

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
    _("loaded_n_total").innerHTML = "Téléchargement " + event.loaded + " octets sur " + event.total;
    var percent = (event.loaded / event.total) * 100;
    _("progressBar").value = Math.round(percent);
    _("status").innerHTML = Math.round(percent) + "% — veuillez patienter…";
}
function completeHandler(event) {
    _("progressBar").value = 0;
    _("loaded_n_total").innerHTML = "";
    _("status").innerHTML = "Image transmise — posez votre question à l'IA.";
    document.getElementById('imggo').value = 'ok';
}
function errorHandler(event)  { _("status").innerHTML = "Erreur de téléchargement"; }
function abortHandler(event)  { _("status").innerHTML = "Téléchargement abandonné"; }
</script>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'>
<td height="2"><b><font id='menumodule1'>TRIADE-COACH</font></b>
<span class="tco-subtitle">&mdash; <i>L'intelligence artificielle à portée de main&nbsp;!</i></span></td>
</tr>
<tr id='cadreCentral0'>
<td valign="top">

<?php
include_once("./common/config2.inc.php");
include_once("./common/config6.inc.php");

if ((MODULETRIADECOACH == "oui") || (!defined('MODULETRIADECOACH'))) {
    if ((file_exists("./common/config-ia.php")) || (file_exists("./common/config-ia-pers.php"))) {
        include_once("common/productId.php");
        include_once("common/config-ia.php");
        $productID = PRODUCTID;
        $iakey = "";
        if (defined("IAKEY")) $iakey = IAKEY;
        if (file_exists("./common/config-ia-pers.php")) {
            include_once("common/config-ia-pers.php");
            $iakeypers = recupkeytriade($_SESSION["membre"], $_SESSION["id_pers"], $_SESSION["idparent"]);
            $lienIA = "ajaxCopilotPersCoach(document.getElementById('question').value,'$productID','$iakey','$iakeypers','afficheretour',document.getElementById('coach').value,document.getElementById('imggo').value,document.getElementById('img').value,document.getElementById('searchweb').checked)";
        } else {
            $lienIA = "ajaxCopilotCoach(document.getElementById('question').value,'$productID','$iakey','afficheretour',document.getElementById('coach').value,document.getElementById('imggo').value,document.getElementById('img').value,document.getElementById('searchweb').checked)";
        }
    } else {
        $lienIA = "alert('Votre Triade n\'est pas configuré pour utiliser l\'IA. Contacter votre administrateur Triade')";
        $bt = "<a href='gescompte.php'><img src='image/commun/engrenage.png' width='40' align='center' /></a>";
    }
} else {
    $lienIA = "alert('Votre Triade n\'est pas configuré pour utiliser l\'IA. Contacter votre administrateur Triade')";
}

$infocoach = "";
$defaultmessage = "Je suis votre coach, comment puis-je vous aider ?";
if (isset($_POST["idobj"])) {
    $idobj = $_POST["idobj"];
    $data  = recupInfoCoaching($idobj);
    $objet = $data[0][3];
    $infocoach = $data[0][4];
    $defaultmessage = "Comment puis-je vous aider sur $objet ?";
}
$anneeScolaire = anneeScolaireViaIdClasse($_SESSION['idclasse']);

$taille   = "2Mo";
$maxsize  = "2000000";
if (MAXUPLOAD == "oui") { $taille = "8Mo"; $maxsize = "8000000"; }
?>

<div class="tco-wrap">

  <!-- Sélecteur de coach -->
  <div class="tco-coach-bar">
    <form method="post" name="formulaire" action="triade-coach.php">
      <label class="tco-coach-lbl">Coach :</label>
      <select onchange="this.form.submit();" name="idobj" class="tco-coach-select">
        <option></option>
        <?php filtreObjetCoachingStudent($anneeScolaire, $_POST['idobj']); ?>
      </select>
    </form>
  </div>

  <!-- Upload image -->
  <form id="upload_form" enctype="multipart/form-data" method="post" class="tco-upload-area">
    <span class="tco-upload-lbl">&#128247; Image à analyser :</span>
    <input type="file" name="Filedata" id="Filedata"
           onchange="uploadFile('<?php print $md5 ?>')"
           accept="image/jpeg" class="tco-file-input">
    <span class="tco-upload-hint">(max <?php print $taille ?>)</span>
    <div class="tco-progress-wrap">
      <progress id="progressBar" value="0" max="100" class="tco-progress"></progress>
      <span id="status" class="tco-status"></span>
      <span id="loaded_n_total" class="tco-status"></span>
    </div>
  </form>

  <!-- Zone de saisie -->
  <div class="tco-input-row">
    <input type="text"
           placeholder="<?php print htmlspecialchars($defaultmessage) ?>"
           name="question" id="question"
           maxlength="300"
           class="tco-question">
    <input type="hidden" name="coach" id="coach" value="<?php print preg_replace('/"/', '', $infocoach) ?>">
    <input type="hidden" name="imggo" id="imggo" value="ko">
    <input type="hidden" name="img"   id="img"   value="<?php print $md5 ?>">
    <button type="button" class="tco-send-btn" onclick="<?php print $lienIA ?>">&#9654; Envoyer</button>
  </div>

  <!-- Options -->
  <div class="tco-options-row">
    <label class="tco-web-label">
      <input type="checkbox" name="searchweb" id="searchweb" value="1">
      &#127760; Recherche Web
    </label>
    <?php if (!empty($bt)) { print $bt; } ?>
    <span class="tco-disclaimer">TRIADE-COACH peut afficher des informations inexactes ou choquantes qui ne représentent pas l'opinion de Triade.</span>
  </div>

  <!-- Réponse IA -->
  <div id="afficheretour" class="tco-result"></div>
  <div id="afficheToken" class="tco-token"></div>

</div>

</td></tr></table>

<?php
if (in_array($_SESSION["membre"], ["menuadmin","menuscolaire"])) :
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else :
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
