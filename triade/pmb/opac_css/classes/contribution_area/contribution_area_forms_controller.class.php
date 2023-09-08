<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_forms_controller.class.php,v 1.17 2019-05-24 14:18:19 tsamson Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
	die ( "no access" );

require_once ($class_path . '/contribution_area/contribution_area.class.php');
require_once ($class_path . '/contribution_area/contribution_area_store.class.php');
require_once ($class_path . '/encoding_normalize.class.php');
require_once ($class_path . '/onto/onto_store_arc2_extended.class.php');
require_once ($class_path . '/onto/common/onto_common_uri.class.php');
require_once ($class_path . '/emprunteur.class.php');

/**
 * class contribution_area_forms_controller
 */
class contribution_area_forms_controller {
	public static $identifier = 0;
	public static $datastore;
	public static $ontology;
	
	public static function get_datastore() {
		if (! isset ( self::$datastore )) {
			$store_config = array(
					/* db */
					'db_name' => DATA_BASE,
					'db_user' => USER_NAME,
					'db_pwd' => USER_PASS,
					'db_host' => SQL_SERVER,
					/* store */
					'store_name' => 'contribution_area_datastore',
					/* stop after 100 errors */
					'max_errors' => 100,
					'store_strip_mb_comp_str' => 0 
			);
			$tab_namespaces = array (
					"dc" => "http://purl.org/dc/elements/1.1",
					"dct" => "http://purl.org/dc/terms/",
					"owl" => "http://www.w3.org/2002/07/owl#",
					"rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
					"rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
					"xsd" => "http://www.w3.org/2001/XMLSchema#",
					"pmb" => "http://www.pmbservices.fr/ontology#",
					"ca" => "http://www.pmbservices.fr/ca/" 
			);
			
			self::$datastore = new onto_store_arc2_extended ( $store_config );
			self::$datastore->set_namespaces ( $tab_namespaces );
		}
		return self::$datastore;
	}
	
	public static function get_ontology() {
		if(!isset(self::$ontology)){
			$contribution_store = new contribution_area_store();
			self::$ontology = $contribution_store->get_ontology();
		}
		return self::$ontology;
	}

	public static function search_in_store() {
		global $start, $datas;
		global $completion, $param2;
		$range = self::get_range_from_completion ( $completion );
		
		$query = "SELECT ?uri ?prop ?obj WHERE {";
		if ($param2) {
			$query .= "?uri pmb:area " . $param2 . " .";
		}
		$query .= "?uri rdf:type '" . $range . "' .
				?uri pmb:displayLabel ?label .";
		if (addslashes ( $datas ) != '*') {
			$query .= "filter regex(?label, '^" . addslashes ( $datas ) . "','i') .";
		}
		$query .= "?uri ?prop ?obj
		}
		ORDER BY ?label";
		
