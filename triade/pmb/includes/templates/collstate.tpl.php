<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collstate.tpl.php,v 1.21 2023/12/20 08:26:48 dgoron Exp $

// templates pour gestion des autorités collections

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $base_path, $collstate_form, $msg, $current_module, $location_field, $statut_field, $emplacement_field, $support_field, $collstate_advanced_form, $collstate_expl_list_form, $charset, $tpl_collstate_bulletins_list_page; 

require_once($base_path."/javascript/misc.inc.php");

//	----------------------------------
// $collection_form : form saisie collection

$collstate_form = jscript_unload_question()."
<script type='text/javascript'>
	function test_form(form){
		unload_off();
		!!return_form!!
	}
	function confirm_delete() {
        result = confirm(\"".$msg["confirm_suppr"]."\");
        if(result) {
        	unload_off();
          	document.location='./catalog.php?categ=serials&sub=collstate_delete&id=!!id!!&serial_id=!!serial_id!!&location=!!location_id!!';
		}
    }
    function calculate_collections_state() {
		var url= \"./ajax.php?module=catalog&categ=collections_state&fname=calculate_collections_state\";
		var state_col = new http_request();
		var separator = '';
		
		var post_params = \"&id_serial=!!serial_id!!&id_location=\"+document.getElementById('location_id').value;
		if(document.getElementById('collstate_advanced_expl_list_bulletins')) {
			post_params += \"&bulletins=\"+document.getElementById('collstate_advanced_expl_list_bulletins').value;
		}
		if(state_col.request(url,1,post_params)) alert(state_col.get_text());
		else {
			document.getElementById('state_collections').value= state_col.get_text();
		}
	}				
</script>
<script src='javascript/ajax.js'></script>

<form class='form-$current_module' id='saisie_collstate' name='saisie_collstate' method='post' action='!!action!!' onSubmit=\"return false\" >
	<h3>!!libelle!!</h3>
	<div class='form-contenu'>
		!!location_field!!
		!!emplacement_field!!
		!!expl_list!!
		
		<!-- state_collections -->
		<div class='row'>
			<div class='row'>
				<label class='etiquette' for='state_collections'>".$msg["collstate_form_collections"]."</label>
				<input id='btn_calc_1' class='bouton_small' value='Calculer' style='visibility: visible;' onclick=\"calculate_collections_state();\" type='button'>
			</div>
			<div class='row'>
				<textarea rows='5' class='saisie-80em' id='state_collections' name='state_collections'>!!state_collections!!</textarea>
			</div>
		</div>
		
		<!-- cote -->
		<div class='row'>
			<div class='row'>
				<label class='etiquette' for='cote'>".$msg["collstate_form_cote"]."</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-80em' size='80' id='cote' name='cote' value=\"!!cote!!\" />
			</div>
		</div>
		
		<!-- archive -->
		<div class='row'>
			<div class='row'>
				<label class='etiquette' for='archive'>".$msg["collstate_form_archive"]."</label>
			</div>
			<div class='row'>
			<input type='text' class='saisie-80em' size='80' id='archive' name='archive' value=\"!!archive!!\" />
			</div>
		</div>
		
		<!-- origine -->
		<div class='row'>
			<div class='row'>
				<label class='etiquette' for='origine'>".$msg["collstate_form_origine"]."</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-80em' size='80' id='origine' name='origine' value=\"!!origine!!\" />
			</div>
		</div>
		
		<!-- note -->
		<div class='row'>
			<div class='row'>
				<label class='etiquette' for='note'>".$msg["collstate_form_note"]."</label>
			</div>
			<div class='row'>
				<textarea rows='2' class='saisie-80em' id='note' name='note'>!!note!!</textarea>
			</div>
		</div>
		
		<!-- lacune -->
		<div class='row'>
			<div class='row'>
				<label class='etiquette' for='lacune'>".$msg["collstate_form_lacune"]."</label>
			</div>
			<div class='row'>
				<textarea rows='2' class='saisie-80em' id='lacune' name='lacune'>!!lacune!!</textarea>
			</div>
		</div>
		!!support_field!!
		!!statut_field!!
		
		!!parametres_perso!!
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='$msg[76]' !!annul!! />
			<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) this.form.submit();\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
	<div class='row'></div>
</form>
";

$location_field="
<!-- localisation -->
<div class='row'>
	<div class='row'>
		<label class='etiquette' for='location'>".$msg["collstate_form_localisation"]."</label>
	</div>
	<div class='row'>
		!!location!!
	</div>
</div>";

$statut_field="
<!-- statut -->
<div class='row'>
	<div class='row'>
		<label class='etiquette' for='statut'>".$msg["collstate_form_statut"]."</label>
	</div>
	<div class='row'>
		!!statut!!
	</div>
</div>";

$emplacement_field="
<!-- emplacement -->
<div class='row'>
	<div class='row'>
		<label class='etiquette' for='emplacement'>".$msg["collstate_form_emplacement"]."</label>
	</div>
	<div class='row'>
		!!emplacement!!
	</div>
</div>";

