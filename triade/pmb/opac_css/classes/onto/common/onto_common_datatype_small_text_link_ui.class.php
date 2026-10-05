<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_small_text_link_ui.class.php,v 1.6.2.1.2.1 2025/02/06 10:53:23 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Common\Library\CSRF\CollectionCSRF;

/**
 * onto_common_datatype_small_text_link_ui
 */
class onto_common_datatype_small_text_link_ui extends onto_common_datatype_small_text_card_ui
{

    private static $default_type = "http://www.w3.org/2000/01/rdf-schema#Literal";

    /**
     *
     * @param string $item_uri
     * @param onto_property $property
     * @param onto_restriction $restrictions
     * @param [onto_common_datatype_small_text] $datas
     * @param string $instance_name
     * @param string $flag
     * @return mixed
     */
    public static function get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag)
    {
        global $charset, $ontology_tpl, $pmb_curl_timeout;

        $max = $restrictions->get_max();
        $form = $ontology_tpl['form_row_link'];
        $form = str_replace("!!onto_row_label!!", htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8'), ENT_QUOTES, $charset), $form);
        $form = str_replace("!!onto_input_type!!", htmlentities(self::$default_type, ENT_QUOTES, $charset), $form);

        $content = '';

        if ( !empty($datas) && is_array($datas) ) {
            $i = 1;
            $new_element_order = max(array_keys($datas));

            $form = str_replace("!!onto_new_order!!", $new_element_order, $form);

            foreach ($datas as $key => $data) {

                $row = $ontology_tpl['form_row_content'];

                if ($data->get_order()) {
                    $order = $data->get_order();
                } else {
                    $order = $key;
                }

                $inside_row = $ontology_tpl['form_row_content_small_text_link'];
                $inside_row = str_replace("!!onto_row_content_small_text_value!!", htmlentities($data->get_formated_value(), ENT_QUOTES, $charset), $inside_row);
                $inside_row = str_replace("!!onto_row_content_small_text_range!!", $property->range[0], $inside_row);
                $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

                $input = $ontology_tpl['form_row_content_input_open_link'];
                if ($new_element_order == $order) {
                    if ($restrictions->get_max() < $i || $restrictions->get_max() === - 1) {
                        $input .= $ontology_tpl['form_row_content_input_add_text_link'];
                    }
                }

                $row = str_replace("!!onto_row_inputs!!", $input, $row);
                $row = str_replace("!!onto_row_order!!", $order, $row);

                $content .= $row;
                $i ++;
            }
        } else {
            $form = str_replace("!!onto_new_order!!", "1", $form);

            $row = $ontology_tpl['form_row_content'];

            $inside_row = $ontology_tpl['form_row_content_small_text_link'];
            $inside_row = str_replace("!!onto_row_content_small_text_value!!", "", $inside_row);
            $inside_row = str_replace("!!onto_row_content_small_text_range!!", $property->range[0], $inside_row);
            $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

            $input = $ontology_tpl['form_row_content_input_open_link'];
            if ($restrictions->get_max() != 1) {
                $input .= $ontology_tpl['form_row_content_input_add_text_link'];
            }
            $row = str_replace("!!onto_row_inputs!!", $ontology_tpl['form_row_content_input_open_link'], $row);
            $row = str_replace("!!onto_row_order!!", "0", $row);

            $content .= $row;
        }

        $onto_rows = "";
        $onto_rows .= $content;
        $onto_rows .= $ontology_tpl['onto_script_small_text_link'];
        
        $collectionCSRF = new CollectionCSRF();
        $tokens = json_encode($collectionCSRF->getArrayTokens());
        
        $onto_rows = str_replace('!!tokens_csrf!!', $tokens, $onto_rows);
        
        $onto_rows = str_replace("!!pmb_curl_timeout!!", $pmb_curl_timeout, $onto_rows);
        $onto_rows = str_replace("!!csrf_token_id!!", $instance_name . '_' . $property->pmb_name, $onto_rows);

        $form = str_replace("!!input_add!!", $ontology_tpl['form_row_content_input_add_card'], $form);
        $form = str_replace("!!onto_row_max_card!!", $max, $form);

        $form = str_replace("!!onto_rows!!", $onto_rows, $form);
        $form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
        $form = str_replace("!!onto_row_id!!", $instance_name . '_' . $property->pmb_name, $form);

        return $form;
    }

    /**
     *
     * @param string $item_uri
     * @param onto_property $property
     * @param onto_restriction $restrictions
     * @param [onto_common_datatype_small_text] $datas
     * @param string $instance_name
     * @param string $flag
     * @return string
     */
    public static function get_validation_js($item_uri, $property, $restrictions, $datas, $instance_name, $flag)
    {
        global $msg, $pmb_curl_timeout;

        $mandatory = 0;
        if (!empty($property->pmb_extended['mandatory']) && $property->pmb_extended['mandatory']) {
            $mandatory = 1;
        }
        $collectionCSRF = new CollectionCSRF();
        return '{
			"message": "' . addslashes($property->get_label()) . '",
			"valid" : true,
			"is_required" : ' . $mandatory . ',
			"error": "",
            "get_csrf_token": function(){
                const tabTokens_' . $instance_name . '_' . $property->pmb_name . ' = ' . json_encode($collectionCSRF->getArrayTokens()) . ';
            },
			"check": function(){
                this.valid = true;
				var order = document.getElementById("' . $instance_name . '_' . $property->pmb_name . '_new_order").value;
				for (var i=0; i <= order ; i++){
					var node = document.getElementById("' . $instance_name . '_' . $property->pmb_name . '_"+i+"_value");
					if(node && node.value){
                        var csrf_token = tabTokens_' . $instance_name . '_' . $property->pmb_name . '[0];
                        tabTokens_' . $instance_name . '_' . $property->pmb_name . '.splice(0, 1);
                        var testlink = encodeURIComponent(node.value);
                        var req = new XMLHttpRequest();
                        req.open("GET", "./ajax.php?module=ajax&categ=chklnk&timeout=' . $pmb_curl_timeout . '&link="+testlink+"&csrf_token="+csrf_token, true);
                        req.onreadystatechange = function (aEvt) {
                            if (req.readyState == 4) {
                                if(req.status == 200){
                                    var type_status = req.responseText.substr(0,1);
                                    if(type_status != "2" || type_status != "3"){
                                        this.valid = false;
                                        this.error = "url_not_valid";
                                    }
                                }
                            }
                        }
                        req.send(null);
					}

                    if ( this.is_required && !node.value ) {
                        this.valid = false;
                        this.error = "min";
                    }
				}
				return this.valid;
			},
			"get_error_message": function(){
 				switch(this.error){
					case "url_not_valid" :
						this.message = "' . addslashes($msg['onto_error_url_not_valid']) . '";
						break;
					case "min" :
						this.message = "' . addslashes($msg['onto_error_no_minima']) . '";
						break;
 				}
				this.message = this.message.replace("%s","' . addslashes($property->get_label()) . '");
				return this.message;
			}
		}';
    }
}