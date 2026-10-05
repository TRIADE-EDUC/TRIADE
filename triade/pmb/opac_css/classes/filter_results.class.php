<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: filter_results.class.php,v 1.17.2.1 2024/10/16 07:50:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/acces.class.php");

class filter_results {
	
	private $notice_ids = '';
	
    private static $domain = null;
	
	protected const NB_PER_PASS = 50000;

	public function __construct($notice_ids,$filter_by_view=1) {
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
			pmb_mysql_free_result($res);
			//filtrage par vue...
			
			if($filter_by_view) $this->_filter_by_view();
			
			$records = explode(',',$notice_ids);
			$tmp = $this->get_array_results();
			$intersect = array();
			//On fait par passes sinon ca prend trop de memoire sur les gros tableaux
			if(count($records) > count($tmp)) {
				$chunked = array_chunk($records, static::NB_PER_PASS);
				unset($records);
				$to_intersect_with = $tmp;
				
			} else {
				$chunked = array_chunk($tmp, static::NB_PER_PASS);
				unset($tmp);
				$to_intersect_with = $records;
			}
			foreach($chunked as $chunk) {
				$intersect = array_merge(array_intersect($chunk, $to_intersect_with), $intersect);
			}
			$this->notice_ids = implode(',', $intersect);
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
		global $opac_opac_view_activate;
		
		if($opac_opac_view_activate && $_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
		    if(!empty($this->notice_ids)) {
    			$query = "SELECT DISTINCT opac_view_num_notice AS id_notice FROM opac_view_notices_".$_SESSION["opac_view"].
    				gen_where_in('opac_view_notices_'.$_SESSION["opac_view"].'.opac_view_num_notice', $this->notice_ids);
    			$res = pmb_mysql_query($query);
    			$this->notice_ids = "";
    			if($res && pmb_mysql_num_rows($res)){
    				while ($row = pmb_mysql_fetch_object($res)){
    					if ($this->notice_ids != "") $this->notice_ids.= ",";
    					$this->notice_ids.= $row->id_notice;
    				}
    			}
		    }
		}
	}

	
	protected function _get_filter_query(){
		global $gestion_acces_active;
		global $gestion_acces_empr_notice;
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
            $this->initDomain();
            $query = self::$domain->getFilterQuery($_SESSION['id_empr_session'],4,'id_notice',$this->notice_ids);
		} else {
			$query = '';
		}
		if(!$query){
			$query = "SELECT DISTINCT notice_id AS id_notice FROM notices JOIN notice_statut ON notices.statut= id_notice_statut 
				".gen_where_in('notices.notice_id', $this->notice_ids).
				" AND ((notice_visible_opac=1 AND notice_visible_opac_abon=0)".($_SESSION["user_code"]?" OR (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
            
		}
		return $query;
	}

    private function initDomain()
    {
        if(self::$domain !== null){
            return self::$domain;
        }
        $ac= new acces();
        self::$domain = $ac->setDomain(2);
        return self::$domain;
    }
    
}
	