$support_field="
<!-- support -->
<div class='row'>
	<div class='row'>
		<label class='etiquette' for='support'>".$msg["collstate_form_support"]."</label>
	</div>
	<div class='row'>
		!!support!!
	</div>
</div>";
	
$collstate_advanced_form = "
<script type='text/javascript'>
	function collstate_add_expl(form) {
		if (!form.collstate_caddie_bull.selectedIndex) {
			alert('".addslashes($msg['collstate_advanced_form_no_bull_caddie_selected'])."');
			return false;
		}
		if (!form.collstate_caddie_expl.selectedIndex) {
			alert('".addslashes($msg['collstate_advanced_form_no_expl_caddie_selected'])."');
			return false;
		}
		if(form.collstate_cb_expl.value) {
			var request = new http_request();
			var post_params = '&id_caddie_bull='+form.collstate_caddie_bull[form.collstate_caddie_bull.selectedIndex].value+'&id_caddie_expl='+form.collstate_caddie_expl[form.collstate_caddie_expl.selectedIndex].value+'&cb_expl='+form.collstate_cb_expl.value;
			request.request('./ajax.php?module=catalog&categ=collections_state&fname=add_expl', true, post_params, true, expl_added);
		}
		form.collstate_cb_expl.value = '';
		form.collstate_cb_expl.focus();
	}
	
	function expl_added(data) {
		if (data == 1) {
			update_expl_list();
			expandBase('collstate_advanced_expl_list', false);
		}
	}
</script>
<form class='form-$current_module' id='collstate_advanced_form' name='collstate_advanced_form' method='post' action='' onSubmit=\"collstate_add_expl(this); return false\" >
	<h3>".$msg['collstate_advanced_form_title']."</h3>
	<div class='form-contenu'>	
		<!-- caddie bulletins -->
		<div class='row'>
			<div class='row'>
				<label class='etiquette' for='collstate_caddie_bull'>".$msg["collstate_advanced_form_bull_caddie"]."</label>
			</div>
			<div class='row'>
				!!caddie_bull!!
			</div>
		</div>
		<!-- caddie expl -->
		<div class='row'>
			<div class='row'>
				<label class='etiquette' for='collstate_caddie_expl'>".$msg["collstate_advanced_form_expl_caddie"]."</label>
			</div>
			<div class='row'>
				!!caddie_expl!!
			</div>
		</div>
		<!-- cb expl -->
		<div class='row'>
			<div class='row'>
				<label class='etiquette' for='collstate_cb_expl'>".$msg["collstate_advanced_cb_expl"]."</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-20em' id='collstate_cb_expl' name='collstate_cb_expl' value='' />
				<input type='submit' value='".$msg['collstate_advanced_form_validate']."' class='bouton' />
			</div>
		</div>
	</div>
	<div class='row'></div>
</form>";

$collstate_expl_list_form = "
<script type='text/javascript'>
	function update_expl_list() {
		var form = document.getElementById('collstate_advanced_form');
		var expl_list_nb = document.getElementById('collstate_advanced_expl_listParent_nb');
		expl_list_nb.innerHTML = '0';
		var expl_list_container = document.getElementById('collstate_advanced_expl_listChild');
		expl_list_container.innerHTML = '';
		
		var div_caddie = document.createElement('div');
		div_caddie.setAttribute('class','row');
		var label_node = document.createElement('label');
		label_node.setAttribute('class','etiquette');
		label_node.innerHTML = '".htmlentities($msg["caddie_de_BULL"], ENT_QUOTES, $charset)." : ';
		if(form.collstate_caddie_bull.selectedIndex) {
			var text_node = document.createTextNode(form.collstate_caddie_bull[form.collstate_caddie_bull.selectedIndex].innerHTML);
			document.getElementById('collstate_advanced_caddie_bull_id').value = form.collstate_caddie_bull[form.collstate_caddie_bull.selectedIndex].value;
		} else {
			var text_node = document.createTextNode('aucun panier');
			document.getElementById('collstate_advanced_caddie_bull_id').value = 0;
		}
		div_caddie.appendChild(label_node);
		div_caddie.appendChild(text_node);
		expl_list_container.appendChild(div_caddie);
				
		var div_caddie = document.createElement('div');
		div_caddie.setAttribute('class','row');
		var label_node = document.createElement('label');
		label_node.setAttribute('class','etiquette');
		label_node.innerHTML = '".htmlentities($msg["caddie_de_EXPL"], ENT_QUOTES, $charset)." : ';
		if(form.collstate_caddie_expl.selectedIndex) {
			var text_node = document.createTextNode(form.collstate_caddie_expl[form.collstate_caddie_expl.selectedIndex].innerHTML);
			document.getElementById('collstate_advanced_caddie_expl_id').value = form.collstate_caddie_expl[form.collstate_caddie_expl.selectedIndex].value;
		} else {
			var text_node = document.createTextNode('aucun panier');
			document.getElementById('collstate_advanced_caddie_expl_id').value = 0;
		}
		div_caddie.appendChild(label_node);
		div_caddie.appendChild(text_node);
		expl_list_container.appendChild(div_caddie);
		
		if(form.collstate_caddie_expl[form.collstate_caddie_expl.selectedIndex].value) {
			var request = new http_request();
			request.request('./ajax.php?module=catalog&categ=collections_state&fname=get_data_expl_list&id_caddie_expl='+form.collstate_caddie_expl[form.collstate_caddie_expl.selectedIndex].value+'&id_caddie_bull='+form.collstate_caddie_bull[form.collstate_caddie_bull.selectedIndex].value+'&id_location='+document.getElementById('location_id').value, false, '', true, function(data) {
				data = JSON.parse(data);
				var div = document.createElement('div');
				div.setAttribute('class','row');
				div.innerHTML = '&nbsp;';
				expl_list_container.appendChild(div);
				
				if(data.expl_list.length) {
					if(data.expl_list.length < data.nb_expl) {
						expl_list_nb.innerHTML = data.expl_list.length+' sur '+data.nb_expl;
					} else {
						expl_list_nb.innerHTML = data.expl_list.length;
					}
					for (var i = 0; i < data.expl_list.length; i++) {
						var expl_div = document.createElement('div');
						expl_div.innerHTML = data.expl_list[i];
						expl_list_container.appendChild(expl_div);
					}
					document.getElementById('collstate_advanced_expl_list_bulletins').value = data.bulletins.join(',');
					if (data.cote && !document.getElementById('cote').value) {
						document.getElementById('cote').value = data.cote;
					}
				}
			});
		}
	}
