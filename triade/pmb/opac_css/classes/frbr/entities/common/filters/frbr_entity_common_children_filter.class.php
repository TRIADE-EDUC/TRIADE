<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_children_filter.class.php,v 1.2 2018-08-22 16:23:31 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_children_filter extends frbr_entity_root{
	protected $num_datanode;
	
	public function __construct($id=0){
		$this->id = $id+0;
		parent::__construct();
	}
	
	public function set_num_datanode($id){
		$this->num_datanode = $id+0;
	}
	
	/*
	 * Récupération des informations en base
	 */
	protected function fetch_data(){
		$this->parameters = new stdClass();
		$this->set_num_datanode(0);
		if($this->id){
		//on commence par aller chercher ses infos
			$query = " 
			    SELECT id_datanode_content, datanode_content_num_datanode, datanode_content_data 
			    FROM frbr_datanodes_content 
			    WHERE id_datanode_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->id = $row->id_datanode_content+0;
				$this->num_datanode = $row->datanode_content_num_datanode+0;
				$this->json_decode($row->datanode_content_data);
			}	
		}
	}
	
	/*
	 * Sauvegarde des infos depuis un formulaire...
	 */
	public function save_form(){
	    global $datanode_children_filter_choice, $datanode_children_filter_id, $datanode_children_filter_operator;
		
	    $this->parameters->children_filter = (!empty($datanode_children_filter_choice) ? $datanode_children_filter_choice : "");
	    $this->parameters->children_filter_operator = (!empty($datanode_children_filter_operator) ? $datanode_children_filter_operator : "");
		$this->id = $datanode_children_filter_id;
		
		if($this->id){
			$query = "UPDATE frbr_datanodes_content SET";
			$clause = " WHERE id_datanode_content=".$this->id;
		}else{
			$query = "INSERT INTO frbr_datanodes_content SET";
			$clause = "";
		}
		$query.= " 
			datanode_content_type = 'children_filter',
			datanode_content_object = '".$this->class_name."',".
			($this->num_datanode ? "datanode_content_num_datanode = '".$this->num_datanode."'," : "")."		
			datanode_content_data = '".addslashes($this->json_encode())."'
			".$clause;
		$result = pmb_mysql_query($query);
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			//on supprime les anciens filtres...
			$query = "DELETE FROM frbr_datanodes_content WHERE id_datanode_content != '".$this->id."' AND datanode_content_type='children_filter' AND datanode_content_num_datanode = '".$this->num_datanode."'";
			pmb_mysql_query($query);
			
			return true; 
		}
		return false;
	}

	/*
	 * Méthode de suppression
	 */
	public function delete(){
		if($this->id){
			$query = "DELETE FROM frbr_datanodes_content WHERE id_datanode_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if($result){
				return true;
			}else{
				return false;
			}
		}
	}
}