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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();

$nvalide = "create";
$valider = VALIDER;
$nomEvaluation   = "";
$proxEvaluation  = "";
$checkedEval1    = "";
$checkedEval2    = "";
$idEvaluation    = "";
$okEnr = false; $okSupp = false; $okMod = false;

if (isset($_POST["modif"])) {
    $nvalide = "modifinfo";
    $valider = "Modifier";
    $data           = affEvalHoraireMotif($_POST["evaluation2"]);
    $idEvaluation   = $data[0][0];
    $nomEvaluation  = $data[0][1];
    $proxEvaluation = $data[0][2];
    if ($data[0][3] == "cours") { $checkedEval1 = "checked='checked'"; }
    if ($data[0][3] == "eval")  { $checkedEval2 = "checked='checked'"; }
}

if (isset($_POST["create"]))   { enrEvalHoraire($_POST["saisie_evaluation"], $_POST["saisie_basehoraire"], $_POST["type_eval"]); $okEnr = true; }
if (isset($_POST["supp"]))     { suppEvalHoraire($_POST["evaluation"]); $okSupp = true; }
if (isset($_POST["modifinfo"])) {
    $cr = modifEvalHoraire($_POST["idEval"], $_POST["saisie_evaluation"], $_POST["saisie_basehoraire"], $_POST["type_eval"]);
    if ($cr) { $okMod = true; }
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des prestations</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post">
<div class="form-row" style="padding:12px 16px 6px">
  <label class="form-lbl">Nom de la prestation :</label>
  <input type="text" name="saisie_evaluation" size="40" maxlength="40" value="<?php print $nomEvaluation ?>" class="form-ctrl">
</div>
<div class="form-row" style="padding:6px 16px">
  <label class="form-lbl">Taux horaire <i>(HT)</i> :</label>
  <input type="text" name="saisie_basehoraire" size="5" value="<?php print $proxEvaluation ?>" class="form-ctrl" style="width:80px">
</div>
<div class="form-row" style="padding:6px 16px 10px">
  <label class="form-lbl">Type :</label>
  <span>
    cours <input type="radio" name="type_eval" value="cours" <?php print $checkedEval1 ?>>
    &nbsp;&nbsp; évaluation <input type="radio" name="type_eval" value="eval" <?php print $checkedEval2 ?>>
  </span>
</div>
<div class="toolbar" style="padding:10px 16px;border-top:1px solid #e8eaf6">
  <script language="JavaScript">buttonMagicSubmit("<?php print $valider ?>","<?php print $nvalide ?>");</script>
</div>
<input type="hidden" name="idEval" value="<?php print $idEvaluation ?>">
</form>

<div style="border-top:2px solid #c5cae9;margin:8px 4px 4px"></div>

<form method="post">
<div class="form-row" style="padding:10px 16px">
  <label class="form-lbl">Supprimer une prestation :</label>
  <select name="evaluation">
    <option><?php print LANGCHOIX ?></option>
    <?php select_EvalHoraire(); ?>
  </select>
  &nbsp;<input type="submit" name="supp" value="Ok" class="bouton2">
</div>
</form>

<form method="post">
<div class="form-row" style="padding:6px 16px 12px">
  <label class="form-lbl">Modifier une prestation :</label>
  <select name="evaluation2">
    <option><?php print LANGCHOIX ?></option>
    <?php select_EvalHoraire(); ?>
  </select>
  &nbsp;<input type="submit" name="modif" value="Ok" class="bouton2">
</div>
</form>

<div class="toolbar" style="padding:10px 16px;border-top:1px solid #e8eaf6">
  <script language="JavaScript">buttonMagicRetour("gestion_vacation.php","_parent")</script>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?>></SCRIPT>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script>
document.addEventListener('DOMContentLoaded', function() {
  <?php if ($okEnr):  ?>alertify.success("Prestation enregistrée.");<?php endif; ?>
  <?php if ($okSupp): ?>alertify.success("Prestation supprimée.");<?php endif; ?>
  <?php if ($okMod):  ?>alertify.success("<?php print LANGDONENR ?>");<?php endif; ?>
});
</script>
<?php Pgclose(); ?>
</BODY></HTML>
