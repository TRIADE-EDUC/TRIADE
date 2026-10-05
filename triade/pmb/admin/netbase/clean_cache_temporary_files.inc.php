<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean_cache_temporary_files.inc.php,v 1.4.2.1 2024/07/26 09:13:37 jparis Exp $
use Pmb\Animations\Models\AnimationModel;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg;
global $v_state, $spec;

require_once ($class_path."/netbase/netbase_cache.class.php");

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["cleaning_cache_temporary_files"]);

$cleaned = netbase_cache::clean_files($base_path."/temp");
if($cleaned) {
	//Correctement réalisé en gestion, on nettoye à l'OPAC
	$cleaned = netbase_cache::clean_files($base_path."/opac_css/temp");
}

AnimationModel::cleanCache();

if($cleaned) {
	$v_state .= netbase::get_display_progress_v_state($msg["cleaning_cache_temporary_files"], 'OK');
} else {
    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_cache_temporary_files"], 'KO');
}
$spec = $spec - CLEAN_CACHE_TEMPORARY_FILES;

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);