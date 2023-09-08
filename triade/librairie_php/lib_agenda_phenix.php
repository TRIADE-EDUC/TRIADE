<?php

include_once 'lib_prefixe.php';
include_once 'timezone.php';
include_once 'lib_param.php';
include_once 'conf_error.php';

global $prefixe;
global $cnx;


function ajoutNoteAgenda($util_id,$date,$sujet,$commentaire) {
	global $cnx;
	$datecreation=dateDMY2()." ".dateHIS();
	$sql="INSERT INTO ${prefixe}px_agenda (
		age_mere_id,
		age_util_id,
		age_aty_id,
		age_date,
		age_heure_debut,
		age_heure_fin,
		age_ape_id,
		age_periode1,
		age_periode2,
		age_periode3,
		age_periode4,
		age_plage,
		age_plage_duree,
		age_libelle,
		age_detail,
		age_rappel,
		age_rappel_coeff,
		age_email,
		age_prive,
		age_couleur,
		age_nb_participant,
		age_createur_id,
		age_date_creation,
		age_modificateur_id,
		age_date_modif,
		age_disponibilite,
		age_lieu,
		age_cal_id,
		age_email_contact 
	   ) VALUES (
			'0',
			'$util_id',
			'2',
			'$date',
			'7.50',
			'7.75',
			'1',
			'0',
			'0',
			'0',
			'0',
			'1',
			'10',
			'$sujet',
			'$commentaire',
			'0',
			'1',
			'0',
			'1',
			'',
			'1',
			'2',
			'$datecreation',
			'2',
			'$datecreation',
			'1',
			'',
			'0',
			'0'
		);";
	execSql($sql);
	$id=mysql_insert_id();
	$sql="INSERT INTO ${prefixe}px_agenda_concerne (aco_age_id,aco_util_id,aco_rappel_ok,aco_termine) VALUE ('$id','$util_id','1','0')";
	execSql($sql);
}


function recupUtil_Id($idpers) {
	global $cnx;
	$sql="SELECT util_id FROM ${prefixe}px_utilisateur  WHERE util_login='$idpers'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) { return $data[0][0]; }
	return -1;	
}
?>
