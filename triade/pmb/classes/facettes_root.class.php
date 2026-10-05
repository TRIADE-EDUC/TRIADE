<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_root.class.php,v 1.42.2.4.2.7 2025/05/23 09:40:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Common\Views\VueJsView;

global $class_path;
require_once($class_path."/facette_search_compare.class.php");
require_once($class_path."/encoding_normalize.class.php");

abstract class facettes_root
{
    /**
     * Objets séparées par des virgules
     * @var string
     */
    public $objects_ids;

    /**
     * Liste des facettes
     * @var array
     */
    public $facettes;

    /**
     * Liste des facettes calculées
     * @var array
     */
    public $tab_facettes;

    /**
     * Flag pour indiquer qu'au moins une des facettes sera affichée
     * @var boolean
     */
    public $exists_with_results = false;

    /**
     * Mode d'affichage (extended/external)
     * @var string
     */
    public $mode = 'extended';

    /**
     * Methode d'affinage (filter / search)
     */
    public static $refining_method = '';
    
    /**
     * Comparateur de notice activé (oui/non)
     * @var string
     */
    protected static $compare_notice_active;

    /**
     * Instance
     * @var facette_search_compare
     */
    protected $facette_search_compare;

    /**
     * Liste des facettes sélectionnées
     * @var array
     */
    protected $clicked;

    /**
     * Liste des facettes non sélectionnées
     * @var array
     */
    protected $not_clicked;

    /**
     * Liste des valeurs par facette hors limite
     */
    protected $facette_plus;

    protected static $facet_type;

    /**
     * Mode de recherche (simple / multi-criteres)
     * @var string
     */
    protected static $search_mode = 'extended_search';

    protected static $hidden_form_name = 'search_form';

    protected static $elements_list_nb_per_page = 20;
    
    protected static $url_base;
    
    protected $uniqid_labels = []; 

    public function __construct($objects_ids = '')
    {
        $this->objects_ids = $objects_ids;
        $this->facette_existing();
        $this->nb_results_by_facette();
    }

    protected static function get_authorities_types()
    {
        return [
            'authors',
            'categories',
            'publishers',
            'collections',
            'subcollections',
            'series',
            'titres_uniformes',
            'indexint'
        ];
    }

    protected static function get_facettes_sets()
    {
        global $PMBuserid;

        $facettes_sets = new facettes_sets(static::$facet_type);
        return $facettes_sets->get_filtered_list($PMBuserid);
    }

    protected static function get_selected_num_facettes_set()
    {
        if (! empty($_SESSION['facettes_sets'][static::$facet_type])) {
            $facettes_set = new facettes_set($_SESSION['facettes_sets'][static::$facet_type]);
            if ($facettes_set->get_id()) {
                if (static::$facet_type == $facettes_set->get_type() || (static::$facet_type == 'authorities' && in_array($facettes_set->get_type(), facettes_sets::get_authorities_types()))) {
                    return $_SESSION['facettes_sets'][static::$facet_type];
                }
            }
        }
        $objects = static::get_facettes_sets();
        if (! empty($objects)) {
            return $objects[0]->get_id();
        }
    }

    protected static function get_query_restricts($selected = 0)
    {
        $selected = intval($selected);
        $where = "WHERE facette_visible=1";
        if (! empty(static::$facet_type)) {
            if (static::$facet_type == 'authorities') {
                $authorities_types = static::get_authorities_types();
                $where .= " AND (facette_type IN ('" . implode("','", $authorities_types) . "') OR facette_type LIKE 'authperso%')";
            } else {
                $where .= " AND facette_type = '" . addslashes(static::$facet_type) . "'";
            }
            if ($selected) {
                $where .= " AND num_facettes_set = " . $selected;
            }
        }
        return $where;
    }

    protected function get_query()
    {
        $selected = static::get_selected_num_facettes_set();
        $query = "SELECT * FROM " . static::$table_name . " JOIN facettes_sets ON id_set = num_facettes_set";
        $query .= " " . static::get_query_restricts($selected);
        $query .= "  ORDER BY facette_order, facette_name";
        return $query;
    }

    protected function facette_existing()
    {
        global $opac_view_filter_class;

        $this->facettes = [];
        $query = $this->get_query();
        $result = pmb_mysql_query($query);
        while ($row = pmb_mysql_fetch_object($result)) {
            if ($opac_view_filter_class) {
                if (!$opac_view_filter_class->is_selected(static::$table_name, $row->id_facette+0)) {
                    continue;
                }
            }
            $this->facettes[] = [
                'id' => intval($row->id_facette),
                'name' => translation::get_translated_text($row->id_facette, 'facettes', 'facette_name', $row->facette_name),
                'id_critere' => intval($row->facette_critere),
                'id_ss_critere' => intval($row->facette_ss_critere),
                'nb_result' => intval($row->facette_nb_result),
                'limit_plus' => intval($row->facette_limit_plus),
                'type_sort' => intval($row->facette_type_sort),
                'order_sort' => intval($row->facette_order_sort),
                'datatype_sort' => $row->facette_datatype_sort
            ];
        }
    }

