<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parametres_perso.class.php,v 1.149.4.5 2025/04/25 14:16:44 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

// Gestion des champs personnalisés
global $include_path;
global $class_path;

require_once $include_path . "/templates/parametres_perso.tpl.php";
require_once $include_path . "/parser.inc.php";
require_once $include_path . "/fields_empr.inc.php";
require_once $include_path . "/datatype.inc.php";
require_once $class_path . "/translation.class.php";
require_once $class_path . "/onto/onto_parametres_perso.class.php";
require_once $class_path . "/authorities_collection.class.php";

class parametres_perso
{

    public $prefix;

    public $no_special_fields;

    public $error_message;

    public $values;

    public $base_url;

    public $t_fields;

    public $option_visibilite = array();

    public $list_values = null;

    public static $fields = array();

    public static $st_fields = array();

    protected static $out_values = array();

    /**
     * Le champ personnalisé a une option import
     * @var integer
     */
    protected $hasImportField = 0;

    /**
     * Constructeur
     *
     * @param string $prefix : type de champ Perso
     * @param string $base_url : l'url a appeler pour les formulaires de gestion
     * @param array $option_visibilite : tableau des options à afficher
     */
    public function __construct($prefix, $base_url = "", $option_visibilite = array())
    {
        global $_custom_prefixe_, $charset;

        $this->option_visibilite = $option_visibilite;

        $this->prefix = $prefix;
        $this->base_url = $base_url;
        $_custom_prefixe_ = $prefix;

        // Lecture des champs
        $this->no_special_fields = 0;
        $this->t_fields = array();
        $this->hasImportField = ('notices' == $this->prefix) ? 1 : 0;
        if (! isset(self::$st_fields[$this->prefix])) {
            $requete = "select * from " . $this->prefix . "_custom order by ordre";
            $resultat = pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat) == 0) {
                self::$st_fields[$this->prefix] = false;
            } else {
                while ($r = pmb_mysql_fetch_object($resultat)) {
                    self::$st_fields[$this->prefix][$r->idchamp]["DATATYPE"] = $r->datatype;
                    self::$st_fields[$this->prefix][$r->idchamp]["NAME"] = $r->name;
                    self::$st_fields[$this->prefix][$r->idchamp]["TITRE"] = $r->titre;
                    self::$st_fields[$this->prefix][$r->idchamp]["TYPE"] = $r->type;
                    self::$st_fields[$this->prefix][$r->idchamp]["OPTIONS"][0] = _parser_text_no_function_("<?xml version='1.0' encoding='" . $charset . "'?>\n" . $r->options, "OPTIONS");
                    if (! isset(self::$st_fields[$this->prefix][$r->idchamp]["OPTIONS"][0]["REPEATABLE"][0]["value"])) {
                        self::$st_fields[$this->prefix][$r->idchamp]["OPTIONS"][0]["REPEATABLE"][0]["value"] = 0;
                    }
                    self::$st_fields[$this->prefix][$r->idchamp]["MANDATORY"] = $r->obligatoire;
                    self::$st_fields[$this->prefix][$r->idchamp]["ORDER"] = $r->ordre;
                    self::$st_fields[$this->prefix][$r->idchamp]["OPAC_SHOW"] = $r->multiple;
                    self::$st_fields[$this->prefix][$r->idchamp]["SEARCH"] = $r->search;
                    self::$st_fields[$this->prefix][$r->idchamp]["EXPORT"] = $r->export;
                    self::$st_fields[$this->prefix][$r->idchamp]["IMPORT"] = 0;
                    if ($this->hasImportField) {
                        self::$st_fields[$this->prefix][$r->idchamp]["IMPORT"] = $r->import;
                    }
                    self::$st_fields[$this->prefix][$r->idchamp]["FILTERS"] = $r->filters;
                    self::$st_fields[$this->prefix][$r->idchamp]["EXCLUSION"] = $r->exclusion_obligatoire;
                    self::$st_fields[$this->prefix][$r->idchamp]["POND"] = $r->pond;
                    self::$st_fields[$this->prefix][$r->idchamp]["OPAC_SORT"] = $r->opac_sort;
                    self::$st_fields[$this->prefix][$r->idchamp]["COMMENT"] = $r->comment;
                    self::$st_fields[$this->prefix][$r->idchamp]["NUM_TYPE"] = $r->num_type ?? 0;
                }
                pmb_mysql_free_result($resultat);
            }
        }
        if (self::$st_fields[$this->prefix] == false) {
            $this->no_special_fields = 1;
        } else {
            $this->t_fields = self::$st_fields[$this->prefix];
        }
    }

    /**
     * Affichage de l'écran de gestion des paramètres perso (la liste de tous les champs définis)
     *
     * @return string
     */
    public function show_field_list()
    {
        list_custom_fields_ui::set_prefix($this->prefix);
        list_custom_fields_ui::set_option_visibilite($this->option_visibilite);
        $list_custom_fields_ui = new list_custom_fields_ui();
        return $list_custom_fields_ui->get_display_list();
    }

    public function gen_liste_field($select_name = "p_perso_liste", $selected_id = 0, $msg_no_select = '')
    {
        global $msg;

        $onchange = "";
        $requete = "select idchamp, name, titre, type, datatype, multiple, obligatoire, ordre ,search, export,exclusion_obligatoire, opac_sort from " . $this->prefix . "_custom order by ordre";
        return gen_liste($requete, "idchamp", "titre", $select_name, $onchange, $selected_id, 0, $msg["parperso_no_field"], 0, $msg_no_select, 0);
    }

    protected function get_classements_options_list()
    {
        global $charset;

        $options_list = '';

        $query = "select distinct custom_classement from " . $this->prefix . "_custom where custom_classement <> '' order by custom_classement";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $options_list .= "<option value='" . htmlentities($row->custom_classement, ENT_QUOTES, $charset) . "'>" . htmlentities($row->custom_classement, ENT_QUOTES, $charset) . "</option>";
            }
        }
        return $options_list;
    }

    protected function check_visibility_option($name)
    {
        if (! empty($this->option_visibilite[$name]) && $this->option_visibilite[$name] == 'none') {
            return false;
        }
        return true;
    }

    public function get_edit_content_form($idchamp = 0)
    {
        global $msg, $charset;
        global $include_path;
        global $type_list_empr;
        global $datatype_list;
        if ($idchamp != 0 and $idchamp != "") {
            $requete = "select * from " . $this->prefix . "_custom where idchamp=$idchamp";
            $resultat = pmb_mysql_query($requete) or die(pmb_mysql_error());
            $r = pmb_mysql_fetch_object($resultat);

            $name = $r->name;
            $titre = $r->titre;
            $type = $r->type;
            $datatype = $r->datatype;
            $options = $r->options;
            $multiple = $r->multiple;
            $obligatoire = $r->obligatoire;
            $ordre = $r->ordre;
            $search = $r->search;
            $export = $r->export;
            $import = $this->hasImportField ? $r->import : '';
            $filters = $r->filters;
            $exclusion = $r->exclusion_obligatoire;
            $pond = $r->pond;
            $opac_sort = $r->opac_sort;
            $comment = $r->comment;
            $classement = $r->custom_classement;
        } else {
            $name = '';
            $titre = '';
            $type = '';
            $datatype = '';
            $options = '';
            $multiple = '';
            $obligatoire = '';
            $ordre = '';
            $search = '';
            $export = '';
            $import = '';
            $filters = '';
            $exclusion = '';
            $pond = '';
            $opac_sort = '';
            $comment = '';
            $classement = '';
        }
        $interface_content_form = new interface_content_form(static::class);
        $interface_content_form->add_element('name', 'parperso_field_name')
            ->add_input_node('text', $name)
            ->set_class('saisie-20em');
        $interface_content_form->add_element('titre', 'parperso_field_title')
            ->add_input_node('text', $titre)
            ->set_class('saisie-30em')
            ->set_attributes(array('data-translation-fieldname' => 'titre'));
        $interface_content_form->add_element('comment', '707')
        	->add_textarea_node($comment);

        // Liste des types
        $t_list = "<select id='type' name='type'>\n";
        reset($type_list_empr);
        foreach ($type_list_empr as $key => $val) {
            $t_list .= "<option value='" . $key . "'";
            if ($type == $key) {
                $t_list .= " selected";
            }
            $t_list .= ">" . htmlentities($val, ENT_QUOTES, $charset) . "</option>\n";
        }
        $t_list .= "</select>\n";
        $onclick = "openPopUp('" . $include_path . "/options_empr/options.php?name=&type='+this.form.type.options[this.form.type.selectedIndex].value+'&_custom_prefixe_=" . $this->prefix . "','options');";
        $interface_content_form->add_element('type', 'parperso_input_type')
            ->add_html_node($t_list . "&nbsp;<input type='button' class='bouton' value='" . $msg['parperso_options_edit'] . "' onClick=\"" . $onclick . "\"/>");

        // Liste des types de données
        $t_list = "<select id='datatype' name='datatype'>\n";
        reset($datatype_list);
        foreach ($datatype_list as $key => $val) {
            $t_list .= "<option value='" . $key . "'";
            if ($datatype == $key) {
                $t_list .= " selected";
            }
            $t_list .= ">" . htmlentities($val, ENT_QUOTES, $charset) . "</option>\n";
        }
        $t_list .= "</select>\n";
        $interface_content_form->add_element('datatype', 'parperso_data_type')
            ->add_html_node($t_list);

        if ($this->check_visibility_option('multiple')) {
            $interface_content_form->add_element('multiple', '', 'flat')
                ->add_input_node('boolean', $multiple)
                ->set_label_code((strpos($this->prefix, "gestfic") !== false ? 'parperso_fiche_visibility' : 'parperso_opac_visibility'));
        }
        if ($this->check_visibility_option('opac_sort')) {
            $interface_content_form->add_element('opac_sort', '', 'flat')
                ->add_input_node('boolean', $opac_sort)
                ->set_label_code('parperso_opac_sort');
        }
        if ($this->check_visibility_option('obligatoire')) {
            $interface_content_form->add_element('obligatoire', '', 'flat')
                ->add_input_node('boolean', $obligatoire)
                ->set_label_code('parperso_mandatory');
        }
        if ($this->check_visibility_option('search')) {
            $interface_content_form->add_element('search', '', 'flat')
                ->add_input_node('boolean', $search)
                ->set_label_code('parperso_field_search');
        }
        if ($this->check_visibility_option('export')) {
            $interface_content_form->add_element('export', '', 'flat')
                ->add_input_node('boolean', $export)
                ->set_label_code('parperso_exportable');
        }
        if ($this->hasImportField && $this->check_visibility_option('import')) {
            $interface_content_form->add_element('import', '', 'flat')
                ->add_input_node('boolean', $import)
                ->set_label_code('parperso_importable');
        }
        if ($this->check_visibility_option('filters')) {
            $interface_content_form->add_element('filters', '', 'flat')
                ->add_input_node('boolean', $filters)
                ->set_label_code('parperso_filters');
        }
        if ($this->check_visibility_option('exclusion')) {
            $interface_content_form->add_element('exclusion', '', 'flat')
                ->add_input_node('boolean', $exclusion)
                ->set_label_code('parperso_exclusion');
        }

        $html_node = "
        <input list='custom_classements' class='saisie-30emr' id='classement' type='text' name='classement' value='" . htmlentities($classement, ENT_QUOTES, $charset) . "' completion='custom_classements' autocomplete='off' param1='" . $this->prefix . "'/>
        <datalist id='custom_classements'>
            " . $this->get_classements_options_list() . "
        </datalist>";
        $interface_content_form->add_element('classement', 'parperso_field_classement')
            ->add_html_node($html_node);

        $interface_content_form->add_element('pond', 'parperso_field_pond')
            ->add_input_node('integer', $pond);

        $interface_content_form->add_element('idchamp')
            ->add_input_node('hidden', $idchamp);
        $interface_content_form->add_element('_options')
            ->add_input_node('hidden', $options);
        if ($options != "") {
            $param = _parser_text_no_function_("<?xml version='1.0' encoding='" . $charset . "'?>\n" . $options, "OPTIONS");
            $interface_content_form->add_element('_for')
                ->add_input_node('hidden', $param["FOR"]);
        } else {
            $interface_content_form->add_element('_for')
                ->add_input_node('hidden', '');
        }
        $interface_content_form->add_element('ordre')
            ->add_input_node('hidden', $ordre);

        return $interface_content_form->get_display();
    }

    // Affichage du formulaire d'édition d'un champ perso
    public function show_edit_form($idchamp = 0)
    {
        global $form_edit;
        global $include_path;
        global $msg;

        if ($idchamp != 0 and $idchamp != "") {
            $name = $this->get_field_name_from_id($idchamp);
            $form_edit = str_replace("!!form_titre!!", sprintf($msg["parperso_field_edition"], $name), $form_edit);
            $form_edit = str_replace("!!action!!", "update", $form_edit);
            $form_edit = str_replace("!!supprimer!!", "&nbsp;<input type='button' class='bouton' value='" . $msg["63"] . "' onClick=\"if (confirm('" . $msg["parperso_delete_field"] . "')) { this.form.action.value='delete'; this.form.submit();} else return false;\">", $form_edit);
        } else {
            $form_edit = str_replace("!!form_titre!!", $msg["parperso_create_new_field"], $form_edit);
            $form_edit = str_replace("!!action!!", "create", $form_edit);
            $form_edit = str_replace("!!supprimer!!", "", $form_edit);
        }
        $form_edit = str_replace("!!content_form!!", $this->get_edit_content_form($idchamp), $form_edit);

        $form_edit = str_replace("!!prefix!!", $this->prefix, $form_edit);
        $form_edit = str_replace("!!base_url!!", $this->base_url, $form_edit);

        echo $form_edit;

        $translation = new translation($idchamp, $this->prefix . '_custom');
        print $translation->connect('parperso_form');
    }

    // Création d'une erreur si options non valides ou formulaires de création d'un champ mal rempli
    public function make_error($message)
    {
        global $msg;
        error_message_history($msg["540"], $message, 1);
        exit();
    }

    // Validation du formulaire de création
    public function check_form()
    {
        global $action, $msg, $idchamp;
        global $datatype_list, $type_list_empr;

        global $name, $titre, $type, $_for, $datatype, $multiple, $obligatoire, $exclusion, $search, $export;
        global $import, $filters, $pond, $opac_sort, $comment, $classement;

        $idchamp = intval($idchamp);

        // Vérification conformité du champ name
        if (! preg_match("/^[A-Za-z][A-Za-z0-9_]*$/", $name)) {
            $this->make_error(sprintf($msg["parperso_check_field_name"], $name));
        }
        // On vérifie que le champ name ne soit pas déjà existant
        if ($action == "update") {
            $requete = "select idchamp from " . $this->prefix . "_custom where name='$name' and idchamp<>$idchamp";
        } else {
            $requete = "select idchamp from " . $this->prefix . "_custom where name='$name'";
        }
        $resultat = pmb_mysql_query($requete);
        if (pmb_mysql_num_rows($resultat) > 0) {
            $this->make_error(sprintf($msg["parperso_check_field_name_already_used"], $name));
        }

        $titre = ! empty($titre) ? $titre : $name;
        if (! isset($type_list_empr[$type])) {
            $this->make_error($msg["parperso_check_type"]);
        }
        $_for = isset($_for) ? $_for : '';
        if ($_for != $type) {
            $this->make_error($msg["parperso_check_type"]);
        }
        if (! isset($datatype_list[$datatype])) {
            $this->make_error($msg["parperso_check_type"]);
        }
        $multiple = isset($multiple) ? intval($multiple) : 0;
        $obligatoire = isset($obligatoire) ? intval($obligatoire) : 0;
        $exclusion = isset($exclusion) ? intval($exclusion) : 0;
        $search = isset($search) ? intval($search) : 0;
        $export = isset($export) ? intval($export) : 0;
        $import = isset($import) ? intval($import) : 0;
        $filters = isset($filters) ? intval($filters) : 0;
        $pond = isset($pond) ? intval($pond) : 0;
        $opac_sort = isset($opac_sort) ? intval($opac_sort) : 0;
        $comment = isset($comment) ? $comment : '';
        $classement = isset($classement) ? $classement : '';

        // TODO vérifier classement
    }

    // Validation des valeurs des champs soumis lors de la saisie d'une fiche emprunteur ou autre...
    public function check_submited_fields()
    {
        global $chk_list_empr, $charset;

        $nberrors = 0;
        $this->error_message = "";

        if (! $this->no_special_fields) {
            reset($this->t_fields);
            foreach ($this->t_fields as $key => $val) {
                $check_message = "";
                $field = array();
                $field["ID"] = $key;
                $field["NAME"] = $this->t_fields[$key]["NAME"];
                $field["MANDATORY"] = $this->t_fields[$key]["MANDATORY"];
                $field["ALIAS"] = $this->t_fields[$key]["TITRE"];
                $field["OPTIONS"] = $this->t_fields[$key]["OPTIONS"];
                $field["DATATYPE"] = $this->t_fields[$key]["DATATYPE"];
                $field["PREFIX"] = $this->prefix;
                $field["SEARCH"] = $this->t_fields[$key]["SEARCH"];
                $field["EXPORT"] = $this->t_fields[$key]["EXPORT"];
                $field["FILTERS"] = (! empty($this->t_fields[$key]["FILTERS"]) ? $this->t_fields[$key]["FILTERS"] : '');
                $field["EXCLUSION"] = $this->t_fields[$key]["EXCLUSION"];
                $field["OPAC_SORT"] = $this->t_fields[$key]["OPAC_SORT"];
                $field["COMMENT"] = $this->t_fields[$key]["COMMENT"];
                global ${$val["NAME"]};
                $field['VALUES'] = ${$val["NAME"]};
                eval($chk_list_empr[$this->t_fields[$key]["TYPE"]] . "(\$field,\$check_message);");
                if ($check_message != "") {
                    $nberrors ++;
                    $this->error_message .= "<p>" . $check_message . "</p>";
                }
            }
        }
        return $nberrors;
    }

    // Presence ou nom de valeurs lors de la saisie
    public function presence_submited_fields()
    {
        global $chk_list_empr, $charset;

        if (! $this->no_special_fields) {
            reset($this->t_fields);
            foreach ($this->t_fields as $key => $val) {
                $field_name = $this->t_fields[$key]["NAME"];
                global ${$field_name};
                $field = ${$field_name};
                if ($field[0])
                    return true;
            }
        }
        return false;
    }

    // Presence ou nom de valeurs lors de la saisie dans les champs exclus
    public function presence_exclusion_fields()
    {
        global $chk_list_empr, $charset;

        $exclu_tab = array();
        if (! $this->no_special_fields) {
            reset($this->t_fields);
            foreach ($this->t_fields as $key => $val) {
                if ($this->t_fields[$key]["EXCLUSION"])
                    $exclu_tab[] = $this->t_fields[$key];
            }
            if (is_array($exclu_tab)) {
                foreach ($exclu_tab as $key => $val) {
                    $field_name = $exclu_tab[$key]["NAME"];
                    global ${$field_name};
                    $field = ${$field_name};
                    if ($field[0])
                        return true;
                }
            }
            return false;
        }
        return false;
    }

    // retourne la liste des valeurs des champs perso cherchable d'une notice
    public function get_fields_recherche($id)
    {
        $return_val = '';
        $this->get_values($id);
        foreach ($this->values as $field_id => $vals) {
            if ($this->t_fields[$field_id]["SEARCH"]) {
                foreach ($vals as $value) {
                    $return_val .= $this->get_formatted_output(array($value), $field_id) . ' '; // Sa valeur
                }
            }
        }
        return stripslashes($return_val);
    }

    // retourne la liste des valeurs des champs perso cherchable d'une notice sous forme d'un tableau par champ perso
    public function get_fields_recherche_mot($id)
    {
        $return_val = array();
        $this->get_values($id);
        foreach ($this->values as $field_id => $vals) {
            if ($this->t_fields[$field_id]["SEARCH"]) {
                foreach ($vals as $value) {
				    if (!isset($return_val[$field_id])) {
				        $return_val[$field_id] = '';
				    }
                    $return_val[$field_id] .= strip_tags(stripslashes($this->get_formatted_output(array($value), $field_id))) . ' '; // Sa valeur
                }
            }
        }
        return $return_val;
    }

    // retourne la liste des valeurs des champs perso cherchable d'une notice sous forme d'un tableau par champ perso
    public function get_fields_recherche_mot_array($id)
    {
        $return_val = array();
        $this->get_values($id);
        foreach ($this->values as $field_id => $vals) {
            if ($this->t_fields[$field_id]["SEARCH"]) {
                foreach ($vals as $value) {
                    $return_val[$field_id][] = stripslashes($this->get_formatted_output(array($value), $field_id)) . ' '; // Sa valeur
                    if ($this->t_fields[$field_id]["TYPE"] == "query_auth") {
                        $return_val[$field_id] = $this->get_enhanced_values($return_val[$field_id], $value, $field_id);
                    }
                }
            }
        }
        return $return_val;
    }

    protected function get_enhanced_values($values, $value, $field_id)
    {
        global $pmb_perso_sep;

        $field_id = intval($field_id);
        $field = static::$fields[$this->prefix][$field_id];
        switch ($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]) {
            case 2: // categories
                $thes = thesaurus::getByEltId($value);
                $q = "select id_noeud from noeuds where num_thesaurus = '" . $thes->id_thesaurus . "' AND num_renvoi_voir = '" . $value . "'";
                $res = pmb_mysql_query($q);
                if (pmb_mysql_num_rows($res)) {
                    while ($row = pmb_mysql_fetch_assoc($res)) {
                        $values[] = stripslashes($this->get_formatted_output(array($row["id_noeud"]), $field_id)) . ' ';
                    }
                }
                break;
            case 9: // concepts
                if (! intval($value)) {
                    $value = onto_common_uri::get_id($value);
                }
                if (! $value) {
                    break;
                }
                $aut = authorities_collection::get_authority(AUT_TABLE_CONCEPT, $value);
                $formated_value = implode($pmb_perso_sep, $aut->get_altlabel());
                if ($formated_value) {
                    $values[] = $formated_value;
                }
                break;
        }
        return $values;
    }

    // Enregistrement des champs perso soumis lors de la saisie d'une fichie emprunteur ou autre...
    public function rec_fields_perso($id)
    {
        // Enregistrement des champs personalisés
        $requete = "delete from " . $this->prefix . "_custom_values where " . $this->prefix . "_custom_origine=$id";
        pmb_mysql_query($requete);
        $requete = "delete from " . $this->prefix . "_custom_dates where " . $this->prefix . "_custom_origine=$id";
        pmb_mysql_query($requete);
        reset($this->t_fields);
        foreach ($this->t_fields as $key => $val) {
            $name = $val["NAME"];
            global ${$name};
            $value = ${$name};
            if (is_array($value) && count($value)) {
                for ($i = 0; $i < count($value); $i ++) {
                    if (isset($value[$i]) && $value[$i] !== "") {
                        $requete = "insert into " . $this->prefix . "_custom_values (" . $this->prefix . "_custom_champ," . $this->prefix . "_custom_origine," . $this->prefix . "_custom_" . $val["DATATYPE"] . "," . $this->prefix . "_custom_order) values($key,$id,'" . $value[$i] . "',$i)";
                        pmb_mysql_query($requete);

                        if ($this->t_fields[$key]["TYPE"] == 'date_flot') {
                            $interval = explode("|||", $value[$i]);
                            $date_type = $interval[0];

                            $date_start_signe = 1;
                            $date_end_signe = 1;
                            if (substr($interval[1], 0, 1) == '-') {
                                // date avant JC
                                $date_start_signe = - 1;
                                $interval[1] = substr($interval[1], 1);
                            }
                            if (substr($interval[2], 0, 1) == '-') {
                                // date avant JC
                                $date_end_signe = - 1;
                                $interval[2] = substr($interval[2], 1);
                            }
                            // années saisie inférieures à 4 digit
                            if (strlen($interval[1]) < 4) {
                                $interval[1] = str_pad($interval[1], 4, '0', STR_PAD_LEFT);
                            }
                            if ($interval[2] && strlen($interval[2]) < 4) {
                                $interval[2] = str_pad($interval[2], 4, '0', STR_PAD_LEFT);
                            }

                            $date_start = detectFormatDate($interval[1], 'min');
                            $date_end = detectFormatDate($interval[2], 'max');

                            if ($date_start == '0000-00-00') {
                                $date_start = '';
                            }
                            if ($date_end == '0000-00-00') {
                                $date_end = '';
                            }
                            if ($date_start || $date_end) {
                                if (! $date_end) {
                                    $date_end = detectFormatDate($interval[1], 'max');
                                    $date_end_signe = $date_start_signe;
                                }
                                // format en integer
                                $date_start = str_replace('-', '', $date_start) * $date_start_signe;
                                $date_end = str_replace('-', '', $date_end) * $date_end_signe;
                                if ($date_end < $date_start) {
                                    $date = $date_start;
                                    $date_start = $date_end;
                                    $date_end = $date;
                                }
                                $requete = "insert into " . $this->prefix . "_custom_dates (" . $this->prefix . "_custom_champ," . $this->prefix . "_custom_origine,
										" . $this->prefix . "_custom_date_type," . $this->prefix . "_custom_date_start," . $this->prefix . "_custom_date_end," . $this->prefix . "_custom_order)
										values($key,$id,$date_type,'" . $date_start . "','" . $date_end . "',$i)";
                                pmb_mysql_query($requete);
                            }
                        }
                    }
                }
            }
        }
    }

    public function read_form_fields_perso($name)
    {
        // Enregistrement des champs personalisés
        $return_val = '';
        reset($this->t_fields);
        foreach ($this->t_fields as $key => $val) {
            if ($val["NAME"] == $name) {
                global ${$name};
                $value = ${$name};
                for ($i = 0; $i < count($value); $i ++) {
                    $return_val .= $value[$i];
                }
            }
        }
        return $return_val;
    }

    public function read_base_fields_perso($name, $id)
    {
        global $val_list_empr;
        global $charset;

        $perso = array();
        // Récupération des valeurs stockées
        $this->get_values($id);
        if (! $this->no_special_fields) {
            reset($this->t_fields);
            foreach ($this->t_fields as $key => $val) {
                if ($val["NAME"] == $name) {
                    if (isset($this->values[$key]) && is_array($this->values[$key])) {
                        for ($i = 0; $i < count($this->values[$key]); $i ++) {
                            $return_val .= $this->values[$key][$i];
                        }
                    }
                }
            }
        }

        return $return_val;
    }

    public function read_base_fields_perso_values($name, $id)
    {
        global $val_list_empr;
        global $charset;

        $perso = array();
        // Récupération des valeurs stockées
        $this->get_values($id);
        if (! $this->no_special_fields) {
            reset($this->t_fields);
            foreach ($this->t_fields as $key => $val) {
                if ($val["NAME"] == $name) {
                    if (isset($this->values[$key]) && is_array($this->values[$key])) {
                        for ($i = 0; $i < count($this->values[$key]); $i ++) {
                            $perso[] = $this->values[$key][$i];
                        }
                    }
                }
            }
        }
        return $perso;
    }

    protected function _sort_values_by_format_values($a, $b)
    {
        if ($a['order'] != $b['order']) {
            return ($a['order'] < $b['order']) ? - 1 : 1;
        }
        if (strtolower(strip_tags($a['format_value'])) == strtolower(strip_tags($b['format_value']))) {
            return 0;
        }
        return (strtolower(strip_tags($a['format_value'])) < strtolower(strip_tags($b['format_value']))) ? - 1 : 1;
    }

    protected function sort_values($fields)
    {
        $values = array();
        foreach ($fields as $field_id => $field_values) {
            uasort($field_values, array($this,'_sort_values_by_format_values'));
            $values[$field_id] = array();
            foreach ($field_values as $value) {
                $values[$field_id][] = $value['value'];
            }
        }
        return $values;
    }

    // Récupération des valeurs stockées dans les base pour un emprunteur ou autre
    public function get_values($id)
    {
        $id = intval($id);
        // Récupération des valeurs stockées
        $this->values = $this->list_values = array();

        if ((! $this->no_special_fields) && ($id)) {
            $requete = "select " . $this->prefix . "_custom_champ," . $this->prefix . "_custom_origine," . $this->prefix . "_custom_small_text, " . $this->prefix . "_custom_text, " . $this->prefix . "_custom_integer, " . $this->prefix . "_custom_date, " . $this->prefix . "_custom_float, " . $this->prefix . "_custom_order from " . $this->prefix . "_custom_values where " . $this->prefix . "_custom_origine=" . $id;
            $resultat = pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat)) {
                $values = array();
                while ($r = pmb_mysql_fetch_array($resultat)) {

                    $index = "{$this->prefix}_custom_";
                    if (isset($this->t_fields[$r[$this->prefix."_custom_champ"]]) && isset($this->t_fields[$r[$this->prefix."_custom_champ"]]["DATATYPE"])) {
                        $index .= $this->t_fields[$r[$this->prefix."_custom_champ"]]["DATATYPE"];
                    }

                    $values[$r[$this->prefix . "_custom_champ"]][] = array(
                        'value' => $r[$index] ?? "",
                        'format_value' => $this->get_formatted_output(array($r[$index] ?? ""), $r["{$this->prefix}_custom_champ"], true),
                        'order' => $r["{$this->prefix}_custom_champ"]
                    );
                    $this->list_values[] = $r[$index] ?? "";
                }
                pmb_mysql_free_result($resultat);
                $this->values = $this->sort_values($values);
            }
        }
    }

    // Affichage des champs à saisir dans le formulaire de modification/création d'un emprunteur ou autre
    public function show_editable_fields($id, $from_z3950 = false)
    {
        global $aff_list_empr, $charset;
        $perso = array();
        $perso["FIELDS"] = array();
        $perso["CHECK_SCRIPTS"] = '';
        if (! $this->no_special_fields) {
            if (! $from_z3950) {
                $this->get_values($id);
            }
            $check_scripts = "";
            reset($this->t_fields);
            foreach ($this->t_fields as $key => $val) {

                if($from_z3950 && $this->hasImportField && ($this->t_fields[$key]["IMPORT"] != 1)) {
                    continue;
                }
                if (! isset($this->values[$key])) {
                    $this->values[$key] = array();
                }
                $t = array();
                $t["ID"] = $key;
                $t["NAME"] = $val["NAME"];
                $t["TITRE"] = $val["TITRE"];
                $t["COMMENT"] = $val["COMMENT"];
                $t["COMMENT_DISPLAY"] = "";
                if ($t["COMMENT"]) {
                    $t["COMMENT_DISPLAY"] = "&nbsp;<span class='pperso_comment' title='" . htmlentities($t["COMMENT"], ENT_QUOTES, $charset) . "' >" . nl2br(htmlentities($t["COMMENT"], ENT_QUOTES, $charset)) . "</span>";
                }
                $field = array();
                $field["ID"] = $key;
                $field["NAME"] = $this->t_fields[$key]["NAME"];
                $field["MANDATORY"] = $this->t_fields[$key]["MANDATORY"];
                $field["ORDER"] = $this->t_fields[$key]["ORDER"];
                $field["SEARCH"] = $this->t_fields[$key]["SEARCH"];
                $field["EXPORT"] = $this->t_fields[$key]["EXPORT"];
                $field["FILTERS"] = $this->t_fields[$key]["FILTERS"];
                $field["EXCLUSION"] = $this->t_fields[$key]["EXCLUSION"];
                $field["OPAC_SORT"] = $this->t_fields[$key]["OPAC_SORT"];
                $field["COMMENT"] = $this->t_fields[$key]["COMMENT"];
                $field["ALIAS"] = $this->t_fields[$key]["TITRE"];
                $field["DATATYPE"] = $this->t_fields[$key]["DATATYPE"];
                $field["OPTIONS"] = $this->t_fields[$key]["OPTIONS"];
                $field["VALUES"] = $this->values[$key];
                $field["PREFIX"] = $this->prefix;
                $field["ID_ORIGINE"] = $id;

                $aff = '';
                $function_name = $aff_list_empr[$this->t_fields[$key]['TYPE']];
                if (function_exists($function_name)) {
                    $aff = $function_name($field, $check_scripts);
                }

                $t["AFF"] = $aff;
                $t["NAME"] = $field["NAME"];
                $perso["FIELDS"][] = $t;
            }
            // Compilation des javascripts de validité renvoyés par les fonctions d'affichage
            $check_scripts = "<script>function cancel_submit(message) { alert(message); return false;}\nfunction check_form() {\n" . $check_scripts . "\nreturn true;\n}\n</script>";
            $perso["CHECK_SCRIPTS"] = $check_scripts;
        } else
            $perso["CHECK_SCRIPTS"] = "<script>function check_form() { return true; }</script>";
        return $perso;
    }

    public function show_field($key, $val)
    {
        global $val_list_empr;
        global $charset;

        $field = array();
        $field['TITRE'] = '<b>' . htmlentities($val['TITRE'], ENT_QUOTES, $charset) . ' : </b>';
        $field['TITRE_CLEAN'] = htmlentities($val['TITRE'], ENT_QUOTES, $charset);
        $field['OPAC_SHOW'] = $val['OPAC_SHOW'];
        if (! isset($this->values[$key])) {
            $this->values[$key] = array();
        }
        if (! isset(static::$fields[$this->prefix][$key])) {
            static::$fields[$this->prefix][$key] = array();
            static::$fields[$this->prefix][$key]['ID'] = $key;
            static::$fields[$this->prefix][$key]['NAME'] = $this->t_fields[$key]['NAME'];
            static::$fields[$this->prefix][$key]['MANDATORY'] = $this->t_fields[$key]['MANDATORY'];
            static::$fields[$this->prefix][$key]['SEARCH'] = $this->t_fields[$key]['SEARCH'];
            static::$fields[$this->prefix][$key]['EXPORT'] = $this->t_fields[$key]['EXPORT'];
            static::$fields[$this->prefix][$key]["FILTERS"] = $this->t_fields[$key]["FILTERS"];
            static::$fields[$this->prefix][$key]['EXCLUSION'] = $this->t_fields[$key]['EXCLUSION'];
            static::$fields[$this->prefix][$key]['OPAC_SORT'] = $this->t_fields[$key]['OPAC_SORT'];
            static::$fields[$this->prefix][$key]['COMMENT'] = $this->t_fields[$key]['COMMENT'];
            static::$fields[$this->prefix][$key]['ALIAS'] = $this->t_fields[$key]['TITRE'];
            static::$fields[$this->prefix][$key]['DATATYPE'] = $this->t_fields[$key]['DATATYPE'];
            static::$fields[$this->prefix][$key]['OPTIONS'] = $this->t_fields[$key]['OPTIONS'];
            static::$fields[$this->prefix][$key]['VALUES'] = $this->values[$key];
            static::$fields[$this->prefix][$key]['PREFIX'] = $this->prefix;
        }
        $field['TYPE'] = $this->t_fields[$key]['TYPE'];
        $aff = $val_list_empr[$this->t_fields[$key]['TYPE']](static::$fields[$this->prefix][$key], $this->values[$key]);

        if (is_array($aff) && $aff['ishtml'] == true) {
            $field['AFF'] = $aff['value'];
            if (isset($aff['details'])) {
                $field['DETAILS'] = $aff['details'];
            }
        } else {
            $field['AFF'] = htmlentities($aff, ENT_QUOTES, $charset);
        }
        $field['NAME'] = static::$fields[$this->prefix][$key]['NAME'];
        $field['ID'] = static::$fields[$this->prefix][$key]['ID'];
        return $field;
    }

    // Affichage des champs en lecture seule pour visualisation d'un fiche emprunteur ou autre...
    public function show_fields($id)
    {
        $perso = array();
        $perso["FIELDS"] = array();
        // Récupération des valeurs stockées pour l'emprunteur
        $this->get_values($id);
        if (! $this->no_special_fields) {
            // Affichage champs persos
            reset($this->t_fields);
            foreach ($this->t_fields as $key => $val) {
                $t = $this->show_field($key, $val);
                $perso['FIELDS'][] = $t;
            }
        }
        return $perso;
    }

    public function get_field_id_from_name($name)
    {
        $query = "select idchamp from " . $this->prefix . "_custom where name='" . addslashes($name) . "'";
        $result = pmb_mysql_query($query);
        return pmb_mysql_result($result, 0, 0);
    }

    public function get_field_name_from_id($id)
    {
        $query = "select name from " . $this->prefix . "_custom where idchamp='" . addslashes($id) . "'";
        $result = pmb_mysql_query($query);
        return pmb_mysql_result($result, 0, 0);
    }

    public function get_formatted_output($values, $field_id)
    {
        global $val_list_empr, $charset;

        if (! isset(static::$fields[$this->prefix][$field_id])) {
            if (! empty($this->t_fields[$field_id])) {
                static::$fields[$this->prefix][$field_id] = array();
                static::$fields[$this->prefix][$field_id]["ID"] = $field_id;
                static::$fields[$this->prefix][$field_id]["NAME"] = $this->t_fields[$field_id]["NAME"];
                static::$fields[$this->prefix][$field_id]["MANDATORY"] = $this->t_fields[$field_id]["MANDATORY"];
                static::$fields[$this->prefix][$field_id]["SEARCH"] = $this->t_fields[$field_id]["SEARCH"];
                static::$fields[$this->prefix][$field_id]["EXPORT"] = $this->t_fields[$field_id]["EXPORT"];
                static::$fields[$this->prefix][$field_id]["FILTERS"] = $this->t_fields[$field_id]["FILTERS"];
                static::$fields[$this->prefix][$field_id]["EXCLUSION"] = $this->t_fields[$field_id]["EXCLUSION"];
                static::$fields[$this->prefix][$field_id]["OPAC_SORT"] = $this->t_fields[$field_id]["OPAC_SORT"];
                static::$fields[$this->prefix][$field_id]["COMMENT"] = $this->t_fields[$field_id]["COMMENT"];
                static::$fields[$this->prefix][$field_id]["ALIAS"] = $this->t_fields[$field_id]["TITRE"];
                static::$fields[$this->prefix][$field_id]["DATATYPE"] = $this->t_fields[$field_id]["DATATYPE"];
                static::$fields[$this->prefix][$field_id]["OPTIONS"] = $this->t_fields[$field_id]["OPTIONS"];
                static::$fields[$this->prefix][$field_id]["VALUES"] = $values;
                static::$fields[$this->prefix][$field_id]["PREFIX"] = $this->prefix;
            }
        }
        if (! empty($this->t_fields[$field_id])) {
            $aff = $val_list_empr[$this->t_fields[$field_id]["TYPE"]](static::$fields[$this->prefix][$field_id], $values);
        }
        if (isset($aff)) {
            if (is_array($aff)) {
                return $aff['withoutHTML'];
            }
            return $aff;
        }
        return '';
    }

    // Appelé par sort_out_values
    protected function _sort_out_values_by_format_values($a, $b)
    {
        if ($a['order'] != $b['order']) {
            return ($a['order'] < $b['order']) ? - 1 : 1;
        }
        if (strtolower(strip_tags($a['format_value'])) == strtolower(strip_tags($b['format_value']))) {
            return 0;
        }
        return (strtolower(strip_tags($a['format_value'])) < strtolower(strip_tags($b['format_value']))) ? - 1 : 1;
    }

    // Appelé dans get_out_values
    protected function sort_out_values()
    {
        $fields = $this->values;
        foreach ($fields as $name => $field) {
            uasort($field['values'], array($this, '_sort_out_values_by_format_values'));
            $this->values[$name]['values'] = $field['values'];
        }
    }

    // Récupération des valeurs stockées dans les base pour un emprunteur ou autre
    public function get_out_values($id)
    {
        // Récupération des valeurs stockées
        if (! isset(self::$out_values[$id])) {
            if ((! $this->no_special_fields) && ($id)) {
                $this->values = array();
                $requete = "select " . $this->prefix . "_custom_champ," . $this->prefix . "_custom_origine," . $this->prefix . "_custom_small_text, " . $this->prefix . "_custom_text, " . $this->prefix . "_custom_integer, " . $this->prefix . "_custom_date, " . $this->prefix . "_custom_float, " . $this->prefix . "_custom_order from " . $this->prefix . "_custom_values join " . $this->prefix . "_custom on idchamp=" . $this->prefix . "_custom_champ where " . $this->prefix . "_custom_origine=" . $id;
                $resultat = pmb_mysql_query($requete);
                while ($r = pmb_mysql_fetch_array($resultat)) {
                    $this->values[$this->t_fields[$r[$this->prefix . "_custom_champ"]]["NAME"]]['label'] = $this->t_fields[$r[$this->prefix . "_custom_champ"]]["TITRE"];
                    $this->values[$this->t_fields[$r[$this->prefix . "_custom_champ"]]["NAME"]]['id'] = $r[$this->prefix . "_custom_champ"];
                    $format_value = $this->get_formatted_output(array($r[$this->prefix . "_custom_" . $this->t_fields[$r[$this->prefix . "_custom_champ"]]["DATATYPE"]]), $r[$this->prefix . "_custom_champ"], true);
                    $this->values[$this->t_fields[$r[$this->prefix . "_custom_champ"]]["NAME"]]['values'][] = array(
                        'value' => $r[$this->prefix . "_custom_" . $this->t_fields[$r[$this->prefix . "_custom_champ"]]["DATATYPE"]],
                        'format_value' => $format_value,
                        'order' => $r[$this->prefix . "_custom_order"]
                    );
                    if (! isset($this->values[$this->t_fields[$r[$this->prefix . "_custom_champ"]]["NAME"]]['all_format_values'])) {
                        $this->values[$this->t_fields[$r[$this->prefix . "_custom_champ"]]["NAME"]]['all_format_values'] = '';
                    }
                    $this->values[$this->t_fields[$r[$this->prefix . "_custom_champ"]]["NAME"]]['all_format_values'] .= $format_value . ' ';
                }
                $this->sort_out_values();
            } else {
                $this->values = array();
            }
            self::$out_values[$id] = $this->values;
        } else {
            $this->values = self::$out_values[$id];
        }
        return self::$out_values[$id];
    }

    // Suppression de la base des valeurs d'un emprunteur ou autre...
    public function delete_values($id)
    {
        $id = intval($id);
        $requete = "DELETE FROM " . $this->prefix . "_custom_values where " . $this->prefix . "_custom_origine=$id";
        $res = pmb_mysql_query($requete);
        $requete = "DELETE FROM " . $this->prefix . "_custom_dates where " . $this->prefix . "_custom_origine=$id";
        $res = pmb_mysql_query($requete);
    }

    // Gestion des actions en administration
    public function proceed()
    {
        global $action, $sphinx_active;
        global $name, $titre, $type, $datatype, $_options, $multiple, $obligatoire, $search, $export, $import, $filters, $exclusion, $ordre, $idchamp, $id, $pond, $opac_sort, $comment, $classement;

        switch ($action) {

            case "nouv":
                $this->show_edit_form();
                break;

            case "edit":
                $this->show_edit_form($id);
                break;

            case "create":
                $this->check_form();
                $resultat = pmb_mysql_query("select max(ordre) from " . $this->prefix . "_custom");
                $ordre = 1;
                if (pmb_mysql_num_rows($resultat) != 0) {
                    $ordre = pmb_mysql_result($resultat, 0, 0) + 1;
                }
                $requete = "insert into " . $this->prefix . "_custom set ";
                $requete .= "name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', ";
                $requete .= "multiple=$multiple, obligatoire=$obligatoire, ordre=" . intval($ordre) . ", search=$search, export=$export, ";
                if ($this->hasImportField) {
                    $requete .= "import=$import, ";
                }
                $requete .= "filters=$filters, exclusion_obligatoire=$exclusion, pond=$pond, opac_sort=$opac_sort, comment='$comment', ";
                $requete .= "custom_classement='$classement' ";
                pmb_mysql_query($requete);
                $idchamp = pmb_mysql_insert_id();
                if (! empty($sphinx_active) && ! empty($search)) {
                    $sphinx = new sphinx_indexer();
                    $sphinx->editSphinxTables($this->prefix, 'create', $name, $idchamp, $datatype);
                }

                $contribution_area_store = new contribution_area_store();
                $contribution_area_store->reset_store(contribution_area_store::DATASTORE, true);
                $contribution_area_store->reset_store(contribution_area_store::ONTOSTORE, true);

                $translation = new translation($idchamp, $this->prefix . "_custom");
                $translation->update("titre");
                echo $this->show_field_list();
                onto_parametres_perso::reinitialize();
                break;

            case "update":
                $this->check_form();
                $res = pmb_mysql_query("SELECT search FROM " . $this->prefix . "_custom WHERE idchamp=$idchamp");
                $old_search = pmb_mysql_result($res, 0, 0);
                $requete = "update " . $this->prefix . "_custom set ";
                $requete .= "name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', ";
                $requete .= "multiple=$multiple, obligatoire=$obligatoire, ordre=" . intval($ordre) . ", search=$search, export=$export, ";
                if ($this->hasImportField) {
                    $requete .= "import=$import, ";
                }
                $requete .= "filters=$filters, exclusion_obligatoire=$exclusion, pond=$pond, opac_sort=$opac_sort, comment='$comment', ";
                $requete .= "custom_classement='$classement' where idchamp=$idchamp";

                pmb_mysql_query($requete);
                if (! empty($sphinx_active) && (! empty($old_search) || ! empty($search))) {
                    $sphinx = new sphinx_indexer();
                    if (empty($search)) {
                        $sphinx->editSphinxTables($this->prefix, 'delete', $name, $idchamp, $datatype);
                    } else {
                        $sphinx->editSphinxTables($this->prefix, 'update', $name, $idchamp, $datatype);
                    }
                }

                $contribution_area_store = new contribution_area_store();
                $contribution_area_store->reset_store(contribution_area_store::DATASTORE, true);
                $contribution_area_store->reset_store(contribution_area_store::ONTOSTORE, true);
                $contribution_area_store->check_properties_form($this->prefix . "_custom");

                $translation = new translation($idchamp, $this->prefix . "_custom");
                $translation->update("titre");
                echo $this->show_field_list();
                onto_parametres_perso::reinitialize();
                break;

            case "up":
                $requete = "select ordre from " . $this->prefix . "_custom where idchamp=$id";
                $resultat = pmb_mysql_query($requete);
                $ordre = pmb_mysql_result($resultat, 0, 0);
                $requete = "select max(ordre) as ordre from " . $this->prefix . "_custom where ordre<$ordre";
                $resultat = pmb_mysql_query($requete);
                $ordre_max = @pmb_mysql_result($resultat, 0, 0);
                if ($ordre_max) {
                    $requete = "select idchamp from " . $this->prefix . "_custom where ordre=$ordre_max limit 1";
                    $resultat = pmb_mysql_query($requete);
                    $idchamp_max = pmb_mysql_result($resultat, 0, 0);
                    $requete = "update " . $this->prefix . "_custom set ordre='" . $ordre_max . "' where idchamp=$id";
                    pmb_mysql_query($requete);
                    $requete = "update " . $this->prefix . "_custom set ordre='" . $ordre . "' where idchamp=" . $idchamp_max;
                    pmb_mysql_query($requete);
                }
                echo $this->show_field_list();
                break;

            case "down":
                $requete = "select ordre from " . $this->prefix . "_custom where idchamp=$id";
                $resultat = pmb_mysql_query($requete);
                $ordre = pmb_mysql_result($resultat, 0, 0);
                $requete = "select min(ordre) as ordre from " . $this->prefix . "_custom where ordre>$ordre";
                $resultat = pmb_mysql_query($requete);
                $ordre_min = @pmb_mysql_result($resultat, 0, 0);
                if ($ordre_min) {
                    $requete = "select idchamp from " . $this->prefix . "_custom where ordre=$ordre_min limit 1";
                    $resultat = pmb_mysql_query($requete);
                    $idchamp_min = pmb_mysql_result($resultat, 0, 0);
                    $requete = "update " . $this->prefix . "_custom set ordre='" . $ordre_min . "' where idchamp=$id";
                    pmb_mysql_query($requete);
                    $requete = "update " . $this->prefix . "_custom set ordre='" . $ordre . "' where idchamp=" . $idchamp_min;
                    pmb_mysql_query($requete);
                }
                echo $this->show_field_list();
                break;

            case "delete":
                $contribution_area_store = new contribution_area_store();
                $contribution_area_store->reset_store(contribution_area_store::DATASTORE, true);
                $contribution_area_store->reset_store(contribution_area_store::ONTOSTORE, true);
                $contribution_area_store->check_properties_form($this->prefix . "_custom", $idchamp, true);

                $res = pmb_mysql_query("SELECT search FROM " . $this->prefix . "_custom WHERE idchamp=$idchamp");
                $old_search = pmb_mysql_result($res, 0, 0);
                if (! empty($sphinx_active) && ! empty($old_search)) {
                    $sphinx = new sphinx_indexer();
                    $sphinx->editSphinxTables($this->prefix, 'delete', '', $idchamp);
                }
                pmb_mysql_query("DELETE FROM " . $this->prefix . "_custom WHERE idchamp=$idchamp");
                pmb_mysql_query("DELETE FROM " . $this->prefix . "_custom_values WHERE " . $this->prefix . "_custom_champ=$idchamp");
                pmb_mysql_query("DELETE FROM " . $this->prefix . "_custom_lists WHERE " . $this->prefix . "_custom_champ=$idchamp");
                translation::delete($idchamp, $this->prefix . "_custom", "titre");
                echo $this->show_field_list();
                onto_parametres_perso::reinitialize();
                break;

            default:
                echo $this->show_field_list();
        }
    }

    public function get_pond($id)
    {
        return $this->t_fields[$id]["POND"];
    }

    public function get_ajax_list($name, $start)
    {
        $values = array();
        reset($this->t_fields);
        foreach ($this->t_fields as $key => $val) {
            if ($val['NAME'] == $name) {
                switch ($val['TYPE']) {
                    case 'list':
                        $values = custom_fields_list::get_ajax_list($key, $this->prefix);
                        break;
                    case 'query_list':
                        $field = array();
                        $field['OPTIONS'] = $val['OPTIONS'];
                        $q = $field['OPTIONS'][0]['QUERY'][0]['value'];
                        $r = pmb_mysql_query($q);
                        if (pmb_mysql_num_rows($r)) {
                            while ($row = pmb_mysql_fetch_row($r)) {
                                $values[$row[0]] = $row[1];
                            }
                        }
                        break;
                }
                break;
            }
        }
        if (count($values) && $start && $start != '%') {
            $filtered_values = array();
            foreach ($values as $k => $v) {
                if (strtolower(substr(convert_diacrit($v), 0, strlen($start))) == strtolower($start)) {
                    $filtered_values[$k] = $v;
                } elseif (strtolower(substr($v, 0, strlen($start))) == strtolower($start)) {
                    $filtered_values[$k] = $v;
                }
            }
            return $filtered_values;
        }
        return $values;
    }

    public function get_field_form($id, $field_name, $values)
    {
        global $aff_list_empr_search, $charset;
        $field = array();
        $field['ID'] = $id;
        $field['NAME'] = $this->t_fields[$id]['NAME'];
        $field['MANDATORY'] = $this->t_fields[$id]['MANDATORY'];
        $field['ALIAS'] = $this->t_fields[$id]['TITRE'];
        $field['COMMENT'] = $this->t_fields[$id]['COMMENT'];
        $field['DATATYPE'] = $this->t_fields[$id]['DATATYPE'];
        $field['OPTIONS'] = $this->t_fields[$id]['OPTIONS'];
        $field['VALUES'] = $values;
        $field['PREFIX'] = $this->prefix;

        $r = '';
        $function_name = $aff_list_empr_search[$this->t_fields[$id]['TYPE']];
        if (function_exists($function_name)) {
            $check_scripts = '';
            $r = $function_name($field, $check_scripts, $field_name);
        }

        return $r;
    }

	public function get_rgaa_label($id, $field_name) {
	    $class_name = "custom_fields_".$this->t_fields[$id]['TYPE'];
	    if (class_exists($class_name)) {
	        $field=array();
	        $field['ID']=$id;
	        $field['NAME']=$this->t_fields[$id]['NAME'];
	        $field['OPTIONS']=$this->t_fields[$id]['OPTIONS'];
	        return $class_name::get_rgaa_label($field, $field_name);
	    }
	    return '';
	}

    /**
     * Importe la valeur d'un champ personnalisé pour un élément concerné.
     * Gère les listes et vérifie le type de document.
     * Controle les URL et résolveur de lien.
     *
     * La valeur doit-être celle à ajouter. (Ne trouve pas l'éditeur concerné si lien vers éditeurs par exemple)
     *
     * @param integer $id : l'identifiant de l'élément concerné par la valeur du champ personnalisé
     * @param string $fieldName : le nom du champ personnalisé
     * @param mixed $value : la valeur à insérer
     * @param string $prefix : le préfixe de la table des champs personnalisés concernés, par défaut notices
     *
     * @return boolean true si réussi, false sinon
     *
     */
    static public function import($id, $fieldName, $value, $prefix = "notices")
    {
        $tab = array();
        $idchamp = 0;
        $type = '';
        $datatype = '';
        $check_message = '';

        // on trouve l'id, le type de champ et le type des données
        $query = 'SELECT idchamp,type,datatype FROM ' . $prefix . '_custom WHERE name="' . addslashes($fieldName) . '" LIMIT 1';
        $result = pmb_mysql_query($query) or die('Echec d\'execution de la requete ' . $query . '  : ' . pmb_mysql_error());

        if (pmb_mysql_num_rows($result)) {
            while ($line = pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC)) {
                $idchamp = $line['idchamp'];
                $type = $line['type'];
                $datatype = $line['datatype'];
            }
        } else {
            return false;
        }

        switch ($type) {
            case 'list':
                // Selection des valeurs si list
                $query = 'SELECT * FROM ' . $prefix . '_custom_lists WHERE ' . $prefix . '_custom_champ=' . $idchamp;
                $result = pmb_mysql_query($query) or die('Echec d\'execution de la requete ' . $query . '  : ' . pmb_mysql_error());

                if (pmb_mysql_num_rows($result)) {
                    while ($line = pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC)) {
                        $tab[$line[$prefix . '_custom_list_lib']] = $line[$prefix . '_custom_list_value'];
                    }
                }
                if (! $tab[$value]) {
                    if (in_array($value, $tab)) {
                        // on passe la clé et pas le libellé
                        foreach ($tab as $tmpKey => $tmpVal) {
                            if ($value == $tmpVal) {
                                $value = $tmpKey;
                                break;
                            }
                        }
                    } else {
                        // Ajout dans _custom_list
                        if ($datatype == 'integer' || $datatype == 'float') {
                            if (empty($tab)) {
                                $val = 1;
                            } else {
                                $val = max($tab) + 1;
                            }
                        } else {
                            $val = $value;
                        }

                        $query = 'INSERT INTO ' . $prefix . '_custom_lists (' . $prefix . '_custom_champ,' . $prefix . '_custom_list_value,' . $prefix . '_custom_list_lib) VALUES (' . $idchamp . ',"' . addslashes(trim($val)) . '","' . addslashes(trim($value)) . '")';
                        pmb_mysql_query($query) or die('Echec d\'execution de la requete ' . $query . '  : ' . pmb_mysql_error());

                        $tab[$value] = $val;
                    }
                }
                break;
            case 'date_box':
            case 'comment':
            case 'text':
            case 'query_list':
            case 'query_auth':
            case 'external':
                // rien à faire
                $tab[$value] = $value;
                break;
            case 'resolve':
                // ici on vérifi la présence d'un pipe dans le resolveur
                if (! preg_match('/\|/', $value)) {
                    return false;
                }
                $tab[$value] = $value;
                break;
            case 'url':
                // ici on vérifi le format de l'url
                if (! preg_match('/^http:\/\/www\./', $value)) {
                    return false;
                }
                $tab[$value] = $value;
                break;
            default:
                return false;
                break;
        }

        // on appele la fonction de nettoyage du type de donnée.
        $tab[$value] = call_user_func_array('chk_type_' . $datatype, array($tab[$value], &$check_message));

        if ($check_message) {
            print $check_message;
            return false;
        }

        // Ajout dans _custom_values
        $query = 'DELETE FROM ' . $prefix . '_custom_values WHERE ' . $prefix . '_custom_champ=' . $idchamp . ' AND ' . $prefix . '_custom_origine=' . $id . ' AND ' . $prefix . '_custom_' . $datatype . '="' . trim(addslashes($tab[$value])) . '"';
        pmb_mysql_query($query) or die('Echec d\'execution de la requete ' . $query . '  : ' . pmb_mysql_error());

        $query = 'INSERT INTO ' . $prefix . '_custom_values (' . $prefix . '_custom_champ,' . $prefix . '_custom_origine,' . $prefix . '_custom_' . $datatype . ') VALUES (' . $idchamp . ',' . $id . ',"' . trim(addslashes($tab[$value])) . '")';
        pmb_mysql_query($query) or die('Echec d\'execution de la requete ' . $query . '  : ' . pmb_mysql_error());

        return true;
    }

    // Affichage des champs de recherche
    public function show_search_fields()
    {
        global $aff_list_empr_search, $charset;

        $perso = array();
        $check_scripts = "";
        reset($this->t_fields);
        foreach ($this->t_fields as $key => $val) {
            if ($this->t_fields[$key]["SEARCH"] || $this->t_fields[$key]["FILTERS"]) {
                $t = array();
                $t["NAME"] = $val["NAME"];
                $t["TITRE"] = $val["TITRE"];
                $t["COMMENT"] = $val["COMMENT"];
                $field = array();
                $field["ID"] = $key;
                $field["NAME"] = $this->t_fields[$key]["NAME"];
                $field_name = $field["NAME"];
                $values = array();
                if ($val["NAME"] == $field_name) {
                    global ${$field_name};
                    $value = ${$field_name};
                    if (! empty($value)) {
                        for ($i = 0; $i < count($value); $i ++) {
                            if ($value[$i]) {
                                $values[] = stripslashes($value[$i]);
                            }
                        }
                    }
                }
                $field["VALUES"] = $values;
                $field["PREFIX"] = $this->prefix;
                $field["OPTIONS"] = $this->t_fields[$key]["OPTIONS"];

                $aff = '';
                $function_name = $aff_list_empr_search[$this->t_fields[$key]['TYPE']];
                if (function_exists($function_name)) {
                    $aff = $function_name($field, $check_scripts, $field_name);
                }

                $t["AFF"] = $aff;
                $t["NAME"] = $field["NAME"];
                $perso["FIELDS"][] = $t;
            }
        }
        return $perso;
    }

    // Lecture des champs de recherche
    public function read_search_fields_from_form()
    {
        $perso = array();
        reset($this->t_fields);
        foreach ($this->t_fields as $key => $val) {
            if ($this->t_fields[$key]["SEARCH"] || $this->t_fields[$key]["FILTERS"]) {
                $t = array();
                $t["ID"] = $key;
                $t["DATATYPE"] = $val["DATATYPE"];
                $t["NAME"] = $val["NAME"];
                $t["TITRE"] = $val["TITRE"];
                $t["COMMENT"] = $val["COMMENT"];
                $name = $this->t_fields[$key]["NAME"];
                $values = array();
                if ($val["NAME"] == $name) {
                    global ${$name};
                    $value = ${$name};
                    if (is_array($value)) {
                        for ($i = 0; $i < count($value); $i ++) {
                            if ($value[$i]) {
                                $values[] = $value[$i];
                            }
                        }
                    }
                }
                $t["VALUE"] = $values;
                $perso["FIELDS"][] = $t;
            }
        }
        return $perso;
    }

    public static function prefix_var_tree($tree, $prefix)
    {
        for ($i = 0; $i < count($tree); $i ++) {
            $tree[$i]['var'] = $prefix . "." . $tree[$i]['var'];
            if (isset($tree[$i]['children']) && $tree[$i]['children']) {
                $tree[$i]['children'] = self::prefix_var_tree($tree[$i]['children'], $prefix);
            }
        }
        return $tree;
    }

    public function get_format_data_structure($full = true)
    {
        global $msg;

        $main_fields = array();
        foreach ($this->t_fields as $key => $val) {
            $field = $this->t_fields[$key];
            $main_fields[] = array(
                'var' => $field['NAME'],
                'desc' => $field["TITRE"],
                'children' => array(
                    array(
                        'var' => $field['NAME'] . ".id",
                        'desc' => $msg['frbr_entity_common_datasource_desc_custom_fields_id'],
                    ),
                    array(
                        'var' => $field['NAME'] . ".label",
                        'desc' => $msg['frbr_entity_common_datasource_desc_custom_fields_label'],
                    ),
                    array(
                        'var' => $field['NAME'] . ".values",
                        'desc' => $msg['frbr_entity_common_datasource_desc_custom_fields_values'],
                        'children' => array(
                            array(
                                'var' => $field['NAME'] . ".values[i].format_value",
                                'desc' => $msg['frbr_entity_common_datasource_desc_custom_fields_values_format_value'],
                            ),
                            array(
                                'var' => $field['NAME'] . ".values[i].value",
                                'desc' => $msg['frbr_entity_common_datasource_desc_custom_fields_values_value'],
                            )
                        )
                    )
                )
            );
        }
        return $main_fields;
    }

    public function get_t_fields()
    {
        return $this->t_fields;
    }

    public static function get_pperso_prefix_from_type($type)
    {
        switch ($type) {
            case TYPE_NOTICE:
                return 'notices';
            case TYPE_AUTHOR:
                return 'author';
            case TYPE_CATEGORY:
                return 'categ';
            case TYPE_PUBLISHER:
                return 'publisher';
            case TYPE_COLLECTION:
                return 'collection';
            case TYPE_SUBCOLLECTION:
                return 'subcollection';
            case TYPE_SERIE:
                return 'serie';
            case TYPE_TITRE_UNIFORME:
                return 'tu';
            case TYPE_INDEXINT:
                return 'indexint';
            case TYPE_CONCEPT_PREFLABEL:
            case TYPE_CONCEPT:
                return 'skos';
            case TYPE_AUTHPERSO:
            default:
                if ($type > 1000 || $type == TYPE_AUTHPERSO) {
                    return 'authperso';
                }
                return '';
        }
    }

    public function get_sort_type()
    {
        switch ($this->prefix) {
            case 'author':
                return 'authors';
            case 'categ':
                return 'categories';
            case 'publisher':
                return 'publishers';
            case 'collection':
                return 'collections';
            case 'subcollection':
                return 'subcollections';
            case 'serie':
                return 'series';
            case 'indexint':
                return 'indexint';
            case 'skos':
                return 'concepts';
            case 'authperso':
                return 'authperso';
        }
    }

    public function get_field_list($msg_no_select = '')
    {
        global $msg;

        $field_list = array();

        $query = "SELECT idchamp, titre FROM {$this->prefix}_custom ORDER BY ordre";
        $result = pmb_mysql_query($query);

        if (pmb_mysql_num_rows($result)) {
            $field_list[] = [
                "value" => 0,
                "label" => $msg_no_select
            ];

            while ($row = pmb_mysql_fetch_assoc($result)) {
                $field_list[] = [
                    "value" => intval($row['idchamp']),
                    "label" => $row['titre']
                ];
            }
        }
        return $field_list;
    }
}