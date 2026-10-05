<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_common.tpl.php,v 1.19 2021/07/05 09:48:03 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $vedette_tpl,$msg,$charset,$lang,$base_path;

//TODO Mettre la CSS dans la feuille de style
$vedette_tpl['css'] = '
<style>
	.vedette_composee_corp {
		border:solid 1px #ccc;
		color: #333;
		padding:10px;
	}
	
	.vedette_composee_available_fields {
	    max-width: 20%;
	    width: 20%;
		color: #333;
	    float: left;
	    border-right: solid 1px #ccc;
	}
	
	.vedette_composee_available_field {
		color: #333;
		background-color: #fff;
		border:solid 1px #ccc;
		width:90%;
		padding: 5px;
		margin:5px auto;
		cursor:move;
	}
	
	.vedette_composee_subdivisions {
		max-width: 79%;
		margin-left: 21%;
		box-sizing: border-box;
		overflow:hidden;
	}
	
	.vedette_composee_subdivision {
		color: #333;
		background-color: #fff;
		border:solid 1px #ccc;
		margin:5px auto;
		vertical-align: middle;
	}
	
	.vedette_composee_subdivision_label, .vedette_composee_element, .vedette_composee_element_form {
		float: left;
	}
	
	.vedette_composee_subdivision_label {
        height:100px; 
		width: 90px;
		line-height: 18px;
		padding: 5px;
	}
		
	.vedette_composee_apercu {
		background-color: #DFDCD9;
	}
</style>
';

$vedette_tpl['form_body_script']='
<script type="text/javascript" src="./javascript/vedette_composee_drag_n_drop.js"></script>
<script type="text/javascript" src="./javascript/vedette_grammar.js"></script>
<script type="text/javascript">
	!!available_fields_scripts!!
	var directSearch = !!direct_search!!

	var vedette_element =  {

		get_vedette_element : function(vedette_type) {
			switch (vedette_type) {
				!!get_vedette_element_switchcases!!
				default:
					break;
			}
		},

		create_box : function(caller_property_name,vedette_type, parent, vedette_composee_subdivision_id, vedette_composee_element_order, id, label, rawlabel, vedette_composee_order, params, caller_property_name_contribution) {
			var vedette_element = this.get_vedette_element(vedette_type);
			vedette_element.create_box(caller_property_name,parent, vedette_composee_subdivision_id, vedette_composee_element_order, id, label, rawlabel, vedette_composee_order, params, caller_property_name_contribution);
		},

		update_box : function(vedette_type, parent, vedette_composee_subdivision_id, vedette_composee_element_order, vedette_composee_order, authid) {
			var parent_id = parent.getAttribute("id");
			var form = document.getElementById(parent_id + "_form");
			var text = document.getElementById(parent_id + "_label");
			var element_id = document.getElementById(parent_id + "_id");
			var caller_property_name = parent_id.split("_composed")[0]+"_composed"; 
			parent.removeChild(form);

			var vedette_element = this.get_vedette_element(vedette_type);
			vedette_element.create_box(caller_property_name,parent, vedette_composee_subdivision_id, vedette_composee_element_order, element_id.value, text.value, text.getAttribute("rawlabel"), vedette_composee_order, authid);
		},

		callback : function(vedette_type, id) {
			var vedette_element = this.get_vedette_element(vedette_type);
			var caller_property_name = id.split("_composed")[0]+"_composed";
			vedette_element.callback(id);
			var order= id.substr((caller_property_name + "_").length).split("_")[0];
			vedette_composee_update_all(caller_property_name + "_"+order+"_vedette_composee_subdivisions");
		}
	}

	function vedette_composee_callback(id) {
		var vedette_type = document.getElementById(id).getAttribute("vedettetype");
		vedette_element.callback(vedette_type, id);
	}

	var grammar_tabs = document.querySelectorAll(".grammar_tab");
	if(grammar_tabs.length) {
	    grammar_tabs.forEach(el => {
	    		el.addEventListener("click", function(e) {
	                manageVedetteTab(el);
	            });	
	    });
	}
