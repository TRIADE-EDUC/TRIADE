<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_ui.tpl.php,v 1.82.2.2.2.1 2025/02/06 10:53:23 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

/*
 * Common
 */
$ontology_tpl['form_row'] = '
<div id="!!onto_row_id!!"  data-pmb-uniqueId="!!data_pmb_uniqueid!!">
	<div class="row" title="!!form_row_label_tooltip!!">	
		<hr />
		<label class="etiquette !!form_row_content_mandatory_class!!" for="!!onto_row_id!!">
            !!onto_row_label!! !!form_row_content_mandatory_sign!! 
        </label>
		!!form_row_content_comment!! !!form_row_content_tooltip!!
	</div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!" autocomplete="off" />
	!!onto_rows!!
</div>
<script>
	!!onto_row_scripts!!
</script>
';

$ontology_tpl['form_row_content']='
<div class="row contribution_area_flex" id="!!onto_row_id!!_!!onto_row_order!!">
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
<input type="button" class="bouton" id="!!onto_row_id!!_add" data-element-name = "!!onto_row_id!!"  data-element-order="0" class="bouton" value="'.$msg['ontology_p_add_button'].'" />
';

$ontology_tpl['form_row_content_input_add_merge_property']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" id="!!onto_row_id!!_add_merge_property" data-dojo-props="elementName : \'!!onto_row_id!!\', elementOrder : \'0\'">'.$msg['ontology_p_add_button'].'</button>
<script>
	var !!onto_row_id!!_template = "!!merge_properties_template!!";
</script>
';

$ontology_tpl['form_row_content_input_add_resource_selector']='
<input type="button" class="bouton" id="!!onto_row_id!!_add_resource_selector" data-element-name = "!!onto_row_id!!"  data-element-order="0" class="bouton" value="'.$msg['ontology_p_add_button'].'" />
';

$ontology_tpl['form_row_content_input_add_responsability_selector']='
<input type="button" class="bouton" id="!!onto_row_id!!_add_responsability_selector" data-element-name = "!!onto_row_id!!"  data-element-order="0" class="bouton" value="'.$msg['ontology_p_add_button'].'" />
';

$ontology_tpl['form_row_content_input_add_item_creator']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" id="!!onto_row_id!!_add_item_creator" data-dojo-props="elementName : \'!!onto_row_id!!\', elementOrder : \'0\' " >'.$msg['ontology_p_add_button'].'</button>
';

$ontology_tpl['form_row_content_input_add_linked_record']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" id="!!onto_row_id!!_add_linked_record" data-dojo-props="elementName : \'!!onto_row_id!!\', elementOrder : \'0\' " >'.$msg['ontology_p_add_button'].'</button>
';

$ontology_tpl['form_row_content_input_add_linked_authority']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" id="!!onto_row_id!!_add_linked_authority" data-dojo-props="elementName : \'!!onto_row_id!!\', elementOrder : \'0\' " >'.$msg['ontology_p_add_button'].'</button>
';

