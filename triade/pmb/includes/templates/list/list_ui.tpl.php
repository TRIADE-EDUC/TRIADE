<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_ui.tpl.php,v 1.21 2019-05-27 10:10:32 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//	------------------------------------------------------------------------------
//	$list_ui_search_form_tpl : template de recherche pour les listes
//	------------------------------------------------------------------------------
global $list_ui_search_form_tpl, $base_path, $current_module, $msg, $charset, $list_ui_search_hidden_form_tpl, $list_ui_options_content_form_tpl;
global $list_ui_datasets_content_form_tpl, $list_ui_js_sort_script_sort, $list_ui_search_add_filter_form_tpl, $list_ui_search_order_form_tpl, $list_dataset_form_tpl;

$list_ui_search_form_tpl = "
<script src='".$base_path."/javascript/ajax.js'></script>
<form class='form-".$current_module."' id='!!form_name!!' name='!!form_name!!' method='post' action=\"!!action!!\" >
	<h3>!!form_title!!</h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>
		<div id='!!objects_type!!_search_label' class='list_ui_search_label !!objects_type!!_search_label'>
			<img src='".get_url_icon('minus.gif')."' class='img_plus' name='imEx' id='!!objects_type!!_search_img' title='".$msg['plus_detail']."' border='0' hspace='3'>
			<span class='list_ui_search_label_text'>
				<label>".htmlentities($msg['list_ui_search'], ENT_QUOTES, $charset)."</label>
			</span>
		</div>
		<div id='!!objects_type!!_search_content' class='list_ui_search_content !!objects_type!!_search_content'>
			!!list_search_content_form_tpl!!
		</div>
		!!list_options_content_form_tpl!!
		!!list_datasets_my_content_form_tpl!!
		!!list_datasets_shared_content_form_tpl!!
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
				<input type='hidden' id='!!objects_type!!_json_filters' name='!!objects_type!!_json_filters' value='!!json_filters!!' />
				<input type='hidden' id='!!objects_type!!_json_selected_columns' name='!!objects_type!!_json_selected_columns' value='!!json_selected_columns!!' />
				<input type='hidden' id='!!objects_type!!_json_applied_group' name='!!objects_type!!_json_applied_group' value='!!json_applied_group!!' />
				<input type='hidden' id='!!objects_type!!_json_applied_sort' name='!!objects_type!!_json_applied_sort' value='!!json_applied_sort!!' />
				<input type='hidden' id='!!objects_type!!_page' name='!!objects_type!!_page' value='!!page!!' />
				<input type='hidden' id='!!objects_type!!_nb_per_page' name='!!objects_type!!_nb_per_page' value='!!nb_per_page!!' />
				<input type='hidden' id='!!objects_type!!_pager' name='!!objects_type!!_pager' value='!!pager!!' />
				<input type='hidden' id='!!objects_type!!_selected_filters' name='!!objects_type!!_selected_filters' value='!!selected_filters!!' />
				<input type='hidden' id='!!objects_type!!_initialization' name='!!objects_type!!_initialization' value='' />
				<input type='submit' id='!!objects_type!!_button_search' class='bouton' value='".$msg['search']."' />&nbsp;
				!!list_button_add!!
				!!list_button_save!!
				!!list_button_initialization!!
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
	<input type='hidden' id='!!objects_type!!_json_filters' name='!!objects_type!!_json_filters' value='!!json_filters!!' />
	<input type='hidden' id='!!objects_type!!_json_selected_columns' name='!!objects_type!!_json_selected_columns' value='!!json_selected_columns!!' />
	<input type='hidden' id='!!objects_type!!_json_applied_group' name='!!objects_type!!_json_applied_group' value='!!json_applied_group!!' />
	<input type='hidden' id='!!objects_type!!_json_applied_sort' name='!!objects_type!!_json_applied_sort' value='!!json_applied_sort!!' />
	<input type='hidden' id='!!objects_type!!_page' name='!!objects_type!!_page' value='!!page!!' />
	<input type='hidden' id='!!objects_type!!_nb_per_page' name='!!objects_type!!_nb_per_page' value='!!nb_per_page!!' />
	<input type='hidden' id='!!objects_type!!_pager' name='!!objects_type!!_pager' value='!!pager!!' />
	<input type='hidden' id='!!objects_type!!_selected_filters' name='!!objects_type!!_selected_filters' value='!!selected_filters!!' />
</form>
";

