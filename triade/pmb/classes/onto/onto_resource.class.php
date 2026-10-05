<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_resource.class.php,v 1.9 2023/05/05 07:16:43 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/onto/onto_ontology.class.php");


/**
 * class onto_resource
 * 
 */
class onto_resource {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * @var string
	 * @access public
	 */
	public $uri;

	/**
	 * @var string
	 * @access public
	 */
	public $name;

	/**
	 * @var string
	 * @access public
	 */
	public $label;

	/**
	 * Nom utilisé pour la factory
	 *
	 * @var string
	 * @access private
	 */
	public $pmb_name;
	
	/**
	 * @access public
	 */
	public $flags;
	
	/**
	 * @access public
	 */
	public $flag;
	
	/**
	 * @var string
	 * @access public
	 */
	public $type;
	
    /**
     * Méthode permettant de calculer l'affichage du nom d'un formulaire de contribution
     * 
     * @return string
     */
	public function get_display_name() {
	    global $msg;
	    
	    $label = $this->label;
	    if (substr($this->name,0,4) == "msg:") {
	        if (isset($msg[substr($this->name,4)])) {
	            $label = $msg[substr($this->name,4)];
	        } else {
	            // si on trouve pas le message on met juste le code dans le label
	            $label = substr($this->name,4);
	        }
	    }
	    
	    //cas particulier pour les authperso et les type de contenu editorial
	    $pmb_type = explode('_', $this->pmb_name);
	    if (!empty($pmb_type[1]) && !empty($msg['contribution_area_form_type_' . $pmb_type[0]])) {
	        $label = $this->name;
	        return $msg['contribution_area_form_type_' . $pmb_type[0]] . " : $label";
	    }
	    
	    return $label;
	}
	
    /**
     * Méthode permettant de calculer le name pour le graph
     * 
     * @return string
     */
	public function get_display_name_for_area() {
	    global $msg;
	    
	    $label = $this->name;
	    if (substr($this->name,0,4) == "msg:") {
	        if (isset($msg[substr($this->name,4)])) {
	            $label = $msg[substr($this->name,4)];
	        } else {
	            // si on trouve pas le message on met juste le code dans le label
	            $label = substr($this->name,4);
	        }
	    }
	    return $label;
	}
} // end of onto_resource