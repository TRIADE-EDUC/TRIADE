<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions.tpl.php,v 1.50.2.1 2025/01/20 13:13:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $sug_search_form, $msg, $current_module, $acquisition_sugg_localises, $charset, $acquisition_sugg_categ, $sel_date_form, $sug_list_form, $sug_modif_form, $back_url, $orig_form_mod;
global $bt_chk, $bt_supChk, $bt_imp, $bt_exporter, $bt_export_tableau, $lk_url_sug, $script, $nb_per_page, $sel_orig_form, $bt_fusVal;

//	------------------------------------------------------------------------------
//	$search_form : template de recherche pour les suggestions
//	------------------------------------------------------------------------------

$sug_search_form = "
<script type='text/javascript' src='javascript/ajax.js'></script>
<script type='text/javascript' src='javascript/suggestion.js'></script>
<script type='text/javascript' >
	var msg_parcourir='".addslashes($msg['parcourir'])."'; 
	var msg_raz='".addslashes($msg['raz'])."'; 
</script>
<form class='form-".$current_module."' id='search' name='search' method='post' action=\"!!action!!\">
	<h3>!!form_title!!</h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>
		
		<div class='row'>
			<div class='colonne3'>
				<input type='text' class='saisie-30em' id='user_input' name='user_input' value='!!user_input!!' />
			</div>
		</div>
		<div class='row'>	
			";
if ($acquisition_sugg_localises == '1') {
	$sug_search_form.="
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_location'], ENT_QUOTES, $charset)."</label>
				<div class='row'>
					<!-- sel_location -->
				</div>	
			</div>";
}
if ($acquisition_sugg_categ == '1') {
	$sug_search_form.="
  			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_categ'], ENT_QUOTES, $charset)."</label>
				<div class='row'>	
					<!-- sel_categ -->
				</div>
			</div>";
}			
$sug_search_form.= "
			<script type='text/javascript'>
				function filtrer_user(){
					document.forms['search'].submit();
				}
			</script>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_sug_etat'], ENT_QUOTES, $charset)."</label>
				<div class='row'>
					<!-- sel_state -->
				</div>
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_sugg_filtre_src'], ENT_QUOTES, $charset)."</label>
				<div class='row'>
					!!sug_filtre_src!!
				</div>
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_sugg_filtre_user'], ENT_QUOTES, $charset)."</label>
				<div class='row'>
					<input type='hidden' id='user_id0' name='user_id[0]' value='!!user_id!!'/>
					<input type='hidden' id='user_statut0' name='user_statut[0]' value='!!user_statut!!' />
					<input type='text' id='user_txt0' name='user_txt[0]' class='saisie-20emr' value='!!user_txt!!'/>
					<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=origine&caller=search&param1=user_id0&param2=user_txt0&param3=user_statut0&deb_rech='+".pmb_escape()."(this.form.user_txt0.value)+'&callback=filtrer_user', 'selector')\" />
					<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.user_id0.value='';this.form.user_statut0.value='';this.form.user_txt0.value=''\"/>
					<input type='button' onclick='add_orig();' value='+' class='bouton_small' />
					<input type='hidden' id='max_orig' value='!!max_orig!!' />
					<div id='add_orig' ><!-- sel_orig --></div>
				</div>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				!!sel_date!!
			</div>
		</div>					
		
		<div class='row'></div>
	</div>		
	
	<div class='row'>
		<div class='left'>
			<input type='submit' class='bouton' value='".$msg['142']."' />
			<!-- bouton_add -->
		</div>
		<div class='right'>
			<!-- lien_last -->
		</div>
	</div>

	<div class='row'></div>
</form>
<br />
";

