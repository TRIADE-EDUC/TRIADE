<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_transport_http.class.php,v 1.2 2016/12/22 16:36:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/curl.class.php");

class openurl_transport_http {

    public function __construct() {}
    
    public function get($url){
    	$curl = new Curl();
    	$rep =$curl->get($url);
    	return $rep->__toString();
    }
}