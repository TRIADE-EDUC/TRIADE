<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_priority.class.php,v 1.1 2016-01-07 15:34:16 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
		$this->id = $id;
		$this->fetch_data();
	}
		
	protected function fetch_data(){
		global $dbh;
		
		if ($this->id) {
			$query = "select * from scan_request_priorities where id_scan_request_priority = ".$this->id;
			$result = pmb_mysql_query($query, $dbh);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->label = $row->scan_request_priority_label;
				$this->weight = $row->scan_request_priority_weight;
			}
		}
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		return $this->label;
	}
}