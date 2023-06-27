<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external_services_esusers.class.php,v 1.4 2017-05-19 10:06:11 dgoron Exp $

//Gestion des utilisateurs et des groupes externes des services externes

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/external_services.class.php");

/*
======================================================================================
Comment ça marche toutes ces classe?

     ............................
     .         es_base          .
     ............................
     . classe de base, contient .
     . le mécanisme des erreurs .
     ............................
                   ^ hérite de
                   |
                   |
    .----------------------------.              .--------------------.
    |         es_esuser          |              |     es_esusers     |
    |----------------------------| [all]        |--------------------|
    | représente un utilisateur  |<-------------| contient tous les  |
    | externe                    |              | utilisateurs       |
    '----------------------------'              '--------------------'
                   ^ [0..all]
                   |
                   |
          .----------------.                   .----------------------.
          |  es_esgroups   |                   |      es_esgroup      |
          |----------------| [all]             |----------------------|
          | contient tous  |<------------------| représente un groupe |
          | les groupes    |                   | d'utilisateurs       |
          '----------------'                   '----------------------'

======================================================================================
*/

define("ES_USER_UNKNOWN_USERID",1);
define("ES_GROUP_UNKNOWN_USERID",2);

class es_esuser extends es_base {
	public $esuser_id;
	public $esuser_username;
	public $esuser_fullname;
	public $esuser_password;
	public $esuser_group;

	public function __construct($userid) {
		global $dbh;
		$userid+=0; //Conversion en int
		$sql = 'SELECT * from es_esusers WHERE esuser_id = '.$userid;
		$res = pmb_mysql_query($sql, $dbh);
		if (pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_assoc($res);
			$this->esuser_id = $row["esuser_id"];
			$this->esuser_username = $row["esuser_username"];
			$this->esuser_fullname = $row["esuser_fullname"];
			$this->esuser_password = $row["esuser_password"];
			$this->esuser_group = $row["esuser_groupnum"];
		}
		else {
			$this->set_error(ES_USER_UNKNOWN_USERID,$msg["es_user_unknown_user"]);
		}
	}
	
	public static function username_exists($username) {
		global $dbh;
		$sql = "SELECT esuser_id FROM es_esusers WHERE esuser_username = '".addslashes($username)."'";
		$res = pmb_mysql_query($sql, $dbh);
		return pmb_mysql_num_rows($res) > 0 ? pmb_mysql_result($res, 0, 0) : 0;
	}
	
	public static function add_new() {
		global $dbh;
		$sql = "INSERT INTO es_esusers () VALUES ()";
		$res = pmb_mysql_query($sql, $dbh);
		$new_esuser_id = pmb_mysql_insert_id($dbh);
		return new es_esuser($new_esuser_id);
	}
	
	public static function create_from_credentials($user_name, $password) {
		global $dbh;
		$sql = "SELECT esuser_id FROM es_esusers WHERE esuser_username = '".addslashes($user_name)."' AND esuser_password = '".addslashes($password)."'";
		$res = pmb_mysql_query($sql, $dbh);
		if (!pmb_mysql_num_rows($res))
			return false;
		$id = pmb_mysql_result($res, 0, 0);
		return new es_esuser($id);
	}
	
	public function commit_to_db() {
		global $dbh;
		//on oublie pas que includes/global_vars.inc.php s'amuse à tout addslasher tout seul donc on le fait pas ici
		$sql = "UPDATE es_esusers SET esuser_username = '".$this->esuser_username."', esuser_password = '".$this->esuser_password."', esuser_fullname = '".$this->esuser_fullname."', esuser_groupnum = ".$this->esuser_group." WHERE esuser_id = ".$this->esuser_id."";
		pmb_mysql_query($sql, $dbh);
	}
	
	public function delete() {
		global $dbh;
		//Deletons l'user
		$sql = "DELETE FROM es_esusers WHERE esuser_id = ".$this->esuser_id;
		pmb_mysql_query($sql, $dbh);

		//Enlevons l'user de tout les groupes dans lesquels il était.
		$sql = "DELETE FROM es_esgroup_esusers WHERE esgroupuser_usertype=1 AND esgroupuser_usernum = ".$this->esuser_id;
		pmb_mysql_query($sql, $dbh);
	}
	
}

class es_esusers extends es_base {
	public $users=array();//Array of es_esuser
	
	public function __construct() {
		global $dbh;
		$sql = 'SELECT esuser_id from es_esusers';
		$res = pmb_mysql_query($sql, $dbh);
		while ($row=pmb_mysql_fetch_assoc($res)) {
			$aesuser = new es_esuser($row["esuser_id"]);
			$this->users[] = clone $aesuser;
		}
	}
}

class es_esgroup extends es_base {
	public $esgroup_id;
	public $esgroup_name;
	public $esgroup_fullname;
	public $esgroup_pmbuserid;
	public $esgroup_pmbuser_username;
	public $esgroup_pmbuser_lastname;
	public $esgroup_pmbuser_firstname;
	public $esgroup_esusers=array();
	public $esgroup_emprgroups=array();
	
