<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis.inc.php,v 1.11 2022/02/10 13:11:12 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $opac_avis_allow, $article_id, $section_id, $notice_id, $id, $private;
global $opac_notice_affichage_class;

$article_id = intval($article_id);
$section_id = intval($section_id);
$notice_id = intval($notice_id);
if ($opac_avis_allow==0 || (!$notice_id && !$article_id && !$section_id)) {
	ajax_http_send_response("0");
}

require_once($class_path."/notice_affichage.class.php");
require_once($class_path."/notice_affichage.ext.class.php");
require_once($class_path."/avis.class.php");

switch($sub){
	case 'save':
		if($article_id) {
			$saved = avis::save_avis($id, $article_id, AVIS_ARTICLES);
		} elseif($section_id) {
			$saved = avis::save_avis($id, $section_id, AVIS_SECTIONS);
		} else {
			$saved = avis::save_avis($id, $notice_id, AVIS_RECORDS);
		}
		if ($saved) {
			if($private && $article_id){
				$avis = new avis($article_id, AVIS_ARTICLES);
				ajax_http_send_response($avis->get_display_detail());
			} elseif($private && $section_id){
				$avis = new avis($section_id, AVIS_SECTIONS);
				ajax_http_send_response($avis->get_display_detail());
			} elseif($private && $notice_id) {
				if ($opac_notice_affichage_class) $notice_affichage=$opac_notice_affichage_class; else $notice_affichage="notice_affichage";
				$notice=new $notice_affichage($notice_id);
				ajax_http_send_response($notice->avis_detail());
			} else{
				ajax_http_send_response("1");				
			}
		} else { 
			ajax_http_send_response("0");
		}
		break;
	case 'refresh':
		$avis=new avis($notice_id);
		ajax_http_send_response($avis->get_display());
		break;
	case 'delete':
		$deleted = avis::delete_avis($id);
		if ($deleted) {
			if($article_id) {
				$avis = new avis($article_id, AVIS_ARTICLES);
				ajax_http_send_response($avis->get_display_detail());
			} elseif($section_id) {
				$avis = new avis($section_id, AVIS_SECTIONS);
				ajax_http_send_response($avis->get_display_detail());
			} else {
				if ($opac_notice_affichage_class) $notice_affichage=$opac_notice_affichage_class; else $notice_affichage="notice_affichage";
				$notice=new $notice_affichage($notice_id);
				ajax_http_send_response($notice->avis_detail());
			}
		} else { 
			ajax_http_send_response("0");
		}
		break;
}
