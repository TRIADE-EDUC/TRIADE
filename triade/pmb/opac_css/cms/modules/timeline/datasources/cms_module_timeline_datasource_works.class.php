<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_timeline_datasource_works.class.php,v 1.2 2017/10/10 08:29:37 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/notice.class.php');
require_once($class_path.'/parametres_perso.class.php');

class cms_module_timeline_datasource_works extends cms_module_timeline_datasource_authorities {

	protected static $prefix = 'tu';
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_generic_authorities_uniform_titles", //A revoir assez rapidement 
		);
	}

	/**
	 * Définition des champs utilisables pour la valorisation 
	 * de la structure JSON renvoyée par la source
	 */
	protected function init_usable_fields(){
		/** Les différents champs de titres + les champs perso non répetable de type small texte **/
		$this->title_fields = array_merge(array(
			"isbd" => $this->msg['cms_module_timeline_datasource_works_isbd'],
		), $this->get_perso_fields('text', 'small_text'));

		/** Le champs résumé + les champs de type text large unique **/
		$this->resume_fields = array_merge(array(
			"comment" => $this->msg['cms_module_timeline_datasource_works_comment']
		), $this->get_perso_fields('text', 'text'));
		
		
		$this->image_fields = array_merge(array(
			"thumbnail_url" => $this->msg['cms_module_timeline_datasource_works_thumbnail_url']
		), $this->get_perso_fields('url', 'text'));
		
		$this->date_fields = array_merge(array(
			"date_date" => $this->msg['cms_module_timeline_datasource_works_date']
		), $this->get_perso_fields('date_box', 'date'));
		
	}
}