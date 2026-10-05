<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_loc.class.php,v 1.1 2020/11/10 09:19:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class resa_loc{
	
	protected $id_location;
	
	protected $empr_location;
	
	protected $data;
	
	public function __construct($id_location=0, $empr_location=0) {
		$this->id_location = intval($id_location);
		$this->empr_location = intval($empr_location);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->data = array();
		$query = "SELECT resa_loc, resa_emprloc FROM resa_loc";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$this->data[$row->resa_emprloc][] = $row->resa_loc;
			}
		}
	}
	
	public function get_data() {
		return $this->data;
	}
}