<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_authority_serie.class.php,v 1.6 2017/04/25 15:22:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/i_2709.class.php");
require_once($class_path."/notice_authority_generic.class.php");
require_once($class_path."/collection.class.php");
require_once($class_path."/subcollection.class.php");
require_once($class_path."/serie.class.php");

/*
 * Classe pour les autorité classiques...
 */
class notice_authority_serie extends iso2709_notices implements notice_authority_generic{
	public $is_utf8;
	public $type;
	public $common_data;
	public $specifics_data;
	public $rejected_forms;
	public $associated_forms;
	public $parallel_forms;
	public $use_rejected;
	public $use_associated;
	public $use_parallel;
	public $import_subcoll;

	public function __construct($data="",$type="UNI",$file_charset="iso-8859-1",$import_subcoll=false){
		if($file_charset == "utf-8"){
			$this->is_utf8 = true;
		}
		$this->import_subcoll=$import_subcoll;
		parent::__construct($data,$type);
		$this->get_type();
	}

	public function get_type(){
		if($this->guide_infos['bl'] == "s" && $this->fields['110']){
			$code = substr($this->fields['110'][0]['a'][0],0,1);
			if($code == "b"){
				if($this->fields['410']){
					$this->type = "subcollection";	
				}else{
					$this->type = "collection";
				}
			}
		}/*elseif($this->fields['200'][0]['f']){
			$this->type = "serie";
		}*/else{
			$this->type = "";
		}
	}

	/*
	 * Pour avoir le numéro d'autorité 
	 */
	public function format_authority_number($authority_number){
		global $pmb_import_modele_authorities;
		//appel à la méthode statique de la classe d'import...
		if($pmb_import_modele_authorities!= 0){
			return call_user_func(array($pmb_import_modele_authorities,"format_authority_number"),$authority_number,20);
		}else{
			return $authority_number;
		}
	}	

	public function get_informations($use_rejected = true, $use_associated = true, $use_parallel = false){
		$this->use_rejected = $use_rejected;
		$this->use_associated = $use_associated;
		$this->use_parallel = $use_parallel;
		if(!$this->error){
			$this->get_common_informations();
			$this->get_specifics_informations();
			$this->get_rejected_forms();
			$this->get_associated_forms();
			$this->get_parallel_forms();
		}
	}

	public function get_common_informations(){
		$this->common_data = array();
		$this->common_data['authority_number'] = $this->format_authority_number($this->fields['001'][0]['value']);
		$this->common_data['lang'] = $this->fields[101][0]['a'][0];
		$this->common_data['source']=array(
			'country' => $this->fields[801][0]['a'][0],
			'origin' => $this->fields[801][0]['b'][0],
			'date' => $this->fields[801][0]['c'][0]
		);
	}

	public function get_specifics_informations(){
		$this->specifics_data = array();
		switch($this->type){
			case "collection" :
				$this->specifics_data = collection::get_informations_from_unimarc($this->fields,false,$this->import_subcoll);
				break;
			case "subcollection" :
				$this->specifics_data = subcollection::get_informations_from_unimarc($this->fields);
				break;
			case "serie" :
				//$this->specifics_data = serie::get_informations_from_unimarc($this->fields);
				break;
			default : 
				break;
		}
	}

	public function get_rejected_forms(){
		//n'existe pas sur ces types d'autorités
	}

	public function get_associated_forms(){
		$this->associated_forms = array();
	}

	public function get_parallel_forms(){
		//non géré dans PMB
	}

	public function check_if_exists($data){
		switch($data['type_authority']){
			case "collection" :
				$id = collection::check_if_exists($data);
				break;
			case "subcollection" :
				$id = subcollection::check_if_exists($data);
				break;
		}
		return $id;
	}
}