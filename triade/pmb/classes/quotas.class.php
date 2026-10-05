<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: quotas.class.php,v 1.62.2.2 2024/10/01 14:47:11 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de calcul des quotas
global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/marc_table.class.php");
require_once($class_path."/quota.class.php");

class quotas {
	
	protected $descriptor;
	protected $data;
	public static $instance;
	
	public function __construct($descriptor="") {
		global $lang;
		global $include_path;
		
		if ($descriptor=="") $this->descriptor=$include_path."/quotas/$lang.xml"; else $this->descriptor=$descriptor;
		$this->parse_file();
	}
	
	public function parse_file() {
		// 		global $_parsed_quotas_;
	    global $charset, $default_tmp_storage_engine;
		
		// Gestion de fichier subst
		$p_descriptor_subst=substr($this->descriptor,0,-4)."_subst.xml";
		if (file_exists($p_descriptor_subst)) {
			$this->descriptor=$p_descriptor_subst;
		}
		if(!isset($this->data)) {
			$this->data = array();
			//Parse le fichier dans un tableau
			$fp=fopen($this->descriptor,"r") or die(htmlentities("Can't find XML file ".$this->descriptor, ENT_QUOTES, $charset));
			$xml=fread($fp,filesize($this->descriptor));
			fclose($fp);
			$param=_parser_text_no_function_($xml, "PMBQUOTAS");
			
			if (!isset($param["TABLE"])) {
				$table="quotas";
			} else  {
				$table=$param["TABLE"];
			}
			$this->data['_table_'] = $table;
			
			//Récupération des éléments
			for ($i=0; $i<count($param["ELEMENTS"][0]["ELEMENT"]); $i++) {
				$p_elt=$param["ELEMENTS"][0]["ELEMENT"][$i];
				$elt=array();
				$elt["NAME"]=$p_elt["NAME"];
				$elt["ID"]=$p_elt["ID"];
				$elt["COMMENT"]=$p_elt["COMMENT"];
				$elt["LINKEDTO"]=$p_elt["LINKEDTO"][0]["value"];
				$elt["TABLELINKED"]=$p_elt["TABLELINKED"][0]["value"];
				$elt["TABLELINKED_BY"]=(isset($p_elt["TABLELINKED"][0]["BY"]) ? $p_elt["TABLELINKED"][0]["BY"] : '');
				$elt["LINKEDFIELD"]=$p_elt["LINKEDFIELD"][0]["value"];
				$elt["LINKEDID"]=$p_elt["LINKEDID"][0]["value"];
				$elt["LINKEDID_BY"]=(isset($p_elt["LINKEDID"][0]["BY"]) ? $p_elt["LINKEDID"][0]["BY"] : '');
				$elt["TABLE"]=$p_elt["TABLE"][0]["value"];
				if (isset($p_elt["TABLE"][0]["TYPE"]) && ($p_elt["TABLE"][0]["TYPE"]=="marc_list")) {
					$ml=new marc_list($elt["TABLE"]);
					reset($ml->table);
					$requete="create temporary table ".$elt["TABLE"]." (id varchar(255),libelle varchar(255)) ENGINE={$default_tmp_storage_engine} ";
					pmb_mysql_query($requete);
					foreach ($ml->table as $key => $val) {
						$requete="insert into ".$elt["TABLE"]." (id,libelle) values('".addslashes($key)."','".addslashes($val)."')";
						pmb_mysql_query($requete);
					}
					$elt["FIELD"]="id";
					$elt["LABEL"]="libelle";
				} else {
					$elt["FIELD"]=$p_elt["FIELD"][0]["value"];
					$elt["LABEL"]=$p_elt["LABEL"][0]["value"];
				}
				if(!defined($elt["NAME"])) {
					define($elt["NAME"],$elt["ID"]);
				}
				$this->data['_elements_'][]=$elt;
			}
			
			//Récupération des types
			for ($i=0; $i<count($param["TYPES"][0]["TYPE"]); $i++) {
				$p_typ=$param["TYPES"][0]["TYPE"][$i];
				$typ=array();
				$typ["NAME"]=$p_typ["NAME"];
				$typ["ID"]=$p_typ["ID"];
				$typ["COMMENT"]=$p_typ["COMMENT"];
				$typ["SHORT_COMMENT"]=$p_typ["SHORT_COMMENT"];
				$typ["COMMENTFORCELEND"]=(isset($p_typ["COMMENTFORCELEND"]) ? $p_typ["COMMENTFORCELEND"] : '');
				$typ["FILTER_ID"]=(isset($p_typ["FILTER_ID"]) ? $p_typ["FILTER_ID"] : '');
				$typ["SPECIALCLASS"]=(isset($p_typ["SPECIALCLASS"]) ? $p_typ["SPECIALCLASS"] : '');
				$typ["DEFAULT_VALUE_LABEL"]=(isset($p_typ["DEFAULT_VALUE_LABEL"]) ? $p_typ["DEFAULT_VALUE_LABEL"] : '');
				if(isset($p_typ["CONFLIT_MAX"])) {
					$typ["CONFLIT_MAX"]=($p_typ["CONFLIT_MAX"] == "no" ? false : true);
				} else {
					$typ["CONFLIT_MAX"]='';
				}
				if(isset($p_typ["CONFLIT_MIN"])) {
					$typ["CONFLIT_MIN"]=($p_typ["CONFLIT_MIN"] == "no" ? false : true);
				} else {
					$typ["CONFLIT_MIN"]='';
				}
				$typ["ELEMENTS_LABEL"]=(isset($p_typ["ELEMENTS_LABEL"]) ? $p_typ["ELEMENTS_LABEL"] : '');
				
				if(isset($p_typ["ENTITY"])) {
					$p_typ_entity=$p_typ["ENTITY"][0];
					$typ["ENTITY"]=$p_typ_entity["NAME"];
					if ($p_typ_entity["MAXQUOTA"]=="yes") $typ["MAX_QUOTA"]=true;
					
					$typ["COUNT_TABLE"]=$p_typ_entity["COUNTTABLE"][0]["value"];
					$typ["COUNT_FIELD"]=$p_typ_entity["COUNTFIELD"][0]["value"];
					$typ["COUNT_FILTER"]=$p_typ_entity["COUNTFILTER"][0]["value"];
					$typ["MAX_ERROR_MESSAGE"]=$p_typ_entity["MAX_ERROR_MESSAGE"][0]["value"];
					$typ["PARTIAL_ERROR_MESSAGE"]=$p_typ_entity["PARTIAL_ERROR_MESSAGE"][0]["value"];
					$typ["DEFAULT_ERROR_MESSAGE"]=$p_typ_entity["DEFAULT_ERROR_MESSAGE"][0]["value"];
				} else {
					$typ["ENTITY"]='';
					$typ["MAX_QUOTA"]='';
					
					$typ["COUNT_TABLE"]='';
					$typ["COUNT_FIELD"]='';
					$typ["COUNT_FILTER"]='';
					$typ["MAX_ERROR_MESSAGE"]='';
					$typ["PARTIAL_ERROR_MESSAGE"]='';
					$typ["DEFAULT_ERROR_MESSAGE"]='';
				}
				
				if (isset($p_typ["MAX"]) && ($p_typ["MAX"]=="yes")) $typ["MAX"]=true; else $typ["MAX"]=false;
				if (isset($p_typ["MIN"]) && ($p_typ["MIN"]=="yes")) $typ["MIN"]=true; else $typ["MIN"]=false;
				if (isset($p_typ["FORCELEND"]) && ($p_typ["FORCELEND"]=="yes")) $typ["FORCELEND"]=true; else $typ["FORCELEND"]=false;
				if (isset($p_typ["MOSTFAVOURABLE"]) && ($p_typ["MOSTFAVOURABLE"]=="yes")) $typ["MOST_FAVOURABLE"]=true; else $typ["MOST_FAVOURABLE"]=false;
				
				$quotas=array();
				$countfields=array();
				for ($j=0; $j<count($p_typ["QUOTAS"][0]["ON"]); $j++) {
					$quotas[]=$p_typ["QUOTAS"][0]["ON"][$j]["value"];
					if(isset($p_typ["QUOTAS"][0]["ON"][$j]["COUNTFIELDS"])) {
						$countfields[]=$p_typ["QUOTAS"][0]["ON"][$j]["COUNTFIELDS"];
					} else {
						$countfields[]='';
					}
				}
				$typ["QUOTAS"]=$quotas;
				$typ["COUNTFIELDS"]=$countfields;
				
				if(!defined($typ["NAME"])) {
				    define($typ["NAME"],$typ["ID"]);
				}
				$this->data['_types_'][]=$typ;
			}
		}
	}
	
