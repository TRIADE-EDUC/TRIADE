<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_authorities_selectors_list_ui.class.php,v 1.3 2019-02-12 15:10:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_authorities_list_ui.class.php');
require_once($class_path.'/authority.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste d'autorité dans un sélecteur
 * @author dgoron
 *
 */
class elements_authorities_selectors_list_ui extends elements_authorities_list_ui {
	
	protected function generate_element($element_id, $recherche_ajax_mode=0){
		global $include_path;
		$this->add_context_parameter('in_selector', true);
		$authority = new authority($element_id);
		$authority->add_context_parameter('in_selector', true);
		$authority->add_context_parameter('in_search', false);
		$template_path = $include_path.'/templates/authorities/list/'.$authority->get_string_type_object().'.html';
		if(file_exists($include_path.'/templates/authorities/list/'.$authority->get_string_type_object().'_subst.html')){
			$template_path = $include_path.'/templates/authorities/list/'.$authority->get_string_type_object().'_subst.html';
		}
		$context = array('list_element' => $authority);
		return static::render($template_path, $context, $this->get_context_parameters());
	}
}