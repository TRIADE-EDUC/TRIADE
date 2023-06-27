<?php

function php_ini_get($key) {

$entrees = array(
'engine','short_open_tag','asp_tags','precision',
'y2k_compliance','output_buffering','implicit_flush',
'allow_call_time_pass_reference','safe_mode',
'safe_mode_exec_dir','safe_mode_allowed_env_vars',
'safe_mode_protected_env_vars','highlight.string',
'highlight.comment','highlight.keyword','highlight.bg',
'highlight.default','highlight.html','error_reporting',
'display_errors','log_errors','track_errors',
'error_prepend_string','error_append_string','error_log',
'warn_plus_overloading','register_globals',
'variables_order','register_argc_argv','track_vars',
'gpc_order','magic_quotes_gpc','magic_quotes_runtime',
'auto_prepend_file','auto_append_file','default_mimetype',
'default_charset','include_path','doc_root','user_dir',
'upload_tmp_dir','upload_max_filesize','extension_dir',
'define_syslog_variables','SMTP','sendmail_from',
'sendmail_path','debugger.host','debugger.port',
'debugger.enabled','logging.method','logging.directory',
'sql.safe_mode','mysql.allow_persistent',
'mysql.max_persistent','mysql.max_links',
'mysql.default_port','mysql.default_host',
'mysql.default_user','mysql.default_password',
'bcmath.scale','browscap','session.save_handler',
'session.save_path','session.use_cookies','session.name',
'session.auto_start','session.cookie_lifetime',
'session.cookie_path','session.cookie_domain',
'session.serialize_handler','session.gc_probability',
'session.gc_maxlifetime','session.referer_check',
'session.entropy_length','session.entropy_file',
'session.entropy_length','session.entropy_file',
'session.cache_limiter','session.cache_expire',
'assert.active','assert.warning','assert.bail',
'assert.callback','assert.quiet_eval'
);

	$retour = array();

	foreach($entrees as $e){
	//	print $e." : ";
		$f = ini_get($e);
	//	print $f."<br>";
		if ($e == $key) {
			$f = ini_get($e);
			return $f;
		}
		if (!is_bool($f))
		$retour[$e] = $f;
	}
	return $retour;

}


function php_module_load($key) {
	$data=get_loaded_extensions();
	foreach($data as $i => $value){
			if (strtolower($key) == strtolower($data[$i])) {
					return 1;
			}
	}
	return 0;
}
?>
