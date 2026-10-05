<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: modelling.php,v 1.8 2023/09/04 14:53:06 tsamson Exp $


// définition du minimum nécessaire 
$base_path=".";                            
$base_auth = "MODELLING_AUTH";  
$base_title = "\$msg[param_modelling]";  
                            
$base_use_dojo=1; 

require_once ($base_path."/includes/init.inc.php");  
require_once($class_path."/modules/module_modelling.class.php");

print " <script type='text/javascript' src='javascript/ajax.js'></script>";

$module_modelling = new module_modelling();

$module_modelling->proceed_header();

if($pmb_javascript_office_editor){
    print $pmb_javascript_office_editor;
    print "<script type='text/javascript'>
        pmb_include('$base_path/javascript/tinyMCE_interface.js');
    </script>";
}

$id = intval($id);
$module_modelling->set_object_id($id);
$module_modelling->set_url_base($base_path.'/modelling.php?categ='.$categ);
$module_modelling->proceed();

// pied de page
$module_modelling->proceed_footer();

// deconnection MYSql
pmb_mysql_close();