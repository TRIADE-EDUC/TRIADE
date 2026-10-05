<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_records_all_search.class.php,v 1.3 2023/08/16 15:22:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/search/level1/level1_records_search.class.php");
require_once($base_path.'/includes/templates/tous.tpl.php');
require_once($base_path.'/includes/notice_affichage.inc.php');

class level1_records_all_search extends level1_records_search {

	protected function get_hidden_search_form_name() {
    	return 'search_tous';
    }

    protected function get_hidden_search_content_form() {
    	global $charset;
    	global $map_emprises_query;
    	global $opac_indexation_docnum_allfields;
    	global $join;

    	$content_form = parent::get_hidden_search_content_form();
    	if($map_emprises_query) {
    		foreach($map_emprises_query as $map_emprise_query){
    			$content_form .= "
    				<input type=\"hidden\" name=\"map_emprises_query[]\" value=\"$map_emprise_query\">";
    		}
    	}
    	if($opac_indexation_docnum_allfields) {
    		$content_form .= "<input type=\"hidden\" name=\"join\" value=\"".htmlentities($join,ENT_QUOTES,$charset)."\">";
    	}
    	return $content_form;
    }

    protected function get_mode() {
    	return 'tous';
    }

    protected function get_affiliate_label() {
    	global $msg;

    	return $msg['tous'];
    }

    protected function add_in_session() {
    	$_SESSION["level1"]['tous']["form"] = $this->get_hidden_search_form();;
    	$_SESSION["level1"]['tous']["count"] = $this->get_nb_results();
    }

    public function proceed() {
    	global $msg, $charset;
    	global $opac_allow_affiliate_search;
    	global $search_result_affiliate_lvl1;
    	global $opac_show_results_first_page;
    	global $opac_notices_depliable, $begin_result_liste;
    	global $opac_nb_results_first_page;
    	global $opac_indexation_docnum_allfields;

    	if($opac_allow_affiliate_search){
    		print $this->get_display_result_affiliate();
    		if($opac_show_results_first_page && $this->get_nb_results() > 0) {
    			print "<div id='res_first_page'>\n";
    			if ($opac_notices_depliable) print $begin_result_liste;
    			$nb=0;
    			$recherche_ajax_mode=0;
    			$notices = array();
    			$notices = $this->get_searcher()->get_sorted_result("default",0,$opac_nb_results_first_page);

    			for ($i =0 ; $i<$opac_nb_results_first_page;$i++){
    				if($i>4)$recherche_ajax_mode=1;
    				if($i==count($notices))break;
    				print pmb_bidi(aff_notice($notices[$i], 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
    			}
    			print '</div>';
    		}
    	}else{
    		if($this->get_nb_results()) {
    			$libelle=($opac_indexation_docnum_allfields ? " [".$msg['docnum_search_with']."] " : '');
				if($opac_show_results_first_page && $this->get_nb_results() > $opac_nb_results_first_page) {
					print "<strong>".$msg['tous'].$libelle."</strong> ".$opac_nb_results_first_page." ".$msg['notice_premiere']." ".$this->get_nb_results()." ".$msg['results']." ";
					print "<a href=\"javascript:document.forms['search_tous'].submit()\">".$msg['notice_toute']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' /></a><br />";
				} else {
					print "<strong>".$msg['tous'].$libelle."</strong> ".$this->get_nb_results()." ".$msg['results']." ";
					print "<a href=\"javascript:document.forms['search_tous'].submit()\"> ".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' /></a><br />";
				}
				if($opac_show_results_first_page) {
					print "<div id='res_first_page'>\n";
					if ($opac_notices_depliable) print $begin_result_liste;
					$nb=0;
					$recherche_ajax_mode=0;
					$notices = array();
					$notices = $this->get_searcher()->get_sorted_result("default",0,$opac_nb_results_first_page);
					for ($i =0 ; $i<$opac_nb_results_first_page;$i++){
						if($i>4)$recherche_ajax_mode=1;
						if($i==count($notices))break;
						print pmb_bidi(aff_notice($notices[$i], 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
					}
					print '</div>';
				}
				print "<div class='search_result'>";
				print $this->get_hidden_search_form();
				print "</div>";
    		}
    	}
    }
}
?>