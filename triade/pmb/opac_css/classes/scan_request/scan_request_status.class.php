<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_status.class.php,v 1.2 2016-01-13 15:41:42 apetithomme Exp $

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
	
	protected $infos_editable;
	
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
				$this->cancelable = $row->scan_request_status_cancelable;
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
	
	public function is_infos_editable() {
		return $this->infos_editable;
	}
}