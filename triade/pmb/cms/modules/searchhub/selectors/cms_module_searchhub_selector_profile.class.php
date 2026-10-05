<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_searchhub_selector_profile.class.php,v 1.1.2.2 2024/12/30 16:11:34 qvarin Exp $

if (stristr($_SERVER["REQUEST_URI"], ".class.php")) {
    die("no access");
}

class cms_module_searchhub_selector_profile extends cms_module_common_selector
{
    public const DEFAULT_VALUE = '';

    /**
     * Constructeur
     *
     * @param integer $id (optional, default: 0)
     */
    public function __construct($id = 0)
    {
        parent::__construct($id);
        if (!isset($this->parameters)) {
            $this->parameters = ['profile' => self::DEFAULT_VALUE];
        }
    }

    /**
     * Retourne le formulaire de parametrage du selecteur
     *
     * @return string
     */
    public function get_form()
    {
        $form = <<<HTML
            <div class="row">
                <div class="row">
                    <div class="colonne3">
                        <label for="{$this->get_hash()}_profile">{$this->format_text($this->msg["cms_module_searchhub_selector_profile_label"])}</label>
                    </div>
                    <div class="colonne-suite">
                        <select name="{$this->get_hash()}_profile" required>{$this->get_options_profile()}</select>
                    </div>
                </div>
            </div>
            HTML;

        return $form . parent::get_form();
    }

    /**
     * Retourne les options du selecteur de profils
     *
     * @return string
     */
    private function get_options_profile()
    {
        $option = '<option value="%s" %s>%s</option>';

        if (empty($this->parameters['profile']) || $this->parameters['profile'] == self::DEFAULT_VALUE) {
            $options = sprintf(
                $option,
                $this->format_text(self::DEFAULT_VALUE),
                'selected="selected" disabled="disabled"',
                $this->format_text($this->msg["cms_module_searchhub_selector_profile_default"])
            );
        } else {
            $options = sprintf(
                $option,
                $this->format_text(self::DEFAULT_VALUE),
                'disabled="disabled"',
                $this->format_text($this->msg["cms_module_searchhub_selector_profile_default"])
            );
        }

        foreach ($this->fetch_profiles() as $key => $profil) {
            $options .= sprintf(
                $option,
                $this->format_text($profil['id']),
                $this->parameters['profile'] == $profil['id'] ? 'selected="selected"' : '',
                $this->format_text($profil['name'])
            );
        }

        return $options;
    }

    /**
     * Retourne la liste des profils
     *
     * @return array
     */
    private function fetch_profiles()
    {
        $query = "SELECT managed_module_box FROM cms_managed_modules WHERE managed_module_name = '".addslashes($this->module_class_name)."'";
        $result = pmb_mysql_query($query);

        if (pmb_mysql_num_rows($result)) {
            $managed_module_box = pmb_mysql_result($result,0,0);
            $managed_module_box = unserialize($managed_module_box);
            if (!empty($managed_module_box) && is_array($managed_module_box)) {
                return $managed_module_box['module']['profiles'] ?? [];
            }
        }

        return [];
    }

    /**
     * Enregistre le formulaire de parametrage du selecteur
     *
     * @return boolean
     */
    public function save_form()
    {
        $profile = $this->get_value_from_form('profile') ?? self::DEFAULT_VALUE;
        $this->parameters['profile'] = intval($profile);

        return parent::save_form();
    }

    /**
     * Retourne la valeur du selecteur
     *
     * @return int|null
     */
    public function get_value()
    {
        if (!isset($this->value)) {
            $this->value = $this->parameters['profile'];
        }
        return $this->value;
    }
}
