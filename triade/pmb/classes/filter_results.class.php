<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: filter_results.class.php,v 1.7 2023/05/03 12:41:20 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/acces.class.php");

class filter_results {
	
	private $notice_ids = '';
	public $user = 0;
	
	public function __construct($notice_ids, $user=0) {
		global $PMBuserid;
		$this->user = $user;
		if($this->user = 0) $this->user = $PMBuserid;
		$this->notice_ids = $notice_ids;
		if($this->notice_ids!=''){
			//filtrage sur statut ou droits d'accès..
			$query = $this->_get_filter_query();
			if($query){
				$res = pmb_mysql_query($query);
				$this->notice_ids="";
				if(pmb_mysql_num_rows($res)){
					while ($row = pmb_mysql_fetch_assoc($res)){
						if($this->notice_ids != "") $this->notice_ids.=",";
						$this->notice_ids.=$row['id_notice'];
					}
				}
			}
		}
	}
	
	public function get_results(){
		return $this->notice_ids;
	} 
	
	protected function _get_filter_query(){
		global $gestion_acces_active;
		global $gestion_acces_user_notice;
		if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
			$ac= new acces();
			$dom_2= $ac->setDomain(1);
			$query = $dom_2->getFilterQuery($this->user,4,'id_notice',$this->notice_ids);
		} else {
			$query = '';
		}
		return $query;
	}

}
	