	//Récupération d'un élément à partir de son nom
	public function get_element_by_name($element_name) {
		if (isset($this->data)) {
			reset($this->data['_elements_']);
			foreach ($this->data['_elements_'] as $key => $val) {
				if ($val["NAME"]==$element_name) {
					return $key;
				}
			}
			return -1;
		} else return -1;
	}
	
	//Récupération d'un élément à partir de son ID
	public function get_element_by_id($element_id) {
		if (isset($this->data)) {
			reset($this->data['_elements_']);
			foreach ($this->data['_elements_'] as $key => $val) {
				if ($val["ID"]==$element_id)
					return $key;
			}
			return -1;
		} else return -1;
	}
	
	//Récupération de l'ID d'un élément par son nom
	public function get_element_id_by_name($element_name) {
		if (isset($this->data)) {
			reset($this->data['_elements_']);
			foreach ($this->data['_elements_'] as $key => $val) {
				if ($val["NAME"]==$element_name)
					return $val["ID"];
			}
			return -1;
		} else return -1;
	}
	
	//Récupération de l'ID de plusieurs éléments par leur noms séparés par des virgules
	public function get_elements_id_by_names($elements) {
		$id=0;
		$elts=explode(",",$elements);
		for ($j=0; $j<count($elts); $j++) {
			$id|=$this->get_element_id_by_name($elts[$j]);
		}
		
		return $id;
	}
	
