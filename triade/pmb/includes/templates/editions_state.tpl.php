<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state.tpl.php,v 1.5 2021/03/15 09:02:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $editions_state_content_form, $current_module, $base_path, $msg, $editions_state_form_tabs, $charset, $edition_state_render, $edition_state_filter_form;

$editions_state_content_form="
<div class='row'>
	<div class='colonne2'>
		<div class='row'>
			<label class='etiquette' for='editions_state_name'>".$msg["editions_state_form_name"]."</label>
		</div>
		<div class='row'>
			<input type='text' name='editions_state_name' value='!!name!!' maxlength='255' class='saisie-50em' />
		</div>
	</div>
	<div class=colonne_suite>
		<div class='row'>
			<label class='etiquette' for='editions_state_classement'>$msg[proc_clas_proc]</label>
		</div>
		<div class='row'>
			!!classement!!
		</div>
	</div>
</div>
<div class='row'>
	<div class='row'>
		<label for='editions_state_datasource'>".$msg['editions_state_datasource']."</label>
		<select name='editions_state_datasource' id='editions_state_datasource' onchange='load_tab_content(this.value)' !!datasource_readonly!!>
			!!datasource_options!!
		</select>
		<!--editions_state_datasource-->
		<script type='text/javascript'>
			function load_tab_content(datasource){
				if(datasource!=0){
					document.forms['editions_state_form'].action = '".$base_path."/edit.php?categ=state&action=edit&id=!!id!!';
					document.forms['editions_state_form'].partial_submit.value = 2;
					document.forms['editions_state_form'].submit();
				}
			}
			function test_form(form) {
				if(form.editions_state_name.value.length == 0) {
					alert('".addslashes($msg["editions_state_name_isempty"])."');
					return false;
				}
				if(document.getElementById('editions_state_datasource').value == 0) {
					alert('".addslashes($msg["editions_state_source_isempty"])."');
					return false;
				}
				return true;
			}
		</script>
	</div>
</div>
<div class='row'>&nbsp;</div>
<input type='hidden' name='editionsstate_active_tab' id='editionsstate_active_tab'value='!!active_tab!!'/>
!!tabs!!
<div class='row'>
	<label class='etiquette' for='form_comment'>$msg[707]</label>
</div>
<div class='row'>
	<input type='text' name='editions_state_comment' value='!!comment!!' maxlength='255' class='saisie-50em' />
</div>
<input type='hidden' name='partial_submit' value='0'/>
";

$editions_state_form_tabs = "
		<script type='text/javascript' src='".$javascript_path."/editions_state_dnd.js'></script>
		<div class='row'>
			<div id='content_onglet_perio'>
				<span id='editions_fields' class='onglet-perio-selected'>
					<a href='#' onClick='load_editions_tab(\"fields\");return false;'>".$msg['editions_state_fields_label']."</a>
				</span>
				<span id='editions_filters' class='onglets-perio'>
					<a href='#' onClick='load_editions_tab(\"filters\");return false;'>".$msg['editions_state_filter_label']."</a>
				</span>
				<span id='editions_order' class='onglets-perio'>
					<a href='#'  onClick='load_editions_tab(\"order\");return false;'>".$msg['editions_state_order_label']."</a>
				</span>
			</div>
			<div class='bulletins-perio' id='fields_tab'>
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<div id='fields_fields' class='colonne5' recept='yes' recepttype='editionsstatefieldslist' highlight='editionsstate_highlight' downlight='editionsstate_downlight'>
						<div class='row'>".htmlentities($msg["editions_datasource_tpl_champ"],ENT_QUOTES,$charset)."</div>
						!!fields_fields_list!!
					</div>
					<div class='colonne_suite'>
						<div id='fields_fields_content' recept='yes' recepttype='editionsstatefields' highlight='editionsstate_highlight' downlight='editionsstate_downlight'>
							<div class='row'>".htmlentities($msg["editions_datasource_tpl_depot_champ"],ENT_QUOTES,$charset)."</div>
							!!fields_fields_content!!
						</div>
					</div>	
				</div>
				<div class='row'>&nbsp;</div>
			</div>
			<div class='bulletins-perio' id='filters_tab' style='display:none;'>
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<div id='filters_fields' class='colonne5' recept='yes' recepttype='editionsstatefilterslist' highlight='editionsstate_highlight' downlight='editionsstate_downlight'>
						<div class='row'>".htmlentities($msg["editions_datasource_tpl_filtre"],ENT_QUOTES,$charset)."</div>
						!!filters_fields_list!!
					</div>
					<div class='colonne_suite'>
						<div  id='filters_fields_content' recept='yes' recepttype='editionsstatefilters' highlight='editionsstate_highlight' downlight='editionsstate_downlight'>
							<div class='row' style='width:100%;'>".htmlentities($msg["editions_datasource_tpl_depot_filtre"],ENT_QUOTES,$charset)."</div>
							!!filters_fields_content!!
						</div>
					</div>	
				</div>
				<div class='row'>&nbsp;</div>
			</div>
			<div class='bulletins-perio' id='order_tab' style='display:none;'>
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<div class='colonne5' id='order_fields' recept='yes' recepttype='editionsstateorderslist' highlight='editionsstate_highlight' downlight='editionsstate_downlight'>
						<div class='row'>".htmlentities($msg["editions_datasource_tpl_tri"],ENT_QUOTES,$charset)."</div>
						!!order_fields_list!!
					</div>
					<div class='colonne_suite'>
						<div id='order_fields_content' style='width:100%;' recept='yes' recepttype='editionsstateorders' highlight='editionsstate_highlight' downlight='editionsstate_downlight'>
							<div class='row'>".htmlentities($msg["editions_datasource_tpl_depot_tri"],ENT_QUOTES,$charset)."</div>
							!!order_fields_content!!
						</div>
					</div>	
				</div>
				<div class='row'>&nbsp;</div>
			</div>
			<script type='text/javascript'>
				load_editions_tab(document.getElementById('editionsstate_active_tab').value);
				function load_editions_tab(tab){
					switch(tab){
						case 'filters':
							document.getElementById('editions_fields').className = 'onglets-perio';
							document.getElementById('fields_tab').style.display = 'none';
							document.getElementById('fields_fields').style.display = 'none';
							document.getElementById('fields_fields_content').style.display = 'none';
							document.getElementById('editions_filters').className = 'onglet-perio-selected';
							document.getElementById('filters_tab').style.display = 'block';
							document.getElementById('filters_fields').style.display = 'block';
							document.getElementById('filters_fields_content').style.display = 'block';
							document.getElementById('editions_order').className = 'onglets-perio';
							document.getElementById('order_tab').style.display = 'none';
							document.getElementById('order_fields').style.display = 'none';
							document.getElementById('order_fields_content').style.display = 'none';
							break;
						case 'order':
							document.getElementById('editions_fields').className = 'onglets-perio';
							document.getElementById('fields_tab').style.display = 'none';
							document.getElementById('fields_fields').style.display = 'none';
							document.getElementById('fields_fields_content').style.display = 'none';
							document.getElementById('editions_filters').className = 'onglets-perio';
							document.getElementById('filters_tab').style.display = 'none';
							document.getElementById('filters_fields').style.display = 'none';
							document.getElementById('filters_fields_content').style.display = 'none';
							document.getElementById('editions_order').className = 'onglet-perio-selected';
							document.getElementById('order_tab').style.display = 'block';
							document.getElementById('order_fields').style.display = 'block';
							document.getElementById('order_fields_content').style.display = 'block';
							break;
						case 'fields':
						default : 
							tab = 'fields';
							document.getElementById('editions_fields').className = 'onglet-perio-selected';
							document.getElementById('fields_tab').style.display = 'block';
							document.getElementById('fields_fields').style.display = 'block';
							document.getElementById('fields_fields_content').style.display = 'block';
							document.getElementById('editions_filters').className = 'onglets-perio';
							document.getElementById('filters_tab').style.display= 'none';
							document.getElementById('filters_fields').style.display = 'none';
							document.getElementById('filters_fields_content').style.display = 'none';
							document.getElementById('editions_order').className = 'onglets-perio';
							document.getElementById('order_tab').style.display = 'none';
							document.getElementById('order_fields').style.display = 'none';
							document.getElementById('order_fields_content').style.display = 'none';
							break;	
					}
					document.getElementById('editionsstate_active_tab').value=tab;
					recalc_recept();
				}
			</script>
		</div>
		<div class='row'>&nbsp;</div>";

