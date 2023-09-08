<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: threshold.tpl.php,v 1.3 2019-05-27 10:34:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $threshold_form_tpl, $msg, $current_module, $base_path, $charset, $pmb_gestion_devise;

$threshold_form_tpl = "
<script type='text/javascript'>
	function threshold_delete() {
		return confirm(\"".addslashes($msg['threshold_delete_confirm'])."\");
	}	
</script>
<form class='form-".$current_module."' name='threshold_form' method='post' action='".$base_path."/admin.php?categ=acquisition&sub=thresholds&action=save&id=!!id!!' >
	<h3>".htmlentities($msg['threshold_form_edit'], ENT_QUOTES, $charset)."</h3>
	<!--	Contenu du form	-->
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg['threshold_entity'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite' >
				<b>!!entity_label!!</b>
				<input type='hidden' id='threshold_num_entity' name='threshold_num_entity' value='!!num_entity!!' />
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg['threshold_label'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite' >
				<input type='text' id='threshold_label' name='threshold_label' class='saisie-30em' value='!!label!!' />
			</div>
		</div>
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg['threshold_amount'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite' >
				<input type='text' id='threshold_amount' name='threshold_amount' class='saisie-10em' value='!!amount!!' /> ".$pmb_gestion_devise."
			</div>
		</div>
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg['threshold_amount_tax_included'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite' >
				<input type='checkbox' id='threshold_amount_tax_included' name='threshold_amount_tax_included' value='1' !!amount_tax_included!! />
			</div>
		</div>
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg['threshold_footer'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite' >
				<textarea id='threshold_footer' name='threshold_footer' cols='55' rows='10'>!!footer!!</textarea>
			</div>
		</div>
	</div>
	<!-- Boutons -->
	<div class='row'>
		<div class='left'>
			<input class='bouton' type='button' value=' $msg[76] ' onclick=\"history.go(-1);\" />&nbsp;
			<input class='bouton' type='submit' value=' $msg[77] '/>
		</div>
		<div class='right'>
			!!button_delete!!
		</div>
		<div class='row'></div>
	</div>
</form>";