	//Récupération de la structure type de quota par son id
	public function get_quota_type_by_id($type_id) {
		$r=array();
		if (isset($this->data)) {
			for ($i=0; $i<count($this->data['_types_']); $i++) {
				if ($this->data['_types_'][$i]["ID"]==$type_id) {
					$r=$this->data['_types_'][$i];
					break;
				}
			}
		}
		return $r;
	}
	
	//Récupération de la structure type de quota par son id
	public function get_quota_type_by_name($type_id) {
		$r=array();
		if (isset($this->data)) {
			for ($i=0; $i<count($this->data['_types_']); $i++) {
				if ($this->data['_types_'][$i]["NAME"]==$type_id) {
					$r=$this->data['_types_'][$i];
					break;
				}
			}
		}
		return $r;
	}
	
	//Récupération du tableau des ids de chaque élément composant un id multiple
	public function get_table_ids_from_elements_id($id) {
		$r=array();
		for ($i=0; $i<count($this->data['_elements_']); $i++) {
			if (((int)$id&(int)$this->data['_elements_'][$i]["ID"])==(int)$this->data['_elements_'][$i]["ID"]) {
				$r[]=$this->data['_elements_'][$i]["ID"];
			}
		}
		return $r;
	}
	
	//Récupération du titre correspondant aux éléments du quota (par xxx et par xxx et ...)
	public function get_title_by_elements_id($id) {
		global $msg;
		
		$ids=$this->get_table_ids_from_elements_id($id);
		$r=array();
		for ($i=0; $i<count($ids); $i++) {
			$r[]=$msg["quotas_by"]." ".$this->data['_elements_'][$this->get_element_by_id($ids[$i])]["COMMENT"];
		}
		return implode(" ".$msg["quotas_and"]." ",$r);
	}
	
	public function get_data() {
		return $this->data;
	}
	
	public static function get_instance($descriptor="") {
		global $include_path, $lang;
		if ($descriptor=="") {
			$descriptor=$include_path."/quotas/$lang.xml";
		}
		if(!isset(static::$instance[$descriptor])) {
			static::$instance[$descriptor] = new quotas($descriptor);
		}
		return static::$instance[$descriptor];
	}
}

?>