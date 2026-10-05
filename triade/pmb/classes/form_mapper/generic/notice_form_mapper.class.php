<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_form_mapper.class.php,v 1.6 2019/04/29 11:44:35 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/form_mapper/form_mapper.class.php');
require_once($class_path.'/titre_uniforme.class.php');
require_once($class_path.'/marc_table.class.php');
require_once($class_path.'/notice.class.php');
require_once($class_path.'/authority.class.php');


class notice_form_mapper extends form_mapper {
	
	private $id;

	private $noticeObject; 
	
	/**
	 * Constructeur de form_mapper Notice
	 * @param int $id Id de la notice en base
	 */
	public function __construct() {
		
	}

	public function setId($id) {
		$this->id = $id * 1;
		$this->init();
	}
	
	private function init() {
		$this->noticeObject = new notice($this->id);
	}
	
	public function getDestinations() {
	    global $pmb_use_uniform_title;
	    
	    if ($pmb_use_uniform_title) {
	        return array('notice', 'tu');
	    }
	    return array('notice');
	}
	
	public function getProfiles($dest) {
		global $charset, $msg;
		
		$returnedArray = array();
		switch ($dest) {
			case 'tu':
				$returnedArray[] = array(
				    'label' => $msg['notice_create_derived_work'],
				    'dest' => $dest,
				    'is_dropdown' => false,
				    'profiles' => array('url' => './autorites.php?categ=titres_uniformes&sub=titre_uniforme_form&id=0&source_type=notice&source_id=' . $this->noticeObject->id, 'label'=>$msg['notice_create_derived_work'])
				);
				break;
			case 'notice':
			    $returnedArray[] = array(
    			    'label' => $msg['notice_create_manifestation_fille'],
    			    'dest' => $dest,
    			    'is_dropdown' => false,
    			    'profiles' => array('url' => './catalog.php?categ=create_form&id=0&notice_parent=' . $this->noticeObject->id . '&source_type=notice&source_id=' . $this->noticeObject->id, 'label'=>$msg['notice_create_manifestation_fille'])
			    );
			    break;
		}
		return $returnedArray;
	}
	
	/**
	 * Retourne le mappage associé au type passé en parametre
	 * @param String $dest
	 * @return array
	 */
	public function getMapping($dest) {
	    
	    switch ($dest) {
	        case 'notice':
	            $tit1 = $this->noticeObject->tit1;
	            
	            $fonction = new marc_list('function');
	            $authors_label = array();
	            $authors_id = array();
	            $functions_id = array();
	            $functions_label = array();
	            foreach ($this->noticeObject->responsabilites['auteurs'] as $author) {
	                $authority = new authority(0, $author['id'], AUT_TABLE_AUTHORS);
	                $authors_label[$author['responsability']][] = $authority->get_isbd();
	                $authors_id[$author['responsability']][] = $author['id'];
	                $functions_id[$author['responsability']][] = $author['fonction'];
	                $functions_label[$author['responsability']][] = $fonction->table[$author['fonction']];
	            }
	            $authors_array0 = array();
	            if (count($authors_label[0])) {
	                $authors_array0 = array(
	                    'multiple' => 'false',
	                    'fields' => array(
	                        array('type'=> 'input', 'name'=>'f_aut0', 'values'=> $authors_label[0]),
	                        array('type'=> 'input', 'name'=>'f_aut0_id', 'values'=> $authors_id[0]),
	                        array('type'=> 'input', 'name'=>'f_f0', 'values'=> $functions_label[0]),
	                        array('type'=> 'input', 'name'=>'f_f0_code', 'values'=> $functions_id[0]),
	                    ),
	                );
	            }
	            $authors_array1 = array();
	            if (count($authors_label[1])) {
	                $authors_array1 = array(
	                    'jscallback' => 'add_aut',
	                    'multiple' => 'true',
	                    'callbackParams' => array(1),
	                    'fields' => array(
	                        array('type'=> 'input', 'name'=>'f_aut1', 'values'=> $authors_label[1]),
	                        array('type'=> 'input', 'name'=>'f_aut1_id', 'values'=> $authors_id[1]),
	                        array('type'=> 'input', 'name'=>'f_f1', 'values'=> $functions_label[1]),
	                        array('type'=> 'input', 'name'=>'f_f1_code', 'values'=> $functions_id[1]),
	                    ),
	                );
	            }
	            $authors_array2 = array();
	            if (count($authors_label[2])) {
	                $authors_array2 = array(
	                    'jscallback' => 'add_aut',
	                    'multiple' => 'true',
	                    'callbackParams' => array(2),
	                    'fields' => array(
	                        array('type'=> 'input', 'name'=>'f_aut2', 'values'=> $authors_label[2]),
	                        array('type'=> 'input', 'name'=>'f_aut2_id', 'values'=> $authors_id[2]),
	                        array('type'=> 'input', 'name'=>'f_f2', 'values'=> $functions_label[2]),
	                        array('type'=> 'input', 'name'=>'f_f2_code', 'values'=> $functions_id[2]),
	                    ),
	                );
	            }

	            $editeur = array();
	            if ($this->noticeObject->ed1_id) {
	                $editeur = array(
	                    'multiple' => 'false',
	                    'fields' => array(
	                        array('type'=> 'input', 'name'=>'f_ed1_id', 'values'=> array($this->noticeObject->ed1_id)),
	                        array('type'=> 'input', 'name'=>'f_ed1', 'values'=> array($this->noticeObject->ed1)),
	                    ),
	                );
	            }
	            
	            $year = array();
	            if ($this->noticeObject->year) {
	                $year = array(
	                    'multiple' => 'false',
	                    'fields' => array(
	                        array('type'=> 'input', 'name'=>'f_year', 'values'=> array($this->noticeObject->year)),
	                    ),
	                );
	            }
	            
	            $concepts = array();
                $concept = new index_concept($this->noticeObject->id, TYPE_NOTICE);
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
	                    'multiple' => 'false',
	                    'fields' => array(
	                        array('type'=> 'input', 'name'=>'f_tit1', 'values'=> array($tit1)),
	                    ),
	                ),
	                $authors_array0,
	                $authors_array1,
	                $authors_array2,
	                $editeur,
	                $year,
	                $concepts_array,
	            );
	            break;
	        case 'tu':
	            global $for_oeuvre_type;
	            global $for_oeuvre_nature;
	            global $mapperParams;
	            
// 	        	/**
// 	        	 * Paramètres supplémentaires passés au mapper depuis le javascript (ici oeuvre nature et oeuvre type)
// 	        	 * Accès via $mapperParams->mapper->nomDuParametre
// 	        	 * 
// 	        	 */
// 	        	if($mapperParams){
// 	        		$mapperParams = json_decode(stripslashes($mapperParams));
// 	        	}
	        	
