<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_contribution.class.php,v 1.33.2.1 2025/01/16 11:24:28 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($base_path . "/selectors/classes/selector.class.php");
require_once ($base_path . "/selectors/templates/sel_contribution.tpl.php");
require_once ($class_path . '/searcher/searcher_factory.class.php');
require_once ($class_path . '/author.class.php');
require_once ($class_path . "/authority.class.php");
require_once "$class_path/contribution_area/contribution_area_forms_controller.class.php";
require_once $class_path . '/onto/contribution/onto_contribution_datatype_resource_selector.class.php';
require_once $class_path . '/onto/contribution/onto_contribution_datatype_resource_selector_ui.class.php';
require_once ($class_path . '/contribution_area/contribution_area_scenario.class.php');

class selector_contribution extends selector
{

    protected $searcher_contributions_tabs_instance;

    public function __construct($user_input = '')
    {
        parent::__construct($user_input);
        $this->objects_type = 'contribution';
    }

    public function proceed()
    {
        global $msg;
        global $action;
        global $pmb_allow_authorities_first_page;
        global $form_display_mode;

        $entity_form = '';
        $response = '';
        switch ($action) {
            case 'simple_search':
                $entity_form = $this->get_simple_search_form();
                break;
            case 'advanced_search':
                $entity_form = $this->get_advanced_search_form();
                break;
            case 'results_search':
                $response = $this->results_search();
                break;
            case 'results_search_store':
                $response = $this->results_search('store');
                break;
            case 'save_in_store':
                $response = contribution_area_forms_controller::save_in_store($this->data['id'] ?? 0, $this->data['type'] ?? 'record');
                break;
            case 'get_edit_url':
                $entity_form = $this->get_edit_url();
                break;
            default:
                print $this->get_sel_header_template();
                print $this->get_js_script();
                print $this->get_sel_footer_template();
                print $this->get_sub_tabs();
                break;
        }

        if (! empty($entity_form)) {
            header("Content-Type: text/html; charset=UTF-8");
            print encoding_normalize::utf8_normalize($entity_form);
        } elseif (! empty($response)) {
            print encoding_normalize::json_encode($response);
        }
    }

    protected function get_js_script()
    {
        global $jscript;
        global $jscript_common_selector;

        if (! isset($jscript)) {
            $jscript = $jscript_common_selector;
        }
        return $jscript;
    }

    protected function get_form()
    {
        global $msg, $charset;
        global $selector_author_form;
        global $type_autorite;

        $form = $selector_author_form;
        $sel_pp = "";
        $sel_coll = "";
        $sel_con = "";
        $form = str_replace("!!titre_ajout!!", $msg['selector_author_add'], $form);
        $form = str_replace("!!display!!", "display:none", $form);
        $completion = 'authors_person';
        $form = str_replace("!!sel_pp!!", $sel_pp, $form);
        $form = str_replace("!!sel_coll!!", $sel_coll, $form);
        $form = str_replace("!!sel_con!!", $sel_con, $form);
        $form = str_replace("!!completion_name!!", $completion, $form);
        $form = str_replace("!!deb_saisie!!", htmlentities($this->user_input, ENT_QUOTES, $charset), $form);
        $form = str_replace("!!base_url!!", static::get_base_url(), $form);
        return $form;
    }

    protected function get_add_link()
    {
        return $this->data['form_id'] ?? "";
    }

    protected function get_add_label()
    {
        global $msg;
        return $msg['selector_author_add'];
    }

    protected function save()
    {
        global $author_type;
        global $author_name, $author_rejete;
        global $date, $lieu, $ville, $pays;
        global $subdivision, $numero;

        $value['type'] = $author_type;
        $value['name'] = $author_name;
        $value['rejete'] = $author_rejete;
        $value['date'] = $date;
        $value['voir_id'] = 0;
        $value['lieu'] = $lieu;
        $value['ville'] = $ville;
        $value['pays'] = $pays;
        $value['subdivision'] = $subdivision;
        $value['numero'] = $numero;

        $auteur = new auteur();
        $auteur->update($value);
        return $auteur->id;
    }

