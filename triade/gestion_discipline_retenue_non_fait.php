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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Retenue non effectuées </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php

//<UL> Imprimer les retenues non effectuer à ce jour :
//<a href="#" onclick="print_retenue_non_effectuer();">
//<img src="./image/print.gif" align=center border=0 alt="Imprimer"> </UL>
//</A>
$im1="";
$im2="";
$im3="";
$im4="";
if (isset($_POST["trie"])) {
	if ($_POST["trie"] == "nom") { $orderby="e.nom,e.prenom"; $im1="<img src='image/commun/za2.png' />"; } 
	if ($_POST["trie"] == "date") { $orderby="d.date_de_la_retenue DESC"; $im2="<img src='image/commun/za2.png' />"; } 
	if ($_POST["trie"] == "dateinv") { $orderby="d.date_de_la_retenue "; $im3="<img src='image/commun/za.png' />"; } 
	if ($_POST["trie"] == "classe") { $orderby="e.classe,e.nom,e.prenom"; $im4="<img src='image/commun/za2.png' />"; } 

}else{
	$orderby="e.classe,e.nom,e.prenom";
	$im4="<img src='image/commun/za2.png' />";
}

include("./librairie_php/class.writeexcel_workbook.inc.php");
include("./librairie_php/class.writeexcel_worksheet.inc.php");

$fichierxls="./data/fichier_ASCII/export_discipline_".$_SESSION["id_pers"].".xls";
@unlink($fichierxls);

$workbook = new writeexcel_workbook($fichierxls);
$worksheet =& $workbook->addworksheet("Listing Retenu non fait.");

$header =& $workbook->addformat();
$header->set_color('white');
$header->set_align('center');
$header->set_align('vcenter');
$header->set_pattern();
$header->set_fg_color('blue');

$center =& $workbook->addformat();
$center->set_align('left');

$worksheet->set_column('A:B',10);
$worksheet->set_column('B:C',20);
$worksheet->set_column('C:D',20);
$worksheet->set_column('D:E',30);
$worksheet->set_column('E:F',10);
$worksheet->set_column('F:G',50);


$worksheet->write('A1',"Id Etudiant",$header);
$worksheet->write('B1',"Nom Etudiant",$header);
$worksheet->write('C1',"Prénom Etudiant",$header);
$worksheet->write('D1',"Classe",$header);
$worksheet->write('E1',"le",$header);
$worksheet->write('F1',"Motif.",$header);

?>
<br>
<form method='post' >
&nbsp;&nbsp;Trie&nbsp;:&nbsp;<select name='trie' onchange="this.form.submit()" >
	<option value='classe' id='select1'><?php print LANGCHOIX ?></option>
	<option value='classe' id='select0'>par classe</option>
	<option value='date' id='select0'>par date</option>
	<option value='dateinv' id='select0'>par date inversée</option>
	<option value='nom' id='select0'>par nom</option>
	</select> <span id='affexcel' ></span>
</form>
<table border="1" bordercolor="#000000" width="100%">
<tr>
<TD bgcolor=yellow width=25%>&nbsp;Nom&nbsp;Prénom<?php print $im1 ?>&nbsp;</TD>
<TD bgcolor=yellow width=15%>&nbsp;Classe&nbsp;<?php print $im4 ?>&nbsp;</TD>
<TD bgcolor=yellow width=10% >&nbsp;Le&nbsp;<?php print $im2 ?><?php print $im3 ?>&nbsp;</TD>
<TD bgcolor=yellow width=10% >&nbsp;Reporter</TD>
<TD bgcolor=yellow width=5% align=center>&nbsp;Motif&nbsp;</TD>
<TD bgcolor=yellow width=5% align=center>&nbsp;Tel.&nbsp;</TD>
<?php
$data_2=affRetenu_eleve_non_effectue($orderby);
// $data : tab bidim - soustab 3 champs
$H=1;
for($j=0;$j<count($data_2);$j++) {
	$classe=chercheClasse($data_2[$j][13]);
	$repport=$data_2[$j][14];
	if ($repport != "0000-00-00") { $imgreport="<a href='#'  onMouseOver=\"AffBulle('<font size=2> Report du ".dateForm($repport)." </FONT>');\" onMouseOut='HideBulle()'><img src='./image/commun/important.png' border='0'></a>"; }else{  $imgreport=''; }
?>
	<TR class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td ><?php $lien=ucwords($data_2[$j][11])." ".ucwords($data_2[$j][12]); infoBulleEleveSansLoupe($data_2[$j][0],$lien); ?></td>
	<td ><?php print $classe[0][1]?></td>
	<td ><?php print dateForm($data_2[$j][1])?></td>
	<td >
	<input type=button value=Reporter onclick="open('gestion_discipline_retenue_non_fait_suite.php?saisie_id=<?php print $data_2[$j][0]?>&saisie_date=<?php print $data_2[$j][1]?>&saisie_heure=<?php print $data_2[$j][2]?>&saisie_duree=<?php print $data_2[$j][10]?>','_parent','');this.value='<?php print LANGPROF18 ?>';" class=BUTTON >
	</td>
	<td align=center valign=top >
	<?php
	$texte=$data_2[$j][7];
?><?php print $imgreport ?>&nbsp;<a href="#" onMouseOver="AffBulle('<font size=2><b><?php print html_quotes($texte) ?></B></FONT>');" onMouseOut='HideBulle()'>
	<img src="./image/visu.gif" align=center border=0>
	</A>
	</td>
	<td  align=center>
	<a href="#" onMouseOver="AffBulle('<font size=2> Tel Dom : <b><?php print $data[$i][4]?></B><BR> <?php print LANGABS51 ?> : <b><?php print $data[$i][5]?></b><BR> <?php print LANGABS52?> : <b><?php print $data[$i][6]?> </b> </FONT>');" onMouseOut='HideBulle()'>
	<img src="./image/l_port.gif" align=center border=0>
	</A>
	</td>
	</TR>
<?php

	$H++;
	$worksheet->write("A$H",$data_2[$j][0],$center);
	$worksheet->write("B$H",$data_2[$j][11],$center);
	$worksheet->write("C$H",$data_2[$j][12],$center);
	$worksheet->write("D$H",$classe[0][1],$center);
	$worksheet->write("E$H",dateForm($data_2[$j][1]),$center);
	$worksheet->write("F$H","$texte",$center);
}
print "</table>";

$workbook->close();


?>

<script>document.getElementById('affexcel').innerHTML="&nbsp;&nbsp;&nbsp;<input type=button onclick=\"open('visu_document.php?fichier=<?php print $fichierxls?>','_blank','');\" value='<?php print "Récupération au format xls" ?>'  class='bouton2'>";
</script>
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
