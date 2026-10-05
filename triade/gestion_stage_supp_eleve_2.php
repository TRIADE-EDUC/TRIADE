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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("3");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGSTAGE88 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<?php
if (isset($_GET["id"])) {
	$prenom  = recherche_eleve_prenom($_GET["id"]);
	$nom     = recherche_eleve_nom($_GET["id"]);
	$idclasse= chercheIdClasseDunEleve($_GET["id"]);
}

if (isset($_GET["idsupp"])) {
	supp_stage_eleve($_GET["idsupp"]);
	$classeeleve   = $_GET["idclasse"];
	$periode       = $_GET["periode"];
	$identreprise  = $_GET["entr"];
	$ideleve       = $_GET["id"];
	$classeeleve   = chercheClasse_nom($classeeleve);
	supphistoStage($identreprise,$ideleve,$classeeleve,$periode);
}
?>

<!-- ── En-tête nom élève + retour ── -->
<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin:8px 0 10px;">
  <span style="font-size:13px;font-weight:700;color:#080A66;"><?php print ucwords($prenom)." ".strtoupper($nom) ?></span>
  <form method="post" action="gestion_stage_supp_eleve.php" style="margin:0;">
    <input type="hidden" name="saisie_classe" value="<?php print chercheIdClasseDunEleve($_GET['id']) ?>">
    <button type="submit" class="btn-retour"><?php print LANGSTAGE73 ?></button>
  </form>
</div>

<!-- ── Tableau des stages ── -->
<table class="cc-data-table">
<thead>
<tr class="cc-thead-row">
  <th class="cc-th" style="width:5%">N° <?php print LANGSTAGE50 ?></th>
  <th class="cc-th cc-th-center"><?php print LANGSTAGE72 ?></th>
  <th class="cc-th"><?php print LANGSTAGE39 ?></th>
  <th class="cc-th cc-th-center"><?php print LANGBT50 ?></th>
</tr>
</thead>
<tbody>
<?php
$data=recherche_stage_eleve($_GET["id"]);
for ($i=0; $i<countTriade($data); $i++) {
	$numstage  = rechercheNumStage($data[$i][11]);
	$identr    = $data[$i][1];
	$entreprise= recherche_entr_nom_via_id($identr);
	$periode   = recherchedatestage2($data[$i][11],$idclasse);
?>
<tr class="cc-tr-data">
  <td class="cc-td">Stage <?php print $numstage ?></td>
  <td class="cc-td cc-td-center"><?php print $periode ?></td>
  <td class="cc-td"><?php print $entreprise ?></td>
  <td class="cc-td cc-td-center">
    <button type="button" class="btn-dest" style="background:#c62828;"
      onclick="open('gestion_stage_supp_eleve_2.php?idsupp=<?php print $data[$i][13]?>&id=<?php print $_GET['id']?>&entr=<?php print $identr?>&periode=<?php print $periode?>&idclasse=<?php print $idclasse?>','_parent','')">
      <?php print LANGBT50 ?>
    </button>
  </td>
</tr>
<?php } ?>
</tbody>
</table>

</td></tr></table>
<?php
if ($_SESSION['membre'] == "menuadmin") :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY>
</HTML>
