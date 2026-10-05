<?php
session_start();
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
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<title>Triade — Mot de passe Intra-MSN</title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Mot de passe Intra-MSN</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php
$saved = false;
if (isset($_POST["create"])) {
    include_once("./librairie_php/db_triade_admin.php");
    $cnx = cnx();
    modifPasseAdminIntraMSN($_POST["pwd"]);
    Pgclose($cnx);
    $saved = true;
}
?>

<?php if ($saved): ?>
<div class="card">
  <div class="card-body" style="text-align:center;">
    <p style="color:#2e7d32;font-weight:700;"><i class="bi bi-check-circle-fill"></i> Mot de passe Intra-MSN initialisé.</p>
  </div>
</div>
<?php else: ?>
<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-chat-square-dots-fill" style="margin-right:6px;"></i>Mot de passe Intra-MSN</span>
  </div>
  <div class="card-body">
    <form method="post">
      <div class="form-row">
        <label class="form-lbl">Login :</label>
        <span style="font-size:12px;color:#555;font-style:italic;">administrateur</span>
      </div>
      <div class="form-row" style="margin-top:6px;">
        <label class="form-lbl">Mot de passe :</label>
        <input type="text" name="pwd" class="bouton2">
      </div>
      <div style="margin-top:12px;">
        <input type="submit" value="Enregistrer" class="btn btn-primary" name="create">
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php if ($saved): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    alertify.success('Mot de passe Intra-MSN enregistré.');
});
</script>
<?php endif; ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
