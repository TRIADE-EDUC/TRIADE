<?php
session_start();
error_reporting(0);
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../librairie_php/timezone.php");

$msgHoraire = "";
$msgType    = "";

if (isset($_POST["rien"])) {
    if (isset($_POST["acceseleve"]) && $_POST["acceseleve"] == 1) {
        touch("../data/parametrage/noacces.eleve");
    } else {
        @unlink("../data/parametrage/noacces.eleve");
    }
    if (isset($_POST["accesparent"]) && $_POST["accesparent"] == 1) {
        touch("../data/parametrage/noacces.parent");
    } else {
        @unlink("../data/parametrage/noacces.parent");
    }
}

if (isset($_POST["justifie"])) {
    $text = nl2br(htmlentities($_POST["com"]));
    if (trim($_POST["com"]) == "") {
        @unlink("../data/parametrage/acces.commentaire");
    } else {
        $fp = fopen("../data/parametrage/acces.commentaire", "w");
        fwrite($fp, $text);
        fclose($fp);
    }
}

$cnx = cnx();

if (isset($_POST['horaire'])) {
    $debut_acces = $_POST['debut_acces'];
    $fin_acces   = $_POST['fin_acces'];
    if (isValidTime($debut_acces) && isValidTime($fin_acces)) {
        enr_parametrage("debut_acces", $debut_acces, dateDMY());
        enr_parametrage("fin_acces",   $fin_acces,   dateDMY());
        $msgHoraire = "Plage horaire enregistrée.";
        $msgType    = "success";
    } else {
        $msgHoraire = "Erreur de saisie : format hh:mm attendu.";
        $msgType    = "error";
    }
}

if (isset($_POST['horairesupp'])) {
    supp_parametrage("debut_acces");
    supp_parametrage("fin_acces");
    $msgHoraire = "Restriction horaire supprimée.";
    $msgType    = "success";
}

// Lire états actuels
$chekparentnon = file_exists("../data/parametrage/noacces.parent") ? "checked" : "";
$chekparentoui = $chekparentnon ? "" : "checked";
$chekelevenon  = file_exists("../data/parametrage/noacces.eleve")  ? "checked" : "";
$chekeleveoui  = $chekelevenon  ? "" : "checked";

$donne = "";
if (file_exists("../data/parametrage/acces.commentaire")) {
    $fp    = fopen("../data/parametrage/acces.commentaire", "r");
    $donne = fread($fp, 9000000);
    $donne = preg_replace("/\&lt;br \/&gt;/", "", $donne);
    fclose($fp);
}

