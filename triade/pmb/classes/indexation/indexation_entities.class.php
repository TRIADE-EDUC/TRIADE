<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_entities.class.php,v 1.15.2.4 2024/09/19 10:41:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/indexation.class.php");

//classe générique de calcul d'indexation des entités...
class indexation_entities extends indexation {
	
	protected $mode = 'file'; //sql | file
	protected $directory_files = '';
	protected $deleted_index = true;
	
	protected $fields_prefix = '';
	protected $words_prefix = '';
	
	protected $prefix_temporary_file;
	
	protected static $objects_mode = 'all'; //all - ids - query
	protected $objects_ids;
	protected $objects_query;
	
	protected $files_lines = 0;
	
	public function __construct($xml_filepath, $table_prefix, $type = 0){
		global $base_path;
		
		$this->directory_files = $base_path.'/temp/indexation/';
		parent::__construct($xml_filepath, $table_prefix, $type);
	}
	
	public function raz_fields_table() {
		//remise a zero de la table au début
		pmb_mysql_query("TRUNCATE TABLE ".$this->fields_prefix."_global_index");
	}
	
	public function raz_words_table() {
		//remise a zero de la table au début
		pmb_mysql_query("TRUNCATE TABLE ".$this->words_prefix."_global_index");
	}
	
	public function disable_fields_table_keys() {
		pmb_mysql_query("ALTER TABLE ".$this->fields_prefix."_global_index DISABLE KEYS");
	}
	
	public function disable_words_table_keys() {
		pmb_mysql_query("ALTER TABLE ".$this->words_prefix."_global_index DISABLE KEYS");
	}
	
	public function enable_fields_table_keys() {
		pmb_mysql_query("ALTER TABLE ".$this->fields_prefix."_global_index ENABLE KEYS");
	}
	
	public function enable_words_table_keys() {
		pmb_mysql_query("ALTER TABLE ".$this->words_prefix."_global_index ENABLE KEYS");
	}
	
	protected function clean_temporary_files() {
	    
	}
	
	protected function get_prefix_temporary_file() {
	    if(empty($this->prefix_temporary_file)) {
	        $this->prefix_temporary_file = "indexation_".LOCATION;
	    }
	    return $this->prefix_temporary_file;
	}
	
	protected function get_query_objects_restriction($reference_field, $command='WHERE') {
	    if(!empty($this->objects_query)) {
	        return " ".$command." ".$reference_field." IN (".$this->objects_query.")";
	    } elseif(!empty($this->objects_ids)) {
	        return " ".$command." ".$reference_field." IN (".implode(',', $this->objects_ids).")";
	    }
	    return "";
	}
	
	protected function add_direct_fields($object_id, $datatype='all') {
		//Recherche des champs directs
	    if($this->check_datatype($datatype) && isset($this->temp_not['f']) && count($this->temp_not['f'])) {
			$this->queries[0]["rqt"]= "select ".$this->reference_table.".".$this->reference_key." as subst_for_indexation, ".implode(',',$this->temp_not['f'][0])." from ".$this->reference_table;
			$objects_restriction = $this->get_query_objects_restriction($this->reference_key);
			if($objects_restriction) {
			    $this->queries[0]["rqt"].=" ".$objects_restriction;
			} elseif($object_id) {
				$this->queries[0]["rqt"].=" where ".$this->reference_key."='".$object_id."'";
			}
			$this->queries[0]["table"]=$this->reference_table;
		}
	}
	
	protected function get_select_fields_external($table, $k, $v) {
		$select = parent::get_select_fields_external($table, $k, $v);
		//DG : on vérifie que le select ne contient pas 2 fois le champ ci-dessous
		if (in_array('categories.langue as lang', $select)) {
		    $select = array_unique($select);
		}
		$select[] = $this->reference_table.".".$this->reference_key." as subst_for_indexation";
		return $select;
	}
	
	protected function get_query_select_isbd_external($id_aut) {
		return "select $id_aut as id_aut_for_isbd, ".$this->reference_table.".".$this->reference_key." as subst_for_indexation from ".$this->reference_table;
	}
	
	protected function get_query_where_external($table) {
		$where="";
		if(static::$objects_mode == 'query') {
		    $where .= " where ".$this->reference_table.".".$this->reference_key." IN (!!objects_query!!)";
		} elseif(static::$objects_mode == 'ids') {
		    $where .= " where ".$this->reference_table.".".$this->reference_key." IN (!!objects_ids!!)";
		}
		if(isset($table['FILTER'])){
			foreach ( $table['FILTER'] as $filter ) {
				if($tmp=trim($filter["value"])){
					if(empty($where)) {
						$where.=" WHERE (".$tmp.")";
					} else {
						$where.=" AND (".$tmp.")";
					}
				}
			}
		}
		return $where;
	}
	
