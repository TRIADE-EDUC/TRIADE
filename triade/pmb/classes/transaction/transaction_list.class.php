<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transaction_list.class.php,v 1.6 2021/01/13 13:06:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/transaction/transaction.tpl.php");

class transactype_list {	
	public $transactype_list=array(); // liste des caisses
	
	public function __construct(){
		$this->fetch_data();		
	}
	
	protected function fetch_data(){
		// les data...	
		$this->transactype_list=array();	
		$rqt = "select * from transactype order by transactype_name";
		$res = pmb_mysql_query($rqt);
		$i=0;
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->transactype_list[$i]['id'] = $row->transactype_id;
				$this->transactype_list[$i]['name'] = $row->transactype_name;
				$this->transactype_list[$i]['unit_price'] = $row->transactype_unit_price;
				$this->transactype_list[$i]['quick_allowed'] = $row->transactype_quick_allowed;			
				$i++;
			}
		}
	}
	
	public function get_data(){
		return $this->transactype_list;	
	}
	
	public function get_count(){
		return count($this->transactype_list);		
	}
}