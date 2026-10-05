<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
    $anneeScolaire=$_POST["anneeScolaire"];
    setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
/***************************************************************************
 *                              T.R.I.A.D.E
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./affectation_modif_key.php'</script>";
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'>
  <td height="2">
    <b><font id='menumodule1'>
      <i class="bi bi-people-fill" style="margin-right:6px"></i><?php print LANGTITRE19?>
    </font></b>
  </td>
</tr>
<tr id='cadreCentral0'>
<td>

<?php
if (isset($_GET["sClasseGrp"])) $sClasseGrp=$_GET["sClasseGrp"];
if (isset($_POST["sClasseGrp"])) $sClasseGrp=$_POST["sClasseGrp"];

$tri='tous';
include_once('librairie_php/db_triade.php');
if (isset($_POST["saisie_tri"])) {
    $libelle=libelleTrimestre($_POST["saisie_tri"]);
    $tri=$_POST["saisie_tri"];
    $anneeScolaire=$_POST["anneeScolaire"];
}
?>

<!-- ── Filtre ─────────────────────────────────────────────────────────────── -->
<div class="card" style="margin:8px 6px 10px">
<div class="card-body" style="padding:10px 12px">
<form method="post" action="modifaffect.php">
  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">

    <div class="form-row" style="flex:1;min-width:150px">
      <label class="form-label">
        <i class="bi bi-calendar3" style="color:#080A66"></i> Période
      </label>
      <select name="saisie_tri" class="cc-select">
        <?php if (isset($_POST["saisie_tri"])): ?>
        <option value="<?php print $_POST["saisie_tri"]; ?>"><?php print $libelle; ?></option>
        <?php endif; ?>
        <option value="tous">Toute l'année</option>
        <option value="trimestre1">Trimestre 1 / Semestre 1</option>
        <option value="trimestre2">Trimestre 2 / Semestre 2</option>
        <option value="trimestre3">Trimestre 3</option>
      </select>
    </div>

    <div class="form-row" style="flex:1;min-width:140px">
      <label class="form-label">
        <i class="bi bi-mortarboard" style="color:#080A66"></i> Année scolaire
      </label>
      <select name="anneeScolaire" class="cc-select">
        <?php filtreAnneeScolaireSelectNote($anneeScolaire); ?>
      </select>
    </div>

    <div style="display:flex;gap:6px;align-items:flex-end;padding-bottom:1px">
      <input type="hidden" value="<?php print $sClasseGrp ?>" name="sClasseGrp">
      <script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","rien");</script>
      <?php if ($_SESSION["membre"] == "menuprof"): ?>
      <script language=JavaScript>buttonMagicRetour("profp.php","_self");</script>
      <?php endif; ?>
    </div>

  </div>
</form>
</div>
</div>

<!-- ── Tableau des classes ────────────────────────────────────────────────── -->
<div style="margin:0 6px 10px;overflow-x:auto">
<table class="table" style="width:100%">
  <thead>
  <tr>
    <th class="cc-th">
      <i class="bi bi-diagram-3" style="margin-right:5px"></i><?php print ucwords(LANGPER25)?>
    </th>
    <th class="cc-th" style="width:130px;text-align:center">
      <i class="bi bi-person-lines-fill" style="margin-right:4px"></i>Affecter
    </th>
    <th class="cc-th" style="width:110px;text-align:center">
      <i class="bi bi-list-ol" style="margin-right:4px"></i>Ordre
    </th>
  </tr>
  </thead>
  <tbody>
<?php
$cnx=cnx();
if ($_SESSION["membre"] == "menuprof") {
    $cr=verif_profp_class_sans_blacklist($_SESSION["id_pers"],$sClasseGrp);
    if ($cr == "ok") $data=visu_affectation_2_prof($tri,$sClasseGrp,$anneeScolaire);
} else {
    $data=visu_affectation_2($tri,$anneeScolaire);
}
for ($i=0; $i<countTriade($data); $i++):
    $classe=chercheClasse($data[$i][0]);
?>
  <tr class="cc-tr-data">
    <td style="font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;font-weight:600;color:#080A66">
      <i class="bi bi-mortarboard-fill" style="color:#c5cae9;margin-right:5px"></i>
      <?php print $classe[0][1]; ?>
    </td>
    <td style="text-align:center">
      <button type="button" class="btn btn-primary"
              style="font-size:11px;padding:3px 10px"
              onclick="PopupCentrerAttente('./modifaffect2.php?saisie_classe_envoi=<?php print $data[$i][0]?>&saisie_tri=<?php print $tri?>&anneeScolaire=<?php print $anneeScolaire ?>',1000,500,'tollbar=no,menubar=no,scrollbars=yes,resizable=yes');">
        <i class="bi bi-pencil-square"></i> <?php print ucwords(LANGPER30)?>
      </button>
    </td>
    <td style="text-align:center">
      <button type="button" class="btn"
              style="font-size:11px;padding:3px 10px;background:#e8eaf6;color:#080A66;border:1px solid #c5cae9"
              onclick="PopupCentrerAttente('./modifaffect4.php?saisie_classe_envoi=<?php print $data[$i][0]?>&saisie_tri=<?php print $tri?>&anneeScolaire=<?php print $anneeScolaire ?>',1000,500,'tollbar=no,menubar=no,scrollbars=yes,resizable=yes');">
        <i class="bi bi-list-ol"></i> <?php print ucwords(LANGPER30)?>
      </button>
    </td>
  </tr>
<?php
endfor;
unset($data);
Pgclose();
?>
  </tbody>
</table>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
