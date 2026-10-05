<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_facets.class.php,v 1.35.2.5.2.1 2025/04/10 08:02:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path, $include_path;
require_once($class_path."/facette_search.class.php");
require_once($class_path."/search_universes/external/search_segment_external_facets.class.php");
require_once($class_path."/search_universes/search_segment.class.php");
require_once($include_path.'/templates/search_universes/search_segment_facets.tpl.php');

class search_segment_facets extends facettes
{
    protected $num_segment;

    protected $segment_search;

    /**
     *
     * @var search_segment
     */
    protected $segment = null;

    public function __construct($objects_ids = '')
    {
        $this->num_segment = (!empty(func_get_args()[1]) ? intval(func_get_args()[1]) : 0);
        parent::__construct($objects_ids);
    }

    protected function get_query()
    {
        return "SELECT * FROM facettes
					JOIN search_segments_facets ON search_segments_facets.num_facet = facettes.id_facette
					WHERE num_search_segment = ".$this->num_segment."
					ORDER BY search_segment_facet_order";
    }

    public function set_num_segment($num_segment)
    {
        $this->num_segment = intval($num_segment);
    }

    public function get_num_segment()
    {
        if (isset($this->num_segment)) {
            return $this->num_segment;
        }
        return 0;
    }

    public function set_segment_search($segment_search)
    {
        $this->segment_search = $segment_search;
    }

    public function get_segment_search()
    {
        if (isset($this->segment_search)) {
            return $this->segment_search;
        }
        return '';
    }

    protected function get_action_form()
    {
        return static::format_url("lvl=search_segment&action=segment_results&mode=".$this->mode."&facette_test=1&id=".$this->num_segment);
    }

