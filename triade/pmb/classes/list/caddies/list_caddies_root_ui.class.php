<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_caddies_root_ui.class.php,v 1.20.2.4.2.1 2025/02/20 15:31:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_caddies_root_ui extends list_ui {
	
	protected $caddie_object_type;
	
	protected $lien_origine;
	
	protected $action_click;
	
	protected $item;
	
	protected static $lien_edition;
	
	protected $lien_suppr;
	
	protected static $lien_creation;
	
	protected $nocheck;
	
	protected $lien_pointage;
	
	protected $from_item;
	
	protected $script_submit;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		if(!empty($filters['display_mode'])) {
			$this->init_display_mode($filters['display_mode']);
		}
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function init_display_mode($display_mode) {
		switch ($display_mode) {
			case 'editable':
				$this->item = 0;
				static::$lien_edition = 1;
				static::$lien_creation = 1;
				$this->nocheck = false;
				$this->lien_pointage = 0;
				break;
			case 'in_cart':
				static::$lien_edition = 0;
				static::$lien_creation = 0;
				$this->nocheck = false;
				$this->lien_pointage = 0;
				break;
			case 'display':
			default:
				$this->item = 0;
				static::$lien_edition = 0;
				static::$lien_creation = 1;
				$this->nocheck = false;
				$this->lien_pointage = 0;
				break;
		}
	}
	
	protected function _get_query_base() {
		$model_class_name = static::$model_class_name;
		$query = $model_class_name::get_query_cart_list($this->filters['type'], 0, 0, false);
		return $query;
	}
	
	protected function get_object_instance($row) {
	    global $idcaddie_new;
	    
	    $idcaddie = $row->{static::$field_name};
	    if (!empty($idcaddie_new) && ($idcaddie_new == $idcaddie)) {
	        $this->script_submit =  "
					<script>
						if(document.getElementById('id_".$idcaddie."')) {
							document.getElementById('id_".$idcaddie."').checked=true;
							if(document.forms['print_options']) {
								document.forms['print_options'].submit();
							}
						}
					</script>";
	    }
	    return new static::$model_class_name($idcaddie);
	}
	
	protected function add_object($row) {
	    global $PMBuserid;
	    
	    $rqt_autorisation=explode(" ",$row->autorisations);
	    if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $row->autorisations_all || $PMBuserid==1) {
	        parent::add_object($row);
	    }
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function get_classement_instance($object) {
		$model_class_name = static::$model_class_name;
		return new classementGen($model_class_name::$table_name, $object->get_idcaddie());
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'display_mode' => '',
				'type' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'type', 1 => 'classement_label');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = array(
			'main_fields' => array(
					'name' => '103',
					'pointed_unpointed' => 'pointed_unpointed',
					'edition_and_actions' => 'caddie_menu_action',
					'classement_label' => 'classementGen_list_title',
					'classement_selector' => 'classementGen_list_form_title'
			),
		);
	}
	
	/**
	 * Initialisation des colonnes éditables disponibles
	 */
	protected function init_available_editable_columns() {
	    $this->available_editable_columns = array(
	        'classement_selector',
	    );
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('name');
		$this->add_column('pointed_unpointed');
		$this->add_column('edition_and_actions');
		if(static::$lien_creation) {
			$this->add_column('classement_selector');
		}
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		global $deflt_catalog_expanded_caddies;
		
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('classement_selector', 'edition_type', 'authority');
		$this->set_setting_column('classement_selector', 'edition_completion', static::$model_class_name.'_classement');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['default']['expanded_display'] = $deflt_catalog_expanded_caddies;
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'name', 'pointed_unpointed', 'title_infopage',
				'edition_and_actions', 'classement_selector'
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('name');
		$this->add_applied_sort('comment');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'name':
	            return $sort_by.", comment";
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {

		parent::set_filters_from_form();
	}
		
	public function get_display_search_form() {
	    //Ne pas retourner le formulaire car non compatible avec l'ajout d'éléments dans un panier
	    //#98177 : La liste des paniers doivent rester dans le formulaire print_options
	    return '';
	}
	
	protected function get_grouped_label($object, $property) {
		global $msg;
		
		$grouped_label = '';
		switch($property) {
			case 'type':
				$grouped_label = "<b>".$msg["caddie_de_".$object->type]."</b>";
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}

	protected function get_cell_content_link_name($object) {
		global $action, $base_path, $current_module, $msg;
		
		$content = '';
		if($this->item && $action!="save_cart" && $action!="del_cart") {
			$content .= (!$this->nocheck?"<input type='checkbox' id='id_".$object->get_idcaddie()."' name='caddie[".$object->get_idcaddie()."]' value='".$object->get_idcaddie()."'>":"")."&nbsp;";
			if(!$this->nocheck){
				$content.=  "<a href='#' onclick='javascript:document.getElementById(\"id_".$object->get_idcaddie()."\").checked=true;document.forms[\"print_options\"].submit();'>";
			} else {
				if ($this->lien_pointage) {
					$content.=  "<a href='#' onclick='javascript:document.getElementById(\"idcaddie\").value=".$this->item.";document.getElementById(\"idcaddie_selected\").value=".$object->get_idcaddie().";document.forms[\"print_options\"].submit();'>";
				} else {
					$content.=  "<a href='#' onclick='javascript:document.getElementById(\"idcaddie\").value=".$object->get_idcaddie().";document.forms[\"print_options\"].submit();'>";
				}
			}
		} else {
			if($this->from_item) {
				$content .= "
				<script type='text/javascript'>
					function ".$object->type."_delete_item(idcaddie,id_item) {
						var url = '".$base_path."/ajax.php?module=".$current_module."&categ=caddie&sub=list_from_item&action=delete&idcaddie='+idcaddie+'&object_type=".$object->type."&id_item='+id_item;
				 		var ajax_gestion=new http_request();
						ajax_gestion.request(url,0,'',1,".$object->type."_delete_item_callback,0,0);
					}
					function ".$object->type."_delete_item_callback(response) {
						var data = response;
						if(document.getElementById('".strtolower($object->type)."_caddie_".$this->from_item."_content')) {
							dojo.forEach(dijit.findWidgets(dojo.byId('".strtolower($object->type)."_caddie_".$this->from_item."_content')), function(w) {
								w.destroyRecursive();
							});
							if(typeof(data) != 'undefined') {
								document.getElementById('".strtolower($object->type)."_caddie_".$this->from_item."_content').innerHTML = data;
							} else {
								document.getElementById('".strtolower($object->type)."_caddie_".$this->from_item."_content').innerHTML = '';
							}
							dojo.parser.parse('".strtolower($object->type)."_caddie_".$this->from_item."_content');
						}
					}
				</script>
				<a onclick='".$object->type."_delete_item(".$object->get_idcaddie().",".$this->from_item.");' style='cursor:pointer;'>
					<img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" />
				</a>";
			}
			$link = $this->lien_origine."&action=".$this->action_click."&object_type=".$object->type."&idcaddie=".$object->get_idcaddie()."&item=".$this->item;
			$content.= "<a href='$link'>";
		}
		return $content;
	}
	
	protected function get_cell_content_edition_and_actions($object) {
		global $msg;
		global $action;
		
		if (static::$lien_edition) {
			$aff_lien = "<input type=button class=bouton value='$msg[caddie_editer]' onclick=\"document.location='".$this->lien_origine."&action=edit_cart&idcaddie=".$object->get_idcaddie()."';\" />";
		} else {
			$aff_lien = "";
		}
		if($this->item && $action != "save_cart" && $action != "del_cart") {
			return $aff_lien;
		} else {
			if (static::$lien_creation) {
				$model_class_name = static::$model_class_name;
				if(count($this->objects) < 200) {
				    $show_actions = $model_class_name::show_actions($object->get_idcaddie(), $object->type);
				} else {
				    $show_actions = $model_class_name::show_actions($object->get_idcaddie(), $object->type, 0);
				}
				return $aff_lien."&nbsp;".$show_actions.($object->acces_rapide ? " <img src='".get_url_icon('chrono.png')."' title='".$msg['caddie_fast_access']."'>":"");
			} else {
				return $aff_lien;
			}
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $PMBuserid;
		
		$content = '';
		switch($property) {
			case 'name':
				$content .= $this->get_cell_content_link_name($object);
				$content .= "<span ".($object->favorite_color != '#000000' ? "style='color:".$object->favorite_color."'" : "").">";
				$content .= "<strong>".$object->name."</strong>";
				if ($object->comment){
					$content.= "<br /><small>(".$object->comment.")</small>";
				}
				$content .= "</span>";
				$content .= "</a>";
				break;
			case 'pointed_unpointed':
				$content .= "<b>".$object->nb_item_pointe."</b>". $msg['caddie_contient_pointes']." / <b>".$object->nb_item."</b>";
				break;
			case 'edition_and_actions':
				$content .= $this->get_cell_content_edition_and_actions($object);
				break;
			case 'classement_selector':
				$classementGen = $this->get_classement_instance($object);
				if(count($this->objects) < 200) {
				    $content .= $classementGen->show_selector(static::get_controller_url_base(),$PMBuserid);
				} else {
				    $content .= $classementGen->show_input_completion(static::get_controller_url_base(),$PMBuserid);
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$class="list_ui_list_cell_content ".$this->objects_type."_list_cell_content".($property ? "_".$property : '')." ";
		switch($property) {
			case 'name':
				$class .= 'classement50';
				break;
			case 'pointed_unpointed':
				$class .= 'classement15';
				break;
			case 'edition_and_actions':
				$class .= 'classement20';
				break;
			case 'classement_selector':
				$class .= 'classement10';
				break;
		}
		return array(
				'class' => $class,
				
		);
	}
	
	public function get_error_message_empty_list() {
		global $msg;
		return $msg[398];
	}
	
	protected function get_button_add() {
		global $msg;
		
		return $this->get_interface_button($msg['new_cart'], ['location' => $this->lien_origine."&action=new_cart&object_type=".$this->caddie_object_type."&item=".$this->item]);
	}
	
	protected function get_current_tab() {
		global $sub, $quoi, $quelle, $moyen;
		
		$current_tab = '';
		switch($sub) {
			case "pointage" :
				$current_tab .= $sub."_".$moyen;
				break;
			case "action" :
				$current_tab .= $sub."_".$quelle;
				break;
			case "collecte" :
				$current_tab .= $sub."_".$moyen;
				break;
			case "gestion" :
			default:
				$current_tab .= $sub."_".$quoi;
				break;
		}
		return $current_tab;
	}
	
	protected function get_link_action($quoi, $action) {
		global $msg;
		global $sub;
		
		$href = static::get_controller_url_base();
		switch($sub) {
			case "pointage" :
				$href .= "&moyen=".$quoi;
				break;
			case "action" :
				$href .= "&quelle=".$quoi;
				break;
			case "collecte" :
				$href .= "&moyen=".$quoi;
				break;
			case "gestion" :
			default:
				$href .= "&quoi=".$quoi;
				break;
		}
		$href .= "&action=".$action;
		return array(
				'href' => $href,
				'confirm' => $msg['caddies_'.$action]
		);
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		if($this->filters['display_mode'] == 'editable' || static::$lien_edition) {
			$this->add_selection_action('delete', $msg['63'], 'interdit.gif', $this->get_link_action('panier', 'list_delete'));
		}
		if($this->get_current_tab() == 'action_supprpanier') {
			$this->add_selection_action('supprpanier', $msg['caddie_menu_action_supprpanier'], '', $this->get_link_action('supprpanier', 'list_supprpanier'));
		}
		if($this->filters['display_mode'] == 'editable' || static::$lien_edition) {
		    //Bouton modifier
		    /*$edit_link = array(
		        'showConfiguration' => static::get_controller_url_base()."&action=list_save"
		    );
		    $this->add_selection_action('edit', $msg['62'], 'b_edit.png', $edit_link);
			*/
		}
	}
	
	public function set_caddie_object_type($caddie_object_type) {
		$this->caddie_object_type = $caddie_object_type;
	}
	
	public function set_lien_origine($lien_origine) {
		$this->lien_origine = $lien_origine;
	}
	
	public function set_action_click($action_click) {
		$this->action_click = $action_click;
	}
	
	public function set_item($item) {
		$this->item = intval($item);
	}
	
	public static function set_lien_edition($lien_edition) {
		static::$lien_edition = intval($lien_edition);
	}
	
	public function set_lien_suppr($lien_suppr) {
		$this->lien_suppr = intval($lien_suppr);
	}
	
	public static function set_lien_creation($lien_creation) {
		static::$lien_creation = intval($lien_creation);
	}
	
	public function set_nocheck($nocheck) {
		$this->nocheck = $nocheck;
	}
	
	public function set_lien_pointage($lien_pointage) {
		$this->lien_pointage = intval($lien_pointage);
	}
	
	public function set_from_item($from_item) {
		$this->from_item = intval($from_item);
	}
	
	public function get_script_submit() {
		if(!isset($this->script_submit)) {
			$this->script_submit = '';
		}
		return $this->script_submit;
	}
	
	protected function save_object($object, $property, $value) {
	    if (is_object($object)) {
	        switch ($property) {
	            case 'classement_selector':
	                $this->get_classement_instance($object)->saveLibelle($value);
	                break;
	        }
	    }
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=caddies';
	}
	
	public static function run_action_list($action='') {
		$selected_objects = static::get_selected_objects();
		if(count($selected_objects)) {
			foreach ($selected_objects as $id) {
				$model_class_name = static::$model_class_name;
				$model_class_instance = new $model_class_name($id);
				if ($model_class_name::check_rights($id)) {
					switch ($action) {
						case 'list_delete':
							$model_class_instance->delete();
							break;
						case 'list_supprpanier':
							$elt_flag = $elt_flag_inconnu = 1;
							$elt_no_flag = $elt_no_flag_inconnu = 1;
							if ($elt_flag) $model_class_instance->del_item_flag($elt_flag_inconnu);
							if ($elt_no_flag) $model_class_instance->del_item_no_flag($elt_no_flag_inconnu);
							break;
					}
				}
			}
		}
	}
}