<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transaction.class.php,v 1.9 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/transaction/transaction.tpl.php");

class transactype  {
	public $id = 0;				// identifiant de la transactype
	public $name = "";				// Libellé de la transactype
	public $unit_price = 0;		// prix unitaire
	public $quick_allowed = 0;		// Autorisation de l'encaissement rapide
	
	public function __construct($id=0){		
		$this->id = intval($id);		
		$this->fetch_data();		
	}
	
	protected function fetch_data(){	
		$this->name="";
		$this->unit_price = 0;
		$this->quick_allowed = 0;		
		if(!$this->id)	return false;
		
		// les infos générales...	
		$rqt = "select * from transactype where transactype_id ='".$this->id."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$row = pmb_mysql_fetch_object($res);
			$this->id = $row->transactype_id;
			$this->name = $row->transactype_name;
			$this->unit_price = $row->transactype_unit_price;
			$this->quick_allowed = $row->transactype_quick_allowed;			
		}
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('f_name', 'transactype_form_name')
		->add_input_node('text', $this->name);
		$interface_content_form->add_element('f_unit_price', 'transactype_form_unit_price')
		->add_input_node('text', $this->unit_price);
		$interface_content_form->add_element('f_quick_allowed', 'transactype_form_quick_allowed')
		->add_input_node('boolean', $this->quick_allowed);
		return $interface_content_form->get_display();
	}
	
	public function get_form(){
		global $msg;
		
		$interface_form = new interface_admin_form('transactype');
		if(!$this->id){
			$interface_form->set_label($msg['transactype_form_titre_add']);
		}else{
			$interface_form->set_label($msg['transactype_form_titre_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg["transactype_form_delete_question"])
		->set_content_form($this->get_content_form())
		->set_table_name('transactype')
		->set_field_focus('f_name');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form(){		
		global $f_name;
		global $f_unit_price;
		global $f_quick_allowed;
		
		$this->name=stripslashes($f_name);
		$this->unit_price=intval($f_unit_price);
		$this->quick_allowed=intval($f_quick_allowed);
	}
	
	public function save(){
		if($this->id){
			$save = "update ";
			$clause = "where transactype_id = '".$this->id."'";
		}else{
			$save = "insert into ";
			$clause = "";
		}
		$save.=" transactype set transactype_name='". addslashes( $this->name). "', transactype_unit_price='".$this->unit_price. "' ,transactype_quick_allowed='". $this->quick_allowed. "'   $clause";
		pmb_mysql_query($save);
		if(!$this->id){
			$this->id=pmb_mysql_insert_id();
		}
	}
	
	public static function delete($id){
		$id = intval($id);
		$rqt = "delete FROM transactype WHERE transactype_id ='".$id."'";
		pmb_mysql_query($rqt);
		return true;
	}
}