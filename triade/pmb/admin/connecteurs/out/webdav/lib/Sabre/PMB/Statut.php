<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Statut.php,v 1.5 2016-01-26 15:36:15 dgoron Exp $
namespace Sabre\PMB;

class Statut extends Collection {
	protected $statut_id;

	function __construct($name,$config) {
		parent::__construct($config);
		
		$this->statut_id = substr($this->get_code_from_name($name),1);
		$query = "select gestion_libelle from notice_statut where id_notice_statut = ".$this->statut_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$this->statut_libelle = pmb_mysql_result($result,0,0);	
		}
		$this->type = "statut";
	}

	function getName() {
		return $this->format_name($this->statut_libelle." (S".$this->statut_id.")");
	}
	
	function getNotices(){
		
		$this->notices = array();		
		if(!count($this->notices)){
			if($this->statut_id){
				//notice
				$query = "select notice_id from notices join explnum on explnum_bulletin = 0 and explnum_notice = notice_id where statut = '".$this->statut_id."' and explnum_mimetype != 'URL'";
				//notice de bulletin
				$query.= " union select notice_id from notices join bulletins on niveau_biblio = 'b' and notice_id = num_notice and num_notice != 0 join explnum on explnum_bulletin = bulletin_id and explnum_notice = 0 where statut = '".$this->statut_id."' and explnum_mimetype != 'URL'";
				$this->filterNotices($query);		
			}
		}
		return $this->notices;
	}
	
	function update_notice_infos($notice_id){
		if($notice_id*1 >0){
			$query = "update notices set statut = ".$this->statut_id." where notice_id = ".$notice_id;
			pmb_mysql_query($query);
		}
	}
}