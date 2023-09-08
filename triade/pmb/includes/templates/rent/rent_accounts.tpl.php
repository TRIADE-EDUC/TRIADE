<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_accounts.tpl.php,v 1.16 2019-05-27 10:14:55 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//	------------------------------------------------------------------------------
//	$rent_accounts_search_form_tpl : template de recherche pour les décomptes
//	------------------------------------------------------------------------------

global $base_path, $charset, $current_module, $msg, $rent_accounts_search_form_tpl;

$rent_accounts_search_form_tpl = "
<script src='javascript/pricing_systems.js'></script>
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	var msg_acquisition_accounts_checked_empty = '".addslashes($msg['acquisition_accounts_checked_empty'])."';
	var msg_account_show_invoices_selector_title = '".addslashes($msg['account_show_invoices_selector_title'])."';
</script>
<form class='form-".$current_module."' id='accounts_search_form' name='accounts_search_form' method='post' action=\"".$base_path."/acquisition.php?categ=rent&sub=accounts\" >
	<h3>!!form_title!!</h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
				<br />
				!!selector_entities!!
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_budg_exer'], ENT_QUOTES, $charset)."</label>
				<br />
				!!selector_exercices!!
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_account_type_name'], ENT_QUOTES, $charset)."</label>
				<br />
				!!selector_types!!
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_account_num_publisher'], ENT_QUOTES, $charset)."</label>
				<br />
				<input type='text' id='accounts_search_form_publisher' autfield='accounts_search_form_num_publisher' completion='publishers' class='saisie-20emr' value='!!publisher!!' autocomplete='off' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=editeur&caller=accounts_search_form&p1=accounts_search_form_num_publisher&p2=accounts_search_form_publisher&deb_rech='+this.form.accounts_search_form_publisher.value, 'selector')\"/>
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.accounts_search_form_publisher.value=''; this.form.accounts_search_form_num_publisher.value='0'; \" />
				<input type='hidden' id='accounts_search_form_num_publisher' name='accounts_search_form_num_publisher' value='!!num_publisher!!' />
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_account_num_supplier'], ENT_QUOTES, $charset)."</label>
				<br />		
				<input type='text' id='accounts_search_form_supplier' autfield='accounts_search_form_num_supplier' completion='fournisseur' class='saisie-20emr' value='!!supplier!!' autocomplete='off' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=fournisseur&caller=accounts_search_form&param1=accounts_search_form_num_supplier&param2=accounts_search_form_supplier&id_bibli='+this.form.accounts_search_form_entities.value+'&deb_rech='+this.form.accounts_search_form_supplier.value, 'selector')\"/>
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.accounts_search_form_supplier.value=''; this.form.accounts_search_form_num_supplier.value='0'; \" />
				<input type='hidden' id='accounts_search_form_num_supplier' name='accounts_search_form_num_supplier' value='!!num_supplier!!' />
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_account_num_author'], ENT_QUOTES, $charset)."</label>
				<br />		
				<input type='text' id='accounts_search_form_author' autfield='accounts_search_form_num_author' completion='authors' class='saisie-20emr' value='!!author!!' autocomplete='off' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=auteur&caller=accounts_search_form&param1=accounts_search_form_num_author&param2=accounts_search_form_author&deb_rech='+this.form.accounts_search_form_author.value, 'selector')\"/>
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.accounts_search_form_author.value=''; this.form.accounts_search_form_num_author.value='0'; \" />
				<input type='hidden' id='accounts_search_form_num_author' name='accounts_search_form_num_author' value='!!num_author!!' />
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_account_invoiced_filter'], ENT_QUOTES, $charset)."</label>
				<br />
				!!selector_account_invoiced_filter!!
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_account_request_status'], ENT_QUOTES, $charset)."</label>
				<br />
				!!selector_request_status!!
			</div>
			<div class='colonne3'>
				<label class='etiquette' for='account_pricing_sytem'>".htmlentities($msg['acquisition_account_num_pricing_system'],ENT_QUOTES,$charset)."</label>
				<br />
				!!pricing_systems!!
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='colonne3'>
				<div class='row'>		
					<label class='etiquette'>".$msg['acquisition_account_event_date']."</label>
				</div>
				<div class='row'>
					<input type='text' name='accounts_search_form_event_date_start' id='accounts_search_form_event_date_start' value='!!event_date_start!!'  data-dojo-type='dijit/form/DateTextBox' required='false' />
					 - <input type='text' name='accounts_search_form_event_date_end' id='accounts_search_form_event_date_end' value='!!event_date_end!!'  data-dojo-type='dijit/form/DateTextBox' required='false' />
				</div>
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='hidden' id='accounts_json_filters' name='accounts_json_filters' value='!!json_filters!!' />
			<input type='hidden' id='accounts_page' name='accounts_page' value='!!page!!' />
			<input type='hidden' id='accounts_nb_per_page' name='accounts_nb_per_page' value='!!nb_per_page!!' />
			<input type='hidden' id='accounts_pager' name='accounts_pager' value='!!pager!!' />
			<input type='submit' class='bouton' value='".$msg['search']."' />&nbsp;
			<input type='button' class='bouton' value='".$msg['acquisition_new_account']."' onclick=\"document.location='!!link_add_account!!';\"/>
		</div>
		<div class='row'></div>
	</div>
</form>
<div class='row'>
	<span id='accounts_messages' class='erreur'>!!messages!!</span>
</div>
<script type='text/javascript'>
	ajax_parse_dom();
</script>
";