$ontology_tpl['form_row_content_input_del']='
<button type="button" data-dojo-type="dijit/form/Button" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_del(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">'.$msg['ontology_p_del_button'].'</button>
';

// Info-bulle
$ontology_tpl['form_row_content_tooltip'] = '
<i id="!!onto_row_id!!_tooltip" class="contribution_tooltip fa fa-info-circle"  style="cursor:help;" aria-hidden="true" title="!!form_row_content_tooltip_content!!"></i>
';

// Champ obligatoire
$ontology_tpl['form_row_content_mandatory_sign'] = '
<span class="contribution_mandatory_fields" title="'.$msg['onto_contribution_mandatory_field'].'">*</span>';

/*
 * Text
 */
$ontology_tpl['form_row_content_text']='
<textarea cols="80" rows="4" wrap="virtual" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" !!onto_input_props!!>!!onto_row_content_text_value!!</textarea>
';


/*
 * Small text
 */
$ontology_tpl['form_row_content_small_text']='
<input type="text" class="form_row_content_small_text" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" !!onto_input_props!!/>
!!onto_row_combobox_lang!!
';

/*
 * Small text card
 */
$ontology_tpl['form_row_card'] = '
<div id="!!onto_row_id!!" data-pmb-uniqueId="!!data_pmb_uniqueid!!">
	<div class="row" title="!!form_row_label_tooltip!!">
		<label class="etiquette !!form_row_content_mandatory_class!!" for="!!onto_row_id!!">!!onto_row_label!! !!form_row_content_mandatory_sign!!</label>
		!!form_row_content_comment!! !!form_row_content_tooltip!!
	</div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	<input type="hidden" id="!!onto_row_id!!_input_type" value="!!onto_input_type!!"/>
	!!onto_rows!!
</div>
<script>
	!!onto_row_scripts!!
</script>
';

$ontology_tpl['form_row_content_small_text_card']='
<input type="text" class="form_row_content_small_text_card" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" !!onto_input_props!!/>
!!onto_row_combobox_lang!!
';

$ontology_tpl['form_row_content_input_add_card']='
<input class="bouton_small" id="!!onto_row_id!!_add_card" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_card(\'!!onto_row_id!!\',!!onto_row_max_card!!);ajax_parse_dom();">
';

/*
 * Ressource selector
 */
$ontology_tpl['form_row_content_resource_selector']='
<input type="text" class="form_row_content_resource_selector" list="!!onto_row_id!!_!!onto_row_order!!_display_label_list" value="!!form_row_content_resource_selector_display_label!!"
    data-dojo-type="apps/pmb/contribution/datatypes/ResourceSelector" 
    data-dojo-props="
        name:\'!!onto_row_id!![!!onto_row_order!!][display_label]\', 
        id:\'!!onto_row_id!!_!!onto_row_order!!_display_label\', 
        baseId:\'!!onto_row_id!!_!!onto_row_order!!\', 
        completion:\'!!onto_completion!!\', 
        autexclude:\'!!onto_current_element!!\', 
        param1:\'!!onto_equation_query!!\', 
        param2:\'!!onto_area_id!!\', 
        value:\'!!form_row_content_resource_selector_display_label!!\', 
        valueNodeId:\'!!onto_row_id!!_!!onto_row_order!!_value\', 
        isDraft:\'!!form_row_content_item_creator_is_draft!!\', 
        isDraftNodeId:\'!!onto_row_id!!_!!onto_row_order!!_is_draft\', 
        templateNodeId:\'!!onto_row_id!!_!!onto_row_order!!_resource_template\'" 
    !!onto_disabled!! autocomplete="off" att_id_filter="!!onto_current_range!!"/>
<datalist id="!!onto_row_id!!_!!onto_row_order!!_display_label_list">
</datalist>
<input type="hidden" value="!!form_row_content_resource_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden" value="!!form_row_content_resource_selector_is_draft!!" name="!!onto_row_id!![!!onto_row_order!!][is_draft]" id="!!onto_row_id!!_!!onto_row_order!!_is_draft"/>
';

/*
 * Item creator
 */
$ontology_tpl['form_row_content_item_creator']='
<input type="text" class="form_row_content_item_creator" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"  value="!!form_row_content_item_creator_display_label!!" autocomplete="off" readonly/>
<input type="hidden" value="!!form_row_content_item_creator_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden" value="!!form_row_content_item_creator_is_draft!!" name="!!onto_row_id!![!!onto_row_order!!][is_draft]" id="!!onto_row_id!!_!!onto_row_order!!_is_draft"/>
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
<input type="button" id="!!onto_row_id!!_!!onto_new_order!!_sel" class="bouton" data-form_url="!!url_linked_form!!" data-form_property="!!linked_tab_title!!" data-edit_label='.$msg['ontology_p_edit_button'].' value="'.$msg['ontology_p_sel_button'].'" data-linked_scenario="!!linked_scenario!!"/>
';

$ontology_tpl['form_row_content_hidden_linked_form']='
<input type="hidden" id="!!onto_row_id!!_!!onto_new_order!!_sel" class="bouton" data-form_url="!!url_linked_form!!" data-form_property="!!linked_tab_title!!" data-edit_label='.$msg['ontology_p_edit_button'].' value="'.$msg['ontology_p_sel_button'].'" data-linked_scenario="!!linked_scenario!!"/>
';

$ontology_tpl['form_row_content_search']='
<input type="button" id="!!onto_row_id!!_!!onto_new_order!!_search" class="bouton" data-form_url="!!url_search_form!!"  data-form_property="!!linked_tab_title!!" data-edit_label='.$msg['ontology_p_edit_button'].' value="'.$msg['ontology_p_find_button'].'" data-linked_scenario="!!linked_scenario!!" />
';

$ontology_tpl['form_row_content_edit']='
<input type="button" id="!!onto_row_id!!_!!onto_new_order!!_edit" class="bouton" data-form_url="!!url_edit_form!!"  data-form_property="!!linked_tab_title!!" data-edit_label='.$msg['ontology_p_edit_button'].' value="'.$msg['ontology_p_edit_button'].'"  data-linked_scenario="!!linked_scenario!!"/>
';

$ontology_tpl['form_row_content_edit_hidden']='
<input type="hidden" id="!!onto_row_id!!_!!onto_new_order!!_edit" class="bouton" data-form_url=""  data-form_property="!!linked_tab_title!!" data-edit_label='.$msg['ontology_p_edit_button'].' value="'.$msg['ontology_p_edit_button'].'"  data-linked_scenario="!!linked_scenario!!"/>
';

$ontology_tpl['form_row_content_input_remove']='
<input type="button" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_remove_selector_value(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton" value="'.$msg['ontology_p_del_button'].'"/>
';

$ontology_tpl['form_row_content_input_remove_hidden']='
<input type="hidden" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_remove_selector_value(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton" value="'.$msg['ontology_p_del_button'].'"/>
';

/*
 * checkbox
 */
$ontology_tpl['form_row_content_checkbox']='
<input type="checkbox" class="saisie-80em" !!onto_row_content_checkbox_checked!! value="1" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" !!onto_input_props!!/>
';

/*
 * date & dojo widget en général (supp & add) 
*/
$ontology_tpl['form_row_content_date']='
<input type="date" id="!!onto_row_id!!_!!onto_row_order!!_value" name="!!onto_row_id!![!!onto_row_order!!][value]" value="!!onto_date!!" !!onto_input_props!!/>';

$ontology_tpl['form_row_content_widget_add']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_dojo_element(\'!!onto_row_id!!\',0);">
';

/**
 * Bouton suppression widget dojo
 */
$ontology_tpl['form_row_content_widget_del']='
<button type="button" data-dojo-type="dijit/form/Button" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_remove_dojo_element(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">'.$msg['ontology_p_del_button'].'</button>
';

/** 
 * Représentation d'un entier 
 */
$ontology_tpl['form_row_content_integer']='
<input type="text" class="saisie-80em" value="!!onto_row_content_integer_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" !!onto_input_props!!/>
';

/**
 * Représentation d'un marclist
 */
$ontology_tpl['form_row_content_marclist']='
<select name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" class="form_row_content_marclist" !!onto_disabled!!>
    !!onto_row_content_marclist_options!!
</select>';

/*
 * Liste
 */
$ontology_tpl['form_row_content_list']='
<select 
	name="!!onto_row_id!![!!onto_row_order!!][value][]"
	id="!!onto_row_id!!_!!onto_row_order!!_value"
	!!onto_disabled!!
>
	!!onto_row_content_list_options!!
</select>
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
<div id="!!onto_row_id!!" data-pmb-uniqueId="!!data_pmb_uniqueid!!">
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!" />
	!!onto_rows!!
</div>
<script>
	!!onto_row_scripts!!
</script>
';

$ontology_tpl['form_row_content_hidden']='
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
	<input type="hidden" value="!!onto_row_content_hidden_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
	<input type="hidden" value="!!onto_row_content_hidden_lang!!" name="!!onto_row_id!![!!onto_row_order!!][lang]" id="!!onto_row_id!!_!!onto_row_order!!_lang"/>
	<input type="hidden" value="!!onto_row_content_hidden_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
</div>
';

//pour le sélecteur de ressource, on a besoin du champ display_label
$ontology_tpl['form_row_content_resource_selector_hidden']='
<div class="row" id="!!onto_row_id!!" data-pmb-uniqueId="!!data_pmb_uniqueid!!">
	<input type="hidden" value="!!onto_row_content_hidden_display_label!!" name="!!onto_row_id!![!!onto_row_order!!][display_label]" id="!!onto_row_id!!_!!onto_row_order!!_display_label"/>
	<input type="hidden" value="!!onto_row_content_hidden_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
	<input type="hidden" value="!!onto_row_content_hidden_is_draft!!" name="!!onto_row_id!![!!onto_row_order!!][is_draft]" id="!!onto_row_id!!_!!onto_row_order!!_is_draft"/>
	<input type="hidden" value="!!onto_row_content_hidden_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
	<input type="hidden" value="!!onto_row_content_hidden_assertions!!" name="!!onto_row_id!![!!onto_row_order!!][assertions]" id="!!onto_row_id!!_!!onto_row_order!!_assertions"/>
</div>
';

//Pour le champ caché de type liste, on a besoin d'un tableau de values 
$ontology_tpl['form_row_content_list_hidden']='
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
	!!form_row_content_list_item_hidden!!
	<input type="hidden" value="!!onto_row_content_hidden_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
	<input type="hidden" value="!!form_row_content_list_hidden_values!!" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
</div>
';

$ontology_tpl['form_row_content_list_item_hidden']='
	<input type="hidden" id="!!onto_row_id!!_!!onto_row_order!!_value_list" value="!!onto_row_content_hidden_value!!" name="!!onto_row_id!![!!onto_row_order!!][value][]"/>
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
    <input type="hidden"  value="!!onto_row_content_file_value!!" name="!!onto_row_id!![!!onto_row_order!!][default_value]" id="!!onto_row_id!!_!!onto_row_order!!_default_value"/>
    <input type="hidden"  value="!!onto_row_content_file_data!!" name="!!onto_row_id!![!!onto_row_order!!][data]" id="!!onto_row_id!!_!!onto_row_order!!_data"/>
    <script>
    		var form = document.forms["!!onto_form_name!!"];
    		if (form.getAttribute("enctype") != "multipart/form-data") {
    			form.setAttribute("enctype","multipart/form-data");
    		}
    </script> 
</div>
';
/**
 * last file
 */
$ontology_tpl['form_row_content_last_file']='
<label id="!!onto_row_id!!_value_label">'.$msg["onto_contribution_last_file"].' : <em>!!onto_row_content_file_value!!</em></label>
<br/>
';

/*
* Merge properties
*/
$ontology_tpl['form_row_merge_properties'] = '
<div id="!!onto_row_id!!" data-pmb-uniqueId="!!data_pmb_uniqueid!!">
	<div class="row">
		<label class="etiquette">!!onto_row_label!!</label>
		<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	</div>
	!!onto_rows!!
</div>
';


/**
 * champ caché pour le type
 */
$ontology_tpl['form_row_content_type'] = '
	<input type="hidden" value="!!onto_row_content_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * Responsability selector
 */
$ontology_tpl['form_row_content_responsability_selector']='
<input type="text" value="!!form_row_content_responsability_selector_display_label!!" list="!!onto_row_id!!_!!onto_row_order!!_display_label_list" 
    data-dojo-type="apps/pmb/contribution/datatypes/ResourceSelector" 
    data-dojo-props="
        name:\'!!onto_row_id!![!!onto_row_order!!][display_label]\', 
        id:\'!!onto_row_id!!_!!onto_row_order!!_display_label\',
        baseId:\'!!onto_row_id!!_!!onto_row_order!!\',  
        completion:\'!!onto_completion!!\', 
        autexclude:\'!!onto_current_element!!\', 
        param1:\'!!onto_equation_query!!\', 
        param2:\'!!onto_area_id!!\',
        value:\'!!form_row_content_responsability_selector_display_label!!\', 
        valueNodeId:\'!!onto_row_id!!_!!onto_row_order!!_value\',
        isDraft:\'!!form_row_content_responsability_selector_is_draft!!\', 
        isDraftNodeId:\'!!onto_row_id!!_!!onto_row_order!!_is_draft\', 
        templateNodeId:\'!!onto_row_id!!_!!onto_row_order!!_resource_template\'" 
    !!onto_disabled!! autocomplete="off" placeholder="'.$msg['contribution_placeholder_responsability'].'" title="'.$msg['contribution_placeholder_responsability'].'"/>
<datalist id="!!onto_row_id!!_!!onto_row_order!!_display_label_list"></datalist>
!!template_responsability_function_value!!
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][function_type]" id="!!onto_row_id!!_!!onto_row_order!!_function_type"/>
<input type="hidden" value="!!form_row_content_responsability_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" class="is_draft" value="!!form_row_content_responsability_selector_is_draft!!" name="!!onto_row_id!![!!onto_row_order!!][is_draft]" id="!!onto_row_id!!_!!onto_row_order!!_is_draft">
<input type="hidden" value="!!form_row_content_responsability_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/**
 * Responsability selector function Author
 */
$ontology_tpl['responsability_selector_function_value']='
<select name="!!onto_row_id!![!!onto_row_order!!][function_value]" id="!!onto_row_id!!_!!onto_row_order!!_function_value" title="'.$msg['contribution_placeholder_responsability_function'].'">
	!!onto_row_content_marclist_options!!
</select>';

/**
 * Responsability autocomplete input function Author
 */
$ontology_tpl['responsability_autocomplete_function_value']='
<input type="text" value="!!form_row_content_responsability_selector_function_label!!" list="!!onto_row_id!!_!!onto_row_order!!_function_label_list" 
    data-dojo-type="apps/pmb/contribution/datatypes/ResourceSelector" 
    data-dojo-props="
        name:\'!!onto_row_id!![!!onto_row_order!!][function_label]\', 
        id:\'!!onto_row_id!!_!!onto_row_order!!_function_label\',
        baseId:\'!!onto_row_id!!_!!onto_row_order!!\',  
        completion:\'fonction\', 
        autexclude:\'\', 
        param1:\'!!limited_function!!\', 
        param2:\'\',
        value:\'!!form_row_content_responsability_selector_function_label!!\', 
        valueNodeId:\'!!onto_row_id!!_!!onto_row_order!!_function_value\'"
    autocomplete="off" placeholder="'.$msg['contribution_placeholder_responsability_function'].'" title="'.$msg['contribution_placeholder_responsability_function'].'"/>
<datalist id="!!onto_row_id!!_!!onto_row_order!!_function_label_list"></datalist>
<input type="hidden" value="!!form_row_content_responsability_selector_function_value!!" name="!!onto_row_id!![!!onto_row_order!!][function_value]" id="!!onto_row_id!!_!!onto_row_order!!_function_value">
';

/*
 * linked record selector
 */
$ontology_tpl['form_row_content_linked_record_selector']='
!!onto_row_content_linked_record_selector!!
<input type="text" value="!!form_row_content_linked_record_selector_display_label!!" list="!!onto_row_id!!_!!onto_row_order!!_display_label_list" 
    data-dojo-type="apps/pmb/contribution/datatypes/ResourceSelector" 
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
<input type="checkbox" title="'.$msg['link_reciproque_title'].'" class="add_reverse_link" value="1" name="!!onto_row_id!![!!onto_row_order!!][assertions][add_reverse_link]" id="!!onto_row_id!!_!!onto_row_order!!_assertions_add_reverse_link" !!form_row_content_linked_record_selector_add_reverse_link_checked!!>
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][marclist_type]" id="!!onto_row_id!!_!!onto_row_order!!_marclist_type"/>
<input type="hidden" value="!!form_row_content_linked_record_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_linked_record_selector_is_draft!!" name="!!onto_row_id!![!!onto_row_order!!][is_draft]" id="!!onto_row_id!!_!!onto_row_order!!_is_draft">
<input type="hidden" value="!!form_row_content_linked_record_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
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
        <img class="img_plus" src="'.get_url_icon('plus.gif').'" id="!!onto_row_id!!_!!onto_row_order!!_img_plus" data-prefix="!!onto_row_id!!_!!onto_row_order!!"
        	onclick="show_comment_area(this.dataset.prefix);"/>		
        <input type="text" value="!!form_row_content_linked_authority_selector_display_label!!" list="!!onto_row_id!!_!!onto_row_order!!_display_label_list" 
            data-dojo-type="apps/pmb/contribution/datatypes/ResourceSelector" 
            data-dojo-props="
                name:\'!!onto_row_id!![!!onto_row_order!!][display_label]\', 
                id:\'!!onto_row_id!!_!!onto_row_order!!_display_label\', 
                baseId:\'!!onto_row_id!!_!!onto_row_order!!\', 
                completion:\'!!onto_completion!!\', 
                autexclude:\'!!onto_current_element!!\', 
                param1:\'!!onto_equation_query!!\', 
                param2:\'!!onto_area_id!!\', 
                value:\'!!form_row_content_linked_authority_selector_display_label!!\', 
                valueNodeId:\'!!onto_row_id!!_!!onto_row_order!!_value\',
                isDraft:\'!!form_row_content_linked_record_selector_is_draft!!\', 
                isDraftNodeId:\'!!onto_row_id!!_!!onto_row_order!!_is_draft\', 
                templateNodeId:\'!!onto_row_id!!_!!onto_row_order!!_resource_template\'" 
            !!onto_disabled!! autocomplete="off"/>
        <datalist id="!!onto_row_id!!_!!onto_row_order!!_display_label_list"></datalist>
        <input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][marclist_type]" id="!!onto_row_id!!_!!onto_row_order!!_marclist_type"/>
        <input type="hidden" value="!!form_row_content_linked_authority_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
        <input type="hidden" value="!!form_row_content_linked_authority_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
		<input type="hidden" value="!!form_row_content_linked_authority_selector_is_draft!!" name="!!onto_row_id!![!!onto_row_order!!][is_draft]" id="!!onto_row_id!!_!!onto_row_order!!_is_draft">
        <!-- c est pas top de mettre les boutons direct ici mais sinon on est trop embete avec le display flex (pour aligner tous les champs)-->
    	!!onto_row_inputs!!
    </div>
    <div class="row" id="!!onto_row_id!!_!!onto_row_order!!_comment_area" style="display:none;">
        <div class="row">
            <label>'.$msg["653"].'</label>
        </div>
        <div class="row contribution_area_flex">
            '.$msg["parperso_option_duration_begin"].' <input type="text" placeholder="JJ/MM/AAAA" size="11" id="!!onto_row_id!!_!!onto_row_order!!_start_date" name="!!onto_row_id!![!!onto_row_order!!][start_date]" value="!!form_row_content_linked_authority_selector_start_date!!">
            '.$msg["parperso_option_duration_end"].' <input type="text" placeholder="JJ/MM/AAAA" size="11" id="!!onto_row_id!!_!!onto_row_order!!_end_date" name="!!onto_row_id!![!!onto_row_order!!][end_date]" value="!!form_row_content_linked_authority_selector_end_date!!">
        </div>        
        <div class="row">
            <label>'.$msg["comment"].'</label><br>
    	</div>
        <div class="row">
            <textarea class="saisie-80em aut_link_comment" name="!!onto_row_id!![!!onto_row_order!!][comment]" id="!!onto_row_id!!_!!onto_row_order!!_comment" cols="62" rows="2" >!!form_row_content_linked_authority_selector_comment!!</textarea>	
        </div>
    </div>
</div>
';


$ontology_tpl['form_row_content_linked_authority_selector_script'] = '
    function onchange_aut_link_contrib_selector(prefix) {
        var aut_link_display_label = dijit.registry.byId(prefix + "_display_label");
        var aut_link_value = document.getElementById(prefix + "_value");
        var aut_link_datalist = document.getElementById(prefix + "_display_label_list");
        aut_link_display_label.domNode.value = "";
        aut_link_value.value = "";
        aut_link_datalist.innerHTML = "";

        var selector = document.getElementById(prefix + "_authority_type");
        var selIndex = selector.selectedIndex;
        var table = selector.options[selIndex].value;
        var table_name = "authors";

        switch (table) {
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
				table_name = "titres_uniformes";
                break;
			case "8" :
				table_name = "indexint";
                break;
			case "10" :
				table_name = "onto";
                f_aut_link_libelle.setAttribute("att_id_filter", "http://www.w3.org/2004/02/skos/core#Concept");
                break;
            default : 
                if (table > 1000) {
				    table_name = "authperso_" + (table - 1000);
                }
                break;
		}	
        aut_link_display_label.completion = table_name;
        show_add_btn(prefix);
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

    function show_add_btn(prefix) {
        var selector = document.getElementById(prefix + "_authority_type");
        var selIndex = selector.selectedIndex;
        var table = selector.options[selIndex].value;
        
        var selectType = "author";

        switch (table) {
			case "1" :
                selectType = "author";
                break;
			case "2" :
                selectType = "category";
                break;
			case "3" :
                selectType = "publisher";
                break;
			case "4" :
                selectType = "collection";
                break;
			case "5" :
                selectType = "subcollection";
                break;
			case "6" :
                selectType = "serie";
                break;
			case "7" :
                selectType = "work";
                break;
			case "8" :
                selectType = "indexint";
                break;
			case "10" :
                selectType = "concept";
                break;
            default : 
                if (table > 1000) {
                    selectType = "authperso_" + (table - 1000);
                }
                break;
		}	
        var btnAdd = document.getElementById(prefix + "_sel");
        var btnSearch = document.getElementById(prefix + "_search");
        var jsonDataNode = document.getElementById(prefix + "_json_data");
        var formEditUrl = "";

        var displayBtn = "none";
        if (jsonDataNode && jsonDataNode.value) {
            var jsonData = JSON.parse(jsonDataNode.value);
            jsonData.create = 0;
            jsonData.multiple_scenarios = 0;
            if (jsonData.sub_form_data[selectType]) {
                displayBtn = "block";
                jsonData.create = 1;
                jsonData.multiple_scenarios = jsonData.sub_form_data[selectType].multiple;
            }
            jsonData.type = selectType;
            jsonData.select_tab = 1;
            formEditUrl = "./select.php?what=contribution&selector_data="+encodeURIComponent(JSON.stringify(jsonData));
            jsonData.select_tab = 0;
            var formSearchUrl = "./select.php?what=contribution&selector_data="+encodeURIComponent(JSON.stringify(jsonData));
            
            btnSearch.setAttribute("data-form_url", formSearchUrl);
        }
        if (btnAdd) {
            btnAdd.setAttribute("data-form_url", formEditUrl);
            btnAdd.style.display = displayBtn;
        }
    }

    function show_add_buttons() {
        var maxOrder = document.getElementById("!!onto_row_id!!_new_order");
        if (maxOrder) {
            for(var i = 0; i <= maxOrder.value; i++) {
                show_add_btn("!!onto_row_id!!_"+i);
            }
        }
    }

    show_add_buttons();
';

/*
 * resource template
 */
$ontology_tpl['form_row_content_resource_template'] = '
<div id="!!onto_row_id!!_!!onto_row_order!!_resource_template" class="contribution_resource_template"></div>
';

/*
 * script commun de champs calculés
 */
$ontology_tpl['form_row_common_field_change_script'] = '
	if (typeof !!onto_row_id!!_already_parsed == "undefined") {
		var !!onto_row_id!!_already_parsed = true;
		require(["dojo/on", "dojo/topic", "dojo/dom", "dojo/ready"], function(on, topic, dom, ready) {
			ready(function() {
				var valueInput = dom.byId("!!onto_row_id!!_0_value");
				var displayLabelInput = dom.byId("!!onto_row_id!!_0_display_label");
				if (valueInput) {
					on(valueInput, "change", function() {
						topic.publish("form/change", "!!data_pmb_uniqueid!!");
					});
				}
				if (displayLabelInput) {
					on(displayLabelInput, "change", function() {
						topic.publish("form/change", "!!data_pmb_uniqueid!!");
					});
				}
	
				topic.subscribe("form/getValues", function(uniqueId, deferred) {
					if (uniqueId == "!!data_pmb_uniqueid!!") {
						var params = {uniqueId: "!!data_pmb_uniqueid!!"};
						if (valueInput) {
							params.value = valueInput.value;
						}
						if (displayLabelInput) {
							params.displayLabel = displayLabelInput.value;
						}
						deferred.resolve(params);
					}
				});
			});
		});
	}
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
    if (window.NodeList && !NodeList.prototype.forEach) {
        NodeList.prototype.forEach = function(callback, thisArg) {
            thisArg = thisArg || window;
            for (let i = 0; i < this.length; i++) {
                callback.call(thisArg, this[i], i, this);
            }
        };
    }
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

$ontology_tpl['form_row_content_floating_date'] = "
					<select id='!!onto_row_id!!_!!onto_row_order!!_value' name='!!onto_row_id!![!!onto_row_order!!][value]' onchange=\"date_flottante_type_onchange('!!onto_row_id!!_!!onto_row_order!!');\">
 						!!select_floating_date_options!!
					</select>
 					<span id='!!onto_row_id!!_!!onto_row_order!!_date_begin_zone' class='span_floating_date'>
						<label id='!!onto_row_id!!_!!onto_row_order!!_date_begin_zone_label' for='!!onto_row_id!!_!!onto_row_order!!_date_begin'>" . $msg['parperso_option_duration_begin'] . "</label>
						<input type='text' id='!!onto_row_id!!_!!onto_row_order!!_date_begin' name='!!onto_row_id!![!!onto_row_order!!][date_begin]' value='!!floating_date_begin!!' placeholder='" . $msg["format_date_input_placeholder"] . "' maxlength='11' size='11' />
					</span>
 					<span id='!!onto_row_id!!_!!onto_row_order!!_date_end_zone' class='span_floating_date'>
						<label id='!!onto_row_id!!_!!onto_row_order!!_date_end_zone_label' for='!!onto_row_id!!_!!onto_row_order!!_date_end'>" . $msg['parperso_option_duration_end'] . "</label>
						<input type='text' id='!!onto_row_id!!_!!onto_row_order!!_date_end' name='!!onto_row_id!![!!onto_row_order!!][date_end]' value='!!floating_date_end!!' placeholder='" . $msg["format_date_input_placeholder"] . "' maxlength='11' size='11' />
					</span>
					<label>" . $msg['parperso_option_duration_comment'] . "</label>
					<input type='text' id='!!onto_row_id!!_!!onto_row_order!!_comment' name='!!onto_row_id!![!!onto_row_order!!][comment]' value='!!floating_date_comment!!' class='saisie-30em'/>
					<input class='bouton' type='button' value='X' onClick=\"date_flottante_reset_fields('!!onto_row_id!!_!!onto_row_order!!');\"/>
            <!--<input class='bouton' type='button' value='+' onclick='add_custom_date_flottante_()' >-->
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
<input type="button" class="bouton" id="!!onto_row_id!!_add_multilingual_qualified" data-element-name = "!!onto_row_id!!"  data-element-order="0" class="bouton" value="'.$msg['ontology_p_add_button'].'" />
';
/*
 * Multilingual qualified type Text
 */
$ontology_tpl['onto_row_content_multilingual_qualified_text']="
<input type='text' class='onto_row_content_multilingual_qualified_text' value='!!onto_row_content_values!!' 
        id='!!onto_row_id!!_!!onto_row_order!!_value' 
        name='!!onto_row_id!![!!onto_row_order!!][value]'
        maxlength='!!multilingual_qualified_maxlength!!' />
";

/*
 * Multilingual qualified type Text large
 */
$ontology_tpl['onto_row_content_multilingual_qualified_textarea']="
<textarea class='onto_row_content_multilingual_qualified_textarea' id='!!onto_row_id!!_!!onto_row_order!!_value' name='!!onto_row_id!![!!onto_row_order!!][value]' maxlength='!!multilingual_qualified_maxlength!!'>!!onto_row_content_values!!</textarea>";

/*
 * Button save draft contribution
 */
$ontology_tpl['onto_contribution_save_button_draft']='

<input type="button" id="save_button_draft" class="bouton" onclick="event_save_draft(!!sub_params!!)" title="'.htmlentities($msg['onto_contribution_save_title'],ENT_QUOTES,$charset).'" value="'.htmlentities($msg['contribution_save_button_draft'],ENT_QUOTES,$charset).'"/>';


/*
 * Small text link
 */
$ontology_tpl['form_row_link'] = '
<div id="!!onto_row_id!!">
	<div class="row" title="!!form_row_label_tooltip!!">
        <label class="etiquette !!form_row_content_mandatory_class!!" for="!!onto_row_id!!">
            !!onto_row_label!!  !!form_row_content_mandatory_sign!!
        </label>
        !!form_row_content_comment!! !!form_row_content_tooltip!!
    </div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	<input type="hidden" id="!!onto_row_id!!_input_type" value="!!onto_input_type!!">
	!!onto_rows!!
</div>
';


$ontology_tpl['form_row_content_input_add_text_link']='
<input type="button" class="bouton" id="!!onto_row_id!!_add_text_link" data-element-name = "!!onto_row_id!!"  data-element-order="0" class="bouton" value="'.$msg['ontology_p_add_button'].'" />
';


$ontology_tpl['form_row_content_small_text_link']='
<div id="!!onto_row_id!!_!!onto_row_order!!_lien_check" style="display:inline"></div>
<input type="text" class="saisie-80em" onchange="check_link(this);"  value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
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
const tabTokens_common_onto_!!csrf_token_id!! = !!tokens_csrf!!;
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
        document.getElementById(baseNodeId + '_lien_check').appendChild(wait);
		var csrf_token = tabTokens_common_onto_!!csrf_token_id!![0];
		tabTokens_common_onto_!!csrf_token_id!!.splice(0, 1);
        var testlink = encodeURIComponent(element.value);
        var req = new XMLHttpRequest();
        req.open('GET', './ajax.php?module=ajax&categ=chklnk&timeout=!!pmb_curl_timeout!!&link='+encodeURI(testlink)+'&csrf_token='+csrf_token, true);
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
        }
        req.send(null);
    }
}
</script>";

$ontology_tpl['form_row_content_input_json_data']='
<input type="hidden" id="!!onto_row_id!!_!!onto_row_order!!_json_data" value="!!onto_json_data!!"/>
';