	protected function get_query_order_by_external() {
		return " ORDER BY subst_for_indexation";
	}
	
	protected function get_tables_from_external_field_factory($table, $v) {
		switch ($v['DATATYPE']) {
			case 'aut_link':
				$indexation_aut_link = new indexation_aut_link($this->type);
				$indexation_aut_link->set_type('entities');
				return $indexation_aut_link->get_tables($table['NAME']);
		}
	}
	
	protected function init_external_field_union_rqt($table, $k, $v) {
		$query = "SELECT * FROM (".implode(" union ",$this->queries[$k]["new_rqt"]['rqt']).") AS rqt";
		$query .= $this->get_query_order_by_external();
		$this->queries[$k]["rqt"] = $query;
	}
	
	protected function init_external_field_union_isbd_tab_req($table, $k, $v) {
		$query = "SELECT * FROM (".implode(" union ",$this->isbd_tab_req).") AS rqt";
		$query .= $this->get_query_order_by_external();
		$this->isbd_ask_list[$k]['req']=  $query;
	}
	
	protected function add_mots_query_text($nom_champ, $value, $langage, $keep_empty=false) {
		if($this->mode == 'file') {
			return array();
		}
		parent::add_mots_query_text($nom_champ, $value, $langage, $keep_empty);
	}
	
	protected function add_data_tab_insert($object_id, $infos, $value, $order_fields, $keep_empty=false) {
		if($this->mode == 'file') {
			return array();
		}
		parent::add_data_tab_insert($object_id, $infos, $value, $order_fields, $keep_empty);
	}
	
	protected function add_custom_data_tab_insert($object_id, $infos, $values, $order_fields, $keep_empty=false) {
		if($this->mode == 'file') {
			return array();
		}
		parent::add_custom_data_tab_insert($object_id, $infos, $values, $order_fields, $keep_empty);
	}
		
	protected function get_array_file_field_insert($object_id, $order_fields, $isbd, $autorite = 0) {
		return array($object_id, $order_fields, addslashes(trim($isbd)), (intval($autorite)));
	}
	
	protected function add_file_field_insert($object_id,$infos,$order_fields,$isbd, $lang = '', $autorite = 0) {
		if(!empty($this->directory_files)) {
			$content = $this->get_array_file_field_insert($object_id, $order_fields, $isbd, $autorite);
			$filename = $this->get_prefix_temporary_file()."_".$infos["champ"]."_".$infos["ss_champ"]."_".$infos["pond"].($lang ? "_".$lang : '').".pmb";
			file_put_contents($this->directory_files.$filename, json_encode($content)."\r\n", FILE_APPEND);
			
			//Ralenti l'indexation mais demandé pour réduire l'utilisation du disque
			$this->files_lines++;
			if($this->files_lines > 1000000) {
			    //Limitons l'empreinte mémoire
			    $this->maj_bdd_from_files();
			}
		}
	}
	
	protected function add_tab_field_insert($object_id,$infos,$order_fields,$isbd, $lang = '', $autorite = 0) {
		switch ($this->mode) {
			case 'file':
				$this->add_file_field_insert($object_id,$infos,$order_fields,$isbd, $lang, $autorite);
				break;
			default:
				parent::add_tab_field_insert($object_id, $infos, $order_fields, $isbd, $lang, $autorite);
				break;
		}
	}
	
	public function launch_indexation($steps_fields=false){
		//on s'assure qu'on a lu le XML et initialisé ce qu'il faut...
		if(!$this->initialized) {
			$this->init();
		}
		
		//on a des éléments à indexer...
		if (!$this->champ_trouve) {
			return false;
		}
		if(!is_dir($this->directory_files)) {
			mkdir($this->directory_files);
		} else {
		    $this->clean_temporary_files();
		}
		
		// on empile l'indexation dans le répertoire temp
		if ($steps_fields === false) {
		    $uniqId = PHP_log::prepare_time($this->get_label());
		    $this->maj(0);
		    PHP_log::register($uniqId);
		} else {
		    $this->maj_by_step($steps_fields);
		}
		
		// on dépile en base de données
		$this->maj_bdd_from_files();
		
	}
	
