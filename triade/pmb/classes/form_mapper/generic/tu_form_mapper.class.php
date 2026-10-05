<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tu_form_mapper.class.php,v 1.7 2022/02/07 08:45:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/form_mapper/form_mapper.class.php');
require_once($class_path.'/titre_uniforme.class.php');
require_once($class_path.'/marc_table.class.php');

class tu_form_mapper extends form_mapper{
	
	private $id;

	private $tuObject; 
	
	/**
	 * Constructeur de form_mapper titre uniforme
	 * @param int $id Id du titre uniforme en base
	 */
	public function __construct(){
		
	}

	public function setId($id){
		$this->id = intval($id);
		$this->init();
	}
	
	private function init(){
		$this->tuObject = new titre_uniforme($this->id);
	}
	
	public function getDestinations(){
		return array('notice', 'tu');
	}
	
	public function getProfiles($dest){
		global $msg;
		$returnedArray = array();
		switch($dest){
			case 'tu':
				$oeuvreNature = new marc_list('oeuvre_nature');
				$oeuvreType = new marc_list('oeuvre_type');
				$recomposedArray = array();
				foreach($oeuvreNature->table as $natureKey => $nature){
					foreach($oeuvreType->table as $typeKey => $type){
						if($nature != '' && $type != ''){
							$recomposedArray[] = array('label'=>($nature.' - '.$type), 'url' => './autorites.php?categ=titres_uniformes&sub=titre_uniforme_form&id=0&source_type=tu&dest=tu&mapper[oeuvre_type]='.$typeKey.'&mapper[oeuvre_nature]='.$natureKey.'&source_id='.$this->tuObject->id);
						}
					}
				}
				$returnedArray[] = 
					array(
						'label' => $msg['authority_tu_create_derived_expression'], 
						'dest' => $dest, 
						'is_dropdown' => true,
						'profiles' => $recomposedArray
					);
				return $returnedArray;
				break;
			case 'notice':
				$returnedArray[] =
				array(
						'label' => $msg['authority_tu_create_derived_manifestation'],
						'dest' => $dest,
						'is_dropdown' => false,
						'profiles' => array('url'=> './catalog.php?categ=modif&id=0&source_type=tu&source_id='.$this->tuObject->id, 'label'=>$msg['authority_tu_create_derived_manifestation'])
				);
				return $returnedArray;
				break;
		}
	}
	
