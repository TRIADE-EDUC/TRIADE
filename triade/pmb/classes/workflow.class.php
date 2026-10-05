<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: workflow.class.php,v 1.4.18.2 2025/05/13 15:23:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path.'/parser.inc.php');

class workflow {
	
	
	public $states_com = array();
	public $object = array();
	public $object_states = array();
	public $object_states_by_id = array();	
	public $object_types = array();
	public $object_types_by_id = array();
	public $object_name = '';
	public $object_workflow = array();
	public $object_startstate = array();
	public $object_transitions = array();
	
	/*
	 * Constructeur
	 */
	public function __construct($obj_name,$workflow_name=''){
		
	    global $include_path, $charset;
		
		$this->object_name = $obj_name;
		
		$file = $include_path."/demandes/workflow.xml";
		$file_subst = $include_path."/demandes/workflow_subst.xml";
		
		if (file_exists($file_subst)) {
			$xml=file_get_contents($file_subst,"r");		
		} else $xml=file_get_contents($file,"r") or die(htmlentities("Can't find XML file $file", ENT_QUOTES, $charset));
		
		//Parse le fichier dans un tableau	
		$param=_parser_text_no_function_($xml,"STRUCTURE");
		
		//Liste des états généraux (nom=>libellé)
		for($i=0;$i<count($param['STATES'][0]['STATE']);$i++){
			$this->states_com[$param['STATES'][0]['STATE'][$i]['NAME']] = $param['STATES'][0]['STATE'][$i]['COMMENT'];
		}

		//Chargement de l'objet
		for($i=0;$i<count($param['OBJECTS']);$i++){
			for($j=0;$j<count($param['OBJECTS'][$i]['OBJECT']);$j++){
				$nom = $param['OBJECTS'][$i]['OBJECT'][$j]['NAME'];
				if($nom == $this->object_name){
					$this->object = $param['OBJECTS'][$i]['OBJECT'][$j];
				}	
			}
		}
		
		//Chargement des attributs de l'objet
		
		//Etats
		for($i=0;$i<count($this->object['STATES'][0]['STATE']);$i++){
			$this->object_states_by_id[$this->object['STATES'][0]['STATE'][$i]['ID']] = $this->object['STATES'][0]['STATE'][$i]['NAME'];
			$this->object_states[$this->object['STATES'][0]['STATE'][$i]['NAME']]['ID'] = $this->object['STATES'][0]['STATE'][$i]['ID'];
			if(isset($this->object['STATES'][0]['STATE'][$i]['DEFAULT'])) {
				$this->object_states[$this->object['STATES'][0]['STATE'][$i]['NAME']]['DEFAULT'] = $this->object['STATES'][0]['STATE'][$i]['DEFAULT'];
			} else {
				$this->object_states[$this->object['STATES'][0]['STATE'][$i]['NAME']]['DEFAULT'] = '';
			}
			if(isset($this->object['STATES'][0]['STATE'][$i]['IMAGE'])) {
				$this->object_states[$this->object['STATES'][0]['STATE'][$i]['NAME']]['IMAGE'] = $this->object['STATES'][0]['STATE'][$i]['IMAGE'];
			} else {
				$this->object_states[$this->object['STATES'][0]['STATE'][$i]['NAME']]['IMAGE'] = '';
			}
			$this->object_states[$this->object['STATES'][0]['STATE'][$i]['NAME']]['COMMENT'] = $this->getStateCommentById($this->object['STATES'][0]['STATE'][$i]['ID']);
		}	
		//Types
		if(isset($this->object['TYPES'][0]['TYPE'])) {
			for($i=0;$i<count($this->object['TYPES'][0]['TYPE']);$i++){
				$this->object_types_by_id[$this->object['TYPES'][0]['TYPE'][$i]['ID']] = $this->object['TYPES'][0]['TYPE'][$i]['NAME'];
				$this->object_types[$this->object['TYPES'][0]['TYPE'][$i]['NAME']]['ID'] = $this->object['TYPES'][0]['TYPE'][$i]['ID'];
				if(isset($this->object['TYPES'][0]['TYPE'][$i]['DEFAULT'])) {
					$this->object_types[$this->object['TYPES'][0]['TYPE'][$i]['NAME']]['DEFAULT'] = $this->object['TYPES'][0]['TYPE'][$i]['DEFAULT'];
				} else {
					$this->object_types[$this->object['TYPES'][0]['TYPE'][$i]['NAME']]['DEFAULT'] = '';
				}
				$this->object_types[$this->object['TYPES'][0]['TYPE'][$i]['NAME']]['IMAGE'] = $this->object['TYPES'][0]['TYPE'][$i]['IMAGE'];
				$this->object_types[$this->object['TYPES'][0]['TYPE'][$i]['NAME']]['COMMENT']= $this->object['TYPES'][0]['TYPE'][$i]['COMMENT'];
			}
		}
		//Workflow
		for($i=0;$i<count($this->object['WORKFLOW']);$i++){
			if($this->object['WORKFLOW'][$i]['NAME'] == $workflow_name)
				$this->object_workflow = $this->object['WORKFLOW'][$i];
		}
		//Transitions possibles
		if(isset($this->object_workflow['SOURCE'])) {
			for($i=0;$i<count($this->object_workflow['SOURCE']);$i++) {
				$cibles=array();
				
				for($j=0;$j<count($this->object_workflow['SOURCE'][$i]['TARGET']); $j++) {
					$cibles[] = $this->object_workflow['SOURCE'][$i]['TARGET'][$j]['NAME'];
				}
				$this->object_transitions[$this->object_workflow['SOURCE'][$i]['NAME']]=$cibles;
	
			}
		}
	}
	
