<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_filter.class.php,v 1.14 2023/05/03 15:15:15 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_filter extends frbr_entity_root{
	protected $num_datanode;
	
	protected $indexation_type;
	protected $indexation_path;
	protected $fields;
	protected $details;
	public $manage_id;
	public function __construct($id=0){
	    $this->id = (int) $id;
		parent::__construct();
	}
	
	public function set_num_datanode($id){
	    $this->num_datanode = (int) $id;
	}
	
	public function get_num_datanode(){
	    return $this->num_datanode;
	}
	
	/*
	 * Récupération des informations en base
	 */
	protected function fetch_data(){
		$this->parameters = new stdClass();
		$this->set_num_datanode(0);
		if($this->id){
		//on commence par aller chercher ses infos
			$query = " select id_datanode_content, datanode_content_num_datanode, datanode_content_data from frbr_datanodes_content where id_datanode_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->id = (int) $row->id_datanode_content;
				$this->num_datanode = (int) $row->datanode_content_num_datanode;
				$this->json_decode($row->datanode_content_data);
			}	
		}
	}
	
	public function get_human_query() {
		global $msg;
		global $charset;
		
		$human_query = "";
		$details = (isset($this->managed_datas[$this->manage_id]['details']) ? $this->managed_datas[$this->manage_id]['details'] : []);
		$frbr_instance_fields = new frbr_filter_fields($this->indexation_type, $this->indexation_path, $details);
		if (isset($this->managed_datas[$this->manage_id]['fields'])) {
			foreach ($this->managed_datas[$this->manage_id]['fields'] as $field) {
				$f=explode("_",$field['NAME']);
				$title = "";
				if ($f[0] == "authperso") {
				    $groups = $frbr_instance_fields->grouped();
				    foreach($groups as $group) {
				        foreach ($group as $id => $label) {
				            if ($id == $field['NAME']) {
				                $title = $label;
				                break;
				            }
				        }
				    }
				} elseif ($f[2] && isset($frbr_instance_fields::$fields[$frbr_instance_fields->type]["FIELD"][$f[1]]["TABLE"][0]["TABLEFIELD"][$f[2]]["NAME"])) {
					$title = $msg[$frbr_instance_fields::$fields[$frbr_instance_fields->type]["FIELD"][$f[1]]["TABLE"][0]["TABLEFIELD"][$f[2]]["NAME"]];
				} elseif (isset($msg[$frbr_instance_fields::$fields[$frbr_instance_fields->type]["FIELD"][$f[1]]["NAME"]])) {
					$title = $msg[$frbr_instance_fields::$fields[$frbr_instance_fields->type]["FIELD"][$f[1]]["NAME"]];
				} else  {
				    $title = $frbr_instance_fields::$fields[$frbr_instance_fields->type]["FIELD"][$f[1]]["NAME"];
				}
				switch ($field['INTER']) {
					case "and":
						$inter_op=$msg["search_and"];
						break;
					case "or":
						$inter_op=$msg["search_or"];
						break;
					case "ex":
						$inter_op=$msg["search_exept"];
						break;
					default:
						$inter_op="";
						break;
				}
				if ($inter_op) $inter_op="<strong>".htmlentities($inter_op,ENT_QUOTES,$charset)."</strong>";
				$human_query .= $inter_op." <i><strong>".htmlentities($title,ENT_QUOTES,$charset)."</strong> ".htmlentities(get_msg_to_display(frbr_filter_fields::get_operators()[$field['OP']]),ENT_QUOTES,$charset)." (".htmlentities($field['FIELD'][0],ENT_QUOTES,$charset).")</i> ";
			}
		}
		return $human_query;
	}
	
	/*
	 * Méthode de génération du formulaire... 
	 */
	public function get_form(){
		$form = "";
		if (isset($this->manage_id) && $this->manage_id) {				
			$form = $this->get_human_query();
			$form .= "<img src='".get_url_icon('b_edit.png')."' alt='".$this->format_text($this->msg['frbr_entity_common_filter_edit'])."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"loadDialog\", \"parameters\":{\"element\":\"filter\", \"idElement\":\"".$this->num_datanode."\", \"manageId\": \"".str_replace("filter", "", $this->manage_id)."\", \"quoi\" : \"filters\", \"numPage\":\"".static::get_num_page_from_num_datanode($this->num_datanode)."\"}}' title=\"".$this->format_text($this->msg['frbr_entity_common_filter_edit'])."\" />";
		}
		return $form;
	}
	
	/*
	 * Sauvegarde des infos depuis un formulaire...
	 */
	public function save_form(){
		global $datanode_filter_choice;
		
		$this->parameters->id = str_replace("filter", "", $datanode_filter_choice);
		
		if($this->id){
			$query = "update frbr_datanodes_content set";
			$clause = " where id_datanode_content=".$this->id;
		}else{
			$query = "insert into frbr_datanodes_content set";
			$clause = "";
		}
		$query.= " 
			datanode_content_type = 'filter',
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
			$query = "delete from frbr_datanodes_content where id_datanode_content != '".$this->id."' and datanode_content_type='filter' and datanode_content_num_datanode = '".$this->num_datanode."'";
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
			$query = "delete from frbr_datanodes_content where id_datanode_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if($result){
				return true;
			}else{
				return false;
			}
		}
	}

	public function filter_datas($datas){
		$filtered_datas = array();
		if(is_array($this->parameters->id) && is_array($datas) && count($this->parameters->id) && count($datas)){
		    $frbr_filter_fields = new frbr_filter_fields($this->indexation_type, $this->indexation_path, $this->details);
			$frbr_filter_fields->unformat_fields($this->fields);
			$filtered_datas = $frbr_filter_fields->filter_datas($datas);
		}
		return $filtered_datas;
	}
	
	public function set_entity_class_name($entity_class_name){
		$this->entity_class_name = $entity_class_name;
		$this->fetch_managed_datas("filters");
	}
	
	public function set_manage_id($manage_id){
		$this->manage_id = $manage_id;
	}
	
	public function set_indexation_type($indexation_type) {
		$this->indexation_type = $indexation_type;
	}
	
	public function set_indexation_path($indexation_path) {
		$this->indexation_path = $indexation_path;
	}
	
	public function set_fields($fields) {
		$this->fields = $fields;
	}
	
	public function set_details($details) {
	    $this->details = $details;
	}
}