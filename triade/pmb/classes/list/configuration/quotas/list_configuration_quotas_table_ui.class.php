<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_quotas_table_ui.class.php,v 1.2 2022/11/03 15:29:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/configuration/quotas/list_configuration_quotas_ui.class.php");

class list_configuration_quotas_table_ui extends list_configuration_quotas_ui {
	
	protected static $elements_id;
	
	protected $ids;
	
	protected function _get_quota_element_($level) {
		$id = $this->get_ids()[$level];
		return quota::$_quotas_[static::$quota_instance->descriptor]['_elements_'][static::$quota_instance->get_element_by_id($id)];
	}
	protected function _get_query_level($level) {
		$_quota_element_=$this->_get_quota_element_($level);
		return "select distinct ".$_quota_element_["FIELD"].", ".$_quota_element_["LABEL"]." from ".$_quota_element_["TABLE"]." order by ".$_quota_element_["LABEL"];
		
	}
	
	protected function get_level_data($level) {
		$level_data = array();
		$_quota_element_=$this->_get_quota_element_($level);
		$query = $this->_get_query_level($level);
		$result = pmb_mysql_query($query);
		while ($r=pmb_mysql_fetch_array($result)) {
			$t=array();
			$t["ID"]=$r[$_quota_element_["FIELD"]];
			$t["LABEL"]=$r[$_quota_element_["LABEL"]];
			$level_data[]=$t;
		}
		return $level_data;
	}
	
	protected function fetch_level_data($level,$prefixe_label,$prefixe_varname,$ids,$elements, $object=null) {
		$elements_level = $elements[$ids[$level]];
		//Pour chaque élément
		foreach ($elements_level as $element_level) {
			if($level == 0) {
				$object = new stdClass();
			}
			//On récupère le label du champ concerné
			$prefixe_label_=$element_level["LABEL"];
			
			$prefixe_varname_=$prefixe_varname."_".$element_level["ID"];
			//Si on n'est pas au dernier niveau, on appel récursivement
			if ($level<(count($ids)-1)) {
				if(empty($object->parents)) {
					$object->parents = array();
				}
				$parent_object = new stdClass();
				$parent_object->id = $prefixe_varname_;
				$parent_object->label = $element_level["LABEL"];
				$object->parents[$level] = $parent_object;
				//Appel récursif
				$this->fetch_level_data($level+1,$prefixe_label_,$prefixe_varname_,$ids,$elements, $object);
			} else {
				$object->id = $prefixe_varname_;
				$object->label = $element_level["LABEL"];
				$this->add_object(clone $object);
			}
		}
	}
	
	protected function fetch_data() {
		$this->objects = array();
		
		$elements = array();
		$ids = $this->get_ids();
		for ($level=0; $level<count($ids); $level++) {
			$elements[$ids[$level]] = $this->get_level_data($level);
		}
		$this->fetch_level_data(0,"","",$ids,$elements);
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array();
		foreach ($this->get_ids() as $id) {
			$this->no_sortable_columns[] = "group_element_level_".$id;
		}
		$this->no_sortable_columns[] = "short_comment";
		$this->no_sortable_columns[] = "force";
	}
	
	public function init_applied_group($applied_group=array()) {
		$this->applied_group = array();
		$ids = $this->get_ids();
		for($i=0; $i<count($ids)-1; $i++) {
			$this->applied_group[$i] = "group_element_level_".$ids[$i];
		}
	}
	
	protected function get_main_fields_from_sub() {
		global $msg;
		global $force_lend;
		
		$main_fields = array();
		foreach ($this->get_ids() as $id) {
			$main_fields["group_element_level_".$id] = quota::$_quotas_[static::$quota_instance->descriptor]['_elements_'][static::$quota_instance->get_element_by_id($id)]["COMMENT"];
		}
		$main_fields['short_comment'] = static::$quota_instance->quota_type["SHORT_COMMENT"];
		$force="";
		if (static::$quota_instance->quota_type["FORCELEND"]) {
			if ($force_lend) {
				//Si le forçage est autorisé en règle générale, en particulier il faut proposer l'interdiction
				$force=$msg["quotas_dont_lend_element"];
			} else {
				//Si le forçage n'est pas autorisé en règle générale, il faut proposer l'autorisation de le faire
				$force=$msg["quotas_force_lend_element"];
			}
			$main_fields['force'] = $force;
		}
		return $main_fields;
	}
	
