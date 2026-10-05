<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_admin_status.class.php,v 1.13 2021/01/20 07:27:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class scan_request_admin_status {
	protected $scan_request_status;	//tableau des status 
	
	public function __construct(){
		$this->fetch_data();
	}
		
	protected function fetch_data(){
		$this->scan_request_status = array();
		
		$rqt = "select * from scan_request_status order by scan_request_status_label asc";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->scan_request_status[] =array(
					'id' => $row->id_scan_request_status,
					'label' => $row->scan_request_status_label,
					'opac_show' => $row->scan_request_status_opac_show,
					'class_html' => $row->scan_request_status_class_html,
					'infos_editable' => $row->scan_request_status_infos_editable,
					'cancelable' => $row->scan_request_status_cancelable,
					'is_closed' => $row->scan_request_status_is_closed
				);
			}
		}
	}

	public function get_scan_request_status(){
		return $this->scan_request_status;
	}

	public function get_selector_options($selected=0){
		global $charset;
		global $deflt_scan_request_status;
		
		if(!$selected){
			$selected=$deflt_scan_request_status;
		}		
		$options = "";
		for($i=0 ; $i<count($this->scan_request_status) ; $i++){
			$options.= "
			<option value='".$this->scan_request_status[$i]['id']."' ".($this->scan_request_status[$i]['id']==$selected ? "selected='selected'" : "").">".htmlentities($this->scan_request_status[$i]['label'],ENT_QUOTES,$charset)."</option>";	
		}
		return $options;
	}
	
	public function get_selector_options_multiple($list_status = array()){
		global $charset;
	
		$options = "";
		for($i=0 ; $i<count($this->scan_request_status) ; $i++){
			$options.= "
			<option value='".$this->scan_request_status[$i]['id']."' ".(count($list_status) && in_array($this->scan_request_status[$i]['id'],$list_status)? "selected='selected'" : "").">".htmlentities($this->scan_request_status[$i]['label'],ENT_QUOTES,$charset)."</option>";
		}
		return $options;
	}
}