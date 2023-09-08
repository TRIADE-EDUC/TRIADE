<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: extended_search_dnd.tpl.php,v 1.9 2019-05-27 14:28:49 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $extended_search_dnd_tpl;

$extended_search_dnd_tpl = '
<link rel="stylesheet" type="text/css" href="./javascript/dojo/dojox/grid/resources/Grid.css">
<link rel="stylesheet" type="text/css" href="./javascript/dojo/dojox/grid/resources/claroGrid.css">
<style type="text/css">
div.form-contenu table.table-no-border {
	border-collapse: collapse;
}

.claro .dojoDndItemBefore,
.claro .dojoDndItemAfter {
	border-bottom: none;
	border-top: none;
}

.claro tr.dojoDndItem.dojoDndItemBefore {
	border-top: 5px solid #369;
}

.claro tr.dojoDndItem.dojoDndItemAfter {
	border-bottom: 5px solid #369;
}
</style>
<div id="!!unique_identifier!!_extended_search_dnd_container" data-dojo-type="dijit/layout/BorderContainer" data-dojo-props="splitter:true" style="height:800px;width:100%;">
</div>
<script type="text/javascript">
	require(["apps/search/!!search_controller_class!!", "dojo/dom", "dojo/dom-style", "dijit/registry", "dojo/ready", "dojo/domReady!"], function(SearchController, dom, domStyle, registry, ready){
		var searchController = new SearchController("!!unique_identifier!!_extended_search_dnd_container", "!!search_controller_module!!");
		ready(function(){
			var mh= Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
			var off=0;
			var obj = dom.byId("!!unique_identifier!!_extended_search_dnd_container");  
			// pour retrouver les top
			do {
				off+= obj.offsetTop;
			} while (obj = obj.offsetParent); 
			var obj = dom.byId("!!unique_identifier!!_extended_search_dnd_container");
			// on retire également les margin-bottom des parents...
			do {
				if(obj.nodeType == 1){  
					off+= domStyle.get(obj,"marginBottom");
				}
			} while (obj = obj.parentNode);
			// ascenseur vertical inexistant (sauf si le menu de gauche dépasse, mais là...)
			if (registry.byId("!!unique_identifier!!_extended_search_dnd_container")) {
                registry.byId("!!unique_identifier!!_extended_search_dnd_container").resize({h:(mh-off)});
            }
		});
	});
</script>';