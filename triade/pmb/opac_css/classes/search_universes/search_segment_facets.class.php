<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_facets.class.php,v 1.12 2019-06-10 15:17:16 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/facette_search.class.php");
require_once($include_path.'/templates/search_universes/search_segment_facets.tpl.php');

class search_segment_facets extends facettes {
	
	protected $num_segment;
	
	protected $segment_search;

	public function __construct($objects_ids = '') {
	    $num_segment = (!empty(func_get_args()[1]) ? intval(func_get_args()[1]) : 0);
	    if ($num_segment) {
	        $this->num_segment = $num_segment;
	    }
	    parent::__construct($objects_ids);
	}
	
	protected function get_query() {
		return "SELECT * FROM facettes
					JOIN search_segments_facets ON search_segments_facets.num_facet = facettes.id_facette
					WHERE num_search_segment = ".$this->num_segment."
					ORDER BY search_segment_facet_order";
	}
	
	public function set_num_segment($num_segment) {
		$this->num_segment = $num_segment + 0;
	}
	
	public function get_num_segment() {
	    if (isset($this->num_segment)) {
	        return $this->num_segment;
	    }
		return 0;
	}
	
	public function set_segment_search($segment_search) {
		$this->segment_search = $segment_search;
	}
	
	public function get_segment_search() {
	    if (isset($this->segment_search)) {
	        return $this->segment_search;
	    }
		return '';
	}
	
	protected function get_action_form() {
	    return static::format_url("lvl=search_segment&action=segment_results&mode=".$this->mode."&facette_test=1&id=".$this->num_segment);
	}
	
	public function create_ajax_table_facettes(){
	    global $base_path;
	    global $charset;
	    global $mode;
	    global $msg;
		global $universe_query;
	    
	    $this->create_search_environment();
	    $table = "<form name='facettes_multi' class='facettes_multis' method='POST' action='".$this->get_action_form()."'>";
	    if(static::get_compare_notice_active()){
	        $facette_compare=$this->get_facette_search_compare();
	        //Le tableau des critères de comparaisons
	        if(count($facette_compare->facette_compare)){
	            $table_compare=$facette_compare->gen_table_compare();
	
	            $table .= "<h3 class='facette_compare_MainTitle'><table><tr><td style='width:90%;'>".htmlentities($msg['facette_list_compare_crit'],ENT_QUOTES,$charset)."</td>
				<td><a onclick='".static::get_link_back(true)."' class='facette_compare_raz'><img alt='".htmlentities($msg['facette_compare_reinit'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['facette_compare_reinit'],ENT_QUOTES,$charset)."' width='18px' height='18px' src='".get_url_icon('cross.png')."'/></a></td></tr></table>
				</h3><table id='facette_compare'>".$table_compare."</table><br/>";
	
	            //Le tableau des critères de comparaisons
	            if(count($facette_compare->facette_groupby)){
	                $table_groupby=$facette_compare->gen_table_groupby();
	            }
	            $table .= "<h3 class='facette_compare_SubTitle'><img id='facette_compare_not_clickable' src='".get_url_icon('group_by.png')."'/> ".htmlentities($msg['facette_list_groupby_crit'],ENT_QUOTES,$charset)."</h3><table id='facette_groupby'>".$table_groupby."</table><br/>";
	        }
	        	
	        //le bouton de retour
	        if(isset($_SESSION['filtre_compare']) && $_SESSION['filtre_compare']=='compare'){
	            $table .= "<input type='button' class='bouton backToResults' value='".htmlentities($msg['facette_compare_search_result'],ENT_QUOTES,$charset)."' onclick='".static::get_link_back()."'/><br /><br />";
	        }elseif((!isset($_SESSION['filtre_compare']) || $_SESSION['filtre_compare']!='compare') && count($facette_compare->facette_compare)){
	            $table .= "<input type='button' class='bouton' value='".htmlentities($msg['facette_compare_search_compare'],ENT_QUOTES,$charset)."' onclick='valid_compare();'/><br /><br />";
	        }
	    }
	
	    if(count($this->get_not_clicked())){
	        if(static::get_compare_notice_active()){
	            $table .= "<div id='facettes_help'></div>";
	            $table .= "<h3 class='facette_compare_listTitle'>".htmlentities($msg['facette_list_compare'],ENT_QUOTES,$charset)." &nbsp;<img onclick='open_popup(document.getElementById(\"facettes_help\"),\"".htmlentities($msg['facette_compare_helper_message'],ENT_QUOTES,$charset)."\");' height='18px' width='18px' title='".htmlentities($msg['facette_compare_helper'],ENT_QUOTES,$charset)."' alt='".htmlentities($msg['facette_compare_helper'],ENT_QUOTES,$charset)."' src='".get_url_icon('quest.png')."'/></h3>".$this->get_display_not_clicked()."<br/>";
	        }else{
	            $table .= "<h3 class='facette_compare_listTitle'>".htmlentities($msg['facette_list'],ENT_QUOTES,$charset)."</h3>".$this->get_display_not_clicked()."<br/>";
	        }
	    }
	    $table .= "</form>";
	    $table.= "<script type='text/javascript'>
                    require(['apps/pmb/search_universe/SearchSegmentController'], function(SearchSegmentController){
                        new SearchSegmentController({numSegment : '".static::get_num_segment()."' ".($universe_query ? ',universeQuery: "'.$universe_query.'"' : '')."});
                    });
                </script>";
	    return $table;
	}
	
