<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: extended_search_dnd.tpl.php,v 1.10 2023/08/17 09:47:52 dbellamy Exp $

global $extended_search_dnd_tpl, $extended_search_dnd_tab_tpl; 
global $msg, $javascript_path; 
global $extended_search_dnd_tpl_segment,$extended_search_dnd_tab_tpl_segment;

$extended_search_dnd_link = '
<link rel="stylesheet" type="text/css" href="'.$javascript_path.'/dojo/dojox/grid/resources/Grid.css">
<link rel="stylesheet" type="text/css" href="'.$javascript_path.'/dojo/dojox/grid/resources/claroGrid.css">
';


$extended_search_dnd_script = '<script>
	require(["apps/search/SearchControllerStatic", "dojo/domReady!"], function(SearchControllerStatic){
		var searchController = SearchControllerStatic.getInstance();
	});
</script>';

//segment
$extended_search_dnd_script_segment = '<script>
	require(["apps/search/search_universe/SearchController", "dojo/domReady!"], function(SearchController){
	var searchController = new SearchController();
	});
</script>';

$extended_search_dnd_tab_tpl = $extended_search_dnd_link.'<div data-dojo-type="apps/pmb/tab/tabController" style="height:800px;width:100%;margin-top:2%;">
    <div id="extended_search_dnd_container" data-dojo-type="dijit/layout/BorderContainer" data-dojo-props="title: \''.$msg['search_extended'].'\', splitter:true" style="height:800px;width:100%;">
    </div>
</div>'.$extended_search_dnd_script;

$extended_search_dnd_tpl = $extended_search_dnd_link.'
<div id="extended_search_dnd_container" data-dojo-type="dijit/layout/BorderContainer" data-dojo-props="splitter:true" style="height:800px;width:100%;">
</div>'.$extended_search_dnd_script;

//propre aux segments de recherche
$extended_search_dnd_tpl_segment = $extended_search_dnd_link.'
<div id="extended_search_dnd_container" data-dojo-type="dijit/layout/BorderContainer" data-dojo-props="splitter:true" style="height:800px;width:100%;"></div>'
.$extended_search_dnd_script_segment;

$extended_search_dnd_tab_tpl_segment = $extended_search_dnd_link.'<div data-dojo-type="apps/pmb/tab/tabController" style="height:800px;width:100%;margin-top:2%;">
    <div id="extended_search_dnd_container" data-dojo-type="dijit/layout/BorderContainer" data-dojo-props="title: \''.$msg['search_extended'].'\', splitter:true" style="height:800px;width:100%;">
    </div>
</div>'.$extended_search_dnd_script_segment;