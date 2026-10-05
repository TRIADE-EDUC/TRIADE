<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_datasource_sections.class.php,v 1.11 2023/04/26 13:26:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/cms/cms_section.class.php");

/**
 * class docwatch_datasource_sections
 *
*/
class docwatch_datasource_sections extends docwatch_datasource{

	/** Aggregations: */

	/** Compositions: */

	/*** Attributes: ***/


	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		parent::__construct($id);
	} // end of member function __construct

	/**
	 * Génération de la structure de données representant les items de type rubrique
	 *
	 */
	
	protected function get_items_datas($items){
		$rubriques_retour = array();
		if(count($items)){
			foreach($items as $id){
				$rubrique_instance = new cms_section($id);
				$rubrique_data = $rubrique_instance->format_datas();
				$rubrique = array();
				$rubrique['type'] = 'section';
				$rubrique['num_section'] = $rubrique_data->get_id();
				$rubrique['title'] = $rubrique_data->get_title();
				$rubrique['summary'] = $rubrique_data->get_resume();
				$rubrique['content'] = $rubrique_data->get_resume();
				$rubrique['logo_url'] = $rubrique_data->get_logo()['large'];
				$rubrique['url'] = $this->get_constructed_link("section", $rubrique_data->get_id());
				if($rubrique_data->get_start_date() == ""){
					$rubrique['publication_date'] = extraitdate($rubrique_data->get_create_date());
				}
				else{
					$rubrique['publication_date'] = $rubrique_data->get_start_date();
				}	
				if(count($rubrique_data->get_descriptors())){
				    $descriptors = array();
				    $rubrique_data_descriptors = $rubrique_data->get_descriptors();
				    for($i=0 ; $i<count($rubrique_data_descriptors) ; $i++){
				        $descriptors[]  = array('id' => $rubrique_data_descriptors[$i]['id']);
				    }
				    $rubrique['descriptors'] = $descriptors;
				}
				$rubriques_retour[] = $rubrique;
			}
		}
		return $rubriques_retour;
	}
	
	public function filter_datas($datas, $user=0){
		return $this->filter_sections($datas, $user);
	}
	
	public function get_available_selectors(){
		global $msg;
		return array(
				'docwatch_selector_parent_sections' => $msg['docwatch_selector_parent_sections'],
				'docwatch_selector_sections_type_section_generic' => $this->msg['docwatch_datasource_selector_sections_type_section_generic'],
				'docwatch_selector_sections_type_section' => $this->msg['docwatch_datasource_selector_sections_type_section']
		);
	}
	
	public function get_form_content(){
		global $msg,$charset;
		$form = parent::get_form_content();
		$form .= "
		<div class='row'>&nbsp;</div>
 		<div class='row'>
 			<label>".htmlentities($msg['dsi_docwatch_datasource_sections_link_select'],ENT_QUOTES,$charset)."</label>
 		</div>
 		<div class='row'>
 			".$this->get_constructor_link_form("section",get_class($this))."
 		</div>";
		return $form;
	}
	
	public function set_from_form() {
		$this->save_constructor_link_form("section",get_class($this));
		parent::set_from_form();
	}
} // end of docwatch_datasource_sections

