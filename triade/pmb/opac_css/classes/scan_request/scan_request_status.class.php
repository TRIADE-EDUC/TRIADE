<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_status.class.php,v 1.4 2021/05/28 09:41:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class scan_request_status {
	
	protected $id;
	
	protected $label;
	
	protected $class_html;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $opac_show;
	
	protected $cancelable;
	
	protected $is_closed;
	
	protected $infos_editable;
	
	public function __construct($id){
		$this->id = intval($id);
		$this->fetch_data();
	}
		
	protected function fetch_data(){
		if ($this->id) {
			$query = "select * from scan_request_status where id_scan_request_status = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->label = $row->scan_request_status_label;
				$this->class_html = $row->scan_request_status_class_html;
				$this->opac_show = $row->scan_request_status_opac_show;
				$this->cancelable = $row->scan_request_status_cancelable;
				$this->is_closed = $row->scan_request_status_is_closed;
				$this->infos_editable = $row->scan_request_status_infos_editable;
			}
		}
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_class_html() {
		return $this->class_html;
	}
	
	public function is_opac_show() {
		return $this->opac_show;
	}
	
	public function is_cancelable() {
		return $this->cancelable;
	}
	
	public function is_closed() {
	    return $this->is_closed;
	}
	
	public function is_infos_editable() {
		return $this->infos_editable;
	}
}