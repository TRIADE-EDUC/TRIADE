<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: actes.tpl.php,v 1.22 2019-05-27 16:04:40 btafforeau Exp $


if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $search_form_actes, $current_module, $msg, $charset;

//	------------------------------------------------------------------------------
//	$search_form : template de recherche pour les actes
//	------------------------------------------------------------------------------
$search_form_actes = "
<form class='form-".$current_module."' id='search' name='search' method='post' action=\"!!action!!\">
	<h3>!!form_title!!</h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne2'>
				<label class='etiquette'>&nbsp;</label>
			</div>
			<div class='colonne2'>
				<label class='etiquette'><!-- sel_exercice_label --></label>
			</div>
		</div>
		<div class='row'>
			<div class='colonne2'>
				<input type='text' class='saisie-30em' id='user_input' name='user_input' value='!!user_input!!'/>
			</div>
			<div class='colonne2'>
				<!-- sel_exercice -->
			</div>
		</div>
		<div class='row'>
			<div class='colonne2'>
				<label class='etiquette'>".htmlentities($msg['acquisition_statut'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne2'>
				<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
			</div>
		</div>
		<div class='row'>
			<div class='colonne2'>
				<!-- sel_statut -->
			</div>
			<div class='colonne2'>
				<!-- sel_bibli -->
			</div>
		</div>		
		<div class='row'></div>
	</div>		
	<div class='row'>
		<div class='left'>
			<input type='submit' class='bouton' value='".$msg['142']."' />
			<!-- bouton_add -->
		</div>
	</div>
	<div class='row'><input type='hidden' id='sortBy' name='sortBy' value='!!sortBy!!'></div>
</form>
<br />
";

?>
