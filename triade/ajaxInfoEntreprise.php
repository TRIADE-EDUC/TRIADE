<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/choixlangue.php");
include_once("./librairie_php/langue.php");

$cnx=cnx();
if (isset($_POST["idEntreprise"])) {
	$data=recherche_activite_id($_POST["idEntreprise"]);
	// id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,nbchambre,siteweb,grphotelier,nbetoile,registrecommerce,siren,siret,formejuridique,secteureconomique,INSEE,NAFAPE,NACE,typeorganisation
	for($i=0;$i<count($data);$i++) {
		$infoEntreprise.="<font class=T2>";
		$infoEntreprise.=stripslashes(LANGSTAGE39)." : <b><font color=red>".$data[$i][1]."</font></b><br>";

		$infoEntreprise.=stripslashes("Registre du commerce")." : <b>".stripslashes($data[$i][17])."</b><br>";
		$infoEntreprise.=stripslashes("SIREN")." : <b>".stripslashes($data[$i][18])."</b><br>";
		$infoEntreprise.=stripslashes("SIRET")." : <b>".stripslashes($data[$i][19])."</b><br>";
		$infoEntreprise.=stripslashes("Forme juridique")." : <b>".stripslashes($data[$i][20])."</b><br>";
		$infoEntreprise.=stripslashes("Secteur économique")." : <b>".stripslashes($data[$i][21])."</b><br>";
		$infoEntreprise.=stripslashes("Secteur INSEE")." : <b>".stripslashes($data[$i][22])."</b><br>";
		$infoEntreprise.=stripslashes("Code NAF/APE")." : <b>".stripslashes($data[$i][23])."</b><br>";
		$infoEntreprise.=stripslashes("Branche d'activité (NACE)")." : <b>".stripslashes($data[$i][24])."</b><br>";
		$infoEntreprise.=stripslashes("Type d'organisation")." : <b>".stripslashes($data[$i][25])."</b><br>";
 



		$infoEntreprise.=stripslashes(LANGSTAGE40)." : <b>".stripslashes($data[$i][7])."</b><br>";
		$infoEntreprise.=stripslashes(LANGSTAGE28)." : <b>".stripslashes($data[$i][3])."</b><br>";
		$infoEntreprise.=stripslashes(LANGSTAGE30)." : <b>".stripslashes($data[$i][5])."</b><br>";
		$infoEntreprise.=stripslashes(LANGSTAGE29)." : <b>".stripslashes($data[$i][4])."</b><br>";
		$infoEntreprise.=stripslashes(LANGSTAGE27)." : <b>".stripslashes($data[$i][2])."</b><br>";
		$infoEntreprise.=stripslashes(LANGSTAGE42)." : <b>".stripslashes($data[$i][8])." / ".stripslashes($data[$i][9])." </b><br>";
		$infoEntreprise.=stripslashes(LANGSTAGE36)." : <b>".stripslashes($data[$i][10])." </b><br>";
		$infoEntreprise.="Site web :     <b>".$data[$i][14]." </b><br>";
		$infoEntreprise.=stripslashes("Groupe Hôtelier ").": <b>".stripslashes($data[$i][15])."</b><br>";
		$infoEntreprise.=stripslashes("Nbr d'étoiles ").": <b>".$data[$i][16]."</b><br>";
		$infoEntreprise.="Nbr Chambres : <b>".$data[$i][13]."</b><br>";
		$infoEntreprise.=stripslashes(LANGSTAGE37)." : <b>".stripslashes($data[$i][11])."</b><br>";
		$infoEntreprise.="</font>";
	}
}
if (trim($infoEntreprise) == "") { 
	$infoEntreprise="<center><font class='T2'>AUCUNE INFORMATION</font></center>";	
}
Pgclose();
sleep(1);
print $infoEntreprise;

?>
