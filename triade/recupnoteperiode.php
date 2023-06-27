<?php
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

// les matières et leur ordre d'affectation
function ordre_matiere($idClasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
$sql=<<<SQL
SELECT
	a.code_matiere,a.code_prof,a.ordre_affichage,
	-- libelle||' '||sous_matiere,b.code_classe
	case
		when sous_matiere = '0' then lower(trim(libelle))
		else lower(trim(libelle||' '||sous_matiere))
	end
	,m.sous_matiere,m.libelle,a.nb_heure
FROM
	${prefixe}affectations a, ${prefixe}matieres m
WHERE
	a.code_classe = '$idClasse'
AND a.code_matiere = m.code_mat
AND annee_scolaire = '$anneeScolaire'
ORDER BY
	a.ordre_affichage
SQL;
$curs=execSql($sql);
	$ordre=chargeMat($curs);
	unset($curs);
	return $ordre;
}


function ordre_matiere_visubull($idClasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
$sql=<<<SQL
SELECT
	a.code_matiere,a.code_prof,a.ordre_affichage,
	-- libelle||' '||sous_matiere,b.code_classe
	case
		when sous_matiere = '0' then lower(trim(libelle))
		else lower(trim(libelle||' '||sous_matiere))
	end
	,m.sous_matiere,m.libelle,a.ects,a.langue
FROM
	${prefixe}affectations a, ${prefixe}matieres m
WHERE
	a.code_classe = '$idClasse'
AND a.code_matiere = m.code_mat
AND a.visubull = '1'
AND annee_scolaire = '$anneeScolaire'
ORDER BY
	a.ordre_affichage
SQL;
	$curs=execSql($sql);
	$ordre=chargeMat($curs);
	unset($curs);
	return $ordre;
}


function ordre_matiere_visubull_trim($idClasse,$trim) {
        global $cnx;
        global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
$sql=<<<SQL
SELECT
        a.code_matiere,a.code_prof,a.ordre_affichage,
        -- libelle||' '||sous_matiere,b.code_classe
        case
                when sous_matiere = '0' then lower(trim(libelle))
                else lower(trim(libelle||' '||sous_matiere))
        end
        ,m.sous_matiere,m.libelle,a.ects,a.langue
FROM
        ${prefixe}affectations a, ${prefixe}matieres m
WHERE
        a.code_classe = '$idClasse'
AND a.code_matiere = m.code_mat
AND a.visubull = '1'
AND (a.trim = '$trim' OR a.trim='tous')
AND annee_scolaire = '$anneeScolaire'
ORDER BY
        a.ordre_affichage
SQL;
        $curs=execSql($sql);
        $ordre=chargeMat($curs);
        unset($curs);
        return $ordre;
}




// ------------------
function recupEleve($idClasse,$anneeScolaire="") {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
	if (trim($anneeScolaire) == "") {
		$sql="SELECT nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone,numero_eleve,tel_fixe_eleve FROM ${prefixe}eleves WHERE classe='$idClasse' ORDER BY nom,prenom";
		$curs=execSql($sql);
		$liste=chargeMat($curs);
		unset($curs);
		return $liste;
	}else{
		$sql="(SELECT nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone,numero_eleve,tel_fixe_eleve FROM ${prefixe}eleves , ${prefixe}classes  WHERE  classe='$idClasse' AND code_class='$idClasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT e.nom,e.prenom,e.lv1,e.lv2,e.elev_id,e.date_naissance,e.lieu_naissance,e.adr1,e.code_post_adr1,e.commune_adr1,e.telephone,e.numero_eleve,e.tel_fixe_eleve FROM ${prefixe}eleves e , ${prefixe}classes c , ${prefixe}eleves_histo h WHERE h.idclasse='$idClasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.elev_id ) ORDER BY 1 ";
		$curs=execSql($sql);
		$liste=chargeMat($curs);
		unset($curs);
		return $liste;
	}
}


// ------------------
function verifAffichageMatierelv1($nomEleve,$prenomEleve,$matiere) {
	global $cnx;
	global $prefixe;
	$nomEleve=strtolower($nomEleve);
	$prenomEleve=strtolower($prenomEleve);
	$matiere=strtolower($matiere);
	$sql="SELECT nom,prenom,lv1 FROM ${prefixe}eleves WHERE  nom='$nomEleve' AND prenom='$prenomEleve' AND lv1='$matiere'";
	$curs=execSql($sql);
	$curs=pg_numrows($curs);
	return $curs;
}


function verifsousmatierebull($idMatiere) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_mat,sous_matiere,libelle FROM ${prefixe}matieres  WHERE code_mat='$idMatiere' ";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	unset($curs);
	unset($sql);
	if ($resultat[0][1] != "0") {
		return $resultat;
	}else {
		return 0;
	}
}

function verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordreaffich) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_matiere,code_groupe FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordreaffich' ";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	unset($curs);
	unset($sql);
	if ($resultat[0][1] == 0) {
		return $resultat[0][1];
	}else {
		$idGr=$resultat[0][1];
		return $idGr;
	}
}


function verifMatiereGroupe($idMatiere,$idEleve,$idClasse,$ordreaffich) {
        global $cnx;
        global $prefixe;
        $sql="SELECT code_matiere,code_groupe,code_classe,ordre_affichage FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordreaffich' ";
        $curs=execSql($sql);
        $resultat=chargeMat($curs);
        unset($curs);
        unset($sql);
        if ($resultat[0][1] == 0) {
                return(0);
        }else {
                return($resultat[0][1]);
        }
}

function verifMatierAvecGroupeRecupId2($idMatiere,$idClasse,$ordreaffich) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_matiere,code_groupe FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordreaffich' ";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	unset($curs);
	unset($sql);
	if ($resultat[0][1] == 0) {
		return -1;
	}else {
		$idGr=$resultat[0][1];
		return $idGr;
	}
}


function recherche_prof($idMatiere,$idClasse,$ordre){
	global $cnx;
	global $prefixe;
	$sql="SELECT code_matiere,code_groupe,code_classe,ordre_affichage,code_prof FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordre' ";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	return $resultat[0][4];
}

function verifMatiereAvecGroupe3($idMatiere,$idEleve,$idClasse,$ordreaffich) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_matiere,code_groupe,code_classe,ordre_affichage FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordreaffich' ";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	unset($curs);
	unset($sql);
	if ($resultat[0][1] == 0) {
		return 0;
	}else {
		$idGr=$resultat[0][1];
	}

	$sql="SELECT group_id,liste_elev FROM ${prefixe}groupes WHERE group_id='$idGr'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
	$liste_eleves=preg_replace('/\}/',"",$liste_eleves); $liste[$cc][1] /= 4; 
	$listeEleve=preg_split('/,/', $liste_eleves);
	foreach ($listeEleve as $valeur) {
		if ($valeur == $idEleve) {
			return $idGr;
		}
	}
	return -1;
}

function verifMatiereAvecGroupe2($idMatiere,$idEleve,$idClasse,$ordreaffich) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_matiere,code_groupe,code_classe,ordre_affichage FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordreaffich' ";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	unset($curs);
	unset($sql);
	if ($resultat[0][1] == 0) {
		return $resultat[0][1];
	}else {
		$idGr=$resultat[0][1];
	}
	$sql="SELECT group_id,liste_elev FROM ${prefixe}groupes WHERE group_id='$idGr'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
	$liste_eleves=preg_replace('/\}/',"",$liste_eleves); $liste[$cc][1] /= 4; 
	$listeEleve=preg_split('/,/', $liste_eleves);
	foreach ($listeEleve as $valeur) {
		if ($valeur == $idEleve) {
			return 1;
		}
	}
	return 0;
}



function verifElevDansGroupe($idGr,$idEleve) {
	global $cnx;
	global $prefixe;
	$sql="SELECT group_id,liste_elev FROM ${prefixe}groupes WHERE group_id='$idGr'";
        $res=execSql($sql);
        $data=chargeMat($res);
        $liste_eleves=preg_replace('/\{/',"",$data[0][1]);
        $liste_eleves=preg_replace('/\}/',"",$liste_eleves); $liste[$cc][1] /= 4;
        $listeEleve=preg_split('/,/', $liste_eleves);
        foreach ($listeEleve as $valeur) {
                if ($valeur == $idEleve) {
                        return 1;
                }
        }
        return 0;
}

function verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$ordreaffich) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_matiere,code_groupe,code_classe,ordre_affichage FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordreaffich' ";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	unset($curs);
	unset($sql);
	if ($resultat[0][1] == 0) {
		return $resultat[0][1];
	}else {
		$idGr=$resultat[0][1];
	}

	$sql="SELECT group_id,liste_elev FROM ${prefixe}groupes WHERE group_id='$idGr'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
	$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
	$listeEleve=preg_split('/,/', $liste_eleves);
	foreach ($listeEleve as $valeur) {
		if ($valeur == $idEleve) {
			return 0;
		}
	}
	return $idGr;
}


function verifMatiereOPT4($idmatiere,$idClasse,$ordre) {
	global $cnx;
        global $prefixe;
        $sql="SELECT langue FROM ${prefixe}affectations WHERE code_matiere='$idmatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordre'";
	$curs=execSql($sql);
        $resultat=chargeMat($curs);
	if ($resultat[0][1] == "OPT4") return true;
	return false;
}


function verifMatiereAvecGroupeUE($idMatiere,$idEleve,$idClasse,$ordre) {
         global $cnx;
         global $prefixe;
         $sql="SELECT code_matiere,code_groupe,code_classe,ordre_affichage FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordre' ";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	unset($curs);
	unset($sql);
	if ($resultat[0][1] == 0) {
		return $resultat[0][1];
	}else {
		$idGr=$resultat[0][1];
	}

	$sql="SELECT group_id,liste_elev FROM ${prefixe}groupes WHERE group_id='$idGr'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
	$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
	$listeEleve=preg_split('/,/', $liste_eleves);
	foreach ($listeEleve as $valeur) {
		if ($valeur == $idEleve) {
			return 0;
		}
	}
	return $idGr;
}


function recupECTS($idmatiere,$idClasse,$trim) {
	global $cnx;
	global $prefixe;
	if ($trim == "T4") {
		$sql="SELECT ects FROM ${prefixe}affectations WHERE code_matiere='$idmatiere' AND code_classe='$idClasse'";
	}else{
		$sql="SELECT ects FROM ${prefixe}affectations WHERE code_matiere='$idmatiere' AND code_classe='$idClasse'";
	}		
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	return $resultat[0][0];
}

function recupCoefUE($idmatiere,$idClasse,$trim) {
	global $cnx;
	global $prefixe;
	$sql="SELECT coef FROM ${prefixe}affectations WHERE code_matiere='$idmatiere' AND code_classe='$idClasse'  AND (trim='tous' OR trim='$trim') ";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	return $resultat[0][0];
}

function recupCoefUEviaGrp($idmatiere,$idClasse,$trim,$idgroupe) {
        global $cnx;
        global $prefixe;
	if ($idgroupe == '') $idgroupe=0;
        $sql="SELECT coef FROM ${prefixe}affectations WHERE code_matiere='$idmatiere' AND code_classe='$idClasse'  AND (trim='tous' OR trim='$trim') AND code_groupe='$idgroupe' ";
        $curs=execSql($sql);
        $resultat=chargeMat($curs);
        return $resultat[0][0];
}


function recupNote($idEleve,$idMatiere,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		elev_id,
		code_mat,
		date,
		sujet,
		typenote,
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	 ";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		elev_id,
		code_mat,
		date,
		sujet,
		typenote,
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	return $liste;
}

function recupNote2($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		elev_id,
		code_mat,
		date,
		sujet,
		typenote,
		notationsur,
		prof_id
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id='$idprof' ";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		elev_id,
		code_mat,
		date,
		sujet,
		typenote,
		notationsur,
		prof_id
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id='$idprof' ";
	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	return $liste;
}


function recupNoteBull($nbNote,$idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$idgroupe)  {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if ($idgroupe == 0) {
	$sql="
	SELECT
		TRUNCATE(note,2),
		elev_id,
		code_mat,
		date,
		sujet,
		typenote,
		notationsur,
		prof_id
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id='$idprof' 
	AND (noteexam = '' OR  noteexam IS null)
	
	LIMIT 0,$nbNote ";
	}else{
	$sql="
	SELECT
		TRUNCATE(note,2),
		elev_id,
		code_mat,
		date,
		sujet,
		typenote,
		notationsur,
		prof_id
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id='$idprof' 
	AND ((id_groupe = '$idgroupe') OR (id_groupe = '0'))
	AND (noteexam = '' OR  noteexam IS null)
	LIMIT 0,$nbNote ";

	}
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	for($i=0;$i<count($liste);$i++) {
		if ($liste[$i][0] < 0) {
			$liste[$i][0]=preg_replace('/.00/','',$liste[$i][0]);
			$liste[$i][0]=preg_replace('/-1/','abs',$liste[$i][0]);
			$liste[$i][0]=preg_replace('/-2/','disp',$liste[$i][0]);
			$liste[$i][0]=preg_replace('/-3/',' ',$liste[$i][0]);
			$liste[$i][0]=preg_replace('/-4/','DNN',$liste[$i][0]);
			$liste[$i][0]=preg_replace('/-5/','DNR',$liste[$i][0]);
			$liste[$i][0]=preg_replace('/-6/','VAL',$liste[$i][0]);
		}
		$l.=$liste[$i][0]." ";
	}
	return $l;
}


//--------------------
// pour Exam
//--------------
function recupExam($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
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
	AND sujet=\"exam\"";
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
	AND sujet=\"exam\"";
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
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][0] ;    // * $liste[$cc][1]
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale *1 ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}


function recupNoteExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$examen) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
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
	AND noteexam='$examen'";
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	for($i=0;$i<count($liste);$i++){
		$coef=$liste[$i][1];
		$listing.=$liste[$i][0].", ";
	}	
	$listing=preg_replace('/, $/','',$listing);
	return($listing);
}


function recupNoteGroupeExamen($idEleve,$idMatiere,$idgroupe,$dateDebut,$dateFin,$idprof,$examen) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
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
	AND id_groupe = '$idgroupe'
	AND prof_id = '$idprof'
	AND note >= 0
	AND noteexam='$examen'";
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	for($i=0;$i<count($liste);$i++){
		$coef=$liste[$i][1];
		$listing.=$liste[$i][0].", ";
	}	
	$listing=preg_replace('/, $/','',$listing);
	return($listing);
}


function recupExamPigierNimes($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
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
	AND noteexam=\"examen\"";
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
		if ($liste[$cc][2] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
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

function listingNoteExam($idEleve,$idMatiere,$dateDebut,$dateFin,$exam,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="
	SELECT
		TRUNCATE(note,2)
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND prof_id = '$idprof'
	AND noteexam=\"$exam\"";
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	unset($curs);
	return($liste);
}

//-----------------------------
// pour le bulletin de note
//-----------------------------
function moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		typenote,
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
	";
	$curs=execSql($sql);
//	print $sql."<br><br>";
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
		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }	
		if ($liste[$cc][3] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][3] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][3] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][3] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][3] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][1] * $liste[$cc][0] ;
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;
}


function moyenneDevoir($idMatiere,$date,$idprof,$sujet,$coeff,$examen,$idgroupe,$idclasse) {
	global $cnx;
	global $prefixe;
	$sujet=addslashes($sujet);
	$examen=addslashes($examen);
	$date=dateFormBase2($date);	
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		typenote,
		notationsur
	FROM
		${prefixe}notes
	WHERE
		code_mat='$idMatiere'
		AND date = '$date'
		AND coef = '$coeff'
		AND id_classe = '$idclasse'
		AND id_groupe = '$idgroupe'
		AND noteexam = '$examen'
		AND prof_id = '$idprof'
		AND sujet='$sujet'
	";
	$curs=execSql($sql);
//	print $sql."<br><br>";
	$liste=chargeMat($curs);
	unset($curs);
	$notefinale="";
	$nbcoef=0;
	$min=10000;
	$max=0;
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }	
		if ($liste[$cc][3] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][3] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][3] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][3] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][3] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][1] * $liste[$cc][0] ;
		$notedevoir= $notetempo / $liste[$cc][1];

		$notefinale= $notefinale + $notetempo ;

		if ($notedevoir > $max) { $max=$notedevoir; }
		if ($notedevoir < $min) { $min=$notedevoir; }
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	$max=number_format($max,2,'.','');
	$min=number_format($min,2,'.','');
	$param['moy']=$notefinale;
	$param['max']=$max;
	$param['min']=$min;
	return($param);
}



function moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		typenote,
		notationsur

	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
		AND code_mat='$idMatiere'
		AND date >= '$dateDebut'
		AND date <= '$dateFin'
		AND note >= 0
	";
	$curs=execSql($sql);
//	print $sql."<br><br>";
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
		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }	
		if ($liste[$cc][3] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][3] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][3] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][3] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][3] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][1] * $liste[$cc][0] ;
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;
}


function moyenneEleveMatiereBrevetViaExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$examen) {
	global $cnx;
	global $prefixe;
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		typenote,
		notationsur

	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
		AND code_mat='$idMatiere'
		AND date >= '$dateDebut'
		AND date <= '$dateFin'
		AND noteexam = '$examen'
		AND note >= 0
	";
	$curs=execSql($sql);
//	print $sql."<br><br>";
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
		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }	
		if ($liste[$cc][3] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][3] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][3] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][3] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][3] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][1] * $liste[$cc][0] ;
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;
}


function sommeMoyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		typenote,
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
	";
	$curs=execSql($sql);
//	print $sql."<br><br>";
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
		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }	
		if ($liste[$cc][3] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][3] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][3] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][3] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][3] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][1] * $liste[$cc][0] ;
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	//@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	$notefinale=array('nbSomme'=>$notefinale,'nbNote'=>$nbcoef);
	return $notefinale;
}

function moyenneEleveMatiereExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$examen) {
	global $cnx;
	global $prefixe;

	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sqlsuite="";
	if ($examen == "pigiercanne") {
		$sqlsuite=" AND lower(noteexam) LIKE '%blanc%' ";
	}else{
		$sqlsuite=" AND noteexam = '$examen' ";
	}

	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		typenote,
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
		AND code_mat='$idMatiere'
		AND date >= '$dateDebut'
		AND date <= '$dateFin'
		AND prof_id = '$idprof'
		$sqlsuite
		AND note >= 0
	";
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
		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }	
		if ($liste[$cc][3] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][3] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][3] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][3] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][3] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][1] * $liste[$cc][0] ;
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;
}

function verifSiAbsExamen2($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$examen) {
        global $cnx;
        global $prefixe;
        $dateDebut=dateFormBase($dateDebut);
        $dateFin=dateFormBase($dateFin);
        $sqlsuite="";
        if ($examen == "pigiercanne") {
                $sqlsuite=" AND lower(noteexam) LIKE '%blanc%' ";
        }else{
                $sqlsuite=" AND noteexam = '$examen' ";
        }

        $sql="
        SELECT
                TRUNCATE(note,2)
        FROM
                ${prefixe}notes
        WHERE
                elev_id='$idEleve'
                AND code_mat='$idMatiere'
                AND date >= '$dateDebut'
                AND date <= '$dateFin'
                AND prof_id = '$idprof'
                $sqlsuite
        ";
        $curs=execSql($sql);
        $liste=chargeMat($curs);
        unset($curs);
        $notefinale="";
        for($cc=0;$cc<count($liste);$cc++) {
                if ($liste[$cc][0]=="-1.00") { return "ABS" ;    }
                if ($liste[$cc][0]=="-2.00") { return "DISP" ;   }
                if ($liste[$cc][0]=="-3.00") { return "ABS"  ;   }
                if ($liste[$cc][0]=="-4.00") { return "DNN"  ;   }
                if ($liste[$cc][0]=="-5.00") { return "DNR"  ;   }
                if ($liste[$cc][0]=="-6.00") { return "VALIDE" ; }
                $notefinale=$liste[$cc][0];
	} 
	return;
}



function verifSiAbsExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$examen) {
        global $cnx;
        global $prefixe;
        $dateDebut=dateFormBase($dateDebut);
        $dateFin=dateFormBase($dateFin);
        $sqlsuite="";
        if ($examen == "pigiercanne") {
                $sqlsuite=" AND lower(noteexam) LIKE '%blanc%' ";
        }else{
                $sqlsuite=" AND noteexam = '$examen' ";
        }

        $sql="
        SELECT
                TRUNCATE(note,2)
        FROM
                ${prefixe}notes
        WHERE
                elev_id='$idEleve'
                AND code_mat='$idMatiere'
                AND date >= '$dateDebut'
                AND date <= '$dateFin'
                AND prof_id = '$idprof'
                $sqlsuite
        ";
        $curs=execSql($sql);
        $liste=chargeMat($curs);
        unset($curs);
        $notefinale="";
        for($cc=0;$cc<count($liste);$cc++) {
                if ($liste[$cc][0]=="-1.00") { $abs='1'; continue; }
                if ($liste[$cc][0]=="-2.00") { $disp='1'; continue; }
                if ($liste[$cc][0]=="-3.00") { $abs='1'; continue; }
                if ($liste[$cc][0]=="-4.00") { $DNN='1'; continue; }
                if ($liste[$cc][0]=="-5.00") { $DNR='1'; continue; }
                if ($liste[$cc][0]=="-6.00") { $VAL='1'; continue; }
                $notefinale=$liste[$cc][0];
	}
        if (($abs == 1) && ($notefinale == "")) {
        	return "ABS";
        }else{
		return;
	}
}



function moyenneEleveMatiereSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;

	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);

	if(DBTYPE=='pgsql') {
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		typenote,
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
		AND code_mat='$idMatiere'
		AND date >= '$dateDebut'
		AND date <= '$dateFin'
		AND (noteexam = '' OR noteexam IS NULL ) 
		AND prof_id = '$idprof'
		AND note >= 0
	";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		typenote,
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
		AND code_mat='$idMatiere'
		AND date >= '$dateDebut'
		AND date <= '$dateFin'
		AND (noteexam = '' OR noteexam IS NULL ) 
		AND prof_id = '$idprof'
		AND note >= 0
	";
	}
	$curs=execSql($sql);
//	print $sql."<br><br>";
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
		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }	
		if ($liste[$cc][3] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][3] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][3] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][3] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][3] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][1] * $liste[$cc][0] ;
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;
}


function moyenneEleveMatiereExam($idEleve,$idMatiere,$examen,$idprof) {
	global $cnx;
	global $prefixe;

	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);

	if(DBTYPE=='pgsql') {
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		typenote,
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
		AND code_mat='$idMatiere'
		AND noteexam='$examen'
		AND prof_id = '$idprof'
		AND note >= 0
	";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		typenote,
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
		AND code_mat='$idMatiere'
		AND noteexam='$examen'
		AND prof_id = '$idprof'
		AND note >= 0
	";
	}
	$curs=execSql($sql);
//	print $sql."<br><br>";
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
		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }	
		if ($liste[$cc][3] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][3] == 5) { $liste[$cc][0]*=4;  $liste[$cc][1] /= 4;  }
		if ($liste[$cc][3] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][3] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][3] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		$notetempo= $liste[$cc][1] * $liste[$cc][0] ;
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;
}

//lisaa
function moyenneCCMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
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
	AND sujet != \"exam\"";
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
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$notetempo= $liste[$cc][1] * $liste[$cc][0] ;
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}

function moyenneCCMatierePigierNimes($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
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
	AND noteexam='' OR noteexam IS NULL
	AND note >= 0";
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
		$nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
		if ($liste[$cc][2] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][2] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][2] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][2] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][2] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
		$notetempo= $liste[$cc][1] * $liste[$cc][0] ;
		$notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;

}

function moyenneEleveMatiereGroupeSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="
	SELECT
		TRUNCATE(note,2),
		coef,
		code_mat,
		id_groupe,
		typenote,
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
	AND (noteexam = '' OR noteexam IS NULL )
	AND ((id_groupe = '$idgroupe') OR (id_groupe = '0')) ";
	
	// AND ( id_groupe = '$idgroupe' OR id_groupe = 0 )";
	// supprimer OR id_groupe = 0


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

		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }
		if ($liste[$cc][5] == 10) { $liste[$cc][0]*=2;  $liste[$cc][1] /= 2;  }
		if ($liste[$cc][5] == 5) { $liste[$cc][0]*=4;  $liste[$cc][1] /= 4;  }
		if ($liste[$cc][5] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][5] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][5] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
                $nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
                $notetempo= $liste[$cc][1] * $liste[$cc][0] ;
                $notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;
}


function moyenneEleveMatiereGroupe($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="
	SELECT
		TRUNCATE(note,2),
		coef,
		code_mat,
		id_groupe,
		typenote,
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
	AND ((id_groupe = '$idgroupe') OR (id_groupe = '0')) ";
	
	// AND ( id_groupe = '$idgroupe' OR id_groupe = 0 )";
	// supprimer OR id_groupe = 0
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

		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }
		if ($liste[$cc][5] == 10) { $liste[$cc][0]*=2;  $liste[$cc][1] /= 2;  }
		if ($liste[$cc][5] == 5) { $liste[$cc][0]*=4;  $liste[$cc][1] /= 4;  }
		if ($liste[$cc][5] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][5] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][5] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
                $nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
                $notetempo= $liste[$cc][1] * $liste[$cc][0] ;
                $notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;
}

function verifNoteDansMatiere($idMatiere,$idClasse,$idgroupe,$dateDebut,$dateFin) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT * FROM ${prefixe}notes WHERE code_mat='$idMatiere' AND date >= '$dateDebut' AND date <= '$dateFin' 
		AND ((id_classe='$idClasse' AND  id_groupe = '0') OR (id_groupe = '$idgroupe'  AND   id_classe='-1')  OR  (id_groupe = '$idgroupe'  AND   id_classe='$idClasse') ) ";
	$curs=execSql($sql);
	$liste=chargeMat($curs);
	if (count($liste) > 0) { return false ; }else{ return true; }
}

function sommeMoyenneEleveMatiereGroupe($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="
	SELECT
		TRUNCATE(note,2),
		coef,
		code_mat,
		id_groupe,
		typenote,
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
	AND ((id_groupe = '$idgroupe') OR (id_groupe = '0')) ";
	
	// AND ( id_groupe = '$idgroupe' OR id_groupe = 0 )";
	// supprimer OR id_groupe = 0
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

		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }
		if ($liste[$cc][5] == 10) { $liste[$cc][0]*=2;  $liste[$cc][1] /= 2;  }
		if ($liste[$cc][5] == 5) { $liste[$cc][0]*=4;  $liste[$cc][1] /= 4;  }
		if ($liste[$cc][5] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][5] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][5] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
                $nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
                $notetempo= $liste[$cc][1] * $liste[$cc][0] ;
                $notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	//@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	$notefinale=array('nbSomme'=>$notefinale,'nbNote'=>$nbcoef);
	return $notefinale;
}

function moyenneEleveMatiereGroupeExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$examen) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);

        if ($examen == "pigiercanne") {
                $sqlsuite=" AND lower(noteexam) LIKE '%blanc%' ";
        }else{
                $sqlsuite=" AND noteexam = '$examen' ";
        }


	$sql="
	SELECT
		TRUNCATE(note,2),
		coef,
		code_mat,
		id_groupe,
		typenote,
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
	$sqlsuite
	AND ((id_groupe = '$idgroupe') OR (id_groupe = '0')) ";
	
	
	// AND ( id_groupe = '$idgroupe' OR id_groupe = 0 )";
	// supprimer OR id_groupe = 0

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

		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }
		if ($liste[$cc][5] == 10) { $liste[$cc][0]*=2;  $liste[$cc][1] /= 2;  }
		if ($liste[$cc][5] == 5) { $liste[$cc][0]*=4;  $liste[$cc][1] /= 4;  }
		if ($liste[$cc][5] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][5] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][5] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
                $nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
                $notetempo= $liste[$cc][1] * $liste[$cc][0] ;
                $notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;
}


function moyenneEleveMatiereGroupeExam($idEleve,$idMatiere,$examen,$idgroupe,$idprof) {
	global $cnx;
	global $prefixe;
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	if(DBTYPE=='pgsql')
	{
	$sql="
	SELECT
		TRUNC(note,2),
		coef,
		code_mat,
		id_groupe,
		typenote,
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND noteexam='$examen'
	AND prof_id = '$idprof'
	AND note >= 0
	AND id_groupe = '$idgroupe'
	";
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		coef,
		code_mat,
		id_groupe,
		typenote,
		notationsur
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND noteexam='$examen'
	AND prof_id = '$idprof'
	AND note >= 0
	AND ((id_groupe = '$idgroupe') OR (id_groupe = '0')) ";
	}
	
	// AND ( id_groupe = '$idgroupe' OR id_groupe = 0 )";
	// supprimer OR id_groupe = 0

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

		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }
		if ($liste[$cc][5] == 10) { $liste[$cc][0]*=2; $liste[$cc][1] /= 2;  }
		if ($liste[$cc][5] == 5) { $liste[$cc][0]*=4; $liste[$cc][1] /= 4;  }
		if ($liste[$cc][5] == 40) { $liste[$cc][0]/=2; $liste[$cc][1] *= 2;  }
		if ($liste[$cc][5] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
		if ($liste[$cc][5] == 30) { $liste[$cc][0]/=1.5; $liste[$cc][1] *= 1.5;  }
                $nbcoef=$nbcoef + $liste[$cc][1];        // $liste[$cc][1] -> coef
                $notetempo= $liste[$cc][1] * $liste[$cc][0] ;
                $notefinale= $notefinale + $notetempo ;
	}
	if ($nbcoef == 0) {$notefinale=""; return  $notefinale; }
	@$notefinale=$notefinale / $nbcoef ;
	if ($noteengl=="oui") { $notefinale=$notefinale * 5; }
	$notefinale=number_format($notefinale,2,'.','');
	return $notefinale;
}

function coeffMatiere($idMatiere,$idClasse){
	global $cnx;
	global $prefixe;
	$sql="SELECT coef FROM ${prefixe}affectations WHERE code_classe='$idClasse' AND code_matiere='$idMatiere' ";
	$curs=execSql($sql);
	$liste2=chargeMat($curs);
	unset($curs);
	return $liste2;
}

function recupCoeff($idMatiere,$idClasse,$ordre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT coef FROM ${prefixe}affectations WHERE ordre_affichage='$ordre' AND code_classe='$idClasse' AND code_matiere='$idMatiere' ";
	$curs=execSql($sql);
	$liste2=chargeMat($curs);
	unset($curs);
	if (count($liste2) > 0) {
		return $liste2[0][0];
	}else {
		return "";
	}
}

function moyGenEleve($note,$coef) {
	if ($coef == 0) { return " " ;}
	$resultat= $note / $coef;
	$resultat=number_format($resultat,2,',','');
	return $resultat;
}


function moyGenEleveab($note,$coef) {
	if ($coef == 0) { return " " ;}
	$resultat= $note / $coef;
	return $resultat;
}



