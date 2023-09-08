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
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<?php include("./librairie_php/googleanalyse.php"); ?>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();

if (isset($_POST["saisie_date"])) {
	$date=$_POST["saisie_date"];
	$dateFin=$_POST["saisie_date_fin"];
}else{
	$date=dateDMY();
	$dateFin=dateDMY();
}

?>
<?php 
if (isset($_SESSION["triretenue"])) { $tri=$_SESSION["triretenue"];}else{$tri="classe"; }
if (isset($_POST["tri"])) {
	$tri=$_POST["tri"];
	$_SESSION["triretenue"]=$tri;
}
$selectedTriNom="";$selectedTriClasse="";
if ($tri == "classe") { $selectedTriClasse="selected='selected'"; }
if ($tri == "nom") { $selectedTriNom="selected='selected'"; }
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>

<script language="JavaScript" >
function print_sanction_du_jour2(){
	var ok=confirm("Confirmez l'impression \n Service Triade");
        if (ok) {
		open('gestion_sanction_du_jour_print.php?debut=<?php print $date ?>&fin=<?php print $dateFin ?>&tri=<?php print $tri ?>','_blank','');		
        }
}
</script>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPARENT15bis?> <?php print dateDMY()?> </font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<BR>
<!-- // fin  -->
<UL> <font class="T2"><?php print LANGDISC2bis?> :</font>
<a href="#" onclick="print_sanction_du_jour2();">
<img src="./image/print.gif" align=center border=0 alt="<?php print LANGaffec_cre41?> "> </UL>
</A>


<form method=post name="formulaire" >
&nbsp;&nbsp;&nbsp;<?php print LANGTE2 ?>&nbsp;&nbsp;
<input type=text name=saisie_date value="<?php print $date?>"  onclick="this.value=''" size=12 class=bouton2>
<?php
include_once("librairie_php/calendar.php");
calendar("id1","document.formulaire.saisie_date",$_SESSION["langue"],"0");
?>&nbsp;&nbsp;
<?php print LANGTE11 ?> 
&nbsp;&nbsp;
<input type=text name=saisie_date_fin value="<?php print $dateFin?>"  onclick="this.value=''" size=12 class=bouton2>
<?php
include_once("librairie_php/calendar.php");
calendar("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0");
?>&nbsp;&nbsp;
<input type=submit name="modif_date" value="<?php print LANGBT28 ?>"  class=bouton2>

<br><br>
&nbsp;&nbsp;<font class='T1'>Trier sur : </font>
<select name="tri" >
<option value="classe" id='select1' <?php print $selectedTriClasse ?> >Par classe</option>
<option value="nom"  id='select1' <?php print $selectedTriNom ?> >Par Nom</option>
</select>
</form>


<table border="1" bordercolor="#000000" width="100%" style="border-collapse: collapse;" >
<tr>
<TD bgcolor=yellow width='50%'  >&nbsp;<?php print LANGNA1 ?>&nbsp;&nbsp;<?php print LANGNA2 ?>&nbsp;</TD>
<TD bgcolor=yellow width=5%>&nbsp;<?php print LANGELE4 ?>&nbsp;</TD>
<TD bgcolor=yellow width=30 >&nbsp;<?php print "Attribué&nbsp;par" ?>&nbsp;</TD>
<TD bgcolor=yellow width=5% align=center>&nbsp;<?php print "Info." ?>&nbsp;</TD>
<TD bgcolor=yellow width=5% align=center>&nbsp;<?php print LANGDISC17 ?>.&nbsp;</TD>
<?php
$data=recherche_sanction_du_jour_2bis($date,$dateFin,$tri);
// id,id_eleve,motif,id_category,date_saisie,origin_saisie,enr_en_retenue,signature_parent,attribuer_par,devoir_a_faire,description_fait
$a=0;
for($i=0;$i<count($data);$i++) {
	$a++;
	$ideleve=$data[$i][1];
	$classe=chercheClasse(chercheIdClasseDunEleve($data[$i][1]));
	$datesaisie=dateForm($data[$i][4]);
?>
	<TR class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td ><?php print infoBulleEleveSansLoupe($data[$i][1],ucwords(recherche_eleve_nom($data[$i][1])))?>
	<?php print ucwords(recherche_eleve_prenom($data[$i][1]))?></td>
	<td ><?php print $classe[0][1]?></td>
	<td >&nbsp;<?php print preg_replace('/ /','&nbsp;',$data[$i][8]) ?>&nbsp;</td>
	<td bgcolor='#FFFFFF'align=center valign=top >
	<?php
	$message1=html_quotes($data[$i][2]);
	$message2=html_quotes($data[$i][9]);
	$fait=html_quotes($data[$i][10]);
	?>
	<a href="#" onMouseOver="AffBulle('<font class=T2 ><font color=#FFFFFF><u>Date de saisie</u> :</font> <?php print $datesaisie ?><br /><font color=#FFFFFF ><u><?php print LANGPARENT15."</u></font><font color=#000000 > : ".$message1 ?><br /> <font color=#FFFFFF><u>Description des faits</u> </font><font class=T2 color=#000000 > :&nbsp;&nbsp;<br> <?php print $fait ?> </font><br /> <font color=#FFFFFF> <u>Devoir à faire</u> </font><font class=T2 color=#000000 > : &nbsp;&nbsp;<br><?php print $message2 ?></font>');" onMouseOut='HideBulle()'>
	<img src="./image/visu.gif" align=center border=0>
	</A>
	</td>
	<td bgcolor='#FFFFFF' align=center>
	<a href="#" onMouseOver="AffBulle('<font size=2> <?php print LANGABS41?> : <b><?php print cherchetel($ideleve)?></B>&nbsp;&nbsp;<BR> <?php print "Portable 1 " ?> : <b><?php print cherchetelportable1($ideleve)?> </b> &nbsp;&nbsp;<br> <?php print "Portable 2 " ?> : <b><?php print cherchetelportable2($ideleve)?> </b><BR> <?php print LANGABS39?> : <b><?php print cherchetelpere($ideleve)?></b>&nbsp;&nbsp;<BR> <?php print LANGABS40?> : <b><?php print cherchetelmere($ideleve)?> </b> &nbsp;&nbsp;<br> Email : <b><?php print cherchemail($ideleve)?> </b>  &nbsp;&nbsp;</FONT>');" onMouseOut='HideBulle()'>
	<img src="./image/l_port.gif" align=center border=0></A>
<input type=hidden name="saisie_date_<?php print $a?>" value="<?php print $data[$i][1]?>" >
<input type=hidden name="saisie_heure_<?php print $a?>" value="<?php print $data[$i][2]?>" >
<input type=hidden name="saisie_id_<?php print $a?>" value="<?php print $data[$i][0]?>" >
	</td>
	</TR>

<?php


	}

print "</table>";
?>
<BR><br>
<input type=hidden name="saisie_nb" value="<?php print $a?>" >
<!-- 
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("Mise à jour des retenues","rien"); //text,nomInput</script>
</td></tr></table>
</form>
-->
<BR>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
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
