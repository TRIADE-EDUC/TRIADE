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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php"); 
validerequete("menuadmin");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCARNET19 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<?php 

$nom_carnet=$_POST["saisie_nom_carnet"];
$code_lettre=(isset($_POST["code_lettre"])) ? 1 : 0 ; 
$code_chiffre=(isset($_POST["code_chiffre"])) ? 1 : 0 ; 
$code_couleur=(isset($_POST["code_couleur"])) ? 1 : 0 ; 
$code_note=(isset($_POST["code_note"])) ? 1 : 0 ; 
$code_julesverne=(isset($_POST["code_julesverne"])) ? 1 : 0 ; 
$code_commentaire=(isset($_POST["code_commentaire"])) ? 1 : 0 ; 

?>

<font class="T2">&nbsp;&nbsp;<b><?php print LANGCARNET38 ?> "<?php print $nom_carnet ?>"</b></font><br /><br />

<form action='carnet_admin_creation_4.php' method="post">
<table border=0 align=center width=85%>
<tr><td align="left" colspan=2><font class="T2"><?php print LANGCARNET39 ?> : </font><br /><br /></td></tr>
<tr><td align="right" width=50%><font class="T2"><?php print LANGCARNET40 ?>  : </font></td>
<td><select name="saisie_nb_periode">
<option value=1 id="select1">1 <?php print LANGCARNET36 ?></option>
<option value=2 id="select1">2 <?php print LANGCARNET37 ?></option>
<option value=3 id="select1">3 <?php print LANGCARNET37 ?></option>
<option value=4 id="select1">4 <?php print LANGCARNET37 ?></option>
<option value=5 id="select1">5 <?php print LANGCARNET37 ?></option>
</select>
</td></tr>

<tr><td align=center colspan="2"><br />
<table><tr><td>
<script language=JavaScript>buttonMagicRetour2("carnet_admin.php","_parent","<?php print LANGCIRCU14?>");</script>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGCONTINUER ?>","rien"); //text,nomInput</script>&nbsp;&nbsp; </td></tr></table>
</td></tr>
</table>

<input type="hidden" name="saisie_nom_carnet" value="<?php print $nom_carnet ?>" />
<input type="hidden" name="code_lettre" value="<?php print $code_lettre ?>" />
<input type="hidden" name="code_chiffre" value="<?php print $code_chiffre ?>" />
<input type="hidden" name="code_couleur" value="<?php print $code_couleur ?>" />
<input type="hidden" name="code_note" value="<?php print $code_note ?>" />
<input type="hidden" name="code_julesverne" value="<?php print $code_julesverne ?>" />
<input type="hidden" name="code_commentaire" value="<?php print $code_commentaire ?>" />

</form>
<br /><br />

<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois exécuter
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
