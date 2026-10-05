<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();
if (($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"resaressource") == 0)) {
    PgClose();
    header("Location: accespersonneldenied.php?titre=Module Gestion des ressources.");
}
if ($_SESSION["membre"] != "menupersonnel") { validerequete("2"); }
/***************************************************************************
 *                              T.R.I.A.D.E
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] " ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>

<?php
$libelle = "";
$id = "";
$info = "";
$okenr = false;
$okmod = false;
$bt = LANGRESA17;

if (isset($_POST["create"])) {
    $classenom = stripslashes($_POST["saisie_creat_classe"]);
    $info      = stripslashes($_POST["saisie_info"]);
    $id        = $_POST["idsalleequip"];
    if (!empty($classenom)) {
        if ($id == "") {
            $cr = create_equip($classenom, $info);
            if ($cr) $okenr = true;
        } else {
            $cr = modif_equip($classenom, $info, $id);
            if ($cr) $okmod = true;
        }
    }
    $info = "";
}

if (isset($_GET["id"])) {
    $data    = rechercheInfoSalleEquipement($_GET["id"]);
    $libelle = $data[0][1];
    $id      = $data[0][0];
    $info    = $data[0][2];
    $bt      = "Modifier équipement";
}

Pgclose();
?>

<form method="post" onsubmit="return verifcreatequip()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>
  <i class="bi bi-cpu" style="margin-right:6px"></i><?php print LANGRESA15 ?>
</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="max-width:520px;margin:12px auto">

<div class="card">
  <div class="card-body">

    <div class="form-row">
      <label class="form-lbl"><?php print LANGRESA16 ?> :</label>
      <input type="text" name="saisie_creat_classe" class="form-ctrl" maxlength="15"
        value="<?php print htmlspecialchars(stripslashes($libelle)) ?>" style="width:220px" autocomplete="off">
    </div>

    <div class="form-row" style="margin-top:10px">
      <label class="form-lbl"><?php print LANGRESA18 ?> :</label>
      <input type="text" name="saisie_info" class="form-ctrl"
        value="<?php print htmlspecialchars(stripslashes($info)) ?>" style="width:220px" autocomplete="off">
    </div>

    <div class="toolbar" style="margin-top:14px">
      <script language="JavaScript">buttonMagicSubmit("<?php print $bt ?>","create");</script>
      <script language="JavaScript">buttonMagicRetour("resr_admin.php","_parent")</script>
    </div>

  </div>
</div>

</div>

<input type="hidden" name="idsalleequip" value="<?php print $id ?>">
</td></tr></table>
</form>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?>></SCRIPT>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
<?php if ($okenr): ?>
    alertify.success(<?php echo json_encode(LANGRESA19); ?>);
<?php elseif ($okmod): ?>
    alertify.success("Équipement modifié.");
<?php endif; ?>
});
</script>
</BODY></HTML>
