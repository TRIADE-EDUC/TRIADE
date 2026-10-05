<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_works_datasource_events.class.php,v 1.2 2018/08/16 10:27:33 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_works_datasource_events extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'authperso';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "SELECT oeuvre_event_authperso_authority_num AS id, oeuvre_event_tu_num AS parent FROM tu_oeuvres_events JOIN authperso_authorities ON oeuvre_event_authperso_authority_num = id_authperso_authority WHERE oeuvre_event_tu_num IN (".implode(',', $datas).")";
		if (!empty($this->parameters->authperso_id)) {
		    if (is_array($this->parameters->authperso_id)) {
		        $query .= " AND authperso_authority_authperso_num IN ('".implode("','", $this->parameters->authperso_id)."')";
		    } else {
		        $query .= " AND authperso_authority_authperso_num = '".$this->parameters->authperso_id."'";
		    }
		}
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
	
	public function save_form() {
	    global $datanode_authperso_id;
	    if(isset($datanode_authperso_id)){
	        $this->parameters->authperso_id = $datanode_authperso_id;
	    } else {
	        unset($this->parameters->authperso_id);
	    }
	    return parent::save_form();
	}
	
	public function get_form() {
	    if (!isset($this->parameters->authperso_id)) {
	        $this->parameters->authperso_id = '';
	    }
	    $form = parent::get_form();
	    $form.= "
            <div class='row'>
				<div class='colonne3'>
					<label for='datanode_work_link_type'>".$this->format_text($this->msg['frbr_entity_works_datasource_events_type'])."</label>
				</div>
				<div class='colonne-suite'>
					".$this->get_event_type_selector($this->parameters->authperso_id)."
				</div>
			</div>";
	    return $form;
	}
	
	public function get_event_type_selector($selected = array()) {
	    $events = authpersos::get_oeuvre_event_authpersos();
	    $selector = "<select name='datanode_authperso_id' id='datanode_authperso_id'>";
	    $options = '';
	    
	    foreach($events as $nb => $event){	        
	        if ((is_array($selected) && in_array($event['id'], $selected)) || ($event['id'] == $selected)) {
	            $options .= "<option value='".$event['id']."' selected='selected'>".$event['name']."</option>";
	        } else {
	            if (empty($selected) && $nb == 0) {
	                $options .= "<option value='".$event['id']."' selected='selected'>".$event['name']."</option>";
	            } else  {
	                $options .= "<option value='".$event['id']."'>".$event['name']."</option>";
	            }
	        }
	    }
	    $selector.= $options;
	    $selector.= '</select>';
	    return $selector;
	}
}