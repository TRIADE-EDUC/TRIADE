<?php
session_start();
include_once("./common/config2.inc.php");
if ((isset($_SESSION['adminplus'])) && ($_SESSION["adminplus"] != "suppreme")) {
	if (defined("PASSMODULEINDIVIDUEL")) {
		if (PASSMODULEINDIVIDUEL == "oui") {
			header("Location:base_de_donne_key.php?key=passmoduleindividuel");
		}
	}
}
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
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/rechercheV4.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Entretiens - <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"], "entretien")) {
		accesNonReserveFen();
		exit;
	}
} else {
	validerequete("2");
}
include_once("./librairie_php/ajax.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS369 ?></font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<div style="display:flex; flex-direction:column; gap:8px;">

<!-- SECTION 1 : Recherche par élève -->
<div class="card" style="margin:0;">
  <div class="card-header card-header-primary">
    <span><?php print LANGMESS369 ?> <i class="bi bi-person-search" style="margin-left:6px;"></i></span>
  </div>
  <div class="card-body">
    <form method="post" onsubmit="return valide_recherche_eleve()" name="formulaire" id="formulaire">
      <div class="form-row">
        <label class="form-lbl"><?php print LANGABS3 ?> :</label>
        <div style="position:relative;">
          <input type="text" name="saisie_nom_eleve" size="22" id="search" autocomplete="off"
                 onkeyup="searchRequestV4('search','eleve','resultat','formulaire','saisie_nom_eleve')"
                 class="bouton2">
          <div id="resultat" style="padding:0 0 0 4px;width:16em;border-style:none;background-color:#EEEEEE;position:absolute;z-index:100;"></div>
        </div>
      </div>
      <div style="margin-top:10px;">
        <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT39 ?>","create");</script>
      </div>
    </form>
  </div>
</div>

<?php
if (defined("PASSMODULEINDIVIDUEL") && PASSMODULEINDIVIDUEL == "oui" && empty($_SESSION["adminplus"])) {
	print "<script>location.href='./base_de_donne_key.php?key=passmoduleindividuel'</script>";
}

if (isset($_POST["saisie_nom_eleve"])) {
	$saisie_nom_eleve = trim($_POST["saisie_nom_eleve"]);
	$motif = strtolower($saisie_nom_eleve);
	$sql = <<<EOF
SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM {$prefixe}eleves e, {$prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom
EOF;
	$res  = execSql($sql);
	$data = chargeMat($res);
?>
<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-search" style="margin-right:6px;"></i><?php print LANGRECH2 ?> : <span class="card-badge"><?php print ucwords(stripslashes($motif)) ?></span></span>
  </div>
  <?php if (countTriade($data) <= 0): ?>
  <div class="card-body" style="color:#888;font-style:italic;"><?php print LANGRECH3 ?></div>
  <?php else: ?>
  <table class="table" style="margin:0;">
    <thead>
      <tr>
        <th class="cc-th"><?php print ucwords(LANGIMP10) ?></th>
        <th class="cc-th"><?php print LANGIMP8 ?></th>
        <th class="cc-th"><?php print LANGIMP9 ?></th>
      </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < countTriade($data); $i++): ?>
      <tr class="cc-tr-data">
        <td><?php print $data[$i][0] ?></td>
        <td>
          <a href="entretien2.php?eid=<?php print $data[$i][3] ?>"
             style="color:#080A66;font-weight:600;text-decoration:underline;"
             title="Accès journal d'entretien">
            <?php print strtoupper($data[$i][1]) ?>
          </a>
        </td>
        <td><?php print ucwords($data[$i][2]) ?></td>
      </tr>
    <?php endfor; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php } ?>

<!-- SECTION 2 : Entretien par classe -->
<div class="card" style="margin:0;">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-people-fill" style="margin-right:6px;"></i><?php print LANGMESS370 ?></span>
  </div>
  <div class="card-body">
    <form method="POST" onsubmit="return verifAccesFiche2()" name="formulaire2" action="entretien_classe.php">
      <div class="form-row">
        <label class="form-lbl"><?php print LANGPROFG ?> :</label>
        <select name="sClasseGrp" class="cc-select">
          <option id="select0"><?php print LANGCHOIX ?></option>
          <?php select_classe(); ?>
        </select>
      </div>
      <div style="margin-top:10px;">
        <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
      </div>
    </form>
  </div>
</div>

<!-- SECTION 3 : Consultation cumul par professeur -->
<form method="post" onsubmit="return valide_consul_classe1()" name="formulaire1">
<div class="card" style="margin:0;">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-person-badge-fill" style="margin-right:6px;"></i><?php print LANGMESS371 ?></span>
  </div>
  <div class="card-body">
    <div class="form-row">
      <label class="form-lbl"><?php print LANGPROFG ?> :</label>
      <select id="saisie_classe" name="saisie_classe" class="cc-select">
        <option id="select0"><?php print LANGCHOIX ?></option>
        <?php select_classe(); ?>
      </select>
    </div>
    <div style="margin-top:10px;">
      <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT28 ?>","consult");</script>
    </div>
  </div>

  <?php $dataPedago = recupListPedago(); ?>
  <table class="table" style="margin:0;border-top:1px solid #dde0f0;">
    <thead>
      <tr>
        <th class="cc-th"><?php print LANGMESS372 ?></th>
        <th class="cc-th"><?php print LANGMESS373 ?></th>
      </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i <= countTriade($dataPedago); $i++):
          $cumulHeure = cumulEntretienPedago($dataPedago[$i][6]);
          if (is_numeric($dataPedago[$i][2])): ?>
      <tr class="cc-tr-data">
        <td><?php print civ($dataPedago[$i][2])." ".$dataPedago[$i][1] ?></td>
        <td><?php print $cumulHeure ?></td>
      </tr>
    <?php endif; endfor; ?>
    </tbody>
  </table>
