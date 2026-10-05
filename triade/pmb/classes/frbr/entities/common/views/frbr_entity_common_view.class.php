<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_view.class.php,v 1.6.8.1 2025/04/24 12:37:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_view extends frbr_entity_root{

	public function __construct($id=0){
	    $this->id = (int) $id;
		parent::__construct();
	}

	protected function fetch_data(){
		$this->parameters = new stdClass();
		if($this->id){
		//on commence par aller chercher ses infos
			$query = " select id_cadre_content, cadre_content_num_cadre, cadre_content_data from frbr_cadres_content where id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->id = (int) $row->id_cadre_content;
				$this->num_cadre = (int) $row->cadre_content_num_cadre;
				$this->json_decode($row->cadre_content_data);
			}
		}
	}

	public function save_form(){
		if($this->id){
			$query = "update frbr_cadres_content set";
			$clause = " where id_cadre_content=".$this->id;
		}else{
			$query = "insert into frbr_cadres_content set";
			$clause = "";
		}
		$query.= "
			cadre_content_type = 'view',
			cadre_content_object = '".$this->class_name."',".
			($this->num_cadre ? "cadre_content_num_cadre = '".$this->num_cadre."'," : "")."
			cadre_content_data = '".addslashes($this->json_encode())."'
			".$clause;
		$result = pmb_mysql_query($query);
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			//on supprime les anciennes vues...
			$query = "delete from frbr_cadres_content where id_cadre_content != '".$this->id."' and cadre_content_type='view' and cadre_content_num_cadre = '".$this->num_cadre."'";
			pmb_mysql_query($query);

			return true;
		}
		return false;
	}

	public function set_num_cadre($id){
	    $this->num_cadre = (int) $id;
	}

	/*
	 * Méthode de suppression
	 */
	public function delete(){
		global $dbh;
		if($this->id){
			$query = "delete from frbr_cadres_content where id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if($result){
				return true;
			}else{
				return false;
			}
		}
	}

	public function get_form(){
		return "";
	}

	public function render($datas, $grouped_datas = []){
		return "";
	}

	public function get_format_data_structure(){
		return array();
	}

	public function set_entity_class_name($entity_class_name){
		$this->entity_class_name = $entity_class_name;
		$this->fetch_managed_datas("view");
	}
}