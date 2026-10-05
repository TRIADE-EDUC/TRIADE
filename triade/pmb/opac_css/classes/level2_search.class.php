<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level2_search.class.php,v 1.11 2023/12/08 15:25:27 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $opac_search_other_function;
if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

/* Classe qui permet de faire la recherche de premier niveau */

class level2_search {
	public $user_query;
	public $type;

	protected $elements_ids;

    public function __construct($user_query, $type) {
    	$this->user_query = $user_query;
    	$this->type = $type;
    }

    protected function get_title() {
    	global $msg;

    	return $msg[$this->type.'_found'];
    }

    protected function get_query() {
    	$query = '';
    	return $query;
    }

//     protected function get_display_element($element) {
//     	$display = '';
//     	switch($this->type) {
//     		case 'indexint':
//     			$display .= "<a href='".$this->get_permalink($element->indexint_id)."'><img src='".get_url_icon('folder.gif')."' style='border:0px'/> ".$element->indexint_name." ".$element->indexint_comment."</a>";
//     			break;
//     		default:
//     			$display .= "<li class='categ_colonne'><span class='notice_fort'><a href='".$this->get_permalink($element->id)."'>".$element->name."</a></span></li>";
// //     			$display .= elements_authorities_list_ui::generate_element($element->id);
//     			break;
//     	}
//     	return $display;
//     }

    /**
     * Enregistrement des stats
     */
    protected function search_log($count) {
    	global $nb_results_tab;

		$nb_results_tab[$this->type] = $count;
    }

	public function proceed() {
		global $msg, $charset;
		global $include_path;
		global $opac_allow_affiliate_search;
		global $search_result_affiliate_lvl2_head;
		global $search_result_affiliate_lvl2_footer;
		global $opac_search_other_function;
		global $catal_navbar;
		global $pmb_logs_activate;
		global $count;
		global $tab;

	    if($opac_allow_affiliate_search){
	        print str_replace('!!title!!', common::format_title($msg['resultat_recherche']), $search_result_affiliate_lvl2_head);
		}else {
			print "
            <div id=\"resultatrech\">".common::format_title($msg['resultat_recherche'])."
				<div id=\"resultatrech_container\">
				    <div id=\"resultatrech_see\">";
		}

		//le contenu du catalogue est calculé dans 2 cas  :
		// 1- la recherche affiliée n'est pas activée, c'est donc le seul résultat affichable
		// 2- la recherche affiliée est active et on demande l'onglet catalog...
		if(!$opac_allow_affiliate_search || ($opac_allow_affiliate_search && $tab == "catalog")){
		    $display = $this->get_display_elements_list();
		    print $this->get_search_title();
			print '<div id="resultatrech_liste">' . $display . '</div>';
			if($opac_allow_affiliate_search) {
			    print $catal_navbar . $search_result_affiliate_lvl2_footer;
			}
			else print "</div></div>";
			if ($this->type == 'extended') print "</div>"; // un div en +
		}else{
			$this->search_affiliate();
		}
		//Enregistrement des stats
		if($pmb_logs_activate){
			$this->search_log($count);
		}
    }

    public function get_elements_ids() {
    	if(!isset($this->elements_ids)) {
    		$this->elements_ids = array();
    	}
    	return $this->elements_ids;
    }

    public function get_search_title(){
    	global $charset, $count, $opac_search_other_function, $opac_allow_affiliate_search;
    	$search_title = "<h3 class='searchResult-search' id='searchResult-search' >
				<span class='searchResult-equation'>
					<b>".$count."</b> ".$this->get_title()." <b>
					'".htmlentities($this->user_query,ENT_QUOTES,$charset)."'";

    	if ($opac_search_other_function) {
    		$search_title.= " ".search_other_function_human_query($_SESSION["last_query"]);
    	}
    	$search_title.= "</b></span>";
    	$search_title.= activation_surlignage();
    	$search_title.= "</h3>\n";

    	if(!$opac_allow_affiliate_search)
    		$search_title.= "
					</div>";

    	return $search_title;
    }
}
?>