<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource.class.php,v 1.22 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource extends frbr_entity_root {
	protected $num_datanode;
	protected $entity_type;
	protected static $links_types = array();
	protected $used_external_filter=false;
	protected $external_filter;
	protected $used_external_sort=false;
	protected $external_sort;
	protected $limitable=true;
	
	public function __construct($id=0){
	    $this->id = (int) $id;
		parent::__construct();
	}
	
	/*
	 * Récupération des informations en base
	 */
	protected function fetch_data(){
		$this->parameters = new stdClass();
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
	
	
	protected function get_sub_datasources() {
		return array();
	}
	
	/*
	 * Méthode de génération du formulaire... 
	 */
	public function get_form(){
		$form = "<div class='row'>";
		$sub_datasources = $this->get_sub_datasources();
		if(count($sub_datasources)) {
			if(count($sub_datasources)>1) {
				$form .= "
					<div class='colonne3'>
						<label for='datanode_sub_datasource_choice'>".$this->format_text($this->msg['frbr_entity_common_datasource_sub_datasource_choice'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='datanode_sub_datasource_choice' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"frbrEntityLoadElemForm\", \"parameters\":{\"id\":\"0\", \"domId\":\"sub_datasource_form\",\"filterRefresh\":\"1\",\"sortRefresh\":\"1\"}}'>
							<option value='".$this->get_sub_datasource_default_value()."'>".$this->format_text($this->msg['frbr_entity_common_datasource_sub_datasource_choice'])."</option>";
				foreach($sub_datasources as $sub_datasource){
					$form.= "
							<option value='".$sub_datasource."'".(isset($this->parameters->sub_datasource_choice) && $sub_datasource == $this->parameters->sub_datasource_choice ? " selected='selected'" : "").">".$this->format_text($this->msg[$sub_datasource])."</option>";
				}
				$form.="
						</select>
					</div>";
			} else if (count($sub_datasources) == 1) {
				$form.="<input type='hidden' name='datanode_sub_datasource_choice' id='datanode_sub_datasource_choice' value='".$sub_datasources[0]."' />";
			}
		}
		$form.="	<div id='sub_datasource_form'>
						<input type='hidden' name='datanode_entity_type' id='datanode_entity_type' value='".$this->get_entity_type()."' />
					</div>";
		if ($this->limitable && isset($this->entity_type) && $this->entity_type && (count($sub_datasources)<=1)) {
			$form.= "
				<div class='row'>
					<div class='colonne3'>
						<label for='datanode_datasource_nb_max_elements'>".$this->format_text($this->msg['frbr_entity_common_datasource_nb_max_elements'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='datanode_datasource_nb_max_elements' value='".(isset($this->parameters->nb_max_elements) ? $this->parameters->nb_max_elements : '15')."'/>
					</div>
				</div>";
		}
		if (isset($this->parameters->sub_datasource_choice) && $this->parameters->sub_datasource_choice) {
			$form.="<script type='text/javascript'>
						require(['dojo/topic'],					
						function(topic){
							topic.publish('ParametersFormsReady', 'frbrEntityLoadElemForm', {'elem' : '".$this->parameters->sub_datasource_choice."', 'id':'".$this->id."', 'domId': 'sub_datasource_form', 'filterRefresh': '0', 'sortRefresh' :  '0'});	  
						});
					</script>";
		}
		$form.="</div>";
		
		return $form;
	}	
	
	/*
	 * Sauvegarde des infos depuis un formulaire...
	 */
	public function save_form(){
		global $datanode_sub_datasource_choice;
		global $datanode_datasource_nb_max_elements;
		
		$this->parameters->sub_datasource_choice = $datanode_sub_datasource_choice;
		$this->parameters->nb_max_elements = (int) $datanode_datasource_nb_max_elements;
		
		if($this->id){
			$query = "update frbr_datanodes_content set";
			$clause = " where id_datanode_content='".$this->id."'";
		}else{
			$query = "insert into frbr_datanodes_content set";
			$clause = "";
		}
		$query.= " 
			datanode_content_type = 'datasource',
			datanode_content_object = '".$this->class_name."',".
			($this->num_datanode ? "datanode_content_num_datanode = '".$this->num_datanode."'," : "")."		
			datanode_content_data = '".addslashes($this->json_encode())."'
			".$clause;
		$result = pmb_mysql_query($query);
		
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			//on supprime les anciennes sources de données...
			$query = "delete from frbr_datanodes_content where id_datanode_content != '".$this->id."' and datanode_content_type='datasource' and datanode_content_num_datanode = '".$this->num_datanode."'";
			pmb_mysql_query($query);
			
			return true;
		}else{
			return false;
		}
	}
	
	/*
	 * Méthode de suppression
	 */
	public function delete(){
		if($this->id){
			$query = "delete from frbr_datanode_content where id_datanode_content = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if($result){
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function get_format_data_structure(){
		return array();
	}
	
	/**
	 * 
	 * @param string $query
	 * @return arrau:
	 */
	protected function get_datas_from_query($query) {
		$result = pmb_mysql_query($query);
		$datas = array();
		while ($row = pmb_mysql_fetch_object($result)) {
			$datas[$row->parent][] = $row->id;
			$datas[0][] = $row->id;
		}
		return $datas;
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		return $datas;
	}
	
	/*
	 * Méthode pour filtrer les résultats
	 */
	public function filter_datas($datas=array()){
	    if (count($datas)) {
	        if ($this->used_external_filter){
	            foreach($datas as $parent => $data) {
	                $datas[$parent] = $this->external_filter->filter_datas($data);
	            }
	        }
	    }
	    return $datas;
	}
	
	/*
	 * Méthode pour trier les résultats
	 */
	public function sort_datas($datas=array()){
		if (count($datas)) {
		    if ($this->used_external_sort){
		        foreach($datas as $parent => $data) {
		            $datas[$parent] = $this->external_sort->sort_datas($data);
		        }
		    }
		}
		return $datas;
	}
	
	public function get_num_datanode(){
		return $this->num_datanode;
	}
	
	public function set_num_datanode($id){
	    $this->num_datanode = (int) $id;
	}
	
	public function get_entity_type() {
		return $this->entity_type;
	}

	public function set_entity_class_name($entity_class_name){
		$this->entity_class_name = $entity_class_name;
		$this->fetch_managed_datas("filters");
	}
	
	public function set_filter($filter){
		$this->used_external_filter = true;
		$this->external_filter = $filter;
	}
	
	public function set_sort($sort){
		$this->used_external_sort = true;
		$this->external_sort = $sort;
	}
	
	public function have_child(){
		$query = "select id_datanode from frbr_datanodes where datanode_num_parent = '".$this->num_datanode."' ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			return true;
		}
		return false;
	}
	
	protected function get_sub_datasource_default_value(){
		return '';
	}
}