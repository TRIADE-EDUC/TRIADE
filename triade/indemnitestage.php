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
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Indemnités de stage</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
$data=visu_affectation();
$somme=0;
?>

<div style="margin:10px 0 6px;font-size:12px;font-weight:700;color:#080A66;">Moyenne indemnité de stage par classe</div>

<table class="brs-table">
<thead>
<tr>
  <th class="brs-th">Classe</th>
  <th class="brs-th brs-th-num" style="text-align:right;">Moyenne</th>
</tr>
</thead>
<tbody>
<?php
for ($i=0; $i<countTriade($data); $i++) {
	$val = number_format(moyenneIndemnite($data[$i][0]), 2, '.', '');
	$somme += $val;
	$nonzero = ($val > 0) ? " brs-td-nonzero" : "";
	print "<tr class='brs-tr'>";
	print "<td class='brs-td'>".$data[$i][1]."</td>";
	print "<td class='brs-td brs-td-num".$nonzero."' style='text-align:right;'>".$val." ".unitemonnaie()."</td>";
	print "</tr>";
}
$total = number_format($somme, 2, ',', '');
?>
</tbody>
<tfoot>
<tr class="brs-tr-total">
  <td class="brs-td"></td>
  <td class="brs-td brs-td-total" style="text-align:right;">
    <span class="brs-td-total-lbl" style="margin-right:16px;">Total</span><?php print $total." ".unitemonnaie() ?>
  </td>
</tr>
</tfoot>
</table>

<br>
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
</BODY></HTML>
