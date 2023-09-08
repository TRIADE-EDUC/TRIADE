<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_ui.tpl.php,v 1.26 2019-04-19 14:33:32 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

/*
 * Common
 */
$ontology_tpl['form_row'] = '
<div id="!!onto_row_id!!">
	<div class="row">	
		<label class="etiquette" for="!!onto_row_id!!">!!onto_row_label!!</label>
	</div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	!!onto_rows!!
</div>
';

$ontology_tpl['form_row_content']='
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
	!!onto_inside_row!!
	!!onto_row_inputs!!
</div>
';

$ontology_tpl['form_row_content_input_add']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add(\'!!onto_row_id!!\',0);">
';

$ontology_tpl['form_row_content_input_add_selector']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_select(\'!!onto_row_id!!\',0);">
';

$ontology_tpl['form_row_content_input_add_ressource_selector']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_selector(\'!!onto_row_id!!\',0);">
';

$ontology_tpl['form_row_content_input_del']='
<input type="button" value="'.$msg['ontology_p_del_button'].'" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_del(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

/*
 * Text
 */
$ontology_tpl['form_row_content_text']='
<textarea cols="80" rows="4" wrap="virtual" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">!!onto_row_content_text_value!!</textarea>
!!onto_row_combobox_lang!!
<input type="hidden" value="!!onto_row_content_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';


/*
 * Small text
 */
$ontology_tpl['form_row_content_small_text']='
<input type="text" class="saisie-80em" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
!!onto_row_combobox_lang!!
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * Small text card
 */
$ontology_tpl['form_row_card'] = '
<div id="!!onto_row_id!!">
	<div class="row"><label class="etiquette" for="!!onto_row_id!!">!!onto_row_label!!</label></div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	<input type="hidden" id="!!onto_row_id!!_input_type" value="!!onto_input_type!!">
	<input type="hidden" id="!!onto_row_id!!_available_lang" value=\'!!tab_available_lang!!\'>
	<input type="hidden" id="!!onto_row_id!!_lang_label" value=\'!!tab_lang_label!!\'>
	<div class="row" id="!!onto_row_id!!_combobox_lang" >!!onto_row_combobox_lang!! !!input_add!!</div>
	!!onto_rows!!
</div>
';

$ontology_tpl['form_row_content_small_text_card']='
<input type="text" class="saisie-80em" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
!!label_lang!!
<input type="hidden" value="!!onto_row_content_small_text_lang!!" name="!!onto_row_id!![!!onto_row_order!!][lang]" id="!!onto_row_id!!_!!onto_row_order!!_lang" />
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

$ontology_tpl['form_row_content_input_del_card']='
<input type="button" value="'.$msg['ontology_p_del_button'].'" id="!!onto_row_id!!_!!onto_row_order!!_del_card" onclick="onto_del_card(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

$ontology_tpl['form_row_content_input_add_card']='
<input class="bouton_small" id="!!onto_row_id!!_add_card" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_card(\'!!onto_row_id!!\',!!onto_row_max_card!!);ajax_parse_dom();">
';

/*
 * Ressource selector
 */
$ontology_tpl['form_row_content_resource_selector']='
<input type="text" value="!!form_row_content_resource_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]" 
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"   
	completion="!!onto_completion!!" 
	autexclude="!!onto_current_element!!" 
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
<input type="hidden" value="!!form_row_content_resource_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_resource_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

$ontology_tpl['form_row_content_input_sel']='
<input type="button" class="bouton_small" onclick="onto_open_selector(\'!!onto_row_id!!\',\'!!property_name!!\', \'!!onto_current_range!!\');" value="'.$msg['ontology_p_sel_button'].'" id="!!onto_row_id!!_sel" />
';

/**
 * Linked forms
 */
//$ontology_tpl['form_row_content_linked_form']='
//<div data-dojo-type="dijit/form/DropDownButton">
//		<div data-dojo-type="dijit/TooltipDialog">
//			!!linked_forms!!
//		</div>
//</div>
//';

//$ontology_tpl['form_row_content_linked_form_button']='
//<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" data-form_url="!!url_linked_form!!" id="!!onto_row_id!!_!!linked_form_id!!_sel" data-form_title="!!linked_form_title!!" >!!linked_form_title!!</button>
//';

$ontology_tpl['form_row_content_linked_form']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" data-form_url="!!url_linked_form!!" id="!!onto_row_id!!_sel" data-form_title="!!linked_form_title!!">'.$msg['ontology_p_sel_button'].'</button>
';

$ontology_tpl['form_row_content_input_remove']='
<input type="button" id="!!onto_row_id!!_!!onto_row_order!!_del" value="'.$msg['ontology_p_del_button'].'" onclick="onto_remove_selector_value(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

/*
 * checkbox
 */
$ontology_tpl['form_row_content_checkbox']='
<input type="checkbox" class="saisie-80em" !!onto_row_content_checkbox_checked!! value="1" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';


/*
 * date & dojo widget en général (supp & add) 
*/
$ontology_tpl['form_row_content_date']='
		<input type="text" id="!!onto_row_id!!_!!onto_row_order!!_value" name="!!onto_row_id!![!!onto_row_order!!][value]" value="!!onto_date!!" data-dojo-type="dijit/form/DateTextBox"/>
		<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>';

$ontology_tpl['form_row_content_widget_add']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_dojo_element(\'!!onto_row_id!!\',0);">
';

/**
 * Bouton suppression widget dojo
 */
$ontology_tpl['form_row_content_widget_del']='
<input type="button" value="'.$msg['ontology_p_del_button'].'" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_remove_dojo_element(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

/** 
 * Représentation d'un entier 
 */
$ontology_tpl['form_row_content_integer']='
<input type="text" class="saisie-80em" value="!!onto_row_content_integer_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden" value="!!onto_row_content_integer_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/**
 * Représentation d'un marclist
 */
$ontology_tpl['form_row_content_marclist']='
<select name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
	!!onto_row_content_marclist_options!!
</select>		
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>';



/*
 * Ressource selector multiple
*/
$ontology_tpl['form_row_content_resource_selector_record']='
<input type="text" value="!!form_row_content_resource_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"
	completion="!!resource_type!!"
	autexclude="!!onto_current_element!!"
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
<input type="hidden" value="!!form_row_content_resource_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_resource_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * Liste
 */
$ontology_tpl['form_row_content_list']='
<select name="!!onto_row_id!![!!onto_row_order!!][value][]" id="!!onto_row_id!!_!!onto_row_order!!_value" !!onto_row_multiple!!>
	!!onto_row_content_list_options!!
</select>		
<input type="hidden" value="!!onto_row_content_list_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<input type="hidden" value="!!onto_row_content_list_lang!!" name="!!onto_row_id!![!!onto_row_order!!][lang]" id="!!onto_row_id!!_!!onto_row_order!!_lang"/> 
';

/*
 * Liste mutliple
 */
$ontology_tpl['form_row_content_list_multi']='
<select
	name="!!onto_row_id!![!!onto_row_order!!][value][]"
	id="!!onto_row_id!!_!!onto_row_order!!_value"
	multiple="yes"
	!!onto_disabled!!
>
	!!onto_row_content_list_options!!
</select>
';

/*
 * Hidden field
 */
$ontology_tpl['form_row_hidden'] = '
<div id="!!onto_row_id!!">
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	!!onto_rows!!
</div>
';

$ontology_tpl['form_row_content_hidden']='
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
	<input type="hidden" value="!!onto_row_content_hidden_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
	<input type="hidden" value="!!onto_row_content_hidden_lang!!" name="!!onto_row_id!![!!onto_row_order!!][lang]" id="!!onto_row_id!!_!!onto_row_order!!_lang"/> 
	<input type="hidden" value="!!onto_row_content_hidden_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
</div>
';

/*
 * Merge properties
 */
$ontology_tpl['form_row_merge_properties'] = '
<div id="!!onto_row_id!!">
	<div class="row">
		<label class="etiquette">!!onto_row_label!!</label>
		<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!" data-dojo-type="dijit/form/TextBox"/>
	</div>
	!!onto_rows!!
</div>
';

/**
 * Selecteur PMB
 */
$ontology_tpl['form_row_content_input_sel_pmb']='
<input type="button" class="bouton_small" onclick="onto_open_pmb_selector(\'!!onto_row_id!!_\',\'!!onto_selector_url!!\');" value="'.$msg['ontology_p_sel_button'].'" id="!!onto_row_id!!_sel" />
<input type="hidden" value="!!onto_pmb_selector_min_card!!"  id="!!onto_row_id!!_min">
<input type="hidden" value="!!onto_pmb_selector_max_card!!"  id="!!onto_row_id!!_max">	
<input type="hidden" value="!!max_field_value!!" name="!!onto_row_id!!_max_field" id="!!onto_row_id!!_max_field"/>
';


/*
 * Ressource selector
 */
$ontology_tpl['form_row_content_resource_selector_pmb']='
<input type="text" value="!!form_row_content_resource_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_label_!!onto_row_order!!" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
	autfield="!!onto_row_id!!_value_!!onto_row_order!!"
	completion="!!onto_completion!!"
	autexclude="!!onto_current_element!!"
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
<input type="hidden" value="!!form_row_content_resource_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_value_!!onto_row_order!!">
<input type="hidden" value="!!form_row_content_resource_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_type_!!onto_row_order!!"/>
';

$ontology_tpl['form_row_content_input_add_selector_pmb']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_pmb_selector(\'!!onto_row_id!!\');">
';

$ontology_tpl['form_row_content_input_remove_pmb']='
<input type="button" id="!!onto_row_id!!_del_!!onto_row_order!!" value="'.$msg['ontology_p_del_button'].'" onclick="onto_remove_pmb_selector_value(event);" class="bouton_small">
';

/*
 *	File
 */
$ontology_tpl['form_row_content_file']='
!!onto_contribution_last_file!!
<input type="file"  value="!!onto_row_content_file_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden"  value="!!onto_row_content_file_value!!" name="!!onto_row_id!![!!onto_row_order!!][default_value]" id="!!onto_row_id!!_!!onto_row_order!!_default_value" data-dojo-type="dijit/form/TextBox"/>
<script type="text/javascript">
	var form = document.forms["!!onto_form_name!!"];
	if (form.getAttribute("enctype") != "multipart/form-data") {
		form.setAttribute("enctype","multipart/form-data");
	}
</script>
';

$ontology_tpl['form_row_content_last_file']='
<label>'.$msg["onto_last_file"].' : <em id="!!onto_row_id!!_!!onto_row_order!!_onto_last_file_label">!!onto_row_content_file_value!!</em></label>
<input type="hidden" value="!!onto_row_content_file_id!!" name="!!onto_row_id!![!!onto_row_order!!][onto_file_id]" id="!!onto_row_id!!_!!onto_row_order!!_onto_file_id" /> 
<br/>
';

$ontology_tpl['form_row_content_url']='
!!onto_max_value!!
<div id="!!onto_row_id!!_!!onto_row_order!!_picto" style="display:inline"></div>
<input type="text" data-url-field="true" onchange="onto_check_lnk(this)" class="saisie-80em" value="!!onto_row_content_url_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
!!onto_url_add_button!!
<input type="hidden" value="!!onto_row_content_url_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

$ontology_tpl['form_row_content_input_add_url']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_url(\'!!onto_row_id!!\',0);">
';

$ontology_tpl['form_row_content_input_del_url']='
<input type="button" value="'.$msg['ontology_p_del_button'].'" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_del(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

$ontology_tpl['form_row_content_url_max_value']='
<input type="hidden" id="!!onto_row_id!!_max_value" value="!!onto_restrict_max_value!!"/>
';

$ontology_tpl['form_row_content_input_add_file']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_file(\'!!onto_row_id!!\',0);">
';


$ontology_tpl['onto_contribution_datatype_docnum_file_script'] = '
		<script type="text/javascript">
			if (!window.!!instance_name!!_!!property_name!!_change) {
				window.!!instance_name!!_!!property_name!!_change = true;
			    require(["dojo/request/iframe", "dojo/query", "dojo/on", "dojo/dom-attr", "dojo/dom-construct", "dojo/dom", "dojo/dom-style", "dojo/ready"], function (iframe, query, on, domAttr, domConstruct, dom, domStyle, ready) {
			        ready(function () {
			            query("#!!instance_name!!_!!property_name!! input[type=\'file\']").forEach(function (node) {
			                on(node, "change", function (e) {
			                    var form_name = domAttr.get(e.target.form, "name");
			                    iframe("'.$base_path.'/ajax.php?module=ajax&categ=contribution&sub=ajax_check_values&what=docnum_file_doublon", {
			                        form: form_name,
			                        data: {
			                            field_name: domAttr.get(e.target, "name")
			                        },
			                        handleAs: "json"
			                    }).then(function (data) {
			                        if (dom.byId("docnum_file_duplications")) {
			                            domConstruct.destroy(dom.byId("docnum_file_duplications"));
			                        }
			                        if (data.doublon) {
			                            var html = "<div id=\"docnum_file_duplications\"><strong style=\"color:red;\">'.$msg['onto_contribution_datatype_docnum_file_duplication_existing'].'<\/strong><br/>";
			                            for (var i = 0; i < data.records.length; i++) {
			                                html += data.records[i];
			                            }
			                            html += "<\/div>";
			                            domConstruct.place(html, e.target, "after");
			                            domStyle.set(form_name + "_onto_contribution_save_button", "display", "none");
			                            domStyle.set(form_name + "_onto_contribution_push_button", "display", "none");
			                        } else {
			                            domStyle.set(form_name + "_onto_contribution_save_button", "display", "");
			                            domStyle.set(form_name + "_onto_contribution_push_button", "display", "");
			                            var labelNode = dom.byId(node.getAttribute("id").replace("docnum_file", "label"));
			                           	if(labelNode && !labelNode.value){
			                            	if(node.value.lastIndexOf("\\\") != -1){
			                            		labelNode.value = node.value.substr(node.value.lastIndexOf("\\\")+1);
			                            	}else{
			                            		labelNode.value = node.value;
			                            	}
			              
			                           	}
			                        }
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
 * champ caché pour le type
 */
$ontology_tpl['form_row_content_type'] = '
	<input type="hidden" value="!!onto_row_content_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type" data-dojo-type="dijit/form/TextBox"/>
';

/*
 * Responsability selector
 */
$ontology_tpl['form_row_content_responsability_selector']='
<input type="text" value="!!form_row_content_responsability_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"
	completion="!!onto_completion!!"
	autexclude="!!onto_current_element!!"
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
<select name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
	!!onto_row_content_marclist_options!!
</select>
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<input type="hidden" value="!!form_row_content_responsability_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_responsability_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * linked record selector
 */
$ontology_tpl['form_row_content_linked_record_selector']='
<input type="text" value="!!form_row_content_linked_record_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"
	completion="!!onto_completion!!"
	autexclude="!!onto_current_element!!"
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
!!onto_row_content_linked_record_selector!!
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<input type="hidden" value="!!form_row_content_linked_record_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_linked_record_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/**
 * resource selector opac
 */
$ontology_tpl['form_row_content_resource_selector_opac']='
<div data-dojo-type="apps/pmb/contribution/datatypes/MemorySelector"
    data-dojo-id="!!onto_row_id!!_!!onto_row_order!!_memory"
	data-dojo-props="
		data : [{id : \'!!form_row_content_resource_selector_display_label!!\', datas : \'!!form_row_content_resource_selector_display_label!!\', value : \'!!form_row_content_resource_selector_value!!\'}]"
></div>
<input data-dojo-type="apps/pmb/contribution/datatypes/ResourceSelector"
    data-dojo-props="
		store:!!onto_row_id!!_!!onto_row_order!!_memory,
		query : {
			completion : \'!!onto_completion!!\',
			autexclude : \'!!onto_current_element!!\',
			param1 : \'!!onto_equation_query!!\',
			param2 : \'!!onto_area_id!!\',
			handleAs : \'json\'
		},
		searchAttr:\'datas\',
		labelAttr : \'datas\',
		valueNodeId:\'!!onto_row_id!!_!!onto_row_order!!_value\',
    	value:\'!!form_row_content_resource_selector_display_label!!\',
		!!onto_disabled!!"
    name="!!onto_row_id!![!!onto_row_order!!][display_label]"
    id="!!onto_row_id!!_!!onto_row_order!!_display_label"
/>
<input type="hidden" value="!!form_row_content_resource_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" data-dojo-type="dijit/form/TextBox"/>
';

/*
 * linked authority selector
 */
$ontology_tpl['form_row_content_linked_authority_selector']='
<input type="text" value="!!form_row_content_linked_authority_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"
	completion="!!onto_completion!!"
	autexclude="!!onto_current_element!!"
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
<select name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
	!!onto_row_content_marclist_options!!
</select>
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<input type="hidden" value="!!form_row_content_linked_authority_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_linked_authority_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/**
 * Liste boutons radios ou checkbox
 */
$ontology_tpl['form_row_content_list_checkbox_option']='
<input type="!!radio_or_checkbox!!" name="!!onto_row_id!![!!onto_row_order!!][value][]" id="!!onto_row_id!!_!!onto_row_order!!_!!onto_row_content_value_index!!" value="!!onto_row_content_value!!" !!onto_checked!! !!onto_disabled!! />
<label for="!!onto_row_id!!_!!onto_row_order!!_!!onto_row_content_value_index!!">!!onto_row_content_label!!<label>';

$ontology_tpl['form_row_content_list_checkbox'] = '
<input type="hidden" value="!!onto_row_content_values!!" id="!!onto_row_id!!_!!onto_row_order!!_value" />
<script>
if (typeof window.!!onto_row_id!!_!!onto_row_order!!_script == "undefined") {
	document.querySelectorAll("input[name=\'!!onto_row_id!![!!onto_row_order!!][value][]\']").forEach(function(node, index, nodes) {
		node.addEventListener("click", function() {
			var values = [];
			nodes.forEach(function(node) {
				if (node.checked) {
					values.push(node.value);
				}
			});
			document.getElementById("!!onto_row_id!!_!!onto_row_order!!_value").value=values.join();
		});
	});
	window.!!onto_row_id!!_!!onto_row_order!!_script = true;
}
</script>';