<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: print_cart_tpl.tpl.php,v 1.5 2019-05-27 12:35:59 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $cart_tpl_list_tpl, $cart_tpl_list_line_tpl, $cart_tpl_form_tpl, $charset, $msg, $pmb_javascript_office_editor, $current_module;

$cart_tpl_list_tpl="	
<h1>".htmlentities($msg["admin_print_cart_tpl_title"], ENT_QUOTES, $charset)."</h1>			
<table>
	<tr>				
		<th>	".htmlentities($msg["admin_print_cart_tpl_id"], ENT_QUOTES, $charset)."			
		</th>		
		<th>	".htmlentities($msg["admin_print_cart_tpl_name"], ENT_QUOTES, $charset)."			
		</th> 			 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' name='add_empr_button' value='".htmlentities($msg["admin_print_cart_tpl_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=mailtpl&sub=print_cart_tpl&action=form'\" />	
";

$cart_tpl_list_line_tpl="
<tr  class='!!odd_even!!' onmousedown=\"document.location='./admin.php?categ=mailtpl&sub=print_cart_tpl&action=form&id=!!id!!';\"  style=\"cursor: pointer\" 
onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td style='vertical-align:top'>				
		!!id!!
	</td> 	
	<td style='vertical-align:top'>				
		!!name!!
	</td> 	
	
</tr> 	
";

$cart_tpl_form_tpl="	
	$pmb_javascript_office_editor
	<script type='text/javascript' src='./javascript/tinyMCE_interface.js'></script>
<script type='text/javascript'>
	function test_form(form){
		if((form.f_name.value.length == 0) )		{
			alert('".$msg["admin_mailtpl_name_error"]."');
			return false;
		}
		return true;
	}
</script>
<h1>!!msg_title!!</h1>		
<form class='form-".$current_module."' id='print_cart_tpl' name='print_cart_tpl'  method='post' action=\"admin.php?categ=mailtpl&sub=print_cart_tpl\" >

	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='f_name'>".$msg['admin_print_cart_tpl_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='f_name' id='f_name' value='!!name!!' />
		</div>
		
		<div class='row'>
			<label class='etiquette' for='f_header'>".$msg["admin_print_cart_tpl_form_header"]."</label>
			<div class='row'>
				<textarea id='f_header' name='f_header' cols='100' rows='20'>!!header!!</textarea>
			</div>
		</div>
		<div class='row'>
			<label class='etiquette' for='f_footer'>".$msg["admin_print_cart_tpl_form_footer"]."</label>
			<div class='row'>
				<textarea id='f_footer' name='f_footer' cols='100' rows='20'>!!footer!!</textarea>
			</div>
		</div>
		<div class='row'> 
		</div>
	</div>
	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_print_cart_tpl_exit']."'  onclick=\"document.location='./admin.php?categ=mailtpl&sub=print_cart_tpl'\"  />
			<input type='button' class='bouton' value='".$msg['admin_print_cart_tpl_save']."' onclick=\"document.getElementById('action').value='save';if (test_form(this.form)) this.form.submit();\" />
			!!duplicate!!
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>		
";
