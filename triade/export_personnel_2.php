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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");

$nbordre    = countTriade($_POST['liste']);
$nbcolplus  = (int)$_POST['nbcolplus'];
$saisie_type = isset($_POST['saisie_type']) ? $_POST['saisie_type'] : '';

$tab = isset($_POST['liste']) ? $_POST['liste'] : [];
if ($nbcolplus > 0) {
    for ($i = 0; $i < $nbcolplus; $i++) {
        $tab[] = "$i";
        $nbordre++;
    }
}

$labelMap = [
    'nom'            => 'Nom',
    'prenom'         => 'Prénom',
    'civ_1'          => 'Civilité',
    'adr1'           => 'Adresse',
    'code_post_adr1' => 'Code postal',
    'commune_adr1'   => 'Commune',
    'tel_port_1'     => 'Tél. portable',
    'telephone'      => 'Téléphone',
    'identifiant'    => 'Identifiant',
    'indice_salaire' => 'Indice salaire',
    'code_barre'     => 'Code barre',
    'email'          => 'Email',
];

$items = [];
$liste = '';
foreach ($tab as $value) {
    $label = isset($labelMap[$value]) ? $labelMap[$value] : null;
    $items[] = ['value' => $value, 'label' => $label];
    $liste .= $value.'%##%';
}
$liste = preg_replace('/%##%$/', '', $liste);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>Exportation du personnel — Ordre des colonnes</font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

<?php if (!empty($items)): ?>
<form method="post" action="export_personnel_3.php">
  <input type="hidden" name="liste" value="<?php print htmlspecialchars($liste) ?>">
  <input type="hidden" name="saisie_type" value="<?php print htmlspecialchars($saisie_type) ?>">

  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#080A66">
      <i class="bi bi-sort-numeric-down"></i> Définir l'ordre des colonnes dans le fichier Excel
    </div>
    <div class="card-body" style="padding:10px 14px">
      <p style="font-size:11px;color:#888;margin:0 0 10px;font-style:italic">Sélectionnez le numéro de position pour chaque colonne.</p>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px">
        <?php foreach ($items as $item):
          $isCustom = ($item['label'] === null);
        ?>
        <div style="display:flex;align-items:center;gap:6px;padding:6px 8px;background:#f5f7ff;border:1px solid #e4e9f8;border-radius:5px;font-size:11px">
          <select name="ordre[]" style="border:1px solid #c5cae9;border-radius:4px;padding:2px 4px;font-size:11px;flex-shrink:0">
            <option value="">N°</option>
            <?php for ($i = 1; $i <= $nbordre; $i++) print "<option value='$i'>$i</option>"; ?>
          </select>
          <?php if ($isCustom): ?>
          <input type="text" name="nbcolname[]" placeholder="Nom colonne" style="border:1px solid #c5cae9;border-radius:4px;padding:2px 6px;font-size:11px;flex:1;min-width:0">
          <?php else: ?>
          <span style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php print htmlspecialchars($item['label']) ?></span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div style="display:flex;gap:8px;padding:4px 0">
    <button type="submit" name="create" class="btn btn-primary">
      Générer le fichier <i class="bi bi-file-earmark-excel"></i>
    </button>
    <script language=JavaScript>buttonMagicRetour("export_personnel.php","_self")</script>
  </div>

</form>
<?php else: ?>
<div style="background:#fff3e0;border:1px solid #ffcc80;border-radius:6px;padding:10px 14px;font-size:12px;color:#e65100">
  <i class="bi bi-exclamation-triangle"></i> Aucune colonne sélectionnée. <a href="export_personnel.php" style="color:#080A66">Retour</a>
</div>
<?php endif; ?>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
