<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transferts_popup.php,v 1.15.8.1 2025/03/04 07:32:08 dgoron Exp $

global $id_notice, $id_bulletin, $hook_tansfert_popup_result, $selecteur;

// d?finition du minimum n?c?ssaire
$base_path="../..";
$base_auth = "TRANSFERTS_AUTH";
$base_title = "\$msg[6]";

$base_use_dojo = 1;
$base_transferts_popup = 1;

require_once ($base_path."/includes/init.inc.php");

global $class_path, $action, $msg, $PMBuserid;
global $transferts_ghost_expl_gen_script;

if (!empty($transferts_ghost_expl_gen_script)) {
    require_once ($base_path."/includes/".$transferts_ghost_expl_gen_script);
}
require_once($class_path."/transfert.class.php");
require_once($class_path."/expl.class.php");

if ($action=="enregistre") {
	//on enregistre la demande de transfert
	//on transforme la liste en tableau
	$tab_id = explode(",",$expl_ids);
	/**
	 * Création des exemplaires fantômes
	 */
	if(isset($transfert_type) && $transfert_type == "1"){
		$createdGhostsIds = array();
		$ghost = new exemplaire($expl_virtual_cb, '');
		$ghost->typdoc_id = (int) $from_typdoc;
		$ghost->cote = stripslashes($expl_virtual_cote);
		$ghost->location_id = (int) $from_location;
		$ghost->section_id = (int) $from_section;
		$ghost->codestat_id = (int) $from_codestat;
		$ghost->owner_id = (int) $from_owner;
		$ghost->statut_id = (int) $expl_virtual_status;
		$ghost->expl_comment = stripslashes($expl_virtual_comment);
		if(isset($from_notice)) $ghost->id_notice = (int) $from_notice;
		else $ghost->id_bulletin = (int) $from_bulletin;
		$ghost->ref_num = (int) $from_expl_parent_id; 
		$ghost->save();
		$createdGhostsIds[] = $ghost->expl_id;
		$tab_id = $createdGhostsIds;
	}else if(isset($transfert_all_group)){
		foreach($transfert_all_group as $group_id){
			if(check_group_transferability($group_id)){
				$tab_id = array_merge(get_group_expls($group_id), $tab_id);
			}
		}
	}
	$trans = new transfert();
	$tab_id = array_unique($tab_id);
	//pour chaque exemplaire
	foreach ($tab_id as $id_expl) {
		//on genere les transferts
		$trans->validation_send_event=1;
		$num = $trans->creer_transfert_catalogue($id_expl, $dest_id, $date_retour, stripslashes($motif),$transferts_popup_ask_date);
		if($num){
			$query = 'update transferts set transfert_ask_user_num= "'.$PMBuserid.'" where id_transfert="'.$num.'" ';
			pmb_mysql_query( $query );
		}
	}
	//le script pour fermer la popup
	echo $transferts_popup_enregistre_demande;

} else {
	/**
	 * TODO: traiter les 2 cas:
	 * 	-Pas d'expl fournis
	 * 	-Pas d'expl transférable
	 * 		-> Quel impact sur la popup ? 
	 * 			-> Fermeture directe ?
	 *	 		-> Alert d'error ?
	 */
	$expl = implode(',', check_transferability(explode(',', $expl)));
	

	require_once($class_path.'/event/events/event_resa.class.php');
	$evt = new event_resa('resa', 'resa_tansfert_popup');
	$evt->set_resa($id_notice, $id_bulletin);
	$evth = events_handler::get_instance();
	$evth->send($evt);
	if($evt->get_result()){
		$hook_tansfert_popup_result = $evt->get_result();
	} else {
	    $hook_tansfert_popup_result = '';
	}
	
	/**
	 * Inclusion templates exemplaire fantome si le paramètre est activé
	 */
	$radio_expl_fantome = "";
	$table_expl_fantome = "";
	if($transferts_ghost_expl_enable){
		$radio_expl_fantome .= $transferts_popup_expl_fantome_radio;
		$table_expl_fantome .= $transferts_popup_table_expl_fantomes;
		$tmpStringGhost = str_replace("!!class_ligne!!", "even", $transferts_popup_ligne_tableau_ex_fantome);
		$generatedGhostCb = init_gen_code_exemplaire('', '');
	} else {
	    $tmpStringGhost = '';
	}
	$transferts_popup_global = str_replace('!!expl_fantome_checkbox!!', $radio_expl_fantome, $transferts_popup_global);
	$transferts_popup_global = str_replace("!!table_exemplaire_fantome!!", $table_expl_fantome, $transferts_popup_global);
	$transferts_popup_global = str_replace("<!--!!hook_tansfert_popup_result!!-->", $hook_tansfert_popup_result, $transferts_popup_global);
	
	//le nombre de colonnes dans la requete pour remplacer les champs dans le template
	$expls_groups = array();
	$nb = 0;
	if($expl) {
		$groups_fields = '';
		$groups_tables = '';
		if($pmb_pret_groupement){
			$groups_fields .=" , id_groupexpl, groupexpl_name";
			$groups_tables .=  " LEFT JOIN groupexpl_expl ON groupexpl_expl.groupexpl_expl_num = exemplaires.expl_id ".
					" LEFT JOIN groupexpl ON groupexpl.id_groupexpl = groupexpl_expl.groupexpl_num ";
		}
	
		//on affiche la confirmation de la demande
		$rqt = "SELECT expl_cb, expl_id, expl_notice, expl_bulletin, expl_location, expl_codestat, expl_owner, expl_section,
				expl_cote, expl_typdoc, location_libelle, section_libelle, tdoc_libelle, lender_libelle ".
					$groups_fields." FROM exemplaires".
					" LEFT JOIN docs_location ON exemplaires.expl_location=docs_location.idlocation".
					" LEFT JOIN docs_section ON exemplaires.expl_section=docs_section.idsection ".
					" LEFT JOIN docs_type ON exemplaires.expl_typdoc=docs_type.idtyp_doc  ".
					" LEFT JOIN lenders ON idlender=expl_owner " .
				$groups_tables.
				" WHERE expl_id IN (".$expl.")";
		$res = pmb_mysql_query($rqt);
		if($res && pmb_mysql_num_rows($res)) {
			$nbCols = pmb_mysql_num_fields($res);
			
			while ($values=pmb_mysql_fetch_array($res)) {
				if (!isset($values['id_groupexpl'])) {
					$values['id_groupexpl'] = 0;
				}
				if (!isset($expls_groups[$values['id_groupexpl']*1])) {
					$expls_groups[$values['id_groupexpl']*1] = array();
				}
				$expls_groups[$values['id_groupexpl']*1][] = $values;
			}
		}
	}
	$tmpString = "";
	/**
	 * TODO: Gérer les droits sur les groupes
	 */
	foreach ($expls_groups as $expl_group_id => $values_array) {
		if($pmb_pret_groupement){
			if ($expl_group_id) { //!= 0 donc, un id défini
				$group_checkbox = "";
				if(check_group_transferability($expl_group_id)){
					$group_checkbox = str_replace('!!group_id!!', $expl_group_id, $transfert_popup_groups_checkbox);
					$group_checkbox = str_replace("!!group_expl_libelle!!",$msg['transfert_entire_group_expls']." : ".$values_array[0]['groupexpl_name'],$group_checkbox); 
				}
				$tmpString .= str_replace("!!group_libelle!!", $values_array[0]['groupexpl_name'], $transfert_popup_ligne_groupe_tableau);
				$tmpString = str_replace("!!group_expl_checkbox!!",$group_checkbox	,$tmpString); 
			} else { //Exemplaires ne se trouvant pas dans un groupe 
				$tmpString .= str_replace("!!group_libelle!!", $msg['transfert_expls_not_in_groups'], $transfert_popup_ligne_groupe_tableau);
				$tmpString = str_replace("!!group_expl_checkbox!!", '',$tmpString);
			}	
		}
		foreach ($values_array as $values) {
			if ($nb % 2){
				$tmpLigne = str_replace("!!class_ligne!!", "odd", $transferts_popup_ligne_tableau);
			}else{			
				$tmpLigne = str_replace("!!class_ligne!!", "even", $transferts_popup_ligne_tableau);
			}
	
			//on parcourt toutes les colonnes de la requete
			for($i=0; $i<$nbCols; $i++) {
				//on remplace les données à afficher
				$tmpLigne = str_replace("!!".pmb_mysql_field_name($res,$i)."!!",$values[$i] ?? '',$tmpLigne);
				if($nb == 0 && $transferts_ghost_expl_enable){
				    if ($tmpStringGhost) {
    					$tmpStringGhost = str_replace("!!".pmb_mysql_field_name($res,$i)."!!",$values[$i],$tmpStringGhost);
    					switch(pmb_mysql_field_name($res,$i)){
    						case 'expl_id':
    							$tmpStringGhost = str_replace("!!expl_status!!",do_selector('docs_statut', "expl_virtual_status", $transferts_ghost_statut_expl_transferts), $tmpStringGhost);
    							$tmpStringGhost = str_replace("!!expl_parent_id!!",$values[$i],$tmpStringGhost);
    							break;
    						case 'expl_cb':
    							$tmpStringGhost = str_replace("!!cb_ghost_from!!",$values[$i],$tmpStringGhost);
    							break;
    						case 'expl_notice':
    							$tmpStringGhost = str_replace("!!parent_type!!",'notice',$tmpStringGhost);
    							$tmpStringGhost = str_replace("!!parent_num!!",$values[$i],$tmpStringGhost);
    							break;
    						case 'expl_bulletin':
    							$tmpStringGhost = str_replace("!!parent_type!!",'bulletin',$tmpStringGhost);
    							$tmpStringGhost = str_replace("!!parent_num!!",$values[$i],$tmpStringGhost);
    							break;
    					}
				    }
				}
			}
			//on ajoute la ligne aux autres
			$tmpString .= $tmpLigne;
			//le compteur pour la couleur
			$nb++;
		}
		
	}
	
	//on remplace la liste d'exemplaire dans le template
	$tmpString = str_replace("!!liste_exemplaires!!", $tmpString, $transferts_popup_global);
	if($tmpStringGhost){
		$tmpStringGhost = str_replace("!!new_expl_cb!!",get_ghost_expl_cb($generatedGhostCb), $tmpStringGhost);
		$tmpStringGhost = str_replace("!!expl_status!!", $selecteur, $tmpStringGhost);
		$tmpString = str_replace("!!liste_exemplaires_fantomes!!", $tmpStringGhost, $tmpString);
	}
	
	//la localisation par d?faut de l'utilisateur pour la destination
	$rqt = "SELECT idlocation, location_libelle " .
			"FROM docs_location " .
			"INNER JOIN users ON idlocation=deflt_docs_location " .
			"WHERE userid=".$PMBuserid;
	$res = pmb_mysql_query($rqt);
	$values=pmb_mysql_fetch_array($res);
	$tmpString = str_replace("!!dest_localisation!!", $values[1], $tmpString);
	$tmpString = str_replace("!!loc_id!!", $values[0], $tmpString);
	
	//on y met la date de pret par defaut
	$date_pret = mktime(0, 0, 0, date("m"), date("d")+$transferts_nb_jours_pret_defaut, date("Y"));
	$date_pret_aff = date("Ymd", $date_pret);
	$tmpString = str_replace("!!date_retour_simple!!", $date_pret_aff, $tmpString);
	$date_pret_aff = date("Y-m-d", $date_pret);
	$tmpString = str_replace("!!date_retour_mysql!!", $date_pret_aff, $tmpString);
	$date_pret_aff = date("d/m/Y", $date_pret);
	$tmpString = str_replace("!!date_retour!!", $date_pret_aff, $tmpString);
	
	//on y met les id d'exemplaire
	$tmpString = str_replace("!!expl_ids!!", $expl, $tmpString);
	
	echo $tmpString;
}

