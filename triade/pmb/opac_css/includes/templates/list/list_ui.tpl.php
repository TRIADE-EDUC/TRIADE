<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_ui.tpl.php,v 1.5 2023/12/28 09:51:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//	------------------------------------------------------------------------------
//	$list_ui_search_form_tpl : template de recherche pour les listes
//	------------------------------------------------------------------------------
global $list_ui_search_form_tpl, $javascript_path, $current_module, $msg, $charset, $list_ui_search_hidden_form_tpl;
global $list_ui_js_sort_script_sort, $list_ui_js_fast_filters_script, $list_ui_search_order_form_tpl;

$list_ui_search_hidden_fields = "
<input type='hidden' id='!!objects_type!!_json_filters' name='!!objects_type!!_json_filters' value='!!json_filters!!' />
<input type='hidden' id='!!objects_type!!_json_selected_columns' name='!!objects_type!!_json_selected_columns' value='!!json_selected_columns!!' />
<input type='hidden' id='!!objects_type!!_json_applied_group' name='!!objects_type!!_json_applied_group' value='!!json_applied_group!!' />
<input type='hidden' id='!!objects_type!!_json_applied_sort' name='!!objects_type!!_json_applied_sort' value='!!json_applied_sort!!' />
<input type='hidden' id='!!objects_type!!_page' name='!!objects_type!!_page' value='!!page!!' />
<input type='hidden' id='!!objects_type!!_nb_per_page' name='!!objects_type!!_nb_per_page' value='!!nb_per_page!!' />
<input type='hidden' id='!!objects_type!!_pager' name='!!objects_type!!_pager' value='!!pager!!' />
<input type='hidden' id='!!objects_type!!_selected_filters' name='!!objects_type!!_selected_filters' value='!!selected_filters!!' />
<input type='hidden' id='!!objects_type!!_ancre' name='!!objects_type!!_ancre' value='!!ancre!!' />
<input type='hidden' id='!!objects_type!!_go_directly_to_ancre' name='!!objects_type!!_go_directly_to_ancre' value='!!go_directly_to_ancre!!' />
<input type='hidden' id='!!objects_type!!_initialization' name='!!objects_type!!_initialization' value='' />
<input type='hidden' id='!!objects_type!!_applied_action' name='!!objects_type!!_applied_action' value='' />
<input type='hidden' id='objects_type' name='objects_type' value='!!objects_type!!' />
";

$list_ui_search_form_tpl = "
<script src='".$javascript_path."/ajax.js'></script>
<form class='form-".$current_module."' id='!!form_name!!' name='!!form_name!!' method='post' action=\"!!action!!\" >
	<h3>!!form_title!!</h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>
		<div id='!!objects_type!!_search_label' class='list_ui_search_label !!objects_type!!_search_label' style='display:!!unfoldable_filters!!;'>
			<img src='!!expandable_icon!!' class='img_plus' name='imEx' id='!!objects_type!!_search_img' title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' />
			<span class='list_ui_search_label_text'>
				<label>".htmlentities($msg['list_ui_search'], ENT_QUOTES, $charset)."</label>
			</span>
		</div>
		<div id='!!objects_type!!_search_content' class='list_ui_search_content !!objects_type!!_search_content' style='display:!!unfolded_filters!!;'>
			!!list_search_content_form_tpl!!
		</div>
		!!list_options_content_form_tpl!!
		!!list_datasets_my_content_form_tpl!!
		!!list_datasets_shared_content_form_tpl!!
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
				".$list_ui_search_hidden_fields."
				<input type='submit' id='!!objects_type!!_button_search' class='bouton' value='".$msg['apply']."' onclick=\"this.form.!!objects_type!!_applied_action.value = 'apply';\" />&nbsp;
				!!list_button_save!!
				!!list_button_initialization!!
				!!list_button_add!!
				!!list_buttons_extension!!
			</div>
			<div class='right'>
				!!export_icons!!
			</div>
		</div>
		<div class='row'>&nbsp;</div>
	</div>
</form>
<div class='row'>
	<span id='!!objects_type!!_messages' class='erreur'>!!messages!!</span>
</div>
<script>
	require(['dojo/ready', 'apps/list/ManageSearch'], function(ready, ManageSearch) {
		 ready(function(){
			new ManageSearch('!!objects_type!!');
		});
	});
	ajax_parse_dom();
</script>
";

