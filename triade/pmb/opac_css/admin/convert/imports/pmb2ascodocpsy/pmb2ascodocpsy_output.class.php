<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb2ascodocpsy_output.class.php,v 1.1 2018/07/25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($base_path."/admin/convert/convert_output.class.php");

class pmb2ascodocpsy_output extends convert_output {
	function _get_header_($output_params) {
		$tab_r = array("TYPE","AUT","TIT","EDIT","LIEU","PAGE","DATE","MOTCLE","NOMP","NOTES","PRODFICH","LOC","COL","THEME","RESU","SUPPORT","SUPPORTPERIO","LIEN","VOL","CANDES","CONGRTIT","CONGRLIE","CONGRDAT","CONGRNUM","ISBNISSN","REED","DIPSPE","REV","VIEPERIO","ETATCOL","NUM","PDPF","NATTEXT","DATETEXT","DATEPUB","NUMTEXOF","DATEVALI","ANNEXE","LIENANNE","DATESAIS");
		$r = implode("\t", $tab_r);
		$r.= "\n";
		return $r;
	}
}

?>