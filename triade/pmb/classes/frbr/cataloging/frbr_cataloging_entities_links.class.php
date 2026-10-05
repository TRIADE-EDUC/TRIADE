<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_entities_links.class.php,v 1.16 2024/03/22 15:31:04 qvarin Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/onto/onto_pmb_entities_store.class.php');
require_once($class_path.'/authority.class.php');
require_once($class_path.'/frbr/cataloging/frbr_cataloging_entities_link.class.php');
require_once($class_path.'/encoding_normalize.class.php');
require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator.class.php');

class frbr_cataloging_entities_links {
	
	protected static $links;
	
	protected static $links_labels;
	
	static public function get_links() {
		if (!empty(static::$links)) {
			return static::$links;
		}
		static::$links = array();
		$query = '
			select * where {
				?link rdfs:subClassOf pmb:entity_link .
				?link pmb:name ?link_name .
				?link rdfs:domain ?domain .
				?domain pmb:name ?domain_name .
				?link rdfs:range ?destination .
				?destination pmb:name ?destination_name .
			}
		';
		onto_pmb_entities_store::query($query);
		if (onto_pmb_entities_store::num_rows()) {
			$results = onto_pmb_entities_store::get_result();
			foreach ($results as $result) {
				$link = new frbr_cataloging_entities_link($result->link, $result->link_name, $result->domain_name);
				if (!isset(static::$links[$result->domain_name])) {
					static::$links[$result->domain_name] = array();
				}
				$link->add_destination($result->destination, $result->destination_name);
				static::$links[$result->domain_name][$result->link_name] = $link;
			}
		}
		return static::$links;
	}
	
	static public function get_json_links($destination_type = '', $possible_links = array()) {
		static::get_links();
		$return = array();
		$checked_links = array();
		foreach (static::$links as $domain => $links) {
			$return[$domain] = array();
			foreach ($links as $link_name => $link) {
				$destinations = $link->get_destinations();
				if($destination_type){
					foreach($destinations as $destination){
						if($destination['name'] == $destination_type){
							$return[$domain][$link_name] = array();
							$return[$domain][$link_name]['linked_entities'] = $destinations;
							$return[$domain][$link_name]['label'] = $link->get_label();
							$type = $link->get_type();
							if (!empty($type)) {
								$return[$domain][$link_name]['type'] = $type;
							}
						}
					}
				}else{
					$return[$domain][$link_name] = array();
					$return[$domain][$link_name]['linked_entities'] = $destinations;
					$return[$domain][$link_name]['label'] = $link->get_label();
					$type = $link->get_type();
					if (!empty($type)) {
						$return[$domain][$link_name]['type'] = $type;
					}	
				}
			}
			if(count($possible_links)){
				foreach($return[$domain] as $link_name => $link){
					foreach($possible_links as $possible_link){
						if($possible_link['name'] == $link_name){
							if(!isset($checked_links[$domain])){
								$checked_links[$domain] = array();
							}
							$checked_links[$domain][$link_name] = $link;
						}
					}
				}	
			}else{
				$checked_links = $return;
			}
			
		}
		return encoding_normalize::json_encode($checked_links);
	}
	
	static public function get_label($link_uri) {
		global $lang;
		
		if (!isset(static::$links_labels)) {			
			$query = '
				select * where {
					?link rdfs:label ?label .
					?link rdfs:subClassOf pmb:entity_link
				}
			';
			onto_pmb_entities_store::query($query);
			if (onto_pmb_entities_store::num_rows()) {
				$results = onto_pmb_entities_store::get_result();
				foreach ($results as $result) {
					if (!isset(static::$links_labels[$result->link])) {
						static::$links_labels[$result->link] = array();
					}
					if (!empty($result->label_lang)) {
						static::$links_labels[$result->link][$result->label_lang] = $result->label;
					}
				}
			}
		}

		$sub_lang = substr($lang, 0, 2);
			
		if (isset(static::$links_labels[$link_uri][$sub_lang])) {
			return static::$links_labels[$link_uri][$sub_lang];
		}
		if (isset(static::$links_labels[$link_uri][''])) {
			return static::$links_labels[$link_uri][''];
		}
		if (isset(static::$links_labels[$link_uri]['fr'])) {
			return static::$links_labels[$link_uri]['fr'];
		}
	}
	
