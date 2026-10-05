<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: print_cart_tpl.class.php,v 1.6 2023/07/04 09:14:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/print_cart_tpl.tpl.php");

class print_cart_tpl {
	private $id = 0;
	private $name;
	private $header;
	private $footer;
	
	public function __construct($id = 0) {
		$this->id = (int) $id;
		$this->fetch_data();
	}
	
	private function fetch_data() {
		$this->name = '';
		$this->header = '';
		$this->footer = '';
		
		$req = "select * from print_cart_tpl where id_print_cart_tpl=". $this->id;
		$resultat = pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			$r = pmb_mysql_fetch_object($resultat);		
			$this->id = $r->id_print_cart_tpl;	
			$this->name = $r->print_cart_tpl_name;	
			$this->header = $r->print_cart_tpl_header;	
			$this->footer = $r->print_cart_tpl_footer;	
		} else {
			$this->id = 0;
		}
	}

	public function get_id() {
		return $this->id;
	}
	
	public function set_id($id) {
		$this->id = intval($id);
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_header() {
		return $this->header;
	}
	
	public function get_footer() {
		return $this->footer;
	}
	
	public function set_name($name) {
		$this->name = $name;
	}
	
	public function set_header($header) {
		$this->header = $header;
	}

	public function set_footer($footer) {
		$this->footer = $footer;
	}
	       
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('f_name', 'admin_print_cart_tpl_form_name')
		->add_input_node('text', $this->name);
		$interface_content_form->add_element('f_header', 'admin_print_cart_tpl_form_header')
		->add_textarea_node($this->header)
		->set_cols(100)
		->set_rows(20);
		$interface_content_form->add_element('f_footer', 'admin_print_cart_tpl_form_footer')
		->add_textarea_node($this->footer)
		->set_cols(100)
		->set_rows(20);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $cart_tpl_content_js_form, $msg;
		
		$interface_form = new interface_form('print_cart_tpl');
		if(!$this->id){
			$interface_form->set_label($msg['admin_print_cart_tpl_form_add']);
		}else{
			$interface_form->set_label($msg['admin_print_cart_tpl_form_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_duplicable(true)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($cart_tpl_content_js_form.$this->get_content_form())
		->set_table_name('print_cart_tpl')
		->set_field_focus('f_name');
		return $interface_form->get_display();
	}

	public function set_properties_from_form() {
		global $f_name, $f_header, $f_footer;
		
		$this->name = stripslashes($f_name);
		$this->header = stripslashes($f_header);
		$this->footer = stripslashes($f_footer);
	}
	
	public function save() {
		$fields = "
			print_cart_tpl_name='".addslashes($this->name)."',
			print_cart_tpl_header='".addslashes($this->header)."',
			print_cart_tpl_footer='".addslashes($this->footer)."'
		";		
		if(!$this->id){ // Ajout
			$req = "INSERT INTO print_cart_tpl SET ".$fields ;	
			pmb_mysql_query($req);
			$this->id = pmb_mysql_insert_id();
		} else {
			$req = "UPDATE print_cart_tpl SET ".$fields." where id_print_cart_tpl=".$this->id;	
			pmb_mysql_query($req);				
		}	
	}	
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$req="DELETE from print_cart_tpl WHERE id_print_cart_tpl=".$id;
			pmb_mysql_query($req);
		}
		return true;	
	}	
} 

