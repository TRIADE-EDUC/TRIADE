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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
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
<tr id='coulBar0'><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE39?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGBT11?>","list_suppleant.php","liste_suppleant","height=300,width=700,scrollbars=yes,status=no","");</script>
<script language=JavaScript>buttonMagic("<?php print "Supprimer affectation suppléant"  ?>","suppression_compte_suppleant.php","_parent","","");</script>&nbsp;&nbsp;</td></tr></table>
</td></tr></table>
<br />

<form method=post onsubmit="return verifsuppleant()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE9?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<blockquote><BR>
<fieldset><legend><?php print LANGMODIF5 ?></legend>
<table width=80% border=0 cellpadding="2" cellspacing="2" align=center>
<tr><td align=right ><font class="T2">Civ : </font></td><td>
<select name="saisie_intitule" > 
<?php 
include_once('librairie_php/db_triade.php');
listingCiv();
?>
</select>
</td></tr>
<tr><td align=right width=55%><font class="T2"><?php print LANGNA1?> : </font></td><td><input type=text name="saisie_creat_nom"  maxlength=40>&nbsp;<font id='color2' ><b>*</b></font> </td></tr>
<tr><td align=right><font class="T2"><?php print LANGNA2?> : </font></td><td><input type=text name="saisie_creat_prenom"   maxlength=40>&nbsp;<font id='color2' ><b>*</b></font> </td></tr>
<tr><td align=right><font class="T2"><?php print LANGNA3?> : </font></td><td><input type=text name="saisie_creat_password"  maxlength=50>&nbsp;<font id='color2' ><b>*</b></font> </td></tr>
<tr><td align=right><font class="T2"><?php print LANGNA5?>: </font></td><td><select name="saisie_remplacement">
    <option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
$cnx=cnx();
select_personne_2('ENS',30); // creation des options
?>
                                 </select> </td></tr>
<tr><td align=right colspan="2"><font class="T2"><?php print ucwords(LANGTE2)?> <input type=text size=12 name='saisie_date_entree' value='<?php print dateDMY() ?>'> <?php print ucwords(LANGTE10)?></font> 
<input type=text name="saisie_date_sortie" size=12 value='inconnu' onclick="this.value='jj/mm/aaaa'">
</td></tr>
</table>
</fieldset>
<br><br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT7?>","create"); //text,nomInput</script>
<br />
<BR></center>
     </blockquote>
     <!-- // fin  -->
     </td></tr></table>
     </form>
     <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
     <SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<?php
if(isset($_POST["create"])):
        $cr=create_suppleant($_POST["saisie_creat_nom"],$_POST["saisie_creat_prenom"],$_POST["saisie_creat_password"],$_POST["saisie_remplacement"],$_POST["saisie_date_entree"],$_POST["saisie_date_sortie"],$_POST["saisie_intitule"]);
        if($cr == 1){
                alertJs(LANGNA4);
        }else if ($cr == -3) {
			alertJs(LANGPASSG2);
		}else{
                	alertJs(LANGPASSG3);
        }
endif;
        Pgclose();
?>


</BODY></HTML>
