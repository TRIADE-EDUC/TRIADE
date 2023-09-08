<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_bannettes_ui.class.php,v 1.7 2019-06-06 13:05:45 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($include_path.'/templates/list/bannettes/list_bannettes_ui.tpl.php');
require_once($base_path."/dsi/func_common.inc.php");

class list_bannettes_ui extends list_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$query = 'select id_bannette, nom_bannette, proprio_bannette, comment_public FROM bannettes ';
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = new bannette($row->id_bannette);
	}
	
	protected function _get_query_order() {
		if ($this->applied_sort['by']) {
			$order = '';
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				case 'label':
				case 'name':
					$order .= 'nom_bannette, comment_public';
					break;
				case 'comment_public':
					$order .= 'comment_public';
					break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				if($this->applied_sort['asc_desc'] == 'desc' && strpos($order, ',')) {
					$cols = explode(',', $order);
					$query_order = " order by ";
					foreach ($cols as $i=>$col) {
						if($i) {
							$query_order .= ","; 
						}
						$query_order .= " ".$col." ".$this->applied_sort['asc_desc'];
					}
					return $query_order;
				} else {
					return " order by ".$order." ".$this->applied_sort['asc_desc'];
				}
			} else {
				return "";
			}
		}
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'name',
				'asc_desc' => 'asc'
		);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'label' => 'dsi_ban_form_nom',
						'equations' => 'dsi_ban_list_equ',
						'number_records' => 'dsi_ban_nb_notices',
						'number_subscribed' => 'dsi_ban_nb_abonnes',
						'send_last_date' => 'dsi_ban_date_last_envoi',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('label');
		$this->add_column('equations');
		$this->add_column('number_records');
		$this->add_column('number_subscribed');
		$this->add_column('send_last_date');
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
		
		$name = $this->objects_type.'_name';
		global ${$name};
		if(isset(${$name}) && ${$name} != '') {
			$this->filters['name'] = ${$name};
		}
		if(isset($id_classement)) {
			$this->filters['id_classement'] = $id_classement;
		}
		$type = $this->objects_type.'_type';
		global ${$type};
		if(isset(${$type})) {
			$this->filters['type'] = ${$type};
		}
		parent::set_filters_from_form();
	}
		
	public function get_export_icons() {
		return "";
	}
	
	protected function get_search_filter_name() {
		global $msg;
		
		return "<input class='saisie-20em' id='".$this->objects_type."_name' type='text' name='".$this->objects_type."_name' value=\"".$this->filters['name']."\" title='$msg[3000]' />";
	}
	
	protected function get_search_filter_id_classement() {
		return gen_liste_classement("BAN", $this->filters['id_classement'], "this.form.submit();");
	}
	
	protected function get_search_filter_type() {
		global $msg, $charset;
		
		return "<select name='".$this->objects_type."_type'>
					<option value='0' ".(!$this->filters['type'] ? "selected='selected'" : "").">".htmlentities($msg['dsi_all_types'], ENT_QUOTES, $charset)."</option>
					<option value='1' ".($this->filters['type'] == 1 ? "selected='selected'" : "").">".htmlentities($msg['dsi_menu_ban_pro'], ENT_QUOTES, $charset)."</option>
					<option value='2' ".($this->filters['type'] == 2 ? "selected='selected'" : "").">".htmlentities($msg['dsi_menu_ban_abo'], ENT_QUOTES, $charset)."</option>
				</select>";
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $base_path, $categ, $sub;
		
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', $base_path.'/dsi.php?categ='.$categ.'&sub='.$sub, $search_form);
		return $search_form;
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_hidden_form() {
		global $base_path, $categ, $sub;
	
		$search_form = parent::get_search_hidden_form();
		$search_form = str_replace('!!action!!', $base_path.'/dsi.php?categ='.$categ.'&sub='.$sub, $search_form);
	
		return $search_form;
	}
		
	protected function get_button_add() {
		global $msg;
	
		return "<input class='bouton' type='button' value='".$msg['ajouter']."' onClick=\"document.location='".static::get_controller_url_base().'&suite=add'."';\" />";
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		global $sub;
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($sub == 'lancer') {
			$filters [] = '(DATE_ADD(date_last_envoi, INTERVAL periodicite DAY) <= sysdate())';
		}
		if($this->filters['auto'] !== '') {
			$filters [] = 'bannette_auto = "'.$this->filters['auto'].'"';
		}
		if($this->filters['id_classement']) {
			$filters [] = 'num_classement = "'.$this->filters['id_classement'].'"';
		} elseif($this->filters['id_classement'] === 0) {
			$filters [] = 'num_classement = "0"';
		}
		if($this->filters['name']) {
			$filters [] = 'nom_bannette like "%'.str_replace("*", "%", addslashes($this->filters['name'])).'%"';
		}
		if($this->filters['num_empr'] != '') {
			$filters [] = 'num_empr = "'.$this->filters['num_empr'].'"';
		}
		if($this->filters['proprio_bannette'] !== '') {
			$filters [] = 'proprio_bannette = "'.$this->filters['proprio_bannette'].'"';
		}
		if($this->filters['type']) {
			switch ($this->filters['type']) {
				case 1:
					$filters [] = 'proprio_bannette = 0';
					break;
				case 2:
					$filters [] = 'proprio_bannette != 0';
					break;
			}
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);		
		}
		return $filter_query;
	}
	
	protected function _get_label_cell_header($name) {
		global $msg, $charset;
		global $current_module;
	
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

	protected static function get_equations($id_bannette) {
	    global $msg;
	    
		$requete = "select id_equation, num_classement, nom_equation, comment_equation, proprio_equation, num_bannette from equations, bannette_equation where num_equation=id_equation and proprio_equation=0 and num_bannette='".$id_bannette."' order by nom_equation " ;
		$resequ = pmb_mysql_query($requete);
		$equ_trouvees =  pmb_mysql_num_rows($resequ) ;
		$equations = "" ;
		while ($equa=pmb_mysql_fetch_object($resequ)) {
			$equations .= "<li>".$equa->nom_equation."</li>";
		}
		if($equ_trouvees == 0) {
			$equations .= $msg['dsi_ban_no_equ'];
		} else {
			$equations = "<ul>".$equations."</ul>";
		}
		return $equations;
	}
	
	/**
	 * Fonction de callback
	 * @param account $a
	 * @param account $b
	 */
	protected function _compare_objects($a, $b) {
		if($this->applied_sort['by']) {
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				case 'equations' :
					return strcmp(strip_tags(static::get_equations($a->id_bannette)), strip_tags(static::get_equations($b->id_bannette)));
					break;
				case 'number_records':
					return $this->intcmp($a->nb_notices, $b->nb_notices);
					break;
				case 'number_subscribed':
					return $this->intcmp($a->nb_abonnes, $b->nb_abonnes);
					break;
				case 'send_last_date':
					return strcmp($a->aff_date_last_envoi, $b->aff_date_last_envoi);
					break;
				default :
					return parent::_compare_objects($a, $b);
					break;
			}
		}
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		global $sub;
		
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'bannettes', $display);
		$display = str_replace('!!sub!!', $sub, $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
	
		$content = '';
		switch($property) {
			case 'label':
				$content .= "
					<strong>".htmlentities($object->nom_bannette,ENT_QUOTES, $charset)."</strong>
					<strong>(".htmlentities($object->nom_classement,ENT_QUOTES, $charset).")</strong>
					<ul>
						<em>".htmlentities($object->comment_gestion,ENT_QUOTES, $charset)."</em>
					</ul>";
				break;
			case 'equations':
				$content .= static::get_equations($object->id_bannette);
				break;
			case 'number_records':
				$content .= $object->nb_notices;
				break;
			case 'number_subscribed':
				$content .= $object->nb_abonnes;
				if ($object->num_panier) {
					$content .= "&nbsp;&nbsp;<img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' />";
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
	
	protected function get_display_cell($object, $property) {
		$td_javascript="";
		$td_style="";
		switch($property) {
			case 'label':
				$td_javascript.=" onclick=\"document.location='".static::get_controller_url_base()."&id_bannette=".$object->id_bannette;
				$td_javascript.="&suite=acces";
				break;
			case 'equations':
				$td_javascript.=" onclick=\"document.location='".static::get_controller_url_base()."&id_bannette=".$object->id_bannette;
				$td_javascript.="&suite=affect_equation";
				break;
			case 'number_subscribed':
				$td_javascript.=" onclick=\"document.location='".static::get_controller_url_base()."&id_bannette=".$object->id_bannette;
				$td_javascript.="&suite=affect_lecteurs";
				break;
			case 'send_last_date':
				$td_javascript.=" sorttable_customkey='".$object->date_last_envoi."' ";
				$td_javascript.=" onclick=\"document.location='./dsi.php?categ=diffuser&sub=auto&id_bannette=".$object->id_bannette;
				break;
		}
		if($td_javascript) {
			//$td_javascript .= "&page=".$this->pager['page']."&nbr_lignes=".$this->pager['nb_results']."&nb_per_page=".$this->pager['nb_per_page'];
			$td_javascript.="';\" ";
			$td_style .= " cursor: pointer;";
		}
		$display = "<td style='vertical-align:top; ".$td_style."' ".$td_javascript.">".$this->get_cell_content($object, $property)."</td>";
		return $display;
	}
	
	public function set_pager_in_session() {
		$_SESSION['list_'.$this->objects_type.'_pager']['page'] = $this->pager['page'];
		parent::set_pager_in_session();
	}
	
	public static function get_controller_url_base() {
		global $base_path, $sub;
	
		return $base_path.'/dsi.php?categ=bannettes&sub='. $sub;
	}
}