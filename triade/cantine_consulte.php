<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET -
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
?>
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include_once("./common/config5.inc.php") ?>
<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
<meta http-equiv="CacheControl" content="no-cache" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="expires" content="-1" />
<meta name="Copyright" content="Triade©, 2001" />
<link rel="SHORTCUT ICON" href="./favicon.ico" />
<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
?>
	<body  id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
	<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
	<script type="text/javascript" src="./librairie_js/messagerie_fenetre.js"></script>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center">
	<div align='center'><?php top_h(); ?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Consultation de votre compte cantine" ?></font></b></td></tr>
	<tr id='cadreCentral0'><td valign='top' >
	<!-- // fin  -->


<table border='1' width=100% bgcolor='#FFFFFF' style="border-collapse: collapse; -webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;padding:3px;  " >
<tr>
<td bgcolor='yellow' width=5%><font class='T2'>&nbsp;Date&nbsp;</font></td>
<td bgcolor='yellow'><font class='T2'>&nbsp;Détail&nbsp;</font></td>
<td bgcolor='yellow' width=15% align='right' colspan=2 ><font class='T2'>&nbsp;Montant&nbsp;<?php print unitemonnaie() ?>&nbsp;</font></td>
</tr>

<?php
$idpers=$_SESSION["id_pers"];
$membre=$_SESSION["membre"];
$data=recupComptaPers($idpers,$membre); //date,prix,plateau
for($i=0;$i<count($data);$i++) {
	if ($i < 50) {
		print "<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">";
		print "<td ><font class='T2'>&nbsp;".dateForm($data[$i][0])."&nbsp;</font></td>";
		print "<td ><font class='T2'>&nbsp;".urldecode($data[$i][2])."</font></td>";
		print "<td align='right' ><font class='T2'>".affichageFormatMonnaie($data[$i][1])."&nbsp;</font></td>";
		print "</tr>";
	}
	$total+=$data[$i][1];
}
	if ($total < 0) {
		$color="color='red'";
	}else{
		$color="color='green'";
	}
	print "<tr>";
	print "<td colspan=2 align='right' id='bordure' ><font class='T2'>Totaux : </font></td>";
	print "<td align='right'  id='bordure' ><font class='T2' $color ><b>".affichageFormatMonnaie($total)."</b>&nbsp;</font></td>";
	
	print "</tr>";
?>

</table>

	



	<?php
	print "</td></tr></table>";
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     		print "<SCRIPT type='text/javascript' ";
	       	print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
       		print "</SCRIPT>";
	}else{
       		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
      		print "</SCRIPT>";
	      	top_d();
      		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
		print "</SCRIPT>";
	}
	
?>
</BODY></HTML>
