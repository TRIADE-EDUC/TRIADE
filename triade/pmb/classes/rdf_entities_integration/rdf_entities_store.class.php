<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_store.class.php,v 1.1 2017/02/07 14:43:32 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class rdf_entities_store {
	
	protected $config;
	
	protected $ns;
	
	protected $store;
	
	public function __construct($config) {
		$this->config = $config;
		$this->ns = array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
		);
	}
	
	public function get_properties($uri) {
		return array();
	}
	
	public function get_property($uri, $property_uri) {
		return array();
	}
	
	public function add_ns($prefix, $uri) {
		if (!isset($this->ns[$prefix])) {
			$this->ns[$prefix] = $uri;
		}
	}
	
	public function __call($method_name, $arguments) {
		if (method_exists($this->store, $method_name)) {
			return call_user_func_array(array($this->store, $method_name), $arguments);
		}
		return false;
	}
}