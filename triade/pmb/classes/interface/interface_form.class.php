<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_form.class.php,v 1.9 2018-04-26 14:52:32 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/translation.class.php');

class interface_form {
	
	protected $name;
	
	protected $object_id;
	
	protected $label;
	
	protected $url_base;
	
	protected $confirm_delete_msg;
	
	protected $content_form;
	
	protected $table_name;
	
	public function __construct($name = ''){
		$this->name = $name;
	}
	
	public function get_display($ajax = false) {
		global $msg, $charset;
		global $current_module;
		
		$display = "
		<form class='form-".$current_module."' id='".$this->name."' name='".$this->name."'  method='post' action=\"".$this->url_base."&action=save&id=".$this->object_id."\" >
			<h3>".htmlentities($this->label, ENT_QUOTES, $charset)."</h3>
			<div class='form-contenu'>
				".$this->content_form."
			</div>	
			<div class='row'>	
				<div class='left'>				    
					<input type='button' class='bouton' name='cancel_button' id='cancel_button' value='".$msg['76']."'  onclick=\"document.location='".$this->url_base."'\"  />
					<input type='submit' class='bouton' name='save_button' id='save_button' value='".$msg['77']."' />
				</div>
				<div class='right'>
					".($this->object_id ? "<input type='button' class='bouton' name='delete_button' id='delete_button' value='".htmlentities($msg["63"], ENT_QUOTES, $charset)."' onclick=\"if(confirm('".htmlentities($this->confirm_delete_msg, ENT_QUOTES, $charset)."')){document.location='".$this->url_base."&action=delete&id=".$this->object_id."';}\" />" : "")."
				</div>
			</div>
		<div class='row'></div>
		</form>";
		if(isset($this->table_name) && $this->table_name) {
			$translation = new translation($this->object_id, $this->table_name);
			$display .= $translation->connect($this->name);
		}
		return $display;
	}
	
	public function get_display_ajax() {
		global $msg, $charset;
		global $current_module;
		
		$display = "
		<form class='form-".$current_module."' id='".$this->name."' name='".$this->name."'  method='post' action=\"".$this->url_base."&action=save&id=".$this->object_id."\" >
			<h3>".htmlentities($this->label, ENT_QUOTES, $charset)."</h3>	
			<div class='form-contenu'>
				".$this->content_form."
			</div>	
			<div class='row'>	
				<div class='left'>
					<input type='button' class='bouton' name='cancel_button' id='cancel_button' value='".$msg['76']."' />
					<input type='submit' class='bouton' name='save_button' id='save_button' value='".$msg['77']."' />
				</div>
				<div class='right'>
					".($this->object_id ? "<input type='button' class='bouton' name='delete_button' id='delete_button' value='".htmlentities($msg["63"], ENT_QUOTES, $charset)."' />" : "")."
				</div>
			</div>
		<div class='row'></div>
		</form>";
		if(isset($this->table_name) && $this->table_name) {
			$translation = new translation($this->object_id, $this->table_name);
			$display .= $translation->connect($this->name);
		}
		return $display;
	}
	
	public function branch_translations($table_name) {
		$translation = new translation($this->object_id, $table_name);
		return $translation->connect($this->name);
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_object_id() {
		return $this->object_id;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_url_base() {
		return $this->url_base;
	}
	
	public function get_confirm_delete_msg() {
		global $msg;
		
		if(!isset($this->confirm_delete_msg)) {
			if(isset($msg[$this->name.'_confirm_delete'])) {
				$this->confirm_delete_msg = $msg[$this->name.'_confirm_delete'];
			}
		}
		return $this->confirm_delete_msg;
	}
	
	public function set_name($name) {
		$this->name = $name;
		return $this;
	}
	
	public function set_object_id($object_id) {
		$this->object_id = $object_id+0;
		return $this;
	}
	
	public function set_label($label) {
		$this->label = $label;
		return $this;
	}
	
	public function set_url_base($url_base) {
		$this->url_base = $url_base;
		return $this;
	}
	
	public function set_confirm_delete_msg($confirm_delete_msg) {
		$this->confirm_delete_msg = $confirm_delete_msg;
		return $this;
	}
	
	public function set_content_form($content_form) {
		$this->content_form = $content_form;
		return $this;
	}
	
	public function set_table_name($table_name) {
		$this->table_name = $table_name;
		return $this;
	}
}