function moyeMatGenGroupe($idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof) {
	global $cnx;
	global $prefixe;
	$tablisteEleve=listeEleveDansGroupe($idgroupe);
	$i=0;
	$notefinale=0;
	foreach($tablisteEleve as $cle => $value) {
		$idEleve=$value;
		$moyenneEleve=moyenneEleveMatiereGroupe($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
		if (trim($moyenneEleve) != "") {
			$i++;		
			$notefinale+=$moyenneEleve;
		}
	}
	if ($i > 0) {
		$notefinale=$notefinale/$i;
		$notefinale=number_format($notefinale,2,'.','');
	}else{
		$notefinale="";
	}	
	return $notefinale;
}


function moyeMatGenGroupeSansExam($idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof) {
	global $cnx;
	global $prefixe;
	$tablisteEleve=listeEleveDansGroupe($idgroupe);
	$i=0;
	$notefinale=0;
	foreach($tablisteEleve as $cle => $value) {
		$idEleve=$value;
		$moyenneEleve=moyenneEleveMatiereGroupeSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
		if (trim($moyenneEleve) != "") {
			$i++;		
			$notefinale+=$moyenneEleve;
		}
	}
	if ($i > 0) {
		$notefinale=$notefinale/$i;
		$notefinale=number_format($notefinale,2,'.','');
	}else{
		$notefinale="";
	}	
	return $notefinale;
}



function sommeMoyeMatGenGroupe($idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof) {
	global $cnx;
	global $prefixe;
	$tablisteEleve=listeEleveDansGroupe($idgroupe);
	$i=0;
	$notefinale=0;
	foreach($tablisteEleve as $cle => $value) {
		$idEleve=$value;
		$moyenneEleve=moyenneEleveMatiereGroupe($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
		if (trim($moyenneEleve) != "") {
			$i++;		
			$notefinale+=$moyenneEleve;
		}
	}
	if ($i > 0) {
		// $notefinale=$notefinale/$i;
		$notefinale=number_format($notefinale,2,'.','');
		$notefinale=array('nbSomme'=>$notefinale,'nbNote'=>$i);
	}else{
		$notefinale="";
	}	
	return $notefinale;
}

function moyeMatGenGroupeExamen($idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$examen) {
	global $cnx;
	global $prefixe;
	$tablisteEleve=listeEleveDansGroupe($idgroupe);
	$i=0;
	$notefinale=0;
	foreach($tablisteEleve as $cle => $value) {
		$idEleve=$value;
		$moyenneEleve=moyenneEleveMatiereGroupeExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$examen);
		if (trim($moyenneEleve) != "") {
			$i++;		
			$notefinale+=$moyenneEleve;
		}
	}
	if ($i > 0) {
		$notefinale=$notefinale/$i;
		$notefinale=number_format($notefinale,2,'.','');
	}else{
		$notefinale="";
	}	
	return $notefinale;
}


function RangsGroupe($idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof) {
	global $cnx;
	global $prefixe;
	$tablisteEleve=listeEleveDansGroupe($idgroupe);
	$i=0;
	$notefinale=0;
	foreach($tablisteEleve as $cle => $value) {
		$idEleve=$value;
		$moyenneEleve=moyenneEleveMatiereGroupe($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
		if (trim($moyenneEleve) != "") {
			$i++;		
			$classement[$i]=$moyenneEleve;
		}
		
	}
	if ($i > 0) {
		arsort($classement);
		return $classement;
	}
}

function recherchetypenotegroupe($idMatiere,$dateDebut,$dateFin,$idgroupe) {
	global $cnx;
	global $prefixe;

	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT truncate(note,2),coef,typenote FROM ${prefixe}notes WHERE note >= 0 AND code_mat='$idMatiere' AND date>='$dateDebut' AND date<='$dateFin' AND ( id_groupe = '$idgroupe' OR id_groupe = 0 )";
	// AND ( id_groupe = '$idgroupe' OR id_groupe = 0 )";
	// supprimer OR id_groupe = 0

        $curs=execSql($sql);
        $liste=chargeMat($curs);
	unset($curs);
	$noteret="fr";
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == "en") { $noteret="en"; }
	}
	return $noteret;
}

function recherchetypenote($idMatiere,$dateDebut,$dateFin,$idclasse) {
	global $cnx;
	global $prefixe;

	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
	$sql="SELECT truncate(note,2),coef,typenote FROM ${prefixe}notes WHERE code_mat='$idMatiere' AND date>='$dateDebut' AND date<='$dateFin' AND id_classe = '$idclasse'";
	$curs=execSql($sql);
        $liste=chargeMat($curs);
	unset($curs);
	$noteret="fr";
	for($cc=0;$cc<count($liste);$cc++) {
		if ($liste[$cc][0]=="-1") { continue; }
		if ($liste[$cc][0]=="-2") { continue; }
		if ($liste[$cc][0]=="-3") { continue; }
		if ($liste[$cc][0]=="-4") { continue; }
		if ($liste[$cc][0]=="-5") { continue; }
		if ($liste[$cc][2] == "en") { $noteret="en"; }
	}
	return $noteret;
}

function moyeMatGenExamen($idMatiere,$dateDebut,$dateFin,$idclasse,$idprof,$examen) {
	global $cnx;
	global $prefixe;

	$tablisteEleve=recupEleve($idclasse); 
	$ii=0;
	$notefinale=0;
	for($i=0;$i<count($tablisteEleve);$i++) {
		$idEleve=$tablisteEleve[$i][4];
		$moyenneEleve=moyenneEleveMatiereExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$examen);
		if (trim($moyenneEleve) != "") {
			$ii++;		
			$notefinale+=$moyenneEleve;
		}
	}

	if ($ii > 0) {
		$notefinale=$notefinale/$ii;
		$notefinale=number_format($notefinale,2,'.','');
	}else{
		$notefinale="";
	}	
	return $notefinale;
}


function moyeMatGen($idMatiere,$dateDebut,$dateFin,$idclasse,$idprof) {
	global $cnx;
	global $prefixe;

	$tablisteEleve=recupEleve($idclasse); 
	$ii=0;
	$notefinale=0;
	for($i=0;$i<count($tablisteEleve);$i++) {
		$idEleve=$tablisteEleve[$i][4];
		$moyenneEleve=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
		if (trim($moyenneEleve) != "") {
			$ii++;		
			$notefinale+=$moyenneEleve;
		}
	}

	if ($ii > 0) {
		$notefinale=$notefinale/$ii;
		$notefinale=number_format($notefinale,2,'.','');
	}else{
		$notefinale="";
	}	
	return $notefinale;
}


function moyeMatGenSansExam($idMatiere,$dateDebut,$dateFin,$idclasse,$idprof) {
	global $cnx;
	global $prefixe;

	$tablisteEleve=recupEleve($idclasse); 
	$ii=0;
	$notefinale=0;
	for($i=0;$i<count($tablisteEleve);$i++) {
		$idEleve=$tablisteEleve[$i][4];
		$moyenneEleve=moyenneEleveMatiereSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
		if (trim($moyenneEleve) != "") {
			$ii++;		
			$notefinale+=$moyenneEleve;
		}
	}

	if ($ii > 0) {
		$notefinale=$notefinale/$ii;
		$notefinale=number_format($notefinale,2,'.','');
	}else{
		$notefinale="";
	}	
	return $notefinale;
}

function moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idclasse) {
	global $cnx;
	global $prefixe;

	$tablisteEleve=recupEleve($idclasse); 
	$ii=0;
	$notefinale=0;
	for($i=0;$i<count($tablisteEleve);$i++) {
		$idEleve=$tablisteEleve[$i][4];
		$moyenneEleve=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if (trim($moyenneEleve) != "") {
			$ii++;		
			$notefinale+=$moyenneEleve;
		}
	}

	if ($ii > 0) {
		$notefinale=$notefinale/$ii;
		$notefinale=number_format($notefinale,2,'.','');
	}else{
		$notefinale="";
	}	
	return $notefinale;
}

function moyeMatGenBrevetExamen($idMatiere,$dateDebut,$dateFin,$idclasse,$examen) {
	global $cnx;
	global $prefixe;

	$tablisteEleve=recupEleve($idclasse); 
	$ii=0;
	$notefinale=0;
	for($i=0;$i<count($tablisteEleve);$i++) {
		$idEleve=$tablisteEleve[$i][4];
		$moyenneEleve=moyenneEleveMatiereBrevetViaExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$examen);
		if (trim($moyenneEleve) != "") {
			$ii++;		
			$notefinale+=$moyenneEleve;
		}
	}

	if ($ii > 0) {
		$notefinale=$notefinale/$ii;
		$notefinale=number_format($notefinale,2,'.','');
	}else{
		$notefinale="";
	}	
	return $notefinale;
}

