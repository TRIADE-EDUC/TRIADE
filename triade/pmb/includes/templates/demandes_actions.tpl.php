<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_actions.tpl.php,v 1.20 2019-05-27 09:13:01 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $current_module;
global $pmb_gestion_devise;
global $js_liste_action, $content_liste_action, $form_liste_action, $form_modif_action, $form_consult_action, $form_add_docnum, $form_see_docnum, $form_communication;

$js_liste_action = "
	<script src='./javascript/demandes.js' type='text/javascript'></script>
	<script src='./javascript/dynamic_element.js' type='text/javascript'></script>
	<script type='text/javascript' src='./javascript/demandes_form.js'></script>
	<script type='text/javascript'>
		var msg_demandes_note_confirm_demande_end='".addslashes($msg['demandes_note_confirm_demande_end'])."'; 
		var msg_demandes_actions_nocheck='".addslashes($msg['demandes_actions_nocheck'])."'; 
		var msg_demandes_confirm_suppr = '".addslashes($msg['demandes_confirm_suppr'])."';
		var msg_demandes_note_confirm_suppr = '".addslashes($msg['demandes_note_confirm_suppr'])."';
	</script>
";

$content_liste_action = "
	<input type='hidden' id='iddemande' name='iddemande' value='!!iddemande!!'/>
	<input type='hidden' id='last_modified' name='last_modified' value='!!last_modified!!'/>
	<h3>".$msg['demandes_action_liste']."</h3>
	<div class='form-contenu'>
		<table>
			<tbody>
				<tr>
					!!expand_header!!
					<th></th>
					<th>".$msg['demandes_action_type']."</th>
					<th>".$msg['demandes_action_sujet']."</th>
					<th>".$msg['demandes_action_detail']."</th>	
					<th>".$msg['demandes_action_statut']."</th>				
					<th>".$msg['demandes_action_date']."</th>
					<th>".$msg['demandes_action_date_butoir']."</th>
					<th>".$msg['demandes_action_createur']."</th>
					<th>".$msg['demandes_action_time_elapsed']." (".$msg['demandes_action_time_unit'].")</th>
					<th>".$msg['demandes_action_cout']."</th>
					<th>".$msg['demandes_action_progression']."</th>	
					<th>".$msg['demandes_action_nbnotes']."</th>					
					<th></th>
				</tr>
				!!liste_action!!				
			</tbody>
		</table>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='submit' class='bouton' value='".$msg['demandes_action_add']."' onclick='!!change_action_form!!if(this.form.idaction!=undefined){this.form.idaction.value=\"\";}this.form.last_modified.value=\"\";this.form.act.value=\"add_action\"' />
		</div>
		<div class='right'>
			!!btn_suppr!!
		</div>
	</div>
	<div class='row'></div>
";

$form_liste_action = "
	<form class='form-".$current_module."' id='liste_action' name='liste_action' method='post' action=\"./demandes.php?categ=action\"  >
	<input type='hidden' name='act' id='act' />
	".$content_liste_action."	
	</form>	
<script>
!!script_expand!!
parse_dynamic_elts();
</script>
";

$form_modif_action = "

