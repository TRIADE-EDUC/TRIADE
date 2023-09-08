<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if  (($_SESSION["membre"] == "menuadmin") || ( ($_SESSION["membre"] == "menuprof") && (verif_profp_eleve2($_POST["ideleve"],$_SESSION["id_pers"])) ) ) {
	if (isset($_POST["ideleve"])) {
		$mention=$_POST["mention"];
		$type_bulletin=$_POST["typebulletin"];
		$montessori="aucun";
		$anneeScolaire=$_POST["anneeScolaire"];
		if ($type_bulletin == "montessori") { $montessori=$mention; }
		if ($type_bulletin == "montessori_spec") { $montessori=$mention; }
		if ($type_bulletin == "seminaire") { $montessori=$mention; }
		
		$leap_felicitation=0;
		$leap_encouragement=0;
		$leap_megcomp=0;
		$leap_megtrav=0;
		if ($type_bulletin == "leap") {
			$mention=preg_replace('/,$/','',$mention);
			$tab=explode(",",$mention);
			foreach($tab as $key=>$value) {
				if ($value == "leap_felicitation") $leap_felicitation=1;
				if ($value == "leap_encouragement") $leap_encouragement=1;
				if ($value == "leap_megcomp") $leap_megcomp=1;
				if ($value == "leap_megtrav") $leap_megtrav=1;
			}
			modifLeap($_POST["ideleve"],$_POST["tri"],'',$montessori,$type_bulletin,$leap_felicitation,$leap_encouragement,$leap_megcomp,$leap_megtrav,$anneeScolaire);
		}
		$cr=create_comm_profp_bull($_POST["ideleve"],$_POST["tri"],$_POST["commentaire"],$anneeScolaire,'');
		if ($cr) {
			print "ok";
		}else{
			print "pasok";
		}
	
	}else{
		print "pasok";
	}
}else{
	print "pasok";
}
PgClose($cnx);
sleep(1);
?>
