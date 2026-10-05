<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_priorities.class.php,v 1.1 2016/01/07 15:34:16 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/scan_request/scan_request_priority.class.php');

class scan_request_priorities {
	
	/**
	 * Tableau des priorités
	 * @var scan_request_priority
	 */
	protected $scan_request_priorities;	//tableau des priorités 
	
	public function __construct(){
		$this->fetch_data();
	}
	
	protected function fetch_data(){
		$this->scan_request_priorities = array();
		
		$query = "select id_scan_request_priority from scan_request_priorities order by scan_request_priority_weight, scan_request_priority_label asc";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->scan_request_priorities[] = new scan_request_priority($row->id_scan_request_priority);
			}
		}
	}

	public function get_scan_request_priorities(){
		return $this->scan_request_priorities;
	}

	public function get_selector_options($selected = 0){
		global $charset;
		global $deflt_scan_request_priorities;
		
		$options = "";
		foreach ($this->scan_request_priorities as $scan_request_priority){
			$options.= "
			<option value='".$scan_request_priority->get_id()."'".($scan_request_priority->get_id() == $selected ? "selected='selected'" : "").">".htmlentities($scan_request_priority->get_label(),ENT_QUOTES,$charset)."</option>";	
		}
		return $options;
	}
}