</script>';

$vedette_tpl['form_body']='
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_corp" class="vedette_composee_corp">
    <script type="text/javascript" id="vedette_script_!!property_name!!_!!vedette_composee_order!!">
    	!!caller!!_!!property_name!!_!!vedette_composee_order!!_tab_vedette_elements = !!tab_vedette_elements!!;
    	!!caller!!_!!property_name!!_!!vedette_composee_order!!_separator = "!!vedette_separator!!";
    </script>
	<!-- zone aperçu -->
	<div><label class="etiquette" for="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_apercu">'.$msg['vedette_composee_apercu'].' : </label>
		<input type="text" class="saisie-80em vedette_composee_apercu" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_apercu" value="!!vedette_composee_apercu!!" readonly="readonly" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][value]"/>
	</div>
	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][type]" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_type" value="!!vedette_composee_type!!"/>
	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][id]" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_id" value="!!vedette_composee_id!!"/>
	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][grammar]" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_grammar" value="!!vedette_composee_grammar!!"/>
	<div class="row">&nbsp;</div>
	
	<!-- zone liste champs -->
	<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_available_fields" class="vedette_composee_available_fields">
			!!vedette_composee_available_fields!!
		<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_delete_element" recept="yes" recepttype="vedette_composee_delete_element" highlight="vedette_element_highlight" downlight="vedette_element_downlight"><img src="'.get_url_icon('suppr_all.gif').'"/></div>
	</div>
	
	<!-- zone subdivision -->
	<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_subdivisions" class="vedette_composee_subdivisions">
		!!vedette_composee_subdivisions!!
	</div>
	
	<div class="row">&nbsp;</div>

</div>
';

$vedette_tpl['form_body_contribution']='
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_corp" class="vedette_composee_corp">
    <script type="text/javascript" id="vedette_script_!!property_name!!_!!vedette_composee_order!!">
    	!!caller!!_!!property_name!!_!!vedette_composee_order!!_tab_vedette_elements = !!tab_vedette_elements!!;
    	!!caller!!_!!property_name!!_!!vedette_composee_order!!_separator = "!!vedette_separator!!";
    </script>
	<!-- zone aperçu -->
	<div><label class="etiquette" for="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_apercu">'.$msg['vedette_composee_apercu'].' : </label>
		<input type="text" class="saisie-80em vedette_composee_apercu" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_apercu" value="!!vedette_composee_apercu!!" readonly="readonly" name="!!caller!!_!!property_name_contribution!!_[!!vedette_composee_order!!][value]"/>
	</div>
	<input type="hidden" name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][type]" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_type" value="!!vedette_composee_type!!"/>
	<input type="hidden" name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][id]" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_id" value="!!vedette_composee_id!!"/>
	<input type="hidden" name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][grammar]" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_grammar" value="!!vedette_composee_grammar!!"/>
	<div class="row">&nbsp;</div>
	
	<!-- zone liste champs -->
	<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_available_fields" class="vedette_composee_available_fields">
			!!vedette_composee_available_fields!!
		<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_delete_element" recept="yes" recepttype="vedette_composee_delete_element" highlight="vedette_element_highlight" downlight="vedette_element_downlight"><img src="'.get_url_icon('suppr_all.gif').'"/></div>
	</div>
	
	<!-- zone subdivision -->
	<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_subdivisions" class="vedette_composee_subdivisions">
		!!vedette_composee_subdivisions!!
	</div>
	
	<div class="row">&nbsp;</div>

</div>
';

$vedette_tpl['grammar_head'] = '
<ul class="grammar_tabs">
    !!grammar_tabs!!
</ul>
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_corp" class="vedette_composee_corp">
    !!grammar_body!!
</div>
<input type="hidden" id="grammar_property_name" value="!!property!!">
<input type="hidden" id="grammar_instance_name" value="!!caller!!">
';

