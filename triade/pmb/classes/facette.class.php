<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette.class.php,v 1.11 2018-10-26 09:12:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion d'une facette pour la recherche Gestion et OPAC
require_once($class_path."/facette_search_opac.class.php");
require_once($class_path."/translation.class.php");
require_once($include_path."/templates/facette.tpl.php");
require_once("$class_path/search_universes/search_segment_facets.class.php");

class facette {
	protected $id;
	protected $name;
	protected $crit;
	protected $ss_crit;
	protected $nb_result;
	protected $visible_gestion;	
	protected $visible;
	protected $type_sort;
	protected $order_sort;
	protected $datatype_sort;
	protected $order;
	protected $limit_plus;
	protected $opac_views_num;	
	protected $type = 'notices';
	protected $is_external;
	public static $table_name = 'facettes';
	
	public function __construct($id=0, $is_external=false){
		$this->id = $id*1;
		$this->is_external = $is_external*1;
		if($is_external) {
			static::$table_name = 'facettes_external';
			$this->type='notices_externes';
		}
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->name = '';
		$this->crit = 0;
		$this->ss_crit = 0;
		$this->nb_result = 0;
		$this->limit_plus = 0;
		$this->visible = 0;
		$this->type_sort = 0;
		$this->order_sort = 0;
		$this->datatype_sort = 'alpha';
		$this->order = 0;
		$this->opac_views_num = '';
		if($this->id) {
			$query = "SELECT * FROM ".static::$table_name." WHERE id_facette=".$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			
			$this->id = $row->id_facette;
			$this->type = $row->facette_type;
			$this->name = $row->facette_name;
			$this->crit =$row->facette_critere;
			$this->ss_crit = $row->facette_ss_critere;
			$this->nb_result = $row->facette_nb_result;
			$this->limit_plus = $row->facette_limit_plus;
			$this->visible_gestion = $row->facette_visible_gestion;
			$this->visible = $row->facette_visible;
			$this->type_sort = $row->facette_type_sort;
			$this->order_sort = $row->facette_order_sort;
			$this->datatype_sort = $row->facette_datatype_sort;
			$this->order = $row->facette_order;
			$this->opac_views_num = $row->facette_opac_views_num;
		}
	}
	
