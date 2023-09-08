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
<script language="JavaScript" src="./librairie_js/lib_ordre_liste.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<script language="javaScript">
function prepEnvoi() {
	var hid = new String();
	var tab = new Array();
	var data = window.document.formulaire.saisie_recherche.options;
	for (i=0;i<data.length;i++)
	{
		tab.push(data[i].value);
	}
	document.formulaire.saisie_recherche_final.value=tab.join(",");
}

</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

if (isset($_POST["modif"])) {
	$liste=$_POST["saisie_recherche_final"];
	$idcarnet=$_POST["saisie_idcarnet"];
	modifOrdreCompetence($idcarnet,$liste);
}else{
	$idcarnet=$_GET["id"];
	if (isset($_POST["saisie_idcarnet"])) {
		$idcarnet=$_POST["saisie_idcarnet"];
	}
}
$nom_carnet=chercheNomCarnet($idcarnet);

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Modifier le carnet de suivi : <font id='color2'> $nom_carnet </font>" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br />

<?php
$data=listeCompetence($idcarnet); // id,idcarnet,libelle,ordre
if (count($data) > 0 ) {
	print "<form  method='post' name='formulaire' >";
	print "<table>";
?>
<tr>
<td align=center>
     		<select size=10 name="saisie_recherche" style="width:330px" multiple="multiple">
<?php
	for($i=0;$i<count($data);$i++) {
		print "<option value=\"".$data[$i][0]."\"  >".trunchaine($data[$i][2],46)."</option>";
	}
?>
	</select>
	</td>
	<td align=center>
<?php print LANGRECH6 ?> <br><br>
<input type=button value='<?php print LANGCHER7 ?>' style='width:100px' onClick='tjs_haut(this.form.saisie_recherche)' STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
<br><br>
<input type=button value='<?php print LANGCHER8 ?>' style='width:100px' onClick='tjs_bas(this.form.saisie_recherche)' STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
	  </td>
</tr>
<tr><td colspan='2'><br /><br />
&nbsp;&nbsp;&nbsp;<input type="submit" value="<?php print "Valider la modification" ?>" class="BUTTON" onclick="prepEnvoi()" name="modif" >
<input type="button" value="<?php print "Retour Menu Carnet de Suivi" ?>" class="BUTTON" onclick="open('carnet_admin.php','_parent','')" />
</ul>
</td></tr>
</table>
<input type=hidden name="saisie_idcarnet" value="<?php print $idcarnet ?>" />
<input type=hidden name="saisie_recherche_final" />
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
