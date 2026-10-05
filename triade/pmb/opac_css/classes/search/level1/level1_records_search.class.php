<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_records_search.class.php,v 1.3.2.1 2024/05/02 15:39:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/level1_search.class.php");

class level1_records_search extends level1_search {

	protected $searcher;

	protected function get_hidden_search_form_name() {
    	$form_name = '';
    	switch ($this->type) {
    		case 'title':
    			$form_name .= "search_objects";
    			break;
    		default:
    			$form_name .= parent::get_hidden_search_form_name();
    			break;
    	}
    	return $form_name;
    }

    protected function get_hidden_search_content_form() {
    	global $charset;
    	global $typdoc;

    	$content_form = parent::get_hidden_search_content_form();
    	$content_form .= "<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">\n";
    	$content_form .= "<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities(implode(",",$this->get_searcher()->get_typdocs()),ENT_QUOTES,$charset)."\">";
    	return $content_form;
    }

    protected function get_searcher() {
    	if(!isset($this->searcher)) {
	    	switch ($this->type) {
	    		case 'title':
	    			$this->searcher = new searcher_title($this->user_query);
	    			break;
	    		case 'keywords':
	    			$this->searcher = new searcher_keywords($this->user_query);
	    			break;
	    		case 'abstract':
	    			$this->searcher = new searcher_abstract($this->user_query);
	    			break;
	    		case 'extended':
	    		    $this->searcher = new searcher_extended($this->user_query);
	    		    break;
	    		case 'all':
	    			global $map_emprises_query;
	    			$this->searcher = searcher_factory::get_searcher('records', 'all_fields', $this->user_query,$map_emprises_query);
	    			break;
	    		case 'ai_search':
	    			$this->searcher = new searcher_ai_search($this->user_query);
	    			break;
	    	}
    	}
	    return $this->searcher;
    }

    public function get_nb_results() {
    	if(!isset($this->nb_results)) {
    		$searcher = $this->get_searcher();
    		$searcher->get_result();
    		$this->nb_results = $searcher->get_nb_results();
	    	if($this->nb_results) {
	    		$this->add_in_session();
	    	}
    	}
    	return $this->nb_results;
    }

    protected function add_in_session() {
        if(!is_array($_SESSION["level1"][$this->type])) {
            $_SESSION["level1"][$this->type] = [];
        }
        $_SESSION["level1"][$this->type]["form"] = $this->get_hidden_search_form();;
    	$_SESSION["level1"][$this->type]["count"] = $this->get_nb_results();
    }

    protected function get_search_type() {
    	return 'notices';
    }
}
?>