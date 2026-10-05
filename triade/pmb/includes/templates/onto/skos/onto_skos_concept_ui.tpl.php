<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_ui.tpl.php,v 1.30 2019/10/14 09:28:44 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

$ontology_tpl['scheme_list_selector']='
	<select !!scheme_list_selector_onchange!! name="!!scheme_list_selector_name!!" id="!!scheme_list_selector_id!!" !!multiple!! class="saisie-30em">
		!!scheme_list_selector_options!!
	</select>
';

$ontology_tpl['scheme_list_selector_option']='
	<option !!scheme_list_selector_options_selected!! value="!!scheme_list_selector_options_value!!">!!scheme_list_selector_options_label!!</option>
';


$ontology_tpl['scheme_radio_selector']='
	<input type="radio" name="!!scheme_list_selector_name!!" !!scheme_list_selector_options_selected!! !!scheme_list_selector_onchange!! 
		id="!!scheme_list_selector_name!!_!!scheme_list_selector_options_value!!" value="!!scheme_list_selector_options_value!!">
		<label for="!!scheme_list_selector_name!!_!!scheme_list_selector_options_value!!">!!scheme_list_selector_options_label!!</label>
';



$ontology_tpl['skos_concept_list']='
<div class="row">
	<script type="javascript" src="./javascript/sorttable.js"></script>
	<table class="sortable">
		<tr>
			<th></th>
			<th>!!list_header!!</th>
			<th>!!list_header_utilisation!!</th>
            <th></th>
		</tr>
		!!list_content!!
	</table>
	!!list_pagination!!
</div>
';

$ontology_tpl['skos_concept_list_line_doc']='
<tr>
	<td style="text-align:center; width:25px;">
		<a title="'.$msg['authority_list_see_label'].'" href="!!list_line_link_see!!">
			<i class="fa fa-eye"></i>
		</a>
	</td>
	<td>
		<img style="border:0px; margin:3px 3px" src="'.get_url_icon('doc.gif').'">
		!!statut!!		
		<a !!list_line_onclick!! href="!!list_line_href!!" title="!!list_line_title!!">!!list_line_libelle!!</a>
	</td>
	<td onmousedown="document.location=\'!!list_line_nb_utilisations_href!!\'">
		!!list_line_nb_utilisations!!
	</td>
    <td>
		!!list_line_basket!!
	</td>
</tr>
';

$ontology_tpl['skos_concept_list_line_folder']='
<tr>
	<td style="text-align:center; width:25px;">
		<a title="'.$msg['authority_list_see_label'].'" href="!!list_line_link_see!!">
			<i class="fa fa-eye"></i>
		</a>
	</td>
	<td>
		<a class="skos_concept_list_line_folder_icon" href="!!list_line_folder_href!!"><img hspace="3" border="0" src="'.get_url_icon('folderclosed.gif').'"></a>
		!!statut!!		
		<a !!list_line_onclick!! href="!!list_line_href!!" title="!!list_line_title!!">!!list_line_libelle!!</a>
	</td>
	<td onmousedown="document.location=\'!!list_line_nb_utilisations_href!!\'">
		!!list_line_nb_utilisations!!
	</td>
    <td>
		!!list_line_basket!!
	</td>
</tr>
';

$ontology_tpl['skos_concept_search_form']='
<form action="!!skos_concept_search_form_action!!" method="post" name="search" class="form-autorites" onsubmit="check_submit();">
	<h3>'.$msg['357'].' : !!skos_concept_search_form_title!!</h3>
	<div class="form-contenu">
		<div class="row">
			<div class="colonne3">
	            <!-- sel_authority_statuts -->
			</div>
			<div class="colonne_suite">
				<input id="id_user_input" type="text" value="!!skos_concept_search_form_user_input!!" name="user_input" class="saisie-50em">
			</div>
		</div>
		<div class="row">
			<div class="colonne3">
				<input type="checkbox" id="only_top_concepts" name="only_top_concepts" !!only_top_concepts_onchange!! !!only_top_concepts_checked!!>
				<label for="only_top_concepts">'.$msg['onto_skos_concept_only_top_concepts'].'</label>
			</div>
			<div class="colonne_suite">
				!!skos_concept_search_form_selector!!
			</div>
		</div>
		<div class="row">
		</div>
		<input type="hidden" name="skos_concept_search_form_submitted" value="1"/>
	</div>
	<div class="row">
		<div class="left">
			<input type="submit" onclick="return test_form(this.form)" value="Rechercher" class="bouton">
			<input type="button" class="bouton" onclick="!!skos_concept_search_form_concept_onclick!!" value="!!skos_concept_search_form_concept_value!!"/>
			<input type="button" class="bouton" onclick="!!skos_concept_search_form_composed_onclick!!" value="'.$msg['onto_add_composed_concept'].'"/>
		</div>
		<div class="right">
			<a id="skos_concept_search_form_last_concepts_link" href="!!skos_concept_search_form_last_concepts_link!!">'.$msg['onto_skos_show_last_concepts'].'</a>
            <!-- imprimer_concepts -->
		</div>		
		<div class="row"> </div>			
	</div>
