<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_records_list_ui.class.php,v 1.14.2.1 2024/12/30 11:01:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/elements_list/elements_list_ui.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste de notices
 * @author vtouchard
 *
 */
class elements_records_list_ui extends elements_list_ui {
	
	protected $level;
	
	protected static $link_initialized;
	
	protected static $link;
	protected static $link_expl;
	protected static $link_explnum;
	protected static $link_serial;
	protected static $link_analysis;
	protected static $link_bulletin;
	protected static $link_explnum_serial;
	protected static $link_explnum_analysis;
	protected static $link_explnum_bulletin;
	protected static $link_notice_bulletin;
	protected static $link_delete_cart;
	
	protected $show_expl;
	protected $show_resa;
	protected $show_explnum;
	protected $show_statut;
	protected $show_opac_hidden_fields;
	protected $show_resa_planning;
	protected $show_map;
	protected $show_abo_actif;
	
	protected $print;
	protected $button_explnum;
	protected $anti_loop;
	protected $draggable;
	protected $no_link;
	protected $ajax_mode;
	protected static $lazy_loading = false;
	
	public function __construct($contents, $nb_results, $mixed, $groups=array(), $nb_filtered_results = 0) {
		static::init_links();
		$this->init_shows();
		$this->init_options();
		parent::__construct($contents, $nb_results, $mixed, $groups, $nb_filtered_results);
	}
	
	protected static function init_links() {
		if(!isset(static::$link_initialized)) {
			static::$link = notice::get_pattern_link();
			static::$link_expl = exemplaire::get_pattern_link();
			static::$link_explnum = explnum::get_pattern_link();
			static::$link_serial = serial::get_pattern_link();
			static::$link_analysis = analysis::get_pattern_link();
			static::$link_bulletin = bulletinage::get_pattern_link();
			static::$link_explnum_serial = "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!";
			static::$link_explnum_analysis = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
			static::$link_explnum_bulletin = "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=!!bul_id!!&explnum_id=!!explnum_id!!";
			static::$link_notice_bulletin = bulletinage::get_pattern_link();
			static::$link_delete_cart = '';
			static::$link_initialized = 1;
		}
	}
	
	protected function init_shows() {
		$this->show_expl = 1;
		$this->show_resa = 1;
		$this->show_explnum = 1;
		$this->show_statut = 1;
		$this->show_opac_hidden_fields = true;
		$this->show_resa_planning = 1;
		$this->show_map = 1;
		$this->show_abo_actif = 0;
	}
	
	protected function init_options() {
		$this->print = 0;
		$this->button_explnum = 0;
		$this->anti_loop = array();
		$this->draggable = 1;
		$this->no_link = false;
		$this->ajax_mode = 1;
	}
	
