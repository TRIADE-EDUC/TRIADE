<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr.php,v 1.5 2022/04/15 12:16:06 dbellamy Exp $


// définition du minimum nécessaire 
$base_path=".";                            
$base_auth = "CMS_AUTH";  
$base_title = "\$msg[cms_onglet_title]";  
                            
$base_use_dojo=1; 

require_once ("$base_path/includes/init.inc.php");
require_once($class_path."/modules/module_frbr.class.php");

print " <script type='text/javascript' src='javascript/ajax.js'></script>";

$module_frbr = new module_frbr();

$module_frbr->proceed_header();

$id = intval($id);
$module_frbr->set_object_id($id);
$module_frbr->set_url_base($base_path.'/frbr.php?categ='.$categ);
$module_frbr->proceed();

// pied de page
$module_frbr->proceed_footer();

// deconnection MYSql
pmb_mysql_close();
