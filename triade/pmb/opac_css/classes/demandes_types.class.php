<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_types.class.php,v 1.6 2021/01/14 09:17:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/liste_simple.class.php");
require_once($class_path."/workflow.class.php");
/*
 * Classe des types de demandes
 */
class demandes_types extends liste_simple{
	public $id = 0;
	public $allowed_actions = array();
	
	public function __construct($table,$col_id_name,$col_lib_name,$id_liste=0){
		$this->table = $table;
		$this->colonne_id_nom = $col_id_name;
		$this->colonne_lib_nom = $col_lib_name;
	
		$this->id_liste = $id_liste;
	
		if(!$this->id_liste){
			$this->lib_liste ='';
			$workflow = new workflow('ACTIONS');
			$this->allowed_actions = $workflow->getTypeList();
		} else {
			$req = "select $this->colonne_lib_nom as lib,allowed_actions from $this->table where $this->colonne_id_nom ='".$this->id_liste."'";
			$res = pmb_mysql_query($req);
			$list = pmb_mysql_fetch_object($res);
			$this->lib_liste = $list->lib;
			$this->allowed_actions = unserialize($list->allowed_actions);
			if(!is_array($this->allowed_actions) || !count($this->allowed_actions)){
				$workflow = new workflow('ACTIONS');
				$this->allowed_actions = $workflow->getTypeList();
			}
		}
		$this->setParametres();
	}
	
	
	public function setParametres(){
		$this->setMessages('demandes_ajout_type','demandes_modif_type','demandes_del_type','demandes_add_type','demandes_no_type_available','demandes_used_type');
		$this->setActions('admin.php?categ=demandes&sub=type','admin.php?categ=demandes&sub=type');
	}	
	
	public function hasElements(){
		$q = "select count(1) from demandes where type_demande = '".$this->id_liste."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	public static function get_qty() {
		$q = "select count(1) from demandes_type";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
}
?>