	        	$authors = array();
	        	$fonction = new marc_list('function');
                $authors_label = array();
                $authors_id = array();
                $functions_id = array();
                $functions_label = array();
                foreach($this->noticeObject->responsabilites['auteurs'] as $author){
                    $authority = new authority(0, $author['id'], AUT_TABLE_AUTHORS);
                    $authors_label[] = $authority->get_isbd();
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
        		
// 	        	
// 	        	
// 	        	foreach($this->tuObject->responsabilites['auteurs'] as $author){
// 	        		$authors_label[] = $author['objet']->get_isbd();
// 	        		$authors_id[] = $author['id'];
// 	        		$functions_id[] = $author['fonction'];
// 	        		$functions_label[] = $fonction->table[$author['fonction']];
// 	        	}

//         		$authors_array = array(
//         				'jscallback' => 'add_aut',
//         				'callbackParams' => array(0),
//         				'multiple' => 'true',
//         				'fields' => $fields
//         		);
        		
	        	$tuTitle = $this->noticeObject->tit1;
	        	if($this->noticeObject->tit4){
	        	    $tuTitle.= ' : '.$this->noticeObject->tit4;
	        	}
	        	if($this->noticeObject->tit3){
	        	    $tuTitle.= ' = '.$this->noticeObject->tit3;
	        	}	        	
	        	
        		return array(
        			array(
        				'multiple' => 'false',
        				'fields' => array(
        					array('type'=> 'input', 'name'=>'tu_name', 'values'=> array($tuTitle)),
        				),
        			),
        			array(
        				'multiple' => 'false',
        				'fields' => array(
        					array('type'=> 'input', 'name'=>'date', 'values'=> array($this->noticeObject->year)),
        				),
        			),
        		    array(
        		        'jscallback' => 'add_tu_notices',
        		        'multiple' => 'true',
        		        'fields' => array(
        		            array('type'=> 'input', 'name'=>'f_tu_notices', 'values'=> array($this->noticeObject->tit1)),
        		            array('type'=> 'input', 'name'=>'f_tu_notices_code', 'values'=> array($this->noticeObject->id)),
        		        ),
        		    ),
        			$authors_array
        		);
   	        default:
	            return array();
	            break;
	    }
	}
	// fin class
}