<?php
/***************************************************************************
 *                              AMBIS
 *                            ---------------
 *
 *   begin                : Decemebre 2007
 *   copyright            : (C) 2000 AMBIS
 *   Site                 : http://www.ambis.fr
 *
 *
 ***************************************************************************/

function gestion_ue($idClasse,$semestre,$annuel=false,$anneeScolaire) {
	global $cnx;
	global $prefixe;
	if ($anneeScolaire == '') $anneeScolaire=$_COOKIE["anneeScolaire"];
	if ($annuel == true) {
		$clause="AND (a.semestre = '1' OR a.semestre = '2'  OR a.semestre = '0' ) ";  
	}else{
		if ($semestre==3) {$clause="AND (a.semestre = '1' OR a.semestre = '2'  OR a.semestre = '0' ) ";}  
		if ($semestre==2) {$clause="AND (a.semestre = '2' OR a.semestre = '0') ";}
		if ($semestre==1) {$clause="AND (a.semestre = '1' OR a.semestre = '0') ";} 
	}
$sql=<<<SQL
SELECT
	a.code_ue,a.num_ue,trim(a.nom_ue),m.code_ue_detail,m.code_matiere,a.semestre
FROM
	${prefixe}ue a, ${prefixe}ue_detail m
WHERE
	a.code_classe = '$idClasse' 
$clause	
AND a.code_ue = m.code_ue
AND annee_scolaire='$anneeScolaire'
GROUP BY m.code_matiere
ORDER BY
	a.num_ue asc ,m.code_ue_detail
SQL;

	$curs=execSql($sql);
	$ue=chargeMat($curs);
	unset($curs);
	return $ue;
}

function recup_coef($idMatiere,$idClasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="
	SELECT
		${prefixe}affectations.coef
	FROM
		${prefixe}affectations 
	WHERE
		${prefixe}affectations.code_matiere='$idMatiere'
	AND ${prefixe}affectations.code_classe= '$idClasse' 
	AND ${prefixe}affectations.annee_scolaire= '$anneeScolaire' 
	";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	return $resultat[0][0];
	unset($curs);

}

function recup_coef_temp($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$partiel) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$where=0;
	if ($partiel=='partiel') {
	$where = " AND sujet ='PARTIEL' ";
	} elseif ($partiel=='periode') {
	$where = " AND sujet <>'PARTIEL' ";
	}

	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);

	$sql="
	SELECT
		${prefixe}affectations.coef,
		${prefixe}notes.id_classe,
		${prefixe}affectations.code_matiere,
		${prefixe}affectations.code_classe
	FROM
		${prefixe}notes , ${prefixe}affectations 
	WHERE
		${prefixe}notes.elev_id='$idEleve'
	AND ${prefixe}affectations.code_matiere='$idMatiere'
	AND ${prefixe}affectations.code_classe= ${prefixe}notes.id_classe	
	AND ${prefixe}affectations.annee_scolaire='$anneeScolaire' 
	AND ${prefixe}notes.code_mat='$idMatiere' " .$where .";";

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
		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }	
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

function cpte_matiere_ue($idUe) {
	global $cnx;
	global $prefixe;
$sql=<<<SQL
SELECT
	code_ue
FROM
	${prefixe}ue_detail 
WHERE
	code_ue = '$idUe'
SQL;
	$curs=execSql($sql);
	return mysqli_num_rows($curs);
	unset($curs);
}

function recup_ordre($idMatiere,$idClasse) {
	global $cnx;
	global $prefixe; 
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$sql="SELECT code_matiere,code_groupe,code_classe,ordre_affichage,code_prof FROM ${prefixe}affectations WHERE code_matiere='$idMatiere' AND code_classe='$idClasse' AND annee_scolaire='$anneeScolaire' ";
	$curs=execSql($sql);
	$resultat=chargeMat($curs);
	return $resultat[0][3];
}

function recupNotepartiel($idEleve,$idMatiere,$dateDebut,$dateFin,$idClasse) {
	global $cnx;
	global $prefixe;
	$anneeScolaire=$_COOKIE["anneeScolaire"];
	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);
//  MODIFIER POUR RECUP COEFF DANS TABLE AFFECTATION 28/10/15
	$sql="
	SELECT
		TRUNCATE(${prefixe}notes.note,2),
		${prefixe}notes.elev_id,
		${prefixe}notes.code_mat,
		${prefixe}notes.date,
		${prefixe}notes.sujet,
		${prefixe}notes.typenote,
		${prefixe}affectations.coef,
		${prefixe}notes.id_classe,
		${prefixe}affectations.code_matiere,
		${prefixe}affectations.code_classe
	FROM
		${prefixe}notes , ${prefixe}affectations 
	WHERE
		${prefixe}notes.elev_id='$idEleve'
	AND ${prefixe}notes.note>=0	
	AND ${prefixe}affectations.code_matiere='$idMatiere'
	AND ${prefixe}affectations.code_classe='$idClasse'
	AND ${prefixe}notes.code_mat='$idMatiere'
	AND ${prefixe}notes.date >= '$dateDebut'
	AND ${prefixe}notes.date <= '$dateFin'
	AND ${prefixe}affectations.annee_scolaire='$anneeScolaire' 
	AND ${prefixe}notes.sujet LIKE '%PARTIEL%'
	GROUP BY ${prefixe}notes.sujet,${prefixe}notes.date";
	
	$curs=execSql($sql);
	$liste_note=chargeMat($curs);
	unset($curs);
	return $liste_note;
}

