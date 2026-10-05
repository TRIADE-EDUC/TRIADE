<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_page_concept.class.php,v 1.6 2024/03/22 15:31:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/authorities/page/authority_page.class.php");


/**
 * class skos_page_concept
 * Controler d'une Page OPAC représentant un concept de SKOS
 */
class skos_page_concept extends authority_page {
	
	/**
	 * Instance du concept
	 * @var skos_concept
	 */
	private $concept;
	
	/**
	 * Constructeur d'une page concept
	 * @param int $concept_id Identifiant du concept à représenter
	 * @return void
	 */
	public function __construct($concept_id) {
		$concept_id = intval($concept_id);
		$this->concept = new skos_concept($concept_id);
	}
	
	/**
	 * Affiche les données renvoyées par les vues
	 */
	public function proceed($context = array()){
		global $pmb_url_base, $categ, $sub, $id, $quoi;
		
		$this->authority = new authority(0, $this->concept->get_id(), AUT_TABLE_CONCEPT);
		// On va chercher les onglets de l'autorité
		$this->authority->set_authority_tabs($this->get_authority_tabs());
		// On va chercher la liste d'éléments à afficher
		$authority_list_ui = $this->authority->get_authority_list_ui();
		if ($authority_list_ui) $authority_list_ui->set_current_url($pmb_url_base.'autorites.php?categ='.$categ.'&sub='.$sub.'&id='.$id.'&quoi='.$quoi);
//  	$context['authority']=array(
// 			//affichage des termes génériques...
// 			'broaders' => skos_view_concepts::get_broaders_list($this->concept->get_broaders()),
// 			//affichage de l'intitulé du concept
// 			'title' => skos_view_concept::get_concept($this->concept),
//  			//affichage de l'intitulé du concept non formaté	
//  			'light_title' => skos_view_concept::get_concept_title($this->concept),
// 			//affichage des termes spécifiques...
// 			'narrowers' => skos_view_concepts::get_narrowers_list($this->concept->get_narrowers()),
// 			//toutes les informations du concept
// 			'details' => skos_view_concept::get_detail_concept($this->concept),
// 			//affichage des concepts composé utilisant le concept
// 			'composed_concepts' => skos_view_concepts::get_composed_concepts_list($this->concept->get_composed_concepts()),
// 			//notices indexées
// 			//recordslist' => skos_view_concept::get_notices_indexed_with_concept($this->concept),
// 			//autorités indexées
// 			'authoritieslist' => skos_view_concept::get_authorities_indexed_with_concept($this->concept)
//  	);
		$context['authority'] = $this->authority;
		print $this->authority->render($context);
		
	}
	
	
	public function get_indexed_notices(){
		return $this->concept->get_indexed_notices();
	}
}