function sommeMoyeMatGen($idMatiere,$dateDebut,$dateFin,$idclasse,$idprof) {
	global $cnx;
	global $prefixe;
	$tablisteEleve=recupEleve($idclasse); 
	$ii=0;
	$notefinale=0;
	for($i=0;$i<count($tablisteEleve);$i++) {
		$idEleve=$tablisteEleve[$i][4];
		$moyenneEleve=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
		if (trim($moyenneEleve) != "") {
			$ii++;		
			$notefinale+=$moyenneEleve;
		}
	}
	if ($ii > 0) {
		// $notefinale=$notefinale/$ii;
		$notefinale=number_format($notefinale,2,'.','');
		$notefinale=array('nbSomme'=>$notefinale,'nbNote'=>$ii);
	}else{
		$notefinale="";
	}	
	return $notefinale;
}


function Rangs($idMatiere,$dateDebut,$dateFin,$idclasse,$idprof) {
	global $cnx;
	global $prefixe;

	$tablisteEleve=recupEleve($idclasse); 
	$ii=0;
	$notefinale=0;
	for($i=0;$i<count($tablisteEleve);$i++) {
		$idEleve=$tablisteEleve[$i][4];
		$moyenneEleve=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);	
		if (trim($moyenneEleve) != "") {
			$ii++;		
			$classement[$ii]=$moyenneEleve;
		}
	}

	if ($ii > 0) {
		arsort($classement);
		return $classement;
	}
}



function profAff($idMatiere,$idClasse,$ordre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT code_prof FROM ${prefixe}affectations WHERE ordre_affichage='$ordre' AND code_classe='$idClasse' AND code_matiere='$idMatiere' ";
	$curs=execSql($sql);
	$liste2=chargeMat($curs);
	unset($curs);
	return $liste2[0][0];
}

function calculMoyenClasseBlanc($idClasse,$eleveT,$dateDebut,$dateFin,$ordre,$examen){
	global $cnx;
	global $prefixe;
	$ordre=ordre_matiere($idClasse);
	for($i=0;$i<count($ordre);$i++) {
		$idMatiere=$ordre[$i][0];
		$num_ordre=$ordre[$i][2];
		$tabmatiere[$num_ordre]="$num_ordre##$idMatiere";
	}
	//eleves
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
		// variable eleve
		$idEleve=$eleveT[$j][4];
		//moyennes matières
		foreach ($tabmatiere as $key => $value)  {
			list($num_ordre,$idMatiere)=preg_split('/##/',$value);
			$idprof=recherche_prof($idMatiere,$idClasse,$num_ordre);
			$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$num_ordre);
			if ($verifGroupe) { 
		       		$noteaff="";
			}else{
				if ($examen == "Partiel Blanc2") { $examen="Partiel Blanc"; $pigiermelun="1"; }  
				$noteaff=moyenneEleveMatiereExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$examen);
				if ($pigiermelun == "1") $examen="Partiel Blanc2";
			}
			
			// mise en place des coeff
			if ($examen == "BAC Blanc") { $typeexamen="bull401"; }  // BAC Blanc
			if ($examen == "BTS Blanc") { $typeexamen="bull402"; }  // BTS Blanc
			if ($examen == "Brevet Blanc") { $typeexamen="bull403"; }  // Brevet Blanc
			if ($examen == "CAP Blanc") { $typeexamen="bull404"; }  // CAP Blanc
			if ($examen == "BEP Blanc") { $typeexamen="bull405"; }  // BEP Blanc
			if ($examen == "Partiel Blanc") { $typeexamen="bull406"; }  // Partiel Blanc
			if ($examen == "Brevet Professionnel Blanc") { $typeexamen="bull409"; }  // Partiel Blanc
			if (($examen == "pigiercanne") || ($examen == "jtc") || ($examen == "Partiel Blanc2") ) { 
				$coeffaff=recupCoeff($idMatiere,$idClasse,$num_ordre);
			}else{
				$coeffaff=recup_coef_bulletin($typeexamen,$idClasse,$idMatiere,$num_ordre);
			}
			if ($noteaff != "" ) {
			        $noteMoyEleGTempo = $noteaff * $coeffaff;
       	        		$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
		                $coefEleG=$coefEleG + $coeffaff;
			}
		}
		$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
		if (trim($moyenEleve) != "") {
			$moyenEleve=preg_replace('/,/','.',$moyenEleve);
			$moyenClasseGen=$moyenClasseGen+$moyenEleve;
			$nbeleve2++;
		}
		$coefEleG="";
		$noteMoyEleG="";
	} // fin du for on passe à l'eleve suivant
	if ($nbeleve2 > 0) {
		$moyenClasseGen=number_format($moyenClasseGen/$nbeleve2,2,'.','');
	}else{
		$moyenClasseGen="";
	}	
	return $moyenClasseGen;
}


function calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$ordre){
	global $cnx;
	global $prefixe;
	$ordre=ordre_matiere($idClasse);
	for($i=0;$i<count($ordre);$i++) {
		$idMatiere=$ordre[$i][0];
		$num_ordre=$ordre[$i][2];
		$tabmatiere[$num_ordre]="$num_ordre##$idMatiere";
	}
	//eleves
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
		// variable eleve
		$idEleve=$eleveT[$j][4];
		//moyennes matières
		foreach ($tabmatiere as $key => $value)  {
			list($num_ordre,$idMatiere)=preg_split('/##/',$value);
			$idprof=recherche_prof($idMatiere,$idClasse,$num_ordre);
			$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$num_ordre);
			if ($verifGroupe) { 
		       		$noteaff="";
			}else{
				$noteaff=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
			}
			// mise en place des coeff
			$coeffaff=recupCoeff($idMatiere,$idClasse,$num_ordre);
			if ( $noteaff != "" ) {
			        $noteMoyEleGTempo = $noteaff * $coeffaff;
       	        		$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
		                $coefEleG=$coefEleG + $coeffaff;
			}
		}
		$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
		if (trim($moyenEleve) != "") {
			$moyenEleve=preg_replace('/,/','.',$moyenEleve);
			$moyenClasseGen=$moyenClasseGen+$moyenEleve;
			$nbeleve2++;
		}
		$coefEleG="";
		$noteMoyEleG="";
	} // fin du for on passe à l'eleve suivant
	if ($nbeleve2 > 0) {
		$moyenClasseGen=number_format($moyenClasseGen/$nbeleve2,2,'.','');
	}else{
		$moyenClasseGen="";
	}	
	return $moyenClasseGen;
}


function calculMoyenClasseSansExam($idClasse,$eleveT,$dateDebut,$dateFin,$ordre){
	global $cnx;
	global $prefixe;
	$ordre=ordre_matiere($idClasse);
	for($i=0;$i<count($ordre);$i++) {
		$idMatiere=$ordre[$i][0];
		$num_ordre=$ordre[$i][2];
		$tabmatiere[$num_ordre]="$num_ordre##$idMatiere";
	}
	//eleves
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
		// variable eleve
		$idEleve=$eleveT[$j][4];
		//moyennes matières
		foreach ($tabmatiere as $key => $value)  {
			list($num_ordre,$idMatiere)=preg_split('/##/',$value);
			$idprof=recherche_prof($idMatiere,$idClasse,$num_ordre);
			$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$num_ordre);
			if ($verifGroupe) { 
		       		$noteaff="";
			}else{
				$noteaff=moyenneEleveMatiereSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
			}
			// mise en place des coeff
			$coeffaff=recupCoeff($idMatiere,$idClasse,$num_ordre);
			if ( $noteaff != "" ) {
			        $noteMoyEleGTempo = $noteaff * $coeffaff;
       	        		$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
		                $coefEleG=$coefEleG + $coeffaff;
			}
		}
		$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
		if (trim($moyenEleve) != "") {
			$moyenEleve=preg_replace('/,/','.',$moyenEleve);
			$moyenClasseGen=$moyenClasseGen+$moyenEleve;
			$nbeleve2++;
		}
		$coefEleG="";
		$noteMoyEleG="";
	} // fin du for on passe à l'eleve suivant
	if ($nbeleve2 > 0) {
		$moyenClasseGen=number_format($moyenClasseGen/$nbeleve2,2,'.','');
	}else{
		$moyenClasseGen="";
	}	
	return $moyenClasseGen;
}