	protected function check_step($step='') {
        if (static::$step == 'all' || (in_array($step, static::$steps) && static::$step == $step)) {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * METHODE A RETIRER A LA FIN DES TESTS
	 * {@inheritDoc}
	 * @see indexation::maj()
	 */
	public function maj($object_id,$datatype='all'){
	    //initialisation du mode, il peut avoir été réinitialisé avant
	    $this->mode = 'file';
		$object_id = intval($object_id);
		//on s'assure qu'on a lu le XML et initialisé ce qu'il faut...
		if(!$this->initialized) {
			$this->init();
		}
		//on a des éléments à indexer...
		if (!$this->champ_trouve) {
		    return false;
		}
		if(!is_dir($this->directory_files)) {
		    mkdir($this->directory_files);
		} else {
		    $this->clean_temporary_files();
		}
		
		//Recherche des champs directs
		$this->add_direct_fields($object_id, $datatype);
		//qu'est-ce qu'on efface?
		if(!$this->deleted_index) {
			$this->delete_index($object_id, $datatype);
		}
		
		//on réinitialise les tableaux d'injection
		//qu'est-ce qu'on met a jour ?
		$this->tab_insert=array();
		$this->tab_field_insert=array();
		
		if($this->check_step('main')) {
			$this->maj_queries($object_id, $datatype);
		}
		
		// Les champs perso
		if($this->check_step('custom_field')) {
			$this->maj_custom_fields($object_id, $datatype);
		}
		
		//Les autorités perso
		if($this->check_step('authperso')) {
			$this->maj_authperso($object_id, $datatype);
		}
		
		// Les autorités perso liées
		if($this->check_step('authperso_link')) {
			$this->maj_authperso_link($object_id, $datatype);
		}
		
	    if($this->check_step('isbd') && count($this->isbd_ask_list)){
			$this->maj_isbd_ask_list($object_id, $datatype);
		}
		
		if ($this->check_step('callables') && count($this->callables)) {
			$this->maj_callables($object_id, $datatype);
		}
		return true;
	}
	
	public function maj_by_step($step=''){
	    static::$step = $step;
	    
	    $uniqId = PHP_log::prepare_time('['.$this->get_label().'] '.static::$step, 'indexation');
	    $updated = $this->maj(0);
	    if ($updated) {
	        $this->maj_bdd_from_files();
	    }
	    PHP_log::register($uniqId);
	    
	    $step_indice = array_key_exists($step, static::$steps);
	    if($step_indice !== false) {
	        $step_indice++;
	        if(!empty(static::$steps[$step_indice])) {
	            return static::$steps[$step_indice];
	        }
	    }
	    
	    return '';
	}
	
	public function maj_by_fields($step='', $fields=[]){
	    static::$step = $step;
	    $this->restrict_fields = [];
	    if (!empty($fields)) {
	        $this->restrict_fields = $fields;
	    }
	    $labels = [];
	    foreach ($fields as $field_id) {
	        $labels[] = $this->get_label_field($field_id);
	    }
	    $log_label = '['.$this->get_label().' / '.static::$step.'] '.implode(' - ', $labels);
	    $uniqId = PHP_log::prepare_time($log_label, 'indexation');
	    $updated = $this->maj(0);
	    if ($updated) {
	        //Log de charge du répertoire temporaire
	        $this->log_heavy_directory_size($log_label);
	        $this->maj_bdd_from_files();
	    }
	    PHP_log::register($uniqId);
	    if ($updated) {
            return 1;    
	    }
	    return 0;
	}
	
	protected function maj_query_get_builded_query($object_id, $query) {
	    if(!empty($this->objects_query)) {
	        return str_replace("!!objects_query!!", $this->objects_query, $query);
	    } elseif(!empty($this->objects_ids)) {
	        return str_replace("!!objects_ids!!", implode(',', $this->objects_ids), $query);
	    } else {
	        return parent::maj_query_get_builded_query($object_id, $query);
	    }
	}
	
	protected function maj_isbd_ask($object_id, $infos) {
	    if(!empty($this->objects_query)) {
	        $infos["req"] = str_replace("!!objects_query!!", $this->objects_query, $infos["req"]);
	    } elseif(!empty($this->objects_ids)) {
	        $infos["req"] = str_replace("!!objects_ids!!", implode(',', $this->objects_ids), $infos["req"]);
	    }
	    parent::maj_isbd_ask($object_id, $infos);
	}

	protected function get_query_custom_field($table) {
	    $prefix = $table;
	    return "SELECT ".$prefix."_custom_champ,".$prefix."_custom_origine,".$prefix."_custom_small_text, ".$prefix."_custom_text, ".$prefix."_custom_integer, ".$prefix."_custom_date, ".$prefix."_custom_float, ".$prefix."_custom_order, datatype
			FROM ".$prefix."_custom_values
			JOIN ".$prefix."_custom ON ".$prefix."_custom.idchamp = ".$prefix."_custom_values.".$prefix."_custom_champ AND ".$prefix."_custom.search = 1
            ".$this->get_query_objects_restriction($prefix."_custom_origine")."
	        ORDER BY ".$prefix."_custom_origine, ".$prefix."_custom_order";
	}
	
	protected function maj_custom_field($object_id, $table, $id, $code_champ) {
		global $charset;
		
		$p_perso = $this->get_parametres_perso_class($table);
		$prefix = $table;
		$query = $this->get_query_custom_field($table);
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_array($result)) {
				$code_ss_champ = $row[$prefix."_custom_champ"];
				$value = $row[$prefix."_custom_".$row['datatype']];
				$order =  $row[$prefix."_custom_order"];
				//on doit retrouver l'id des eléments...
				switch($table){
					case "expl" :
						$object_id = exemplaire::get_expl_notice_from_id($row[$prefix."_custom_origine"]);
						break;
					case "explnum" :
						$query_explnum = "select explnum_notice, explnum_bulletin from explnum where explnum_id = ".$row[$prefix."_custom_origine"];
						$result_explnum = pmb_mysql_query($query_explnum);
						$row_explnum = pmb_mysql_fetch_object($result_explnum);
						if($row_explnum->explnum_notice) {
							$object_id = $row_explnum->explnum_notice;
						}
						break;
					default :
						$object_id = $row[$prefix."_custom_origine"];
						break;
				}
				
				$val = stripslashes($p_perso->get_formatted_output(array($value),$code_ss_champ)).' ';//Sa valeur
// 				if ($this->t_fields[$field_id]["TYPE"] == "query_auth") {
// 					$return_val[$field_id] = $this->get_enhanced_values($return_val[$field_id], $value, $field_id);
// 				}
				//la table pour les recherche exacte
				$infos = array(
						'champ' => $code_champ,
						'ss_champ' => $code_ss_champ,
						'pond' => $p_perso->get_pond($code_ss_champ)
				);
				//Elimination des balises HTML - Y compris celles mal formées
				$val = preg_replace('#<[^>]+>#','',$val);
				//Lorsque cela est entité en base (ex : Editeur HTML)
				$val = html_entity_decode($val, ENT_QUOTES, $charset);
				if($val != ''){
					$this->add_tab_field_insert($object_id, $infos, $order, $val);
				}
			}
			pmb_mysql_free_result($result);
		}
	}
	
