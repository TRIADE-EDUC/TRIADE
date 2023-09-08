<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Indexint.php,v 1.8 2016-01-26 15:36:15 dgoron Exp $
namespace Sabre\PMB;

class Indexint extends Collection {
	protected $indexint;

	function __construct($name,$config) {
		$this->config = $config;
		$this->type = "indexint";
		$code = $this->get_code_from_name($name);
		$id = substr($code,1);
		if($id){
			$this->indexint = new \indexint($id);
		}
		
	}
	
	function getChildren(){
		$current_children=array();
		$children = parent::getChildren();
		$query = "select indexint_id from indexint where num_pclass = ".$this->indexint->id_pclass." and indexint_name like '".addslashes(trim($this->indexint->name,0))."%' and indexint_id != ".$this->indexint->indexint_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$current_children[] = $this->getChild("(I".$row->indexint_id.")",$this->config);
			}			
		}
		usort($current_children,"sortChildren");
		return array_merge($children,$current_children);
	}

	function getName() {
		return $this->format_name($this->indexint->name." - ".$this->indexint->comment.""." (I".$this->indexint->indexint_id.")");
	}
	
	function need_to_display($categ_id){
		return true;
	}
	
	function getNotices(){
		$this->notices = array();		
		if($this->indexint->indexint_id){
			$clause ="";
			//notice
			$query = "select notice_id from notices join explnum on explnum_bulletin = 0 and explnum_notice = notice_id where (indexint = ".$this->indexint->indexint_id." or indexint in(select indexint_id from indexint where num_pclass = ".$this->indexint->id_pclass." and indexint_name like '".addslashes(trim($this->indexint->name,0))."%' and indexint_id != ".$this->indexint->indexint_id.")) and explnum_mimetype != 'URL'";
			//notice de bulletin
			$query.= " union select notice_id from notices join bulletins on niveau_biblio = 'b' and notice_id = num_notice and num_notice != 0 join explnum on explnum_bulletin = bulletin_id and explnum_notice = 0 where (indexint = ".$this->indexint->indexint_id." or indexint in (select indexint_id from indexint where num_pclass = ".$this->indexint->id_pclass." and indexint_name like '".addslashes(trim($this->indexint->name,0))."%' and indexint_id != ".$this->indexint->indexint_id.")) and explnum_mimetype != 'URL'";
			
			$this->filterNotices($query);		
		}
		return $this->notices;
	}
	
	function update_notice_infos($notice_id){
		if($notice_id*1 >0){
			$query = "update notices set indexint = ".$this->indexint->indexint_id." where notice_id = ".$notice_id;
			pmb_mysql_query($query);
		}
	}
}