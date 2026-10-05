<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_datastore.class.php,v 1.4 2021/01/13 14:24:54 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/onto_store_arc2.class.php");

/**
 * class skos_onto
 * Classe gérant un accès au store de données SKOS
*/
class skos_datastore {
	/**
	 * Instance de la classe d'interrogation ARC2
	 * @var onto_store_arc2
	 * @access private
	 */
	private static $store = array();
	
	private static $data_resource = null;
	
	private static $namespaces = array(
		"skos"	=> "http://www.w3.org/2004/02/skos/core#",
		"dc"	=> "http://purl.org/dc/elements/1.1",
		"dct"	=> "http://purl.org/dc/terms/",
		"owl"	=> "http://www.w3.org/2002/07/owl#",
		"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
		"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
		"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
		"pmb"	=> "http://www.pmbservices.fr/ontology#"	
	);
	
	private static $config = array(
		'db_name' => DATA_BASE,
		'db_user' => USER_NAME,
		'db_pwd' => USER_PASS,
		'db_host' => SQL_SERVER,
		/* store */
		'store_name' => 'rdfstore',
		/* stop after 100 errors */
		'max_errors' => 100,
		'store_strip_mb_comp_str' => 0
	);

	/**
	 * Inialisation de l'instance d'onto_store_arc2 dans self::store
	 * @return void
	 * @access private
	 */
	private static function init(){
		if(!is_object(self::$store)){
			self::$store = new onto_store_arc2(self::$config);
			self::$store->set_namespaces(self::$namespaces);
		}
	}
	
	/**
	 * Exécute une nouvelle requête SPARQL sur le store de données SKOS
	 * @param query string  <p>Requête SPARQL a lancer sur le store ARC2</p>
	 * @return bool
	 * @access public
	 */
	public static function query($query){
		self::init();
		return self::$store->query($query);
	}
	
	/**
	 * Retourne le nombre de lignes de la dernière requete SPARQL sur le store de données SKOS
	 * @return <p>Nombre de lignes pour la dernière requete<br>FALSE si le store n'est pas initialisé</p>
	 * @access public
	 */
	public static function num_rows(){
		if(is_object(self::$store)){
			return self::$store->num_rows();
		}
		return false;
	}
	
	/**
	 * Retourne le résulat de la dernière requete SPARQL sur le store de données SKOS
	 * @return <p>Tableau du résultat pour la dernière requete<br>FALSE si le store n'est pas initialisé</p>
	 * @access public
	 */
	public static function get_result(){
		if(is_object(self::$store)){
			return self::$store->get_result();
		}
		return false;
	}
	
	/**
	 * Retourne les erreurs de la dernière requete SPARQL sur le store de données SKOS
	 * @return <p>ERREURS</p>
	 * @access public
	 */
	public static function get_errors(){
		if(is_object(self::$store)){
			return self::$store->get_errors();
		}
		return false;
	}
	
	/**
	 * 
	 * @return ARC2_Resource
	 */
	public static function get_data_resource(){
		if(!is_object(self::$data_resource)){
			self::$data_resource= ARC2::getResource(array('ns'=>self::$namespaces));
			self::init();
			self::$data_resource->setStore(ARC2::getStore(self::$config));
			self::$data_resource->store->getDBCon();
		}
		return self::$data_resource;
	}
	
	public static function get_store() {
		self::init();
		return self::$store;
	}
}