    public function nb_results_by_facette()
    {
        global $msg;

        $this->tab_facettes = [];
        if ($this->objects_ids != "") {
            foreach ($this->facettes as $facette) {
                $query = $this->get_query_by_facette($facette['id_critere'], $facette['id_ss_critere']);
                if ($facette['type_sort'] == 0) {
                    $query .= " nb_result";
                } else {
                    if ($facette['datatype_sort'] == 'date') {
                        $query .= " STR_TO_DATE(value,'".$msg['format_date']."')";
                    } elseif ($facette['datatype_sort'] == 'num') {
                        $query .= " value*1";
                    } else {
                        $query .= " value";
                    }
                }
                if ($facette['order_sort'] == 0) {
                    $query .= " asc";
                } else {
                    $query .= " desc";
                }
                if ($facette['nb_result'] > 0) {
                    $query .= " LIMIT"." ".$facette['nb_result'];
                }
                $result = pmb_mysql_query($query);
                $j = 0;
                $array_tmp = [];
                $array_value = [];
                $array_nb_result = [];
                if ($result && pmb_mysql_num_rows($result)) {
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $array_tmp[$j] = $row->value." "."(".intval($row->nb_result).")";
                        $array_value[$j] = $row->value;
                        $array_nb_result[$j] = intval($row->nb_result);
                        $j++;
                    }
                    $this->exists_with_results = true;
                }
                $this->tab_facettes[] = [
                    'name' => $facette['name'],
                    'facette' => $array_tmp,
                    'code_champ' => $facette['id_critere'],
                    'code_ss_champ' => $facette['id_ss_critere'],
                    'value' => $array_value,
                    'nb_result' => $array_nb_result,
                    'size_to_display' => $facette['limit_plus']
                ];
            }
        }
    }

    protected static function get_link_not_clicked($name, $label, $code_champ, $code_ss_champ, $id, $nb_result)
    {
        $datas = array(
            $name,
            $label,
            $code_champ,
            $code_ss_champ,
            $id,
            $nb_result
        );
        $link = "facettes_valid_facette(" . encoding_normalize::json_encode($datas) . ");";
        return $link;
    }

    public static function see_more($json_facette_plus)
    {
        global $charset;

        $arrayRetour = [];
        if (!empty($json_facette_plus['facette'])) {
            for ($j=0; $j<count($json_facette_plus['facette']); $j++) {
                $facette_libelle = static::get_formatted_value($json_facette_plus['code_champ'], $json_facette_plus['code_ss_champ'], $json_facette_plus['value'][$j]);
                $facette_id = facette_search_compare::gen_compare_id($json_facette_plus['name'], $json_facette_plus['value'][$j], $json_facette_plus['code_champ'], $json_facette_plus['code_ss_champ'], $json_facette_plus['nb_result'][$j]);
                $facette_value = encoding_normalize::json_encode([
                    $json_facette_plus['name'],
                    $json_facette_plus['value'][$j],
                    $json_facette_plus['code_champ'],
                    $json_facette_plus['code_ss_champ'],
                    $facette_id,
                    $json_facette_plus['nb_result'][$j]
                ]);
                if ($facette_libelle) {
                    $arrayRetour[] = [
                        'facette_libelle' => htmlentities($facette_libelle, ENT_QUOTES, $charset),
                        'facette_number' => htmlentities($json_facette_plus['nb_result'][$j], ENT_QUOTES, $charset),
                        'facette_id' => $facette_id,
                        'facette_value' => htmlentities($facette_value, ENT_QUOTES, $charset),
                        'facette_link' => static::get_link_not_clicked($json_facette_plus['name'], $json_facette_plus['value'][$j], $json_facette_plus['code_champ'], $json_facette_plus['code_ss_champ'], $facette_id, $json_facette_plus['nb_result'][$j]),
                        'facette_code_champ' => htmlentities($json_facette_plus['code_champ'], ENT_QUOTES, $charset)
                    ];
                }
            }
        }
        return encoding_normalize::json_encode($arrayRetour);
    }

    public static function destroy_dom_node()
    {
        if ($_SESSION["cms_build_activate"]) {
            return "";
        } else {
            return "
				<script>
							require(['dojo/ready', 'dojo/dom-construct'], function(ready, domConstruct){
								ready(function(){
									domConstruct.destroy('facette');
								});
							});
				</script>";
        }
    }

    public static function get_nb_facettes()
    {
        $query = "SELECT count(id_facette) FROM " . static::$table_name;
        if (static::$table_name == 'facettes_external') {
            $query .= " WHERE facette_visible_gestion=1";
            if (! empty(static::$facet_type)) {
                $query .= " AND facette_type = '" . addslashes(static::$facet_type) . "'";
            }
        } else {
            $query .= " " . static::get_query_restricts();
        }
        $result = pmb_mysql_query($query);
        return pmb_mysql_result($result, 0);
    }

    protected static function get_ajax_base_url()
    {
        global $base_path;

        return $base_path."/ajax.php?module=ajax&categ=".static::$table_name."&search_mode=".static::$search_mode;
    }

    protected static function get_ajax_url()
    {
        return static::get_ajax_base_url()."&sub=get_data&hidden_form_name=".static::$hidden_form_name."&facet_type=".static::$facet_type;
    }

    protected static function get_ajax_filtered_data_url()
    {
        return static::get_ajax_base_url()."&sub=get_filtered_data&hidden_form_name=".static::$hidden_form_name."&facet_type=".static::$facet_type;
    }

    public static function call_ajax_facettes()
    {
        $ajax_facettes = "";
        if (static::get_nb_facettes()) {
            $ajax_facettes .= static::get_facette_wrapper();
            $ajax_facettes .= "
            <div id='facette_wrapper'>
                <img src='".get_url_icon('patience.gif')."'/>";
            
            $default_facettes = static::get_default_facette_search();
            if (!empty($default_facettes)) {
                $ajax_facettes .= "<script>valid_facettes_multi(".encoding_normalize::json_encode($default_facettes).");</script>";
            } else {
                $ajax_url = static::get_ajax_url();
                $ajax_facettes .= <<<HTML
                        <script>
                            var req = new http_request();
                            req.request("{$ajax_url}",false,null,true,function(data){
                                var response = JSON.parse(data);
                                document.getElementById('facette_wrapper').innerHTML=response.display;
                                if(!response.exists_with_results) {
                                    require(['dojo/dom-construct', 'dojo/domReady!'], function(domConstruct){
                                        domConstruct.destroy('facettes_list');
                                    });
                                } else {
                                    document.getElementById('results_list').classList.add('has_facettes');
                                }
                            }, '', '', true);
                        </script>
                HTML;
            }
            $ajax_facettes .= "</div>";
        }
        return $ajax_facettes;
    }

    public static function make_facette($objects_ids)
    {
        $return = "";
        $class_name = static::class;
        $facettes = new $class_name($objects_ids);
        if ($facettes->exists_with_results) {
            $return .= static::get_facette_wrapper();
            $return .= $facettes->create_ajax_table_facettes();
        } else {
            $return .= self::destroy_dom_node();
        }
        return $return;
    }

    public static function make_ajax_facette($objects_ids)
    {
        $class_name = static::class;
        $facettes = new $class_name($objects_ids);
        return [
            'exists_with_results' => (isset($_SESSION["cms_build_activate"]) && $_SESSION["cms_build_activate"] ? true : $facettes->exists_with_results),
            'display' => $facettes->create_ajax_table_facettes()
        ];
    }

    protected static function get_ajax_see_more_url()
    {
        return static::get_ajax_base_url()."&sub=see_more";
    }

    protected static function get_ajax_filters_url($action='')
    {
        return static::get_ajax_base_url()."&sub=filters&action=".$action."&facet_type=".static::$facet_type."&hidden_form_name=".static::$hidden_form_name."&elements_list_nb_per_page=".static::$elements_list_nb_per_page;
    }
    
    protected static function get_ajax_session_default_values_url()
    {
        return static::get_ajax_base_url()."&sub=session_default_values&facet_type=".static::$facet_type;
    }
    
    public static function get_modal_data()
    {
        $data = [];
        $data['form_name'] = "facettes_multi";
        return $data;
    }
    
    public static function get_default_facette_search()
    {
        $default_facettes = [];
        $session_values = static::get_session_values();
        if (static::$refining_method == 'filter' && !empty($session_values)) {
            foreach ($session_values as $facettes_values) {
                foreach ($facettes_values as $facette_values) {
                    if (!isset($facette_values[2])) {
                        $facette_values[2] = 0;
                    }
                    if (!isset($facette_values[3])) {
                        $facette_values[3] = 0;
                    }
                    $default_facettes[][] = $facette_values;
                }
            }
        }
        return $default_facettes;
    }
    
    public static function get_facette_wrapper()
    {
        global $base_path, $pmb_facettes_modal_activate, $opac_rgaa_active;
        
        $script = "";
        
        if (1 == $pmb_facettes_modal_activate) {
            $vueJsView = new VueJsView("facettes/modal", static::get_modal_data());
            $script .= $vueJsView->render();
        }
        
        $script .= "
		<script src='".$base_path."/javascript/select.js' type='text/javascript'></script>
        <script type='text/javascript'>
            var facettes_hidden_form_name = '".static::$hidden_form_name."';
            var facettes_ajax_see_more_url = '".static::get_ajax_see_more_url()."';
            var facettes_ajax_filtered_data_url = '".static::get_ajax_filtered_data_url()."';
            var facettes_ajax_filters_get_elements_url = '".static::get_ajax_filters_url('get_elements')."';
            var facettes_ajax_session_default_values_url = '".static::get_ajax_session_default_values_url()."';
			var facettes_display_fieldsets = '".($opac_rgaa_active ? true : false)."';
			var facettes_modal_activate = '".intval($pmb_facettes_modal_activate)."';
        </script>
        <script src='".$base_path."/javascript/facettes.js' type='text/javascript'></script>
		<script>
            function facettes_get_mode() {
                return '".static::$refining_method."';
            }
            ";
        if (static::get_compare_notice_active()) {
            $compare_class_name = static::$compare_class_name;
            $script .= $compare_class_name::get_compare_wrapper();
        }
        $script .= "</script>";
       
        return $script;
    }

    public static function destroy_global_search_element($indice)
    {
        global $search;

        $nb_search = count($search);
        for ($i=$indice; $i<=$nb_search; $i++) {
            $op = "op_".$i."_".$search[$i];
            $field_ = "field_".$i."_".$search[$i];
            $inter = "inter_".$i."_".$search[$i];
            $fieldvar = "fieldvar_".$i."_".$search[$i];
            global ${$op};
            global ${$field_};
            global ${$inter};
            global ${$fieldvar};
            if ($i == $nb_search) {
                unset($GLOBALS[$op]);
                unset($GLOBALS[$field_]);
                unset($GLOBALS[$inter]);
                unset($GLOBALS[$fieldvar]);
                unset($search[$i]);
                array_pop($search);
            } else {
                // on décale
                $n = $i+1;
                $search[$i] = $search[$n];
                $op = "op_".$n."_".$search[$n];
                $field_ = "field_".$n."_".$search[$n];
                $inter = "inter_".$n."_".$search[$n];
                $fieldvar = "fieldvar_".$n."_".$search[$n];
                global ${$op_next};
                global ${$field_next};
                global ${$inter_next};
                global ${$fieldvar_next};

                ${$op} = ${$op_next};
                ${$field_} = ${$field_next};
                ${$inter} = ${$inter_next};
                ${$fieldvar} = ${$fieldvar_next};
            }
        }
    }

    public static function checked_facette_search()
    {
        global $param_delete_facette, $param_default_facette;

        $session_values = static::get_session_values();
        if (!is_array($session_values)) {
            $session_values = [];
        }
        // Suppression facette
        if ($param_delete_facette != "") {
            // On évite le rafraichissement de la page
            static::delete_session_value($param_delete_facette);
        } elseif (!empty($param_default_facette) && intval($param_default_facette) == 1) {
            $check_facette = static::get_checked();
            if ($session_values != $check_facette) {
                $session_values = [];
                static::set_session_values($session_values);
            }
        } else {
            $tmpArray = [];
            $check_facette = static::get_checked();
            foreach ($check_facette as $facet_values) {
                $ajout = true;
                if (count($tmpArray)) {
                    foreach ($tmpArray as $k2 => $prev_values) {
                        if (($prev_values[2] == $facet_values[2]) && ($prev_values[3] == $facet_values[3])) {
                            $tmpArray[$k2][1][] = $facet_values[1];
                            $ajout = false;
                            break;
                        }
                    }
                }
                if ($ajout) {
                    $tmpItem = [];
                    $tmpItem[0] = $facet_values[0];
                    $tmpItem[1] = [$facet_values[1]];
                    $tmpItem[2] = $facet_values[2];
                    $tmpItem[3] = $facet_values[3];
                    $tmpArray[] = $tmpItem;
                }
            }
            // ajout facette : on vérifie qu'elle n'est pas déjà en session (rafraichissement page)
            $trouve = false;
            if (count($session_values)) {
                foreach ($session_values as $v) {
                    if ($tmpArray == $v) {
                        $trouve = true;
                        break;
                    }
                }
            }
            if (!$trouve && count($tmpArray)) {
                $session_values[] = $tmpArray;
            }
            static::set_session_values($session_values);
        }
        static::make_facette_search_env();
    }
    
    public static function get_nb_result_groupby($facettes)
    {
        $nb_result = 0;
        foreach ($facettes as $facette) {
            $nb_result += $facette['nb_result'];
        }
        return $nb_result;
    }

    public function get_clicked()
    {
        if (!isset($this->clicked)) {
            $session_values = static::get_session_values();
            if (is_array($session_values)) {
                $this->clicked = $session_values;
            } else {
                $this->clicked = [];
            }
        }
        return $this->clicked;
    }

    public function get_not_clicked()
    {
        $this->not_clicked = [];
        $this->facette_plus = [];
        foreach ($this->tab_facettes as $keyFacette=>$vTabFacette) {
            $affiche = true;
            foreach ($vTabFacette['value'] as $keyValue=>$vLibelle) {
                $clicked = false;
                foreach ($this->get_clicked() as $vSessionFacette) {
                    foreach ($vSessionFacette as $vDetail) {
                        if (($vDetail[2] == $vTabFacette['code_champ']) && ($vDetail[3] == $vTabFacette['code_ss_champ']) && (in_array($vLibelle, $vDetail[1]))) {
                            $clicked = true;
                            break;
                        }
                    }
                }
                if (! $clicked) {
                    $key = $vTabFacette['name'] . "_" . $this->facettes[$keyFacette]['id'];
                    if ($vTabFacette['size_to_display'] == '-1') {
                        $this->not_clicked[$key][] = array(
                            'see_more' => true
                        );
                        $affiche = false;
                    } elseif ($vTabFacette['size_to_display'] != '0') {
                        if (isset($this->not_clicked[$key]) && count($this->not_clicked[$key]) >= $vTabFacette['size_to_display']) {
                            $this->not_clicked[$key][] = array(
                                'see_more' => true
                            );
                            $affiche = false;
                        }
                    }
                    if ($affiche) {
                        $this->not_clicked[$key][] = [
                            'libelle' => $vLibelle,
                            'code_champ' => $vTabFacette['code_champ'],
                            'code_ss_champ' => $vTabFacette['code_ss_champ'],
                            'nb_result' => $vTabFacette['nb_result'][$keyValue]
                        ];
                    } else {
                        $this->facette_plus[$this->facettes[$keyFacette]['id']]['facette'][] = $vLibelle." "."(".$vTabFacette['nb_result'][$keyValue].")";
                        $this->facette_plus[$this->facettes[$keyFacette]['id']]['value'][] = $vLibelle;
                        $this->facette_plus[$this->facettes[$keyFacette]['id']]['nb_result'][] = $vTabFacette['nb_result'][$keyValue];
                        $this->facette_plus[$this->facettes[$keyFacette]['id']]['code_champ'] = $vTabFacette['code_champ'];
                        $this->facette_plus[$this->facettes[$keyFacette]['id']]['code_ss_champ'] = $vTabFacette['code_ss_champ'];
                        $this->facette_plus[$this->facettes[$keyFacette]['id']]['name'] = $vTabFacette['name'];
                        if (static::get_compare_notice_active()) {
                            $id = facette_search_compare::gen_compare_id($vTabFacette['name'], $vLibelle, $vTabFacette['code_champ'], $vTabFacette['code_ss_champ'], $vTabFacette['nb_result'][$keyValue]);
                            $facette_compare = $this->get_facette_search_compare();
                            if (isset($facette_compare->facette_compare[$id]) && $facette_compare->facette_compare[$id]) {
                                $facette_compare->set_available_compare($id, true);
                            }
                        }
                    }
                }
            }
        }
        return $this->not_clicked;
    }

    public function get_facette_plus()
    {
        return $this->facette_plus;
    }

    protected function get_display_clicked_detail($v=array())
    {
        global $charset;
        
        $display = "";
        $tmp = 0;
        foreach ($v as $vDetail) {
            foreach ($vDetail[1] as $vDetailLib) {
                if ($tmp) {
                    $display .= "<br>";
                }
                $display .= htmlentities($vDetail[0]." : ".static::get_formatted_value($vDetail[2], $vDetail[3], $vDetailLib), ENT_QUOTES, $charset);
                $tmp++;
            }
        }
        return $display;
    }
    
    protected function is_define_default($v) {
        $session_default_values = static::get_session_default_values();
        if (!empty($session_default_values)) {
            foreach ($session_default_values as $default_values) {
                if ($default_values == $v) {
                    return true;
                }
            }
        }
        return false;
    }
    
    protected function get_display_clicked_reinitialize($k, $v, $detail='')
    {
        global $msg, $charset;
        
        if (!empty($detail)) {
            $detail = $msg['search_facette']." ".$detail." ";
        }
        if ($this->is_define_default($v)) {
            $information = $detail.$msg["facette_define_default"];
            return "<a class='define-default-facettes-link' href='#' title='".htmlentities($information, ENT_QUOTES, $charset)."'>
                <img src='".get_url_icon('lock.png')."' alt='".htmlentities($information, ENT_QUOTES, $charset)."'/>
    		</a>";
        } else {
            $information = $detail.$msg["facette_delete_one"];
            return "<a class='reinitialize-facettes-link' href='#' onclick='".static::get_link_delete_clicked($k, count($this->clicked))."' title='".htmlentities($information, ENT_QUOTES, $charset)."'>
                <img src='".get_url_icon('cross.png')."' alt='".htmlentities($information, ENT_QUOTES, $charset)."'/>
    		</a>";
        }
    }
    
    protected function get_display_clicked_table()
    {
        $display = "<table id='active_facette' role='presentation'>";
        $n = 0;
        foreach ($this->clicked as $k=>$v) {
            ($n % 2) ? $pair_impair="odd" : $pair_impair="even";
            $n++;
            $clicked_detail = $this->get_display_clicked_detail($v);
            $display .= "
						<tr class='".$pair_impair."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\">
							<td>".$clicked_detail."</td>
							<td>".$this->get_display_clicked_reinitialize($k, $v, strip_tags($clicked_detail))."</td>
						</tr>";
        }
        $display .= "</table>";
        return $display;
    }
    
    protected function get_display_clicked_fieldset()
    {
        $display = "
        <fieldset id='active_facette' class='facette_fieldset'>";
//         $display .= "<legend></legend>";
        foreach ($this->clicked as $k=>$v) {
            $clicked_detail = $this->get_display_clicked_detail($v);
            $display .= "
			<div>
                <span>".$clicked_detail."</span>
                <span>".$this->get_display_clicked_reinitialize($k, $v, strip_tags($clicked_detail))."</span>
            </div>";
        }
        $display .= "
        </fieldset>";
        return $display;
    }
    
    protected static function get_link_define_default_facettes() 
    {
        $link = "facettes_define_default();";
        return $link;
    }
    
    protected static function get_link_delete_default_facettes()
    {
        $link = "facettes_delete_default();";
        return $link;
    }
    
    protected function get_display_clicked_action($name, $label, $title)
    {
        global $charset;
        
        switch ($name) {
            case 'reinitialize':
                $event = static::get_link_reinit_facettes();
                $icon = "<i class='fa fa-undo' aria-hidden='true'></i>";
                break;
            case 'define-default':
                $event = static::get_link_define_default_facettes();
                $icon = "";
                break;
            case 'delete-default':
                $event = static::get_link_delete_default_facettes();
                $icon = "";
                break;
        }
        if(static::$refining_method == 'filter') {
            return "<button onclick='".$event." return false;' title='".htmlentities($title, ENT_QUOTES, $charset)."' class='".$name."-facettes-link' style='cursor:pointer'>
                    ".htmlentities($label, ENT_QUOTES, $charset)."
                </button>";
        } else {
            return "<a href='#' onclick='".$event."' title='".htmlentities($title, ENT_QUOTES, $charset)."' class='".$name."-facettes-link'>
                    ".htmlentities($label, ENT_QUOTES, $charset)."
                    ".$icon."
                </a>";
        }
    }
    
    protected function get_display_clicked()
    {
        global $msg;
        global $opac_rgaa_active;

        $display_clicked = "";
        if($opac_rgaa_active) {
            $display_clicked .= $this->get_display_clicked_fieldset();
        } else {
            $display_clicked .= $this->get_display_clicked_table();
        }
        $display_clicked .= "
        <div class='row center'>
    		<span class='reinitialize-facettes'>
                ".$this->get_display_clicked_action('reinitialize', $msg['reset'], $msg['facette_reset_all'])."
    		</span>";
        
        if (static::$table_name == 'facettes_external') {
            $display_clicked .= "</div>";
        } else {
            $display_clicked .= "
            <span class='define-default-facettes'>
                ".$this->get_display_clicked_action('define-default', $msg['facettes_define_default'], $msg['facettes_define_default'])."
            </span>
        </div>";
            $display_clicked .= "
        <div class='row center'>
    		<span class='delete-default-facettes'>
                ".$this->get_display_clicked_action('delete-default', $msg['facettes_delete_default'], $msg['facettes_delete_default'])."
    		</span>
        </div>";
        }
        
        return $display_clicked;
    }
    
    protected function get_uniqid_label($detailFacette)
    {
        $key = $detailFacette['code_champ']."_".$detailFacette['code_ss_champ']."_".$detailFacette['libelle'];
        if (empty($this->uniqid_labels[$key])) {
            // $this->uniqid_labels[$key] = "facette-". $detailFacette['code_champ'] . $j . rand(0,999);
            $this->uniqid_labels[$key] = uniqid("facette-". $detailFacette['code_champ']."-", false);
        }
        return $this->uniqid_labels[$key];
    }
    
    protected function get_display_not_clicked_facette_checkbox($id, $name, $facette_libelle, $detailFacette)
    {
        global $charset;
        
        $idFacetteLabel = $this->get_uniqid_label($detailFacette);
        $cacValue = encoding_normalize::json_encode([$name,$detailFacette['libelle'],$detailFacette['code_champ'],$detailFacette['code_ss_champ'],$id,$detailFacette['nb_result']]);
        return "
        <span class='facette_coche'>
            <input id='$idFacetteLabel' type='checkbox' name='check_facette[]' value='".htmlentities($cacValue, ENT_QUOTES, $charset). "' />
        </span>";
    }
    
    protected function get_display_not_clicked_facette_label($id, $name, $facette_libelle, $detailFacette)
    {
        global $charset;
        
        $idFacetteLabel = $this->get_uniqid_label($detailFacette);
        $link = static::get_link_not_clicked($name, $detailFacette['libelle'], $detailFacette['code_champ'], $detailFacette['code_ss_champ'], $id, $detailFacette['nb_result']);
        return "
        <span class='facette_col_info'>
            <a href='#' ".$this->on_facet_click($link)." style='cursor:pointer' rel='nofollow' class='facet-link' >
                <span class='facette_libelle'>
                    <label for='$idFacetteLabel'>
                    	".htmlentities($facette_libelle, ENT_QUOTES, $charset)."
                    </label>
                </span>
                <span class='facette_number'>
                    [".htmlentities($detailFacette['nb_result'], ENT_QUOTES, $charset)."]
                </span>
            </a>
        </span>";
    }
    
    protected function get_display_not_clicked_facette($idfacette, $name, $facette)
    {
        global $msg, $charset;
        global $opac_rgaa_active;
        
        $display_not_clicked = "";
        $flagSeeMore = false;
        $j=0;
        foreach ($facette as $detailFacette) {
            if (!isset($detailFacette['see_more'])) {
                $id = facette_search_compare::gen_compare_id($name, $detailFacette['libelle'], $detailFacette['code_champ'], $detailFacette['code_ss_champ'], $detailFacette['nb_result']);
                
//                 $cacValue = encoding_normalize::json_encode([$name,$detailFacette['libelle'],$detailFacette['code_champ'],$detailFacette['code_ss_champ'],$id,$detailFacette['nb_result']]);
                if (static::get_compare_notice_active()) {
                    $facette_compare=$this->get_facette_search_compare();
                    if (!isset($facette_compare->facette_compare[$id]) || empty($facette_compare->facette_compare[$id])) {
//                        $onclick = 'select_compare_facette(\''.htmlentities($cacValue, ENT_QUOTES, $charset) . '\')';
//                        $img = 'double_section_arrow_16.png';
                    } else {
                        $facette_compare->set_available_compare($id, true);
//                         $onclick='';
//                         $img='vide.png';
                    }
                }
                $facette_libelle = static::get_formatted_value($detailFacette['code_champ'], $detailFacette['code_ss_champ'], $detailFacette['libelle']);
                if ($facette_libelle) {
                    if($opac_rgaa_active) {
                        $display_not_clicked .= "
						<li style='display: block;' class='facette_tr'>
							".$this->get_display_not_clicked_facette_checkbox($id, $name, $facette_libelle, $detailFacette)."
                            ".$this->get_display_not_clicked_facette_label($id, $name, $facette_libelle, $detailFacette)."
                        </li>";
                    } else {
                        $display_not_clicked .= "
						<tr style='display: block;' class='facette_tr'>
							<td class='facette_col_coche'>
								".$this->get_display_not_clicked_facette_checkbox($id, $name, $facette_libelle, $detailFacette)."
                            </td>
                            <td  class='facette_col_info'>
                                ".$this->get_display_not_clicked_facette_label($id, $name, $facette_libelle, $detailFacette)."
                            </td>
                        </tr>";
                    }
                    $j++;
                }
            } elseif (!$flagSeeMore) {
                if($opac_rgaa_active) {
                    $display_not_clicked .= "
					<li style='display: block;' class='facette_tr_see_more'>
						<button type='button' class='button-unstylized' onclick='javascript:facette_see_more(".$idfacette.",".encoding_normalize::json_encode($this->facette_plus[$idfacette]).");'><span class='facette_plus_link' aria-label='".htmlentities($msg['facette_plus_label'], ENT_QUOTES, $charset)."'>".htmlentities($msg['facette_plus_link'], ENT_QUOTES, $charset)."</span></button>
					</li>";
                } else {
                    $display_not_clicked .= "
					<tr style='display: block;' class='facette_tr_see_more'>
						<td colspan='3'>
                            <button type='button' class='button-unstylized' onclick='javascript:facette_see_more(".$idfacette.",".json_encode(encoding_normalize::utf8_normalize($this->facette_plus[$idfacette]), JSON_HEX_APOS | JSON_HEX_QUOT) . ");'>".$msg["facette_plus_link"]."</button>
						</td>
					</tr>";
                }
                $flagSeeMore = true;
            }
        }
        return $display_not_clicked;
    }
    
    protected function get_display_not_clicked_groupby_detail($name, $currentFacette)
    {
        $facette_compare=$this->get_facette_search_compare();
        $idGroupBy = facette_search_compare::gen_groupby_id($name, $currentFacette['code_champ'], $currentFacette['code_ss_champ']);
        $groupBy = facette_search_compare::gen_groupby($name, $currentFacette['code_champ'], $currentFacette['code_ss_champ'], $idGroupBy);
        $display = facette_search_compare::get_groupby_row($facette_compare, $groupBy, $idGroupBy);
        if (isset($facette_compare->facette_groupby[$idGroupBy]) && $facette_compare->facette_groupby[$idGroupBy]) {
            $facette_compare->set_available_groupby($idGroupBy, true);
        }
        return $display;
    }
    
    protected function get_display_not_clicked_fieldset($idfacette, $name, $currentFacette, $facette)
    {
        global $charset;
        
        $display = "<fieldset id='facette_list_".$idfacette."' class='facette_fieldset facette_expande'>";
        
        if (static::get_compare_notice_active()) {
            $facette_compare=$this->get_facette_search_compare();
        }
        if (static::get_compare_notice_active() && count($facette_compare->facette_compare)) {
            $display .= "
			<legend style='width:90%' onclick='javascript:test(\"facette_list_".$idfacette."\");' aria-expanded='true' aria-controls='facette_body_".$idfacette."' role='button'>
				".htmlentities($name, ENT_QUOTES, $charset)."
			</legend>";
            $display.= $this->get_display_not_clicked_groupby_detail($name, $currentFacette);
        } else {
            $display .= "
			<legend>
                <span id='legend_facette_". $idfacette ."' class='visually-hidden'>". htmlentities($name, ENT_QUOTES, $charset). "</span>
				<button type='button' class='facette_name button-unstylized' onclick='javascript:test(\"facette_list_".$idfacette."\");' aria-expanded='true' aria-controls='facette_body_".$idfacette."'>".htmlentities($name, ENT_QUOTES, $charset)."</button>
			</legend>";
        }
        $display .= "<ul id='facette_body_".$idfacette."' aria-labelledby='legend_facette_". $idfacette ."'>";
        $display .= $this->get_display_not_clicked_facette($idfacette, $name, $facette);
        $display .= "</ul>";
        $display .= "</fieldset>";
        return $display;
    }
    
    protected function get_display_not_clicked_table($idfacette, $name, $currentFacette, $facette)
    {
        global $charset;
        
        $display = "<table id='facette_list_".$idfacette."' class='facette_expande' role='presentation'>";
//         $display .= "<thead>";
        $display .= "<tr>";
        if (static::get_compare_notice_active()) {
            $facette_compare=$this->get_facette_search_compare();
        }
        if (static::get_compare_notice_active() && ! empty($facette_compare->facette_compare) && count($facette_compare->facette_compare)) {
            $display .= "
							<th style='width:90%' onclick='javascript:test(\"facette_list_" .$idfacette."\");' colspan='2' aria-expanded='true' aria-controls='facette_body_".$idfacette."' role='button'>
								".htmlentities($name, ENT_QUOTES, $charset)."
							</th>";
            $display.= $this->get_display_not_clicked_groupby_detail($name, $currentFacette);
        } else {
            $display .= "
						<th onclick='javascript:test(\"facette_list_".$idfacette."\");' aria-expanded='true' aria-controls='facette_body_".$idfacette."' role='button'>
							".htmlentities($name, ENT_QUOTES, $charset)."
						</th>";
        }
        $display .= "</tr>";
//         $display .= "</thead>";
        $display .= "<tbody id='facette_body_".$idfacette."' role='group' aria-labelledby='legend_facette_". $idfacette ."'>";
        $display .= $this->get_display_not_clicked_facette($idfacette, $name, $facette);
        $display .= "</tbody>";
        $display .="</table>";
        return $display;
    }
    
    protected function get_display_not_clicked()
    {
        global $charset;
        global $msg;
        global $opac_rgaa_active;

        $display = '';
        if (is_array($this->not_clicked) && count($this->not_clicked)) {
            foreach ($this->not_clicked as $tmpName=>$facette) {
                $tmpArray = explode("_", $tmpName);
                $idfacette = array_pop($tmpArray);
                $name = get_msg_to_display(implode("_", $tmpArray));
                $currentFacette = current($facette);
                if ($opac_rgaa_active) {
                    $display .= $this->get_display_not_clicked_fieldset($idfacette, $name, $currentFacette, $facette);
                } else {
                    $display .= $this->get_display_not_clicked_table($idfacette, $name, $currentFacette, $facette);
                }
            }
            $display .= "<input type='hidden' value='' id='filtre_compare_facette' name='filtre_compare'>";
            $display .= "<input aria-label='".htmlentities($msg['facette_filtre_apply_selection'], ENT_QUOTES, $charset)."' class='bouton bouton_filtrer_facette_bas filter_button' type='button' value='".htmlentities($msg["facette_filtre"], ENT_QUOTES, $charset)."' name='filtre' ".$this->get_filter_button_action().">";
            if (static::get_compare_notice_active()) {
                $display .= "<input class='bouton' type='button' value='".htmlentities($msg["facette_compare"], ENT_QUOTES, $charset)."' name='compare' onClick='valid_facettes_compare()'>";
            }
        }
        return $display;
    }

    protected function get_filter_button_action()
    {
        return "onClick='valid_facettes_multi()'";
    }

    protected function on_facet_click($link = '')
    {
        return "onclick='".$link." return false;'";
    }

    public function create_ajax_table_facettes()
    {
        global $charset;
        global $mode;
        global $msg;

        if (static::get_compare_notice_active()) {
            $facette_compare = $this->get_facette_search_compare();
        }

        $table = "<form name='facettes_multi' class='facettes_multis' method='POST' action='" . static::format_url("lvl=more_results&mode=" . $this->mode . "&facette_test=1") . "'>";
        if (count($this->get_clicked())) {
            $table .= "<h3>".htmlentities($msg['facette_active'], ENT_QUOTES, $charset)."</h3>";
            $table .= $this->get_display_clicked()."<br/>";
        }

        if (static::get_compare_notice_active()) {
            // Le tableau des critères de comparaisons
            $table .= $this->get_display_compare();

            // le bouton de retour
            if (isset($_SESSION['filtre_compare']) && $_SESSION['filtre_compare'] == 'compare') {
                $table .= "<input type='button' class='bouton backToResults' value='".htmlentities($msg['facette_compare_search_result'], ENT_QUOTES, $charset)."' onclick='".static::get_link_back()."'/><br /><br />";
            } elseif ((!isset($_SESSION['filtre_compare']) || $_SESSION['filtre_compare'] != 'compare') && !empty($facette_compare->facette_compare) && count($facette_compare->facette_compare)) {
                $table .= "<input type='button' class='bouton' value='".htmlentities($msg['facette_compare_search_compare'], ENT_QUOTES, $charset)."' onclick='valid_compare();'/><br /><br />";
            }
        }
        if (! empty($_SESSION['facettes_sets'][static::$facet_type])) {
            $selected = $_SESSION['facettes_sets'][static::$facet_type];
        } else {
            $selected = 0;
        }
        $facettes_sets = new facettes_sets(static::$facet_type);
        $facettes_sets_selector = $facettes_sets->get_display_selector($selected);
        if (count($this->get_not_clicked())) {
            if (static::get_compare_notice_active()) {
                $table .= "<div id='facettes_help'></div>";
                $table .= "<h3 class='facette_compare_listTitle'>".htmlentities($msg['facette_list_compare'], ENT_QUOTES, $charset) . " &nbsp;<img onclick='open_popup(document.getElementById(\"facettes_help\"),\"".htmlentities($msg['facette_compare_helper_message'], ENT_QUOTES, $charset)."\");' height='18px' width='18px' title='".htmlentities($msg['facette_compare_helper'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['facette_compare_helper'], ENT_QUOTES, $charset)."' src='".get_url_icon('quest.png')."'/></h3>";
            } else {
                $table .= "<h3 class='facette_compare_listTitle'>" . htmlentities($msg['facette_list'], ENT_QUOTES, $charset) . "</h3>";
            }
            $table .= $facettes_sets_selector;
            $table .= $this->get_display_not_clicked() . "<br/>";
        } else {
            $table .= $facettes_sets_selector;
        }
        $table .= "</form>";
        return $table;
    }

    protected function get_display_compare_reinitialize()
    {
        global $msg, $charset;
        
        return "<a onclick='".static::get_link_back(true)."' class='facette_compare_raz'>
            <img width='18px' height='18px'
            alt='".htmlentities($msg['facette_compare_reinit'], ENT_QUOTES, $charset)."'
            title='".htmlentities($msg['facette_compare_reinit'], ENT_QUOTES, $charset)."'
            src='".get_url_icon('cross.png')."'/>
        </a>";
    }
    
    protected function get_display_compare_table()
    {
        global $msg, $charset;
        
        if (static::get_compare_notice_active()) {
            $facette_compare = $this->get_facette_search_compare();
        }
        
        $sectionTitle = "<h3 class='facette_compare_MainTitle'>%s</h3>";
        $display = sprintf(
            $sectionTitle,
            "<table role='presentation'>
				        <tr>
				            <td style='width:90%;'>".htmlentities($msg['facette_list_compare_crit'], ENT_QUOTES, $charset)."</td>
				            <td>
				                ".$this->get_display_compare_reinitialize()."
			                </td>
				        </tr>
				    </table>"
            );
        $display .= "<table id='facette_compare' role='presentation'>".$facette_compare->gen_table_compare()."</table><br/>";
        
        $sectionTitle = "<h3 class='facette_compare_SubTitle'>%s</h3>";
        $display .= sprintf(
            $sectionTitle,
            "<img id='facette_compare_not_clickable' src='".get_url_icon('group_by.png')."'/> "
            . htmlentities($msg['facette_list_groupby_crit'], ENT_QUOTES, $charset)
            );
        //Le tableau des critères de comparaisons
        $display .= "<table id='facette_groupby' role='presentation'>";
        if (count($facette_compare->facette_groupby)) {
            $display .= $facette_compare->gen_table_groupby();
        }
        $display .= "</table><br/>";
        return $display;
    }
    
    protected function get_display_compare_fieldset()
    {
        global $msg, $charset;
        
        if (static::get_compare_notice_active()) {
            $facette_compare = $this->get_facette_search_compare();
        }
        $display = "<fieldset>
            <legend class='facette_compare_MainTitle'>
                ".htmlentities($msg['facette_list_compare_crit'], ENT_QUOTES, $charset)."
                ".$this->get_display_compare_reinitialize()."
            </legend>
            <ul id='facette_compare'>".$facette_compare->gen_fieldset_compare()."</ul>
        </fieldset>
        ";
        
        $display .= "<fieldset>
            <legend class='facette_compare_SubTitle'>
                <img id='facette_compare_not_clickable' src='".get_url_icon('group_by.png')."'/> "
                .htmlentities($msg['facette_list_groupby_crit'], ENT_QUOTES, $charset)."
            </legend>";
        //La liste des critères de comparaisons
        if (count($facette_compare->facette_groupby)) {
            $display .= "
                <ul id='facette_groupby'>
                    ".$facette_compare->gen_fieldset_groupby()."
                </ul>";
        }
        $display .= "
        </fieldset>";
        return $display;
    }
    
    /**
     * Tableau de comparaison des critères
     * @return string
     */
    protected function get_display_compare()
    {
        global $opac_rgaa_active;
        
        $display = "";
        if (static::get_compare_notice_active()) {
            $facette_compare = $this->get_facette_search_compare();
        }
        if (! empty($facette_compare->facette_compare) && count($facette_compare->facette_compare)) {
            if ($opac_rgaa_active) {
                $display .= $this->get_display_compare_fieldset();
            } else {
                $display .= $this->get_display_compare_table();
            }
        }
        return $display;
    }
    
    public static function set_session_facettes_set($num_facettes_set)
    {
        $_SESSION['facettes_sets'][static::$facet_type] = $num_facettes_set;
    }

    protected function get_action_form()
    {
        return static::format_url("lvl=more_results&mode=".$this->mode."&facette_test=1");
    }

    public static function session_filtre_compare()
    {
        global $filtre_compare;

        $_SESSION['filtre_compare'] = $filtre_compare;
    }

    public static function get_checked()
    {
        global $charset;
        global $name;
        global $value;
        global $champ;
        global $ss_champ;
        global $check_facette;

        // si rien en multi-sélection, il n'y a qu'une seule facette de cliquée
        // on l'ajoute au tableau pour avoir un traitement unique après
        if (!isset($check_facette) || !count($check_facette)) {
            $check_facette = [];
            if (!empty($name) && isset($value)) {
                $check_facette[] = [
                    stripslashes($name),
                    stripslashes($value),
                    $champ,
                    $ss_champ
                ];
            }
        } else {
            // le tableau est addslashé automatiquement
            foreach ($check_facette as $k=>$v) {
                $check_facette[$k] = json_decode(stripslashes($v));
                // json_encode/decode ne fonctionne qu'avec des données utf-8
                if ($charset != 'utf-8') {
                    foreach ($check_facette[$k] as $key=>$value) {
                        $check_facette[$k][$key] = encoding_normalize::utf8_decode($check_facette[$k][$key]);
                    }
                }
            }
        }
        return $check_facette;
    }

    public function get_facette_search_compare()
    {
        if (!isset($this->facette_search_compare)) {
            $this->facette_search_compare = new facette_search_compare();
        }
        return $this->facette_search_compare;
    }

    public function get_json_datas()
    {
        $datas = [
                'clicked' => $this->get_clicked(),
                'not_clicked' => $this->get_not_clicked(),
                'facette_plus' => $this->get_facette_plus()
        ];
        return encoding_normalize::json_encode($datas);
    }

    public static function get_compare_notice_active()
    {
        if (! isset(static::$compare_notice_active)) {
            if (static::$table_name == 'facettes') {
                // Désactivé pour les facettes non externes
                static::$compare_notice_active = 0;
            } else {
                global $pmb_compare_notice_active;
                static::$compare_notice_active = intval($pmb_compare_notice_active);
            }
        }
        return static::$compare_notice_active;
    }

    public static function set_search_mode($search_mode)
    {
        static::$search_mode = $search_mode;
    }

    public static function set_hidden_form_name($hidden_form_name)
    {
        static::$hidden_form_name = $hidden_form_name;
    }

    public static function set_url_base($url_base)
    {
        static::$url_base = $url_base;
    }

    protected static function format_url($url)
    {
        global $base_path;

        if (!isset(static::$url_base)) {
            static::$url_base = $base_path.'/index.php?';
        }
        if (strpos(static::$url_base, "lvl=search_segment")) {
            return static::$url_base.str_replace('lvl', '&action', $url);
        } else {
            return static::$url_base.$url;
        }
    }

    public static function get_facet_type()
    {
        if (empty(static::$facet_type)) {
            static::$facet_type = 'notices';
        }
        return static::$facet_type;
    }
    
    public static function set_facet_type($type)
    {
        static::$facet_type = $type;
    }

    public static function set_elements_list_nb_per_page($elements_list_nb_per_page)
    {
        static::$elements_list_nb_per_page = intval($elements_list_nb_per_page);
    }
    
    /**
     * Liste des facettes (tableau ordonné)
     *
     * @return array
     */
    public static function get_list($type = 'notices')
    {
        $q = "select * from " . static::$table_name . " where facette_type='" . addslashes($type) . "' order by facette_order";

        $r = pmb_mysql_query($q);
        if (0 === pmb_mysql_num_rows($r)) {
            return [];
        }

        $list = [];
        while ($row = pmb_mysql_fetch_assoc($r)) {
            $list[$row['id_facette']] = $row;
        }
        return $list;
    }
}// end class
