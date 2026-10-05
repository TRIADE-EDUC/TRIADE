<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_graph_ui.class.php,v 1.5 2021/11/05 11:05:18 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_list_ui.class.php');
/**
 * Classe d'affichage d'un onglet qui affiche une liste d'article du contenu éditorial
 * @author ngantier
 *
 */
class elements_graph_ui extends elements_list_ui {
	
	protected function generate_elements_list(){
		global $include_path;
		
		$template_path = $include_path.'/templates/entities_graph.tpl.html';
		if(file_exists($include_path.'/templates/entities_graph_subst.tpl.html')){
			$template_path = $include_path.'/templates/entities_graph_subst.tpl.html';
		}
		if(file_exists($template_path)){
			$h2o = H2o_collection::get_instance($template_path);
			// Content -> Structure json à passer au constructeur de la classe dojo permettant de générer le graphe
			if (empty($this->contents['nodes'])) {
			    $this->contents['nodes'] = "[]";
			}
			if (empty($this->contents['links'])) {
			    $this->contents['links'] = "[]";
			}
			$graph = array('nodes'=> $this->contents['nodes'], 'links' => $this->contents['links']);
			return $h2o->render(array('graph' => $graph));
		}
		return '';
	}
	
	/**
	 * dérivation permettant de supprimer l'affichage du paginateur
	 */
	public function get_elements_list_nav(){
		return '';
	}
	
	public function is_expandable() {
		return false;
	}
	
	public function can_display_content() {
	    global $pmb_entity_graph_activate;
	    return $pmb_entity_graph_activate;
	}
}