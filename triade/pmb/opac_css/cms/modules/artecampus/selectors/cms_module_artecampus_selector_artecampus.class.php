<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_artecampus_selector_artecampus.class.php,v 1.1.2.1 2024/07/18 12:38:21 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_artecampus_selector_artecampus extends cms_module_common_selector
{
    /**
     * Retourne le formulaire d'administration du selector
     *
     * @return string
     */
    public function get_form()
    {
        return '
        <div class="row">
            <div class="colonne3">
                <label for="id_connector">' . $this->format_text($this->msg['id_connector']) . '</label>
            </div>
            <div class="colonne-suite">' . $this->get_input() . '</div>
        </div>' . parent::get_form();
    }

    /**
     * Retourne l'input
     *
     * @return string
     */
    protected function get_input()
    {
        return '<input
            value="'. $this->get_value() .'"
            id="id_connector"
            type="text"
            pattern="[1-9]{1}[0-9]*"
            required="required"
            name="' . $this->get_form_value_name("id_connector") . '"
            placeholder="' . $this->format_text($this->msg['id_connector_placeholder']) . '"
        />';
    }

    /**
     * Retourne la valeur du selector
     *
     * @return int
     */
    public function get_value()
    {
        $this->value = empty($this->parameters) ? "" : intval($this->parameters);
        return $this->value;
    }

    /**
     * Enregistre les paramètres du selector
     *
     * @return bool
     */
    public function save_form()
    {
        $this->parameters = $this->get_value_from_form('id_connector');
        return parent::save_form();
    }

}
