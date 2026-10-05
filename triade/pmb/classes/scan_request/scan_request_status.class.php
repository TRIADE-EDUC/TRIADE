<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_status.class.php,v 1.9 2023/07/06 13:46:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/scan_request/scan_request_admin_status.tpl.php");

class scan_request_status {
	
	protected $id;
	
	protected $label;
	
	protected $class_html;
	
	protected $opac_show;

	protected $infos_editable;
	
	protected $cancelable;
	
	protected $is_closed;
	
	protected $workflow;	
	
	/**
	 * 
	 * @var boolean
	 */
	
	public function __construct($id){
		$this->id = $id;
		$this->fetch_data();
	}
		
	protected function fetch_data(){
		$this->label = '';
		$this->class_html = '';
		$this->opac_show = 0;
		$this->infos_editable = 0;
		$this->cancelable = 0;
		$this->is_closed = 0;
		if ($this->id) {
			$query = "select * from scan_request_status where id_scan_request_status = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->label = $row->scan_request_status_label;
				$this->class_html = $row->scan_request_status_class_html;
				$this->opac_show = $row->scan_request_status_opac_show;
				$this->infos_editable = $row->scan_request_status_infos_editable;
				$this->cancelable = $row->scan_request_status_cancelable;
				$this->is_closed = $row->scan_request_status_is_closed;

				$rqt_workflow = "select scan_request_status.scan_request_status_label, scan_request_status_workflow.scan_request_status_workflow_to_num from scan_request_status join scan_request_status_workflow on scan_request_status.id_scan_request_status = scan_request_status_workflow.scan_request_status_workflow_to_num and scan_request_status_workflow.scan_request_status_workflow_from_num=".$this->id;
				$res_workflow = pmb_mysql_query($rqt_workflow);
				if(pmb_mysql_num_rows($res_workflow)){
					while($r = pmb_mysql_fetch_object($res_workflow)){
						$this->workflow[]=array(
							'id' => $r->scan_request_status_workflow_to_num,
							'label' => $r->scan_request_status_label,
						);
					}
				}
			}
		}
	}

	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->set_grid_model('flat_column_3');
		$interface_content_form->add_element('scan_request_status_label', 'editorial_content_publication_state_label')
		->add_input_node('text', $this->label);
		$interface_content_form->add_inherited_element('display_colors', 'scan_request_status_class_html', 'editorial_content_publication_state_class_html')
		->init_nodes([$this->class_html]);
		$interface_content_form->add_element('scan_request_status_visible', 'editorial_content_publication_state_visible')
		->add_input_node('boolean', $this->opac_show);
		$interface_content_form->add_element('scan_request_cancelable', 'scan_request_cancelable')
		->add_input_node('boolean', $this->cancelable);
		$interface_content_form->add_element('scan_request_infos_editable', 'scan_request_infos_editable')
		->add_input_node('boolean', $this->infos_editable);
		$interface_content_form->add_element('scan_request_is_closed', 'scan_request_is_closed')
		->add_input_node('boolean', $this->is_closed);
		return $interface_content_form->get_display();
	}
	
	public function get_form(){
		global $msg;
		
		$interface_form = new interface_admin_form('scan_request_status_form');
		if(!$this->id){
			$interface_form->set_label($msg['editorial_content_publication_state_add']);
		}else{
			$interface_form->set_label($msg['editorial_content_publication_state_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->label." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('scan_request_status')
		->set_field_focus('scan_request_status_label');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $scan_request_status_label, $scan_request_status_visible, $scan_request_status_class_html;
		global $scan_request_cancelable, $scan_request_infos_editable, $scan_request_is_closed;
		
		$this->label = stripslashes($scan_request_status_label);
		$this->opac_show = intval($scan_request_status_visible);
		$this->cancelable = intval($scan_request_cancelable);
		$this->infos_editable = intval($scan_request_infos_editable);
		$this->class_html = stripslashes($scan_request_status_class_html);
		$this->is_closed = intval($scan_request_is_closed);
	}
	
	public function save(){
		if($this->id){
			$query = "update scan_request_status set ";
			$clause = "where id_scan_request_status = ".$this->id;
		}else{
			$query = "insert into scan_request_status set ";
			$clause = "";
		}
		$query.= "
			scan_request_status_label = '".addslashes($this->label)."',
			scan_request_status_opac_show = ".$this->opac_show.",
			scan_request_status_cancelable = ".$this->cancelable.",
			scan_request_status_infos_editable = ".$this->infos_editable.",
			scan_request_status_class_html = '".addslashes($this->class_html)."',
			scan_request_status_is_closed = ".$this->is_closed;
		$query.= " ".$clause;
		pmb_mysql_query($query);
	}
	
	public static function delete($id){
		global $msg;
		$id = intval($id);
		if($id){
			$error = array();
			if($id == 1){
				$error[] = $msg['scan_request_status_forbidden'];
			} else {
				$result = pmb_mysql_query("select count(1) from scan_requests where scan_request_num_status ='".$id."'");
				$total = pmb_mysql_result($result, 0, 0);
				if($total){
					$error[] = $msg['scan_request_status_used'];
				}
				$result = pmb_mysql_query("select count(1) from scan_request_status_workflow where scan_request_status_workflow_from_num != scan_request_status_workflow_to_num and (scan_request_status_workflow_from_num ='".$id."' or scan_request_status_workflow_to_num ='".$id."')");
				$total = pmb_mysql_result($result, 0, 0);
				if($total){
					$error[] = $msg['scan_request_status_workflow_used'];
				}
			}
			if($error){
				print "
				<script type='text/javascript'>
					alert(\"".implode('.', $error)."\");
				</script>";
				return false;
			}else{
				$query = "delete from scan_request_status where id_scan_request_status = ".$id;
				pmb_mysql_query($query);
				$query = "delete from scan_request_status_workflow where scan_request_status_workflow_from_num = scan_request_status_workflow_to_num and scan_request_status_workflow_from_num = ".$id;
				pmb_mysql_query($query);
				return true;
			}
		}
		return true;
	}
	
	public function get_workflow_options(){
		global $charset;
		$options = "";
		
		foreach($this->workflow as $to_statut){
			$options.= "
			<option value='".$to_statut['id']."'".($to_statut['id']==$this->id ? " selected='selected' " : "").">".htmlentities($to_statut['label'],ENT_QUOTES,$charset)."</option>";
		}
		return $options;
	}
		
	public function get_label() {
		return $this->label;
	}

	public function get_workflow() {
		return $this->workflow;
	}
	
	public function get_class_html() {
		return $this->class_html;
	}
	
	public function is_opac_show() {
		return $this->opac_show;
	}
		
	public function is_infos_editable() {
		return $this->infos_editable;
	}
	
	public function is_cancelable() {
		return $this->cancelable;
	}
	
	public function is_closed() {
	    return $this->is_closed;
	}
	
	public function get_id(){
		return $this->id;
	}
}