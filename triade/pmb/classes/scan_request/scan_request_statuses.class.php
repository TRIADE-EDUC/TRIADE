<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_statuses.class.php,v 1.4 2017-01-25 16:43:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class scan_request_statuses {

	
	protected $statuses;	
	
	/**
	 * 
	 * @var boolean
	 */
	
	public function __construct(){
		$this->fetch_data();
	}
		
	protected function fetch_data(){
		global $dbh;
		
		$this->statuses=array();
		$query = "select * from scan_request_status";
		$result = pmb_mysql_query($query, $dbh);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->statuses[]=new scan_request_status($row->id_scan_request_status);
			}
		}
	}

	static function get_options($selected=0){
		global $charset,$dbh;

		$options = '';
		$query = "select * from scan_request_status";
		$result = pmb_mysql_query($query, $dbh);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$options.= "
					<option value='".$row->id_scan_request_status."'".($row->id_scan_request_status==$selected ? " selected='selected' " : "").">".htmlentities($row->scan_request_status_label,ENT_QUOTES,$charset)."</option>";
			}
		}
		return $options;
	}
	
	public function get_statuses() {
		return $this->statuses;
	}
	
	
}