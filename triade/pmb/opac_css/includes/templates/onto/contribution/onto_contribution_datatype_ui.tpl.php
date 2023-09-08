<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_ui.tpl.php,v 1.10 2018-10-29 16:18:43 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

$ontology_tpl['onto_contribution_datatype_docnum_file_script'] = '
		<script type="text/javascript">
			if (!window.!!instance_name!!_!!property_name!!_change) {
				window.!!instance_name!!_!!property_name!!_change = true;
			    require([
					"dojo/request/iframe",
					"dojo/query",
					"dojo/on",
					"dojo/dom-attr",
					"dojo/dom-construct",
					"dojo/dom",
					"dojo/dom-style",
					"dojo/ready",
					"dojox/widget/Standby"
				], function (iframe, query, on, domAttr, domConstruct, dom, domStyle, ready, Standby) {
			        ready(function () {
			            query("#!!instance_name!!_!!property_name!! input[type=\'file\']").forEach(function (node) {
			                on(node, "change", function (e) {
			                    var form_name = domAttr.get(e.target.form, "name");
								var standby = new Standby({target: "!!instance_name!!", text: "", imageText: ""});
								document.body.appendChild(standby.domNode);
								standby.startup();
								standby.show();
			                    iframe("'.$base_path.'/ajax.php?module=ajax&categ=contribution&sub=ajax_check_values&what=docnum_file", {
			                        form: form_name,
			                        data: {
			                            field_name: domAttr.get(e.target, "name")
			                        },
			                        handleAs: "json"
			                    }).then(function (data) {
			                        if (dom.byId("docnum_file_duplications")) {
			                            domConstruct.destroy(dom.byId("docnum_file_duplications"));
			                        }
			                    	var message = "";
			                    	if (parseInt(data.max_size)) {
			                    		message = "'.addslashes(sprintf($msg['onto_contribution_datatype_docnum_file_bigger_than_size_limit'], ini_get('upload_max_filesize'))).'";
			                    	} else if (parseInt(data.doublon)) {
			                            message = "'.addslashes($msg['onto_contribution_datatype_docnum_file_duplication_existing']).'";
			                    	}
			                    	if (message) {
			                    		var html = "<div id=\"docnum_file_duplications\"><strong style=\"color:red;\">" + message + "<\/strong><br/>";
			                    		if (data.records) {
				                            for (var i = 0; i < data.records.length; i++) {
				                                html += data.records[i];
				                            }
			                    		}
			                            html += "<\/div>";
			                            domConstruct.place(html, e.target, "after");
			                            if (dom.byId(form_name + "_onto_contribution_save_button")) {
			                            	domStyle.set(form_name + "_onto_contribution_save_button", "display", "none");
			                            }
			                            if (dom.byId(form_name + "_onto_contribution_push_button")) {
			                            	domStyle.set(form_name + "_onto_contribution_push_button", "display", "none");
										}
			                        } else {
			                            if (dom.byId(form_name + "_onto_contribution_save_button")) {
			                            	domStyle.set(form_name + "_onto_contribution_save_button", "display", "");
			                            }
			                            if (dom.byId(form_name + "_onto_contribution_push_button")) {
			                            	domStyle.set(form_name + "_onto_contribution_push_button", "display", "");
										}
			                            var labelNode = dom.byId(node.getAttribute("id").replace("docnum_file", "label"));
			                           	if(labelNode){
			                            	if(node.value.lastIndexOf("\\\") != -1){
			                            		labelNode.value = node.value.substr(node.value.lastIndexOf("\\\")+1);
			                            	}else{
			                            		labelNode.value = node.value;
			                            	}
			                           	}
			                        }
									standby.hide();
			                    }, function (err) {
			                        console.log(err);
			                    });
			                });
			            });
			        });
			    });
			}
		</script>';

/**
 * Upload directories
 */
$ontology_tpl['form_row_content_upload_directories'] = '
<input type="text" value="!!form_row_content_upload_directories_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]" readonly="readonly"/>
<input type="hidden" value="!!form_row_content_upload_directories_value!!" id="!!onto_row_id!!_!!onto_row_order!!_value" name="!!onto_row_id!![!!onto_row_order!!][value]"/>
<input type="hidden" value="http://www.w3.org/2000/01/rdf-schema#Literal" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<button id="!!onto_row_id!!_!!onto_row_order!!_button_dialog"></button>
<div id="!!onto_row_id!!_!!onto_row_order!!_dialog">
</div>
<script type="text/javascript">
	require([
		"dojo/store/Memory",
		"dijit/tree/ObjectStoreModel",
		"dijit/Tree",
		"dojo/dom",
		"dijit/registry",
		"dojo/on",
		"dojo/_base/lang",
		"apps/pmb/PMBDialog",
		"dijit/form/Button",
		"dojo/domReady!"
	], function (Memory, ObjectStoreModel, Tree, dom, registry, on, lang, Dialog, Button) {
		// Déjà fait une fois, ça ne sert à rien de le refaire !
		if (registry.byId("!!onto_row_id!!_!!onto_row_order!!_dialog")) {
			return false;
		}
		var dialog = new Dialog({}, "!!onto_row_id!!_!!onto_row_order!!_dialog");
		
		new Button({
			class: "bouton_small",
			label: "...",
			onClick: function() {
				dialog.show();
			}
		}, "!!onto_row_id!!_!!onto_row_order!!_button_dialog").startup();

		var store = new Memory({
			data : !!onto_row_memory_data!!,
	        getChildren: function(object){
	            return this.query({parent: object.id});
	        }
		});

		var model = new ObjectStoreModel({
			store: store,
        	query: {id: "root"},
			mayHaveChildren: function(item){
 				if (this.store.query({parent: item.id}).length) {
					return true;
				}
				return false;
			}
		});
		var tree = new Tree({
			id: "!!onto_row_id!!_!!onto_row_order!!_upload_directories_tree",
			model: model,
			showRoot: false,
			getIconClass : function(item, opened) {
				return (opened ? "dijitFolderOpened" : "dijitFolderClosed");
			},
			onClick : function(node) {
				dialog.hide();
				dom.byId("!!onto_row_id!!_!!onto_row_order!!_display_label").value = node.formatted_path_name;
				dom.byId("!!onto_row_id!!_!!onto_row_order!!_value").value = node.formatted_path_id;
			}
		});
		tree.placeAt(dialog);
		tree.startup();
		dialog.resize();
	});
</script>
';