<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_log.class.php,v 1.3 2024/02/28 13:52:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class scheduler_log {
	
	
	//Constructeur.	 $text
	public function __construct() {
	}
	
	public static function open($filename='log_errors.log'){
		global $base_path;
		
		// Enregistrer les erreurs dans un fichier de log
		ini_set('log_errors', 1);
		// Nom du fichier qui enregistre les logs (attention aux droits à l'écriture)
		ini_set('error_log', $base_path.'/temp/'.LOCATION.'_'.$filename);
    }
    
    public static function add_content($filename='log_errors.log', $content=''){
    	global $base_path;
    	
    	file_put_contents($base_path.'/temp/'.LOCATION.'_'.$filename, "\r\n".$content, FILE_APPEND);
    }
    
    public static function get_content($filename='log_errors.log'){
    	global $base_path;
    	
    	$content = '';
    	if(file_exists($base_path.'/temp/'.LOCATION.'_'.$filename)) {
    		$content .= nl2br(file_get_contents($base_path.'/temp/'.LOCATION.'_'.$filename));
    	} elseif(file_exists($base_path.'/temp/'.$filename)) {
    	    $content .= nl2br(file_get_contents($base_path.'/temp/'.$filename));
    	}
    	return $content;
    }
    
    public static function delete($filename='log_errors.log'){
    	global $base_path;
    	
    	if(file_exists($base_path.'/temp/'.LOCATION.'_'.$filename)) {
    	    @unlink($base_path.'/temp/'.LOCATION.'_'.$filename);
    	}
    	if(file_exists($base_path.'/temp/'.$filename)) {
    		@unlink($base_path.'/temp/'.$filename);
    	}
    }
}
