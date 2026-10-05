<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_forms_controller.class.php,v 1.31 2024/03/22 15:31:04 qvarin Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/contribution_area/contribution_area.class.php');
require_once($class_path.'/encoding_normalize.class.php');
require_once($include_path.'/templates/contribution_area/contribution_area_forms.tpl.php');
require_once($class_path.'/contribution_area/contribution_area_form.class.php');

/**
 * class contribution_area_forms_controller
 */
class contribution_area_forms_controller {
	
	public static $identifier = 0;
	protected static $store_data;
	protected static $classes_properties = array();
	protected static $entity_forms = array();
	protected static $form_properties = array();
	protected static $entities;
	protected static $initialized = false;
	protected static $contribution_status = array();
	public static $datastore;
	public static $ontology;
	
	public static function get_store_data(){
		if (isset(self::$store_data)) {
			return self::$store_data;
		}
		self::fetch_data();
		self::get_contribution_status();
		$store_data = array();
		foreach(self::$entities as $entity){
		    $entity->name = $entity->get_display_name_for_area();
			$store_data[] = $entity;
			foreach (self::$entity_forms[$entity->uri] as $form) {
				foreach ($form["properties"] as $property) {
					$store_data[] = $property;					
				}	
				unset($form["properties"]);
				$store_data[] = $form;
			}
		}
		$store_data = array_merge($store_data,self::$contribution_status);
		self::$store_data = $store_data;
		return self::$store_data;	
	}
	
	public static function fetch_data(){		
		if(!self::$initialized){
			self::$entities = array();
			$ontology = contribution_area::get_ontology();
			$classes_array = $ontology->get_classes_uri();
			foreach($classes_array as $entity){
				if (isset($entity->flags)  && (is_array($entity->flags) && in_array('pmb_entity', $entity->flags))) {
					$entity->type = "entity";
					$entity->id = self::get_identifier();
					self::$entities[] = $entity;
					
					$classes_properties = $ontology->get_class_properties($entity->uri);
					foreach ($classes_properties as $property_uri) {
						$property = $ontology->get_property($entity->uri, $property_uri);
						self::$classes_properties[$entity->pmb_name][$property->pmb_name] = $ontology->get_property($entity->uri, $property_uri);
					}
					self::$entity_forms[$entity->uri] = array();
					self::get_entity_forms(self::$entity_forms[$entity->uri], $entity);
				}
			}
			self::$initialized = true;
		}
	}

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
	
