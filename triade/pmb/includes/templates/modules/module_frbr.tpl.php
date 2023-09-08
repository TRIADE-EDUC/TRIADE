<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_frbr.tpl.php,v 1.13 2019-05-27 09:42:35 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");
global $module_frbr_left_menu, $module_frbr_cataloging_content, $module_frbr_cataloging_schemes, $msg;

$module_frbr_left_menu = "
<div id='menu'>
<h3 onclick='menuHide(this,event)'>".$msg['admin_menu_modules']."</h3>
<ul>
	<li><a href='./frbr.php?categ=cataloging&sub=general'>".$msg['93']."</a></li>
	<li><a href='./frbr.php?categ=cataloging&sub=schemes&action=select'>".$msg['frbr_cataloging_schemes']."</a></li>
</ul>
</div>";

$module_frbr_cataloging_content = "
<h1 class='section-title'>".$msg['frbr_cataloging_title']."</h1>
<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true' style='height:800px;width:100%;'>
	
	<div data-dojo-type='apps/frbr/cataloging/DatanodesUI' data-dojo-props='splitter:true, direction: \"vertical\",startExpanded: true, region:\"leading\"' style='width:20%;'></div>
	
	<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true, region:\"center\"' style='height:100%;width:auto;'>
		
		<div data-dojo-type='dijit/layout/TabContainer' data-dojo-props='splitter:true, region:\"center\"' style='width:auto;height:95%'>
			<div data-dojo-type='apps/frbr/cataloging/GraphUI' title='".$msg['frbr_cataloging_graph_title']."' data-dojo-props='splitter:true' ></div>
			<div data-dojo-type='apps/frbr/cataloging/SearchUI' data-dojo-props='id:\"frbr_search_pane\"' title='".$msg['frbr_cataloging_search_title']."'></div>
			<div data-dojo-type='apps/frbr/cataloging/AddUI' title='".$msg['create']."'></div>
		</div>

		<div data-dojo-type='apps/frbr/cataloging/ItemsListUI' style='width:auto;height:50%' data-dojo-props='direction: \"horizontal\", startExpanded: false, splitter:true, region:\"bottom\"' title='".$msg['frbr_cataloging_itemslist_title']."'></div>
	</div>

</div>";

$module_frbr_cataloging_schemes = "
	<h1 class='section-title'>".$msg['frbr_cataloging_schemes']."</h1>
	<div class='row'>
	</div>
";
?>