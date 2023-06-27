<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: s.php,v 1.9 2018-02-13 15:08:42 dgoron Exp $
$base_path=".";

require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

// si paramétrage authentification particulière et pour le re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

if($opac_search_other_function){
	require_once($include_path."/".$opac_search_other_function);
}

if(!isset($autoloader) || !is_object($autoloader)){
	require_once($class_path.'/autoloader.class.php');
	$autoload = new autoloader();
}
require_once("$class_path/shorturl/shorturls.class.php");

if(isset($h)){
	shorturls::proceed($h);
}