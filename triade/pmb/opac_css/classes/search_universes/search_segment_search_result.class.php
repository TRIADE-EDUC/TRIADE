<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_search_result.class.php,v 1.25 2019-06-13 15:33:03 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path,$include_path,$class_path,$msg;
require_once($class_path."/search_universes/search_segment_facets.class.php");
require_once($class_path."/search_universes/search_universes_history.class.php");
require_once($class_path."/searcher/searcher_factory.class.php");
require_once($class_path."/more_results.class.php");
require_once($include_path.'/search_queries/specials/combine/search.class.php');
require_once($class_path.'/cms/cms_editorial_searcher.class.php');
require_once($class_path.'/elements_list/elements_cms_editorial_articles_list_ui.class.php');
require_once($class_path.'/elements_list/elements_cms_editorial_sections_list_ui.class.php');
require_once($class_path.'/elements_list/elements_concepts_list_ui.class.php');
require_once $class_path.'/entities.class.php';

class search_segment_search_result {
    
    /**
     * 
     * @var search_segment
     */
    protected $segment;
    
    protected $searcher;
	
    public function __construct($segment) {
        $this->segment = $segment;
    }
	
	public function get_display_facets() {
		global $es, $base_path;
		
		$facettes_tpl = '';
		$tab_result = $this->init_session_facets();
		$segment_facets = new search_segment_facets();
		$segment_facets->set_num_segment($this->segment->get_id());
		$segment_facets->set_segment_search($es->json_encode_search());
	    $content = $es->make_segment_search_form($base_path.'/index.php?lvl=search_segment&id='.$this->segment->get_id().'&action=segment_results', 'form_values', "", true);
	    $facettes_tpl .= $segment_facets->call_facets($content);
		
		return $facettes_tpl;
	}
	
	protected function get_searcher() {
	    global $user_query;
	    if (!isset($this->searcher)) {
    	    if ($this->segment->get_type() == TYPE_NOTICE) {
    	        $this->searcher = searcher_factory::get_searcher('records', 'extended');
    	    } elseif(($this->segment->get_type() == TYPE_CMS_ARTICLE) || ($this->segment->get_type() == TYPE_CMS_SECTION)) {
    	        $this->searcher = new cms_editorial_searcher('*', (TYPE_CMS_ARTICLE ? 'article' : 'section'));
    	    } else {
    	        $this->searcher = searcher_factory::get_searcher('authorities', 'extended');
    	    }
	    }
	    return $this->searcher;
	}	
	
	public function get_nb_results() {
	    global $search_type;
	    
	    $search_type="search_universes";
	    $this->prepare_segment_search();
	    search_segment_facets::checked_facette_search();
	    //search_segment_facets::make_facette_search_env();
	    rec_history();
	    $this->get_searcher();
	    
	    return $this->searcher->get_nb_results();
	}
	
	protected function prepare_segment_search(){
	    global $user_query;
	    global $universe_query;
	    global $search;
	    global $segment_json_search;
	    global $deleted_search_nb;
	    global $es;
	    
	    if(!is_object($es)){
	    	if($this->get_type_from_segment() == TYPE_NOTICE){
            	$es = new search();
	    	}elseif(($this->get_type_from_segment() == TYPE_CMS_ARTICLE) || ($this->get_type_from_segment() == TYPE_CMS_SECTION)){
	    	    $es = new search('search_fields_articles');
	    	}else{
            	$es = new search_authorities('search_fields_authorities');
            }
	    }
	    
	    if (!is_array($search)) {
	    	$search = array();
	    }
	    
	    search_universes_history::update_json_search_with_history();
	    
	    if (!empty($segment_json_search)) {
	    	$es->json_decode_search(stripslashes($segment_json_search));
	    }
	    
	    if (!in_array('s_10', $search)) {
	    	$new_index = count($search);
		    $search[$new_index] = 's_10';
	    
	    	global ${'inter_'.$new_index.'_s_10'};
		    global ${'op_'.$new_index.'_s_10'};
		    global ${'field_'.$new_index.'_s_10'};
	    
	    	${'inter_'.$new_index.'_s_10'} = 'and';
		    ${'op_'.$new_index.'_s_10'} = 'EQ';
	    	${'field_'.$new_index.'_s_10'} = array($this->segment->get_id());
	    	
	    	//ajout de l'universe_query dans le cas d'un changement de segment (sans user_query)
	    	search_universes_history::init_universe_query_from_history();
	    	if (empty($user_query) && !empty($universe_query)) {
	    	    $universe_query_mc = combine_search::simple_search_to_mc(stripslashes($universe_query), true, $this->get_type_from_segment());
	    	    $es->json_decode_search($universe_query_mc);
	    	}
	    }
	    
	    if (!empty($user_query)) {
	    	$user_query_mc = combine_search::simple_search_to_mc(stripslashes($user_query), true, $this->get_type_from_segment());
	    	$es->json_decode_search($user_query_mc);
	    	unset($user_query);
	    }
	    
	    if (isset($deleted_search_nb)) {
	    	$es->delete_search($deleted_search_nb);
	    }
	    
	    $this->init_global_universe_id();
	}
	
