<?php
session_start();
error_reporting(0);
include("./common/config.inc.php");
include("./librairie_php/db_triade.php");
validerequete("menuadmin");

$cnx = cnx();

$ident = array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession = hashSessionVar($ident);
unset($ident);
$Spid = $mySession["Spid"];

$sql = "
SELECT
    a.code_classe,
    trim(c.libelle),
    a.code_matiere,
";
if (DBTYPE == 'pgsql') {
    $sql .= " trim(m.libelle)||' '||trim(m.sous_matiere)||' '||trim(langue), ";
} elseif (DBTYPE == 'mysql') {
    $sql .= " CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(langue) ), ";
}
$sql .= "
    a.code_groupe,
    trim(g.libelle)
FROM
    {$prefixe}affectations a,
    {$prefixe}matieres m,
    {$prefixe}classes c,
    {$prefixe}groupes g
WHERE
    code_prof='$Spid'
AND a.code_classe = c.code_class
AND a.code_matiere = m.code_mat
AND a.code_groupe = group_id
ORDER BY
    c.libelle
";
$curs = execSql($sql);
$data = chargeMat($curs);
@array_unshift($data, array());
for ($i = 0; $i < countTriade($data); $i++) {
    $tmp = explode(" 0 ", $data[$i][3]);
    $data[$i][3] = $tmp[0].' '.$tmp[1];
}
freeResult($curs);
unset($curs);
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn']); ?></title>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<link rel="stylesheet" href="./librairie_css/alertify.default.min.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<style>
.cc-form-wrap  { padding:16px 20px; }
.cc-form-row   { display:flex; align-items:center; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
.cc-form-label { font-size:13px; font-weight:600; color:#1a237e; min-width:120px; }
.cc-form-select { padding:5px 10px; border:1px solid #c5cae9; border-radius:6px; font-size:13px; background:#fff; }
.cc-btn-row    { display:flex; gap:10px; margin-top:18px; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]; ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]; ?>1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td><b><font id='menumodule1'>Listing des paiements par classe</font></b></td></tr>
<tr id='cadreCentral0'><td>

<form method="POST" onsubmit="return verifAccesFiche()" name="formulaire" action="compta_listing2.php">
<div class="cc-form-wrap">

  <div class="cc-form-row">
    <span class="cc-form-label"><?php print LANGPROFG ?> :</span>
    <select name="sClasseGrp" size="1" class="cc-form-select">
      <option id='select0'><?php print LANGCHOIX ?></option>
      <?php select_classe(); ?>
    </select>
  </div>

  <div class="cc-form-row">
    <span class="cc-form-label">Filtre :</span>
    <select name='anneescolairefiltre' class="cc-form-select">
      <?php filtreAnneeScolaireSelect($filtre) ?>
    </select>
  </div>

  <div class="cc-btn-row">
    <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
    <script language=JavaScript>buttonMagicRetour2("comptaetat.php","_self","<?php print LANGSTAGE73 ?>")</script>
  </div>

</div>
</form>

</td></tr></table>

<?php
if ($_SESSION['membre'] == "menuadmin") {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
@Pgclose();
?>
</BODY>
</HTML>
