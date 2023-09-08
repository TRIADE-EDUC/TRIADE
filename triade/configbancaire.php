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
<form method=post onsubmit="return verifcommun()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Enregistrement des informations bancaires"?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
     <!-- // fin  -->
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
if (isset($_POST["create"])) {
	if (check_rib($_POST["codebanque"], $_POST["guichet"], $_POST["num_compte"], $_POST["rib"])) {
		enr_parametrage("bancsociete",$_POST["saisie_societe"]);
		enr_parametrage("banccode",$_POST["codebanque"]);
		enr_parametrage("bancguic",$_POST["guichet"]);
		enr_parametrage("bancompte",$_POST["num_compte"]);
		enr_parametrage("banrib",$_POST["rib"]);
		if (isValideIBAN($_POST["iban"])) {
			enr_parametrage("iban",$_POST["iban"]);
			alertJs(LANGDONENR);
		}else{
			alertJs("Erreur IBAN");	
		}
		
	}else{
		alertJs("Erreur RIB");
	}
}

$bancsoc=aff_enr_parametrage("bancsociete");
$banccode=aff_enr_parametrage("banccode");
$bancguic=aff_enr_parametrage("bancguic");
$bancompte=aff_enr_parametrage("bancompte");
$banrib=aff_enr_parametrage("banrib");
$iban=aff_enr_parametrage("iban");

if ($iban[0][1] == "") { 
	$iban=Rib2Iban($banccode[0][1],$bancguic[0][1],$bancompte[0][1],$banrib[0][1]);
}else{
	$iban=$iban[0][1];
}

Pgclose();
?>
<BR>
<TABLE border=0 align=center>
<tr>
<td align="right"><font class="T2"><?php print "Nom de votre société"?> :</font></td>
<td><input type=text name="saisie_societe" value="<?php print $bancsoc[0][1] ?>" size=33 maxlength=30></td>
</tr>

<tr>
<td align="right"><font class="T2"><?php print "Code Banque"?> :</font></td>
<td><input type=text name="codebanque" value="<?php print $banccode[0][1] ?>" size=10 maxlength=10></td>
</tr>

<tr>
<td align="right"><font class="T2"><?php print "Guichet"?> :</font></td>
<td><input type=text name="guichet" value="<?php print $bancguic[0][1] ?>" size=5 maxlength=5></td>
</tr>


<tr>
<td align="right" valign="top"><font class="T2"><?php print "N° compte"?>  :</font></td>
<td><input type=text name="num_compte" value="<?php print $bancompte[0][1] ?>" size=33 maxlength=50 /></td>
</tr>

<tr>
<td align="right"><font class="T2"><?php print "Cle RIB"?> :</font></td>
<td><input type=text name="rib" value="<?php print $banrib[0][1] ?>" size=2 maxlength=2></td>
</tr>


<tr>
<td align="right"><font class="T2"><?php print "IBAN"?> :</font></td>
<td><input type=text name="iban" value="<?php print $iban ?>" size=33 maxlength=40 ></td>
</tr>


<tr><td colspan=2 align=center>
<br><br><table align=center border=0><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print "Enregistrement des informations bancaires"?>","create"); //text,nomInput</script>
</td><tr></table>
<br><br>
</td></tr>

</table>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
