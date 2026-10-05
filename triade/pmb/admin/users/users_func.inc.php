<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users_func.inc.php,v 1.62 2022/04/01 12:27:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once("$class_path/entites.class.php");
require_once("$class_path/coordonnees.class.php");
	
function get_coordonnees_etab($user_id='0', $field_values=array(), $current_field=0, $form_name='') {

	global $msg, $charset;
	global $acquisition_active;
	global $user_acquisition_adr_form;
	
	if (!$acquisition_active || !ACQUISITION_AUTH || !$user_id) return;
	
	//Affichage de la liste des bibliothèques auxquelles a accès l'utilisateur
	$q = entites::list_biblio($user_id);
	$res = pmb_mysql_query($q);
	$nbr = pmb_mysql_num_rows($res);
	
	if ($nbr == '0') return;
	
	$tab1 = explode('|', $field_values[$current_field]);

	$tab_adr=array();
	foreach ($tab1 as $key=>$value) {
		$tab2=explode(',', $value);
		$tab_adr[$tab2[0]]['id_adr_fac']=$tab2[1];
		$tab_adr[$tab2[0]]['id_adr_liv']=$tab2[2];
	}

	$acquisition_user_param = "";
	while($row=pmb_mysql_fetch_object($res)){
		
		$acquisition_user_param.= "<div class='row'>";
		$acquisition_user_param.= "<label class='etiquette'>".htmlentities($row->raison_sociale, ENT_QUOTES, $charset)."</label>";
		
		$temp_adr_form = $user_acquisition_adr_form;
		
		if ($tab_adr[$row->id_entite]['id_adr_fac']) {
			$coord = new coordonnees($tab_adr[$row->id_entite]['id_adr_fac']);
			$id_adr_fac = $coord->id_contact;
			if($coord->libelle != '') $adr_fac = htmlentities($coord->libelle, ENT_QUOTES, $charset)."\n";
			if($coord->contact != '') $adr_fac.= htmlentities($coord->contact, ENT_QUOTES, $charset)."\n";
			if($coord->adr1 != '') $adr_fac.= htmlentities($coord->adr1, ENT_QUOTES, $charset)."\n";
			if($coord->adr2 != '') $adr_fac.= htmlentities($coord->adr2, ENT_QUOTES, $charset)."\n";
			if($coord->cp !='') $adr_fac.= htmlentities($coord->cp, ENT_QUOTES, $charset).' ';
			if($coord->ville != '') $adr_fac.= htmlentities($coord->ville, ENT_QUOTES, $charset);
		} else {
			$id_adr_fac = '0';
			$adr_fac = '';
		}

		if ($tab_adr[$row->id_entite]['id_adr_liv']) {
			$coord = new coordonnees($tab_adr[$row->id_entite]['id_adr_liv']);
			$id_adr_liv = $coord->id_contact;
			if($coord->libelle != '') $adr_liv = htmlentities($coord->libelle, ENT_QUOTES, $charset)."\n";
			if($coord->contact != '') $adr_liv.= htmlentities($coord->contact, ENT_QUOTES, $charset)."\n"; 
			if($coord->adr1 != '') $adr_liv.= htmlentities($coord->adr1, ENT_QUOTES, $charset)."\n";
			if($coord->adr2 != '') $adr_liv.= htmlentities($coord->adr2, ENT_QUOTES, $charset)."\n";
			if($coord->cp !='') $adr_liv.= htmlentities($coord->cp, ENT_QUOTES, $charset).' ';
			if($coord->ville != '') $adr_liv.= htmlentities($coord->ville, ENT_QUOTES, $charset);
		} else {
			$id_adr_liv = 0;
			$adr_liv = '';
		}

		$temp_adr_form = str_replace('!!id_bibli!!',$row->id_entite, $temp_adr_form);
		$temp_adr_form = str_replace('!!id_adr_liv!!',$id_adr_liv, $temp_adr_form);
		$temp_adr_form = str_replace('!!adr_liv!!',$adr_liv, $temp_adr_form);
		$temp_adr_form = str_replace('!!id_adr_fac!!',$id_adr_fac, $temp_adr_form);
		$temp_adr_form = str_replace('!!adr_fac!!',$adr_fac, $temp_adr_form);
						
		$acquisition_user_param.= $temp_adr_form;
		$acquisition_user_param.= "</div>";
		
	}
	$acquisition_user_param = str_replace('!!form_name!!', $form_name, $acquisition_user_param);
	$acquisition_user_param="<hr /><div class='row'>".htmlentities($msg['acquisition_user_deflt_adr'], ENT_QUOTES, $charset).$acquisition_user_param."</div>";
	return $acquisition_user_param;			
}


function set_coordonnees_etab() {

	global $id_adr_fac, $id_adr_liv;

	$acquisition_user_param = "";	
	if (!is_array($id_adr_fac)) {
		$acquisition_user_param .= "speci_coordonnees_etab = '' ";
		return $acquisition_user_param ;
	}
	
	ksort($id_adr_fac);
	reset($id_adr_fac);
	$i=0;
	$j=count($id_adr_fac);
	foreach ($id_adr_fac as $key => $val) {
		$acquisition_user_param.=$key.','.$val.','.$id_adr_liv[$key];
		$i++;
		if ($i < $j) $acquisition_user_param.='|';
	};
	
	$acquisition_user_param = "speci_coordonnees_etab = '".$acquisition_user_param."' "; 
	return $acquisition_user_param;			
}

//Retourne un tableau (userid=>nom prenom) à partir d'un tableau d'id 
function getUserName($tab=array()) {
	$res=array();
	if(is_array($tab) && count($tab)) {
		$q ="select userid, concat(nom,' ',prenom) as lib from users where userid in ('".implode("','", $tab)."') ";
		$r = pmb_mysql_query($q);
		while($row=pmb_mysql_fetch_object($r)) {
			$res[$row->userid]=$row->lib;
		}
	}
	return $res;
}

