<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: semantic.php,v 1.5 2022/07/28 12:35:46 jparis Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "";  
$base_title = "\$msg[semantic]";
$base_use_dojo=1;

require_once ("$base_path/includes/init.inc.php");  
// ini_set('errors_display',1);
// error_reporting(E_ALL);
require_once($class_path."/ontologies.class.php");
require_once($include_path."/templates/semantic.tpl.php");
print "<div id='att' style='z-Index:1000'></div>";
print $menu_bar;
print $extra;
print $extra2;
print $extra_info;
if($use_shortcuts) {
	include("$include_path/shortcuts/circ.sht");
}

$ontologies = new ontologies();
$layout = str_replace("!!ontologies_menu!!",$ontologies->get_semantic_menu(),$semantic_layout);
print $layout;
if($ontology_id){
	$ontology = new ontology($ontology_id);
	$ontology->exec_data_framework();
}else{
	//TODO
	switch($categ){
		case 'plugin' :
			$plugins = plugins::get_instance();
			$file = $plugins->proceed("semantic",$plugin,$sub);
			if($file){
				include $file;
			}
			break;
		default : 
			print "<div class='row'>PAGE D'AIDE</div>";
			break;
	}
}
print $semantic_layout_end;
print $footer;

html_builder();

pmb_mysql_close($dbh);