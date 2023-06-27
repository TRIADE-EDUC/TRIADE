<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_status.tpl.php,v 1.2 2019-05-27 10:32:59 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $admin_contribution_area_status_form, $base_path, $current_module, $msg;

//statuts de contribution
$admin_contribution_area_status_form = "
<form class='form-$current_module' name='statusform' method=post action=\"".$base_path."/modelling.php?categ=contribution_area&sub=status&action=update&id=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_libelle'>".$msg["docnum_statut_libelle"]."</label>
	</div>
	<div class='row'>
		<input type=text name='form_gestion_libelle' value='!!gestion_libelle!!' class='saisie-50em' />
	</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne5'>
			<label class='etiquette' for='form_class_html'>".$msg["docnum_statut_class_html"]."</label>
		</div>
		<div class='colonne_suite'>
			!!class_html!!
		</div>
	</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne5'>
			<label class='etiquette' for='form_used_for'>".$msg["authorities_used_for"]."</label>
		</div>
		<div class='colonne_suite'>
			!!list_entities!!
		</div>
	</div>
	<div class='row'>&nbsp;</div>
	</div>
	<!-- Boutons -->
	<div class='row'>
		<div class='left'>
			<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='".$base_path."/modelling.php?categ=contribution_area&sub=status&action='\">&nbsp;
			<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
		<div class='right'>
			!!bouton_supprimer!!
		</div>
	</div>
	<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['statusform'].elements['form_gestion_libelle'].focus();</script>";