    protected function get_authority_instance($authority_id = 0, $object_id = 0)
    {
        // return new authority($authority_id, $object_id, AUT_TABLE_AUTHORS);
        return authorities_collection::get_authority('authority', $authority_id, [
            'num_object' => $object_id,
            'type_object' => AUT_TABLE_AUTHORS
        ]);
    }

    /**
     *
     * @param int $id
     * @param int $object_id
     *
     * @return string
     *
     * @see selector::get_display_object($id, $object_id)
     */
    protected function get_display_object($id = 0, $object_id = 0)
    {
        global $msg, $charset;
        global $caller;
        global $callback;

        $display = '';
        $authority = $this->get_authority_instance($id, $object_id);
        $author = $authority->get_object_instance(array(
            'recursif' => 1
        ));
        $author_voir = "";
        // gestion des voir :
        if ($author->see) {
            $auteur_see = new auteur($author->see);
            $author_voir = $auteur_see->authority->get_display_statut_class_html() . "<a href='#' onclick=\"set_parent('$caller', '$author->see', '" . htmlentities(addslashes($auteur_see->get_isbd()), ENT_QUOTES, $charset) . "','$callback')\">" . htmlentities($auteur_see->get_isbd(), ENT_QUOTES, $charset) . "</a>";
            $author_voir = ".&nbsp;-&nbsp;<i>" . $msg['see'] . "</i>&nbsp;:&nbsp;" . $author_voir;
        }
        $display .= "<div class='row'>";
        $display .= pmb_bidi($authority->get_display_statut_class_html() . "<a href='#' onclick=\"set_parent('$caller', '" . $authority->get_num_object() . "', '" . htmlentities(addslashes($author->get_isbd()), ENT_QUOTES, $charset) . "','$callback')\">" . $author->get_isbd() . "</a>");
        $display .= pmb_bidi($author_voir);
        $display .= "</div>";
        return $display;
    }

    protected function get_searcher_instance()
    {
        return searcher_factory::get_searcher('authors', '', $this->user_input);
    }

    protected function get_link_pagination()
    {
        global $rech_regexp;
        global $type_autorite;

        $type_autorite = intval($type_autorite);
        $link = static::get_base_url() . "&rech_regexp=$rech_regexp&user_input=" . rawurlencode($this->user_input) . "&type_autorite=" . $type_autorite . "&page=!!page!!";
        return $link;
    }

    public function get_sel_header_template()
    {
        global $base_path;

        $sel_header = "
			<div id='att' style='z-Index:1000'></div>
			<script src='" . $base_path . "/includes/javascript/ajax.js'></script>
		    <div class='row'>
			    <label for='selector_title' class='etiquette'></label>
			</div>
			<div class='row'>
			";
        return $sel_header;
    }

    public function get_sel_search_form_template()
    {
        global $msg, $charset;

        $sel_search_form = "
			<form name='" . $this->get_sel_search_form_name() . "' method='post' action='" . static::get_base_url() . "'>
				<input type='text' name='f_user_input' value=\"" . htmlentities($this->user_input, ENT_QUOTES, $charset) . "\">
				&nbsp;
				<input type='submit' class='bouton_small' value='" . $msg[142] . "' />
				!!bouton_ajouter!!
			</form>
			<script>
				<!--
				document.forms['" . $this->get_sel_search_form_name() . "'].elements['f_user_input'].focus();
				-->
			</script>
		";
        return $sel_search_form;
    }

    public function get_title()
    {
        global $msg;
        return $msg["empr_menu_contribution_area"];
    }

    protected function get_search_form()
    {
        global $charset;
        global $bt_ajouter;

        $sel_search_form = $this->get_sel_search_form_template();
        // if($bt_ajouter == "no"){
        // $sel_search_form = str_replace("!!bouton_ajouter!!", '', $sel_search_form);
        // } else {
        $bouton_ajouter = "<input type='button' class='bouton_small' onclick=\"document.location='" . $this->get_add_link() . "'\" value='" . htmlentities($this->get_add_label(), ENT_QUOTES, $charset) . "' />";
        $sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
        // }
        $sel_pp = "";
        $sel_coll = "";
        $sel_con = "";
        $sel_all = "";
        $sel_all = "selected";
        $sel_search_form = str_replace("!!sel_pp!!", $sel_pp, $sel_search_form);
        $sel_search_form = str_replace("!!sel_coll!!", $sel_coll, $sel_search_form);
        $sel_search_form = str_replace("!!sel_con!!", $sel_con, $sel_search_form);
        $sel_search_form = str_replace("!!sel_all!!", $sel_all, $sel_search_form);
        return $sel_search_form;
    }

