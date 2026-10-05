<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb2webprisme_output.class.php,v 1.2 2019/07/05 13:25:15 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($base_path."/admin/convert/convert_output.class.php");

class pmb2webprisme_output extends convert_output {
	public function _get_header_($output_params) {
		$r="DO;;DS;;OP;;NOM;;SITE;;MEL;;DE;;COMMENT;;DOC;;LI";
		return $r;
	}
}

?>