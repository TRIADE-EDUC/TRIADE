<?php

// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ui.class.php,v 1.8 2019/07/05 13:25:15 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * 
 * Classe permettant l'aiguillage du module onto
 * Prend en charge l'articulation des menus génériques en fonction de l'ontologie
 */
class onto_ui {
	
	/**
	 * @var onto_handler handler
	 */
	protected $handler;
	
	/**
	 * @var onto_common_controler controler
	 */
	protected $controler;
	
	/** variables d'aiguillage **/
	protected $params;
	

	/**
	 * On attribut les paramètres d'aiguillage dans la classe.
	 * Si je dispose d'un sub (nom de classe) et d'un id j'instancie l'item correspondant
	 * 
	 * 
	 * @param string $ontology_filepath
	 * @param string $onto_store_type
	 * @param array $onto_store_config
	 * @param string $data_store_type
	 * @param array $data_store_config
	 * @param array $tab_namespaces
	 * @param string $default_display_label
	 * @param onto_param $params
	 */
	public function __construct($ontology_filepath, $onto_store, $onto_store_config, $data_store_type, $data_store_config, $tab_namespaces, $default_display_label, $params){
		$this->params=$params;
		
		$this->handler = new onto_handler($ontology_filepath, $onto_store, $onto_store_config, $data_store_type, $data_store_config, $tab_namespaces, $default_display_label);
		$this->handler->get_ontology();
		
		if(!isset($params->sub) || !$params->sub){
			$params->sub=$this->handler->get_first_ontology_class_name();
		}
		$controler_class_name=self::resolve_controler_class_name($this->handler->get_onto_name());
		$this->controler=new $controler_class_name($this->handler,$this->params);
	}
	
	public function proceed(){
		return $this->controler->proceed();
	}
	
	/**
	 *
	 * Renvoi le nom de la class controler à utiliser pour l'ontologie
	 *
	 * @return string
	 */
	public static function resolve_controler_class_name($ontology_name){
		return self::search_controler_class_name($ontology_name);
	}
	
	/**
	 *
	 * Recherche et renvoi le nom de classe controler le plus approprié pour l'ontologie
	 *
	 * @param string $ontology_name
	 * @return string 
	 */
	public static function search_controler_class_name($ontology_name){
		$suffixe = "_controler";
		$prefix="onto_";
		if(class_exists($prefix.$ontology_name.$suffixe)){
			//La classe controler a le même nom que l'ontologie
			//ex : onto_skos<=>onto_skos_controler
			return $prefix.$ontology_name.$suffixe;
		}else{
			return $prefix.'common'.$suffixe;
		}
		return false;
	}
	
}