$list_ui_search_hidden_form_tpl = "
<script src='".$javascript_path."/ajax.js'></script>
<form class='form-".$current_module."' id='!!form_name!!' name='!!form_name!!' method='post' action=\"!!action!!\" style='display:none;'>
	".$list_ui_search_hidden_fields."
</form>
<div class='row'>
	<span id='!!objects_type!!_messages' class='erreur'>!!messages!!</span>
</div>
";

$list_ui_js_sort_script_sort = "
	<script>
		function !!objects_type!!_sort_by(criteria, asc_desc, indice) {
			var url = '!!ajax_controller_url_base!!&action=!!action!!&sort_by='+criteria;
			if(asc_desc == 'desc') {
				//on repasse en tri croissant
				url += '&sort_asc_desc=asc';
			} else if(asc_desc == 'asc') {
				//on repasse en tri décroissant
				url += '&sort_asc_desc=desc';
			}
			var req = new http_request();
			if(document.getElementById('!!objects_type!!_json_filters_'+indice)) {
				var filters = document.getElementById('!!objects_type!!_json_filters_'+indice).value;
			} else if(document.getElementById('!!objects_type!!_json_filters')) {
				var filters = document.getElementById('!!objects_type!!_json_filters').value;
			} else {
				var filters = '';
			}
			if(document.getElementById('!!objects_type!!_pager_'+indice)) {
				var pager = document.getElementById('!!objects_type!!_pager_'+indice).value;
			} else if(document.getElementById('!!objects_type!!_pager')) {
				var pager = document.getElementById('!!objects_type!!_pager').value;
			} else {
				var pager = '';
			}
			if(document.getElementById('!!objects_type!!_ancre_'+indice)) {
				var ancre = document.getElementById('!!objects_type!!_ancre_'+indice).value;
			} else if(document.getElementById('!!objects_type!!_ancre')) {
				var ancre = document.getElementById('!!objects_type!!_ancre').value;
			} else {
				var ancre = '';
			}
            if(document.getElementById('!!objects_type!!_list_'+indice)) {
				var table = document.getElementById('!!objects_type!!_list_'+indice);
			} else if(document.getElementById('!!objects_type!!_list_0')) {
				var table = document.getElementById('!!objects_type!!_list_0');
			} else {
				var table = document.getElementById('!!objects_type!!_list');
			}
            table.innerHTML = '<tr><td><img src=\"".get_url_icon('patience.gif')."\"/></td></tr>';
			req.request(url,1, 'object_type=!!objects_type!!&filters='+encodeURIComponent(filters)+'&pager='+pager+'&ancre='+ancre, true, function(response) {
                table.innerHTML = response;
    			if(document.getElementById('!!objects_type!!_applied_sort_by_0')) {
    				var options = document.getElementById('!!objects_type!!_applied_sort_by_0').options;
    				for (var i= 0; i < options.length; i++) {
    				    if (options[i].value === criteria) {
    				        options[i].selected= true;
    				        break;
    				    }
    				}
    				switch(asc_desc) {
    					case 'asc': //on repasse en tri décroissant
    						document.getElementById('!!objects_type!!_applied_sort_asc_0').removeAttribute('checked');
    						document.getElementById('!!objects_type!!_applied_sort_desc_0').setAttribute('checked', 'checked');
    						break;
    					case 'desc': //on repasse en tri croissant
    					default:
    						document.getElementById('!!objects_type!!_applied_sort_asc_0').setAttribute('checked', 'checked');
    						document.getElementById('!!objects_type!!_applied_sort_desc_0').removeAttribute('checked');
    						break;
    				}
    			}
				if(ancre) {
					window.location='#'+ancre;
				}
				if(typeof initIt !== 'undefined') {
					initIt();
				}
            });
		}
	</script>
";

$list_ui_js_fast_filters_script = "
<script>
	require(['dojo/ready', 'apps/list/ManageFastFilters'], function(ready, ManageFastFilters) {
		 ready(function(){
			new ManageFastFilters('!!ajax_controller_url_base!!', '!!objects_type!!', '!!all_on_page!!');
		});
	});
</script>
";

$list_ui_search_order_form_tpl = "
<div class='row'>
	<div class='row'>
		<label class='etiquette' for='!!objects_type!!_applied_sort_by_0'>".$msg["list_applied_sort"]."</label>
	</div>
    <div class='row'>
        !!applied_sort_selectors!!
    </div>
</div>
";