    protected function get_searcher_tabs_instance()
    {
        if (! isset($this->searcher_tabs_instance)) {
            $type = $this->data['type'] ?? "record";
            switch ($type) {
                case "record":
                case "serial" :
                case "bull":
                    $this->searcher_tabs_instance = new searcher_selectors_tabs('records');
                    break;
                default:
                    $this->searcher_tabs_instance = new searcher_selectors_tabs('authorities');
                    break;
            }
        }
        return $this->searcher_tabs_instance;
    }

    public function get_objects_type()
    {
        if (isset($this->data['type'])) {
            return entities::get_searcher_mode_from_type($this->data['type']);
        }
        return $this->objects_type;
    }

    protected function get_sel_search_form_name()
    {
        if (! empty($this->objects_type) && ! empty($this->data['form_id'])) {
            return "selector_" . $this->objects_type . "_search_form_" . $this->data['form_id'];
        } else {
            return "selector_search_form";
        }
    }

    protected function get_sub_tabs()
    {
        global $tab_id, $pmb_contribution_opac_accordion_result, $sub_title;

        $FormToCall = "FormContributionTabSelector";
        if ($pmb_contribution_opac_accordion_result) {
            $FormToCall = "FormContributionAccordionSelector";
        }

        $current_url = static::get_base_url();
        // Ajout du params from_contrib afin de récuperer les bons résultats de recherches dans le store
        $current_url .= "&from_contrib=1";
        $current_url = str_replace('select.php?', 'ajax.php?module=selectors&', $current_url);
        $sub_tab_add = 0;
        $form_url = '';
        $select_tab = 0;
        $multi_scenario = 0;

        $form_url = $this->generate_form_url();

        if (! empty($this->data['multiple_scenarios']) && empty($this->data['edit_contribution'])) {
            $multi_scenario = 1;
        }

        if (! empty($this->data['create']) || ! empty($this->data['edit_contribution'])) {
            $sub_tab_add = 1;
        }

        // Permet de d'activer la sélection de la tab
        if (! empty($this->data['select_tab'])) {
            $select_tab = 1;
        }
        $searcher_tab = $this->get_searcher_tabs_instance();
        return '
        <div id="widget-container_' . $tab_id . '"></div>
        <script>
            require(["apps/pmb/form/contribution/' . $FormToCall . '", "dojo/dom"], function(' . $FormToCall . ', dom){
                new ' . $FormToCall . '({
                        doLayout: false,
                        selectorURL:"' . $current_url . '",
                        multicriteriaMode: "' . $searcher_tab->get_mode_multi_search_criteria() . '",
                        subTabAdd:' . $sub_tab_add . ',
                        subTabEdit:' . ($this->data['edit_contribution'] ?? '0') . ',
                        formURL:"' . $form_url . '",
                        selectTab:' . $select_tab . ',
                        multiScenario:' . $multi_scenario . ',
                        multiScenarioTitle : "' . $sub_title . '",
                        tabId:"' . $tab_id . '",
                        noSearch:"' . intval($this->data['no_search']) . '",
                    },
                    "widget-container_' . $tab_id . '"
                );
            });
        </script>
        ';
    }

    private function get_edit_url()
    {
        $html = "";
        if (! empty($this->data['entity_uri'])) {
            $entity = onto_contribution_datatype_resource_selector::get_properties_from_uri($this->data['entity_uri']);
            $type = $entity['http://www.pmbservices.fr/ontology#sub'] ?? onto_contribution_datatype_resource_selector_ui::get_type_from_range($entity['http://www.w3.org/1999/02/22-rdf-syntax-ns#type']);
            if (! empty($entity['http://www.pmbservices.fr/ontology#parent_scenario_uri']) && ! empty($this->data['scenario']) && $this->data['scenario'] == $entity['http://www.pmbservices.fr/ontology#parent_scenario_uri']) {
                $params["is_draft"] = $this->data['is_draft'];
                $html = onto_contribution_datatype_resource_selector_ui::get_edit_url($entity, $entity['http://www.pmbservices.fr/ontology#parent_scenario_uri'], $type, $params);
            } else if ('convert' === $this->data['sub']) {
                $params['id'] = $entity['identifier'];
                $params['is_entity'] = true;
                $params['is_draft'] = false;
                $params['sub_form'] = 1;
                $html = onto_contribution_datatype_resource_selector_ui::get_edit_url($entity, $this->data['scenario'], $type, $params);
            }
        }
        return $html;
    }