$vedette_tpl['grammar_tab'] = '
    <li class="grammar_tab !!grammar_selected!!" id="grammar_tab_!!grammar_index!!">
        <input type="hidden" name="grammar_name_!!grammar_index!!" id="grammar_name_!!grammar_index!!" value="!!grammar_value!!" />
        <label>!!grammar_label!!</label>
    </li>
';

$vedette_tpl['grammar_body'] = '
    <script type="text/javascript" id="vedette_script_!!property_name!!_!!vedette_composee_order!!">
    	!!caller!!_!!property_name!!_!!vedette_composee_order!!_tab_vedette_elements = !!tab_vedette_elements!!;
    	!!caller!!_!!property_name!!_!!vedette_composee_order!!_separator = "!!vedette_separator!!";
    </script>
    <div class="grammar_content">
        <!-- zone aperçu -->
    	<div><label class="etiquette" for="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_apercu">'.$msg['vedette_composee_apercu'].' : </label>
    		<input type="text" class="saisie-80em vedette_composee_apercu" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_apercu" value="!!vedette_composee_apercu!!" readonly="readonly" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][value]"/>
    	</div>
    	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][type]" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_type" value="!!vedette_composee_type!!"/>
    	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][id]" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_id" value="!!vedette_composee_id!!"/>
    	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][grammar]" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_grammar" value="!!vedette_composee_grammar!!"/>
    	<div class="row">&nbsp;</div>
    	
    	<!-- zone liste champs -->
    	<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_available_fields" class="vedette_composee_available_fields">
    			!!vedette_composee_available_fields!!
    		<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_delete_element" recept="yes" recepttype="vedette_composee_delete_element" highlight="vedette_element_highlight" downlight="vedette_element_downlight"><img src="'.get_url_icon('suppr_all.gif').'"/></div>
    	</div>
    	
    	<!-- zone subdivision -->
    	<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_subdivisions" class="vedette_composee_subdivisions">
    		!!vedette_composee_subdivisions!!
    	</div>
    	
    	<div class="row">&nbsp;</div>
    </div>
';

$vedette_tpl['vedette_composee_available_field']='
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_vedette_composee_available_field_!!available_field_id!!" class="vedette_composee_available_field" draggable="yes" dragtype="vedette_composee_available_fields" dragtext="!!vedette_composee_available_field_label!!" authid="!!authid!!" vedettetype="!!available_field_type!!" available_field_num="!!available_field_num!!" parentorder="!!vedette_composee_order!!" data-pmb-params=\'!!vedette_element_params!!\'>
	!!vedette_composee_available_field_label!!
</div>
';

$vedette_tpl['vedette_composee_subdivision']='
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!" class="vedette_composee_subdivision" recepttype="vedette_composee_subdivision" recept="yes" highlight="vedette_element_highlight" downlight="vedette_element_downlight" cardmin="!!vedette_composee_subdivision_cardmin!!" cardmax="!!vedette_composee_subdivision_cardmax!!" subdivisiontype="!!vedette_composee_subdivision_id!!" order="!!vedette_composee_subdivision_order!!" parentorder="!!vedette_composee_order!!">
	<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_label" class="vedette_composee_subdivision_label">!!vedette_composee_subdivision_label!!</div>
	!!vedette_composee_subdivision_elements!!
	<div class="row"></div>
</div>
<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][elements_order]" value="!!elements_order!!" id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_elements_order" />
<div class="row"></div>
';

$vedette_tpl['vedette_composee_element']='
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!" class="vedette_composee_element" dragtype="vedette_composee_element" recepttype="vedette_composee_element" draggable="yes" recept="yes" order="!!vedette_composee_element_order!!" highlight="vedette_element_highlight" downlight="vedette_element_downlight" vedettetype="!!vedette_composee_element_type!!" handler="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_handler" >
	<span id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_handler" style="float:left;padding-right:7px;" >
		<img src="'.get_url_icon('drag_symbol.png').'" style="vertical-align:middle;" />
	</span>
	!!vedette_composee_element_form!!
</div>
';

$vedette_tpl['vedette_composee_get_vedette_element_switchcase']='
case "!!vedette_type!!":
	return !!vedette_type!!;
	break;';