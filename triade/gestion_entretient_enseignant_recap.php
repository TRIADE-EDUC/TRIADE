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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/ajax-verif-champs.js"></script>
<script language="JavaScript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" src="./librairie_js/scriptaculous.js"></script>
<script language="JavaScript" src="./librairie_js/xorax_serialize.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();

$date=date("Y");
$date2=date("Y")-1;

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<?php

if (isset($_POST["create"])) {
	ajoutEntretienEnseignant($_POST["id_liste_eleve"],$_POST["idpers"],$_POST["heure"]);

}


if (isset($_GET["eid"])) { $eid=$_GET["eid"]; }
if (isset($_POST["idpers"])) {$eid=$_POST["idpers"]; }
if($eid) {
	$sql="SELECT pers_id,nom,prenom,prenom2,type_pers,civ,photo,email FROM ${prefixe}personnel WHERE pers_id='$eid'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$nomProf=$data[0][1];
	$prenomProf=$data[0][2];
}



?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'><?php print "Temps d'accompagnement d'un enseignant" ?> </b></font></td></tr>
<tr  id='cadreCentral0' >
<td valign=top>
<br>
<table border=0 width="100%" >
<tr><td>
<font class=T2>&nbsp;&nbsp;&nbsp;</font><img src="image_trombi.php?idP=<?php print $eid ?>" border=0 >
</td><td>
<font class=T2>
Nom prénom : <b><?php print ucwords($data[0][1]." ".$data[0][2])?></b>
<br><br>
Classe : <b><?php print ucwords($data[0][3])?></b>
<br><font class=T1>
<?php 
$lvo=chercheLvo($eid);   // lv1,lv2,`option`
?>
Lv1/Spé : <a href="#" title="<?php print $lvo[0][0] ?>" ><?php print trunchaine($lvo[0][0],40); ?></a>
<br>
Lv2/Spé : <a href="#" title="<?php print $lvo[0][1] ?>" ><?php print trunchaine($lvo[0][1],40); ?></a>
<br>
Option : <a href="#" title="<?php print $lvo[0][2] ?>" ><?php print trunchaine($lvo[0][2],40); ?></a>
</font>
</td></tr></table>
<br>
<br> 
<form method="post" name="formulaire" action="gestion_entretient_enseignant_recap.php" onsubmit="return validEntretienprof()" >
<ul>
<font class='T2'>
Nombre d'heure passée : <input type='text' name='heure' size='5' value="hh:mm" onclick="this.value=''" onKeyPress="onlyChar2(event)" /><br /><br />

Classe de l'élève : <select name="saisie_classe" onChange="afficheEleve(this.value)" ><option id='select0' value=''><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select>
<br /><br />

Nom de l'élève : <select name="saisie_eleve" id='saisie_eleve' ></select><br><br>
<input type='button' value="Ajouter" class='BUTTON' onclick="ajout()"/>
<input type='hidden' value="<?php print $eid ?>" name="idpers" />

<br /><br />

</font>
</ul>
<font class='T2'>&nbsp;&nbsp;Liste des élèves : <span id='liste_eleve'></span></font>
<br><br><br>
<input type='hidden' name='id_liste_eleve' id="id_liste_eleve" />
<ul><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create");</script></ul>
<br><br>
</form>
</table>
<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'><?php print "Temps d'accompagnement déjà effectué." ?> </b></font></td></tr>
<tr  id='cadreCentral0' >
<td valign=top>
<?php
$data=listingEntretienEnseignantParReferenceViaIdprof($eid);
//idprof,duree,idclasse,date_saisie,reference,ideleve
for($i=0;$i<count($data);$i++) {
	$prof=strtoupper(recherche_personne_nom($data[$i][0],"ENS"))." ".ucwords(recherche_personne_prenom($data[$i][0],"ENS"));
	$duree=timeForm($data[$i][1]);
	$date=dateForm($data[$i][3]);
	$reference=$data[$i][4];
	print "<font class='T2'>Le $date durée : $duree ";
	$dataDetail=listingEntretienEnseignantViaReference($reference); //idprof,duree,idclasse,date_saisie,ideleve
	$listing="";
	for($j=0;$j<count($dataDetail);$j++) {
		$idclasse=$dataDetail[$j][2];
		$nomEleve=rechercheEleveNomPrenom($dataDetail[$j][4]);
		$classe=chercheClasse_nom($idclasse);
		$listing.="- $nomEleve ($classe)<br>";
	}	
	$listing=html_quotes($listing);
	print "[<a href='#' onMouseOver=\"AffBulle3('Participant(s)','image/commun/info.jpg','$listing'); window.status=''; return true;\" onMouseOut='HideBulle()'>participant</a>]</font> <a href='gestion_entretient_enseignant?supp=$reference' title='Supprimer' ><img src='image/commun/trash.png' border='0' align='center'/></a><br>";

}
?>
</td>
</tr></table>


<script>
function ajout() {
	select = document.getElementById("saisie_eleve");
	choice = select.selectedIndex;
	valeur = select.options[choice].value;
	texte = select.options[choice].text;
	document.getElementById('id_liste_eleve').value += valeur+",";
	document.getElementById('liste_eleve').innerHTML += texte +" / ";
}
</script>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