$list_ui_options_content_form_tpl = "
		<div class='row'>&nbsp;</div>
		<div id='!!objects_type!!_options_label' class='list_ui_options_label !!objects_type!!_options_label'>
			<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='!!objects_type!!_options_img' title='".$msg['plus_detail']."' border='0' hspace='3'>
			<span class='list_ui_options_label_text'>
				<label>".htmlentities($msg['list_ui_options'], ENT_QUOTES, $charset)."</label>
			</span>
		</div>
		<div id='!!objects_type!!_options_content' class='list_ui_options_content !!objects_type!!_options_content'>
			<div class='list_ui_options_columns'>
				<div class='list_ui_options_columns_available'>
					<label class='etiquette'>".$msg['list_ui_options_available_columns']."</label>
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
						<label class='etiquette'>".$msg['list_ui_options_selected_columns']."</label>
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
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='list_ui_options_group'>
					<span class='list_ui_options_group_label_text'>
						<label>".htmlentities($msg['list_ui_options_group_by'], ENT_QUOTES, $charset)."</label>
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

$list_ui_datasets_content_form_tpl = "
<div class='row'>&nbsp;</div>
<div id='!!objects_type!!_datasets_!!which!!_label' class='list_ui_datasets_label !!objects_type!!_datasets_label'>
	<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='!!objects_type!!_datasets_!!which!!_img' title='".$msg['plus_detail']."' border='0' hspace='3'>
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
			var url = './ajax.php?module=".$current_module."&categ=!!categ!!&sub=!!sub!!&action=!!action!!&sort_by='+criteria;
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
			req.request(url,1, 'object_type=!!objects_type!!&filters='+filters+'&pager='+pager);
			if(document.getElementById('!!objects_type!!_list_'+indice)) {
				var table = document.getElementById('!!objects_type!!_list_'+indice);
			} else if(document.getElementById('!!objects_type!!_list_0')) {
				var table = document.getElementById('!!objects_type!!_list_0');
			} else {
				var table = document.getElementById('!!objects_type!!_list');
			}
			table.innerHTML = req.get_text();
			if(document.getElementById('!!objects_type!!_applied_sort_by')) {
				var options = document.getElementById('!!objects_type!!_applied_sort_by').options;
				for (var i= 0; i < options.length; i++) {
				    if (options[i].value === criteria) {
				        options[i].selected= true;
				        break;
				    }
				}
				switch(asc_desc) {
					case 'asc': //on repasse en tri décroissant
						document.getElementById('!!objects_type!!_applied_sort_asc').removeAttribute('checked');
						document.getElementById('!!objects_type!!_applied_sort_desc').setAttribute('checked', 'checked');
						break;
					case 'desc': //on repasse en tri croissant
					default:
						document.getElementById('!!objects_type!!_applied_sort_asc').setAttribute('checked', 'checked');
						document.getElementById('!!objects_type!!_applied_sort_desc').removeAttribute('checked');
						break;
				}
			}		
		}
	</script>
";

$list_ui_search_add_filter_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label>".htmlentities($msg['list_ui_add_filter'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<select id='!!objects_type!!_add_filter' name='!!objects_type!!_add_filter' onchange='this.form.submit();'>!!add_filter_options!!</select>
		</div>
	</div>
</div>
";

$list_ui_search_order_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["list_applied_sort"]."</label>
		</div>
		<div class='row'>
			<select id='!!objects_type!!_applied_sort_by' name='!!objects_type!!_applied_sort[by]'>!!order_options!!</select>
			<input type='radio' id='!!objects_type!!_applied_sort_asc' name='!!objects_type!!_applied_sort[asc_desc]' value='asc' !!applied_sort_asc!! /><label for='!!objects_type!!_applied_sort_asc'>".$msg["list_applied_sort_asc"]."</label>
			<input type='radio' id='!!objects_type!!_applied_sort_desc' name='!!objects_type!!_applied_sort[asc_desc]' value='desc' !!applied_sort_desc!! /><label for='!!objects_type!!_applied_sort_desc'>".$msg["list_applied_sort_desc"]."</label>
		</div>
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
		!!list_search_filters_form_tpl!!
		!!list_search_order_form_tpl!!
		!!list_options_content_form_tpl!!
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='!!objects_type!!_pager'>".$msg['list_pager']."</label>
		</div>
		<div class='row'>
			".$msg['per_page']." <input type='number' class='saisie-5em' name='!!objects_type!!_nb_per_page' id='!!objects_type!!_nb_per_page' value='!!nb_per_page!!' />
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);'>
			<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);'>
		</div>
		<div class='row'>
			<label class='etiquette' for='form_type'>".$msg['list_autorisations']." :</label><br />
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			!!autorisations_users!!
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='list_default_selected'>".$msg['list_default_selected']."</label>
			<input type='checkbox' name='list_default_selected' id='list_default_selected' value='1' !!default_selected!! />
		</div>
	</div>
	<div class='row'>
		<div class='left'>
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
";