		$result = array ();
		if (self::get_datastore ()->query ( $query )) {
			$row = self::get_datastore ()->get_result ();
			for($i = 0; $i < count ( $row ); $i ++) {
				if (! isset ( $result [$row [$i]->uri] )) {
					$result [$row [$i]->uri] = array ();
				}
				$result [$row [$i]->uri] [$row [$i]->prop] = $row [$i]->obj;
			}
			return $result;
		}
	}
	
	public static function get_range_from_completion($completion) {
		switch ($completion) {
			case 'notice' :
				return 'http://www.pmbservices.fr/ontology#record';
			case 'authors' :
				return 'http://www.pmbservices.fr/ontology#author'; // TODO : A revoir pour le traitement ici
				return 'http://www.pmbservices.fr/ontology#responsability';
			case 'categories' :
				return 'http://www.pmbservices.fr/ontology#category';
			case 'publishers' :
				return 'http://www.pmbservices.fr/ontology#publisher';
			case 'collections' :
				return 'http://www.pmbservices.fr/ontology#collection';
			case 'subcollections' :
				return 'http://www.pmbservices.fr/ontology#sub_collection';
			case 'serie' :
				return 'http://www.pmbservices.fr/ontology#serie';
			case 'titres_uniformes' :
				return 'http://www.pmbservices.fr/ontology#work';
			case 'indexint' :
				return 'http://www.pmbservices.fr/ontology#indexint';
			case 'concepts' :
				return 'http://www.w3.org/2004/02/skos/core#Concept';
			default :
				return '';
		}
	}
	
	public static function show_result() {
		global $handleAs;
		$results = self::search_in_store ();
		$returns = array();
		if (isset($handleAs) && $handleAs == "json") {
			foreach ( $results as $uri => $result ) {	
				//datas : valeur utilisée pour la recherche et l'affichage
				//id : valeur cachée qui sera posté dans le champ 'display_label'
				//value : utilisée pour remplir le champ caché 'value'  			
				$returns[] = array("id" => $result['http://www.pmbservices.fr/ontology#displayLabel'], "datas" => $result['http://www.pmbservices.fr/ontology#displayLabel'], "value" => $uri);
			}
		}
		return $returns;
	}
	
	public static function get_empr_forms($id_empr, $validated_forms = false, $last_id = 0) {
		global $charset;

		$id_empr+= 0;
		if (!$id_empr) {
			return array();
		}		
		
		$query = "SELECT * WHERE {
					?s <http://www.pmbservices.fr/ontology#has_contributor> '" . $id_empr . "' .
					?s ?p ?o .
					?s <http://www.pmbservices.fr/ontology#last_edit> ?last_edit 
				} 
				ORDER BY DESC (?last_edit)";
		
		$results = array ();
		//Parse initial des résultats de la requete sparql
		if (self::get_datastore ()->query ( $query )) {
			$rows = self::get_datastore ()->get_result ();
			foreach ( $rows as $row ) {
				if (! isset ( $results [$row->s] )) {
					$results [$row->s] = array ();
				}
				$results [$row->s] [explode('#', $row->p)[1]] = htmlentities($row->o,ENT_QUOTES,$charset);
				
				if (empty($results[$row->s]["uri_id"])) {
				    $uri_id = onto_common_uri::get_id($row->s);
				    if (empty($uri_id)) {
				        $uri_id = onto_common_uri::set_new_uri($row->s);
				    }
				    $results[$row->s]["uri_id"] = $uri_id;
				}
			}
		}
		
		return self::edit_results_to_template($results, $validated_forms, $last_id);
	}
	
	public static function get_moderation_forms($id_empr) {
		global $charset;
		
		$id_empr+= 0;
		if (!$id_empr) {
			return array();
		}
		$ids_empr = array();
		//gestion des droits
		global $gestion_acces_active, $gestion_acces_contribution_moderator_empr;
		if (($gestion_acces_active == 1) && ($gestion_acces_contribution_moderator_empr == 1)) {
			$ac = new acces();
			$dom_6 = $ac->setDomain(6);
			$query = $dom_6->getResourceList($id_empr, 4);
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_array($result)) {
				if ($row[0] != $id_empr) {
					$ids_empr[] = $row[0];
				}
			}			
		}
				
		$query = "SELECT * WHERE {
					?s <http://www.pmbservices.fr/ontology#has_contributor> ?contributor .
					?s ?p ?o .
					?s <http://www.pmbservices.fr/ontology#last_edit> ?last_edit
				}
				ORDER BY ?contributor DESC (?last_edit)";		
		
		$results = array ();
		//Parse initial des résultats de la requete sparql
		if (self::get_datastore()->query($query)) {
			$rows = self::get_datastore()->get_result();
			foreach ($rows as $row) {
				if (in_array($row->contributor, $ids_empr)) {
					if (!isset($results[$row->s])) {
						$results[$row->s] = array ();
					}
					$results[$row->s][explode('#', $row->p)[1]] = htmlentities($row->o,ENT_QUOTES,$charset);
					
					if (empty($results[$row->s]["uri_id"])) {
					    $uri_id = onto_common_uri::get_id($row->s);
					    if (empty($uri_id)) {
					        $uri_id = onto_common_uri::set_new_uri($row->s);
					    }
					    $results[$row->s]["uri_id"] = $uri_id;
					}
					
					if (!isset($results[$row->s]["contributor"])) {
						$results[$row->s]["contributor"] = $row->contributor;
						
						//droit de modification sur ce contributeur
						if (!isset($results[$row->s]["can_edit"])) {
							$results[$row->s]["can_edit"] = $dom_6->getRights($_SESSION['id_empr_session'],$row->contributor, 8);
						}

						//droit de validation sur ce contributeur
						if (!isset($results[$row->s]["can_push"])) {
							$results[$row->s]["can_push"] = $dom_6->getRights($_SESSION['id_empr_session'],$row->contributor, 16);
						} 
					}
				}
			}
		}
		return self::edit_results_to_template($results, false, 0);
	}
	
	public static function get_link_from_type($type, $id, $bulletin = false) {
		switch ($type) {
			case 'http://www.pmbservices.fr/ontology#record' :
				if ($bulletin){
					$query = "SELECT bulletin_id FROM bulletins WHERE num_notice = '".$id."'";
					$result = pmb_mysql_query($query);
					if (pmb_mysql_num_rows($result)) {
						$bulletin = pmb_mysql_fetch_object($result);
						return './index.php?lvl=bulletin_display&id='.$bulletin->bulletin_id;
					}					
				}
				return './index.php?lvl=notice_display&id='.$id;
			case 'http://www.pmbservices.fr/ontology#author' :
				return './index.php?lvl=author_see&id='.$id;
			case 'http://www.pmbservices.fr/ontology#category' :
				return './index.php?lvl=categ_see&id='.$id;
			case 'http://www.pmbservices.fr/ontology#collection' :
				return './index.php?lvl=coll_see&id='.$id;
			case 'http://www.w3.org/2004/02/skos/core#Concept' :
				return './index.php?lvl=concept_see&id='.$id;
			case 'http://www.pmbservices.fr/ontology#docnum' :
				$query = 'SELECT explnum_notice, explnum_bulletin FROM explnum WHERE explnum_id = "'.$id.'"';
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					$row = pmb_mysql_fetch_object($result);
					if ($row->explnum_notice) {
						return './index.php?lvl=notice_display&id='.$row->explnum_notice;
					} else {
						return './index.php?lvl=bulletin_display&id='.$row->explnum_bulletin;
					}					
				}
				return '#';
			case 'http://www.pmbservices.fr/ontology#indexint' :
				return './index.php?lvl=indexint_see&id='.$id;
			case 'http://www.pmbservices.fr/ontology#publisher' :
				return './index.php?lvl=publisher_see&id='.$id;
			case 'http://www.pmbservices.fr/ontology#serie' :
				return './index.php?lvl=serie_see&id='.$id;
			case 'http://www.pmbservices.fr/ontology#sub_collection' :
				return './index.php?lvl=subcoll_see&id='.$id;
			case 'http://www.pmbservices.fr/ontology#work' :
				return './index.php?lvl=titre_uniforme_see&id='.$id;
			case 'http://www.pmbservices.fr/ontology#expl' :
				$query = 'SELECT expl_notice, expl_bulletin FROM exemplaires WHERE expl_id = "'.$id.'"';
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					$row = pmb_mysql_fetch_assoc($result);
					if ($row['expl_notice']) {
						return './index.php?lvl=notice_display&id='.$row['expl_notice'];
					} else {
						return './index.php?lvl=bulletin_display&id='.$row['expl_bulletin'];
					}
				}
				return '#';
			default :
				return '#';
		}
	}
	
	public static function get_area_infos($area_id) {
		$area_infos = array();
		$area_id += 0;
		if ($area_id) {
			$area = new contribution_area($area_id);
			$area_infos['id'] = $area->get_id();
			$area_infos['name'] = $area->get_title();
			$area_infos['color'] = $area->get_color();
		}
		return $area_infos;
	}
	
	public static function get_contributor_infos($contributor_id) {
		$contributor_infos = array();
		$contributor_id += 0;
		if ($contributor_id) {
			$contributor = new emprunteur($contributor_id);
			$contributor_infos['id'] = $contributor->id;
			$contributor_infos['name'] = $contributor->nom.' '.$contributor->prenom;
		}
		return $contributor_infos;
	}
	
	public static function edit_results_to_template($results,$validated_forms = false, $last_id = 0) {
		global $msg, $charset, $pmb_contribution_opac_show_sub_form;
		//gestion des droits
		global $gestion_acces_active, $gestion_acces_empr_contribution_scenario, $gestion_acces_empr_contribution_area;
		if ($gestion_acces_active == 1) {
			$ac = new acces();
			if ($gestion_acces_empr_contribution_area == 1) {
				$dom_4 = $ac->setDomain(4);
			}
			if ($gestion_acces_empr_contribution_scenario == 1) {
				$dom_5 = $ac->setDomain(5);
			}
		}
		
		$returned_result = array ();
		//Composition d'un résultat manipulable dans les templates
		$onto = self::get_ontology();
		foreach ($results as $form_uri => $properties_array) {
			
			//droit sur l'espace
			if ($properties_array['area'] && isset($dom_4)) {
				if (!$dom_4->getRights($_SESSION['id_empr_session'],$properties_array['area'], 4)) {
					continue;
				}
			}
				
			if (!$validated_forms && !empty($properties_array["identifier"])) {
				continue;
			} else if ($validated_forms && !isset($properties_array["identifier"])) {
				continue;
			}
				
			// afficher ou pas les sous-contributions
			if (!empty($properties_array['sub_form']) && !$pmb_contribution_opac_show_sub_form) {
				continue;
			}
		
			if (!isset($returned_result[$onto->get_class_label($properties_array['type'])])) {
				$returned_result [$onto->get_class_label($properties_array['type'])] = array ();
			}
				
			if($properties_array['last_edit']){
				$properties_array['last_edit'] = date($msg['date_format'].' H:i', $properties_array['last_edit']);
			}
			//infos de l'espace
			if ($properties_array['area']) {
				$properties_array['area'] = self::get_area_infos($properties_array['area']);
			}
			//id de l'entité en base SQL
			if (!empty($properties_array['identifier'])) {
				if (isset($properties_array['bibliographical_lvl']) && $properties_array['bibliographical_lvl'] == 'b') {
					$properties_array['link'] = self::get_link_from_type($properties_array['type'], $properties_array['identifier'], true);
				} else {
					$properties_array['link'] = self::get_link_from_type($properties_array['type'], $properties_array['identifier']);
				}
			}
				
			//Droits d'accés
			if (!isset($properties_array['can_edit'])) {
				//on n'autorise pas défaut
				$properties_array['can_edit'] = 1;
			}
			if (!isset($properties_array['can_push'])) {
				//on n'autorise pas défaut
				$properties_array['can_push'] = 1;
			}
			if(isset($dom_5) && $properties_array['parent_scenario_uri']){
				$scenario_uri = 'http://www.pmbservices.fr/ca/Scenario#'.$properties_array['parent_scenario_uri'];
				// Si on n'a déjà plus les droits d'édition, ça ne sert à rien de tester plus
				if ($properties_array['can_edit']) {
					$properties_array['can_edit'] = $dom_5->getRights($_SESSION['id_empr_session'],onto_common_uri::get_id($scenario_uri), 8);
				}
				// Si on n'a déjà plus les droits de validation, ça ne sert à rien de tester plus
				if ($properties_array['can_push']) {
					$properties_array['can_push'] = $dom_5->getRights($_SESSION['id_empr_session'],onto_common_uri::get_id($scenario_uri), 16);
				}
			}			
			//infos du contributeur
			if (!empty($properties_array['contributor'])) {
				$properties_array['contributor'] = self::get_contributor_infos($properties_array['contributor']);
			}
				
			$returned_result[$onto->get_class_label($properties_array ['type'])][$form_uri] = $properties_array;
			if ($last_id && ($last_id == $properties_array['uri_id'])) {
				$returned_result['last_contribution'][$form_uri] = $properties_array;
			}
		}
		return $returned_result;
	}
} // end of contribution_area_forms_controller