</form>
<script type="text/javascript">
	document.forms["search"].elements["user_input"].focus();
	//c\'est appellé par le onchange du sélecteur de statut...
	// pas hyper générique mais ca reste efficace				
	function check_submit(){
		var user_input = document.getElementById("id_user_input");
		if(user_input.value === ""){
			user_input.value = "*";
		}
	}
</script>
<div class="row"></div>
<div class="skos_concept_search_form_breadcrumb row">
	<a href="!!skos_concept_search_form_href!!"><img style="border:0px; margin:3px 3px" class="align_middle" src="'.get_url_icon('top.gif').'"></a>
	!!skos_concept_search_form_breadcrumb!!
	<hr>
</div>
';

$ontology_tpl['skos_concept_list_selector_line_folder']="
<tr>
	<td>
		<a class='skos_concept_list_line_folder_icon' href='!!folder_href!!'><img hspace='3' border='0' src='".get_url_icon('folderclosed.gif')."'></a>
		!!statut!!
		<a href='#' onclick=\"set_parent('!!caller!!', '!!element!!', '!!uri!!', '!!item!!', '!!range!!', '!!callback!!')\" title=\"!!infobulle_libelle!!\">!!item_libelle!!</a>
	</td>
</tr>";
$ontology_tpl['skos_concept_list_selector_line_doc']="
<tr>
	<td>
		<img hspace='3' border='0' src='".get_url_icon('doc.gif')."'>
		!!statut!!
		<a href='#' onclick=\"set_parent('!!caller!!', '!!element!!', '!!uri!!', '!!item!!', '!!range!!', '!!callback!!')\" title=\"!!infobulle_libelle!!\">!!item_libelle!!</a>
	</td>
</tr>";

$ontology_tpl['skos_concept_selector_search_form_add'] = "<input type='button' class='bouton' value='!!add_button_label!!' onclick='!!add_button_onclick!!'/>";

$ontology_tpl['skos_concept_selector_search_form']="
<form name='search_form' method='post' action='!!base_url!!'>
	!!skos_concept_search_form_selector!!
<div class='row'>
	<input type='checkbox' id='only_top_concepts' name='only_top_concepts' !!only_top_concepts_onchange!! !!only_top_concepts_checked!!>
	<label for='only_top_concepts'>".$msg['onto_skos_concept_only_top_concepts']."</label>
</div>
	<input id='id_deb_rech' type='text' name='deb_rech' value=\"!!deb_rech!!\">
	&nbsp;
	<input type='submit' class='bouton_small' value='$msg[142]' />&nbsp;
	!!button_add!!
</form>
<script type='text/javascript'>
	if(document.forms['search_form'].elements['deb_rech']){
		document.forms['search_form'].elements['deb_rech'].focus()
	}
	function check_submit(){
		var user_input = document.getElementById('id_deb_rech');
		if(user_input.value === ''){
			user_input.value = '*';
		}
	}
</script>
<div class='row'></div>
	<div class='row skos_concept_search_form_breadcrumb'>
		<a href='!!skos_concept_search_form_href!!'><img hspace='3' border='0' class='align_middle' src='".get_url_icon('top.gif')."'></a>
		!!skos_concept_selector_breadcrumb!!
	</div>
<hr />";