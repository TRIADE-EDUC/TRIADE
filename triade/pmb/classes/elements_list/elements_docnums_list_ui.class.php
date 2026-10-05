<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_docnums_list_ui.class.php,v 1.5 2019/02/12 15:10:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_list_ui.class.php');
require_once($include_path.'/h2o/pmb_h2o.inc.php');
require_once($class_path.'/explnum.class.php');
require_once($class_path.'/notice.class.php');
// require_once($class_path.'/serials.class.php'); // Entraine une fatal sur index.php

/**
 * Classe d'affichage d'un onglet qui affiche une liste de documents numériques
 * @author vtouchard
 *
 */
class elements_docnums_list_ui extends elements_list_ui {
	
	protected function generate_element($element_id, $recherche_ajax_mode=0){
		global $include_path;
		
		$docnum = new explnum($element_id);
		if ($docnum->explnum_notice) {
			$record = new notice($docnum->explnum_notice);
		} else {
			$record = new bulletinage($docnum->explnum_bulletin);
		}
		
		$template_path = $include_path.'/templates/explnum_in_list.tpl.html';
		if(file_exists($include_path.'/templates/explnum_in_list_subst.tpl.html')){
			$template_path = $include_path.'/templates/explnum_in_list_subst.tpl.html';
		}
		$context = array('list_element' => $docnum, 'list_element_record' => $record);
		return static::render($template_path, $context, $this->get_context_parameters());
	}
	
	public function is_expandable() {
		return false;
	}

}