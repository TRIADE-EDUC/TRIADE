<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_records_indexer.class.php,v 1.6 2019-05-27 12:55:59 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/sphinx/sphinx_indexer.class.php';

class sphinx_records_indexer extends sphinx_indexer {
	
	public function __construct() {
		global $include_path;
		$this->object_id = 'id_notice';
		$this->object_key = 'notice_id';
		$this->object_index_table = 'notices_fields_global_index';
		$this->object_table = 'notices';
		parent::__construct();
		$this->filters = ['statut', 'typdoc'];
		$this->setChampBaseFilepath($include_path."/indexation/notices/champs_base.xml");
	}
	
	public function fillExplnumIndex($explnum_id=0){
	    global $sphinx_indexes_prefix;
	    
		$options['size'] = 80;
		$object_id+=0;
		
		//Remplissage des indexs...
		if($explnum_id*1 == 0){
			$noti_query = 'select explnum_id as id, explnum_notice as num_record, explnum_index_wew as content from explnum where explnum_notice != 0 and explnum_bulletin = 0';
			$bull_query = 'select explnum_id as id, num_notice as num_record, explnum_index_wew as content from explnum join bulletins on bulletin_id =explnum_bulletin where explnum_notice = 0 and explnum_bulletin != 0 and num_notice!=0';
			$rq='select * from (('.$noti_query.') union ('.$bull_query.')) as uni order by 1';
		}else{
			$noti_query = 'select explnum_id as id, explnum_notice as num_record, explnum_index_wew as content from explnum where explnum_notice != 0 and explnum_bulletin = 0 and explnum_id = '.$explnum_id;
			$bull_query = 'select explnum_id as id, num_notice as num_record, explnum_index_wew as content from explnum join bulletins on bulletin_id =explnum_bulletin where explnum_notice = 0 and explnum_bulletin != 0 and num_notice!=0 and explnum_id = '.$explnum_id;
			$rq='select * from (('.$noti_query.') union ('.$bull_query.')) as uni order by 1';
		}
		$res=pmb_mysql_query($rq);
		if ($res) {
			pmb_mysql_query('set session group_concat_max_len = 16777216');
			if(!$explnum_id) print ProgressBar::start(pmb_mysql_num_rows($res),"EXPLNUMS",$options);
			while ($object=pmb_mysql_fetch_object($res)) {
				//purge...
				pmb_mysql_query('delete from '.$sphinx_indexes_prefix.'records_explnums where id = '.$object->id,$this->getDBHandler());
				$query = 'insert into '.$sphinx_indexes_prefix.'records_explnums (id,content,num_record) values('.$object->id.',\''.addslashes($object->content).'\',\''.$object->num_record.'\')';
				if(!pmb_mysql_query($query,$this->getDBHandler())){
					print $table. ' : '.pmb_mysql_error($this->getDBHandler()). "\n".$query;die;
				}
				if(!$explnum_id) print ProgressBar::next();
			}
			if(!$explnum_id) print ProgressBar::finish();
		}
	}
	
	protected function addSpecificsFilters($id, $filters =array()){
		$filters = parent::addSpecificsFilters($id, $filters);
		$result = pmb_mysql_query('select typdoc,statut from notices where notice_id = '.$id);
		$row = pmb_mysql_fetch_object($result);
		$filters['statut'] = $row->statut;
		$filters['typdoc'] = $row->typdoc;
		return $filters;
	}
}