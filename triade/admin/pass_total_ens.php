<?php
session_start();
error_reporting(0);
if (empty($_SESSION["admin1"])) {
    print "<script>location.href='./acces_refuse.php'</script>"; exit;
}
include_once("../common/lib_admin.php");
include_once("../common/config.inc.php");
include_once("../common/lib_ecole.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
$cnx = cnx();

$done    = false;
$message = "";

if (isset($_POST["create"])) {
    $emailenvoie = $_POST["emailenvoie"] ?? "";
    if ($_POST["initia"] == "0") {
        initialisePasswordEnseignant($emailenvoie);
        $message = "Les mots de passe sont réinitialisés.";
    } elseif ($_POST["initia"] == "1") {
        initialisePasswordDefinieEnseignant($_POST["passedef"], $emailenvoie);
        $message = "Les mots de passe sont réinitialisés.";
    }
    history_cmdAdmin("Admin Triade", "MODIF", "Réinitialisation mot de passe Enseignant");
    $done = true;
}
Pgclose($cnx);
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<style>
* { box-sizing:border-box; }
body { margin:0; padding:12px; background:#f0f2fa; font-family:Electrolize,Arial,sans-serif; }
.pw-wrap { max-width:460px; margin:0 auto; display:flex; flex-direction:column; gap:8px; }
.pw-radio-group { display:flex; flex-direction:column; gap:6px; }
.pw-radio-group label { display:flex; align-items:center; gap:7px; font-size:12px; cursor:pointer; padding:5px 8px; border-radius:6px; border:1px solid #dde0f0; }
.pw-radio-group label:has(input:checked) { background:#e8ebff; border-color:#080A66; font-weight:600; }
</style>
<title>Triade — Mots de passe enseignants</title>
</HEAD>
<body>
<div class="pw-wrap">

<?php if ($done): ?>
  <div class="card">
    <div class="card-header card-header-primary"><span><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>Résultat</span></div>
    <div class="card-body">
      <p style="color:#2e7d32;font-weight:700;"><i class="bi bi-check-circle-fill"></i> <?php print htmlspecialchars($message) ?></p>
      <?php if (file_exists("../data/fic_pass.txt")): ?>
      <div style="margin-top:10px;">
        <button type="button" class="btn btn-primary" onclick="open('recupepwens.php','_blank','')">
          <i class="bi bi-download" style="margin-right:5px;"></i>Récupérer les mots de passe
        </button>
      </div>
      <?php endif; ?>
      <div style="margin-top:12px;">
        <script language="JavaScript">buttonMagicFermeture();</script>
      </div>
    </div>
  </div>

<?php else: ?>

  <div class="card">
    <div class="card-header card-header-primary"><span><i class="bi bi-person-workspace" style="margin-right:6px;"></i>Réinitialisation — enseignants</span></div>
    <div class="card-body">
      <form name="formulaire" method="post" enctype="multipart/form-data"
            onsubmit="document.formulaire.rien.disabled=true">

        <?php if ((LAN == "oui") && ValideMail(MAILREPLY)): ?>
        <div style="margin-bottom:10px;">
          <label style="display:flex;align-items:center;gap:7px;font-size:12px;cursor:pointer;">
            <input type="checkbox" name="emailenvoie" value="1" onclick="envoiMailP()">
            Envoyer un email avec le nouveau mot de passe
          </label>
          <div id="infourl" style="display:none;" class="alert alert-danger" style="margin-top:6px;font-size:11px;">
            <i class="bi bi-exclamation-triangle-fill"></i> Vérifiez l'adresse internet du site Triade dans Config. Général avant validation !
          </div>
        </div>
        <?php endif; ?>

        <div class="pw-radio-group">
          <label>
            <input type="radio" name="initia" value="0" checked
              onclick="document.getElementById('zone-def').style.display='none';">
            Mot de passe aléatoire
          </label>
          <label>
            <input type="radio" name="initia" value="1"
              onclick="document.getElementById('zone-def').style.display='block';">
            Mot de passe défini
          </label>
        </div>
        <div id="zone-def" style="display:none;margin-top:8px;padding:8px 10px;background:#f5f7ff;border-radius:6px;border:1px solid #dde0f0;font-size:12px;">
          Mot de passe : <input type="text" name="passedef" class="bouton2" style="width:160px;">
        </div>

        <div style="margin-top:14px;display:flex;gap:8px;">
          <script language="JavaScript">buttonMagicSubmit("Confirmer","rien");</script>
          <script language="JavaScript">buttonMagicFermeture();</script>
        </div>
        <input type="hidden" name="create">
      </form>
    </div>
  </div>

<?php endif; ?>

</div>
<script>
function envoiMailP() {
    var el = document.getElementById('infourl');
    el.style.display = (el.style.display === 'none') ? 'block' : 'none';
}
</script>
</body>
</HTML>
