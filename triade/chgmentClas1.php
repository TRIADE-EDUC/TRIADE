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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
       print "<script>";
       print "location.href='./base_de_donne_key.php'";
       print "</script>";
       exit;
}
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBASE23 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<center>
<font class=T2><?php print LANGBASE24 ?>.</font>
<br><br>
<table border=0>
<tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGBT48?>","acces2.php","_parent","","") //value,lien,name,option,actionpossible</script>
<script language=JavaScript>buttonMagic("<?php print LANGBT47?>","chgmentClas0.php","_parent","",""); </script>&nbsp;&nbsp;
<br /><br />
</td></tr>
</table>
</center>

<!-- // fin form -->
 </td></tr></table>

<?php
$annefutur=$_POST["anneefutur"];
for($i=0;$i<$_POST["nbEleve"];$i++){
	$id_eleve=$_POST["idEleve_$i"];
	$nomeleve=recherche_eleve_nom($id_eleve);
	$prenomeleve=recherche_eleve_prenom($id_eleve);
	$newsClasse=$_POST["new_classe_$i"];
	if ($newsClasse == 'rien') { 
		continue; 
	}elseif ($newsClasse == 'quit') {  // l'eleve quitte l'etablissement
		@suppression_eleve($id_eleve);
		history_cmd($_SESSION["nom"],"SUPPRESSION","Eleve $nomeleve $prenomeleve");
		continue;
	}elseif ($newsClasse == 'sansclasse') {
		$cr=@changementClasseEleve($id_eleve,"-1",$anneefutur);  // -1 dans une classe signifie eleve sans classe.
		history_cmd($_SESSION["nom"],"SANS CLASSE","Eleve $nomeleve");
	}else {
		// on prendre les modifs de l'eleve avec UPDATE
		$cr=@changementClasseEleve($id_eleve,$newsClasse,$annefutur);
	}
	if ($cr) {
		history_cmd($_SESSION["nom"],"CHANGEMENT CLASSE","Eleve $nomeleve");
		// supprimer les infos de l'eleves
		// note, abs, retards, sanctions, dispenses, brevet 
		SuppressionInfoEleveSuiteChangement($id_eleve);
	}
	
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
Pgclose();
?>
</BODY>
</HTML>
