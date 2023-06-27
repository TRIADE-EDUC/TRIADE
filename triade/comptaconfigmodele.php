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
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_comptaModele.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_comptaModeleSupp.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<script language='JavaScript'>
function newmodele() {
	resultat=document.formulaire.listemodele.options[document.formulaire.listemodele.options.selectedIndex].value;
	resultat=resultat.substr(0,19);
	if (document.formulaire.listemodele.options[document.formulaire.listemodele.options.selectedIndex].value != "-1") {
		document.getElementById("nommodele").style.visibility='hidden';
	}else{
		document.getElementById("nommodele").style.visibility='visible';
	}
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Gestion des versements" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
?>

<?php 
if (isset($_POST["create"])) {
	enrConfigVersementModele($_POST['listemodele'],$_POST["nameversement"],$_POST["montantversement"],$_POST["dateversement"],$_POST['nommodele']);
	if ($_POST['listemodele'] > 0) {
		$option="<option id='select1' value='".$_POST['listemodele']."' >".chercheNomModeleVers($_POST['listemodele'])."</option>";
	}else{
		$option="<option id='select1' value='".chercheIdConfigVersModele($_POST['nommodele'])."' >".$_POST['nommodele']."</option>";
	}
}
?>

<br />
<form method=post name="formulaire" action="comptaconfigmodele.php" >


<table border=0 align=center width="100%">
<tr><td align="right"><font class=T2><?php print "Nom du modèle"?> :</font></td>
<td>
<select  name="listemodele" onChange='newmodele()' >
<?php print $option ?>
<option id='select1' ><?php print LANGCHOIX ?></option>
<option id='select1' value="-1" ><?php print "Nouveau" ?></option>
<?php listingModele() ?>
</select>
<input type=text name='nommodele' id='nommodele' style='visibility:hidden' size=15 maxlength='15' /> 


<tr><td align="right" ><font class=T2>Intitulé du versement :</font></td>
    <td ><input type="text" name="nameversement" size='30' maxlength='30' /></td></tr>

<tr><td align="right" ><font class=T2>Montant du versement :</font></td>
    <td><input type="text" name="montantversement" size=30 /></td></tr>

<tr><td align="right" ><font class=T2>Date d'échéance :</font></td>
    <td><input type="text" name="dateversement" value="" size=12 onKeyPress="onlyChar(event)" > <?php include_once("librairie_php/calendar.php"); calendarMoiAnnee('id1','document.formulaire.dateversement',$_SESSION["langue"],"0");?></td></tr>

<tr><td height=20></td></tr>
<tr><td colspan=2 align=center ><table><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","create"); //text,nomInput</script></td><td><script language=JavaScript> buttonMagicRetour2("comptaconfig.php","_parent","Quitter")</script></td></tr></table></td></tr>
</table>
</form>
<hr>


<script>
function modif(id,dateM,montant,libelle) {
	document.getElementById('date'+id).innerHTML="<input type='text' name='newdate' value='"+dateM+"' size='9' onchange='varnewdate"+id+"=this.value;' />";	
	document.getElementById('montant'+id).innerHTML="<input type='text' name='newmontant' value='"+montant+"' size='4'  onchange='varnewmontant"+id+"=this.value'/>";	
	document.getElementById('libelle'+id).innerHTML="<input type='text'  maxlength='30' name='newlibelle' value=\""+libelle+"\" size='35' onchange='varnewlibelle"+id+"=this.value'/>";	
	document.getElementById('enr'+id).style.visibility='visible';
}

</script>

<?php
print "<table border='1' width='100%' bgcolor='#FFFFFF' bordercolor='#000000' >";
$dataModele=affModele(); // 
$j=0;
foreach($dataModele as $idmodele=>$nommodele) {
	$j++;
	print "<tr>";
	print "<td colspan='4' id='bordure' ><font class='T2'>Modèle : ".$nommodele;
	print "</font><span id='aff$j' ></span></td></tr>";
	print "<tr><td width=70 id='bordure' ></td>
		<td id='bordure' bgcolor=yellow >Libellé du versement</td>
		<td width='10%' bgcolor=yellow  id='bordure' >&nbsp;Date&nbsp;d'échéance&nbsp;</td>
		<td width='5%' bgcolor=yellow  id='bordure' >&nbsp;Montant&nbsp;</td></tr>";
	$data=recupConfigVersementModele($idmodele); //  id,refmodele,nommodele,libellevers,montantvers,datevers
	for($i=0;$i<count($data);$i++) {
		$libelle=addslashes($data[$i][3]);
		$montant=$data[$i][4];
		$id=$data[$i][0];
		$date=dateForm($data[$i][5]);
		print "<script>var varnewlibelle$j$i=\"$libelle\" ; var varnewmontant$j$i=\"$montant\" ; var varnewdate$j$i=\"$date\" ;</script>";
		print "<tr id='tr$j$i' ><form><td width=70 id='bordure' align='right' ><span id='enr$j$i' style='visibility:hidden' ><a href=\"javascript:enrModif('$id',varnewlibelle$j$i,varnewmontant$j$i,varnewdate$j$i,'aff$j','enr$j$i')\" title=\"".LANGENR."\" ><img src='image/commun/export.png'  border='0' ></a></span>&nbsp;<span id='modif$j$i'><a href=\"javascript:modif('$j$i','$date','$montant','$libelle')\" ><img src='image/commun/update1.png' border='0' /></a></span>&nbsp;<span id='supp$j$i'><a href=\"javascript:suppModif('$id','enr$j$i','supp$j$i','modif$j$i','tr$j$i','aff$j')\" title=\"".LANGacce5."\" ><img src='image/commun/trash.png' border='0' /></a></span>&nbsp;</td>
			<td id='bordure' bgcolor='#CCCCCC'><img src='image/on1.gif' width='8' height='8' /> <span id='libelle$j$i'>$libelle</span></span></td>
			<td width='5%'  bgcolor='#CCCCCC' id='bordure'>&nbsp;<span id='date$j$i' >$date</span>&nbsp;</td>
			<td width='5%'  bgcolor='#CCCCCC' id='bordure'>&nbsp;<span id='montant$j$i' >".preg_replace('/ /','&nbsp;',affichageFormatMonnaie($montant))."</span>&nbsp;</td></form></tr>";
	}
	print "<tr><td height='20' id='bordure' ></td></tr>";
}

print "</table>";


?>

<br /><br />
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
     ?>
   </BODY></HTML>
