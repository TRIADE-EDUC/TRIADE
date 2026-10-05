<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean_autoload_files.inc.php,v 1.5 2024/04/17 13:55:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $v_state, $spec;

require_once ($class_path."/netbase/netbase_cache.class.php");

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["clean_autoload_files"]);

$cleaned = netbase_cache::clean_autoload_files();

if($cleaned) {
    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_autoload_files"], 'OK');
} else {
    $v_state .= netbase::get_display_progress_v_state($msg["cleaning_autoload_files"], 'KO');
}
$spec = $spec - CLEAN_AUTOLOAD_FILES;

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);