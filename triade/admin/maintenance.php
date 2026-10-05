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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<title>Triade — Maintenance</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/db_triade_admin.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>

<?php
if (isset($_GET["saisie_efface"]) && $_GET["saisie_efface"] == "oui") {
    $fic = "../data/maintenance.txt";
    if (file_exists($fic)) { unlink($fic); }
}
?>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Service de maintenance</font></b></td></tr>
<tr id='cadreCentral0'><td>

<!-- Formulaire -->
<div style="margin:10px 8px 14px;background:#f5f7ff;border:1px solid #c5cae9;border-radius:8px;padding:14px 18px;">
    <form method="post" name="formulaire">

        <div style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#333;margin-bottom:12px;">
            Le service Triade sera indisponible le
            <input type="text" value="jj/mm/aaaa" readonly name="dates" size="12"
                   style="font-size:12px;border:1px solid #c5cae9;border-radius:4px;padding:3px 8px;cursor:pointer;">
            <?php
            include_once("../librairie_php/calendar.php");
            calendarDim("id2", "document.formulaire.dates", $_SESSION["langue"], "0", "0");
            ?>
        </div>

        <div style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#333;margin-bottom:14px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            entre
            <select name="time1" class="cc-select">
                <?php for ($h = 0; $h < 24; $h++) { $v = sprintf("%02dh00", $h); echo "<option value=\"$v\">$v</option>"; } ?>
            </select>
            et
            <select name="time2" class="cc-select">
                <?php for ($h = 0; $h < 24; $h++) { $v = sprintf("%02dh00", $h); echo "<option value=\"$v\">$v</option>"; } ?>
            </select>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <input type="submit" value="Ajouter la maintenance" name="create" class="btn-enr">
            <a href="maintenance.php?saisie_efface=oui" class="btn-retour" style="text-decoration:none;">Supprimer la maintenance</a>
        </div>

        <?php
        if (isset($_POST["create"])) {
            list($jour, $mois, $annee) = preg_split('/\//', $_POST["dates"], 3);
            if (checkdate($mois, $jour, $annee)) {
                $fic = "../data/maintenance.txt";
                $fichier = fopen($fic, "w");
                fwrite($fichier, $_POST["dates"] . ":" . $_POST["time1"] . ":" . $_POST["time2"]);
                fclose($fichier);
            } else {
                alertJs("Date refusée");
            }
        }
        ?>
    </form>
</div>

<!-- Maintenance active -->
<?php
$fic = "../data/maintenance.txt";
if (file_exists($fic)) {
    $fichier = fopen($fic, "r");
    $message = fread($fichier, 100);
    fclose($fichier);
    list($date, $time1, $time2) = preg_split("/:/", $message, 3);
?>
<div style="margin:0 8px 14px;padding:14px 18px;background:#fff3f3;border:1px solid #f5c6c6;border-radius:8px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#333;">
    <div style="font-size:13px;font-weight:700;color:#c0392b;margin-bottom:8px;">&#9888; Maintenance active sur la page d'accueil</div>
    Une intervention est prévue sur le logiciel.<br><br>
    Le service Triade sera inaccessible le <strong><?php echo htmlspecialchars($date); ?></strong>
    entre <strong><?php echo htmlspecialchars($time1); ?></strong> et <strong><?php echo htmlspecialchars($time2); ?></strong>.
</div>
<?php } ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
