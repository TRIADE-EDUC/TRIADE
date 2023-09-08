<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menuadmin") {
	if (isset($_POST["ideleve"])) {
		$cnx=cnx();
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
		// LEAP
		if ($type_bulletin == "leap") {
			$mention=preg_replace('/,$/','',$mention);
			$tab=explode(",",$mention);
			foreach($tab as $key=>$value) {
				if ($value == "leap_felicitation") $leap_felicitation=1;
				if ($value == "leap_encouragement") $leap_encouragement=1;
				if ($value == "leap_megcomp") $leap_megcomp=1;
				if ($value == "leap_megtrav") $leap_megtrav=1;
			}
		}
		$cr=create_comm_direc_bull($_POST["ideleve"],$_POST["tri"],$_POST["commentaire"],$montessori,$type_bulletin,$leap_felicitation,$leap_encouragement,$leap_megcomp,$leap_megtrav,$jtc_promu,$jtc_reprendre,$jtc_orientation,$pp_av_trav,$pp_av_comp,$pp_enc,$pp_feli,$ppv2_av,$ppv2_faible,$ppv2_passable,$ppv2_enc,$ppv2_feli,$anneeScolaire);
		if ($cr) {
			history_cmd($_SESSION["nom"],"COMMENTAIRE","Bulletin");
			print "ok";
		}else{
			print "pasok";
		}
		PgClose($cnx);
	}else{
		print "";
	}
}else{
	print "pasok";
}
sleep(1);
?>
