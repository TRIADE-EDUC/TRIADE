<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette.class.php,v 1.19.4.2 2025/03/04 10:09:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion d'une facette pour la recherche Gestion et OPAC
global $class_path, $include_path;
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
	protected $num_facettes_set;
	protected $type = 'notices';
	protected $is_external;
	public static $table_name = 'facettes';
	
	public function __construct($id=0, $is_external=false){
		$this->id = intval($id);
		$this->is_external = intval($is_external);
		if($this->is_external) {
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
		$this->num_facettes_set = 0;
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
			$this->num_facettes_set = $row->num_facettes_set;
		}
	}
	
	public function get_content_form() {
	    global $msg, $charset;
	    global $pmb_opac_view_activate;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    
	    if (!empty($this->num_facettes_set)) {
	        $facette_sets = new facettes_set($this->num_facettes_set);
	        $interface_content_form->add_element('facettes_set', 'facettes_set')
	        ->add_html_node($facette_sets->get_translated_name());
	    }
	    $interface_content_form->add_element('label_facette', 'intitule_facette')
	    ->add_input_node('text', $this->name)
	    ->set_attributes(array('data-translation-fieldname' => 'facette_name'));
	    
	    //TODO : div class='row' id='list_fields'
	    $facette_search = facettes_opac_controller::get_facette_search_opac_instance($this->type,$this->is_external);
	    $interface_content_form->add_element('list_crit', 'list_crit_form_facette')
	    ->add_html_node("<div class='row' id='list_fields'>".$facette_search->create_list_fields($this->crit, $this->ss_crit)."</div><div id='liste2' class='row'></div>");
	    
	    $element = $interface_content_form->add_element('type_sort', 'crit_sort_facette');
	    $element->set_display_nodes_separator('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
	    $element->add_input_node('radio', '0')
	    ->set_label_code('intit_gest_tri1')
	    ->set_checked(!$this->type_sort ? true : false);
	    $element->add_input_node('radio', '1')
	    ->set_label_code('intit_gest_tri2')
	    ->set_checked($this->type_sort ? true : false);
	    
	    $element = $interface_content_form->add_element('order_sort', 'order_sort_facette');
	    $element->set_display_nodes_separator('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
	    $element->add_input_node('radio', '0')
	    ->set_label_code('intit_gest_tri3')
	    ->set_checked(!$this->order_sort ? true : false);
	    $element->add_input_node('radio', '1')
	    ->set_label_code('intit_gest_tri4')
	    ->set_checked($this->order_sort ? true : false);
	    
	    $element = $interface_content_form->add_element('datatype_sort', 'datatype_sort_facette');
	    $element->set_display_nodes_separator('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
	    $element->add_input_node('radio', 'alpha')
	    ->set_label_code('datatype_sort_alpha')
	    ->set_checked(!$this->datatype_sort || $this->datatype_sort == 'alpha' ? true : false);
	    $element->add_input_node('radio', 'num')
	    ->set_label_code('datatype_sort_num')
	    ->set_checked($this->datatype_sort == 'num' ? true : false);
	    $element->add_input_node('radio', 'date')
	    ->set_label_code('datatype_sort_date')
	    ->set_checked($this->datatype_sort == 'date' ? true : false);
	        
	    $interface_content_form->add_element('list_nb', 'list_nbMax_form_facette')
	    ->add_input_node('number', $this->nb_result);
	    
	    $interface_content_form->add_element('limit_plus', 'facette_limit_plus_form')
	    ->add_input_node('number', $this->limit_plus);
	    
	    if($this->is_external) {
    	    $interface_content_form->add_element('visible_gestion', 'facettes_admin_check_visible_gestion', 'flat')
    	    ->add_input_node('boolean', $this->visible_gestion);
	    }
	    if (!empty($this->num_facettes_set)) {
	        //Visible en gestion
	        $interface_content_form->add_element('visible', 'facettes_admin_check_visible', 'flat')
	        ->add_input_node('boolean', $this->visible);
	    } else {
	        //Visible à l'OPAC
	        $interface_content_form->add_element('visible', 'facettes_admin_check_visible_opac', 'flat')
	        ->add_input_node('boolean', $this->visible);
	    }
	    
	    if($pmb_opac_view_activate) {
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
	        $interface_content_form->add_element('opac_views_num', 'admin_opac_facette_opac_views')
	        ->add_html_node($select_view);
	    }
	    
	    return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg,$charset;
		global $tpl_js_form_facette;
		global $sub;
		
		$interface_form = new interface_form('facette_form');
		$url_base = $interface_form->get_url_base().'&type='.$this->type;
		if (!empty($this->num_facettes_set)) {
		    $url_base .= "&num_facettes_set=".$this->num_facettes_set;
		}
		$interface_form->set_url_base($url_base);
		if(!$this->id){
		    $interface_form->set_label($msg['lib_nelle_facette_form']);
		}else{
		    $interface_form->set_label($msg['update_facette']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg(sprintf($msg['label_alert_delete_facette'],htmlentities($this->name,ENT_QUOTES,$charset)))
		->set_content_form($this->get_authperso_selector().$this->get_content_form())
		->set_table_name(static::$table_name)
		->set_field_focus('label_facette');
		$display = $tpl_js_form_facette;
		$display .=	$interface_form->get_display();
		
		$display = str_replace('!!sub!!', $sub, $display);
		$display = str_replace('!!type!!', $this->type, $display);
		$display = str_replace('!!id!!', htmlentities($this->id,ENT_QUOTES,$charset), $display);
		
		return $display;
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
		global $num_facettes_set;
		
		$this->name = stripslashes($label_facette);
		$this->crit = intval($list_crit);
		$this->ss_crit = intval($list_ss_champs);
		$this->nb_result = intval($list_nb);
		$this->visible_gestion = intval($visible_gestion);
		$this->visible = intval($visible);
		$this->type_sort = intval($type_sort);
		$this->order_sort = intval($order_sort);
		$this->datatype_sort = stripslashes($datatype_sort);
		$this->limit_plus = intval($limit_plus);
		$this->opac_views_num = '';
		if($pmb_opac_view_activate) {
			if (is_array($opac_views_num) && count($opac_views_num)) {
				if (!in_array("",$opac_views_num)) {
					$this->opac_views_num = implode(",", $opac_views_num);
				}
			}
		}
		$this->num_facettes_set = intval($num_facettes_set);
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
			facette_opac_views_num='".$this->opac_views_num."',
            num_facettes_set='".$this->num_facettes_set."'	
			".$clause;
		pmb_mysql_query($query);
		if(!$this->id) {
			$this->id = pmb_mysql_insert_id();
		}
		//sauvegarde dans les vues..
		if ($pmb_opac_view_activate) {
			$this->save_view_facette();
		}
		$translation = new translation($this->id, static::$table_name);
		$translation->update("facette_name", "label_facette");
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
		$views = array();
		$req = "select opac_view_id from opac_views";
		$myQuery = pmb_mysql_query($req);
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
					$myQuery = pmb_mysql_query($query);
					$param = array();
					if ($myQuery && pmb_mysql_num_rows($myQuery)) {
						while ($row = pmb_mysql_fetch_object($myQuery)) {
							$param = unserialize($row->opac_filter_param);
							if (!in_array($this->id, $param["selected"])) {
								$param["selected"][] = $this->id;
								$param=addslashes(serialize($param));
								$requete="update opac_filters set opac_filter_param='$param' where opac_filter_view_num=".$view_selected." and opac_filter_path='".static::$table_name."'";
								pmb_mysql_query($requete);
							}
						}
					} else {
						$param["selected"][] = $this->id;
						$param=addslashes(serialize($param));
						$requete="insert into opac_filters set opac_filter_view_num=".$view_selected.",opac_filter_path='".static::$table_name."', opac_filter_param='$param' ";
						pmb_mysql_query($requete);
					}
				}
			}
			if (isset($views["unselected"]) && count($views["unselected"])) {
				foreach ($views["unselected"] as $view_unselected) {
					$query="select opac_filter_param FROM opac_filters where opac_filter_view_num=".$view_unselected." and  opac_filter_path='".static::$table_name."' ";
					$myQuery = pmb_mysql_query($query);
					$param = array();
					if ($myQuery && pmb_mysql_num_rows($myQuery)) {
						while ($row = pmb_mysql_fetch_object($myQuery)) {
							$param = unserialize($row->opac_filter_param);
							if ($key = array_search($this->id, $param["selected"])) {
								array_splice($param["selected"], $key, 1);
								$param=addslashes(serialize($param));
								$requete="update opac_filters set opac_filter_param='$param' where opac_filter_view_num=".$view_unselected." and opac_filter_path='".static::$table_name."'";
								pmb_mysql_query($requete);
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
	
	public function set_num_facettes_set($num_facettes_set) {
	    $this->num_facettes_set = $num_facettes_set;
	}
	
	protected function get_authperso_selector() {
	    return "";
	}
}

