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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
Modification de la Retenue  de <font id="color2"><?php print recherche_eleve($_GET["saisie_id"])?></font> </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<BR>
<form name=formulaire method="post" action="gestion_discipline_retenue_non_fait_suite_2.php" onsubmit="return verif_retenue_non_fait()">
<UL>
<font class="T2">Indiquez la nouvelle date pour la retenue <input type=text name=datenews size=13 value="<?php print dateForm($_GET["saisie_date"]) ?>"  >
<?php
include_once("librairie_php/calendar.php");
calendar('id1','document.formulaire.datenews',$_SESSION["langue"],"1");
?>
<BR>
<BR>
Indiquez l'heure de la retenue <input type=text name=heurenews size=7 value="<?php print timeForm($_GET["saisie_heure"]) ?>" >
<BR>
<BR>
Indiquez la duréee de la retenue <input type=text name=dureenews size=7 value="<?php print timeForm($_GET["saisie_duree"]) ?>" >
<BR>
<BR><br>
<?php
$devoir=rechercheDevoirRetenu($_GET["saisie_id"],$_GET["saisie_date"],$_GET["saisie_heure"]);
$description_fait=rechercheDescriptionFaitRetenu($_GET["saisie_id"],$_GET["saisie_date"],$_GET["saisie_heure"]);
?>
<input type=hidden name=saisie_date value="<?php print $_GET["saisie_date"]?>" >
<textarea name="devoir" style="display:none" ><?php print $devoir ?></textarea>
<textarea name="description_fait" style="display:none" ><?php print $description_fait ?></textarea>
<input type=hidden name=saisie_heure value="<?php print $_GET["saisie_heure"]?>" >
<input type=hidden name=saisie_id value="<?php print $_GET["saisie_id"]?>" >
<script language=JavaScript>buttonMagicSubmit("Valider le changement","create"); //text,nomInput</script>
</form>
<form method=POST action="gestion_discipline_retenue_non_fait_suite_2.php">
<input type=hidden name=saisie_date value="<?php print $_GET["saisie_date"]?>" >
<input type=hidden name=saisie_heure value="<?php print $_GET["saisie_heure"]?>" >
<input type=hidden name=saisie_id value="<?php print $_GET["saisie_id"]?>" >
<script language=JavaScript>buttonMagicSubmit("Supprimer cette retenue","supp"); //text,nomInput</script>
<br><br>
</form>
</font>
</UL>
<BR>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
   <?php
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY></HTML>
