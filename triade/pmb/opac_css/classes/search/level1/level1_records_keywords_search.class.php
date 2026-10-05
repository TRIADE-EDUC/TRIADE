<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_records_keywords_search.class.php,v 1.5 2023/08/17 09:47:57 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/search/level1/level1_records_search.class.php");

class level1_records_keywords_search extends level1_records_search {
	    
    public function proceed() {
    	global $msg, $charset;
    	global $opac_allow_affiliate_search;
    	global $auto_submit;
    	global $opac_allow_tags_search;
    	
    	global $search_terms;
    	$aq = new analyse_query($this->user_query);
    	$search_terms = $aq->get_positive_terms($aq->tree);
    	//On enlève le dernier terme car il s'agit de la recherche booléenne complète
    	if (!empty($search_terms)) {
        	unset($search_terms[count($search_terms)-1]);
    	}
    	
    	if($opac_allow_affiliate_search){
    		if ($auto_submit) {
    			if($this->get_nb_results()){
    				print "<div class='search_result'>".$this->get_hidden_search_form()."</div>
    					<script>
							document.search_keywords.action = '".$this->get_form_action()."&tab=catalog';
							document.search_keywords.submit();
						</script>";
    			}
    		} else {
    			print $this->get_display_result_affiliate();
    		}
    	}else{
    		if($this->get_nb_results()) {
    			if ($auto_submit) {
    				print $this->get_hidden_search_form()."<script>document.forms['search_keywords'].submit();</script>";
    			} else {
    				print "<div class='search_result' id=\"titre\" name=\"titre\">";
    				print "<strong>";
    				if($opac_allow_tags_search) {
    					print $msg['tag'];
    				} else {
    					print $msg['keywords'];
    				}
					print "</strong> ";
					print $this->get_display_link_result();
					print $this->get_hidden_search_form();
					print "</div>";
    			}
    		}
    	}
    }
    
    protected function get_mode() {
    	return 'keyword';
    }
}
?>