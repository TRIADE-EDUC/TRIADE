<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_ui.tpl.php,v 1.58.2.3 2024/12/18 07:42:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//	------------------------------------------------------------------------------
//	$list_ui_search_form_tpl : template de recherche pour les listes
//	------------------------------------------------------------------------------
global $list_ui_search_form_tpl, $base_path, $current_module, $msg, $charset, $list_ui_search_hidden_form_tpl, $list_ui_options_content_form_tpl;
global $list_ui_datasets_content_form_tpl, $list_ui_settings_content_form_tpl, $list_ui_js_sort_script_sort, $list_ui_js_fast_filters_script, $list_ui_search_add_filter_form_tpl, $list_ui_search_order_form_tpl, $list_dataset_form_tpl;
global $list_ui_settings_display_content_form_tpl, $list_ui_settings_columns_content_form_tpl, $list_ui_settings_filters_content_form_tpl, $list_ui_settings_selection_actions_content_form_tpl;
global $list_default_dataset_form_tpl;

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
<script src='".$base_path."/javascript/ajax.js'></script>
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
			<div class='left list_ui_search_buttons !!objects_type!!_search_buttons'>
				".$list_ui_search_hidden_fields."
				<input type='submit' id='!!objects_type!!_button_search' class='bouton' value='".$msg['apply']."' onclick=\"this.form.!!objects_type!!_applied_action.value = 'apply';\" />&nbsp;
				!!list_button_save!!
				!!list_button_initialization!!
				!!list_button_add!!
                !!list_button_import!!
				!!list_buttons_extension!!
			</div>
			<div class='right list_ui_export_icons !!objects_type!!_export_icons'>
				!!export_icons!!
			</div>
		</div>
		<div class='row'>&nbsp;</div>
	</div>
</form>
<div class='row'>
	<span id='!!objects_type!!_messages' class='erreur'>!!messages!!</span>
</div>
<script type='text/javascript'>
	require(['dojo/ready', 'apps/list/ManageSearch'], function(ready, ManageSearch) {
		 ready(function(){
			new ManageSearch('!!objects_type!!');
		});
	});
	ajax_parse_dom();
</script>
";

$list_ui_search_hidden_form_tpl = "
<script src='".$base_path."/javascript/ajax.js'></script>
<form class='form-".$current_module."' id='!!form_name!!' name='!!form_name!!' method='post' action=\"!!action!!\" style='display:none;'>
	".$list_ui_search_hidden_fields."
</form>
<div class='row'>
	<span id='!!objects_type!!_messages' class='erreur'>!!messages!!</span>
</div>
";

