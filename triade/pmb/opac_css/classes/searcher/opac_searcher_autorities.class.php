<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_searcher_autorities.class.php,v 1.1 2015/03/20 15:42:37 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/searcher/opac_searcher_generic.class.php");

//un jour ca sera utile
class opac_searcher_autorities extends opac_searcher_generic {
	
	public function _get_search_type(){
		return "authorites";
	}
}