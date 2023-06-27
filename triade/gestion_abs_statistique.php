<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Statistiques absences et retards" ?></font></b></td></tr>
<tr id='cadreCentral0' ><td valign='top'>
     <!-- // fin  -->
<form method='post'>


<table width="100%" align="center" border="0">

<tr><td height='20'></td></tr>
<tr>
<td width=80% align=right><font class="T2"><?php print LANGBULL3 ?> :</font></td>
<td align=left><select name='anneeScolaire' onChange="this.form.submit()" >
<?php
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select>
</tr>

<tr><td height='20'></td></tr>
<tr>
<td width=80% align=right><font class="T2"><?php print "TOP 10 des absences et retards" ?> :</font></td>
<td align=left><script language=JavaScript> buttonMagic("<?php print LANGBT28?>","stat_abs_top.php","_parent","","")</script></td>
</tr>

<tr><td height='20'></td></tr>

<tr>
<td align=right><font class="T2"><?php print "Absences et Retards par classe" ?> :</font></td>
<td align=left><script language=JavaScript> buttonMagic("<?php print LANGBT28?>","stat_abs_classe.php","_parent","","")</script></td>
</tr>

<tr><td height='20'></td></tr>

<tr>
<td align=right><font class="T2"><?php print "Absences et Retards par matière" ?> :</font></td>
<td align=left><script language=JavaScript> buttonMagic("<?php print LANGBT28?>","stat_abs_matiere.php","_parent","","")</script></td>
</tr>

<tr><td height='20'></td></tr>

<tr>
<td align=right><font class="T2"><?php print "Absences et Retards par enseignant" ?> :</font></td>
<td align=left><script language=JavaScript> buttonMagic("<?php print LANGBT28?>","stat_abs_prof.php","_parent","","")</script></td>
</tr>

<tr><td height='20'></td></tr>

<tr>
<td align=right><font class="T2"><?php print "Absences et Retards par taux horaire" ?> :</font></td>
<td align=left><script language=JavaScript> buttonMagic("<?php print LANGBT28?>","stat_abs_horaire.php","_parent","","")</script></td>
</tr>

<tr><td height='20'></td></tr>

<tr><td></td><td><script language=JavaScript>buttonMagicRetour('gestion_abs_retard.php','_parent') </script></td></tr>

<tr><td height='20'></td></tr>

</table>
</form>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
