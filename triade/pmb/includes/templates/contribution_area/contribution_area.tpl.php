<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.tpl.php,v 1.33 2023/12/20 09:43:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $contribution_area_list_tpl, $msg, $charset, $contribution_area_list_line_tpl, $contribution_area_add_button;
global $contribution_area_delete_button, $contribution_area_form, $current_module, $contribution_area_form_definition, $contribution_area_computed_form;

$contribution_area_list_tpl="
<table>
	<tr>
		<th>".htmlentities($msg['67'], ENT_QUOTES, $charset)."</th>
		<th>".htmlentities($msg['contribution_area_status'], ENT_QUOTES, $charset)."</th>
		<th></th>
		<th>".htmlentities($msg['frbr_page_order'], ENT_QUOTES, $charset)."</th>
	</tr>
	!!list!!
</table>";

$contribution_area_list_line_tpl="
<tr  class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">
	<td style='cursor: pointer' onmousedown=\"document.location='./modelling.php?categ=contribution_area&sub=area&action=define&id=!!id!!';\">
		<div class='area_color' style='width:10px; height:10px; background-color:!!area_color!!; display:inline-block;'></div>
		!!area_title!!
	</td>
	<td style='cursor: pointer' onmousedown=\"document.location='./modelling.php?categ=contribution_area&sub=area&action=define&id=!!id!!';\">
		!!area_status!!
	</td>
	<td>
		<input type='button' class='bouton' value='".$msg['contribution_area_params']."' onclick=\"document.location='./modelling.php?categ=contribution_area&sub=area&action=edit&id=!!id!!';\"/>
		<input type='button' class='bouton' value='".$msg['admin_contribution_area_computed']."' onclick=\"document.location='./modelling.php?categ=contribution_area&sub=area&action=computed&id=!!id!!';\"/>
        !!disabled_default_area!!
	</td>
    <td>
		<img src='".get_url_icon('bottom-arrow.png')."' title='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset).
		          "' onclick=\"document.location='./modelling.php?categ=contribution_area&sub=area&action=down&id=!!id!!';\" style='cursor:pointer;'>
		<img src='".get_url_icon('top-arrow.png')."' title='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset).
		          "' onclick=\"document.location='./modelling.php?categ=contribution_area&sub=area&action=up&id=!!id!!';\" style='cursor:pointer;'>
	</td>
</tr>
";

$contribution_area_edit_entity= "
<input type='button' !!disabled_default_area!! class='bouton !!disabled_default_area!!' value='!!button_default_area!!' onclick=\"if(confirm('!!confirm_msg_default!!')) document.location='./modelling.php?categ=contribution_area&sub=area&action=default&id=!!id!!';\"/>";

$contribution_area_add_button = "
	<div class='row'>
		<input type='button' class='bouton' name='add_button' value='".htmlentities($msg["admin_nomenclature_voice_add"], ENT_QUOTES, $charset)."'
		onclick=\"document.location='./modelling.php?categ=contribution_area&sub=area&action=edit'\" />
	</div>
";

$contribution_area_delete_button = "
    <input type='button' class='bouton' name='delete_button' 
        value='".htmlentities($msg["admin_nomenclature_voice_form_del"], ENT_QUOTES, $charset)."' 
        onclick=\"delete_area(!!id!!)\" 
    />
    <script>
        function delete_area(id){
            resultat = window.confirm('" . $msg["contribution_area_confirm_del"]. "');
            if(resultat) {
                document.location='./modelling.php?categ=contribution_area&sub=area&action=delete&id='+id;
            } else {
                return false;
            }
        }
    </script>
";

$contribution_area_form = 
"<form class='form-".$current_module."' id='contribution_area' name='contribution_area'  method='post' action=\"modelling.php?categ=contribution_area&sub=area&action=save\" enctype='multipart/form-data'>
	<h3>!!msg_title!!</h3>	
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='area_title'>".$msg['contribution_area_title']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='area_title' id='area_title' value='!!area_title!!' />
		</div>
		<div class='row'>
			<label class='etiquette' for='area_comment'>".$msg['contribution_area_comment']."</label>
		</div>
		<div class='row'>
			<textarea name='area_comment' id='area_comment'>!!area_comment!!</textarea>
		</div>
		<div class='row'>
			<label class='etiquette' for='area_color'>".$msg['contribution_area_color']."</label>
		</div>
		<div class='row'>
			<input type='color' class='saisie-20em' name='area_color' id='area_color' value='!!area_color!!' />
		</div>
		<div class='row'>
			<label for='area_status'>". $msg['contribution_area_status'] ."</label>
		</div>
		<div class='row'>
			<select name='area_status' id='area_status'>
				!!area_status!!
			</select>
		</div>
		!!area_rights!!
		<div class='row'>
		    <label class='etiquette' for='area_repo_template_authorities'>".$msg['contribution_area_repo_template_authorities']."</label>
	    </div>
		<div class='row'>
	        <input type='text' class='saisie-50em' name='area_repo_template_authorities' id='area_repo_template_authorities' value='!!area_repo_template_authorities!!' />
	    </div>
		<div class='row'>
		    <label class='etiquette' for='area_repo_template_records'>".$msg['contribution_area_repo_template_records']."</label>
	    </div>
		<div class='row'>
	        <input type='text' class='saisie-50em' name='area_repo_template_records' id='area_repo_template_records' value='!!area_repo_template_records!!' />
	    </div>
		<div class='row'>
		    <label class='etiquette' for='area_logo'>".$msg['contribution_area_logo']."</label>
	    </div>
		<div class='row'>
	        <input type='file' class='saisie-50em' name='area_logo' id='area_logo' />
            <input class='bouton' type='button' onclick='delete_logo()' value='X'/>
            <input type='hidden' name='area_del_logo' id='area_del_logo' value='false'/>
	    </div>
        <br />
		<div class='row'>
	         <img id='area_logo_src' src='!!area_logo!!' width = '250px' height = '250px' style = 'display: !!area_logo_src_display!!;'> 
	    </div>
		<div class='row'></div>
        <div class='row'>
            <input type='checkbox' id='area_opac_visibility' name='area_opac_visibility' value='1' !!area_opac_visibility!!>
            <label for='area_opac_visibility'>".$msg['contribution_area_opac_visibility']."</label>
        </div>
	</div>
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_nomenclature_voice_form_exit']."' onclick=\"document.location='./modelling.php?categ=contribution_area&sub=area'\"  />
			<input type='submit' class='bouton' value='".$msg['admin_nomenclature_voice_form_save']."' onclick=\"document.getElementById('action').value='save';if (!test_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>
