<?php
session_start();
$anneeScolaire=$_POST["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menupersonnel") {
        $cnx=cnx();
        if (!verifDroit($_SESSION["id_pers"],"cahiertextes")) { 
        	accesNonReserveFen();
                exit();
        }
        Pgclose();
}else{
	validerequete("menuadmin");	
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROF37 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<?php
$cnx=cnx();


/* ordre de classement : 
 * CLASSE > ENSEIGNANT > MATIERE > SOUS MATIERE > entrées classées par dates 
 *
 * fichier 2 : ordre de classement : 
 * ENSEIGNANT >CLASSE > MATIERE > SOUS MATIERE > entrées classées par dates 
 *
 */
?>
<form name="formulaire" method="post" action="cahiertexteexport2.php"  >
<br />
<!-- // fin  -->
<table width="90%" border="0" align="center">
<tr><td align="right" width='50%' ><font class="T2"><?php print LANGBULL3 ?> :</font></td><td>
                 <select name='anneeScolaire' >
                 <?php
                 filtreAnneeScolaireSelectNote($anneeScolaire,8);
                 ?>
                 </select></td></tr>
<tr><td height='20' ></td></tr>
<tr>
<td align="right"><font class="T2"><?php print LANGBULL8?> :</font></td>
<td colspan="2"   align="left"><input type="text" value="<?php print $_COOKIE["dateDebut_export_cahierTexte"]; ?>" name="saisie_date_debut" TYPE="text" size=13  class=bouton2 >
<?php
 include_once("librairie_php/calendar.php");
 calendar("id1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0");
?>
</td>
</tr>
<tr>
<td  align="right"><br><font class="T2"><?php print LANGBULL9?>  : </font></td>
<td colspan="2"  align="left"><br><input type="text" value="<?php print $_COOKIE["dateFin_export_cahierTexte"]; ?>" name="saisie_date_fin" TYPE="text" size=13 class=bouton2 >
<?php
 include_once("librairie_php/calendar.php");
 calendar("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0");
?>
</td></tr>

<tr><td height='20' ></td></tr>

<tr><td colspan='2' align='center' >
<table align=center ><tr><td align="center"><script language=JavaScript>buttonMagicSubmit3("<?php print "Export Word" ?>","rien","");</script></td>
<td><script>buttonMagicRetour('cahiertextesadmin.php','_self')</script></td></tr></table>

</td></tr></table>
<br><br>

<?php
Pgclose();
?>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
