<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch.tpl.php,v 1.31 2023/05/17 07:42:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $docwatch_tpl, $msg, $docwatch_new_source_form_tpl;
global $charset, $current_module, $docwatch_watch_form_tpl, $docwatch_category_form_tpl, $docwatch_duplicate_source_form_tpl;

$docwatch_tpl = "
<script type='text/javascript' src='./javascript/ajax.js'></script>
<script type='text/javascript'>
	dojo.require('dojox.layout.ContentPane');
</script>
<link rel='stylesheet' type='text/css' href='./javascript/dojo/dojox/grid/resources/Grid.css'>
<link rel='stylesheet' type='text/css' href='./javascript/dojo/dojox/grid/resources/claroGrid.css'>
		
<div data-dojo-id='availableDatasources' data-dojo-type='dojo/store/Memory' data-dojo-props='data:".htmlentities(encoding_normalize::json_encode(docwatch_watch::get_available_datasources()),ENT_QUOTES,"utf-8")."'>
<div data-dojo-id='availableDatatags' data-dojo-type='dojo/store/Memory' data-dojo-props='data:".htmlentities(encoding_normalize::json_encode(docwatch_item::get_available_datatags()),ENT_QUOTES,"utf-8")."'>

</div>
<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true' style='height:800px;width:100%;'>
	<div data-dojo-type='apps/docwatch/WatchesUI' data-dojo-props='splitter:true,region:\"left\"' style='height:100%;width:200px;'>
	</div>
	<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true,region:\"center\"' style='height:100%;width:auto;'>
		<div data-dojo-type='dijit/layout/TabContainer' data-dojo-props='splitter:true,region:\"top\"' style='width:auto;height:50%'>
			<div data-dojo-type='apps/docwatch/ItemsListUI' title='".$msg['dsi_docwatch_itemslistui_title']."'>
			</div>
			<div data-dojo-type='apps/docwatch/SourcesListUI' title='".$msg['dsi_docwatch_sourceslistui_title']."'>
			</div>
		</div>
		<div  data-dojo-props='region:\"center\"' style='width:auto;height:50%' data-dojo-type='apps/docwatch/ItemUI'></div>
		<div  data-dojo-props='region:\"center\"' style='width:auto;display:none;height:50%;overflow:auto' data-dojo-type='apps/docwatch/SourceUI'></div>
	</div>	
</div>
";

$docwatch_new_source_form_tpl = '
<form data-dojo-attach-point="containerNode" data-dojo-attach-event="onreset:_onReset,onsubmit:_onSubmit" ${!nameAttrSetting}>	
	<h3>'.$msg["dsi_docwatch_add_source"].'</h3>
	<div class="form-contenu">
		<select name="selector_choice" data-dojo-type="dijit/form/Select" style="width:auto">
			<option value="0"></option>';
foreach(docwatch_watch::get_available_datasources() as $datasource){
	$docwatch_new_source_form_tpl.= '
			<option value="'.$datasource['class'].'">'.$datasource['label'].'</option>';
} 		
	
$docwatch_new_source_form_tpl.= '
		</select>		
	</div>
</form>';


