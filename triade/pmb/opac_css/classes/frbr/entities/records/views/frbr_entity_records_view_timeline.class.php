<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_view_timeline.class.php,v 1.2 2021/08/09 13:48:24 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// require_once($class_path."/notice_tpl.class.php");

class frbr_entity_records_view_timeline extends frbr_entity_common_view_timeline {
	
    protected static $prefix = 'notices';
    
	public function __construct($id=0){
		parent::__construct($id);
		
	}
	
	protected function init_usable_fields(){
	    /** Les différents champs de titres + les champs perso non répetable de type small texte **/
	    $this->title_fields = array_merge(array(
	        "tit1" => $this->msg['cms_module_timeline_datasource_records_main_title'],
	        "tit2" => $this->msg['cms_module_timeline_datasource_records_other_title'],
	        "tit3" => $this->msg['cms_module_timeline_datasource_records_parallel_title']
	    ), $this->get_perso_fields('text', 'small_text'));
	    
	    /** Le champs résumé + les champs de type text large unique **/
	    $this->resume_fields = array_merge(array(
	        "n_resume" => $this->msg['cms_module_timeline_datasource_records_resume']
	    ), $this->get_perso_fields('text', 'text'));
	    
	    /** Le champs résumé + les champs de type text large unique **/
	    $this->image_fields = array_merge(array(
	        "thumbnail_url" => $this->msg['cms_module_timeline_datasource_records_thumbnail_url']
	    ), $this->get_perso_fields('url', 'text'));
	    
	    $this->date_fields = array_merge(array(
	        "date_parution" => $this->msg['cms_module_timeline_datasource_records_date_parution'],
	        "create_date" => $this->msg['cms_module_timeline_datasource_records_create_date']
	    ), $this->get_perso_fields('date_box', 'date'));
	}
	
	public function render($datas, $grouped_datas = []){
	    $events = array();
	    foreach($datas as $id){
	        $record = new notice($id);
	        $rd = new record_datas($id);
	        $event = [];
	        
	        if(!empty($this->parameters->timeline_fields)){
	            foreach($this->parameters->timeline_fields as $field_name => $field_value){
	                if($field_value){
    	                if(strpos($field_value, 'c_perso') !== false){
    	                    $field_value = explode('c_perso_', $field_value)[1];
    	                    $event[$field_name] = $this->get_cp_value($field_value, $id);
    	                }else{
    	                    $event[$field_name] = (isset($record->{$field_value}) ? $record->{$field_value} : null);
    	                }
	                }
	            }
	        }
	        //HACK 	
	        $event['resume'] = (isset($event['resume'])? $event['resume'] :'').record_display::get_display_in_result($id,'timeline');
	        $events[] = $event;
	    }
	    return parent::render($events);
	}
}