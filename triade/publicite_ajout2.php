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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body bgcolor="#FAEBD7" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2"> <b><font  color="red"><font  color="#FFFFFF">Ajout d'une bannière de publicité</font></b></td>
</tr>
<tr bgcolor="#CCCCCC">
<td >
     <!-- // fin  -->
<?php
$fichier=$_FILES['saisie_fichier']['name'];
$type=$_FILES['saisie_fichier']['type'];  //type du fichier  (format)
$tmp_name=$_FILES['saisie_fichier']['tmp_name'];
$size=$_FILES['saisie_fichier']['size'];  //taille du fichier

if ( (!empty($fichier)) &&  ($size <= 400000)) {  // 400 ko max
   if  (($type == "image/pjpeg")||($type == "image/gif")){
     $erreur_fichier="non";
     $fichier=str_replace(" ","_",$fichier); //remplace les blancs par "_" 
     move_uploaded_file($tmp_name,"../image/publicite/$fichier");
   }
}

if ($erreur_fichier == "non" ) {
?>	
<UL><BR>
<u>Nom de la bannière</u> : <b><?php print $_POST[saisie_nom_banniere]?></b><BR>
<BR>
<u>Date de mise en service</u> :  <b><?php print $_POST[saisie_date_debut]?></b> au <b><?php print $_POST[saisie_date_fin]?></b>
<BR>
<br>
<u>Fréquence d'apparution</u> :  <b><?php print $_POST[saisie_frequence]?></b> <BR>
<BR>
<u>Bannière</u> : <br>
</UL>
<center>
<a href="<?php print $_POST[saisie_lien]?>" target="_blank"><img src="../image/publicite/<?php print $fichier?>" border="0" align=center></a> 
</center>
<BR><BR>
<?php
// enregistrement dans la table
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
$cr=create_banniere($_POST[saisie_nom_banniere],$_POST[saisie_date_debut],$_POST[saisie_date_fin],$_POST[saisie_frequence],$_POST[saisie_lien],$fichier);
if($cr):
     alertJs("Nouvelle bannière créée - Active sous 48h -- Service Triade");
     $today= date ("j M, Y");
     $fichier=fopen("./".REPADMIN."/data/publicite_demande.txt","a+");
     flock($fichier, 1);
     fwrite($fichier,"<font color=red>Le $today : </font> Ajout d'un publicité <br><br>");
     fwrite($fichier,"<u>Ecole:</u> ".REPECOLE." <br><br>");
    fwrite($fichier,"<u>Contact:</u> $_SESSION[nom] $_SESSION[prenom]<br><br>");
     fwrite($fichier,"<u>Description:</u> Une publicité est ajouté = $_POST[saisie_nom_banniere] <br><br>");
     fwrite($fichier,"-------------------------------------------------<br><br>");
     flock($fichier, 3);
     fclose($fichier);
	   
else:
     error(0);
endif;
Pgclose();
// sinon
}else {
?>
<br>
<center>
<font color=red><b>ERREUR sur le fichier à transmettre !!</b></font> <br>
</center>
<ul>
- il doit être au format gif ou jpg <br />
<br />
- Taille max du fichier 30 ko <br />
<br />
<br />
 <input type=button value="< précédent" onclick="history.go(-1);">
 </ul>
 <br /><br />
<?php
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
   </BODY></HTML>
