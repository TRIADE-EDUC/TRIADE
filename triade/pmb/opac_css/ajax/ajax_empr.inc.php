<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_empr.inc.php,v 1.2 2020/09/25 07:20:16 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
global $sub;
global $query_id_empr, $query_empr_login, $query_empr_mail;

if(!defined('EMPR_LOGIN_IS_VALID')) define('EMPR_LOGIN_IS_VALID','1');
if(!defined('EMPR_LOGIN_NOT_SET_ERROR')) define('EMPR_LOGIN_NOT_SET_ERROR','2');
if(!defined('EMPR_LOGIN_PATTERN_ERROR')) define('EMPR_LOGIN_PATTERN_ERROR','3');
if(!defined('EMPR_LOGIN_UNIQUENESS_ERROR')) define('EMPR_LOGIN_UNIQUENESS_ERROR','4');

if(!defined('EMPR_MAIL_IS_VALID')) define('EMPR_MAIL_IS_VALID','1');
if(!defined('EMPR_MAIL_IS_UNIQUE')) define('EMPR_MAIL_IS_UNIQUE','1');
if(!defined('EMPR_MAIL_NOT_SET_ERROR')) define('EMPR_MAIL_NOT_SET_ERROR','2');
if(!defined('EMPR_MAIL_UNIQUENESS_ERROR')) define('EMPR_MAIL_UNIQUENESS_ERROR','3');

require $class_path.'/emprunteur.class.php';

switch($sub){
	
	case 'check_login':
		
		if(empty($query_id_empr)) {
			$query_id_empr=0;
		}
		$query_id_empr = intval($query_id_empr);
		if(!isset($query_empr_login)) {
			$query_empr_login = '';
		}
		if(!$query_empr_login) {
			ajax_http_send_response(EMPR_LOGIN_NOT_SET_ERROR);
			exit;
		}
		$check_login_pattern = emprunteur::check_login_pattern($query_empr_login);
		if (!$check_login_pattern) {
			ajax_http_send_response(EMPR_LOGIN_PATTERN_ERROR);
			exit;
		}
		$check_login_uniqueness = emprunteur::check_login_unicity($query_empr_login, $query_id_empr);
		if (!$check_login_uniqueness) {
			ajax_http_send_response(EMPR_LOGIN_UNIQUENESS_ERROR);
			exit;
		} 
		ajax_http_send_response(EMPR_LOGIN_IS_VALID);
		break;
		
	case 'check_login_uniqueness':
		
		if(empty($query_id_empr)) {
			$query_id_empr=0;
		}
		$query_id_empr = intval($query_id_empr);
		if(!isset($query_empr_login)) {
			$query_empr_login = '';
		}
		if(!$query_empr_login) {
			ajax_http_send_response(EMPR_LOGIN_NOT_SET_ERROR);
			exit;
		}
		$check_login_uniqueness = emprunteur::check_login_uniqueness($query_empr_login, $query_id_empr);
		if (!$check_login_uniqueness) {
			ajax_http_send_response(EMPR_LOGIN_UNIQUENESS_ERROR);
			exit;
		}
		ajax_http_send_response(EMPR_LOGIN_IS_VALID);
		break;
		
	case 'check_mail_uniqueness':
		
		if(empty($query_id_empr)) {
			$query_id_empr=0;
		}
		$query_id_empr = intval($query_id_empr);
		if(!isset($query_empr_mail)) {
			$query_empr_mail = '';
		}
		if(!$query_empr_mail) {
			ajax_http_send_response(EMPR_MAIL_NOT_SET_ERROR);
			exit;
		}
		$check_mail_uniqueness = emprunteur::check_mail_uniqueness($query_empr_mail, $query_id_empr);
		if (!$check_mail_uniqueness) {
			ajax_http_send_response(EMPR_MAIL_UNIQUENESS_ERROR);
			exit;
		}
		ajax_http_send_response(EMPR_MAIL_IS_UNIQUE);
		break;
}

