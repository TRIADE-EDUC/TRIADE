<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_animation_cp_val.class.php,v 1.2 2023/08/17 09:47:54 dbellamy Exp $

use Pmb\Animations\Orm\AnimationCustomFieldOrm;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_selector_animation_cp_val extends cms_module_common_selector
{

    public function get_form()
    {
        $form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='{$this->get_form_value_name("cp")}'>
					    {$this->format_text($this->msg['cms_module_common_selector_animation_cp_val_cp_label'])}
					</label>
				</div>
				<div class='colonne-suite'>
					{$this->gen_select()}
				</div>
			</div>
			<div id='{$this->get_form_value_name("cp")}_values' class='row'></div>";
        $form .= parent::get_form();
        return $form;
    }

    public function gen_select()
    {
        $customFieldsOrm = AnimationCustomFieldOrm::findAll();
        $select = "<select
                    name='{$this->get_form_value_name("cp")}'
                    id='{$this->get_form_value_name("cp")}_select'
                    onchange='load_cp_val_{$this->get_form_value_name("cp")}(this.value)'>
                        <option value=''></option>";
        foreach ($customFieldsOrm as $customFieldOrm) {
            $selected = "";
            if (
                !empty($this->parameters) &&
                !empty($this->parameters['cp']) &&
                $customFieldOrm->idchamp == $this->parameters['cp']
                ) {
                    $selected = "selected='selected'";
                }

                $select .= sprintf('<option value="%s" %s>%s</option>', $customFieldOrm->idchamp, $selected, $this->format_text($customFieldOrm->titre));
        }
        $select .= "<select>";
        $select .= "
            <script>
				function load_cp_val_{$this->get_form_value_name("cp")}(id_cp){
					dojo.xhrGet({
						url : '{$this->get_ajax_link(array($this->class_name."_hash[]" => $this->hash))}&id_cp='+id_cp,
						handelAs : 'text/html',
						load : function(data){
							const node = dojo.byId('{$this->get_form_value_name("cp")}_values');
							if (node) {
							    node.innerHTML = data;
							} else {
							    node.innerHTML = 'no';
							}
						}
					});
				}
			</script>";

        if (!empty($this->parameters['cp'])) {
            $select .= "
			<script>
			    load_cp_val_{$this->get_form_value_name("cp")}({$this->parameters['cp']});
			</script>";
        }

        return $select;
    }

    public function save_form()
    {
        $this->parameters['cp'] = $this->get_value_from_form("cp");
        $this->parameters['cp_val'] = $this->get_value_from_form("cp_val");
        return parent::save_form();
    }

    /*
     * Retourne la valeur sélectionné
     */
    public function get_value()
    {
        if (! $this->value) {
            $this->value = $this->parameters;
        }
        return $this->value;
    }

    public function execute_ajax()
    {
        global $id_cp;

        $id_cp = intval($id_cp);

        $response = [
            "content" => "",
            "content-type" => "text/html",
        ];

        if (!$id_cp) {
            return $response;
        }

        $pp = new parametres_perso("anim_animation");
        $pp->get_values(0);

        $response['content'] = "
    		<div class='colonne3'>
    			<label for='{$this->get_form_value_name("cp_val")}'>
    			    {$this->format_text($this->msg['cms_module_common_selector_animation_cp_val_cp_val_label'])}
    			</label>
    		</div>
    		<div class='colonne-suite'>
    			{$pp->get_field_form($id_cp, $this->get_form_value_name("cp_val"), $this->parameters['cp_val'] ?? "")}
    		</div>
		";

        return $response;
    }
}