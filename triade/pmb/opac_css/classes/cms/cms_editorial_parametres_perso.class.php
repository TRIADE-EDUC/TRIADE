<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_parametres_perso.class.php,v 1.46 2023/09/13 08:55:34 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . "/parametres_perso.class.php");

class cms_editorial_parametres_perso extends parametres_perso
{

    public $num_type;

    public $base_url;

    public $option_visibilite = [];

    public function __construct($type, $base_url = "")
    {
        global $_custom_prefixe_, $msg;
        global $aff_list_empr;
        global $aff_list_empr_search;
        global $aff_filter_list_empr;
        global $chk_list_empr;
        global $val_list_empr;
        global $type_list_empr;
        global $options_list_empr;

        $this->option_visibilite = array(
            'multiple' => "none",
            'opac_sort' => "none",
            'exclusion' => "none"
        );

        $this->prefix = "cms_editorial";
        $this->base_url = $base_url;
        $_custom_prefixe_ = "cms_editorial";

        $this->num_type = intval($type);

        $this->fetch_data_cache();
    }

    protected function fetch_data_cache()
    {
        if ($tmp = cms_cache::get_at_cms_cache($this)) {
            $this->restore($tmp);
        } else {
            $this->fetch_data();
            cms_cache::set_at_cms_cache($this);
        }
    }

    protected function restore($cms_object)
    {
        if (is_object($cms_object)) {
            foreach (get_object_vars($cms_object) as $propertieName => $propertieValue) {
                $this->{$propertieName} = $propertieValue;
            }
        }
    }