	public function get_form() {
		global $msg,$charset;
		global $tpl_form_facette;
		global $pmb_opac_view_activate;
		global $sub;
		
		$form = $tpl_form_facette;
		if($this->id) {
			$form = str_replace('!!libelle!!', htmlentities($msg['update_facette'],ENT_QUOTES,$charset), $form);
			$form = str_replace('!!val_submit_form!!', htmlentities($msg['submitMajFacette'],ENT_QUOTES,$charset), $form);
			$input_delete = "<input class='bouton' id='delete_button' type='button' value='".htmlentities($msg['submitSupprFacette'],ENT_QUOTES,$charset)."' onClick='javascript:confirm_delete()'/>";
			$form = str_replace('!!val_submit_suppr!!', $input_delete, $form);
		} else {
			$form = str_replace('!!libelle!!', htmlentities($msg['lib_nelle_facette_form'],ENT_QUOTES,$charset), $form);
			$form = str_replace('!!val_submit_form!!', htmlentities($msg['submitSendFacette'],ENT_QUOTES,$charset), $form);
			$form = str_replace('!!val_submit_suppr!!', '', $form);
		}
		$form = str_replace('!!name_del_facette!!',sprintf($msg['label_alert_delete_facette'],htmlentities($this->name,ENT_QUOTES,$charset)),$form);
		$form = str_replace('!!label!!',htmlentities($this->name,ENT_QUOTES,$charset),$form);

		$facette_search = new facette_search_opac($this->type,$this->is_external);
		$form = str_replace('!!liste1!!', $facette_search->create_list_fields($this->crit, $this->ss_crit), $form);
		
		$form = str_replace('!!type_sort_nb_results_checked!!', (!$this->type_sort ? "checked='checked'" : ""), $form);
		$form = str_replace('!!type_sort_value_checked!!', ($this->type_sort ? "checked='checked'" : ""), $form);
		
		$form = str_replace('!!order_sort_asc_checked!!', (!$this->order_sort ? "checked='checked'" : ""), $form);
		$form = str_replace('!!order_sort_desc_checked!!', ($this->order_sort ? "checked='checked'" : ""), $form);
		
		$form = str_replace('!!datatype_sort_alpha_checked!!', (!$this->datatype_sort || $this->datatype_sort == 'alpha' ? "checked='checked'" : ""), $form);
		$form = str_replace('!!datatype_sort_num_checked!!', ($this->datatype_sort == 'num' ? "checked='checked'" : ""), $form);
		$form = str_replace('!!datatype_sort_date_checked!!', ($this->datatype_sort == 'date' ? "checked='checked'" : ""), $form);
		
		$form = str_replace('!!val_nb!!', $this->nb_result, $form);
		$form = str_replace('!!limit_plus!!',$this->limit_plus,$form);		
		
		if($this->is_external) {
			$form = str_replace('!!visible_gestion_checked!!', ($this->visible_gestion ? "checked='checked'" : ""), $form);
		}else {
			// Facette classique non encore disponible en gestion
			$form = str_replace('!!visible_gestion_checked!!', 'disabled', $form);			
		}	
		
		$form = str_replace('!!visible_checked!!', ($this->visible ? "checked='checked'" : ""), $form);

		$form = str_replace('!!sub!!', $sub, $form);
		$form = str_replace('!!type!!', $this->type, $form);
		$form = str_replace('!!id!!', htmlentities($this->id,ENT_QUOTES,$charset), $form);
		
		if($pmb_opac_view_activate){
			if($this->opac_views_num != "") {
				$liste_views = explode(",", $this->opac_views_num);
			} else {
				$liste_views = array();
			}
			$query = "SELECT opac_view_id,opac_view_name FROM opac_views order by opac_view_name";
			$result = pmb_mysql_query($query);
			$select_view = "<select id='opac_views_num' name='opac_views_num[]' multiple>";
			if (pmb_mysql_num_rows($result)) {
				$select_view .="<option id='opac_view_num_all' value='' ".(!count($liste_views) ? "selected" : "").">".htmlentities($msg["admin_opac_facette_opac_view_select"],ENT_QUOTES,$charset)."</option>";
				$select_view .="<option id='opac_view_num_0' value='0' ".(in_array(0,$liste_views) ? "selected" : "").">".htmlentities($msg["opac_view_classic_opac"],ENT_QUOTES,$charset)."</option>";
				while($row = pmb_mysql_fetch_object($result)) {
					$select_view .="<option id='opac_view_num_".$row->opac_view_id."' value='".$row->opac_view_id."' ".(in_array($row->opac_view_id,$liste_views) ? "selected" : "").">".htmlentities($row->opac_view_name,ENT_QUOTES,$charset)."</option>";
				}
			} else {
				$select_view .="<option id='opac_view_num_empty' value=''>".htmlentities($msg["admin_opac_facette_opac_view_empty"],ENT_QUOTES,$charset)."</option>";
			}
			$select_view .= "</select>";
			$form = str_replace('!!list_opac_views!!', $select_view, $form);
		}
		$translation = new translation($this->id, static::$table_name);
		$form .= $translation->connect('facette_form');
		
		return $form;
	}
	
	public function set_properties_from_form() {
		global $label_facette;
		global $list_crit;
		global $list_ss_champs;
		global $list_nb;
		global $visible_gestion;
		global $visible;
		global $type_sort;
		global $order_sort;
		global $datatype_sort;
		global $limit_plus;
		global $pmb_opac_view_activate, $opac_views_num;
		
		$this->name = stripslashes($label_facette);
		$this->crit = $list_crit*1;
		$this->ss_crit = $list_ss_champs*1;
		$this->nb_result = $list_nb*1;
		$this->visible_gestion = $visible_gestion*1;
		$this->visible = $visible*1;
		$this->type_sort = $type_sort*1;
		$this->order_sort = $order_sort*1;
		$this->datatype_sort = stripslashes($datatype_sort);
		$this->limit_plus = $limit_plus*1;
		$this->opac_views_num = '';
		if($pmb_opac_view_activate) {
			if (is_array($opac_views_num) && count($opac_views_num)) {
				if (!in_array("",$opac_views_num)) {
					$this->opac_views_num = implode(",", $opac_views_num);
				}
			}
		}
	}
	
