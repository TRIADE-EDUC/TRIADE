<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_animation.class.php,v 1.1 2021/03/26 08:51:56 qvarin Exp $

use Pmb\Animations\Orm\AnimationOrm;

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class cms_module_common_selector_animation extends cms_module_common_selector
{

    public function get_form()
    {
        $form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>" . $this->format_text($this->msg['cms_module_common_selector_animation_id_animation']) . "</label>
				</div>
				<div class='colonne-suite'>";
        $form .= $this->get_input();
        $form .= "
				</div>
			</div>";
        $form .= parent::get_form();
        return $form;
    }

    public function save_form()
    {
        $this->parameters = $this->get_value_from_form('id_animation');
        return parent::save_form();
    }

    protected function get_input()
    {
        $value = $this->get_value();
        if (empty($value)) {
            $value = "";
        }
        $template = '<input value="'. $value .'" id="animations" name="' . $this->get_form_value_name("id_animation") . '">';
        return $template;
    }

    /**
     * Retourne la valeur sélectionné
     */
    public function get_value()
    {
        if (! $this->value) {
            $this->value = $this->parameters;
        }
        return $this->value;
    }
}