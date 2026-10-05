<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_item.class.php,v 1.13 2019/07/31 08:54:13 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/onto/skos/onto_skos_concept_item.tpl.php');
require_once($class_path.'/authorities_statuts.class.php');
require_once($class_path.'/authorities_collection.class.php');
require_once($class_path."/audit.class.php");

class onto_skos_concept_item extends onto_common_item {

	public function get_form($prefix_url="",$flag="",$action="save") {
	    global $msg, $charset, $pmb_type_audit;
	    global $pmb_map_activate;
	    
		$form = parent::get_form($prefix_url,$flag,$action);
		if($flag != "concept_selector_form"){
			$id = $this->get_id();			
			if ($pmb_map_activate) {
			    $map_edition = new map_edition_controler(AUT_TABLE_CONCEPT, $id);
			    $map_form = $map_edition->get_form();
			    $form = str_replace('<!-- map -->', $map_form , $form);
			}			
			$aut_link= new aut_link(AUT_TABLE_CONCEPT,$id);
			$form = str_replace('<!-- aut_link -->', $aut_link->get_form(onto_common_uri::get_name_from_uri($this->get_uri(), $this->onto_class->pmb_name)) , $form);
			$aut_pperso = new aut_pperso("skos", $id);
			$form = str_replace('<!-- aut_pperso -->', $aut_pperso->get_form(), $form);
		}else {
			$form = str_replace('<!-- aut_link -->', "" , $form);
			$form = str_replace('<!-- aut_pperso -->', "", $form);
		}
		if(!onto_common_uri::is_temp_uri($this->uri)){
			$form=str_replace("!!onto_form_replace!!", '<input type="button" class="bouton" onclick="document.location=\''.$prefix_url.'&action=replace\'" value="'.htmlentities($msg['158'],ENT_QUOTES,$charset).'"/>', $form);
			$form=str_replace("!!onto_form_merge!!", '<input type="button" class="bouton" onclick="document.location=\''.$prefix_url.'&action=merge\'" value="'.htmlentities($msg['merge'],ENT_QUOTES,$charset).'"/>', $form);
			$form=str_replace("!!onto_form_duplicate!!", '<input type="button" class="bouton" onclick="document.location=\''.$prefix_url.'&action=duplicate\'" value="'.htmlentities($msg['duplicate'],ENT_QUOTES,$charset).'"/>', $form);
			if ($pmb_type_audit) {
				$form=str_replace("!!onto_form_audit!!", audit::get_dialog_button($this->get_id(), AUDIT_CONCEPT), $form);
			} else {
				$form=str_replace("!!onto_form_audit!!", '', $form);
			}
		}else{
		    $form=str_replace("!!onto_form_replace!!", '', $form);
		    $form=str_replace("!!onto_form_merge!!", '', $form);
			$form=str_replace("!!onto_form_duplicate!!", '', $form);
			$form=str_replace("!!onto_form_audit!!", '', $form);
		}
		$form = str_replace('!!auth_statut_selector!!', authorities_statuts::get_form_for(AUT_TABLE_CONCEPT, $this->get_statut_id()), $form);
		return $form;
	}
	
	public function get_statut_id(){
	    $query_statut = 'select num_statut from authorities where num_object = "'.$this->get_id().'" and type_object='.AUT_TABLE_CONCEPT;
	    $result = pmb_mysql_query($query_statut);
	    $statut = 1;
	    if($result && pmb_mysql_num_rows($result)){
	        $data = pmb_mysql_fetch_object($result);
	        $statut = $data->num_statut;
	    }
	    return $statut;
	}
	
	public function get_replace_form($prefix_url = "") {
		global $ontology_tpl, $charset;
		
		$concept = authorities_collection::get_authority(AUT_TABLE_CONCEPT, $this->get_id());
		/* @var $concept skos_concept */
		$form = $ontology_tpl['form_replace'];
		
		$form = str_replace("!!onto_action!!", $prefix_url.'&action=replace', $form);
		$form = str_replace("!!old_concept_libelle!!", $concept->get_display_label(), $form);
		
		return $form;
	}
	
	public function get_merge_form($prefix_url = "") {
	    global $ontology_tpl;
	    
	    $concept = authorities_collection::get_authority(AUT_TABLE_CONCEPT, $this->get_id());
	    /* @var $concept skos_concept */
	    $form = $ontology_tpl['form_merge'];
	    
	    $form = str_replace("!!onto_action!!", $prefix_url.'&action=merge', $form);
	    $form = str_replace("!!old_concept_libelle!!", $concept->get_display_label(), $form);
	    
	    return $form;
	}
}