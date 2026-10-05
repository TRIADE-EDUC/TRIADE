<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_searcher_explnums.class.php,v 1.3 2021/12/27 08:17:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/searcher/opac_searcher_generic.class.php');

class opac_searcher_explnums extends opac_searcher_generic {
	
	protected $notices_searched = false;		// booléen pour éviter de tourner en rond...
	protected $notices_ids = '';
	protected $explnums_notices = array();		//tableau association identifiant de doc.numérique <=> identifiant de notice
	
	public function __construct($user_query=''){
		parent::__construct($user_query);
		$this->object_key = "explnum_id";
		$this->object_table = 'explnum';
		$this->object_index_key= "num_obj";
		$this->object_words_table = "explnum_words_global_index";
		$this->object_fields_table = "explnum_fields_global_index";
		$this->stemming_active=0;
		$this->keep_empty=0;
	}

	protected function _get_search_type(){
		return "explnums";
	}
	
	protected function _analyse(){
		if(!is_object($this->aq) && $this->user_query){
			$this->aq= new analyse_query_explnum($this->user_query,0,0,1,$this->keep_empty,$this->stemming_active);
		}
	}

	protected function _filter_results(){
		$this->_get_notices_ids();
		
		if($this->notices_ids!='') {
			
			$query='select notice_id from notices where 1 ';

			//filtrage par type de document
			global $typdoc;
			if($typdoc) {
				$query.= "and typdoc = ('".$typdoc."')";
			}

			//filtrage par visibilite
			global $gestion_acces_active,$gestion_acces_empr_notice;
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
				$q = $dom_2->getFilterQuery($_SESSION['id_empr_session'],16,'id_notice',$this->notices_ids);
				$query.= 'and notice_id in ('.$q.') ';
			} else {
				$query.= 'and statut in (select id_notice_statut from notice_statut where ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)'.($_SESSION["user_code"]?' or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)':'').')) ';
				$query.= 'and notice_id in ('.$this->notices_ids.') ';
			}
			
			//filtrage par vue
			global $opac_opac_view_activate;
			if($opac_opac_view_activate && $_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
				$query.= ' and notice_id in (select opac_view_num_notice from opac_view_notices_'.$_SESSION["opac_view"].') ';  
			}
			
			//filtrage par recherche perso
			global $include_path,$opac_search_other_function;
			$custom_query = '';
			if ($opac_search_other_function){
				require_once($include_path.'/'.$opac_search_other_function);
				$custom_query = search_other_function_clause();
				if ($custom_query) {
					$query.= ' and notice_id in ('.$custom_query.')';
				}
			}
			
			if($query) {
				$notices_ids = array();
				$r = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($r)) {
					while($o=pmb_mysql_fetch_object($r)) {
						$notices_ids[] = $o->notice_id;
					}
				}
				$this->explnums_notices = array_intersect($this->explnums_notices, $notices_ids);
				$this->objects_ids = implode(',',array_keys($this->explnums_notices));
			}			
		}
	}
	
	protected function _get_notices_ids() {
		if(!$this->notices_searched) {
			$this->notices_ids='';
			$this->_get_objects_ids();
			if($this->objects_ids=='') {
				return;
			}
			if($this->user_query !== "*"){
				//Lien avec les notices de monographies/articles
				$q = 'select explnum_notice as notice_id, explnum_id as num_obj from explnum where explnum_notice!=0 and explnum_id in ('.$this->objects_ids.')';
				//Lien avec les notices de periodique des bulletins
				$q.= ' union ';
	 			$q.= 'select bulletin_notice as notice_id, explnum_id as num_obj from explnum join bulletins on explnum_bulletin=bulletin_id where num_notice=0 and explnum_id in ('.$this->objects_ids.')';		
				//Lien avec les notices de bulletins
				$q.= ' union ';
	 			$q.= 'select num_notice as notice_id, explnum_id as num_obj from explnum join bulletins on explnum_bulletin=bulletin_id where num_notice!=0 and explnum_id in ('.$this->objects_ids.')';
	 		} else {
				//Lien avec les notices de monographies/articles
				$q = 'select explnum_notice as notice_id, explnum_id as num_obj from explnum where explnum_notice!=0 ';
				//Lien avec les notices de periodique des bulletins
				$q.= 'union ';
	 			$q.= 'select bulletin_notice as notice_id, explnum_id as num_obj from explnum join bulletins on explnum_bulletin=bulletin_id where num_notice=0 ';		
				//Lien avec les notices de bulletins
				$q.= 'union ';
	 			$q.= 'select num_notice as notice_id, explnum_id as num_obj from explnum join bulletins on explnum_bulletin=bulletin_id where num_notice!=0 ';
			} 	
			$r = pmb_mysql_query($q);
			if(pmb_mysql_num_rows($r)) {
				$this->explnums_notices=array();
				while($o=pmb_mysql_fetch_object($r)) {
					if($o->notice_id) {
						$this->explnums_notices[$o->num_obj]=$o->notice_id;
					}
				}
				$this->notices_ids = implode(',',array_unique($this->explnums_notices));
			}
			$this->notices_searched=true;
		}
	}

	
}