<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_type_article_generic.class.php,v 1.4.18.1 2024/12/17 14:36:16 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_selector_type_article_generic extends cms_module_common_selector
{
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
     * Retourne le tableau des sous-selecteurs
     *
     * @return array
     */
    protected function get_sub_selectors()
    {
        return [
            'cms_module_common_selector_generic_article',
        ];
    }

    /**
     * Retourne le formulaire du selector
     *
     * @return void
     */
    public function get_form()
    {
        $form = parent::get_form();
        $form .= "
			<div id='type_editorial_fields'>
				<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_common_selector_article_generic_type_select'])."</label>
					</div>
					<div class='colonne-suite'>
					".$this->gen_select()."
					</div>
				</div>
			</div>";
        return $form;

    }

    /**
     * Création du sous-selecteur
     *
     * @return string
     */
    protected function gen_select()
    {
        //si on est en création de cadre
        if(!$this->id) {
            $this->parameters = [];
        }
        $select = "<select name='".$this->get_form_value_name("select_field")."' >";
        $query = "select * from cms_editorial_types, cms_editorial_custom where editorial_type_element = 'article_generic' and id_editorial_type=num_type order by titre ";
        $result = pmb_mysql_query($query);
        $select .= "
				<option value='0'>".$this->format_text($this->msg['cms_module_common_selector_article_generic_type_select_invit'])."</option>";
        if(pmb_mysql_num_rows($result)) {
            while($r = pmb_mysql_fetch_object($result)) {
                $select .= "
				<option value='".$r->idchamp."'".($r->idchamp == $this->parameters["type_editorial_field"] ? "selected='selected'" : "").">".$this->format_text($r->titre)."</option>";
            }
        } else {
            $select .= "
				<option value ='0'>".$this->format_text($this->msg['cms_module_common_selector_article_generic_type_no'])."</option>";
        }
        $select .= "</select>";

        return $select;
    }

    /**
     * Enregistrement du formulaire
     *
     * @return boolean
     */
    public function save_form()
    {
        $this->parameters["type_editorial_field"] = $this->get_value_from_form("select_field");
        return parent::save_form();
    }

    /**
     * Retourne la valeur sélectionné
     *
     * @return integer
     */
    public function get_value()
    {
        // recup id de l'article dans le sous selecteur
        if (!$this->value) {
            $query = "select id_editorial_type from cms_editorial_types where editorial_type_element = 'article_generic'";
            $result = pmb_mysql_query($query);
            if(pmb_mysql_num_rows($result)) {
                $fields = new cms_editorial_parametres_perso(pmb_mysql_result($result, 0, 0));

                if ($this->parameters['sub_selector']) {
                    $sub = new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
                    $fields->get_values($sub->get_value());
                    $this->value = $fields->values[$this->parameters['type_editorial_field']] ?? null;
                }
            }
        }
        return $this->value;
    }
}
