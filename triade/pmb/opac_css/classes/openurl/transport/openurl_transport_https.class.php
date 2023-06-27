<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_transport_https.class.php,v 1.2 2016-12-22 16:36:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/openurl/transport/openurl_transport.class.php");

class openurl_transport_byref_https extends openurl_transport_byref{

    public function __construct($url,$notice_id,$source_id,$byref_url) {
    	parent::__construct($url,$notice_id,$source_id,$byref_url);
    }
}

class openurl_transport_byval_https extends openurl_transport_byval{

    public function __construct() {
    	parent::__construct();
    }
}

class openurl_transport_inline_https extends openurl_transport_inline{

    public function __construct() {
    	parent::__construct();
    }
}