<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_actions.tpl.php,v 1.17 2023/08/17 09:47:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $form_liste_action;
global $form_modif_action;
global $form_consult_action;
global $form_see_docnum;
global $msg, $current_module;

$form_liste_action ="
<script src='./includes/javascript/demandes.js' ></script>
<script src='./includes/javascript/dynamic_element.js' ></script>
<script>

var base_path = '.';
var imgOpened = new Image();
imgOpened.src = '".get_url_icon("minus.gif")."';
var imgClosed = new Image();
imgClosed.src = '".get_url_icon("plus.gif")."';
var imgPatience =new Image();
imgPatience.src = '".get_url_icon("patience.gif")."';
var expandedDb = '';

function expand_note(el, id_action , unexpand) {
	if (!isDOM){
    	return;
	}

	var whichEl = document.getElementById(el + 'Child');
	var whichElTd = document.getElementById(el + 'ChildTd');
	var whichIm = document.getElementById(el + 'Img');

  	if(whichEl.style.display == 'none') {
		if(whichElTd.innerHTML==''){
			var req = new http_request();
			req.request('./ajax.php?module=ajax&categ=demandes&quoifaire=show_dialog',true,'id_action='+id_action,true,function(data){
		  		whichElTd.innerHTML=data;
		  		window.location.href=\"#fin\";
			});
		}
		whichEl.style.display  = '';
    	if (whichIm){
    		whichIm.src= imgOpened.src;
    	}
    	changeCoverImage(whichEl);
		window.location.href=\"#fin\";
	}else if(unexpand) {
    	whichEl.style.display='none';
    	if (whichIm){
    		whichIm.src=imgClosed.src;
    	}
  	}
}

function change_read(el, id_action) {
	if (!isDOM){
    	return;
	}
	var whichEl = document.getElementById(el);
	var whichIm1 = document.getElementById(el + 'Img1');
	var whichIm2 = document.getElementById(el + 'Img2');
	var whichTr = whichIm1.parentNode.parentNode;

	var req = new http_request();
	req.request('./ajax.php?module=demandes&categ=action&quoifaire=change_read',true,'id_action='+id_action,true,function(data){
 		if(data == 1){
			if(whichIm1.style.display == ''){
				whichIm1.style.display = 'none';
				whichIm2.style.display = '';
			} else {
				whichIm1.style.display = '';
				whichIm2.style.display = 'none';
			}

			if(whichIm1.parentNode.parentNode.style.fontWeight == ''){
				whichIm1.parentNode.parentNode.style.fontWeight = 'bold';

			} else {
				whichIm1.parentNode.parentNode.style.fontWeight = '';

			}
 		}
	});
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
		alert(\"".$msg['demandes_actions_nocheck']."\");
		return false;
	}

	var sup = confirm(\"".$msg['demandes_confirm_suppr']."\");
	if(!sup)
		return false;
	return true;
}

function alert_progression(){
	alert(\"".$msg['demandes_progres_ko']."\");
}

function alert_cout(){
  	alert(\"".$msg['demandes_action_cout_ko']."\");
}

function alert_temps(){
   	alert(\"".$msg['demandes_action_time_ko']."\");
}

</script>
<style type=\"text/css\">
	.even{
		background-color: rgba(182,225,243,0.5);
	}
	.odd{
		background-color: rgba(134,236,159,0.5);
	}
	.surbrillance{
		background: rgb(104,168,196);
	}
</style>

	<input type='hidden' name='act' id='act' />
	<input type='hidden' id='iddemande' name='iddemande' value='!!iddemande!!'/>
	<input type='hidden' id='last_modified' value='!!last_modified!!'/>
	<h3>".$msg['demandes_action_liste']."</h3>
	<div class='form-contenu'>
		!!liste_action!!
	</div>
	<div class='row'></div>

<script>
!!script_expand!!
</script>

";


$form_modif_action = "
<script src='./includes/javascript/demandes.js' ></script>
<form class='form-".$current_module."' id='modif_action' name='modif_action' method='post' action=\"!!form_action!!\">
	<input type='hidden' id='act' name='act' />
	<input type='hidden' id='idaction' name='idaction' value='!!idaction!!'/>
	<input type='hidden' id='iddemande' name='iddemande' value='!!iddemande!!'/>
	<input type='hidden' id='idtype' name='idtype' value='!!idtype!!'/>
	<input type='hidden' id='idstatut' name='idstatut' value='!!idstatut!!'/>

	<input type='hidden' id='time_elapsed' name='time_elapsed' value='!!time_elapsed!!'/>
	<input type='hidden' id='progression' name='progression' value='!!progression!!'/>
	<input type='hidden' id='cout' name='cout' value='!!cout!!'/>
	<input type='hidden' id='ck_prive' name='ck_prive' value='!!ck_prive!!'/>

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
				!!libelle_type!!
			</div>
			<div class='colonne3'>
				!!libelle_statut!!
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
			<input type='date' name='date_debut' id='date_debut' value='!!date_debut!!' />
			</div>
			<div class='colonne3'>
				<input type='date' name='date_fin' id='date_fin' value='!!date_fin!!' />
				</div>
			<div class='colonne3'>&nbsp;</div>
		</div>
		<div class='row'></div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='$msg[76]' onClick=\"!!cancel_action!!\" />
			<input type='submit' class='bouton' value='$msg[77]' onClick='this.form.act.value=\"save_action\" ; return test_form(this.form); ' />
		</div>
	</div>
	<div class='row'></div>
</form>
<div class='row' id='docnum'>
</div>
<script>
	function test_form(form) {

		if(isNaN(form.progression.value) || form.progression.value > 100 || form.progression.value < 0){
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

		var deb = dijit.byId('date_debut').get('value');
		var end = dijit.byId('date_fin').get('value');

 		var date_debut = dojo.date.stamp.toISOString(deb, {selector: 'date'});
 		var date_fin = dojo.date.stamp.toISOString(end, {selector: 'date'});

	    if(date_debut > date_fin){
	    	alert(\"$msg[demandes_date_ko]\");
	    	return false;
	    }

		return true;

	}
</script>
";

$form_consult_action = "
<script src='./includes/javascript/demandes.js' ></script>
<form class='form-".$current_module."' id='see_action' name='see_action' method='post' action=\"./empr.php?tab=request&lvl=list_dmde&sub=open_demande\">
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
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['demandes_retour']."' onClick=\"document.location='./empr.php?tab=request&lvl=list_dmde&sub=open_demande&view=all!!params_retour!!'\"/>
		</div>
	</div>
	<div class='row'><br /></div>
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
<div class='row'></div>
</form>
";

?>