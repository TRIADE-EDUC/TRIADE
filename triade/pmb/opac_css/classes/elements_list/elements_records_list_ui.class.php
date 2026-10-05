<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_records_list_ui.class.php,v 1.7.2.1 2024/12/30 11:01:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/elements_list/elements_list_ui.class.php');
// require_once($class_path.'/serial_display.class.php');
// require_once($class_path.'/mono_display.class.php');

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
			static::$link = './catalog.php?categ=isbd&id=!!id!!';
			static::$link_expl = './catalog.php?categ=edit_expl&id=!!notice_id!!&cb=!!expl_cb!!&expl_id=!!expl_id!!';
			static::$link_explnum = './catalog.php?categ=edit_explnum&id=!!notice_id!!&explnum_id=!!explnum_id!!';
			static::$link_serial = './catalog.php?categ=serials&sub=view&serial_id=!!id!!';
			static::$link_analysis = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!bul_id!!&art_to_show=!!id!!';
			static::$link_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!';
			static::$link_explnum_serial = "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!";
			static::$link_explnum_analysis = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
			static::$link_explnum_bulletin = "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=!!bul_id!!&explnum_id=!!explnum_id!!";
			static::$link_notice_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!';
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
		$record = new record_datas($element_id);		
		$this->add_context_parameter('element_id', $element_id);
		$template_path = $this->get_template_path($record->get_niveau_biblio());
		$notice_affichage = new notice_affichage($element_id);
		$notice_affichage->do_header();
		$context = array(
		    'list_element' => $record,
		    'isbd' => $notice_affichage->notice_header,
		    'header_without_html' => str_replace(array("\n", "\t", "\r"), '', strip_tags($notice_affichage->notice_header)),
		    'detail' => record_display::get_display_in_result($element_id),
		);
		$render = static::render($template_path, $context, $this->get_context_parameters());
		return $render;
	}
	
	private function get_template_path(string $niveau_biblio) {
	    global $include_path, $opac_record_templates_folder;
	    
	    //parametre a creer si besoin
	    if($opac_record_templates_folder) {
	        $template_directory = $opac_record_templates_folder;
	    } else {
	        $template_directory = 'common';
	    }
	    
	    $template_name = "record";
	    switch ($niveau_biblio) {
	        case "s":
	            $template_name = "serial";
	            break;
	        case "b":
	            $template_name = "bulletin";
	            break;
	        case "a":
	            $template_name = "article";
	            break;
	        case "m":
	        default:
	            $template_name = "record";
	            break;
	    }
	    
	    switch (true) {
	        case file_exists($include_path.'/templates/record/'.$template_directory.'/list/'.$template_name.'_subst.html'):
	            return $include_path.'/templates/record/'.$template_directory.'/list/'.$template_name.'_subst.html';
	        case file_exists($include_path.'/templates/record/'.$template_directory.'/list/'.$template_name.'.html'):
	            return $include_path.'/templates/record/'.$template_directory.'/list/'.$template_name.'.html';
	        case file_exists($include_path.'/templates/record/'.$template_directory.'/list/record_subst.html'):
	            return $include_path.'/templates/record/'.$template_directory.'/list/record_subst.html';
	        case file_exists($include_path.'/templates/record/'.$template_directory.'/list/record.html'):
	            return $include_path.'/templates/record/'.$template_directory.'/list/record.html';
	    }
	    return "";
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