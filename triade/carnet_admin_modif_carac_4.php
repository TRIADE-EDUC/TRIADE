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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php"); 
validerequete("menuadmin");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Modification du Carnet de Suivi" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<?php 

$idcarnet=$_POST["idcarnet"];
$nom_carnet=$_POST["saisie_nom_carnet"];
$code_lettre=$_POST["code_lettre"];
$code_chiffre=$_POST["code_chiffre"];
$code_couleur=$_POST["code_couleur"];
$code_julesverne=$_POST["code_julesverne"];
$code_commentaire=$_POST["code_commentaire"];
$code_note=$_POST["code_note"];
$nb_periode=$_POST["saisie_nb_periode"];

?>

<font class="T2">&nbsp;&nbsp;<b>Gestion du carnet "<?php print $nom_carnet ?>"</b></font><br /><br />

<script>
function desactive(case1) {
	if (document.getElementById(case1).disabled == true) { 
		document.getElementById(case1).disabled=false;
	}else{
		document.getElementById(case1).disabled=true;
	}
}
</script>

<form action='carnet_admin_modif_carac_5.php' method="post">
<table border=0 align=center width=95%>
<tr><td align="left" colspan=2><font class="T2">Sections associées à ce Carnet de Suivi  : </font><br /><br /></td></tr>
<tr><td align="left" width=10% valign="top"><font class="T2">*Sections  : </font><br><br>
<?php
print "<table border='0' width='90%' ><tr>";


$cnx=cnx();
$data=listeSection(); //  id,libelle,listeidclasse

$j=1;
for($i=0;$i<count($data);$i++) {
	if (verifSectionCarnet($idcarnet,$data[$i][0])) { 
		$checked="checked='checked'";
		$disabled="";	
	}else{
		$checked="";
		$disabled="disabled='disabled'";
	}
	print "<td align='right'>";
	print $data[$i][1]."<input type=checkbox name='section[]' value=\"".$data[$i][0]."\" $checked onclick=\"desactive('case$i')\" />";
	print "<select name='ordre[]' id='case$i'  $disabled >";
	print "<option value=''></option>";
	print "<option value='1' id='select1'>1</option>";
	print "<option value='2' id='select1'>2</option>";
	print "<option value='3' id='select1'>3</option>";
	print "<option value='4' id='select1'>4</option>";
	print "</select></td> ";


	if ($j >= 4) { print "</tr><tr>";$j=0; }else{ $j++; } 

}
print "</td></tr></table>";
Pgclose();
?>


<tr><td align=center colspan="2"><br />
<table><tr><td><script language=JavaScript> buttonMagic("<?php print "Gestion des sections" ?>","carnet_admin_creation_section.php","section","width=500,height=400","")</script><script language=JavaScript>buttonMagicReactualise();</script><script language=JavaScript>buttonMagicSubmit("<?php print "Continuer --->" ?>","rien");</script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>
</td></tr>
</table><br /><br />

<input type="hidden" name="saisie_nom_carnet" value="<?php print $nom_carnet ?>" />
<input type="hidden" name="code_lettre" value="<?php print $code_lettre ?>" />
<input type="hidden" name="code_chiffre" value="<?php print $code_chiffre ?>" />
<input type="hidden" name="code_couleur" value="<?php print $code_couleur ?>" />
<input type="hidden" name="code_julesverne" value="<?php print $code_julesverne ?>" />
<input type="hidden" name="code_commentaire" value="<?php print $code_commentaire ?>" />
<input type="hidden" name="code_note" value="<?php print $code_note ?>" />
<input type="hidden" name="saisie_nb_periode" value="<?php print $nb_periode ?>" />
<input type="hidden" name="idcarnet" value="<?php print $idcarnet ?>" />
</form>


&nbsp;&nbsp;<font class="T1"><i>* Maximum 4 choix possibles (les 4 premiers seront conservés)</i></font>
<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
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
