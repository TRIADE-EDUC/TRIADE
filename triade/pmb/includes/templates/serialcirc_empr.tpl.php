<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_empr.tpl.php,v 1.9 2019/05/27 14:16:26 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $empr_serialcirc_tmpl, $msg, $charset, $empr_serialcirc_tmpl_item, $empr_serialcirc_circ_tmpl_item;

$empr_serialcirc_tmpl="			
<div class='row'>	
 <hr/>
</div>		
<div class='row'>
	<div class='left'>
	<h3>".htmlentities($msg["serialcirc_empr_title"],ENT_QUOTES,$charset)."</h3>
	</div>
	
</div>	
<div class='row'></div>
<script type='text/javascript'>
	function serialcirc_empr_select_all(form){
		y=form.serialcirc_empr_list.value;
		ids=y.split('|');
		while (ids.length>0) {
			id=ids.shift();
			document.getElementById('serialcirc_'+id).click();		
		} 
	}

	function serialcirc_tr(form, duplicate){
		y=form.serialcirc_empr_list.value;
		ids=y.split('|');
		var need = false;				
		while (ids.length>0) {
			id=ids.shift();
			if(document.getElementById('serialcirc_'+id) && document.getElementById('serialcirc_'+id).checked){
				need=true;			
			}		
		}				
		if(need){
			if (duplicate) {
				form.serialcirc_action.value='dup';
			} else {
				form.serialcirc_action.value='tr';
			}
			openPopUp('./select.php?what=emprunteur&caller=serial_empr_action&param1=serialcirc_new_empr&param2=serialcirc_new_empr_label&callback=serialcirc_tr_confirm', 'selector');
		}			
	}

	function serialcirc_delete(form){
		y=form.serialcirc_empr_list.value;
		ids=y.split('|');
		var need = false;				
		while (ids.length>0) {
			id=ids.shift();
			if(document.getElementById('serialcirc_'+id) && document.getElementById('serialcirc_'+id).checked){
				need=true;			
			}		
		}				
		if(need && confirm('".addslashes($msg['serialcirc_empr_delete_confirm'])."')){
			form.serialcirc_action.value='delete';
			form.submit();										
		}				
	}

	function serialcirc_tr_confirm(){
		var conf = null;
		var serialcirc_action = document.forms['serial_empr_action'].serialcirc_action.value;
		if (serialcirc_action == 'tr') {
			conf = confirm('".addslashes($msg['serialcirc_empr_forward_confirm'])."');
		} else if (serialcirc_action == 'dup') {
			conf = confirm('".addslashes($msg['serialcirc_empr_duplicate_confirm'])."');
		}
		if(conf){
			document.forms['serial_empr_action'].submit();			
		}
	}
</script>
<form name='serial_empr_action' id='serial_empr_action' method='post' action='./circ.php?categ=pret&form_cb=!!empr_cb!!'>				
	<table width='100%' class='sortable'>
		<thead>		
		<tr>
			<th colspan='5'>
				<input type='button' class='bouton' name='serialcirc_del' onclick='serialcirc_delete(this.form);' value='".htmlentities($msg["serialcirc_empr_delete_button"],ENT_QUOTES,$charset)."'/>
				<input type='button' class='bouton' name='serialcirc_forward' onclick='serialcirc_tr(this.form, false);' value='".htmlentities($msg["serialcirc_empr_forward_button"],ENT_QUOTES,$charset)."'/>
				<input type='button' class='bouton' name='serialcirc_duplicate' onclick='serialcirc_tr(this.form, true);' value='".htmlentities($msg["serialcirc_empr_duplicate_button"],ENT_QUOTES,$charset)."'/>
				<input type='text' class='saisie-20emr' disabled='disabled' name='serialcirc_new_empr_label' value=''/>
			</th>
		</tr>
		<tr>
			<th>
				".htmlentities($msg["serialcirc_empr_perio"],ENT_QUOTES,$charset)."
			</th>
			<th>
				".htmlentities($msg["serialcirc_empr_abt"],ENT_QUOTES,$charset)."
			</th>
			<th>
				".htmlentities($msg["serialcirc_empr_bulletinage"],ENT_QUOTES,$charset)."
			</th>
			<th>
				".htmlentities($msg["serialcirc_expl_see"],ENT_QUOTES,$charset)."
			</th>
			<th>
				<input type='button' onclick='serialcirc_empr_select_all(this.form)' class='bouton' value='+' name='serialcirc_block_all' title='".htmlentities($msg["resa_tout_cocher"],ENT_QUOTES,$charset)."'/>
				<input type='hidden' name='serialcirc_empr_list' value='!!serialcirc_empr_ids_list!!'/>
				<input type='hidden' name='serialcirc_action' value=''/>	
				<input type='hidden' name='serialcirc_new_empr' value=''/>	
			</th>
		</tr>
		</thead>				
		!!serialcirc_empr_list!!
	</table>
</form>";
$empr_serialcirc_tmpl_item="
		<tr>
			<td>
				!!periodique!!
			</td>
			<td>
				!!abt!!
			</td>
			<td>
				!!bulletinage_see!!
			</td>
			<td>
				!!exemplaire_see!!
			</td>
			<td class='center'>
				<input id='serialcirc_!!id!!' type='checkbox' name='serialcirc[]' value='!!id!!'/>
			</td>
		</tr>
";		
$empr_serialcirc_circ_tmpl_item="
		<tr>
			<td>
				!!periodique!!
			</td>
			<td>
				!!abt!!
			</td>
			<td>
				!!bulletinage_see!!
			</td>

			<td>
				!!exemplaire_see!!
			</td>
			<td class='center'>
				<img src='".get_url_icon('interdit.gif')."' title='".htmlentities($msg['serialcirc_empr_only_in_circ'],ENT_QUOTES,$charset)."'/>
			</td>
		</tr>";