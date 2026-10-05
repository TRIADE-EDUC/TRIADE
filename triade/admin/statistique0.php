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
error_reporting(0);
if (isset($_POST["saisieecole"])) :
    if ($_POST["saisieecole"] != '0') :
        print "<script language=JavaScript>open('../statistique.php','_parent','');</script>";
    endif;
endif;
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
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade — Statistiques</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<!-- Section 1 : Statistiques établissement -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des Statistiques des établissements</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div style="margin:12px 8px;">
    <form method="POST">
        <input type="hidden" name="saisieecole" value="<?php echo htmlspecialchars(REPECOLE); ?>">
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;">
                Statistique de l'établissement
            </span>
            <input type="submit" value="Consulter" class="btn-enr">
        </div>
    </form>
</div>

</td></tr></table>

<br>

<!-- Section 2 : Compteur & analyse -->
<?php
include_once('librairie_php/db_triade_admin.php');
include_once("./librairie_php/lib_statistique_valeur.php");
$affiche      = resultat_count("compteur_acces.txt");
$affiche_time = resultat_time("compteur_acces.time");
$cnx = cnx();
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des Statistiques — Compteur</font></b></td></tr>
<tr id='cadreCentral0'><td>

<!-- Compteur accès -->
<div style="margin:10px 8px 14px;">
<table class="cc-data-table">
    <thead>
        <tr class="cc-thead-row">
            <th class="cc-th">Page analysée</th>
            <th class="cc-th cc-th-center" style="width:15%;">Nb d'accès</th>
            <th class="cc-th cc-th-center">Date dernier accès</th>
        </tr>
    </thead>
    <tbody>
        <tr class="cc-tr-data">
            <td class="cc-td">Consultation accès</td>
            <td class="cc-td cc-td-center"><?php echo $affiche; ?></td>
            <td class="cc-td cc-td-center"><?php echo $affiche_time; ?></td>
        </tr>
    </tbody>
</table>
</div>

<!-- Analyse temps d'exécution -->
<div style="margin:0 8px 6px;font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;">
    Analyse du temps d'exécution des pages :
</div>
<div style="margin:0 8px 14px;">
<?php
$data = analyse_page();
?>
<table class="cc-data-table">
    <thead>
        <tr class="cc-thead-row">
            <th class="cc-th">Fichier</th>
            <th class="cc-th cc-th-center" style="width:20%;">Valeur Min</th>
            <th class="cc-th cc-th-center" style="width:20%;">Valeur Max</th>
        </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < count($data); $i++) { ?>
        <tr class="cc-tr-data">
            <td class="cc-td"><input type="text" value="<?php echo htmlspecialchars($data[$i][0]); ?>" size="30" style="font-size:11px;border:1px solid #c5cae9;border-radius:4px;padding:2px 6px;"></td>
            <td class="cc-td cc-td-center"><?php echo $data[$i][2]; ?></td>
            <td class="cc-td cc-td-center"><?php echo $data[$i][1]; ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