$list_ui_options_content_form_tpl = "
		<div class='row'>&nbsp;</div>
		<div id='!!objects_type!!_options_label' class='list_ui_options_label !!objects_type!!_options_label'>
			<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='!!objects_type!!_options_img' title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' />
			<span class='list_ui_options_label_text'>
				<label>".htmlentities($msg['list_ui_options'], ENT_QUOTES, $charset)."</label>
			</span>
		</div>
		<div id='!!objects_type!!_options_content' class='list_ui_options_content !!objects_type!!_options_content'>
			<div class='list_ui_options_columns'>
				<div class='list_ui_options_columns_available'>
					<label class='etiquette' for='!!objects_type!!_available_columns'>".$msg['list_ui_options_available_columns']."</label>
					<br />
					!!available_columns!!
				</div>
				<div class='list_ui_options_columns_change'>
					<img src='".get_url_icon('right-arrow.png')."' id='!!objects_type!!_options_move_available_to_selected' title='".htmlentities($msg['list_ui_options_move_available_to_selected'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['list_ui_options_move_available_to_selected'], ENT_QUOTES, $charset)."' class='list_ui_options_move_available_to_selected'/>
					<br />
					<img src='".get_url_icon('left-arrow.png')."' id='!!objects_type!!_options_move_selected_to_available' title='".htmlentities($msg['list_ui_options_move_selected_to_available'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['list_ui_options_move_selected_to_available'], ENT_QUOTES, $charset)."' class='list_ui_options_move_selected_to_available'/>
				</div>
				<div class='list_ui_options_columns_selected'>
					<div class='list_ui_options_columns_selected_block'>
						<label class='etiquette' for='!!objects_type!!_selected_columns'>".$msg['list_ui_options_selected_columns']."</label>
						<br />
						!!selected_columns!!
					</div>
					<div class='list_ui_options_columns_selected_buttons'>
						<img src='".get_url_icon('first-arrow.png')."' id='!!objects_type!!_options_move_option_first' title='".htmlentities($msg['list_ui_options_move_option_first'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['list_ui_options_move_option_first'], ENT_QUOTES, $charset)."' class='list_ui_options_move_option_first'/>		
						<br />
						<img src='".get_url_icon('top-arrow.png')."' id='!!objects_type!!_options_move_option_top' title='".htmlentities($msg['list_ui_options_move_option_top'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['list_ui_options_move_option_top'], ENT_QUOTES, $charset)."' class='list_ui_options_move_option_top'/>
						<br />
						<img src='".get_url_icon('bottom-arrow.png')."' id='!!objects_type!!_options_move_option_bottom' title='".htmlentities($msg['list_ui_options_move_option_bottom'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['list_ui_options_move_option_bottom'], ENT_QUOTES, $charset)."' class='list_ui_options_move_option_bottom'/>
						<br />
						<img src='".get_url_icon('last-arrow.png')."' id='!!objects_type!!_options_move_option_last' title='".htmlentities($msg['list_ui_options_move_option_last'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['list_ui_options_move_option_last'], ENT_QUOTES, $charset)."' class='list_ui_options_move_option_last'/>
					</div>
				</div>
			</div>
			<div class='row'>
				<div class='list_ui_options_group'>
					<span class='list_ui_options_group_label_text'>
						<label for='!!objects_type!!_applied_group_0'>".htmlentities($msg['list_ui_options_group_by'], ENT_QUOTES, $charset)."</label>
					</span>
 					!!applied_group_selectors!!
				</div>
			</div>
		</div>
		<script type='text/javascript'>
			require(['dojo/ready', 'apps/list/ManageOptions'], function(ready, ManageOptions) {
				 ready(function(){
					new ManageOptions('!!objects_type!!');
				});
			});
		</script>
";

$list_ui_settings_content_form_tpl = "
		<div class='row'>&nbsp;</div>
		<div id='!!objects_type!!_settings_label' class='list_ui_settings_label !!objects_type!!_settings_label'>
			<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='!!objects_type!!_settings_img' title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' />
			<span class='list_ui_settings_label_text'>
				<label>".htmlentities($msg['list_ui_settings'], ENT_QUOTES, $charset)."</label>
			</span>
		</div>
		<div id='!!objects_type!!_settings_content' class='list_ui_settings_content !!objects_type!!_settings_content'>
			!!list_settings_display_content_form_tpl!!
			!!list_settings_columns_content_form_tpl!!
			!!list_settings_filters_content_form_tpl!!
			!!list_settings_selection_actions_content_form_tpl!!
			<div class='row'>&nbsp;</div>
		</div>
		<script type='text/javascript'>
			require(['dojo/ready', 'apps/list/ManageSettings'], function(ready, ManageSettings) {
				 ready(function(){
					new ManageSettings('!!objects_type!!');
				});
			});
		</script>
";

$list_ui_datasets_content_form_tpl = "
<div class='row'>&nbsp;</div>
<div id='!!objects_type!!_datasets_!!which!!_label' class='list_ui_datasets_label !!objects_type!!_datasets_label'>
	<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='!!objects_type!!_datasets_!!which!!_img' title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' />
	<span class='list_ui_datasets_label_text'>
		<label>!!datasets_label!!</label>
	</span>
</div>
<div id='!!objects_type!!_datasets_!!which!!_content' class='list_ui_datasets_content !!objects_type!!_datasets_content'>
	!!datasets_content!!
</div>
<script type='text/javascript'>
	require(['dojo/ready', 'apps/list/ListDatasets'], function(ready, ListDatasets) {
		 ready(function(){
			new ListDatasets('!!objects_type!!_datasets_!!which!!_content', '!!objects_type!!', '!!which!!', '!!controller_url_base!!');
		});
	});
</script>";

$list_ui_js_sort_script_sort = "
	<script type='text/javascript'>
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
<script type='text/javascript'>
	require(['dojo/ready', 'apps/list/ManageFastFilters'], function(ready, ManageFastFilters) {
		 ready(function(){
			new ManageFastFilters('!!ajax_controller_url_base!!', '!!objects_type!!', '!!all_on_page!!');
		});
	});
