<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign.tpl.php,v 1.3 2023/08/17 09:47:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $campaign_view_tpl, $msg, $base_path;

$campaign_view_tpl = "
<h3>!!title!!</h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	!!content_view!!
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value='".$msg['76']."' onclick=\"history.go(-1);\" />&nbsp;
		<input id='campaign_consolidate' class='bouton' type='button' value='".$msg['consolidate']."'/>
	</div>
	<div class='right'>
		<input id='campaign_delete' class='bouton' type='button' value='".$msg['63']."'/>
	</div>
	<div class='row'></div>
</div>
<br /><br />
<div class='row'></div>

<script>
	require([
		'dojox/charting/Chart',
		'dojox/charting/plot2d/Pie',
		'dojo/on',
		'dojo/dom',
		'dojo/request',
	], function(Chart, Pie, on, dom, request){

		
		on(dom.byId('campaign_consolidate'), 'click', function() {
			request('".$base_path."/ajax.php?module=edit&categ=campaigns&action=consolidate&id=!!id!!').then(function(data) {
				updateGraphes(JSON.parse(data));
			});
		});
		on(dom.byId('campaign_delete'), 'click', function() {
			document.location = '".$base_path."/edit.php?categ=opac&sub=campaigns&action=delete&id=!!id!!';
		});
	});
</script>
";