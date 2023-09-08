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
<SCRIPT LANGUAGE="JavaScript" src="./librairie_js/messagerie_fenetre.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php  print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<?php

include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

if(isset($_POST["suppmess"])) {
	for($i=0;$i<$_POST["saisie_nb"];$i++) {
		$checkbox="saisie_poubelle_".$i;
		$id_supp="saisie_id_poubelle_".$i;
		$checkbox=$_POST[$checkbox];
		$id_supp=$_POST[$id_supp];
		if ($checkbox == "on") {
			$cr=suppression_message_brouillon($id_supp);
		}
	}
}
?>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >

<script>CreerFenetreBe();</script>
<SCRIPT language="JavaScript" <?php  print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php  include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php  top_h(); ?>
<a name=ancre>
<SCRIPT language="JavaScript" <?php  print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS382 ?></font></b></font></td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // fin  -->
<table width="100%" >
<form method="POST" name="form1" >

<!-- message -->
<?php


//---------
$fichier="messagerie_reception.php#idrep=$idrep";
$table="messageries";
$requete="WHERE  brouillon='1' ";
$nbaff=20;
if ((isset($_GET["nba"])) && ($deb != 1)){
	$depart=$_GET["limit"];
}else {
	$depart=0;
}


$data=affichage_messagerie_brouillon_limit($type_personne,$destinataire,$depart,$nbaff);
// id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, lu_par_utilisateur, idpiecejointe


for($i=0;$i<count($data);$i++) {
		if (fichierJointExiste($data[$i][11])) { 
			$imgpiecejointe="<img src='image/attach.gif' align='center' border='0' title='Pièce jointe' />"; 
		}else{ 
			$imgpiecejointe="";  
		}

$reponse_poubelle="<TR><td  bordercolor='#FFFFFF' colspan='4' ><input type=submit name='suppmess' value='".LANGBT50."' class='button' > ";
$reponse_poubelle.="</TD></TR>";
$reponse_checkbox="<input type=checkbox name=saisie_poubelle_".$i." onClick=\"DisplayLigne('tr".$i."')\" >";
$hidden="<input type=hidden name=saisie_id_poubelle_".$i." value=".$data[$i][0]." >";
$hidden_nb="<input type=hidden name=saisie_nb value=".count($data).">";
         
	 $imgc="lettre.gif"; 
?>
<TR id="tr<?php print $i ?>" class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
<TD width=5% > <?php  print "$reponse_checkbox"; print "$hidden";?></TD>
<TD ><A href='#'  onclick="return apercu('./messagerie_reception_brouillon.php?saisie_id_message=<?php print $data[$i][0]?>')"><img src='./image/commun/<?php print $imgc?>' align=center border=0 alt='Message'> <?php print $imgpiecejointe?> <?php print  trunchaine(stripslashes(trim($data[$i][8])),'25')?></A></TD>
<TD>
<?php print $bold?>
<?php
$titre="";
$emetteur=recherche_personne($data[$i][1]);
print $titre.$emetteur;
?>
<?php print $finbold?>
</TD>
<TD align=center width=20%><?php print $bold?><?php print dateForm($data[$i][4])?> <BR> <?php print $data[$i][5]?><?php print $finbold ?></TD>
</TR>
<?php
}
?>

<!-- fin message -->
<tr><TD height=10  colspan=4></TD></TR>
<?php  print "$reponse_poubelle"; print $hidden_nb; ?>
</table>
<table width=100% border=0 >
<tr><td align=left width=33%><br>&nbsp;<?php precedent0($fichier,$table,$depart,$nbaff,$requete); ?><br><br></td>
<td align=right width=33%><br><?php suivant0($fichier,$table,$depart,$nbaff,$requete); ?>&nbsp;<br><br></td>
</tr></table>

<!-- // fin  -->
</td></tr></table>
</form>
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
Pgclose();
?>
<?php include_once("./librairie_php/finbody.php"); ?>

     </BODY></HTML>
