<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_ui.tpl.php,v 1.7 2022/11/10 15:02:54 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

$ontology_tpl['list']='
<div class="row">
	<script type="javascript" src="./javascript/sorttable.js"></script>
	<table class="sortable">
		<tr>	
            <th></th>
			<th>!!list_header!!</th>
		</tr>
		!!list_content!!
	</table>
	<div class="row">
		<input type="button" class="bouton" onclick="!!list_onclick!!" value="!!list_value!!"/>	
	</div>
	!!list_pagination!!
</div>	
';

$ontology_tpl['list_line']='
<tr>
    <td style="text-align:center; width:25px;">
		<a title="'.$msg['authority_list_see_label'].'" href="!!list_line_link_see!!">
			<i class="fa fa-eye"></i>
		</a>
	</td>
	<td>
		<a href="!!list_line_href!!">!!list_line_libelle!!</a>
	</td>
</tr>
';

$ontology_tpl['list_assertions'] = "
<br />
<div class='row'>
	<div class='colonne10'>
		<img src='".get_url_icon('alert.gif')."' class='align_left'/>
	</div>
	<div class='erreur colonne80'>".$msg["onto_common_warning_object_in_assertions"]."</div>
	<table>
		<tr>
			<th>".$msg["onto_common_assertion_subject"]."</th>
			<th>".$msg["onto_common_assertion_predicate"]."</th>
			<th>".$msg["onto_common_assertion_object"]."</th>
		</tr>
		!!list_content!!
	</table>
</div>
<div class='row'>
	<input type='button' class='bouton' value='".$msg["76"]."' onClick='document.location=\"!!href_cancel!!\"'>
	<input type='button' class='bouton' value='".$msg["autorite_suppr_categ_forcage_button"]."' onClick='document.location=\"!!href_continue!!\"'>
</div>
";

$ontology_tpl['list_assertions_line'] = '
<tr>
	<td>!!assertion_subject!!</td>
	<td>!!assertion_predicate!!</td>
	<td>!!assertion_object!!</td>
</tr>
';

$ontology_tpl['search_form']='
<form action="!!search_form_action!!" method="post" name="search" class="form-autorites" onsubmit="check_submit();">
	<h3>'.$msg['357'].' : !!search_form_title!!</h3>
	<div class="form-contenu">
		<div class="row">
			<div class="colonne3">
	            <label for="user_input">'.$msg['global_search'].'</label>
			</div>
			<div class="colonne_suite">
				<input id="id_user_input" type="text" value="!!search_form_user_input!!" name="user_input" class="saisie-50em">
			</div>
		</div>
		<div class="row">
		</div>
	</div>
	<div class="row">
		<div class="left">
			<input type="submit" onclick="return test_form(this.form)" value="Rechercher" class="bouton">
			<input type="button" class="bouton" onclick="!!search_form_add_value_onclick!!" value="!!search_form_add_value!!"/>
			
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
';