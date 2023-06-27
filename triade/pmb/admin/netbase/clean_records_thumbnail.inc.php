<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean_records_thumbnail.inc.php,v 1.1 2019-03-29 11:54:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $opac_url_base;

require_once($class_path."/thumbnail.class.php");

$v_state=urldecode($v_state);

print "<br /><br /><h2 class='center'>".htmlentities($msg["cleaning_records_thumbnail"], ENT_QUOTES, $charset)."</h2>";

$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["cleaning_records_thumbnail"], ENT_QUOTES, $charset)." : ";

if(thumbnail::is_valid_folder('record')) {
	$query = "select notice_id, thumbnail_url from notices where thumbnail_url like 'data:image%'";
	$result = pmb_mysql_query($query);
	if (pmb_mysql_num_rows($result)) {
		while($row = pmb_mysql_fetch_object($result)) {
			$created = thumbnail::create_from_base64($row->notice_id, 'records', $row->thumbnail_url);
			if($created) {
				$thumbnail_url = $opac_url_base."getimage.php?noticecode=&vigurl=";
				$thumbnail_url .= "&notice_id=".$row->notice_id;
				$query = "update notices set thumbnail_url = '".addslashes($thumbnail_url)."' where notice_id = ".$row->notice_id;
				pmb_mysql_query($query);
			}
		}
	}
	$v_state.= "OK";
} else {
	$v_state.= $msg['notice_img_folder_no_access'];
}

$spec = $spec - CLEAN_RECORDS_THUMBNAIL;

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, '', '2');