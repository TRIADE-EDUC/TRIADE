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
<style>
.cantine-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:12px 8px}
.cantine-card{background:#fff;border:1px solid #dde0f0;border-radius:8px;padding:12px 14px;display:flex;flex-direction:column;gap:8px;transition:box-shadow .15s,border-color .15s}
.cantine-card:hover{box-shadow:0 3px 12px rgba(8,10,102,.1);border-color:#b0b8e8}
.cantine-card-header{display:flex;align-items:center;gap:8px}
.cantine-card-icon{font-size:20px;line-height:1;flex-shrink:0}
.cantine-card-title{font-size:12px;font-weight:700;color:#080A66}
.cantine-btn{width:100%;background:#080A66;color:#fff;border:none;border-radius:6px;padding:6px 10px;font-size:11px;font-weight:600;cursor:pointer;transition:background .15s;text-align:center}
.cantine-btn:hover{background:#0b0f8a}
.cantine-btn-outline{width:100%;background:#f0f2fa;color:#080A66;border:1px solid #dde0f0;border-radius:6px;padding:6px 10px;font-size:11px;font-weight:600;cursor:pointer;transition:background .15s}
.cantine-btn-outline:hover{background:#dde0f0}
</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestionnaire de cantine</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<?php
include_once('./librairie_php/db_triade.php');
$cnx=cnx();
if ( (verifDroit($_SESSION["id_pers"],"cantine")) || ($_SESSION["membre"] == "menuadmin") ) {
?>

<div class="cantine-grid">

    <div class="cantine-card">
        <div class="cantine-card-header"><div class="cantine-card-icon">&#128203;</div><div class="cantine-card-title">Nombre de passages aujourd'hui</div></div>
        <form action='cantine_nb_passage.php' method='post'>
            <button type="submit" class="cantine-btn">Accéder</button>
        </form>
    </div>

    <div class="cantine-card">
        <div class="cantine-card-header"><div class="cantine-card-icon">&#127869;</div><div class="cantine-card-title">Passage cantine</div></div>
        <button type="button" class="cantine-btn" onclick="open('cantine_passage.php','cantine','width=850,height=650,resizable=yes,scrollbars=yes')">Ouvrir</button>
    </div>

    <div class="cantine-card">
        <div class="cantine-card-header"><div class="cantine-card-icon">&#9881;</div><div class="cantine-card-title">Configuration des repas</div></div>
        <form action='cantine_config.php' method='post'>
            <button type="submit" class="cantine-btn">Accéder</button>
        </form>
    </div>

    <div class="cantine-card">
        <div class="cantine-card-header"><div class="cantine-card-icon">&#128196;</div><div class="cantine-card-title">Configuration des régimes</div></div>
        <form action='regime_ajout.php' method='post'>
            <button type="submit" class="cantine-btn">Accéder</button>
        </form>
    </div>

    <div class="cantine-card">
        <div class="cantine-card-header"><div class="cantine-card-icon">&#128101;</div><div class="cantine-card-title">Affectation des régimes</div></div>
        <form action='regime_affectation.php' method='post'>
            <button type="submit" class="cantine-btn">Accéder</button>
        </form>
    </div>

    <div class="cantine-card">
        <div class="cantine-card-header"><div class="cantine-card-icon">&#128176;</div><div class="cantine-card-title">Comptabilité d'un compte</div></div>
        <form action='cantine_compta.php' method='post'>
            <button type="submit" class="cantine-btn">Accéder</button>
        </form>
    </div>

    <div class="cantine-card">
        <div class="cantine-card-header"><div class="cantine-card-icon">&#128228;</div><div class="cantine-card-title">Exportation des données</div></div>
        <form action='cantine_export.php' method='post'>
            <button type="submit" class="cantine-btn">Accéder</button>
        </form>
    </div>

    <div class="cantine-card">
        <div class="cantine-card-header"><div class="cantine-card-icon">&#128202;</div><div class="cantine-card-title">Statistique / Comptage</div></div>
        <form action='cantine_stat.php' method='post'>
            <button type="submit" class="cantine-btn">Accéder</button>
        </form>
    </div>

</div>

<?php
} else {
    accesNonReserve();
}
?>

<!-- // fin  -->
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
