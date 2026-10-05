<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: s.php,v 1.12 2023/12/01 08:51:24 dbellamy Exp $
$base_path=".";

require_once($base_path."/includes/init.inc.php");

//fichiers necessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

// si paramétrage authentification particuliere et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) {
    require_once($base_path.'/includes/ext_auth.inc.php');
}

if($opac_search_other_function){
	require_once($include_path."/".$opac_search_other_function);
}

require_once("$class_path/shorturl/shorturls.class.php");

if(isset($h)){
    try {
        shorturls::proceed($h);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