</script>
";

$list_ui_search_add_filter_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label for='!!objects_type!!_add_filter'>".htmlentities($msg['list_ui_add_filter'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<select id='!!objects_type!!_add_filter' name='!!objects_type!!_add_filter' data-filters-number='!!selected_filters_number!!'>!!add_filter_options!!</select>
		</div>
	</div>
</div>
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

$list_dataset_form_tpl="
<script src='".$base_path."/javascript/ajax.js'></script>
<form class='form-".$current_module."' id='list_dataset_form' name='list_dataset_form'  method='post' action=\"!!action!!\" >
	<h3>!!title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='list_label'>".$msg['list_label']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='list_label' id='list_label' value='!!label!!' />
		</div>
		<div class='row'>&nbsp;</div>
		<div id='!!objects_type!!_search_content_filters'>
			<div class='row'>
				<div class='row'>
					<label class='etiquette'>".$msg["list_ui_filters"]."</label>
				</div>
			    <div class='row'>
			        !!list_search_filters_form_tpl!!
			    </div>
			</div>
		</div>
		!!list_search_add_filter_form_tpl!!
		<div id='!!objects_type!!_search_content_order'>
			!!list_search_order_form_tpl!!
		</div>
		!!list_options_content_form_tpl!!
		!!list_settings_content_form_tpl!!
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label>".$msg['list_pager']."</label>
		</div>
		<div class='row'>
			<label for='!!objects_type!!_nb_per_page' style='all:unset'>".$msg['per_page']."</label> <input type='number' class='saisie-5em' name='!!objects_type!!_nb_per_page' id='!!objects_type!!_nb_per_page' value='!!nb_per_page!!' !!all_on_page!! />
		</div>
		<div class='row'>
			<label for='!!objects_type!!_pager_position' style='all:unset'>".$msg['pager_position']."</label> !!pager_position!!
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);'>
			<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);'>
		</div>
		<div class='row'>
			<label class='etiquette'>".$msg['list_autorisations']." :</label><br />
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			!!autorisations_users!!
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='list_num_ranking'>".($msg['list_ranking'] ?? "")."</label>
		</div>
		<div class='row'>
			!!ranking!!
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='checkbox' name='list_default_selected' id='list_default_selected' value='1' !!default_selected!! />
			<label for='list_default_selected'>".$msg['list_default_selected']."</label>
		</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='hidden' id='!!objects_type!!_selected_filters' name='!!objects_type!!_selected_filters' value='!!selected_filters!!' />
			<input type='button' class='bouton' value='".$msg['76']."'  onclick=\"document.location='!!cancel_action!!'\"  />
			<input type='submit' class='bouton' value='".$msg['77']."' onclick=\"if (!list_dataset_check_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['list_dataset_form'].elements['list_label'].focus();
	function list_dataset_check_form(form) {
		if(!form.elements['list_label'].value) {
			alert('".addslashes($msg['list_label_mandatory'])."');
			return false;
		}
		return true;
	}				
</script>
<script type='text/javascript'>
	require(['dojo/ready', 'apps/list/ManageSearch'], function(ready, ManageSearch) {
		 ready(function(){
			new ManageSearch('!!objects_type!!');
		});
	});
	ajax_parse_dom();
</script>
";

$list_ui_settings_display_content_form_tpl = "
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='list_ui_settings_display_label'>
		<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='!!objects_type!!_settings_display_img' title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' />
		<span class='list_ui_settings_display_label_text'>
			<label>".htmlentities($msg['list_ui_settings_display'], ENT_QUOTES, $charset)."</label>
		</span>
	</div>
	<div id='!!objects_type!!_settings_display_content' class='list_ui_settings_display_content !!objects_type!!_settings_display_content'>
		!!settings_display!!
	</div>
</div>
";

