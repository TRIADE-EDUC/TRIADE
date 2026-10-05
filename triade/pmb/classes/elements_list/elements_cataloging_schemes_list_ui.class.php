<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_cataloging_schemes_list_ui.class.php,v 1.3 2018/10/18 09:08:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_list_ui.class.php');
require_once($class_path.'/frbr/cataloging/frbr_cataloging_datastore.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste de schéma de catalogage
 * @author ngantier
 *
 */
class elements_cataloging_schemes_list_ui extends elements_list_ui {
	
	protected function generate_elements_list(){
		$elements_list = '';
		foreach($this->contents as $id){			
			$elements_list.= $this->generate_cataloging_scheme($id);
		}
		return $elements_list;
	}
	
	private function generate_cataloging_scheme($id){
		global $include_path;
		
		$frbr_cataloging_scheme = new frbr_cataloging_scheme($id);
		$template_path = $include_path.'/templates/frbr/cataloging/frbr_cataloging_schemes_list.html';
		if(file_exists($include_path.'/templates/frbr/cataloging/frbr_cataloging_schemes_list_subst.html')){
			$template_path = $include_path.'/templates/frbr/cataloging/frbr_cataloging_schemes_list_subst.html';
		}
		if(file_exists($template_path)){
			$h2o = H2o_collection::get_instance($template_path);
			$context = array(
					'scheme' => $frbr_cataloging_scheme,
					'linked_elements' => $frbr_cataloging_scheme->get_linked_elements() 
			);
			return $h2o->render($context);
		}
		return '';
	}
}