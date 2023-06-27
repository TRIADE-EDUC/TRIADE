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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>    <?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Entretien individuel des enseignants" ?></font></b></td></tr>
<tr id='cadreCentral0'><td >
<!-- // fin  -->
<blockquote><BR>
<form method=post  action="gestion_entretient_enseignant2.php"  name="formulaire">
<fieldset><legend><?php print "Choix de l'enseignant"?></legend>
&nbsp;&nbsp;
<font class="T2"><?php print LANGNA1." ".LANGNA2?>  :</font> <select name="idpers">
             <option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_personne('ENS'); // creation des options
?>
</select> <BR><br>
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print 'Accèder' ?>","supp"); //text,nomInput</script></UL></UL></UL><br>
<?php brmozilla($_SESSION["navigateur"]); ?>
</fieldset>
</blockquote>
<!-- // fin  -->
</td></tr></table>

</form>

<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Temps d'accompagnement d'un enseignant" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<blockquote><BR>
<form method=post  action="gestion_entretient_enseignant_recap.php"  name="formulaire2">
<fieldset><legend><?php print "Choix de l'enseignant"?></legend>
&nbsp;&nbsp;
<font class="T2"><?php print LANGNA1." ".LANGNA2?>  :</font> <select name="idpers">
             <option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_personne('ENS'); // creation des options
?>
</select> <BR><br>
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print 'Accèder' ?>","supp"); //text,nomInput</script></UL></UL></UL><br>
<?php brmozilla($_SESSION["navigateur"]); ?>
</fieldset>
</blockquote>
<!-- // fin  -->
</td></tr></table>

</form>

<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Statistique par classe" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign='top'>
<?php
$data=listingEntretienEnseignant(); // idprof,duree,idclasse,date_saisie,reference,ideleve
?>
<?php if (count($data) > 0) { ?>
<img src="ajax-graph-entretien-prof.php" />
<br><br><br>
<?php } ?>
<?php
for($i=0;$i<count($data);$i++) {
	$idprof=$data[$i][0];
	$seconde=conv_en_seconde($data[$i][1]);
	if ($seconde != "") {
		$tabProf[$idprof]+=$seconde;
	}
}
print "<table width='100%' border='1'>";
print "<tr><td bgcolor='yellow'>Enseignant</td>";
print "<td bgcolor='yellow'>Durée</td>";
print "</tr>";
foreach($tabProf as $idprof=>$duree) {
	print "<tr>";
	print "<td>".recherche_personne($idprof)."</td>";
	print "<td>".convert_sec($duree)."</td>";
	print "</tr>";
}
print "</table>";
?>
</td></tr></table>



<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
