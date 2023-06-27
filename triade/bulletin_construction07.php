<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onUnload="attente_close()" >
<?php include_once("./librairie_php/lib_licence.php"); include_once("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBULL5?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<form method="post" action="bulletin_construction07_2.php" >
<!-- // fin  --><br> <br>

<ul><font class="T2">Indiquer les matières figurant sur le bulletin.<br>
<br>
<?php

include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
$cnx=cnx();

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];

$ordre=ordre_matiere_visubull($_POST["saisie_classe"]); // recup ordre matiere

for($i=0;$i<count($ordre);$i++) {
	$matiere=chercheMatiereNom($ordre[$i][0]);
	$idMatiere=$ordre[$i][0];

	print "<input type=checkbox value='$idMatiere' name='listematiere[]' > ".ucwords($matiere)." <br>";

}

// recup année scolaire
?>

<br>
<?php print LANGNNOTE4 ?> : <input type=text name="saisie_titre" value="TITRE" > 
</font>
</ul>

<ul>

<script language=JavaScript>buttonMagicRetour("imprimer_trimestre.php","_parent")</script>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGBULL6 ?>","rien","onclick='attente()'"); //text,nomInput,action</script>&nbsp;&nbsp;
</ul>
<br><br>

<input type=hidden name='saisie_classe' value="<?php print $_POST["saisie_classe"];?>" >
<input type=hidden name='saisie_trimestre' value="<?php print $_POST["saisie_trimestre"];?>" >
<input type=hidden name='saisie_classe' value="<?php print $_POST["saisie_classe"];?>" >
<input type=hidden name='annee_scolaire' value="<?php print $_POST["annee_scolaire"];?>" >
<input type=hidden name='NoteUsa' value="<?php print $_POST["NoteUsa"];?>" >
<input type=hidden name='typetrisem' value="<?php print $_POST["typetrisem"];?>" >
<input type=hidden name='type_pdf' value="<?php print $_POST["type_pdf"];?>" >



</form>
<!-- // fin  -->
</td></tr></table>
<script language=JavaScript>attente_close();</script>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
print "</SCRIPT>";
endif ;
?>
</BODY></HTML>
<?php
Pgclose();
?>
