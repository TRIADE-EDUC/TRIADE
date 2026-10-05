<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_expl.class.php,v 1.2 2023/08/17 09:47:55 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class frbr_entity_common_datasource_expl extends frbr_entity_common_datasource {
	
	protected $custom_list;
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->entity_type = "expl";
	}
		
	protected function get_prefixes() {
		return array(
			'expl'
		);
	}
	
	protected function get_custom_list() {
		if(!isset($this->custom_list)) {
			$this->custom_list = array();
			foreach ($this->get_prefixes() as $prefix) {
				$query = "select idchamp, titre, options, datatype from ".$prefix."_custom order by name";
				$result = pmb_mysql_query($query);
				while($row = pmb_mysql_fetch_assoc($result)) {
					$options = _parser_text_no_function_($row['options']);
					$this->custom_list[$prefix][] = $row;
				}
			}
		}
		
		return $this->custom_list;
	}
	
	protected function get_custom_list_selector() {
		global $charset;
		
		if(!isset($this->parameters->id)) $this->parameters->id = 0;
		
		$custom_list = $this->get_custom_list();
		$selector = "<select name='datanode_datasource_expl'>";
		foreach ($custom_list as $prefix=>$customs) {
			foreach ($customs as $custom) {
			    $selector .= "<option value='" . $custom['idchamp'] . "' " . ($this->parameters->id == $custom['idchamp'] ? "selected='selected'" : "").">".htmlentities($custom['titre'], ENT_QUOTES, $charset)."</option>";
			}
		}
		$selector .= "</select>";
		return $selector;
	}
	
	public function get_form(){
        $form = "<div class='row'>";
        $sub_datasources = $this->get_sub_datasources();
        if (!empty($sub_datasources) && is_array($sub_datasources)) {
            if (count($sub_datasources) > 1) {
                $form .= "
				<div class='colonne3'>
					<label for='datanode_sub_datasource_choice'>".$this->format_text($this->msg['frbr_entity_common_datasource_sub_datasource_choice'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='datanode_sub_datasource_choice' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"frbrEntityLoadElemForm\", \"parameters\":{\"id\":\"0\", \"domId\":\"sub_datasource_form\",\"filterRefresh\":\"1\",\"sortRefresh\":\"1\"}}'>
						<option value='".$this->get_sub_datasource_default_value()."'>".$this->format_text($this->msg['frbr_entity_common_datasource_sub_datasource_choice'])."</option>";
                foreach ($sub_datasources as $sub_datasource) {
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
    
        if (!empty($this->limitable) && !empty($this->entity_type) && (count($sub_datasources) <=1)) {
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
						topic.publish('ParametersFormsReady', 'frbrEntityLoadElemForm', {'elem' : '".$this->parameters->sub_datasource_choice."', 'id':'".$this->id."', 'domId': 'sub_datasource_form', 'filterRefresh': '0', 'sortRefresh' :  '0'});
					});
				</script>";
        }
        $form.="</div>";	    
		$form .= "
			<div class='row'>
				<div class='colonne3'>
 					<label for='datasource_expl'>".($this->msg['frbr_entity_common_datasource_expl_choice'])."</label>
				</div>
				<div class='colonne-suite'>
					".$this->get_custom_list_selector()."
				</div>
			</div>";
		return $form;
	}
	
	public function save_form(){
	    global $datanode_datasource_expl;
		$this->parameters->id = $datanode_datasource_expl;
		return parent::save_form();
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
	    $query = "select ".$this->parameters->prefix."_custom_".$this->parameters->datatype." as id, ".$this->parameters->prefix."_custom_origine as parent from ".$this->parameters->prefix."_custom_values where ".$this->parameters->prefix."_custom_champ = ".$this->parameters->id." and ".$this->parameters->prefix."_custom_origine in (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}

}