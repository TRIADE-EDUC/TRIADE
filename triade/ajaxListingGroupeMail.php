<?php
error_reporting(0);
$idgroupe=$_POST["idgroupe"];
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/langue-librairie.php");
$cnx=cnx();

$data=listingGroupeMail($idgroupe); // id,idpers,liste_id,libelle,public
$liste="";
if (count($data) > 0) {
	$liste="Groupe Mail : ".nomDuGroupeMail($idgroupe)." ( ".count($data)." personne(s) )<br><br>";
	foreach($data as $liste_pers=>$value) {
		$personne=recherche_personne($value);
		if ($personne != "") {
			$liste.="<img src='./image/commun/on1.gif' height='8' width='8' > <b>".utf8_encode($personne)."</b> (".recherche_type_personne($value).") ";	
		}
	}
}else{
	$liste="<center><font class='T2'>AUCUNE INFORMATION</font></center>";
}
sleep(1);
print $liste;
?>