function chargeMatperiode($curs) {
	global $cnx;
	global $prefixe;
	$liste_note = "- ";
//	print_r ($curs);
	for ($nb_ue=0;$nb_ue<count($curs);$nb_ue++) { 
		$liste_note = $liste_note.$curs[$nb_ue][0]." - ";
	}
	return $liste_note;
}

function recupNoteperiode($idEleve,$idMatiere,$dateDebut,$dateFin) {
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
		coef
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND sujet NOT LIKE '%PARTIEL%'";
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
		coef 
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
	AND code_mat='$idMatiere'
	AND date >= '$dateDebut'
	AND date <= '$dateFin'
	AND sujet NOT LIKE '%PARTIEL%'";
	}
//	print ($sql);
	$curs=execSql($sql);
	$liste=chargeMat($curs);
//	print_r ($liste);
	unset($curs);
	return $liste;
}

//-----------------------------
// pour le bulletin de note
//-----------------------------
function moyenneEleveMatiereVatel($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$partiel) {
	global $cnx;
	global $prefixe;
	$where=0;
	if ($partiel=='partiel') {
	$where = " AND sujet like '%PARTIEL%' ";
	} elseif ($partiel=='periode') {
	$where = " AND sujet NOT like '%PARTIEL%' ";
	}

	$dateDebut=dateFormBase($dateDebut);
	$dateFin=dateFormBase($dateFin);

	if(DBTYPE=='pgsql') {
	$sql="
	SELECT
		TRUNC(note,2),
		TRUNC(coef,2),
		typenote
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
		AND code_mat='$idMatiere'
		AND date >= '$dateDebut'
		AND date <= '$dateFin'
		AND prof_id = '$idprof'
		AND note >= 0 ". $where;
	}
	elseif(DBTYPE=='mysql')
	{
	$sql="
	SELECT
		TRUNCATE(note,2),
		TRUNCATE(coef,2),
		typenote
	FROM
		${prefixe}notes
	WHERE
		elev_id='$idEleve'
		AND code_mat='$idMatiere'
		AND date >= '$dateDebut'
		AND date <= '$dateFin'
		AND prof_id = '$idprof'
		AND note >= 0 ".$where;
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
		if ($liste[$cc][2] == "en") { $liste[$cc][0] =  $liste[$cc][0] / 5; $noteengl="oui"; }	
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

function calculMoyenClasseVatel($idClasse,$eleveT,$dateDebut,$dateFin,$ordre,$partiel){
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
			list($num_ordre,$idMatiere)=preg_split("/##/",$value);
			$idprof=recherche_prof($idMatiere,$idClasse,$num_ordre);
			$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$num_ordre);
			if ($verifGroupe) { 
		       		$noteaff="";
			}else{
				$noteaff=moyenneEleveMatiereVatel($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$partiel);
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
		
	} // fin du for on passe à l'eleve suivan
	if ($nbeleve2 > 0) {
		$moyenClasseGen=number_format($moyenClasseGen/$nbeleve2,2,'.','');
	}else{
		$moyenClasseGen="";
	}	
	return $moyenClasseGen;
}

function moyeMatGenVatel($idMatiere,$dateDebut,$dateFin,$idclasse,$idprof) {
	global $cnx;
	global $prefixe;

	$tablisteEleve=recupEleve($idclasse); 
	$ii=0;
	$notefinale=0;
	for($i=0;$i<count($tablisteEleve);$i++) {
		$idEleve=$tablisteEleve[$i][4];
		$moyenneEleve=moyenneEleveMatiereVatel($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,'periode');
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

function format_moyenne($note_m){
// Modif AMBIS 08 06 10 prb affichage note vide à 0
//	if ($note_m!=1000 && $note_m!=' ' && $note_m!=-1 || $note_m==0  ) {

	if ($note_m!=1000 && $note_m>=0 && is_numeric($note_m)) {
		$moyenne_formater = number_format($note_m,2,'.','');
	} else {
		$moyenne_formater = '';
	}
	return $moyenne_formater;
}


 //-------------------------------------------------------------------------//
// AMBIS Gestion des UE 9.10.2008										   //
//-------------------------------------------------------------------------//

function create_ue($nom_ue) {
        global $cnx;
	global $prefixe;
        $sql="INSERT INTO ${prefixe}ue(libelle) VALUES ('$nom_classe')";
	return(execSql($sql));
}

function vatel_create($champs,$table) {
    	global $cnx;
	global $prefixe;
//	print_r($champs);
	if (isset($champs)) {
		foreach ($champs as $nomvar => $valeurvar) {
			if (!empty($valeurvar) && $nomvar!='create' && $nomvar!='nb' && (!preg_match('/code_matiere/',$nomvar)) && (!preg_match('/idprof/',$nomvar))  ){
				$valeurvar=preg_replace("/\\\/","",$valeurvar);
				$valeurvar=addslashes($valeurvar);
				$requete_nom .= "`".$nomvar."`,";
				$requete_valeur .= "'".$valeurvar."',";
			}
		}
		$sql=" INSERT INTO `${prefixe}".$table."` ( ".substr($requete_nom, 0, -1)." ) VALUES ( ".substr($requete_valeur,0,-1).");";
		execSql($sql);
		$rt=$cnx->connection->insert_id;
		return($rt);
	}
}

function vatel_create_due($champs,$id,$table) {
    	global $cnx;
	global $prefixe;
	if (isset($champs)) {
		foreach ($champs as $nomvar => $valeurvar) {
		if (!empty($valeurvar) ) {
				$sql=" INSERT INTO `${prefixe}".$table."` (`code_matiere`,`code_ue`) VALUES ('".$valeurvar."','".$id."');";
				
				 (execSql($sql));
			}
		}
	}
}


function vatel_create_due_bis($code_matiere,$id,$idprof) {
    	global $cnx;
	global $prefixe;
	$sql=" INSERT INTO `${prefixe}ue_detail` (`code_matiere`,`code_ue`,`code_enseignant`) VALUES ('$code_matiere','$id','$idprof');";	
	execSql($sql);	
}

function vatel_liste_ue($idue) {  // fonction utilisée pour afficher la liste de toute des UE
   		global $cnx;
		global $prefixe;
		if ($idue !='') {
			$sql= " select * from `${prefixe}ue` where code_ue='".$idue."' ;";
		} else {
			$sql= " select * from `${prefixe}ue` order by code_classe,semestre;";
		}
	//	print $sql;
		return (chargeMat(execSql($sql)));
}

// affichage classe
function Vatel_affUneClasse($id){
	global $cnx;
	global $prefixe;
        $sql="SELECT libelle FROM ${prefixe}classes where code_class='".$id."' ;";
//		print $sql;
        $res2=execSql($sql);
        $data2=chargeMat($res2);
        return $data2;
}

function vatel_liste_uedetail($idue) {  // fonction utilisée pour afficher la liste de toute des UE
   		global $cnx;
		global $prefixe;
		if ($idue !='') {
			$sql= " select code_ue_detail,code_ue,code_matiere,code_enseignant  from `${prefixe}ue_detail` where code_ue='".$idue."' ;";
		} else {
			$sql= " select code_ue_detail,code_ue,code_matiere,code_enseignant  from `${prefixe}ue` order by code_classe;";
		}
	//	print $sql;
		return (chargeMat(execSql($sql)));
}

function vatel_modif_ue($champs,$id) {
   		global $cnx;
		global $prefixe;
	if (isset($champs)) {
		foreach ($champs as $nomvar => $valeurvar) {
			if (!empty($valeurvar) && $nomvar!='create'  && $nomvar!='id_detail' && $nomvar!='nb' && (!preg_match('/code_matiere/',$nomvar)) && (!preg_match('/idprof/',$nomvar))  ){
				$requete .="`".$nomvar."` = '".$valeurvar."',";
			}
		}
		$req=" update `${prefixe}ue` set ".substr($requete, 0, -1)." WHERE `code_ue` = '".$id."' LIMIT 1 ;";
		return (execSql($req));
	}
}

function vatel_modif_due($id) {
   		global $cnx;
		global $prefixe;
		$req = "DELETE FROM `tria_ue_detail` WHERE `code_ue` = '".$id."';";
		return (execSql($req));
}

function vatel_supp_ue($id,$table) {
   		global $cnx;
		global $prefixe;
		$req = "DELETE FROM `${prefixe}".$table."` WHERE `code_ue` = '".$id."';";
		return (execSql($req));
}

function vatel_moyenne($tableau) {
   		global $cnx;
		global $prefixe;
		array_multisort($tableau, SORT_ASC );
		$vm['min']= $tableau[0];
		$vm['max']=(end($tableau));
		$vm['moy']= ((array_sum($tableau))/(count($tableau)) );
		return ($vm);
	//	$nbmat=array_search(end($tableau), $tableau);
	//	$somme=array_sum($moyenne_matiere_part[1]);
	//	$moy=array_sum($moyenne_matiere_part[1])/($nbmat+1);

}

//-------------------------------------------------------------------------//
// AMBIS 14/12/09 

function Vatel_Classe_desc($id){ // recup descrlong de la classe pour affichage bulletin
	global $cnx;
	global $prefixe;
        $sql="SELECT desclong FROM ${prefixe}classes where code_class='".$id."' ;";
        $res3=execSql($sql);
        $data3=chargeMat($res3);
        return $data3[0][0];
} 


function vatel_liste_ueViaIdClasse($idclasse,$anneeScolaire) {  // fonction utilisée pour afficher la liste de toute des UE
        global $cnx;
        global $prefixe;
        $sql="select * from ${prefixe}ue where code_classe='$idclasse' AND annee_scolaire='$anneeScolaire' ;";
        return (chargeMat(execSql($sql)));
}

 
?>
