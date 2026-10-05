<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_datanode.class.php,v 1.5.8.1 2025/01/23 15:48:16 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_datanode extends frbr_entity_common_entity_datanode {
    /**
     * num d'authperso temporaire tant qu'on a pas enregistre la datasource
     * @var int
     */
    protected $temp_authperso_id = 0;

	public function __construct($id=0){
		parent::__construct($id);
	}

	protected  function format_datasource_name($name) {
	    if (strpos($name, "authperso") !== false) {
	        $authperso =  preg_split("#_([\d]+)#", $name, 0 ,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	        if (!empty($authperso[1]) && intval($authperso[1])) {
	            $query = "SELECT authperso_name FROM authperso WHERE id_authperso = ".$authperso[1];
	            $result = pmb_mysql_query($query);
	            if (pmb_mysql_num_rows($result)) {
	                $row = pmb_mysql_fetch_assoc($result);
	                return $this->format_text($row['authperso_name']);
	            }
	        }
	    }
	    return $this->format_text($this->msg[$name]);
	}

	/**
	 * donnees complementaires
	 * @return array
	 */
	public function get_additional_managed_datas() {
	    return [
	        "authperso_id" => $this->get_datasource_data('authperso_id'),
	    ];
	}

	public function get_sort_selector(){
	    global $msg, $charset;
	    $form = "";
	    $authperso_id = $this->get_datasource_data('authperso_id');
	    if (empty($authperso_id)) {
	        $authperso_id = $this->temp_authperso_id;
	    }
	    if($this->entity_type){
            $form .= "<select id='datanode_sort_choice' name='datanode_sort_choice' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"frbrEntityLoadManagedElemForm\", \"parameters\":{\"elem\":\"frbr_entity_common_sort\", \"id\":\"0\", \"domId\":\"sort_form\", \"numPage\":\"".$this->page->get_id()."\", \"className\" : \"".$this->class_name."\", \"indexation\" : ".encoding_normalize::json_encode($this->informations['indexation'])."}}'>
 						<option value=''>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_sort_choice'])."</option>";
            if(isset($this->managed_datas['sorting'])) {
                foreach($this->managed_datas['sorting'] as $key => $infos) {
                    if (isset($infos["details"]["authperso_id"]) && $infos["details"]["authperso_id"] == $authperso_id) {
                        $form.= "
                        <option value='".$key."' ".(isset($this->sort['data']) && $key == "sort".$this->sort['data']->id ? "selected='selected'" : "").">".$infos['name']."</option>";
                    }
                }
            }
            $form.="
				</select>
			<img src='".get_url_icon('add.png')."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"loadDialog\", \"parameters\":{\"element\":\"sort\", \"idElement\":\"".$this->id."\", \"manageId\": \"0\", \"quoi\" : \"sorting\", \"className\" : \"".$this->class_name."\", \"authperso_id\" : \"".intval($authperso_id)."\"}}' title=\"".$this->format_text($this->msg['frbr_entity_common_entity_datanode_sort_create'])."\" />";
	    } else {
	        $form .= "<p>".htmlentities($msg['frbr_datasource_choice'], ENT_QUOTES, $charset)."</p>";
	    }
	    return $form;
	}

	public function get_filters_selector(){
	    global $msg, $charset;
	    $form = "";
	    $authperso_id = $this->get_datasource_data('authperso_id');
	    if (empty($authperso_id) ) {
	        $authperso_id = $this->temp_authperso_id;
	    }
	    if($this->entity_type){
            $form .= "
 					<select id='datanode_filter_choice' name='datanode_filter_choice' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"frbrEntityLoadManagedElemForm\", \"parameters\":{\"elem\":\"frbr_entity_common_filter\", \"id\":\"0\", \"domId\":\"filter_form\", \"numPage\":\"".$this->page->get_id()."\", \"className\" : \"".$this->class_name."\", \"indexation\" : ".encoding_normalize::json_encode($this->informations['indexation'])."}}'>
 						<option value=''>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_filter_choice'])."</option>";
            if(isset($this->managed_datas['filters'])) {
                foreach($this->managed_datas['filters'] as $key => $infos) {
                    if (isset($infos["details"]["authperso_id"]) && $infos["details"]["authperso_id"] == $authperso_id) {
                        $form.= "
						<option value='".$key."' ".(isset($this->filter['data']) && $key == "filter".$this->filter['data']->id ? "selected='selected'" : "").">".$infos['name']."</option>";
                    }
                }
            }
            $form.="
					</select>";
            $form.="<img src='".get_url_icon('add.png')."' alt='".$msg["925"]."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"loadDialog\", \"parameters\":{\"element\":\"filter\", \"idElement\":\"".$this->id."\", \"manageId\": 0, \"quoi\" : \"filters\", \"className\" : \"".$this->class_name."\", \"authperso_id\" : \"".intval($authperso_id)."\"}}' title=\"".$this->format_text($this->msg['frbr_entity_common_entity_datanode_filter_create'])."\" />";
	    } else {
	        $form .= "<p>".htmlentities($msg['frbr_datasource_choice'], ENT_QUOTES, $charset)."</p>";
	    }
	    return $form;
	}

	public function set_temp_authperso_id($id) {
	    $this->temp_authperso_id = intval($id);
	}
}