<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_typepage_opac.class.php,v 1.23 2022/02/07 09:01:59 jparis Exp $
use Pmb\Common\Helper\Portal;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_typepage_opac {

    public static function get_type_page() {
		return Portal::getTypePage();
	}
	
	public static function get_subtype_page() {
        return Portal::getSubTypePage();
	}
	
	public static function get_label($type) {
	    return Portal::getLabel($type);
	}
}