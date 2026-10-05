<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connecteurs.tpl.php,v 1.19 2023/12/06 08:48:07 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $admin_connecteur_global_params, $current_module, $msg, $admin_connecteur_source_global_params, $admin_connecteur_source_progress;

//template form connecteurs
$admin_connecteur_global_params="
<form name='connector_form' action='admin.php?categ=connecteurs&sub=in&act=update&id=!!id!!' method='post' class='form-$current_module'>
	<h3>".$msg["connecteurs_properties"]."</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'><label for='timeout'/>".$msg["connecteurs_timeout"]."</label></div><div class='colonne_suite'><input type='text' name='timeout' id='timeout' value='!!timeout!!' class='saisie-5em'/></div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='retry'/>".$msg["connecteurs_ntry"]."</label></div><div class='colonne_suite'><input type='text' name='retry' id='retry' value='!!retry!!' class='saisie-5em'/></div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='ttl'/>".$msg["connecteurs_timeavail"]."</label></div><div class='colonne_suite'><input type='text' name='ttl' id='ttl' value='!!ttl!!' class='saisie-5em'/></div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='repository'/>".$msg["connecteurs_repository"]."</label></div><div class='colonne_suite'>!!repository!!</div>
		</div>
		<div class='row'>	
			!!special_form!!
		</div>
	</div>
	<div class='row'><input type='button' value='".$msg["76"]."' class='bouton' onClick='history.go(-1);'/>&nbsp;<input type='submit' value='".$msg["77"]."' class='bouton'/></div> 
</form>
";

//template source
$admin_connecteur_source_global_params="
<div class='row'>
	<div class='colonne3'><label for='name'>".$msg["connecteurs_source_name"]."</label></div><div class='colonne_suite'><input type='text' name='name' id='name' value='!!name!!' class='saisie-30em'/></div>
</div>
<div class='row'>
	<div class='colonne3'><label for='categories'>".$msg["connecteurs_source_categories"]."</label></div><div class='colonne_suite'>!!categories!!</div>
</div>
<div class='row'>
	<div class='colonne3'><label for='opac_allowed'>".$msg["connecteurs_source_opac_allowed"]."</label></div><div class='colonne_suite'><input type='checkbox' name='opac_allowed' id='opac_allowed' value='1' !!opac_allowed_checked!! /></div>
</div>
<div class='row'>
	<div class='colonne3'><label for='opac_selected'>".$msg["connecteurs_source_opac_selected"]."</label></div><div class='colonne_suite'><input type='checkbox' name='opac_selected' id='opac_selected' value='1' !!opac_selected_checked!! /></div>
</div>
<div class='row'>
	<div class='colonne3'><label for='opac_affiliate_search'>".$msg["connecteurs_source_opac_affiliate_search"]."</label></div><div class='colonne_suite'><input type='checkbox' name='opac_affiliate_search' id='opac_affiliate_search' value='1' !!opac_affiliate_search!! /></div>
</div>
<div class='row'>
	<div class='colonne3'><label for='gestion_selected'>".$msg["connecteurs_source_gestion_selected"]."</label></div><div class='colonne_suite'><input type='checkbox' name='gestion_selected' id='gestion_selected' value='1' !!gestion_selected_checked!! /></div>
</div>
<div class='row'>
	<div class='colonne3'><label for='comment'>".$msg["connecteurs_source_desc"]."</label></div><div class='colonne_suite'><textarea class='saisie-30em' name='comment' id='comment'>!!comment!!</textarea></div>
</div>
<div class='row'>
	<div class='colonne3'><label for='timeout'>".$msg["connecteurs_timeout"]."</label></div><div class='colonne_suite'><input type='text' name='timeout' id='timeout' value='!!timeout!!' class='saisie-5em'/></div>
</div>
<div class='row'>
	<div class='colonne3'><label for='retry'>".$msg["connecteurs_ntry"]."</label></div><div class='colonne_suite'><input type='text' name='retry' id='retry' value='!!retry!!' class='saisie-5em'/></div>
</div>
<div class='row'>
	<div class='colonne3'><label for='ttl'>".$msg["connecteurs_timeavail"]."</label></div><div class='colonne_suite'><input type='text' name='ttl' id='ttl' value='!!ttl!!' class='saisie-5em'/></div>
</div>
<div class='row'>
	<div class='colonne3'><label for='repository'>".$msg["connecteurs_repository"]."</label></div><div class='colonne_suite'>!!repository!!</div>
</div>
<div class='row'>
	<div class='colonne3'><label for='rep_upload'>".$msg["connecteurs_rep_upload"]."</label></div><div class='colonne_suite'>!!rep_upload!!</div>
</div>
<div class='row'>
	<div class='colonne3'><label for='upload_doc_num'>".$msg["connecteurs_source_upload_doc_num"]."</label></div><div class='colonne_suite'><input type='checkbox' name='upload_doc_num' id='upload_doc_num' value='1' !!upload_doc_num!! /></div>
</div>
<div class='row'>
	<div class='colonne3'><label for='ico_notice'>".$msg["connecteurs_ico_notice"]."</label></div><div class='colonne_suite'><input type='text' name='ico_notice' id='ico_notice' value='!!ico_notice!!' class='saisie-30em'/></div>
</div>
!!enrichment!!
<div class='row'>	
	!!special_form!!
</div>
<input type='hidden' name='act' value='update_source'/>
";

$admin_connecteur_source_progress="";