<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_users_groups_ui.class.php,v 1.2 2021/01/12 07:23:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_users_groups_ui extends list_configuration_users_ui {
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		global $msg;
		
		parent::__construct($filters, $pager, $applied_sort);
		$this->add_object((object) array('grp_id' => 0, 'grp_name' => $msg['admin_usr_grp_non_aff']));
	}
	
	protected function _get_query_base() {
		return 'SELECT * FROM users_groups';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('grp_name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'grp_name' => 'admin_usr_grp_lib',
				'users' => 'admin_usr_grp_usr',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'users':
				$query = "select userid, username, prenom, nom from users where grp_num='".$object->grp_id."' order by username ";
				$result = pmb_mysql_query($query);
				while (($row = pmb_mysql_fetch_object($result))) {
					$content .= "<a href= \"./admin.php?categ=users&sub=users&action=modif&id=".$row->userid."\" >";
					$lib = $row->username.' (';
					if (trim($row->prenom)!=='') $lib.= $row->prenom.' ';
					$lib.= $row->nom.')';
					$content .= htmlentities($lib, ENT_QUOTES, $charset);
					$content .= '</a><br />';
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->grp_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_usr_grp_add'];
	}
}