// avec gestion des matieres facultatifs
function calculMoyenClasse2($idClasse,$eleveT,$dateDebut,$dateFin,$ordre,$tabMatFacul){
	global $cnx;
	global $prefixe;
	$ordre=ordre_matiere($idClasse);
	for($i=0;$i<count($ordre);$i++) {
		$idMatiere=$ordre[$i][0];
		$num_ordre=$ordre[$i][2];
		$tabmatiere[$num_ordre]="$num_ordre##$idMatiere";
	}
	//eleves
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
		// variable eleve
		$idEleve=$eleveT[$j][4];
		//moyennes matières
		foreach ($tabmatiere as $key => $value)  {
			list($num_ordre,$idMatiere)=preg_split('/##/',$value);
			$idprof=recherche_prof($idMatiere,$idClasse,$num_ordre);
			$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$num_ordre,$noteaff);
			if ($verifGroupe) { 
		       		$noteaff="";
			}else{
				$noteaff=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
			}
			// mise en place des coeff
			$coeffaff=recupCoeff($idMatiere,$idClasse,$num_ordre);
			if ( $noteaff != "" ) {
				if (verifMatFacul($tabMatFacul,$idMatiere,$noteaff)) { continue; }
			        $noteMoyEleGTempo = $noteaff * $coeffaff;
       	        		$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
		                $coefEleG=$coefEleG + $coeffaff;
			}
		}
		$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
		if (trim($moyenEleve) != "") {
			$moyenEleve=preg_replace('/,/','.',$moyenEleve);
			$moyenClasseGen=$moyenClasseGen+$moyenEleve;
			$nbeleve2++;
		}
		$coefEleG="";
		$noteMoyEleG="";
	} // fin du for on passe à l'eleve suivant
	if ($nbeleve2 > 0) {
		$moyenClasseGen=number_format($moyenClasseGen/$nbeleve2,2,'.','');
	}else{
		$moyenClasseGen="";
	}	
	return $moyenClasseGen;
}

function calculNoteVieScolaireEns($idEleve,$trimestre) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT  note,idclasse,idmatiere FROM ${prefixe}notes_scolaire WHERE ideleve='$idEleve' AND  trimestre='$trimestre' AND annee_scolaire='$anneeScolaire' ORDER BY idclasse DESC";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	$idclasse=$data[0][1];
	$sql="SELECT coefprof,coefviescolaire FROM ${prefixe}notes_scolaire_param WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$data2=chargeMat($curs);
	$coefprof=$data2[0][0];
	$coefviescolaire=$data2[0][1];
	for ($i=0;$i<count($data);$i++) {
		$idmatiere=$data[$i][2];
		$note=$data[$i][0];
		if ($note >= 0) {
			if ($idmatiere != "-10") {
				$noteMoyEleGTempo = $note * $coefprof;
       		        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefprof;
			}
		}
	}
	$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	if (trim($moyenEleve) != "") {
		$moyenClasseGen=preg_replace('/,/','.',$moyenEleve);
		return number_format($moyenClasseGen,2,'.','');
	}else{
		$moyenClasseGen="";
		return $moyenClasseGen;
	}

}

function calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$trimestre,$examen=0) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT  note,idclasse,idmatiere FROM ${prefixe}notes_scolaire WHERE ideleve='$idEleve' AND trimestre='$trimestre' AND examen='$examen' AND annee_scolaire='$anneeScolaire' ORDER BY idclasse DESC";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	$idclasse=$data[0][1];
	$sql="SELECT coefprof,coefviescolaire FROM ${prefixe}notes_scolaire_param WHERE idclasse='$idclasse'  AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$data2=chargeMat($curs);
	$coefprof=$data2[0][0];
	$coefviescolaire=$data2[0][1];
	for ($i=0;$i<count($data);$i++) {
		$idmatiere=$data[$i][2];
		$note=$data[$i][0];
		if ($note >= 0) {
			if ($idmatiere == "-10") {
				$noteMoyEleGTempo = $note * $coefviescolaire;
       		        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefviescolaire;
			}else{
				$noteMoyEleGTempo = $note * $coefprof;
       		        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefprof;
			}
		}
	}
	$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	if (trim($moyenEleve) != "") {
		$moyenClasseGen=preg_replace('/,/','.',$moyenEleve);
	}else{
		$moyenClasseGen="";
	}
	return $moyenClasseGen;
}

function calculNoteVieScolaireJusquauTrimestre($idEleve,$coefProf,$coefVieScol,$trimestre) {
	global $cnx;
	global $prefixe; 
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	if ($trimestre == "trimestre1") { $tabT[]="trimestre1"; }
	if ($trimestre == "trimestre2") { $tabT[]="trimestre1";$tabT[]="trimestre2"; }
	if ($trimestre == "trimestre3") { $tabT[]="trimestre1";$tabT[]="trimestre2";$tabT[]="trimestre3"; }
	foreach($tabT as $key => $trimestre) {
		$sql="SELECT  note,idclasse,idmatiere FROM ${prefixe}notes_scolaire WHERE ideleve='$idEleve' AND  trimestre='$trimestre' AND annee_scolaire='$anneeScolaire' ";
		$curs=execSql($sql);
		$data=chargeMat($curs);
		$idclasse=$data[0][1];
		$sql="SELECT coefprof,coefviescolaire FROM ${prefixe}notes_scolaire_param WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
		$curs=execSql($sql);	
		$data2=chargeMat($curs);
		$coefprof=$data2[0][0];
		$coefviescolaire=$data2[0][1];
		for ($i=0;$i<count($data);$i++) {
			$idmatiere=$data[$i][2];
			$note=$data[$i][0];
			if ($note >= 0) {
				if ($idmatiere == "-10") {
					$noteMoyEleGTempo = $note * $coefviescolaire;
       			        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
					$coefEleG=$coefEleG + $coefviescolaire;
				}else{
					$noteMoyEleGTempo = $note * $coefprof;
       			        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
					$coefEleG=$coefEleG + $coefprof;
				}
			}
		}
	}
	$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	if (trim($moyenEleve) != "") {
		$moyenClasseGen=preg_replace('/,/','.',$moyenEleve);
	}else{
		$moyenClasseGen="";
	}
	return $moyenClasseGen;
}


function calculNoteVieScolaireBrevet($idEleve,$idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT  note,idclasse,idmatiere FROM ${prefixe}notes_scolaire WHERE ideleve='$idEleve' AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	//$idclasse=$data[0][1];
	$sql="SELECT coefprof,coefviescolaire FROM ${prefixe}notes_scolaire_param WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$data2=chargeMat($curs);
	$coefprof=$data2[0][0];
	$coefviescolaire=$data2[0][1];
	for ($i=0;$i<count($data);$i++) {
		$idmatiere=$data[$i][2];
		$note=$data[$i][0];
		if ($note >= 0) {
			if ($idmatiere == "-10") {
				$noteMoyEleGTempo = $note * $coefviescolaire;
       		        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefviescolaire;
			}else{
				$noteMoyEleGTempo = $note * $coefprof;
       		        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefprof;
			}
		}
	}
	$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	if (trim($moyenEleve) != "") {
		$moyenClasseGen=preg_replace('/,/','.',$moyenEleve);
	}else{
		$moyenClasseGen="";
	}
	return $moyenClasseGen;
}


function moyeMatGenVieScolaireBrevet($idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT   coefprof,coefviescolaire FROM ${prefixe}notes_scolaire_param WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$data2=chargeMat($curs);
	$coefprof=$data2[0][0];
	$coefviescolaire=$data2[0][1];
	$sql="SELECT  note,idclasse,idmatiere FROM ${prefixe}notes_scolaire WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	for ($i=0;$i<count($data);$i++) {
		$idmatiere=$data[$i][2];
		$note=$data[$i][0];
		if ($note >= 0) {
			if ($idmatiere == "-10") {
				$noteMoyEleGTempo = $note * $coefviescolaire;
       		        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefviescolaire;
			}else{
				$noteMoyEleGTempo = $note * $coefprof;
       		        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefprof;
			}
		}
	}
	$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	if (trim($moyenEleve) != "") {
		$moyenClasseGen=preg_replace('/,/','.',$moyenEleve);
	}else{
		$moyenClasseGen="";
	}
	return $moyenClasseGen;
}




