<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_docnum_thumbnail.inc.php,v 1.3 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $v_state, $spec;

require_once ($class_path."/netbase/netbase_explnum.class.php");

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["gen_docnum_thumbnail_in_progress"]);
$nb_thumbnail = netbase_explnum::gen_docnum_thumbnail();
$spec = $spec - GEN_DOCNUM_THUMBNAIL;
$v_state .= netbase::get_display_progress_v_state($msg["gen_docnum_thumbnail_in_progress"], $nb_thumbnail." ".$msg["gen_docnum_thumbnail_end"]);
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();
print netbase::get_process_state_form($v_state, $spec);