	protected function maj_custom_fields($object_id, $datatype='all') {
		if(count($this->tab_pp) && $this->check_datatype($datatype, 'custom_field')) {
			foreach ($this->tab_pp as $code_champ => $table ) {
			    if($this->check_restrict_field($code_champ)) {
				    $this->maj_custom_field($object_id, $table, 0, $code_champ);
			    }
			}
		}
	}
	
	protected function get_query_authperso_custom_field() {
	    $prefix = 'authperso';
	    return "SELECT ".$prefix."_custom_champ,".$prefix."_custom_origine,".$prefix."_custom_small_text, ".$prefix."_custom_text, ".$prefix."_custom_integer, ".$prefix."_custom_date, ".$prefix."_custom_float, ".$prefix."_custom_order, num_type, datatype
			FROM ".$prefix."_custom_values
			JOIN ".$prefix."_custom ON ".$prefix."_custom.idchamp = ".$prefix."_custom_values.".$prefix."_custom_champ AND ".$prefix."_custom.search = 1 
			".$this->get_query_objects_restriction($prefix."_custom_origine")."
            ORDER BY ".$prefix."_custom_origine, ".$prefix."_custom_order";
	}
	
	protected function maj_authperso_custom_field() {
		global $charset;
		
		$p_perso = $this->get_parametres_perso_class('authperso');
		$prefix = 'authperso';
		$query = $this->get_query_authperso_custom_field();
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_array($result)) {
				$code_champ = $this->authperso_code_champ_start+$row['num_type'];
				$code_ss_champ = $row[$prefix."_custom_champ"];
				$value = $row[$prefix."_custom_".$row['datatype']];
				$order =  $row[$prefix."_custom_order"];
				$object_id = $row[$prefix."_custom_origine"];
				
				$val = stripslashes($p_perso->get_formatted_output(array($value),$code_ss_champ)).' ';//Sa valeur
// 				if ($this->t_fields[$field_id]["TYPE"] == "query_auth") {
// 					$return_val[$field_id] = $this->get_enhanced_values($return_val[$field_id], $value, $field_id);
// 				}
					//la table pour les recherche exacte
				$infos = array(
						'champ' => $code_champ,
						'ss_champ' => $code_ss_champ,
						'pond' => $p_perso->get_pond($code_ss_champ)
				);
				//Elimination des balises HTML - Y compris celles mal formées
				$val = preg_replace('#<[^>]+>#','',$val);
				//Lorsque cela est entité en base (ex : Editeur HTML)
				$val = html_entity_decode($val, ENT_QUOTES, $charset);
				if($val != ''){
					$this->add_tab_field_insert($object_id, $infos, $order, $val);
				}
			}
			pmb_mysql_free_result($result);
		}
	}
	
	protected function get_query_authperso() {
	    return "SELECT id_authperso, notice_authperso_notice_num, id_authperso_authority
				FROM authperso, notices_authperso,authperso_authorities
				WHERE id_authperso=authperso_authority_authperso_num and notice_authperso_authority_num=id_authperso_authority
                ".$this->get_query_objects_restriction("notice_authperso_notice_num", "AND")."
	            ORDER BY notice_authperso_notice_num, notice_authperso_order";
	}
	
	protected function maj_authperso($object_id, $datatype='all') {
		global $charset;
		
		if(count($this->tab_authperso) && $this->check_datatype($datatype, 'authperso') && $this->check_restrict_field($this->authperso_code_champ_start)) {
			$order_fields=1;
			$query = $this->get_query_authperso();
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
    			$id_authperso_authority = 0;
    			while($row = pmb_mysql_fetch_object($result)) {
    				if(empty($id_authperso_authority) || ($id_authperso_authority != $row->id_authperso_authority)) {
    					$order_fields=1;
    				}
    				$code_champ = $row->id_authperso+$this->authperso_code_champ_start;
    				//la table pour les recherche exacte
    				$infos = array(
    						'champ' => $code_champ,
    						'ss_champ' => 0,
    						'pond' => 0
    				);
    				
    				$isbd = $this->get_entity_isbd('authperso', $row->id_authperso_authority);
    				//Elimination des balises HTML - Y compris celles mal formées
    				$isbd = preg_replace('#<[^>]+>#','',$isbd);
    				//Lorsque cela est entité en base (ex : Editeur HTML)
    				$isbd = html_entity_decode($isbd, ENT_QUOTES, $charset);
    				
    				$this->add_tab_field_insert($row->notice_authperso_notice_num, $infos, $order_fields, $isbd);
    				
    				$this->add_data_tab_insert($object_id, $infos, $isbd, $order_fields);
    				$order_fields++;
//     				$index_fields[$field['code_champ']]['ss_champ'][0][]
    			}
    			pmb_mysql_free_result($result);
			}
			
			$this->maj_authperso_custom_field();
		}
	}

	protected function get_query_authperso_link($object_id) {
		$object_id = intval($object_id);
		$authority_type = $this->get_authority_type();
		return "SELECT id_authperso_authority, authperso_authority_authperso_num
		FROM ".$this->reference_table."
		JOIN aut_link ON (".$this->reference_table.".".$this->reference_key."=aut_link.aut_link_from_num and aut_link_from = ".$authority_type." or (".$this->reference_table.".".$this->reference_key." = aut_link_to_num and aut_link_to = ".$authority_type." ))
		JOIN authperso_authorities ON (aut_link.aut_link_to_num=authperso_authorities.id_authperso_authority or ( aut_link_from_num=authperso_authorities.id_authperso_authority ))
		WHERE ((aut_link.aut_link_to > 1000))";
// 		".$this->get_query_objects_restriction("notice_authperso_notice_num", "AND")."
	}
	
	protected function add_isbd_s_from_query($object_id, $infos, $query) {
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$order_fields = 1;
			while($row = pmb_mysql_fetch_object($result)){
				if(empty($object_id) || ($object_id != $row->subst_for_indexation)) {
					$object_id = $row->subst_for_indexation;
					$order_fields = 1;
				}
				$entity_isbd = $this->get_entity_isbd($infos["class_name"], $row->id_aut_for_isbd);
				$this->add_isbd_ask($object_id, $entity_isbd, $infos, $order_fields);
				$order_fields++;
			}
			pmb_mysql_free_result($result);
		}
	}
	
	protected function get_query_index_concept($entity_type) {
	    return "SELECT num_object, num_concept, order_concept FROM index_concept WHERE type_object = ".$entity_type." ".$this->get_query_objects_restriction("num_object", "AND")." ORDER BY num_object, order_concept";
	}
	
	public function index_concept_get_concepts_property_from_entity($callables_data) {
	    global $thesaurus_concepts_autopostage;
	    
	    $entity_type = $callables_data[0]['parameters'][0];
// 	    $scheme_id = $callables_data[0]['parameters'][1];
	    $query = $this->get_query_index_concept($entity_type);
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        while ($row = pmb_mysql_fetch_object($result)){
	            foreach ($callables_data as $callable_data) {
	                $return_data = [];
	                $property = $callable_data['parameters'][2];
	                switch ($property) {
	                    case 'generic':
	                        if ($thesaurus_concepts_autopostage) {
	                            $concept_uri = onto_common_uri::get_uri($row->num_concept);
	                            $query = "SELECT ?broadpath {<".$concept_uri."> pmb:broadPath ?broadpath}";
	                            skos_datastore::query($query);
	                            if (skos_datastore::num_rows()) {
	                                $values = [];
	                                foreach (skos_datastore::get_result() as $skos_result) {
	                                    $ids_broders = explode('/', $skos_result->broadpath);
	                                    foreach ($ids_broders as $id_broader) {
	                                        if ($id_broader) {
	                                            $broader_label = index_concept::get_concept_from_id($id_broader, 'label');
	                                            if (!in_array($broader_label, $values)) {
	                                                $values[] = $broader_label;
	                                            }
	                                        }
	                                    }
	                                }
	                                if(!empty($values)) {
	                                    $this->add_callable_data_tab_insert($row->num_concept, $callable_data, $values);
	                                }
	                            }
	                        }
	                        break;
	                    default:
	                        $return_value = index_concept::get_concept_from_id($row->num_concept, $property);
	                        if (!empty($return_value)) {
	                            $return_data = [$return_value];
	                        }
	                        break;
	                }
	                if (!empty($return_data)) {
	                   $this->add_callable_data_tab_insert($row->num_object, $callable_data, $return_data);
	                }
	            }
	        }
	        pmb_mysql_free_result($result);
	    }
	}
	
	public function index_concept_get_concepts_labels_from_entity($callables_data) {
	    for($i=0; $i<count($callables_data); $i++) {
	        $callables_data[$i]['parameters'][2] = 'label';
	    }
	    $this->index_concept_get_concepts_property_from_entity($callables_data);
	}
	
	public function index_concept_get_concepts_altlabels_from_entity($callables_data) {
	    for($i=0; $i<count($callables_data); $i++) {
	        $callables_data[$i]['parameters'][2] = 'altlabel';
	    }
	    $this->index_concept_get_concepts_property_from_entity($callables_data);
	}
	
	public function index_concept_get_concepts_hiddenlabels_from_entity($callables_data) {
	    for($i=0; $i<count($callables_data); $i++) {
	        $callables_data[$i]['parameters'][2] = 'hiddenlabel';
	    }
	    $this->index_concept_get_concepts_property_from_entity($callables_data);
	}
	
	public function index_concept_get_generic_concepts_labels_from_entity($callables_data) {
	    for($i=0; $i<count($callables_data); $i++) {
	        $callables_data[$i]['parameters'][2] = 'generic';
	    }
	    $this->index_concept_get_concepts_property_from_entity($callables_data);
	}
	
