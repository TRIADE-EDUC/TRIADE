<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external_services_esusers.class.php,v 1.8 2023/07/07 09:28:49 dgoron Exp $

//Gestion des utilisateurs et des groupes externes des services externes

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
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

	public function __construct($userid=0) {
		$this->esuser_id = intval($userid);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $msg;
		
		$this->esuser_username = '';
		$this->esuser_fullname = '';
		$this->esuser_password = '';
		$this->esuser_group = 0;
		$query = 'SELECT * from es_esusers WHERE esuser_id = '.$this->esuser_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_assoc($result);
			$this->esuser_username = $row["esuser_username"];
			$this->esuser_fullname = $row["esuser_fullname"];
			$this->esuser_password = $row["esuser_password"];
			$this->esuser_group = $row["esuser_groupnum"];
		}
		else {
			$this->set_error(ES_USER_UNKNOWN_USERID,$msg["es_user_unknown_user"]);
		}
	}
	
	public function get_content_form() {
		global $msg, $charset;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('esuser_username', 'es_user_username')
		->add_input_node('text', $this->esuser_username);
		$interface_content_form->add_element('esuser_fullname', 'es_user_fullname')
		->add_input_node('text', $this->esuser_fullname);
		$interface_content_form->add_element('esuser_password', 'es_user_password')
		->add_input_node('text', $this->esuser_password);
		
		//group
		$esgroups = new es_esgroups();
		$groupselect = '<select name="esuser_esgroup">';
		$groupselect .= '<option value="0">'.$msg["es_user_group_none"].'</option>';
		foreach ($esgroups->groups as &$aesgroup) {
			$groupselect .= '<option '.($this->esuser_group == $aesgroup->esgroup_id ? 'selected' : '').' value="'.$aesgroup->esgroup_id.'">'.htmlentities($aesgroup->esgroup_name.' ('.$aesgroup->esgroup_fullname.')' ,ENT_QUOTES, $charset).'</option>';
		}
		$groupselect .= '</select>';
		$interface_content_form->add_element('esuser_esgroup', 'es_user_group')
		->add_html_node($groupselect);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('form_esuser');
		if(!$this->esuser_id){
			$interface_form->set_label($msg['es_users_add']);
		}else{
			$interface_form->set_label($msg['es_users_edit']);
		}
		$interface_form->set_object_id($this->esuser_id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->esuser_username." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('es_esusers')
		->set_field_focus('esuser_username');
		return $interface_form->get_display();				
	}
	
	public function set_properties_from_form() {
		global $esuser_username, $esuser_fullname, $esuser_password, $esuser_esgroup;
		
		$this->esuser_username = $esuser_username;
		$this->esuser_fullname = $esuser_fullname;
		$this->esuser_password = $esuser_password;
		$this->esuser_group = $esuser_esgroup;
	}
	
	public function save() {
		$this->commit_to_db();
	}
	
	public static function username_exists($username) {
		$sql = "SELECT esuser_id FROM es_esusers WHERE esuser_username = '".addslashes($username)."'";
		$res = pmb_mysql_query($sql);
		return pmb_mysql_num_rows($res) > 0 ? pmb_mysql_result($res, 0, 0) : 0;
	}
	
	public static function add_new() {
		$sql = "INSERT INTO es_esusers () VALUES ()";
		pmb_mysql_query($sql);
		$new_esuser_id = pmb_mysql_insert_id();
		return new es_esuser($new_esuser_id);
	}
	
	public static function create_from_credentials($user_name, $password) {
		$sql = "SELECT esuser_id FROM es_esusers WHERE esuser_username = '".addslashes($user_name)."' AND esuser_password = '".addslashes($password)."'";
		$res = pmb_mysql_query($sql);
		if (!pmb_mysql_num_rows($res))
			return false;
		$id = pmb_mysql_result($res, 0, 0);
		return new es_esuser($id);
	}
	
	public function commit_to_db() {
		//on oublie pas que includes/global_vars.inc.php s'amuse à tout addslasher tout seul donc on le fait pas ici
		$sql = "UPDATE es_esusers SET esuser_username = '".addslashes($this->esuser_username)."', esuser_password = '".addslashes($this->esuser_password)."', esuser_fullname = '".addslashes($this->esuser_fullname)."', esuser_groupnum = ".addslashes($this->esuser_group)." WHERE esuser_id = ".$this->esuser_id."";
		pmb_mysql_query($sql);
	}
	
	public function delete() {
		//Deletons l'user
		$sql = "DELETE FROM es_esusers WHERE esuser_id = ".$this->esuser_id;
		pmb_mysql_query($sql);

		//Enlevons l'user de tout les groupes dans lesquels il était.
		$sql = "DELETE FROM es_esgroup_esusers WHERE esgroupuser_usertype=1 AND esgroupuser_usernum = ".$this->esuser_id;
		pmb_mysql_query($sql);
	}
	
}