    public function create_ajax_table_facettes()
    {
        global $base_path;
        global $charset;
        global $mode;
        global $msg;
        global $universe_query;
        global $opac_rgaa_active;

        $this->create_search_environment();
        $table = "<form name='facettes_multi' class='facettes_multis' method='POST' action='".$this->get_action_form()."'>";
        if (static::get_compare_notice_active()) {
            $facette_compare=$this->get_facette_search_compare();
            //Le tableau des critères de comparaisons
            if (count($facette_compare->facette_compare)) {
                $table_compare=$facette_compare->gen_table_compare();


                $sectionTitle = "<h3 class='facette_compare_MainTitle'>%s</h3>";
                if ($opac_rgaa_active) {
                    $sectionTitle = "<h2 class='facette_compare_MainTitle'>%s</h2>";
                }

                $table .= sprintf(
                    $sectionTitle,
                    "<table role='presentation'>
						<tr>
							<td style='width:90%;'>".htmlentities($msg['facette_list_compare_crit'], ENT_QUOTES, $charset)."</td>
							<td>
								<a onclick='".static::get_link_back(true)."' class='facette_compare_raz'>
									<img width='18px' height='18px'
										alt='".htmlentities($msg['facette_compare_reinit'], ENT_QUOTES, $charset)."'
										title='".htmlentities($msg['facette_compare_reinit'], ENT_QUOTES, $charset)."'
										src='".get_url_icon('cross.png')."'/>
								</a>
							</td>
						</tr>
					</table>"
                );
                $table .= "<table id='facette_compare' role='presentation'>".$table_compare."</table><br/>";

                //Le tableau des critères de comparaisons
                if (count($facette_compare->facette_groupby)) {
                    $table_groupby=$facette_compare->gen_table_groupby();
                }

				$sectionTitle = "<h3 class='facette_compare_SubTitle'>%s</h3>";
                if ($opac_rgaa_active) {
                    $sectionTitle = "<h2 class='facette_compare_SubTitle'>%s</h2>";
                }

				$table .= sprintf(
                    $sectionTitle,
                    "<img id='facette_compare_not_clickable' src='".get_url_icon('group_by.png')."'/> "
                     . htmlentities($msg['facette_list_groupby_crit'], ENT_QUOTES, $charset)
                );
                $table .= "<table id='facette_groupby' role='presentation'>".$table_groupby."</table><br/>";
            }

            //le bouton de retour
            if (isset($_SESSION['filtre_compare']) && $_SESSION['filtre_compare']=='compare') {
                $table .= "<input type='button' class='bouton backToResults' value='".htmlentities($msg['facette_compare_search_result'], ENT_QUOTES, $charset)."' onclick='".static::get_link_back()."'/><br /><br />";
            } elseif ((!isset($_SESSION['filtre_compare']) || $_SESSION['filtre_compare']!='compare') && count($facette_compare->facette_compare)) {
                $table .= "<input type='button' class='bouton' value='".htmlentities($msg['facette_compare_search_compare'], ENT_QUOTES, $charset)."' onclick='valid_compare();'/><br /><br />";
            }
        }

        if (count($this->get_not_clicked())) {
            $sectionTitle = $opac_rgaa_active ? "<h2 class='facette_compare_listTitle'>%s</h2>" : "<h3 class='facette_compare_listTitle'>%s</h3>";
            
            if (static::get_compare_notice_active()) {
                if($opac_rgaa_active) {
                    $img = "<button class='button-unstylized' aria-controls='facettes_help' type='button' onclick='open_popup(document.getElementById(\"facettes_help\"),\"".htmlentities($msg['facette_compare_helper_message'], ENT_QUOTES, $charset)."\");'>
                                <img height='18px' width='18px'
                                    title='".htmlentities($msg['facette_compare_helper'], ENT_QUOTES, $charset)."'
                                    alt='".htmlentities($msg['facette_compare_helper'], ENT_QUOTES, $charset)."'
                                    src='".get_url_icon('quest.png')."'/>
                            </button>";
                } else {
                    $img = "<img height='18px' width='18px'
					            onclick='open_popup(document.getElementById(\"facettes_help\"),\"".htmlentities($msg['facette_compare_helper_message'], ENT_QUOTES, $charset)."\");'
					            title='".htmlentities($msg['facette_compare_helper'], ENT_QUOTES, $charset)."'
					            alt='".htmlentities($msg['facette_compare_helper'], ENT_QUOTES, $charset)."'
					            src='".get_url_icon('quest.png')."'/>";
                }
                
                $sectionTitle = sprintf(
                    $sectionTitle,
                    htmlentities($msg['facette_list_compare'], ENT_QUOTES, $charset) . " &nbsp;{$img}"
                );
                $table .= "<div id='facettes_help' role='dialog' aria-labelledby='".htmlentities($msg['facette_compare_helper'], ENT_QUOTES, $charset)."' aria-modal='true'></div>";
                $table .= $sectionTitle.$this->get_display_not_clicked()."<br/>";
            } else {
                $sectionTitle = sprintf(
					$sectionTitle,
					htmlentities($msg['facette_list_compare'], ENT_QUOTES, $charset)
				);
                $table .= $sectionTitle.$this->get_display_not_clicked()."<br/>";
            }
        }
        $table .= "</form>";
        $table.= "<script>
					require(['apps/pmb/search_universe/SearchSegmentController'], function(SearchSegmentController){
						new SearchSegmentController({numSegment : '".static::get_num_segment()."' ".($universe_query ? ',universeQuery: "'.$universe_query.'"' : '')."});
					});
                </script>";
        return $table;
    }

