<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authors.tpl.php,v 1.59 2019-05-27 12:17:04 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $author_form, $author_replace; 
global $pmb_autorites_verif_js, $base_path;
global $pmb_form_authorities_editables, $PMBuserid;
global $msg, $current_module, $charset, $author_warning_author_exist;
//	----------------------------------
// $author_form : form saisie auteur
// champs :
//	author_type : 70/71 (select)
//	author_nom element d'entrée
//	author_rejete element rejeté
//	date1 (text max:4) date 1
//	date2 (text max:4) date 2
//	voir_id (hidden) id de la forme retenue
//	voir_libelle 
$author_form = jscript_unload_question()."
".($pmb_autorites_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_autorites_verif_js'></script>":"")."
<script type='text/javascript'>
	function test_form(form) {
		if (typeof check_form == 'function') {
			if (!check_form()) {
				return false;
			}
		}
	";

if ($pmb_autorites_verif_js != "") {
	$author_form .= "
		if(typeof check_perso_author_form == 'function'){
			var check = check_perso_author_form(form);
			if (check == false) return false;
		}";
}

$author_form .= "
		if(form.author_nom.value.length == 0)
			{
				alert(\"$msg[213]\");
				return false;
			}

		if(form.voir_libelle.value.length == 0)
			{
				form.voir_id.value='';
			}
		unload_off();	
		return true;
	}

function confirm_delete() {
        result = confirm(\"".$msg['confirm_suppr']."\");
        if(result) {
        	unload_off();
            document.location='!!delete_action!!';
		} else
            document.forms['saisie_auteur'].elements['author_nom'].focus();
    }
	function check_link(id) {
		w=window.open(document.getElementById(id).value);
		w.focus();
	}
</script>
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
	     ready(function(){
	     	new FormEdit();
	     });
	});
</script>
<script type='text/javascript'>
	document.title='!!document_title!!';
</script>
<form class='form-$current_module' id='saisie_auteur' name='saisie_auteur' method='post' action='!!action!!' onSubmit=\"return false\" enctype='multipart/form-data'>
<div class='row'>
	<div class='left'>
		<h3><label id='libelle_titre'>!!libelle!!</label></h3>
	</div>
	<div class='right'>";
	$author_form.='
	<!-- Selecteur de statut -->
		<label class="etiquette" for="authority_statut">'.$msg['authorities_statut_label'].'</label>
		!!auth_statut_selector!!
	';

	if ($PMBuserid==1 && $pmb_form_authorities_editables==1){
		$author_form.="<input type='button' class='bouton_small' value='".$msg["authorities_edit_format"]."' id=\"bt_inedit\"/>";
	}
	if ($pmb_form_authorities_editables==1) {
		$author_form.="<input type='button' class='bouton_small' value=\"".$msg["authorities_origin_format"]."\" id=\"bt_origin_format\"/>";
	}
	$author_form .= "
	</div>