	protected function get_grouped_label($object, $property) {
		$grouped_label = '';
		if(strpos($property, "group_element_level") !== false) {
			$element_id = str_replace('group_element_level_', '', $property);
			$ids = $this->get_ids();
			$level = array_key_exists($element_id, $ids);
			if($level !== -1) {
				$grouped_label .= $object->parents[$level]->label;
			}
		} else {
			$grouped_label .= parent::get_grouped_label($object, $property);
		}
		return $grouped_label;
	}
	
	protected function get_jsscript() {
		global $min_value,$max_value,$max_quota;
		
		$jsscript="r=true; if (!check_nan(this)) r=false; ";
		if ((static::$quota_instance->quota_type["MAX"])&&($max_value)&&(!$max_quota)) {
			$jsscript.="if (!check_max('!!val!!',$max_value)) r=false; ";
		}
		if ((static::$quota_instance->quota_type["MIN"])&&($min_value)&&(!$max_quota)) {
			$jsscript.="if (!check_min('!!val!!',$min_value)) r=false; ";
		}
		if ($jsscript) $jsscript.=" if (!r) { this.value=''; this.focus(); return false;}";
		return $jsscript;
	}
	
	protected function get_cell_content($object, $property) {
		global $class_path;
		
		$content = '';
		if(!empty($this->applied_group) && array_search($property, $this->applied_group) !== false) {
			// on laisse la case vide
			$content = '';
		} elseif(strpos($property, "group_element_level") !== false) {
			//Si c'est le dernier niveau
			$content .= $object->label;
		} else {
			switch ($property) {
				case 'short_comment':
					$jsscript = $this->get_jsscript();
					if ($jsscript) {
						$jsscript_=str_replace("!!val!!","val".$object->id,$jsscript);
					} else {
						$jsscript_="";
					}
					//Recherche d'une valeur de quota déjà enregistrée
					$quota=$this->_get_object_property_short_comment($object);
					if(static::$quota_instance->quota_type['SPECIALCLASS']){
						require_once($class_path."/".static::$quota_instance->quota_type['SPECIALCLASS'].".class.php");
						$content .= call_user_func(array(static::$quota_instance->quota_type['SPECIALCLASS'],'get_quota_form'),"val".$object->id,$quota);
					}else{
						$content .= "<input type='text' class='saisie-5em' name='val".$object->id."' value='".$quota."' ";
						if ($jsscript_) $content .= "onChange=\"".$jsscript_."\"/>";
					}
					break;
				case 'force':
					$checked = ($this->_get_object_property_force($object) ? "checked='checked'" : "");
					$prefixe_varname_="_".$object->id;
					$content .= "<input type='checkbox' name='forc".$prefixe_varname_."' value='1' ".$checked."/>";
					break;
				default:
					$content .= parent::get_cell_content($object, $property);
					break;
			}
		}
		return $content;
	}
	
	protected function _get_object_property_short_comment($object) {
		$values_ids=explode("_",substr($object->id,1));
		//Recherche d'une valeur de quota déjà enregistrée
		return static::$quota_instance->search_for_element_value($values_ids,$this->get_ids());
	}
	
	protected function _get_object_property_force($object) {
		$values_ids=explode("_",substr($object->id,1));
		$forc=static::$quota_instance->search_for_element_forc($values_ids,$this->get_ids());
		if ($forc) {
			return true;
		} else {
			return false;
		}
	}
	
	protected function get_display_group_header_list($group_label, $level=1, $uid='') {
		$display = "
		<tr id='".$uid."_group_header'>
			<th class='list_ui_content_list_group list_ui_content_list_group_level_".$level." ".$this->objects_type."_content_list_group ".$this->objects_type."_content_list_group_level_".$level."' colspan='".count($this->columns)."'>
				".$this->get_cell_group_label($group_label, ($level-1))."
			</th>
		</tr>";
		return $display;
	}
	
	public function get_ids() {
		if(!isset($this->ids)) {
			$this->ids=static::$quota_instance->get_table_ids_from_elements_id_ordered(static::$elements_id);
		}
		return $this->ids;
	}
	
	public static function set_elements_id($elements_id) {
		static::$elements_id = intval($elements_id);
	}
}