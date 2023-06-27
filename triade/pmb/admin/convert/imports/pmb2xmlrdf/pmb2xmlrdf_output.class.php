<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb2xmlrdf_output.class.php,v 1.1 2018-07-25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/synchro_rdf.class.php");
require_once($base_path."/admin/convert/convert_output.class.php");

class pmb2xmlrdf_output extends convert_output {

	public function _get_footer_($output_params) {
		$export=new synchro_rdf(session_id());
		$contenuRdf=$export->exportStoreXml();
	
		//Suppression des tables temporaires
		$res=pmb_mysql_query("SHOW TABLES LIKE '".session_id()."%'");
		while($row=pmb_mysql_fetch_array($res)){
			pmb_mysql_query("DROP TABLE ".$row[0]);
		}
	
		return $contenuRdf;
	}
}

?>