	public function get_display_results($display_navbar = true) {
	    global $base_path;
	    global $debut,$opac_search_results_per_page;
	    global $count, $page, $es;
	    global $facettes_tpl;
	    global $charset;
	    global $msg;
	    
	    $count = $this->get_nb_results();
	    
	    $html = '<div id="search_universe_segment_result_list">';	    
	    //il faudrait revoir ce système de globales
	    if($count > 0){
	        $html.= "<h4 class='segment_search_results'>".$count." ".htmlentities($msg['results'], ENT_QUOTES, $charset)."</h4>";
	        if(!$page) {
	            $debut = 0;
	        } else {
	            $debut = ($page-1)*$opac_search_results_per_page;
	        }
	        if(($this->get_type_from_segment() == TYPE_NOTICE) && (isset($_SESSION["last_sortnotices"]) && $_SESSION["last_sortnotices"]!=="")){
	            $sorted_results = $this->searcher->get_sorted_result($_SESSION["last_sortnotices"],$debut,$opac_search_results_per_page);
	        }else{
	            if(($this->get_type_from_segment() == TYPE_CMS_ARTICLE) || ($this->get_type_from_segment() == TYPE_CMS_SECTION)){
	                $sorted_results = array_slice($this->searcher->get_sorted_result("article_title", "asc", 0),$debut,$opac_search_results_per_page);
	            }else{
	                $sorted_results = $this->get_sorted_result();
	            }
	            
	        }
	        if(is_string($sorted_results)){
	        	$sorted_results = explode(',', $sorted_results);
	        }
	        if (count($sorted_results)) {
	            $_SESSION['tab_result_current_page'] = implode(",", $sorted_results);
	        } else {
	            $_SESSION['tab_result_current_page'] = "";
	        }
	        //TODO cartographie ?
	        //print searcher::get_current_search_map(0);
	    }else{
	        $html.= "<h4 class='segment_search_results'>".htmlentities($msg['no_result'], ENT_QUOTES, $charset)."</h4>";
	    }
		
	    if($this->get_type_from_segment() == TYPE_NOTICE){
	        $html = '<div id="search_universe_segment_result_list">'.aff_notice(-1);
	    	$recherche_ajax_mode=0;
	    	
	    	for ($i =0 ; $i<count($sorted_results);$i++) {
	    		if($i>4) {
	    			$recherche_ajax_mode=1;
	    		}
	    			
	    		$html.= pmb_bidi(aff_notice($sorted_results[$i], 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
	    	}
	    	$html.= aff_notice(-2);
	    }elseif(($this->get_type_from_segment() == TYPE_CMS_SECTION) || ($this->get_type_from_segment() == TYPE_CMS_ARTICLE)){
	        if($this->get_type_from_segment() == TYPE_CMS_ARTICLE){
	            $cms_list_ui = new elements_cms_editorial_articles_list_ui($sorted_results, $count, true);
	        }else{
	            $cms_list_ui = new elements_cms_editorial_sections_list_ui($sorted_results, $count, true);
	        }
	        $html .= $cms_list_ui->get_elements_list();
	    }else{
	    	if($sorted_results){
// 	    		$sorted_results = array_slice($sorted_results, $debut, $opac_search_results_per_page);
	    	    if($this->get_type_from_segment() == TYPE_CONCEPT){
	    	      $elements_list_ui = new elements_concepts_list_ui($sorted_results, $count, true);	    	      
	    	    } else {	    	        
	    	      $elements_list_ui = new elements_authorities_list_ui($sorted_results, $count, true);
	    	    }
	    		$html .= $elements_list_ui->get_elements_list();
	    	}
	    	
	    }
	    $html.= facette_search_compare::form_write_facette_compare();
	    if($display_navbar){
	        $html.= more_results::get_navbar();
	        $facettes_tpl = $this->get_display_facets();
	    }
	    $html.= "</div>";
	    return $html;
	}

	protected function init_session_facets() {
	    global $reinit_facette;
	    global $es;
	    global $search_type;
        
	    $tab_result = $this->get_searcher()->get_result();
	    $_SESSION['segment_result'][$this->segment->get_id()] = $this->searcher->get_result();
	    return $tab_result;
	}
	
	protected function get_type_from_segment(){
		return $this->segment->get_type();
	}
	
	protected function init_global_universe_id() {
	    global $universe_id;
	    global $search_index;
	    
	    //si on ne provient pas d'un univers, n'y d'un historique
	    if (empty($universe_id) && empty($search_index)) {
	        $universe_id = $this->segment->get_num_universe(); 
	    }
	}
	
	protected function get_sorted_result() {
	    global $debut, $opac_search_results_per_page;
	    // Méthode provisoire pour classer par ordre alphabétique les résultats de recherche
	    // à revoir plus tard pour des tris paramétrables
	    if (get_class($this->searcher) == 'searcher_extended') {
	        return $this->searcher->get_sorted_result("default",$debut,$opac_search_results_per_page);
	    }
	    $aut_table = entities::get_aut_table_from_type($this->segment->get_type());
	    $table = entities::get_table_from_const($aut_table);
	    $prefix = entities::get_table_prefix_from_const($aut_table);
	    $join = '';
	    $index = '';
	    switch ($aut_table) {
	        case AUT_TABLE_CONCEPT:
	            return $this->searcher->get_sorted_result("default",$debut,$opac_search_results_per_page);
	        case AUT_TABLE_CATEG:
	            $index = $table.'.index_'.$prefix;
	            $join = 'join '.$table.' on '.$table.'.num_noeud = authorities.num_object';
                break;
	        case AUT_TABLE_PUBLISHERS:
	            $index = $table.'.index_'.$prefix;
	            $join = 'join '.$table.' on '.$table.'.ed_id = authorities.num_object';
	            break;
	        case AUT_TABLE_COLLECTIONS:
	            $index = $table.'.index_coll';
	            $join = 'join '.$table.' on '.$table.'.'.$prefix.'_id = authorities.num_object';	
	            break;
	        case AUT_TABLE_SERIES:
	            $index = $table.'.serie_index';
	            $join = 'join '.$table.' on '.$table.'.'.$prefix.'_id = authorities.num_object';
	            break;
	        default :
	            $index = $table.'.index_'.$prefix;
    	        $join = 'join '.$table.' on '.$table.'.'.$prefix.'_id = authorities.num_object';
	            break;
	    }
	    
	    $sorted_results = array();
	    $query = 'select authorities.id_authority, '.$index.' as name
     	        from authorities '.$join.'
    	        where id_authority
     	        in ('.$this->searcher->get_objects_ids().')
     	        order by name ASC limit '.$debut.','.$opac_search_results_per_page;
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        while($row = pmb_mysql_fetch_assoc($result)) {
	            $sorted_results[]=$row['id_authority'];
	        }
	    }
	    return $sorted_results;
	}
}