<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_item.tpl.php,v 1.5 2017-12-28 08:48:32 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path,$ontology_id, $pmb_form_authorities_editables, $PMBuserid;

$ontology_tpl['form_body'] = '
<script type="text/javascript" src="./includes/javascript/ajax.js"></script>    
<form id="!!onto_form_id!!" name="!!onto_form_name!!" method="POST" action="!!onto_form_action!!" class="form-autorites" onSubmit="return false;" >
	<input type="hidden" id="item_uri" name="item_uri" value="!!uri!!"/>	
	<input type="hidden" id="prefix_uri" name="prefix_uri" value="!!prefix_uri!!"/>	
	<div id="form-contenu">
		<div class="row">&nbsp;</div>
		<div id="zone-container">
			!!onto_form_content!!
		</div>
	</div>
	<div class="row">&nbsp;</div>
	<input type="hidden" id="parent_scenario_uri" name="parent_scenario_uri" value="!!parent_scenario_uri!!"/>
	<input type="hidden" id="contributor" name="contributor" value="!!contributor!!"/>
	<div class="left">
		!!onto_form_history!!
		!!onto_form_submit!!
		!!onto_form_push!!
	</div>
	<div class="right">
		!!onto_form_delete!!
	</div>
	<div class="row"></div>
</form>
!!onto_form_scripts!!
';