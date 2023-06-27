<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: groupexpl.tpl.php,v 1.9 2019-05-27 13:47:15 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $url_gestion, $groupexpl_list_tpl, $msg, $current_module, $groupexpl_list_line_tpl, $th_sur_location, $td_sur_location, $pmb_sur_location_activate, $groupexpl_see_form_tpl;
global $groupexpl_see_form_list_line_tpl, $groupexpl_see_form_principale_tpl, $groupexpl_confirm_form_tpl, $groupexpl_confirm_form_list_line_tpl, $groupexpl_form_tpl;
global $groupexpl_form_add_expl_tpl, $groupexpl_form_list_line_tpl;

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

<h1>".$msg["groupexpl_submenu_list_title"]."</h1>
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

$th_sur_location="";
$td_sur_location="";
if(isset($pmb_sur_location_activate) && $pmb_sur_location_activate){	
	$th_sur_location="<th>".$msg["sur_location_expl"]."</th>";
	$td_sur_location="<td>!!sur_loc_libelle!!</td>";
}

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
			<table  class='sortable' width='100%'>
				<tr>					
					<th>".$msg['groupexpl_form_cb']."</th>
					<th>".$msg['groupexpl_form_notice']."</th>	
					$th_sur_location
					<th>".$msg[298]."</th>	
					<th>".$msg[295]."</th>	
					<th>".$msg[296]."</th>	
					<th>".$msg[297]."</th>	
					<th>".$msg['groupexpl_see_form_checked_title']."</th>	
					<th>".$msg['groupexpl_form_raz_button']."</th>	
				</tr>
				!!expl_list!!
			</table>
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
					
$groupexpl_see_form_list_line_tpl="
	<tr>
		<td><a href='./circ.php?categ=visu_ex&form_cb_expl=!!cb!!'>!!cb!!</a></td>
		<td>!!notice!!</td>	
		$td_sur_location
		<td>!!location_libelle!!</td>	
		<td>!!section_libelle!!</td>	
		<td>!!expl_cote!!</td>	
		<td>!!statut_libelle!!</td>	
		<td>!!checked!!</td>		 
		<td><input type='button' class='bouton_small align_middle' value='X' 
			onclick=\"document.location='./circ.php?categ=groupexpl&action=raz_check&id=!!id!!&form_cb_expl=!!cb!!'; \">
		</td>	
		
	</tr>
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
			<table  class='sortable' width='100%'>
				<tr>					
					<th>".$msg['groupexpl_form_cb']."</th>
					<th>".$msg['groupexpl_form_notice']."</th>	
					$th_sur_location
					<th>".$msg[298]."</th>	
					<th>".$msg[295]."</th>	
					<th>".$msg[296]."</th>	
					<th>".$msg[297]."</th>	
					<th>".$msg['groupexpl_see_form_checked_title']."</th>						
				</tr>
				!!expl_list!!
			</table>
		</div>		
	</div>
<div class='row'></div>
</form>		
";		
			
$groupexpl_confirm_form_list_line_tpl="
	<tr>
		<td !!tr_color!!><a href='./circ.php?categ=visu_ex&form_cb_expl=!!cb!!'>!!cb!!</a></td>
		<td !!tr_color!!>!!notice!!</td>	
		$td_sur_location
		<td !!tr_color!!>!!location_libelle!!</td>	
		<td !!tr_color!!>!!section_libelle!!</td>	
		<td !!tr_color!!>!!expl_cote!!</td>	
		<td !!tr_color!!>!!statut_libelle!!!!emprunteur!!</td>	
		<td !!tr_color!!>!!checked!!</td>		 		
	</tr>
";

$groupexpl_form_tpl="
<script type='text/javascript' src='./javascript/sorttable.js'></script>
<script type='text/javascript'>
	function test_form(form){
		if((form.name.value.replace(/^\s+$/g,'').length == 0) )		{
			alert('".addslashes($msg["groupexpl_form_name_error"])."');
			return false;
		}
		return true;
	}
</script>
<h1>!!msg_title!!</h1>		
<form class='form-".$current_module."' id='groupexpl_form' name='groupexpl_form'  method='post' action=\"./circ.php?categ=groupexpl\" >

	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['groupexpl_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>
		!!location!!
		<div class='row'>
			<label class='etiquette' for='statut_principal'>".$msg['groupexpl_form_statut_principal']."</label>
		</div>
		<div class='row'>
			!!statut_principal!!
		</div>
		<div class='row'>
			<label class='etiquette' for='statut_others'>".$msg['groupexpl_form_statut_others']."</label>
		</div>
		<div class='row'>
			!!statut_others!!
		</div>
		<div class='row'>
			<label class='etiquette' for='comment'>".$msg["groupexpl_form_comment"]."</label>
			<div class='row'>
				<textarea id='comment' name='comment' cols='50' rows='2'>!!comment!!</textarea>
			</div>
		</div>
		

		<div class='row'> 
		</div>		
		<div class='row'>
			<table  class='sortable' width='100%'>
				<tr>
					<th>".$msg['groupexpl_form_cb']."</th>
					<th>".$msg['groupexpl_form_notice']."</th>	
					$th_sur_location
					<th>".$msg[298]."</th>	
					<th>".$msg[295]."</th>	
					<th>".$msg[296]."</th>	
					<th>".$msg[297]."</th>	
					<th>".$msg['groupexpl_form_resp_expl']."</th>	
					<th>".$msg['groupexpl_see_form_checked_title']."</th>	
					<th></th>	
				</tr>
				!!expl_list!!
			</table>
		</div>	
	</div>
	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['groupexpl_form_save']."' onclick=\"document.getElementById('action').value='save';if (test_form(this.form)) this.form.submit();\" />
			<input type='button' class='bouton' value='".$msg['groupexpl_form_exit']."' onclick=\"document.location='./circ.php?categ=groupexpl'\"  />
			!!see_button!!
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>	
!!add_expl!!

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

$groupexpl_form_list_line_tpl="
	<tr class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\" >
		<td><a href='./circ.php?categ=visu_ex&form_cb_expl=!!cb!!'>!!cb!!</a></td>
		<td>!!notice!!</td>	
		$td_sur_location
		<td>!!location_libelle!!</td>	
		<td>!!section_libelle!!</td>	
		<td>!!expl_cote!!</td>	
		<td>!!statut_libelle!!</td>	
		<td><input type='radio' value='!!expl_num!!' !!resp_expl_num_checked!! name='resp_expl_num'></td>		 
		<td>!!checked!!</td>		 
		<td><input type='button' class='bouton_small align_middle' value='X' 
			onclick=\"document.location='./circ.php?categ=groupexpl&action=del_expl&id=!!id!!&form_cb_expl=!!cb!!'; \">
		</td>	
		
	</tr>
";