<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_views.class.php,v 1.6.8.1 2025/04/29 08:54:01 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes d'affectation des vues aux utilisateurs OPAC
// on réutilise la mécanique des quotas...
global $class_path;
require_once($class_path."/quotas.class.php");


class opac_views  extends quota {

	public function __construct(){

	}

	//formulaire d'un champ de quota...
	public static function get_quota_form($prefix,$value){
		global $msg, $charset;

		$value= !empty($value) ? unserialize($value) : null;
		if(!is_array($value)){
			$value = array(
				'allowed' => array(0),
				'default' => 0
			);
		}
		if(!is_array($value['allowed'])){
			$value['allowed'] = array();
		}
		$form="
		<table>
			<tr>
				<th>".$msg['opac_view_allowed']."</th>
				<th>".$msg['opac_view']."</th>
				<th>".$msg['opac_view_default']."</th>
			</tr>
			<tr>
				<td>
					<input type='checkbox' ".(in_array(0,$value['allowed']) ? "checked='checked' " : "")."name='".$prefix."[allowed][]' value='0'/>
				</td>
				<td>".$msg['opac_view_classic_opac']."</td>
				<td>
					<input type='radio' ".(0 == $value['default'] ? "checked='checked' " : "")."name='".$prefix."[default]' value='0'/>
				</td>
			</tr>";
		$query = "select opac_view_id, opac_view_name from opac_views order by opac_view_name";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$form.="
			<tr>";
				$form.="
				<td>
					<input type='checkbox' ".(in_array($row->opac_view_id,$value['allowed']) ? "checked='checked' " : "")."name='".$prefix."[allowed][]' value='".htmlentities($row->opac_view_id,ENT_QUOTES,$charset)."'/>
				</td>
				<td>
					".htmlentities($row->opac_view_name,ENT_QUOTES,$charset)."
				</td>
				<td>
					<input type='radio' ".($row->opac_view_id == $value['default'] ? "checked='checked' " : "")."name='".$prefix."[default]' value='".htmlentities($row->opac_view_id,ENT_QUOTES,$charset)."'/>
				</td>";
				$form.="
			</tr>";
			}
		}
		$form.="
		</table>";
		return $form;
	}

	public static function get_storable_value($value) {
		return addslashes(serialize($value));
	}

	public static function get_selector($name, $selected = array()) {
		global $msg, $charset;

		$query = "SELECT opac_view_id,opac_view_name FROM opac_views order by opac_view_name";
		$result = pmb_mysql_query($query);
		$select_view = "<select id='".$name."' name='".$name."[]' multiple>";
		if (pmb_mysql_num_rows($result)) {
			$select_view .="<option id='opac_view_num_all' value='' ".(!count($selected) ? "selected" : "").">".htmlentities($msg["admin_opac_facette_opac_view_select"],ENT_QUOTES,$charset)."</option>";
			$select_view .="<option id='opac_view_num_0' value='0' ".(in_array(0,$selected) ? "selected" : "").">".htmlentities($msg["opac_view_classic_opac"],ENT_QUOTES,$charset)."</option>";
			while($row = pmb_mysql_fetch_object($result)) {
				$select_view .="<option id='opac_view_num_".$row->opac_view_id."' value='".$row->opac_view_id."' ".(in_array($row->opac_view_id,$selected) ? "selected" : "").">".htmlentities($row->opac_view_name,ENT_QUOTES,$charset)."</option>";
			}
		} else {
			$select_view .="<option id='opac_view_num_empty' value=''>".htmlentities($msg["admin_opac_facette_opac_view_empty"],ENT_QUOTES,$charset)."</option>";
		}
		$select_view .= "</select>";
		return $select_view;
	}
}