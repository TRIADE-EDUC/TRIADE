<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_backbone.class.php,v 1.9 2019/08/22 09:44:33 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_backbone extends frbr_entity_root{
	protected $num_page;

	protected $indexation_type;
	protected $indexation_path;

	public function __construct($id=0){
	    $this->id = (int) $id;
		parent::__construct();
	}

	public function set_num_page($num_page){
	    $this->num_page = (int) $num_page;
	}

	/*
	 * Récupération des informations en base
	 */
	protected function fetch_data(){
		$this->parameters = new stdClass();
		if($this->id){
			$query = " select id_page_content, page_content_num_page, page_content_data from frbr_pages_content where id_page_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->id = (int) $row->id_page_content;
				$this->num_page = (int) $row->page_content_num_page;
				$this->json_decode($row->page_content_data);
			}
		}
	}

	public function get_human_query() {
		global $msg;
		global $charset;

		$human_query = "";
		$frbr_instance_fields = new frbr_backbone_fields($this->indexation_type, $this->indexation_path);
		foreach ($this->managed_datas[$this->manage_id]['fields'] as $field) {
			$f = explode("_", $field['NAME']);
			if (isset($frbr_instance_fields::$fields[$frbr_instance_fields->type]["FIELD"][$f[1]]["TABLE"][0]["TABLEFIELD"]) && $f[2] && $this->indexation_type != 'skos') {
				$title = $msg[$frbr_instance_fields::$fields[$frbr_instance_fields->type]["FIELD"][$f[1]]["TABLE"][0]["TABLEFIELD"][$f[2]]["NAME"]];
			} else {
			    if (isset($msg[$frbr_instance_fields::$fields[$frbr_instance_fields->type]["FIELD"][$f[1]]["NAME"]])) {
			        $title =$msg[$frbr_instance_fields::$fields[$frbr_instance_fields->type]["FIELD"][$f[1]]["NAME"]];
			    } else {
			        $title =$frbr_instance_fields::$fields[$frbr_instance_fields->type]["FIELD"][$f[1]]["NAME"];
			    }
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
		return $human_query;
	}

	/*
	 * Méthode de génération du formulaire...
	 */
	public function get_form(){
		$form = "";
		if (isset($this->manage_id) && $this->manage_id) {
			$form = $this->get_human_query();
			$form .= "<img src='".get_url_icon('b_edit.png')."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"loadDialog\", \"parameters\":{\"element\":\"backbone\", \"idElement\":\"".$this->num_page."\", \"manageId\": \"".str_replace("backbone", "", $this->manage_id)."\", \"quoi\" : \"backbones\", \"numPage\":\"".$this->num_page."\"}}' title=\"".$this->format_text($this->msg['frbr_entity_common_backbone_edit'])."\" />";
		}
		return $form;
	}

	/*
	 * Sauvegarde des infos depuis un formulaire...
	 */
	public function save_form(){
		global $page_backbone_choice;

		$this->parameters->id = str_replace("backbone", "", $page_backbone_choice);

		if($this->id){
			$query = "update frbr_pages_content set";
			$clause = " where id_page_content=".$this->id;
		}else{
			$query = "insert into frbr_pages_content set";
			$clause = "";
		}
		$query.= "
			page_content_type = 'backbone',
			page_content_object = '".$this->class_name."',".
			($this->num_page ? "page_content_num_page = '".$this->num_page."'," : "")."
			page_content_data = '".addslashes($this->json_encode())."'
			".$clause;
		$result = pmb_mysql_query($query);
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			//on supprime les anciens filtres...
			$query = "delete from frbr_pages_content where id_page_content != '".$this->id."' and page_content_type='backbone' and page_content_num_page = '".$this->num_page."'";
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
			$query = "delete from frbr_pages_content where id_page_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if($result){
				return true;
			}else{
				return false;
			}
		}
	}

	public function set_entity_class_name($entity_class_name){
		$this->entity_class_name = $entity_class_name;
		$this->fetch_managed_datas("backbones");
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
}