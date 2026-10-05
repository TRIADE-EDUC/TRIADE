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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();

if (isset($_POST["supp"])) {
	$cr=circulaireSup($_POST["saisie_id"]);
	if ($cr) {
		@unlink("./data/circulaire/".trim($_POST["saisie_nom_fic"]));
	}
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGCIRCU19 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">

<?php
if ($_SESSION["membre"] == "menuprof")      { $data=circulaireAffProf("true"); }
if ($_SESSION["membre"] == "menuscolaire")  { $data=circulaireAffVieScolaire('date',''); }
if ($_SESSION["membre"] == "menuadmin")     { $data=circulaireAffAdmin2(); }
?>

<table class="cc-data-table">
<thead>
<tr class="cc-thead-row">
  <th class="cc-th"><?php print LANGTE7 ?></th>
  <th class="cc-th"><?php print LANGFORUM12 ?></th>
  <th class="cc-th"><?php print LANGCIRCU20 ?></th>
  <th class="cc-th cc-th-center"><?php print LANGBT50 ?></th>
</tr>
</thead>
<tbody>
<?php for ($i=0; $i<countTriade($data); $i++) { ?>
<form method="post" style="display:contents">
<tr class="cc-tr-data">
  <td class="cc-td"><?php print dateForm($data[$i][4]) ?></td>
  <td class="cc-td">
    <a href="#" onMouseOver="AffBulle('<font face=Verdana size=1><B><?php print LANGCIRCU21 ?> :</B> <font color=blue><?php print htmlspecialchars($data[$i][2]) ?></font></font>'); window.status=''; return true;" onMouseOut="HideBulle()" style="color:#080A66;">
      <?php print htmlspecialchars($data[$i][1]) ?>
    </a>
  </td>
  <td class="cc-td">
    <a href="visu_document.php?fichier=./data/circulaire/<?php print $data[$i][3] ?>" target="_blank" class="btn-dest" style="text-decoration:none;display:inline-block;">Visualiser</a>
  </td>
  <td class="cc-td cc-td-center">
    <input type="hidden" name="saisie_id"      value="<?php print $data[$i][0] ?>">
    <input type="hidden" name="saisie_nom_fic" value="<?php print $data[$i][3] ?>">
    <button type="submit" name="supp" class="btn-dest" style="background:#c62828;"><?php print LANGBT50 ?></button>
  </td>
</tr>
</form>
<?php } ?>
</tbody>
</table>

<div class="na-foot" style="margin-top:10px;">
  <a href="circulaire_admin.php" class="btn-retour" style="text-decoration:none;">&larr; Administration</a>
</div>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
