<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_animationslist_selector_type.class.php,v 1.3 2022/06/03 13:12:23 jparis Exp $
use Pmb\Animations\Orm\AnimationTypesOrm;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_animationslist_selector_type extends cms_module_common_selector
{

    public function __construct($id = 0)
    {
        parent::__construct($id);
    }

    public function get_form()
    {
        $form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>" . $this->format_text($this->msg['cms_module_animationslist_selector_type_id_type']) . "</label>
				</div>
				<div class='colonne-suite'>
                    <select  name='" . $this->get_form_value_name("id_type") . "[]' multiple='yes'>
                        ".$this->get_options_type()."
                    </select>
				</div>
			</div>";
        $form .= parent::get_form();
        return $form;
    }

    public function save_form()
    {
        $this->parameters = $this->get_value_from_form("id_type");
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

    private function get_options_type()
    {
        $options = "";
        $animationTypes = AnimationTypesOrm::findAll();
        $index = count($animationTypes);
        for ($i = 0; $i < $index; $i++) {
            $selected = "";
            if (is_array($this->parameters) && in_array($animationTypes[$i]->id_type,$this->parameters)) {
                $selected = "selected='selected'";                
            }
            $options .= "<option value='{$this->format_text($animationTypes[$i]->id_type)}' $selected>{$animationTypes[$i]->label}</option>";
        }
        return $options;
    }
}

