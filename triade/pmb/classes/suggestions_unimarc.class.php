<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_unimarc.class.php,v 1.6 2021/08/02 12:03:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/iso2709.class.php");

class suggestions_unimarc{
	
	public $sugg_uni_id=0;
	public $sugg_uni_notice='';
	public $sugg_uni_origine='';
	public $sugg_uni_num_notice=0;
	
	/*
	 * Constructeur
	 */
	public function __construct($id=0){
		$this->sugg_uni_id = intval($id);
		$this->sugg_uni_notice = "";
		$this->sugg_uni_origine = "";
		$this->sugg_uni_num_notice = 0;
		if($this->sugg_uni_id){
			$req = "select * from import_marc where id_import='".$this->sugg_uni_id."'";
			$res = pmb_mysql_query($req);
			if($res){
				$uni = pmb_mysql_fetch_object($res);
				$this->sugg_uni_notice = $uni->notice;
				$this->sugg_uni_origine = $uni->origine;
				$this->sugg_uni_num_notice = $uni->no_notice;
			}			
		}
	}
	
	/*
	 * Enregistrement
	 */
	public function save(){
		$req = "insert into import_marc set notice='".addslashes($this->sugg_uni_notice)."', 
			origine='".addslashes($this->sugg_uni_origine)."',
			no_notice='".addslashes($this->sugg_uni_num_notice)."'";
		pmb_mysql_query($req); 
		$this->sugg_uni_id = pmb_mysql_insert_id();
	}
	
	/*
	 * Suppression
	 */
	public function delete(){
		$req = "delete from import_marc where origine='".$this->sugg_uni_origine."' and no_notice='".$this->sugg_uni_num_notice."'";
		pmb_mysql_query($req);
	}
	
}
?>