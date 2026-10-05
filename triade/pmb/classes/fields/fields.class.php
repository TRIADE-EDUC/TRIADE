<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fields.class.php,v 1.6.4.1 2025/01/28 15:28:30 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/onto/onto_index.class.php');
require_once($class_path.'/skos/skos_datastore.class.php');
require_once($class_path.'/skos/skos_onto.class.php');

class fields {

	/**
	 * Critères
	 * @var array
	 */
	public static $fields;

	protected $field_tableName;
	protected $field_keyName;

	protected $details;
	protected static $pp;
	public $type;
	public $xml_indexation;

	public $current_engine;

	public function __construct($type='', $xml_indexation="", $details = []) {
	    global $default_tmp_storage_engine;

	    $this->current_engine = $default_tmp_storage_engine;
    	$this->type = $type;
		$this->xml_indexation=$xml_indexation;
		$this->details = $details;
		if ($type == 'skos') {
            $this->get_concepts_fields();
		} else {
            $this->parse_xml_file($this->type, $this->xml_indexation);
		}
	}

	protected function get_concepts_fields() {
	    $onto_index = onto_index::get_instance('skos');
	    $onto_index->load_handler('', skos_onto::get_store(), array(), skos_datastore::get_store(), array(), array(), 'http://www.w3.org/2004/02/skos/core#prefLabel');

	    $tab_code_champ = $onto_index->get_tab_code_champ();
	    self::$fields[$this->type] =array('FIELD'=>array());
	    foreach($tab_code_champ as $k=>$v) {
	        $datatype = 'skos';
	        $datatype_label = '';
	        $label = '';
            $label_key = key($v);
            $tab_label_key = explode('_', $label_key);
            if(!empty($tab_label_key[0])) {
                $datatype_label = $onto_index->handler->get_label($tab_label_key[0]);
            }
            if(!empty($tab_label_key[1])) {
                $label = $onto_index->handler->get_label($tab_label_key[1]);
            }
            self::$fields[$this->type]['FIELD'][$k]=array('ID'=>$k, 'NAME'=>$label, 'DATATYPE'=>$datatype, 'DATATYPELABEL'=> $datatype_label);

	    }
	}

	//recuperation de champs_base.xml
	protected function parse_xml_file($type='', $xml_filepath='') {
		if(!isset(self::$fields[$type])) {
			$subst_file = str_replace(".xml","_subst.xml",$xml_filepath);
			if(file_exists($subst_file)){
				$file = $subst_file;
			}else $file = $xml_filepath ;
			$fp=fopen($file,"r");
			if ($fp) {
				$xml=fread($fp,filesize($file));
			}
			fclose($fp);

			self::$fields[$type] = _parser_text_no_function_($xml,"INDEXATION",$file);
			$tmp_fields = array();
			foreach (self::$fields[$type]["FIELD"] as $field) {
				if(self::$fields[$type]['REFERENCE'][0]["value"] == "authperso_authorities") {
					$field['ID'] = str_replace('!!id_authperso!!', $this->get_id_authperso(), $field['ID']);
				}
				$field['ID'] = intval($field['ID']);
				$tmp_fields[$field['ID']] = $field;
				if(isset($field['TABLE'][0]['TABLEFIELD']) && count($field['TABLE'][0]['TABLEFIELD']) > 1) {
					$tmp_fields[$field['ID']]['TABLE'][0]['TABLEFIELD'] = array();
					foreach ($field['TABLE'][0]['TABLEFIELD'] as $tablefield) {
						if(self::$fields[$type]['REFERENCE'][0]["value"] == "authperso_authorities") {
							$tablefield["ID"] = str_replace('!!id_authperso!!', $this->get_id_authperso(), $tablefield["ID"]);
						}
						$tablefield["ID"] = intval($tablefield["ID"]);
						$tmp_fields[$field['ID']]['TABLE'][0]['TABLEFIELD'][$tablefield["ID"]] = $tablefield;
					}
				}
				if(isset($field['DATATYPE'])) {
					switch ($field['DATATYPE']) {
						case 'custom_field':
							switch ($field["TABLE"][0]["value"]) {
								case "authperso" :
								    static::$pp[$field["TABLE"][0]["value"]] = new custom_parametres_perso("authperso", "authperso", $this->get_id_authperso());
									break;
								default:
									static::$pp[$field["TABLE"][0]["value"]] = new parametres_perso($field["TABLE"][0]["value"]);
									break;
							}
							break;
					}
				}
			}
			self::$fields[$type]["FIELD"] = $tmp_fields;
		}
	}