$sel_date_form[0] = "<label class='etiquette'>!!msg!!</label>";
$sel_date_form[1] = "
<input type='hidden' id='date_inf' name='date_inf' value='!!date_inf!!' />
<input type='button' name='date_inf_lib' class='bouton_small' value='!!date_inf_lib!!' onclick=\"openPopUp('./select.php?what=calendrier&caller='+this.form.name+'&date_caller=&param1=date_inf&param2=date_inf_lib&auto_submit=NO&date_anterieure=YES', 'calendar');\">
<input type='button' class='bouton_small' value='".$msg['raz']."' onclick=\"this.form.elements['date_inf_lib'].value='".$msg['parperso_nodate']."'; this.form.elements['date_inf'].value='';\" >
";
$sel_date_form[2] = "
<input type='hidden' id='date_sup' name='date_sup' value='!!date_sup!!' />
<input type='button' name='date_sup_lib' class='bouton_small' value='!!date_sup_lib!!' onclick=\"openPopUp('./select.php?what=calendrier&caller='+this.form.name+'&date_caller=&param1=date_sup&param2=date_sup_lib&auto_submit=NO&date_anterieure=YES', 'calendar');\">
<input type='button' class='bouton_small' value='".$msg['raz']."' onclick=\"this.form.elements['date_sup_lib'].value='".$msg['parperso_nodate']."'; this.form.elements['date_sup'].value='';\" >
";

//	------------------------------------------------------------------------------
// $sug_list_form : template de liste pour les suggestions
//	------------------------------------------------------------------------------

$sug_list_form ="
<script type='text/javascript' src='./javascript/sorttable.js'></script>
<form class='form-$current_module' id='sug_list_form' name='sug_list_form' method='post' action=\"\" >
	<div class='form-contenu'>
		<table class='sortable' width='100%'><tr>
			<th>".htmlentities($msg['acquisition_sug_dat_cre'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['acquisition_sug_tit'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['acquisition_sug_edi'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['acquisition_sug_aut'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['acquisition_sug_etat'], ENT_QUOTES, $charset)."</th>	
			<th>".htmlentities($msg['acquisition_sug_iscat'], ENT_QUOTES, $charset)."</th>
			<th>".htmlentities($msg['acquisition_sug_url'], ENT_QUOTES, $charset)."</th>";

if ($acquisition_sugg_categ == '1') {
	$sug_list_form.="<th>".htmlentities($msg['acquisition_categ'], ENT_QUOTES, $charset)."</th>";
}		
$sug_list_form.="
	<th>".htmlentities($msg['acquisition_sugg_src'], ENT_QUOTES, $charset)."</th>
	<th>".htmlentities($msg['acquisition_sugg_date_publication'], ENT_QUOTES, $charset)."</th>	
	<th>".htmlentities($msg['acquisition_sugg_piece_jointe'], ENT_QUOTES, $charset)."</th>"
;	
$sug_list_form.="				
			<th>&nbsp;</th></tr>
			<!-- sug_list -->
		</table>
	</div>
	<div class='row'>
		<div class='left'><!-- bt_imp -->&nbsp;<!-- bt_exporter -->&nbsp;<!-- bt_export_tableau -->&nbsp;<!-- bt_todo --><span class='child' ><!-- to_categ --></span></div>
		<div class='right'><!-- bt_chk --></div>
	</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='left'><!-- bt_list --></div>
		<div class='right'><!-- bt_sup --></div>
	</div>
</form>
<!-- script -->
<br />
<div class='form' >
	<!-- nav_bar -->
</div>
";

//	------------------------------------------------------------------------------
// $sug_modif_form : template du form de suggestions
//	------------------------------------------------------------------------------
$sug_modif_form = "
<script language='javascript' type='text/javascript'>
function isnum(sText){
	var valid_chars = '0123456789.';
	for (var i = 0; i < sText.length; i++) {		
		if (valid_chars.indexOf(sText.charAt(i)) == -1) {
			if(i == 0) {
				if(sText.charAt(i) != '-')
					return false;
			} else return false;		
		} 
		
	}	
	return true;
}
		
function add_numeric_obj(obj,inc) {
	var id_obj = document.getElementById(obj);
	if(!isnum(id_obj.value)) id_obj.value=0;
	id_obj.value = parseInt(id_obj.value) + inc ;
}
</script>
<form class='form-$current_module' id='sug_modif_form' name='sug_modif_form' method='post' action='!!action!!' enctype='multipart/form-data'>
	<h3>!!form_title!!</h3>
	<div class='form-contenu'>
	
		<div class='row'>
			<div class='colonne5'>
				<label class='etiquette' >".htmlentities($msg['acquisition_sug_dat_cre'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne5'>
				<label class='etiquette' for='lib_orig'>".htmlentities($msg['acquisition_sug_orig'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne5'>
				<label class='etiquette' >".htmlentities($msg['acquisition_sug_poi'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne5'>
				<label class='etiquette' >".htmlentities($msg['acquisition_sug_etat'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne5'>
			";
