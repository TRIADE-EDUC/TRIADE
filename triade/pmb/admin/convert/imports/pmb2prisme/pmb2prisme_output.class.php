<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb2prisme_output.class.php,v 1.1 2018-07-25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($base_path."/admin/convert/convert_output.class.php");

class pmb2prisme_output extends convert_output {
	function _get_header_($output_params) {
		$r= "REF;;OP;;DS;;TY;;URL;;GEN;;AU;;AUCO;;AS;;DIST;;TI;;TN;;COL;;TP;;SO;;ED;;ISBN;;DP;;DATRI;;ND;;NO;;GO;;HI;;DENP;;DE;;CD;;RESU";
		return $r;
	}
}

?>