	public function grouped(){
		global $msg;

		$array_grouped = array();
		foreach (self::$fields[$this->type]['FIELD'] as $field) {
			$field['ID'] = intval($field['ID']);
		    if(!empty($msg[$field['NAME']]) && $tmp = $msg[$field['NAME']]){
				$lib = $tmp;
			}else{
				$lib = $field['NAME'];
			}
			if(!isset($field['DATATYPE'])) $field['DATATYPE'] = '';
			switch ($field['DATATYPE']) {
				case 'custom_field':
					$array_dyn_tmp = array();
					foreach (static::$pp[$field["TABLE"][0]["value"]]->t_fields as $id => $df) {
						$array_dyn_tmp[$id] = $df["TITRE"];
					}
					if(count($array_dyn_tmp)) {
						asort($array_dyn_tmp);
					}
					foreach ($array_dyn_tmp as $inc=>$lib) {
						$array_grouped[$field['NAME']][$field["TABLE"][0]["value"]."_".$field['ID']."_".$inc] = $lib;
					}
					break;
				case 'skos' :
				    $array_grouped[$field['DATATYPELABEL']]["f_".$field['ID']."_1"] = $lib;
				    break;

				default:
					if(isset($field['TABLE'][0]['TABLEFIELD']) && count($field['TABLE'][0]['TABLEFIELD']) > 1) {
						foreach ($field['TABLE'][0]['TABLEFIELD'] as $tablefield) {
							$tablefield["ID"] = intval($tablefield["ID"]);
							if(isset($tablefield['NAME'])) {
							    if(isset($msg[$tablefield['NAME']]) && $tmp= $msg[$tablefield['NAME']]){
									$lib = $tmp;
								}else{
									$lib = $tablefield['NAME'];
								}
								$array_grouped[$field['NAME']]["f_".$field['ID']."_".$tablefield['ID']] =  $lib;
							}
						}
					} else {
						$array_grouped['default']["f_".$field['ID']."_0"] = $lib;
					}
					break;
			}
		}
		return $array_grouped;

	}

	//liste des critères
	public function get_selector($selector_id='', $optional_opt=''){
		global $msg, $charset;
		global $pmb_extended_search_auto;

		$url = '';

		$fields_grouped = $this->grouped();
		if ($pmb_extended_search_auto) $select="<select name='add_field' id='".$selector_id."' onChange=\"if (this.form.add_field.value!='') { this.form.action='$url'; this.form.target=''; this.form.submit();} else { alert('".htmlentities($msg["multi_select_champ"],ENT_QUOTES,$charset)."'); }\" >\n";
		else $select="<select name='add_field' id='".$selector_id."'>\n";
		$select .= "<option value='' style='color:#000000'>".htmlentities($msg["multi_select_champ"],ENT_QUOTES,$charset)."</option>\n";
		foreach ($fields_grouped as $name => $group) {
			if($name == 'default') {
				$select .= "<optgroup label='".htmlentities($msg["champs_principaux_query"],ENT_QUOTES,$charset)."' class='erreur'>\n";
			} else {
			    $select .= "<optgroup label='".htmlentities(((isset($msg[$name]))?$msg[$name]:$name),ENT_QUOTES,$charset)."' class='erreur'>\n";
			}
			foreach ($group as $id => $value) {
				$select.="<option value=".$id." style='color:#000000'>".$value."</option>";
			}
			$select .= "</optgroup>";
		}
		$select.= $optional_opt;
		$select.="</select>";
		return $select;
	}

	public function add_field($field) {
		global $fields;
		$fields[] = $field;
	}

	protected function get_global_value($name) {
		global ${$name};
		return ${$name};
	}

	protected function set_global_value($name, $value='') {
		global ${$name};
		${$name} = $value;
	}

	protected function get_id_authperso() {
        return 0;
	}

	protected function gen_temporary_table($table_name, $main='', $with_pert=false) {
		$query="create temporary table ".$table_name." ENGINE=".$this->current_engine." ".$main;
		pmb_mysql_query($query);
		$query="alter table ".$table_name." add idiot int(1)";
		@pmb_mysql_query($query);
		$query="alter table ".$table_name." add unique(".$this->field_keyName.")";
		@pmb_mysql_query($query);
		if($with_pert) {
			$query="alter table ".$table_name." add pert decimal(16,1) default 1";
			@pmb_mysql_query($query);
		}
	}

	protected function upgrade_columns_temporary_table($table_name, $field_keyName, $with_pert=false) {
		if (!pmb_mysql_num_rows(pmb_mysql_query("show columns from ".$table_name." like 'idiot'"))) {
			$query="alter table ".$table_name." add idiot int(1)";
			pmb_mysql_query($query);
		}
		$query="alter table ".$table_name." add unique($field_keyName)";
		pmb_mysql_query($query);
		if($with_pert) {
			if (!pmb_mysql_num_rows(pmb_mysql_query("show columns from ".$table_name." like 'pert'"))) {
				$query="alter table ".$table_name." add pert decimal(16,1) default 1";
				pmb_mysql_query($query);
			}
		}
	}

	public function get_details() {
	    return $this->details;
	}

	public function set_details($details) {
	    $this->details = $details;
	}
}