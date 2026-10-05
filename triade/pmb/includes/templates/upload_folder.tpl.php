<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: upload_folder.tpl.php,v 1.10 2023/12/22 13:39:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $rep_edit_form, $up_frame, $current_module, $msg;

$rep_edit_form = "
<form class='form-$current_module' id='folder_list_form' name='folder_list_form' method='post' action=\"./admin.php?categ=docnum&sub=rep\">
	<script type='text/javascript'>
		function test_form(form) {
			if((form.rep_nom.value.length == 0) || (form.rep_path.value.length == 0)) {
				alert(\"".$msg['upload_repertoire_error_creation']."\");
				return false;
			}
			return true;
		}	
		function changeEtatHash(){
			if(document.getElementById('rep_hash').value == 1){
				document.getElementById('rep_navig').options[1].selected = true;	
				document.getElementById('rep_sub').style.visibility= \"visible\";		
			} 			
		}
	</script>
	
	<input type='hidden' name='action' />
	<input type='hidden' name='id' value='!!id!!' />
	<h3><span>".$msg['upload_repertoire_modify']."</span>
	<div class='form-contenu'>
		<div class='row'>
			<label for='rep_nom'>".$msg['upload_repertoire_nom']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80em' name='rep_nom' id='rep_nom' value='!!rep_nom!!' />
		</div> ";

$rep_edit_form .="<div class='row'>
			<label for='rep_path'>".$msg['upload_repertoire_path']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80em' name='rep_path' id='rep_path' value='!!rep_path!!' />
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='rep_navig'>".$msg['upload_repertoire_navig']."</label>
			</div>
			<div class='colonne3'>
				<label for='rep_hash'>".$msg['upload_repertoire_hash']."</label>
			</div>
			<div class='colonne3'>
				<label>".$msg['upload_repertoire_subfolder']."</label>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<select id='rep_navig' name='rep_navig'>
					<option value='1' !!select_nav_yes!!>".$msg['upload_repertoire_yes']."</option>
					<option value='0' !!select_nav_no!!>".$msg['upload_repertoire_no']."</option>
				</select>
			</div>	
			<div class='colonne3'>
				<select id='rep_hash' name='rep_hash' onchange=\"changeEtatHash();\">
					<option value='1' !!select_hash_yes!!>".$msg['upload_repertoire_yes']."</option>
					<option value='0' !!select_hash_no!!>".$msg['upload_repertoire_no']."</option>
				</select>
			</div>	
			<div class='colonne3'>
				!!champ_sub!!
			</div>
		</div>		
		<div class='row'>
			<label for='rep_utf8'>".$msg['upload_repertoire_utf8']."</label>
		</div>
		<div class='row'>
			<select id='rep_utf8' name='rep_utf8'>
				<option value='1' !!select_utf8_yes!!>".$msg['upload_repertoire_yes']."</option>
				<option value='0' !!select_utf8_no!!>".$msg['upload_repertoire_no']."</option>
			</select>
		</div>			
	</div>
	<div class='row'>
		<div class='left'>			
			<input type='button' class='bouton' value='$msg[76]' onclick='document.location=\"./admin.php?categ=docnum&sub=rep\"' /> 
			<input type='submit' class='bouton' value='$msg[77]' onclick='this.form.action.value=\"save_rep\"; return test_form(this.form);' />
		</div>
		<div class='right'>
			!!btn_suppr!!
		</div>
	</div>
	<div class='row'></div>
</form>
";

$up_frame = "
	<h3>".$msg['upload_repertoire_selection']."</h3>
	<form class='form-".$current_module."' id='up_frame_modif' name='up_frame_modif' method='post' style='width : 95%'>
	<div class='row' >
	</div>
	<div class='row' >
		<div id='up_fiel_tree' ></div>
	</div>
	</form>
";
