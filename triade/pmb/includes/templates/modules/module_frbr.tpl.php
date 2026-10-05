<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_frbr.tpl.php,v 1.16 2021/04/29 13:10:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");
global $module_frbr_cataloging_content, $module_frbr_cataloging_schemes, $msg;

$module_frbr_cataloging_content = "
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
	<div class='row'>
	</div>
";
?>