$docwatch_watch_form_tpl = '
<div style="width: 400px; height: 500px; overflow: auto;">
<form name ="new_watch_form" data-dojo-attach-point="containerNode" data-dojo-attach-event="onreset:_onReset,onsubmit:_onSubmit" ${!nameAttrSetting}>	
	<div class="form-contenu">
		<input type="hidden" name="id" id="id" value=""/>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_category_form_category_parent']).'</label>
		</div>
		<div class="row">
			<select  id="parent" name="parent" data-dojo-type="dijit/form/Select" style="width:auto"></select>
		</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_libelle']).'</label>
		</div>	
		<div class="row">		
			<input type="text" style="width: 35em;" id="title" name="title" required="true" data-dojo-type="dijit/form/ValidationTextBox"/>
		</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_ttl']).'</label>
		</div>	
		<div class="row">		
			<input type="text" id="ttl" name="ttl" required="true" data-dojo-type="dijit/form/NumberTextBox"/>
		</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_desc']).'</label>
		</div>
		<div class="row">		
			<input type="text" id="desc" name="desc" data-dojo-type="dijit/form/Textarea"/>
		</div>
		<div id="docwatch_logo"></div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_logo_url']).'</label>
		</div>
		<div class="row">		
			<input type="text" style="width: 35em;" id="logo_url" name="logo_url" data-dojo-type="dijit/form/TextBox"/>
		</div>
		<div class="row">!!options_rss!!</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_rights']).'</label>
		</div>
		<div class="row">
			<button data-dojo-type="dijit/form/Button" type="button">'.encoding_normalize::utf8_normalize($msg['tout_cocher_checkbox']).'
			    <script type="dojo/on" data-dojo-event="click" data-dojo-args="evt">
			        require(["dojo/dom", "dojo/query"], function(dom, query){    
						var checkboxes = query(\'input[type="checkbox"]\', dom.byId("user_id_table"));
						for(var i=0 ; i<checkboxes.length ; i++){
							checkboxes[i].checked = true;
						}
						
			        });
			    </script>
			</button>
			<button data-dojo-type="dijit/form/Button" type="button">'.encoding_normalize::utf8_normalize($msg['tout_decocher_checkbox']).'
			    <script type="dojo/on" data-dojo-event="click" data-dojo-args="evt">
			        require(["dojo/dom", "dojo/query"], function(dom, query){
			        	var idUser = dom.byId("owner").value;    
						var checkboxes = query(\'input[type="checkbox"]\', dom.byId("user_id_table"));
						for(var i=0 ; i<checkboxes.length ; i++){
							if(checkboxes[i].value != idUser){
								checkboxes[i].checked = false;
							}
						}
			        });
			    </script>
			</button>		
		</div>					
		<div class="row">!!users_checkboxes!!</div>
		<div class="row"></div>
		<div class="row">!!options_record!!</div>
		<div class="row">!!options_article!!</div>
		<div class="row">!!options_section!!</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_watch_form_boolean_expression']).'</label>
            <img  class="aide" onclick="aide_regex();return false;" height="14px" width="14px" title="'.htmlentities($msg['dsi_docwatch_watch_form_boolean_expression'],ENT_QUOTES,$charset).'" alt="'.htmlentities($msg['dsi_docwatch_watch_form_boolean_expression'],ENT_QUOTES,$charset).'" src="'.get_url_icon('info.png').'"/>
		</div>
		<div class="row">
			<input type="text" style="width: 35em;" id="boolean_expression" name="boolean_expression" data-dojo-type="dijit/form/TextBox" /> 
		</div>
		</div>
	<div class="row">	
		<div class="left">
			<button data-dojo-type="dijit/form/Button" id="docwatch_form_save" type="submit">'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_form_save']).'</button>
		</div>
		<div class="right">
			<button data-dojo-type="dijit/form/Button" id="docwatch_form_delete" type="button">'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_form_delete']).'</button>
		</div>
	</div>	
	<div class="row"></div>		
</form>
</div>';
$docwatch_category_form_tpl = '
<form data-dojo-attach-point="containerNode" data-dojo-attach-event="onreset:_onReset,onsubmit:_onSubmit" ${!nameAttrSetting}>	
	<div class="form-contenu">
		<input type="hidden" name="id" id="id" value=""/>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_category_form_category_parent']).'</label>
		</div>
		<div class="row">
			<select  id="parent" name="parent" data-dojo-type="dijit/form/Select" style="width:auto"></select>
		</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_category_form_libelle']).'</label>
		</div>	
		<div class="row">		
			<input type="text" id="title" name="title" required="true" data-dojo-type="dijit/form/ValidationTextBox"/>
		</div>
		<div class="row"></div>
	</div>
	<div class="row">	
		<div class="left">
			<button data-dojo-type="dijit/form/Button" id="docwatch_form_save" type="submit">'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_form_save']).'</button>
		</div>
		<div class="right">
			<button data-dojo-type="dijit/form/Button" id="docwatch_form_delete" type="button">'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_form_delete']).'</button>
		</div>
	</div>	
	<div class="row"></div>		
</form>
';

$docwatch_duplicate_source_form_tpl = '
<form data-dojo-attach-point="containerNode" data-dojo-attach-event="onreset:_onReset,onsubmit:_onSubmit" ${!nameAttrSetting}>
	<div class="form-contenu">
		<input type="hidden" name="id_duplicated_datasource" id="id_duplicated_datasource" value=""/>
		<input type="hidden" name="className" id="className" value=""/>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_datasource_title']).'</label>
		</div>	
		<div class="row">		
			<input type="text" id="title" name="title" required="true" data-dojo-type="dijit/form/ValidationTextBox"/>
		</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_source_form_watch_parent']).'</label>
		</div>
		<div class="row">
			<select  id="num_watch" name="num_watch" data-dojo-type="dijit/form/Select" style="width:auto"></select>
		</div>
		<div class="row"></div>
	</div>
	<div class="row">
		<div class="left">
			<button data-dojo-type="dijit/form/Button" id="docwatch_form_save" type="submit">'.encoding_normalize::utf8_normalize($msg['dsi_docwatch_form_save']).'</button>
		</div>
	</div>
	<div class="row"></div>
</form>
';

