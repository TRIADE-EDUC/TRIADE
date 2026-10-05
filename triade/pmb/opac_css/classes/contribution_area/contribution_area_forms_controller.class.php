<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_forms_controller.class.php,v 1.68 2024/03/22 15:31:05 qvarin Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
	die ( "no access" );

global $class_path;
require_once ($class_path . '/contribution_area/contribution_area_form.class.php');
require_once ($class_path . '/contribution_area/contribution_area.class.php');
require_once ($class_path . '/contribution_area/contribution_area_store.class.php');
require_once ($class_path . '/encoding_normalize.class.php');
require_once ($class_path . '/onto/onto_store_arc2_extended.class.php');
require_once ($class_path . '/onto/common/onto_common_uri.class.php');
require_once ($class_path . '/emprunteur.class.php');
require_once ($class_path . '/rdf_entities_conversion/rdf_entities_converter.class.php');
require_once ($class_path . '/explnum.class.php');

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
		global $id_empr, $from_contrib;
		
		$result = array ();
		if (empty($from_contrib) || empty($id_empr)) {
		    return $result;
		}
		
		$range = self::get_rdf_type_from_type ( $completion );
		
		$query = "SELECT ?uri ?prop ?obj WHERE {";
		if ($param2 && !$from_contrib) {
			$query .= "?uri pmb:area " . $param2 . " .";
		}
		$query .= "?uri rdf:type <" . $range . "> .
				?uri pmb:displayLabel ?label .";
		if (addslashes(substr($datas, 0, 1)) != '*') {
			$query .= "filter regex(?label, '" . addslashes($datas) . "','i') .";
		} elseif (addslashes(substr($datas, 0, 1)) == '*') {		    
		    $query .= "filter regex(?label, '" . addslashes(substr($datas, 1)) . "','i') .";
		}
		$query .= "?uri pmb:has_contributor '$id_empr' ."; 
		$query .= "?uri ?prop ?obj
		}
		ORDER BY ?label";
		
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
	
	public static function get_rdf_type_from_type($type, $authperso_num = 0) {
	    switch ($type) {
		    case 'record' :
		    case 'records' :
		    case 'notices' :
		    case 'notice' :
				return 'http://www.pmbservices.fr/ontology#record';
			case 'authors' :
			case 'author' :
			case 'auteur' :
			case 'auteurs' :
				return 'http://www.pmbservices.fr/ontology#author'; // TODO : A revoir pour le traitement ici
				return 'http://www.pmbservices.fr/ontology#responsability';
			case 'categories' :
			case 'categorie' :
			case 'category' :
				return 'http://www.pmbservices.fr/ontology#category';
			case 'editeur' :
			case 'publisher' :
			case 'publishers' :
				return 'http://www.pmbservices.fr/ontology#publisher';
			case 'collections' :
			case 'collection' :
				return 'http://www.pmbservices.fr/ontology#collection';
			case 'subcollections' :
			case 'subcollection' :
				return 'http://www.pmbservices.fr/ontology#sub_collection';
			case 'serie' :
			case 'series' :
				return 'http://www.pmbservices.fr/ontology#serie';
			case 'titres_uniformes' :
			case 'titre_uniforme':
			case 'work':
			case 'works':
				return 'http://www.pmbservices.fr/ontology#work';
			case 'indexint' :
				return 'http://www.pmbservices.fr/ontology#indexint';
			case 'concepts' :
			case 'concept' :
			case 'skos_concepts' :
				return 'http://www.w3.org/2004/02/skos/core#Concept';
			case 'authpersos' :
			case 'authperso' :
			    if ($authperso_num > 0){
			        return 'http://www.pmbservices.fr/ontology#authperso_'.$authperso_num;
			    }
			case 'authority' :
			case 'authorities' :
				return 'http://www.pmbservices.fr/ontology#authority';
			case 'bulletin' :
			case 'bulletins' :
			    return 'http://www.pmbservices.fr/ontology#bulletin';
			case 'perio' :
			case 'serial' :
			case 'serials' :
			    return 'http://www.pmbservices.fr/ontology#serial';
			default :
				return 'http://www.pmbservices.fr/ontology#'.$type;
		}
	}
	
	public static function show_result() {
	    $results = self::search_in_store();
	    
		$returns = array();
		if(empty($results)) return $returns;
		
		foreach ( $results as $uri => $result ) {	
		    
			//datas : valeur utilisée pour la recherche et l'affichage
			//id : valeur cachée qui sera posté dans le champ 'display_label'
		    //value : utilisée pour remplir le champ caché 'value'
		    if (!empty($result['http://www.pmbservices.fr/ontology#identifier'])) continue;
		    if (!empty($result['http://www.pmbservices.fr/ontology#is_draft'])) continue;
		    
		    switch ($result['http://www.w3.org/1999/02/22-rdf-syntax-ns#type']) {
		        case 'http://www.pmbservices.fr/ontology#author' :
		            $label = $result['http://www.pmbservices.fr/ontology#displayLabel'];
		            if(!empty($result['http://www.pmbservices.fr/ontology#author_first_name'])) {
		                $label .= ', ' . $result['http://www.pmbservices.fr/ontology#author_first_name'];
		            }
		            break;
		        case 'http://www.pmbservices.fr/ontology#publisher' :
		            $label = $result['http://www.pmbservices.fr/ontology#displayLabel'];
		            if (!empty($result["http://www.pmbservices.fr/ontology#town"])) {
		                $label .= ' (' . $result["http://www.pmbservices.fr/ontology#town"].')';
		            }
		            break;
		        default:
		            $label = $result['http://www.pmbservices.fr/ontology#displayLabel'];
		            break;
		    }
		    
			$returns[] = array("label" => $label, "id" => $result['http://www.pmbservices.fr/ontology#displayLabel'], "datas" => $result['http://www.pmbservices.fr/ontology#displayLabel'], "value" => $uri);
		}
		return $returns;
	}
	
	public static function get_empr_forms($id_empr, $validated_forms = false, $last_id = 0, $draft_forms = false) {
		$id_empr = intval($id_empr);
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
				//$results [$row->s] [explode('#', $row->p)[1]] = htmlentities($row->o,ENT_QUOTES,$charset);
				$results[$row->s][explode('#', $row->p)[1]] = $row->o;
				if (empty($results[$row->s]["uri_id"])) {
				    $uri_id = onto_common_uri::get_id($row->s);
				    if (empty($uri_id)) {
				        $uri_id = onto_common_uri::set_new_uri($row->s);
				    }
				    $results[$row->s]["uri_id"] = $uri_id;
				}
			}
		}
		
		return self::edit_results_to_template($results, $validated_forms, $last_id, $draft_forms);
	}
	
	public static function get_moderation_forms($id_empr) {
	    $id_empr = intval($id_empr);
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
					
// 					$results[$row->s][explode('#', $row->p)[1]] = htmlentities($row->o,ENT_QUOTES,$charset);
					$results[$row->s][explode('#', $row->p)[1]] = $row->o;
					
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
			case 'http://www.pmbservices.fr/ontology#bulletin' :
				return './index.php?lvl=bulletin_display&id='.$id;
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
			    if (strpos($type, 'authperso') !== false) {
			        return "./index.php?lvl=authperso_see&id=$id";
			    }
				return '#';
		}
	}
	
	public static function get_area_infos($area_id) {
		$area_infos = array();
		$area_id = intval($area_id);
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
		$contributor_id = intval($contributor_id);
		if ($contributor_id) {
			$contributor = new emprunteur($contributor_id);
			$contributor_infos['id'] = $contributor->id;
			$contributor_infos['name'] = $contributor->nom.' '.$contributor->prenom;
		}
		return $contributor_infos;
	}
	
	public static function edit_results_to_template($results,$validated_forms = false, $last_id = 0, $draft_forms = false) {
		global $msg, $pmb_contribution_opac_show_sub_form;
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
		    
		    if (strpos($form_uri, 'authperso') !== false && !$properties_array["displayLabel"]) {
		        $properties_array["displayLabel"] = authperso::get_isbd($properties_array["identifier"]);
		    }
			
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
			if(isset($dom_5) && !empty($properties_array['parent_scenario_uri'])){
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
			
			if (empty($properties_array['is_draft'])) {
			    $properties_array['is_draft'] = false;
			}
			
			$properties_array['icon'] = self::get_icon_src($properties_array);
			
			if (!empty($properties_array["thumbnail"]) && $properties_array['sub'] != 'docnum') {
			    $properties_array['thumbnail'] = '';
			}
			
			if (!empty($properties_array['sub']) && $properties_array['sub'] == 'docnum'){
	            $properties_array['thumbnail_name'] = '';
	            
	            if (!empty($properties_array["thumbnail"])) {
		            $thumbnail_data = json_decode($properties_array["thumbnail"]);
	            } else {
		            $thumbnail_data = '';
	            }
	            
	            if (is_object($thumbnail_data) && ($thumbnail_data->path && $thumbnail_data->id_upload_directory)){
	                //Cas de la vignette de thumbnail (nouvelle version)
	                $upload_folder = new upload_folder($thumbnail_data->id_upload_directory);
	                $filename = $upload_folder->repertoire_path.$thumbnail_data->path;
	                $properties_array['thumbnail'] = "data:image/png;base64,".file_get_contents($filename);
	                $properties_array['thumbnail_name'] = $thumbnail_data->name;

	            } elseif (!empty($properties_array["thumbnail_url"])) {
	                $properties_array['thumbnail'] = $properties_array["thumbnail_url"];
	                
	            } elseif (!empty($properties_array["thumbnail_url"])) {
	                //Cas de la vignette de thumbnail (ancienne version) - permet d'assurer la compatibilite avec l'existant
		            $properties_array['thumbnail'] = "data:image/png;base64,".stripslashes($thumbnail_data->thumbnail);
		            $properties_array['thumbnail_name'] = stripslashes($thumbnail_data->name);
		            
	            } elseif (!empty($properties_array['docnum_file']) && !empty($properties_array['upload_directory'])) {
		            //Cas de la vignette de doc num
		            $rep = new upload_folder($properties_array['upload_directory']);
		            $file = construire_vignette("","",$rep->repertoire_path.$properties_array['docnum_file']);
		            if ($file) {
    		            $properties_array['thumbnail'] = "data:image/png;base64,".base64_encode($file);
		            } else {
    		            $properties_array['thumbnail'] = get_url_icon('no_image.png');
		            }
			    
		        } else {
			        $properties_array['thumbnail'] = get_url_icon('no_image.png');
			    }
			}
			
			if (!isset($returned_result[$onto->get_class_label($properties_array['type'])]) && $properties_array['is_draft'] == $draft_forms) {
			    $returned_result [$onto->get_class_label($properties_array['type'])] = array ();
			}
			
			if ($properties_array['is_draft'] == $draft_forms) {
			    $returned_result[$onto->get_class_label($properties_array ['type'])][$form_uri] = $properties_array;
			}
				
			if ($last_id && ($last_id == $properties_array['uri_id'])) {
				$returned_result['last_contribution'][$form_uri] = $properties_array;
			}
		}
		return $returned_result;
	}
	
	public static function get_display_label($class_uri){
	    $query = "select ?displayLabel where {
			<".$class_uri."> pmb:isbd ?displayLabel
		}";
	    if (self::get_datastore ()->query ( $query )) {
	        $results = self::get_datastore ()->get_result ();
	        foreach($results as $result){
	            return $result->displayLabel;
	        }
	    }
	    return '';
	}
	
	public static function search_in_draft() {
	    global $start, $datas;
	    global $completion, $param2;
	    $range = self::get_range_from_completion ( $completion );
	    
	    $query = "SELECT ?uri ?prop ?obj WHERE {";
	    if ($param2) {
	        $query .= "?uri pmb:area " . $param2 . " .";
	    }
	    $query .= "?uri rdf:type <" . $range . "> .
                ?uri ?prop ?obj . ";
	    if (addslashes(substr($datas, 0, 1)) != '*') {
	        $query .= "filter regex(?obj, '" . addslashes($datas) . "','i') .";
	    } elseif (addslashes(substr($datas, 0, 1)) == '*') {
	        $query .= "filter regex(?obj, '" . addslashes(substr($datas, 1)) . "','i') .";
	    }
	    $query .= "}";
	    
	    $result = array ();
	    if (self::get_datastore ()->query ( $query )) {
	        $row = self::get_datastore ()->get_result ();
	        for($i = 0; $i < count ( $row ); $i ++) {
	            if (!in_array($row[$i]->uri, $result)) {
	                $result[] = $row[$i]->uri;
	            }
	        }
	    }
	    return $result;
	}
	
	public static function save_in_store($id, $type) {
	    global $opac_url_base;
	    
	    $display_label = "";
	    $uri = "";
	    if (is_numeric($id) && $id) {
	        $entity_id = 0;
	        $entity_type = '';
	        switch ($type) {
	            case 'authority' :
	            case 'authorities' :
	                $authority = authorities_collection::get_authority($type, $id);
	                $entity_id = $authority->get_num_object();
	                $entity_type = static::get_string_type_from_authority($authority);
	                break;
	            default :
	                $entity_id = $id;
	                $entity_type = $type;
	                break;
	        }
	        $rdf_type = self::get_rdf_type_from_type($entity_type);
            $query = "	select ?uri where {
									?uri pmb:identifier '".addslashes($entity_id)."' .
									?uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <".addslashes($rdf_type)."> .
							}";
            
            self::get_datastore()->query($query);
            $display_label = entities::get_label_from_entity($id, $type);
            if (!self::get_datastore()->num_rows()) {
                $uri = addslashes(onto_common_uri::get_new_uri("",$opac_url_base.$entity_type."#"));
                $query = "
                        <$uri> pmb:identifier '".addslashes($entity_id)."' .
                        <$uri> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <".addslashes($rdf_type).">";
                $query = "insert into <pmb> {
                    $query
                }";
                self::get_datastore()->query($query);
            } else {
                $results = self::get_datastore()->get_result();
                $uri = $results[0]->uri;
            }
	    } else { // si pas numeric, il s'agit d'une uri
	        $uri = $id;
	        $query = "	select ?displayLabel where {
						<$uri> pmb:displayLabel ?displayLabel .
				}";
	        self::get_datastore()->query($query);
	        if (self::get_datastore()->num_rows()) {
	            $results = self::get_datastore()->get_result();
	            $display_label = $results[0]->displayLabel;
	        }
	        
	    }
        
        $response = [
            "uri" => $uri,
            "displayLabel" => $display_label
        ];
        return $response;
	}
	
	private static function get_string_type_from_authority(authority $authority){
	    switch ($authority->get_type_object()) {
	        case AUT_TABLE_AUTHORS :
	            return 'author';
	        case AUT_TABLE_CATEG :
	            return 'category';
	        case AUT_TABLE_PUBLISHERS :
	            return 'publisher';
	        case AUT_TABLE_COLLECTIONS :
	            return 'collection';
	        case AUT_TABLE_SUB_COLLECTIONS :
	            return 'subcollection';
	        case AUT_TABLE_SERIES :
	            return 'serie';
	        case AUT_TABLE_TITRES_UNIFORMES :
	            return 'titre_uniforme';
	        case AUT_TABLE_INDEXINT :
	            return 'indexint';
	        case AUT_TABLE_CONCEPT :
	            return 'concept';
	        case AUT_TABLE_AUTHPERSO :
	            $query = "SELECT authperso_authority_authperso_num FROM authperso_authorities WHERE id_authperso_authority = ".$authority->get_num_object();
	            $result = pmb_mysql_query($query);
	            if (pmb_mysql_num_rows($result)) {
	                $row = pmb_mysql_fetch_array($result);
	                return "authperso_".$row[0];
	            }
	            return 'authperso';
	    }
	    return "";
	}
	
	static public function get_empr_forms_done(int $empr_id, int $last_id = 0) 
	{
	    global $msg, $pmb_type_audit, $pmb_contribution_opac_edit_entity, $pmb_contribution_opac_show_sub_form;

	    $forms = array();
	    // L'audit doit être activer pour récupérer les entitées du contributeur.
	    if (!$pmb_type_audit) return $forms;
	    
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
	    
	    $all_audit = audit::get_all_from_user_id($empr_id, 1);
	    $onto = self::get_ontology();
	    
	    foreach ($all_audit as $audit) {
	        if (empty($audit->info)) continue;
	        
	        $info_audit = encoding_normalize::json_decode($audit->info, true);
	        if (empty($info_audit['uri'])) continue;

	        //Test de l'affichage des contributions venant de sous-formulaire
	        if (!empty($info_audit['subform']) && empty($pmb_contribution_opac_show_sub_form)) {
	            continue;
	        }
	        // on affiche pas les notices de bulletin
	        if ($audit->type_obj == AUDIT_NOTICE) {
	            $query = "SELECT bulletin_id FROM bulletins WHERE num_notice = $audit->object_id";
	            $result = pmb_mysql_query($query);
	            if (pmb_mysql_num_rows($result)) {
	                continue;
	            }
	        }
	        
	        $entity_type = "";
	        
	        if (strpos($info_audit['uri'], "article") !== false) {
	            $matches = array();
	            if (preg_match("/(article_\d+)/", $info_audit['uri'], $matches)) {
	                $entity_type = $matches[0];
	            }
	        }
	        
	        if (strpos($info_audit['uri'], "section") !== false) {
    	        $matches = array();
	            if (preg_match("/(section_\d+)/", $info_audit['uri'], $matches)) {
	                $entity_type = $matches[0];
	            }
	        }
	        
	        if (strpos($info_audit['uri'], "authperso") !== false) {
	            $matches = array();
	            if (preg_match("/(authperso_\d+)/", $info_audit['uri'], $matches)) {
	                $entity_type = $matches[0];
	            }
	        }
	        
	        if (empty($entity_type)) {
	            $entity_type = rdf_entities_converter::get_entity_type_from_object_type_audit($audit->type_obj);
	        }
	        
	        if ($audit->type_obj == AUDIT_CONCEPT) {	            
                $uri_type = 'http://www.w3.org/2004/02/skos/core#Concept';
	        } else {	            
                $uri_type = 'http://www.pmbservices.fr/ontology#'.$entity_type;
	        }
	        
            $can_edit = $pmb_contribution_opac_edit_entity;
            
	        // dans le can_edit on fait get_default_form_id_by_type définit les global $scenario_uri et $area_id
	        //Dans l'espace de modification a-t-on un scénario et un formulaire qui correspond?
            if (!self::can_edit($entity_type, $audit->object_id)) {
                $can_edit = false;
            }
            
	        global $scenario_uri, $area_id;
	        
	        // Droit sur l'espace
	        $area_access = false;
	        if (!empty($area_id)) {
                $area_access = true;
	            if (isset($dom_4)) {
	                $area_access = $dom_4->getRights($_SESSION['id_empr_session'], $area_id, 4);
	            }
	        }
	        
	        if (empty($forms[$onto->get_class_label($uri_type)])) {
	            $forms[$onto->get_class_label($uri_type)] = array();
	        }
	        
	        // Droit de modification et de validation
	        if ($pmb_contribution_opac_edit_entity) {
	            
	            // APCA : 09/02/2020 On ne tien pas compte du paramétre pour les droits sur les scénarios
//     	        if (isset($dom_5) && $scenario_uri) {
//                     // Droits de validation
//                     $can_push = $dom_5->getRights($_SESSION['id_empr_session'], onto_common_uri::get_id($scenario_uri), 16);
//     	        }

    	        $can_push = true;
	        } else {
	            // $pmb_contribution_opac_edit_entity
	            // La modification des contributions doit être activé
    	        $can_push = false;
	        }
	        
	        // Droits de modification
	        if (!$can_push || !$area_access || !$scenario_uri || !$area_id) {
	            $can_edit = false;
	        }
	        
	        // l'entité est un bulletin
	        $bulletin = false;
	        if ($audit->type_obj == AUDIT_BULLETIN) {
    	        $bulletin = true;
	        }
	        
	        $data=array();
            $data["sub"] = $entity_type;
	        $instance = rdf_entities_converter::get_entity($audit->object_id, $entity_type);
	        if ($audit->type_obj == AUDIT_AUTHOR) {
	            $data["author_type"] = $instance->type;
	        } else if ($audit->type_obj == AUDIT_NOTICE || $audit->type_obj == AUDIT_BULLETIN) {
	            $data["doctype"] = $instance->notice->typdoc;
	            $data["bibliographical_lvl"] = $instance->notice->niveau_biblio;
	        } else if ($audit->type_obj == AUDIT_TITRE_UNIFORME) {
	            $data["oeuvre_type"] = $instance->oeuvre_type;
	            $data["oeuvre_nature"] = $instance->oeuvre_nature;
	        }
	        
	        $thumbnail = '';
	        if ($audit->type_obj == AUDIT_EXPLNUM){
    	        $thumbnail = "./vig_num.php?explnum_id=".$instance->explnum_id;
	        }

	        $date = new DateTime(audit::get_last_edit_from_object_id($audit->object_id));
	        $forms[$onto->get_class_label($uri_type)][$info_audit['uri']] = array(
	            "identifier" => $audit->object_id,
	            "type" => $uri_type,
	            "entity_type" => $entity_type,
	            "displayLabel" => rdf_entities_converter::get_entity_isbd($audit->object_id, $entity_type),
	            "can_edit" => $can_edit,
	            "can_push" => $can_push,
	            "link" => self::get_link_from_type($uri_type, $audit->object_id, $bulletin),
	            "last_edit" => date($msg['date_format'].' H:i', $date->getTimestamp()),
	            "icon" => self::get_icon_src($data),
	            "thumbnail" => $thumbnail
	        );

	        if (!empty($last_id) && ($last_id == $audit->object_id)) {
	            $forms['last_contribution'][$info_audit['uri']] = $forms[$onto->get_class_label($uri_type)][$info_audit['uri']];
	        }
	    }
	    
	    return $forms;
	}
	
	static public function get_entity_const(string $type) {
	    switch($type) {
	        case 'record':
	            return TYPE_NOTICE;
	        case 'author':
	            return TYPE_AUTHOR;
	        case 'collection':
	            return TYPE_COLLECTION;
	        case 'authperso':
	            return TYPE_AUTHPERSO;
	        case 'category':
	            return TYPE_CATEGORY;
	        case 'indexint':
	            return TYPE_INDEXINT;
	        case 'concept':
	            return TYPE_CONCEPT;
	        case 'editeur':
	            return TYPE_PUBLISHER;
	        case 'serie':
	            return TYPE_SERIE;
	        case 'subcollection':
	            return TYPE_SUBCOLLECTION;
	        case 'titre_uniforme':
	        case 'work':
	            return TYPE_TITRE_UNIFORME;
            default:
                if (strpos($type, "authperso") !== false) {
                    $id = str_replace("authperso_", "", $type);
                    $id = intval($id) + 1000;
                    return $id;
                }
	    }
	}
	
	public static function can_edit($entity_type, $entity_id = 0) {
	    global $pmb_contribution_opac_edit_entity, $opac_contribution_area_activate;
	    global $gestion_acces_active, $gestion_acces_empr_contribution_scenario, $gestion_acces_empr_contribution_area;
	    
	    $form_id = contribution_area_form::get_default_form_id_by_type($entity_type, $entity_id);

	    global $scenario_uri, $area_id;
	    
	    if (!$_SESSION['id_empr_session'] || !$gestion_acces_active || !$pmb_contribution_opac_edit_entity || !$area_id || !$scenario_uri || !$form_id || !$opac_contribution_area_activate) {
	        return false;
	    }
	    
	    $ac = new acces();
	    
	    // APCA : 09/02/2020 On ne tien pas compte du paramétre pour les droits sur les scénarios
	    // if (!$gestion_acces_empr_contribution_area || !$gestion_acces_empr_contribution_scenario) {
	    
	    if (!$gestion_acces_empr_contribution_area) {
	        return false;
	    } else {
	        $dom_4 = $ac->setDomain(4);
	        
	        // APCA : 09/02/2020 On ne tien pas compte du paramétre pour les droits sur les scénarios
	        //$dom_5 = $ac->setDomain(5);
	    }
	    
	    // Droit sur l'espace
	    if (!$dom_4->getRights($_SESSION['id_empr_session'], $area_id, 4)) {
	        return false;
	    }
	    
	    // APCA : 09/02/2020 On ne tien pas compte du paramétre pour les droits sur les scénarios
// 	    if (!$dom_5->getRights($_SESSION['id_empr_session'], onto_common_uri::get_id($scenario_uri), 16)) {
// 	        return false;
// 	    }
	    
	    return true;
	}
	
	
	public static function alert_mail_users_pmb() {
	    $mail_opac_user_contribution = new mail_opac_user_contribution();
	    $query = "SELECT * FROM users WHERE user_alert_contribmail = 1";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        while ($user = pmb_mysql_fetch_object($result)) {
	            if ($user->user_email) {
	                $mail_opac_user_contribution->set_mail_to_id($user->userid);
	                $mail_opac_user_contribution->set_user($user);
	                $mail_opac_user_contribution->send_mail();
	            }
	        }
	    }
	}
	
	//fonction permettant d'alerter un contributeur de la validation de sa contribution
	public static function mail_empr_contribution_validate($uri) {
	    $store = new contribution_area_store();
	    $dataStore = $store->get_datastore();
	    $query = "SELECT * WHERE {
                    <".$uri."> <http://www.pmbservices.fr/ontology#has_contributor> ?id_contributor.
                    <".$uri."> <http://www.pmbservices.fr/ontology#last_edit> ?last_edit.
                    <".$uri."> <http://www.pmbservices.fr/ontology#displayLabel> ?display_label.
                }";
	    $dataStore->query($query);
	    $results = $dataStore->get_result();

	    //S'il s'agit du contributeur qui valide sa propre contribution, on sort !
	    if ($results[0]->id_contributor == $_SESSION["id_empr_session"]); return;
	    
	    // On va cherche l'emprunteur
	    $empr  = new emprunteur($results[0]->id_contributor);
	    if ($empr->mail) {
	        $mail_opac_reader_contribution = new mail_opac_reader_contribution();
	        $mail_opac_reader_contribution->set_mail_to_id($results[0]->id_contributor);
	        $mail_opac_reader_contribution->set_empr($empr);
	        $mail_opac_reader_contribution->set_datastore_results($results);
	        $mail_opac_reader_contribution->send_mail();
	    }
	}
	
	public static function get_icon_src($data) {
	    switch ($data["sub"]){
	        case "record":
	        case "bulletin":
	            return notice::get_picture_url_no_image($data["bibliographical_lvl"], $data["doctype"]);      
            break;
	        case "author":
	            if ($data["author_type"] && $data['sub'] && file_exists(get_url_icon("authorities/".$data['sub']."_".$data['author_type']."_icon.png"))) {
    	            return get_url_icon("authorities/".$data['sub']."_".$data['author_type']."_icon.png");
	            }elseif ($data['sub'] && file_exists(get_url_icon('authorities/'.$data["sub"].'_icon.png'))) {
    	            return get_url_icon('authorities/'.$data["sub"].'_icon.png');
	            } else {
	                return "";
	            }
            break;
	        case "work":
	            $tu_type = $data["oeuvre_type"];
	            $tu_nature = $data["oeuvre_nature"];
	            if (file_exists(get_url_icon('authorities/tu_'.$tu_nature.'_'.$tu_type.'_icon.png'))) {
	               return get_url_icon('authorities/tu_'.$tu_nature.'_'.$tu_type.'_icon.png');
	            }
	            if (file_exists(get_url_icon("authorities/titre_uniforme_icon.png"))) {
	                return get_url_icon("authorities/titre_uniforme_icon.png");
	            }
	        case "docnum":
	            if (file_exists(get_url_icon("icone_nouveautes.png"))) {
	                return get_url_icon("icone_nouveautes.png");
	            }
	        default :
	            if (strpos($data["sub"],"authperso") !== false){
	                return get_url_icon('authorities/authperso_icon.png');
	            }
	            if (file_exists(get_url_icon('authorities/'.$data["sub"].'_icon.png'))) {
	                return get_url_icon('authorities/'.$data["sub"].'_icon.png');
	            }
	            return "";
	            break;
	    }
	}
	
	public static function get_all_forms() {
	    $forms = array();
	    $query = 'select * from contribution_area_forms';
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
	        while($row = pmb_mysql_fetch_object($result)) {
	            $row->form_parameters = encoding_normalize::json_decode($row->form_parameters, true);
	            $forms[] = $row;
	        }
	    }
	    return $forms;
	}
}
