<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac.class.php,v 1.2 2022/08/01 06:44:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

abstract class mail_opac extends mail_root {
	
    protected function get_mail_headers() {
    	global $charset;
    	
    	$headers  = "MIME-Version: 1.0\n";
    	$headers .= "Content-type: text/html; charset=".$charset."\n";
    	return $headers;
    }
}