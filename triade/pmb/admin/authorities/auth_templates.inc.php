<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: auth_templates.inc.php,v 1.2 2016/05/12 16:31:43 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/auth_templates.class.php");

switch ($action){
	case 'save':		
		if(auth_templates::save_form()){
			print "<h3>".$msg['auth_template_successfully_saved']."</h3>";
		}else{
			print "<h3>".$msg['auth_template_unsuccessfully_saved']."</h3>";
		}
		print auth_templates::show_form();
		break;
	default:
		print auth_templates::show_form();
		break;
}

