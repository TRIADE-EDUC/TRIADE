<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subtab.class.php,v 1.1 2021/02/05 12:52:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class subtab
 * Un sous-menu
 */
class subtab {
	
	protected $sub;
	
	protected $label_code;
	
	protected $label;
	
	protected $title_code;
	
	protected $title;
	
	protected $url_extra;
	
	protected $destination_link;
		
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function fetch_data(){
		
	}
	
	public function get_form() {
		
	}
	
	public function set_properties_from_form() {
		
	}
	
	public function save() {
		
	}
	
	public static function delete() {
		
	}
	
	public function get_sub() {
		return $this->sub;
	}
	
	public function set_sub($sub) {
		$this->sub = $sub;
		return $this;
	}
	
	public function get_label_code() {
		return $this->label_code;
	}
	
	public function set_label_code($label_code) {
		$this->label_code = $label_code;
		return $this;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function set_label($label) {
		$this->label = $label;
		return $this;
	}
	
	public function get_title_code() {
		return $this->title_code;
	}
	
	public function set_title_code($title_code) {
		$this->title_code = $title_code;
		return $this;
	}
	
	public function get_title() {
		return $this->title;
	}
	
	public function set_title($title) {
		$this->title = $title;
		return $this;
	}
	
	public function get_url_extra() {
		return $this->url_extra;
	}
	
	public function set_url_extra($url_extra) {
		$this->url_extra = $url_extra;
		return $this;
	}
	
	public function get_destination_link() {
		return $this->destination_link;
	}
	
	public function set_destination_link($destination_link) {
		$this->destination_link = $destination_link;
		return $this;
	}
} // end of subtab
