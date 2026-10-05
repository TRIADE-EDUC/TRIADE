<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_context_object.class.php,v 1.2 2016/12/22 16:36:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/openurl/openurl.class.php");
require_once($class_path."/openurl/entities/openurl_entities.class.php");
require_once($class_path.'/search.class.php');

class openurl_context_object extends openurl_root {
	public $infos= array();//	tableau regroupant des infos générales..;
	public $entitites;		//	tableau des entités

    public function __construct() {
    	$this->uri = parent::$uri."/fmt";
    }
    
    public function serialize(){}
}