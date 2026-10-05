<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_result.inc.php,v 1.71 2024/02/29 10:58:36 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// résultats d'une recherche sur mots utilisateur OPAC
if(!isset($mode)) $mode = '';

require_once($class_path.'/search_result.class.php');

//Enregistrement de la recherche
require_once($include_path."/rec_history.inc.php");

if ($get_last_query) {
	$get_query = $_SESSION["last_query"];
}

if (isset($get_query) && $get_query) {
	$reinit_facette = 1;
	get_last_history();
	get_history($get_query);
	$_SESSION["new_last_query"]=$get_query;
}


// affichage recherche
search_result::set_url_base('./index.php?');
search_result::set_search_type($search_type);
search_result::set_user_query(stripslashes($user_query));
search_result::proceed();

/** Fin affichage de la page **/
