<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/choixlangue.php");
include_once("./librairie_php/langue.php");

if (isset($_POST["id"])) {
	$cnx=cnx();
	$data=recupInfoVersement($_POST["ideleve"],$_POST["id"]); // ideleve,idversement,montantvers,datevers,modepaiement
	$montantVers=number_format($data[0][2],2,'.','');
	$dateVers=$data[0][3];
	if ($dateVers != "") { $dateVers=dateForm($dateVers); }else{  $dateVers="en attente";  } 

	$dateduJour=date("Ymd");
	$dateVersOr=preg_replace('/-/',"",$_POST["dateversor"]);


	$nbj=preg_replace('/-/',"",dateFormBase($dateVers))-$dateduJour;

	if ($nbj <= 0) { 
		$nbj=""; 
	}else{
		$nbj="<font class='T1'><i>(dans $nbj jour(s))</i></font>";
	}

	$unite=unitemonnaie();
	print "<font class=T2>";
	print "Versement : <b>".affichageFormatMonnaie($montantVers)." $unite</b><br>";
	if ($dateduJour >= preg_replace('/-/',"",dateFormBase($dateVers)))  {
		print "Encaiss&eacute; le : $dateVers $nbj <br>";
	}else{
		print "Encaissement : $dateVers $nbj <br>";
	}
	print "</font>";

	if (($montantVers == "0.00") && ($dateduJour > $dateVersOr)) {
		print "<font class=T2>Etat : <img src='image/commun/important.png' align='center' border='0' alt='Retard paiement' ></font> <i>Retard paiement</i><br>";
	}

	if (($montantVers < $_POST["montantavers"] ) && ($dateduJour > $dateVersOr) && ($montantVers != "0.00") ) {
		print "<font class=T2>Etat : <img src='image/commun/warning.gif' align='center' border='0' alt='Paiement incomplet' ></font> <i>Paiement incomplet</i><br>";
	}

	if (($montantVers >= $_POST["montantavers"] ) && ($dateduJour >= preg_replace('/-/',"",dateFormBase($dateVers)))   ) {
		print "<font class=T2>Etat : <img src='image/commun/valid.gif' align='center' border='0' alt='Paiement effectué' ></font> <i>Paiement effectué</i><br>";
	}

	

	Pgclose();
}else{
	print "";
}
sleep(1);
?>
