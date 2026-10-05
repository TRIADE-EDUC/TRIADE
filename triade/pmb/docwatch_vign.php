<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_vign.php,v 1.1 2015/12/15 11:27:20 dgoron Exp $

// définition du minimum nécéssaire 
$base_path     = ".";                            
$base_auth     = "";  
$base_title    = "";    
$base_noheader = 1;
$base_nocheck  = 1;
$base_nobody   = 1;

require_once ("$base_path/includes/init.inc.php");  
session_write_close();
require_once($class_path."/docwatch/docwatch_logo.class.php");

$logo = new docwatch_logo($id,$type);
$logo->show_picture($mode);