	protected function generate_element($element_id, $recherche_ajax_mode=0){
	    $element_id = intval($element_id);
	    if(!$element_id) {
	        return '';
	    }
		$result = pmb_mysql_query("SELECT niveau_biblio FROM notices WHERE notice_id=".$element_id);
		$niveau_biblio = pmb_mysql_result($result, 0, 'niveau_biblio');
		if (static::$lazy_loading) {
		    $recherche_ajax_mode = 1;
		}
		switch($niveau_biblio) {
			case 'm' :
				// notice de monographie
				$display = new mono_display($element_id, $this->get_level(), static::get_link(), $this->show_expl, static::get_link_expl(), static::get_link_delete_cart(), static::get_link_explnum(),$this->show_resa, $this->print, $this->show_explnum, $this->show_statut, $this->anti_loop, $this->draggable, $this->no_link, $this->show_opac_hidden_fields,($this->ajax_mode ? $recherche_ajax_mode : 0), $this->show_resa_planning, $this->show_map, 0, $this->context_parameters);
				break;
			case 's' :
				// on a affaire à un périodique
				$display = new serial_display($element_id, $this->get_level(), static::get_link_serial(), static::get_link_analysis(), static::get_link_bulletin(), static::get_link_delete_cart(), static::get_link_explnum_serial(), $this->button_explnum, $this->print, $this->show_explnum, $this->show_statut, $this->show_opac_hidden_fields, $this->draggable,($this->ajax_mode ? $recherche_ajax_mode : 0), $this->anti_loop, $this->no_link, $this->show_map, 0, $this->show_abo_actif, $this->show_expl, $this->context_parameters);
				break;
			case 'a' :
				// on a affaire à un article
				// function serial_display ($id, $level='1', $action_serial='', $action_analysis='', $action_bulletin='', $lien_suppr_cart="", $lien_explnum="", $bouton_explnum=1,$print=0,$show_explnum=1, $show_statut=0, $show_opac_hidden_fields=true, $draggable=0 ) {
				$display = new serial_display($element_id, $this->get_level(), static::get_link_serial(), static::get_link_analysis(), static::get_link_bulletin(), static::get_link_delete_cart(), static::get_link_explnum_analysis(), $this->button_explnum, $this->print, $this->show_explnum, $this->show_statut, $this->show_opac_hidden_fields, $this->draggable,($this->ajax_mode ? $recherche_ajax_mode : 0), $this->anti_loop, $this->no_link, $this->show_map, 0, $this->show_abo_actif, $this->show_expl, $this->context_parameters);
				break;
			case 'b' :
				// on a affaire à un bulletin
				$rqt_bull_info = "SELECT s.notice_id as id_notice_mere, bulletin_id as id_du_bulletin, b.notice_id as id_notice_bulletin 
						FROM notices as s, notices as b, bulletins 
						WHERE b.notice_id=".$element_id." and s.notice_id=bulletin_notice and num_notice=b.notice_id";
				$rst_bull_info = pmb_mysql_query($rqt_bull_info);
				$id_bulletin = 0;
				if(pmb_mysql_num_rows($rst_bull_info)) {
					$bull_ids=pmb_mysql_fetch_object($rst_bull_info);
					$id_bulletin = $bull_ids->id_du_bulletin; 
				}
				$display = new mono_display($element_id, $this->get_level(), str_replace('!!id!!' , $id_bulletin, static::get_link_notice_bulletin()), $this->show_expl, static::get_link_expl(), static::get_link_delete_cart(), str_replace("!!bul_id!!", $id_bulletin, static::get_link_explnum_bulletin()),$this->show_resa, $this->print, $this->show_explnum, $this->show_statut, $this->anti_loop, $this->draggable, $this->no_link, $this->show_opac_hidden_fields,($this->ajax_mode ? $recherche_ajax_mode : 0), $this->show_resa_planning, $this->show_map, 0, $this->context_parameters);
// 				static::set_link_notice_bulletin('');
				break;
			default:
			    return '';
			    break; 
		}
		return $display->result;
	}
	
	protected function get_level() {
		if(!isset($this->level)) {
			$this->level = 6;
		}
		return $this->level;
	}
	
	protected static function get_link() {
		global $link;
		
		if($link) {
			return $link;
		} else {
			return static::$link;
		}
	}
	
	protected static function get_link_expl() {
		global $link_expl;
		
		if($link_expl) {
			return $link_expl;
		} else {
			return static::$link_expl;
		}
	}
	
	protected static function get_link_explnum() {
		global $link_explnum;
		
		if($link_explnum) {
			return $link_explnum;
		} else {
			return static::$link_explnum;
		}
	}
	
	protected static function get_link_serial() {
		global $link_serial;
	
		if($link_serial) {
			return $link_serial;
		} else {
			return static::$link_serial;
		}
	}
	
	protected static function get_link_analysis() {
		global $link_analysis;
	
		if($link_analysis) {
			return $link_analysis;
		} else {
			return static::$link_analysis;
		}
	}
	
