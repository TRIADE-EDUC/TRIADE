<?php
      session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
if(!isset($_POST["deleteSupp"])) {
?>
<form method=post onsubmit="return valide_supp_choix('saisie_supp_compte','un suppléant')" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSUPP0?></font></b></td>
</tr>
<tr id='cadreCentral0'  >
<td >
<!-- // fin  -->
<blockquote><BR>
<fieldset><legend><?php print LANGSUPP1?></legend>
&nbsp;&nbsp;
<?php print LANGNA1." ".LANGNA2 ?> : <select name="saisie_supp_compte">
<option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php select_suppleant() ?>
</select> <BR>
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGSUPP2?>","deleteSupp"); //text,nomInput</script></UL></UL></UL>
</fieldset>
</blockquote>
<!-- // fin  -->
</td></tr></table>
</form>

<?php
// fin du if première affichage (le formulaire de choix)
}
// deuxième vue de la page : la confirmation
//
// un compte est proposé pour la suppression
// on affiche les données concernant la
// personne à supprimer et on demande
// une confirmation de suppression
if(isset($_POST["deleteSupp"])):

$pid=$_POST["saisie_supp_compte"];

$sql=<<<EOF

SELECT
	p.civ,
	p.nom,
	p.prenom,
	p1.civ,
	p1.nom,
	p1.prenom
FROM
	${prefixe}personnel p,
	${prefixe}personnel p1,
	${prefixe}vacataires v
WHERE
	p.pers_id = v.pers_id
AND	p.pers_id = '$pid'
AND	p1.pers_id = v.rpers_id

EOF;

$res=execSql($sql);
$data=chargeMat($res);

$sciv=civ($data[0][0]);
$snom=strtoupper($data[0][1]);
$sprenom=ucwords($data[0][2]);
$prciv=civ($data[0][3]);
$prnom=strtoupper($data[0][4]);
$prprenom=ucwords($data[0][5]);

?>

     <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
     <tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSUPP0?></font></b></td></tr>
     <tr id='cadreCentral0' >
     <td >
     <!-- // fin  -->
<table border=0 width=100%>
<TR><TD>
<UL><BR>
<?php print LANGSUPP3 ?> <br />
<br />
<font color="red"><?php print $sciv." ".$sprenom." ".$snom ?></font><br />
<br />
</p>
</UL>
<p><center>
<form method="POST">
<input type="hidden" name="pid" value="<?php print $pid?>" />
<input type="submit" name="deleteSupp_confirm" value="<?php print LANGSUPP4?>" / STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
</form></center>
</p>

</TD></TR></table>
<!-- // fin  -->
</td></tr></table>
<?php

endif;
// fin du if affichage 2
?>

<?php
// if affichage 3
//
// troisième vue de la page
// la personne est supprimée
// plus message que tout est ok

if(isset($_POST["deleteSupp_confirm"])):
$cr=verif_utiliser($_POST["pid"]);
if ($cr) {
    $personne=recherche_personne($_POST["pid"]);
	$cr=supp_suppleant($_POST["pid"]);
	if($cr) {
		@delete_comptaVacation($_POST["pid"]);
		alertJs("Affectation suppléant supprimée");
                history_cmd($_SESSION["nom"],"SUPPRESSION",$personne);
		reload_page('suppression_compte_suppleant.php');
	}else {
		error(0);
	}
}else {
	alertJs(LANGSUPP5);
}
endif;

//
// fin if affichage 3
Pgclose()
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