	/*
	 * Retourne le nom d'un état en fonction de son id
	 */
	public function getStatesById($state_id){
	    if (isset($this->object_states_by_id[$state_id])) {
	        return $this->object_states_by_id[$state_id];
	    }
	    return '';
	}
	
	/*
	 * Retourne le libellé associé à un état
	 */
	public function getStateCommentById($state_id){
		global $msg;
		
		if (isset($this->object_states_by_id[$state_id]) && !empty($this->states_com[$this->object_states_by_id[$state_id]])) {
    		$message = explode(':',$this->states_com[$this->object_states_by_id[$state_id]]);
    		if (!empty($msg[$message[1]])) {
    		    return $msg[$message[1]];
    		}
		}
		return '';
	}
	
	/*
	 * Retourne le libellé associé à un type
	 */
	public function getTypeCommentById($type_id){
		global $msg;

		if (isset($this->object_types_by_id[$type_id]) && !empty($this->object_types[$this->object_types_by_id[$type_id]]['COMMENT'])) {
            $message = explode(":",$this->object_types[$this->object_types_by_id[$type_id]]['COMMENT']);
            if (!empty($msg[$message[1]])) {
                return $msg[$message[1]];
            }
		}
		return '';
	}
	
	/*
	 * Retourne la liste des états joignables depuis un autre état
	 */
	public function getStateList($state_id=-1){
		
		$state_list = array();
		
		if($state_id == -1){
			$i=0;
			foreach($this->object_states as $value){
				$i++;
				$state_list[$i]['id'] = $value['ID'];
				if($value['DEFAULT']){
					$state_list[$i]['default'] = $value['DEFAULT'];
				}
				if($value['IMAGE']){
					$state_list[$i]['image'] = $value['image'];
				}
				$state_list[$i]['comment'] = $value['COMMENT'];
			}
		} else {
			$nom = $this->getStatesById($state_id);
			if(array_key_exists($nom,$this->object_transitions)) {
				for($i=0;$i<count($this->object_transitions[$nom]);$i++){
					$state_list[$i]['id'] = $this->object_states[$this->object_transitions[$nom][$i]]['ID'];
					if( $this->object_states[$this->object_transitions[$nom][$i]]['DEFAULT']){
						$state_list[$i]['default'] = $this->object_states[$this->object_transitions[$nom][$i]]['DEFAULT'];
					}
					if( $this->object_states[$this->object_transitions[$nom][$i]]['IMAGE']){
						$state_list[$i]['image'] = $this->object_states[$this->object_transitions[$nom][$i]]['IMAGE'];
					}
					$state_list[$i]['comment'] = $this->getStateCommentById($this->object_states[$this->object_transitions[$nom][$i]]['ID']);
				}
			}
		}
		
		return $state_list;
	}
	
	/*
	 * Retourne la liste des types d'un objet
	 */
	public function getTypeList(){
		global $msg;
		
		$type_list = array();
		$i=0;
		
		foreach($this->object_types as $value){
			$i++;
			$type_list[$i]['id'] = $value['ID'];
			if( $value['DEFAULT']){
				$type_list[$i]['default'] = $value['DEFAULT'];
			} else {
				$type_list[$i]['default'] = '';
			}
			if( $value['IMAGE']){
				$type_list[$i]['image'] = $value['IMAGE'];
			}
			$message = explode(':',$value['COMMENT']);
			$type_list[$i]['comment'] = $msg[$message[1]];
		}
		
		return $type_list;
	}
}