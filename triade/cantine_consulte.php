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
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include_once("./common/config5.inc.php") ?>
<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
<meta http-equiv="CacheControl" content="no-cache" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="expires" content="-1" />
<meta name="Copyright" content="Triade©, 2001" />
<link rel="SHORTCUT ICON" href="./favicon.ico" />
<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<style>
/* ── Cantine – Design 2026 ── */
* { box-sizing: border-box; }
.cantine-wrap {
    padding: 10px;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
}
.cantine-card {
    background: #fff; border: 1px solid #e8e4c0;
    border-radius: 8px; overflow: hidden;
}
.cantine-table {
    width: 100%; border-collapse: collapse; font-size: 13px;
}
.cantine-table thead th {
    padding: 8px 12px; font-weight: 700;
    text-align: left; white-space: nowrap;
}
.cantine-table thead th.right { text-align: right; }
.cantine-table tbody tr {
    border-bottom: 1px solid #f5f2de;
    transition: background .1s;
}
.cantine-table tbody tr:last-child { border-bottom: none; }
.cantine-table tbody tr:hover { background: #FFFDE7; }
.cantine-table td {
    padding: 7px 12px; color: #333;
}
.cantine-table td.right { text-align: right; }
.cantine-table td.date { white-space: nowrap; color: #666; font-size: 12px; }
.cantine-table tfoot tr {
    background: #FFFBE6; border-top: 2px solid #e8d870;
}
.cantine-table tfoot td {
    padding: 8px 12px; font-weight: 700;
}
.total-positive { color: #2a7a2a; }
.total-negative { color: #c00; }
</style>
</head>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();
?>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/messagerie_fenetre.js"></script>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Consultation de votre compte cantine</font></b></td></tr>
<tr id='cadreCentral0'><td valign='top'>

<div class="cantine-wrap">
<div class="cantine-card">
<table class="cantine-table">
    <thead>
        <tr id='coulBar0'>
            <th style="width:15%"><span class="m">Date</span></th>
            <th><span class="m">Détail</span></th>
            <th class="right" colspan="2"><span class="m">Montant <?php print unitemonnaie() ?></span></th>
        </tr>
    </thead>
    <tbody>
    <?php
    $idpers = $_SESSION["id_pers"];
    $membre = $_SESSION["membre"];
    $data   = recupComptaPers($idpers, $membre);
    $total  = 0;
    for ($i = 0; $i < countTriade($data); $i++) {
        if ($i < 50) {
            print "<tr>";
            print "<td class='date'>&nbsp;".dateForm($data[$i][0])."&nbsp;</td>";
            print "<td>&nbsp;".urldecode($data[$i][2])."</td>";
            print "<td class='right'>".affichageFormatMonnaie($data[$i][1])."&nbsp;</td>";
            print "</tr>";
        }
        $total += $data[$i][1];
    }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align:right">Totaux :</td>
            <td class="right <?php print ($total < 0) ? 'total-negative' : 'total-positive' ?>">
                <?php print affichageFormatMonnaie($total) ?>&nbsp;
            </td>
        </tr>
    </tfoot>
</table>
</div><!-- /.cantine-card -->
</div><!-- /.cantine-wrap -->

<?php
print "</td></tr></table>";
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}
?>
</BODY></HTML>
