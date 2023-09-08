<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Tree.php,v 1.9 2017-06-13 10:18:06 arenou Exp $

namespace Sabre\PMB;

use Sabre\DAV;
use Sabre\PMB;

class Tree extends DAV\ObjectTree {
	private $id_thesaurus;
	private $only_with_notices;
	protected $restricted_objects = "";
	

	function __construct($config) {
		$this->config = $config;
  		$this->id_thesaurus = $config['used_thesaurus'];
		$this->only_with_notices = $config['only_with_notices'];
		$this->get_restricted_objects($config['included_sets']);
		$this->getRootNode();
	}
	
	function getRootNode(){
		$this->rootNode = new RootNode($this->config);
	}
	
    function get_restricted_objects($restrict_sets){
    	
    	if($this->restricted_objects == ""){
    		if(count($restrict_sets)){
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
				$pathPart = utf8_decode($pathPart);
			}
			// If this part of the path is just a dot, it actually means we can skip it
            if ($pathPart=='.' || $pathPart=='') continue;

            if (!($currentNode instanceof DAV\ICollection))
                throw new DAV\Exception\FileNotFound('Could not find node at path: ' . $path);
			$parent = $currentNode;	
           	$currentNode = $currentNode->getChild($pathPart);
           	$currentNode->set_parent($parent);
		}
		$this->cache[$path] = $currentNode;
		return $currentNode;
    }
}