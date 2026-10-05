<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation.class.php,v 1.79.2.3.2.1 2025/03/05 10:21:40 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/double_metaphone.class.php");
require_once($class_path."/stemming.class.php");
require_once($class_path."/authorities_collection.class.php");


//classe générique de calcul d'indexation...
class indexation {
	public static $xml_indexation =array();
	public $table_prefix ="";
	protected $type = 0;
	public $temp_not=array();
	public $temp_ext=array();
	public $temp_marc=array();
	public $champ_trouve=false;
	public $tab_code_champ = array();
	public $tab_languages=array();
	public $tab_keep_empty = array();
	public $tab_pp=array();
	public $tab_authperso=array();
	public $authperso_code_champ_start = 0;
	public $tab_authperso_link=array();
	public $authperso_link_code_champ_start = 0;
	public $isbd_ask_list=array();
	public $isbd_tab_req=array();
	protected $initialized = false;
	protected $queries = array();
	protected $queries_lang= array();
	protected $datatypes = array();
	protected $reference_key = "";
	protected $reference_table = "";
	protected static $marclist_languages;
	protected $marclist_instance;
	protected static $marclist_liste_mots;
	protected static $languages;
	protected static $languages_messages;
	protected static $num_words = array();
	protected $deleted_index = false;
	protected static $authpersos=array();
	protected static $parametres_perso=array();
	public $callables = array();
	protected static $authperso_notice = array();

	protected $tab_field_insert = array();
	protected $tab_insert = array();
	protected $steps_fields = array();
	protected $steps_fields_number = 0;
	protected $restrict_fields = array();

	protected $tab_fields = array();
	protected $tab_mots = array();
	protected $exclude_insert_words_table = array();

	public static $steps = [
	    'main',
	    'custom_field',
	    'authperso',
	    'authperso_link',
	    'isbd',
	    'callables'
	];
	public static $step = 'all';

	public function __construct($xml_filepath, $table_prefix, $type = 0){
		$this->table_prefix = $table_prefix;
		$this->type = $type;

		// On veut faire un optimisation de memoire
		authorities_collection::setOptimizer(authorities_collection::OPTIMIZE_MEMORY);

		//recuperation du fichier xml de configuration
		if(!isset(static::$xml_indexation[$this->type]) || !count(static::$xml_indexation[$this->type])) {
			if(!file_exists($xml_filepath)) return false;

			$subst_file = str_replace(".xml","_subst.xml",$xml_filepath);
			if(is_readable($subst_file)){
				$file = $subst_file;
			}else $file = $xml_filepath ;

			$fp=fopen($file,"r");
			if ($fp) {
				$xml=fread($fp,filesize($file));
			}
			fclose($fp);
			static::$xml_indexation[$this->type]=_parser_text_no_function_($xml,"INDEXATION",$file);
		}
	}

	public function get_type(){
		return $this->type;
	}

	public function set_type($type){
		$this->type = $type;
	}

	protected function init_properties() {
		$this->temp_not=array();
		$this->temp_ext=array();
		$this->temp_marc=array();
		$this->champ_trouve=false;
		$this->tab_code_champ = array();
		$this->tab_languages=array();
		$this->tab_keep_empty = array();
		$this->tab_pp=array();
		$this->tab_authperso_link=array();
		$this->authperso_link_code_champ_start = 0;
		$this->isbd_ask_list=array();
		$this->reference_key = static::$xml_indexation[$this->type]['REFERENCEKEY'][0]['value'];
		$this->reference_table = static::$xml_indexation[$this->type]['REFERENCE'][0]['value'];
		$this->callables = array();
	}

