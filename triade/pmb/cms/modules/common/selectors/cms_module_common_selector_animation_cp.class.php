<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_animation_cp.class.php,v 1.2.6.1 2025/04/16 13:00:54 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

use Pmb\Animations\Orm\AnimationCustomFieldOrm;

class cms_module_common_selector_animation_cp extends cms_module_common_selector
{
    public function __construct($id = 0){
        parent::__construct($id);
    }

    public function get_form()
    {
        $form = parent::get_form();
        $form .= "
		<div class='row'>
			<div class='colonne3'>
                <label for='{$this->get_form_value_name("cp")}'>
                    {$this->format_text($this->msg['cms_module_common_selector_animation_cp_label'])}
				</label>
			</div>
			<div class='colonne_suite'>
                {$this->gen_select()}
			</div>
		</div>";

        return $form;
    }

    public function gen_select()
    {
        $customFieldsOrm = AnimationCustomFieldOrm::findAll();
        $select = "<select name='{$this->get_form_value_name("cp")}'>";
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

        return $select;
    }

    public function save_form()
    {
        $this->parameters['cp'] = $this->get_value_from_form("cp");
        return parent::save_form();
    }

    /*
     * Retourne la valeur sélectionné
     */
    public function get_value()
    {
        if (! $this->value) {
            $this->value = $this->parameters['cp'] ?? null;
        }
        return $this->value;
    }
}