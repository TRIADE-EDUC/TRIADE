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

<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Modifier un encaissement" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >

<?php
if (!isset($_GET["ideleve"])) {
	$nomEleve=$_POST["saisie_nom_eleve"];
	$sql="SELECT elev_id,nom,prenom,classe FROM  ${prefixe}eleves  WHERE  nom='$nomEleve' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 1) {
		print "<table border='1' width='100%' bordercolor='#000000' >";
		print "<tr><td bgcolor='yellow'>Nom Prénom</td><td bgcolor='yellow' >Classe</td>";
		print "<td bgcolor='yellow' align='center' >Sélectionner</td>";
		print "</tr>";
		for($i=0;$i<count($data);$i++) {
			print "<tr  class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">";
			print "<td>".$data[$i][1]." ".$data[$i][2]."</td><td>".chercheClasse_nom($data[$i][3])."</td>";
			print "<td width='5%'><input type='button' onclick=\"open('compta_modif.php?ideleve=".$data[$i][0]."','_parent','')\" class='button' value='Sélectionner' /></td>";
			print "</tr>";
		}
		print "</table>";
	}else{
		$ideleve=$data[0][0];
	}
}else{
	$ideleve=$_GET["ideleve"];
}

if ($ideleve > 0) {
	$nomeleve=recherche_eleve_nom($ideleve);
	$prenomeleve=recherche_eleve_prenom($ideleve);
	$idclasse=chercheClasseEleve($ideleve);
	$classe=chercheClasse_nom($idclasse);
?>
	
	<input type="hidden" name="ideleve" value="<?php print $ideleve ?>" />
	<table>
	<tr><td valign='top' ><img src="image_trombi.php?idE=<?php print $ideleve ?>" border=0 ></td>
	<td valign="top">
	&nbsp;&nbsp;<font class=T2>Nom : <?php print $nomeleve ?></font>
	<br><br>
	&nbsp;&nbsp;<font class=T2>Prénom : <?php print $prenomeleve ?></font>
	<br><br>
	&nbsp;&nbsp;<font class=T2>Classe : <?php print ucwords($classe) ?></font>
	</td></tr>
<tr><td colspan='2'>&nbsp;&nbsp;<font class=T2>Boursier : <?php print etatBoursier($ideleve) ?> (<?php print montantBourse($ideleve) ?>) </font></td></tr>
	<tr><td colspan='2'>&nbsp;&nbsp;<font class=T2>Indemnité de stage : <?php print montantIndemniteStage($ideleve) ?> </font></td></tr>
	</table>
	<br><br>
	<table width=100% border='1' bordercolor='#000000' >
	<tr>
	<td bgcolor='yellow' align='center' width=5 >&nbsp;Date&nbsp;</td>
	<td bgcolor='yellow' align='center'>Versement</td>
	<td bgcolor='yellow' align='center'>Montant</td>
	<td bgcolor='yellow' align='center'>Mode paiement</td>
	<td bgcolor='yellow' align='center' width=2% >Modifier</td>
	</tr>
<?php
	$data=listVersement($ideleve); // ideleve,idversement,montantvers,datevers,modepaiement
	for($i=0;$i<count($data);$i++) {
		$nomVersement=chercheNomVersement($data[$i][1]);
		if ($nomVersement != "") {
			print "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\">";
			print "<td valign='top'>&nbsp;".dateForm($data[$i][3])."&nbsp;</td>";
			print "<td valign='top'>&nbsp;".$nomVersement."</td>";
			print "<td valign='top'>&nbsp;".preg_replace('/ /','&nbsp;',affichageFormatMonnaie($data[$i][2]))."</td>";
			print "<td valign='top'>&nbsp;".nl2br($data[$i][4])."</td>";
			print "<td valign='top' align=center ><input type=button value='Modifer' class='bouton2' onclick=\"open('compta_modif.php?idvers=".$data[$i][1]."&date=".$data[$i][3]."&ideleve=".$ideleve."','_parent','')\" /></td>";
			print "</tr>";
		}

	}
?>
	</table>
<?php
}
?>


<br /><br />
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
   </BODY></HTML>