</div>

<?php
if (isset($_POST["consult"])) {
	$saisie_classe = $_POST["saisie_classe"];
	$sql = "SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves,{$prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res  = execSql($sql);
	$data = chargeMat($res);
	$cl   = $data[0][0];
?>
<div class="card" style="margin:0;">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-mortarboard-fill" style="margin-right:6px;"></i><?php print LANGELE4 ?> : <span class="card-badge"><?php print $cl ?></span>&nbsp;&mdash;&nbsp;<?php print LANGCOM3 ?> <span class="card-badge"><?php print countTriade($data) ?></span></span>
  </div>
  <?php if (countTriade($data) <= 0): ?>
  <div class="card-body" style="color:#888;font-style:italic;"><?php print LANGRECH1 ?></div>
  <?php else: ?>
  <table class="table" style="margin:0;">
    <thead>
      <tr>
        <th class="cc-th" width="60%"><?php print ucwords(LANGIMP8)." ".ucwords(LANGIMP9) ?></th>
        <th class="cc-th">Cumul</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $cumulTotal = 0;
    for ($i = 0; $i < countTriade($data); $i++):
        $eid = $data[$i][1];
        $cumulHeure   = cumulEntretien2($eid);
        $cumulTotal  += cumulEntretien22($eid, $cl);
    ?>
      <tr class="cc-tr-data">
        <td><?php print strtoupper($data[$i][2]) ?> <?php print trunchaine(ucwords($data[$i][3]), 30) ?></td>
        <td><?php print $cumulHeure ?></td>
      </tr>
    <?php endfor; ?>
    </tbody>
  </table>
  <div class="card-footer-total">
    <i class="bi bi-clock-history" style="margin-right:5px;"></i>
    Soit : <?php print timeForm(convert_sec($cumulTotal)) ?> d'entretien en <?php print $cl ?>
  </div>
  <?php endif; ?>
</div>
<?php } ?>
</form>

</div><!-- /flex column -->
</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}
Pgclose();
?>
</div>
</BODY>
</HTML>
