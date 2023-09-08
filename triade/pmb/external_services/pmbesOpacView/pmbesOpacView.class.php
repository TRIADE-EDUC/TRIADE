<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesOpacView.class.php,v 1.2 2017-06-22 08:49:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once($class_path."/opac_view.class.php");

class pmbesOpacView extends external_services_api_class {
	
	public function gen_search() {
		global $dbh;
		$views=new opac_view();
		$views->gen();
	
	}
	
}




?>