<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: convert_output.class.php,v 1.1 2018/07/25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class convert_output {

    public function _get_header_($output_params) {
    	return "";
    }
    
    public function _get_footer_($output_params) {
    	return "";
    }
    
    public static function get_instance() {
    	
    }
}

?>