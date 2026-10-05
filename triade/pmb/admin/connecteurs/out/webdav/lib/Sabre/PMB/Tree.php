<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Tree.php,v 1.13.4.2 2025/03/14 08:07:35 qvarin Exp $

namespace Sabre\PMB;

use Sabre\DAV;
use encoding_normalize;

class Tree extends DAV\Tree {
	private $id_thesaurus;
	private $only_with_notices;
	protected $restricted_objects = "";
	public $config;


	public function __construct($config) {
		$this->config = $config;
  		$this->id_thesaurus = $config['used_thesaurus'];
		$this->only_with_notices = $config['only_with_notices'];
		$this->get_restricted_objects($config['included_sets']);
		$this->getRootNode();
	}

	public function getRootNode(){
		$this->rootNode = new RootNode($this->config);
	}

    public function get_restricted_objects($restrict_sets){

    	if($this->restricted_objects == ""){
    		if(is_countable($restrict_sets) && count($restrict_sets)){
	    		$tab =array();
	    		for ($i=0 ; $i<count($restrict_sets) ; $i++){
	    			$set = new \connector_out_set($restrict_sets[$i]);
	    			$tab = array_merge($tab,$set->get_values());
	    			$tab = array_unique($tab);
	    		}
	    		$this->restricted_objects = implode(",",$tab);
				$tab = array();
    		}
    	}
    }

    protected function get_restricted_objects_query() {
    	return "select notice_id as object_id from notices";
    }

	public function getNodeForPath($path) {
		global $charset;
        $path = trim($path,'/');
        if (isset($this->cache[$path])) return $this->cache[$path];

        $currentNode = $this->rootNode;
        $currentNode->restricted_objects = $this->restricted_objects;
        $currentNode->parentNode = null;
        $i=0;
        // We're splitting up the path variable into folder/subfolder components and traverse to the correct node..
        $exploded_path = explode('/',$path);
        for($i=0 ; $i<count($exploded_path) ; $i++) {
			$pathPart = $exploded_path[$i];
			if($charset != 'utf-8'){
				$pathPart = encoding_normalize::utf8_decode($pathPart);
			}
			// If this part of the path is just a dot, it actually means we can skip it
            if ($pathPart=='.' || $pathPart=='') continue;

            if (!($currentNode instanceof DAV\ICollection))
                throw new DAV\Exception\NotFound('Could not find node at path: ' . $path);
			$parent = $currentNode;
           	$currentNode = $currentNode->getChild($pathPart);
           	$currentNode->set_parent($parent);
		}
		$this->cache[$path] = $currentNode;
		return $currentNode;
    }
}