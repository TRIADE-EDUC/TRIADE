<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: groupexpl.tpl.php,v 1.13 2023/11/17 14:27:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $url_gestion, $groupexpl_list_tpl, $msg, $current_module, $groupexpl_list_line_tpl, $groupexpl_see_form_tpl;
global $groupexpl_see_form_principale_tpl, $groupexpl_confirm_form_tpl;
global $groupexpl_form_add_expl_tpl;

if(!isset($url_gestion)) $url_gestion = '';

$groupexpl_list_tpl="
<script type='text/javascript' src='./javascript/sorttable.js'></script>
<script type='text/javascript'>
	function test_form(form) {
		if(form.form_cb_expl.value.replace(/^\s+$/g,'').length == 0 ) {
				alert(\"$msg[292]\");
				document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
				return false;
			}
		return true;
	}
</script>
<form class='form-retour-expl' name='saisie_cb_ex' method='post' method='post' action=\"./circ.php?categ=groupexpl&action=search_expl\">	
	<div class='row'>
		<label class='etiquette' for='form_cb_expl'>".$msg['groupexpl_cb_found']."</label>
	</div>
	<div class='row'>
		<input class='saisie-20em' type='text' id='form_cb_expl' name='form_cb_expl' value=''/>
		&nbsp;&nbsp;
		<input type='button' class='bouton' value='$msg[502]' onclick=\"if (test_form(this.form)) this.form.submit();\"/>
	</div>
	<div class='row'><span class='erreur' >!!error_message!!</span></div>
</form>		
<form class='form-$current_module' name='check_resa' action='$url_gestion' method='post'>
	<span class='usercheckbox'>
		<input type='radio' name='montrerquoi' value='all' id='all' !!all_checked!! onclick='this.form.submit();'>
		<label for='all'>".$msg['groupexpl_all_select']."</label>
		<input type='radio' name='montrerquoi' value='pret' id='pret' !!pret_checked!! onclick='this.form.submit();'>
		<label for='pret'>".$msg['groupexpl_pret_select']."</label>
		<input type='radio' name='montrerquoi' value='checked' id='checked' !!error_checked!! onclick='this.form.submit();'>
		<label for='checked'>".$msg['groupexpl_checked_select']."</label>
	</span>
	!!location_filter!!
</form>	
<table  class='sortable' width='100%'>
	<tr>
		<th>".$msg['groupexpl_list_name_title']."</th>
		<th>".$msg['groupexpl_list_emprunteur_title']."</th>		 
		<th>".$msg['groupexpl_list_error_title']."</th>	
		<th></th>	
	</tr>
	!!list!!
</table>
<input type='button' class='bouton' value='".$msg['groupexpl_add_button']."' onClick=\"document.location='./circ.php?categ=groupexpl&action=form'\" />

<script type='text/javascript'>
	document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
</script>
";	

$groupexpl_list_line_tpl="
	<tr class='!!odd_even!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!odd_even!!'\" >
		<td><a href='./circ.php?categ=groupexpl&action=form&id=!!id!!'>!!name!!</a></td>
		<td>!!emprunteur!!</td>		 
		<td>!!error!!</td>	
		<td>
			<input type='button' class='bouton' value='".$msg['groupexpl_list_see']."' onClick=\"document.location='./circ.php?categ=groupexpl&action=see_form&id=!!id!!'\" />
		</td>	
	</tr>
";

$groupexpl_see_form_tpl="
<script type='text/javascript' src='./javascript/sorttable.js'></script>
<script type='text/javascript'>
	function test_form(form){
		if((form.form_cb_expl.value.length == 0) )		{
			return false;
		}
		return true;
	}
</script>
<h1>".$msg['groupexpl_see_form_edit']."</h1>	
<form class='form-retour-expl' name='saisie_cb_ex' method='post' method='post' action=\"./circ.php?categ=groupexpl&action=search_expl&action=do_check\">		
	<h3>!!name!!</h3>
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='row'>
		!!location!!<br />
		!!comment!!	
	</div>	
	<div class='row'>
		<label class='etiquette' for='form_cb_expl'>".$msg['groupexpl_see_form_check_cb']."</label>
	</div>
	<div class='row'>
		<input class='saisie-20em' type='text' id='form_cb_expl' name='form_cb_expl' value=''/>
		&nbsp;&nbsp;
		<input type='button' class='bouton' value='$msg[502]' onclick=\"if (test_form(this.form)) this.form.submit();\"/>
	</div>
	
	<div class='row'>
		<span class='erreur' >!!error_message!!</span>
		!!info_message!!
	</div>
</form>	

<form class='form-".$current_module."' id='groupexpl_form' name='groupexpl_form'  method='post' action=\"./circ.php?categ=groupexpl\" >
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='form-contenu'>		
		<h3>".$msg["groupexpl_see_form_expl_principal"]." !!responsable!!</h3>
		<div class='row'>
			!!expl_list!!
		</div>	
		<div class='row'> 
		</div>
	</div>
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['groupexpl_form_edit_button']."' onclick=\"document.getElementById('action').value='form';this.form.submit();\" />
			<input type='button' class='bouton' value='".$msg['groupexpl_form_exit']."'  onclick=\"document.location='./circ.php?categ=groupexpl'\"  />
		</div>
		<div class='right'>
			<input type='button' class='bouton' value='".$msg['groupexpl_form_raz_all_button']."' onclick=\"document.getElementById('action').value='raz_check';this.form.submit();\" />
						
		</div>
	</div>
<div class='row'></div>
</form>		
<script type='text/javascript'>
	document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
</script>
";
		
$groupexpl_see_form_principale_tpl="
<a href='./circ.php?categ=visu_ex&form_cb_expl=!!cb!!'>!!cb!!</a>!!notice!!	
";

$groupexpl_confirm_form_tpl="
<h1>".$msg['groupexpl_see_form_confirm']." : <a href='./circ.php?categ=groupexpl&action=see_form&id=!!id!!'>!!name!!</a></h1>		
<form class='form-".$current_module."' id='groupexpl_form' name='groupexpl_form'  method='post' action=\"./circ.php?categ=groupexpl\" >
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='form-contenu'>		
		!!message!!
		!!comment!!				
		<div class='row'>
			!!expl_list!!
		</div>		
	</div>
<div class='row'></div>
</form>		
";

$groupexpl_form_add_expl_tpl="
<script type='text/javascript'>
	function test_form2(form) {
		if(form.form_cb_expl.value.replace(/^\s+$/g,'').length == 0 ) {
			alert(\"$msg[292]\");
			document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
			return false;
		}
		return true;
	}
</script>
<form class='form-retour-expl' name='saisie_cb_ex' method='post' method='post' action=\"./circ.php?categ=groupexpl&action=search_expl&action=add_expl\">		
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='row'>
		<label class='etiquette' for='form_cb_expl'>".$msg['groupexpl_form_add_cb']."</label>
	</div>
	<div class='row'>
		<input class='saisie-20em' type='text' id='form_cb_expl' name='form_cb_expl' value=''/>
		&nbsp;&nbsp;
		<input type='button' class='bouton' value='$msg[502]' onclick=\"if (test_form2(this.form)) this.form.submit();\"/>
	</div>
	
	<div class='row'>
		<span class='erreur' >!!error_message!! !!error_html_message!!</span>
		!!info_message!!
	</div>
</form>	
<script type='text/javascript'>
	document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
</script>
";