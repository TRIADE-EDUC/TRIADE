<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_state.tpl.php,v 1.8 2019-05-27 16:55:44 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $serialcirc_state_list, $msg, $charset, $serialcirc_state_list_line, $serialcirc_state_filters_form, $current_module;

$serialcirc_state_list = "
	!!filters_form!!
		
	<table width='100%'>
		<tr>
			<th>
				".htmlentities($msg["1150"],ENT_QUOTES,$charset)."
			</th>
			<th>
				".htmlentities($msg["serialcirc_circ_list_bull_circulation_abonnement"],ENT_QUOTES,$charset)."
			</th>
			<th>
				".htmlentities($msg["379"],ENT_QUOTES,$charset)."
			</th>
			<th>
				".htmlentities($msg["adresse_empr"],ENT_QUOTES,$charset)."
			</th>
			<th>
				".htmlentities($msg["ville_empr"],ENT_QUOTES,$charset)."
			</th>
			<th>
				".htmlentities($msg["serial_circ_state_end_location"],ENT_QUOTES,$charset)."
			</th>
		</tr>
		!!lines!!
	</table>
	!!pagination!!
";

$serialcirc_state_list_line="
	<tr>
		<td>
			<a href='!!periodique_link!!' title='!!periodique!!'>!!periodique!!</a>
		</td>
		<td>
			<a href='!!abonnement_link!!' title='!!abonnement!!'>!!abonnement!!</a>
		</td>
		<td>
			<a href='!!empr_link!!' title='!!empr!!'>!!empr!!</a>
		</td>
		<td>
			!!address!!
		</td>
		<td>
			!!city!!
		</td>
		<td>
			!!end_location!!
		</td>
	</tr>
";

$serialcirc_state_filters_form = "
	<script src='./javascript/ajax.js'></script>
	<form class='form-$current_module' name='serialcirc_state_filters' method='post' action='!!form_action!!'>
		<div class='form-contenu'>
			<div class='colonne_suite'>
				<div class='row'>
					<label class='etiquette' for='serialcirc_state_location_filter'>".$msg['298']."</label>
				</div>
				<div class='row'>
					!!serialcirc_state_location_filter!!&nbsp;
				</div>
			</div>
			<div class='colonne_suite'>
				<div class='row'>
					<label class='etiquette' for='serialcirc_state_location_filter'>".$msg['396']."</label>
				</div>
				<div class='row'>
					!!serialcirc_state_caddie_filter!!&nbsp;
				</div>
			</div>
			<div class='colonne_suite'>
				<div class='row'>
					<label class='etiquette' for='serialcirc_state_perio_filter_label'>".$msg['1150']."</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30emr' value='!!perio_label!!' id='serialcirc_state_perio_filter_label' name='serialcirc_state_perio_filter_label' autocomplete='off' completion='perio' autfield='serialcirc_state_perio_filter_id'>
					<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=perio&caller=serialcirc_state_filters&param1=serialcirc_state_perio_filter_id&param2=serialcirc_state_perio_filter_label', 'selector_notice')\" title='".$msg['157']."' value='".$msg['parcourir']."' />
					<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.serialcirc_state_perio_filter_label.value=''; this.form.serialcirc_state_perio_filter_id.value='0'; \" />
					<input type='hidden' id='serialcirc_state_perio_filter_id' name='serialcirc_state_perio_filter_id' value='!!perio_id!!'>
				</div>
			</div>
			<div class='colonne_suite'>
				<div class='row'>
					<label class='etiquette' for='serialcirc_state_date_echeance_filter'>".$msg['serialcirc_state_date_echeance_filter']."</label>
				</div>
				<div class='row'>
					!!serialcirc_state_date_echeance_filter!!&nbsp;
				</div>
			</div>
			<input type='hidden' name='dest' value=''/>
			<div class='row'></div>
		</div>
		<div class='row'>
			<div class='left'>
				<input type='submit' class='bouton' value='".$msg['142']."'/>
			</div>
			<div class='right'>
				<img style='cursor:pointer;' src='".get_url_icon('tableur.gif')."' onclick='serialcirc_state_export(\"TABLEAU\");' alt='".$msg['caddie_choix_edition_TABLEAU']."' title='".$msg['caddie_choix_edition_TABLEAU']."'/>&nbsp;&nbsp;
				<img style='cursor:pointer;' src='".get_url_icon('tableur_html.gif')."' onclick='serialcirc_state_export(\"TABLEAUHTML\");' alt='".$msg['caddie_choix_edition_TABLEAUHTML']."' title='".$msg['caddie_choix_edition_TABLEAUHTML']."'/>&nbsp;&nbsp;
			</div>
		</div>
		<div class='row'></div>
	</form>
	<script type='text/javascript'>
		ajax_parse_dom();
						
		function serialcirc_state_export(dest) {
			document.forms['serialcirc_state_filters'].dest.value = dest;
			document.forms['serialcirc_state_filters'].submit();
			document.forms['serialcirc_state_filters'].dest.value = '';
		}
	</script>";