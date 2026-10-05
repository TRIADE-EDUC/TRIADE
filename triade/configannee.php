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
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<style>
.ca-stack{display:flex;flex-direction:column;gap:14px;padding:12px 8px}
.ca-card{background:#fff;border:1px solid #dde0f0;border-radius:10px;padding:16px}
.ca-card-title{font-size:12px;font-weight:700;color:#080A66;display:flex;align-items:center;gap:7px;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #eef0f8}
.ca-card-icon{font-size:16px}
.ca-date-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px}
.ca-date-label{font-size:12px;font-weight:600;color:#333;min-width:60px}
.ca-date-group{display:flex;align-items:center;gap:6px}
.ca-date-sep{font-size:14px;color:#888;font-weight:700}
.ca-date-input{width:52px;padding:7px 8px;border:1px solid #dde0f0;border-radius:7px;font-size:14px;font-weight:700;color:#080A66;text-align:center;background:#f8f9ff}
.ca-date-input:focus{border-color:#080A66;outline:none;box-shadow:0 0 0 2px rgba(8,10,102,.08)}
.ca-date-hint{font-size:10px;color:#aaa;text-align:center;display:block;margin-top:2px}
.ca-btn{background:#080A66;color:#fff;border:none;border-radius:7px;padding:9px 20px;font-size:12px;font-weight:700;cursor:pointer;margin-top:6px}
.ca-btn:hover{background:#0b0f8a}
.ca-btn-danger{background:#c62828;color:#fff;border:none;border-radius:7px;padding:9px 20px;font-size:12px;font-weight:700;cursor:pointer}
.ca-btn-danger:hover{background:#b71c1c}
.ca-btn-ok{background:#2e7d32;color:#fff;border:none;border-radius:7px;padding:9px 20px;font-size:12px;font-weight:700;cursor:pointer}
.ca-btn-ok:hover{background:#1b5e20}
.ca-msg{border-radius:7px;padding:9px 14px;font-size:12px;font-weight:600;margin-bottom:10px}
.ca-msg.ok{background:#e8f5e9;border:1px solid #a5d6a7;color:#2e7d32}
.ca-msg.error{background:#fce4ec;border:1px solid #f48fb1;color:#c62828}
.ca-summer-status{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;margin-bottom:12px}
.ca-summer-status.on{background:#fce4ec;color:#c62828;border:1px solid #f48fb1}
.ca-summer-status.off{background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7}
.ca-summer-desc{font-size:12px;color:#555;margin-bottom:14px;line-height:1.6}
</style>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Configuration de l'année scolaire</font></b></td></tr>
<tr id='cadreCentral0'><td>
<!-- // fin  -->
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");

$reponse = "";
$reponseOk = true;

$dj = aff_valeur_parametrage("anneescolaire_dj");
$fj = aff_valeur_parametrage("anneescolaire_fj");
$dm = aff_valeur_parametrage("anneescolaire_dm");
$fm = aff_valeur_parametrage("anneescolaire_fm");

if (isset($_POST['okannee'])) {
    $dj = $_POST["dj"];
    $fj = $_POST["fj"];
    $dm = $_POST["dm"];
    $fm = $_POST["fm"];
    if (($dj > 0) && ($dj <= 31) && ($fj > 0) && ($fj <= 31) && ($dm > 0) && ($dm <= 12) && ($fm > 0) && ($fm <= 12)) {
        enr_parametrage("anneescolaire_dj",$dj,'');
        enr_parametrage("anneescolaire_fj",$fj,'');
        enr_parametrage("anneescolaire_dm",$dm,'');
        enr_parametrage("anneescolaire_fm",$fm,'');
        $reponse = "Données enregistrées.";
        $reponseOk = true;
    } else {
        $reponse = "Erreur de saisie : vérifiez les jours (1-31) et mois (1-12) indiqués.";
        $reponseOk = false;
    }
}

if (isset($_POST["okete"])) { touch("./data/parametrage/noacces.ete"); }
if (isset($_POST["koete"])) { @unlink("./data/parametrage/noacces.ete"); }

Pgclose();
$modeEte = file_exists("./data/parametrage/noacces.ete");
?>

<div class="ca-stack">

<?php if ($reponse): ?>
<div class="ca-msg <?php print $reponseOk ? 'ok' : 'error' ?>">
    <?php print $reponseOk ? '&#10003; ' : '&#9888; ' ?><?php print $reponse ?>
</div>
<?php endif; ?>

<!-- Dates année scolaire -->
<div class="ca-card">
    <div class="ca-card-title"><span class="ca-card-icon">&#128197;</span> Dates de l'année scolaire</div>
    <form method="post" action="configannee.php">
        <div class="ca-date-row">
            <span class="ca-date-label">Début :</span>
            <span style="font-size:12px;color:#555">Le</span>
            <div>
                <input type="text" name="dj" class="ca-date-input" value="<?php print $dj ?>" maxlength="2">
                <span class="ca-date-hint">jour</span>
            </div>
            <span class="ca-date-sep">/</span>
            <div>
                <input type="text" name="dm" class="ca-date-input" value="<?php print $dm ?>" maxlength="2">
                <span class="ca-date-hint">mois</span>
            </div>
        </div>
        <div class="ca-date-row">
            <span class="ca-date-label">Fin :</span>
            <span style="font-size:12px;color:#555">Le</span>
            <div>
                <input type="text" name="fj" class="ca-date-input" value="<?php print $fj ?>" maxlength="2">
                <span class="ca-date-hint">jour</span>
            </div>
            <span class="ca-date-sep">/</span>
            <div>
                <input type="text" name="fm" class="ca-date-input" value="<?php print $fm ?>" maxlength="2">
                <span class="ca-date-hint">mois</span>
            </div>
        </div>
        <script>buttonMagicSubmit('<?php print VALIDER ?>','okannee','ok')</script>
    </form>
</div>

<!-- Mode été -->
<div class="ca-card">
    <div class="ca-card-title"><span class="ca-card-icon">&#9728;</span> Mode été</div>
    <div class="ca-summer-status <?php print $modeEte ? 'on' : 'off' ?>">
        <?php print $modeEte ? '&#9888; Activé — accès restreint' : '&#10003; Désactivé — accès normal' ?>
    </div>
    <div class="ca-summer-desc">
        En mode été, tous les comptes n'ont plus accès à Triade sauf les comptes Direction.
    </div>
    <form method="post">
        <?php if ($modeEte): ?>
        <script>buttonMagicSubmit('D&eacute;sactiver','koete','ok')</script>
        <?php else: ?>
        <script>buttonMagicSubmit('<?php print VALIDER ?>','okete','ok')</script>
        <?php endif; ?>
    </form>
</div>

</div><!-- /.ca-stack -->

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
