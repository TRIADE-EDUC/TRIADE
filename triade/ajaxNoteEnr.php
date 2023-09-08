<?php
session_start();
error_reporting(0);
if ((empty($_SESSION["nom"])) && (empty($_SESSION["membre"]))) { exit; }
if (($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "eleve") || ($_SESSION["membre"] == "menupersonnel")) { exit; }
if ((isset($_POST["ideleve"]) && ($_POST["ideleve"] > 0) )) {
	include_once("./common/config.inc.php");
	include_once("./librairie_php/db_triade.php");
	$cnx=cnx();

	$ideleve=$_POST["ideleve"];
	$date=$_POST["date"];
	$mid=$_POST["code_mat"];
	$coef=$_POST["coef"];
	$note=$_POST["note"];
	$sujet=$_POST["sujet"];
	$typenote=$_POST["noteusa"];
	$noteExam=$_POST["noteexamen"];
	$notationSur= $_POST["notationsur"];
	$idprof=$_POST["prof_id"];
	$notevisible=$_POST["notevisible"];
	$idclasse=$_POST["idcl"];
	$idgrp=$_POST["gid"];


	switch($note){
		case 'VAL':
			$note='-6';
			break;
		case 'DNR':
			$note='-5';
			break;
		case 'DNN':
			$note='-4';
			break;
		case 'disp':
			$note='-2';
			break;
		case 'abs':
			$note='-1';
			break ;
		case 'néant':
			$note='-3';
			break ;
		case ' ':
			$note='-3';
			break;
		case '':
			$note='-3';
			break;
		default :
			$note=$note;
	}

	$sujet=preg_replace('/\+/',' ',$sujet);
	$sujet=preg_replace('/\?/',' ',$sujet);
	$sujet=preg_replace('/\//',' ',$sujet);
	$sujet=preg_replace('/&/',' ',$sujet);
	$sujet=preg_replace('/%/',' ',$sujet);
	$sujet=preg_replace('/µ/',' ',$sujet);
	$sujet=preg_replace('/\^/',' ',$sujet);
	$sujet=preg_replace('/\(/',' ',$sujet);
	$sujet=preg_replace('/\)/',' ',$sujet);
	$sujet=preg_replace('/"/',' ',$sujet);
	$sujet=preg_replace("/'/",' ',$sujet);
	$sujet=preg_replace('/\$/',' ',$sujet);
	$sujet=preg_replace('/£/',' ',$sujet);
	$sujet=preg_replace('/:/',' ',$sujet);
	$sujet=preg_replace('/=/',' ',$sujet);
	$sujet=preg_replace('/\*/',' ',$sujet);
	$sujet=preg_replace('/¨/',' ',$sujet);
	$sujet=preg_replace('/;/',' ',$sujet);

	$sql="SELECT * FROM  ${prefixe}notes WHERE elev_id='$ideleve' AND prof_id='$idprof' AND code_mat='$mid' AND coef='$coef' AND date='$date' AND id_classe='$idclasse' AND id_groupe='$idgrp' AND noteexam='$noteExam' AND sujet='".utf8_decode($sujet)."' ";
	$curs=execSql($sql);
        unset($sql);
        if ((count(chargeMat($curs)) == 0) && (trim($note) != ""))  {
		$sql="INSERT INTO ${prefixe}notes(
			elev_id,
			prof_id,
			code_mat,
			coef,
			date,
			sujet,
			note,
			id_classe,
			id_groupe,
			typenote,
			noteexam,
			notationsur,
			notevisiblele)
		VALUES ( 
			'$ideleve',
			'$idprof',
			'$mid',
			'$coef',
			'$date',
			'".utf8_decode($sujet)."',
			'$note',
			'$idclasse',
			'$idgrp',
			'$typenote',
			'$noteExam',
			'$notationSur',
			'$notevisible')";
		$cr=execSql($sql);
		if ($cr) {
			$nomprenom=rechercheEleveNomPrenom($ideleve);
			history_cmd($_SESSION["nom"],"AJOUT","Note pour $nomprenom");
			print "ok";
		}else{
			print "pasok";
		}
		PgClose($cnx);
	}else{
		print "pasok";
	}
}else{
	print "pasok";
}
sleep(1);
?>
