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
<?php
include_once("./common/lib_ecole.php");
include_once("./common/lib_admin.php");
include_once("./common/config.inc.php");
include_once("./".REPADMIN."/librairie_php/lib_error.php");
include_once("./".REPADMIN."/librairie_php/mactu.php");
if (empty($_SESSION["admin1"])) {
    print "<script language='javascript'>";
    print "location.href='/".REPECOLE."/".REPADMIN."/acces_refuse.php'";
    print "</script>";
    exit;
}
?>
<script>var largeurfen='1024'</script>
<script>var banniere='online'</script>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./<?php echo REPADMIN; ?>/librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script type="text/javascript" src="./librairie_js/logo.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade — Statistiques</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="./<?php echo REPADMIN; ?>/librairie_js/menudepart.js"></SCRIPT>
<?php include("./".REPADMIN."/librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./<?php echo REPADMIN; ?>/librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Statistique de l'établissement</font></b></td></tr>
<tr id='cadreCentral0'><td>

<!-- Navigation statistiques -->
<div style="margin:10px 8px 14px;">
    <table class="cc-data-table">
        <tbody>
            <tr class="cc-tr-data">
                <td class="cc-td">Information Nav., Version, OS, Langue</td>
                <td class="cc-td cc-td-center" style="width:120px;">
                    <a href="#" onclick="open('statistique_nav.php','_parent','')" class="btn-nav" style="text-decoration:none;">Consulter</a>
                </td>
            </tr>
            <tr class="cc-tr-data">
                <td class="cc-td">Information type de connexion</td>
                <td class="cc-td cc-td-center">
                    <a href="#" onclick="open('statistique_debit.php','_parent','')" class="btn-nav" style="text-decoration:none;">Consulter</a>
                </td>
            </tr>
            <tr class="cc-tr-data">
                <td class="cc-td">Information connexion par heure</td>
                <td class="cc-td cc-td-center">
                    <a href="#" onclick="open('statistique_conc_heure.php','_parent','')" class="btn-nav" style="text-decoration:none;">Consulter</a>
                </td>
            </tr>
            <tr class="cc-tr-data">
                <td class="cc-td">Information utilisateur enregistré</td>
                <td class="cc-td cc-td-center">
                    <a href="#" onclick="open('statistique_conc_utilisateur.php','_parent','')" class="btn-nav" style="text-decoration:none;">Consulter</a>
                </td>
            </tr>
            <tr class="cc-tr-data">
                <td class="cc-td">Information type d'écran</td>
                <td class="cc-td cc-td-center">
                    <a href="#" onclick="open('statistique_ecran.php','_parent','')" class="btn-nav" style="text-decoration:none;">Consulter</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Journal des connexions -->
<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();

if (isset($_GET["limit"])) {
    $data = trace_aff('0');
} else {
    $data = trace_aff('400');
}
$date = dateDMY();
$date = datemoinsn($date, 365);
?>

<div style="margin:0 8px 6px;font-size:11px;font-family:Electrolize,Trebuchet MS,Arial;color:#555;">
    <i>Les informations de connexion sont conservées pendant <b>un an</b>.</i>
    <?php if (!isset($_GET["limit"])): ?>
    &nbsp;— Affichage limité aux 400 dernières connexions.
    <?php endif; ?>
</div>

<div style="margin:0 8px 14px;">
<table class="cc-data-table">
    <thead>
        <tr class="cc-thead-row">
            <th class="cc-th cc-th-center" style="width:14%;">Date</th>
            <th class="cc-th">Nom — Prénom</th>
            <th class="cc-th cc-th-center" style="width:16%;">Adresse IP</th>
            <th class="cc-th" style="width:26%;">Système — Navigateur</th>
        </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < countTriade($data); $i++) { ?>
        <tr class="cc-tr-data">
            <td class="cc-td cc-td-center">
                <?php echo dateForm($data[$i][2]); ?><br>
                <span style="font-size:10px;color:#666;"><?php echo $data[$i][3]; ?></span>
            </td>
            <td class="cc-td">
                <?php echo htmlspecialchars($data[$i][0]); ?><br>
                <span style="font-size:10px;color:#666;"><?php echo htmlspecialchars($data[$i][6]); ?></span>
            </td>
            <td class="cc-td cc-td-center"><?php echo htmlspecialchars($data[$i][1]); ?></td>
            <td class="cc-td"><?php echo htmlspecialchars($data[$i][4]); ?> — <?php echo htmlspecialchars($data[$i][5]); ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</div>

<?php if (!isset($_GET["limit"])): ?>
<div style="margin:0 8px 14px;">
    <a href="statistique.php?limit" class="btn-retour" style="text-decoration:none;">
        Liste complète depuis le <?php echo dateForm($date); ?>
    </a>
</div>
<?php endif; ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./<?php echo REPADMIN; ?>/librairie_js/menudepart2.js"></SCRIPT>
</body>
</html>
