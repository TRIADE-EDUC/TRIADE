<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_view_concept.class.php,v 1.25 2023/08/28 14:04:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_composee.class.php");
require_once($base_path."/cms/modules/common/includes/pmb_h2o.inc.php");
include_once($include_path."/templates/skos/skos_view_concept.tpl.php");
require_once($class_path."/skos/skos_view_concepts.class.php");

/**
 * class skos_view_concept
 * La vue d'un concept
*/
class skos_view_concept {
	
	/**
	 * Retourne l'affichage d'un concept
	 * @param array $datas Données
	 * @param string $template Nom du template à utiliser
	 * @return string
	 */
	static protected function render($datas, $template) {
	    global ${$template}, $base_path;
		
		$template_path = $base_path.'/temp/'.LOCATION.'_'.$template;
		if(!file_exists($template_path) || (md5(${$template}) != md5_file($template_path))) {
		    file_put_contents($template_path, ${$template});
		}
		$h2o = H2o_collection::get_instance($template_path);
		return $h2o->render(array("concept"=>$datas));
	}
	
	/**
	 * Retourne la génération d'un concept avec un lien vers chaque élément de sa composition s'il s'agit d'un concept composé
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_concept_in_list_with_all_links($concept) {
		if ($vedette = $concept->get_vedette()) {
			$vedette_elements = $vedette->get_elements();
			$datas['separator'] = $vedette->get_separator();
			$datas['elements'] = array();
			foreach ($vedette_elements as $elements) {
				foreach ($elements as $element) {
					$datas['elements'][] = array(
							'label' => $element->get_isbd(),
							'link' => str_replace("!!id!!", $element->get_db_id(), $element->get_lien_opac())
					);
				}
			}
			return self::render($datas, "skos_view_concept_concept_in_list_with_all_links");
		} else {
			// Sinon c'est un concept classique
			return self::get_concept_in_list($concept);
		}
	}
	
	/**
	 * Retourne la génération d'un concept classique
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_concept_in_list($concept) {
		global $liens_opac;
		
		$datas = array(
				'label' => $concept->get_display_label(),
				'link' => str_replace("!!id!!", $concept->get_id(), $liens_opac['lien_rech_concept'])
		);
		return self::render($datas, "skos_view_concept_concept_in_list");
	}

	/**
	 * Met en forme le libellé d'un concept
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_concept($concept) {
		$datas = array(
			'label' => $concept->get_display_label()
		);
		return self::render($datas, "skos_view_concept_concept");
	}
	
	/**
	 * Gère l'affichage de la grammaire si concept composé
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_detail_concept($concept) {
		$display_datas = array();
		
		$datas = $concept->get_details();
		$formatted_datas = array();
		foreach ($datas as $property => $values){
			$formatted_datas[$property]['values'] = $values; 
			$formatted_datas[$property]['label'] = skos_onto::get_property_label("http://www.w3.org/2004/02/skos/core#Concept", $property);
		}
		$display_datas['properties'] = $formatted_datas;
		
		if ($vedette = $concept->get_vedette()) {
			$vedette_elements = $concept->get_vedette()->get_elements();
			$datas['composed_concept_separator'] = $vedette->get_separator();
			$display_datas['composed_concept_elements'] = array();			
			$subdivisions_header = $concept->get_vedette()->get_subdivisions();
			foreach ($subdivisions_header as $subdivision_header) {
			    foreach ($vedette_elements as $subdivision => $elements) {
			        if($subdivision_header['code'] == $subdivision) {
			            foreach ($elements as $element) {
			                if ($element->get_db_id()) {
    			                $display_datas['composed_concept_elements'][$vedette->get_subdivision_name_by_code($subdivision)][] = array(
    			                    'label' => $element->get_isbd(),
    			                    'link' => str_replace("!!id!!", $element->get_db_id(), $element->get_lien_opac())
    			                );
			                }
			            }
			        }
			    }
			}
		}
		return self::render($display_datas, "skos_view_concept_detail_concept");
	}	
	
	/**
	 * Retourne l'affichage de la liste des autorités indexées avec le concept
	 * @param skos_concept $concept
	 * @return string
	 */
	static public function get_authorities_indexed_with_concept($concept) {
		global $msg, $liens_opac, $charset;
		
		$indexed_authorities = $concept->get_indexed_authorities();
		$datas = [];
		foreach ($indexed_authorities as $type => $authorities) {
			foreach ($authorities as $authority) {
				switch ($type) {
					case 'author' :
						if (!isset($datas['authorities']['author'])) {
							$datas['authorities']['author'] = array('type_name' => $msg['isbd_author'], 'elements' => array());
						}
						$datas['authorities']['author']['elements'][] = array(
								'label' => $authority->get_isbd(),
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_auteur'])
						);
						break;
					case 'category':
						if (!isset($datas['authorities']['category'])) {
							$datas['authorities']['category'] = array('type_name' => $msg['isbd_categories'], 'elements' => array());
						}
						$datas['authorities']['category']['elements'][] = array(
								'label' => $authority->libelle,
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_categ'])
						);
						break;
					case 'publisher' :
						if (!isset($datas['authorities']['publisher'])) {
							$datas['authorities']['publisher'] = array('type_name' => $msg['isbd_editeur'], 'elements' => array());
						}
						$datas['authorities']['publisher']['elements'][] = array(
								'label' => $authority->display,
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_editeur'])
						);
						break;
					case 'collection' :
						if (!isset($datas['authorities']['collection'])) {
							$datas['authorities']['collection'] = array('type_name' => $msg['isbd_collection'], 'elements' => array());
						}
						$datas['authorities']['collection']['elements'][] = array(
								'label' => $authority->get_isbd(),
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_collection'])
						);
						break;
					case 'subcollection' :
						if (!isset($datas['authorities']['subcollection'])) {
							$datas['authorities']['subcollection'] = array('type_name' => $msg['isbd_subcollection'], 'elements' => array());
						}
						$datas['authorities']['subcollection']['elements'][] = array(
								'label' => $authority->get_isbd(),
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_subcollection'])
						);
						break;
					case 'serie' :
						if (!isset($datas['authorities']['serie'])) {
							$datas['authorities']['serie'] = array('type_name' => $msg['isbd_serie'], 'elements' => array());
						}
						$datas['authorities']['serie']['elements'][] = array(
								'label' => $authority->get_isbd(),
								'link' => str_replace("!!id!!", $authority->s_id, $liens_opac['lien_rech_serie'])
						);
						break;
					case 'titre_uniforme' :
						if (!isset($datas['authorities']['titre_uniforme'])) {
							$datas['authorities']['titre_uniforme'] = array('type_name' => $msg['isbd_titre_uniforme'], 'elements' => array());
						}
						$datas['authorities']['titre_uniforme']['elements'][] = array(
								'label' => $authority->get_isbd(),
								'link' => str_replace("!!id!!", $authority->id, $liens_opac['lien_rech_titre_uniforme'])
						);
						break;
					case 'indexint' :
						if (!isset($datas['authorities']['indexint'])) {
							$datas['authorities']['indexint'] = array('type_name' => $msg['isbd_indexint'], 'elements' => array());
						}
						$datas['authorities']['indexint']['elements'][] = array(
								'label' => $authority->get_isbd(),
								'link' => str_replace("!!id!!", $authority->indexint_id, $liens_opac['lien_rech_indexint'])
						);
						break;
					case 'expl' :
						break;
					case 'explnum' :
						break;
					case 'authperso' :
					    $authority_name = ($charset != 'utf-8' ? encoding_normalize::utf8_decode($authority->get_name()) : $authority->get_name());
						if (!isset($datas['authorities'][$authority_name])) {
							$datas['authorities'][$authority_name] = array('type_name' => $authority_name, 'elements' => array());
						}
						$datas['authorities'][$authority_name]['elements'][] = array(
								'label' => $authority->get_isbd(),
								'link' => str_replace("!!id!!", $authority->get_id(), $liens_opac['lien_rech_authperso'])
						);
						break;
				}
			}
		}
		return self::render($datas, "skos_view_concept_authorities_indexed_with_concept");
	}
}