	/**
	 * Retourne le mappage associé au type passé en parametre
	 * @param String $dest
	 * @return array
	 */
	public function getMapping($dest){
		
	    switch($dest){
	        case 'tu':
	        	global $mapperParams;
	        	
	        	/**
	        	 * Paramètres supplémentaires passés au mapper depuis le javascript (ici oeuvre nature et oeuvre type)
	        	 * Accès via $mapperParams->mapper->nomDuParametre
	        	 * 
	        	 */
	        	if($mapperParams){
	        		$mapperParams = json_decode(stripslashes($mapperParams));
	        	}
	        	
	        	$fonction = new marc_list('function');
	        	$authors_label = array();
	        	$authors_id = array();
	        	$functions_id = array();
	        	$functions_label = array();
	        	foreach($this->tuObject->responsabilites['auteurs'] as $author){
	        		$authors_label[] = $author['objet']->get_isbd();
	        		$authors_id[] = $author['id'];
	        		$functions_id[] = $author['fonction'];
	        		$functions_label[] = $fonction->table[$author['fonction']];
	        	}
        		$fields = array(
        				array('type'=> 'input', 'name'=>'f_aut0', 'values'=> $authors_label),
        				array('type'=> 'input', 'name'=>'f_aut0_id', 'values'=> $authors_id),
        				array('type'=> 'input', 'name'=>'f_f0', 'values'=> $functions_label),
        				array('type'=> 'input', 'name'=>'f_f0_code', 'values'=> $functions_id)
        		);
        		$authors_array = array(
        				'jscallback' => 'add_aut',
        				'callbackParams' => array(0),
        				'multiple' => 'true',
        				'fields' => $fields
        		);
        		
        		$concept = new index_concept($this->tuObject->id, TYPE_TITRE_UNIFORME);
        		$concepts = $concept->get_concepts();
        		$concept_labels = $concept_values = $concept_types = array();
        		for ($i = 0; $i < count($concepts); $i++) {
        		    $concept_labels[] = $concepts[$i]->get_display_label();
        		    $concept_values[] = $concepts[$i]->get_uri();
        		    $concept_types[] = $concepts[$i]->get_type();
        		}
        		$concepts_fields = array(
        		    array('type'=> 'input', 'name'=>'concept_label', 'values'=> $concept_labels),
        		    array('type'=> 'input', 'name'=>'concept_value', 'values'=> $concept_values),
        		    array('type'=> 'input', 'name'=>'concept_type', 'values'=> $concept_types),
        		);
        		$concepts_array = array(
        		    'jscallback' => 'onto_add',
        		    'mainType' => "concept",
        		    'callbackParams' => array('concept', 0),
        		    'multiple' => 'true',
        		    'fields' => $concepts_fields
        		);
        		
        		return array(
        			array(
        				'jscallback' => 'add_oeuvre_expression',
        				'multiple' => 'true',
        				'fields' => array(
        						array('type'=> 'input', 'name'=>'f_oeuvre_expression', 'values'=> array($this->tuObject->display)),
        						array('type'=> 'input', 'name'=>'f_oeuvre_expression_code', 'values'=> array($this->tuObject->id)),
        				),
	        		),
        			array(
        				'multiple' => 'false',
        				'fields' => array(
        					array('type'=> 'input', 'name'=>'tu_name', 'values'=> array($this->tuObject->name)),
        				),
        			),
        			array(
        				'multiple' => 'false',
        				'fields' => array(
        					array('type'=> 'input', 'name'=>'date', 'values'=> array($this->tuObject->date)),
        				),
        			),
        			$authors_array,
        		    $concepts_array
        		);
        		case 'notice':
		        	$fonction = new marc_list('function');
		        	$authors_label = array();
		        	$authors_id = array();
		        	$functions_id = array();
		        	$functions_label = array();
		        	
		        	foreach($this->tuObject->responsabilites['auteurs'] as $author){
		        		$authors_label[] = $author['objet']->get_isbd();
		        		$authors_id[] = $author['id'];
		        		$functions_id[] = $author['fonction'];
		        		$functions_label[] = $fonction->table[$author['fonction']];
		        	}
        			if(count($authors_label)>1){
        				$authors_array =
        				array(
        						'jscallback' => 'add_aut',
        						'multiple' => 'true',
        						'callbackParams' => array(1),
        						'fields' => array(
	        						array('type'=> 'input', 'name'=>'f_aut1', 'values'=> $authors_label),
	        						array('type'=> 'input', 'name'=>'f_aut1_id', 'values'=> $authors_id),
	        						array('type'=> 'input', 'name'=>'f_f1', 'values'=> $functions_label),
	        						array('type'=> 'input', 'name'=>'f_f1_code', 'values'=> $functions_id)
        						),
        				);
        			}else{
        				$authors_array = 
        				array(
	        	        	'multiple' => 'false',
	        	        	'fields' => array(
        						array('type'=> 'input', 'name'=>'f_aut0', 'values'=> $authors_label),
        						array('type'=> 'input', 'name'=>'f_aut0_id', 'values'=> $authors_id),
        						array('type'=> 'input', 'name'=>'f_f0', 'values'=> $functions_label),
        						array('type'=> 'input', 'name'=>'f_f0_code', 'values'=> $functions_id),
        					),
        				);
        			}
        			
        			$concept = new index_concept($this->tuObject->id, TYPE_TITRE_UNIFORME);
        			$concepts = $concept->get_concepts();
        			$concept_labels = $concept_values = $concept_types = array();
        			for ($i = 0; $i < count($concepts); $i++) {
        			    $concept_labels[] = $concepts[$i]->get_display_label();
        			    $concept_values[] = $concepts[$i]->get_uri();
        			    $concept_types[] = $concepts[$i]->get_type();
        			}
        			$concepts_fields = array(
        			    array('type'=> 'input', 'name'=>'concept_label', 'values'=> $concept_labels),
        			    array('type'=> 'input', 'name'=>'concept_value', 'values'=> $concept_values),
        			    array('type'=> 'input', 'name'=>'concept_type', 'values'=> $concept_types),
        			);
        			$concepts_array = array(
        			    'jscallback' => 'onto_add',
        			    'mainType' => "concept",
        			    'callbackParams' => array('concept', 0),
        			    'multiple' => 'true',
        			    'fields' => $concepts_fields
        			);
        			
					return array(
						array(
							'jscallback' => 'add_titre_uniforme',
							'multiple' => 'true',
							'fields' => array(
	                            array('type'=> 'input', 'name'=>'f_titre_uniforme', 'values'=> array($this->tuObject->display)),
	                            array('type'=> 'input', 'name'=>'f_titre_uniforme_code', 'values'=> array($this->tuObject->id)),                         
                        	)
						),
    	          	   	array(
        	           		'multiple' => 'false',
        	            	'fields' => array(
        	                	array('type'=> 'input', 'name'=>'f_tit1', 'values'=> array($this->tuObject->name))
        	            	)
    	          	   	),
						array(
							'multiple' => 'false',
							'fields' => array(
									array('type'=> 'input', 'name'=>'f_year', 'values'=> array($this->tuObject->date))
							)
						),
						$authors_array,
					    $concepts_array
					);
	        default:
	            return array();
	            break;
	    }
	}
	// fin class
}