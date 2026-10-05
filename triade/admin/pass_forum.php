<?php
session_start();
if (empty($_SESSION["admin1"])) {
    print "<script>location.href='./acces_refuse.php'</script>"; exit;
}
error_reporting(0);
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");

$erreur = "";
$modif  = "oui";

if (isset($_POST["create"])) {
    include_once("../common/mdep.php");
    if (crypt(md5($_POST["saisie_ancien"]), "T2") == $MDP && $_POST["saisie_new"] == $_POST["saisie_renew"]) {
        $mp      = crypt(md5(trim(strtolower($_POST["saisie_new"]))), "T2");
        $fichier = fopen("../common/mdep.php", "w");
        fwrite($fichier, "<?php\n\$MDP=\"$mp\";\n?>\n");
        fclose($fichier);
        $cnx = cnx();
        history_cmd("Admin Triade", "MODIF", "Mot de passe Forum");
        Pgclose($cnx);
        $modif = "non";
    } else {
        $erreur = "error";
    }
}
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
<style>* { box-sizing:border-box; } body { margin:0; padding:12px; background:#f0f2fa; font-family:Electrolize,Arial,sans-serif; } .pw-wrap { max-width:380px; margin:0 auto; }</style>
<title>Triade — Mot de passe forum</title>
</HEAD>
<body>
<div class="pw-wrap">
<?php if ($modif == "non"): ?>
  <div class="card">
    <div class="card-header card-header-primary"><span><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>Mot de passe modifié</span></div>
    <div class="card-body" style="text-align:center;">
      <p style="color:#2e7d32;font-weight:700;margin-bottom:14px;"><i class="bi bi-check-circle-fill"></i> Mot de passe forum enregistré avec succès.</p>
      <script language="JavaScript">buttonMagicFermeture();</script>
    </div>
  </div>
<?php else: ?>
  <div class="card">
    <div class="card-header card-header-primary"><span><i class="bi bi-chat-dots-fill" style="margin-right:6px;"></i>Modifier le mot de passe du forum</span></div>
    <div class="card-body">
      <?php if ($erreur): ?>
      <div class="alert alert-danger" style="margin-bottom:10px;font-size:12px;"><i class="bi bi-exclamation-triangle-fill"></i> Mot de passe incorrect ou confirmation ne correspond pas.</div>
      <?php endif; ?>
      <form name="formulaire" method="post">
        <div class="form-row">
          <label class="form-lbl">Ancien mot de passe :</label>
          <input type="password" name="saisie_ancien" class="bouton2" autofocus>
        </div>
        <div class="form-row" style="margin-top:6px;">
          <label class="form-lbl">Nouveau mot de passe :</label>
          <input type="password" name="saisie_new" class="bouton2">
        </div>
        <div class="form-row" style="margin-top:6px;">
          <label class="form-lbl">Confirmer :</label>
          <input type="password" name="saisie_renew" class="bouton2">
        </div>
        <div style="margin-top:12px;display:flex;gap:8px;">
          <script language="JavaScript">buttonMagicSubmit("Enregistrer","create");</script>
          <script language="JavaScript">buttonMagicFermeture();</script>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>
</div>
</body>
</HTML>
