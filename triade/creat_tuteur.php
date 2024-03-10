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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>

<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS180  ?></font></b></td></tr><tr id='cadreCentral0'><td >
<table align=center border=0 width='100%' ><tr><td>
<img src="image/commun/personne.png" align='center' />
</td><td>
<script language=JavaScript>buttonMagic("<?php print LANGMESS181 ?>","list_tuteur.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGAGENDA86 ?>","base_de_donne_importation20.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGCREAT2 ?>","suppression_compte_tuteur.php","_parent","","");</script>&nbsp;&nbsp;</td></tr></table>
</td></tr></table>
<br />
<?php
include_once("librairie_php/db_triade.php");
include_once("common/config.inc.php");
$cnx=cnx();
$affiche=affichageMessageSecurite();
$txt2=preg_replace('/\<b\>/',"",$affiche);
$txt2=preg_replace('/\<\/b\>/',"",$txt2);
$txt2=preg_replace('/\<br \/\>/',"",$txt2);
?>
<form method=post action="creat_tuteur.php" onsubmit="return verifcommun('<?php print $txt2?>')" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGMESS180 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<blockquote><BR>
<?php
if ($_SESSION["nav"] != "IE") { 
	print "<SCRIPT language=\"JavaScript\">InitBulle('#000000','#FFFFFF','red',1);</SCRIPT>";
} 
?>
<fieldset><legend><?php print LANGMODIF5 ?></legend>
<table width=100% border=0 cellpadding="2" cellspacing="2" >
<tr><td align=right ><font class="T2"><?php print LANGMESS178 ?></td><td>
<select name="saisie_intitule" > 
<?php listingCiv() ?>
</select>
</td></tr>
<tr><td align=right width=40%><font class="T2"><?php print LANGNA1?> : </font></td><td><input type=text name="saisie_creat_nom"  size=33  maxlength=30>&nbsp;<font id='color2' ><b>*</b></font> </td></tr>
<tr><td align=right><font class="T2"><?php print LANGNA2?> : </font></td><td><input type=text name="saisie_creat_prenom" size=23  maxlength=30>&nbsp;<font id='color2' ><b>*</b></font>
<A href='#' onMouseOver="AffBulle3('INFORMATION','image/commun/info.jpg','<font face=Verdana size=1><?php print "Si pas de prénom, indiquer \'inconnu\'" ?></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center border=0></A>
</td></tr>
<tr><td align=right><font class="T2"><?php print LANGNA3?> : </font></td><td><input type=text name="saisie_creat_password"  size=23 maxlength=50 >&nbsp;<font id='color2' ><b>*</b></font>
<?php
include_once("librairie_php/db_triade.php");
$affiche=affichageMessageSecurite();
?>
<A href='#' onMouseOver="AffBulle3('ATTENTION','image/commun/warning.jpg','<font face=Verdana size=1><?php print $affiche ?></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center border=0></A>
<?php
if ($_SESSION["nav"] == "IE") { 
	print "<SCRIPT language=\"JavaScript\">InitBulle('#000000','#FFFFFF','red',1);</SCRIPT>";
} 
?>
</td></tr>
<tr><td align=right><font class="T2"><?php print LANGMESS183 ?> : </font></td>
<td>
<select name='id_societe' >
<option STYLE='color:#000066;background-color:#FCE4BA' value='0' ><?php print LANGCHOIX ?></option>
<?php
select_entreprise_limit(25);
?>
</select>
</td></tr>
<tr><td align=right><font class="T2"><?php print LANGMESS184 ?> : </font></td><td><input type=text name="saisie_qualite" size=33 maxlength=100 ></td></tr>


</table></fieldset>
<br><br><br>
<fieldset><legend><?php print LANGMODIF7 ?></legend>
<TABLE width=80% border=0 cellpadding="2" cellspacing="2">
<tr><td align=right><font class="T2"><?php print LANGMODIF8 ?> : </font></td><td><input type=text name="saisie_creat_adr" size=33 maxlength=100></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF9 ?> : </font></td><td><input type=text name="saisie_creat_code" size=33 maxlength=15></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF10 ?> : </font></td><td><input type=text name="saisie_creat_commune" size=33 maxlength=40></td></tr>
<tr><td align=right><font class="T2"><?php print LANGAGENDA73 ?> : </font></td><td><input type=text name="saisie_pays" size=33 maxlength=50></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF11 ?> : </font></td><td><input type=text name="saisie_creat_tel" size=33 maxlength=18></td></tr>
<tr><td align=right><font class="T2"><?php print LANGAGENDA76 ?> : </font></td><td><input type=text name="saisie_creat_tel_port" size=33 maxlength=18></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF12 ?> : </font></td><td><input type=text name="saisie_creat_mail" size=33 maxlength=150></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMESS179 ?> : </font></td><td><input type=text name="saisie_indice_salaire" size=33 maxlength=150></td></tr>
</TABLE>
</fieldset>
<br><br>
<center>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT7?>","create"); //text,nomInput</script>

<BR></center>
</blockquote>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
</form>
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

<?php
if(isset($_POST["create"])):
	include_once("librairie_php/db_triade.php");
	validerequete("3");
    	$cnx=cnx();
	if ($_POST["saisie_creat_prenom"] == "inconnu") {
		$prenom=" ";
	}else{
		$prenom=$_POST["saisie_creat_prenom"];
	}
    	$cr=create_personnel($_POST["saisie_creat_nom"],$prenom,$_POST["saisie_creat_password"],'TUT',$_POST["saisie_intitule"],'',$_POST["saisie_creat_adr"],$_POST["saisie_creat_code"],$_POST["saisie_creat_tel"],$_POST["saisie_creat_mail"],$_POST["saisie_creat_commune"],$_POST["saisie_creat_tel_port"],$_POST["id_societe"],$_POST["saisie_pays"],$_POST["saisie_indice_salaire"],$_POST["saisie_qualite"]);
   	if($cr == 1) {
            alertJs(LANGNA4);
    	}else if ($cr == -3) {
	    		$affiche=affichageMessageSecurite2();	
			alertJs($affiche);
	}else if ($cr == -1) {
			alertJs(LANGCREAT1);
	}else {
           alertJs(LANGPASSG3);
    }
	Pgclose();
endif;
?>
   </BODY></HTML>