// 	public function index_concept_get_specific_concepts_labels_from_entity($infos, $entity_type) {
// 	}
	
	protected function maj_callable($object_id, $data) {
// 	    if(method_exists($this, $data['class_name'].'_'.$data['method'])) {
// 	        $callback_parameters = array($data);
// 	        if (!empty($data['parameters'])) {
// 	            $callback_parameters = array_merge($callback_parameters, explode(',', $data['parameters']));
// 	        }
// 	        call_user_func_array(array($this, $data['class_name'].'_'.$data['method']), $callback_parameters);
// 	    } else {
	        $query = "SELECT ".$this->reference_key." FROM ".$this->reference_table." 
            ".$this->get_query_objects_restriction($this->reference_key)."
            ORDER BY ".$this->reference_key;
	        $result = pmb_mysql_query($query);
	        while ($row = pmb_mysql_fetch_object($result)) {
	            parent::maj_callable($row->{$this->reference_key}, $data);
	        }
// 	    }
	}
	
	protected function maj_optimized_callables($object_id, $callables_data) {
	    $formatted_callables_data = array();
	    foreach ($callables_data as $i=>$data) {
	        if($this->check_restrict_field($data['champ'])) {
    	        if(method_exists($this, $data['class_name'].'_'.$data['method'])) {
    	            if (!empty($data['parameters'])) {
    	                $data['parameters'] = explode(',', $data['parameters']);
    	            }
    	            $formatted_callables_data[$i] = $data;
    	        } else {
    	            $this->maj_callable($object_id, $data);
    	        }
	        }
	    }
	    if (!empty($formatted_callables_data)) {
	        call_user_func_array(array($this, $data['class_name'].'_'.$data['method']), ['callables_data' => $formatted_callables_data]);
	    }
	}
	
	protected function maj_callables($object_id, $datatype='all') {
	    global $base_path;
	    
	    $optimized_callables = $this->get_optimized_callables();
	    foreach ($optimized_callables as $callables_class_path=>$methods) {
	        if (!file_exists($base_path.'/'.$callables_class_path)) {
	            continue;
	        }
	        require_once($base_path.'/'.$callables_class_path);
	        foreach ($methods as $callables_data) {
	            $this->maj_optimized_callables($object_id, $callables_data);
	        }
	    }
	}
	
	protected function push_elements($tab_insert, $tab_field_insert){
		
	}
	
	protected function log_heavy_directory_size($log_label) {
	    $filesizes = 0;
	    $directory_files = opendir($this->directory_files);
	    while($file = readdir($directory_files)){
	        if($file != "." && $file !=".." && $file !="CVS" && $file !=".svn" && is_file($this->directory_files.$file) && strpos($file, $this->get_prefix_temporary_file()) !== false) {
	            $filesize = round(filesize($this->directory_files.$file) / 1024 / 1024, 2);
                $filesizes += $filesize;
	        }
	    }
	    
	    //Au-delà de 50Mo
	    if($filesizes > 50) {
	        PHP_log::register(PHP_log::prepare($log_label, 'indexation'), $filesizes." MB");
	    }
	}
	
	protected function import_bdd_sql_file($prefix) {
		if(file_exists($this->directory_files.$prefix.'_global_index.sql')) {
			$handle = fopen($this->directory_files.$prefix.'_global_index.sql', 'r');
			if ($handle) {
				while (!feof($handle)) {
					$query = "";
					while ( (substr($query, strlen($query)-1, 1) != "\n") && (!feof($handle)) ) {
						$query.= fgets($handle,4096);
					}
					$query = rtrim($query);
					if ($query != "") {
						pmb_mysql_query($query);
					}
				}
				/*On ferme le fichier*/
				fclose($handle);
				unlink($this->directory_files.$prefix.'_global_index.sql');
			}
		}
	}
	
	protected function maj_bdd_fields_global_index() {
		$this->mode = '';
		if(file_exists($this->directory_files.$this->fields_prefix.'_global_index.sql')) {
			unlink($this->directory_files.$this->fields_prefix.'_global_index.sql');
		}
		$directory_files = opendir($this->directory_files);
		while($file = readdir($directory_files)){
		    if($file != "." && $file !=".." && $file !="CVS" && $file !=".svn" && is_file($this->directory_files.$file) && strpos($file, $this->get_prefix_temporary_file()) !== false) {
				$handle = fopen($this->directory_files.$file, 'r');
				if ($handle) {
					$file_infos = str_replace($this->get_prefix_temporary_file()."_", '', $file);
					$file_infos = substr($file_infos, 0, strpos($file_infos, '.'));
					$exploded_infos = explode('_', $file_infos);
					$infos = array(
							'champ' => $exploded_infos[0],
							'ss_champ' => $exploded_infos[1],
							'pond' => (!empty($exploded_infos[2]) ? $exploded_infos[2] : 0)
					);
					$lang = '';
					if(!empty($exploded_infos[3])) {
						$lang .= $exploded_infos[3];
						if(!empty($exploded_infos[4])) {
							$lang .= '_'.$exploded_infos[4];
						}
					}
					while (!feof($handle)) {
						/*On lit la ligne courante*/
						$buffer = fgets($handle);
						/*On l'affiche*/
						$entity = encoding_normalize::json_decode($buffer, true);
						if(!empty($entity)) {
							$this->add_tab_field_insert($entity[0], $infos, $entity[1], stripslashes($entity[2]), $lang, $entity[3]);
						}
						
						if(count($this->tab_field_insert) > 5000) {
							$this->save_elements($this->tab_insert, $this->tab_field_insert);
							$this->tab_insert = array();
							$this->tab_field_insert = array();
						}
					}
					$this->save_elements($this->tab_insert, $this->tab_field_insert);
					$this->tab_insert = array();
					$this->tab_field_insert = array();
					/*On ferme le fichier*/
					fclose($handle);
				}
			}
		}
// 		$this->import_bdd_sql_file($this->fields_prefix);
	}
	
	protected function maj_bdd_words_global_index() {
		if(file_exists($this->directory_files.$this->words_prefix.'_global_index.sql')) {
			unlink($this->directory_files.$this->words_prefix.'_global_index.sql');
		}
		
		$directory_files = opendir($this->directory_files);
		while($file = readdir($directory_files)){
		    if($file != "." && $file !=".." && $file !="CVS" && $file !=".svn" && is_file($this->directory_files.$file) && strpos($file, $this->get_prefix_temporary_file()) !== false) {
				$handle = fopen($this->directory_files.$file, 'r');
				if ($handle) {
				    $file_infos = str_replace($this->get_prefix_temporary_file()."_", '', $file);
					$file_infos = substr($file_infos, 0, strpos($file_infos, '.'));
					$exploded_infos = explode('_', $file_infos);
					//Les champs dont le contenu contient la syntaxe "msg:" sont historiquement exclus de la table _mots/_words
					if(empty($this->exclude_insert_words_table[$exploded_infos[0]][$exploded_infos[1]])) {
    					$infos = array(
    							'champ' => $exploded_infos[0],
    							'ss_champ' => $exploded_infos[1],
    							'pond' => (!empty($exploded_infos[2]) ? $exploded_infos[2] : 0)
    					);
    					while (!feof($handle)) {
    						/*On lit la ligne courante*/
    						$buffer = fgets($handle);
    						/*On l'affiche*/
    						$entity = encoding_normalize::json_decode($buffer, true);
    						if(!empty($entity)) {
    							$this->add_data_tab_insert($entity[0], $infos, stripslashes($entity[2]), $entity[1]/*, $keep_empty=false*/);
    						}
    						
    						if(count($this->tab_insert) > 100000) {
    							$this->save_elements($this->tab_insert, $this->tab_field_insert);
    							$this->tab_insert = array();
    							$this->tab_field_insert = array();
    						}
    					}
    					$this->save_elements($this->tab_insert, $this->tab_field_insert);
    					$this->tab_insert = array();
    					$this->tab_field_insert = array();
					}
					/*On ferme le fichier*/
					fclose($handle);
					unlink($this->directory_files.$file);
				}
			}
		}
// 		$this->import_bdd_sql_file($this->words_prefix);
	}
	
	public function maj_bdd_from_files() {
	    $restaure_mode = $this->mode;
		$this->mode = '';
		
// 		$uniqId = PHP_log::prepare_time($this->get_label().' : maj_bdd_fields_global_index', 'indexation');
		$this->maj_bdd_fields_global_index();
// 		PHP_log::register($uniqId);
		
// 		$uniqId = PHP_log::prepare_time($this->get_label().' : maj_bdd_words_global_index', 'indexation');
		$this->maj_bdd_words_global_index();
// 		PHP_log::register($uniqId);

		$this->mode = $restaure_mode;
		$this->files_lines = 0;
	}
	
	public function get_label() {
		return '';
	}
	
	public function get_directory_files() {
		return $this->directory_files;
	}
	
	public static function set_objects_mode($objects_mode) {
	    static::$objects_mode = $objects_mode;
	}
	
	public function set_objects_ids($objects_ids) {
	    $this->objects_ids = [];
	    array_walk($objects_ids, "intval");
	    if (!empty($objects_ids)) {
	        $this->objects_ids = $objects_ids;
	        static::$objects_mode = 'ids';
	    }
	}
	
	public function set_objects_query($objects_query) {
	    $this->objects_query = $objects_query;
	}
}