if ($acquisition_sugg_categ=='1'){
	$sug_modif_form.= "<label class='etiquette' for='num_categ'>".htmlentities($msg['acquisition_categ'], ENT_QUOTES, $charset)."</label>";
} else {
	$sug_modif_form.= "&nbsp;";
}
$sug_modif_form.="		
			</div>
		</div>
		<div class='row'>
			<div class='colonne5'>
				!!dat_cre!!
			</div>
			<div class='colonne5'>
				<input type='hidden' id='orig' name='orig' value='!!orig!!' />
				<input type='hidden' id='typ' name='typ' value='!!typ!!' />
				<input type='hidden' id='id_notice' name='id_notice' value='!!id_notice!!' />
				!!lib_orig!!
				!!creator_ajout!!
				!!list_user!!
				<div id='user_list'></div>
			</div>
<!--
			<div class='colonne5'>
				<input type='hidden' id='poi' name='poi' value='!!poi!!' />
				<input type='text' id='poi_tot' name='poi_tot' class='saisie-10emd' readonly='readonly' value='!!poi_tot!!' />
			</div>
-->
			<div class='colonne5'>
				<input type='hidden' id='poi' name='poi' value='!!poi!!' />
				<input type='hidden' id='poi_tot' name='poi_tot' value='!!poi_tot!!' />
				<span id='aff_poi_tot' >!!poi_tot!!</span>
			</div>
			<div class='colonne5'>
				<input type='hidden' id='statut' name='statut' value='!!statut!!' />
				!!lib_statut!!
			</div>
			<div class='colonne5'>";
if ($acquisition_sugg_categ=='1'){
	$sug_modif_form.= "!!categ!!";
} else {
	$sug_modif_form.= "&nbsp;";
}
$sug_modif_form.="
			</div>
		</div>
		
		<div class='row'>
		
			<div class='colonne5'>
				<label class='etiquette' for='nombre_expl'>".htmlentities($msg['acquisition_sug_qte'], ENT_QUOTES, $charset)."</label>
				<div class='row'>
					<!-- nombre_expl -->
					<input  class='bouton_small' type='button' value='-' onclick=\"add_numeric_obj('nombre_expl',-1)\">
					<input maxLength='4' type='text' size='2' value='!!nombre_expl!!' id='nombre_expl' name='nombre_expl' >
					<input class='bouton_small' type='button' value='+' onclick=\"add_numeric_obj('nombre_expl',1)\">
				</div>	
			</div>
		
";	
if ($acquisition_sugg_localises == '1') {
$sug_modif_form.="
			<div class='colonne5'>
				<label class='etiquette'>".htmlentities($msg['acquisition_location'], ENT_QUOTES, $charset)."</label>
				<div class='row'>
					<!-- sel_location -->
				</div>	
			</div>";
		}
