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
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_comptaSupp.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<script language='JavaScript'>
function selectionne() {
	resultat=document.formulaire.listemodele.options[document.formulaire.listemodele.options.selectedIndex].value;
	resultat=resultat.substr(0,19);
	if (document.formulaire.listemodele.options[document.formulaire.listemodele.options.selectedIndex].value == "-1") {
		document.formulaire.nameversement.value="";
		document.formulaire.nameversement.disabled=false;
		document.formulaire.dateversement.value="";
		document.formulaire.dateversement.disabled=false;
		document.formulaire.montantversement.value="";
		document.formulaire.montantversement.disabled=false;
		document.getElementById("nameversement").style.visibility='visible';
		document.getElementById("dateversement").style.visibility='visible';
		document.getElementById("montantversement").style.visibility='visible';

	}else{
		document.formulaire.nameversement.value="";
		document.formulaire.nameversement.disabled=true;
		document.formulaire.dateversement.value="";
		document.formulaire.dateversement.disabled=true;
		document.formulaire.montantversement.value="";
		document.formulaire.montantversement.disabled=true;
		document.getElementById("nameversement").style.visibility='hidden';
		document.getElementById("dateversement").style.visibility='hidden';
		document.getElementById("montantversement").style.visibility='hidden';
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

$anneescolairefiltre=$_COOKIE["anneeScolaire"];

if (isset($_GET["anneescolairefiltre"])) {
	$anneescolairefiltre=$_GET["anneescolairefiltre"];
}


if (isset($_POST["anneescolaire"])) {
	$anneescolairefiltre=$_POST["anneescolaire"];
}


if (isset($_POST["anneescolairefiltre"])) {
	$anneescolairefiltre=$_POST["anneescolairefiltre"];
}


if (isset($_GET["idelevesupp"])) {
	$eid=$_GET["idelevesupp"];
	supprConfigCompta($eid);
	$nom=recherche_eleve_nom($eid);
	$prenom=recherche_eleve_prenom($eid);
	$idClasseEleve=chercheIdClasseDunEleve($eid);
}

if (isset($_GET["idvalid"])) {
	$eid=$_GET["eid"];
	comptaValideClasse($_GET["idvalid"],$eid);
}

if (isset($_GET["idexclu"])) {
	$eid=$_GET["eid"];
	comptaExcluClasse($_GET["idexclu"],$eid);
}

if (isset($_GET['eid'])) {
	$eid=$_GET['eid'];
	$nom=recherche_eleve_nom($eid);
	$prenom=recherche_eleve_prenom($eid);
	$idClasseEleve=chercheIdClasseDunEleve($eid);
}

if (isset($_POST['ideleve'])) {
	$eid=$_POST['ideleve'];
	$nom=recherche_eleve_nom($eid);
	$prenom=recherche_eleve_prenom($eid);
	$idClasseEleve=chercheIdClasseDunEleve($eid);
}

?>
<br />
<form method=post name="formulaire" action="comptaconfigeleve.php" >


<table border=0 align=center width="100%">
<tr><td align="right"><font class=T2><?php print "Nom de l'élève "?> :</font></td>
<td><input type="hidden" name="ideleve" value='<?php print $eid ?>' /><b>
<?php print trunchaine($nom." ".$prenom,40) ?></b></td></tr>

<tr><td align="right" ><font class=T2>Modèle de règlement :</font></td>
    <td ><select  name="listemodele" onChange='selectionne()' >
	<option id='select1' value="-1" ><?php print "" ?></option>
	<?php listingModele() ?>
	</select></td></tr>


<tr><td align="right" ><font class=T2>Intitulé du versement :</font></td>
    <td ><input type="text" name="nameversement" size='30' maxlength='30' /></td></tr>

<tr><td align="right" ><font class=T2>Montant du versement :</font></td>
    <td><input type="text" name="montantversement" size=30 /></td></tr>

<tr><td align="right" ><font class=T2>Mode de paiement :</font></td>
    <td><select name="modedepaiement" >
	<option value='' id='select0' >Choix...</option>
	<option value='Par CB' id='select1' >Par CB</option>
	<option value='Par chèque' id='select1' >Par chèque</option>
	<option value='Par paypal' id='select1' >Par paypal</option>
	<option value='Par virement' id='select1' >Par virement</option>
	<option value='Par Espèce' id='select1' >Par Espèce</option>
	</select>
</td></tr>

<tr><td align="right" ><font class=T2>Pour l'année scolaire :</font></td>
<td ><select name='anneescolaire'><?php print anneeScolaireSelect() ?></select></td></tr>

<tr><td align="right" ><font class=T2>Date d'échéance :</font></td>
    <td><input type="text" name="dateversement" value="" size=12  onKeyPress="onlyChar(event)" > <?php include_once("librairie_php/calendar.php"); calendarMoiAnnee('id1','document.formulaire.dateversement',$_SESSION["langue"],"0");?></td></tr>

<tr><td height=20></td></tr>
<tr><td colspan=2 align=center ><table><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","create"); //text,nomInput</script></td><td><script language=JavaScript> buttonMagicRetour2("comptaconfigeleve0.php","_parent","Autre élève")</script></td>
<td><script language=JavaScript> buttonMagicRetour2("comptaconfigeleve.php?idelevesupp=<?php print $eid ?>","_parent","Supprimer toute la config.")</script></td>
</tr></table></td></tr>

</table>
</form>
<hr>
&nbsp;&nbsp;<img src="image/commun/warning.gif" /> Versement déjà effectué - 
<img src="image/commun/update1.png" /> Modification -
<img src="image/commun/export.png" /> Sauvegarde -
<img src="image/commun/trash.png" /> Suppression
<br /><br />
<center>
<img src='image/commun/b_drop.png' /> Non affecté -
<img src='image/commun/valid.gif' /> Affecté 
</center>
<br>
<form method='post' action="comptaconfigeleve.php?eid=<?php print $eid?>" >
&nbsp;&nbsp;<font class=T2>Filtre : </font><select onChange='this.form.submit()' name='anneescolairefiltre' > <?php filtreAnneeScolaireSelect($anneescolairefiltre) ?> </select><br><br>
</form><br>
<?php 
if (isset($_POST["create"])) {
	if ($_POST["listemodele"] == "-1") {
		enrConfigVersementEleve($_POST["saisie_classe"],$_POST["nameversement"],$_POST["montantversement"],$_POST["dateversement"],$_POST['ideleve'],$_POST['modedepaiement'],$_POST["anneescolaire"]);
	}else{
		enrConfigVersementEleveViaModele($_POST["listemodele"],$_POST['ideleve'],$_POST["saisie_classe"],$_POST['modedepaiement'],$_POST["anneescolaire"]);
	}
}
?>

<script>
function modif(id,dateM,montant,libelle) {
	document.getElementById('date'+id).innerHTML="<input type='text' name='newdate' value='"+dateM+"' size='9' onchange='varnewdate"+id+"=this.value;' />";	
	document.getElementById('montant'+id).innerHTML="<input type='text' name='newmontant' value='"+montant+"' size='7'  onchange='varnewmontant"+id+"=this.value'/>";	
	document.getElementById('libelle'+id).innerHTML="<input type='text'  maxlength='30' name='newlibelle' value=\""+libelle+"\" size='40' onchange='varnewlibelle"+id+"=this.value'/>";	
	document.getElementById('enr'+id).style.visibility='visible';
}

</script>

<?php
print "<table border='1' width='100%' bgcolor='#FFFFFF' bordercolor='#000000'  >";
$dataClasse=affClasse(); // code_class,libelle
for($j=0;$j<count($dataClasse);$j++) {
	$idclasse=$dataClasse[$j][0];
	if ($idclasse != $idClasseEleve) { continue; }
	$classe=$dataClasse[$j][1];
	print "<tr>";
	print "<td colspan='4' id='bordure' ><font class='T2'>Classe : ".ucwords($classe);
	print "</font> / Commun à toute la classe</td></tr>";
	print "<tr><td width='70' id='bordure' ></td>
		<td id='bordure' bgcolor=yellow >Libellé du versement</td>
		<td width='10%' bgcolor=yellow  id='bordure' >&nbsp;Date&nbsp;d'échéance&nbsp;</td>
		<td width='5%' bgcolor=yellow  id='bordure' >&nbsp;Montant&nbsp;</td></tr>";
	$data=recupConfigVersement($idclasse,$anneescolairefiltre); // id,idclasse,libellevers,montantvers,datevers
	for($i=0;$i<count($data);$i++) {
		$libelle=$data[$i][2];
		$montant=$data[$i][3];
		$id=$data[$i][0];
		$modedepaiement=$data[$i][5];
		$date=dateForm($data[$i][4]);
		print "<tr>";
		$idcomptaclasse=$data[$i][0];
		if (verifcomptaExclu($idcomptaclasse,$eid)) {
			print "<td width=70 id='bordure' align='right' ><a href='comptaconfigeleve.php?idvalid=$idcomptaclasse&eid=$eid&anneescolairefiltre=$anneescolairefiltre'><img src='image/commun/b_drop.png' border='0' /></a>";
			$s="<s>";
			$ss="</s>";
		}else{
			print "<td width=70 id='bordure' align='right' ><a href='comptaconfigeleve.php?idexclu=$idcomptaclasse&eid=$eid&anneescolairefiltre=$anneescolairefiltre'><img src='image/commun/valid.gif' border='0' /></a>";
			$s="";
			$ss="";
		}
		
		print 	"</td>
			<td id='bordure' bgcolor='#CCCCCC'><img src='image/on1.gif' width='8' height='8' /><span>$s$libelle$ss</span></span></td>
			<td width='5%'  bgcolor='#CCCCCC' id='bordure'>&nbsp;<span>$s$date$ss</span>&nbsp;</td>
			<td width='5%'  bgcolor='#CCCCCC' id='bordure' align='right'>&nbsp;<span>$s".preg_replace('/ /','&nbsp;',affichageFormatMonnaie($montant))."$ss</span>&nbsp;</td></form></tr>";
	}
	print "<tr><td height='20' id='bordure' ></td></tr>";
}



print "<tr>";
print "<td colspan='4' id='bordure' ><font class='T2'>Individuel : ";
print "</font><span id='aff$j' ></span></td></tr>";
print "<tr><td width=70 id='bordure' ></td>
	<td id='bordure' bgcolor=yellow >Libellé du versement</td>
	<td width='10%' bgcolor=yellow  id='bordure' >&nbsp;Date&nbsp;d'échéance&nbsp;</td>
	<td width='5%' bgcolor=yellow  id='bordure' >&nbsp;Montant&nbsp;</td></tr>";

$data=recupConfigVersementEleve($eid,$anneescolairefiltre); // id,idclasse,libellevers,montantvers,datevers
for($i=0;$i<count($data);$i++) {
	$libelle=$data[$i][2];
	$montant=$data[$i][3];
	$id=$data[$i][0];
	$modedepaiement=$data[$i][5];
	$montant=affichageFormatMonnaie($montant);
	$date=dateForm($data[$i][4]);
	print "<script>var varnewlibelle$j$i=\"$libelle\" ; var varnewmontant$j$i=\"$montant\" ; var varnewdate$j$i=\"$date\" ;</script>";
	print "<tr id='tr$j$i' ><form><td width=70 id='bordure' align='right' >
		<span id='enr$j$i' style='visibility:hidden' ><a href=\"javascript:enrModif('$id',varnewlibelle$j$i,varnewmontant$j$i,varnewdate$j$i,'aff$j','enr$j$i')\" title=\"".LANGENR."\" ><img src='image/commun/export.png'  border='0' ></a></span>&nbsp;<span id='modif$j$i'><a href=\"javascript:modif('$j$i','$date','$montant','".addslashes($libelle)."')\" ><img src='image/commun/update1.png' border='0' /></a></span>";
	if (!verifVersementSurConfig($id)) {
		print "&nbsp;<span id='supp$j$i'><a href=\"javascript:suppModif('$id','enr$j$i','supp$j$i','modif$j$i','tr$j$i','aff$j')\" title=\"".LANGacce5."\" ><img src='image/commun/trash.png' border='0' /></a></span>&nbsp;";
	}else{
		print "<img src='./image/commun/warning.gif' border='0' align='center' title=\"ENCAISSEMENT DEJA EFFECTUE\" />";
	}
	print "</td>
	<td id='bordure' bgcolor='#CCCCCC'><img src='image/on1.gif' width='8' height='8' /><span id='libelle$j$i'>$libelle</span></span></td>
	<td width='5%'  bgcolor='#CCCCCC' id='bordure'>&nbsp;<span id='date$j$i' >$date</span>&nbsp;</td>
	<td width='5%'  bgcolor='#CCCCCC' id='bordure' align='right' >&nbsp;<span id='montant$j$i' >".preg_replace('/ /','&nbsp;',affichageFormatMonnaie($montant))."</span>&nbsp;</td></form></tr>";
}
print "<tr><td height='20' id='bordure' ></td></tr>";
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
