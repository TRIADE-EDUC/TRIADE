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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Configuration des dates de début et de fin d'année" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<br><br>
     <!-- // fin  -->
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();


$dj=aff_valeur_parametrage("anneescolaire_dj");
$fj=aff_valeur_parametrage("anneescolaire_fj");
$dm=aff_valeur_parametrage("anneescolaire_dm");
$fm=aff_valeur_parametrage("anneescolaire_fm");

if (isset($_POST['okannee'])) {
	$dj=$_POST["dj"];
	$fj=$_POST["fj"];
	$dm=$_POST["dm"];
	$fm=$_POST["fm"];
	
	if (($dj > 0) && ($dj <= 31) && ($fj > 0) && ($fj <= 31) && ($dm > 0) && ($dm <= 12) && ($fm > 0) && ($fm <= 12)) {
		enr_parametrage("anneescolaire_dj",$dj,'');
		enr_parametrage("anneescolaire_fj",$fj,'');
		enr_parametrage("anneescolaire_dm",$dm,'');
		enr_parametrage("anneescolaire_fm",$fm,'');
		$reponse="<center><font class='T2' id='color3' ><b>Données Enregistrées</b></font></center><br>";
	}else{
		$reponse="<center><font class='T2' id='color3' ><b>Erreur de saisie sur vos jours ou mois indiqués</b></font></center><br>";
	}

}



Pgclose();

?>
<?php print $reponse ?>
<form method='post' action='configannee.php' >
<font class='T2'>
<ul>
Indiquer le jour et le mois du début de votre année scolaire : <input type='text' size=2 value='<?php print $dj ?>' name='dj' /> / <input type='text' size=2 value='<?php print $dm ?>' name='dm' /><br /><br />
Indiquer le jour et le mois de la fin de votre année scolaire : <input type='text' size=2 value='<?php print $fj ?>' name='fj' /> / <input type='text' size=2 value='<?php print $fm ?>' name='fm' /><br /><br />
<br>
<script>buttonMagicSubmit('<?php print VALIDER ?>','okannee','ok')</script>
</ul>
<br>
</font>
<BR><br>
</form>

</td></tr></table>
<br><br>

<?php
if (isset($_POST["okete"])) {
	touch("./data/parametrage/noacces.ete");	
}

if (isset($_POST["koete"])) {
	unlink("./data/parametrage/noacces.ete");	
}



?>
<form method='post' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Passage de Triade en mode &eacute;t&eacute; " ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><br/>
<ul>
Tous les comptes n'auront plus acc&egrave;s &agrave; leur compte Triade sauf les comptes directions. <br/><br />
<?php
if (file_exists("./data/parametrage/noacces.ete")) {
	print "<script>buttonMagicSubmit('D&eacute;sactiver','koete','ok')</script>";
}else{
	print "<script>buttonMagicSubmit('".VALIDER."','okete','ok')</script>";
}
?>
</ul>
<br/>
<br/>
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