    public static function make_facette_search_env()
    {
        global $search;
        global $check_facette;

        //creation des globales => parametres de recherche
        $n = count($search);
        if (is_array($check_facette)) {
            $fields = [];
            foreach ($check_facette as $facet) {
                if (!isset($fields[$facet[2]][$facet[3]])) {
                    $facet[1] = [$facet[1]];
                    $fields[$facet[2]][$facet[3]] = $facet;
                } else {
                    $fields[$facet[2]][$facet[3]][1][] = $facet[1];
                }
            }
            $i = 0;
            foreach ($fields as $field => $subfields) {
                foreach ($subfields as $subfield) {
                    $search[] = "s_3";
                    $fieldname = "field_".($i+$n)."_s_3";
                    global ${$fieldname};
                    ${$fieldname} = [$subfield];
                    $op = "op_".($i+$n)."_s_3";
                    $op_ = "EQ";
                    global ${$op};
                    ${$op}=$op_;

                    $inter = "inter_".($i+$n)."_s_3";
                    $inter_ = "and";
                    global ${$inter};
                    ${$inter} = $inter_;
                    $i++;
                }
            }
        }
    }

    protected static function get_link_not_clicked($name, $label, $code_champ, $code_ss_champ, $id, $nb_result)
    {
        return '';
    }

    protected static function get_ajax_see_more_url()
    {
        $url = parent::get_ajax_see_more_url();
        $url .= "&action=segment_results";
        return $url;
    }

    public static function get_session_values()
    {
        return null;
    }

    public static function set_session_values($session_values)
    {
        return;
    }

    protected function get_filter_button_action()
    {
        return "";
    }

    protected function on_facet_click($link = '')
    {
        return "";
    }

    public function call_facets($additional_content = "")
    {
        global $universe_query;
        global $base_path;

        $ajax_facettes = "<div id='facette_wrapper'>";
        $ajax_facettes .= $additional_content;
        $ajax_facettes .= $this->get_refine_form();
        $ajax_facettes .= static::get_facette_wrapper();
        $ajax_facettes .="
                <div id='facette_wrapper_child'>
    				<img id='facette_wrapper_patience'  src='".get_url_icon('patience.gif')."'/>
    				<script>
    				    require(['dojo/query', 'dojo/dom-construct', 'dojo/request/xhr', 'dojo/dom', 'dojo/parser', 'dojo/domReady!'], function(query, domConstruct, xhr, dom, parser){
    			            var url = '".$base_path."/ajax.php?module=ajax&categ=facettes&sub=get_data&num_segment=".$this->num_segment."';
    				        xhr(url,{
        						data : {segment_search : '".$this->get_segment_search()."', universe_query: '".$universe_query."'},
        						handleAs: 'json',
        						method:'POST',
        					}).then(function(response){
        						if (response) {
        						    dom.byId('facette_wrapper_child').innerHTML = response.display;
        						    query('script').forEach(function(node) {
                    					domConstruct.create('script', {
                    						innerHTML: node.innerHTML
                    					}, node, 'replace');
                    				});
        							if(response.map_location) {
            						    var mapLocationSearch = dom.byId('map_location_search');
        								if(mapLocationSearch) {
        									mapLocationSearch.innerHTML = response.map_location;
    										parser.parse(mapLocationSearch);
        								}
        							}
        						}
        					});
    				    });
    				</script>
    			</div>
			</div>
            ";
        return $ajax_facettes;
    }

    protected function create_search_environment()
    {
        $search_class = new search();
        $search_class->json_decode_search($this->get_segment_search());
    }

    public function get_clicked()
    {
        if (!isset($this->clicked)) {
            global $search;
            $this->clicked = [];
            //on reconstruit la session des facettes pour que l'affichage fonctionne comme avant
            if (is_array($search) && count($search)) {
                foreach ($search as $i => $value) {
                    if ($value == 's_3') {
                        $field = "field_".$i."_s_3";
                        global ${$field};
                        if (!empty(${$field})) {
                            $this->clicked[] = ${$field};
                        }
                    }
                }
            }
        }
        return $this->clicked;
    }

