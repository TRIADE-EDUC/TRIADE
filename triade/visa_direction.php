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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"visadirection")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}elseif ($_SESSION["membre"] == "menuadmin") {
	validerequete("menuadmin");
}else{
	if (PROFPACCESVISADIRECTION == "oui") {
		validerequete("menuprof");
		verif_profp_class($_SESSION["id_pers"],$_SESSION["profpclasse"]);
	}else{
		validerequete("menuadmin");
	}
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valideTab()" name="formulaire" action="visa_direction2.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTMESS480 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // debut form  -->
     <blockquote><BR>
	<font class="T2"><?php print LANGBULL3 ?> :</font>
                 <select name='annee_scolaire' >
                 <?php
		 $anneeScolaire=$_COOKIE["anneeScolaire"];
                 filtreAnneeScolaireSelectNote($anneeScolaire,3);
                 ?>
                 </select>
		<br><br>
               <font class=T2><?php print LANGPROFG?> :</font> <select id="saisie_classe" name="saisie_classe">
                                   <option id='select0' ><?php print LANGCHOIX?></option>
<?php
if ($_SESSION["membre"] == "menuprof") {
	print "<option id='select1' value='".$_SESSION["profpclasse"]."' >".chercheClasse_nom($_SESSION["profpclasse"])."</option>";
}else{
	select_classe(); // creation des options
}
?>
</select> <br /><br />
<font class=T2>

<?php print LANGBASE40 ?> <select name="typetrisem" onchange="trimes();" >
     <option value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
     <option value="trimestre" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28?></option>
     <option value="semestre"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29?></option>
     <option value="annuel"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGMESS334?></option>
     </select>  : 
     <select name="saisie_trimestre">
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
     </Select>
     <br /><br />
<?php print LANGMESS332 ?> <select name="type_bulletin" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='default' >Standard</option>
<?php if (VATEL != 1) { ?>
	<optgroup label="<?php print LANGBULL49 ?>">
	<option value='bacblanc' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM30?> BAC Blanc</option>
	<option value='btsblanc' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM30?> BTS Blanc</option>
	<option value='brevetblanc' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM30?> Brevet Blanc</option>
	<option value='capblanc' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM30?> CAP Blanc</option>
	<option value='bepblanc' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM30?> BEP Blanc</option>
	<option value='partielblanc' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM30?> Partiel Blanc</option>
	<optgroup label="Montessori" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='montessori' >Bulletin standard</option>
	<option STYLE='color:#000066;background-color:#CCCCFF' value='montessori_spec' >Bulletin Spécif.</option>
	<optgroup label="Pigier" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='pigierparis' >Bulletin Pigier Paris</option>
	<option STYLE='color:#000066;background-color:#CCCCFF' value='pigierparisv2' >Bulletin Pigier Paris V2</option>
	<optgroup label="La cheneraie" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='cheneraie' >Bulletin La Cheneraie</option>
	<optgroup label="Seminaire" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='seminaire' >Bulletin standard</option>
	<optgroup label="LEAP" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='leap' >Bulletin standard</option>
	<optgroup label="JTC" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='jtc' >Bulletin Immaculée Conception</option>
	<optgroup label="Unité Enseignement" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='univproafrique' >Bulletin Univ. Pro. Afrique</option>
<?php } ?>
     </select>
</font>
<br><br>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","consult"); //text,nomInput</script>
</UL></UL></UL>
</blockquote>
</form>
<br><br><br>
<!-- // fin form -->
 </td></tr></table>


<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