	public static function make_facette_search_env() {
	    global $search;
	    global $check_facette;
	
	    //creation des globales => parametres de recherche
	    $n = count($search);
	    if (is_array($check_facette)) { 
	        $fields = [];
	        foreach($check_facette as $facet){
	            if(!isset($fields[$facet[2]][$facet[3]])){
	                $facet[1] = array($facet[1]);
                    $fields[$facet[2]][$facet[3]] = $facet;
	            }else{
	                $fields[$facet[2]][$facet[3]][1][] = $facet[1];
	            }
	        }
	        $i = 0;
	        foreach($fields as $field => $subfields){
	            foreach($subfields as $subfield){
	                $search[] = "s_3";
	                $fieldname = "field_".($i+$n)."_s_3";
	                global ${$fieldname};
	                ${$fieldname} = array($subfield);
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
	
	public static function get_session_values() {
        return null;
	}
	
	public static function set_session_values($session_values) {
		return;
	}
	
	protected function get_filter_button_action() {
	    return "";
	}
	
	protected function on_facet_click($link = '') {
	    return "";
	}
	
	public function call_facets($additional_content = "") {
	    global $universe_query;
	    global $base_path;
	    
		$ajax_facettes = $additional_content;
        $ajax_facettes .= static::get_facette_wrapper();
        $ajax_facettes .="
			<div id='facette_wrapper'>
				<img src='".get_url_icon('patience.gif')."'/>
				<script type='text/javascript'>
				    require(['dojo/query', 'dojo/dom-construct', 'dojo/request/xhr', 'dojo/ready', 'dojo/dom', 'dojo/parser'], function(query, domConstruct, xhr, ready, dom, parser){
			            var url = '".$base_path."/ajax.php?module=ajax&categ=facettes&sub=get_data&num_segment=".$this->num_segment."';
				        xhr(url,{
    						data : {segment_search : '".$this->get_segment_search()."', universe_query: '".$universe_query."'},
    						handleAs: 'json',
    						method:'POST',
    					}).then(function(response){
    						if (response) {
    						    dom.byId('facette_wrapper').innerHTML = response.display;
    						    query('script').forEach(function(node) {
                					domConstruct.create('script', {
                						innerHTML: node.innerHTML,
                						type: 'text/javascript'
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
			</div>";
	    return $ajax_facettes;
	}
	
	protected function create_search_environment() {	    
	    $search_class = new search();
	    $search_class->json_decode_search($this->get_segment_search());
	}
	
	public function get_clicked() {	    
	    if(!isset($this->clicked)) {
	       global $search;
	       $this->clicked = array();
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
	
	protected function get_query_by_facette($id_critere, $id_ss_critere, $type = "notices") {
	    global $lang;
	    
	    if($type == 'notices'){
	        $plural_prefix = 'notices';
	        $prefix = 'notice';
	    }else{
	        $plural_prefix = 'authorities';
	        $prefix = 'authority';
	    }
   	    $query = 'select value ,count(distinct id_'.$prefix.') as nb_result from (SELECT value,id_'.$prefix.' FROM '.$plural_prefix.'_fields_global_index'.
   	   	    gen_where_in($plural_prefix.'_fields_global_index.id_'.$prefix, $this->objects_ids).'
				AND code_champ = '.($id_critere+0).'
				AND code_ss_champ = '.($id_ss_critere+0).'
				AND lang in ("","'.$lang.'","'.substr($lang,0,2).'")) as sub
				GROUP BY value
				ORDER BY ';
   	   	    
   	    return $query;
	}
	
	public function get_ajax_facette() {
	    $facettes_exists_with_or_without_results = false;
	    if($this->exists_with_results || count($this->get_clicked())){
	        $facettes_exists_with_or_without_results = true;
	    }
	    return array(
	        'exists_with_results' => ($_SESSION["cms_build_activate"] ? true : $facettes_exists_with_or_without_results),
	        'display' => $this->create_ajax_table_facettes(),
	        'map_location' =>  $this->get_map_location()
	    );
	}
}