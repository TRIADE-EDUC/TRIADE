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
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");

if ($_SESSION["membre"] == "menuscolaire") {
    if (MODULEPREINSCRIPTIONVIESCOLAIRE != "oui") validerequete("menuadmin");
} else {
    validerequete("menuadmin");
}

$msgSucces = "";

if (isset($_POST["suppfiche"])) {
    suppfichepreinscription($_POST["id_eleve"]);
    $msgSucces = "Fiche de pré-inscription supprimée.";
}

if (isset($_POST["inscription"])) {
    foreach ($_POST["listing"] as $value) {
        transferePreinscription($value);
    }
}

if (isset($_POST["deletefiche"])) {
    foreach ($_POST["listing"] as $value) {
        suppfichepreinscription($value);
    }
}

$data    = listingPreinscription($_POST["saisie_classe"] ?? '', $_POST["saisie_filtre"] ?? '', $_POST["annee_scolaire"] ?? '');
$nbliste = countTriade($data);
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<title>Vie Scolaire - Triade - Compte de <?php echo $_SESSION['nom'].' '.$_SESSION['prenom']; ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php if ($msgSucces): ?>
<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('<?php echo addslashes($msgSucces); ?>'); });</script>
<?php endif; ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<!-- ── Section navigation ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGMESS335; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="ca-nav-card">
  <div class="ca-nav-actions">
    <script language="JavaScript">buttonMagic("<?php echo LANGTMESS455 ?>","preinscription_direction.php","_parent","","");</script>
  </div>
</div>
</td></tr></table>

<br>

<!-- ── Section liste ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGMESS335; ?></font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<!-- Filtres -->
<form method="post" action="listepreinscription.php" style="margin:0 8px 10px;">
<div style="background:#f5f7ff;border:1px solid #c5cae9;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
  <span style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;"><?php echo LANGMESS347; ?> :</span>
  <select id="saisie_classe" name="saisie_classe" class="ca-select">
    <?php
    if (isset($_POST["saisie_classe"])) {
        if ($_POST["saisie_classe"] > 0) {
            echo "<option value='".$_POST["saisie_classe"]."'>".trunchaine(chercheClasse_nom($_POST["saisie_classe"]), 15)."</option>";
        } else {
            echo "<option value='Tous'>Tous</option>";
        }
    }
    ?>
    <option value=""><?php echo LANGCHOIX; ?></option>
    <option value="Tous"><?php echo LANGTOUS; ?></option>
    <?php select_classe2(15); ?>
  </select>
  <select id="filtre" name="saisie_filtre" class="ca-select">
    <?php if (isset($_POST["saisie_filtre"])): ?>
      <option value="<?php echo htmlspecialchars($_POST["saisie_filtre"]); ?>"><?php echo htmlspecialchars($_POST["saisie_filtre"]); ?></option>
    <?php endif; ?>
    <option value="Tous"><?php echo LANGTOUS; ?></option>
    <option value="En attente"><?php echo LANGTMESS456; ?></option>
    <option value="Accepté"><?php echo LANGTMESS457; ?></option>
    <option value="Réfusé"><?php echo LANGTMESS458; ?></option>
  </select>
  <span style="font-size:12px;color:#444;">Année :</span>
  <input type="text" size="4" maxlength="4" name="annee_scolaire"
         value="<?php echo htmlspecialchars($_POST["annee_scolaire"] ?? ''); ?>"
         class="ca-input" style="max-width:55px;">
  <script language="JavaScript">buttonMagicSubmit("<?php echo LANGBT28 ?>","rien");</script>
</div>
</form>

<!-- Tableau liste -->
<div style="margin:0 8px 10px;">
<form method="post" name="form2" style="margin:0;">
<table class="cc-data-table">
  <thead>
    <tr class="cc-thead-row">
      <th class="cc-th cc-th-center" style="width:36px;">
        <input type="checkbox" onclick="validecase();" name="tous" value="1">
      </th>
      <th class="cc-th" style="width:90px;"><?php echo LANGAGENDA104; ?></th>
      <th class="cc-th" style="width:90px;"><?php echo LANGTMESS459; ?></th>
      <th class="cc-th"><?php echo LANGNA1.' '.LANGELE3; ?></th>
      <th class="cc-th"><?php echo LANGELE4; ?></th>
    </tr>
  </thead>
</table>
</form>

<form method="post" name="form1" style="margin:0;">
<table class="cc-data-table" style="border-top:none;">
  <tbody>
  <?php for ($i = 0; $i < $nbliste; $i++):
      $decision  = $data[$i][3];
      $annee     = $data[$i][6];
      $idEleve   = $data[$i][5];
      $nomPrenom = trunchaine(strtoupper($data[$i][0]).' '.ucwords($data[$i][1]), 30);
      $classeNom = chercheClasse_nom($data[$i][2]);
      if ($decision == "Accepté") {
          $decStyle = "color:#2e7d32;font-weight:700;";
      } elseif ($decision == "Refusé" || $decision == "Réfusé") {
          $decStyle = "color:#c0392b;font-weight:700;";
      } else {
          $decStyle = "color:#856404;";
      }
  ?>
    <tr id="tr<?php echo $i; ?>" class="cc-tr-data">
      <td class="cc-td cc-td-center" style="width:36px;">
        <input type="checkbox" name="listing[]" value="<?php echo $idEleve; ?>" onclick="DisplayLigne('tr<?php echo $i; ?>')">
      </td>
      <td class="cc-td" style="width:90px;font-size:11px;">
        <?php echo $annee; ?>&nbsp;-&nbsp;<?php echo $annee + 1; ?>
      </td>
      <td class="cc-td" style="width:90px;">
        <span style="<?php echo $decStyle; ?>font-size:11px;">
          <?php echo htmlspecialchars(preg_replace('/ /', '&nbsp;', $decision)); ?>
        </span>
        &nbsp;<a href="preinscriptiondetail.php?ideleve=<?php echo $idEleve; ?>">
          <img src="image/commun/b_edit.png" border="0" style="vertical-align:middle;">
        </a>
      </td>
      <td class="cc-td"><?php echo htmlspecialchars($nomPrenom); ?></td>
      <td class="cc-td">
        <a href="#" title="<?php echo htmlspecialchars($classeNom); ?>" style="color:#080A66;text-decoration:none;">
          <?php echo htmlspecialchars(trunchaine($classeNom, 10)); ?>
        </a>
      </td>
    </tr>
  <?php endfor; ?>
  </tbody>
</table>

<div style="margin-top:10px;display:flex;gap:10px;flex-wrap:wrap;">
  <script language="JavaScript">buttonMagicSubmit("<?php echo LANGTMESS460 ?>","inscription");</script>
  <script language="JavaScript">buttonMagicSubmit("<?php echo LANGTMESS461 ?>","deletefiche");</script>
</div>
</form>
</div>

</td></tr></table>

<script>
function validecase() {
    var nb = <?php echo $nbliste; ?>;
    var checked = document.form2.tous.checked;
    for (var i = 0; i < nb; i++) {
        document.form1.elements[i].checked = checked;
        DisplayLigne('tr' + i);
    }
}
</script>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
Pgclose();
?>
</body></html>
