<?php
error_reporting(0);
$idpers=$_POST["idpers"];
$idclasse=$_POST["idclasse"];
$dateDebut=$_POST["dateDebut"];
$dateFin=$_POST["dateFin"];
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();


$data=dateEnseignantEvalparDate($idpers,$idclasse,$dateDebut,$dateFin); //id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,taux,coursannule
$j=0;
$unite=unitemonnaie();
for($i=0;$i<count($data);$i++) {
	$j++;
	$u="";$uf="";
	if($data[$i][11] == "1") { $u="<s>"; $uf="</s>"; }
	$listeHoraire.="<img src='./image/commun/on1.gif' height='8' width='8' > $u". dateForm($data[$i][3])." (".timeForm($data[$i][4])."-".affichageFormatMonnaie($data[$i][10])." $unite) $uf ";
	if ($j == 2) { $listeHoraire.="<br />"; $j=0; }
}
if (trim($listeHoraire) == "") { 
	$listeHoraire="<center><font class='T2'>AUCUNE INFORMATION</font></center>";
}
Pgclose();
sleep(1);
print $listeHoraire;
?>