echo $footer;

// deconnection MYSql
pmb_mysql_close();

function get_ghost_expl_cb($code_exemplaire,$notice_id=0, $bulletin_id=0){
    $notice_id = intval($notice_id);
    $bulletin_id = intval($bulletin_id);
	//Génération automatique de code barre, activé pour cet abonnement
	$requete="DELETE from exemplaires_temp where sess not in (select SESSID from sessions)";
	pmb_mysql_query($requete);
	
    do{
		$code_exemplaire = gen_code_exemplaire($notice_id, $bulletin_id, $code_exemplaire);
		$requete="select expl_cb from exemplaires WHERE expl_cb='$code_exemplaire'";
	    $res0 = pmb_mysql_query($requete);
	    $requete="select cb from exemplaires_temp WHERE cb='$code_exemplaire' AND sess <>'".SESSid."'";
	    $res1 = pmb_mysql_query($requete);
    } while((pmb_mysql_num_rows($res0)||pmb_mysql_num_rows($res1)));
	      
	//Memorise dans temps le cb et la session pour le cas de multi utilisateur session
	$requete="INSERT INTO exemplaires_temp (cb ,sess) VALUES ('$code_exemplaire','".SESSid."')";
	pmb_mysql_query($requete);
	return $code_exemplaire;
}

function check_transferability($expl_ids){
	foreach($expl_ids as $expl_id){
		if(!transfert::est_transferable($expl_id)){
			if(($key = array_search($expl_id, $expl_ids)) !== false) {
				unset($expl_ids[$key]);
			}
		}
	}
	return $expl_ids;
}

function check_group_transferability($group_id){
	$expl_ids = get_group_expls($group_id);
	$transferable = true;
	if (!empty($expl_ids)) {
    	foreach($expl_ids as $expl_id){
    		if(!transfert::est_transferable($expl_id)){
    			$transferable = false;
    		}
    	}
	}
	return $transferable;
}

function get_group_expls($group_id){
    $group_id = intval($group_id);
	$rqt = "SELECT distinct groupexpl_expl_num from groupexpl_expl where groupexpl_num = ".$group_id;
	$result = pmb_mysql_query($rqt);
	$expl_ids_from_group = array();
	while($res = pmb_mysql_fetch_object($result)){
		$expl_ids_from_group[] = $res ->groupexpl_expl_num;
	}
	return $expl_ids_from_group;
}

?>