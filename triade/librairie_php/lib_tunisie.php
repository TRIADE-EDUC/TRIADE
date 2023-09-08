<?php
/***************************************************************************
 *                              T.R.I.A.D.E - TUNISIE
 *                            --------------------------
 *
 *   begin                : Novembre 2008
 *   copyright            : (C) 2008 - MEDALI 
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

// pour Oral
//--------------
function recupOral($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"oral\"";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"oral\"";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	$notefinale="";
	$nbcoef=0;
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][0] ;    // * $liste[$cc][1]
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale *1 ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}
//--------------------
// pour Cont1
//--------------
function recupCont1($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"cont1\"";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"cont1\"";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	$notefinale="";
	$nbcoef=0;
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][0] ;    // * $liste[$cc][1]
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale *1 ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}
//--------------------
// pour Cont2
//--------------
function recupCont2($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"cont2\"";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"cont2\"";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	$notefinale="";
	$nbcoef=0;
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][0] ;    // * $liste[$cc][1]
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale *1 ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}
//--------------------
// pour Tecrit
//--------------
function recupTecrit($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"tecrit\"";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"tecrit\"";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	$notefinale="";
	$nbcoef=0;
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][0] ;    // * $liste[$cc][1]
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale *1 ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}
//--------------------
// pour Trvx
//--------------
function recupTrvx($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"trvx\"";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"trvx\"";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	$notefinale="";
	$nbcoef=0;
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][0] ;    // * $liste[$cc][1]
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale *1 ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}
//--------------------
// pour Eval1
//--------------
function recupEval1($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"eval1\"";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"eval1\"";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	$notefinale="";
	$nbcoef=0;
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][0] ;    // * $liste[$cc][1]
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale *1 ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}
//--------------------
// pour Eval2
//--------------
function recupEval2($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"eval2\"";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"eval2\"";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	$notefinale="";
	$nbcoef=0;
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][0] ;    // * $liste[$cc][1]
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale *1 ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}
//--------------------
// pour Cont
//--------------
function recupCont($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"cont\"";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"cont\"";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	$notefinale="";
	$nbcoef=0;
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][0] ;    // * $liste[$cc][1]
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale *1 ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}
//--------------------
// pour Synth
//--------------
function recupSynth($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"synth\"";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND note >= 0
	AND sujet=\"synth\"";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	$notefinale="";
	$nbcoef=0;
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][0] ;    // * $liste[$cc][1]
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale *1 ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}

?>
