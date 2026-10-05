<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<HTML>
<HEAD>
<?php
include_once("./librairie_php/lib_licence.php");

// Solde tokens si config IA disponible
$solde  = null;
$iakey  = '';
$iaActif = false;
if (file_exists('../common/config-ia.php')) {
    include_once('../common/config-ia.php');
    if (defined('IAKEY') && IAKEY !== '') {
        $iakey   = IAKEY;
        $iaActif = true;
        if (file_exists('../librairie_php/db_ia.php')) {
            include_once('../librairie_php/db_ia.php');
            $solde = getIABalance($iakey);
        }
    }
}

$tokensDisp   = $solde ? intval($solde['nbtokencredit']) : 0;
$tokensFormat = $solde ? number_format($tokensDisp, 0, ',', ' ') : '—';
$badgeColor   = ($solde && $tokensDisp > 0) ? '#d4edda' : '#f8d7da';
$badgeText    = ($solde && $tokensDisp > 0) ? '#155724' : '#721c24';
$badgeLabel   = $iaActif ? ($tokensFormat . ' tokens') : 'Non inscrit';
?>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade — TRIADE-COPILOT</title>
<style>
.tc-wrap     { padding: 16px; }
.tc-steps    { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
.tc-step {
    flex: 1; min-width: 130px; background: #fff;
    border: 2px solid #c5caee; border-radius: 10px;
    padding: 18px 14px; text-align: center;
    position: relative;
}
.tc-step.active  { border-color: #080A66; background: #f0f2fa; }
.tc-step.done    { border-color: #28a745; background: #f8fff9; }
.tc-step.locked  { opacity: 0.45; pointer-events: none; cursor: default; }
.tc-step a       { text-decoration: none !important; color: inherit; display: block; }
.tc-step:not(.locked) a:hover .tc-icon { transform: scale(1.08); }
.tc-num {
    width: 22px; height: 22px; border-radius: 50%;
    background: #c5caee; color: #080A66;
    font-size: 11px; font-weight: bold; line-height: 22px;
    display: inline-block; margin-bottom: 8px;
}
.tc-step.active .tc-num  { background: #080A66; color: #fff; }
.tc-step.done   .tc-num  { background: #28a745; color: #fff; }
.tc-icon  { font-size: 26px; margin-bottom: 8px; transition: transform .15s; }
.tc-titre { font-weight: bold; color: #080A66; font-size: 12px; margin-bottom: 4px; }
.tc-desc  { font-size: 11px; color: #666; line-height: 1.4; }
.tc-abo-bar {
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
    background: #f0f2fa; border: 1px solid #c5caee; border-radius: 8px;
    padding: 10px 14px; margin-bottom: 20px; font-size: 12px;
}
.tc-badge { padding: 3px 12px; border-radius: 12px; font-weight: bold; font-size: 11px; }
.tc-alert {
    background: #fff3e0; border: 1px solid #ffcc80; border-radius: 6px;
    padding: 10px 14px; font-size: 12px; margin-bottom: 14px; color: #856404;
}
.tc-alert a { color: #c0392b; font-weight: 700; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>TRIADE-COPILOT</font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php if (LAN == "oui"): ?>
<?php
include_once("../librairie_php/db_triade.php");
valideProductId();
include_once("../common/config2.inc.php");
?>

<div class="tc-wrap">

<?php if ($iaActif): ?>
    <!-- Solde -->
    <div class="tc-abo-bar">
        <span>Solde IA :</span>
        <span class="tc-badge" style="background:<?php echo $badgeColor; ?>;color:<?php echo $badgeText; ?>;">
            <?php echo htmlspecialchars($badgeLabel); ?>
        </span>
        <?php if ($solde && $tokensDisp < 1000000): ?>
        <span style="color:#856404;font-size:11px;">&#9888; Solde faible — pensez à recharger</span>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="tc-alert">
        &#9888; Vous n'êtes pas encore inscrit à TRIADE-COPILOT.<br>
        Commencez par l'<strong>Étape 1</strong> ci-dessous — l'inscription est <strong>gratuite</strong>.
    </div>
<?php endif; ?>

    <!-- Étapes -->
    <div class="tc-steps">

        <!-- Étape 1 : Inscription -->
        <div class="tc-step <?php echo $iaActif ? 'done' : 'active'; ?>">
            <a href="ia-inscription.php">
                <div class="tc-num"><?php echo $iaActif ? '&#10003;' : '1'; ?></div>
                <div class="tc-icon">&#128221;</div>
                <div class="tc-titre">Inscription</div>
                <div class="tc-desc">
                    <?php if ($iaActif): ?>
                        Compte actif
                    <?php else: ?>
                        Créer un compte<br>TRIADE-COPILOT gratuit
                    <?php endif; ?>
                </div>
            </a>
        </div>

        <!-- Étape 2 : Crédits -->
        <div class="tc-step <?php echo $iaActif ? '' : 'locked'; ?>">
            <?php if ($iaActif): ?>
            <a href="copilot_abo.php">
            <?php endif; ?>
                <div class="tc-num">2</div>
                <div class="tc-icon">&#128179;</div>
                <div class="tc-titre">Crédits IA</div>
                <div class="tc-desc">Acheter des tokens,<br>consulter le solde</div>
            <?php if ($iaActif): ?>
            </a>
            <?php endif; ?>
        </div>

        <!-- Étape 3 : Agents IA -->
        <div class="tc-step <?php echo $iaActif ? '' : 'locked'; ?>">
            <?php if ($iaActif): ?>
            <a href="copilot_config_agents.php">
            <?php endif; ?>
                <div class="tc-num">3</div>
                <div class="tc-icon">&#129302;</div>
                <div class="tc-titre">Agents IA</div>
                <div class="tc-desc">Activer / désactiver<br>les agents disponibles</div>
            <?php if ($iaActif): ?>
            </a>
            <?php endif; ?>
        </div>

        <!-- Mon compte -->
        <?php if ($iaActif): ?>
        <div class="tc-step">
            <a href="copilot_mon_compte.php">
                <div class="tc-num">&#128100;</div>
                <div class="tc-icon">&#128100;</div>
                <div class="tc-titre">Mon compte</div>
                <div class="tc-desc">Consulter et modifier<br>les infos du compte</div>
            </a>
        </div>
        <?php endif; ?>

    </div>

    <?php if ($iaActif && defined('AFFICHAGEIA') && AFFICHAGEIA != "oui"): ?>
    <div style="margin-top:6px;padding:10px 14px;background:#fff3e0;border:1px solid #ffcc80;border-radius:6px;font-size:12px;color:#856404;">
        &#9888; Activez TRIADE-COPILOT dans <a href="configuration.php" style="color:#c0392b;font-weight:700;">Config. Générale</a> pour que les agents soient visibles.
    </div>
    <?php endif; ?>

</div>

<?php else: ?>
<div style="margin:14px 8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;color:#856404;text-align:center;">
    &#9888; <?php echo ERREUR1; ?><br><br><i><?php echo ERREUR2; ?></i>
</div>
<?php endif; ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
