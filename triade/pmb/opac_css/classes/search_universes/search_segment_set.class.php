<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_set.class.php,v 1.20.2.3.2.1 2025/03/17 13:36:37 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/search_universes/search_segment_set.tpl.php');
require_once($class_path."/search.class.php");

class search_segment_set {

	protected $num_segment;

	protected $human_query;

	protected $data_set;

	protected $type;

	protected $segment_label = "";

	protected $table_tempo;

	/**
	 * @var search|search_authorities
	 */
	protected $search_instance;

	public function __construct($num_segment = 0){
		$this->num_segment = intval($num_segment);
		$this->fetch_data();
	}

	protected function fetch_data() {
	    $this->type = '';

		if ($this->num_segment) {
			$query = '
			    SELECT search_segment_set, search_segment_type, search_segment_label
			    FROM search_segments
			    WHERE id_search_segment = "'.$this->num_segment.'"
			';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_assoc($result);
				pmb_mysql_free_result($result);

				$this->data_set = $row['search_segment_set'];
				$this->segment_label = $row['search_segment_label'];
				$this->type = $row['search_segment_type'];
			}
		}
	}

	/**
	 * Retourne le jeu de recherche
	 *
	 * @return string
	 */
	public function get_data_set() {
	    return $this->data_set ?? "";
	}

	public function get_human_query() {
	    return $this->segment_label;
	}

	public function get_form() {
	    global $msg, $charset, $base_url;
	    global $search_segment_set_form;

	    if (empty($search_segment_set_form))  {
	        return '';
	    }

	    $search_segment_set_form = str_replace('!!segment_id!!', $this->num_segment, $search_segment_set_form);
	    $search_segment_set_form = str_replace('!!segment_type!!', $this->get_search_type_from_segment_type(), $search_segment_set_form);
	    $search_segment_set_form = str_replace('!!segment_set_human_query!!', $this->get_human_query(), $search_segment_set_form);
	    $search_segment_set_form = str_replace('!!segment_set_data_set!!', $this->get_data_set(), $search_segment_set_form);

	    return $search_segment_set_form;
	}

	public function set_properties_from_form(){
	    $search = $this->get_search_instance();
	    $this->data_set = $search->json_encode_search();
	    $this->human_query = $search->make_human_query();
	}

	public function update() {
	    if (!$this->num_segment) {
	        return false;
	    }
		$query = '
		    UPDATE search_segments
		    SET search_segment_set = "'.addslashes($this->data_set).'"
		    WHERE id_search_segment = "'.$this->num_segment.'"';
		pmb_mysql_query($query);

		return true;

	}

	public function get_search_instance() {
		if (isset($this->search_instance)) {
			return $this->search_instance;
		}
	    if (isset($this->type)) {
	        switch($this->type) {
	            case TYPE_NOTICE :
	                $this->search_instance = new search('search_fields_gestion');
	                break;
	            case TYPE_EXTERNAL :
	                $this->search_instance = new search('search_fields_unimarc_gestion');
	                break;
	            case TYPE_ANIMATION :
	                $this->search_instance = new search('search_fields_animations');
	                break;
	            case TYPE_CMS_EDITORIAL :
	                $this->search_instance = new search('search_fields_cms_editorial');
	                break;
	            default :
	                $this->search_instance = new search_authorities('search_fields_authorities_gestion');
	                break;
	        }
	        return $this->search_instance;
	    }
	    $this->search_instance = new search('search_fields_gestion');
	    return $this->search_instance;
	}

	protected function get_search_type_from_segment_type() {
	    if (isset($this->type)) {
	        switch($this->type) {
	            case TYPE_AUTHOR :
	                return 'auteur';
	            case TYPE_CATEGORY :
	                return 'categorie';
	            case TYPE_COLLECTION :
	                return 'collection';
	            case TYPE_CONCEPT :
	                return 'ontology';
	            case TYPE_INDEXINT :
	                return 'indexint';
	            case TYPE_NOTICE :
	                return 'notice';
	            case TYPE_PUBLISHER :
	                return 'editeur';
	            case TYPE_SERIE :
	                return 'serie';
	            case TYPE_SUBCOLLECTION :
	                return 'subcollection';
	            case TYPE_TITRE_UNIFORME :
	                return 'titre_uniforme';
	            case TYPE_ANIMATION :
	                return 'animations';
	            case TYPE_CMS_EDITORIAL :
	                return 'cms_editorial';
	            default :
	                if (intval($this->type) > 1000) {
	                    $id_authperso = (intval($this->type) - 1000);
	                    return 'authperso';
	                }
	                return 'notice';

	        }
	    }
	}

	//Permet de savoir si nous avons des notices ou des autorit�s ?
	public function get_entity_type_segment(){
	    if (isset($this->type)) {
	        switch ($this->type) {
	            case TYPE_NOTICE;
    	            return 'record';
	            case TYPE_ANIMATION;
    	            return 'animations';
	            case TYPE_CMS_EDITORIAL;
    	            return 'cms_editorial';
	            default:
        	        return 'authority';
	        }
	    }
	}

	public function make_search($prefix = '') {
	    global $search;

	    if (!is_array($search)) {
	    	$search = array();
	    }
		if (isset($this->table_tempo)) {
			return $this->table_tempo;
		}

		$this->get_search_instance();
		if (empty($this->data_set)) {
			$this->search_instance->push();
			$this->data_set = combine_search::simple_search_to_mc(stripslashes('*'), true, $this->type, $this->search_instance);
			$this->search_instance->pull();
		}


		$cache = $this->_get_in_cache();
		if ($cache === false) {
			$this->search_instance->json_decode_search($this->data_set);
			$this->table_tempo = $this->search_instance->make_search($prefix);
			$this->_set_in_cache();
		} else {
			global $default_tmp_storage_engine;

			$this->table_tempo = $prefix . "_" . md5(microtime(true));
			$query = "CREATE TEMPORARY TABLE ". $this->table_tempo  ." (
				". $this->search_instance->keyName ." int(11) UNIQUE,
				idiot int(1),
				pert decimal(16,1) default 1
			) ENGINE=".$default_tmp_storage_engine." ";
			pmb_mysql_query($query);

			$query = "INSERT INTO " . $this->table_tempo . " (" . $this->search_instance->keyName . ", idiot, pert) VALUES ";
			for ($i=0, $len = count($cache); $i < $len; $i++) {
				$row = $cache[$i];
				if ($i > 0) {
					$query .= ", ";
				}

				$row['idiot'] = $row['idiot'] ?? "NULL";
				$query .= "('".addslashes($row[$this->search_instance->keyName])."', '".$row['idiot']."', '".$row['pert']."')";
			}

			pmb_mysql_query($query);
		}


		return $this->table_tempo;
	}

	public function get_num_segment(){
	    return $this->num_segment;
	}

	public function set_search_instance(&$search_instance) {
	    $this->search_instance = $search_instance;
	}

	public function get_dynamic_params(){
	    global $search;
	    $this->get_search_instance();
	    $dynamic_params = [];
	    if (!empty($this->data_set)) {
	        $this->search_instance->push();
	        $this->search_instance->json_decode_search(stripslashes($this->data_set));
	        for ($i = 0 ; $i < count($search) ; $i++) {
	            if ($search[$i] == "s_12") {
	                global ${"fieldvar_".$i."_s_12"};
	                if (!empty( ${"fieldvar_".$i."_s_12"}) && is_array( ${"fieldvar_".$i."_s_12"}) &&  isset(${"fieldvar_".$i."_s_12"}[1])) {
	                    $fielvar = ${"fieldvar_".$i."_s_12"}[1];
	                    global ${$fielvar};
	                    if (${$fielvar}) {
	                        $dynamic_params[$fielvar] = ${$fielvar};
	                    }
	                }
	            }
	        }
	        $this->search_instance->pull();
	    }
	    return $dynamic_params;
	}

	/**
	 * Permet de savoir s'il y a un champ dynamique
	 *
	 * @return Boolean
	 */
	public function use_dynamic_field()
	{
	    return strpos($this->data_set, "s_12") !== false;
	}

	/**
	 * Retourne la signature de la recherche pour le cache
	 *
	 * @return string
	 */
	protected function _get_sign()
	{
		return md5("segment_id=" . $this->num_segment);
	}

	/**
	 * Permet de savoir si nous avons la recherche en cache
	 *
	 * @return false|array
	 */
	protected function _get_in_cache()
	{
		if ($this->use_dynamic_field()) {
			// on ne fait pas de cache des recherches dynamiques
			return false;
		}

		$read = "select value from search_cache where object_id='".$this->_get_sign()."'";
		$res = pmb_mysql_query($read);
		if (pmb_mysql_num_rows($res)){
			$row = pmb_mysql_fetch_assoc($res);
			$value = gzuncompress($row['value']);
		    $value = unserialize($value);
			return $value ?? false;
		} else {
			return false;
		}
	}

	/**
	 * Permet de mettre la recherche en cache
	 *
	 * @return void
	 */
	protected function _set_in_cache()
	{
		global $opac_search_cache_duration;

		if ($this->use_dynamic_field()) {
			// on ne fait pas de cache des recherches dynamiques
			return false;
		}

		$query = "SELECT * FROM " . $this->table_tempo;
		$result = pmb_mysql_query($query);
		if (!pmb_mysql_num_rows($result)) {
			return false;
		}

		$data = [];
		while ($row = pmb_mysql_fetch_assoc($result)) {
			$data[] = $row;
		}
		pmb_mysql_free_result($result);

		$str_to_cache = gzcompress(serialize($data));

		$data = null;
		$row = null;

		if (!pmb_mysql_num_rows(pmb_mysql_query('select 1 from search_cache where object_id = "'.addslashes($this->_get_sign()).'" limit 1'))) {
			$duration = intval($opac_search_cache_duration) * 2;
			$insert = "insert into search_cache set object_id ='".addslashes($this->_get_sign())."', value ='".addslashes($str_to_cache)."', delete_on_date = now() + interval ".$duration." second";
			pmb_mysql_query($insert);
		}
	}
}