    protected function fetch_data()
    {
        global $charset;

        // Lecture des champs
        $this->no_special_fields = 0;
        $this->t_fields = array();
        if (! isset(self::$st_fields[$this->prefix . '_' . $this->num_type])) {
            $requete = "select idchamp, name, titre, type, datatype, obligatoire, options, multiple, search, export, exclusion_obligatoire, pond, opac_sort, comment from " . $this->prefix . "_custom where num_type = '" . $this->num_type . "' order by ordre";

            $resultat = pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat) == 0)
                self::$st_fields[$this->prefix . '_' . $this->num_type] = false;
            else {
                while ($r = pmb_mysql_fetch_object($resultat)) {
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["DATATYPE"] = $r->datatype;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["NAME"] = $r->name;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["TITRE"] = $r->titre;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["TYPE"] = $r->type;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["OPTIONS"][0] = _parser_text_no_function_("<?xml version='1.0' encoding='" . $charset . "'?>\n" . $r->options, "OPTIONS");
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["MANDATORY"] = $r->obligatoire;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["OPAC_SHOW"] = $r->multiple;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["SEARCH"] = $r->search;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["EXPORT"] = $r->export;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["EXCLUSION"] = $r->exclusion_obligatoire;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["POND"] = $r->pond;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["OPAC_SORT"] = $r->opac_sort;
                    self::$st_fields[$this->prefix . '_' . $this->num_type][$r->idchamp]["COMMENT"] = $r->comment;
                }
            }
        }
        if (self::$st_fields[$this->prefix . '_' . $this->num_type] == false) {
            $this->no_special_fields = 1;
        } else {
            $this->t_fields = self::$st_fields[$this->prefix . '_' . $this->num_type];
        }
    }

    // Gestion des actions en administration
    public function proceed()
    {
        global $action;
        global $name, $titre, $type, $datatype, $_options, $multiple, $obligatoire, $search, $export, $exclusion, $ordre, $idchamp, $id, $pond, $opac_sort, $comment;

        switch ($action) {
            case "nouv":
                $this->show_edit_form();
                break;
            case "edit":
                $this->show_edit_form($id);
                break;
            case "create":
                $this->check_form();
                $requete = "select max(ordre) from " . $this->prefix . "_custom where num_type = " . $this->num_type;
                $resultat = pmb_mysql_query($requete);
                if (pmb_mysql_num_rows($resultat) != 0)
                    $ordre = pmb_mysql_result($resultat, 0, 0) + 1;
                else
                    $ordre = 1;

                $requete = "insert into " . $this->prefix . "_custom set num_type = '$this->num_type', name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple='$multiple', obligatoire='$obligatoire', ordre='$ordre', search='$search', export='$export', exclusion_obligatoire='$exclusion', opac_sort='$opac_sort' ";
                pmb_mysql_query($requete);
                echo $this->show_field_list();
                break;
            case "update":
                $this->check_form();
                $requete = "update " . $this->prefix . "_custom set name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple='$multiple', obligatoire='$obligatoire', ordre='$ordre', search='$search', export='$export', exclusion_obligatoire='$exclusion', pond='$pond', opac_sort='$opac_sort' where idchamp='" . ($idchamp * 1) . "'";
                pmb_mysql_query($requete);
                echo $this->show_field_list();
                break;
            case "up":
                $requete = "select ordre from " . $this->prefix . "_custom where idchamp='" . ($id * 1) . "'";
                $resultat = pmb_mysql_query($requete);
                $ordre = pmb_mysql_result($resultat, 0, 0);
                $requete = "select max(ordre) as ordre from " . $this->prefix . "_custom where ordre<'" . ($ordre * 1) . "' and num_type = '" . $this->num_type . "'";
                $resultat = pmb_mysql_query($requete);
                $ordre_max = @pmb_mysql_result($resultat, 0, 0);
                if ($ordre_max) {
                    $requete = "select idchamp from " . $this->prefix . "_custom where ordre='" . ($ordre_max * 1) . "' and num_type = '" . $this->num_type . "' limit 1";
                    $resultat = pmb_mysql_query($requete);
                    $idchamp_max = pmb_mysql_result($resultat, 0, 0);
                    $requete = "update " . $this->prefix . "_custom set ordre='" . $ordre_max . "' where idchamp='" . ($id * 1) . "' and num_type = '" . $this->num_type . "'";
                    pmb_mysql_query($requete);
                    $requete = "update " . $this->prefix . "_custom set ordre='" . ($ordre * 1) . "' where idchamp='" . ($idchamp_max * 1) . "' and num_type = '" . $this->num_type . "'";
                    pmb_mysql_query($requete);
                }
                echo $this->show_field_list();
                break;
            case "down":
                $requete = "select ordre from " . $this->prefix . "_custom where idchamp='" . ($id * 1) . "'";
                $resultat = pmb_mysql_query($requete);
                $ordre = pmb_mysql_result($resultat, 0, 0);
                $requete = "select min(ordre) as ordre from " . $this->prefix . "_custom where ordre>'" . ($ordre * 1) . "' and num_type = '" . $this->num_type . "'";
                $resultat = pmb_mysql_query($requete);
                $ordre_min = @pmb_mysql_result($resultat, 0, 0);
                if ($ordre_min) {
                    $requete = "select idchamp from " . $this->prefix . "_custom where ordre='" . ($ordre_min * 1) . "' and num_type = '" . ($this->num_type * 1) . "' limit 1";
                    $resultat = pmb_mysql_query($requete);
                    $idchamp_min = pmb_mysql_result($resultat, 0, 0);
                    $requete = "update " . $this->prefix . "_custom set ordre='" . ($ordre_min * 1) . "' where idchamp='" . ($id * 1) . "' and num_type = '" . $this->num_type . "'";
                    pmb_mysql_query($requete);
                    $requete = "update " . $this->prefix . "_custom set ordre='" . ($ordre * 1) . "' where idchamp='" . ($idchamp_min * 1) . "' and num_type = '" . $this->num_type . "'";
                    pmb_mysql_query($requete);
                }
                echo $this->show_field_list();
                break;
            case "delete":
                $requete = "delete from " . $this->prefix . "_custom where idchamp= '" . ($idchamp * 1) . "'";
                pmb_mysql_query($requete);
                $requete = "delete from " . $this->prefix . "_custom_values where " . $this->prefix . "_custom_champ= '" . ($idchamp * 1) . "'";
                pmb_mysql_query($requete);
                $requete = "delete from " . $this->prefix . "_custom_lists where " . $this->prefix . "_custom_champ= '" . ($idchamp * 1) . "'";
                pmb_mysql_query($requete);
                echo $this->show_field_list();
                break;
            default:
                echo $this->show_field_list();
        }
    }

    public function get_selector_options($selected = 0, $all_type = 0)
    {
        global $msg, $charset;
        $options = $type = "";
        // les champs génériques...
        if ($all_type) {
            $query = "select editorial_type_element from cms_editorial_types ";
        } else {
            $query = "select editorial_type_element from cms_editorial_types where id_editorial_type=" . $this->num_type;
        }
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $type = pmb_mysql_result($result, 0, 0);
            $generic_type = $this->get_generic_type($type);
            if ($generic_type) {
                $generic = new cms_editorial_parametres_perso($generic_type, $this->base_url);
                $options .= $generic->get_selector_options($selected);
            }
        }

        if (strpos($type, "generic") !== false) {
            $options .= "
			<option value='0'" . (! $selected ? "selected='selected'" : "") . ">" . $msg['cms_editorial_form_type_field_choice'] . "</option>";
        }
        foreach ($this->t_fields as $id => $field) {
            $options .= "
			<option value='" . $id . "'" . ($id == $selected ? "selected='selected'" : "") . ">" . htmlentities($field["TITRE"], ENT_QUOTES, $charset) . "</option>";
        }
        return $options;
    }

    // Affichage de l'écran de gestion des paramètres perso (la liste de tous les champs définis)
    public function show_field_list()
    {
        global $type_list_empr;
        global $datatype_list;
        global $form_list;
        global $msg;

        $query = "select editorial_type_label, editorial_type_element, editorial_type_comment from cms_editorial_types where id_editorial_type = " . $this->num_type;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
        }

        if (strpos($row->editorial_type_element, "generic") !== false) {
            $res = "<h3>" . $msg['editorial_content_type_fieldslist_' . $row->editorial_type_element . '_definition'] . "</h3>";
        } else {
            $res = "<h3>" . sprintf($msg['editorial_content_type_fieldslist_definition'], $row->editorial_type_label) . "</h3>";
        }
        $res .= "
		<div class='row'>&nbsp;<div>";
        $requete = "select idchamp, name, titre, type, datatype, multiple, obligatoire, ordre ,search, export,exclusion_obligatoire, opac_sort from " . $this->prefix . "_custom where num_type = " . $this->num_type . " order by ordre";
        $resultat = pmb_mysql_query($requete);
        /*
         * if(!$resultat)
         * {
         * echo "ya pas de res : ".pmb_mysql_num_rows($resultat)."<br />";
         * }
         * echo "nombre : ".pmb_mysql_num_rows($resultat)."<br />";
         */
        if (pmb_mysql_num_rows($resultat) == 0) {
            $res .= "<br /><br />" . $msg["parperso_no_field"] . "<br />";
            $form_list = str_replace("!!liste_champs_perso!!", $res, $form_list);
            $form_list = str_replace("!!base_url!!", $this->base_url, $form_list);
        } else {
            $res .= "<table style='width:100%'>\n";
            $res .= "<tr><th></th><th>" . $msg["parperso_field_name"] . "</th><th>" . $msg["parperso_field_title"] . "</th><th>" . $msg["parperso_input_type"] . "</th><th>" . $msg["parperso_data_type"] . "</th>";
            if (isset($this->option_visibilite["multiple"]) && $this->option_visibilite["multiple"] == "block")
                $res .= "<th>" . ((strpos($this->prefix, "gestfic") !== false) ? $msg["parperso_fiche_visibility"] : $msg["parperso_opac_visibility"]) . "</th>";
            if (isset($this->option_visibilite["opac_sort"]) && $this->option_visibilite["opac_sort"] == "block")
                $res .= "<th>" . $msg["parperso_opac_sort"] . "</th>";
            if (isset($this->option_visibilite["obligatoire"]) && $this->option_visibilite["obligatoire"] == "block")
                $res .= "<th>" . $msg["parperso_mandatory"] . "</th>";
            if (isset($this->option_visibilite["search"]) && $this->option_visibilite["search"] == "block")
                $res .= "<th>" . $msg["parperso_field_search_tableau"] . "</th>";
            if (isset($this->option_visibilite["export"]) && $this->option_visibilite["export"] == "block")
                $res .= "<th>" . $msg["parperso_exportable"] . "</th>";
            if (isset($this->option_visibilite["exclusion"]) && $this->option_visibilite["exclusion"] == "block")
                $res .= "<th>" . $msg["parperso_exclusion_entete"] . "</th></tr>\n";
            else
                $res .= "</tr>\n";
            $parity = 1;
            $n = 0;
            while ($r = pmb_mysql_fetch_object($resultat)) {
                if ($parity % 2) {
                    $pair_impair = "even";
                } else {
                    $pair_impair = "odd";
                }
                $parity += 1;
                $tr_javascript = " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
                $action_td = " onmousedown=\"document.location='" . $this->base_url . "&action=edit&id=$r->idchamp';\" ";
                $res .= "<tr class='$pair_impair' style='cursor: pointer' $tr_javascript>";
                $res .= "<td>";
                $res .= "<input type='button' class='bouton_small' value='-' onClick='document.location=\"" . $this->base_url . "&action=up&id=" . $r->idchamp . "\"'/></a><input type='button' class='bouton_small' value='+' onClick='document.location=\"" . $this->base_url . "&action=down&id=" . $r->idchamp . "\"'/>";
                $res .= "</td>";
                $res .= "<td $action_td><b>" . $r->name . "</b></td><td $action_td>" . $r->titre . "</td><td $action_td>" . $type_list_empr[$r->type] . "</td><td $action_td>" . $datatype_list[$r->datatype] . "</td>";
                if (isset($this->option_visibilite["multiple"]) && $this->option_visibilite["multiple"] == "block") {
                    $res .= "<td $action_td>";
                    if ($r->multiple == 1)
                        $res .= $msg["40"];
                    else
                        $res .= $msg["39"];
                    $res .= "</td>";
                }
                if (isset($this->option_visibilite["opac_sort"]) && $this->option_visibilite["opac_sort"] == "block") {
                    $res .= "<td $action_td>";
                    if ($r->opac_sort == 1)
                        $res .= $msg["40"];
                    else
                        $res .= $msg["39"];
                    $res .= "</td>";
                }
                if (isset($this->option_visibilite["obligatoire"]) && $this->option_visibilite["obligatoire"] == "block") {
                    $res .= "<td $action_td>";
                    if ($r->obligatoire == 1)
                        $res .= $msg["40"];
                    else
                        $res .= $msg["39"];
                    $res .= "</td>";
                }
                if (isset($this->option_visibilite["search"]) && $this->option_visibilite["search"] == "block") {
                    $res .= "<td $action_td>";
                    if ($r->search == 1)
                        $res .= $msg["40"];
                    else
                        $res .= $msg["39"];
                    $res .= "</td>";
                }
                if (isset($this->option_visibilite["export"]) && $this->option_visibilite["export"] == "block") {
                    $res .= "<td $action_td>";
                    if ($r->export == 1)
                        $res .= $msg["40"];
                    else
                        $res .= $msg["39"];
                    $res .= "</td>";
                }
                if (isset($this->option_visibilite["exclusion"]) && $this->option_visibilite["exclusion"] == "block") {
                    $res .= "<td $action_td>";
                    if ($r->exclusion_obligatoire == 1)
                        $res .= $msg["40"];
                    else
                        $res .= $msg["39"];
                    $res .= "</td>";
                }
                $res .= "</tr>\n";
            }
            $res .= "</table>";
            $form_list = str_replace("!!liste_champs_perso!!", $res, $form_list);
            $form_list = str_replace("!!base_url!!", $this->base_url, $form_list);
        }
        // ajout d'un bouton retour à la liste...
        if (strpos($row->editorial_type_element, "generic") !== false) {
            $base_url = str_replace($row->editorial_type_element, substr($row->editorial_type_element, 0, strpos($row->editorial_type_element, "_")), $this->base_url);
        } else {
            $base_url = $this->base_url;
        }
        $form_list .= "&nbsp;<input type='button' class='bouton' value=' " . $msg['editorial_content_type_fieldslist_back'] . " ' onclick='document.location=\"" . str_replace("&quoi=fields&type_id=" . $this->num_type, "&action=", $base_url) . "\"'/>";
        return $form_list;
    }

    // Récupération des valeurs stockées dans les base pour un emprunteur ou autre
    public function get_out_values($id)
    {
        $id = intval($id);
        // Récupération des valeurs stockées
        if ((! $this->no_special_fields) && ($id)) {
            $this->values = array();
            $requete = "select " . $this->prefix . "_custom_champ," . $this->prefix . "_custom_origine," . $this->prefix . "_custom_small_text, " . $this->prefix . "_custom_text, " . $this->prefix . "_custom_integer, " . $this->prefix . "_custom_date, " . $this->prefix . "_custom_float, " . $this->prefix . "_custom_order from " . $this->prefix . "_custom_values join " . $this->prefix . "_custom on idchamp=" . $this->prefix . "_custom_champ and num_type = " . $this->num_type . "  where " . $this->prefix . "_custom_origine=" . $id;
            $resultat = pmb_mysql_query($requete);
            while ($r = pmb_mysql_fetch_array($resultat)) {
                $this->values[$this->t_fields[$r[$this->prefix . "_custom_champ"]]["NAME"]]['label'] = $this->t_fields[$r[$this->prefix . "_custom_champ"]]["TITRE"];
                $this->values[$this->t_fields[$r[$this->prefix . "_custom_champ"]]["NAME"]]['id'] = $r[$this->prefix . "_custom_champ"];
                $this->values[$this->t_fields[$r[$this->prefix . "_custom_champ"]]["NAME"]]['values'][] = array(
                    'value' => $r[$this->prefix . "_custom_" . $this->t_fields[$r[$this->prefix . "_custom_champ"]]["DATATYPE"]],
                    'format_value' => $this->get_formatted_output(array(
                        $r[$this->prefix . "_custom_" . $this->t_fields[$r[$this->prefix . "_custom_champ"]]["DATATYPE"]]
                    ), $r[$this->prefix . "_custom_champ"], true),
                    'order' => $r[$this->prefix . "_custom_order"],
                    'details' => $this->get_details($this->t_fields[$r[$this->prefix . "_custom_champ"]], $r[$this->prefix . "_custom_" . $this->t_fields[$r[$this->prefix . "_custom_champ"]]["DATATYPE"]])
                );
            }
            $this->sort_out_values();
        } else
            $this->values = array();
        return $this->values;
    }

    public function delete_all()
    {
        $query = "select idchamp from " . $this->prefix . "_custom where num_type = " . $this->num_type;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $requete = "delete from " . $this->prefix . "_custom where idchamp=$row->idchamp";
                pmb_mysql_query($requete);
                $requete = "delete from " . $this->prefix . "_custom_values where " . $this->prefix . "_custom_champ=$row->idchamp";
                pmb_mysql_query($requete);
                $requete = "delete from " . $this->prefix . "_custom_lists where " . $this->prefix . "_custom_champ=$row->idchamp";
                pmb_mysql_query($requete);
            }
        }
    }

    // Suppression de la base des valeurs d'un emprunteur ou autre...
    public function delete_values($id, $type = "")
    {
        $id = intval($id);
        if ($type) {
            // on va chercher les champs génériques
            $generic_type = $this->get_generic_type($type);
            if ($generic_type) {
                $generic = new cms_editorial_parametres_perso($generic_type);
                $generic->delete_values($id);
            }
        }
        // on récupère la liste des champs associés...
        $query = "select idchamp from " . $this->prefix . "_custom where num_type = " . $this->num_type;
        $result = pmb_mysql_query($query);
        $idchamp = "";
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                if ($idchamp)
                    $idchamp .= ",";
                $idchamp .= $row->idchamp;
            }
        }
        if (! $idchamp)
            $idchamp = "''";

        $requete = "DELETE FROM " . $this->prefix . "_custom_values where " . $this->prefix . "_custom_champ in (" . $idchamp . ") and " . $this->prefix . "_custom_origine=$id";
        pmb_mysql_query($requete);
    }

    // Récupération des valeurs stockées dans les base pour un emprunteur ou autre
    public function get_values($id)
    {
        $id = intval($id);
        // Récupération des valeurs stockées pour l'emprunteur
        $this->values = $this->list_values = array();

        if ((! $this->no_special_fields) && ($id)) {
            $requete = "select " . $this->prefix . "_custom_champ," . $this->prefix . "_custom_origine," . $this->prefix . "_custom_small_text, " . $this->prefix . "_custom_text, " . $this->prefix . "_custom_integer, " . $this->prefix . "_custom_date, " . $this->prefix . "_custom_float, " . $this->prefix . "_custom_order from " . $this->prefix . "_custom_values join " . $this->prefix . "_custom on idchamp=" . $this->prefix . "_custom_champ and num_type = " . $this->num_type . " where " . $this->prefix . "_custom_origine=" . $id;
            $resultat = pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat)) {
                $values = array();
                while ($r = pmb_mysql_fetch_array($resultat)) {
                    $values[$r[$this->prefix . "_custom_champ"]][] = array(
                        'value' => $r[$this->prefix . "_custom_" . $this->t_fields[$r[$this->prefix . "_custom_champ"]]["DATATYPE"]],
                        'format_value' => $this->get_formatted_output(array(
                            $r[$this->prefix . "_custom_" . $this->t_fields[$r[$this->prefix . "_custom_champ"]]["DATATYPE"]]
                        ), $r[$this->prefix . "_custom_champ"], true),
                        'order' => $r[$this->prefix . "_custom_order"]
                    );
                    $this->list_values[] = $r[$this->prefix . "_custom_" . $this->t_fields[$r[$this->prefix . "_custom_champ"]]["DATATYPE"]];
                }
                $this->values = $this->sort_values($values);
            }
        }
    }

    // Affichage des champs à saisir dans le formulaire de modification/création d'un emprunteur ou autre
    public function show_editable_fields($id, $type = "")
    {
        global $aff_list_empr, $charset;
        $perso = array();
        // on va chercher les champs génériques
        $generic_type = $this->get_generic_type($type);
        $generic_check_script = "";
        if ($generic_type) {
            $generic = new cms_editorial_parametres_perso($generic_type, $this->base_url);
            $p = $generic->show_editable_fields($id);
            $perso['FIELDS'] = $p['FIELDS'];
            $perso['CHECK_SCRIPTS'] = $p['CHECK_SCRIPTS'];
        }
        if (! $this->no_special_fields) {
            $this->get_values($id);
            $check_scripts = "";
            reset($this->t_fields);
            foreach ($this->t_fields as $key => $val) {
                if (! isset($this->values[$key]))
                    $this->values[$key] = array();
                $t = array();
                $t["NAME"] = $val["NAME"];
                $t["TITRE"] = $val["TITRE"];

                $field = array();
                $field["ID"] = $key;
                $field["NAME"] = $val["NAME"];
                $field["MANDATORY"] = $val["MANDATORY"];
                $field["SEARCH"] = $val["SEARCH"];
                $field["EXPORT"] = $val["EXPORT"];
                $field["EXCLUSION"] = $val["EXCLUSION"];
                $field["OPAC_SORT"] = $val["OPAC_SORT"];
                $field["ALIAS"] = $val["TITRE"];
                $field["DATATYPE"] = $val["DATATYPE"];
                $field["OPTIONS"] = $val["OPTIONS"];
                $field["VALUES"] = $this->values[$key];
                $field["PREFIX"] = $this->prefix;
                $field["ID_ORIGINE"] = $id;
                eval("\$aff=" . $aff_list_empr[$val["TYPE"]] . "(\$field,\$check_scripts);");
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

    // Enregistrement des champs perso soumis lors de la saisie d'une fichie emprunteur ou autre...
    public function rec_fields_perso($id, $type = "")
    {
        $id = intval($id);
        $this->check_submited_fields();
        $query = "select editorial_type_element from cms_editorial_types where id_editorial_type=" . $this->num_type;
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            $requete = "delete " . $this->prefix . "_custom_values from " . $this->prefix . "_custom_values
 					join cms_editorial_custom on cms_editorial_custom_champ = idchamp
 					join cms_editorial_types on num_type=id_editorial_type
 					and editorial_type_element = '" . pmb_mysql_result($result, 0, 0) . "' 
 					where " . $this->prefix . "_custom_origine=$id";
            pmb_mysql_query($requete);
        }
        if ($type) {
            // Enregistrement des champs personalisés
            // on va chercher les champs génériques
            $generic_type = $this->get_generic_type($type);
            if ($generic_type) {
                $generic = new cms_editorial_parametres_perso($generic_type, $this->base_url);
                $generic->rec_fields_perso($id);
            }
        }
        reset($this->t_fields);
        foreach ($this->t_fields as $key => $val) {
            $name = $val["NAME"];
            global ${$name};
            $value = ${$name};
            if (! empty($value)) {
                for ($i = 0; $i < count($value); $i ++) {
                    if ($value[$i] !== "") {
                        $requete = "insert into " . $this->prefix . "_custom_values (" . $this->prefix . "_custom_champ," . $this->prefix . "_custom_origine," . $this->prefix . "_custom_" . $val["DATATYPE"] . "," . $this->prefix . "_custom_order) values($key,$id,'" . $value[$i] . "',$i)";
                        pmb_mysql_query($requete);
                    }
                }
            }
        }
    }

    // Duplication des champs perso d'un contenu éditorial...
    public function duplicate_fields_perso($id, $duplicate_from_id, $type = "")
    {
        $id = intval($id);
        $duplicate_from_id = intval($duplicate_from_id);
        if ($type) {
            // Enregistrement des champs personalisés
            // on va chercher les champs génériques
            $generic_type = $this->get_generic_type($type);
            if ($generic_type) {
                $generic = new cms_editorial_parametres_perso($generic_type);
                $requete = "select " . $generic->prefix . "_custom_champ," . $generic->prefix . "_custom_origine," . $generic->prefix . "_custom_small_text, " . $generic->prefix . "_custom_text, " . $generic->prefix . "_custom_integer, " . $generic->prefix . "_custom_date, " . $generic->prefix . "_custom_float, " . $generic->prefix . "_custom_order from " . $generic->prefix . "_custom_values join " . $generic->prefix . "_custom on idchamp=" . $generic->prefix . "_custom_champ and num_type = " . $generic_type . " where " . $generic->prefix . "_custom_origine=" . $duplicate_from_id;
                $resultat = pmb_mysql_query($requete);
                while ($r = pmb_mysql_fetch_array($resultat)) {
                    $requete = "insert into " . $generic->prefix . "_custom_values (" . $generic->prefix . "_custom_champ," . $generic->prefix . "_custom_origine," . $generic->prefix . "_custom_small_text, " . $generic->prefix . "_custom_text, " . $generic->prefix . "_custom_integer, " . $generic->prefix . "_custom_date, " . $generic->prefix . "_custom_float, " . $generic->prefix . "_custom_order) 
						values(" . $r[$generic->prefix."_custom_champ"] . "," . $id . ",'" . addslashes($r[$generic->prefix."_custom_small_text"]) . "','" . addslashes($r[$generic->prefix."_custom_text"]) . "','" . addslashes($r[$generic->prefix."_custom_integer"]) . "','" . addslashes($r[$generic->prefix."_custom_date"]) . "','" . addslashes($r[$generic->prefix."_custom_float"]) . "','" . addslashes($r[$generic->prefix."_custom_order"]) . "')";
                    pmb_mysql_query($requete);
                }
            }
        }
        $requete = "select " . $this->prefix . "_custom_champ," . $this->prefix . "_custom_origine," . $this->prefix . "_custom_small_text, " . $this->prefix . "_custom_text, " . $this->prefix . "_custom_integer, " . $this->prefix . "_custom_date, " . $this->prefix . "_custom_float, " . $this->prefix . "_custom_order from " . $this->prefix . "_custom_values join " . $this->prefix . "_custom on idchamp=" . $this->prefix . "_custom_champ and num_type = " . $this->num_type . " where " . $this->prefix . "_custom_origine=" . $duplicate_from_id;
        $resultat = pmb_mysql_query($requete);
        while ($r = pmb_mysql_fetch_array($resultat)) {
            $requete = "insert into " . $this->prefix . "_custom_values (" . $this->prefix . "_custom_champ," . $this->prefix . "_custom_origine," . $this->prefix . "_custom_small_text, " . $this->prefix . "_custom_text, " . $this->prefix . "_custom_integer, " . $this->prefix . "_custom_date, " . $this->prefix . "_custom_float, " . $this->prefix . "_custom_order) 
				values(" . $r[$this->prefix . "_custom_champ"] . "," . $id . ",'" . addslashes($r[$this->prefix . "_custom_small_text"]) . "','" . addslashes($r[$this->prefix . "_custom_text"]) . "','" . addslashes($r[$this->prefix . "_custom_integer"]) . "','" . addslashes($r[$this->prefix . "_custom_date"]) . "','" . addslashes($r[$this->prefix . "_custom_float"]) . "','" . addslashes($r[$this->prefix . "_custom_order"]) . "')";
            pmb_mysql_query($requete);
        }
    }

    public function get_generic_type($type)
    {
        $generic_type = 0;
        if ($type) {
            $query = "select id_editorial_type from cms_editorial_types where editorial_type_element like '" . $type . "_generic'";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_object($result);
                $generic_type = $row->id_editorial_type;
            }
        }
        return $generic_type;
    }

    public function get_formatted_output($values, $field_id, $keep_html = false)
    {
        global $val_list_empr, $charset;

        if (! isset(static::$fields[$this->prefix . '_' . $this->num_type][$field_id])) {
            if (! empty($this->t_fields[$field_id])) {
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id] = array();
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id]["ID"] = $field_id;
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id]["NAME"] = $this->t_fields[$field_id]["NAME"];
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id]["COMMENT"] = $this->t_fields[$field_id]["COMMENT"];
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id]["MANDATORY"] = $this->t_fields[$field_id]["MANDATORY"];
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id]["OPAC_SORT"] = $this->t_fields[$field_id]["OPAC_SORT"];
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id]["ALIAS"] = $this->t_fields[$field_id]["TITRE"];
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id]["DATATYPE"] = $this->t_fields[$field_id]["DATATYPE"];
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id]["OPTIONS"] = $this->t_fields[$field_id]["OPTIONS"];
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id]["VALUES"] = $values;
                static::$fields[$this->prefix . '_' . $this->num_type][$field_id]["PREFIX"] = $this->prefix;
            }
        }
        if (! empty($this->t_fields[$field_id])) {
            $aff = $val_list_empr[$this->t_fields[$field_id]["TYPE"]](static::$fields[$this->prefix . '_' . $this->num_type][$field_id], $values);
        }
        if (isset($aff)) {
            if (is_array($aff)) {
                if ($keep_html) {
                    return $aff['value'];
                }
                return $aff['withoutHTML'];
            }
            return $aff;
        }
        return '';
    }

    public static function get_num_type_from_name($name)
    {
        $query = "select num_type from cms_editorial_custom where name = '" . addslashes($name) . "'";
        $result = pmb_mysql_query($query);
        return pmb_mysql_result($result, 0, 'num_type');
    }

    protected function get_details($custom_field, $id)
    {
        if (! empty($custom_field["TYPE"]) && $custom_field["TYPE"] == "query_auth" && ! empty($id)) {
            return get_authority_details_from_field($custom_field, $id);
        }
        return "";
    }
}