function moyeMatGenVieScolaire($trimestre,$idclasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT   coefprof,coefviescolaire FROM ${prefixe}notes_scolaire_param WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$data2=chargeMat($curs);
	$coefprof=$data2[0][0];
	$coefviescolaire=$data2[0][1];
	$sql="SELECT  note,idclasse,idmatiere FROM ${prefixe}notes_scolaire WHERE idclasse='$idclasse' AND  trimestre='$trimestre' AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	for ($i=0;$i<count($data);$i++) {
		$idmatiere=$data[$i][2];
		$note=$data[$i][0];
		if ($note >= 0) {
			if ($idmatiere == "-10") {
				$noteMoyEleGTempo = $note * $coefviescolaire;
       		        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefviescolaire;
			}else{
				$noteMoyEleGTempo = $note * $coefprof;
       		        	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefprof;
			}
		}
	}
	$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	if (trim($moyenEleve) != "") {
		$moyenClasseGen=preg_replace('/,/','.',$moyenEleve);
	}else{
		$moyenClasseGen="";
	}
	return $moyenClasseGen;
}



function verifMatFacul($tabMatFacul,$idmatiere,$note) {
	global $cnx;
	global $prefixe;
	foreach($tabMatFacul as $key=>$value) {
		if (($idmatiere == $value) && ($note > 10)){
			return "1";
		}
		if (($idmatiere == $value) && ($note <= 10)){
			return "-2";
		}
	}
	return "0";
}


function nbMatiere($ordre,$idEleve,$idClasse) {
	$nb=0;
	for($i=0;$i<count($ordre);$i++) {
		$TT=0;
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
		if ($verifGroupe) {  continue; } 
   		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);
		$datasousmatiere=verifsousmatierebull($idMatiere);
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale=$datasousmatiere[0][2];
			$nomSousMatiere=$datasousmatiere[0][1];
		}
		$sousmatiere=trim($ordre[$i][4]);  
		$libelleMatiere=$ordre[$i][5]; 
		$ordrematiere=$ordre[$i][3]; 
		$ii=$i;
		while(true) {
			$ii++;
			if (verifMatiereAvecGroupe($ordre[$ii][0],$idEleve,$idClasse,$ordre[$ii][2])) { break; }
			if (($sousmatiere != "0") && ($sousmatiere != "")){
				if(!verifMatiereSuivanteCommeSousmatiere($ordre[$ii][0])) { break; }
				$matiereSuivante=chercheMatiereNom3($ordre[$ii][0]);
				if ( trim($libelleMatiere) == trim($matiereSuivante)) {
					$TT=1;
				}else{
					break;
				}
			}else{
				break;
			}
		}
		if ($TT > 0) { 	continue; }
		$nb++;	
	}
	return $nb;
}


//calcul moyenne trimestriel d'un élève
function moyenEleveMat2($idEleve,$idMatiere,$dateDebut,$dateFin,$idclasse,$ordre) {
	$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idclasse,$ordre);
	if ($verifGroupe) { return ""; } // verif pour l'eleve de l'affichage de la matiere
	// mise en place moyenne eleve
	$idprof=recherche_prof($idMatiere,$idclasse,$ordre);
	$noteaff=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
	if ($noteaff == "") {
		$noteaff="";   // si pas de note alors "0"
	}else{
		if (($noteaff < 10) && ($noteaff != "")) { $noteaff="0".$noteaff; }
	}
	$noteaff=preg_replace("/,/",".",$noteaff);
	return $noteaff;
}


function verifMatiereAvecGroupeSansEleve($idMatiere,$idClasse,$ordreaffich) {
        global $cnx;
        global $prefixe;
        $sql="SELECT code_groupe FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND ordre_affichage='$ordreaffich' ";
        $curs=execSql($sql);
        $resultat=chargeMat($curs);
        unset($curs);
        unset($sql);
        return $resultat[0][0];
}


function moyenEleveUE($code_ue,$idClasse,$idEleve,$trimestre,$dateDebut,$dateFin,$ordre) {
        global $cnx;
	global $prefixe;
	$listeMatiere=recupMatiereUE($code_ue,$idClasse);
	for($i=0;$i<count($listeMatiere);$i++) {
		$idmatiere=$listeMatiere[$i][0];
		$idprof=$listeMatiere[$i][2];
		$verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordre);
		if ($verifGroupe) {  continue; } 
	    	$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre);
		$coef=recupCoefUE($idmatiere,$idClasse,$trimestre);
		if (($idgroupe == "0") || (trim($idgroupe) == "")) {
			$noteaffP1=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
		}else{
			$noteaffP1=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
		}

		if ($noteaffP1 != "") {
			$moyUEP1+=$noteaffP1*$coef;
			$coefUEP1+=$coef;
		}


	}
	if ($moyUEP1 != "") {
		$moyUEP1=$moyUEP1/$coefUEP1;
		$moyUEP1Aff=$moyUEP1;
	}else{
		$moyUEP1Aff="";
	}
	return($moyUEP1Aff);
}

function moyenEleveUESansOPT4($code_ue,$idClasse,$idEleve,$trimestre,$dateDebut,$dateFin,$ordre) {
        global $cnx;
        global $prefixe;
        $listeMatiere=recupMatiereUE($code_ue,$idClasse);
        for($i=0;$i<count($listeMatiere);$i++) {
                $idmatiere=$listeMatiere[$i][0];
		if (verifMatiereOPT4($idmatiere,$idClasse,$ordre)) continue;
                $idprof=$listeMatiere[$i][2];
                $verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordre);
                if ($verifGroupe) {  continue; }
                $idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre);
                $coef=recupCoefUE($idmatiere,$idClasse,$trimestre);
                if (($idgroupe == "0") || (trim($idgroupe) == "")) {
                        $noteaffP1=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
                }else{
                        $noteaffP1=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
                }

                if ($noteaffP1 != "") {
                        $moyUEP1+=$noteaffP1*$coef;
                        $coefUEP1+=$coef;
                }


        }
        if ($moyUEP1 != "") {
                $moyUEP1=$moyUEP1/$coefUEP1;
                $moyUEP1Aff=$moyUEP1;
        }else{
                $moyUEP1Aff="";
        }
        return($moyUEP1Aff);
}


function recupCoeffViaTrim($idMatiere,$idClasse,$ordre,$tri) {
	global $cnx;
	global $prefixe;
	$sql="SELECT coef FROM ${prefixe}affectations WHERE ordre_affichage='$ordre' AND code_classe='$idClasse' AND code_matiere='$idMatiere' AND (trim='$tri' OR trim='tous')";
	$curs=execSql($sql);
	$liste2=chargeMat($curs);
	unset($curs);
	if (count($liste2) > 0) {
		return $liste2[0][0];
	}else {
		return "";
	}
}

function recupExamPigierAix($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof) {
        global $cnx;
        global $prefixe;
        $dateDebut=dateFormBase($dateDebut);
        $dateFin=dateFormBase($dateFin);
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
	        AND noteexam LIKE \"%examen%\"";
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
                if ($liste[$cc][2] == 15) { $liste[$cc][0]*=1.333333333333333; $liste[$cc][1] /= 1.33333333333333;  }
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

function verifABSPartiel($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$examen) {
        global $cnx;
        global $prefixe;
        $dateDebut=dateFormBase($dateDebut);
        $dateFin=dateFormBase($dateFin);
        $sql="SELECT * FROM ${prefixe}notes WHERE elev_id='$idEleve' AND code_mat='$idMatiere' AND date >= '$dateDebut' AND date <= '$dateFin' AND prof_id='$idprof' AND noteexam = '$examen' AND note = '-1'"; 
        $curs=execSql($sql);
        $liste=chargeMat($curs);
        return(count($liste));
}

?>
