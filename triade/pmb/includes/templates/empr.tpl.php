<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr.tpl.php,v 1.197 2019-05-27 13:55:33 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $group_id, $force_finance, $short_loan, $empr_list_tmpl, $empr_search_cle_tmpl, $msg, $script0, $pmb_rfid_activate, $script1, $script2, $empr_cb_tmpl, $pmb_rfid_serveur_url, $empr_cb_tmpl, $login_empr_pret_tmpl, $current_module, $empr_cb_tmpl_create, $rfid_port, $pmb_rfid_pret_mode, $empr_pret_allowed, $pmb_short_loan_management, $short_loan, $deflt_short_loan_activate, $pmb_printer_name, $pdfcartelecteur_printer_card_handler, $base_path, $empr_tmpl_consultation, $ldap_accessible, $ldap_accessible, $empr_birthdate_optional, $groupID, $PMBuserid, $pmb_form_editables, $pmb_opac_view_activate, $empr_edit_tmpl, $empr_tmpl_fiche_affichage, $empr_autre_compte_tmpl, $empr_comptes_tmpl, $empr_retard_tpl, $empr_pnb_loans_tmpl;

if(!isset($group_id)) $group_id = 0;
if(!isset($force_finance)) $force_finance = 0;
if(!isset($short_loan)) $short_loan = 0;


// templates pour les forms emprunteurs
//   ----------------------------------

// template pour la liste emprunteurs
$empr_list_tmpl = "
!!empr_search_cle_tmpl!!
!!filters_list!!

!!list!!

<div class='row'>
!!nav_bar!!
</div>
";

$empr_search_cle_tmpl = "<h1>$msg[57] \"<strong>!!cle!!</strong>\" !!where_intitule!! <!--!!nb_total!!--></h1>"; 
// -----------------------------------

// script1 - script2
// niveau de test sur le form de saisie cl? emprunteur
// script0 : aucun test
// script1 : on peut saisir des lettres
// script2 : on ne peut pas saisir des lettres
$script0 = "
<script type='text/javascript'>
<!--
function test_form(form)
	{
		return true;
	}
