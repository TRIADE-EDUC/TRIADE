<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_store_arc2.class.php,v 1.3 2017-04-25 16:13:09 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// require_once($class_path."/rdf_entities_integration/rdf_entities_store.class.php");
require_once($class_path."/rdf/arc2/ARC2.php");

class rdf_entities_store_arc2 extends rdf_entities_store {
	
	public function __construct($config) {
		parent::__construct($config);
		$default_config = array(
				/* db */
				'db_host' => SQL_SERVER,
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS
		);
		$this->config = array_merge($default_config, $this->config);
		$this->config['ns'] = $this->ns;
		$this->store = ARC2::getStore($this->config);

		if (!$this->store->isSetUp()) {
			$this->store->setUp();
		}
	}
	
	public function get_properties($uri){
		$resource = ARC2::getResource(array("ns" => $this->ns));
		$resource->setStore($this->store);
		$resource->setURI($uri);
		$properties = $resource->getProps();
		return $properties;
	}
	
	public function get_property($uri, $property_uri){
		$resource = ARC2::getResource(array("ns" => $this->ns));
		$resource->setStore($this->store);
		$resource->setURI($uri);
		$property = $resource->getProps($property_uri);
		return $property;
	}
	
	public function add_ns($prefix, $uri) {
		parent::add_ns($prefix, $uri);
		$this->config['ns'] = $this->ns;
	}
}