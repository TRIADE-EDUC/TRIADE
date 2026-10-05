<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onunload="attente_close()">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><i class="bi bi-gear" style="margin-right:6px"></i>Configuration Passage CDI</font></b></td></tr>
<?php
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx = cnx();

$okenr = false;
$oksupp = false;

if (isset($_POST['create1'])) {
    enr_config_creneau_cdi($_POST['creneau']);
    $okenr = true;
}
if (isset($_GET['ide'])) {
    suppConfigCreneau($_GET['ide']);
    $oksupp = true;
}

$data = recupPassageCreneauCDI();
?>
<tr id='cadreCentral0'>
<td>

<div style="max-width:600px;margin:12px auto;display:flex;flex-direction:column;gap:12px">

<!-- ── Formulaire ajout ─────────────────────────────────────────────────── -->
<div class="card">
  <div class="card-header card-header-primary">
    <i class="bi bi-plus-circle"></i> Ajouter un type de passage
  </div>
  <div class="card-body">
    <form method="post" action="passagecdi_config.php">
      <div class="form-row" style="align-items:center;gap:10px;flex-wrap:wrap">
        <label class="form-lbl" style="white-space:nowrap">Libellé :</label>
        <input type="text" name="creneau" class="form-ctrl" style="flex:1;min-width:180px" placeholder="Ex : Recherche documentaire" autocomplete="off">
        <button type="submit" name="create1" class="btn-enr"><i class="bi bi-check-lg"></i> <?php print VALIDER ?></button>
      </div>
    </form>
  </div>
</div>

<!-- ── Liste des types de passage ──────────────────────────────────────── -->
<div class="card">
  <div class="card-header card-header-primary">
    <i class="bi bi-list-ul"></i> Types de passage configurés
    <span class="badge" style="margin-left:auto"><?php print countTriade($data) ?></span>
  </div>
  <?php if (countTriade($data) > 0): ?>
  <div style="overflow-x:auto">
  <table style="width:100%;border-collapse:collapse;font-size:13px;font-family:Electrolize,'Trebuchet MS',Arial">
    <thead><tr>
      <th class="cc-th">Libellé</th>
      <th class="cc-th" style="width:1%"></th>
    </tr></thead>
    <tbody>
    <?php for ($i = 0; $i < countTriade($data); $i++):
        $id = $data[$i][0]; ?>
    <tr class="cc-tr-data">
      <td style="padding:8px 12px"><?php print htmlspecialchars($data[$i][1]) ?></td>
      <td style="padding:6px 10px;white-space:nowrap">
        <button type="button"
          class="btn-enr" style="background:#e53935;border-color:#e53935;font-size:11px;padding:4px 10px"
          onclick="if(confirm('Supprimer ce type de passage ?')) location.href='passagecdi_config.php?ide=<?php print $id ?>'">
          <i class="bi bi-trash"></i> Supprimer
        </button>
      </td>
    </tr>
    <?php endfor; ?>
    </tbody>
  </table>
  </div>
  <?php else: ?>
  <div class="card-body" style="color:#888;font-size:13px;font-style:italic">
    Aucun type de passage configuré.
  </div>
  <?php endif; ?>
</div>

<!-- ── Toolbar retour ───────────────────────────────────────────────────── -->
<div class="toolbar">
  <script language="JavaScript">buttonMagicRetour('passagecdi.php','_self')</script>
</div>

</div>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else:
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
<?php if ($okenr): ?>
    alertify.success("Type de passage enregistré.");
<?php elseif ($oksupp): ?>
    alertify.success("Type de passage supprimé.");
<?php endif; ?>
});
</script>
</body>
</html>
