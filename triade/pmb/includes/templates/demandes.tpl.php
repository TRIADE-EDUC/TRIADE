<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes.tpl.php,v 1.41.2.1.2.2 2025/05/16 13:30:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $current_module, $current_module;
global $js_form_modif_demande, $form_consult_dmde, $form_liste_docnum, $form_reponse_final, $reponse_finale, $charset;

$js_form_modif_demande = "
<script src='".$base_path."/javascript/ajax.js'></script>
<script type='text/javascript'>
	function test_form(form) {	
		if(isNaN(form.progression.value) || form.progression.value > 100 || form.progression.value < 0 ){
	    	alert(\"".$msg['demandes_progres_ko']."\");
			return false;
	    }
		if((form.titre.value.length == 0) ||  (form.empr_txt.value.length == 0) || (form.date_debut.value.length == 0)||  (form.date_fin.value.length == 0)){
			alert(\"".$msg['demandes_create_ko']."\");
			return false;
	    } 
	    
	    var deb =form.date_debut.value;
	    var fin = form.date_fin.value;
	   
	    if(deb>fin){
	    	alert(\"".$msg['demandes_date_ko']."\");
	    	return false;
	    }
		return check_form();
			
	}

	function update_pperso(event) {
	    let idType = parseInt(event.target.value);

		let url='./ajax.php?module=demandes&categ=dmde&quoifaire=get_pperso_form&id_demande=!!iddemande!!&type_demande=' + idType;
		let xhr_object=  new http_request();
		xhr_object.request(url);

		let div = document.getElementById('demandes_pperso');
		if(div) {
			div.innerHTML = xhr_object.get_text();
		}
	}

</script>
";

$form_consult_dmde = "
<h1>".$msg['demandes_gestion']." : ".$msg['admin_demandes']."</h1>
<script src='./javascript/demandes.js' type='text/javascript'></script>
<script src='./javascript/tablist.js' type='text/javascript'></script>
<script src='./javascript/select.js' type='text/javascript'></script>
<script type='text/javascript'>
	function confirm_delete(){
		
		var sup = confirm(\"".$msg['demandes_confirm_suppr']."\");
		if(!sup)
			return false;
		return true;	
	}
	
	function alert_progressiondemande(){
		alert(\"".$msg['demandes_progres_ko']."\");
	}
</script>
<form class='form-".$current_module."' id='see_dmde' name='see_dmde' method='post' action=\"./demandes.php?categ=gestion\">
	<h3>!!icone!!!!form_title!!</h3>
	<input type='hidden' id='iddemande' name='iddemande' value='!!iddemande!!'/>
	<input type='hidden' id='act' name='act' />
	<input type='hidden' id='state' name='state' />
	<div class='form-contenu'>
		!!content_form!!	
		<div class='row'></div>
		<div class='row'>
			!!champs_perso!!
		</div>
		<div class='row'></div>
	</div>
	<div class='row'>
		!!btn_etat!!
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['demandes_retour']."' onClick=\"document.location='./demandes.php?categ=list!!params_retour!!'\" />
			<input type='submit' class='bouton' value='$msg[62]' onClick='this.form.act.value=\"modif\" ; ' />			
			!!btns_notice!!
			!!btn_audit!!
			!!btn_repfinal!!
			!!btn_faq!!
		</div>
		<div class='right'>
			!!btn_suppr_notice!!
			<input type='submit' class='bouton' value='".$msg['demandes_delete']."' onClick='this.form.act.value=\"suppr_noti\" ; return confirm_delete();' />
		</div>
	</div>
	<div class='row'></div>
</form>
";

$form_liste_docnum ="
<form class='form-".$current_module."' id='liste_action' name='liste_action' method='post'>
	<h3 id='htitle'>".$msg['demandes_liste_docnum']."</h3>
	<input type='hidden' id='act' name='act' />
	<input type='hidden' id='iddemande' name='iddemande' value='!!iddemande!!'/>
	<div class='form-contenu' >
		<div class='row'>
			!!liste_docnum!!	
		</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['demandes_retour']."' onClick=\"history.go(-1)\" />
			!!btn_attach!!	
		</div>
		<div class='right'>
			<input type='button' class='bouton' name='btn_chk' id='btn_chk' value='".$msg['tout_cocher_checkbox']."' onClick=\"check_all('liste_action','chk',true);\" />
			<input type='button' class='bouton' name='btn_chk' id='btn_chk' value='".$msg['tout_decocher_checkbox']."' onClick=\"check_all('liste_action','chk',false);\" />
			<input type='button' class='bouton' name='btn_chk' id='btn_chk' value='".$msg['inverser_checkbox']."' onClick=\"inverser('liste_action','chk');\" />
		</div>
	</div>
	
</form>

<script type='text/javascript'>

function check_all(the_form,the_objet,do_check){

	var elts = document.forms[the_form].elements[the_objet+'[]'] ;
	var elts_cnt  = (typeof(elts.length) != 'undefined')
              ? elts.length
              : 0;

	if (elts_cnt) {
		for (var i = 0; i < elts_cnt; i++) {
			elts[i].checked = do_check;
		} 
	} else {
		elts.checked = do_check;
	}
	return true;
}

function inverser(the_form,the_objet){

	var elts = document.forms[the_form].elements[the_objet+'[]'] ;
	var elts_cnt  = (typeof(elts.length) != 'undefined')
              ? elts.length
              : 0;

	if (elts_cnt) {
		for (var i = 0; i < elts_cnt; i++) {
			if(elts[i].checked == true) elts[i].checked = false;
			else elts[i].checked = true;
		} 
	} 
	return true;
}

 function verifChk() {
		
	var elts = document.forms['liste_action'].elements['chk[]'];
	var elts_cnt  = (typeof(elts.length) != 'undefined')
              ? elts.length
              : 0;
	nb_chk = 0;
	if (elts_cnt) {
		for(var i=0; i < elts.length; i++) {
			if (elts[i].checked) nb_chk++;
		}
	} else {
		if (elts.checked) nb_chk++;
	}
	if (nb_chk == 0) {
		var sup = confirm(\"".$msg['demandes_confirm_attach_docnum']."\");
		if(!sup) 
			return false;
		return true;
	}
	
	return true;
}
</script>
";

$form_reponse_final = "
<h1>".$msg['demandes_gestion']." : ".$msg['admin_demandes']."</h1>
<form class='form-".$current_module."' id='dmde' name='dmde' method='post' action=\"!!form_action!!\">
	<h3>!!titre_dmde!!</h3>
	<input type='hidden' id='iddemande' name='iddemande' value='!!iddemande!!'/>
	<input type='hidden' id='act' name='act' />	
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_theme']." : </label>
				!!theme_dmde!!
			</div>			
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_sujet']." : </label>
				!!sujet_dmde!!
			</div>			
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".$msg['demandes_type']." : </label>
				!!type_dmde!!
			</div>			
		</div>		
		<div class='row'></div>
	</div>	
</form>
<form class='form-".$current_module."' id='formrepfinale' name='formrepfinale' method='post' action=\"!!form_action!!\">
	<h3>!!form_title!!</h3>
	<input type='hidden' id='iddemande' name='iddemande' value='!!iddemande!!'/>
	<input type='hidden' id='act' name='act' />	
	<div class='form-contenu'>
		
	<div class='row'>
		<textarea id='f_message' name='f_message' wrap='virtual' cols='55' rows='4' >!!reponse!!</textarea>
	</div>
	<div class='row'>
		<div class='left'>			
			<input type='button' class='bouton' value='$msg[76]' onClick=\"!!cancel_action!!\" />
			<input type='submit' class='bouton' value='$msg[77]' onclick='this.form.act.value=\"save_repfinale\"'/>
		</div>
		<div class='right'>
			!!btn_suppr!!
		</div>
	</div>					
	<div class='row'></div>
	</div>
</form>
";

$reponse_finale = "
<form class='form-".$current_module."' id='repfinale' name='formrepfinale' method='post' action=\"!!form_action!!\">
	<h3>".htmlentities($msg['demandes_reponse_finale'],ENT_QUOTES,$charset)."</h3>
		<input type='hidden' id='iddemande' name='iddemande' value='!!iddemande!!'/>
		<input type='hidden' id='act' name='act' />	
		<div class='form-contenu'>		
			<div class='row'>!!repfinale!!</div>
			<div class='row'></div>
		</div>							
		<div class='row'>
			<div class='left'>			
				<input type='submit' class='bouton' value='".$msg['demandes_repfinale_modif']."' onclick='this.form.act.value=\"final_response\" ; ' />&nbsp;
			</div>
			<div class='right'>	
				<input type='submit' class='bouton' value='".$msg['demandes_repfinale_delete']."' onClick='this.form.act.value=\"suppr_repfinale\" ; return confirm_delete();' />	
			</div>
		</div>
		<div class='row'></div>
	</form>	
";

