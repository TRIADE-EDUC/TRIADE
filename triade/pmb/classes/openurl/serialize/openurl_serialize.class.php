<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_serialize.class.php,v 1.3 2017/07/12 09:07:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/openurl/openurl.class.php");

class openurl_serialize extends openurl_root{

    public function __construct() {
    	$this->uri = parent::$uri."/fmt";
    }
    
    public static function serialize($tab){}
}