	protected static function get_link_bulletin() {
		global $link_bulletin;
	
		if($link_bulletin) {
			return $link_bulletin;
		} else {
			return static::$link_bulletin;
		}
	}
	
	protected static function get_link_explnum_serial() {
		global $link_explnum_serial;
	
		if($link_explnum_serial) {
			return $link_explnum_serial;
		} else {
			return static::$link_explnum_serial;
		}
	}
	
	protected static function get_link_explnum_analysis() {
		global $link_explnum_analysis;
	
		if($link_explnum_analysis) {
			return $link_explnum_analysis;
		} else {
			return static::$link_explnum_analysis;
		}
	}
	
	protected static function get_link_explnum_bulletin() {
		global $link_explnum_bulletin;
	
		if($link_explnum_bulletin) {
			return $link_explnum_bulletin;
		} else {
			return static::$link_explnum_bulletin;
		}
	}
	
	protected static function get_link_notice_bulletin() {
		global $link_notice_bulletin;
	
		if($link_notice_bulletin) {
			return $link_notice_bulletin;
		} else {
			return static::$link_notice_bulletin;
		}
	}
	
	protected static function get_link_delete_cart() {
		return static::$link_delete_cart;	
	}
	
	public function set_level($level) {
		$this->level = $level;
	}
	
	public static function set_link($link) {
		static::$link = $link;
	}
	
	public static function set_link_expl($link) {
		static::$link_expl = $link;
	}
	
	public static function set_link_explnum($link) {
		static::$link_explnum = $link;
	}
	
	public static function set_link_serial($link) {
		static::$link_serial = $link;
	}
	
	public static function set_link_analysis($link) {
		static::$link_analysis = $link;
	}
	
	public static function set_link_bulletin($link) {
		static::$link_bulletin = $link;
	}
	
	public static function set_link_explnum_serial($link) {
		static::$link_explnum_serial = $link;
	}
	
	public static function set_link_explnum_analysis($link) {
		static::$link_explnum_analysis = $link;
	}
	
	public static function set_link_explnum_bulletin($link) {
		static::$link_explnum_bulletin = $link;
	}
	
	public static function set_link_notice_bulletin($link) {
		global $link_notice_bulletin;
		
		$link_notice_bulletin = $link;
		static::$link_notice_bulletin = $link_notice_bulletin;
	}
	
	public static function set_link_delete_cart($link) {
		static::$link_delete_cart = $link;
	}
	
	public function set_show_expl($show) {
		$this->show_expl = $show;
	}
	
	public function set_show_resa($show) {
		$this->show_resa = $show;
	}
	
	public function set_show_explnum($show) {
		$this->show_explnum = $show;
	}
	
	public function set_show_statut($show) {
		$this->show_statut = $show;
	}
	
	public function set_show_opac_hidden_fields($show) {
		$this->show_opac_hidden_fields = $show;
	}
	
	public function set_show_resa_planning($show) {
		$this->show_resa_planning = $show;
	}
	
	public function set_show_map($show) {
		$this->show_map = $show;
	}
	
	public function set_show_abo_actif($show) {
		$this->show_abo_actif = $show;
	}
	
	public function set_print($print) {
		$this->print = $print;
	}
	
	public function set_button_explnum($button_explnum) {
		$this->button_explnum = $button_explnum;
	}
	
	public function set_anti_loop($anti_loop) {
		$this->anti_loop = $anti_loop;
	}
	
	public function set_draggable($draggable) {
		$this->draggable = $draggable;
	}
	
	public function set_no_link($no_link) {
		$this->no_link = $no_link;
	}
	
	public function set_ajax_mode($ajax_mode) {
		$this->ajax_mode = $ajax_mode;
	}
	
	public static function enable_lazy_loading() {
	    static::$lazy_loading = true;
	}
	
	public static function disable_lazy_loading() {
	    static::$lazy_loading = false;
	}
}