	static public function get_links_from_entity_type($type, $id = 0){
		$entity_links = self::get_links();
		$entity_type = "";
		switch($type){
			case 'records':
				$entity_type = "record";
				break;
			case 'authorities':
				$authority = new authority($id);
				$entity_type = $authority->get_string_type_object();
				switch ($entity_type) {
					case 'titre_uniforme' :
						$entity_type = 'work';
						break;
				}
				break;
			default :
				$entity_type = $type;
				break;
		}
		return (isset($entity_links[$entity_type]) ? static::links_object_to_array($entity_links[$entity_type]) : array());
	}
	
	static protected function links_object_to_array($entity_links) {
		$links_array =  array();
		if (is_array($entity_links)) {
			foreach ($entity_links as $link => $object) {
				if (!isset($links_array[$link])) {
					$links_array[$link] = array();
				}
				$links_array[$link]['uri'] = $object->get_uri(); 
				$links_array[$link]['name'] = $object->get_name();
				$links_array[$link]['label'] = static::get_label($object->get_uri());
				$links_array[$link]['type'] = $object->get_type();
				$links_array[$link]['sources'] = $object->get_sources();
				$links_array[$link]['destinations'] = $object->get_destinations();
			}
		}
		return $links_array;
	}
	
	static public function get_link_form($source, $target){
		$return = array();
		$return['status'] = false;
		
		$links = self::get_links_from_entity_type($source->type, $source->id);
		
		$source->type = self::get_node_type($source);
		$target->type = self::get_node_type($target, $source->type);
		$source->label = self::get_node_label($source->id, $source->type);
		$target->label = self::get_node_label($target->id, $target->type);
		
		$possible_links = self::check_link_type($links, $target->type);
		$possible_links = self::check_restrictions($source, $possible_links);
		
		if(count($possible_links)){
			$return['status'] = true;
			//un seul lien possible
			if (count($possible_links) == 1 && count($possible_links[0]['type']) == 0) {
				$array_source = (array) $source;
				$array_target = (array) $target;
				self::link_entities($array_source, $array_target, $possible_links[0]['name'], '');
				$return['entity_added'] = true;
			} 
			$template_path =  "./includes/templates/frbr/cataloging/frbr_cataloging_links_form.html";
			if(file_exists("./includes/templates/frbr/cataloging/frbr_cataloging_links_form_subst.html")){
				$template_path =  "./includes/templates/frbr/cataloging/frbr_cataloging_links_form_subst.html";
			}
			
			if(file_exists($template_path)){
				$h2o = H2o_collection::get_instance($template_path);
				$return['html'] = $h2o->render(array(
					'entities' => frbr_cataloging_entities::get_json_entities(),
					'entities_links' => frbr_cataloging_entities_links::get_json_links($target->type, $possible_links),
					'source' => $source,
					'target' => $target,
				));
			}
		}
		return $return;
	}
	
	static protected function check_restrictions($source, $possible_links){
		$returned_links = array();
		foreach($possible_links as $link){
			$linked_entities = onto_pmb_entities_mapping::get_link_from_entity($source->type, $source->id, $link['name']);
			$max_restrict = $linked_entities[$link['name']]['link_max_restriction'];
			if(($max_restrict == -1) || (count($linked_entities[$link['name']]['linked_entities']) < $max_restrict)){
				$returned_links[] = $link;
			}
		}
		return $returned_links;
	}
	
	static protected function get_node_type($node, $destination_type = ''){
		if($node->type == "records"){
			return 'record';
		}else{
			$entity = new authority($node->id);
			$type = $entity->get_string_type_object();
			switch($type) {
				case 'authperso' :
					$auhtperso = $entity->get_object_instance(); 
					if ($auhtperso->is_event()) {
						//TODO : cas particulier à revoir, pas propre
						if ($destination_type == 'work') {
							return 'event';
						}
					}
					return 'authperso'.(isset($auhtperso->info['authperso']['id']) ? '_'.$auhtperso->info['authperso']['id'] : '');					
				case 'titre_uniforme' :
					return 'work';
				default :
					return $type;
			}
		}
	}
	
	static protected function get_node_label($id, $type){
		$id = intval($id);
		if($type != 'record'){
			$entity = new authority($id);
			return $entity->get_isbd(); 
		}
		return notice::get_notice_title($id);
	}
	
