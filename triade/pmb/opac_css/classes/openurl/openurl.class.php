<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl.class.php,v 1.2 2016/12/22 16:36:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class openurl_root {
	public static $uri ="info:ofi";
	public static $serialize ="";

    public function __construct() {
    	    	
    }
}