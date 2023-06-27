<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: filter_results.class.php,v 1.13 2018-09-07 08:08:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/acces.class.php");

class filter_results {
	
	private $notice_ids = '';
	
	
	function __construct($notice_ids,$filter_by_view=1) {
		if(is_array($notice_ids))$notice_ids=implode(',', $notice_ids);		
		$this->notice_ids = $notice_ids;

		if($this->notice_ids!=''){
			//filtrage sur statut ou droits d'accès..
			$query = $this->_get_filter_query();
			$res = pmb_mysql_query($query);
			$this->notice_ids="";
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_assoc($res)){
					if($this->notice_ids != "") $this->notice_ids.=",";
					$this->notice_ids.=$row['id_notice'];
				}
			}
			//filtrage par vue...
			
			if($filter_by_view) $this->_filter_by_view();
			
			$records = explode(',',$notice_ids);
			$tmp = $this->get_array_results();
			$intersect = array_merge(array_intersect($records,$tmp),[]);
			$this->notice_ids = implode(',',$intersect);
		}
	}
	
	
	public function get_results(){
		return $this->notice_ids;
	} 
	
	public function get_array_results(){
		if($this->notice_ids) {
			return explode(",", $this->notice_ids);
		}
		return array();
	}
	
	protected function _filter_by_view(){
		global $opac_opac_view_activate, $dbh;
		
		if($opac_opac_view_activate && $_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
			$query = "select opac_view_num_notice as id_notice from opac_view_notices_".$_SESSION["opac_view"].
				gen_where_in('opac_view_notices_'.$_SESSION["opac_view"].'.opac_view_num_notice', $this->notice_ids);
			$res = pmb_mysql_query($query,$dbh);
			$this->notice_ids = "";
			if($res && pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_object($res)){
					if ($this->notice_ids != "") $this->notice_ids.= ",";
					$this->notice_ids.= $row->id_notice;
				}
			}
		}
	}

	
	protected function _get_filter_query(){
		global $gestion_acces_active;
		global $gestion_acces_empr_notice;
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$query = $dom_2->getFilterQuery($_SESSION['id_empr_session'],4,'id_notice',$this->notice_ids);
		} else {
			$query = '';
		}
		if(!$query){
			$query = "select distinct notice_id as id_notice from notices join notice_statut on notices.statut= id_notice_statut 
				".gen_where_in('notices.notice_id', $this->notice_ids).
				" and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
            
		}
		return $query;
	}

}
	