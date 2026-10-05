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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGETUDE41 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<?php
include_once("librairie_php/db_triade.php");
validerequete("2");
if (isset($_POST["modif"])) {
	for ($i=0; $i<$_POST["nb"]; $i++) {
		$sortir="sortir".$i;
		$sorti = ($_POST[$sortir] == 1) ? "1" : "0";
		modif_eleve_etude($_POST["id_eleve"][$i],$_POST["id_etude"],$_POST["info"][$i],$sorti);
	}
	$id_etude=$_POST["id_etude"];
} else {
	$id_etude=$_POST["saisie_etude"];
}
$data=liste_eleve_etude($id_etude);
?>

<form method="post">
<input type="hidden" name="id_etude" value="<?php print $id_etude ?>">
<input type="hidden" name="nb" value="<?php print countTriade($data) ?>">

<table class="cc-data-table">
<thead>
<tr class="cc-thead-row">
  <th class="cc-th"><?php print LANGTP1 ?> <?php print LANGTP2 ?></th>
  <th class="cc-th"><?php print LANGASS27 ?></th>
</tr>
</thead>
<tbody>
<?php for ($i=0; $i<countTriade($data); $i++) {
	$idclasse=chercheIdClasseDunEleve($data[$i][0]);
	$nomclasse=chercheClasse($idclasse);
?>
<tr class="cc-tr-data">
  <td class="cc-td">
    <b><?php print strtoupper(recherche_eleve_nom($data[$i][0])) ?></b>
    <?php print strtolower(recherche_eleve_prenom($data[$i][0])) ?><br>
    <span style="font-size:11px;color:#666;"><?php print $nomclasse[0][1] ?></span>
  </td>
  <td class="cc-td">
    <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;margin-bottom:6px;">
      <input type="checkbox" value="1" name="sortir<?php print $i ?>" <?php print verifchecketude($data[$i][0],$id_etude) ?>>
      <?php print LANGETUDE44 ?>
    </label>
    <input type="text" name="info[]" size="30" value="<?php print trim($data[$i][2]) ?>" class="cc-select">
    <input type="hidden" name="id_eleve[]" value="<?php print $data[$i][0] ?>">
  </td>
</tr>
<?php } ?>
</tbody>
</table>

<br>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGETUDE43 ?>","modif");</script>
  <script>buttonMagic('Retour','gestion_etude.php','_self','','');</script>
</div>
<br>

</form>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