    protected function get_simple_search_form()
    {
        global $current_module;
        global $msg;
        global $mode;
        global $current_scenario_id;

        // onglets de recherche objets
        $searcher_tabs = $this->get_searcher_tabs_instance();
        if (empty($mode)) {
            $mode = $searcher_tabs->get_mode_objects_type($this->get_objects_type());
        }

        if (! empty($current_scenario_id)) {
            $this->data['scenario'] = $current_scenario_id;
        }

        if (empty($this->data['scenario'])) {
            $this->data['scenario'] = 0;
        }

        if (empty($this->data['area_id'])) {
            $this->data['area_id'] = 0;
        }
        $scenario = new contribution_area_scenario($this->data['scenario'], $this->data['area_id']);
        $equation = $scenario->get_equation_query();

        if (! $equation) {
            $equation = $this->data['equation'] ?? "";
        }
        $form = "";
        // onglets de recherche objets
        $searcher_tabs->set_current_mode($mode);

        $form .= "
            <form id='" . $this->get_sel_search_form_name() . "' name='" . $this->get_sel_search_form_name() . "' class='form-" . $current_module . "' action='' method='post' onSubmit='return searcher_tabs_check_form(\"" . $this->get_sel_search_form_name() . "\");'>
              <div class='form-contenu'>";
        $form .= $searcher_tabs->get_content_form();
        $form .= "
            <div class='row contrib_select_search_table'>
                <label> " . $msg['select_search_tab_title'] . "</label>
            </div>
            <div class='row contrib_select_search_table uk-clearfix'>
                <input type='radio' id='base' name='selectResultTabSearch' value='base' checked>
                <span for='base'>" . $msg['select_search_tab_fond'] . "</span>
            </div>";
        if (! $this->data['is_entity']) {
            $form .= "<div class='row contrib_select_search_table uk-clearfix'>
                <input type='radio' id='contrib' name='selectResultTabSearch' value='contribution'>
                <span for='contrib'>" . $msg['select_search_tab_contribution'] . "</span>
            </div>";
        }
        $form .= "<div class='row'></div>
            <div class='row'>
                <input type='hidden' value='" . $equation . "' name='equation'/>
                <input type='hidden' value='$mode' name='mode'/>
                <input class='bouton' type='button' id='launch_search_button' value='" . $msg['10'] . "' />
          </div>
        </form>";
        $form .= $searcher_tabs->get_script_js_form($this->get_sel_search_form_name());
        return $form;
    }

    protected function results_search($where = '')
    {
        global $mode;

        switch ($where) {
            case 'store':
                $searcher_tabs = $this->get_searcher_contributions_tabs_instance();
                break;
            default:
                $searcher_tabs = $this->get_searcher_tabs_instance();
                break;
        }
        $searcher_tabs->set_current_mode($mode);

        ob_start();
        $searcher_tabs->proceed_search();
        $content = ob_get_contents();
        ob_end_clean();

        return [
            'results' => $content,
            'nb_results' => $searcher_tabs->get_search_nb_results()
        ];
    }

    protected function get_searcher_contributions_tabs_instance()
    {
        if (! isset($this->searcher_contributions_tabs_instance)) {
            $type = $this->data['type'] ?? "record";
            switch ($type) {
                case "record":
                    $this->searcher_tabs_instance = new searcher_contributions_tabs('records');
                    break;
                default:
                    $this->searcher_tabs_instance = new searcher_contributions_tabs('authorities');
                    break;
            }
        }
        return $this->searcher_tabs_instance;
    }

    private function generate_form_url()
    {
        $form_url = "";

        if (empty($this->data['multiple_scenarios']))
            $this->data['multiple_scenarios'] = false;

        if (! empty($this->data['is_entity']) && $this->data['is_entity']) {
            $form_url = $this->make_url_for_entity_done();
        } else {
            $form_url = $this->make_url_for_store_entity();
        }
        return $form_url;
    }

