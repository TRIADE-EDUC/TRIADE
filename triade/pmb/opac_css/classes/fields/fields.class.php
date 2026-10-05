<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fields.class.php,v 1.5.4.1 2025/01/28 15:28:30 rtigero Exp $

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

	protected $sub_type;
	protected $field_tableName;
	protected $field_keyName;
	protected static $pp;
	public $type;
	public $xml_indexation;

	public $current_engine;

	public function __construct($type='', $xml_indexation="", $sub_type = "") {
	    global $default_tmp_storage_engine;

	    $this->current_engine = $default_tmp_storage_engine;
	    $this->type = $type;
	    if ($sub_type) {
	        $this->sub_type = $sub_type;
	    }
		$this->xml_indexation=$xml_indexation;
		switch ($this->type) {
		    case 'authorities':
		        $this->field_tableName = 'authorities_fields_global_index';
		        $this->field_keyName = 'id_authority';
		        break;
		    case 'skos' :
		        $this->field_tableName = 'skos_fields_global_index';
		        $this->field_keyName = 'id_item';
		        break;
		    default:
		        $this->field_tableName = 'notices_fields_global_index';
		        $this->field_keyName = 'id_notice';
		        break;
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

	/**
	 * Ajoute une colonne à la table temporaire du nom et du type précisé
	 */
	protected function add_colum_temporary_table($nomTable, $nomCol,$type) {

		//d'abord on ajoute la colonne
		$cmd_table = "ALTER TABLE " . $nomTable . " ADD " . $nomCol . " ";

		//en fonction du type on met le type mysql
		switch($type) {
			case "num":
				$cmd_table .= "integer";
				break;
			case "date":
				$cmd_table .= "date";
				break;
			case "text":
			default:
				$cmd_table .= "text";
				break;
		}

		//execution de l'ajout de la colonne
		pmb_mysql_query($cmd_table);
	}

	protected function add_values_temporary_table($temporary_table_name, $field, $type, $datas=array(), $use_authority_id = false) {
	    global $default_tmp_storage_engine;

		$f=explode("_",$field);
		$query = "select distinct ".$this->field_keyName.", value from ".$this->field_tableName." where code_champ = ".$f[1];
		if($f[2]) {
			$query .= " and code_ss_champ = ".$f[2];
		}
		$query .= " and ".$this->field_keyName." IN (".implode(",",$datas).") order by value";

		//cas particulier des autorites indexees avec l'id d'autorite
		if ($this->field_tableName == "authorities_fields_global_index") {
		    $query = "
                SELECT DISTINCT num_object AS '".$this->field_keyName."', value
                FROM ".$this->field_tableName."
                JOIN authorities ON authorities.id_authority = ".$this->field_tableName.".".$this->field_keyName."
                WHERE code_champ = $f[1]  AND num_object IN ";
		    if ($use_authority_id) {
		        $query = "SELECT DISTINCT id_authority, value
                    FROM authorities_fields_global_index
                    WHERE code_champ = $f[1]  AND id_authority IN ";
		    }
		    $query .= "(".implode(",",$datas).")";
		    if($f[2]) {
		        $query .= " AND code_ss_champ = ".$f[2];
		    }
		    if ($this->sub_type && !$use_authority_id) {
		        $query .= " AND type_object = ".$this->sub_type;
		    }
		    $query .= " ORDER BY value";
		}
		//
		//On met le tout dans une table temporaire
		//
		pmb_mysql_query("DROP TEMPORARY TABLE IF EXISTS ".$temporary_table_name."_update");
		pmb_mysql_query("CREATE TEMPORARY TABLE ".$temporary_table_name."_update ENGINE={$default_tmp_storage_engine} (".$query.")");
		pmb_mysql_query("alter table ".$temporary_table_name."_update add index(".$this->field_keyName.")");

		//
		//Et on rempli la table tri_tempo avec les éléments de la table temporaire
		//
		$requete = "UPDATE ".$temporary_table_name.", ".$temporary_table_name."_update";
		$requete .= " SET " . $temporary_table_name.".".$field." = " . $temporary_table_name."_update.value";

		//le lien vers la table de tri temporaire
		$requete .= " WHERE " . $temporary_table_name.".".$this->field_keyName;
		$requete .= "=" . $temporary_table_name."_update.".$this->field_keyName;
// 		$requete .= " AND ".$this->params["REFERENCE"].".".$this->params["REFERENCEKEY"]."=".$temporary_table_name.".".$this->params["REFERENCEKEY"];
		$requete .= " AND ".$temporary_table_name."_update.value IS NOT NULL";
		$requete .= " AND ".$temporary_table_name."_update.value != ''";

		pmb_mysql_query($requete);
	}
}