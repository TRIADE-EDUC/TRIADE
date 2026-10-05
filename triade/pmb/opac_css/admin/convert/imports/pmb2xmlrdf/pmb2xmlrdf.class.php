<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb2xmlrdf.class.php,v 1.1 2018/07/25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/synchro_rdf.class.php");
require_once($base_path."/admin/convert/convert.class.php");

class pmb2xmlrdf extends convert {

	public static function _export_notice_($id,$keep_expl=0,$params=array()) {
	
		$export=new synchro_rdf(session_id());
	
		$export->addRdf($id,0);
		
		return;
	}
}
