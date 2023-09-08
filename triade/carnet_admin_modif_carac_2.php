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
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Modification du Carnet de Suivi" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<?php 
$nom_carnet=preg_replace('/"/',"'",$_POST["saisie_nom_carnet"]);
$idcarnet=$_POST["idcarnet"];
$data=chercheTypeNotation($idcarnet);
Pgclose();

$lettreCheck=($data[0][0] == 1) ? "checked='checked'" : "";
$chiffreCheck=($data[0][1] == 1) ? "checked='checked'" : "";
$couleurCheck=($data[0][2] == 1) ? "checked='checked'" : "";
$noteCheck=($data[0][3] == 1) ? "checked='checked'" : "";
$notejulesverne=($data[0][4] == 1) ? "checked='checked'" : "";
$commentaireCheck=($data[0][5] == 1) ? "checked='checked'" : "";
?>

<font class="T2">&nbsp;&nbsp;<b>Gestion du carnet "<?php print $nom_carnet ?>"</b></font><br /><br />

<form action='carnet_admin_modif_carac_3.php' method="post">
<table border=0 align=center width=85%>
<tr><td align="left" colspan=2><font class="T2"><?php print LANGCARNET20 ?> : </font></td></tr>
<tr><td align="right" width=50%><font class="T2"><?php print LANGCARNET21 ?> : </font></td><td><input type=checkbox name="code_lettre" value="1" <?php print $lettreCheck ?> >*</td></tr>
<tr><td align="right"><font class="T2"><?php print LANGCARNET22 ?> : </font></td><td><input type=checkbox name="code_chiffre" value="1" <?php print $chiffreCheck ?> >*</td></tr>
<tr><td align="right"><font class="T2"><?php print LANGCARNET23 ?> : </font></td><td><input type=checkbox name="code_couleur"  value="1" <?php print $couleurCheck ?> >*</td></tr>
<tr><td align="right"><font class="T2"><?php print LANGCARNET24 ?> : </font></td><td><input type=checkbox name="code_note"  value="1" <?php print $noteCheck ?> > <?php print LANGCARNET25 ?> </td></tr>
<tr><td align="right"><font class="T2"><?php print "Commentaire " ?> : </font></td><td><input type=checkbox name="code_commentaire" <?php print $commentaireCheck ?> value="1"></td></tr>
<tr><td align="right"><font class="T2"><?php print "Spécif.&nbsp;Lycée&nbsp;Jules&nbsp;VERNE"  ?> : </font></td><td><input type=checkbox name="code_julesverne"  value="1" <?php print $notejulesverne ?> > **  </td></tr>


<tr><td align="left" colspan=2><br /><br /><font class="T2">* <?php print LANGCARNET26 ?> : </font></td></tr>
<tr><td colspan="2">
<table border=1 bordercolor="#000000" bgcolor="#FFFFFF">
<tr><td align=center  bgcolor="yellow"><i><?php print LANGCARNET27 ?></i></td><td   bgcolor="yellow" align=center><i><?php print LANGCARNET28 ?></i></td>
<td   bgcolor="yellow" align=center><i><?php print LANGCARNET30 ?></i></td><td   bgcolor="yellow" align=center><i><?php print LANGCARNET29 ?></i></td>
 <td   bgcolor="yellow" align=center><i><?php print LANGCARNET31 ?></i></td></tr>
<tr><td align=center>A</td><td align=center>B</td><td align=center>C</td><td align=center>D</td><td align=center>X</td></tr>
<tr><td align=center>1</td><td align=center>2</td><td align=center>4</td><td align=center>4</td><td align=center>X</td></tr>
<tr><td align=center><?php print LANGCARNET32 ?></td><td align=center><?php print LANGCARNET33 ?></td><td align=center><?php print LANGCARNET34 ?></td><td align=center><?php print LANGCARNET35 ?></td><td align=center>X</td></tr>
</table></td></tr>

<tr><td align="left" colspan=2><br /><br /><font class="T2">** <?php print LANGCARNET26 ?> : </font></td></tr>
<tr><td colspan="2">
<table border=1 bordercolor="#000000"  bgcolor="#FFFFFF">
<tr><td align=center  bgcolor="yellow"><i>Notation</i></td><td   bgcolor="yellow" align=center><i><?php print "Désignation" ?></i></td></td></tr>
<tr><td align=center>A </td><td> Compétence acquise et bien installé.</td></tr>
<tr><td align=center>AR </td><td> Compétence acquise, mais mal installé (à renforcer) </td></tr>
</table></td></tr>

<tr><td align=center colspan="2"><br />
<table><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print "Continuer --->" ?>","rien"); //text,nomInput</script> </td></tr></table>
</td></tr>
</table>
<input type=hidden name="idcarnet" value="<?php print $idcarnet ?>" />
<input type="hidden" name="saisie_nom_carnet" value="<?php print $nom_carnet ?>" />
</form>
<br /><br />

<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
