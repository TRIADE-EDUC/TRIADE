<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users.class.php,v 1.6 2023/09/02 07:27:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class users {
	
	protected static $all_users;
	
	protected static function get_form_autorisations_user($user, $selected=false) {
		return "
		<span class='usercheckbox'>
			<input type='checkbox' name='autorisations[]' id='auto_".$user['id']."' value='".$user['id']."' ".($user['id'] == 1 || $selected ? "checked='checked'" : "")." class='checkbox' ".($user['id'] == 1 ? "disabled" : "")." />
			".($user['id'] == 1 ? "<input type='hidden' name='autorisations[]' id='auto_".$user['id']."' value='".$user['id']."' />" : "")."
			<label for='auto_".$user['id']."' class='normlabel'>&nbsp;".$user['name']."</label>
		</span>";
	}
	
	protected static function get_form_autorisations_group($users, $selected=array()) {
		$form = '';
		foreach ($users as $user) {
			$form .= static::get_form_autorisations_user($user, (in_array($user['id'], $selected) ? true : false));
		}
		return $form;
	}
	
	protected static function format_id_list($list, $all=true) {
		$ids = array();
		if($all) {
			foreach ($list as $users) {
				foreach($users as $user) {
					$ids[] = 'auto_'.$user['id'];
				}
			}
		} else {
			foreach($list as $user) {
				$ids[] = 'auto_'.$user['id'];
			}
		}
		return implode('|', $ids);
	}
	
	public static function get_form_autorisations($param_autorisations="1", $on_create=1) {
		global $msg;
		global $PMBuserid;
	
		if ($on_create) $param_autorisations.=" ".$PMBuserid ;
		$autorisations_donnees=explode(" ",$param_autorisations);
		$query = "SELECT userid, username, grp_id, grp_name FROM users LEFT JOIN users_groups ON users_groups.grp_id=users.grp_num order by grp_name, username ";
		$result = pmb_mysql_query($query);
		static::$all_users = array();
		while ($row = pmb_mysql_fetch_object($result)) {
			if($row->grp_name) {
				static::$all_users[$row->grp_name][] = array('id' => $row->userid, 'name' =>$row->username);
			} else {
				static::$all_users[$msg[128]][] = array('id' => $row->userid, 'name' =>$row->username);
			}
		}
		
		
		$autorisations_users="
		<div class='usersgroupscheckbox'>";
		if(count(static::$all_users) > 1) {
			$i = 0;
			foreach (static::$all_users as $label=>$group) {
				$autorisations_users .= "
				<div class='groupcheckbox'>
						<span class='grouplabelcheckbox'>".$label."</span>
						<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list_group_".$i."\").value,1);'>
						<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list_group_".$i."\").value,0);'>
					<div class='groupuserschild groupuserscheckbox ui-clearfix ui-flex ui-flex-1-5 ui-flex-top'>
						".static::get_form_autorisations_group($group, $autorisations_donnees)."
						<input type='hidden' id='auto_id_list_group_".$i."' name='auto_id_list_group_".$i."' value='".static::format_id_list(static::$all_users[$label], false)."' />
					</div>
				</div>";
				$i++;
			}
		} else {
			foreach (static::$all_users as $label=>$group) {
				$autorisations_users .= "
				<div class='groupcheckbox'>
					<div class='groupuserschild groupuserscheckbox ui-clearfix ui-flex ui-flex-1-5 ui-flex-top'>
						".static::get_form_autorisations_group($group, $autorisations_donnees)."
					</div>
				</div>";
			}
		}
		$autorisations_users.="
			<input type='hidden' id='auto_id_list' name='auto_id_list' value='".static::format_id_list(static::$all_users)."' >
		</div>";
		return $autorisations_users;
	}
	
	public static function get_users_from_mail($mail) {
		$users = array();
		$query = "SELECT userid FROM users WHERE user_email = '".addslashes($mail)."'";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_object($result)) {
			$users[$row->userid] = new user($row->userid);
		}
		return $users;
	}
} // fin de déclaration de la classe users