if(!isset($back_url)) $back_url = "onClick=\"document.location='./acquisition.php?categ=sug&action=list'\"";
$sug_modif_form.="	
		<div class='colonne5'>
			<label class='etiquette' for='sug_src'>".htmlentities($msg['acquisition_sugg_filtre_src'], ENT_QUOTES, $charset)."</label>
			<div class='row'>
				!!liste_source!!
			</div>
		</div>
		</div>	
		<div class='row'></div>
		<div class='row'><hr /></div>

		<div class='row'>
			<label class='etiquette' for='tit'>".htmlentities($msg['acquisition_sug_tit'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<input type='text' id='tit' name='tit' class='saisie-60em' value='!!tit!!' />
			!!lien!!
		</div>

		<div class='row'>
			<label class='etiquette' for='edi'>".htmlentities($msg['acquisition_sug_edi'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<input type='text' id='edi' name='edi' class='saisie-30em' value='!!edi!!' />
		</div>

		<div class='row'>
			<label class='etiquette' for='aut'>".htmlentities($msg['acquisition_sug_aut'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<input type='text' id='aut' name='aut' class='saisie-30em' value='!!aut!!' />
		</div>

		<div class='row'>
			<label class='etiquette' for='cod'>".htmlentities($msg['acquisition_sug_cod'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<input type='text' id='cod' name='cod' class='saisie-30em' value='!!cod!!' />
		</div>

		<div class='row'>
			<label class='etiquette' for='pri'>".htmlentities($msg['acquisition_sug_pri'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<input type='text' id='pri' name='pri' class='saisie-10em' value='!!pri!!' />
		</div>

		<div class='row'>
			<label class='etiquette' for='url_sug'>".htmlentities($msg['acquisition_sug_url'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<input type='text' id='url_sug' name='url_sug' class='saisie-80em' value='!!url_sug!!' />
			<!-- url_sug -->
		</div>
		<div class='row'>
			<label class='etiquette' for='com'>".htmlentities($msg['acquisition_sug_com'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<textarea id='com' name='com' class='saisie-80em' cols='62' rows='6' wrap='virtual'>!!com!!</textarea>
		</div>
		<div class='row'>
			<label class='etiquette' for='com_gestion'>".htmlentities($msg['acquisition_sug_com_gestion'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<textarea id='com_gestion' name='com_gestion' class='saisie-80em' cols='62' rows='6' wrap='virtual'>!!com_gestion!!</textarea>
		</div>		
		<div class='row'>
			<label class='etiquette' for='date_publi'>".htmlentities($msg['acquisition_sugg_date_publication'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<input type='text' id='date_publi' name='date_publi' value='!!date_publi!!'>
			<input type='button' class='bouton' id='date_publi_sug' name='date_publi_sug' value='...' onClick=\"openPopUp('./select.php?what=calendrier&caller=sug_modif_form&param1=date_publi&param2=date_publi&auto_submit=NO&date_anterieure=YES', 'calendar')\"/>
		</div>
		<div class='row'>
			<label class='etiquette' for='piece_jointe_sug'>".htmlentities($msg['acquisition_sugg_piece_jointe'], ENT_QUOTES, $charset)."</label>
		</div>
		!!div_pj!!
		<div class='row'></div>		
	</div>	

	<!--	boutons	-->
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='$msg[76]' $back_url />
			<input type='submit' class='bouton' value='$msg[77]' onClick=\"return test_form(this.form);  \" />
			<!-- bouton_cat -->
		</div>
		<div class='right'>
			<!-- bouton_sup -->
		</div>
	</div>

	<div class='row'></div>
</form>


