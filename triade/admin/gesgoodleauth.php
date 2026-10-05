<?php
session_start();
error_reporting(0);
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");

$cnx = cnx();
$deletedPers  = false;
$deletedEleve = false;
if (isset($_POST['googlepers']))  { suppGoogleAuthenPers($_POST['googlepers']);    $deletedPers  = true; }
if (isset($_POST['googleeleve'])) { suppGoogleAuthenEleve($_POST['googleeleve']); $deletedEleve = true; }

$data      = listingPersGoogAuth();
$dataEleve = listingEtudiantGoogAuth();
Pgclose();
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
<title>Triade — Gestion Google Authenticator</title>
</HEAD>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion Google Authenticator</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">

<div class="alert alert-info" style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;">
  <i class="bi bi-phone-fill" style="font-size:18px;flex-shrink:0;margin-top:2px;"></i>
  <span>Sélectionnez un compte pour supprimer son authentification à deux facteurs Google Authenticator. Cette action ne supprime pas le compte, uniquement le token 2FA.</span>
</div>

<div style="display:flex;gap:8px;flex-wrap:wrap;">

  <!-- Personnels -->
  <div class="card" style="flex:1;min-width:220px;">
    <div class="card-header card-header-primary">
      <span><i class="bi bi-people-fill" style="margin-right:6px;"></i>Comptes personnels (<?php print count($data) ?>)</span>
    </div>
    <div class="card-body">
      <?php if (count($data) === 0): ?>
      <p style="color:#888;font-style:italic;font-size:12px;margin:0;">Aucun compte avec 2FA actif.</p>
      <?php else: ?>
      <form method="post" id="form-pers"
        onsubmit="if(document.getElementById('sel-pers').value==='0'){alertify.error('Veuillez sélectionner un compte.');return false;}return confirm('Supprimer l\'authentificator Google de ce compte ?');">
        <div class="form-row">
          <label class="form-lbl">Compte :</label>
          <select name="googlepers" id="sel-pers" class="cc-select">
            <option value="0">Choix...</option>
            <?php for ($i = 0; $i < count($data); $i++): ?>
            <option value="<?php print $data[$i][0] ?>">
              <?php print htmlspecialchars(strtoupper($data[$i][1]).' '.ucwords($data[$i][2])) ?>
            </option>
            <?php endfor; ?>
          </select>
        </div>
        <div style="margin-top:10px;">
          <script language="JavaScript">buttonMagicSubmit('Supprimer l\'authentificator','supppers');</script>
        </div>
      </form>
      <?php endif; ?>
    </div>
  </div>

  <!-- Étudiants -->
  <div class="card" style="flex:1;min-width:220px;">
    <div class="card-header card-header-primary">
      <span><i class="bi bi-mortarboard-fill" style="margin-right:6px;"></i>Comptes étudiants (<?php print count($dataEleve) ?>)</span>
    </div>
    <div class="card-body">
      <?php if (count($dataEleve) === 0): ?>
      <p style="color:#888;font-style:italic;font-size:12px;margin:0;">Aucun compte avec 2FA actif.</p>
      <?php else: ?>
      <form method="post" id="form-eleve"
        onsubmit="if(document.getElementById('sel-eleve').value==='0'){alertify.error('Veuillez sélectionner un compte.');return false;}return confirm('Supprimer l\'authentificator Google de ce compte ?');">
        <div class="form-row">
          <label class="form-lbl">Compte :</label>
          <select name="googleeleve" id="sel-eleve" class="cc-select">
            <option value="0">Choix...</option>
            <?php for ($i = 0; $i < count($dataEleve); $i++): ?>
            <option value="<?php print $dataEleve[$i][0] ?>">
              <?php print htmlspecialchars(strtoupper($dataEleve[$i][1]).' '.ucwords($dataEleve[$i][2])) ?>
            </option>
            <?php endfor; ?>
          </select>
        </div>
        <div style="margin-top:10px;">
          <script language="JavaScript">buttonMagicSubmit('Supprimer l\'authentificator','suppeleve');</script>
        </div>
      </form>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php if ($deletedPers || $deletedEleve): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    alertify.success('Authentificator Google supprimé.');
});
</script>
<?php endif; ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
