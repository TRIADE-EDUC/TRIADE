<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_datanode_ui.class.php,v 1.1 2018/01/17 15:01:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/frbr/cataloging/frbr_cataloging_datanode.tpl.php");

/**
 * class frbr_cataloging_datanode_ui
 * 
 */

class frbr_cataloging_datanode_ui{

	/** Aggregations: */

	/** Compositions: */

	/** Fonctions: */
	
	public static function get_form(){
		global $frbr_cataloging_datanode_form_tpl, $msg;

		$form = $frbr_cataloging_datanode_form_tpl;
		$form = str_replace('!!users_checkboxes!!', self::generate_users(), $form);

		return $form;
	}
	
	public static function generate_users(){
		global $charset;
		$counter = 1;
		$users_checkboxes = "
			<input type='hidden' name='owner' id='owner' value='".SESSuserid."'/>
			<table id='user_id_table'><tr>";
		$query = "select userid, username from users order by username";
		$result=pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row=pmb_mysql_fetch_object($result)){
				$checked = '';
				if($row->userid == SESSuserid){
					$checked = 'checked=\'checked\' onclick=\'return false;\'';
				}
				$users_checkboxes.= "<td><input type='checkbox' ".$checked." id='user_id_".$row->userid."' class='checkbox' name='datanode_allowed_users[]' value='".$row->userid."'/>"."<label for='user_id_".$row->userid."'>".htmlentities($row->username,ENT_QUOTES,$charset)."</label></td>";
				if($counter%6 == 0){
					$users_checkboxes.= "</tr><tr>";
				}
				$counter++;
			}
		}
		$users_checkboxes.="</tr></table>";
		return $users_checkboxes;
	}
} // end of frbr_cataloging_datanode_ui
