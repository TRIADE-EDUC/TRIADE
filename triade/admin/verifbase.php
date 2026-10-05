<?php
session_start();
error_reporting(0);
include_once("../librairie_php/lib_get_init.php");
$id = php_ini_get("safe_mode");
if ($id != 1) { set_time_limit(900); }
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
 *   Site                 : http://www.triade-educ.org
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade — Vérification de la base</title>
<style>
.verif-section { background:#080A66; color:#fff; border-radius:8px 8px 0 0; padding:7px 14px;
                 font-size:12px; font-weight:700; font-family:Electrolize,Trebuchet MS,Arial; margin-top:14px; }
.verif-card    { border:1px solid #c5cae9; border-top:none; border-radius:0 0 8px 8px;
                 background:#fff; margin-bottom:6px; }
.verif-row     { display:flex; align-items:center; padding:7px 14px;
                 border-bottom:1px solid #e8eaf6; }
.verif-row:last-child { border-bottom:none; }
.verif-row:hover { background:#f5f7ff; }
.verif-lbl     { flex:1; font-size:12px; font-weight:700; color:#080A66;
                 font-family:Electrolize,Trebuchet MS,Arial; }
.verif-ico     { width:36px; text-align:center; flex-shrink:0; }
.legend-row    { display:flex; align-items:center; gap:10px; margin-bottom:6px;
                 font-size:11px; color:#444; font-family:Electrolize,Trebuchet MS,Arial; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Vérification et optimisation de la Base</font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<?php
@unlink("../common/md5sum.log");
include_once("./librairie_php/db_triade_admin.php");
include_once("../common/lib_patch.php");

$cnx = cnx();

$codepatch      = "2"; supp_rep_patch();
$codemd5        = "2";
$codeg          = verif_table_groupe();
$coderep        = verif_secu_rep();
$codefic        = "2";
if (defined('ADMIN')) { $codefic = verif_fichier(ADMIN); }
$codeperm       = is_writable("../data/install_log/install.inc") ? "1" : "2";
$codeaffectation= verif_affectation();
$codeoptimization = optimize_mysql();
$verifmatiere   = verif_matiere();
$coderep0       = verif_repertoire();
$veriftable     = verif_table();
$coderep3       = "1";
$codeconfig     = verif_config(); $codeconfig = htaccessRacine();
$configSafeMode = (php_ini_get("safe_mode") != 1) ? "1" : "2";
$configRegisterGlobals = (php_ini_get("register_globals") != 1) ? "1" : "2";
$configMagicQuotesGPC  = (php_ini_get("magic_quotes_gpc") == 1) ? "1" : "2";
$configGD       = (php_module_load("gd") == 1) ? "1" : "";
$configSQLite   = (php_module_load("SQLite3") == 1) ? "1" : "";
$configSimpleXML= (php_module_load("SimpleXML") == 1) ? "1" : "";
$dbbstructureMD5= verifDbb();
$verifagenda    = verifAgenda();
$intramsn       = intraMSN();
$verifmessagerie= verifMessagerie();
?>

<!-- Info + légende -->
<div style="margin:10px 8px 0;">
    <div style="padding:9px 14px;background:#e8eaf6;border-radius:6px;font-size:11px;font-family:Electrolize,Trebuchet MS,Arial;color:#444;margin-bottom:10px;">
        Afin d'assurer la validité des fichiers Triade, nous vous suggérons d'installer le patch de référence MD5 avant chaque vérification.
        <br><br><a href="https://www.triade-educ.org/fr/recupFichierMd5.php?inc=100" target="_blank" class="btn-retour" style="font-size:10px;padding:2px 8px;text-decoration:none;">Télécharger la référence MD5</a>
    </div>

    <div class="na-card" style="padding:10px 14px;margin-bottom:12px;">
        <div class="legend-row"><img src="./image/commun/stat0.gif"> Corrigé — rafraîchir la page pour valider</div>
        <div class="legend-row"><img src="./image/commun/stat1.gif"> Fonctionnement normal</div>
        <div class="legend-row"><img src="./image/commun/stat3.gif"> Erreur — cliquer sur l'icône pour plus d'informations</div>
        <div class="legend-row"><img src="./image/commun/stat2.gif"> Erreur non corrigée — contacter le support Triade</div>
        <div class="legend-row"><img src="./image/commun/stat.gif">  Extension non implémentée — nécessaire dans certains modules</div>
    </div>
</div>

<!-- ── En ligne ─────────────────────────────────────────────── -->
<?php if (LAN == "oui"): ?>

<div class="verif-section">En ligne</div>
<div class="verif-card">

    <div class="verif-row">
        <span class="verif-lbl">Vérification des patchs</span>
        <span class="verif-ico">
            <a href="#" id="lienpatch"><img src="./image/commun/stat<?php print $codepatch ?>.gif" id="verifpatch" border="0"></a>
        </span>
    </div>
    <script language="JavaScript" src="https://support.triade-educ.org/support/version-patch.php?v=<?php print VERSIONPATCH ?>"></script>
    <script language="JavaScript">
    if (update == 0) { document.getElementById("verifpatch").src="./image/commun/stat1.gif"; }
    if (update == 1) { document.getElementById("verifpatch").src="./image/commun/stat3.gif"; document.getElementById("lienpatch").href="update.php"; }
    </script>

    <div class="verif-row">
        <span class="verif-lbl">Vérification fichier MD5</span>
        <span class="verif-ico">
            <a href="#" id="lienmd5" target="_blank"><img src="./image/commun/stat<?php print $codemd5 ?>.gif" id="verifmd5" border="0"></a>
        </span>
    </div>
    <?php
    if (file_exists("../common/config-md5.php")) { include("../common/config-md5.php"); }
    else { define("VERSIONMD5","000"); }
    ?>
    <script language="JavaScript" src="https://support.triade-educ.net/support/version-md5.php?v=<?php print VERSIONMD5 ?>"></script>
    <script language="JavaScript">
    if (updatemd5 == 0) { document.getElementById("verifmd5").src="./image/commun/stat1.gif"; }
    if (updatemd5 == 1) { document.getElementById("verifmd5").src="./image/commun/stat3.gif"; document.getElementById("lienmd5").href="https://www.triade-educ.org/fr/recupFichierMd5.php?inc=200"; }
    </script>

    <div class="verif-row">
        <span class="verif-lbl">Vérification des fichiers Triade</span>
        <span class="verif-ico">
            <?php if ($codefic == 3): ?>
                <a href="listchecksum.php"><img src="./image/commun/stat<?php print $codefic ?>.gif" border="0" alt="Consulter"></a>
            <?php else: ?>
                <img src="./image/commun/stat<?php print $codefic ?>.gif">
            <?php endif; ?>
        </span>
    </div>

</div>

<?php else: ?>
<div style="margin:8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#856404;">
    &#9888; Vous devez valider l'accès internet via le module
    <a href="configuration.php" style="color:#080A66;">Configuration Générale</a>
    pour une vérification totale.
</div>
<?php endif; ?>

<!-- ── Base de données ──────────────────────────────────────── -->
<div class="verif-section">Base de données</div>
<div class="verif-card">
    <div class="verif-row">
        <span class="verif-lbl">Vérification des groupes</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $codeg ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Vérification des matières</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $verifmatiere ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Vérification des tables</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $veriftable ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Vérification de l'agenda</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $verifagenda ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Vérification de la messagerie</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $verifmessagerie ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Optimisation des tables</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $codeoptimization ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Optimisation de l'Intra-Messenger</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $intramsn ?>.gif"></span>
    </div>
</div>

<!-- ── Fichiers & répertoires ──────────────────────────────── -->
<div class="verif-section">Fichiers &amp; répertoires</div>
<div class="verif-card">
    <div class="verif-row">
        <span class="verif-lbl">Vérification des répertoires</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $coderep0 ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Optimisation des répertoires</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $coderep3 ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Sécurité des répertoires</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $coderep ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Vérification des droits d'écriture</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $codeperm ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Vérification des fichiers de config.</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $codeconfig ?>.gif"></span>
    </div>
</div>

<!-- ── Configuration PHP ───────────────────────────────────── -->
<div class="verif-section">Configuration PHP</div>
<div class="verif-card">
    <div class="verif-row">
        <span class="verif-lbl">PHP — safe_mode</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $configSafeMode ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">PHP — register_globals</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $configRegisterGlobals ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Extension PHP / GD</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $configGD ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Extension SQLite</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $configSQLite ?>.gif"></span>
    </div>
    <div class="verif-row">
        <span class="verif-lbl">Extension SimpleXML</span>
        <span class="verif-ico"><img src="./image/commun/stat<?php print $configSimpleXML ?>.gif"></span>
    </div>
</div>

<div style="margin:12px 8px;">
    <a href="verifbase.php" class="btn-retour" style="text-decoration:none;">&#8635; Actualiser</a>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
<?php history_cmd("ADMIN","VERIFICATION","Data & Base"); Pgclose(); ?>
</html>