class es_esusers extends es_base {
	public $users=array();//Array of es_esuser
	
	public function __construct() {
		$sql = 'SELECT esuser_id from es_esusers';
		$res = pmb_mysql_query($sql);
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
	
	public function __construct($group_id=0){
		$this->esgroup_id = intval($group_id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $msg;
		
		$this->esgroup_name = '';
		$this->esgroup_fullname = '';
		$this->esgroup_pmbuserid = 0;
		$this->esgroup_pmbuser_username = '';
		$this->esgroup_pmbuser_lastname = '';
		$this->esgroup_pmbuser_firstname = '';
		$this->esgroup_esusers = array();
		$this->esgroup_emprgroups = array();
		
		$sql = 'SELECT esgroup_id, esgroup_name, esgroup_fullname, esgroup_pmbusernum, users.username, users.nom, users.prenom FROM es_esgroups LEFT JOIN users ON (users.userid = es_esgroups.esgroup_pmbusernum) WHERE esgroup_id = '.$this->esgroup_id;
		$res = pmb_mysql_query($sql);
		if (pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_assoc($res);
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
		
		$sql = "SELECT esuser_id FROM es_esusers WHERE esuser_groupnum = ".$this->esgroup_id;
		$res = pmb_mysql_query($sql);
		while($row = pmb_mysql_fetch_assoc($res)) {
			$this->esgroup_esusers[] = $row["esuser_id"];
		}
		
		$sql = "SELECT * FROM es_esgroup_esusers WHERE esgroupuser_groupnum = ".$this->esgroup_id;
		$res = pmb_mysql_query($sql);
		while($row = pmb_mysql_fetch_assoc($res)) {
			/*if ($row["esgroupuser_usertype"] == 1)
			 $this->esgroup_esusers[] = $row["esgroupuser_usernum"];
			 else*/
			if ($row["esgroupuser_usertype"] == 2)
				$this->esgroup_emprgroups[] = $row["esgroupuser_usernum"];
		}
	}
	
	public function get_form() {
		global $msg, $charset;
		
		//name
		$content_form = '<div class=row><label class="etiquette" for="es_group_name">'.$msg["es_group_name"].'</label><br />';
		$content_form .= '<input name="es_group_name" type="text" value="'.htmlentities($this->esgroup_name,ENT_QUOTES, $charset).'" class="saisie-80em">
			</div>';
		
		//fullname
		$content_form .= '<div class=row><label class="etiquette" for="es_group_fullname">'.$msg["es_group_fullname"].'</label><br />';
		$content_form .= '<input name="es_group_fullname" type="text" value="'.htmlentities($this->esgroup_fullname,ENT_QUOTES, $charset).'" class="saisie-80em">
			</div>';
		
		$pmbusers_sql = "SELECT userid, username, nom, prenom FROM users";
		$pmbusers_res = pmb_mysql_query($pmbusers_sql);
		$pmbusers = array();
		while($pmbusers_row = pmb_mysql_fetch_assoc($pmbusers_res)) {
			$pmbusers[] = $pmbusers_row;
		}
		
		//pmbuser
		$content_form .= '<div class=row><label class="etiquette" for="es_group_pmbuserid">'.$msg["es_group_pmbuserid"].'</label><br />';
		$content_form .= '<select name="es_group_pmbuserid">';
		foreach ($pmbusers as $apmbuser) {
			$content_form .= '<option '.($apmbuser["userid"] == $this->esgroup_pmbuserid ? ' selected ' : '').' value="'.$apmbuser["userid"].'">'.htmlentities($apmbuser["username"].' ('.$apmbuser["nom"].' '.$apmbuser['prenom'].')' ,ENT_QUOTES, $charset).'</option>';
		}
		$content_form .= '</select></div>';
		
		//es_users
		$es_users = new es_esusers();
		$content_form .= '<div class=row><label class="etiquette" for="es_group_esusers">'.$msg["es_group_esusers"].'</label><br />';
		$content_form .= '<select name="es_group_esusers[]" DISABLED MULTIPLE>';
		foreach ($es_users->users as &$aesuser) {
			$content_form .= '<option '.(in_array($aesuser->esuser_id, $this->esgroup_esusers) ? ' selected ' : '').' value="'.$aesuser->esuser_id.'">'.htmlentities($aesuser->esuser_username.' ('.$aesuser->esuser_fullname.')' ,ENT_QUOTES, $charset).'</option>';
		}
		$content_form .= '</select></div>';
		
		//empr_groups
		$pmbemprgroups = array();
		$pmbemprgroup_sql = "SELECT id_groupe, libelle_groupe FROM groupe";
		$pmbemprgroup_res = pmb_mysql_query($pmbemprgroup_sql);
		while($row=pmb_mysql_fetch_assoc($pmbemprgroup_res))
			$pmbemprgroups[] = $row;
			
			$content_form .= '<div class=row><label class="etiquette" for="es_group_emprgroupe">'.$msg["es_group_emprgroupe"].'</label><br />';
			$content_form .= '<select name="es_group_emprgroups[]" MULTIPLE>';
			$content_form .= "<option value=''>".htmlentities($msg["es_group_emprgroupe_none"] ,ENT_QUOTES, $charset)."</option>";
			foreach ($pmbemprgroups as $aemprgroups) {
				$content_form .= '<option '.(in_array($aemprgroups["id_groupe"], $this->esgroup_emprgroups) ? ' selected ' : '').' value="'.$aemprgroups["id_groupe"].'">'.htmlentities($aemprgroups["libelle_groupe"] ,ENT_QUOTES, $charset).'</option>';
			}
			$content_form .= '</select></div>';
		
		$interface_form = new interface_admin_form('form_esgroup');
		if(!$this->esgroup_id){
			$interface_form->set_label($msg['es_groups_add']);
		}else{
			$interface_form->set_label($msg['es_groups_edit']);
		}
		
		$interface_form->set_object_id($this->esgroup_id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->esgroup_name." ?")
		->set_content_form($content_form)
		->set_table_name('es_esgroups')
		->set_field_focus('es_group_name');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $es_group_name, $es_group_fullname, $es_group_pmbuserid, $es_group_esusers, $es_group_emprgroups;
		
		$this->esgroup_name = stripslashes($es_group_name);
		$this->esgroup_fullname = stripslashes($es_group_fullname);
		$this->esgroup_pmbuserid = intval($es_group_pmbuserid);
		if (!is_array($es_group_esusers)) {
			$es_group_esusers = array();
		}
		$this->esgroup_esusers = $es_group_esusers;
		if (!is_array($es_group_emprgroups)) {
			$es_group_emprgroups = array($es_group_emprgroups);
		}
		$this->esgroup_emprgroups = $es_group_emprgroups;
	}
	
	public function save() {
		$this->commit_to_db();
	}
	
	public static function name_exists($name) {
		$sql = "SELECT esgroup_id FROM es_esgroups WHERE esgroup_name = '".addslashes($name)."'";
		$res = pmb_mysql_query($sql);
		return pmb_mysql_num_rows($res) > 0 ? pmb_mysql_result($res, 0, 0) : 0;
	}
	
	public static function id_exists($id) {
		$sql = "SELECT esgroup_id FROM es_esgroups WHERE esgroup_id = ".($id+0)."";
		$res = pmb_mysql_query($sql);
		return pmb_mysql_num_rows($res) > 0 ? pmb_mysql_result($res, 0, 0) : 0;		
	}
	
	public static function add_new() {
		$sql = "INSERT INTO es_esgroups () VALUES ()";
		pmb_mysql_query($sql);
		$new_esgroup_id = pmb_mysql_insert_id();
		return clone new es_esgroup($new_esgroup_id);
	}
	
	public function commit_to_db() {
		//on oublie pas que includes/global_vars.inc.php s'amuse à tout addslasher tout seul donc on le fait pas ici
		$sql = "UPDATE es_esgroups SET esgroup_name = '".addslashes($this->esgroup_name)."', esgroup_fullname = '".addslashes($this->esgroup_fullname)."', esgroup_pmbusernum = '".$this->esgroup_pmbuserid."' WHERE esgroup_id = '".$this->esgroup_id."'";
		pmb_mysql_query($sql);
		
		//Vidage du groupe
		$sql = "DELETE FROM es_esgroup_esusers WHERE esgroupuser_groupnum = ".$this->esgroup_id;
		pmb_mysql_query($sql);
		
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
				pmb_mysql_query($sql);
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
				pmb_mysql_query($sql);
			}
		}
	}
	
	public function delete() {
		//Suppression du groupe
		$sql = "DELETE FROM es_esgroups WHERE esgroup_id = ".$this->esgroup_id;
		pmb_mysql_query($sql);
		//Vidage du groupe
		$sql = "DELETE FROM es_esgroup_esusers WHERE esgroupuser_groupnum = ".$this->esgroup_id;
		pmb_mysql_query($sql);
	}
	
}

class es_esgroups extends es_base {
	public $groups=array();//Array of es_group
	
	public function __construct() {
		$sql = 'SELECT esgroup_id from es_esgroups WHERE esgroup_id <> -1';
		$res = pmb_mysql_query($sql);
		while ($row=pmb_mysql_fetch_assoc($res)) {
			$aesgroup = new es_esgroup($row["esgroup_id"]);
			$this->groups[] = clone $aesgroup;
		}
	}
}

?>