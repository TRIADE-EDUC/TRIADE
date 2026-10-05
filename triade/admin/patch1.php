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
$cnx = cnx();
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
<title>Triade — Gestion des patchs</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des patchs</font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php
if (isset($_GET["idsupp"])) {
    $ficsupp = "./patch_ftp/" . $_GET["idsupp"] . ".zip";
    if (file_exists($ficsupp)) { @unlink($ficsupp); }
}
?>

<!-- Info FTP + espace disque -->
<div style="margin:10px 8px 14px;padding:10px 14px;background:#e8eaf6;border-radius:6px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#444;">
    <b>Mise à jour de Triade.</b><br><br>
    Pour les envois par FTP, transférez les patchs dans le répertoire :
    <code style="background:#fff;border:1px solid #c5cae9;border-radius:4px;padding:2px 8px;font-size:11px;color:#080A66;">/<?php echo ECOLE . "/" . ADMIN; ?>/patch_ftp/</code>
    <div style="margin-top:10px;">
        Espace libre :&nbsp;<strong><?php echo human_readable(diskfreespace("../")); ?></strong>
        <em style="color:#666;"><?php echo filesize_format(diskfreespace("../")); ?></em>
    </div>
</div>

<!-- Liste des patchs disponibles -->
<div style="margin:0 8px 6px;font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;">
    Liste des patchs pouvant être installés :
</div>

<form method="post" name="formulaire" action="patch2.php" onsubmit="document.formulaire.rien.disabled=true" style="margin:0 8px 14px;">
<table class="cc-data-table">
    <thead>
        <tr class="cc-thead-row">
            <th class="cc-th">Nom du patch</th>
            <th class="cc-th cc-th-center" style="width:80px;">Sélection</th>
            <th class="cc-th cc-th-center" style="width:50px;">Suppr.</th>
        </tr>
    </thead>
    <tbody>
<?php
$dirname = './patch_ftp/';
$dir = opendir($dirname);
$files = [];
while ($file = readdir($dir)) { $files[] = $file; }
sort($files);
foreach ($files as $file) {
    if ($file == '.' || $file == '..' || $file == '.htaccess' || is_dir($dirname . $file)) { continue; }
    $fic = preg_replace('/\.zip$/', '', $file);
    echo "<tr class=\"cc-tr-data\">";
    echo "<td class=\"cc-td\">";
    echo "<strong>" . htmlspecialchars($file) . "</strong>";
    if (verifpatchinstall($file)) {
        echo "&nbsp;<span class=\"htip-wrap\" style=\"display:inline-block;\">"
           . "<img src='../image/commun/important.png' border='0' alt='Déjà installé' style='vertical-align:middle;'>"
           . "<span class=\"htip\">Déjà installé</span></span>";
    }
    echo "</td>";
    echo "<td class=\"cc-td cc-td-center\">"
       . "<input type='radio' name='patch_ftp' value=\"" . htmlspecialchars($file) . "\" title='Sélectionner' onclick=\"document.formulaire.rien.disabled=false;\">"
       . "</td>";
    echo "<td class=\"cc-td cc-td-center\">"
       . "<a href='patch1.php?idsupp=" . urlencode($fic) . "'><img src='../image/commun/trash.png' border='0' alt='Supprimer'></a>"
       . "</td>";
    echo "</tr>";
}
closedir($dir);
?>
    </tbody>
</table>

<div style="margin-top:12px;">
    <input type="submit" value="Valider le patch" name="rien" class="btn-enr" disabled="disabled">
    &nbsp;
    <a href="patch.php" class="btn-retour" style="text-decoration:none;">Retour</a>
</div>
</form>

<?php PgClose(); ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
