<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2023/08/17 09:47:55 dbellamy Exp $

if (stristr($_SERVER ['REQUEST_URI'], '.inc.php')) {
    die('no access');
}

require_once "$base_path/plugins/animation/classes/animation_conf.class.php";

if (animation_conf::animations_is_active()) {
    $animation_conf = new animation_conf();
    switch ($sub) {
    	case "configuration" :
    	default:
    		switch ($what) {
    			case 'save' :
    			    $animation_conf->save_form();
    				print '<script>document.location="'.$base_path.'/admin.php?categ=plugin&plugin=animation&sub=animation_conf&what=updated";</script>';
    				break;
    			case 'updated' :
    			    print $animation_conf->get_form(true);
    				break;
    			default:
    			    print $animation_conf->get_form();
    				break;
    		}
    }
} else {
    global $animation_conf_error_animation_not_active;
    print $animation_conf_error_animation_not_active;
}
 
