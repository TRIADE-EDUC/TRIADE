<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acquisition.tpl.php,v 1.33 2019-05-27 16:55:44 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $charset, $acquisition_layout, $current_module, $acquisition_layout_end, $user_query, $acquisition_menu, $plugins;

// $acquisition_menu : menu page acquisition
$acquisition_menu = "
<div id='menu'>
	<h3 onclick='menuHide(this,event)'>".htmlentities($msg['acquisition_menu_ach'],ENT_QUOTES,$charset)."</h3>
	<ul>
		<li><a href='./acquisition.php?categ=ach&sub=devi'>".htmlentities($msg['acquisition_menu_ach_devi'],ENT_QUOTES,$charset)."</a></li>
		<li><a href='./acquisition.php?categ=ach&sub=cmde'>".htmlentities($msg['acquisition_menu_ach_cmde'],ENT_QUOTES,$charset)."</a></li>
		<li><a href='./acquisition.php?categ=ach&sub=recept'>".htmlentities($msg['acquisition_menu_ach_recept'],ENT_QUOTES,$charset)."</a></li>
		<li><a href='./acquisition.php?categ=ach&sub=livr'>".htmlentities($msg['acquisition_menu_ach_livr'],ENT_QUOTES,$charset)."</a></li>
		<li><a href='./acquisition.php?categ=ach&sub=fact'>".htmlentities($msg['acquisition_menu_ach_fact'],ENT_QUOTES,$charset)."</a></li>
		<li><a href='./acquisition.php?categ=ach&sub=fourn'>".htmlentities($msg['acquisition_menu_ach_fourn'],ENT_QUOTES,$charset)."</a></li>
		<li><a href='./acquisition.php?categ=ach&sub=bud'>".htmlentities($msg['acquisition_menu_ref_budget'],ENT_QUOTES,$charset)."</a></li>
	</ul>	
	<h3 onclick='menuHide(this,event)'>".htmlentities($msg['acquisition_menu_sug'],ENT_QUOTES,$charset)."</h3>
	<ul>
		<li><a href='./acquisition.php?categ=sug&sub=multi'>".htmlentities($msg['acquisition_menu_sug_multiple'],ENT_QUOTES,$charset)."</a></li>
		<li><a href='./acquisition.php?categ=sug&sub=import'>".htmlentities($msg['acquisition_menu_sug_import'],ENT_QUOTES,$charset)."</a></li>	
		<li><a href='./acquisition.php?categ=sug&sub=empr_sug'>".htmlentities($msg['acquisition_menu_sug_empr'],ENT_QUOTES,$charset)."</a></li>	
		<li><a href='./acquisition.php?categ=sug'>".htmlentities($msg['acquisition_menu_sug_todo'],ENT_QUOTES,$charset)."</a></li>
	</ul>
	<h3 onclick='menuHide(this,event)'>".htmlentities($msg['acquisition_menu_rent'],ENT_QUOTES,$charset)."</h3>
	<ul>
		<li><a href='./acquisition.php?categ=rent&sub=requests'>".htmlentities($msg['acquisition_menu_rent_requests'],ENT_QUOTES,$charset)."</a></li>
";		
if (SESSrights & ACQUISITION_ACCOUNT_INVOICE_AUTH) {
	$acquisition_menu.= "
		<li><a href='./acquisition.php?categ=rent&sub=accounts'>".htmlentities($msg['acquisition_menu_rent_accounts'],ENT_QUOTES,$charset)."</a></li>
		<li><a href='./acquisition.php?categ=rent&sub=accounts&accounts_search_form_invoiced_filter=1&accounts_search_form_request_status=3'>".htmlentities($msg['acquisition_menu_rent_accounts_to_invoice'],ENT_QUOTES,$charset)."</a></li>
		<li><a href='./acquisition.php?categ=rent&sub=invoices'>".htmlentities($msg['acquisition_menu_rent_invoices'],ENT_QUOTES,$charset)."</a></li>
		<li><a href='./acquisition.php?categ=rent&sub=invoices&invoices_search_form_status=1'>".htmlentities($msg['acquisition_menu_rent_invoices_to_validate'],ENT_QUOTES,$charset)."</a></li>
	";
}		
$acquisition_menu.= "			
	</ul>";
$plugins = plugins::get_instance();	
$acquisition_menu.= $plugins->get_menu('acquisition')."
	<div id='div_alert' class='erreur'></div>
</div>
";
//	----------------------------------

// $acquisition_layout : layout page acquisition
$acquisition_layout = "
<div id='conteneur' class='$current_module'>
$acquisition_menu
<div id='contenu'>
";


// $acquisition_layout_end : layout page acquisition (fin)
$acquisition_layout_end = '
</div>
</div>
';


// $user_query : form de recherche
$user_query = "
<script type='text/javascript'>
<!--
	function test_form(form)
	{
		if(form.user_input.value.length == 0)
			{
				alert(\"$msg[141]\");
				return false;
			}
		return true;
	}
-->
</script>
<form class='form-$current_module' name='search' method='post' action='!!action!!'>
<h3><span>!!user_query_title!!</span></h3>
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne'>
			<input type='text' class='saisie-50em' name='user_input' />
		</div>
		<div class='right'></div>
		<div class='row'></div>
	</div>
</div>
";


$user_query.="	
<div class='row'>
	<div class='left'>
		<input type='submit' class='bouton' value='$msg[142]' onClick=\"return test_form(this.form)\" />
		<input class='bouton' type='button' value='!!add_auth_msg!!' onClick=\"document.location='!!add_auth_act!!'\" />
	</div>
	<div class='right'>
		<!-- lien_derniers -->
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['search'].elements['user_input'].focus();
</script>
<div class='row'></div>
";
?>