    private function make_url_for_entity_done()
    {
        $form_url = "";
        if (! $this->data['multiple_scenarios'] && $this->data['edit_contribution']) {
            // On est en édition d'une entité
            $form_url = './ajax.php?module=ajax&categ=contribution&sub=convert';
            $form_url .= '&action=' . (! empty($this->data['id']) ? 'edit_entity' : 'edit');
            $form_url .= '&sub_tab=1';
            $form_url .= '&sub_form=1';
            $form_url .= '&entity_type=' . ($this->data['type'] ?? '');
            $form_url .= '&entity_id=' . ($this->data['id'] ?? '0');
            $form_url .= '&area_id=' . ($this->data['area_id'] ?? '0');
            $form_url .= '&form_id=' . ($this->data['form_id'] ?? '0');
            $form_url .= '&form_uri=' . ($this->data['form_uri'] ?? '');
            $form_url .= '&scenario_uri=' . ($this->data['scenario'] ?? '');
            $form_url .= '&entity=true';
        } else {
            if (! empty($this->data['create'])) {
                // On est en création
                $form_url = './ajax.php?module=ajax&categ=contribution&sub=scenario_child&id=0';
                $form_url .= '&area_id=' . ($this->data['area_id'] ?? '0');
                $form_url .= '&scenario=' . ($this->data['scenario'] ?? '0');
                $form_url .= '&create_entity=true';
            }

            if ($this->data['multiple_scenarios'] && empty($this->data['edit_contribution'])) {
                $form_url = './ajax.php?module=ajax&categ=contribution&sub=attachment&id=0';
                $form_url .= '&area_id=' . ($this->data['area_id'] ?? '0');
                $form_url .= '&attachment=' . ($this->data['attachment'] ?? '0');
            }

            $form_url .= '&sub_tab=1';
            $form_url .= '&entity=true';
            $form_url .= '&action=' . (! empty($this->data['id']) ? 'edit_entity' : 'edit');
            $form_url .= '&entity_type=' . ($this->data['type'] ?? '');
        }
        return $form_url;
    }

    private function make_url_for_store_entity()
    {
        $form_url = "";

        if ($this->data['multiple_scenarios'] && empty($this->data['edit_contribution'])) {
            $form_url = './ajax.php?module=ajax&categ=contribution&sub=attachment&id=0';
            $form_url .= '&area_id=' . ($this->data['area_id'] ?? '0');
            $form_url .= '&attachment=' . ($this->data['attachment'] ?? '0');
        } else {

            if (! empty($this->data['create'])) {
                $form_url = './ajax.php?module=ajax&categ=contribution&sub=scenario_child&id=0';
                $form_url .= '&area_id=' . ($this->data['area_id'] ?? '0');
                $form_url .= '&scenario=' . ($this->data['scenario'] ?? '0');
                $form_url .= '&sub_tab=1';
                $form_url .= '&is_draft=' . ($this->data['is_draft'] ?? '0');
            }

            if (! empty($this->data['edit_contribution'])) {
                $form_url = './ajax.php?module=ajax&categ=contribution';
                $form_url .= '&sub=' . ($this->data['type'] ?? '');
                $form_url .= '&id=' . ($this->data['id'] ?? '0');
                $form_url .= '&area_id=' . ($this->data['area_id'] ?? '0');
                $form_url .= '&scenario=' . ($this->data['scenario'] ?? '0');
                $form_url .= '&sub_tab=1';
                $form_url .= '&sub_form=1';
                $form_url .= '&form_id=' . ($this->data['form_id'] ?? '0');
                $form_url .= '&form_uri=' . ($this->data['form_uri'] ?? '');
                $form_url .= '&is_draft=' . ($this->data['is_draft'] ?? '0');

                if (! empty($this->data["item_creator"])) {
                    $form_url .= '&origin=' . (urlencode($this->data['origin']) ?? '');
                    $form_url .= '&origin_uri=' . (urlencode($this->data['origin_uri']) ?? '');
                }
            }
        }
        $form_url .= '&entity_type=' . ($this->data['type'] ?? '');

        return $form_url;
    }
}