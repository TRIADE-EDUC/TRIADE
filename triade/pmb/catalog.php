<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: catalog.php,v 1.30 2021/04/28 06:52:35 dgoron Exp $

// définition du minimum nécéssaire
$base_path=".";
$base_auth = "CATALOGAGE_AUTH";
$base_title = "\$msg[6]";
$base_use_dojo = 1;
require_once ("$base_path/includes/init.inc.php");

// pour droit UNIQUE d'ajout de notices
if ((SESSrights & RESTRICTCATAL_AUTH) 
	&& ($categ!="create") 
	&& ($categ!="create_form") 
	&& ($categ!="update") 
	&& ($categ!="explnum_update") 
	&& ($categ!="explnum_create") 
	&& ($categ!="isbd") 
	) {
	$sub="";
	$categ="create";
	}

// modules propres à catalog.php ou à ses sous-modules
require_once($class_path."/modules/module_catalog.class.php");
require_once($class_path.'/interface/catalog/interface_catalog_form.class.php');
include("$include_path/templates/expl.tpl.php");
require("$include_path/templates/catalog.tpl.php");

if(!$categ){
	$categ="search";
	$mode=0;
} elseif($categ == 'caddie') {
	if(empty($sub)) $sub = 'gestion';
}
module_catalog::get_instance()->proceed_header();

include("./catalog/catalog.inc.php");
print alert_sound_script();

module_catalog::get_instance()->proceed_footer();

// deconnection MYSql
pmb_mysql_close();
