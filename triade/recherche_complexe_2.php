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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGCHER1?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<?php
if (trim($_POST["saisie_recherche_final"]) == "") {
		$disable="disabled='disabled'";
		$erreur="<font color='red'>Aucun éléments à rechercher</font>";
}else{
		$disable="";
		$erreur="";
}

$listeaffiche=$_POST["saisie_recherche_final"];
$listeaffiche=preg_replace('/,/',", ",$listeaffiche);
$listeaffiche=preg_replace('/ nomT1/',LANGEDIT8,$listeaffiche);
$listeaffiche=preg_replace('/ prenomT1/',LANGEDIT5bis,$listeaffiche);
$listeaffiche=preg_replace('/ nomT2/',LANGEDIT4,$listeaffiche);
$listeaffiche=preg_replace('/ prenomT2/',LANGEDIT5,$listeaffiche);
$listeaffiche=preg_replace('/ telephone/',LANGIMP20,$listeaffiche);
$listeaffiche=preg_replace('/ profpere/',LANGIMP21,$listeaffiche);
$listeaffiche=preg_replace('/ telprofpere/',LANGIMP22,$listeaffiche);
$listeaffiche=preg_replace('/ profmere/',LANGIMP23,$listeaffiche);
$listeaffiche=preg_replace('/ telprofmere/',LANGIMP24,$listeaffiche);
$listeaffiche=preg_replace('/ telport1/',LANGEDIT2,$listeaffiche);
$listeaffiche=preg_replace('/ lieudenaissance/',LANGEDIT6,$listeaffiche);
$listeaffiche=preg_replace('/ telport2/',LANGEDIT9,$listeaffiche);
$listeaffiche=preg_replace('/,/',", ",$listeaffiche);
?>
<form action='./recherche_complexe_3.php' method=post onsubmit="return valide_recherche_complexe_2()" name="formulaire">
<input type=hidden name="saisie_nb_recherche" value="<?php print $_POST["saisie_nb_recherche"]?>" size=6>
<input type=hidden name="saisie_fichier_format" value="<?php print $_POST["saisie_fichier_format"]?>" size=6>
<input type=hidden name="saisie_separateur" value="<?php print $_POST["saisie_separateur"]?>" size=6>
<blockquote><BR>
<font class=T2><?php print LANGCHER10 ?> : <b><?php print $listeaffiche." ".$erreur?> </b>
<input type=hidden name="saisie_recherche" value="<?php print $_POST["saisie_recherche_final"]?>" ><br><br>
     <?php print LANGCHER11 ?> :</font>
     		<select name="saisie_nombre" >
			<option STYLE='color:#000066;background-color:#FCE4BA'>0</option>
			<option value='1' STYLE='color:#000066;background-color:#CCCCFF'>1</option>
			<option value='2' STYLE='color:#000066;background-color:#CCCCFF'>2</option>
			<option value='3' STYLE='color:#000066;background-color:#CCCCFF'>3</option>
			<option value='4' STYLE='color:#000066;background-color:#CCCCFF'>4</option>
		</select> <input type="submit" name="create" value="<?php print LANGCHER9?> >" <?php print $disable ?> class="BUTTON">
      </blockquote>
      </form>
 <!-- // fin form -->
 </td></tr></table>
<br /><br />
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