</script>
<!-- Liste des expl -->
<div id='collstate_advanced_expl_listParent' class='notice-parent'>
    ".get_expandBase_button('collstate_advanced_expl_list')."
	<span id='collstate_advanced_expl_listParent' class='notice-heada'>
		".$msg["collstate_advanced_expl_list"]." (<span id='collstate_advanced_expl_listParent_nb'>0</span>)
	</span>
	<input type='hidden' id='collstate_advanced_expl_list_bulletins' name='collstate_advanced_expl_list_bulletins' value='' />
	<input type='hidden' id='collstate_advanced_caddie_bull_id' name='collstate_advanced_caddie_bull_id' value='0' />
	<input type='hidden' id='collstate_advanced_caddie_expl_id' name='collstate_advanced_caddie_expl_id' value='0' />
</div>
<div id='collstate_advanced_expl_listChild' class='notice-child' style='margin-bottom:6px;display:none;width:94%'>
</div>
<script type='text/javascript'>
	update_expl_list();
</script>";

$tpl_collstate_bulletins_list_page = "
<script type='text/javascript'>
	function collstate_confirm_delete() {
		result = confirm('".$msg['confirm_suppr']."');
        if(result) {
          	document.location='./catalog.php?categ=serials&sub=collstate_delete&id=!!collstate_id!!&serial_id=!!serial_id!!';
		}
	}
</script>
<h1>".$msg['collstate_linked_bulletins_list_page_title']."</h1>
<div class='row'>
	<div class='notice-perio'>
        <div class='row'>
			<table width='100%'>
				<tbody>
					<tr>
						<td><b>".$msg["collstate_form_localisation"]."</b></td>
						<td>!!localisation!!</td>
					</tr>
					<tr>
						<td><b>".$msg["collstate_form_emplacement"]."</b></td>
						<td>!!emplacement_libelle!!</td>
					</tr>
					<tr>
						<td><b>".$msg["collstate_form_cote"]."</b></td>
						<td>!!cote!!</td>
					</tr>
					<tr>
						<td><b>".$msg["collstate_form_support"]."</b></td>
						<td>!!type_libelle!!</td>
					</tr>
					<tr>
						<td><b>".$msg["collstate_form_statut"]."</b></td>
						<td>!!statut_libelle!!</td>
					</tr>
					<tr>
						<td><b>".$msg["collstate_form_origine"]."</b></td>
						<td>!!origine!!</td>
					</tr>
					<tr>
						<td><b>".$msg["collstate_form_collections"]."</b></td>
						<td>!!state_collections!!</td>
					</tr>
					<tr>
						<td><b>".$msg["collstate_form_archive"]."</b></td>
						<td>!!archive!!</td>
					</tr>
					<tr>
						<td><b>".$msg["collstate_form_lacune"]."</b></td>
						<td>!!lacune!!</td>
					</tr>
				</tbody>
			</table>
		</div>
	    <hr>
		<div class='row'>
			<div class='left'>
				<input class='bouton' onclick='document.location=\"./catalog.php?categ=serials&sub=collstate_form&id=!!collstate_id!!&serial_id=!!serial_id!!&bulletin_id=!!bulletin_id!!\";' value='".$msg['62']."' type='button'>
			</div>
			<div class='right'>
				<input class='bouton' onclick='collstate_confirm_delete();' value='".$msg['63']."' type='button'>
			</div>
			<div class='row'></div>
		</div>
	</div>
</div>
<div>
	<div class='row'>
		<div class='center'>
			!!paginator!!
		</div>
	</div>
	<div class='row'>
		!!bulletins_list!!
	</div>
	<div class='row'>
		<div class='center'>
			!!paginator!!
		</div>
	</div>
</div>";