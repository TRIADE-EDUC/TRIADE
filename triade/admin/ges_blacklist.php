<?php
session_start();
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="UTF-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<title>Triade — Gestion Black-List</title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion Black-List</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php
$cnx = cnx();

if (isset($_GET["supp"])) {
    blacklistsupp($_GET["supp"]);
}

$data = listeblacklistetotal();

$membreLabels = [
    'menuprof'      => 'Enseignant',
    'menuscolaire'  => 'Vie scolaire',
    'menueleve'     => 'Élève',
    'menuadmin'     => 'Direction',
    'menuparent'    => 'Parent',
];
$membreBadge = [
    'menuprof'      => 'badge-warning',
    'menuscolaire'  => 'badge-info',
    'menueleve'     => 'badge-success',
    'menuadmin'     => 'badge-danger',
    'menuparent'    => 'badge-secondary',
];
?>

<div class="alert alert-danger" style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
  <i class="bi bi-shield-fill-exclamation" style="font-size:20px;flex-shrink:0;"></i>
  <span>Liste des personnes ayant tenté d'accéder à un service non autorisé. <strong>Tentative de piratage !</strong> Les comptes ci-dessous sont bloqués.</span>
</div>

<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-ban" style="margin-right:6px;"></i>Comptes bloqués
      <span class="card-badge"><?php print count($data) ?> entrée<?php print count($data) > 1 ? 's' : '' ?></span>
    </span>
  </div>
  <?php if (count($data) === 0): ?>
  <div class="card-body" style="color:#2e7d32;font-style:italic;">
    <i class="bi bi-check-circle" style="margin-right:6px;"></i>Aucune entrée dans la liste noire.
  </div>
  <?php else: ?>
  <table class="table" style="margin:0;">
    <thead>
      <tr>
        <th class="cc-th">Identité</th>
        <th class="cc-th">Tentatives</th>
        <th class="cc-th">IP / Fichier</th>
        <th class="cc-th">Dernière tentative</th>
        <th class="cc-th" style="width:80px;"></th>
      </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < count($data); $i++):
        $typeLabel  = $membreLabels[$data[$i][7]] ?? $data[$i][7];
        $typeBadge  = $membreBadge[$data[$i][7]]  ?? 'badge-secondary';
        $suppUrl    = 'ges_blacklist.php?supp=' . $data[$i][0];
    ?>
      <tr class="cc-tr-data">
        <td>
          <span style="color:#c62828;font-weight:700;font-size:13px;">
            <?php print strtoupper($data[$i][1]) ?> <?php print ucwords($data[$i][2]) ?>
          </span><br>
          <span class="badge <?php print $typeBadge ?>" style="margin-top:3px;"><?php print $typeLabel ?></span>
        </td>
        <td style="text-align:center;">
          <span class="badge badge-danger" style="font-size:13px;"><?php print $data[$i][5] ?></span>
        </td>
        <td style="font-size:11px;">
          <span style="font-family:monospace;color:#555;"><?php print htmlspecialchars($data[$i][4]) ?></span><br>
          <i style="color:#888;"><?php print htmlspecialchars($data[$i][6]) ?></i>
        </td>
        <td style="font-size:12px;"><?php print dateForm($data[$i][3]) ?></td>
        <td style="text-align:center;">
          <a href="<?php print $suppUrl ?>" class="btn btn-danger"
             style="font-size:11px;padding:4px 10px;"
             onclick="return confirm('Supprimer cette entrée de la liste noire ?')">
            <i class="bi bi-trash"></i> Supprimer
          </a>
        </td>
      </tr>
    <?php endfor; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