$data1       = aff_structure("debut_acces");
$data2       = aff_structure("fin_acces");
$debut_acces = $data1[0][1] ?? "";
$fin_acces   = $data2[0][1] ?? "";
$info        = $data2[0][2] ?? "";
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<style>
.acces-toggle { display:flex; align-items:center; gap:16px; }
.acces-toggle-label { font-size:12px; font-weight:600; color:#080A66; min-width:100px; }
.acces-radio-group { display:flex; gap:10px; }
.acces-radio-group label { display:flex; align-items:center; gap:5px; font-size:12px; cursor:pointer; padding:5px 12px; border-radius:6px; border:1px solid #dde0f0; transition:background .15s; }
.acces-radio-group input[type=radio] { accent-color:#080A66; }
.acces-radio-group label:has(input:checked) { background:#e8ebff; border-color:#080A66; font-weight:700; }
.acces-status-on  { color:#2e7d32; font-weight:700; font-size:11px; }
.acces-status-off { color:#c62828; font-weight:700; font-size:11px; }
</style>
<title>Triade — Contrôle d'accès</title>
</HEAD>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Contrôle d'accès</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">

<div style="display:flex;flex-direction:column;gap:8px;">

<!-- Section 1 : Ouverture / fermeture accès -->
<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-door-open-fill" style="margin-right:6px;"></i>Ouverture des accès</span>
  </div>
  <div class="card-body">
    <form method="post">
      <div style="display:flex;flex-direction:column;gap:10px;">

        <div class="acces-toggle">
          <span class="acces-toggle-label">Accès <?php print INTITULEELEVES ?> :</span>
          <div class="acces-radio-group">
            <label><input type="radio" name="acceseleve" value="0" <?php print $chekeleveoui ?>> Ouvert</label>
            <label><input type="radio" name="acceseleve" value="1" <?php print $chekelevenon ?>> Fermé</label>
          </div>
          <?php if ($chekelevenon): ?>
            <span class="acces-status-off"><i class="bi bi-lock-fill"></i> Accès bloqué</span>
          <?php else: ?>
            <span class="acces-status-on"><i class="bi bi-unlock-fill"></i> Accès ouvert</span>
          <?php endif; ?>
        </div>

        <div class="acces-toggle">
          <span class="acces-toggle-label">Accès parents :</span>
          <div class="acces-radio-group">
            <label><input type="radio" name="accesparent" value="0" <?php print $chekparentoui ?>> Ouvert</label>
            <label><input type="radio" name="accesparent" value="1" <?php print $chekparentnon ?>> Fermé</label>
          </div>
          <?php if ($chekparentnon): ?>
            <span class="acces-status-off"><i class="bi bi-lock-fill"></i> Accès bloqué</span>
          <?php else: ?>
            <span class="acces-status-on"><i class="bi bi-unlock-fill"></i> Accès ouvert</span>
          <?php endif; ?>
        </div>

      </div>
      <div style="margin-top:12px;">
        <script language="JavaScript">buttonMagicSubmit("Valider","rien");</script>
      </div>
    </form>
  </div>
</div>

<!-- Section 2 : Message de justification -->
<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-chat-text-fill" style="margin-right:6px;"></i>Message de justification</span>
  </div>
  <div class="card-body">
    <p style="font-size:11px;color:#666;margin:0 0 8px;">Message affiché aux utilisateurs lors d'un accès bloqué.</p>
    <form method="post">
      <textarea name="com" rows="5"
        style="width:100%;border:1px solid #dde0f0;border-radius:6px;padding:8px;font-size:12px;font-family:Electrolize,Arial,sans-serif;resize:vertical;box-sizing:border-box;"
      ><?php print stripslashes($donne) ?></textarea>
      <div style="margin-top:8px;">
        <script language="JavaScript">buttonMagicSubmit("Enregistrer","justifie");</script>
      </div>
    </form>
  </div>
</div>

<!-- Section 3 : Restriction horaire -->
<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-clock-fill" style="margin-right:6px;"></i>Restriction horaire <?php print INTITULEELEVES ?></span>
  </div>
  <div class="card-body">
    <p style="font-size:11px;color:#666;margin:0 0 10px;">
      Limite l'accès à Triade pour les <?php print INTITULEELEVES ?> à une plage horaire définie.
      <?php if ($debut_acces && $fin_acces): ?>
        <span style="color:#080A66;font-weight:700;">Actuelle : <?php print $debut_acces ?> → <?php print $fin_acces ?></span>
        <?php if ($info): ?>
          <span style="color:#888;font-style:italic;"> (enregistré le <?php print $info ?>)</span>
        <?php endif; ?>
      <?php else: ?>
        <span style="color:#888;font-style:italic;">Aucune restriction active.</span>
      <?php endif; ?>
    </p>
    <form method="post">
      <div class="form-row" style="align-items:center;">
        <label class="form-lbl">De :</label>
        <input type="text" name="debut_acces" value="<?php print htmlspecialchars($debut_acces) ?>"
               size="6" placeholder="hh:mm" class="bouton2" style="width:70px;text-align:center;" required>
        <label class="form-lbl" style="margin-left:10px;">à :</label>
        <input type="text" name="fin_acces" value="<?php print htmlspecialchars($fin_acces) ?>"
               size="6" placeholder="hh:mm" class="bouton2" style="width:70px;text-align:center;" required>
      </div>
      <div style="margin-top:10px;display:flex;gap:8px;">
        <script language="JavaScript">buttonMagicSubmit("Enregistrer","horaire");</script>
        <script language="JavaScript">buttonMagicSubmit("Supprimer","horairesupp");</script>
      </div>
    </form>
  </div>
</div>

</div><!-- /flex column -->

<?php if ($msgHoraire): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    alertify.<?php print $msgType ?>('<?php print addslashes($msgHoraire) ?>');
});
</script>
<?php endif; ?>

<?php if (isset($_POST["rien"]) || isset($_POST["justifie"])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    alertify.success('Paramètre enregistré.');
});
</script>
<?php endif; ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