$list_ui_settings_columns_content_form_tpl = "
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='list_ui_settings_columns_label'>
		<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='!!objects_type!!_settings_columns_img' title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' />
		<span class='list_ui_settings_columns_label_text'>
			<label>".htmlentities($msg['list_ui_settings_columns'], ENT_QUOTES, $charset)."</label>
		</span>
	</div>
	<div id='!!objects_type!!_settings_columns_content' class='list_ui_settings_columns_content !!objects_type!!_settings_columns_content'>
 		<table>
			<tr>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_columns_label'], ENT_QUOTES, $charset)."</th>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_columns_align'], ENT_QUOTES, $charset)."</th>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_columns_text'], ENT_QUOTES, $charset)."</th>
			<!--
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_columns_text_color'], ENT_QUOTES, $charset)."</th>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_columns_level'], ENT_QUOTES, $charset)."</th>
			-->
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_columns_visible'], ENT_QUOTES, $charset)."</th>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_columns_fast_filter'], ENT_QUOTES, $charset)."</th>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_columns_exportable'], ENT_QUOTES, $charset)."</th>
			</tr>
			!!settings_columns!!
		</table>
	</div>
</div>
";

$list_ui_settings_filters_content_form_tpl = "
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='list_ui_settings_filters_label'>
		<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='!!objects_type!!_settings_filters_img' title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' />
		<span class='list_ui_settings_filters_label_text'>
			<label>".htmlentities($msg['list_ui_settings_filters'], ENT_QUOTES, $charset)."</label>
		</span>
	</div>
	<div id='!!objects_type!!_settings_filters_content' class='list_ui_settings_filters_content !!objects_type!!_settings_filters_content'>
 		<table>
			<tr>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_filters_label'], ENT_QUOTES, $charset)."</th>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_filters_visible'], ENT_QUOTES, $charset)."</th>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_filters_selection_type'], ENT_QUOTES, $charset)."</th>
			</tr>
			!!settings_filters!!
		</table>
	</div>
</div>
";

$list_ui_settings_selection_actions_content_form_tpl = "
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='list_ui_settings_selection_actions_label'>
		<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='!!objects_type!!_settings_selection_actions_img' title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' />
		<span class='list_ui_settings_selection_actions_label_text'>
			<label>".htmlentities($msg['list_ui_settings_selection_actions'], ENT_QUOTES, $charset)."</label>
		</span>
	</div>
	<div id='!!objects_type!!_settings_selection_actions_content' class='list_ui_settings_selection_actions_content !!objects_type!!_settings_selection_actions_content'>
 		<table>
			<tr>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_selection_actions_label'], ENT_QUOTES, $charset)."</th>
				<th role='columnheader' scope='col'>".htmlentities($msg['list_ui_settings_selection_actions_visible'], ENT_QUOTES, $charset)."</th>
			</tr>
			!!settings_selection_actions!!
		</table>
	</div>
</div>
";

$list_default_dataset_form_tpl="
<script src='".$base_path."/javascript/ajax.js'></script>
<form class='form-".$current_module."' id='list_dataset_form' name='list_dataset_form'  method='post' action=\"!!action!!\" >
	<h3>!!title!!</h3>
	<div class='form-contenu'>
		<div class='row'>&nbsp;</div>
		<div id='!!objects_type!!_search_content_filters'>
			<div class='row'>
				<div class='row'>
					<label class='etiquette'>".$msg["list_ui_filters"]."</label>
				</div>
			    <div class='row'>
			        !!list_search_filters_form_tpl!!
			    </div>
			</div>
		</div>
		!!list_search_add_filter_form_tpl!!
		<div id='!!objects_type!!_search_content_order'>
			!!list_search_order_form_tpl!!
		</div>
		!!list_options_content_form_tpl!!
		!!list_settings_content_form_tpl!!
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='!!objects_type!!_pager'>".$msg['list_pager']."</label>
		</div>
		<div class='row'>
			".$msg['per_page']." <input type='number' class='saisie-5em' name='!!objects_type!!_nb_per_page' id='!!objects_type!!_nb_per_page' value='!!nb_per_page!!' !!all_on_page!! />
		</div>
		<div class='row'>
			".$msg['pager_position']." !!pager_position!!
		</div>
	</div>
	<div class='row'>
		<div class='left'>
            <input type='hidden' id='!!objects_type!!_selected_filters' name='!!objects_type!!_selected_filters' value='!!selected_filters!!' />
			<input type='button' class='bouton' value='".$msg['76']."'  onclick=\"document.location='!!cancel_action!!'\"  />
			<input type='submit' class='bouton' value='".$msg['77']."' />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	require(['dojo/ready', 'apps/list/ManageSearch'], function(ready, ManageSearch) {
		 ready(function(){
			new ManageSearch('!!objects_type!!');
		});
	});
	ajax_parse_dom();
</script>
";