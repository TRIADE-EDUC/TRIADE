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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/calendar.php");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Gestion évaluation enseignement" ?></font></b></td></tr>
<tr id='cadreCentral0'><td>
<br><br>

<?php


$data=aff_enr_parametrage("datedebutevalens");
$debut=$data[0][1];
$data=aff_enr_parametrage("datefinevalens");
$fin=$data[0][1];
if (isset($_POST["createdateconfig"])) {
	$debut=$_POST["debut"];
	$fin=$_POST["fin"];
	enr_parametrage("datedebutevalens",$debut,"");
	enr_parametrage("datefinevalens",$fin,"");
}



?>


<form method='post' action="evalensadmconfig.php" name='formulaire'>
<table  align='center'  >
<tr>
<td>Ouverture du questionnaire à partir du :</td><td> <input type="text" name="debut" value="<?php print $debut ?>" size=12 /> <?php calendar('id1','document.formulaire.debut',$_SESSION["langue"],"0","0");?> (inclus)
</tr>
<tr><td height='20'></td></tr>
<tr>
<td>Fermeture du questionnaire à partir du :</td><td> <input type="text" name="fin" value="<?php print $fin ?>" size=12 /> <?php calendar('id2','document.formulaire.fin',$_SESSION["langue"],"0","0");?> (inclus)
</tr>
<tr><td height='40'></td></tr>
<tr><td colspan='2' >
<table><tr><td><script language=JavaScript>buttonMagicSubmitAtt("<?php print VALIDER ?>","createdateconfig","");</script></td>
<td><script language=JavaScript>buttonMagicRetour2('evalensadm.php','_self',"<?php print LANGSTAGE73 ?>");</script></td></tr></table>

</td></tr>
</table>
</form>
<br>
<hr width='50%'>
<br>

<!-- // debut form  -->
<form action='evalensadmconfig.php'  method="post"  >
<table align="center">
<tr><td> Question : </td><td><input type='text' name='question' size='50' /></td></tr>
<tr><td height='20'></td></tr>
<tr><td colspan='2' >
<table><tr><td><script language=JavaScript>buttonMagicSubmitAtt("<?php print VALIDER ?>","createask","");</script></td>
<td><script language=JavaScript>buttonMagicRetour2('evalensadm.php','_self',"<?php print LANGSTAGE73 ?>");</script></td></tr></table>

</td></tr>
<tr><td height='40'></td></tr>
<tr><td colspan='2'>NB : Réponse possible au question "OUI"  ou "NON"</td></tr>
</table>
</form>
<br>
<hr>

<?php
$cnx=cnx();

if (isset($_POST["createask"])) {
	$question=$_POST["question"];
	savequestion($question);
}

if (isset($_GET["idsupp"])) {
	suppQuestionEvalEns($_GET["idsupp"]); 
}

print "<br><font class='shadow' >Liste des questions en cours : </font><br><br>";

print "<table align='center' width='100%' border='1' style='border-collapse: collapse;' >"; 
$data=listQuestion(); // id,question
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$question=stripslashes($data[$i][1]);
	
	print "<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
	print "<td>";
	print $question;
	print "</td>";
	print "<td  width='1%' >";
	print "<input type='button' value=".LANGBT50." onclick=\"open('evalensadmconfig.php?idsupp=$id','_self','')\" class='BUTTON' />";
	print "</td>";
	print "</tr>";
		
}
print "</table>";
print "<br>";
print "<i>La suppression d'un question entrainera la suppression de TOUTES les réponses assujeti à la question.</i>";

Pgclose();
?>

<br><br>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