<h1>".$msg['demandes_gestion']." : ".$msg['demandes_menu_action']."</h1>
<h2>!!path!!</h2>
<script src='./javascript/demandes.js' type='text/javascript'></script>
<script type='text/javascript'>
	function confirm_delete(){
		var sup = confirm(\"".$msg['demandes_confirm_suppr']."\");
		if(!sup)
			return false;
			
		return true;
	}
</script>
<form class='form-".$current_module."' id='modif_action' name='modif_action' method='post' action=\"!!form_action!!\">
	<h3>!!form_title!!</h3>
	<input type='hidden' id='act' name='act' />
	<input type='hidden' id='idaction' name='idaction' value='!!idaction!!'/>
	<input type='hidden' id='iddemande' name='iddemande' value='!!iddemande!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_type']."</label>
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_statut']."</label>
			</div>
			<div class='colonne3'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				!!select_type!!
				!!type_action!!
			</div>
			<div class='colonne3'>
				!!select_statut!!
			</div>
			<div class='colonne3'>&nbsp;</div>
		</div>
		<div class='row'>
			<label class='etiquette'>".$msg['demandes_action_sujet']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='sujet' id='sujet' value='!!sujet!!' />
		</div>
		<div class='row'>
			<label class='etiquette'>".$msg['demandes_action_detail']."</label>
		</div>
		<div class='row'>
			<textarea id='detail' name='detail' cols='50' rows='4' wrap='virtual'>!!detail!!</textarea>
		</div>
		<div class='row'>
				<label class='etiquette'>".$msg['demandes_action_privacy']."</label>
				<input type='checkbox' name='ck_prive' id='ck_prive' value='1' !!ck_prive!! />
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_date']."</label>
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_date_butoir']."</label>
			</div>
			<div class='colonne3'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<input type='hidden' id='date_debut' name='date_debut' value='!!date_debut!!' />
				<input type='button' class='bouton' id='date_debut_btn' name='date_debut_btn' value='!!date_debut_btn!!' onClick=\"openPopUp('./select.php?what=calendrier&caller=modif_action&date_caller=!!date_debut!!&param1=date_debut&param2=date_debut_btn&auto_submit=NO&date_anterieure=YES', 'calendar')\"/>
			</div>
			<div class='colonne3'>
				<input type='hidden' id='date_fin' name='date_fin' value='!!date_fin!!' />
				<input type='button' class='bouton' id='date_fin_btn' name='date_fin_btn' value='!!date_fin_btn!!' onClick=\"openPopUp('./select.php?what=calendrier&caller=modif_action&date_caller=!!date_fin!!&param1=date_fin&param2=date_fin_btn&auto_submit=NO&date_anterieure=YES', 'calendar')\"/>
			</div>
			<div class='colonne3'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_time_elapsed']." (".$msg['demandes_action_time_unit'].")</label>
			</div>			
			<div class='colonne3'>
				<label class='etiquette'>".sprintf($msg['demandes_action_cout'],$pmb_gestion_devise)."</label>
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_progression']."</label>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<input type='text' class='saisie-20em' name='time_elapsed' id='time_elapsed' value='!!time_elapsed!!' />
			</div>			
			<div class='colonne3'>
				<input type='text' class='saisie-10em' name='cout' id='cout' value='!!cout!!' />
			</div>
			<div class='colonne3'>
				<input type='text' class='saisie-10em' name='progression' id='progression' value='!!progression!!' />
			</div>
		</div>		
		<div class='row'></div>	
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='$msg[76]' onClick=\"!!cancel_action!!\" />
			<input type='submit' class='bouton' value='$msg[77]' onClick='this.form.act.value=\"save_action\" ; return test_form(this.form); ' />
		</div>
		<div class='right'>
			!!btn_suppr!!
		</div>
	</div>
	<div class='row'></div>
