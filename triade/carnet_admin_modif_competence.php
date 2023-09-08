<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E.
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) S.A.R.L. T.R.I.A.D.E. 
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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
if ((isset($_POST["idcarnet"])) &&  ($_POST["idcarnet"] > 0)){
	$idcarnet=$_POST["idcarnet"];
	$nom_carnet=chercheNomCarnet($idcarnet);
}else{
	print "<script>location.href='carnet_admin_modif.php?erreur'</script>";
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Modification du Carnet de Suivi : <font id='color2'> $nom_carnet </font>" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br />

<?php
$data=listeCompetence($idcarnet); // id,idcarnet,libelle,ordre
if (count($data) > 0 ) {
	print "<form action='carnet_admin_competence_1.php' method='post'>";
	print "<table>";
	for($i=0;$i<count($data);$i++) {
		print "<tr>";
		print "<td><font class=T2>".trunchaine($data[$i][2],46)."</font></td>";
		print "<td align=left><input type=radio name='idcompetence' value=\"".$data[$i][0]."\" ></td>";
		print "</tr>";
	}
?>
<tr><td><br /><br />
<script language=JavaScript>buttonMagicSubmit("<?php print "Modifier" ?>","modifdirect_competence"); </script>
	<script language=JavaScript>buttonMagic("<?php print "Changer l&#8217ordre d&#8217affichage" ?>","carnet_admin_modif_competence_ordre.php?id=<?php print $idcarnet ?>","_parent","",""); </script>&nbsp;&nbsp;</td></tr>
</table>
<input type=hidden name="saisie_idcarnet" value="<?php print $idcarnet ?>" />
</form>
<br /><br />
<?php 
}else{
?>
	<form action='carnet_admin_modif_2.php' method="post">
	<input type=hidden name="saisie_carnet" value="<?php print $idcarnet ?>" />
	<center><font class="T2">Aucune compétence pour ce carnet.</font></center><br /><br />
	<table align=center><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print "Retour" ?>","modif");</script></td></tr></table>
	</form>
<?php	
}
?>

<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
