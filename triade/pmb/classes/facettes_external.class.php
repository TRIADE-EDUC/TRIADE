<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_external.class.php,v 1.22.2.2 2025/01/21 16:22:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/facettes_root.class.php");

class facettes_external extends facettes_root {
	
	/**
	 * Nom de la table bdd
	 * @var string
	 */
	public static $table_name = 'facettes_external';
	
	/**
	 * Mode d'affichage (extended/external)
	 * @var string
	 */
	public $mode = 'external';
	
	/**
	 * Methode d'affinage (filter / search)
	 */
	public static $refining_method = 'search';
	
	/**
	 * Nom de la classe de comparaison
	 */
	protected static $compare_class_name = 'facettes_external_search_compare';
	
	public static $fields = array();
	
	protected static $marclist_instance = array();
	
	public function __construct($objects_ids = ''){
		parent::__construct($objects_ids);
	}
	
	//recuperation de champs_base.xml
	public static function parse_xml_file($type='notices_externes') {
		global $include_path;
		if(!isset(self::$fields[$type])) {
			$file = $include_path."/indexation/".$type."/champs_base_subst.xml";
			if(!file_exists($file)){
				$file = $include_path."/indexation/".$type."/champs_base.xml";
			}
			$fp=fopen($file,"r");
			if ($fp) {
				$xml=fread($fp,filesize($file));
			}
			fclose($fp);
			self::$fields[$type] = _parser_text_no_function_($xml,"INDEXATION",$file);
		}
	}
	
	public static function get_sub_queries($id_critere, $id_ss_critere, $values=array()) {
		$id_critere = intval($id_critere);
		$id_ss_critere = intval($id_ss_critere);
		$type='notices_externes';
		self::parse_xml_file($type);
		$unimarcFields = array();
		$fields = static::$fields[$type]['FIELD'];
		if(is_array($fields)) {
			foreach ($fields as $field) {
				if($field['ID'] == $id_critere) {
					if(isset($field['ISBD']) && (str_pad($field['ISBD'][0]['ID'], 2, "0", STR_PAD_LEFT) == $id_ss_critere)) {
						$unimarcFields = array(substr($field['ISBD'][0]['CLASS_NAME'], 0, 3).'$i'); 
					} elseif(count($field['TABLE'][0]['TABLEFIELD']) > 1) {
						foreach ($field['TABLE'][0]['TABLEFIELD'] as $tablefield) {
							if($tablefield['ID']+0 == $id_ss_critere) {
								$unimarcFields = explode(',', $tablefield['UNIMARCFIELD']);
							}
						}
					} else {
						$unimarcFields = explode(',', $field['TABLE'][0]['TABLEFIELD'][0]['UNIMARCFIELD']);
					}
					break;
				}
			}
		}
		$sub_query_values = '';
		if(is_array($values) && count($values)) {
			$sub_query_values .= ' AND (';
			foreach ($values as $i=>$value) {
				if ($i) {
					$sub_query_values .= ' OR ';
				}
				$sub_query_values .= 'value ="'.addslashes($value).'"';
			}
			$sub_query_values .= ') ';
		}
		$sub_queries = array();
		foreach ($unimarcFields as $unimarcField) {
			$ufield = explode('$', $unimarcField);
			if(!empty($ufield[1])) {
				$sub_queries[] = "ufield = '".$ufield[0]."' AND usubfield = '".$ufield[1]."'".$sub_query_values;
			} else {
				$sub_queries[] = "ufield = '".$ufield[0]."'".$sub_query_values;
			}
		}
		return $sub_queries;
	}
	
	protected function get_query() {
	    return "SELECT * FROM ".static::$table_name." WHERE facette_visible_gestion=1 ORDER BY facette_order, facette_name";
	}
	
	protected function get_query_by_facette($id_critere, $id_ss_critere) {
		$sub_queries = static::get_sub_queries($id_critere, $id_ss_critere);
		$selected_sources = static::get_selected_sources();
		$queries = array();
		foreach ($selected_sources as $source) {
			$queries [] = "SELECT value,recid FROM entrepot_source_".$source."
					WHERE recid IN (".$this->objects_ids.")
				AND ((".implode(') OR (', $sub_queries)."))";
		}
		$query = "select value ,count(distinct recid) as nb_result from ("
				.implode(' UNION ', $queries).") as sub
				GROUP BY value
				ORDER BY";
		return $query;
	}
	
	public static function make_facette_search_env() {
		global $search;

		//Destruction des globales avant reconstruction
		static::destroy_global_env(false); // false = sans destruction de la variable de session
		
		//creation des globales => parametres de recherche
		if(empty($search)) {
			$search = array();
		}
		$nb_search = count($search);
		if (!empty($_SESSION['facettes_external'])) {
			for ($i=0;$i<count($_SESSION['facettes_external']);$i++) {
				$search[] = "s_5";
				$field = "field_".($i+$nb_search)."_s_5";
				$field_=array();
				$field_ = $_SESSION['facettes_external'][$i];
				global ${$field};
				${$field} = $field_;
				
				$op = "op_".($i+$nb_search)."_s_5";
				$op_ = "EQ";
				global ${$op};
				${$op}=$op_;
	
				$inter = "inter_".($i+$nb_search)."_s_5";
				$inter_ = "and";
				global ${$inter};
				${$inter} = $inter_;
			}
		}
	}
	
