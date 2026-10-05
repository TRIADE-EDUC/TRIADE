<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_type_editorial.class.php,v 1.14.2.1 2024/12/17 14:36:16 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_selector_type_editorial extends cms_module_common_selector
{
    public $cms_module_common_selector_type_editorial_type;

    /**
     * Constructeur
     *
     * @param integer $id (optional, default: 0)
     */
    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->once_sub_selector = true;
    }

    /**
     * Retourne les sous-selecteurs
     *
     * @return string
     */
    protected function get_sub_selectors()
    {
        return [
            'cms_module_common_selector_env_var',
        ];
    }

    /**
     * Générer le formulaire
     *
     * @return string
     */
    public function get_form()
    {
        $form = parent::get_form();
        $form .= "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_type_editorial_label'])."</label>
				</div>
				<div class='colonne-suite'>";
        $form .= $this->gen_select();
        $form .= "
				</div>
			</div>
			<div id='type_editorial_fields'>
				<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_common_selector_type_editorial_fields_label'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='".$this->get_form_value_name("select_field")."' >";
        $fields = new cms_editorial_parametres_perso($this->parameters["type_editorial"]);
        $form .= $fields->get_selector_options($this->parameters["type_editorial_field"]);
        $form .= "
						</select>
					</div>
				</div>
			</div>";
        return $form;

    }

    /**
     * Générer le select
     *
     * @return string
     */
    protected function gen_select()
    {
        //si on est en création de cadre
        if(!$this->id) {
            $this->parameters = [
                    'type_editorial' => '',
                    'type_editorial_field' => '',
            ];
        }
        $select = "<select name='".$this->get_form_value_name($this->cms_module_common_selector_type_editorial_type)."'
			onchange=\"cms_type_fields(this.value);\" >
		";

        $types = new cms_editorial_types($this->cms_module_common_selector_type_editorial_type);
        $select .= $types->get_selector_options($this->parameters["type_editorial"]);
        $select .= "</select>
		<script>
			function cms_type_fields(id_type){
				dojo.xhrGet({
					url : '".$this->get_ajax_link([$this->class_name."_hash[]" => $this->hash])."&id_type='+id_type,
					handelAs : 'text/html',
					load : function(data){
						dojo.byId('type_editorial_fields').innerHTML = data;
					}
				});
			}
		</script>";

        return $select;
    }

    /**
     * Sauvegarde le formulaire
     *
     * @return boolean
     */
    public function save_form()
    {
        $this->parameters["type_editorial_field"] = $this->get_value_from_form("select_field");
        $this->parameters["type_editorial"] = $this->get_value_from_form($this->cms_module_common_selector_type_editorial_type);
        return parent::save_form();
    }

    /**
     * Retourne la valeur sélectionné
     *
     * @return string|array
     */
    public function get_value()
    {
        if(!$this->value) {
            $fields = new cms_editorial_parametres_perso($this->parameters["type_editorial"]);
            if(!empty($this->parameters['sub_selector'])) {
                $sub = new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
                $fields->get_values($sub->get_value());
                if(isset($fields->values[$this->parameters['type_editorial_field']])) {
                    $this->value = $fields->values[$this->parameters['type_editorial_field']];
                } else {
                    $this->value = '';
                    $query = "select id_editorial_type from cms_editorial_types where editorial_type_element = '".$this->cms_module_common_selector_type_editorial_type."_generic'";
                    $result = pmb_mysql_query($query);
                    if(pmb_mysql_num_rows($result)) {
                        $fields_type = new cms_editorial_parametres_perso(pmb_mysql_result($result, 0, 0));
                        $fields_type->get_values(intval($sub->get_value() ?? 0));
                        if(isset($fields_type->values[$this->parameters['type_editorial_field']])) {
                            $this->value = $fields_type->values[$this->parameters['type_editorial_field']];
                        }
                    }
                }
            } else {
                $this->value = [
                    'type' => $this->parameters['type_editorial'],
                    'field' => $this->parameters['type_editorial_field'],
                ];
            }
        }
        return $this->value;
    }
}
