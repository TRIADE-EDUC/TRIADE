<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_priorities.class.php,v 1.7 2021/01/20 07:27:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class scan_request_priorities {
	protected $scan_request_priorities;	//tableau des priorités 
	
	public function __construct(){
		$this->fetch_data();
	}
		
	protected function fetch_data(){
		$this->scan_request_priorities = array();
		
		$rqt = "select * from scan_request_priorities order by scan_request_priority_weight, scan_request_priority_label asc";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->scan_request_priorities[] =array(
					'id' => $row->id_scan_request_priority,
					'label' => $row->scan_request_priority_label,
					'weight' => $row->scan_request_priority_weight
				);
			}
		}
	}

	public function get_scan_request_priorities(){
		return $this->scan_request_priorities;
	}

	public function get_selector_options($selected=0){
		global $charset;
		global $deflt_scan_request_priorities;
		
		if(!$selected){
			$selected=$deflt_scan_request_priorities;
		}		
		$options = "";
		for($i=0 ; $i<count($this->scan_request_priorities) ; $i++){
			$options.= "
			<option value='".$this->scan_request_priorities[$i]['id']."'".($this->scan_request_priorities[$i]['id']==$selected ? "selected='selected'" : "").">".htmlentities($this->scan_request_priorities[$i]['label'],ENT_QUOTES,$charset)."</option>";	
		}
		return $options;
	}
	
	public static function get_options($selected=0){
		global $charset;
		$options = '';
		$query = "select * from scan_request_priorities order by scan_request_priority_weight, scan_request_priority_label asc";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$options.= "
					<option value='".$row->id_scan_request_priority."'".($row->id_scan_request_priority==$selected ? "selected='selected'" : "").">".htmlentities($row->scan_request_priority_label,ENT_QUOTES,$charset)."</option>";
			}
		}
		return $options;
	}
}