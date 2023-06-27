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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
// connexion P
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form  method=post onsubmit="return verifcreatclasse()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE12?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<BR>
&nbsp;&nbsp;<font class=T2><?php print LANGGRP6 ?></font> : <input type=text name="saisie_creat_classe" size=30  maxlength='30'><BR><br>
&nbsp;&nbsp;<font class=T2><?php print LANGMESS206 ?></font> : <input type=text name="saisie_classe_long" size=60  maxlength='250'><BR><br>
&nbsp;&nbsp;<font class=T2><?php print LANGMESS207 ?></font> : <select name="saisie_site"><?php select_site(1) ?></select><BR><br>
&nbsp;&nbsp;<font class=T2><?php print LANGTMESS506 ?></font> : <input type=text name="specification" size=60  maxlength='200'><BR><br>
&nbsp;&nbsp;<font class=T2><?php print LANGTMESS528 ?></font> : <select name="saisie_langue">
									 <option value='Français / French' id='select1' >Français / French</option>
									 <option value='Anglais / English' id='select1' >Anglais / English</option>
									 <option value='Espagnol / Spanish' id='select1' >Espagnol / Spanish</option>
									 </select><BR><br>
&nbsp;&nbsp;<font class=T2><?php print LANGTMESS512 ?></font> : <select name="saisie_niveau">
									<option value='' id='select0'><?php print LANGCHOIX ?></option>
									<optgroup label="Universitaire"></optgroup>
									<option value='A1' id='select1' >1er année</option>
									<option value='A2' id='select1' >2ieme année</option>
									<option value='A3' id='select1' >3ieme année</option>
									<option value='A4' id='select1' >4ieme année</option>
									<option value='A5' id='select1' >5ieme année</option>
									<option value='PREPA' id='select1' >PREPA</option>
									<option value='M1' id='select1' >Master 1</option>
									<option value='M2' id='select1' >Master 2</option>
									<optgroup label="Livret Scolaire"></optgroup>
									<option value="cycle 2" id='select1' >Cycle 2</option>
									<option value="cycle 3" id='select1' >Cycle 3</option>
									<option value="cycle 4" id='select1' >Cycle 4</option>

				
								     </select><BR><br>
<BR><bR>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT14?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("<?php print LANGCLAS1?>","list_classe.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGSUPP21 ?>","suppression_classe.php","_parent","","");</script>
<br><br><br>

<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
if(isset($_POST["create"])):
        // creation
	$classenom=$_POST["saisie_creat_classe"];
	$classenom=str_replace("\'","",$classenom);
	$classenom=str_replace("\"","",$classenom);
	$saisie_classe_long=$_POST["saisie_classe_long"];
	$saisie_classe_long=str_replace("\"","",$saisie_classe_long);
	$saisie_niveau=$_POST["saisie_niveau"];
	$saisie_langue=$_POST["saisie_langue"];
	$specification=$_POST["specification"];
	
        $cr=create_classe22($classenom,$saisie_classe_long,$_POST["saisie_site"],$saisie_langue,$saisie_niveau,$specification);
        if($cr):
                alertJs(LANGGRP7);
        else:
                alertJs(LANGTMESS447);
        endif;
        Pgclose();
endif;
?>
   </BODY></HTML>
