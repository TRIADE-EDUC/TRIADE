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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
        if (empty($_SESSION["adminplus"])) {
               print "<script>";
                print "location.href='./base_de_donne_key.php'";
                print "</script>";
                exit;
        }
include_once('librairie_php/db_triade.php');
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION["membre"].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION["membre"]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE21?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- //  debut --><br><br>
<center>
<?php
$nom_classe=chercheClasse($_POST["saisie_classe_envoi"]);
?>
<?php print LANGPER35?> <b><?php print $nom_classe[0][1]?></b> <?php print LANGPER35bis ?>
<?php
//--------------------------//

if(delAffectation($_POST["saisie_classe_envoi"],$_POST["anneeScolaire"])){
	vide_notes_classe($_POST["saisie_classe_envoi"],$_POST["anneeScolaire"]);
	history_cmd($_SESSION["nom"],"SUPPRESSION","affectation $nom_classe / année scolaire : ".$_POST["anneeScolaire"]);
}

//--------------------------//
?>
</center>
<br><br>
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGBT48?>","acces2.php","_parent","","") //value,lien,name,option,actionpossible</script>
<script language=JavaScript>buttonMagic("<?php print LANGBT47?>","suppression_affectation0.php","_parent","",""); </script>&nbsp;&nbsp;
</td></tr>
</table>
<br><br>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
