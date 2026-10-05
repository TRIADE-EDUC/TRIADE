<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_datanode.tpl.php,v 1.2 2019/05/27 10:53:02 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $frbr_cataloging_datanode_form_tpl, $msg;

$frbr_cataloging_datanode_form_tpl = '
<div style="width: 400px; height: 500px; overflow: auto;">
<form data-dojo-attach-point="containerNode" data-dojo-attach-event="onreset:_onReset,onsubmit:_onSubmit" ${!nameAttrSetting}>	
	<div class="form-contenu">
		<input type="hidden" name="id" id="id" value=""/>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['frbr_cataloging_form_category_parent']).'</label>
		</div>
		<div class="row">
			<select  id="datanode_num_category" name="datanode_num_category" data-dojo-type="dijit/form/Select" style="width:auto"></select>
		</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['frbr_cataloging_form_label']).'</label>
		</div>	
		<div class="row">		
			<input type="text" id="datanode_title" name="datanode_title" required="true" data-dojo-type="dijit/form/ValidationTextBox"/>
		</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['frbr_cataloging_form_comment']).'</label>
		</div>
		<div class="row">		
			<input type="text" id="datanode_comment" name="datanode_comment" data-dojo-type="dijit/form/Textarea"/>
		</div>
		<div class="row">
			<label>'.encoding_normalize::utf8_normalize($msg['frbr_cataloging_form_rights']).'</label>
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
	</div>
	<div class="row">	
		<div class="left">
			<button data-dojo-type="dijit/form/Button" id="datanode_button_save" type="submit">'.encoding_normalize::utf8_normalize($msg['frbr_cataloging_form_save']).'</button>
		</div>
		<div class="right">
			<button data-dojo-type="dijit/form/Button" id="datanode_button_delete" type="button">'.encoding_normalize::utf8_normalize($msg['frbr_cataloging_form_delete']).'</button>
		</div>
	</div>	
	<div class="row"></div>		
</form>
</div>';