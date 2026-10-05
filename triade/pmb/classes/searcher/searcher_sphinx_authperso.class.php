<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_authperso.class.php,v 1.14.8.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "$class_path/searcher/searcher_sphinx_authorities.class.php";

class searcher_sphinx_authperso extends searcher_sphinx_authorities {
	protected $index_name = 'authperso';

	public $authperso_id;

	public function __construct($user_query, $id_authperso = 0) {
		global $include_path;

		$this->champ_base_path = "$include_path/indexation/authorities/authperso/champs_base.xml";
		parent::__construct($user_query);
		$this->index_name = 'authperso';
		$this->authority_type = AUT_TABLE_AUTHPERSO;
		$this->init_authperso_id($id_authperso);
		$this->object_table = "authperso_authorities";
		$this->object_table_key = "id_authperso_authority";
	}

	protected function get_filters() {
		$filters = parent::get_filters();
		return $filters;
	}

	protected function get_search_indexes() {
		global $lang, $sphinx_indexes_prefix;

		if (empty($this->authperso_id)) $this->init_authperso_id();
		if (!empty($this->authperso_id)) {
		    return $sphinx_indexes_prefix . $this->index_name . '_' . $this->authperso_id . '_' . $lang . ',' . $sphinx_indexes_prefix . $this->index_name . '_' . $this->authperso_id;
		}

		// On cherche dans toutes les autorités persos
		$indexes = '';
		$result = pmb_mysql_query('select id_authperso from authperso');
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if (!empty($indexes)) {
					$indexes .= ',';
				}
				$indexes .= $this->index_name . '_' . $row->id_authperso . '_' . $lang . ',' . $this->index_name . '_' . $row->id_authperso;
			}
		}
		return $indexes;
	}

	protected function get_full_raw_query() {
	    if (empty($this->authperso_id)) $this->init_authperso_id();
	    if (!empty($this->authperso_id)) {
		    return "SELECT id_authority AS id, 100 AS weight FROM authorities JOIN authperso_authorities ON num_object = id_authperso_authority WHERE type_object = $this->authority_type AND authperso_authority_authperso_num = $this->authperso_id";
		}

		return "SELECT id_authority AS id, 100 AS weight FROM authorities WHERE type_object = $this->authority_type";
	}

	public function get_authority_tri() {
		return 'authperso_index_infos_global';
	}

	public function init_authperso_id($id = 0) {
	    global $id_authperso, $authperso_id;

	    $this->authperso_id = $id;
	    if (empty($this->authperso_id)) {
	        // On essaie de récupérer l'id de l'autorité perso depuis les variables connues
	        $this->authperso_id = ($id_authperso ? $id_authperso : $authperso_id);
	    }
	}

	public function get_sphinx_indexes() {
	    $indexes = parent::get_sphinx_indexes();

	    return str_replace("!!id_authperso!!", $this->authperso_id, $indexes);
	}
}