	static protected function get_node_id($id, $type){
		if($type == 'record'){
			return $id;
		}
		$entity = new authority($id);
		return $entity->get_num_object();
	}
	
	static protected function check_link_type($links_source, $destination_type){
		$possible_links = array();
		foreach($links_source as $link){
			foreach($link['destinations'] as $destination){
				if($destination['name'] == $destination_type){				
					$possible_links[] = $link;
				}
			}
		}
		return $possible_links;
	}
	
	static public function link_entities($source, $target, $link, $link_type) {
		if(!empty($source['id']) && !empty($target['id'])){
			
			$source['id'] = self::get_node_id($source['id'], $source['type']);
			$target['id'] = self::get_node_id($target['id'], $target['type']);
			
			$uri_link = 'http://www.pmbservices.fr/ontology#'.$link;
			
			$rdf_integrator_class_name = rdf_entities_integrator::get_entity_integrator_name_from_type($source['type']);
			//on instancie avec un config vide. On a juste besoin des proprietes
			$rdf_integrator = new $rdf_integrator_class_name(array());
			
			//on ajoute l'identifiant de la source
			$rdf_integrator->set_entity_id($source['id']);
			
			$rdf_map_fields = $rdf_integrator->get_map_fields();			
			if (isset($rdf_map_fields[$uri_link])) {
				$rdf_integrator->update_property($rdf_map_fields[$uri_link], $target['id']);
				return true;
			} 
			
			$rdf_foreign_fields = $rdf_integrator->get_foreign_fields();			
			if (isset($rdf_foreign_fields[$uri_link])) {
				$rdf_integrator->update_property($rdf_foreign_fields[$uri_link], $target['id']);
				return true;
			}

			$rdf_cataloging_entities = $rdf_integrator->get_cataloging_entities();
			if (isset($rdf_cataloging_entities[$uri_link])) {
				if (!empty($link_type)) {
					$fields = onto_pmb_entities_mapping::get_entity_rdf_relational_mapping_link_types($source['type']);
					if (isset($fields[array_keys($link_type)[0]]) && isset($link_type[array_keys($link_type)[0]])) {
						$rdf_cataloging_entities[$uri_link]['other_fields'][$fields[array_keys($link_type)[0]]] = $link_type[array_keys($link_type)[0]];
					}					
				}
				$rdf_integrator->add_linked_entity($rdf_cataloging_entities[$uri_link], $target['id']);
				if (isset($rdf_cataloging_entities[$uri_link]['callable'])) {
					$rdf_cataloging_entities[$uri_link]['callable']['arguments'][] = $source['id'];
					$rdf_cataloging_entities[$uri_link]['callable']['arguments'][] = $target['id'];
					
					if (isset($fields[array_keys($link_type)[0]]) && isset($link_type[array_keys($link_type)[0]])) {
						$rdf_cataloging_entities[$uri_link]['callable']['arguments'][] = $link_type[array_keys($link_type)[0]];
					}
					call_user_func_array($rdf_cataloging_entities[$uri_link]['callable']["method"], $rdf_cataloging_entities[$uri_link]['callable']["arguments"]);					
				}
				return true;
			}

			$rdf_linked_entities = $rdf_integrator->get_linked_entities();
			if (isset($rdf_linked_entities[$uri_link])) {
				if (!empty($link_type)) {
					$fields = onto_pmb_entities_mapping::get_entity_rdf_relational_mapping_link_types($source['type']);
					if (isset($fields[array_keys($link_type)[0]]) && isset($link_type[array_keys($link_type)[0]])) {
						$rdf_linked_entities[$uri_link]['other_fields'][$fields[array_keys($link_type)[0]]] = $link_type[array_keys($link_type)[0]];
					}					
				}
				$rdf_integrator->add_linked_entity($rdf_linked_entities[$uri_link], $target['id']);
				return true;
			}			
		}
		return false;		
	}
	

	static public function is_reciprocal($link_name) {
		if (!empty($link_name)) {
			$query = '
					select ?reciprocal where {
						<http://www.pmbservices.fr/ontology#'.$link_name.'> pmb:reciprocalLink ?reciprocal
					}
				';
			onto_pmb_entities_store::query($query);			
			if (onto_pmb_entities_store::num_rows() == 1) {
				$result = onto_pmb_entities_store::get_result();
				if ($result[0]->reciprocal) {
					return true;
				}
			}
		}
		return false;
	}
}