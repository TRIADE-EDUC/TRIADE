<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource.class.php,v 1.24 2024/03/13 10:38:08 qvarin Exp $

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
	protected $parent_type = "";
	private static $main_entity_type = "";
	private static $main_entity_id = 0;

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
		$form.= $this->get_sub_datasources_form();
		if (!empty($this->limitable) && !empty($this->entity_type) && (count($this->get_sub_datasources()) <=1)) {
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
		if (!empty($this->parameters->sub_datasource_choice)) {
			$form.="<script>
						require(['dojo/topic'],
						function(topic){
							topic.publish('ParametersFormsReady', 'frbrEntityLoadElemForm', {'elem' : '".$this->parameters->sub_datasource_choice."', 'id':'".$this->id."', 'domId': 'sub_datasource_form', 'filterRefresh': '0', 'sortRefresh' :  '0', 'numPage' : '".$this->get_parameter("num_page")."'});
						});
					</script>";
		}
		$form.="</div>";

		return $form;
	}

    /**
    * formulaire de la sous datasource
    * @return string
    */
	protected function get_sub_datasources_form() {
	    $form = "";
	    $sub_datasources = $this->get_sub_datasources();
	    if (!empty($sub_datasources) && is_array($sub_datasources)) {
	        if (count($sub_datasources) > 1) {
	            $form .= "
					<div class='colonne3'>
						<label for='datanode_sub_datasource_choice'>".$this->format_text($this->msg['frbr_entity_common_datasource_sub_datasource_choice'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='datanode_sub_datasource_choice' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"frbrEntityLoadElemForm\", \"parameters\":{\"id\":\"0\", \"domId\":\"sub_datasource_form\",\"filterRefresh\":\"1\",\"sortRefresh\":\"1\", \"numPage\" : \"".$this->get_parameter("num_page")."\"}}'>
							<option value='".$this->get_sub_datasource_default_value()."'>".$this->format_text($this->msg['frbr_entity_common_datasource_sub_datasource_choice'])."</option>";
	            foreach ($sub_datasources as $sub_datasource) {
	                $form.= "
							<option value='".$this->get_sub_datasource_value($sub_datasource)."'".(isset($this->parameters->sub_datasource_choice) && $this->get_sub_datasource_value($sub_datasource) == $this->parameters->sub_datasource_choice ? " selected='selected'" : "").">".$this->format_text($this->msg[$sub_datasource])."</option>";
	            }
	            $form.="
						</select>
					</div>";
	        } else if (count($sub_datasources) == 1) {
	            $form.="<input type='hidden' name='datanode_sub_datasource_choice' id='datanode_sub_datasource_choice' value='".$this->get_sub_datasource_value($sub_datasources[0])."' />";
	        }
	    }
	    $form.="	<div id='sub_datasource_form'>
						<input type='hidden' name='datanode_entity_type' id='datanode_entity_type' value='".$this->get_entity_type()."' />
					</div>";
	    return $form;
	}

	/**
	 * pour formater si besoin la valeur de la sous datasource, dans authperso par exemple
	 * @param string $sub_datasource
	 * @return string
	 */
	protected function get_sub_datasource_value($sub_datasource) {
	    return $sub_datasource;
	}

	/*
	 * Sauvegarde des infos depuis un formulaire...
	 */
	public function save_form(){
		global $datanode_sub_datasource_choice;
		global $datanode_datasource_nb_max_elements;

		$this->parameters->sub_datasource_choice = $datanode_sub_datasource_choice;
		$this->parameters->nb_max_elements = (int) $datanode_datasource_nb_max_elements;

		$this->save_custom_field_parameters();

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

			if (isset($row->group_key)) {
			    if (empty($row->group_key)) {
			        $row->group_key = 0;
			    }
			    $label = $this->get_label_from_group($row->group_key);
			    if (!isset($datas["group"])) {
			        $datas["group"] = [];
			    }
			    if (!isset($datas["group"][$row->group_key])) {
			        $datas["group"][$row->group_key] = [];
			    }
			    $datas["group"][$row->group_key]["label"] = $label;
			    $datas["group"][$row->group_key]["values"][] = $row->id;
			}
		}
		return $datas;
	}

	protected function get_label_from_group($group) {
	    return "";
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
	    $datas = $this->clear_data($datas);
		return $datas;
	}

	/*
	 * Méthode pour filtrer les résultats
	 */
	public function filter_datas($datas=array()){
	    if (!empty($datas) && $this->used_external_filter) {
			foreach($datas as $parent => $data) {
				$datas[$parent] = $this->external_filter->filter_datas($data);
			}
	    }
	    return $datas;
	}

	/*
	 * Méthode pour trier les résultats
	 */
	public function sort_datas($datas=array()) {
		if (is_countable($datas) && count($datas) && $this->used_external_sort) {
			foreach($datas as $parent => $data) {
				$datas[$parent] = $this->external_sort->sort_datas($data);
			}
		}
		return $datas;
	}

	public function get_num_datanode(){
	    if ($this->num_datanode) {
	        return $this->num_datanode;
	    }
	    return 0;
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

	protected function filter_data_with_access_rights($data) {
	    return $data;
	}

	public function set_parent_type($parent_type){
	    $this->parent_type = $parent_type;
	    return $this;
	}
	protected function get_parent_type(){
	    return $this->parent_type;
	}
    /**
     * pour les champs perso
     * @param string $type
     * @return number
     */
    protected function get_aut_type_from_entity_type($type) {
        global $authperso_num;
	    switch ($type) {
	        case 'authors':
	            return 1;
	        case 'categories':
	            return 2;
	        case 'publishers':
	            return 3;
	        case 'collections':
	            return 4;
	        case 'subcollections':
	            return 5;
	        case 'series':
	            return 6;
	        case 'indexint':
	            return 7;
	        case 'works':
	            return 8;
	        case 'concepts':
	            return 9;
	        case 'authperso':
	            if (!empty($this->parameters->authperso_id)) {
	                return 1000 + intval($this->parameters->authperso_id);
	            }
	            if (!empty($authperso_num)) {
	                return 1000 + intval($authperso_num);
	            }
	            return 1000;
	        default:
	            return 0;
	    }
	}

    /**
     * @param string $type
     * @return number
     */
    protected function get_type_from_entity_type($type) {
        switch ($type) {
            case 'records':
                return TYPE_NOTICE;
	        case 'authors':
	            return TYPE_AUTHOR;
	        case 'categories':
	            return TYPE_CATEGORY;
	        case 'publishers':
	            return TYPE_PUBLISHER;
	        case 'collections':
	            return TYPE_COLLECTION;
	        case 'subcollections':
	            return TYPE_SUBCOLLECTION;
	        case 'series':
	            return TYPE_SERIE;
	        case 'indexint':
	            return TYPE_INDEXINT;
	        case 'works':
	            return TYPE_TITRE_UNIFORME;
	        case 'concepts':
	            return TYPE_CONCEPT;
	        case 'authperso':
	            return TYPE_AUTHPERSO;
	        default:
	            return 0;
	    }
	}

	/**
	 * methode a deriver au besoin pour les formulaires de sous datasources
	 * @return string
	 */
	public function get_sub_form() {
	    return $this->get_form();
	}

	/**
	 * enregistrement du parametrage du champ perso quand il est poste
	 */
	protected function save_custom_field_parameters() {
	    global $datanode_datasource_custom_field;
	    if (!empty($datanode_datasource_custom_field)) {
	        $custom_field = explode('|||', $datanode_datasource_custom_field);
	        $this->parameters->prefix = $custom_field[0];
	        $this->parameters->id = $custom_field[1];
	        $this->parameters->datatype = $custom_field[2];
	    }

	    global $datanode_datasource_used_in_custom_field;
	    if (!empty($datanode_datasource_used_in_custom_field)) {
	        $custom_field = explode('|||', $datanode_datasource_used_in_custom_field);
	        $this->parameters->prefix = $custom_field[0];
	        $this->parameters->id = $custom_field[1];
	        $this->parameters->datatype = $custom_field[2];
	    }
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_sub_datasource_datas($datas=array()){
	    if(!empty($this->get_parameters()->sub_datasource_choice)) {
	        if (strpos($this->get_parameters()->sub_datasource_choice, "authperso") !== false) {
	            $authperso =  preg_split("#_([\d]+)#", $this->get_parameters()->sub_datasource_choice, 0 ,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	            $sub_datasource = new $authperso[0]();
	            if (!empty($authperso[1])) {
	                $sub_datasource->set_authperso_id($authperso[1]);
	            }
	        } else {
	            $class_name = $this->get_parameters()->sub_datasource_choice;
	            $sub_datasource = new $class_name();
	        }
	        $sub_datasource->set_parameters($this->parameters);
	        if(isset($this->external_filter) && $this->external_filter) {
	            $sub_datasource->set_filter($this->external_filter);
	        }
	        if(isset($this->external_sort) && $this->external_sort) {
	            $sub_datasource->set_sort($this->external_sort);
	        }
	        return $sub_datasource->get_datas($datas);
	    }
	    return $datas;
	}

	public static function set_main_entity($id, $type) {
	    $id = intval($id);
	    if (!empty($id)) {
	        self::$main_entity_id = $id;
	    }
	    if (!empty($type)) {
	        self::$main_entity_type = $type;
	    }
	}

	private function clear_data($datas) {
	    if ($this->entity_type == self::$main_entity_type) {
	        foreach ($datas as $id => $data) {
	            if (is_array($data)) {
    	            for($i = 0; $i < count($data); $i++) {
    	                if ($data[$i] == self::$main_entity_id) {
    	                    unset($data[$i]);
    	                }
    	            }
	                $datas[$id] = array_values($data);
	            }
	        }
	    }
	    return $datas;
	}

	protected function get_main_entity_id() {
	    return self::$main_entity_id;
	}

	protected function get_main_entity_type() {
	    return self::$main_entity_type;
	}
}