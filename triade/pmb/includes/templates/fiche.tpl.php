<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fiche.tpl.php,v 1.12 2019/08/26 15:09:21 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $form_edit_fiche, $form_reindex, $form_search, $current_module, $msg, $charset, $base_path;

$form_edit_fiche = "
<script src='javascript/ajax.js'></script>		
<form class='form-$current_module' name='formulaire' action='!!form_action!!' method='post'>
	<input type='hidden' name='act' value='' />
	<input type='hidden' id='idfiche' name='idfiche' value='!!hidden_id!!' />
	<h3>!!form_titre!!
		&nbsp;
		<img !!visibility_prec!! !!action_prec!! src='".get_url_icon('left.gif')."' alt='".htmlentities($msg['fiche_precedente'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['fiche_precedente'],ENT_QUOTES,$charset)."' hspace='6' class='align_top' border='0' />
		<img !!visibility_suiv!! !!action_suiv!! src='".get_url_icon('right.gif')."' alt='".htmlentities($msg['fiche_suivante'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['fiche_suivante'],ENT_QUOTES,$charset)."' hspace='6' class='align_top' border='0' />
	</h3>
	
	<div class='form-contenu'>
		!!perso_fields!!
	</div>
	<div class='row'>
		<div class='left'>
			!!btn_cancel!!
			!!btn!!
		</div>
		<div class='right'>
			!!btn_del!!
		</div>
	</div>
	<div class='row'></div>
</form>
<!-- focus -->
";


$form_reindex = "
<form class='form-$current_module' name='formulaire' action='$base_path/fichier.php?categ=gerer&mode=reindex&sub=run' method='post'>
	<h3>".htmlentities($msg['fichier_reindex_title'],ENT_QUOTES,$charset)."</h3>
	<input type='hidden' name='act' value='' />
	<div class='form-contenu'>
	".htmlentities($msg['fichier_reindex_howto'],ENT_QUOTES,$charset)."
	</div>
	<div class='row'>
		<input type='submit' class='bouton' value='".htmlentities($msg['fichier_reindex_run'],ENT_QUOTES,$charset)."' onclick='this.form.act.value=\"run\";' />
	</div>
</form>
";

$form_search = "
<form class='form-$current_module' name='formulaire' action='$base_path/fichier.php?categ=consult&mode=search' method='post'>
	<h3>".htmlentities($msg['fichier_search_list'],ENT_QUOTES,$charset)."</h3>
	<input type='hidden' name='act' value='' />
	<input type='hidden' name='dest' value='' />
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette'>".htmlentities($msg['fichier_saisie_label'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' name='perso_word' class='saisie-50em' value='!!perso_word!!' />			
		</div>
	</div>
	<div class='row'>
		<label class='etiquette'>".htmlentities($msg['fiche_export_limite_page1'],ENT_QUOTES,$charset)."</label>
		<input type=\"text\" name=\"nb_per_page\" value=\"!!nb_per_page!!\" class=\"saisie-5em\"/>
		<label class='etiquette'>".htmlentities($msg['fiche_export_limite_page2'],ENT_QUOTES,$charset)."</label>
		<input type='submit' class='bouton' value='".htmlentities($msg[142],ENT_QUOTES,$charset)."' onclick='this.form.act.value=\"search\";this.form.dest.value=\"\";' />
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='image' src='".get_url_icon('tableur.gif')."' border='0' onClick=\"this.form.dest.value='TABLEAU';\" alt='".htmlentities($msg["fiche_export_excel"],ENT_QUOTES,$charset)."' title='".htmlentities($msg["fiche_export_excel"],ENT_QUOTES,$charset)."' />
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='image' src='".get_url_icon('tableur_html.gif')."' border='0' onClick=\"this.form.dest.value='TABLEAUHTML';\" alt='".htmlentities($msg["fiche_export_tableau"],ENT_QUOTES,$charset)."' title='".htmlentities($msg["fiche_export_tableau"],ENT_QUOTES,$charset)."' />
	</div>
</form>
<div class='row'>
	<b>!!message_result!!</b>
</div>
";