<script>
    function delete_logo(){
        let input = document.getElementById('area_del_logo');
        input.value = true;
        
        let logo = document.getElementById('area_logo_src');
        logo.style.display =  'none';
    }
</script>
";

$contribution_area_form_definition = "
<div class='row'>
    <h3>!!area_title!!</h3>

    <div id='graph_legend_container'>
        <label>".$msg['contribution_area_graph_legend']."</label>
        ".get_expandBase_button('graph_legend')."
        <div id='graph_legendChild' style='margin-bottom: 6px; display: none; width: 94%;'>
            <svg id='graph_legend_svg' height='50' width='600'></svg>
        </div>
    </div>
</div>
<div data-dojo-id='availableEntities' data-dojo-type='dojo/store/Memory' data-dojo-props='data:!!available_entities_data!!'></div>
<div data-dojo-id='graphStore' data-dojo-type='apps/contribution_area/GraphStore' data-dojo-props='area_id:!!id!!,data:!!graph_data_store!!,current_scenario:0,graphShapes:!!graph_shapes!!'></div>
		<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true' style='height:800px;width:100%;'>
	<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true,region:\"left\"' style='height:100%;width:200px;'>	
		<div data-dojo-props='region:\"center\", splitter:true' style='width:auto;height:50%;' data-dojo-type='apps/contribution_area/FormsList'></div>
		<div data-dojo-props='region:\"top\",splitter:true' style='width:auto;height:50%' data-dojo-type='apps/contribution_area/ScenariosList'></div>
	</div>
	<div data-dojo-type='apps/contribution_area/Graph' data-dojo-props='splitter:true,region:\"center\"' style='height:100%;width:auto;overflow:scroll;'>
        <div class='graph_menu'>
            <button type='button' class='bouton' data-type='contribution_zoom_out_button' title='".$msg['contribution_zoom_out_button']."'><i class='fa fa-minus'></i></button>
            <button type='button' class='bouton' data-type='contribution_zoom_in_button' title='".$msg['contribution_zoom_in_button']."'><i class='fa fa-plus'></i></button>
			<button type='button' class='bouton' data-type='contribution_resize_button'>".$msg['contribution_resize_button']."</button>            
			<select data-type='contribution_scenario_list'>
                <option value='' selected=''>".$msg['contribution_scenario_select']."</option>
            <select>
        </div>
    </div>
</div>";

$contribution_area_computed_form = "
<div class='row'>
	<h3>
		!!area_title!!
	</h3>
</div>
<script src='./javascript/ace/ace.js' type='text/javascript' charset='utf-8'></script>
<script src='./javascript/ace/theme-eclipse.js' type='text/javascript' charset='utf-8'></script>
<script src='./javascript/ace/mode-javascript.js' type='text/javascript' charset='utf-8'></script>
<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true' style='height:800px;width:100%;'>
	<div data-dojo-type='dijit/layout/ContentPane' data-dojo-props='region:\"left\", splitter:true' style='height:100%;width:200px;'>
		<input type='hidden' id='contribution_area_num' name='contribution_area_num' value='!!id!!'/>
		<div data-dojo-id='computedFieldsStore' data-dojo-type='apps/contribution_area/computed_fields/Store' data-dojo-props='data:!!graph_data_store!!,availableEntities:!!available_entities_data!!,computedFields:!!computed_fields!!,environmentFields:!!environment_fields!!,emprFields:!!empr_fields!!'></div>
		<div data-dojo-id='computedFieldsModel' data-dojo-type='dijit/tree/ObjectStoreModel' data-dojo-props='store: computedFieldsStore, query: {type: \"root\"}, rootId:\"root\"'></div>
		<div data-dojo-id='scenariosComputedTree' data-dojo-type='apps/contribution_area/computed_fields/Tree' data-dojo-props='model: computedFieldsModel' ></div>
	</div>
	<div data-dojo-id='computedFieldsValues' data-dojo-type='apps/contribution_area/computed_fields/Panel' data-dojo-props='region:\"center\"' style='height:100%;width:auto;overflow:scroll;'></div>
</div>";