</form>
<div class='row' id='docnum'>
</div>
<script type='text/javascript'>
	function test_form(form) {	
	
		if(isNaN(form.progression.value) || form.progression.value > 100){
	    	alert(\"$msg[demandes_progres_ko]\");
			return false;
	    }
	    if(isNaN(form.cout.value)){
	    	alert(\"$msg[demandes_action_cout_ko]\");
			return false;
	    }
	    if(isNaN(form.time_elapsed.value)){
	    	alert(\"$msg[demandes_action_time_ko]\");
			return false;
	    }
		if((form.sujet.value.length == 0)){
			alert(\"$msg[demandes_action_create_ko]\");
			return false;
	    } 
	     
	    if(form.date_debut.value>form.date_fin.value){
	    	alert(\"$msg[demandes_date_ko]\");
	    	return false;
	    }
	    
		return true;
			
	}
</script>
";

$form_consult_action = "
<h1>".$msg['demandes_gestion']." : ".$msg['demandes_menu_action']."</h1>
<h2>!!path!!</h2>
<script src='./javascript/demandes.js' type='text/javascript'></script>
<script type='text/javascript'>
	function confirm_delete(){
		var sup = confirm(\"".$msg['demandes_confirm_suppr']."\");
		if(!sup)
			return false;
			
		return true;
	}
</script>
<form class='form-".$current_module."' id='see_action' name='see_action' method='post' action=\"./demandes.php?categ=action\">
	<h3>!!form_title!!</h3>
	<input type='hidden' id='idaction' name='idaction' value='!!idaction!!'/>
	<input type='hidden' id='idstatut' name='idstatut' value='!!idstatut!!'/>
	<input type='hidden' id='iddemande' name='iddemande' value='!!iddemande!!'/>
	<input type='hidden' id='act' name='act' />
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_type']." : </label>
				!!type_action!!
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_date']." : </label>
				!!date_action!!
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_time_elapsed']." (".$msg['demandes_action_time_unit'].") : </label>
				!!time_action!!
			</div>			
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_statut']." : </label>
				!!statut_action!!
			</div>	
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_date_butoir']." : </label>
				!!date_butoir_action!!
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_cout']." : </label>
				!!cout_action!!
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_detail']." : </label>
				!!detail_action!!
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_privacy']." : </label>
				!!prive_action!!
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_progression']." : </label>
				!!progression_action!!
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_action_createur']." : </label>
				!!createur!!
			</div>
		</div>
		<div class='row'></div>
	</div>
	<div class='row'>
		!!btn_etat!!
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['demandes_retour']."' onClick=\"!!cancel_action!!\" />
			<input type='submit' class='bouton' value='$msg[62]' onClick='this.form.act.value=\"modif\" ; ' />
			!!btn_audit!!			
		</div>
		<div class='right'>
			<input type='submit' class='bouton' value='$msg[63]' onClick='this.form.act.value=\"suppr_action\"; return confirm_delete(); ' />
		</div>
	</div>
	<div class='row'></div>
</form>
";

$form_add_docnum = "
<h1>".$msg['demandes_gestion']."</h1>
<h2>!!path!!</h2>
<form class='form-$current_module' ENCTYPE='multipart/form-data' name='explnum' method='post' action='./demandes.php?categ=action'>
<h3>!!form_title!!</h3>
<input type='hidden' id='idaction' name='idaction' value='!!idaction!!'/>
<input type='hidden' id='iddocnum' name='iddocnum' value='!!iddocnum!!'/>
<input type='hidden' id='act' name='act'/>
<div class='form-contenu' >
	<div class='row'>
		<label class='etiquette' for='f_nom'>".$msg['explnum_nom']."</label>
	</div>
	<div class='row'>
		<input type='text' id='f_nom' name='f_nom' class='saisie-80em'  value='!!nom!!' />
	</div>
	<div class='row'>
		<label class='etiquette' for='f_fichier'>".$msg['explnum_fichier']."</label>
	</div>
	<div class='row'>
		<input type='file' id='f_fichier' name='f_fichier' class='saisie-80em' size='65' />
	</div>
	<div class='row'>
		<label class='etiquette' for='f_url'>".$msg['demandes_url_docnum']."</label>
	</div>
	<div class='row'>
		<input type='text' id='f_url' name='f_url' class='saisie-80em' size='65' value='!!url_doc!!'/>
	</div>
	<div class='row'>
		<input type='checkbox' name='ck_prive' id='ck_prive' value='1' !!ck_prive!! />
		<label for='ck_prive' class='etiquette'>".$msg['demandes_note_privacy']."</label>
	</div>
	<div class='row'>
		<input type='checkbox' name='ck_rapport' id='ck_rapport' value='1' !!ck_rapport!!/>
		<label for='ck_rapport' class='etiquette'>".$msg['demandes_docnum_rapport']."</label>
	</div>
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='$msg[76]' onClick=\"!!cancel_action!!\" />
		<input type='submit' class='bouton' value='$msg[77]' onClick='this.form.act.value=\"save_docnum\" ; ' />
	</div>
	<div class='right'>
		!!suppr_btn!!
	</div>
</div>
<div class='row'></div>
</form>
";

$form_see_docnum = "
<form class='form-$current_module' ENCTYPE='multipart/form-data' name='act_docnum' method='post' action='./demandes.php?categ=action' >
<input type='hidden' id='idaction' name='idaction' value='!!idaction!!'/>
<input type='hidden' id='act' name='act' />
<h3>".$msg['demandes_attach_docnum_lib']."</h3>
<div class='form-contenu' >
		!!list_docnum!!
</div>
<div class='row'>
	<input type='submit' class='bouton' value='".$msg['explnum_ajouter_doc']."' onClick='this.form.act.value=\"add_docnum\" ; ' />
</div>
<div class='row'></div>
</form>
";

$form_communication = "
	<form class='form-$current_module' name='com_form' method='post' action='!!action!!'>
		<h3>!!form_title!!</h3>
		<input type='hidden' name='act' id='act'>
		<div class='form-contenu' >
			<table>
				<tbody>
					<tr>
						<th>&nbsp;</th>
						<th>".$msg['demandes_action_sujet']."</th>
						<th>".$msg['demandes_action_detail']."</th>				
						<th>".$msg['demandes_action_date']."</th>
						<th>".$msg['demandes_action_time_elapsed']." (".$msg['demandes_action_time_unit'].")</th>
						<th>".$msg['demandes_action_progression']."</th>
						<th>&nbsp;</th>
					</tr>
					!!liste_comm!!				
				</tbody>
			</table>
		</div>
		<div class='row'>
			!!btn_action!!
		</div>
	</form>
";

?>