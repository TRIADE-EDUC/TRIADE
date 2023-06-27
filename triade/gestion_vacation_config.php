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
<html>
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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Gestion des prestations" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<br><br>
<?php
$nvalide="create";
$valider=VALIDER;
if (isset($_POST["modif"])) {
	$nvalide="modifinfo";
	$valider="Modifier";
	$data=affEvalHoraireMotif($_POST["evaluation2"]); // id,libelle,taux
	$idEvaluation=$data[0][0];
	$nomEvaluation=$data[0][1];
	$proxEvaluation=$data[0][2];
	$checkedEval1=""; $checkedEval2="";
	if ($data[0][3] == "cours") {$checkedEval1="checked='checked'"; }	
	if ($data[0][3] == "eval") {$checkedEval2="checked='checked'"; }	
}


?>
<form method="post" >
&nbsp;&nbsp;<font class="T2"> Nom de la prestation : </font> <input type=text name="saisie_evaluation" size="40" maxlength="40" value="<?php print $nomEvaluation ?>" />
<br><br>
&nbsp;&nbsp;<font class="T2"> Taux horaire : </font> <input type=text name="saisie_basehoraire" size="5" value="<?php print $proxEvaluation ?>" /> <i>(hors taxe)</i>
<br><br>
&nbsp;&nbsp;<font class="T2"> Type de prestation : </font> ( cours <input type=radio name="type_eval"  value="cours" <?php print $checkedEval1 ?> /> )  ( évaluation <input type=radio name="type_eval"  value="eval" <?php print $checkedEval2 ?> /> )


<ul><script language=JavaScript>buttonMagicSubmit("<?php print $valider ?>","<?php print $nvalide ?>"); //text,nomInput</script></ul>



<input type="hidden" name="idEval" value="<?php print $idEvaluation ?>" />
</form>

<?php 
if (isset($_POST["create"])) {	enrEvalHoraire($_POST["saisie_evaluation"],$_POST["saisie_basehoraire"],$_POST["type_eval"]); }
if (isset($_POST["supp"])) {	suppEvalHoraire($_POST["evaluation"]); }
if (isset($_POST["modifinfo"])) { 
	$cr=modifEvalHoraire($_POST["idEval"],$_POST["saisie_evaluation"],$_POST["saisie_basehoraire"],$_POST["type_eval"]); 
	if ($cr) {
		alertJs(LANGDONENR);
	}
}


?>
<br><br>
<hr>
<br>
<form method="post" >&nbsp;&nbsp;<font class="T2"> Supprimer une prestation : </font> <select name="evaluation" >
<option id="select0"><?php print LANGCHOIX ?></option>
<?php
select_EvalHoraire();
?>
</select> <input type=submit name="supp" value="Ok" class="bouton2" />
</form>
<br>
<form method="post" >&nbsp;&nbsp;<font class="T2"> Modifier une prestation : </font> <select name="evaluation2" >
<option id="select0"><?php print LANGCHOIX ?></option>
<?php
select_EvalHoraire();
?>
	</select>  <input type=submit name="modif" value="Ok" class="bouton2" />
</form>
<br><br>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
