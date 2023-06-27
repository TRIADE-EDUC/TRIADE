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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE27?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td valign=top >

     <!-- // fin  -->
<?php
// affichage de la liste d'élèves trouvées
$motif=strtolower(trim($_POST["saisie_nom_eleve"]));
$sql=<<<EOF

SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM ${prefixe}eleves e, ${prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom

EOF;
$res=execSql($sql);
$data=chargeMat($res);

?>
<?php
if( count($data) <= 0 )
        {
        print("<BR><center><font size=3>".LANGDISP1."</font><BR><BR></center>");
        }
else {
for($i=0;$i<count($data);$i++)
        {
        ?>
<FORM name="formulaire_<?php print $i?>" method=post action='gestion_dispence_suite_2.php' onsubmit="return Valide_dispense('<?php print $i ?>')">
<input type=hidden name=saisie_id value="<?php print $i?>">
<table border="1" bordercolor="#000000" width="100%">
<tr  >
<td bgcolor="#FFFFFF" id='bordure' ><?php print ucwords(LANGIMP8)?> : <B>
<input type=hidden value="<?php print trim($data[$i][3])?>" name="saisie_id_eleve_<?php print $i?>">
<?php print ucwords(trim($data[$i][1]))?></b></td></tr>
<tr>
<td bgcolor="#FFFFFF" id='bordure' ><?php print ucwords(LANGIMP9)?> : <b><?php print ucwords(trim($data[$i][2]))?></b>
</td>
</tr>
<tr>
<td bgcolor="#FFFFFF" id='bordure' ><?php print LANGDISP2?> : <input type=text name="saisie_motif_<?php print $i?>" value="<?php print ucwords(LANGINCONNU)?>" size=40 >
<?php print LANGDISP3?> : <input type=checkbox value=true name="saisie_certif_<?php print $i?>" > (<?php print LANGOUI?>)
</td>
</tr>
</table>
<table border="1" bordercolor="#000000" width="100%">
<tr>
<td bgcolor="#FFFFFF" id='bordure'>
<?php print LANGDISP5?> : <select name="saisie_matiere_<?php print $i?>" >
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_matiere();
?>
</select>
</td>

<?php for ($a=0;$a<3;$a++) { // si modif du nombre alors changer la table dispence sur le nombre de possibilite  ?>
<Tr>
<Td bgcolor="#FFFFFF" id='bordure' >
<?php print LANGDISP6?> : <input type=text name="saisie_heure_<?php print $i?>_<?php print $a?>" size=5  onKeyPress="onlyChar2(event)" >
<?php print LANGTE12?>
<select name="saisie_jour_<?php print $i?>_<?php print $a?>" >
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGLUNDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGMARDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGMERCREDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGJEUDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGVENDREDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGSAMEDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGDIMANCHE?></option>
</select>

</td>
</tr>
<?php } ?>
<tr>
<td bgcolor="#FFFFFF" id='bordure' ><?php print LANGDISP4?>
<input type=text size=13 name="saisie_date_debut_<?php print $i?>"  onKeyPress="onlyChar(event)" >
<?php
include_once("librairie_php/calendar.php");
calendar("id1$i","document.formulaire_$i.saisie_date_debut_$i",$_SESSION["langue"],"0");
?>
 <?php print LANGTE11?>
<input type=text size=13 name="saisie_date_fin_<?php print $i?>"  onKeyPress="onlyChar(event)" >
<?php
calendar("id$i","document.formulaire_$i.saisie_date_fin_$i",$_SESSION["langue"],"0");
?>
<A href='#' onMouseOver="AffBulle3('Information','./image/commun/info.jpg','<font face=Verdana size=1><?php print LANGDISP7?></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'>
<img src="./image/help.gif" align=center border=0>
</A>
</td>
</tr>
</table>
<BR>
<center><input type=submit  value="<?php print LANGBT35?> <?php print ucwords(trim($data[$i][1]))." ".ucwords(trim($data[$i][2]))?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></center><BR>
</form>
<HR>
<BR><BR><BR>
        <?php
        }
      }
?>
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
Pgclose();
?>
  <SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
   </BODY></HTML>
