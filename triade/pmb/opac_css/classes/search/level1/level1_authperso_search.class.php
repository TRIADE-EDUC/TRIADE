<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_authperso_search.class.php,v 1.4.14.1.2.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/search/level1/level1_authorities_search.class.php");
require_once($class_path."/authperso.class.php");

class level1_authperso_search extends level1_authorities_search {

	protected $authperso_id;

	public function set_authperso_id($authperso_id) {
		$this->authperso_id = intval($authperso_id);
	}

	protected function get_hidden_search_form_name() {
		$form_name = '';
		$form_name .= "search_authperso_".$this->authperso_id;
		return $form_name;
	}

	public function get_query() {
		$query = parent::get_query();
		$query .= " and authperso_authority_authperso_num = ".$this->authperso_id;
		return $query;
	}

	protected function get_mode() {
		return "authperso_".$this->authperso_id;
	}

	protected function get_session_key() {
		return "authperso_".$this->authperso_id;
	}

	protected function add_in_session() {
		parent::add_in_session();
		$_SESSION["level1"]["authperso_".$this->authperso_id]["name"] = authpersos::get_name($this->authperso_id);
	}

	public function proceed() {
    	global $msg, $charset;

		if($this->get_nb_results()) {
			print "<div class='search_result' id=\"".$this->type."_".$this->authperso_id."\" name=\"".$this->type."_".$this->authperso_id."\">";
    		print "<strong>".htmlentities(authpersos::get_name($this->authperso_id), ENT_QUOTES, $charset)."</strong> ";
    		print $this->get_display_link_result();
    		print $this->get_hidden_search_form();
    		print "</div>";
    	}
    }

    /**
     * Enregistrement des stats
     */
    protected function search_log($count) {
    	global $nb_results_tab;
    	global $mode;

		$nb_results_tab[$mode] = $count;
    }

    public function get_searcher_instance() {
        $searcher = searcher_factory::get_searcher($this->type, '', $this->user_query, $this->authperso_id);
        return $searcher;
    }
}
?>