	public static function destroy_global_env($with_session=true){
		global $search;
		if(is_array($search) && count($search)){
			$nb_search = count($search);
		}else{
			$nb_search = 0;
		}
		for ($i=$nb_search; $i>=0; $i--) {
		    if(!empty($search[$i]) && $search[$i] == 's_5') {
				static::destroy_global_search_element($i);
			}
		}
		if($with_session) {
		    unset($_SESSION['facettes_external']);
		}
	}
	
	protected static function get_link_delete_clicked($indice, $facettes_nb_applied) {
		if ($facettes_nb_applied==1) {
			$link = "facettes_external_reinit();";
		} else {
			$link = "facettes_delete_facette(".$indice.");";
		}
		return $link;
	}
			
	protected static function get_link_reinit_facettes() {
		$link = "facettes_external_reinit();";
		return $link;
	}
	
	protected static function get_link_back($reinit_compare=false) {
		if($reinit_compare) {
			$link = "facettes_reinit_compare();";
		} else {
			$link = "document.".static::$hidden_form_name.".submit();";
		}
		return $link;
	}
	
	public static function get_session_values() {
		if(!isset($_SESSION['facettes_external'])) {
		    $_SESSION['facettes_external'] = static::get_session_default_values;
		}
		return $_SESSION['facettes_external'];
	}
	
	public static function set_session_values($session_values) {
		$_SESSION['facettes_external'] = $session_values;
	}
	
	public static function delete_session_value($param_delete_facette) {
		global $search;
		
		if(isset($_SESSION['facettes_external'][$param_delete_facette])){
			$unset_indice = false;
			$facette_indice = 0;
			foreach ($search as $key=>$value) {
				if($value == 's_5') {
					if($param_delete_facette == $facette_indice) {
						$unset_indice = $key;
					}
					$facette_indice++;
				}
			}
			if($unset_indice !== false) {
				static::destroy_global_search_element($unset_indice);
			}
			unset($_SESSION['facettes_external'][$param_delete_facette]);
			$_SESSION['facettes_external'] = array_values($_SESSION['facettes_external']);
		}
	}
	
	public static function get_session_default_values() {
	    $facet_type = static::get_facet_type();
	    if(!isset($_SESSION['facettes_external_default'][$facet_type])) {
	        $_SESSION['facettes_external_default'][$facet_type] = [];
	    }
	    return $_SESSION['facettes_external_default'][$facet_type];
	}
	
	public static function set_session_default_values($session_default_values=[]) {
	    if (empty($session_default_values)) {
	        $session_default_values = static::get_session_values();
	    }
	    $_SESSION['facettes_external_default'][static::get_facet_type()] = $session_default_values;
	}
	
	public static function delete_session_default_values() {
        unset($_SESSION['facettes_external_default'][static::get_facet_type()]);
	}
	
	public static function get_filter_query_by_facette($id_critere, $id_ss_critere, $values) {
		$sub_queries = static::get_sub_queries($id_critere, $id_ss_critere, $values);
		$queries = array();
		if(is_array($_SESSION["checked_sources"])) {
			foreach ($_SESSION["checked_sources"] as $source) {
				$queries [] = "SELECT recid FROM entrepot_source_".$source."
						WHERE ((".implode(') OR (', $sub_queries)."))";
			}
		}
		$query = "select distinct recid as id_notice from ("
				.implode(' UNION ', $queries).") as sub";
		return $query;
	}
	
	public function get_facette_search_compare() {
		if(!isset($this->facette_search_compare)) {
			$this->facette_search_compare = new facettes_external_search_compare();
		}
		return $this->facette_search_compare;
	}
	
	public static function get_selected_sources() {
		$selected_sources = array();
		if(is_array($_SESSION["checked_sources"])) {
			$selected_sources = $_SESSION["checked_sources"];
		}
		return $selected_sources;
	}
	
	public static function get_formatted_value($id_critere, $id_ss_critere, $value) {
		$id_critere = intval($id_critere);
		$id_ss_critere = intval($id_ss_critere);
		$fields = static::$fields['notices_externes']['FIELD'];
		if(is_array($fields)) {
			foreach ($fields as $field) {
				if($field['ID'] == $id_critere) {
				    if(!empty($field['DATATYPE']) && $field['DATATYPE'] == 'marclist') {
						$marctype = $field['TABLE'][0]['TABLEFIELD'][$id_ss_critere]['MARCTYPE'];
						if($marctype) {
							if(!isset(self::$marclist_instance[$marctype])) {
								self::$marclist_instance[$marctype] = new marc_list($marctype);
							}
							$value = self::$marclist_instance[$marctype]->table[$value];
						}
					}
					break;
				}
			}
		}
		return get_msg_to_display($value);
	}
}// end class
