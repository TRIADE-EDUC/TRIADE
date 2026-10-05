<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../common/config6.inc.php");
?>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade — Installation des patchs</title>
<style>
.patch-upload-card { background:#f5f7ff; border:1px solid #c5cae9; border-radius:8px; padding:14px 18px; margin:10px 8px 14px; }
.patch-upload-title { font-size:12px; font-weight:700; color:#080A66; font-family:Electrolize,Trebuchet MS,Arial; margin-bottom:10px; }
.patch-progress { display:block; width:100%; max-width:320px; height:10px; border-radius:5px; margin:8px 0 4px; }
.patch-status { font-size:11px; color:#080A66; font-family:Electrolize,Trebuchet MS,Arial; margin-top:4px; min-height:16px; }
.patch-disk { display:inline-flex; align-items:center; gap:6px; background:#e8eaf6; border-radius:6px; padding:6px 14px;
              font-size:12px; font-family:Electrolize,Trebuchet MS,Arial; color:#444; }
</style>
<script type="text/javascript">
function _(el) { return document.getElementById(el); }

function uploadFile() {
    var file = _("Filedata").files[0];
    var formdata = new FormData();
    formdata.append("Filedata", file);
    var ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    ajax.addEventListener("error", errorHandler, false);
    ajax.addEventListener("abort", abortHandler, false);
    ajax.open("POST", "patchupload.php");
    ajax.send(formdata);
}

function progressHandler(event) {
    _("loaded_n_total").innerHTML = event.loaded + " octets sur " + event.total;
    var percent = (event.loaded / event.total) * 100;
    _("progressBar").value = Math.round(percent);
    _("status").innerHTML = Math.round(percent) + "% — veuillez patienter…";
}

function completeHandler(event) {
    _("progressBar").value = 0;
    _("loaded_n_total").innerHTML = "";
    _("status").innerHTML = "Fichier transmis avec succès.";
}

function errorHandler(event) { _("status").innerHTML = "Erreur lors du téléchargement."; }
function abortHandler(event) { _("status").innerHTML = "Téléchargement abandonné."; }
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Installation des patchs</font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php
if (!is_dir("./patch_ftp")) { mkdir("./patch_ftp"); }

if (INTER == "oui") { ?>

<div style="margin:14px 8px;padding:12px 16px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#2e7d32;">
    Ce service est pris en compte par notre équipe.<br><br>
    Nous nous occupons de mettre à jour Triade automatiquement.<br><br>
    <strong>L'Équipe Triade</strong>
</div>

<?php } else {
    if (!preg_match('/test-dev\.triade-educ\.net/', $_SERVER['SERVER_NAME'])) { ?>

<!-- Info support -->
<div style="margin:10px 8px 4px;padding:10px 14px;background:#e8eaf6;border-radius:6px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#444;">
    <b>Mise à jour de Triade.</b><br><br>
    Pour obtenir les patchs, consulter le module :
    <a href="https://www.triade-educ.org/fr/support-triade.php" target="_blank" style="color:#080A66;font-weight:700;">Support Triade</a>
</div>

<!-- Upload -->
<?php
$taille = "2Mo"; $maxsize = "2000000";
if (MAXUPLOAD == "oui") { $taille = "8Mo"; $maxsize = "8000000"; }
?>
<div class="patch-upload-card">
    <div class="patch-upload-title">
        <img src='../image/commun/ico_test.gif' style="vertical-align:middle;margin-right:8px;">Envoyer un fichier patch
    </div>
    <form id="upload_form" enctype="multipart/form-data" method="post" style="margin:0;">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <input type="file" name="Filedata" id="Filedata" onchange="uploadFile()" style="font-size:12px;">
            <span class="htip-wrap">
                <img src="../image/help.gif" border="0" style="cursor:pointer;vertical-align:middle;">
                <span class="htip">Taille maximale : <?php echo $taille; ?></span>
            </span>
        </div>
        <progress id="progressBar" value="0" max="100" class="patch-progress"></progress>
        <div id="status" class="patch-status"></div>
        <div id="loaded_n_total" class="patch-status"></div>
    </form>
</div>

<!-- Actions -->
<div style="margin:0 8px 14px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
    <a href="patch1.php" class="btn-retour" style="text-decoration:none;">Accès au gestionnaire de patch</a>
    <div class="patch-disk">
        Espace libre :&nbsp;<strong><?php echo human_readable(diskfreespace("../")); ?></strong>
        <em style="color:#666;"><?php echo filesize_format(diskfreespace("../")); ?></em>
    </div>
</div>

<!-- Liste des patchs -->
<div style="margin:0 8px 6px;font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;">
    Liste des patchs déjà installés :
</div>
<div style="margin:0 8px 14px;">
<?php
include_once("librairie_php/db_triade_admin.php");
$cnx = cnx();
$data = list_patch();
?>
<table class="cc-data-table">
    <thead>
        <tr class="cc-thead-row">
            <th class="cc-th" style="width:18%;">Patch</th>
            <th class="cc-th" style="width:22%;">Date</th>
            <th class="cc-th">Informations</th>
        </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < count($data); $i++) { ?>
        <tr class="cc-tr-data">
            <td class="cc-td"><strong><?php echo $data[$i][0]; ?></strong></td>
            <td class="cc-td"><?php echo dateForm($data[$i][1]); ?>&nbsp;<?php echo $data[$i][2]; ?></td>
            <td class="cc-td">
                <a href="#" onclick="open('patch-info.php?idpatch=<?php echo $data[$i][0]; ?>','','width=400,height=400')" class="cc-btn-consult">
                    Information sur le patch
                </a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<?php Pgclose($cnx); ?>
</div>

<?php } else { ?>

<div style="margin:14px 8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#856404;text-align:center;">
    &#9888; Pas d'installation de patch sur le serveur de DEV
</div>

<?php } } ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