	protected function init_properties_from_xml_indexation() {
		for ($i=0;$i<count(static::$xml_indexation[$this->type]['FIELD']);$i++) { //pour chacun des champs decrits
			if(!isset(static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'])){
				$datatype = "undefined";
			} else {
				$datatype = static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'];
			}
			$this->datatypes[$datatype][] = static::$xml_indexation[$this->type]['FIELD'][$i]['ID'];
			if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['INDEX_ALSO_FROM'])) {
				$index_also_from = static::$xml_indexation[$this->type]['FIELD'][$i]['INDEX_ALSO_FROM'][0]['DATATYPE'];
				foreach ($index_also_from as $other_datatype) {
					$this->datatypes[$other_datatype['value']][] = static::$xml_indexation[$this->type]['FIELD'][$i]['ID'];
				}
			}
			//recuperation de la liste des informations a mettre a jour
			//conservation des mots vides
			if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['KEEPEMPTYWORD']) && static::$xml_indexation[$this->type]['FIELD'][$i]['KEEPEMPTYWORD'] == "yes"){
				$this->tab_keep_empty[]=static::$xml_indexation[$this->type]['FIELD'][$i]['ID'];
			}
			//champ perso
			if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE']) && static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'] == "custom_field"){
				$this->tab_pp[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']]=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['value'];
				//autorité perso
			}else if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE']) && static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'] == "authperso"){
				$this->tab_authperso[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']]=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['value'];
				$this->authperso_code_champ_start=static::$xml_indexation[$this->type]['FIELD'][$i]['ID'];
			}else if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE']) && static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'] == "authperso_link"){
				$this->tab_authperso_link[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']]=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['value'];
				$this->authperso_link_code_champ_start = static::$xml_indexation[$this->type]['FIELD'][$i]['ID'];
			}else if (isset(static::$xml_indexation[$this->type]['FIELD'][$i]['EXTERNAL']) && (static::$xml_indexation[$this->type]['FIELD'][$i]['EXTERNAL'] == "yes")) {
				//champ externe à la table
				//Stockage de la structure pour un accès plus facile
				$this->temp_ext[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']] = static::$xml_indexation[$this->type]['FIELD'][$i];
			} else if (isset(static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'])) {
				// Callables
				$this->callables[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']] = array();
				for ($j = 0; $j < count(static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE']); $j++) {
					$this->callables[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']][] = array(
							'champ' => static::$xml_indexation[$this->type]['FIELD'][$i]['ID'],
							'ss_champ' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['ID'],
							'pond' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['POND'],
							'class_path' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['CLASS_PATH'],
							'class_name' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['CLASS_NAME'],
							'method' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['METHOD'],
							'parameters' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['PARAMETERS']
					);
				}
			} else {
				//champ de la table
				$this->temp_not['f'][0][static::$xml_indexation[$this->type]['FIELD'][$i]['ID']] = static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value'];
				$this->tab_code_champ[0][static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']] = array(
						'champ' => static::$xml_indexation[$this->type]['FIELD'][$i]['ID'],
						'ss_champ' => 0,
						'pond' => static::$xml_indexation[$this->type]['FIELD'][$i]['POND'],
						'no_words' => (isset(static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE']) && static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'] == "marclist" ? true : false),
						'internal' => 1,
						'use_global_separator' => (isset(static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['USE_GLOBAL_SEPARATOR']) ?static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['USE_GLOBAL_SEPARATOR'] : '')
				);
				if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'])){
					$this->tab_code_champ[0][static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']]['marctype']=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
					$this->temp_not['f'][0][static::$xml_indexation[$this->type]['FIELD'][$i]['ID']."_marc"]=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']." as "."subst_for_marc_".static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
				}
				if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['TRANSLATION'])){
					$translation_field = explode('.', static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['TRANSLATION']);
					$this->tab_code_champ[0][static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']]['translation']=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['TRANSLATION'];
					$this->temp_not['f'][0][static::$xml_indexation[$this->type]['FIELD'][$i]['ID']."_translation"]=$translation_field[1]." as "."subst_for_translation_".$translation_field[1];
				}
				if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['IFEMPTY'])){
					$this->tab_code_champ[0][static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']]['if_empty']=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['IFEMPTY'];
					$this->temp_not['f'][0][static::$xml_indexation[$this->type]['FIELD'][$i]['ID']."_if_empty"]=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['IFEMPTY']." as "."subst_for_if_empty_".static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value'];
				}
			}
			if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['ISBD'])){ // isbd autorités
				$this->isbd_ask_list[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']]= array(
						'champ' => static::$xml_indexation[$this->type]['FIELD'][$i]['ID'],
						'ss_champ' => static::$xml_indexation[$this->type]['FIELD'][$i]['ISBD'][0]['ID'],
						'pond' => static::$xml_indexation[$this->type]['FIELD'][$i]['ISBD'][0]['POND'],
						'class_name' => static::$xml_indexation[$this->type]['FIELD'][$i]['ISBD'][0]['CLASS_NAME']
				);
			}
			$this->champ_trouve=true;
		}
	}

	protected function get_query_joins_external($table) {
		$jointure = '';
		if (!isset($table['LINK'])) {
			$table['LINK'] = [];
		}
		for( $j=0 ; $j<count($table['LINK']) ; $j++){
			$link = $table['LINK'][$j];
			if(isset($link["TABLE"][0]['ALIAS'])){
				$alias = $link["TABLE"][0]['ALIAS'];
			}else{
				$alias = (isset($link["TABLE"][0]['value']) ? $link["TABLE"][0]['value'] : '');
			}
			if(!isset($link["LINKRESTRICT"][0]['value'])) {
				$link["LINKRESTRICT"][0]['value'] = '';
			}
			switch ($link["TYPE"]) {
				case "n0" :
					if (isset($link["TABLEKEY"][0]['value'])) {
						$jointure .= " LEFT JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
						if(isset($link["EXTERNALTABLE"][0]['value'])){
							$jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
						}else{
							$jointure .= " ON " . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
						}
						$jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
					} else {
						$jointure .= " LEFT JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
						$jointure .= " ON " . $this->reference_table . "." . $this->reference_key;
						$jointure .= "=" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
					}
					break;
				case "n1" :
					if (isset($link["TABLEKEY"][0]['value'])) {
						$jointure .= " JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
						if(isset($link["EXTERNALTABLE"][0]['value'])){
							$jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
						}else{
							$jointure .= " ON " . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
						}
						$jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
					} else {
						$jointure .= " JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
						$jointure .= " ON " . $this->reference_table . "." . $this->reference_key;
						$jointure .= "=" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
					}
					break;
				case "1n" :
					$jointure .= " JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
					$jointure .= " ON (" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'];
					$jointure .= "=" . $this->reference_table . "." . $link["REFERENCEFIELD"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value']. ") ";


					break;
				case "nn" :
					$jointure .= " JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
					$jointure .= " ON (" . $this->reference_table . "." .  $this->reference_key;
					$jointure .= "=" . $alias . "." . $link["REFERENCEFIELD"][0]['value'] . ") ";
					if (isset($link["TABLEKEY"][0]['value'])) {
						$jointure .= " JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
						$jointure .= " ON (" . $alias . "." . $link["TABLEKEY"][0]['value'];
						$jointure .= "=" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'] ." ".$link["LINKRESTRICT"][0]['value']. ") ";
					} else {
						if(isset($link['LINK'][0])) {
							$current_link = $link;
							do {
								$jointure .= self::get_indexation_sub_join($current_link);
								$link = $current_link;
								$current_link = $current_link['LINK'][0];
							} while (isset($current_link['LINK'][0]) && $current_link['LINK'][0]);
							$jointure .= " JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
							$jointure .= " ON (" . (isset($link['LINK'][0]['TABLE'][0]['ALIAS']) ? $link['LINK'][0]['TABLE'][0]['ALIAS'] : $link['LINK'][0]['TABLE'][0]['value']) . "." . $link['LINK'][0]['EXTERNALFIELD'][0]['value'];
							$jointure .= "=" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'] . " ".(isset($link['LINK'][0]["LINKRESTRICT"][0]['value']) ? $link['LINK'][0]["LINKRESTRICT"][0]['value'] : '').") ";
						} else {
							$jointure .= " JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
							$jointure .= " ON (" . $alias . "." . $link["EXTERNALFIELD"][0]['value'];
							$jointure .= "=" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value'].") ";
						}
					}
					break;
			}
		}
		return $jointure;
	}

	protected function get_query_where_external($table) {
		$where=" where ".$this->reference_table.".".$this->reference_key."=!!object_id!!";
		if(isset($table['FILTER'])){
			foreach ( $table['FILTER'] as $filter ) {
				if($tmp=trim($filter["value"])){
					$where.=" AND (".$tmp.")";
				}
			}
		}
		return $where;
	}

	protected function get_query_order_by_external() {

	}

	protected function get_select_fields_external($table, $k, $v) {
		$select=array();
		if(isset($table['IDKEY'][0])){
			$select[]=( isset($table['IDKEY'][0]['ALIAS']) ? $table['IDKEY'][0]['ALIAS'] : ( isset($table['ALIAS']) ? $table['ALIAS'] : $table['NAME'] )).".".$table['IDKEY'][0]['value']." as subst_for_autorite_".$table['IDKEY'][0]['value'];
		}
		if(!empty($table['TABLEFIELD'])){
			for ($j=0;$j<count($table['TABLEFIELD']);$j++) {
				$select[]=((isset($table['ALIAS']) && (strpos($table['TABLEFIELD'][$j]["value"],".")=== false)) ? $table['ALIAS']."." : "").$table['TABLEFIELD'][$j]["value"];
				if(isset($table['LANGUAGE']) && isset($table['LANGUAGE'][0]['value'])){
					$select[]=$table['LANGUAGE'][0]['value'].(isset($table['LANGUAGE'][0]['ALIAS']) ? ' as '.$table['LANGUAGE'][0]['ALIAS'] : '');
					$this->tab_languages[$k]=(isset($table['LANGUAGE'][0]['ALIAS']) ? $table['LANGUAGE'][0]['ALIAS'] : $table['LANGUAGE'][0]['value']);
				}
				$field_name = $table['TABLEFIELD'][$j]["value"];
				if(strpos(strtolower($table['TABLEFIELD'][$j]["value"])," as ")!== false){ //Pour le cas où l'on a besoin de nommer un champ et d'utiliser un alias
					$field_name = substr($table['TABLEFIELD'][$j]["value"],strpos(strtolower($table['TABLEFIELD'][$j]["value"])," as ")+4);
				}elseif(strpos($table['TABLEFIELD'][$j]["value"],".")!== false){
					$field_name = substr($table['TABLEFIELD'][$j]["value"],strpos($table['TABLEFIELD'][$j]["value"],".")+1);
				}
				$field_name=trim($field_name);
				$this->tab_code_champ[$v['ID']][$field_name] = array(
						'champ' => $v['ID'],
						'ss_champ' => $table['TABLEFIELD'][$j]["ID"],
						'pond' => $table['TABLEFIELD'][$j]['POND'],
						'no_words' => (isset($v['DATATYPE']) && $v['DATATYPE'] == "marclist" ? true : false),
						'autorite' =>  (isset($table['IDKEY'][0]['value']) ? $table['IDKEY'][0]['value'] : '')
				);
				if(isset($table['TABLEFIELD'][$j]['MARCTYPE'])){
					$this->tab_code_champ[$v['ID']][$field_name]['marctype']=$table['TABLEFIELD'][$j]['MARCTYPE'];
					$select[]=(strpos($table['TABLEFIELD'][$j]["value"],".")=== false ? $table['NAME']."." : "").$table['TABLEFIELD'][$j]["value"]." as subst_for_marc_".$table['TABLEFIELD'][$j]['MARCTYPE'];
				}
				if(isset($table['TABLEFIELD'][$j]['TRANSLATION'])){
					$translation_field = explode('.', $table['TABLEFIELD'][$j]['TRANSLATION']);
					$this->tab_code_champ[$v['ID']][$field_name]['translation']=$table['TABLEFIELD'][$j]['TRANSLATION'];
					$select[]=$table['TABLEFIELD'][$j]['TRANSLATION']." as subst_for_translation_".$translation_field[1];
				}
				if(isset($table['TABLEFIELD'][$j]['IFEMPTY'])){
					$this->tab_code_champ[$v['ID']][$field_name]['if_empty']=$table['TABLEFIELD'][$j]['IFEMPTY'];
					$select[]=((isset($table['ALIAS']) && (strpos($table['TABLEFIELD'][$j]["IFEMPTY"],".")=== false)) ? $table['ALIAS']."." : "").$table['TABLEFIELD'][$j]["IFEMPTY"]." as subst_for_if_empty_".$field_name;
				}
			}
		}
		return $select;
	}

	protected function get_query_select_external($table, $k, $v) {
		$select = $this->get_select_fields_external($table, $k, $v);
		return "select ".implode(",",$select)." from ".$this->reference_table;
	}

	protected function get_query_select_isbd_external($id_aut) {
		return "select $id_aut as id_aut_for_isbd from ".$this->reference_table;
	}

	protected function init_external_field_table($table, $k, $v) {
		if(!empty($table['TABLEFIELD'])){
			$use_word=true;
		}else{
			$use_word=false;
		}
		$query = $this->get_query_select_external($table, $k, $v);
		$jointure = $this->get_query_joins_external($table);
		$where = $this->get_query_where_external($table);
		if(isset($table['GROUPBY'][0]['value'])){
            $group_by = " group by ".$table['GROUPBY'][0]['value'];
		} else {
		    $group_by = "";
		}

		if(isset($table['LANGUAGE']) && isset($table['LANGUAGE'][0]['value'])){
			$this->queries_lang[$k]= "select ".$table['LANGUAGE'][0]['value'].(isset($table['LANGUAGE'][0]['ALIAS']) ? ' as '.$table['LANGUAGE'][0]['ALIAS'] : '')." from ";
		}
		if(isset($table['LANGUAGE']) && isset($table['LANGUAGE'][0]['value'])){
		    $this->queries_lang[$k].=$jointure.$where.$group_by;
		}
		if($use_word){
		    $full_query = $query.$jointure.$where.$group_by;
			$this->queries[$k]["new_rqt"]['rqt'][]=$full_query;
		}
		if(isset($this->isbd_ask_list[$k])){ // isbd  => memo de la requete pour retrouver les id des autorités
			if(isset($table['ALIAS'])){
				$id_aut=$table['ALIAS'].".".$table["TABLEKEY"][0]['value'];
			} else {
				$id_aut=$table['NAME'].".".$table["TABLEKEY"][0]['value'];
			}
			$req=$this->get_query_select_isbd_external($id_aut).$jointure.$where.$group_by;
			$this->isbd_tab_req[]=$req;
		}
	}

	protected function get_tables_from_external_field_factory($table, $v) {
		switch ($v['DATATYPE']) {
			case 'aut_link':
				$indexation_aut_link = new indexation_aut_link($this->type);
				return $indexation_aut_link->get_tables($table['NAME']);
		}
	}

	protected function init_external_field_union_rqt($table, $k, $v) {
		$this->queries[$k]["rqt"] = implode(" union ",$this->queries[$k]["new_rqt"]['rqt']);
	}

	protected function init_external_field_union_isbd_tab_req($table, $k, $v) {
		$this->isbd_ask_list[$k]['req']=  implode(" union ",$this->isbd_tab_req);
	}

	protected function init_external_field($k, $v) {
		$this->isbd_tab_req=array();
		if(empty($v["TABLE"][0]['TABLEFIELD']) && !empty($v['TABLE'][0])) {
			$v["TABLE"] = $this->get_tables_from_external_field_factory($v['TABLE'][0], $v);
		}
		//on harmonise les fichiers XML décrivant des requetes...
		if(!empty($v["TABLE"]) && is_countable($v["TABLE"])) {
			for ($i = 0; $i<count($v["TABLE"]); $i++) {
				$table = $v['TABLE'][$i];
				if(!empty($table['TABLEFIELD'])){
					$use_word=true;
				}else{
					$use_word=false;
				}
				$this->init_external_field_table($table, $k, $v);
			}
			if($use_word){
				if(!empty($this->queries[$k]["new_rqt"]['rqt'])) {
					$this->init_external_field_union_rqt($table, $k, $v);
				}
			}
		}
		if(isset($this->isbd_ask_list[$k])){ // isbd  => memo de la requete pour retrouver les id des autorités
			$this->init_external_field_union_isbd_tab_req($table, $k, $v);
		}
	}

	protected function init(){
		$this->init_properties();
		$this->init_properties_from_xml_indexation();
		foreach($this->temp_ext as $k=>$v) {
			$this->init_external_field($k, $v);
		}
		$this->initialized = true;
	}

	protected function get_indexation_location() {
		global $pmb_indexation_location;
		//Indexation localisée ?
		return $pmb_indexation_location;
	}

	protected function get_indexation_lang() {
		//il existe une spécificité pour les notices (langue d'indexation) - classe dérivée
		return "";
	}

	protected function get_languages() {
		global $opac_show_languages;

		$languages = array();
		$query_languages = "select distinct user_lang from users";
		$result_languages = pmb_mysql_query($query_languages);
		if (pmb_mysql_num_rows($result_languages)) {
			while ($row_languages = pmb_mysql_fetch_object($result_languages)) {
				$languages[] = $row_languages->user_lang;
			}
			pmb_mysql_free_result($result_languages);
		}
		$query_languages = "select distinct empr_lang from empr";
		$result_languages = pmb_mysql_query($query_languages);
		if (pmb_mysql_num_rows($result_languages)) {
			while ($row_languages = pmb_mysql_fetch_object($result_languages)) {
				$languages[] = $row_languages->empr_lang;
			}
			pmb_mysql_free_result($result_languages);
		}
		$opac_languages = explode(' ', $opac_show_languages);
		if(isset($opac_languages[1])) {
			$exploded = explode(',', $opac_languages[1]);
			foreach ($exploded as $value) {
				if(trim($value)) {
					$languages[] = trim($value);
				}
			}
		}

		return array_values(array_unique($languages));
	}

	protected function get_marclist_languages() {
		global $include_path;

		$marclist_languages = array();
		$dir = opendir($include_path."/marc_tables");
		while($dir_lang = readdir($dir)){
			if($dir_lang!= "." && $dir_lang!=".." && $dir_lang!="CVS" && $dir_lang!=".svn" && is_dir($include_path."/marc_tables/".$dir_lang)){
				$marclist_languages[] = $dir_lang;
			}
		}
		return array_intersect($this->get_languages(), $marclist_languages);
	}

	protected function maj_query_marctype($k, $nom_champ, $liste_mots, $langage, $autorite) {
		global $lang;

		if(empty($this->tab_fields[$nom_champ])) {
			$this->tab_fields[$nom_champ] = array();
		}
		if(empty($this->tab_mots[$nom_champ])) {
			$this->tab_mots[$nom_champ] = array();
		}
		//on veut toutes les langues, pas seulement celle de l'interface...
		$saved_lang = $lang;
		$code = $liste_mots;
		if ($code) {
			if (!isset(static::$marclist_languages)) {
				static::$marclist_languages = $this->get_marclist_languages();
			}
			foreach (static::$marclist_languages as $marclist_language) {
				$lang = $marclist_language;
				if (!isset($this->marclist_instance[$lang][$this->tab_code_champ[$k][$nom_champ]['marctype']])) {
					$this->marclist_instance[$lang][$this->tab_code_champ[$k][$nom_champ]['marctype']] = new marc_list($this->tab_code_champ[$k][$nom_champ]['marctype']);
				}
				//Gestion des marclists spécifiques
				$table = $this->marclist_instance[$lang][$this->tab_code_champ[$k][$nom_champ]['marctype']];
				switch(true) {
					case isset($table->table[$code]):
						$table = $table->table[$code];
						break;
					case isset($table->table["descendant"][$code]):
						$table = $table->table["descendant"][$code];
						break;
					case isset($table->table["ascendant"][$code]):
						$table = $table->table["ascendant"][$code];
						break;
					default:
						$table = false;
						break;
				}
				if ($table) {
					$liste_mots = $table;
					$this->add_tab_fields($nom_champ, $liste_mots, $lang, $autorite);
					//Etait présent dans la méthode d'indexation de la classe notice
					if (static::class == 'indexation_record') {
						//gestion de la recherche tous champs pour les marclist
						if (!isset(static::$marclist_liste_mots[$liste_mots])) {
							$tab_tmp=array();
							$liste_mots = str_replace('<', ' <', $liste_mots);
							$liste_mots = strip_tags($liste_mots);

							if (!in_array($k,$this->tab_keep_empty)){
								$tab_tmp=explode(' ',strip_empty_words($liste_mots));
							} else {
								$tab_tmp=explode(' ',strip_empty_chars(clean_string($liste_mots)));
							}
							static::$marclist_liste_mots[$liste_mots] = $tab_tmp;
						}
						if ($this->tab_code_champ[$k][$nom_champ]['pond'] > 0) {
							foreach (static::$marclist_liste_mots[$liste_mots] as $mot) {
								if (trim($mot)) {
									$langageKey = $langage;
									if (!trim($langageKey)) {
										$langageKey = "empty";
									}
									$this->add_tab_mots($nom_champ, $mot, $langageKey);
								}
							}
						}
					}
				}
			}
		}
		$lang = $saved_lang;
		$saved_lang = null;
	}

	protected function maj_query_msg($nom_champ, $liste_mots) {
		global $charset, $include_path, $lang;

		if(empty($this->tab_fields[$nom_champ])) {
			$this->tab_fields[$nom_champ] = array();
		}
		$code = substr($liste_mots, 4);
		if (!isset(static::$languages)) {
			$langues = new XMLlist($include_path."/messages/languages.xml");
			$langues->analyser();

			static::$languages = array_intersect_key(array_flip($this->get_languages()), $langues->table);
			$langues = null;
		}
		foreach(static::$languages as $cle => $value){
			// arabe seulement si on est en utf-8
			if (($charset != 'utf-8' and $lang != 'ar') or ($charset == 'utf-8')) {
				if (!isset(static::$languages_messages[$cle])) {
					$messages_instance = new XMLlist($include_path."/messages/".$cle.".xml");
					$messages_instance->analyser();

					static::$languages_messages[$cle] = $messages_instance->table;
					$messages_instance = null;
				}

				$liste_mots = static::$languages_messages[$cle][$code];
				$this->add_tab_fields($nom_champ, $liste_mots, $cle);
			}
		}
	}

	protected function add_mots_query_text($nom_champ, $value, $langage, $keep_empty=false) {
		if($keep_empty) {
			$tab_tmp=explode(' ',strip_empty_chars(clean_string($value)));
		} else {
			$tab_tmp=explode(' ',strip_empty_words($value));
		}
		foreach ($tab_tmp as $mot) {
			if (trim($mot)) {
				$langageKey = $langage;
				if (!trim($langageKey)) {
					$langageKey = "empty";
				}
				$this->add_tab_mots($nom_champ, $mot, $langageKey);
			}
		}
		$tab_tmp = null;
	}

	protected function maj_query_text($k, $nom_champ, $liste_mots, $langage, $autorite) {
		global $charset;

		if(empty($this->tab_fields[$nom_champ])) {
			$this->tab_fields[$nom_champ] = array();
		}
		if(empty($this->tab_mots[$nom_champ])) {
			$this->tab_mots[$nom_champ] = array();
		}

		$liste_mots = str_replace('<', ' <', $liste_mots);
		$liste_mots = strip_tags($liste_mots);

		//Lorsque cela est entité en base (ex : Editeur HTML)
		$liste_mots = html_entity_decode($liste_mots, ENT_QUOTES, $charset);
		//	if($lang!="") $tab_tmp[]=$lang;
		if(!isset($this->tab_code_champ[$k][$nom_champ]['use_global_separator']) || !($this->tab_code_champ[$k][$nom_champ]['use_global_separator'])){
			$this->add_tab_fields($nom_champ, $liste_mots, $langage, $autorite);
		} else {
			$var_global_sep = $this->tab_code_champ[$k][$nom_champ]['use_global_separator'];
			global ${$var_global_sep};

			$tab_liste_mots = explode(${$var_global_sep},$liste_mots);
			if (count($tab_liste_mots)){
				foreach ($tab_liste_mots as $mot) {
					$this->add_tab_fields($nom_champ, $mot, $langage, $autorite);
				}
			}
			$tab_liste_mots = null;
		}
		if(!$this->tab_code_champ[$k][$nom_champ]['no_words']) {
			if (!in_array($k,$this->tab_keep_empty)){
				$keep_empty = false;
			}else{
				$keep_empty = true;
			}
			$this->add_mots_query_text($nom_champ, $liste_mots, $langage, $keep_empty);
		}
	}

	protected function maj_query($object_id, $k, $query) {
		$this->tab_mots=array();
		$this->tab_fields=array();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$last_object_id = 0;
			while ($tab_row=pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC)) {
				if(empty($object_id) && !empty($last_object_id) && $last_object_id != $tab_row["subst_for_indexation"]) {
					$this->add_queries_mots($last_object_id, $this->tab_code_champ[$k]);
					$this->add_queries_fields($last_object_id, $this->tab_code_champ[$k]);
					$this->tab_mots=array();
					$this->tab_fields=array();
				}
				$langage="";
				if (isset($this->tab_languages[$k]) && isset($tab_row[$this->tab_languages[$k]])) {

					switch ($tab_row[$this->tab_languages[$k]]) {
						case "fr" :
							$tab_row[$this->tab_languages[$k]] = 'fr_FR';
							break;
						case "en" :
							$tab_row[$this->tab_languages[$k]] = 'en_UK';
					}

					$langage = $tab_row[$this->tab_languages[$k]];
					$tab_row[$this->tab_languages[$k]] = null;
				}
				foreach($tab_row as $nom_champ => $liste_mots) {
					if (substr($nom_champ, 0, 10) == 'subst_for_') {
						continue;
					}
					if (empty($liste_mots) && !empty($this->tab_code_champ[$k][$nom_champ]['if_empty'])) {
						$liste_mots = $tab_row['subst_for_if_empty_'.$nom_champ];
					}
					if (isset($this->tab_code_champ[$k][$nom_champ]['internal']) && $this->tab_code_champ[$k][$nom_champ]['internal'] && $this->get_indexation_location()) {
						$langage=$this->get_indexation_lang();
					}
					if (isset($this->tab_code_champ[$k][$nom_champ]['marctype']) && $this->tab_code_champ[$k][$nom_champ]['marctype']) {
						//on veut toutes les langues, pas seulement celle de l'interface...
						$autorite = $tab_row["subst_for_marc_".$this->tab_code_champ[$k][$nom_champ]['marctype']];
						$this->maj_query_marctype($k, $nom_champ, $liste_mots, $langage, $autorite);
						$liste_mots = "";
					}
					if (substr($liste_mots, 0, 4) == "msg:") {
						//on veut toutes les langues, pas seulement celle de l'interface...
						$this->maj_query_msg($nom_champ, $liste_mots);
						$exclude_champ = $this->tab_code_champ[$k][$nom_champ];
						if (empty($this->exclude_insert_words_table[$exclude_champ['champ']][$exclude_champ['ss_champ']])) {
						    $this->exclude_insert_words_table[$exclude_champ['champ']][$exclude_champ['ss_champ']] = true;
						}
						$liste_mots = "";
					}
					if ($liste_mots != '') {
						if(isset($this->tab_code_champ[$k][$nom_champ]['autorite'])) {
							$autorite = $tab_row["subst_for_autorite_".$this->tab_code_champ[$k][$nom_champ]['autorite']] ?? 0;
						} else {
							$autorite = 0;
						}
						if (!empty($this->tab_code_champ[$k][$nom_champ]['translation'])) {
						    $this->_init_filtered_languages();
						}
						if (!empty($this->tab_code_champ[$k][$nom_champ]['translation']) && !empty(static::$languages)) {
							$translation = explode('.', $this->tab_code_champ[$k][$nom_champ]['translation']);
							$trans_table = $translation[0];
							$trans_table_key = $translation[1];
							$translation = null;
							foreach (static::$languages as $cle=>$value) {
// 								$translated_text = translation::get_translated_text($tab_row["subst_for_translation_".$trans_table_key], $trans_table, $nom_champ, $liste_mots, $cle);
								$translated_text = translation::get_translated_text($tab_row["subst_for_translation_".$trans_table_key], $trans_table, $nom_champ, '', $cle);
								if (empty($translated_text) && !empty($this->tab_code_champ[$k][$nom_champ]['if_empty'])) {
									$translated_text = translation::get_translated_text($tab_row["subst_for_translation_".$trans_table_key], $trans_table, $this->tab_code_champ[$k][$nom_champ]['if_empty'], '', $cle);
								}
								if (trim($translated_text)) {
									$this->maj_query_text($k, $nom_champ, trim($translated_text), $cle, $autorite);
								} else {
									$this->maj_query_text($k, $nom_champ, $liste_mots, $cle, $autorite);
								}
							}
						} else {
						    $this->maj_query_text($k, $nom_champ, $liste_mots, $langage, $autorite);
						}
					}
				}
				if(empty($object_id)) {
					$last_object_id = $tab_row["subst_for_indexation"];
				}
				$tab_row = null;
			}
			if(empty($object_id) && !empty($last_object_id)) {
				$this->add_queries_mots($last_object_id, $this->tab_code_champ[$k]);
				$this->add_queries_fields($last_object_id, $this->tab_code_champ[$k]);
				$this->tab_mots=array();
				$this->tab_fields=array();
			}
			pmb_mysql_free_result($result);
		}
	}

	protected function add_tab_mots($nom_champ, $mot, $lang) {
		$this->tab_mots[$nom_champ][$lang][] = $mot;
	}

	/**
	 *
	 * @param integer $object_id
	 * @param array $tab_mots
	 * @param array $infos
	 */
	protected function add_queries_mots($object_id, $infos) {
		foreach ($this->tab_mots as $nom_champ=>$tab) {
			$memo_ss_champ="";
			$order_fields=1;
			$pos=1;
			foreach ( $tab as $langage => $mots ) {
				if ($langage == "empty") {
					$langage = "";
				}
				foreach ($mots as $mot) {
					$num_word = indexation::add_word($mot, $langage);
					if($num_word != 0){
						$this->add_tab_insert($object_id, $infos[$nom_champ], $num_word, $order_fields, $pos);
						$pos++;
						if($infos[$nom_champ]['ss_champ']!= $memo_ss_champ) $order_fields++;
						$memo_ss_champ=$infos[$nom_champ]['ss_champ'];
					}
				}
			}
		}
	}

	protected function add_tab_fields($nom_champ, $liste_mots, $lang, $autorite='') {
		$liste_mots = trim($liste_mots);
		if($liste_mots) {
			$this->tab_fields[$nom_champ][] = array(
					'value' => $liste_mots,
					'lang' => $lang,
					'autorite' => $autorite
			);
		}
	}

	/**
	 * la table pour les recherche exacte
	 * @param integer $object_id
	 * @param array $tab_mots
	 * @param array $infos
	 */
	protected function add_queries_fields($object_id, $infos) {
		foreach ($this->tab_fields as $nom_champ=>$tab) {
			foreach($tab as $order => $values){
				$this->add_tab_field_insert($object_id, $infos[$nom_champ], $order+1, $values['value'], $values['lang'], $values['autorite']);
			}
		}
	}

	protected function check_restrict_field($field='') {
	    if (empty($this->restrict_fields) || $field === '' || ($field !== '' && in_array($field,$this->restrict_fields))) {
	        return true;
	    }
	    return false;
	}

	protected function check_datatype($datatype='all', $field='') {
		if ($datatype == 'all' || $datatype == 'scheduler' || (isset($this->datatypes[$datatype]) && !empty($field) && in_array($field,$this->datatypes[$datatype]))) {
			return true;
		}
		return false;
	}

	protected function add_direct_fields($object_id, $datatype='all') {
		//Recherche des champs directs
	    if($this->check_datatype($datatype) && isset($this->temp_not['f']) && count($this->temp_not['f'])) {
			$this->queries[0]["rqt"]= "select ".implode(',',$this->temp_not['f'][0])." from ".$this->reference_table;
			if($object_id) {
				$this->queries[0]["rqt"].=" where ".$this->reference_key."='".$object_id."'";
			}
			$this->queries[0]["table"]=$this->reference_table;
		}
	}

	protected function maj_query_get_builded_query($object_id, $query) {
	    if($object_id) {
	        return str_replace("!!object_id!!",$object_id, $query);
	    } else {
	        return $query;
	    }
	}

	protected function maj_queries($object_id, $datatype='all') {
		foreach($this->queries as $k=>$v) {
		    if ($this->check_datatype($datatype, $k) && $this->check_restrict_field($k)) {
				$query = $this->maj_query_get_builded_query($object_id, $v['rqt']);
				$this->maj_query($object_id, $k, $query);
				if($object_id) {
					$this->add_queries_mots($object_id, $this->tab_code_champ[$k]);
					$this->add_queries_fields($object_id, $this->tab_code_champ[$k]);
				}
			}
		}
	}

	protected function get_expl_ids_from_notice_id($object_id) {
		$ids = array();
		$query = "select expl_id from notices join exemplaires on expl_notice = notice_id and expl_notice!=0 where notice_id = ".$object_id." union select expl_id from notices join bulletins on num_notice = notice_id join exemplaires on expl_bulletin = bulletin_id and expl_bulletin != 0 where notice_id = ".$object_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while($row= pmb_mysql_fetch_object($result)){
				$ids[] =$row->expl_id;
			}
			pmb_mysql_free_result($result);
		}
		return $ids;
	}

	protected function get_explnum_ids_from_notice_id($object_id) {
		$ids = array();
		$query = "select explnum_id from notices join explnum on explnum_notice = notice_id and explnum_notice!=0 where notice_id = ".$object_id." union select explnum_id from notices join bulletins on num_notice = notice_id join explnum on explnum_bulletin = bulletin_id and explnum_bulletin != 0 where notice_id = ".$object_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while($row= pmb_mysql_fetch_object($result)){
				$ids[] =$row->explnum_id;
			}
			pmb_mysql_free_result($result);
		}
		return $ids;
	}

	protected function maj_custom_field($object_id, $table, $id, $code_champ) {
		global $charset;

		$p_perso = $this->get_parametres_perso_class($table);
		$data=$p_perso->get_fields_recherche_mot_array($id);
		$j=0;
		$order_fields=1;
		foreach ( $data as $code_ss_champ => $value ) {
			//la table pour les recherche exacte
			$infos = array(
					'champ' => $code_champ,
					'ss_champ' => $code_ss_champ,
					'pond' => $p_perso->get_pond($code_ss_champ)
			);
			$tab_values=array();
			foreach($value as $val) {
				//Elimination des balises HTML - Y compris celles mal formées
				$val = preg_replace('#<[^>]+>#','',$val);
				//Lorsque cela est entité en base (ex : Editeur HTML)
				$val = html_entity_decode($val, ENT_QUOTES, $charset);
				if($val != ''){
					$this->add_tab_field_insert($object_id, $infos, $j, $val);
					$j++;
					$tab_values[] = $val;
				}
			}
			if(!empty($tab_values)) {
				$this->add_custom_data_tab_insert($object_id, $infos, $tab_values, $order_fields);
			}
			$order_fields++;
		}
	}

	protected function maj_custom_fields($object_id, $datatype='all') {
		if(count($this->tab_pp) && $this->check_datatype($datatype, 'custom_field')) {
			foreach ( $this->tab_pp as $code_champ => $table ) {
			    if($this->check_restrict_field($code_champ)) {
    				//on doit retrouver l'id des eléments...
    				$ids = array();
    				switch($table){
    					case "expl" :
    						$ids = $this->get_expl_ids_from_notice_id($object_id);
    						break;
    					case "explnum" :
    						$ids = $this->get_explnum_ids_from_notice_id($object_id);
    						break;
    					default :
    						$ids = array($object_id);
    				}
    				if(count($ids)){
    					for($i=0 ; $i<count($ids) ; $i++) {
    						$this->maj_custom_field($object_id, $table, $ids[$i], $code_champ);
    					}
    				}
			    }
			}
		}
	}

	protected function add_data_tab_insert($object_id, $infos, $value, $order_fields, $keep_empty=false) {
		$tab_mots=array();
		$langage = '';
		$tab_mots[$langage]=array();
		if($keep_empty) {
			$tab_tmp=explode(' ',strip_empty_chars(clean_string($value)));
		} else {
			$tab_tmp=explode(' ',strip_empty_words($value));
		}
		foreach($tab_tmp as $mot) {
			if(trim($mot)){
			    $tab_mots[$langage][]= $mot;
			}
		}
		if($infos['pond'] > 0){
			$pos=1;
			foreach ( $tab_mots as $langage => $mots ) {
			    foreach ( $mots as $mot ) {
    				$num_word = indexation::add_word($mot, $langage);
    				if($num_word != 0){
    					$this->add_tab_insert($object_id, $infos, $num_word, $order_fields, $pos);
    					$pos++;
    				}
    			}
			}
		}
	}

	protected function add_custom_data_tab_insert($object_id, $infos, $values, $order_fields, $keep_empty=false) {
		$tab_mots=array();
		$langage = '';
		$tab_mots[$langage]=array();
		foreach ($values as $val) {
			if($keep_empty) {
				$tab_tmp=explode(' ',strip_empty_chars(clean_string($val)));
			} else {
				$tab_tmp=explode(' ',strip_empty_words($val));
			}
			foreach($tab_tmp as $mot) {
				if(trim($mot)){
				    $tab_mots[$langage][]= $mot;
				}
			}
		}
		$pos=1;
		foreach ( $tab_mots as $langage => $mots ) {
		    foreach ( $mots as $mot ) {
    			$num_word = indexation::add_word($mot, $langage);
    			$this->add_tab_insert($object_id, $infos, $num_word, $order_fields, $pos);
    			$pos++;
    		}
		}
	}

	protected function maj_authperso($object_id, $datatype='all') {
		global $charset;

		if(count($this->tab_authperso) && $this->check_datatype($datatype, 'authperso') && $this->check_restrict_field($this->authperso_code_champ_start)) {
			$order_fields=1;

			$authpersos = $this->get_authperso_notice($object_id);
			$index_fields=$authpersos->get_index_fields($object_id);
			foreach ( $index_fields as $code_champ => $auth ) {
				$code_champ+=$this->authperso_code_champ_start;
				foreach ($auth['ss_champ'] as $code_ss_champ=>$ss_field){
					$j=1;
					foreach ($ss_field as $val){
						//Elimination des balises HTML - Y compris celles mal formées
						$val = preg_replace('#<[^>]+>#','',$val);
						//Lorsque cela est entité en base (ex : Editeur HTML)
						$val = html_entity_decode($val, ENT_QUOTES, $charset);
						$infos = array(
								'champ' => $code_champ,
								'ss_champ' => $code_ss_champ,
								'pond' => (isset($auth['pond']) ? $auth['pond'] : 0)
						);
						$this->add_tab_field_insert($object_id, $infos, $j, $val);
						$j++;
						$this->add_data_tab_insert($object_id, $infos, $val, $order_fields);
						$order_fields++;
					}
				}
			}
		}
	}

	protected function get_authority_type() {
		switch ($this->reference_table){
			case 'authors':
				return AUT_TABLE_AUTHORS;
			case 'publishers':
				return AUT_TABLE_PUBLISHERS;
			case 'indexint':
				return AUT_TABLE_INDEXINT;
			case 'collections':
				return AUT_TABLE_COLLECTIONS;
			case 'sub_collections':
				return AUT_TABLE_SUB_COLLECTIONS;
			case 'series':
				return AUT_TABLE_SERIES;
			case 'noeuds':
				return AUT_TABLE_CATEG;
			case 'titres_uniformes':
				return AUT_TABLE_TITRES_UNIFORMES;
		}
		return 0;
	}

	protected function get_query_authperso_link($object_id) {
		$object_id = intval($object_id);
		$authority_type = $this->get_authority_type();
		return "
		SELECT id_authperso_authority, authperso_authority_authperso_num
		FROM ".$this->reference_table."
		JOIN aut_link ON (".$this->reference_table.".".$this->reference_key."=aut_link.aut_link_from_num and aut_link_from = ".$authority_type." or (".$this->reference_table.".".$this->reference_key." = aut_link_to_num and aut_link_to = ".$authority_type." ))
		JOIN authperso_authorities ON (aut_link.aut_link_to_num=authperso_authorities.id_authperso_authority or ( aut_link_from_num=authperso_authorities.id_authperso_authority ))
		WHERE ".$this->reference_table.".".$this->reference_key."=".$object_id." AND ((aut_link.aut_link_to > 1000))";
	}

	protected function get_index_fields_authperso($id_type_authperso, $id_authperso) {
		$index_fields = array();
		$authperso = $this->get_authperso_class($id_type_authperso);
		$infos_fields = $authperso->get_info_fields($id_authperso);
		$i = 0;
		foreach($infos_fields as $field){
			if($field['search'] ){
				$index_fields[$field['code_champ']]['pond']=$field['pond'];
				if($field['all_format_values']) {
					$index_fields[$field['code_champ']]['ss_champ'][$i][$field['code_ss_champ']].=$field['all_format_values'];
					$i++;
				}
			}
		}
		return $index_fields;
	}

	protected function maj_authperso_link($object_id, $datatype='all') {
	    if(count($this->tab_authperso_link) && $this->check_datatype($datatype, 'authperso_link') && $this->check_restrict_field($this->authperso_link_code_champ_start)){
			$query = $this->get_query_authperso_link($object_id);
			$result = pmb_mysql_query($query);
			while(($row=pmb_mysql_fetch_object($result))) {
				$index_fields = $this->get_index_fields_authperso($row->authperso_authority_authperso_num, $row->id_authperso_authority);
				foreach ( $index_fields as $code_champ => $auth ) {
					$order_fields=1;
					$code_champ+=$this->authperso_link_code_champ_start;
					foreach ($auth['ss_champ'] as $ss_field){
						$j=1;
						foreach ($ss_field as $code_ss_champ =>$val){
							$infos = array(
									'champ' => $code_champ,
									'ss_champ' => $code_ss_champ,
									'pond' => $auth['pond']
							);
							$this->add_tab_field_insert($object_id,$infos,$j,$val);
							$j++;
							$this->add_data_tab_insert($object_id, $infos, $val, $order_fields);
							$order_fields++;
						}
					}
				}
				$row = null;
			}
			pmb_mysql_free_result($result);
		}
	}

	protected function get_entity_isbd($class_name, $id) {
		switch ($class_name){
			case 'author':
				return entities::get_isbd($id, TYPE_AUTHOR);
			case 'editeur':
				return entities::get_isbd($id, TYPE_PUBLISHER);
			case 'indexint':
				return entities::get_isbd($id, TYPE_INDEXINT);
			case 'collection':
				return entities::get_isbd($id, TYPE_COLLECTION);
			case 'subcollection':
				return entities::get_isbd($id, TYPE_SUBCOLLECTION);
			case 'serie':
				return entities::get_isbd($id, TYPE_SERIE);
			case 'categories':
				return entities::get_isbd($id, TYPE_CATEGORY);
			case 'titre_uniforme':
				return entities::get_isbd($id, TYPE_TITRE_UNIFORME);
			case 'authperso':
				return entities::get_isbd($id, TYPE_AUTHPERSO);
		}
	}

	protected function add_isbd_ask($object_id, $isbd, $infos, $order_fields) {
		$this->add_tab_field_insert($object_id, $infos, $order_fields, $isbd);
		$this->add_data_tab_insert($object_id, $infos, $isbd, $order_fields);
	}

	protected function add_isbd_s_from_query($object_id, $infos, $query) {
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$order_fields = 1;
			while($row = pmb_mysql_fetch_object($result)){
				$entity_isbd = $this->get_entity_isbd($infos["class_name"], $row->id_aut_for_isbd);
				$this->add_isbd_ask($object_id, $entity_isbd, $infos, $order_fields);
				$order_fields++;
			}
		}
	}

	protected function maj_isbd_ask($object_id, $infos) {
		if($object_id) {
			$query = str_replace("!!object_id!!",$object_id,$infos["req"]);
		} else {
			$query = $infos["req"];
		}
		$this->add_isbd_s_from_query($object_id, $infos, $query);
	}

	protected function maj_isbd_ask_list($object_id, $datatype='all') {
		// Les isbd d'autorités
		foreach($this->isbd_ask_list as $k=>$infos){
			if($this->check_datatype($datatype, $k) && $this->check_restrict_field($k)) {
				$this->maj_isbd_ask($object_id, $infos);
			}
		}
	}

	protected function add_callable_data_tab_insert($object_id, $infos, $values) {
		$order_fields = 1;
		for($j=0 ; $j<count($values) ; $j++) {
			if (is_array($values[$j])) {
				foreach ($values[$j] as $callable_lang => $callable_value) {
				    $this->add_tab_field_insert($object_id, $infos, $order_fields, $callable_value, $callable_lang);

				    $this->add_data_tab_insert($object_id, $infos, $callable_value, $order_fields);
				}
			} else {
				$this->add_tab_field_insert($object_id, $infos, $order_fields, $values[$j]);

				$this->add_data_tab_insert($object_id, $infos, $values[$j], $order_fields);
			}
			$order_fields++;
		}
	}

	protected function maj_callable($object_id, $data) {
		$callback_parameters = array($object_id);
		if (!empty($data['parameters'])) {
			$callback_parameters = array_merge($callback_parameters, explode(',', $data['parameters']));
		}
		$callback_return = call_user_func_array(array($data['class_name'], $data['method']), $callback_parameters);

		$this->add_callable_data_tab_insert($object_id, $data, $callback_return);
	}

	protected function get_optimized_callables() {
	    $optimized_callables = [];
	    foreach ($this->callables as $callable_data) {
	        for ($i = 0; $i < count($callable_data); $i++) {
	            $class_path = $callable_data[$i]['class_path'];
	            $method = $callable_data[$i]['method'];
	            $optimized_callables[$class_path][$method][] = $callable_data[$i];
	        }
	    }
	    return $optimized_callables;
	}

	protected function maj_callables($object_id, $datatype='all') {
	    global $base_path;

	    foreach ($this->callables as $callable_data) {
	        for ($i = 0; $i < count($callable_data); $i++) {
	            if (!file_exists($base_path.'/'.$callable_data[$i]['class_path'])) {
	                continue;
	            }
	            if($this->check_restrict_field($callable_data[$i]['champ'])) {
    	            require_once($base_path.'/'.$callable_data[$i]['class_path']);
    	            $this->maj_callable($object_id, $callable_data[$i]);
	            }
	        }
	    }
	}

	public function maj($object_id,$datatype='all'){
		$object_id = intval($object_id);
		//on s'assure qu'on a lu le XML et initialisé ce qu'il faut...
		if(!$this->initialized) {
			$this->init();
		}

		//on a des éléments à indexer...
		if ($this->champ_trouve) {
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


			$this->maj_queries($object_id, $datatype);

			// Les champs perso
			$this->maj_custom_fields($object_id, $datatype);

			//Les autorités perso
			$this->maj_authperso($object_id, $datatype);

			// Les autorités perso liées
			$this->maj_authperso_link($object_id, $datatype);

			if(count($this->isbd_ask_list)){
				$this->maj_isbd_ask_list($object_id, $datatype);
			}
			if (count($this->callables)) {
				$this->maj_callables($object_id, $datatype);
			}
			$this->save_elements($this->tab_insert, $this->tab_field_insert);
		}
	}


	//compile les tableaux et lance les requetes
	protected function save_elements($tab_insert, $tab_field_insert){
		if($tab_insert && count($tab_insert)){
			$req_insert = "insert into ".$this->table_prefix."_words_global_index(".$this->reference_key.",code_champ,code_ss_champ,num_word,pond,position,field_position) values ".implode(',',$tab_insert)." ON DUPLICATE KEY UPDATE num_word = num_word";
			pmb_mysql_query($req_insert);
		}
		if($tab_field_insert && count($tab_field_insert)){
			//la table pour les recherche exacte
			$req_insert = "insert into ".$this->table_prefix."_fields_global_index(".$this->reference_key.",code_champ,code_ss_champ,ordre,value,lang,pond,authority_num) values ".implode(',',$tab_field_insert)." ON DUPLICATE KEY UPDATE value = value";
			pmb_mysql_query($req_insert);
		}
	}

	//vérifie l'utilisation d'un mot dans les tables d'index.
	public static function check_word_use($id_word){
		//TODO
		return true;
	}

	public static function calc_stem($word,$lang){
		$stemming = new stemming($word);
		return $stemming->stem;
	}

	public static function calc_double_metephone($word,$lang){
		$dmeta = new DoubleMetaPhone($word);
		if($dmeta->primary || $dmeta->secondary){
			return $dmeta->primary." ".$dmeta->secondary;
		}else{
			return "";
		}
	}

	public static function add_word($word,$lang){
		if (!$lang) {
			$word_langage = 'common';
		} else {
			$word_langage = $lang;
		}
		if (!isset(static::$num_words[$word_langage][$word])) {
			if(isset(static::$num_words[$word_langage]) && count(static::$num_words[$word_langage]) > 500) {
				// Parade pour éviter le dépassement de mémoire
				static::$num_words[$word_langage] = array();
			}

			$query = "select id_word from words where word = '".$word."' and lang = '".$lang."' LIMIT 1";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				static::$num_words[$word_langage][$word] = pmb_mysql_result($result, 0, 0);
				pmb_mysql_free_result($result);
			}else{
				$double_metaphone = indexation::calc_double_metephone($word, $lang);
				$stem = indexation::calc_stem($word, $lang);
				$element_to_update = "";
				if($double_metaphone){
					$element_to_update.="double_metaphone = '".$double_metaphone."'";
				}
				if($element_to_update) $element_to_update.=",";
				$element_to_update.="stem = '".$stem."'";

				$query = "insert into words set word = '".$word."', lang = '".$lang."'".($element_to_update ? ", ".$element_to_update : "");
				pmb_mysql_query($query);
				static::$num_words[$word_langage][$word] = pmb_mysql_insert_id();
			}
		}
		return static::$num_words[$word_langage][$word];
	}

	protected function delete_index($object_id,$datatype="all"){
		//qu'est-ce qu'on efface?
		if($this->check_datatype($datatype)) {
			$req_del="delete from ".$this->table_prefix."_words_global_index where ".$this->reference_key."='".$object_id."' ";
			pmb_mysql_query($req_del);
			//la table pour les recherche exacte
			$req_del="delete from ".$this->table_prefix."_fields_global_index where ".$this->reference_key."='".$object_id."' ";
			pmb_mysql_query($req_del);
		}else{
			foreach($this->datatypes as $xml_datatype=> $codes){
				if($xml_datatype == $datatype){
					foreach($codes as $code_champ){
						$req_del="delete from ".$this->table_prefix."_words_global_index where ".$this->reference_key."='".$object_id."' and code_champ='".$code_champ."'";
						pmb_mysql_query($req_del);
						//la table pour les recherche exacte
						$req_del="delete from ".$this->table_prefix."_fields_global_index where ".$this->reference_key."='".$object_id."' and code_champ='".$code_champ."'";
						pmb_mysql_query($req_del);
					}
				}
			}
		}
	}

	protected function get_tab_field_insert($object_id,$infos,$order_fields,$isbd, $lang = '', $autorite = 0) {
		return "(".$object_id.",".$infos["champ"].",".$infos["ss_champ"].",".$order_fields.",'".addslashes(trim($isbd))."','".addslashes(trim($lang))."',".$infos["pond"].",".(intval($autorite)).")";
	}

	protected function add_tab_field_insert($object_id,$infos,$order_fields,$isbd, $lang = '', $autorite = 0) {
		$this->tab_field_insert[] = $this->get_tab_field_insert($object_id, $infos, $order_fields, $isbd, $lang, $autorite);
	}

	protected function get_tab_insert($object_id, $infos, $num_word, $order_fields, $pos) {
		return "(".$object_id.", ".$infos["champ"].", ".$infos["ss_champ"].", ".$num_word.", ".$infos["pond"].", ".$order_fields.", ".$pos.")";
	}

	protected function add_tab_insert($object_id, $infos, $num_word, $order_fields, $pos) {
		$this->tab_insert[] = $this->get_tab_insert($object_id, $infos, $num_word, $order_fields, $pos);
	}

	public static function delete_all_index($object_id, $table_prefix, $reference_key, $type = ""){
		$req_del="delete from ".$table_prefix."_words_global_index where ".$reference_key."='".$object_id."' ";
		pmb_mysql_query($req_del);
		//la table pour les recherche exacte
		$req_del="delete from ".$table_prefix."_fields_global_index where ".$reference_key."='".$object_id."' ";
		pmb_mysql_query($req_del);
	}

	public function delete_objects_index($objects_ids=array(),$datatype="all"){
		//on s'assure qu'on a lu le XML et initialisé ce qu'il faut...
		if(!$this->initialized) {
			$this->init();
		}

		//qu'est-ce qu'on efface?
		if($this->check_datatype($datatype)) {
			$join_temporary_table = gen_where_in($this->reference_key, $objects_ids);
			$req_del="delete ".$this->table_prefix."_words_global_index from ".$this->table_prefix."_words_global_index ".$join_temporary_table;
			pmb_mysql_query($req_del);
			//la table pour les recherche exacte
			$req_del="delete ".$this->table_prefix."_fields_global_index from ".$this->table_prefix."_fields_global_index ".$join_temporary_table;
			pmb_mysql_query($req_del);
		}else{
			foreach($this->datatypes as $xml_datatype=> $codes){
				if($xml_datatype == $datatype){
					$join_temporary_table = gen_where_in($this->reference_key, $objects_ids);
					foreach($codes as $code_champ){
						$req_del="delete ".$this->table_prefix."_words_global_index from ".$this->table_prefix."_words_global_index ".$join_temporary_table." and code_champ='".$code_champ."'";
						pmb_mysql_query($req_del);
						//la table pour les recherche exacte
						$req_del="delete ".$this->table_prefix."_fields_global_index from ".$this->table_prefix."_fields_global_index ".$join_temporary_table." and code_champ='".$code_champ."'";
						pmb_mysql_query($req_del);
					}
				}
			}
		}
	}

	/**
	 * Initialisation depuis l'extérieur
	 */
	public function initialization(){
		if(!$this->initialized) {
			$this->init();
		}
	}

	public static function get_indexation_sub_join($link) {
		$jointure = "";
		if(isset($link["TABLE"][0]['ALIAS'])){
			$alias = $link["TABLE"][0]['ALIAS'];
		}else{
			$alias = $link["TABLE"][0]['value'];
		}
		$sub_link = $link['LINK'][0];
		if(isset($sub_link["TABLE"][0]['ALIAS'])){
			$sub_alias = $sub_link["TABLE"][0]['ALIAS'];
		}else{
			$sub_alias = $sub_link["TABLE"][0]['value'];
		}
		switch ($link["TYPE"]) {
			case "n0" :
				break;
			case "n1" :
				break;
			case "1n" :
				break;
			case "nn" :
				$jointure .= " JOIN " . $sub_link["TABLE"][0]['value'].($sub_link["TABLE"][0]['value'] != $sub_alias  ? " AS ".$sub_alias : "");
				$jointure .= " ON (" . $alias . "." .  $link['EXTERNALFIELD'][0]['value'];
				$jointure .= "=" . $sub_alias . "." . $sub_link["REFERENCEFIELD"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value']. ") ";
				break;
		}
		return $jointure;
	}

	public function set_deleted_index($deleted_index) {
		$this->deleted_index = $deleted_index;
	}

	public function get_queries() {
	    return $this->queries;
	}

	public function get_datatypes() {
	    return $this->datatypes;
	}

	public function get_field($code_champ) {
	    if (!empty(static::$xml_indexation[$this->type]['FIELD'])) {
	        foreach (static::$xml_indexation[$this->type]['FIELD'] as $field) {
	            if ($field['ID'] == $code_champ) {
	                return $field;
	            }
	        }
	    }
	    return [];
	}

	public function get_label_field($code_champ) {
	    global $msg;

	    $label = '';
	    $code_champ = intval($code_champ);
	    if (!empty($code_champ)) {
    	    $field = $this->get_field($code_champ);
    	    if (!empty($field)) {
        	    $prev_tmp = '';
        	    if(isset($field['TABLE'][0]['NAME'])){
        	        $prev_tmp = (isset($msg[$field['TABLE'][0]['NAME']]) ? $msg[$field['TABLE'][0]['NAME']] : $field['TABLE'][0]['NAME']);
        	    }
        	    if(isset($msg[$field['NAME']]) && $tmp = $msg[$field['NAME']]){
        	        $label .= $tmp;
        	    }else{
        	        $label .= $field['NAME'];
        	    }
        	    $label .= ($prev_tmp ? ' - '.$prev_tmp : '');
    	    }
	    } else {
	        $label .= "Champs principaux";
	    }
	    return $label;
	}

	protected function add_step_fields($step, $champ) {
	    $this->steps_fields[$step][] = ['champ' => $champ, 'label' => $this->get_label_field($champ)];
	    $this->steps_fields_number++;
	}

	protected function add_step_fields_datatype($step, $datatype) {
	    $fields = [];
	    if (!empty(static::$xml_indexation[$this->type]['FIELD'])) {
	        foreach (static::$xml_indexation[$this->type]['FIELD'] as $field) {
	            if (isset($field['DATATYPE']) && $field['DATATYPE'] == $datatype) {
	                $this->add_step_fields($step, $field['ID']);
	            }
	        }
	    }
	    return $fields;
	}

	public function get_steps_fields() {
	    if(!$this->initialized) {
	        $this->init();
	    }
	    //on a des éléments à indexer...
	    if ($this->champ_trouve) {
	        if(empty($this->steps_fields)) {
	            $this->steps_fields_number = 0;
	            foreach (static::$steps as $step) {
	                $this->steps_fields[$step] = array();
	                switch ($step) {
	                    case 'main':
	                        $this->add_step_fields($step, 0);
	                        foreach($this->queries as $i=>$v) {
	                            $this->add_step_fields($step, $i);
	                        };
	                        break;
	                    case 'custom_field':
	                    case 'authperso':
	                    case 'authperso_link':
	                        $this->add_step_fields_datatype($step, $step);
	                        break;
	                    case 'isbd':
	                        foreach($this->isbd_ask_list as $k=>$infos){
	                            $this->add_step_fields($step, $k);
	                        }
	                        break;
	                    case 'callables':
	                        $optimized_callables = $this->get_optimized_callables();
	                        foreach ($optimized_callables as $methods) {
	                            foreach ($methods as $callable_data) {
	                                for ($i = 0; $i < count($callable_data); $i++) {
	                                    $callable_data[$i]['label'] = $this->get_label_field($callable_data[$i]['champ']);

	                                }
	                                $this->steps_fields['callables'][] = $callable_data;
	                                $this->steps_fields_number++;
	                            }
	                        }
	                        break;
	                }

	            }
	        }
	    }
	    return $this->steps_fields;
	}

	public function get_steps_fields_number() {
	    if(empty($this->steps_fields_number)) {
	        $this->get_steps_fields();
	    }
	    return $this->steps_fields_number;
	}

	protected function get_authperso_class($id_type_authperso){
		if(!isset(self::$authpersos[$id_type_authperso])){
			if(isset(self::$authpersos) && count(self::$authpersos) > 500) {
				// Parade pour éviter le dépassement de mémoire
				self::$authpersos = array();
			}
			self::$authpersos[$id_type_authperso] = new authperso($id_type_authperso);
		}
		return self::$authpersos[$id_type_authperso];
	}

	protected function get_parametres_perso_class($type){
		if(!isset(self::$parametres_perso[$type])){
			if(isset(self::$parametres_perso) && count(self::$parametres_perso) > 500) {
				// Parade pour éviter le dépassement de mémoire
				self::$parametres_perso = array();
			}
			self::$parametres_perso[$type] = new parametres_perso($type);
		}
		return self::$parametres_perso[$type];
	}

	protected function get_authperso_notice($id){
		if(!isset(self::$authperso_notice[$id])){
			if(isset(self::$authperso_notice) && count(self::$authperso_notice) > 500) {
				// Parade pour éviter le dépassement de mémoire
				self::$authperso_notice = array();
			}
			self::$authperso_notice[$id] = new authperso_notice($id);
		}
		return self::$authperso_notice[$id];
	}

	protected function _init_filtered_languages() {
	    global $include_path;
	    if(!isset(static::$languages)) {
	        $langues = new XMLlist($include_path."/messages/languages.xml");
	        $langues->analyser();
	        static::$languages = array_intersect_key(array_flip($this->get_languages()), $langues->table);
	    }
	    return static::$languages;
	}
}