$edition_state_render = "
<div class='row'>&nbsp;</div>
<h3>!!name!!</h3>
<form class='form-$current_module' name='editions_state_form_show' method='post' action='".$base_path."/edit.php?categ=state&action=show&id=!!id!!'>	
	<input type='hidden' name='sub'  id='sub' value='tab'/>		
	<input type='hidden' name='elem' id='elem' value=''/>
	<input type='hidden' name='show_all' id='show_all' value=''/>
	<!-- filter_form_content -->

<script type='text/javascript'>
	function test_form(sub,param){
		document.getElementById('sub').value=sub;
		if(param && (param == 'edit')){
			document.getElementById('elem').value='xls';
			document.getElementById('show_all').value='';
			document.editions_state_form_show.action='".$base_path."/ajax.php?module=edit&categ=editions_state&id=!!id!!';
			document.editions_state_form_show.target='_blank';
		}else if(param && (param == 'show_all')){
			document.getElementById('elem').value='';
			document.getElementById('show_all').value='1';
			document.editions_state_form_show.target='';
			document.editions_state_form_show.action='".$base_path."/edit.php?categ=state&action=show&id=!!id!!';
		}else{
			document.getElementById('elem').value='';
			document.getElementById('show_all').value='';
			document.editions_state_form_show.target='';
			document.editions_state_form_show.action='".$base_path."/edit.php?categ=state&action=show&id=!!id!!';
		}
		document.editions_state_form_show.submit();
	}
		
</script>
<div class='row'>
	<div id='content_onglet_perio'>
		<span id='editions_state_tab' class='!!class_tab!!'>
			<a onclick='test_form(\"tab\");'>".$msg['editions_state_tab']."</a>
		</span>
		<span id='editions_state_tcd' class='!!class_tcd!!'>
			<a onclick='test_form(\"tcd\");'>".$msg['editions_state_tcd']."</a>
		</span>
		<span id='editions_state_group' class='!!class_group!!'>
			<a onclick='test_form(\"group\");'>".$msg['editions_state_group']."</a>
		</span>
		<!--<span id='editions_state_graph' class='!!class_graph!!'>
			<a href='./edit.php?categ=state&action=show&id=!!id!!&sub=graph'>".$msg['editions_state_graph']."</a>
		</span>-->
	</div>
	<div class='bulletins-perio'>
		!!editions_state_render!!
	</div>
</div>
</form>
";

$edition_state_filter_form="		
<h3>".$msg['editions_state_filter_infos']."</h3>
<div class='form-contenu'>	
	<!-- filter_form -->
	<div class='row'>
		<input type='button' class='bouton' value='".htmlentities($msg["sauv_list_filtrer"],ENT_QUOTES,$charset)."' onclick=\"test_form('!!sub!!');\" />
	</div>
</div>	
";
