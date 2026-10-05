<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_bannettes_ui.class.php,v 1.37.2.1.2.1 2025/02/20 15:31:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once($class_path."/equation.class.php");
require_once($class_path."/classements.class.php");
require_once($include_path.'/templates/list/bannettes/list_bannettes_ui.tpl.php');
require_once($base_path."/dsi/func_common.inc.php");

class list_bannettes_ui extends list_ui {
	
	protected static $equations = array();
	
	protected function _get_query_base() {
		$query = 'select id_bannette, nom_bannette, proprio_bannette, comment_public FROM bannettes ';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new bannette($row->id_bannette);
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'name':
	            return 'nom_bannette, comment_public';
	        case 'comment_public':
	            return 'comment_public';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'name' => 'dsi_ban_form_nom',
						'equations' => 'dsi_ban_list_equ',
						'number_records' => 'dsi_ban_nb_notices',
						'number_subscribed' => 'dsi_ban_nb_abonnes',
						'send_last_date' => 'dsi_ban_date_last_envoi',
						'nom_classement' => 'dsi_clas_type_class_BAN'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('name');
		$this->add_column('equations');
		$this->add_column('number_records');
		$this->add_column('number_subscribed');
		$this->add_column('send_last_date');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_selection_actions('delete', 'visible', false);
		$this->set_setting_column('name', 'align', 'left');
		$this->set_setting_column('equations', 'align', 'left');
		$this->set_setting_column('number_records', 'datatype', 'integer');
		$this->set_setting_column('number_subscribed', 'datatype', 'integer');
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'name' => 'dsi_ban_search_nom',
						'id_classement' => 'dsi_classement',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'auto' => '',
				'id_classement' => '',
				'name' => '',
				'proprio_bannette' => '',
				'type' => '',
		        'num_empr' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('name');
		$this->add_selected_filter('id_classement');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $id_classement;
		
		$this->set_filter_from_form('name');
		if(isset($id_classement)) {
			$this->filters['id_classement'] = $id_classement;
		}
		$this->set_filter_from_form('type', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_name() {
		return $this->get_search_filter_simple_text('name');
	}
	
	protected function get_search_filter_id_classement() {
		return gen_liste_classement("BAN", $this->filters['id_classement'], "this.form.submit();");
	}
	
	protected function get_search_filter_type() {
		global $msg;
		
		$options = array(
				0 => $msg['dsi_all_types'],
				1 => $msg['dsi_menu_ban_pro'],
				2 => $msg['dsi_menu_ban_abo']
		);
		return $this->get_search_filter_simple_selection('', 'type', '', $options);
	}
	
	protected function get_button_add() {
		global $msg;
	
		return $this->get_interface_button($msg['ajouter'], ['location' => static::get_controller_url_base().'&suite=add']);
	}
	
	protected function _add_query_filters() {
		global $sub;
		
		if($sub == 'lancer') {
			$this->query_filters [] = '(DATE_ADD(date_last_envoi, INTERVAL periodicite DAY) <= sysdate())';
		}
		if($this->filters['auto'] !== '') {
			$this->query_filters [] = 'bannette_auto = "'.$this->filters['auto'].'"';
		}
		if($this->filters['id_classement']) {
			$this->query_filters [] = 'num_classement = "'.$this->filters['id_classement'].'"';
		} elseif($this->filters['id_classement'] === 0) {
			$this->query_filters [] = 'num_classement = "0"';
		}
		if($this->filters['name']) {
			$this->query_filters [] = 'nom_bannette like "%'.str_replace("*", "%", addslashes($this->filters['name'])).'%"';
		}
		if($this->filters['num_empr'] != '') {
			$this->query_filters [] = 'num_empr = "'.$this->filters['num_empr'].'"';
		}
		if($this->filters['proprio_bannette'] !== '') {
			$this->query_filters [] = 'proprio_bannette = "'.$this->filters['proprio_bannette'].'"';
		}
		if($this->filters['type']) {
			switch ($this->filters['type']) {
				case 1:
					$this->query_filters [] = 'proprio_bannette = 0';
					break;
				case 2:
					$this->query_filters [] = 'proprio_bannette != 0';
					break;
			}
		}
	}
	
	protected function _get_label_cell_header($name) {
		global $msg, $charset;
	
		switch ($name) {
			case 'dsi_ban_form_nom':
				return 
					"<strong>".htmlentities($msg['dsi_ban_form_nom'],ENT_QUOTES, $charset)."</strong>
					(".htmlentities($msg['dsi_classement'],ENT_QUOTES, $charset).")
					<br />
						".htmlentities($msg['dsi_ban_form_com_gestion'],ENT_QUOTES, $charset)."
					";
			case 'dsi_ban_date_last_envoi':
				return "<strong>".htmlentities($msg['dsi_ban_date_last_envoi'],ENT_QUOTES, $charset)."</strong>
					<br />(".htmlentities($msg['dsi_ban_date_last_remp'],ENT_QUOTES, $charset).")";
			default:
				return "<strong>".parent::_get_label_cell_header($name)."</strong>";
				
		}
		
	}

	protected static function get_equations($id_bannette, $proprio_bannette=0) {
		if(!isset(static::$equations[$id_bannette])) {
		    $requete = "select id_equation, num_classement, nom_equation, comment_equation, proprio_equation, num_bannette from equations, bannette_equation where num_equation=id_equation and proprio_equation='".$proprio_bannette."' and num_bannette='".$id_bannette."' order by nom_equation " ;
			$resequ = pmb_mysql_query($requete);
			static::$equations[$id_bannette] = array();
			while ($equa=pmb_mysql_fetch_object($resequ)) {
				$eq_form= new equation($equa->id_equation) ;
				static::$equations[$id_bannette][] = $equa->nom_equation.($proprio_bannette ? $eq_form->make_hidden_search_form("","PRI", $proprio_bannette) : '');
			}
		}
		return static::$equations[$id_bannette];
	}
	
	protected static function get_formatted_equations($id_bannette, $proprio_bannette=0) {
		global $msg;
		
		$formatted_equations = "";
		$equations = static::get_equations($id_bannette, $proprio_bannette);
		if(count($equations) == 0) {
			$formatted_equations .= $msg['dsi_ban_no_equ'];
		} else {
			$formatted_equations = "<ul><li>".implode('</li><li>', $equations)."</li></ul>";
		}
		return $formatted_equations;
	}
	
	protected function _get_object_property_equations($object) {
		return implode(' ', static::get_equations($object->id_bannette));
	}
	
	protected function _get_object_property_number_records($object) {
		return $object->nb_notices;
	}
	
	protected function _get_object_property_number_subscribed($object) {
		return $object->nb_abonnes;
	}
	
	protected function _get_object_property_send_last_date($object) {
		return $object->aff_date_last_envoi;
	}
	
	protected function _get_object_property_nom_classement($object) {
		$classement = classement::get_instance($object->num_classement);
		return $classement->nom_classement;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
	
		$content = '';
		switch($property) {
			case 'name':
				$content .= "
					<strong>".htmlentities($object->nom_bannette,ENT_QUOTES, $charset)."</strong>
					<strong>(".htmlentities($object->nom_classement,ENT_QUOTES, $charset).")</strong>
					<ul>
						<em>".htmlentities($object->comment_gestion,ENT_QUOTES, $charset)."</em>
					</ul>";
				break;
			case 'equations':
				$equations = static::get_equations($object->id_bannette, $object->proprio_bannette);
				if(count($equations) == 0) {
					$content .= $msg['dsi_ban_no_equ'];
				} else {
					$content = "<ul><li>".implode('</li><li>', $equations)."</li></ul>";
				}
				break;
			case 'number_subscribed':
				$content .= $object->nb_abonnes;
				if ($object->num_panier) {
					$content .= "&nbsp;&nbsp;<img src='".get_url_icon('basket_small_20x20.gif')."' title='".htmlentities($msg['400'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['400'], ENT_QUOTES, $charset)."' style='border:0px' class='center' />";
				}
				break;
			case 'send_last_date':
				$content .= "<strong>".htmlentities($object->aff_date_last_envoi,ENT_QUOTES, $charset)."</strong>";
				if ($object->alert_diff) {
					$content .= "<br /><span style='color:red'>(".htmlentities($object->aff_date_last_remplissage,ENT_QUOTES, $charset).")</span>";
				} else {
					$content .= "<br />(".htmlentities($object->aff_date_last_remplissage,ENT_QUOTES, $charset).")" ;
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$onclick="";
		switch($property) {
			case 'name':
			case 'nom_classement':
				$onclick = "document.location=\"".static::get_controller_url_base()."&id_bannette=".$object->id_bannette."&suite=acces\";";
				break;
			case 'equations':
				$onclick = "document.location=\"".static::get_controller_url_base()."&id_bannette=".$object->id_bannette."&suite=affect_equation\";";
				break;
			case 'number_subscribed':
				$onclick = "document.location=\"".static::get_controller_url_base()."&id_bannette=".$object->id_bannette."&suite=affect_lecteurs\";";
				break;
			case 'send_last_date':
				$onclick = " document.location=\"./dsi.php?categ=diffuser&sub=auto&id_bannette=".$object->id_bannette."\";";
				break;
		}
		return array(
				'style' => 'vertical-align:top;',
				'onclick' => $onclick,
		);
	}
	
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'id_classement':
				return "select nom_classement from classements where id_classement=".$this->filters[$property];
		}
		return '';
	}
	
	protected function _get_query_human() {
		global $msg;
		
		$humans = $this->_get_query_human_main_fields();
		if($this->filters['type']) {
			$type_label = '';
			switch ($this->filters['type']) {
				case 1:
					$type_label = $msg['dsi_menu_ban_pro'];
					break;
				case 2:
					$type_label = $msg['dsi_menu_ban_abo'];
					break;
			}
			$humans['type'] = $this->_get_label_query_human($msg['dsi_bannette_type'], $type_label);
		}
		return $this->get_display_query_human($humans);
	}
	
	public function set_pager_in_session() {
		$_SESSION['list_'.$this->objects_type.'_pager']['page'] = $this->pager['page'];
		parent::set_pager_in_session();
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$delete_link = array(
				'href' => static::get_controller_url_base()."&action=list_delete",
				'confirm' => $msg['confirm_suppr']
		);
		$this->add_selection_action('delete', $msg['63'], 'interdit.gif', $delete_link);
	}
	
	public static function delete_object($id) {
		$id = intval($id);
		$bannette = new bannette($id);
		$bannette->delete();
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module, $sub;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=bannettes&sub='.$sub;
	}
}