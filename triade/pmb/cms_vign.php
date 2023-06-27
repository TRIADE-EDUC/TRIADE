<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_vign.php,v 1.5 2015-05-29 14:48:13 arenou Exp $

// définition du minimum nécéssaire 
$base_path     = ".";                            
$base_auth     = ""; //"CIRCULATION_AUTH";  
$base_title    = "";    
$base_noheader = 1;
$base_nocheck  = 1;
$base_nobody   = 1;


require_once($base_path."/classes/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("cms_modules",true);

require_once ("$base_path/includes/init.inc.php");  
session_write_close();
require_once($class_path."/cms/cms_logo.class.php");

$logo = new cms_logo($id,$type);
$logo->show_picture($mode);