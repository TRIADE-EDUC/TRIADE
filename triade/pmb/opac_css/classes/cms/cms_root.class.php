<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_root.class.php,v 1.3 2023/05/05 08:40:14 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_root {
    
    public $session_vars;
    public $get_vars;
    public $post_vars;
    public $env_vars;
	
    public function __construct(){
		
	}	
}