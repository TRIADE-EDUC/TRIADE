<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_datatype_preflabel_ui.class.php,v 1.12 2023/07/27 06:57:38 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($include_path . '/templates/onto/skos/onto_skos_concept_datatype_preflabel_card_ui.tpl.php');
require_once ($class_path . '/vedette/vedette_composee.class.php');

class onto_skos_concept_datatype_preflabel_ui extends onto_common_datatype_small_text_card_ui
{

    public static function get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag)
    {
        global $msg, $charset, $ontology_tpl, $composed, $grammar;

        $duplication = false;
        if (isset($property->get_framework_params()->action) && $property->get_framework_params()->action == "duplicate") {
            $duplication = true;
        }
        $object_id = onto_common_uri::get_id($item_uri);
        if (onto_common_uri::is_temp_uri($item_uri) && ! empty($property->get_framework_params()->id) && $duplication) {
            $object_id = $property->get_framework_params()->id;
        }

        if ($object_id)
            $vedette_id = vedette_link::get_vedette_id_from_object($object_id, TYPE_CONCEPT_PREFLABEL);
        else
            $vedette_id = 0;

        if (! $grammar)
            $grammar = 'rameau';
        $vedette = new vedette_composee($vedette_id, $grammar);
        if ($duplication) {
            $vedette->set_id(0);
        }
        $vedette_ui = new vedette_ui($vedette);
        $form = $ontology_tpl['skos_concept_card_ui_wrapper'];

        // Si on a une vedette composée, on ne veut pas de valeur dans les champs classiques
        if ($composed == 'composed' || $vedette_id) {
            $datas = array();
        }

        $form = str_replace("!!skos_concept_card_ui_parent_form!!", parent::get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag), $form);
        $form = str_replace("!!skos_concept_card_ui_derived_form!!", $vedette_ui->get_form($property->pmb_name, 0, $instance_name, $property->range[0]), $form);

        $form = str_replace("!!onto_row_label!!", htmlentities($property->get_label(), ENT_QUOTES, $charset), $form);
        $form = str_replace("!!instance_name!!", htmlentities($instance_name, ENT_QUOTES, $charset), $form);
        $form = str_replace("!!property_name!!", htmlentities($property->pmb_name, ENT_QUOTES, $charset), $form);

        if ($composed == 'composed' || $vedette_id) {

            $form = str_replace("!!skos_concept_card_ui_btn_value!!", $msg['skos_concept_card_ui_btn_parent'], $form);
            $form = str_replace("!!skos_concept_card_ui_parent_visible!!", "style='display:none'", $form);
            $form = str_replace("!!skos_concept_card_ui_derived_visible!!", "", $form);
            $form = str_replace("!!is_composed!!", "composed", $form);
        } else {

            $form = str_replace("!!skos_concept_card_ui_btn_value!!", $msg['skos_concept_card_ui_btn_derived'], $form);
            $form = str_replace("!!skos_concept_card_ui_parent_visible!!", "", $form);
            $form = str_replace("!!skos_concept_card_ui_derived_visible!!", "style='display:none'", $form);
            $form = str_replace("!!is_composed!!", "", $form);
        }

        return $form;
    }

    public function get_display($datas, $property, $instance_name)
    {}

    /**
     * Retourne un object JSON avec 2 méthodes check et get_error_message
     *
	 * @param property property la propriété concernée
	 * @param restriction $restrictions le tableau des restrictions associées à la propriété
	 * @param array datas le tableau des datatypes
	 * @param string instance_uri URI de l'instance
	 * @param string flag Flag
     *            
     * @return string
     * @static
     * @access public
     */
    public static function get_validation_js($item_uri, $property, $restrictions, $datas, $instance_name, $flag)
    {
        global $msg;

        return '{
			"error": "",
			"message": "",
			"subdivision_error": "",
			"check": function(){
				var is_composed = document.getElementById("' . $instance_name . '_' . $property->pmb_name . '_is_composed");
				if (is_composed && is_composed.value) {
					if (!document.getElementById("' . $instance_name . '_' . $property->pmb_name . '_composed_0_vedette_composee_apercu").value) {
						this.error = "min";
						return false;
					}
						
					var subdivisions = document.getElementById("' . $instance_name . '_' . $property->pmb_name . '_composed_0_vedette_composee_subdivisions");
					
					for (var i in subdivisions.childNodes) {
						if ((subdivisions.childNodes[i].nodeType == 1) && (subdivisions.childNodes[i].getAttribute("class") == "vedette_composee_subdivision")) {
							var nb_elements = this.get_nb_elements_in_subdivision(subdivisions.childNodes[i]);
							
							if (subdivisions.childNodes[i].getAttribute("cardmin") && (subdivisions.childNodes[i].getAttribute("cardmin") > nb_elements)) {
								var subdivision_label = document.getElementById(subdivisions.childNodes[i].getAttribute("id") + "_label").innerHTML;
								
								this.subdivision_error = subdivision_label;
								this.error = "min";
								return false;
							}
							if (subdivisions.childNodes[i].getAttribute("cardmax") && (subdivisions.childNodes[i].getAttribute("cardmax") < nb_elements)) {
								this.error = "max";
								return false;
							}
						}
					}
					return true;
				} else {
					return this.parent.check();
				}
			},
			"get_error_message": function(){
				var is_composed = document.getElementById("' . $instance_name . '_' . $property->pmb_name . '_is_composed");
				
				if (is_composed && is_composed.value) {
	 				switch(this.error){
	 					case "min" :
							this.message = "' . addslashes($msg['onto_error_no_minima']) . '";
							break;
						case "max" : 
							this.message = "' . addslashes($msg['onto_error_too_much_values']) . '";
							break;
	 				}
					if (this.subdivision_error) {
						this.message = this.message.replace("%s","' . addslashes($property->get_label()) . ' (" + this.subdivision_error + ")");
					} else {
						this.message = this.message.replace("%s","' . addslashes($property->get_label()) . '");
					}			
					return this.message;
				} else {
					return this.parent.get_error_message();
				}	
			},
			"get_nb_elements_in_subdivision" : function(subdivision){
				var nb_elements = 0;
				
				for (var i in subdivision.childNodes) {
					if (subdivision.childNodes[i].nodeType == 1 && (subdivision.childNodes[i].getAttribute("class") == "vedette_composee_element")) {
						var text_id = subdivision.childNodes[i].getAttribute("id") + "_label";
						
						if (document.getElementById(text_id).value) {
							nb_elements++;
						}
					}
				}
				return nb_elements;
			},
			"parent": ' . parent::get_validation_js($item_uri, $property, $restrictions, $datas, $instance_name, $flag) . ' 	
		}';
    }
}