	public static function delete_uri($uri) {
		// On supprime tous les triplets correspondant à cette uri
		$query_delete = "delete {
				<".$uri."> ?prop ?obj
				}";
		self::get_datastore ()->query($query_delete);
		$query_delete = "delete {
				?suj ?prop <".$uri.">
				}";
		self::get_datastore ()->query($query_delete);
	}
	
	public static function get_identifier(){
		self::$identifier++;
		return self::$identifier;
	}
	
	public static function get_entity_forms(&$forms_array, $entity){
		$query = 'select id_form, form_title, form_comment, form_parameters from contribution_area_forms where form_type = "'.$entity->pmb_name.'"';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$forms_array[] = array(
						'type' => "form",
						'form_id' => $row->id_form,
						'id' => self::get_identifier(),
						'parent_type' => $entity->pmb_name,
						'name' => $row->form_title,
				        'comment' => $row->form_comment,
						'parent' => $entity->id,
						'pmb_name' => $entity->pmb_name,
				);
 				$form_parameters = json_decode($row->form_parameters, true);

 				$properties = array();
 				if(is_array($form_parameters)){
	 				foreach($form_parameters as $prop => $pValues){
	 					//on regarde s'il s'agit d'un toogle
	 					$properties[] = array(
	 						'type' => "property",
	 						'form_id' => $row->id_form,
	 						'id' => self::get_identifier(),
	 						'parent_type' => $entity->pmb_name,
	 						'name' => $pValues['label'],
	 						//'flag' => (!empty(self::$classes_properties[$entity->pmb_name][$prop]->flags[0]) ? self::$classes_properties[$entity->pmb_name][$prop]->flags[0] : ""),
	 					    'flag' => (!empty(self::$classes_properties[$entity->pmb_name][$prop]->flags) ? static::init_flags(self::$classes_properties[$entity->pmb_name][$prop]->flags) : []),
	 					    'pmb_name' => (!empty(self::$classes_properties[$entity->pmb_name][$prop]->pmb_name) ? self::$classes_properties[$entity->pmb_name][$prop]->pmb_name : "")
	 					);
	 					
	 				}
 				}
	 			$forms_array[count($forms_array)-1]["properties"] = $properties;
			}
		}
		return $forms_array;
	}
	
	private static function init_flags(array $flags) {
	    $authority_key = array_search("authority", $flags);
	    if ($authority_key === false) {
	        return $flags;
	    }
	    unset($flags[$authority_key]);
	    $tab_authorities = [
	        "author",
	        "category",
	        "publisher",
	        "collection",
	        "subcollection",
	        "serie",
	        "work",
	        "indexint",
	        "concept",
	    ];
	    $authpersos = authpersos::get_authpersos();
	    foreach ($authpersos as $authperso) {
	        $tab_authorities[] = "authperso_".$authperso["id"];
	    }
	    $flags = array_merge($flags, $tab_authorities);
	    $flags = array_unique($flags);
	    return $flags;
	}
	
	public static function display_forms_list(){
		global $contribution_area_entity_line;
		global $contribution_area_form_line;
		global $contribution_area_form_table;
		global $msg;
		global $charset;
		self::fetch_data();
		
		$form_list = '
		<div class="row">
			<span class="item-expand">
				<a href="#" onclick="expandAll();return false;">
					<img src="'.get_url_icon('expand_all.gif').'" style="border:0px" id="expandall">
				</a>
				<a href="#" onclick="collapseAll();return false;">
					<img src="'.get_url_icon('collapse_all.gif').'" style="border:0px" id="collapseall">
				</a>
			</span>
		</div>
		<div class="row">
		';
		
		$i = 0;	
		
		foreach (self::$entities as $entity) {
			$forms = "";
			if (!is_array($entity)) {
				$form_line = str_replace('!!entity_id!!', $i.$entity->id, $contribution_area_entity_line);
				$form_line = str_replace('!!entity_name!!', $entity->get_display_name(), $form_line);	
				$form_line = str_replace('!!entity_type!!', $entity->pmb_name, $form_line);
				
				$form_line = str_replace('!!forms_table!!', (count(self::$entity_forms[$entity->uri]) ? $contribution_area_form_table : "") , $form_line);

				$j = 0;
				foreach (self::$entity_forms[$entity->uri] as $form) {
					$j++;
					$forms .= $contribution_area_form_line;					
					if ($j % 2) {
						$forms = str_replace('!!odd_even!!', "odd", $forms);
					} else {
						$forms = str_replace('!!odd_even!!', "even", $forms);
					}
					$forms = str_replace('!!form_name!!', htmlentities($form['name'], ENT_QUOTES, $charset), $forms);
					$forms = str_replace('!!form_id!!', $form['form_id'], $forms);
					$forms = str_replace('!!form_type!!', $entity->pmb_name, $forms);
					
					//On va vérifier si le formulaire est dans un scénario auquel cas on disable la suppression avec un autre message 
					$contribution_form = new contribution_area_form($entity->pmb_name, $form['form_id']);
					$has_linked_scenario = $contribution_form->get_scenario_linked()['count'];
					
					//Desactivation des boutons supprimer pour les formulaires comportant des brouillons (message prioritaire sur le message d'utilisatiuon du formulaire dans un scénario)
					if (contribution_area_form::has_draft_contribution_from_id($form['form_id'])){
					    $forms = str_replace('!!disabled!!', 'disabled', $forms);		
					    $forms = str_replace('!!disabled_message!!', $msg['contribution_has_draft_hover'], $forms);
					}
					else if ($has_linked_scenario) {
					    $forms = str_replace('!!disabled!!', 'disabled', $forms);
					    $forms = str_replace('!!disabled_message!!', $msg['contribution_has_scenario_hover'], $forms);
					} else {
					    $forms = str_replace('!!disabled!!', '', $forms);		
					    $forms = str_replace('!!disabled_message!!', '', $forms);		
					}
					unset($contribution_form);
				}	
				
				$form_line = str_replace('!!forms_number!!', '('.$j.')', $form_line);
				$form_line = str_replace('!!forms_tab!!', $forms, $form_line);				
				$form_list.= $form_line;
				
				$i++;
			}
		}
		$form_list .= "</div>";
		return $form_list;
	}
	
	public static function get_forms_by_entity($entity){
		
	}
	
	public static function get_contribution_status() {
		if (!count(self::$contribution_status)) {
			$query = "SELECT contribution_area_status_id AS id, contribution_area_status_gestion_libelle AS name, contribution_area_status_available_for as available_for FROM contribution_area_status";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_assoc($result)) {
				    $available_for = unserialize($row["available_for"]);
				    if (!empty($available_for)) {
    					$row["type"] = "contributionStatus";
    					$row["pmb_name"] = $row["id"];
    					$row["available_for"] = $available_for;
    					self::$contribution_status[] = $row;
				    }
				}
			}
		}
		return self::$contribution_status;
	}
	
	public static function get_moderation_forms($user_id = 0) {
		global $charset;
		$query = "SELECT * WHERE {
					?s <http://www.pmbservices.fr/ontology#has_contributor> ?contributor .
					?s ?p ?o .
					?s <http://www.pmbservices.fr/ontology#last_edit> ?last_edit .
					optional {
						?s <http://www.pmbservices.fr/ontology#identifier> ?identifier
					}
				}
				ORDER BY ?contributor DESC (?last_edit)";
	
		$results = array ();
		//Parse initial des résultats de la requete sparql
		if (self::get_datastore()->query($query)) {
			$rows = self::get_datastore()->get_result();
			foreach ($rows as $row) {
			    if (empty($row->identifier)) {
					if (!isset($results[$row->s])) {
						$results[$row->s] = array ();
					}
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
					}
				}
			}
		}
		return self::edit_results_to_template($results, false, 0);
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
	
	public static function get_empr_forms($id_empr, $validated_forms = false, $last_id = 0) {
		global $charset;
	
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
	
			default :
				return '#';
		}
	}
	
	public static function edit_results_to_template($results,$validated_forms = false, $last_id = 0) {
		global $msg, $charset, $pmb_contribution_opac_show_sub_form;
		//gestion des droits
	
		$returned_result = array ();
		//Composition d'un résultat manipulable dans les templates
		$onto = self::get_ontology();
		foreach ($results as $form_uri => $properties_array) {
				
			//droit sur l'espace
			if (!empty($properties_array['area']) && isset($dom_4)) {
				if (!$dom_4->getRights($_SESSION['id_empr_session'],$properties_array['area'], 4)) {
					continue;
				}
			}
			if (!$validated_forms && !empty($properties_array["identifier"])) {
				continue;
			} else if ($validated_forms && !isset($properties_array["identifier"])) {
				continue;
			}
	
			if (!isset($returned_result[$onto->get_class_label($properties_array['type'])])) {
				$returned_result [$onto->get_class_label($properties_array['type'])] = array ();
			}
			
			if (!isset($properties_array['entity_type'])) {
			    $properties_array['entity_type'] = $onto->get_class_label($properties_array['type']);
			}
	
			if (!empty($properties_array['last_edit'])) {
				$properties_array['last_edit'] = date($msg['1005'].' H:i', $properties_array['last_edit']);
			}
			//infos de l'espace
			if (!empty($properties_array['area'])) {
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
	
			//infos du contributeur
			if (!empty($properties_array['contributor'])) {
			    $contributor = self::get_contributor_infos($properties_array['contributor']);
			    $properties_array['contributor'] = $contributor;
			    $properties_array['contributor_id'] = $contributor['id'];
			    $properties_array['contributor_name'] = $contributor['name'];
			}	
			$results[$form_uri] = $properties_array;
		}
		return $results;
	}
	
	public static function get_ontology() {
		if(!isset(self::$ontology)){
			self::$ontology = contribution_area::get_ontology();
		}
		return self::$ontology;
	}
	
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

	    // On va cherche l'emprunteur
	    $empr  = new emprunteur($results[0]->id_contributor);
        if ($empr->mail) {
            $mail_reader_contribution = new mail_reader_contribution();
            $mail_reader_contribution->set_mail_to_id($results[0]->id_contributor);
            $mail_reader_contribution->set_empr($empr);
            $mail_reader_contribution->set_datastore_results($results);
            $mail_reader_contribution->send_mail();
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
		
} // end of contribution_area_forms_controller