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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCHER1?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<form action='recherche_complexe_4.php' method=post  name="formulaire">
<input type=hidden name="saisie_fichier_format" value='<?php print $_POST["saisie_fichier_format"]?>' >
<input type=hidden name="saisie_separateur" value='<?php print $_POST["saisie_separateur"]?>' >
<input type=hidden name="saisie_nb_recherche" value='<?php print $_POST["saisie_nb_recherche"]?>' >
<blockquote><BR>
<?php
$listeaffiche=$_POST["saisie_recherche"];
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
<font class="T2"><?php print LANGCHER10?></font> : <b><?php print  $listeaffiche ?> </b>
<input type=hidden name="saisie_recherche" value='<?php print $_POST["saisie_recherche"]?>'><br><br>
  <font class="T2"><?php print LANGCHER11?></font> : <b><?php print $_POST["saisie_nombre"]?> </b><br><br>
  <input type=hidden  name="saisie_nombre" value='<?php print $_POST["saisie_nombre"]?>'>
<br>
 <?php
   for ($i=0;$i<$_POST["saisie_nombre"];$i++ ) {
 ?>
<font class="T2"><?php print LANGCHER12 ?></font> <select name="saisie_critere[]" >
<?php include("./librairie_php/lib_recherche_complexe.php"); ?>
</select> <br><br><font class="T2"><?php print LANGCHER13?></font> <input type="text" maxlength="30" name="saisie_valeur[]" size=20> <br /><br />
<?php
}
?>

<font class="T2"><?php print LANGCHER14 ?> : </font><input type=radio checked value="LIKE"  name="saisie_operateur" class='btradio1'  ><br>
<font class="T2"><?php print LANGCHER15 ?> : </font><input type=radio name="saisie_operateur" value="=" class='btradio1' >
<br /><br /><br />
<input type=submit value="<?php print LANGCHER16 ?>" class="BUTTON">
      </blockquote>
      </form>
 <!-- // fin form -->
 </td></tr></table>
<br /><br />
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
