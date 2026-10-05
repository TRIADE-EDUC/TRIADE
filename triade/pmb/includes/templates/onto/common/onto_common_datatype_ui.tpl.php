<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_ui.tpl.php,v 1.53.6.3.2.1 2025/02/06 10:53:23 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

/*
 * Common
 */
$ontology_tpl['form_row'] = '
<div id="!!onto_row_id!!">
	<div class="row">	
		<label class="etiquette !!form_row_content_mandatory_class!!" for="!!onto_row_id!!">!!onto_row_label!! !!form_row_content_mandatory_sign!!</label>
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

$ontology_tpl['form_row_content_without_flex']='
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
	!!onto_inside_row!!
	!!onto_row_inputs!!
</div>
';

$ontology_tpl['form_row_content_with_flex']='
<div class="row !!onto_row_is_draft!!" id="!!onto_row_id!!_!!onto_row_order!!">
    <div class="contribution_area_flex">
    	!!onto_inside_row!!
    	!!onto_row_inputs!!
    </div>
    !!onto_row_resource_selector!!
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

$ontology_tpl['form_row_content_input_add_linked_record']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_selector(\'!!onto_row_id!!\',0);">
';

$ontology_tpl['form_row_content_input_add_linked_authority']='
<button type="button" data-dojo-type="apps/contribution_area_form/datatypes/ButtonAddAuthority" class="bouton_small" id="!!onto_row_id!!_add_linked_authority" data-dojo-props="elementName : \'!!onto_row_id!!\', elementOrder : \'0\' " >'.$msg['ontology_p_add_button'].'</button>
';

$ontology_tpl['form_row_content_input_del']='
<input type="button" value="'.$msg['ontology_p_del_button'].'" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_del(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

/*
 * Text
 */
$ontology_tpl['form_row_content_text']='
<textarea cols="80" rows="4" wrap="virtual" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" class="!!editor_class!!" >!!onto_row_content_text_value!!</textarea>
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
	<div class="row">
        <label class="etiquette !!form_row_content_mandatory_class!!" for="!!onto_row_id!!">!!onto_row_label!! !!form_row_content_mandatory_sign!!</label>
    </div>
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

//on ajoute simplement des atibuts hidden pour cacher les champs
$ontology_tpl['form_row_content_resource_selector_no_search']='
<input type="hidden" value="!!form_row_content_resource_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]" 
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"   
	completion="!!onto_completion!!" 
	autexclude="!!onto_current_element!!" 
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
<input type="hidden" value="!!form_row_content_resource_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_resource_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * Item creator
 */
$ontology_tpl['form_row_content_item_creator']='
<input type="text" value="!!form_row_content_item_creator_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]" autocomplete="off"/>
<input type="hidden" value="!!form_row_content_item_creator_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_item_creator_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
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
		<input type="date" id="!!onto_row_id!!_!!onto_row_order!!_value" name="!!onto_row_id!![!!onto_row_order!!][value]" value="!!onto_date!!" />
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
<div>
    !!onto_contribution_last_file!!
    <div class="contribution_file_template">
        !!onto_contribution_file_template!!
    </div>
    <input type="file"  value="!!onto_row_content_file_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
    <input type="hidden"  value="!!onto_row_content_file_value!!" name="!!onto_row_id!![!!onto_row_order!!][default_value]" id="!!onto_row_id!!_!!onto_row_order!!_default_value" data-dojo-type="dijit/form/TextBox"/>
    <input type="hidden"  value="!!onto_row_content_file_data!!" name="!!onto_row_id!![!!onto_row_order!!][data]" id="!!onto_row_id!!_!!onto_row_order!!_data"/>
    <script type="text/javascript">
    	var form = document.forms["!!onto_form_name!!"];
    	if (form.getAttribute("enctype") != "multipart/form-data") {
    		form.setAttribute("enctype","multipart/form-data");
    	}
    </script>
</div>
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
<select name="!!onto_row_id!![!!onto_row_order!!][assertions][author_function]" id="!!onto_row_id!!_!!onto_row_order!!_assertions_author_function">
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
!!onto_row_content_linked_record_selector!!
<input type="text" value="!!form_row_content_linked_record_selector_display_label!!" list="!!onto_row_id!!_!!onto_row_order!!_display_label_list"
    data-dojo-type="apps/contribution_area_form/datatypes/ResourceSelector"
    data-dojo-props="
        name:\'!!onto_row_id!![!!onto_row_order!!][display_label]\',
        id:\'!!onto_row_id!!_!!onto_row_order!!_display_label\',
        baseId:\'!!onto_row_id!!_!!onto_row_order!!\',
        completion:\'!!onto_completion!!\',
        autexclude:\'!!onto_current_element!!\',
        param1:\'!!onto_equation_query!!\',
        param2:\'!!onto_area_id!!\',
        value:\'!!form_row_content_linked_record_selector_display_label!!\',
        valueNodeId:\'!!onto_row_id!!_!!onto_row_order!!_value\',
        isDraft:\'!!form_row_content_linked_record_selector_is_draft!!\',
        isDraftNodeId:\'!!onto_row_id!!_!!onto_row_order!!_is_draft\',
        templateNodeId:\'!!onto_row_id!!_!!onto_row_order!!_resource_template\'"
    !!onto_disabled!! autocomplete="off"/>
<datalist id="!!onto_row_id!!_!!onto_row_order!!_display_label_list"></datalist>
<input type="checkbox" title="'.$msg['aut_link_reciproque_title'].'" class="add_reverse_link" value="1" name="!!onto_row_id!![!!onto_row_order!!][assertions][add_reverse_link]" id="!!onto_row_id!!_!!onto_row_order!!_assertions_add_reverse_link" !!form_row_content_linked_record_selector_add_reverse_link_checked!!>
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][marclist_type]" id="!!onto_row_id!!_!!onto_row_order!!_marclist_type"/>
<input type="hidden" value="!!form_row_content_linked_record_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_linked_record_selector_is_draft!!" name="!!onto_row_id!![!!onto_row_order!!][is_draft]" id="!!onto_row_id!!_!!onto_row_order!!_is_draft">
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

<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
    <div class="contribution_area_flex">
        <select name="!!onto_row_id!![!!onto_row_order!!][relation_type_authority]" id="!!onto_row_id!!_!!onto_row_order!!_relation_type_authority">
        	!!onto_row_content_marclist_options!!
        </select>
        !!onto_row_content_authority_type!!
        <img class="img_plus" border="0" hspace="3" src="'.get_url_icon('plus.gif').'" id="!!onto_row_id!!_!!onto_row_order!!_img_plus" data-prefix="!!onto_row_id!!_!!onto_row_order!!"
        	onclick="show_comment_area(this.dataset.prefix);"/>		
        <input type="text" value="!!form_row_content_linked_authority_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
        	autfield="!!onto_row_id!!_!!onto_row_order!!_value"
        	completion="!!onto_completion!!"
        	autexclude="!!onto_current_element!!"
        	att_id_filter="!!onto_current_range!!"
        	autocomplete="off"
    	 />
        <input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
        <input type="hidden" value="!!form_row_content_linked_authority_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
        <input type="hidden" value="!!form_row_content_linked_authority_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
        <!-- c est pas top de mettre les boutons direct ici mais sinon on est trop embete avec le display flex (pour aligner tous les champs)-->
    	!!onto_row_inputs!!
    </div>
    <div class="row" id="!!onto_row_id!!_!!onto_row_order!!_comment_area" style="display:none;">
        <div class="row">
            <label>'.$msg["aut_link_duration_date"].'</label>
        </div>
        <div class="row contribution_area_flex">
            '.$msg["aut_link_duration_entre"].' <input type="text" placeholder="JJ/MM/AAAA" size="11" id="!!onto_row_id!!_!!onto_row_order!!_start_date" name="!!onto_row_id!![!!onto_row_order!!][start_date]" value="!!form_row_content_linked_authority_selector_start_date!!">
            '.$msg["aut_link_duration_et"].' <input type="text" placeholder="JJ/MM/AAAA" size="11" id="!!onto_row_id!!_!!onto_row_order!!_end_date" name="!!onto_row_id!![!!onto_row_order!!][end_date]" value="!!form_row_content_linked_authority_selector_end_date!!">
        </div>        
        <div class="row">
            <label>'.$msg["aut_link_comment"].'</label><br>
    	</div>
        <div class="row">
            <textarea class="saisie-80em aut_link_comment" name="!!onto_row_id!![!!onto_row_order!!][comment]" id="!!onto_row_id!!_!!onto_row_order!!_comment" cols="62" rows="2" >!!form_row_content_linked_authority_selector_comment!!</textarea>	
        </div>
    </div>
    <script type="text/javascript">
        function onchange_aut_link_contrib_selector(prefix, authority_type) {
            var aut_link_display_label = document.getElementById(prefix + "_display_label");
            var aut_link_value = document.getElementById(prefix + "_value");
            aut_link_display_label.value = "";
            aut_link_value.value = "";

            var table_name = "authors";
    
            switch (authority_type) {
    			case "1" :
    				table_name = "authors";
                    break;
    			case "2" :
    				table_name =  "categories";
                    break;
    			case "3" :
    				table_name = "publishers";
                    break;
    			case "4" :
    				table_name = "collections";
                    break;
    			case "5" :
    				table_name = "subcollections";
                    break;
    			case "6" :
    				table_name = "serie";
                    break;
    			case "7" :
    				table_name = "titre_uniforme";
                    break;
    			case "8" :
    				table_name = "indexint";
                    break;
    			case "10" :
    				table_name = "onto";
                    aut_link_display_label.setAttribute("att_id_filter", "http://www.w3.org/2004/02/skos/core#Concept");
                    break;
                default : 
                    if (authority_type > 1000) {
    				    table_name = "authperso_" + (authority_type - 1000);
                    }
                    break;
    		}	
            aut_link_display_label.setAttribute("completion",table_name);
        }
    
        function show_comment_area(prefix) {
    		if(document.getElementById(prefix+"_comment_area").style.display=="none") {
    			document.getElementById(prefix+"_img_plus").src="'.get_url_icon('minus.gif').'";
    			document.getElementById(prefix+"_comment_area").style.display="block";
    		}
    		else {
    			document.getElementById(prefix+"_img_plus").src="'.get_url_icon('plus.gif').'";
    			document.getElementById(prefix+"_comment_area").style.display="none";
    		}
        }
    </script>
</div>
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

$ontology_tpl['form_row_content_floating_date_script'] = "<script>
function date_flottante_type_onchange(field_name) {
    var type = document.getElementById(field_name + '_value').value;
    switch(type) {
        case '4' : // interval date
            document.getElementById(field_name + '_date_begin_zone_label').style.display = '';
            document.getElementById(field_name + '_date_end_zone').style.display = '';
            break;
        case '0' : // vers
        case '1' : // avant
        case '2' : // après
        case '3' : // date précise
        default :
            document.getElementById(field_name + '_date_begin_zone_label').style.display = 'none';
            document.getElementById(field_name + '_date_end_zone').style.display = 'none';
            break;
    }
}
    
function date_flottante_reset_fields(field_name) {
    document.getElementById(field_name + '_date_begin').value = '';
    document.getElementById(field_name + '_date_end').value = '';
    document.getElementById(field_name + '_comment').value = '';
}
</script>";

$ontology_tpl['form_row_content_floating_date'] = "<div>
					<select id='!!onto_row_id!!_!!onto_row_order!!_value' name='!!onto_row_id!![!!onto_row_order!!][value]' onchange=\"date_flottante_type_onchange('!!onto_row_id!!_!!onto_row_order!!');\">
 						!!select_floating_date_options!!
					</select>
 					<span id='!!onto_row_id!!_!!onto_row_order!!_date_begin_zone'>
						<label id='!!onto_row_id!!_!!onto_row_order!!_date_begin_zone_label' for='!!onto_row_id!!_!!onto_row_order!!_date_begin'>" . $msg['parperso_option_duration_begin'] . "</label>
						<input type='text' id='!!onto_row_id!!_!!onto_row_order!!_date_begin' name='!!onto_row_id!![!!onto_row_order!!][date_begin]' value='!!floating_date_begin!!' placeholder='" . $msg["format_date_input_placeholder"] . "' maxlength='11' size='11' />
					</span>
 					<span id='!!onto_row_id!!_!!onto_row_order!!_date_end_zone'>
						<label id='!!onto_row_id!!_!!onto_row_order!!_date_end_zone_label' for='!!onto_row_id!!_!!onto_row_order!!_date_end'>" . $msg['parperso_option_duration_end'] . "</label>
						<input type='text' id='!!onto_row_id!!_!!onto_row_order!!_date_end' name='!!onto_row_id!![!!onto_row_order!!][date_end]' value='!!floating_date_end!!' placeholder='" . $msg["format_date_input_placeholder"] . "' maxlength='11' size='11' />
					</span>
					<label>" . $msg['parperso_option_duration_comment'] . "</label>
					<input type='text' id='!!onto_row_id!!_!!onto_row_order!!_comment' name='!!onto_row_id!![!!onto_row_order!!][comment]' value='!!floating_date_comment!!' class='saisie-30em'/>
					<input class='bouton' type='button' value='X' onClick=\"date_flottante_reset_fields('!!onto_row_id!!_!!onto_row_order!!');\"/>
            <!--<input class='bouton' type='button' value='+' onclick='add_custom_date_flottante_()' >-->
		</div>
		<script>
			date_flottante_type_onchange('!!onto_row_id!!_!!onto_row_order!!');
        </script>";

/*
 * multilingual qualified
 */
$ontology_tpl['form_row_content_multilingual_qualified']="
!!onto_row_content_value_type!!
<input type='hidden' value='!!onto_row_content_multilingual_qualified_range!!' name='!!onto_row_id!![!!onto_row_order!!][type]' id='!!onto_row_id!!_!!onto_row_order!!_type'/>
<select id='!!onto_row_id!!_!!onto_row_order!!_qualification' name='!!onto_row_id!![!!onto_row_order!!][qualification]'>
    !!onto_row_content_qualification_options!!
</select>
<select id='!!onto_row_id!!_!!onto_row_order!!_lang' name='!!onto_row_id!![!!onto_row_order!!][lang]'>
    !!onto_row_content_lang_options!!
</select>";

/*
 * Bouton d'ajout multilingual qualified
 */
$ontology_tpl['form_row_content_input_add_multilingual_qualified']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" id="!!onto_row_id!!_add_multilingual_qualified" data-dojo-props="elementName : \'!!onto_row_id!!\', elementOrder : \'0\' " >'.$msg['ontology_p_add_button'].'</button>
';

/*
 * Multilingual qualified type Text
 */
$ontology_tpl['onto_row_content_multilingual_qualified_text']="
<input type='text' value='!!onto_row_content_values!!' 
        id='!!onto_row_id!!_!!onto_row_order!!_value' 
        name='!!onto_row_id!![!!onto_row_order!!][value]'
        maxlength='!!multilingual_qualified_maxlength!!' />
";

/*
 * Multilingual qualified type Text large
 */
$ontology_tpl['onto_row_content_multilingual_qualified_textarea']="
<textarea id='!!onto_row_id!!_!!onto_row_order!!_value' name='!!onto_row_id!![!!onto_row_order!!][value]' maxlength='!!multilingual_qualified_maxlength!!'>!!onto_row_content_values!!</textarea>";



/*
 * Small text link
 */
$ontology_tpl['form_row_link'] = '
<div id="!!onto_row_id!!">
	<div class="row">
        <label class="etiquette !!form_row_content_mandatory_class!!" for="!!onto_row_id!!">!!onto_row_label!! !!form_row_content_mandatory_sign!!</label>
    </div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	<input type="hidden" id="!!onto_row_id!!_input_type" value="!!onto_input_type!!">
	!!onto_rows!!
</div>
';

$ontology_tpl['form_row_content_input_add_text_link']='
<input type="button" class="bouton" id="!!onto_row_id!!_add_text_link" data-element-name = "!!onto_row_id!!"  onClick="onto_add_link(\'!!onto_row_id!!\', \'!!onto_row_order!!\')" data-element-order="0" class="bouton" value="'.$msg['ontology_p_add_button'].'" />
';

$ontology_tpl['form_row_content_small_text_link']='
<div id="!!onto_row_id!!_!!onto_row_order!!_lien_check" style="display:inline"></div>
<input type="text" class="saisie-80em" onchange="check_link(this);" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

$ontology_tpl['form_row_content_input_open_link']='
<input class="bouton" type="button" onClick="open_link(this)" title="'.$msg['CheckLink'].'" value="'.$msg['CheckButton'].'" id="!!onto_row_id!!_!!onto_row_order!!_open_link"/>
';

$ontology_tpl['onto_script_small_text_link'] = "
<script>
function open_link(inputNode) {

    var baseNodeId = inputNode.id.replace('_open_link', '');
    var node = document.getElementById(baseNodeId+'_value');

    if (node) {
        var link = node.value;
        if (link) {
            window.open(link)
        }
    }
}
const tabTokens_datatype_ui_!!csrf_token_id!! = !!tokens_csrf!!;
function check_link(inputNode) {

    var baseNodeId = inputNode.id.replace('_value', '');
    var element = inputNode;

    if (element  && element.value != '') {

        var wait = document.createElement('img');
        wait.setAttribute('src','".get_url_icon('patience.gif')."');
        wait.setAttribute('align','top');
        while(document.getElementById(baseNodeId + '_lien_check').firstChild){
			document.getElementById(baseNodeId + '_lien_check').removeChild(document.getElementById(baseNodeId + '_lien_check').firstChild);
        }
		var csrf_token = tabTokens_datatype_ui_!!csrf_token_id!![0];
		tabTokens_datatype_ui_!!csrf_token_id!!.splice(0, 1);
        document.getElementById(baseNodeId + '_lien_check').appendChild(wait);
        var testlink = encodeURIComponent(element.value);
        var req = new XMLHttpRequest();
        req.open('GET', './ajax.php?module=ajax&categ=chklnk&timeout=!!pmb_curl_timeout!!&link='+testlink+'&csrf_token='+csrf_token, true);
        req.onreadystatechange = function (aEvt) {
            if (req.readyState == 4) {
                if(req.status == 200){
                    var img = document.createElement('img');
                    var src='';
                    var type_status=req.responseText.substr(0,1);
                    if(type_status == '2' || type_status == '3'){
                        if((element.value.substr(0,7) != 'http://') && (element.value.substr(0,8) != 'https://')) element.value = 'http://'+element.value;
                        src = '".get_url_icon('tick.gif')."';
                    }else{
                        src = '".get_url_icon('error.png')."';
                        img.setAttribute('style','height:1.5em;');
                    }
                    img.setAttribute('src',src);
                    img.setAttribute('align','top');
                    while(document.getElementById(baseNodeId + '_lien_check').firstChild){
						document.getElementById(baseNodeId + '_lien_check').removeChild(document.getElementById(baseNodeId + '_lien_check').firstChild);
                    }
                    document.getElementById(baseNodeId + '_lien_check').appendChild(img);
                }
            } else {
				var img = document.createElement('img');
				var src='';
				//problème...
				src = '".get_url_icon('error.png')."';
				img.setAttribute('style','height:1.5em;');
				img.setAttribute('src',src);
				img.setAttribute('align','top');
				while(document.getElementById(baseNodeId + '_lien_check').firstChild){
					document.getElementById(baseNodeId + '_lien_check').removeChild(document.getElementById(baseNodeId + '_lien_check').firstChild);
				}
				document.getElementById(baseNodeId + '_lien_check').appendChild(img);
			}
        }
        req.send(null);
    }
}
</script>";

$ontology_tpl['form_row_content_resource_selector_hidden']='
<div class="row" id="!!onto_row_id!!">
	<input type="hidden" value="!!onto_row_content_hidden_display_label!!" name="!!onto_row_id!![!!onto_row_order!!][display_label]" id="!!onto_row_id!!_!!onto_row_order!!_display_label"/>
	<input type="hidden" value="!!onto_row_content_hidden_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
	<input type="hidden" value="!!onto_row_content_hidden_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
</div>
';

// Champ obligatoire
$ontology_tpl['form_row_content_mandatory_sign'] = '
<span class="contribution_mandatory_fields" title="'.$msg['is_required'].'">*</span>';

$ontology_tpl['form_row_content_input_json_data']='
<input type="hidden" id="!!onto_row_id!!_!!onto_row_order!!_json_data" value="!!onto_json_data!!"/>
';
