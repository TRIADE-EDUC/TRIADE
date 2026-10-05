<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_onto.class.php,v 1.4 2021/08/12 08:17:06 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/onto_store_arc2.class.php");

/**
 * class skos_onto
 * Classe gérant un accès au store de l'ontologie SKOS
*/
class skos_onto {
	/**
	 * Tableau des labels des propriétés des classes de l'ontologie SKOS
	 * @var array
	 * @access private
	 */
	private static $labels = array();
	
	/**
	 * Instance de la classe d'interrogation ARC2
	 * @var onto_store_arc2
	 * @access private
	 */
	private static $store = array();
	

	/**
	 * Inialisation de l'instance d'onto_store_arc2 dans self::$store
	 * @return void
	 * @access private
	 */
	private static function init(){
		if(!is_object(self::$store)){
			$onto_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'ontology',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
			);
			self::$store = new onto_store_arc2($onto_store_config);
			self::$store->set_namespaces(ONTOLOGY_NAMESPACE);
			global $class_path;
			self::$store->load($class_path."/rdf/skos_pmb.rdf");
		}
	}
	
	public static function get_store(){
		self::init();
		return self::$store;
	}
	
	/**
	 * Exécute une nouvelle requête SPARQL sur le store de l'ontologie SKOS
	 * @param query string  <p>Requête SPARQL a lancer sur le store ARC2</p>
	 * @return bool
	 * @access public
	 */
	public static function query($query){
		self::init();
		return self::$store->query($query);
	}
	
	/**
	 * Retourne le nombre de lignes de la dernière requete SPARQL sur le store de l'ontologie SKOS
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
	 * Retourne le résulat de la dernière requete SPARQL sur le store de l'ontologie SKOS
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
	 * Retoune le label PMB d'une propriété d'une classe de l'ontologie SKOS. Interroge le store si nécessaire
	 * @param class_uri string  <p>URI de la classe associée</p>
	 * @param property_uri string  <p>URI de la propriéte dont on veut le label PMB</p>
	 * @return <p>Retourne le label associé</p>
	 * @access public
	 */
	public static function get_property_label($class_uri,$property_uri){
		if(!isset(self::$labels[$class_uri])){
			self::get_properties_labels($class_uri);
		}
		if(isset(self::$labels[$class_uri][$property_uri])){
			return self::$labels[$class_uri][$property_uri]['label'];
		}else{
			return $property_uri;
		}
	}
		
	/**
	 * Retoune les labels PMB des propriétés d'une classe de l'ontologie SKOS. Interroge le store si nécessaire
	 * @param class_uri string  <p>URI de la classe associée</p>
	 * @return <p>Retourne le tableau de labels associés</p>
	 * @access public
	 */
	public static function get_properties_labels($class_uri){
		// on trouve les libellés?
		if(!isset(self::$labels[$class_uri])){
			//on recherche toutes les propriétés associés
			$query  = "select * where {
				?property rdf:type <http://www.w3.org/1999/02/22-rdf-syntax-ns#Property> .
				?property rdfs:label ?label .
				?property pmb:name ?name . 
				optional {
					?property rdfs:domain ?domain
				}				
			}";
			self::query($query);
			if(self::$store->num_rows()){
				$result = self::$store->get_result();
				//init de la static pour la classe concernée
				self::$labels[$class_uri] = array();
				foreach ($result as $property){
					if(!isset($property->domain) || $property->domain == $class_uri){
						self::$labels[$class_uri][$property->property] = array(
							'pmb_name' => $property->name
						);
						self::$labels[$class_uri][$property->property]['label'] = self::calc_label($class_uri, $property->property,$property->label);
					}
				}
			}
		}
		return self::$labels[$class_uri];
	}

	/**
	 * Récupère le libellé approprié d'une propriété d'une classe d'ontologie dans les messages PMB.
	 * @param class_uri string  <p>URI de la classe associée</p>
	 * @param property_uri string  <p>URI de la propriéte dont on veut le label PMB</p>
	 * @param default_label string  <p>Libellé récupéré dans le store de l'ontologie</p>
	 * @return <p>Retourne le libellé le plus approprié pour la propriété d'une classe de l'ontologie</p>
	 * @access private
	 */
	private static function calc_label($class_uri, $property_uri,$default_label = ""){
		global $msg;
		if(isset($msg['onto_skos_'.self::$labels[$class_uri][$property_uri]['pmb_name']])){
			//le message PMB spécifique pour l'ontologie courante
			$label = $msg['onto_skos_'.self::$labels[$class_uri][$property_uri]['pmb_name']];
		}else if (isset($msg['onto_common_'.self::$labels[$class_uri][$property_uri]['pmb_name']])){
			//le message PMB générique
			$label = $msg['onto_common_'.self::$labels[$class_uri][$property_uri]['pmb_name']];
		}else {
			$label = $default_label;
		}
		return $label;
	}
	
}