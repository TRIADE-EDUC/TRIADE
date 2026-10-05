<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<HTML>
<HEAD>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../common/config6.inc.php");
include_once("../librairie_php/db_visio.php");
$abo = getAbonnementVisio();
$statutAff  = $abo ? $abo['statut'] : 'inactif';
$planAff    = $abo ? strtoupper($abo['plan']) : '—';
$dateFinAff = $abo && $abo['date_fin'] ? date('d/m/Y', strtotime($abo['date_fin'])) : '—';
$badgeColor = ($abo && $abo['statut'] === 'actif') ? '#d4edda' : '#f8d7da';
$badgeText  = ($abo && $abo['statut'] === 'actif') ? '#155724' : '#721c24';
?>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade — Visioconférence</title>
<style>
.tv-wrap { padding: 16px; }
.tv-abo-bar {
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
    background: #f0f2fa; border: 1px solid #c5caee; border-radius: 8px;
    padding: 10px 14px; margin-bottom: 20px; font-size: 12px;
}
.tv-badge { padding: 3px 12px; border-radius: 12px; font-weight: bold; font-size: 11px; }
.tv-cards { display: flex; gap: 14px; flex-wrap: wrap; }
.tv-card {
    flex: 1; min-width: 140px; background: #fff;
    border: 1px solid #c5caee; border-radius: 10px;
    padding: 20px 16px; text-align: center;
    text-decoration: none !important; color: inherit; cursor: pointer;
}
.tv-card:hover, .tv-card:focus, .tv-card:active { text-decoration: none !important; color: inherit; outline: none; }
.tv-card *, .tv-card *:hover { text-decoration: none !important; }
.tv-card .tc-icon  { font-size: 28px; margin-bottom: 10px; }
.tv-card .tc-titre { font-weight: bold; color: #080A66; font-size: 13px; margin-bottom: 6px; }
.tv-card .tc-desc  { font-size: 11px; color: #666; line-height: 1.4; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Triade-Visio</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div class="tv-wrap">
    <div class="tv-abo-bar">
        <span>Abonnement :</span>
        <span class="tv-badge" style="background:<?php echo $badgeColor; ?>;color:<?php echo $badgeText; ?>;">
            <?php echo strtoupper($statutAff); ?>
        </span>
        <span>Plan : <strong><?php echo $planAff; ?></strong></span>
        <span>Expire le : <strong><?php echo $dateFinAff; ?></strong></span>
    </div>

    <div class="tv-cards">
        <div class="tv-card" onclick="location.href='visio_abo.php'">
            <div class="tc-icon">&#9881;&#65039;</div>
            <div class="tc-titre">Abonnement Visio</div>
            <div class="tc-desc">Activer, choisir un plan,<br>gérer les dates et limites</div>
        </div>
        <div class="tv-card" onclick="window.open('../visio/dashboard.php','_blank')">
            <div class="tc-icon">&#128202;</div>
            <div class="tc-titre">Tableau de bord</div>
            <div class="tc-desc">Salles en live, participants<br>connectés, sessions du jour</div>
        </div>
        <div class="tv-card" onclick="window.open('../visio/rooms.php','_blank')">
            <div class="tc-icon">&#128249;</div>
            <div class="tc-titre">Salles actives</div>
            <div class="tc-desc">Voir et rejoindre<br>les salles en cours</div>
        </div>
    </div>
</div>

</td></tr>
</table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