<script type='text/javascript'>

	document.getElementById('tit').focus();
	function test_form(form) {
		
		var ret = true;
		if((form.tit.value.replace(/^\s+|\s+$/g, '').length == 0) || (((form.aut.value.replace(/^\s+|\s+$/g, '').length == 0) && (form.edi.value.replace(/^\s+|\s+$/g, '').length == 0)) 
			&& (form.cod.value.replace(/^\s+|\s+$/g, '').length == 0) 
			&& (form.piece_jointe_sug.value.length == 0))){	    	
			ret = false;
	    }
	    
	    if(!ret)
	    	 alert(\"$msg[acquisition_sug_ko]\");
		return ret;
			
	}
	
	function ajax_origine(){
		
		var action = new http_request();
		var url = './ajax.php?module=acquisition&categ=sugg&id_sugg=!!id_sug!!&quoifaire=ajout_origine';
		action.request(url, true, \"orig=\"+document.getElementById('orig').value+\"&type_orig=\"+document.getElementById('typ').value);
		if(action.get_status() == 0){
			if(document.getElementById('ori')){
				document.getElementById('ori').style.display = 'none';
				document.getElementById('oriChild').style.display = 'none';
			}
			if(document.getElementById('creator_lib_orig')){
				document.getElementById('creator_lib_orig').style.display = 'none';
				document.getElementById('creator_btn_orig').style.display = 'none';
			}
			document.getElementById('user_list').innerHTML = action.get_text();
			document.getElementById('orig').value = 0;
			document.getElementById('typ').value = 0;
			document.getElementById('creator_lib_orig').value = '';
			document.getElementById('creator_lib_orig_ajax').value = '';
		}
		
	}
	
	function ajax_suppr_origine(origine, type){
		
		var action = new http_request();
		var url = './ajax.php?module=acquisition&categ=sugg&id_sugg=!!id_sug!!&quoifaire=suppr_origine';
		action.request(url, true, \"orig=\"+origine+\"&type_orig=\"+type);
		if(action.get_status() == 0){
			if(document.getElementById('ori')){
				document.getElementById('ori').style.display = 'none';
				document.getElementById('oriChild').style.display = 'none';
			}
			document.getElementById('user_list').innerHTML = action.get_text();
		}
		
	}
	

</script>
";


$orig_form_mod = "
	<input type='text' id='lib_orig' name='lib_orig' class='saisie-10emr' value='!!lib_orig!!' onchange=\"openPopUp('./select.php?what=origine&caller=sug_modif_form&param1=orig&param2=lib_orig&param3=typ&param4=poi&param5=poi_tot&param6=aff_poi_tot&deb_rech='+".pmb_escape()."(this.form.lib_orig.value), 'selector')\" />
	<input type='button' class='bouton_small' value='...' onclick=\"openPopUp('./select.php?what=origine&caller=sug_modif_form&param1=orig&param2=lib_orig&param3=typ&param4=poi&param5=poi_tot&param6=aff_poi_tot&deb_rech='+".pmb_escape()."(this.form.lib_orig.value), 'selector')\" />";

$bt_chk ="<input type='button' id='bt_chk' class='bouton_small' value='".$msg['acquisition_sug_checkAll']."' onClick=\"checkAll('sug_list_form', 'chk', check); return false;\" />";
$bt_supChk = "<input type='button' class='bouton_small' value='$msg[63]' onClick=\"supChk();\" />";


$bt_imp = "<input type='button' class='bouton_small' value='$msg[imprimer]' onClick=\"!!imp!!\" />";
$bt_exporter = "<input type='button' class='bouton_small' value='".$msg['admin_Expvers']."' onClick=\"!!exp!!\" /><!-- list_export -->";
$bt_export_tableau = "<input type='button' class='bouton_small' value='".$msg['sugg_export_tableau']."' onClick=\"!!exp!!\" />";

$lk_url_sug = "<a href='!!url_sug!!' target='_blank'><img src='".get_url_icon('globe.gif')."' style='border:0px' alt='' /></a>";


$script = "
<script type='text/javascript'>

	var check = true;

	//Coche et décoche les éléments de la liste
	function checkAll(the_form, the_objet, do_check) {
	
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
		if (check == true) {
			check = false;
			document.getElementById('bt_chk').value = '".$msg['acquisition_sug_uncheckAll']."';
		} else {
			check = true;
			document.getElementById('bt_chk').value = '".$msg['acquisition_sug_checkAll']."';	
		}
		return true;
	}


	//Vérifie que le nb d'élements minimum passé en paramètre est coché
	function verifChk(nb_to_chk) {
		
		var elts = document.forms['sug_list_form'].elements['chk[]'];
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
		if (nb_chk < nb_to_chk) {
			alert(\"".$msg['acquisition_sug_msg_nocheck']." \"+nb_to_chk+\" ".$msg['acquisition_sug_msg_nocheck2']."\");
			return false;	
		}
		return true;
	}

	<!-- script_list -->
";

$script.="

	function fusVal(){
		if(!verifChk(1)) return false;
		r = confirm(\"".$msg['acquisition_sug_msg_fusVal']."\");
		if (r) {
			document.forms['sug_list_form'].setAttribute('action', './acquisition.php?categ=sug&action=fusVal&nb_per_page=".$nb_per_page."');
			document.forms['sug_list_form'].submit();
			return true;	
		}
		return false;
	}

</script>";

$sel_orig_form = "
<div class='row'>
	<input type='hidden' id='user_id!!i!!' name='user_id[!!i!!]' value='!!user_id!!'/>
	<input type='hidden' id='user_statut!!i!!' name='user_statut[!!i!!]' value='!!user_statut!!' />
	<input type='text' id='user_txt!!i!!' name='user_txt[!!i!!]' class='saisie-20emr' value='!!user_txt!!'/>
	<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=origine&caller=search&param1=user_id!!i!!&param2=user_txt!!i!!&param3=user_statut!!i!!&deb_rech='+".pmb_escape()."(this.form.user_txt!!i!!.value)+'&callback=filtrer_user', 'selector')\" />
	<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.user_id!!i!!.value=0;this.form.user_statut!!i!!.value=0;this.form.user_txt!!i!!.value=''\"/>
</div>
";

$bt_fusVal = "<input type='button' class='bouton' value='$msg[acquisition_sug_bt_fus]' onClick=\"fusVal(); \" />";

