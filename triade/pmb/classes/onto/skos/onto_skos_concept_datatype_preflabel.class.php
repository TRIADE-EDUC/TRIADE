<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_datatype_preflabel.class.php,v 1.14 2019/06/11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class onto_skos_concept_datatype_preflabel extends onto_common_datatype{
	
	public function __construct($value, $value_type, $value_properties, $datatype_ui_class_name='') {
		$this->value = $value;
		$this->value_type = $value_type;
		$this->value_properties = $value_properties;
		$this->set_datatype_ui_class_name("onto_skos_concept_datatype_preflabel_card_ui");
	} // end of member function __construct
	
	public function check_value(){
		//TODO : on plug le système des vedettes composé ici pour leurs enregistrement	
		if (is_string($this->value)) return true;
			return false;
	}

	public static function get_values_from_form($instance_name, $property, $uri_item) {
		$datatypes = array();
		$var_name = $instance_name."_".$property->pmb_name;
		
		global ${$var_name};
		if (${$var_name} && count(${$var_name})) {
			global ${$var_name."_is_composed"};
			if (!${$var_name."_is_composed"}) {
				// Ce n'est pas une vedette composée
				
				//On va supprimer une éventuelle vedette précédente
				global ${$var_name."_composed"};
				foreach (${$var_name."_composed"} as $order => $data) {
					if ($data["id"]) {
						$vedette_composee = new vedette_composee($data["id"]);
						$vedette_composee->delete();
					}
				}
				
				// On va chercher les valeurs actuelles
				foreach (${$var_name} as $order => $data) {
					$data=stripslashes_array($data);
					if ($data["value"]) {
						$data_properties = array();
						if ($data["lang"]) {
							$data_properties["lang"] = $data["lang"];
						}
						if ($data["type"] == "http://www.w3.org/2000/01/rdf-schema#Literal") {
							$data_properties["type"] = "literal";
						} else {
							$data_properties["type"] = "uri";
						}
						if (!empty($data["display_label"])) {
							$data_properties["display_label"] = $data["display_label"];
						}
						$class_name = static::class;
						$datatypes[$property->uri][] = new $class_name($data["value"], $data["type"], $data_properties);
					}
				}
			} else {
				// C'est une vedette composée
				global ${$var_name."_composed"};
				foreach (${$var_name."_composed"} as $order => $data) {
					$data=stripslashes_array($data);
					
					if ($data["elements"]) {
						$vedette_composee = new vedette_composee($data["id"], $data["grammar"]);
						if ($data["value"]) {
							$vedette_composee->set_label($data["value"]);
						}
						
						// On commence par réinitialiser le tableau des éléments de la vedette composée
						$vedette_composee->reset_elements();
						
						// On remplit le tableau des éléments de la vedette composée
						foreach ($data["elements"] as $subdivision => $elements) {
							if ($elements["elements_order"] !== "") {
								$elements_order = explode(",", $elements["elements_order"]);
								foreach ($elements_order as $position => $num_element) {
									if ($elements[$num_element]["id"] && $elements[$num_element]["label"]) {
										$velement = $elements[$num_element]["type"];
										if(strpos($velement,"vedette_ontologies") === 0){
											$velement = "vedette_ontologies";
										}
										$available_field_class_name = $vedette_composee->get_at_available_field_num($elements[$num_element]["available_field_num"]);
										if(empty($available_field_class_name['params'])){
											$available_field_class_name['params'] = array();
										}
										$vedette_element = new $velement($elements[$num_element]["available_field_num"], $elements[$num_element]["id"], $elements[$num_element]["label"], $available_field_class_name['params']);
										$vedette_composee->add_element($vedette_element, $subdivision, $position);
									}
								}
							}
						}
						$vedette_composee_id = $vedette_composee->save();
					}
					if ($vedette_composee_id) {
						vedette_link::save_vedette_link($vedette_composee, onto_common_uri::get_id($uri_item), TYPE_CONCEPT_PREFLABEL);
						
						if ($data["value"]) {
							$data_properties = array();
							if ($data["type"] == "http://www.w3.org/2000/01/rdf-schema#Literal") {
								$data_properties["type"] = "literal";
							} else {
								$data_properties["type"] = "uri";
							}
							$class_name = static::class;
							$datatypes[$property->uri][] = new $class_name($data["value"], $data["type"], $data_properties);
						}
					}
				}
			}
		}
		return $datatypes;
	}
}
