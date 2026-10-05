<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb2mba_output.class.php,v 1.2 2019/06/24 10:27:53 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;

require_once ($base_path."/admin/convert/convert_output.class.php");

class pmb2mba_output extends convert_output {
    public function _get_header_($output_params) {
        $def = new output_xml();
        return $def->_get_header_($output_params);
    }
    
    public function _get_footer_($output_params) {
        $def = new output_xml();
        return $def->_get_footer_($output_params);
    }
}

?>