    protected function get_query_by_facette($id_critere, $id_ss_critere, $type = "notices")
    {
        global $lang;

        if ($type == 'notices') {
            $plural_prefix = 'notices';
            $prefix = 'notice';
        } else {
            $plural_prefix = 'authorities';
            $prefix = 'authority';
        }
        $query = 'select value ,count(distinct id_'.$prefix.') as nb_result from (SELECT value,id_'.$prefix.' FROM '.$plural_prefix.'_fields_global_index'.
            gen_where_in($plural_prefix.'_fields_global_index.id_'.$prefix, $this->objects_ids).'
				AND code_champ = '.(intval($id_critere)).'
				AND code_ss_champ = '.(intval($id_ss_critere)).'
				AND lang in ("","'.$lang.'","'.substr($lang, 0, 2).'")) as sub
				GROUP BY value
				ORDER BY ';

        return $query;
    }

    public function get_ajax_facette()
    {
        $facettes_exists_with_or_without_results = false;
        if ($this->exists_with_results || count($this->get_clicked())) {
            $facettes_exists_with_or_without_results = true;
        }
        return [
            'exists_with_results' => ($_SESSION["cms_build_activate"] ? true : $facettes_exists_with_or_without_results),
            'display' => $this->create_ajax_table_facettes(),
            'map_location' =>  $this->get_map_location(),
        ];
    }

    public static function get_instance($objects_ids = "", $num_segment = 0)
    {
        $num_segment = intval($num_segment);
        if ($num_segment) {
            $query = "SELECT search_segment_type FROM search_segments WHERE id_search_segment = $num_segment";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_array($result);
                if ($row[0] == TYPE_EXTERNAL) {
                    return new search_segment_external_facets($objects_ids, $num_segment);
                }
            }
        }
        return new search_segment_facets($objects_ids, $num_segment);
    }

    protected function get_refine_form()
    {
        global $msg, $include_path, $base_path, $charset, $opac_rgaa_active;

        $this->get_segment();
        if($this->segment == null) {
            return '';
        }
        
        $sectionTitle = $opac_rgaa_active ? "<h2>%s</h2>" : "<h3>%s</h3>";
        $sectionTitle = sprintf(
            $sectionTitle,
            sprintf($msg['search_segment_refine_search_in_segment'], $this->segment->get_translated_label())
        );

        $action = $base_path.'/index.php?lvl=search_segment&action=search_result&id='.$this->num_segment;
        $action .= search_universe::get_parameters();
        $tpl = "
            <form name='refine_search_input' id='refine_search_input' action='".htmlentities($action, ENT_QUOTES, $charset)."' method='post' onSubmit=\"if (refine_search_input.refine_user_query.value.length == 0) { refine_search_input.refine_user_query.value='*'; return true; }\">
                {$sectionTitle}
				<input type='text' id='refine_user_query' name='refine_user_query' class='text_query' value=\"\"/>
				<input type='hidden' id='refine_user_rmc' name='refine_user_rmc' value=\"\" />
                <br/>
				<input type='submit' name='ok' value='".$msg["142"]."' class='bouton bouton_filtrer_facette_haut'/>
                <!-- <span class='segment_search_advanced'>
                    <a href='#' title='".$msg["extended_search"]."' onClick=\"openPopUp('./select.php?what=search_segment&action=advanced_search&selector_data={\'type\':\'".$this->segment->get_type()."\'}', 'selector')\"><img src='".get_url_icon('search.gif')."' style='border:0px' /></a>
                </span>
                <script src='".$include_path."/javascript/ajax.js'></script>-->
            </form>";
        return $tpl;
    }

    public function set_segment($num_segment)
    {
        $num_segment = intval($num_segment);
        $this->segment = search_segment::get_instance($num_segment);
        return $this;
    }

    public function get_segment()
    {
        if (isset($this->segment)) {
            return $this->segment;
        }
        if (!empty($this->num_segment)) {
            $this->set_segment($this->num_segment);
            return $this->segment;
        }
        return null;
    }

    public static function get_modal_data()
    {
        $data = [];
        $data['form_name'] = "form_values";
        return $data;
    }
}