	public function save() {
		global $pmb_opac_view_activate;
		
		if($this->id) {
			$query = "UPDATE ".static::$table_name." SET ";
			$clause = " WHERE id_facette=".$this->id;
		} else {
			$query = "INSERT INTO ".static::$table_name." SET ";
			$clause = "";
			$this->order=pmb_mysql_result(pmb_mysql_query("select max(facette_order)+1 as ordre from ".static::$table_name),0,0);
		}
		$query .= "
			facette_type='".addslashes($this->type)."',
			facette_name='".addslashes($this->name)."',
			facette_critere='".$this->crit."',
			facette_ss_critere='".$this->ss_crit."',
			facette_nb_result='".$this->nb_result."',
			facette_visible_gestion='".$this->visible_gestion."',
			facette_visible='".$this->visible."',
			facette_type_sort='".$this->type_sort."',
			facette_order_sort='".$this->order_sort."',
			facette_datatype_sort='".addslashes($this->datatype_sort)."',
			facette_order='".$this->order."',
			facette_limit_plus='".$this->limit_plus."',
			facette_opac_views_num='".$this->opac_views_num."'	
			".$clause;
		$result = pmb_mysql_query($query);
		if(!$this->id) {
			$this->id = pmb_mysql_insert_id();
		}
		//sauvegarde dans les vues..
		if ($pmb_opac_view_activate) {
			$this->save_view_facette();
		}
		$translation = new translation($this->id, static::$table_name);
		$translation->update("facette_name");
	}
	
	public function delete() {
		if($this->id) {
			$query = "DELETE FROM ".static::$table_name." WHERE id_facette=".$this->id;
			pmb_mysql_query($query);
			search_segment_facets::on_delete_facet($this->id);
			translation::delete($this->id, static::$table_name);
			return true;
		}
		return false;
	}
		
	//enregistrement ou MaJ des vues OPAC à partir d'une facette
	protected function save_view_facette(){
		global $dbh;
		
		$views = array();
		$req = "select opac_view_id from opac_views";
		$myQuery = pmb_mysql_query($req, $dbh);
		if (pmb_mysql_num_rows($myQuery)) {
			if ($this->opac_views_num == "") {
				while ($row = pmb_mysql_fetch_object($myQuery)) {
					$views["selected"][] = $row->opac_view_id;
				}
			} else {
				$list_selected_views_num = explode(",",$this->opac_views_num);
				$key_exists = array_search(0, $list_selected_views_num);
				if ($key_exists !== false) {
					array_splice($list_selected_views_num, $key_exists, 1);
				}
				while ($row = pmb_mysql_fetch_object($myQuery)) {
					if (in_array($row->opac_view_id,$list_selected_views_num)) {
						$views["selected"][] = $row->opac_view_id;
					} else {
						$views["unselected"][] = $row->opac_view_id;
					}
				}
			}
			if (isset($views["selected"]) && count($views["selected"])) {
				foreach ($views["selected"] as $view_selected) {
					$query="select opac_filter_param FROM opac_filters where opac_filter_view_num=".$view_selected." and  opac_filter_path='".static::$table_name."' ";
					$myQuery = pmb_mysql_query($query, $dbh);
					$param = array();
					if ($myQuery && pmb_mysql_num_rows($myQuery)) {
						while ($row = pmb_mysql_fetch_object($myQuery)) {
							$param = unserialize($row->opac_filter_param);
							if (!in_array($this->id, $param["selected"])) {
								$param["selected"][] = $this->id;
								$param=addslashes(serialize($param));
								$requete="update opac_filters set opac_filter_param='$param' where opac_filter_view_num=".$view_selected." and opac_filter_path='".static::$table_name."'";
								pmb_mysql_query($requete, $dbh);
							}
						}
					} else {
						$param["selected"][] = $this->id;
						$param=addslashes(serialize($param));
						$requete="insert into opac_filters set opac_filter_view_num=".$view_selected.",opac_filter_path='".static::$table_name."', opac_filter_param='$param' ";
						pmb_mysql_query($requete, $dbh);
					}
				}
			}
			if (isset($views["unselected"]) && count($views["unselected"])) {
				foreach ($views["unselected"] as $view_unselected) {
					$query="select opac_filter_param FROM opac_filters where opac_filter_view_num=".$view_unselected." and  opac_filter_path='".static::$table_name."' ";
					$myQuery = pmb_mysql_query($query, $dbh);
					$param = array();
					if ($myQuery && pmb_mysql_num_rows($myQuery)) {
						while ($row = pmb_mysql_fetch_object($myQuery)) {
							$param = unserialize($row->opac_filter_param);
							if ($key = array_search($this->id, $param["selected"])) {
								array_splice($param["selected"], $key, 1);
								$param=addslashes(serialize($param));
								$requete="update opac_filters set opac_filter_param='$param' where opac_filter_view_num=".$view_unselected." and opac_filter_path='".static::$table_name."'";
								pmb_mysql_query($requete, $dbh);
							}
						}
					}
				}
			}
		}
	}
	
	public function get_id(){
		return $this->id;
	}
	
	public function set_type($type) {
		$this->type = $type;
	}
}

