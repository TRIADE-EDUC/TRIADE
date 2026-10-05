<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: others.inc.php,v 1.6 2019/06/07 08:05:38 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $other_search_form, $other_query, $obj;

// autres recherches (catalogage)
// Armelle : a priori plus utilisé
$other_search_form = str_replace("!!other_query!!", $other_query, $other_search_form);

if(strlen($other_query) || $obj) {
	include_once('./catalog/notices/search/others/other_proceed.inc.php');
	} else {
		print $other_search_form;
		}
