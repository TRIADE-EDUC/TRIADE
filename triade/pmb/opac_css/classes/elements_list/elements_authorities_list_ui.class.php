<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_authorities_list_ui.class.php,v 1.10 2019-06-13 15:33:03 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_list_ui.class.php');
require_once($class_path.'/authority.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste d'autorité
 * @author dgoron
 *
 */
class elements_authorities_list_ui extends elements_list_ui {
	
	protected $parent_path = array();
	
	protected function generate_elements_list(){
		$elements_list = '';
		$recherche_ajax_mode = 0;
		$nb = 0;
		if(is_array($this->contents)){
		 	foreach($this->contents as $element_id){
				if (!in_array($element_id, $this->parent_path)) {
					$this->parent_path[] = $element_id; 
					if(!$recherche_ajax_mode && ($nb++>5)){
						$recherche_ajax_mode = 1;
					}
					$elements_list.= $this->generate_element($element_id, $recherche_ajax_mode);
					array_pop($this->parent_path);
				}	
			}
		}
		return $elements_list;
	}
	
	protected function generate_element($element_id, $recherche_ajax_mode=0){
		global $include_path;
		global $opac_authorities_templates_folder;
		
		if($opac_authorities_templates_folder) {
			$template_directory = $opac_authorities_templates_folder;
		} else {
			$template_directory = 'common';
		}
		$authority = $this->get_authority_instance($element_id);
		$this->add_context_parameter('element_id', $element_id);
		$authority->set_context_parameters($this->get_context_parameters());
		$template_path = $include_path.'/templates/authorities/'.$template_directory.'/list/'.$authority->get_string_type_object().'.html';
		if(file_exists($include_path.'/templates/authorities/'.$template_directory.'/list/'.$authority->get_string_type_object().'_subst.html')){
			$template_path = $include_path.'/templates/authorities/'.$template_directory.'/list/'.$authority->get_string_type_object().'_subst.html';
		}
		$context = array('list_element' => $authority);
		return static::render($template_path, $context, $this->get_context_parameters());
	}
	
	protected function get_authority_instance($element_id) {
	    return authorities_collection::get_authority('authority', $element_id);
	}
}