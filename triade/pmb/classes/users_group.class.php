<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users_group.class.php,v 1.4.2.1 2024/09/06 14:45:19 tsamson Exp $

use Pmb\Dashboard\Orm\DashboardUsersGroupsOrm;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class users_group {
	
	/* ---------------------------------------------------------------
	 propriétés de la classe
	 --------------------------------------------------------------- */
	
	public $id=0;
	public $name='';
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}
	
	/* ---------------------------------------------------------------
	 getData() : récupération des propriétés
	 --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
		
		$requete = 'SELECT * FROM users_groups WHERE grp_id='.$this->id;
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		
		$data = pmb_mysql_fetch_object($result);
		$this->name = $data->grp_name;
	}
	
	public function get_content_form() {
	    //Evenement publié
	    $evt_handler = events_handler::get_instance();
	    $event = new event_users_group("users_group", "group_form");
	    $event->set_group_id($this->id);
	    $event->set_group_name($this->name);
	    $evt_handler->send($event);
	    if ($event->get_content_form()) {
	        return $event->get_content_form();
	    }
	    
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('form_libelle', 'admin_usr_grp_lib')
		->add_input_node('text', $this->name);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		$interface_form = new interface_admin_form('groupform');
		if(!$this->id){
			$interface_form->set_label($msg['admin_usr_grp_add']);
		}else{
			$interface_form->set_label($msg['admin_usr_grp_mod']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('users_groups')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_libelle;
		
		$this->name = stripslashes($form_libelle);
	}
	
	public function get_query_if_exists() {
		return "SELECT count(1) FROM users_groups WHERE grp_name='".addslashes($this->name)."' AND grp_id!='".$this->id."'";
	}
	
	public function save() {
		//if item already exists UPDATE else INSERT
		if($this->id) {
			$q = "UPDATE users_groups SET grp_name='".addslashes($this->name)."' WHERE grp_id='".$this->id."' ";
			pmb_mysql_query($q);
		} else {
			$q = "INSERT INTO users_groups (grp_id, grp_name) VALUES (0, '".addslashes($this->name)."') ";
			pmb_mysql_query($q);
			$this->id = pmb_mysql_insert_id();
		}
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from users where grp_num='".$id."' "),0 ,0 );
			if ($total==0) {
				$q = "DELETE FROM users_groups WHERE grp_id='$id' ";
				pmb_mysql_query($q);

				//Suppression des partages de dashboard liés aux groupe
				DashboardUsersGroupsOrm::deleteWhere('num_users_groups', $id);

				//Evenement publié
				$evt_handler = events_handler::get_instance();
				$event = new event_users_group("users_group", "delete");
				$event->set_group_id($id);
				$evt_handler->send($event);
				
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message('admin_usr_grp_ges', 'admin_usr_grp_del_imp');
				return false;
			}
		}
		return true;
	}
} /* fin de définition de la classe */