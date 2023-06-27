<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_external.class.php,v 1.18 2019-05-16 12:54:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
		$id_critere += 0;
		$id_ss_critere += 0;
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
			if($ufield[1]) {
				$sub_queries[] = "ufield = '".$ufield[0]."' AND usubfield = '".$ufield[1]."'".$sub_query_values;
			} else {
				$sub_queries[] = "ufield = '".$ufield[0]."'".$sub_query_values;
			}
		}
		return $sub_queries;
	}
	
	protected function get_query_by_facette($id_critere, $id_ss_critere, $type = "notices") {
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
	
	public static function get_facette_wrapper(){
		$script = parent::get_facette_wrapper();
		$script .= "
		<script type='text/javascript'>
			function facettes_external_add_searchform(datas) {
				var input_form_values = document.createElement('input');
				input_form_values.setAttribute('type', 'hidden');
				input_form_values.setAttribute('name', 'check_facette[]');
				input_form_values.setAttribute('value', datas);
				document.forms['form_values'].appendChild(input_form_values);
			}	
			function valid_facettes_multi(){
				var facettes_checked = new Array();
				var flag = false;
				//on bloque si aucune case cochée
				var form = document.facettes_multi;
				for (i=0, n=form.elements.length; i<n; i++){
					if ((form.elements[i].checked == true)) {
						//copie le noeud vers form_values
						facettes_external_add_searchform(form.elements[i].value);
						flag = true;
					}
				}
				if(flag) {
					if(document.getElementById('filtre_compare_facette')) {
						document.getElementById('filtre_compare_facette').value='filter';
					}
					if(document.getElementById('filtre_compare_form_values')) {
						document.getElementById('filtre_compare_form_values').value='filter';
					}
					document.form_values.submit();
					return true;
				} else {
					return false;
				}
			}
			function facettes_external_valid_facette(datas){
				facettes_external_add_searchform(JSON.stringify(datas));
				document.form_values.submit();
				return true;
			}
			function facettes_external_reinit() {
				var input_form_values = document.createElement('input');
				input_form_values.setAttribute('type', 'hidden');
				input_form_values.setAttribute('name', 'reinit_facettes_external');
				input_form_values.setAttribute('value', '1');
				document.forms['form_values'].appendChild(input_form_values);
				document.form_values.submit();
				return true;
			}
			function facettes_external_delete_facette(indice) {
				var input_form_values = document.createElement('input');
				input_form_values.setAttribute('type', 'hidden');
				input_form_values.setAttribute('name', 'param_delete_facette');
				input_form_values.setAttribute('value', indice);
				document.forms['form_values'].appendChild(input_form_values);
				document.form_values.submit();
				return true;
			}
			function facettes_external_reinit_compare() {
				var input_form_values = document.createElement('input');
				input_form_values.setAttribute('type', 'hidden');
				input_form_values.setAttribute('name', 'reinit_compare');
				input_form_values.setAttribute('value', '1');
				document.forms['form_values'].appendChild(input_form_values);
				document.form_values.submit();
				return true;
			}
		</script>";
		return $script;
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
		if ($_SESSION['facettes_external']) {
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
	
	public static function destroy_global_search_element($indice) {
		global $search;
		
		$nb_search = count($search);
		for($i=$indice; $i<=$nb_search; $i++) {
			$op="op_".$i."_".$search[$i];
			$field_="field_".$i."_".$search[$i];
			$inter="inter_".$i."_".$search[$i];
			$fieldvar="fieldvar_".$i."_".$search[$i];
			global ${$op};
			global ${$field_};
			global ${$inter};
			global ${$fieldvar};
			if($i == $nb_search) {
				unset($GLOBALS[$op]);
				unset($GLOBALS[$field_]);
				unset($GLOBALS[$inter]);
				unset($GLOBALS[$fieldvar]);
 				unset($search[$i]);
 				array_pop($search);
			} else {
				//on décale
				$n = $i+1;
				$search[$i]=$search[$n];
				$op="op_".$n."_".$search[$n];
				$field_="field_".$n."_".$search[$n];
				$inter="inter_".$n."_".$search[$n];
				$fieldvar="fieldvar_".$n."_".$search[$n];
				global ${$op_next};
				global ${$field_next};
				global ${$inter_next};
				global ${$fieldvar_next};
					
				${$op}=${$op_next};
				${$field_}=${$field_next};
				${$inter}=${$inter_next};
				${$fieldvar}=${$fieldvar_next};
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
			if($search[$i] == 's_5') {
				static::destroy_global_search_element($i);
			}
		}
		if($with_session) unset($_SESSION['facettes_external']);
	}
	
	protected static function get_link_delete_clicked($indice, $facettes_nb_applied) {
		$id += 0;
		if ($facettes_nb_applied==1) {
			$link = "facettes_external_reinit();";
		} else {
			$link = "facettes_external_delete_facette(".$indice.");";
		}
		return $link;
	}
			
	protected static function get_link_not_clicked($name, $label, $code_champ, $code_ss_champ, $id, $nb_result) {
		$datas = array($name, $label, $code_champ, $code_ss_champ, $id, $nb_result);
		$link = "facettes_external_valid_facette(".encoding_normalize::json_encode($datas).");"; 
		return $link;
	}
	
	protected static function get_link_reinit_facettes() {
		$link = "facettes_external_reinit();";
		return $link;
	}
	
	protected static function get_link_back($reinit_compare=false) {
		global $base_path;
		if($reinit_compare) {
			$link = "facettes_external_reinit_compare();";
		} else {
			$link = "document.form_values.submit();";
		}
		return $link;
	}
	
	public static function get_session_values() {
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
		$id_critere += 0;
		$id_ss_critere += 0;
		$fields = static::$fields['notices_externes']['FIELD'];
		if(is_array($fields)) {
			foreach ($fields as $field) {
				if($field['ID'] == $id_critere) {
					if($field['DATATYPE'] == 'marclist') {
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
	
	public function get_query_expl($notices_ids) {
		return '';
	}
	
	public function get_query_explnum($notices_ids) {
		return '';
	}
}// end class
