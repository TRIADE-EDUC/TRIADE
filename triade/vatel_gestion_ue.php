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
 ***************************************************************************
/***************************************************************************
Last updated: 09.10.2008   par AMBIS Cyril 
     upadted: 18.09.2014   par TRIADE-DEV
****************************************************************************/ 
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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS222 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br />
<script language=JavaScript>buttonMagic("<?php print LANGMESS223 ?>","vatel_creat_ue.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGTMESS426 ?>","vatel_list_ue.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print "Copier Unité Enseignement" ?>","vatel_gestion_ue.php?copy","_parent","","");</script>
<?php brmozilla($_SESSION["navigateur"]); ?>
<br />

<?php
if (isset($_POST["copy"])) {
	$saisie_classe_source=$_POST["saisie_classe_source"];
	$anneeScolaireSource=$_POST["anneeScolaireSource"];
	$saisie_classe_destination=$_POST["saisie_classe_destination"];
	$anneeScolaireDest=$_POST["anneeScolaireDest"];
	copyUniteEnseignement($saisie_classe_source,$anneeScolaireSource,$saisie_classe_destination,$anneeScolaireDest);
	print "<br><br><center><font class='T2'>".LANGDONENR."</font></center><br><br>";
}

if (isset($_GET["copy"])) { ?>
<br><hr><br>
<form method='post' action='vatel_gestion_ue.php' >
<ul>
<font class='T2 shadow' id='color3' >IMPORTANT, LA COPIE D'UNITE ENSEIGNEMENT SUPPRIME L'ANCIENNE VERSION  !!</font>
<BR>
<BR>
<font class='T2'>Copier l'unité d'enseignement de la classe :
<select name="saisie_classe_source">
<option value=0  id='select0' ><?php print LANGCHOIX?></option>
<optgroup label="Classe">
<?php
select_classe(); // creation des options
?>
</select> de l'année scolaire : <select name='anneeScolaireSource'>
<?php
print "<option value=''id='select0' >".LANGCHOIX."</option>";
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select>

<br><br>pour la classe :
<select name="saisie_classe_destination">
<option value=0  id='select0' ><?php print LANGCHOIX?></option>
<optgroup label="Classe">
<?php
select_classe(); // creation des options
?>
</select>
de l'année scolaire : 
<select name='anneeScolaireDest'>
<?php
print "<option value=''id='select0' >".LANGCHOIX."</option>";
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select>
<br><br>
<script language=JavaScript>buttonMagicSubmit("<?php print 'Valider la copie' ?>","copy"); //text,nomInput</script>
<br><br>
</form>

<?php } ?>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
