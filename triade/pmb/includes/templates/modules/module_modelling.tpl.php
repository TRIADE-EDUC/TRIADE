<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_modelling.tpl.php,v 1.4 2019-05-27 09:41:33 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $module_modelling_left_menu, $pmb_contribution_area_activate, $module_modelling_menu_ontologies, $module_modelling_menu_contribution_area; 
global $module_modelling_menu_frbr, $msg;

$module_modelling_left_menu = "
<div id='menu'>
<h3 onclick='menuHide(this,event)'>".$msg['admin_menu_modules']."</h3>
<ul>
	<li><a href='./modelling.php?categ=ontologies&sub=general'>".$msg['ontologies']."</a></li>
	<li><a href='./modelling.php?categ=frbr'>".$msg['frbr']."</a></li>";
if($pmb_contribution_area_activate){
	$module_modelling_left_menu .= "<li><a href='./modelling.php?categ=contribution_area'>".$msg['admin_menu_contribution_area']."</a></li>";
}			
$module_modelling_left_menu .= "</ul>
</div>";

//$module_modelling_menu_ontologies = création d'ontologies
$module_modelling_menu_ontologies ="
<h1>".$msg["admin_ontologies"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	!!sub_tabs!!
	!!ontologies_menu!!
</div>";

$module_modelling_menu_contribution_area ="
<h1>".$msg["admin_menu_contribution_area"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	!!sub_tabs!!
</div>";

$module_modelling_menu_frbr ="
<h1>".$msg["frbr"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	!!sub_tabs!!
</div>";

?>