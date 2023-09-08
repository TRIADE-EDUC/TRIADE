<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users_func.inc.php,v 1.56 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/entites.class.php");
require_once("$class_path/coordonnees.class.php");

function show_users() {

	global $msg;
	global $admin_user_list;
	global $admin_user_link1;
	global $admin_user_alert_row;
	
	print "<div class='row'>
	<input class='bouton' type='button' value=' $msg[85] ' onClick=\"document.location='./admin.php?categ=users&sub=users&action=add'\" />
	</div>";
	// affichage du tableau des utilisateurs
	$requete = "SELECT * FROM users ORDER BY username";
	$res = pmb_mysql_query($requete);

	$nbr = pmb_mysql_num_rows($res);

	while(($row=pmb_mysql_fetch_object($res))) {

		// réinitialisation des chaînes
		$dummy = $admin_user_list;
		$dummy1 = $admin_user_link1;
		
		$flag = "<img src='./images/flags/".$row->user_lang.".gif' width='24' height='16' vspace='3'>";

		$dummy =str_replace('!!user_link!!', $dummy1, $dummy);
		$dummy =str_replace('!!user_name!!', "$row->prenom $row->nom", $dummy);
		$dummy =str_replace('!!user_login!!', $row->username, $dummy);

		if($row->rights & ADMINISTRATION_AUTH)
			$dummy =str_replace('!!nuseradmin!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>' , $dummy);
		else 
			$dummy =str_replace('!!nuseradmin!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);

		if($row->rights & CATALOGAGE_AUTH)
			$dummy =str_replace('!!nusercatal!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nusercatal!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);

		if($row->rights & CIRCULATION_AUTH)
			$dummy =str_replace('!!nusercirc!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nusercirc!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);

		if($row->rights & PREF_AUTH)
			$dummy =str_replace('!!nuserpref!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nuserpref!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);

		if($row->rights & ACQUISITION_ACCOUNT_INVOICE_AUTH)
			$dummy =str_replace('!!nuseracquisition_account_invoice!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else
			$dummy =str_replace('!!nuseracquisition_account_invoice!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);	

		if($row->rights & AUTORITES_AUTH)
			$dummy =str_replace('!!nuserauth!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nuserauth!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
		if($row->rights & EDIT_AUTH)
			$dummy =str_replace('!!nuseredit!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nuseredit!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
		if($row->rights & EDIT_FORCING_AUTH)
			$dummy =str_replace('!!nusereditforcing!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nusereditforcing!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
		if($row->rights & SAUV_AUTH)
			$dummy =str_replace('!!nusersauv!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nusersauv!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);

		if($row->rights & DSI_AUTH)
			$dummy =str_replace('!!nuserdsi!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nuserdsi!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
			
		if($row->rights & ACQUISITION_AUTH)
			$dummy =str_replace('!!nuseracquisition!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nuseracquisition!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
			
		if($row->rights & RESTRICTCIRC_AUTH)
			$dummy =str_replace('!!nuserrestrictcirc!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nuserrestrictcirc!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);

		if($row->rights & THESAURUS_AUTH)
			$dummy =str_replace('!!nuserthesaurus!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nuserthesaurus!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
			
		if($row->rights & TRANSFERTS_AUTH)
			$dummy =str_replace('!!nusertransferts!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nusertransferts!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);

		if($row->rights & EXTENSIONS_AUTH)
			$dummy =str_replace('!!nuserextensions!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nuserextensions!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
		
		if($row->rights & DEMANDES_AUTH)
			$dummy =str_replace('!!nuserdemandes!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else 
			$dummy =str_replace('!!nuserdemandes!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
		if($row->rights & CMS_AUTH)
			$dummy =str_replace('!!nusercms!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);	
		else 
			$dummy =str_replace('!!nusercms!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);			
		if($row->rights & CMS_BUILD_AUTH)
			$dummy =str_replace('!!nusercms_build!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else
			$dummy =str_replace('!!nusercms_build!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
		if($row->rights & FICHES_AUTH)
			$dummy =str_replace('!!nuserfiches!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);	
		else 
			$dummy =str_replace('!!nuserfiches!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);		
		if($row->rights & CATAL_MODIF_CB_EXPL_AUTH)
			$dummy =str_replace('!!nusermodifcbexpl!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);	
		else 
			$dummy =str_replace('!!nusermodifcbexpl!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);

		if($row->rights & SEMANTIC_AUTH)
			$dummy =str_replace('!!nusersemantic!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else
			$dummy =str_replace('!!nusersemantic!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);

		if($row->rights & CONCEPTS_AUTH)
			$dummy =str_replace('!!nuserconcepts!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else
			$dummy =str_replace('!!nuserconcepts!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
		
		if($row->rights & MODELLING_AUTH)
			$dummy =str_replace('!!nusermodelling!!', '<img src="'.get_url_icon('coche.gif').'" class="align_top" hspace=3>', $dummy);
		else
			$dummy =str_replace('!!nusermodelling!!', '<img src="'.get_url_icon('uncoche.gif').'" class="align_top" hspace=3>', $dummy);
		
		$dummy = str_replace('!!lang_flag!!', $flag, $dummy);
		$dummy = str_replace('!!nuserlogin!!', $row->username, $dummy);
		$dummy = str_replace('!!nuserid!!', $row->userid, $dummy);
		
		if($row->user_alert_resamail) {
			$user_alert_row = str_replace("!!user_alert!!", $msg['alert_resa_user_mail'].'<img src="'.get_url_icon('tick.gif').'" class="align_top" hspace=3>', $admin_user_alert_row);
			$dummy =str_replace('!!user_alert_resamail!!', $user_alert_row , $dummy);
		} else {
			$dummy =str_replace('!!user_alert_resamail!!', '', $dummy);
		}
		
		if($row->user_alert_demandesmail) {
			$user_alert_row = str_replace("!!user_alert!!", $msg['alert_demandes_user_mail'].'<img src="'.get_url_icon('tick.gif').'" class="align_top" hspace=3>', $admin_user_alert_row);
			$dummy =str_replace('!!user_alert_demandesmail!!', $user_alert_row , $dummy);
		} else {
			$dummy =str_replace('!!user_alert_demandesmail!!', '', $dummy);
		}

		if($row->user_alert_subscribemail) {
			$user_alert_row = str_replace("!!user_alert!!", $msg['alert_subscribe_user_mail'].'<img src="'.get_url_icon('tick.gif').'" class="align_top" hspace=3>', $admin_user_alert_row);
			$dummy =str_replace('!!user_alert_subscribemail!!', $user_alert_row , $dummy);
		} else {
			$dummy =str_replace('!!user_alert_subscribemail!!', '', $dummy);
		}
		
		if($row->user_alert_suggmail) {
			$user_alert_row = str_replace("!!user_alert!!", $msg['alert_sugg_user_mail'].'<img src="'.get_url_icon('tick.gif').'" class="align_top" hspace=3>', $admin_user_alert_row);
			$dummy =str_replace('!!user_alert_suggmail!!', $user_alert_row, $dummy);
		} else {
			$dummy =str_replace('!!user_alert_suggmail!!', '', $dummy);
		}
		
		if($row->user_alert_serialcircmail) {
			$user_alert_row = str_replace("!!user_alert!!", $msg['alert_subscribe_serialcirc_mail'].'<img src="'.get_url_icon('tick.gif').'" class="align_top" hspace=3>', $admin_user_alert_row);
			$dummy =str_replace('!!user_alert_serialcircmail!!', '' , $dummy);
		} else {
			$dummy =str_replace('!!user_alert_serialcircmail!!', '', $dummy);
		}
		
		$dummy = str_replace('!!user_created_date!!', $msg['user_created_date'].format_date($row->create_dt), $dummy);
		
		print $dummy;
	}
	print "<div class='row'>
		<input class='bouton' type='button' value=' $msg[85] ' onClick=\"document.location='./admin.php?categ=users&sub=users&action=add'\" />
		</div>";

}
	
	
function get_coordonnees_etab($user_id='0', $field_values, $current_field, $form_name) {

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