-->
</script>
";
if ($pmb_rfid_activate==1 ) {
	$num_empr_rfid_test="if(0)";	
} else 	{
	$num_empr_rfid_test='';
}	
$script1 = "
<script type='text/javascript'>
<!--
function test_form(form)
	{
		$num_empr_rfid_test
		if(form.form_cb.value.replace(/^\s+|\s+$/g,'').length == 0)
			{
				alert(\"$msg[326]\");
				form.form_cb.focus();
				return false;
			}
		return true;
	}
-->
</script>
";
$script2 = "
<script type='text/javascript'>
<!--
function test_form(form)
	{
		if(form.form_cb.value.replace(/^\s+|\s+$/g,'').length == 0)
			{
				alert(\"$msg[326]\");
				form.form_cb.focus();
				return false;
			}
		var exp = new RegExp('[a-zA-Z]','g');
		if(exp.test(form.form_cb.value))
			{
				alert(\"$msg[327]\");
				form.form_cb.value = '';
				form.form_cb.focus();
				return false;
			}
		return true;
	}
-->
</script>
";

// $empr_cb_tmpl : template pour le form de saisie code-barre en recherche
$empr_cb_tmpl = "
<script type='text/javascript'>
<!--
function aide_search_empr() {
		openPopUp('./help.php?whatis=search_empr', 'regex_howto');
	}
function test_form(form) {
	if (form.form_cb.value.replace(/^\s+|\s+$/g,'').length == 0) {
		form.form_cb.value='*';
		}
	return true;
	}

-->

</script>";
if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url ) {
	$empr_cb_tmpl .=$rfid_js_header;
}
$empr_cb_tmpl .="!!script!!

<h1>!!title!!</h1>
<form class='form-$current_module' id='saisie_cb_ex' name='saisie_cb_ex' method='post' action='!!form_action!!' onSubmit='return test_form(this)'>
<h3>!!titre_formulaire!!</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_cb'>!!message!!</label>
		</div>
	<div class='row'>
		<input class='saisie-20em' id='form_cb' type='text' name='form_cb' value='!!cb_initial!!' title='$msg[3000]' /> !!restrict_location!!
	</div>
</div>
<div class='row'>
	<div class='left'>
		<input type='submit' class='bouton' value='$msg[502]'/>
		<input type='button' class='bouton' value='".$msg['empr_search_advanced']."' onclick='document.location=\"./circ.php?categ=search\"'>
	</div>
	<div class='right'></div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
document.forms['saisie_cb_ex'].elements['form_cb'].focus();";
if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url ) {
	$empr_cb_tmpl .="init_rfid_empr();";
}
$empr_cb_tmpl .="</script>";

// $login_empr_pret_tmpl : template pour le form de saisie login/password en mode circ restreint
$login_empr_pret_tmpl = "
<script type='text/javascript'>
<!--
function test_form(form) {
	if (form.form_login.value.replace(/^\s+|\s+$/g,'').length == 0 || form.form_password.value.replace(/^\s+|\s+$/g,'').length == 0) {
		return false;
		}
	return true;
	}
-->
</script>
<h1>!!title!!</h1>
<form class='form-$current_module' id='saisie_empr_login_password' name='saisie_empr_login_password' method='post' action='!!form_action!!' onSubmit='return test_form(this)'>
<h3>!!titre_formulaire!!</h3>
<div class='form-contenu'>
<div class='colonne3'>
	<div class='row'>
		<label class='etiquette' for='form_login'>".$msg['empr_login']."</label>
		</div>
	<div class='row'>
		<input class='saisie-20em' id='form_login' type='text' name='form_login' value='' title=\"".$msg['empr_login']."\" />
		</div>
	</div>
<div class='colonne_suite'>
	<div class='row'>
		<label class='etiquette' for='form_password'>".$msg['empr_password']."</label>
		</div>
	<div class='row'>
				<input class='saisie-20em' id='form_password' type='password' name='form_password' value='' title=\"".$msg['empr_password']."\" />
</div>
	</div>
<div class='row'>&nbsp;</div>
</div>

<div class='row' >
	<input type='submit' class='bouton' value='$msg[502]'/>
	</div>
</form>
<script type='text/javascript'>
document.forms['saisie_empr_login_password'].elements['form_login'].focus();
</script>";

// $empr_cb_tmpl_create : template pour le form de saisie code-barre en création
$empr_cb_tmpl_create = "
!!script!!
<h1>!!title!!</h1>
<form class='form-$current_module' id='saisie_cb_ex' name='saisie_cb_ex' method='post' action='!!form_action!!' onSubmit='return test_form(this)'>
<h3>!!titre_formulaire!!</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_cb'>!!message!!</label>
	</div>
	<div class='row'>
		<input class='saisie-20em' id='form_cb' type='text' name='form_cb' value='!!cb_initial!!'  />
	</div>
</div>
<div class='row'>
	<input type='submit' class='bouton' value='$msg[502]' />
	</div>
</form>
<script type='text/javascript'>
document.forms['saisie_cb_ex'].elements['form_cb'].focus();
</script>
";

if ($pmb_rfid_activate==1 ) {
	if(!$rfid_port) $rfid_port= get_rfid_port();
	if($pmb_rfid_serveur_url) {
		$indicateur_rfid="<img src='".get_url_icon('sauv_succeed.png')."' id='indicateur' class='align_top' ><span  class='erreur' id='indicateur_nb_doc'></span>";
	}	
	if( $pmb_rfid_serveur_url){
		$script_rfid_antivol="
		
		<script type='text/javascript'>
			setTimeout(\"init_rfid_pret(!!id!!,'!!cb!!',$pmb_rfid_pret_mode);\",0);			
			window.onfocus=function(){rfid_focus_active=1;}
			window.onblur=function(){rfid_focus_active=0;}
		</script>
		";
		
	} else {
		$script_rfid_antivol="
		<script type='text/javascript'>
		init_sans_rfid_pret(!!id!!,'!!cb!!');
		</script>";			
	}	
	if($pmb_rfid_pret_mode)
	$rfid_input_cb="<input type='text' class='saisie-15em' id='cb_doc' name='cb_doc' tabindex='1' value='' /><input  type='button'  id='ajouter' onClick=\"if(document.getElementById('cb_doc').value) flag_error =mode1_add_cb(document.getElementById('cb_doc').value);document.getElementById('cb_doc').value=''\" name='ajouter' class='bouton' value='$msg[925]' />";
	else
	$rfid_input_cb="<input type='text' class='saisie-15em' id='cb_doc' name='cb_doc' tabindex='1' value='' /><input  type='button'  id='ajouter' onClick=\"mode_lecture_cb[document.getElementById('cb_doc').value]='cb';flag_error =Ajax_add_cb(document.getElementById('cb_doc').value);\" name='ajouter' class='bouton' value='$msg[925]' />";
	
	$empr_pret_allowed="
		<div id='loan_zone' >
			<div class='row'>
				<div class='left'>
					$rfid_js_header
					<script src='./javascript/rfid/rfid_pret.js'></script>
						
					$script_rfid_antivol
					$rfid_input_cb
					$indicateur_rfid
					".(($pmb_short_loan_management==1)?"<br /><span id='short_loan_msg' class='short_loan_msg'>".((($short_loan==1) || (!$short_loan && $deflt_short_loan_activate))?$msg['short_loan_enabled']:$msg['short_loan_disabled']).'</span>':'')."
				</div>
				<div class='right'>
					<input type='button' name='express' id='express' class='bouton' value='".$msg['pret_express']."' onClick=\"document.location='./circ.php?categ=express&id_empr=!!id!!&groupID=$groupID".(($pmb_short_loan_management==1)?"&short_loan='+document.getElementById('short_loan').value;":"'")."\" />
					<!-- short_loan -->
				</div>
			</div>
			<div class='row'>
				<table id='table_pret_tmp' name='table_pret_tmp'>
				</table>
			</div>
			<div class='row' id='div_confirm_pret' style='display:none'>
			<h3><input type='button' name='confirm_pret' id='confirm_pret' class='bouton' tabindex='2' value='".$msg['bt_confirm_pret']."' onClick=\"Ajax_confirm_pret();\"/>
		    	&nbsp;<label id='nb_tmp_pret'></label></h3>
			</div>
		</div>
		";
} else {
	$empr_pret_allowed="
	<div id='loan_zone' >
		<div class='left'>
			<!-- custom_fields -->
			<input type='text' class='saisie-15em' id='cb_doc' name='cb_doc' value='' /><input type='submit' name='ajouter' class='bouton' value='$msg[925]' onClick=\"if (check_form(this.form)) {this.form.submit();} else return false;\" />
			".(($pmb_short_loan_management==1)?"<br /><span id='short_loan_msg' class='short_loan_msg'>".((($short_loan==1) || (!$short_loan && $deflt_short_loan_activate))?$msg['short_loan_enabled']:$msg['short_loan_disabled']).'</span>':'')."
		</div>
		<div class='right'>
			<input type='button' name='express' class='bouton' value='".$msg['pret_express']."' onClick=\"document.location='./circ.php?categ=express&id_empr=!!id!!&groupID=$groupID".(($pmb_short_loan_management==1)?"&short_loan='+document.getElementById('short_loan').value;":"'")."\" />
			<!-- short_loan -->
		</div>
	</div>
	";
}

if ($pmb_short_loan_management==1) {
	$short_loan_bt = "
			<input type='button' class='bouton' name='short_loan_bt' id='short_loan_bt' value='".((($short_loan==1) || (!$short_loan && $deflt_short_loan_activate))?$msg['short_loan_disable']:$msg['short_loan_enable'])."' onclick='flip_short_loan();' />
			<input type='hidden' id='short_loan' name='short_loan' value='".((($short_loan==1) || (!$short_loan && $deflt_short_loan_activate))?1:0)."' />
			<script type='text/javascript'>
				function flip_short_loan() {
					var short_loan=document.getElementById('short_loan');
					var short_loan_bt=document.getElementById('short_loan_bt');
					var short_loan_msg=document.getElementById('short_loan_msg');
					var loan_zone=document.forms['pret_doc'];
					if (short_loan.value==0) {
						loan_zone.setAttribute('style','background-color:red;');
						short_loan_msg.innerHTML='".$msg['short_loan_enabled']."';
						short_loan_bt.value='".$msg['short_loan_disable']."';
						short_loan.value=1;
					} else {
						loan_zone.removeAttribute('style');
						short_loan_msg.innerHTML='".$msg['short_loan_disabled']."';
						short_loan_bt.value='".$msg['short_loan_enable']."';
						short_loan.value=0;
					}
					if (document.forms['pret_doc'].elements['cb_doc']!=undefined){
   						document.forms['pret_doc'].elements['cb_doc'].focus();
					}
				}
				if(document.getElementById('short_loan').value==1) {
					document.forms['pret_doc'].setAttribute('style','background-color:red;');
				}
			</script>";
	$empr_pret_allowed = str_replace('<!-- short_loan -->',$short_loan_bt,$empr_pret_allowed);
}

$printer_ticket_script = '';
$printer_ticket_link = '';

if($pmb_printer_name || $pdfcartelecteur_printer_card_handler==2) {
	
	$printer_ticket_script = "
	<div id='printer_script'></div>
	<script type='text/javascript'>
		
		function printer_get_jzebra() {
			if(!document.jzebra) {
				var req = new http_request();
				req.request('$base_path/ajax.php?module=circ&categ=zebra_print_pret&sub=get_script');
				document.getElementById('printer_script').innerHTML=req.get_text();
				return false;
			}
		}
		
		function printer_jzebra_send_ticket(text,printer,encoding) {
			var applet = document.jzebra;
			var found=false;
			if(applet!=null) {
				applet.findPrinter(printer);
				while (!applet.isDoneFinding()) {}
				if(printer == applet.getPrinter()) {
					found = true; 
					if(encoding) {
						applet.setEncoding(encoding);
					}
					applet.append(text);
					applet.print();
				}
			}
			if(!found) {		     	     				
         		alert('".$msg['printer_not_found']."');
         	}
        } 
         				
        function printer_raspberry_send_ticket(url) {
         	
         	var req = new http_request();
         	var tpl;
         	var printer = '';
			var printer_id = 0;
         	var raspberry_ip = '';
			var printer_type = '';
         	
         	//Quelle est l'imprimante sélectionnée ?
         	if (req.request('./ajax.php?module=circ&categ=zebra_print_pret&sub=get_selected_printer')) {
				alert ( req.get_text() );
			} else {
				printer = req.get_text();
			}
			if (printer == '') {
				alert('".$msg['user_printer_not_found']."');
				return;
			}
			
			var temp = printer.split('@');
			printer_id = temp[0];
			raspberry_ip = temp[1];

			//On interroge le raspberry pour connaitre le type d'imprimante (et savoir si elle est bien sur ce raspberry)
			if (req.request('https://' + raspberry_ip + '/getPrinter?idPrinter=' + printer_id)) {
				alert ( req.get_text() );
			} else {
				printer_type = req.get_text();
			}
			if (printer_type == '' || printer_type == 'unknown') {
				alert('".$msg['user_printer_type_not_found']."');
				return;
			}

			//On va générer le template en fonction de l'imprimante
			url = url + '&printer_type=' + printer_type;
			if(req.request(url)){
				alert ( req.get_text() );
			} else {
				tpl = req.get_text();
			}
         	if (tpl.length == 0) {
         		alert('".$msg['printer_tpl_error']."');
         		return;
         	}

			//On envoie l'impression
			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'https://' + raspberry_ip + '/print?', true);
			xhr.setRequestHeader('Content-type', 'text/plain;charset=utf-8');
			xhr.send(JSON.stringify({idPrinter:printer_id,xml:tpl}));
						
         	return;
         				
        }
		
	</script>";
}
if($pmb_printer_name) {
	if (substr($pmb_printer_name,0,9) == 'raspberry') {
		$printer_ticket_script.= "
		<script type='text/javascript'>
		
			function printer_jzebra_print_ticket(url) {
				printer_raspberry_send_ticket(url);
			}
		</script>
		";
	} else {
		$printer_ticket_script.= "
		<script type='text/javascript'>
	
			function printer_jzebra_print_ticket(url) {
				printer_get_jzebra();
				var req = new http_request();
				if(req.request(url)){
					// Il y a une erreur.
					alert ( req.get_text() );
				}else {
					printer_jzebra_send_ticket(req.get_text(),'".$pmb_printer_name."','850');
					return 1;
				}
			}
		</script>
		";
	}
	$printer_ticket_link="<a href='#' onclick=\"printer_jzebra_print_ticket('./ajax.php?module=circ&categ=zebra_print_pret&sub=all&id_empr=!!id!!'); return false;\"><img src='".get_url_icon('print.gif')."' alt='".htmlentities($msg['print_print'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['print_print'],ENT_QUOTES,$charset)."' class='align_middle' border='0'></a>";

}else if($pmb_printer_ticket_url) {
	$printer_ticket_script="		
	<script type='text/javascript'>
	function send_print_ticket(cmd) {
		// Construction de la requete 
		var url='$pmb_printer_ticket_url'; 
		// On initialise la classe:
		var req = new http_request();
		
		if(typeof netscape !== 'undefined') {
			if(netscape.security.PrivilegeManager)netscape.security.PrivilegeManager.enablePrivilege('UniversalBrowserRead');
		}
		// Execution de la requete
		if(req.request(url,1,'xml='+".pmb_escape()."(cmd))){
			// Il y a une erreur. Afficher le message retourne
			alert ( req.get_text() );			
		}else { 
			// la commande est bien passee
			return 1;	
		}
	}		
	function print_ticket(url) {
		// Construction de la requete 
		// On initialise la classe:
		var req = new http_request();
		
		if(typeof netscape !== 'undefined') {
			if(netscape.security.PrivilegeManager)netscape.security.PrivilegeManager.enablePrivilege('UniversalBrowserRead');
		}
		// Execution de la requete
		if(req.request(url)){
			// Il y a une erreur. Afficher le message retourne
			alert ( req.get_text() );			
		}else { 
			// la commande est bien passee		
			send_print_ticket(req.get_text());
			return 1;	
		}
	}
	</script>";
	
	$printer_ticket_link="&nbsp;<a href='#' onclick=\"print_ticket('./ajax.php?module=circ&categ=print_pret&sub=all&id_empr=!!id!!'); return false;\"><img src='".get_url_icon('print.gif')."' alt='Imprimer...' title='Imprimer...' class='align_middle' border='0'></a>";
}
									
$empr_tmpl = "
$printer_ticket_script
<!-- script de confirmation de suppression -->
<script type=\"text/javascript\">
	
	function confirm_delete()
	{
		result = confirm(\"${msg[932]}\");
		if(result)
				document.location = \"./circ.php?categ=empr_delete&id=!!id!!&form_cb=!!cb!!&groupID=$groupID\";
		else
				document.forms['pret_doc'].elements['cb_doc'].focus();
	}

	function check_cb(form)
	{
		x=document.forms['prolong_bloc'].elements['id_bloc'].value;
	    y=form.id_doc.value;
		z='';

		patt=new RegExp(' '+y+' ','g');

		if (patt.test(x)) 
			z=x.replace(patt,'');
		else
			z=x+' '+y+' ';
		document.forms['prolong_bloc'].elements['id_bloc'].value = z;
	}

	function check_allcb(form)
	{
	    y=form.id_inpret.value;
		ids=y.split('|');
		while (ids.length>0) {
			id=ids.shift();
			if (document.forms['prolong'+id].elements['cbox_prol']) document.forms['prolong'+id].elements['cbox_prol'].click();
		}
	}
	
	function see_all_loan(form) {
		if(confirm(pmbDojo.messages.getMessage('empr', 'loan_see_all'))) {
			document.location = '!!link_see_all_loan!!';
		} else {
			check_allcb(form);
		}
	}
</script>
<script type='text/javascript' src='./javascript/tablist.js'></script>
<div id=\"el!!id!!Parent\" class=\"notice-parent\">
   		<h1 id='empr-name'><div class='left'><img src=\"".get_url_icon('plus.gif')."\" class=\"img_plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); return false;\">
   		!!image_caddie_empr!! <span class='empr-name h3-like'>!!prenom!! !!nom!!</span> ".$msg['empr_nb_pret'].": !!info_nb_pret!! ".$msg['empr_nb_resa'].": !!info_nb_resa!! !!info_resa_planning!! !!header_format!!</div><div class='right'>!!empr_resume!! !!empr_statut_libelle!!</div></h1>
   		</div>
	<div class='row'><div class='right'>!!empr_picture!!</div></div>
<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-left:7px;display:none;\"!!depliee!!>
<div id='bloc_adresse_empr'>
	<div class='colonne3'>
		<div class='row'>
			!!adr1!!
		</div>
		<div class='row'>
			!!adr2!!
		</div>
		<div class='row'>
			!!cp!!&nbsp;!!ville!!
		</div>
		<div class='row'>
			!!pays!!
		</div>
		<div class='row'>
			<strong>".$msg['empr_fiche_tel']."</strong> !!tel!!
		</div>
		<div class='row'>
			<strong>$msg[58]$msg[1901]</strong> !!mail_all!!
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>
			<strong>$msg[74] : </strong>!!prof!!
		</div>
		<div class='row'>
			<strong>$msg[75] : </strong>!!date!!
		</div>
		<div class='row'>
			<strong>$msg[125] : </strong>!!sexe!!
		</div>
	</div>
	<div class='colonne_suite'></div>


<div class='row'>
<div class='colonne3'>
	<div class='row'>
		<strong>$msg[1403]</strong>
	</div>
	<div class='row'>
		<strong>$msg[1401] : </strong>!!adhesion!!
	</div>
	<div class='row'>
		<strong>$msg[1402] : </strong>!!expiration!!
	</div>
	<div class='row'>
		<strong>".$msg['date_dern_emprunt']." : </strong>!!last_loan_date!!
	</div>
</div>
<div class='colonne3'>
	<div class='row'>
		<strong>$msg[60] : </strong>!!codestat!!
	</div>
	<div class='row'>
		<strong>$msg[59] : </strong>!!categ!!
	</div>
	<div class='row'>
		<strong>$msg[38] : </strong>!!cb!!
	</div>
	!!abonnement!!
</div>
<div class='colonne_suite'>
	<!-- !!localisation!! -->
	<div class='row'>!!groupes!!
	</div>
	<div class='row'>
		<strong>".$msg['empr_login']." : </strong>!!empr_login!!
	</div>
	<div class='row'>
		!!empr_pwd!!
	</div>
</div>
</div>
<div class='row'></div>
!!perso!!
<div class='row'></div>
</div>
</div>";
if ($ldap_accessible)
	$empr_tmpl .= "<div class='row'>
		<strong>".$msg['empr_authldap'].": </strong>!!info_authldap!!
	</div>";
$empr_tmpl .= "
<div class='row'>
	<div class='erreur'>!!empr_date_depassee!!</div>
</div>
<div class='row'>
	<div class='erreur'>!!empr_categ_age_change!!</div>
</div>
<div class='row'>
	<div class='erreur'>!!empr_msg!!</div>
</div>
!!comptes!!
!!relance!!
<hr />
<div class='row'>
	<div class='left' id='empr_form_actions_buttons'>
		<input type='button' name='modifier' class='bouton' value='$msg[62]' onClick=\"document.location='./circ.php?categ=empr_saisie&id=!!id!!&groupID=$groupID';\" />
		<input type='button' name='dupliquer' class='bouton' value='".$msg['empr_duplicate_button']."' onClick=\"document.location='./circ.php?categ=empr_duplicate&id=!!id!!';\" />
		<input type='button' id='imprimercarte' name='imprimercarte' class='bouton' value='".$msg['imprimer_carte']."' onClick=\"openPopUp('./pdf.php?pdfdoc=carte-lecteur&id_empr=!!id!!', 'print_PDF');\" />";

switch ($pdfcartelecteur_printer_card_handler) {

	//script "print_cb.php" a la racine sur le serveur web
	default :
	case '1' :
		
		if (file_exists("print_cb.php")) {
			$empr_tmpl.= "<a href='#' onClick='h=new http_request(); h.request(\"print_cb.php?cb=!!cb!!&label=!!prenom!! !!nom!!\", false,\"\", false, function(){},function(){},\"impr_cb\")' ><img src='".get_url_icon('print.gif')."' alt='".htmlentities($msg['print_print'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['print_print'],ENT_QUOTES,$charset)."' class='align_middle' border='0'></a>";
		}
		break;		
		
	//impression avec applet jzebra
	case '2' :
		
		if (substr($pmb_printer_name,0,9) == 'raspberry') {
			$empr_tmpl.= "
			<script type='text/javascript'>
			
				function printer_jzebra_print_card(url) {
					var req = new http_request();
					if(req.request(url)){
						alert ( req.get_text() );
					}else {
						printer_raspberry_send_ticket(req.get_text());
						return 1;
					}
				}
			</script>
			";
		} else {
			$empr_tmpl.= "
			<script type='text/javascript'>
				function printer_jzebra_print_card(url) {
					
					printer_get_jzebra();
					var req = new http_request();
					if(req.request(url)){
						alert ( req.get_text() );
					} else {
						printer_jzebra_send_ticket(req.get_text(), '".$pdfcartelecteur_printer_card_name."','850');
						return 1;
					}
				}
			</script>";
		}
		//AUCUN TEMPLATE DE CARTE PAR IMPRIMANTE TICKET DE PRET POUR LE MOMENT...
		//$empr_tmpl.= "<a href='#' onclick=\"printer_jzebra_print_card('./ajax.php?module=circ&categ=zebra_print_card&sub=one&id_empr=!!id!!'); return false;\"><img src='".get_url_icon('print.gif')."' alt='".htmlentities($msg['print_print'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['print_print'],ENT_QUOTES,$charset)."' class='align_middle' border='0'></a>";
		
		break;
	
	//impression raw directe	
	case '3' :
		$empr_tmpl.= "
		<script type='text/javascript'>
			function printer_ajax_send_ticket(post_datas) {
				var url = '".$pdfcartelecteur_printer_card_url."';
				var req=new http_request();		
				req.request(url,true,post_datas,true); 	
				window.setTimeout(function(){req.abort();},1000);		
			}			
			function printer_ajax_print_card(url) {
				var req = new http_request();
				if(req.request(url)){
					// Il y a une erreur. 
					alert ( req.get_text() );			
				}else { 
					printer_ajax_send_ticket(req.get_text());
					return 1;	
				}
			}
		</script>
		";
		
		$empr_tmpl.= "<a href='#' onclick=\"printer_ajax_print_card('./ajax.php?module=circ&categ=zebra_print_card&sub=one&id_empr=!!id!!'); return false;\"><img src='".get_url_icon('print.gif')."' alt='".htmlentities($msg['print_print'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['print_print'],ENT_QUOTES,$charset)."' class='align_middle' border='0'></a>";
		break;
		
}

$empr_tmpl .= "
		!!voir_sugg!!
	</div>
	<div class='right'>			
		<input type='button' name='supprimer' class='bouton' value='".$msg['supprimer']."' onClick=\"confirm_delete()\" />
		</div>
	</div>
<br /><br />
";
if ($pmb_rfid_activate==1) {
	if(!$pmb_rfid_pret_mode) {
		$empr_tmpl .= "
		<form class='form-$current_module' name='pret_doc' onsubmit=\"if(!document.getElementById('cb_doc').value && document.getElementById('div_confirm_pret').style.display=='inline'){Ajax_confirm_pret();return false;}
		Ajax_add_cb(document.getElementById('cb_doc').value);return false;\">
		";
	}else {	
		$empr_tmpl .= "
		<form class='form-$current_module' name='pret_doc' onsubmit=\"if(!document.getElementById('cb_doc').value && document.getElementById('div_confirm_pret').style.display=='inline'){mode1_confirm_pret();return false;}
		mode1_add_cb(document.getElementById('cb_doc').value);document.getElementById('cb_doc').value='';return false;\">
		";
	}	
} else {
	$empr_tmpl .= "
	<form class='form-$current_module' name='pret_doc' action='circ.php' method='post'>
	";
}
$th_sur_location="";
$th_sur_location0="";
$th_colspan_shorloan=9;
if($pmb_sur_location_activate){
	$th_sur_location="<th>".$msg['sur_location_expl']."</th>";
	$th_sur_location0="<th></th>";
	$th_colspan_shorloan=10;
}
$empr_tmpl .= "
<h3>".$msg['ajouterpret']."</h3>
<!--
<div class='row'>
	<label for='cb_doc' class='etiquette'>$msg[61]</label>
	</div>
-->
!!empr_case_pret!!
<input type='hidden' name='cb_empr' value='!!cb!!' />
<input type='hidden' name='id_empr' id='id_empr' value='!!id!!' />
<input type='hidden' name='group_id' value='$group_id' />
<input type='hidden' name='groupID' value='$groupID' />
<input type='hidden' name='categ' value='pret' />
<input type='hidden' name='sub' value='pret_suite' />
<input type='hidden' name='force_finance' value='$force_finance' />
<div class='row'></div>
</form>

<script type='text/javascript'>
if (document.forms['pret_doc'].elements['cb_doc']!=undefined){
   document.forms['pret_doc'].elements['cb_doc'].focus();
}	
</script>

<!-- <h3>$msg[379]</h3> -->
<div class='row'>
	!!pret_msg!! &nbsp;
	</div>
<script type='text/javascript' src='./javascript/sorttable.js'></script>";
if ($pmb_utiliser_calendrier) {
	$empr_tmpl .= "
	<script type='text/javascript'>
		function test_jour_ouverture(f_caller,id,id_value,lib,lib_value,loc_id) {
			var req = new XMLHttpRequest();
			req.open('GET', './ajax.php?module=ajax&categ=calendrier&action=test_ouverture&loc_id='+loc_id+'&id_value='+id_value+'&lib_value='+lib_value, true);
			req.onreadystatechange = function (aEvt) {
		 		if (req.readyState == 4) {
		  			if(req.status == 200) {
		    			my_array = JSON.parse(req.responseText);
		    			if (my_array[0] != undefined) {
							if (confirm(\"".$msg['prolongation_date_fermeture']."\")) {
								id_value = my_array[0];
								lib_value = my_array[1];
							}
		    			}
		    			document.forms[f_caller].elements[id].value = id_value;
						document.forms[f_caller].elements[lib].value = lib_value;
						document.forms[f_caller].submit();
					}
		  		}
		  	};
			req.send(null);			
		}	
	</script>
	";
}
$empr_tmpl .= "
<table class='sortable'>
	<thead>
	<tr>
	<form class='form-$current_module' name='prolong_bloc' action='circ.php'>
		<th colspan='6'>
			<h3>$msg[349] &nbsp;(!!nb_prets_encours!!)&nbsp;&nbsp;
			<input type='button' name='imprimerlistedocs' class='bouton' value='".$msg['imprimer']."' onClick=\"openPopUp('./pdf.php?pdfdoc=ticket_pret&id_empr=!!id!!', 'print_PDF');\" />
			&nbsp;<input type='button' name='imprimerlistedocs' class='bouton' value='".$msg['imprimer_liste_pret']."' onClick=\"openPopUp('./pdf.php?pdfdoc=liste_pret&id_empr=!!id!!', 'print_PDF');\" />
			&nbsp;!!mail_liste_pret!!
			$printer_ticket_link
			&nbsp;!!lettre_retard!!&nbsp;!!mail_retard!!&nbsp;
			!!bt_histo_relance!!
			!!voir_tout_pret!!
			</h3>
		</th>
		<th>".$msg['pret_bloc_prolong']."</th>
		<th class='date_retour'>!!prol_date!!</th>
		<th></th>$th_sur_location0
	</form>
	</tr>
	<tr>
	<form class='form-$current_module' name='sel_bloc'>
		<th>$msg[293]</th>
		<th size='50%'>$msg[652]</th>
		<th>$msg[294]<br />$msg[296]</th>$th_sur_location
		<th>$msg[298]<br />$msg[295]</th>
		<th>$msg[653]</th>
		<th>".$msg['pret_date_retour_initial']."</th>
		<th>".$msg['pret_compteur_prolongation']."</th>
		<th>$msg[654]</th>
		<th class='sorttable_nosort'>
			!!bouton_cocher_prolong!!
			<input type='hidden' name='id_inpret' value=\"!!id_inpret!!\">
		</th>
	</form>
	</tr>
	</thead>
	<tbody>
	!!pret_list!!
	</tbody>
</table>
<div class='row'><hr /></div><div>!!digital_loans_table!!</div>";

if ($pmb_short_loan_management==1) {
$empr_tmpl.="

<table class='sortable'>
	<thead>
	<tr>
	<tr ><th colspan='$th_colspan_shorloan'>".$msg['short_loans']."</th></tr>
	<form class='form-$current_module' name='sel_bloc'>
		<th>$msg[293]</th>
		<th size='50%'>$msg[652]</th>
		<th>$msg[294]<br />$msg[296]</th>$th_sur_location
		<th>$msg[298]<br />$msg[295]</th>
		<th>$msg[653]</th>
		<th>".$msg['pret_date_retour_initial']."</th>
		<th>".$msg['pret_compteur_prolongation']."</th>
		<th>$msg[654]</th>
		<th class='sorttable_nosort'></th>
	</form>
	</tr>
	</thead>
	<tbody>
	!!short_loan_list!!
	</tbody>
</table>
<div class='row'><hr /></div>";
}			
			
$empr_tmpl.="			
<div class='row'>
	<div class='left'>
		<h3>$msg[350]&nbsp;<input type='button' name='Ajouterresa' class='bouton' value='$msg[925]' onClick=\"document.location='./circ.php?categ=resa&id_empr=!!id!!&groupID=$groupID';\" /></h3>
	</div>
	<div class='right'><span id='msg_chg_loc' class='erreur'></span></div>
</div>	
<div class='row'></div>
!!resa_list!!
";

if ($pmb_resa_planning) {
	$empr_tmpl.= "
	<div class='row'><hr /></div>
	<div class='row'>
		<div class='left'>
			<h3>".$msg['resa_menu_planning']."&nbsp;<input type='button' name='Ajouter_resa_planning' class='bouton' value='".$msg[925]."' onClick=\"document.location='./circ.php?categ=resa_planning&resa_action=search_resa&id_empr=!!id!!&groupID=$groupID';\" /></h3>
		</div>
	</div>
	!!resa_planning_list!!
";
}

$empr_tmpl.="
<div class='row'>
	!!dsi!!
</div>
<div class='row'>
	!!caddies!!
</div>
<div class='row'>
	!!serialcirc_empr!!
</div>	
";
		
//*************************************************************************************************************************
$empr_tmpl_consultation = "
<div id=\"el!!id!!Parent\" class=\"notice-parent\">
	<div class='left'>
		<img src=\"".get_url_icon('plus.gif')."\" class=\"img_plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); return false;\">
   		!!image_suppr_caddie_empr!!&nbsp;!!image_caddie_empr!! &nbsp; <a href=!!lien_vers_empr!!>!!nom!! !!prenom!!</a>
   	</div>
   	<div class='right'>
   		!!empr_statut_libelle!!
   	</div>
</div>
<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-left:7px;display:none;\"!!depliee!!>
<div class='left'>
	<div id='bloc_adresse_empr' class='row'>
		<div class='colonne3'>
			<div class='row'>
				!!adr1!!
			</div>
			<div class='row'>
				!!adr2!!
			</div>
			<div class='row'>
				!!cp!!&nbsp;!!ville!!
			</div>
			<div class='row'>
				!!pays!!
			</div>
			<div class='row'>
				<strong>!!tel1!!</strong> / <strong>!!tel2!!</strong>
			</div>
			<div class='row'>
				$msg[58]$msg[1901] !!mail_all!!
			</div>
		</div>
		<div class='colonne3'>
			<div class='row'>
				<strong>$msg[74] : </strong>!!prof!!
			</div>
			<div class='row'>
				<strong>$msg[75] : </strong>!!date!!
			</div>
			<div class='row'>
				<strong>$msg[125] : </strong>!!sexe!!
			</div>
		</div>
		<div class='colonne_suite'>
		</div>
	</div>
	<div id='bloc_adhesion' class='row'>
		<div class='colonne3'>
			<div class='row'>
				<strong>$msg[1403]</strong>
			</div>
			<div class='row'>
				<strong>$msg[1401] : </strong>!!adhesion!!
			</div>
			<div class='row'>
				<strong>$msg[1402] : </strong>!!expiration!!
			</div>
			<div class='row'>
				<strong>".$msg['date_dern_emprunt']." : </strong>!!last_loan_date!!
			</div>
		</div>
		<div class='colonne3'>
			<div class='row'>
				<strong>$msg[60] : </strong>!!codestat!!
			</div>
			<div class='row'>
				<strong>$msg[59] : </strong>!!categ!!
			</div>
			<div class='row'>
				<strong>$msg[38] : </strong>!!cb!!
			</div>
			!!abonnement!!
		</div>
		<div class='colonne_suite'>
			<!-- !!localisation!! -->
			<div class='row'>
				!!groupes!!
			</div>
			<div class='row'>
				<strong>".$msg['empr_login']." : </strong>!!empr_login!!
			</div>
			<div class='row'>
				!!empr_pwd!!
			</div>
		</div>
	</div>
	<div id=bloc_suite class='row'>
		<div class='row'></div>
		!!perso!!
		<div class='row'></div>
		!!empr_msg!!";
if ($ldap_accessible)
	$empr_tmpl_consultation .= "
		<div class='row'>
			<strong>".$msg['empr_authldap'].": </strong>!!info_authldap!!
		</div>";
$empr_tmpl_consultation .= "
	</div>
</div><div class='right'>!!empr_picture!!</div>
<div class='row'></div>
</div>
<div class='row'></div>
";

// propriété du sélecteur de groupe
if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url ) {
		
	$rfid_script_empr="
		$rfid_js_header	
		<script type='text/javascript'>
			var flag_cb_rfid=0;
			flag_program_rfid_ask=0;

			setTimeout('init_rfid_read_cb(f_empr,0);',0);
			
			function f_empr(cb) {
				if(flag_program_rfid_ask==1) {
					program_rfid();
					flag_cb_rfid=0; 
					return;
				}
				if(cb.length==0) {
					flag_cb_rfid=1;
					return;
				} 
				if(!cb[0]) {
					flag_cb_rfid=0; 
					return;
				}
				if(document.getElementById('f_cb').value == cb[0]) flag_cb_rfid=1;
				else  flag_cb_rfid=0;
				if(document.getElementById('f_cb').value == '') {	
					flag_cb_rfid=0;				
					document.getElementById('f_cb').value=cb[0];
				}
			}
			function script_rfid_encode() {
				if(!flag_cb_rfid && flag_rfid_active) {
				    var confirmed = confirm(\"".addslashes($msg['rfid_programmation_confirmation'])."\");
				    if (confirmed) {
						return false;
				    } 
				}
			}

			function program_rfid_ask() {
				if (flag_semaphore_rfid_read==1) {
					flag_program_rfid_ask=1;
				} else {
					program_rfid();
				}
			}

			function program_rfid() {
				flag_semaphore_rfid=1;
				flag_program_rfid_ask=0;
				var cb = document.getElementById('f_cb').value;	
				init_rfid_erase(rfid_ack_erase);
			}
			
			function rfid_ack_erase(ack) {
				var cb = document.getElementById('f_cb').value;
				init_rfid_write_empr(cb,rfid_ack_write);
				
			}
			function rfid_ack_write(ack) {				
				alert (\"".addslashes($msg['rfid_etiquette_programmee_message'])."\");
				flag_semaphore_rfid=0;
			}
			
		</script>
";

	$rfid_program_button="<input  type=button class='bouton' value=' ". $msg['rfid_configure_etiquette_button']." ' onClick=\"program_rfid_ask();\">";	
}else {	
	$rfid_script_empr="";
	$rfid_program_button="";
}



// $empr_form : template pour le form lecteur
$empr_form = jscript_unload_question()."
$rfid_script_empr
<script type='text/javascript'>
	function test_form(form) {
		!!questionrfid!!
		if(form.form_nom.value.replace(/^\s+|\s+$/g,'').length == 0) {
			alert(\"$msg[65]\");
			form.form_nom.focus();
			return false;
		}";
if ($empr_birthdate_optional == 0) {
	$empr_form .="
			if(form.form_year.value.replace(/^\s+|\s+$/g,'').length == 0) {
				alert(\"$msg[762]\");
				form.form_year.focus();
				return false;
			}";
}
$empr_form .="
		!!empr_create_script_call!!
		unload_off();
		return check_form();
	}
</script>
!!empr_create_script_loader!!
<script type='text/javascript' src='javascript/tablist.js'></script>
<script type='text/javascript' src='javascript/ajax.js'></script>
<script type='text/javascript'>
	widths=new Array(".$msg['empr_field_widths'].");	
	var msg_move_to_absolute_pos='".addslashes($msg['move_to_absolute_pos'])."';
	var msg_move_to_relative_pos='".addslashes($msg['move_to_relative_pos'])."';
	var msg_move_saved_ok='".addslashes($msg['move_saved_ok'])."';
	var msg_move_saved_error='".addslashes($msg['move_saved_error'])."';
	var msg_move_up_tab='".addslashes($msg['move_up_tab'])."';
	var msg_move_down_tab='".addslashes($msg['move_down_tab'])."';
	var msg_move_position_tab='".addslashes($msg['move_position_tab'])."';
	var msg_move_position_absolute_tab='".addslashes($msg['move_position_absolute_tab'])."';
	var msg_move_position_relative_tab='".addslashes($msg['move_position_relative_tab'])."';
	var msg_move_invisible_tab='".addslashes($msg['move_invisible_tab'])."';
	var msg_move_visible_tab='".addslashes($msg['move_visible_tab'])."';
	var msg_move_inside_tab='".addslashes($msg['move_inside_tab'])."';
	var msg_move_save='".addslashes($msg['move_save'])."';
	var msg_move_first_plan='".addslashes($msg['move_first_plan'])."';
	var msg_move_last_plan='".addslashes($msg['move_last_plan'])."';
	var msg_move_first='".addslashes($msg['move_first'])."';
	var msg_move_last='".addslashes($msg['move_last'])."';
	var msg_move_infront='".addslashes($msg['move_infront'])."';
	var msg_move_behind='".addslashes($msg['move_behind'])."';
	var msg_move_up='".addslashes($msg['move_up'])."';
	var msg_move_down='".addslashes($msg['move_down'])."';
	var msg_move_invisible='".addslashes($msg['move_invisible'])."';
	var msg_move_visible='".addslashes($msg['move_visible'])."';
	var msg_move_saved_onglet_state='".addslashes($msg['move_saved_onglet_state'])."';
	var msg_move_open_tab='".addslashes($msg['move_open_tab'])."';
	var msg_move_close_tab='".addslashes($msg['move_close_tab'])."';
	
</script>
<script type='text/javascript' src='javascript/move.js'></script>
<h1>!!entete!!</h1>
<form class='form-$current_module' id='empr_form' name='empr_form' method='post' action='!!form_action!!&id=!!id!!&groupID=$groupID'>

	<input type='hidden' name='form_cb' value='!!cb!!' />
	<input type='hidden' name='groupID' value='$groupID' />
	<input type='hidden' name='debit' value='0' />
	
	<div style='float:left'><h3>!!nom!!&nbsp;!!prenom!!</h3></div>
	<div style='float:right'><label for='form_statut' class='etiquette'>".$msg['empr_statut_menu']."</label>&nbsp;<select id='form_statut' name='form_statut'>!!statut!!</select>&nbsp;";

if ($PMBuserid==1 && $pmb_form_editables==1) {
	$empr_form.="<input type='button' class='bouton_small' value='".$msg["empr_edit_format"]."' onClick=\"expandAll(); move_parse_dom(relative)\" id=\"bt_inedit\"/><input type='button' class='bouton_small' value='Relatif' onClick=\"expandAll(); move_parse_dom((!relative))\" style=\"display:none\" id=\"bt_swap_relative\"/>";
}
if ($pmb_form_editables==1) {
	$empr_form.="<input type='button' class='bouton_small' value=\"".$msg["empr_origin_format"]."\" onClick=\"get_default_pos(); expandAll();  ajax_parse_dom(); if (inedit) move_parse_dom(relative); else initIt();\"/>";
}

	$empr_form.= "
	</div>
	<div class='row'></div>
	
	<div class='form-contenu'>
		
		<div class='row'>
			<!-- empr_grille_categ -->
		</div>
		<div class='row'>
			<!-- empr_grille_location -->
		</div>
				
		<div class='row'></div>
		<div class='form-empr-fgrp ui-clearfix' id='g0' etirable='yes' >
			
				<!--   Nom   -->
				<div class='colonne3' id='g0_r0_f0' movable='yes' title='".$msg[67]."' >
					<div class='row'>
						<label class='etiquette' for='form_nom'>".$msg[67]."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-20em' style='width:99%' id='form_nom' name='form_nom' value='!!nom!!' />
					</div>
				</div>
				<!--   Prénom   -->
				<div class='colonne3' id='g0_r0_f1' movable='yes' title='".$msg[68]."' >
					<div class='row'>
						<label for='form_prenom' class='etiquette'>".$msg[68]."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-20em' id='form_prenom' name='form_prenom' value='!!prenom!!' />
					</div>
				</div>
				<!--   CB   -->
				<div class='colonne'  id='g0_r0_f2' movable='yes' title='".$msg[38]."' >
					<div class='row'>
						<label for='form_cb' class='etiquette'>".$msg[38]."</label>
					</div>
					<div class='row'>
						<input class='saisie-10emr' id='f_cb' name='f_cb' readonly value=\"!!cb!!\" />
						<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./circ/setcb.php?f_cb='+this.form.f_cb.value, 'getcb')\" />
						
					</div>
				</div>
				<div class='colonne'  id='g0_r0_f3' movable='yes' title='".$msg[38]."' >
						!!camera!!
				</div>
			
				<!--   Adresse 1   -->
				<div class='colonne2'  id='g0_r1_f0' movable='yes' title='".$msg[69]."' >
					<div class='row'>
						<label for='form_adr1' class='etiquette'>".$msg[69]."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-40em' id='form_adr1' name='form_adr1' maxlength='255' value='!!adr1!!' />
					</div>
				</div>
				<!--   Code postal   -->
				<div class='colonne10' id='g0_r1_f1' movable='yes' title='".$msg[71]."' >
					<div class='row'>
						<label for='form_cp' class='etiquette'>".$msg[71]."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-5em' id='form_cp' name='form_cp' maxlength='10' value='!!cp!!' onchange=\"openPopUp('./select.php?what=codepostal&caller=empr_form&param1=form_ville&param2=form_cp&deb_rech='+".pmb_escape()."(this.form.form_cp.value), 'selector')\" />
					</div>
				</div>
				<!--   Ville   -->
				<div class='colonne_suite' id='g0_r1_f2' movable='yes' title='".$msg[72]."' >
					<div class='row'>
						<label for='form_ville' class='etiquette'>".$msg[72]."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-20em' id='form_ville' name='form_ville' value=\"!!ville!!\" />
						<input type='button'  class='bouton' value='".$msg['parcourir']."' onclick=\"var scp = this.form.form_cp.value; if(!this.form.form_cp.value) { scp=this.form.form_ville.value; } openPopUp('./select.php?what=codepostal&caller=empr_form&param1=form_ville&param2=form_cp&deb_rech='+".pmb_escape()."(scp), 'selector')\" />
					</div>
				</div>
				
				<!--   Adresse 2   -->
				<div class='colonne2' id='g0_r2_f0' movable='yes' title='".$msg[70]."' >
					<div class='row'>
						<label for='form_adr2' class='etiquette'>".$msg[70]."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-40em' id='form_adr2' name='form_adr2' maxlength='255' value='!!adr2!!' />
					</div>
				</div>
				<!--   Pays   -->
				<div class='colonne_suite' id='g0_r2_f1' movable='yes' title='".$msg['empr_pays']."' >
					<div class='row'>
						<label for='form_pays' class='etiquette'>".$msg['empr_pays']."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-40em' id='form_pays' name='form_pays' maxlength='255' value='!!pays!!' />
					</div>
				</div>				
			
				<!--   Téléphone 1   -->
				<div class='colonne4' id='g0_r3_f0' movable='yes' title='".$msg[73]."' >
					<div class='row'>
						<label for='form_tel1' class='etiquette'>".$msg[73]."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-15em' id='form_tel1' name='form_tel1' value='!!tel1!!' />
						".($empr_sms_activation ? "<label for='form_sms' class='etiquette'>".$msg['send_sms']."</label>
						<input type='checkbox' id='form_sms' name='form_sms' value='1' !!sms!! />" : "")."				
					</div>
				</div>
				<!--   Téléphone 2   -->
				<div class='colonne4' id='g0_r3_f1' movable='yes' title='".$msg['73tel2']."' >
					<div class='row'>
						<label for='form_tel2' class='etiquette'>".$msg['73tel2']."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-15em' id='form_tel2' name='form_tel2' value='!!tel2!!' />
					</div>
				</div>
				<!--   E-mail   -->
				<div class='colonne_suite' id='g0_r3_f2' movable='yes' title='".$msg[58]."' >
					<div class='row'>
						<label for='form_mail' class='etiquette'>".$msg[58]."</label>
					</div>
					<div class='row'>
						<input type='text'  class='saisie-40em' size=50 id='form_mail' name='form_mail' value='!!mail!!' />
					</div>
				</div>				
				<div class='row'></div>			
		</div>
			
		<div class='row'></div>	
		<div class='form-empr-fgrp ui-clearfix' id='g1'  etirable='yes' >
		
				<!--   Profession   -->
				<div class='colonne4' id='g1_r0_f0' movable='yes' title='".$msg[74]."' >
					<div class='row'>
						<label for='form_prof' class='etiquette'>".$msg[74]."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-20emr' id='form_prof' name='form_prof' value='!!prof!!' autfield='form_prof' completion='profession' autocomplete='off'>
					</div>
				</div>
				<!--   Sexe   -->
				<div class='colonne4' id='g1_r0_f1' movable='yes' title='".$msg[125]."' >
					<div class='row'>
						<label class='etiquette' for='form_sexe'>".$msg[125]."</label>
					</div>
					<div class='row'>
						<select class='saisie-15em' id='form_sexe' name='form_sexe'>
							<option value='1' sexe_select_1>".$msg[126]."</option>
							<option value='2' sexe_select_2>".$msg[127]."</option>
							<option value='0' sexe_select_0>".$msg[128]."</option>
						</select>
					</div>
				</div>
				<!--   Date de naissance   -->
				<div class='colonne_suite' id='g1_r0_f2' movable='yes' title='".$msg[75]."' >
					<div class='row'>
						<label for='form_year' class='etiquette'>".$msg[75]."</label>
					</div>
					<div class='row'>
						<input type='text'  class='saisie-10em' id='form_year' name='form_year' maxlength='4' value='!!year!!' />
					</div>
				</div>				
				<div class='row'></div>
		</div>
								
		<div class='row'></div>								
		<div class='form-empr-fgrp ui-clearfix' id='g2'  etirable='yes' >
			
				<!--   Categorie   -->
				<div class='colonne4' id='g2_r0_f0' movable='yes' title='".$msg[59]."' >
					<div class='row'>
						<label for='form_categ' class='etiquette'>".$msg[59]."</label>
					</div>
					<div class='row'>
						<select id='form_categ' name='form_categ' class='saisie-20em'>!!categ!!</select>
					</div>
				</div>
				<!--   Code statistique   -->
				<div class='colonne4' id='g2_r0_f1' movable='yes' title='".$msg[60]."' >
					<div class='row'>
						<label for='form_codestat' class='etiquette'>".$msg[60]."</label>
					</div>
					<div class='row'>
						<select name='form_codestat' id='form_codestat' class='saisie-20em'>!!cstat!!</select>
					</div>
				</div>
				<!--   Ajout à un groupe existant   -->
				<div class='colonne_suite' id='g2_r0_f2' movable='yes'  title='".htmlentities($msg['empr_form_ajoutgroupe'],ENT_QUOTES,$charset)."' >
					<div class='row'>
						<label for='form_ajoutgroupe' class='etiquette'>".$msg['empr_form_ajoutgroupe']."</label>
					</div>
					<div class='row'>
						!!groupe_ajout!!
					</div>
				</div>
				
				<div class='row'></div>
	
				<!--   Localisation du lecteur   -->
				<!-- !!localisation!! -->
		
				<div class='row'></div>
		</div>
		
		<div class='row'></div>		
		<div class='form-empr-fgrp ui-clearfix' id='g3'  etirable='yes' >
		
				<!--   Adhésion   -->
				<div class='colonne4' id='g3_r0_f0' movable='yes' title='".$msg[1403]." : ".$msg[1401]."' >
					<div class='row'>
						<label for='form_adhe_ini' class='etiquette'>".$msg[1403]." : ".$msg[1401]."</label>
					</div>
					<div class='row'>
						<strong>!!adhesion!!</strong>
					</div>
				</div>
				<div class='colonne4' id='g3_r0_f1' movable='yes' title='".$msg[1403]." : ".$msg[1402]."' >
					<div class='row'>
						<label for='form_adhe_end' class='etiquette'>".$msg[1403]." : ".$msg[1402]."</label>
					</div>
					<div class='row'>
						<strong>!!expiration!!</strong>
					</div>
				</div>
				<!--   Relance adhesion -->
				<div class='colonne_suite' id='g3_r0_f2' movable='yes'  title='".htmlentities($msg['empr_exp_adh'],ENT_QUOTES,$charset)."'>
					&nbsp;!!adhesion_proche_depassee!!
				</div>
				
	
				<div class='colonne' id='g3_r1_f1' movable='yes'  title='".htmlentities($msg['finance_type_abt'],ENT_QUOTES,$charset)."'>
				<!--Type d'abonnement -->
				!!typ_abonnement!!
				</div>
				
				<div class='row'></div>
		
		</div>
	
		<div class='row'></div>
		<div class='form-empr-fgrp ui-clearfix' id='g4' etirable='yes' >
		
				<!--   Langue   -->
				<div class='colonne4' id='g4_r0_f0' movable='yes'  title='".htmlentities($msg['empr_langue_opac'],ENT_QUOTES,$charset)."'>
					<div class='row'>
						<label for='' class='etiquette'>".$msg['empr_langue_opac']."</label>
					</div>
					<div class='row'>
						!!combo_empr_lang!!
					</div>
				</div>
				<div class='colonne4' id='g4_r0_f1' movable='yes'  title='".htmlentities($msg['empr_login'],ENT_QUOTES,$charset)."'>
					<div class='row'>
						<label for='form_empr_login' class='etiquette'>".$msg['empr_login']."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-15em' id='form_empr_login' name='form_empr_login' value='!!empr_login!!' />
					</div>
				</div>";
		
		if ($ldap_accessible) {
			$empr_form .= "<!-- AuthLDAP - MaxMan -->
				<div class='colonne4' id='g4_r0_f2' movable='yes'  title='AuthLDAP' >
					<div class='row'>
						<label for='form_ldap' class='etiquette'>AuthLDAP</label>
					</div>
					<div class='row'>
						<input type='checkbox' id='form_ldap' name='form_ldap' !!ldap!! />
					</div>
				</div>";	
		}	
		$empr_form .= "
				<div class='colonne_suite' id='g4_r0_f3' movable='yes'  title='".htmlentities($msg['empr_password'],ENT_QUOTES,$charset)."' >
					<div class='row'>
						<label for='form_empr_password' class='etiquette'>".$msg['empr_password']."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-15em' id='form_empr_password' name='form_empr_password' value='' maxlength='255' />
					</div>
				</div>
				
			";
		if($pmb_opac_view_activate ){
			$empr_form .= "
				<!--   vue Opac du lecteur   -->
				<!-- !!opac_view!! -->
				
				";
		}
		$empr_form .= "	
				<div class='row'></div>
		</div>
	
		<div class='row'></div>
		<div class='form-empr-fgrp ui-clearfix' id='g5' etirable='yes'  >
		
				<!--   Message   -->
				<div class='colonne' id='g5_r0_f0' movable='yes' title='".$msg[523]."' >
					<div class='row'>
						<label for='form_codestat' class='etiquette'>".$msg[523]."</label>
					</div>
					<div class='row'>
						<textarea id='f_message_empr' class='saisie-80em' name='form_empr_msg' cols='62' rows='2' wrap='virtual'>!!empr_msg!!</textarea>
					</div>
				</div>
				
				<div class='row'></div>
			
		</div>
	
		<div class='row'></div>
		<div class='form-empr-fgrp ui-clearfix' id='g6' etirable='yes'  >	
				!!champs_perso!!
				
				<div class='row'></div>
			
		</div>

		<div class='row'></div>
		<div class='form-empr-fgrp ui-clearfix' id='g7'  etirable='yes' >
				!!empr_notice_override!!				
		</div>

	</div>
	
	<div class='row'>
		<input type='button' class='bouton' value='$msg[76]' onclick='unload_off();history.go(-1);' />
		<input type='submit' class='bouton' value='$msg[77]' onClick=\"return test_form(this.form)\" />
		$rfid_program_button
		<input type='hidden' name='group_id' value='$group_id' />
	</div>
	
</form>

<script type='text/javascript'>
	".($pmb_form_editables?"get_pos(); ":"")."
	document.forms['empr_form'].elements['form_nom'].focus();
	</script>
";

// $empr_edit_tmpl : template pour le form de saisie nom dans la page edition des emprunteurs
$empr_edit_tmpl = "
<form class='form-$current_module' name='saisie_cb_ex' method='post' action='!!form_action!!'>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='form_cb'>!!message!!</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-20em' id='form_cb' name='form_cb' value='' />
		</div>
	</div>
	<div class='row'>
		<input type='submit' class='bouton' value='Ok' />
		<input type='hidden' name='group_id' value='$group_id'>
	</div>
	<script type='text/javascript'>
		document.forms['saisie_cb_ex'].elements['form_cb'].focus();
	</script>
</form>";

$empr_tmpl_fiche_affichage = "
<div class='row'>
	<h1>!!prenom!! !!nom!! ".$msg['empr_nb_pret'].": !!info_nb_pret!! ".$msg['empr_nb_resa'].": !!info_nb_resa!! !!info_resa_planning!!&nbsp;<input type=button class=bouton  onclick=\"document.location='./circ.php?categ=pret&form_cb=!!cb!!';\" value='".htmlentities($msg['retour_goto_pret'],ENT_QUOTES, $charset)."'></h1>
	</div>
<div class='colonne3'>
	<strong>$msg[1401] : </strong>!!adhesion!!
	</div>
<div class='colonne_suite'>
	<strong>$msg[1402] : </strong>!!expiration!!
	</div>
<div class='row'>
	<div class='erreur'>!!empr_date_depassee!!</div>
	</div>
<div class='row'>
	<div class='erreur'>!!empr_categ_age_change!!</div>
	</div>
<div class='row'>
	<div class='erreur'>!!empr_msg!!</div>
	</div>
<hr />
<div class='row'>
	<h3>$msg[349] &nbsp;(!!nb_prets_encours!!)&nbsp;&nbsp;</h3>
	</div>
<script type='text/javascript' src='./javascript/sorttable.js'></script>
<table>
	<tr>
	<th>$msg[293]</th>
	<th>$msg[652]</th>
	<th>$msg[294]</th>
	<th>$msg[expl_location]</th>
	<th>$msg[653]</th>
	<th>".$msg['pret_date_retour_initial']."</th>
	<th>".$msg['pret_compteur_prolongation']."</th>
	<th>$msg[654]</th>
	</tr>
	!!pret_list!!
	</table>

<div class='row'>&nbsp;</div>
<div class='row'>
	<h3>$msg[350]&nbsp;</h3>
	</div>
	!!resa_list!!
	!!resa_planning_header!!
	!!resa_planning_list!!
";

$empr_autre_compte_tmpl="
	<script type='text/javascript'>
		 function sel_type_transactype(transactype_id,obj,libelle, unit_price ){
		 	document.getElementById('transactype_name').innerHTML=libelle;
		 	if(unit_price>0){
		 		document.getElementById('transactype_unit_price').innerHTML='".$msg["transactype_finance_unit_price"]."' + unit_price;
		 		document.getElementById('transactype_unit_price_val').value=unit_price;
		 		document.getElementById('transactype_unit_price').style.display='block';
		 		document.getElementById('transactype_quantity_part').style.display='block';
		 		document.getElementById('transactype_quantity').value=1;
		 		document.getElementById('transactype_total').value=unit_price;
		 		document.getElementById('transactype_total_part').style.display='block';
		 		document.getElementById('transactype_total').readOnly = true;
		 		 document.getElementById('transactype_quantity').select();
		 		document.getElementById('transactype_quantity').focus();
		 	}else {
		 		document.getElementById('transactype_unit_price').innerHTML=0;
		 		document.getElementById('transactype_unit_price').style.display='none';
		 		document.getElementById('transactype_quantity_part').style.display='none';
		 		document.getElementById('transactype_total').value='';
		 		document.getElementById('transactype_total_part').style.display='block';
		 		document.getElementById('transactype_total').readOnly = false;
		 		document.getElementById('transactype_total').focus();
		 	}
		 	
		 	document.getElementById('transactype_id').value=transactype_id;
		 }
		 
		 function calcul_total(){
		 		var nb=document.getElementById('transactype_quantity').value *100;
		 		var unit_price=document.getElementById('transactype_unit_price_val').value *100;
		 		
		 		document.getElementById('transactype_total').value=(unit_price * nb)/10000;		 
		 }
		 
		function ajoute_transaction(){
		 		var total = document.getElementById('transactype_total').value;			 		
		 		var transactype_id = document.getElementById('transactype_id').value;
		 		var quantity=document.getElementById('transactype_quantity').value;
		 		
		 		list_transactions.document.form_transactions.act.value='transac_add'; 
		 		list_transactions.document.form_transactions.action='encaissement.php?transactype_total='+ total + '&transactype_id=' + transactype_id + '&quantity=' + quantity;
		 		
		 		list_transactions.document.form_transactions.submit();		 		
		 }
	</script>
	<div class='row'>
		<h1>!!prenom!! !!nom!! ".$msg['empr_nb_pret'].": !!info_nb_pret!! ".$msg['empr_nb_resa'].": !!info_nb_resa!!</h1>
	</div>
	<div class='row'><a href='circ.php?categ=pret&id_empr=$id'>".$msg["finance_form_empr_go_back"]."</a></div>
	<div class='row'>
		<div class='colonne2'><h1>!!type_compte!!</h1></div><div class='colonne2' style='text-align:right'><h1>".$msg["finance_solde"]." !!solde!!<br />".$msg["finance_not_validated"]." : !!non_valide!!</h1></div>
	</div>
	<form name='compte_form' method='post' action='./circ.php?categ=pret&sub=compte&typ_compte=!!typ_compte!!&id=$id'>
		<div class='row' id='selector_transaction_list'>
			<div class='colonne3'><input type='radio' name='show_transactions' value='1' id='show_transactions_1' !!checked1!! onClick=\"list_transactions.document.location='./circ/list_transactions.php?id_compte=!!id_compte!!&show_transactions=1';\"/><label for='show_transactions_1'>".$msg["finance_form_empr_ten_last"]."</label></div>
			<div class='colonne3'><input type='radio' name='show_transactions' value='2' id='show_transactions_2' !!checked2!! onClick=\"list_transactions.document.location='./circ/list_transactions.php?id_compte=!!id_compte!!&show_transactions=2';\"/><label for='show_transactions_2'>".$msg["finance_form_empr_not_validated"]."</label></div>
			<div class='colonne3'><input type='radio' name='show_transactions' value='3' id='show_transactions_3' !!checked3!! onClick=\"list_transactions.document.location='./circ/list_transactions.php?id_compte=!!id_compte!!&show_transactions=3&date_debut='+this.form.date_debut.value;\"/><label for='show_transactions_3'>".$msg["finance_form_empr_tr_from"]." </label><input type='text' size='10' name='date_debut' value='!!date_debut!!'></div>
		</div>
		<div class='row'>&nbsp;</div>
		<iframe name='list_transactions' width='100%' height='250' src='./circ/list_transactions.php?id_compte=!!id_compte!!&show_transactions=!!show_transactions!!&date_debut=!!date_debut!!'></iframe>
		<div class='row'>&nbsp;</div>
		<div class='row' id='transactype_list' style='text-align:center'>
			!!transactype_list!!
		</div>
		<div class='row' id='transactype_edit'>
			<div class='colonne4' id='transactype_name'></div>
			<input type='hidden' name='transactype_unit_price_val' id='transactype_unit_price_val' value='0'>
			<input type='hidden' name='transactype_id' id='transactype_id' value='0'>
			
			
			<div class='colonne4' id='transactype_unit_price' style='display:none' ></div>
			<div class='colonne4' id='transactype_quantity_part' style='display:none' >
				".$msg["transactype_finance_quantity"]."
				<input type='text' size='3' name='transactype_quantity' id='transactype_quantity' value='1' onChange=\"calcul_total()\"  tabindex='1'></div>
			<div class='colonne4' id='transactype_total_part' style='display:none' >
				".$msg["transactype_finance_total"]."
				<input type='text' size='10' name='transactype_total' id='transactype_total' value='0'  tabindex='2'>
				<input type='button' class='bouton' value='".$msg["transactype_finance_add"]."' onClick=\"ajoute_transaction();\" tabindex='3'>
			</div>
			
		</div>
		<table>
		<tr><td style='text-align:left'>
			<input type='button' class='bouton' value='".$msg["finance_but_valenc"]."' onClick=\"list_transactions.document.form_transactions.act.value='valenc'; list_transactions.document.form_transactions.submit()\"><br />
			<input type='button' class='bouton' value='".$msg["finance_but_enc"]."' onClick=\"list_transactions.document.form_transactions.act.value='encnoval'; list_transactions.document.form_transactions.submit()\">
		</td>
		<td style='text-align:center'>
			<input type='button' class='bouton' value='".$msg["finance_but_val"]."' onClick=\"list_transactions.document.form_transactions.act.value='val'; list_transactions.document.form_transactions.submit()\"><br />
			<input type='button' class='bouton' value='".$msg["finance_but_supr"]."' onClick=\"if (confirm('".addslashes($msg["finance_confirm_supr"])."')) { list_transactions.document.form_transactions.act.value='supr'; list_transactions.document.form_transactions.submit() }\">
		</td>
		<td style='text-align:right'>
		<input type='button' class='bouton' value='".$msg["finance_but_cred"]."' onClick=\"list_transactions.document.form_transactions.act.value='special'; list_transactions.document.form_transactions.submit()\">
		</td></tr>
		</table>
		</div>
	</form>
";

$empr_comptes_tmpl="
	<div class='row'>
		<h1>!!prenom!! !!nom!! ".$msg['empr_nb_pret'].": !!info_nb_pret!! ".$msg['empr_nb_resa'].": !!info_nb_resa!!</h1>
	</div>
	<div class='row'><a href='circ.php?categ=pret&id_empr=$id'>".$msg["finance_form_empr_go_back"]."</a></div>
	<div class='row'>
		<div class='colonne2'><h1>!!type_compte!!</h1></div><div class='colonne2' style='text-align:right'><h1>".$msg["finance_solde"]." !!solde!!<br />".$msg["finance_not_validated"]." : !!non_valide!!</h1></div>
	</div>
	<form name='compte_form' method='post' action='./circ.php?categ=pret&sub=compte&typ_compte=!!typ_compte!!&id=$id'>
		<div class='row' id='selector_transaction_list'>
			<div class='colonne3'><input type='radio' name='show_transactions' value='1' id='show_transactions_1' !!checked1!! onClick=\"list_transactions.document.location='./circ/list_transactions.php?id_compte=!!id_compte!!&show_transactions=1';\"/><label for='show_transactions_1'>".$msg["finance_form_empr_ten_last"]."</label></div>
			<div class='colonne3'><input type='radio' name='show_transactions' value='2' id='show_transactions_2' !!checked2!! onClick=\"list_transactions.document.location='./circ/list_transactions.php?id_compte=!!id_compte!!&show_transactions=2';\"/><label for='show_transactions_2'>".$msg["finance_form_empr_not_validated"]."</label></div>
			<div class='colonne3'><input type='radio' name='show_transactions' value='3' id='show_transactions_3' !!checked3!! onClick=\"list_transactions.document.location='./circ/list_transactions.php?id_compte=!!id_compte!!&show_transactions=3&date_debut='+this.form.date_debut.value;\"/><label for='show_transactions_3'>".$msg["finance_form_empr_tr_from"]." </label><input type='text' size='10' name='date_debut' value='!!date_debut!!'></div>
		</div>
		<div class='row'>&nbsp;</div>
		<iframe name='list_transactions' width='100%' height='250' src='./circ/list_transactions.php?id_compte=!!id_compte!!&show_transactions=!!show_transactions!!&date_debut=!!date_debut!!'></iframe>
		<div class='row'>&nbsp;</div>
		<div class='row' id='buttons_transaction_list'>
		<table>
		<tr><td style='text-align:left'>
			<input type='button' class='bouton' value='".$msg["finance_but_valenc"]."' onClick=\"list_transactions.document.form_transactions.act.value='valenc'; list_transactions.document.form_transactions.submit()\"><br />
			<input type='button' class='bouton' value='".$msg["finance_but_enc"]."' onClick=\"list_transactions.document.form_transactions.act.value='encnoval'; list_transactions.document.form_transactions.submit()\">
		</td>
		<td style='text-align:center'>
			<input type='button' class='bouton' value='".$msg["finance_but_val"]."' onClick=\"list_transactions.document.form_transactions.act.value='val'; list_transactions.document.form_transactions.submit()\"><br />
			<input type='button' class='bouton' value='".$msg["finance_but_supr"]."' onClick=\"if (confirm('".addslashes($msg["finance_confirm_supr"])."')) { list_transactions.document.form_transactions.act.value='supr'; list_transactions.document.form_transactions.submit() }\">
		</td>
		<td style='text-align:right'>
		<input type='button' class='bouton' value='".$msg["finance_but_cred"]."' onClick=\"list_transactions.document.form_transactions.act.value='special'; list_transactions.document.form_transactions.submit()\">
		</td></tr>
		</table>
		</div>
	</form>
";

$empr_retard_tpl ="
	<script type='text/javascript' src='./javascript/tablist.js'></script>
	<script type='text/javascript' src='./javascript/sorttable.js'></script>
	<div class='row'>
		<h1>!!prenom!! !!nom!! ".$msg['empr_nivo_relance'].": !!nivo_relance!! </h1>
	</div>
	<div class='row'><a href='circ.php?categ=pret&id_empr=!!id!!'>".$msg["finance_form_empr_go_back"]."</a></div>
	<h3>".$msg["empr_histo_late"]."</h3>
	
		!!liste_retard!!
	
			
	<div class='row'>&nbsp;</div>
	<script type='text/javascript'>
		 initIt();
	</script>
";

$empr_pnb_loans_tmpl = "
<table class='sortable'>
	<thead>
	<tr>
	<form class='form-$current_module' name='prolong_bloc' action='circ.php'>
		<th colspan='6'>
			<h3>$msg[349] &nbsp;(!!nb_prets_encours!!)&nbsp;&nbsp;
			<input type='button' name='imprimerlistedocs' class='bouton' value='".$msg['imprimer']."' onClick=\"openPopUp('./pdf.php?pdfdoc=ticket_pret&id_empr=!!id!!', 'print_PDF');\" />
			&nbsp;<input type='button' name='imprimerlistedocs' class='bouton' value='".$msg['imprimer_liste_pret']."' onClick=\"openPopUp('./pdf.php?pdfdoc=liste_pret&id_empr=!!id!!', 'print_PDF');\" />
			</h3>
		</th>
	</form>
	</tr>
	<tr>
	<form class='form-$current_module' name='sel_bloc'>
		<th>$msg[293]</th>
		<th size='50%'>$msg[652]</th>
		<th>$msg[653]</th>
		<th>".$msg['pret_date_retour_initial']."</th>
	</form>
	</tr>
	</thead>
	<tbody>
	!!pret_list!!
	</tbody>
</table>
<div class='row'><hr /></div>";