<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publication_states.inc.php,v 1.1 2012-08-20 12:41:08 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_editorial_publications_states.class.php");

$publication_states = new cms_editorial_publications_states();

switch($action){
	case "edit":
		print $publication_states->get_form($id);
		break;
	case "save":
		$publication_states->save();
		print $publication_states->get_table();
		break;
	case "delete":
		$publication_states->delete($id);
		print $publication_states->get_table();
		break;		
	case "list" :
	default :
		print $publication_states->get_table();
		break;
	
}

