<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_invoices.tpl.php,v 1.11 2019-05-27 10:17:33 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//	------------------------------------------------------------------------------
//	$invoices_search_form_tpl : template de recherche pour les décomptes
//	------------------------------------------------------------------------------

global $base_path, $charset, $current_module, $msg, $rent_invoices_search_form_tpl;

$rent_invoices_search_form_tpl = "
<script src='javascript/pricing_systems.js'></script>
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	var msg_acquisition_invoices_checked_empty = '".addslashes($msg['acquisition_invoices_checked_empty'])."';
</script>
<form class='form-".$current_module."' id='invoices_search_form' name='invoices_search_form' method='post' action=\"".$base_path."/acquisition.php?categ=rent&sub=invoices\" >
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
				<input type='text' id='invoices_search_form_publisher' autfield='invoices_search_form_num_publisher' completion='publishers' class='saisie-20emr' value='!!publisher!!' autocomplete='off' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=editeur&caller=invoices_search_form&p1=invoices_search_form_num_publisher&p2=invoices_search_form_publisher&deb_rech='+this.form.invoices_search_form_publisher.value, 'selector')\"/>
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.invoices_search_form_publisher.value=''; this.form.invoices_search_form_num_publisher.value='0'; \" />
				<input type='hidden' id='invoices_search_form_num_publisher' name='invoices_search_form_num_publisher' value='!!num_publisher!!' />
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_account_num_supplier'], ENT_QUOTES, $charset)."</label>
				<br />		
				<input type='text' id='invoices_search_form_supplier' autfield='invoices_search_form_num_supplier' completion='fournisseur' class='saisie-20emr' value='!!supplier!!' autocomplete='off' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=fournisseur&caller=invoices_search_form&param1=invoices_search_form_num_supplier&param2=invoices_search_form_supplier&id_bibli='+this.form.invoices_search_form_entities.value+'&deb_rech='+this.form.invoices_search_form_supplier.value, 'selector')\"/>
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.invoices_search_form_supplier.value=''; this.form.invoices_search_form_num_supplier.value='0'; \" />
				<input type='hidden' id='invoices_search_form_num_supplier' name='invoices_search_form_num_supplier' value='!!num_supplier!!' />
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_invoice_status'], ENT_QUOTES, $charset)."</label>
				<br />
				!!selector_status!!
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<div class='row'>		
					<label class='etiquette'>".$msg['acquisition_invoice_date']."</label>
				</div>
				<div class='row'>
					<input type='text' name='invoices_search_form_date_start' id='invoices_search_form_date_start' value='!!date_start!!'  data-dojo-type='dijit/form/DateTextBox' required='false' />
					 - <input type='text' name='invoices_search_form_date_end' id='invoices_search_form_date_end' value='!!date_end!!'  data-dojo-type='dijit/form/DateTextBox' required='false' />
				</div>
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='hidden' id='invoices_json_filters' name='invoices_json_filters' value='!!json_filters!!' />
			<input type='hidden' id='invoices_page' name='invoices_page' value='!!page!!' />
			<input type='hidden' id='invoices_nb_per_page' name='invoices_nb_per_page' value='!!nb_per_page!!' />
			<input type='hidden' id='invoices_pager' name='invoices_pager' value='!!pager!!' />
			<input type='submit' class='bouton' value='".$msg['search']."' />&nbsp;
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