</div>
<div class='form-contenu'>
	<div class='row'>
		<a onclick='expandAll();return false;' href='#'><img border='0' id='expandall' src='".get_url_icon('expand_all.gif')."'></a>
		<a onclick='collapseAll();return false;' href='#'><img border='0' id='collapseall' src='".get_url_icon('collapse_all.gif')."'></a>
	</div>
	<div id='zone-container'>
		<!--	type	-->
		<div id='el0Child_0' class='row' movable='yes' title=\"".htmlentities($msg[205], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='author_type_sel'>$msg[205]</label>
				</div>
			<div class='row'>
				<select name='author_type' id='author_type_sel' backbone='yes'>
					<option value='70'!!sel_pp!!>$msg[203]</option>
					<option value='71'!!sel_coll!!>$msg[204]</option>
					<option value='72'!!sel_congres!!>".$msg["congres_libelle"]."</option>		
				</select>
			</div>
		</div>
        
		<div id='el0Child_1' class='row'>
			<div id='el0Child_1_a' class='colonne2' movable='yes' title=\"".htmlentities($msg[201], ENT_QUOTES, $charset)."\">
				<!--	nom	-->
				<div class='row'>
					<label class='etiquette' for='author_nom'>$msg[201]</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30em' id='author_nom' name='author_nom' value=\"!!author_nom!!\" data-pmb-deb-rech='1'/>
				</div>
	        </div>
			<div id='el0Child_1_b' class='colonne_suite' movable='yes' title=\"".htmlentities($msg[202], ENT_QUOTES, $charset)."\">
				<!--	rejete	-->
				<div class='row'>
					<label class='etiquette' for='form_rejete'>$msg[202]</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30em' id='form_rejete' name='author_rejete' value=\"!!author_rejete!!\"  />
				</div>
	        </div>
       </div>
       
		<!--	dates	-->
		<div id='el0Child_2' class='row' movable='yes' title=\"".htmlentities($msg[713], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='form_dates'>$msg[713]</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-80em' id='form_dates' name='date' value='!!date!!'>
			</div>
		</div>
	
		<!--	lieu	-->
		<div id='el0Child_3' class='row' movable='yes' title=\"".htmlentities($msg["congres_lieu_libelle"], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='form_lieu'>".$msg["congres_lieu_libelle"]."</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-80em' id='form_lieu' name='lieu' value='!!lieu!!'>
			</div>
		</div>
		<div id='el0Child_4' class='row'>
			<div id='el0Child_4_a' class='colonne2' movable='yes' title=\"".htmlentities($msg["congres_ville_libelle"], ENT_QUOTES, $charset)."\">
				<!--	ville	-->
				<div class='row'>
					<label class='etiquette' for='form_ville'>".$msg["congres_ville_libelle"]."</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30em' id='form_ville' name='ville' value=\"!!ville!!\" />
				</div>
			</div>
			<div id='el0Child_4_b' class='colonne_suite' movable='yes' title=\"".htmlentities($msg["congres_pays_libelle"], ENT_QUOTES, $charset)."\">
				<!--	pays	-->
				<div class='row'>
					<label class='etiquette' for='form_pays'>".$msg["congres_pays_libelle"]."</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30em' id='form_pays' name='pays' value=\"!!pays!!\"  />
				</div>
	        </div>
		</div>
		<div id='el0Child_5' class='row'>				
			<div id='el0Child_5_a' class='colonne2' movable='yes' title=\"".htmlentities($msg["congres_subdivision_libelle"], ENT_QUOTES, $charset)."\">
				<!--	subdivision	-->
				<div class='row'>
					<label class='etiquette' for='form_subdivision'>".$msg["congres_subdivision_libelle"]."</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30em' id='form_subdivision' name='subdivision' value=\"!!subdivision!!\" />
				</div>
	        </div>
			<div id='el0Child_5_b' class='colonne_suite' movable='yes' title=\"".htmlentities($msg["congres_numero_libelle"], ENT_QUOTES, $charset)."\">
				<!--	numero	-->
				<div class='row'>
					<label class='etiquette' for='form_numero'>".$msg["congres_numero_libelle"]."</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30em' id='form_numero' name='numero' value=\"!!numero!!\"  />
				</div>
	        </div>		
		</div>
		
		<!--	forme retenue	-->
		<div id='el0Child_6' class='row' movable='yes' title=\"".htmlentities($msg[206], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='voir_libelle'>$msg[206]</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-50emr' id='voir_libelle' name='voir_libelle' value=\"!!voir_libelle!!\" completion=\"authors\" autfield=\"voir_id\" autexclude=\"!!id!!\"
			    onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=auteur&caller=saisie_auteur&param1=voir_id&param2=voir_libelle', 'selector'); }\" />
			
				<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=auteur&caller=saisie_auteur&param1=voir_id&param2=voir_libelle', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
				<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.voir_libelle.value=''; this.form.voir_id.value='0'; \" />
				<input type='hidden' value='!!voir_id!!' name='voir_id' id='voir_id' />
			</div>
		</div>		
        <!-- ISNI -->
		<div id='el0Child_10' class='row' movable='yes' title=\"".htmlentities($msg['author_isni'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='author_isni'>" . $msg['author_isni'] ."</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-80em' name='author_isni' id='author_isni' value=\"!!author_isni!!\" maxlength='255' />
			</div>
		</div>
		<!-- web -->
		<div id='el0Child_7' class='row' movable='yes' title=\"".htmlentities($msg[147], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='author_web'>$msg[147]</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-80em' name='author_web' id='author_web' value=\"!!author_web!!\" maxlength='255' />
				<input class='bouton' type='button' onClick=\"check_link('author_web')\" title='$msg[CheckLink]' value='$msg[CheckButton]' />
			</div>
		</div>
	
		<!-- Commentaire -->
		<div id='el0Child_8' class='row' movable='yes' title=\"".htmlentities($msg['author_comment'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='author_comment'>$msg[author_comment]</label>
			</div>
			<div class='row'>
				<textarea class='saisie-80em' id='author_comment' name='author_comment' cols='62' rows='4' wrap='virtual'>!!author_comment!!</textarea>
			</div>
		</div>
		!!concept_form!!
		!!thumbnail_url_form!!
		!!aut_pperso!!
		<div id='el0Child_9' class='row' movable='yes' title=\"".htmlentities($msg['authority_import_denied'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='author_import_denied'>".$msg['authority_import_denied']."</label> &nbsp;
				<input type='checkbox' id='author_import_denied' name='author_import_denied' value='1' !!author_import_denied!!/>
			</div>
		</div>
		<!-- aut_link -->
		<!-- map -->
	</div>
</div>
<!--	boutons	-->
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='$msg[76]' id='btcancel' onClick=\"unload_off();document.location='!!cancel_action!!';\" />
		<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"document.getElementById('save_and_continue').value=0;if (test_form(this.form)) this.form.submit();\" />
		<input type='hidden' name='save_and_continue' id='save_and_continue' value='' />
        <input type='button' id='update_continue' class='bouton' value='" . $msg['save_and_continue'] . "' onClick=\"document.getElementById('save_and_continue').value=1;if (test_form(this.form)) this.form.submit();\" />
		!!remplace!!
		!!voir_notices!!
		!!dupliquer!!
		!!audit_bt!!
		<input type='hidden' name='page' value='!!page!!' />
		<input type='hidden' name='nbr_lignes' value='!!nbr_lignes!!' />
		<input type='hidden' name='user_input' value=\"!!user_input!!\" />
	</div>
	<div class='right'>
		!!delete!!
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['saisie_auteur'].elements['author_nom'].focus();
</script>
!!liste_des_renvoyes_vers!!
";

// $author_replace : form remplacement auteur
$author_replace = "
<script src='javascript/ajax.js'></script>
<form class='form-$current_module' name='author_replace' method='post' action='!!controller_url_base!!&sub=replace&id=!!id!!' onSubmit=\"return false\" >
<h3>$msg[159] !!old_author_libelle!! </h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='par'>$msg[160]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-50emr' id='author_libelle' name='author_libelle' value=\"\" completion=\"authors\" autfield=\"by\" autexclude=\"!!id!!\"
    	onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=auteur&caller=author_replace&param1=by&param2=author_libelle&no_display=!!id!!', 'selector'); }\" />

		<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=auteur&caller=author_replace&param1=by&param2=author_libelle&no_display=!!id!!', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.author_libelle.value=''; this.form.by.value='0'; \" />
		<input type='hidden' name='by' id='by' value=''>
	</div>
	<div class='row'>		
		<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
	</div>	
</div>
<div class='row'>
	<input type='button' class='bouton' value='$msg[76]' id='btcancel' onClick=\"document.location='!!cancel_action!!';\">
	<input type='button' class='bouton' value='$msg[159]' id='btsubmit' onClick=\"this.form.submit();\" >
</div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['author_replace'].elements['author_libelle'].focus();
</script>
";

$author_warning_author_exist = "
<form class='form-".$current_module."' id='forcing_tu_creation' name='forcing_tu_creation' method='post' action='!!action!!' enctype='multipart/form-data'>
    <div class='row'>
		<img src='".get_url_icon('error.gif')."'>
        <strong>!!error_title!!</strong>
        <br/>
        !!error_message!!
    </div>
    <div class='row'>
        !!hidden_values!!
        <input type='hidden' id='forcing_values' name='forcing_values' value='!!forcing_values!!'/>
        <input type='submit' class='bouton' id='forcing_button' value='!!forcing_message!!'/>
    </div>
</form>
";