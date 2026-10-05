<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_timeline_datasource_records.class.php,v 1.4 2019/01/07 12:40:18 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/notice.class.php');
require_once($class_path.'/parametres_perso.class.php');

class cms_module_timeline_datasource_records extends cms_module_timeline_datasource_generic{

	protected static $prefix = 'notices';
	
	protected $entity_type = 'notices';
	
	public function __construct($id=0){
		parent::__construct($id);
	}
		
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_generic_records",
		);
	}

	/**
	 * Définition des champs utilisables pour la valorisation 
	 * de la structure JSON renvoyée par la source
	 */
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
		
	protected function get_full_values($ids){
		
		$events = array();
		foreach($ids as $id){
			$record = new notice($id);
			
			$event = [];

			if(!empty($this->parameters['timeline_fields'])){
				foreach($this->parameters['timeline_fields'] as $field_name => $field_value){
				    if (!$field_value) continue;
					if(strpos($field_value, 'c_perso') !== false){
						$field_value = explode('c_perso_', $field_value)[1];
						$event[$field_name] = $this->get_cp_value($field_value, $id);
					}else{
						$event[$field_name] = $record->{$field_value};
					}
				}
			}
			$events[] = $event;
		}
		return $events;
		
	}
}