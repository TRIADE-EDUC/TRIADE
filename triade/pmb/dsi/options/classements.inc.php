<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classements.inc.php,v 1.9 2017-11-13 10:24:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($suite)) $suite = '';

echo window_title($database_window_title.$msg['dsi_menu_title']);
print "<h1>".$msg['dsi_opt_class']."</h1>" ;
switch($suite) {
    case 'acces':
    	$clas = new classement($id_classement);
    	print $clas->show_form();  
		break;
    case 'add':
    	$clas = new classement(0);
    	print $clas->show_form();  
        break;
    case 'delete':
    	$clas = new classement($id_classement);
    	print $clas->delete();  
		break;
	case 'update':
    	if(!isset($type_classement)) $type_classement = '';
    	$clas = new classement($id_classement);
    	$clas->set_properties_from_form();
    	print $clas->save(); 
        break;
    case 'up':
    	$clas = new classement($id_classement) ;
    	$clas->set_order('up');  
		break;
    case 'down':
    	$clas = new classement($id_classement) ;
    	$clas->set_order('down');  
		break;        
}

print pmb_bidi(dsi_list_classements ()) ;
