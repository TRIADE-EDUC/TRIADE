<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_uri.class.php,v 1.1 2022/11/21 14:56:45 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class onto_ontopmb_uri extends onto_common_uri {

	static public function replace_temp_uri($temp_uri, $class_uri, $uri_prefix="") {
	    // Pour la définition des URI en définition de l'ontologie, on ne veut pas de suffixe numérique...
	    // On assume que les doublons potentiels sont gérés en amont !
	    $last_uri=$uri_prefix;
		$query='update onto_uri SET uri="'.addslashes($last_uri).'" where uri="'.$temp_uri.'"';
		pmb_mysql_query($query);
		
		//On initialise last_uri.
		self::$last_uri=$last_uri;
		return self::$last_uri;	
	}
} // end of onto_common_uri
