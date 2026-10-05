<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_priority.class.php,v 1.3 2023/07/04 15:30:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/scan_request/scan_request_priorities.tpl.php");

class scan_request_priority {
	
	/**
	 * Identifiant
	 * @var int
	 */
	protected $id;
	
	/**
	 * Libellé
	 * @var string
	 */
	protected $label;
	
	/**
	 * Poids
	 * @var int
	 */
	protected $weight;
	
	public function __construct($id){
		$this->id = intval($id);
		$this->fetch_data();
	}
		
	protected function fetch_data(){
		$this->label = '';
		$this->weight = 1;
		if ($this->id) {
			$query = "select * from scan_request_priorities where id_scan_request_priority = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->label = $row->scan_request_priority_label;
				$this->weight = $row->scan_request_priority_weight;
			}
		}
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->set_grid_model('flat_column_3');
		
		$interface_content_form->add_element('scan_request_priority_label', 'editorial_content_publication_state_label')
		->add_input_node('text', $this->label)
		->set_class('saisie-20em');
		$interface_content_form->add_element('scan_request_priority_weight', 'scan_request_priority_weight')
		->add_input_node('integer', $this->weight);
		return $interface_content_form->get_display();
	}
	
	public function get_form(){
		global $msg;
		
		$interface_form = new interface_admin_form('scan_request_priority_form');
		if(!$this->id){
			$interface_form->set_label($msg['scan_request_priorities_add']);
		}else{
			$interface_form->set_label($msg['scan_request_priorities_update']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->label." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('scan_request_priorities')
		->set_field_focus('scan_request_priority_label');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $scan_request_priority_label, $scan_request_priority_weight;
		
		$this->label = stripslashes($scan_request_priority_label);
		$this->weight = intval($scan_request_priority_weight);
	}
	
	public function save(){
		if($this->id){
			$query = "update scan_request_priorities set ";
			$clause = "where id_scan_request_priority = ".$this->id;
		}else{
			$query = "insert into scan_request_priorities set ";
			$clause = "";
		}
		$query.= "
			scan_request_priority_label = '".addslashes($this->label)."',
			scan_request_priority_weight = '".addslashes($this->weight)."' ";
		$query.= " ".$clause;
		pmb_mysql_query($query);
	}
	
	public static function delete($id){
		$id= intval($id);
		if($id){
			$query = "delete from scan_request_priorities where id_scan_request_priority = ".$id;
			pmb_mysql_query($query);
			return true;
		}
		return true;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		return $this->label;
	}
}