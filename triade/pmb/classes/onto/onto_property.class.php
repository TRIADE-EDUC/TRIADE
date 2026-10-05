<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_property.class.php,v 1.8 2023/05/05 09:48:09 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}


require_once($class_path."/onto/onto_resource.class.php");

class onto_property extends onto_resource {

	/**
	 * tableau d'uri
	 * @access public
	 */
	public $domain;

	/**
	 * 
	 * @access public
	 */
	public $range;
	
	/**
	 *
	 * @access public
	 */
	public $default_value = array();
	
	/**
	 *
	 * @access public
	 */
	public $flags = array();

	/**
	 *
	 * @access public
	 */
	public $flag;

	/**
	 *
	 * @access public
	 */
	public $type;

	/**
	 *
	 * @access public
	 */
	public $isDefinedBy;

	/**
	 *
	 * @access public
	 */
	public $definition;

	/**
	 *
	 * @access public
	 */
	public $scopeNote;

	/**
	 *
	 * @access public
	 */
	public $pmb_datatype;

	/**
	 *
	 * @access public
	 */
	public $defaultValueType;

	/**
	 *
	 * @access public
	 */
	public $defaultValue;

	/**
	 *
	 * @access public
	 */
	public $inverseOf;

	/**
	 *
	 * @access public
	 */
	public $subPropertyOf;

	/**
	 *
	 * @access public
	 */
	public $comment;

	/**
	 *
	 * @access public
	 */
	public $distinctWith;

	/**
	 *
	 * @access public
	 */
	public $pound;

	/**
	 *
	 * @access public
	 */
	public $formOrder;

	/**
	 *
	 * @access public
	 */
	public $example;

	/**
	 *
	 * @access public
	 */
	public $undisplayed;
	
	/**
	 *
	 * @access public
	 */
	public $pmb_marclist_type;
	
	/**
	 *
	 * @access public
	 */
	public $subClassOf;
	
	/**
	 *
	 * @access public
	 */
	public $reciprocalLink;
	
	/**
	 *
	 * @access public
	 */
	public $list_item;
	
	/**
	 *
	 * @access public
	 */
	public $is_cp;
	
	/**
	 *
	 * @access public
	 */
	public $cp_options;
	
	/**
	 *
	 * @access public
	 */
	public $pmb_list_query;
	
	/**
	 *
	 * @access public
	 */
	public $pmb_list_item;
	
	/**
	 *
	 * @access public
	 */
	public $multilingue;
	
	/**
	 *
	 * @access public
	 */
	public $useLangConcept;
	
	/**
	 *
	 * @access public
	 */
	public $no_search;
	
	/**
	 *
	 * @access public
	 */
	public $pmb_extended;
	
	public static $properties = array();
	
	/**
	 * 
	 * @param onto_store_arc2_extended|onto_store_arc2 $store
	 * @return boolean|array
	 */
	public function get_properties($store) {
	    
	    if (empty($this->uri)) {
	        return false;
	    }
	   	    
	    if (!isset(static::$properties[$this->uri])) {
	        static::$properties[$this->uri] = array();
	        
	        $uri = $this->uri;
	        // on regarde si $uri contient un < au debut
	        if (substr($uri, 0) != "<") {
	            $uri = "<".$uri;
	        }
	        // on regarde si $uri contient un > à la fin
	        if (substr($uri, -1) != ">") {
	            $uri = $uri.">";
	        }
	        
	        $success = $store->query("SELECT * WHERE {
                $uri ?predicate ?value
            }");
                
            if ($success && $store->num_rows()) {
                $results = $store->get_result();
                foreach ($results as $result) {
                    $propname =  str_replace(ONTOLOGY_NAMESPACE, "", $result->predicate);
                    $propname =  trim($propname);
                    
                    switch ($propname) {
                        case "label":
                            $propname = "name";
                            break;
                        case "name":
                        case "datatype":
                        case "marclist_type":
                        case "list_query":
                            $propname = "pmb_".$propname;
                            break;
                    }
                    
                    static::$properties[$this->uri][$propname] = $result->value;
                    $this->{$propname} = $result->value;
                }
            } else {
                return $store->get_errors();
            }
	    } else {
	        foreach (static::$properties[$this->uri] as $propname => $value) {
	            $this->{$propname} = $value;
	        }
	    }
	    return true;
	}
	
	public function __set($label, $value)
	{
	    if (empty($this->uri) && $label != "uri") {
    	    static::$properties[$this->uri][$label] = $value;
	    }
	    $this->{$label} = $value;
	}

} // end of onto_property