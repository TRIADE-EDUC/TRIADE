<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_status.class.php,v 1.6 2016-02-15 16:36:30 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class scan_request_status {
	
	protected $id;
	
	protected $label;
	
	protected $class_html;
	
	protected $opac_show;

	protected $infos_editable;
	
	protected $cancelable;	
	
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
		global $dbh;
		
		if ($this->id) {
			$query = "select * from scan_request_status where id_scan_request_status = ".$this->id;
			$result = pmb_mysql_query($query, $dbh);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->label = $row->scan_request_status_label;
				$this->class_html = $row->scan_request_status_class_html;
				$this->opac_show = $row->scan_request_status_opac_show;
				$this->infos_editable = $row->scan_request_status_infos_editable;
				$this->cancelable = $row->scan_request_status_cancelable;

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
	
	public function get_id(){
		return $this->id;
	}
	

		
	
}