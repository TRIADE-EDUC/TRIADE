<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: audit.class.php,v 1.30 2021/06/07 09:52:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path . '/contribution_area/contribution_area_forms_controller.class.php');

if ( ! defined( 'AUDIT_CLASS' ) ) {
  define( 'AUDIT_CLASS', 1 );
  
class audit {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	/*
	CREATE TABLE audit (
		type_obj int(1) NOT NULL default '0',
		object_id int(10) unsigned NOT NULL default '0',
		user_id int(8) unsigned NOT NULL default '0',
		user_name varchar(20) NOT NULL default '',
		type_modif int(1) NOT NULL default '1',
		quand timestamp(14) NOT NULL
		) 
	*/
	// ---------------------------------------------------------------
	
	public $type_obj;		// Types d'objets audités : définis dans config.inc.php
						// define('AUDIT_NOTICE'	,    1);
						// define('AUDIT_EXPL'		,    2);
						// define('AUDIT_BULLETIN'	,    3);
						// define('AUDIT_ACQUIS'	,    4);
						// define('AUDIT_PRET'		,    5);
						// define('AUDIT_AUTHOR'	,    6);
						// define('AUDIT_COLLECTION',   7);
						// define('AUDIT_SUB_COLLECTION',8);
						// define('AUDIT_INDEXINT'	,    9);
						// define('AUDIT_PUBLISHER',    10);
						// define('AUDIT_SERIE'	,    11);
						// define('AUDIT_CATEG'	,    12);
						// define('AUDIT_TITRE_UNIFORME',13);
						// define('AUDIT_DEMANDE'	,    14);
						// define('AUDIT_ACTION'	,    15);
						// define('AUDIT_NOTE',16);
						// define('AUDIT_EDITORIAL_ARTICLE',20);
						// define('AUDIT_EDITORIAL_SECTION',21);
	public $object_id;		// id de l'objet audité
	public $user_id;		// id de l'utilisateur lors de l'insertion dans la table
	public $user_name;		// login de l'utilisateur lors de l'insertion dans la table, permet de conserver un truc même après suppression de l'utilisateur
	public $type_modif;	// type de modification : 1 : INSERTION, 2 : MODIFICATION, 3 : MIGRATION
	public $quand;			// timestamp lors de l'insertion dans la table
	public $all_audit;		// tableau de toutes les lignes d'audit de l'objet
	public $info;			// info complémentaire à mémoriser
	/*
	Variables globales nécéssaires 
		$dbh			Accès à la base de données MySQL de PMB
		$PMBuserid		id de l'utilisateur PMB
		$PMBusername	login de l'utilisateur PMB
		$pmb_type_audit	paramètres de PMB sur l'audit : 0 : aucun, 1 création et dernière modif, 2 : création et toutes modifs 
		
	Variables passées aux différentes méthodes selon les besoins
		type_obj
		object_id
		type_modif	 
				
	Méthodes : 
		audit			constructeur : ne fait rien 
							reçoit en paramètres : type_obj et object_id
		get_all			retourne un tableau contenant les infos d'audit de l'objet en fonction de pmb_type_audit
		get_creation	retourne un tableau contenant les infos de création de l'objet 
		get_last		retourne un tableau contenant les infos de la dernière modif de l'objet
		
		insert_creation	insert la ligne d'audit de la création de l'objet
							reçoit en paramètres : type_obj et object_id
		insert_modif	insert une ligne d'audit de modification de l'objet
							reçoit en paramètres : type_obj et object_id
							
		delete_audit	delete toutes les lignes d'audit de l'objet
							reçoit en paramètres : type_obj et object_id
		
	*/	
	// ---------------------------------------------------------------
	//		audit($type, $obj) : constructeur
	// ---------------------------------------------------------------
	public function __construct($type=0, $obj=0) {
		global $pmb_type_audit ; 
		if (!$pmb_type_audit) return 0;
		$this->type_obj = intval($type);
		$this->object_id = intval($obj);
		$this->all_audit=array() ;
		$this->info=array() ;
		$this->info['fields']=array(); 
	}
	
	// ---------------------------------------------------------------
	//		get_all () : récupération toutes informations
	// ---------------------------------------------------------------
	public function get_all() {
		global $pmb_type_audit, $msg ;
		if (!$pmb_type_audit) return 0;
		 $query = "select user_id, user_name, type_user, type_modif, quand, date_format(quand, '".$msg["format_date_heure"]."') as aff_quand, if(type_user=1, concat(empr_prenom, ' ', empr_nom), concat(prenom, ' ', nom )) as prenom_nom ,info from audit
                left join users on user_id=userid and type_user=0
                left join empr on user_id = id_empr and type_user = 1 where ";
        $query .= "type_obj='$this->type_obj' AND ";
        $query .= "object_id='$this->object_id' ";
        $query .= "order by quand ";
		$result = @pmb_mysql_query($query);
		if(!$result) die("can't select from table audit left join users :<br /><b>$query</b> ");
        if(pmb_mysql_num_rows($result)){
			while ($audit=pmb_mysql_fetch_object($result)) {
				$this->all_audit[] = $audit ; 
			}
		}
    }

	// ---------------------------------------------------------------
	//		get_creation () : récupération création
	// ---------------------------------------------------------------
	public function get_creation () {
		global $pmb_type_audit ;
		if (!$pmb_type_audit || !isset($this->all_audit[0])) return 0;
		return $this->all_audit[0];
	}
	
	// ---------------------------------------------------------------
	//		get_last () : récupération dernière modification
	// ---------------------------------------------------------------
	public function get_last () {
		global $pmb_type_audit ;
		if (!$pmb_type_audit || !isset($this->all_audit[(count($this->all_audit)-1)])) return 0;
		return $this->all_audit[(count($this->all_audit)-1)];
	}
	
	// ---------------------------------------------------------------
	//		insert_creation ($type=0, $obj=0) : 
	// ---------------------------------------------------------------
	public static function insert_creation ($type=0, $obj=0,$info="") {
		global $PMBuserid, $PMBusername, $pmb_type_audit ;
		
		if (!$pmb_type_audit) return 0;
		$type = intval($type);
		$obj = intval($obj);
		$query = "INSERT INTO audit SET ";
		$query .= "type_obj='$type', ";
		$query .= "object_id='$obj', ";
		$query .= "user_id='$PMBuserid', ";
		$query .= "user_name='$PMBusername', ";
		$query .= "type_modif=1, ";
		$query .= "info='".addslashes($info)."' ";
		$result = @pmb_mysql_query($query);
		if(!$result) return 0;
		return 1;
	}

	// ---------------------------------------------------------------
	//		insert_modif ($type=0, $obj=0) : 
	// ---------------------------------------------------------------
	public static function insert_modif ($type=0, $obj=0,$info="") {
		global $PMBuserid, $PMBusername, $pmb_type_audit ;
		
		if (!$pmb_type_audit) return 0;
		$type = intval($type);
		$obj = intval($obj);
		if ($pmb_type_audit=='1') {
			$query = "DELETE FROM audit WHERE ";
			$query .= "type_obj='$type' AND ";
			$query .= "object_id='$obj' AND ";
			$query .= "type_modif=2 ";
			$result = @pmb_mysql_query($query);
			if(!$result) return 0;
		}
		$query = "INSERT INTO audit SET ";
		$query .= "type_obj='$type', ";
		$query .= "object_id='$obj', ";
		$query .= "user_id='$PMBuserid', ";
		$query .= "user_name='$PMBusername', ";
		$query .= "type_modif=2, ";
		$query .= "info='".addslashes($info)."' ";
		$result = @pmb_mysql_query($query);
		return 1;
	}
		
	// ---------------------------------------------------------------
	//		delete_audit ($type=0, $obj=0) : 
	// ---------------------------------------------------------------
	public static function delete_audit($type=0, $obj=0) {
		$type = intval($type);
		$obj = intval($obj);
		
		$query = "select info FROM audit WHERE type_obj=".$type." AND object_id=".$obj; 
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while ($audit = pmb_mysql_fetch_object($result)) {
				if(!$audit->info) continue;				
				$info = json_decode($audit->info);
				if($info && $info->uri) {
					contribution_area_forms_controller::delete_uri($info->uri);
				}
			}	
		}
		
		$query = "DELETE FROM audit WHERE ";
		$query .= "type_obj='$type' AND ";
		$query .= "object_id in ($obj) ";
		$result = @pmb_mysql_query($query);
		return 1;
	}
	
	// ---------------------------------------------------------------
	//		A appeler avant l'update de la table pour mémoriser les valeurs
	// ---------------------------------------------------------------	
	public function get_old_infos($requete){
		$old_data=array();
		if($this->info['fields'])
		foreach($this->info['fields'] as $field =>$value){
			$this->info['fields'][$field]['old']="";
		}
		$res = pmb_mysql_query($requete);
		if (($line = pmb_mysql_fetch_array($res))) {
			$old_data=$line;
		}
		foreach($old_data as $field =>$value){
			if(is_numeric($field)) continue;
			$this->info['fields'][$field]['old']=$value;
		}
	}
	
	// ---------------------------------------------------------------
	//		A appeler après l'update de la table pour mémoriser les valeurs
	// ---------------------------------------------------------------
	public function get_new_infos($requete){
		$old_data=array();
		if($this->info['fields'])
		foreach($this->info['fields'] as $field =>$value){
			$this->info['fields'][$field]['new']="";
		}
		$res = pmb_mysql_query($requete);
		if (($line = pmb_mysql_fetch_array($res))) {
			$old_data=$line;
		}
		foreach($old_data as $field =>$value){
			if(is_numeric($field)) continue;
			$this->info['fields'][$field]['new']=$value;
		}		
	}
	// ---------------------------------------------------------------
	//		Insére l'audit avec les champs modifiés
	// ---------------------------------------------------------------
	public function save_info_modif($type=0, $obj=0,$comment=""){
		$info=array();
		$info['comment']=$comment;
		foreach($this->info['fields'] as $field =>$value){
			// on ne garde que les champs modifiés
			if($this->info['fields'][$field]['old']==$this->info['fields'][$field]['new'])continue;
			$info['fields'][$field]['old']=$this->info['fields'][$field]['old'];
			$info['fields'][$field]['new']=$this->info['fields'][$field]['new'];			
		}
		$this->insert_modif ($type, $obj, json_encode($info));
	}
	
	public static function get_dialog_button($object_id=0, $object_type=1) {
		global $msg;
		return "&nbsp;<input class='bouton' type='button' onClick=\"openPopUp('./audit.php?type_obj=".$object_type."&object_id=".$object_id."', 'audit_popup')\" title=\"".$msg['audit_button']."\" value=\"".$msg['audit_button']."\" />&nbsp;";
	}
	
	public static function get_label_from_type($type) {
		global $msg;
		if(isset($msg[strtolower($type)])) {
			return $msg[strtolower($type)];
		}
		return $type;
	}
} // fin if !define 
} // class audit


