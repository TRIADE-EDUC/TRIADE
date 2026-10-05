<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_ui.tpl.php,v 1.20 2021/08/25 14:05:30 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_contribution_tpl,$msg,$base_path;

/*
 * Common
 */
$ontology_contribution_tpl['form_row'] = '
<div id="!!onto_row_id!!">
	<div class="row">
		<label class="etiquette !!form_row_content_mandatory_class!!" for="!!onto_row_id!!">!!onto_row_label!! !!form_row_content_mandatory_sign!!</label>
	</div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	!!onto_rows!!
</div>
';

$ontology_contribution_tpl['form_row_content']='
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!">
	!!onto_inside_row!!
	!!onto_row_inputs!!
</div>
';

$ontology_contribution_tpl['form_row_content_input_add']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add(\'!!onto_row_id!!\',0);">
';

$ontology_contribution_tpl['form_row_content_input_add_selector']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_select(\'!!onto_row_id!!\',0);">
';

$ontology_contribution_tpl['form_row_content_input_add_ressource_selector']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_selector(\'!!onto_row_id!!\',0);">
';

$ontology_contribution_tpl['form_row_content_input_add_linked_record']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_selector(\'!!onto_row_id!!\',0);">
';

$ontology_contribution_tpl['form_row_content_input_del']='
<input type="button" value="'.$msg['ontology_p_del_button'].'" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_del(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

/*
 * Text
 */
$ontology_contribution_tpl['form_row_content_text']='
<textarea cols="80" rows="4" wrap="virtual" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">!!onto_row_content_text_value!!</textarea>
!!onto_row_combobox_lang!!
<input type="hidden" value="!!onto_row_content_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';


/*
 * Small text
 */
$ontology_contribution_tpl['form_row_content_small_text']='
<input type="text" class="saisie-80em" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * Small text card
 */
$ontology_contribution_tpl['form_row_card'] = '
<div id="!!onto_row_id!!">
	<div class="row"><label class="etiquette !!form_row_content_mandatory_class!!" for="!!onto_row_id!!">!!onto_row_label!! !!form_row_content_mandatory_sign!!</label></div>
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	<input type="hidden" id="!!onto_row_id!!_input_type" value="!!onto_input_type!!">
	!!onto_rows!!
</div>
';

$ontology_contribution_tpl['form_row_content_small_text_card']='
<input type="text" class="saisie-80em" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
!!onto_row_combobox_lang!!
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

$ontology_contribution_tpl['form_row_content_input_del_card']='
<input type="button" value="'.$msg['ontology_p_del_button'].'" id="!!onto_row_id!!_!!onto_row_order!!_del_card" onclick="onto_del_card(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

$ontology_contribution_tpl['form_row_content_input_add_card']='
<input class="bouton_small" id="!!onto_row_id!!_add_card" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_card(\'!!onto_row_id!!\',!!onto_row_max_card!!);ajax_parse_dom();">
';

/*
 * Ressource selector
 */
$ontology_contribution_tpl['form_row_content_resource_selector']='
<input type="text" value="!!form_row_content_resource_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"
	completion="!!onto_completion!!"
	autexclude="!!onto_current_element!!"
	att_id_filter="!!onto_current_range!!"
    param1="!!onto_equation_query!!"
	autocomplete="off"
	 />
<input type="hidden" value="!!form_row_content_resource_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_resource_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

//on ajoute simplement des atibuts hidden pour cacher les champs
$ontology_contribution_tpl['form_row_content_resource_selector_no_search']='
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
$ontology_contribution_tpl['form_row_content_item_creator']='
<input type="text" value="!!form_row_content_item_creator_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]" autocomplete="off"/>
<input type="hidden" value="!!form_row_content_item_creator_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_item_creator_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

$ontology_contribution_tpl['form_row_content_input_sel']='
<input type="button" class="bouton_small" onclick="onto_open_selector(\'!!onto_row_id!!\',\'!!onto_selector_url!!\', \'!!onto_current_range!!\');" value="'.$msg['ontology_p_sel_button'].'" id="!!onto_row_id!!_sel" />
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

$ontology_contribution_tpl['form_row_content_linked_form']='
<button type="button" data-dojo-type="dijit/form/Button" class="bouton_small" data-form_url="!!url_linked_form!!" id="!!onto_row_id!!_sel" data-form_title="!!linked_form_title!!">'.$msg['ontology_p_sel_button'].'</button>
';

$ontology_contribution_tpl['form_row_content_input_remove']='
<input type="button" id="!!onto_row_id!!_!!onto_row_order!!_del" value="'.$msg['ontology_p_del_button'].'" onclick="onto_remove_selector_value(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

/*
 * checkbox
 */
$ontology_contribution_tpl['form_row_content_checkbox']='
<input type="checkbox" class="saisie-80em" !!onto_row_content_checkbox_checked!! value="1" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';


/*
 * date & dojo widget en général (supp & add)
 */
$ontology_contribution_tpl['form_row_content_date']='
		<input type="date" id="!!onto_row_id!!_!!onto_row_order!!_value" name="!!onto_row_id!![!!onto_row_order!!][value]" value="!!onto_date!!" />
		<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>';

$ontology_contribution_tpl['form_row_content_widget_add']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_dojo_element(\'!!onto_row_id!!\',0);">
';

/**
 * Bouton suppression widget dojo
 */
$ontology_contribution_tpl['form_row_content_widget_del']='
<input type="button" value="'.$msg['ontology_p_del_button'].'" id="!!onto_row_id!!_!!onto_row_order!!_del" onclick="onto_remove_dojo_element(\'!!onto_row_id!!\',!!onto_row_order!!);" class="bouton_small">
';

/**
 * Représentation d'un entier
 */
$ontology_contribution_tpl['form_row_content_integer']='
<input type="text" class="saisie-80em" value="!!onto_row_content_integer_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden" value="!!onto_row_content_integer_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/**
 * Représentation d'un marclist
 */
$ontology_contribution_tpl['form_row_content_marclist']='
<select name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
	!!onto_row_content_marclist_options!!
</select>
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>';



/*
 * Ressource selector multiple
 */
$ontology_contribution_tpl['form_row_content_resource_selector_record']='
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
$ontology_contribution_tpl['form_row_content_list']='
<select name="!!onto_row_id!![!!onto_row_order!!][value][]" id="!!onto_row_id!!_!!onto_row_order!!_value" !!onto_row_multiple!!>
	!!onto_row_content_list_options!!
</select>
<input type="hidden" value="!!onto_row_content_list_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/**
 * Upload directories
 */
$ontology_contribution_tpl['form_row_content_upload_directories'] = '
<input type="text" value="!!form_row_content_upload_directories_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]" readonly="readonly"/>
<input type="hidden" value="!!form_row_content_upload_directories_value!!" id="!!onto_row_id!!_!!onto_row_order!!_value" name="!!onto_row_id!![!!onto_row_order!!][value]"/>
<input type="hidden" value="http://www.w3.org/2000/01/rdf-schema#Literal" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<input class="bouton_small" type="button" id="!!onto_row_id!!_!!onto_row_order!!_button_dialog" value="...">
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
		"dojo/domReady!",
	], function (Memory, ObjectStoreModel, Tree, dom, registry, on, lang, Dialog) {
		var dialog = new Dialog({}, "!!onto_row_id!!_!!onto_row_order!!_dialog");
    
		on(dom.byId("!!onto_row_id!!_!!onto_row_order!!_button_dialog"), "click", function() {
			dialog.show();
		});
    
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

/*
 * Hidden field
 */
$ontology_contribution_tpl['form_row_hidden'] = '
<div id="!!onto_row_id!!">
	<input type="hidden" id="!!onto_row_id!!_new_order" value="!!onto_new_order!!"/>
	!!onto_rows!!
</div>
';

$ontology_contribution_tpl['form_row_content_hidden']='
<div id="!!onto_row_id!!_!!onto_row_order!!">
	<input type="hidden" value="!!onto_row_content_hidden_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
	<input type="hidden" value="!!onto_row_content_hidden_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
</div>
';

/**
 * champ caché pour le type
 */
$ontology_contribution_tpl['form_row_content_type'] = '
	<input type="hidden" value="!!onto_row_content_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type" data-dojo-type="dijit/form/TextBox"/>
';

/*
 * Responsability selector
 */
$ontology_contribution_tpl['form_row_content_responsability_selector']='
<input type="text" value="!!form_row_content_responsability_selector_display_label!!" class="saisie-80emr" id="!!onto_row_id!!_!!onto_row_order!!_display_label" name="!!onto_row_id!![!!onto_row_order!!][display_label]"
	autfield="!!onto_row_id!!_!!onto_row_order!!_value"
	completion="!!onto_completion!!"
	autexclude="!!onto_current_element!!"
	att_id_filter="!!onto_current_range!!"
	autocomplete="off"
	 />
<select name="!!onto_row_id!![!!onto_row_order!!][author_function]" id="!!onto_row_id!!_!!onto_row_order!!_assertions_author_function">
	!!onto_row_content_marclist_options!!
</select>
<input type="hidden" value="!!onto_row_content_marclist_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<input type="hidden" value="!!form_row_content_responsability_selector_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value">
<input type="hidden" value="!!form_row_content_responsability_selector_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

/*
 * Vedette selector
 */
$ontology_contribution_tpl['form_row_content_vedette'] = '
<div class="row">
	<img class="img_plus" hspace="3" border="0" onclick="expand_vedette(this,\'!!onto_row_id!!_!!onto_row_order!!_vedette_selector\'); return false;" title="'.$msg['plus_detail'].'" name="imEx" src="'.get_url_icon('plus.gif').'">
	<input type="text" class="saisie-30emr" readonly="readonly" name="!!onto_row_id!![!!onto_row_order!!][assertions][author_qualification][apercu_vedette]" id="!!onto_row_id!!_composed_!!onto_row_order!!_vedette_composee_apercu_autre" data-form-name="vedette_composee" value="!!vedette_value!!" />
	<input type="button" class="bouton" value="'.$msg['raz'].'" onclick="del_vedette(\'!!onto_row_id!!\', !!onto_row_order!!);" />
</div>
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!_vedette_selector" style="margin-bottom:6px;display:none">
	!!vedette_author!!
</div>
<script type="text/javascript" src="'.$javascript_path.'/vedette_composee_drag_n_drop.js"></script>
<script type="text/javascript">
	vedette_composee_update_all("!!onto_row_id!!_composed_!!onto_row_order!!_vedette_composee_subdivisions");
    function expand_vedette(el, what) {
		var obj = document.getElementById(what);
		if (obj.style.display == "none") {
			obj.style.display = "block";
	    	el.src = "'.get_url_icon('minus.gif').'";
			init_drag();
            ajax_resize_elements();
		} else {
			obj.style.display = "none";
	    	el.src = "'.get_url_icon('plus.gif').'";
		}
	}
	    	    
	function del_vedette(role, index) {
		vedette_composee_delete_all(role + "_composed_" + index + "_vedette_composee_subdivisions");
		init_drag();
	}
</script>
';

/*
 * regime de licence
 */
$ontology_contribution_tpl['form_row_content_licence'] = '
<div class="row contribution_area_flex">
    <select name="!!onto_row_id!![!!onto_row_order!!][licence][]" id="!!onto_row_id!!_!!onto_row_order!!_licence" !!onto_row_multiple!! onChange="showProfiles(this.value);">
    </select>
    <input type="hidden" value="!!onto_row_content_list_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
</div>
<div class="row" id="!!onto_row_id!!_!!onto_row_order!!_profil_selector" style="margin-bottom:6px;display:none">
</div>
<script type="text/javascript">
    var licenceValue = "!!form_row_content_licence_value!!";
	var licenceData = !!onto_row_licence_data!!
    var selector = document.getElementById("!!onto_row_id!!_!!onto_row_order!!_licence");
    selector.innerHTML = "";
    var defaultOption = document.createElement("option");
    defaultOption.text = "'.$msg["explnum_licence_empty_selector"].'";
    defaultOption.value = 0;
    selector.add(defaultOption);
    for (var id in licenceData) {
        var option = document.createElement("option");
        option.text = licenceData[id].label;
        option.value = id;
        if (licenceData[id].profiles[licenceValue]) {
            option.selected = true;
            showProfiles(id);
        }
        selector.add(option);
    }

    function showProfiles(id) {
        var container = document.getElementById("!!onto_row_id!!_!!onto_row_order!!_profil_selector");
        container.innerHTML = "";
        for (var profilId in licenceData[id].profiles) {
            var span = document.createElement("span");
            container.appendChild(span);
            var input = document.createElement("input");
            input.setAttribute("type", "radio");
            input.setAttribute("id", "!!onto_row_id!!_!!onto_row_order!!_value_" + profilId);
            input.setAttribute("name", "!!onto_row_id!![!!onto_row_order!!][value]");
            input.setAttribute("value", profilId);
            if (licenceValue == profilId) {
                input.setAttribute("checked", true);
            }
            span.appendChild(input);
            var label = document.createElement("label");
            label.setAttribute("for", "!!onto_row_id!!_!!onto_row_order!!_value_" + profilId);
            var img = document.createElement("img");
            img.setAttribute("src", licenceData[id].profiles[profilId].logo);
            img.setAttribute("alt", licenceData[id].profiles[profilId].label);
            img.setAttribute("title", licenceData[id].profiles[profilId].label);
            img.style.maxHeight = "30px";
            label.appendChild(img);
            span.appendChild(label);
        }
        container.style.display = "block";
    }
</script>
';

$ontology_contribution_tpl['list_script_template_tag'] = '
<script type="text/javascript">
    require([
        "apps/contribution_area_form/datatypes/TagsTemplates",
        "dojo/domReady!"
    ], function(TagsTemplates) {
        if (!window.datatypeTemplate) {
            window.datatypeTemplate = {};
        }

        if (!window.datatypeTemplate["!!prefix_id!!"]) {
            window.datatypeTemplate["!!prefix_id!!"] = new TagsTemplates("!!prefix_id!!");
        }
    });
</script>
';