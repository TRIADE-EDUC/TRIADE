<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level2_authpersos_search.class.php,v 1.3.2.1 2024/06/12 07:10:51 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/level2_search.class.php");
require_once($class_path."/elements_list/elements_authorities_list_ui.class.php");
require_once($class_path."/searcher/opac_searcher_autorities_skos_concepts.class.php");
require_once($class_path."/thesaurus.class.php");

class level2_authpersos_search extends level2_authorities_search {
    protected $authperso_id;

    public function __construct($user_query, $type) {
        parent::__construct($user_query, $type);
    }

    public function get_searcher_instance() {
        return searcher_factory::get_searcher($this->type, '', $this->user_query, $this->authperso_id);
    }

    public function get_authperso_id() {
        return $this->authperso_id;
    }

    public function set_authperso_id($authperso_id) {
        $this->authperso_id = intval($authperso_id);
    }
}
?>