	public function __construct($group_id){
		global $dbh;
		$group_id+=0; //Conversion en int
		$sql = 'SELECT esgroup_id, esgroup_name, esgroup_fullname, esgroup_pmbusernum, users.username, users.nom, users.prenom FROM es_esgroups LEFT JOIN users ON (users.userid = es_esgroups.esgroup_pmbusernum) WHERE esgroup_id = '.$group_id;
		$res = pmb_mysql_query($sql, $dbh);
		if (pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_assoc($res);
			$this->esgroup_id = $row["esgroup_id"];
			$this->esgroup_name = $row["esgroup_name"];
			$this->esgroup_fullname = $row["esgroup_fullname"];
			$this->esgroup_pmbuserid = $row["esgroup_pmbusernum"];
			$this->esgroup_pmbuser_username = $row["username"];
			$this->esgroup_pmbuser_lastname = $row["nom"];
			$this->esgroup_pmbuser_firstname = $row["prenom"];
		}
		else {
			$this->set_error(ES_GROUP_UNKNOWN_USERID,$msg["es_user_unknown_group"]);
			return;
		}
		
		$sql = "SELECT esuser_id FROM es_esusers WHERE esuser_groupnum = ".$group_id;
		$res = pmb_mysql_query($sql, $dbh);
		while($row = pmb_mysql_fetch_assoc($res)) {
			$this->esgroup_esusers[] = $row["esuser_id"];
		}
		
		$sql = "SELECT * FROM es_esgroup_esusers WHERE esgroupuser_groupnum = ".$group_id;
		$res = pmb_mysql_query($sql, $dbh);
		while($row = pmb_mysql_fetch_assoc($res)) {
			/*if ($row["esgroupuser_usertype"] == 1)
				$this->esgroup_esusers[] = $row["esgroupuser_usernum"];
			else*/ 
			if ($row["esgroupuser_usertype"] == 2)
				$this->esgroup_emprgroups[] = $row["esgroupuser_usernum"];
		}
	}
	
	public static function name_exists($name) {
		global $dbh;
		$sql = "SELECT esgroup_id FROM es_esgroups WHERE esgroup_name = '".addslashes($name)."'";
		$res = pmb_mysql_query($sql, $dbh);
		return pmb_mysql_num_rows($res) > 0 ? pmb_mysql_result($res, 0, 0) : 0;
	}
	
	public static function id_exists($id) {
		global $dbh;
		$sql = "SELECT esgroup_id FROM es_esgroups WHERE esgroup_id = ".($id+0)."";
		$res = pmb_mysql_query($sql, $dbh);
		return pmb_mysql_num_rows($res) > 0 ? pmb_mysql_result($res, 0, 0) : 0;		
	}
	
	public static function add_new() {
		global $dbh;
		$sql = "INSERT INTO es_esgroups () VALUES ()";
		$res = pmb_mysql_query($sql, $dbh);
		$new_esgroup_id = pmb_mysql_insert_id($dbh);
		return clone new es_esgroup($new_esgroup_id);
	}
	
	function commit_to_db() {
		global $dbh;
		//on oublie pas que includes/global_vars.inc.php s'amuse à tout addslasher tout seul donc on le fait pas ici
		$sql = "UPDATE es_esgroups SET esgroup_name = '".$this->esgroup_name."', esgroup_fullname = '".$this->esgroup_fullname."', esgroup_pmbusernum = '".$this->esgroup_pmbuserid."' WHERE esgroup_id = '".$this->esgroup_id."'";
		pmb_mysql_query($sql, $dbh);
		
		//Vidage du groupe
		$sql = "DELETE FROM es_esgroup_esusers WHERE esgroupuser_groupnum = ".$this->esgroup_id;
		pmb_mysql_query($sql, $dbh);
		
		//Remplissage du groupe (es_users)
		if(count($this->esgroup_esusers)) {
			$sql = "INSERT INTO es_esgroup_esusers (esgroupuser_groupnum ,esgroupuser_usertype ,esgroupuser_usernum) VALUES ";
			$values=array();
			foreach ($this->esgroup_esusers as $aesuser_id) {
				if (!$aesuser_id) continue;
				$values[] = '('.$this->esgroup_id.', 1, '.$aesuser_id.')';
			}
			if(count($values)) {
				$sql .= implode(",", $values);
				pmb_mysql_query($sql, $dbh);
			}
		}
		
		//Remplissage du groupe (groupes de lecteurs)
		if(count($this->esgroup_emprgroups)) {
			$sql = "INSERT INTO es_esgroup_esusers (esgroupuser_groupnum ,esgroupuser_usertype ,esgroupuser_usernum) VALUES ";
			$values=array();
			foreach ($this->esgroup_emprgroups as $aemprgroup_id) {
				if (!$aemprgroup_id) continue;
				$values[] = '('.$this->esgroup_id.', 2, '.$aemprgroup_id.')';
			}
			if(count($values)) {
				$sql .= implode(",", $values);
				pmb_mysql_query($sql, $dbh);
			}
		}
	}
	
	function delete() {
		global $dbh;
		//Suppression du groupe
		$sql = "DELETE FROM es_esgroups WHERE esgroup_id = ".$this->esgroup_id;
		pmb_mysql_query($sql, $dbh);
		//Vidage du groupe
		$sql = "DELETE FROM es_esgroup_esusers WHERE esgroupuser_groupnum = ".$this->esgroup_id;
		pmb_mysql_query($sql, $dbh);
	}
	
}

class es_esgroups extends es_base {
	public $groups=array();//Array of es_group
	
	function __construct() {
		global $dbh;
		$sql = 'SELECT esgroup_id from es_esgroups WHERE esgroup_id <> -1';
		$res = pmb_mysql_query($sql, $dbh);
		while ($row=pmb_mysql_fetch_assoc($res)) {
			$aesgroup = new es_esgroup($row["esgroup_id"]);
			$this->groups[] = clone $aesgroup;
		}
	}
}

?>