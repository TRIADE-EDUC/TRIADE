<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.tpl.php,v 1.82 2022/04/12 14:26:40 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $category_form, $add_see_also, $categ0, $categ1, $form_categ_parent, $form_renvoivoir, $form_renvoivoiraussi, $form_num_aut, $categ_replace_content_form, $categories_liaison_tpl, $charset;
global $traduction_na_tpl, $traduction_cm_tpl, $pmb_form_authorities_editables, $PMBuserid, $pmb_autorites_verif_js, $base_path, $include_path, $categ_browser, $msg, $current_module;
global $thesaurus_mode_pmb, $categ_add_button;

// templates pour la gestion des catégories
require_once("$base_path/javascript/misc.inc.php");
require_once("$include_path/misc.inc.php"); 

// $categ_browser : template du browser de catégories


$categ_browser = "

<br />
<div class='row'>
	!!browser_top!!
	!!browser_header!!<hr />
</div>
<div class='row'>
	<script type='text/javascript' src='./javascript/sorttable.js'></script>
	<table border='0' class='sortable'>
		!!browser_content!!
	</table>
</div>";

// $category_form : template du form de catégories
$category_form = jscript_unload_question();

$category_form.= $pmb_autorites_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_autorites_verif_js'></script>":"";

$category_form.= "
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
	     ready(function(){
	     	new FormEdit();
	     });
	});
</script>
<script type='text/javascript'>
	function test_form(form){
		if (typeof check_form == 'function') {
			if (!check_form()) {
				return false;
			}
		}
		";
if ($pmb_autorites_verif_js != "") {
	$category_form.= "
		if(typeof check_perso_category_form == 'function'){
			var check = check_perso_category_form(form);
			if (check == false) return false;
		}";
}
$category_form.= "if(document.getElementById('category_libelle_defaut').value.replace(/^\s+|\s+$/g, '').length == 0){
			var msg = \"".$msg['thes_libelle_categ_ref_manquant']."\"+\"\\n!!lang_def_js!!\";
			alert(msg);
			return false;
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
            document.forms['categ_form'].elements['category_libelle!!lang_def_cle!!'].focus();
    }
</script>
<script type='text/javascript'>
	document.title='!!document_title!!';
</script>
<form class='form-$current_module' id='categ_form' name='categ_form' method='post' action='!!action!!' enctype='multipart/form-data'>
<div class='row'>
	<div class='left'>
		<h3>!!form_title!!</h3>
	</div>
	<div class='right'>";

	$category_form.='
	<!-- Selecteur de statut -->
		<label class="etiquette" for="authority_statut">'.$msg['authorities_statut_label'].'</label>
		!!auth_statut_selector!!
	';

	if ($PMBuserid==1 && $pmb_form_authorities_editables==1){
		$category_form.="<input type='button' class='bouton_small' value='".$msg["authorities_edit_format"]."' id=\"bt_inedit\"/>";
	}
	if ($pmb_form_authorities_editables==1) {
		$category_form.="<input type='button' class='bouton_small' value=\"".$msg["authorities_origin_format"]."\" id=\"bt_origin_format\"/>";
	}
	$category_form .= "
	</div>
</div>
<div class='form-contenu'>
	<div class='row'>
		<a onclick='expandAll();return false;' href='#'><img border='0' id='expandall' src='".get_url_icon('expand_all.gif')."'></a>
		<a onclick='collapseAll();return false;' href='#'><img border='0' id='collapseall' src='".get_url_icon('collapse_all.gif')."'></a>
	</div>
	<div id='zone-container'>";
	if($thesaurus_mode_pmb != 0) {
	    $category_form .= "
        <div id='el0Child_9' class='row' movable='yes' title=\"".htmlentities($msg['thesaurus'], ENT_QUOTES, $charset)."\">
            <div class='row'>
				<label class='etiquette' >".htmlentities($msg['thesaurus'], ENT_QUOTES, $charset)."</label>
			</div>
            <div class='row'>
                <!-- sel_thesaurus -->
            </div>
        </div>";
	}
$category_form .= "<!-- libelle defaut -->
		<div id='el0Child_0' class='row'>
			<div id='el0Child_0_a' class='colonne2' movable='yes' title=\"".htmlentities($msg[103], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette' >".htmlentities($msg[103], ENT_QUOTES, $charset)."</label><label class='etiquette'>!!lang_def!!</label>
					<!-- bt_lib_trad -->
				</div>
				<div class='row'>
					<input type='text' class='saisie-80em' id='category_libelle_defaut' name='category_libelle!!lang_def_cle!!' value=\"!!lang_def_libelle!!\" />
					<!--	libelle traductions-->
					<div id='lib_trad' class='form-$current_module' style='display:none' >
						!!c_libelle_trad!!
					</div>
				</div>
			</div>
			<div id='el0Child_0_b' class='colonne_suite' movable='yes' title=\"".htmlentities($msg['not_use_in_indexation'], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette'>".htmlentities($msg['not_use_in_indexation'], ENT_QUOTES, $charset)."</label><input type=\"checkbox\" id=\"not_use_in_indexation\"  name=\"not_use_in_indexation\" value='1' !!not_use_in_indexation!! />
				</div>
				<div class='row'></div>
			</div>
		</div>
		
		<div id='el0Child_1' class='row'>
			<!--	note application defaut -->
			<div id='el0Child_1_a' class='colonne2' movable='yes' title=\"".htmlentities($msg['categ_na'], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette'>".htmlentities($msg['categ_na'], ENT_QUOTES, $charset)."</label><label class='etiquette'>!!lang_def!!</label>
					<!-- bt_na_trad -->
				</div>
				<div class='row'>
					<textarea class='saisie-50em' id='category_na' name='category_na!!lang_def_cle!!' cols='40' rows='2' wrap='virtual'>!!lang_def_na!!</textarea>
				</div>
				<div id='na_trad' class='row' style='display:none' >
					<!--note application traductions -->
					!!na_trad!!
				</div>
			</div>
		
			<!--	commentaire defaut -->
			<div id='el0Child_1_b' class='colonne_suite' movable='yes' title=\"".htmlentities($msg['categ_commentaire'], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette'>".htmlentities($msg['categ_commentaire'], ENT_QUOTES, $charset)."</label><label class='etiquette'>!!lang_def!!</label>
					<!-- bt_cm_trad -->
				</div>
				<div class='row'>
					<textarea class='saisie-50em' id='category_comment' name='category_cm!!lang_def_cle!!' cols='40' rows='2' wrap='virtual'>!!lang_def_cm!!</textarea>
				</div>
				<div id='cm_trad' class='row' style='display:none' >
					<!--commentaire traductions -->
					!!cm_trad!!
				</div>
			</div>
		</div>
			
		<!--categ_parent -->
		<!-- renvoivoir -->
		<!-- renvoivoiraussi -->
		<!-- liaison -->
		!!aut_pperso!!
		<div id='el0Child_5' class='row'>
			<div id='el0Child_5_a' class='colonne2' movable='yes' title=\"".htmlentities($msg['categ_num_aut'], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette' >".htmlentities($msg['categ_num_aut'], ENT_QUOTES, $charset)."</label>
				</div>
				<div class='row'>
					<!-- numero_autorite -->
				</div>
			</div>
		</div>
		!!concept_form!!
		!!thumbnail_url_form!!
		<div id='el0Child_6' class='row' movable='yes' title=\"".htmlentities($msg['authority_import_denied'], ENT_QUOTES, $charset)."\">
			<label class='etiquette' for='authority_import_denied'>".$msg['authority_import_denied']."</label> &nbsp;
			<input type='checkbox' id='authority_import_denied' name='authority_import_denied' value='1' !!authority_import_denied!!/>
		</div>		
		<!-- aut_link -->
		<div id='el0Child_8' class='row' movable='yes'>
			<!-- map -->
		</div>
	</div>
</div>

<!--boutons	-->
<div class='row'>
	<div class='left'>
		<input type='button' id='btcancel' class='bouton' value='$msg[76]' onClick=\"unload_off();document.location='!!cancel_action!!'\" />
		<input type='submit' id='btsubmit' class='bouton' value='$msg[77]' onClick=\"document.getElementById('save_and_continue').value=0;return test_form(this.form)\" />
		<input type='hidden' name='save_and_continue' id='save_and_continue' value='' />
		<input type='button' id='update_continue' class='bouton' value='" . $msg['save_and_continue'] . "' onClick=\"document.getElementById('save_and_continue').value=1;if(test_form(this.form)) {this.form.submit();}\" />
		<!-- remplace_categ -->
		!!voir_notices!!
		!!audit_bt!!
		<!-- imprimer_thesaurus -->
		<input type='hidden' name='page' value='!!page!!' />
		<input type='hidden' name='nbr_lignes' value='!!nbr_lignes!!' />
		<input type='hidden' name='user_input' value=\"!!user_input!!\" />
		<input type='hidden' name='nb_per_page' value=\"!!nb_per_page!!\" />
	</div>
	<div class='right'>
		<!-- delete_button -->
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['categ_form'].elements['category_libelle!!lang_def_cle!!'].focus();
	ajax_parse_dom();

	function bascule_trad(item) {
		var elt = document.getElementById(item);
		if (elt.style.display == 'none') elt.style.display = ''; else elt.style.display = 'none'; 
	}
</script>";

$add_see_also="
<script>
	function fonction_selecteur_categ() {
		name=this.getAttribute('id').substring(4);
		name_id = name.substr(0,7)+'_id'+name.substr(7);
		openPopUp('./select.php?what=categorie&caller=categ_form&p1='+name_id+'&p2='+name+'&dyn=1', 'selector_category');
	}
	function fonction_raz_categ() {
		name=this.getAttribute('id').substring(4);
		name_id = name.substr(0,7)+'_id'+name.substr(7);
		name_rec = name.substr(0,7)+'_rec'+name.substr(7);
		document.getElementById(name_id).value=0;
		document.getElementById(name).value='';
		document.getElementById(name_rec).checked=false;
	}
	function get_selector_categ() {
		template = document.getElementById('addcateg');
		selector = '';
		for (let i = 0; i < template.childNodes.length; i++) {
		    if (template.childNodes[i].tagName == 'DIV') {
		        row = template.childNodes[i];
		        for (let j = 0; j < row.childNodes.length; j++) {
		            if (row.childNodes[j].tagName == 'INPUT' && row.childNodes[j].value == '...') {
		                selector = row.childNodes[j].cloneNode();
		                break;
                    }
                }
            }
            if (selector) {
                break;
            }
		}
		if (!selector) {
		    row = template.previousElementSibling;
	        for (let j = 0; j < row.childNodes.length; j++) {
	            if (row.childNodes[j].tagName == 'INPUT' && row.childNodes[j].value == '...') {
	                selector = row.childNodes[j].cloneNode();
	                break;
                }
            }
		}
		return selector;
    }
	function add_categ() {
		let template = document.getElementById('addcateg');
		let f_categ_selector = get_selector_categ();
		let categ = document.createElement('div');
		categ.className = 'row';

		let suffixe = eval('document.categ_form.max_categ.value');
		let nom_id = 'f_categ'+suffixe;
		let f_categ = document.createElement('input');
		f_categ.setAttribute('name',nom_id);
		f_categ.setAttribute('id',nom_id);
		f_categ.setAttribute('type','text');
		f_categ.className='saisie-80emr';
		f_categ.setAttribute('value','');
		f_categ.setAttribute('completion','categories_mul');
		f_categ.setAttribute('autfield','f_categ_id'+suffixe);
		f_categ.setAttribute('autocomplete','off');
		
		let f_categ_rec = document.createElement('input');
		f_categ_rec.name = 'f_categ_rec'+suffixe;
		f_categ_rec.setAttribute('id','f_categ_rec'+suffixe);
		f_categ_rec.setAttribute('type','checkbox');
		f_categ_rec.setAttribute('value','1');		

		let del_f_categ = document.createElement('input');
		del_f_categ.setAttribute('id','del_f_categ'+suffixe);
		del_f_categ.onclick=fonction_raz_categ;
		del_f_categ.setAttribute('type','button');
		del_f_categ.className='bouton_small';
		del_f_categ.setAttribute('readonly','');
		del_f_categ.setAttribute('value','$msg[raz]');
		
		let f_categ_id = document.createElement('input');
		f_categ_id.name='f_categ_id'+suffixe;
		f_categ_id.setAttribute('type','hidden');
		f_categ_id.setAttribute('id','f_categ_id'+suffixe);
		f_categ_id.setAttribute('value','');

		let buttonAdd = document.getElementById('button_add_categ');
		
		categ.appendChild(f_categ);
		categ.appendChild(document.createTextNode(' '));
		categ.appendChild(f_categ_rec);
		categ.appendChild(document.createTextNode(' '));
		categ.appendChild(f_categ_selector);
		categ.appendChild(document.createTextNode(' '));
		categ.appendChild(del_f_categ);
		categ.appendChild(f_categ_id);
		categ.appendChild(document.createTextNode(' '));
		categ.appendChild(buttonAdd);

		template.appendChild(categ);
		
		if(document.getElementById('tab_categ_order')) {
			tab_categ_order = document.getElementById('tab_categ_order');
			if (tab_categ_order.value != '') tab_categ_order.value += ','+suffixe;
		}

		document.categ_form.max_categ.value=suffixe*1+1*1 ;
        ajax_pack_element(f_categ);
	}
</script>";
	
$categ0 = "
	<div class='row'>
		<input type='text' class='saisie-80emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' value=\"!!categ_libelle!!\" 
            completion='categories_mul' autfield='f_categ_id!!icateg!!' autocomplete='off'/>
        <input type='checkbox' id='f_categ_rec!!icateg!!' name='f_categ_rec!!icateg!!' !!chk!! />
		<input type='button' class='bouton_small' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=categorie&caller=categ_form&p1=f_categ_id!!icateg!!&p2=f_categ!!icateg!!&dyn=1&parent=!!parent!!&id2=!!id!!', 'selector_category')\" />
		<input type='button' class='bouton_small' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; this.form.f_categ_rec!!icateg!!.checked=false; \" />
		<input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
		!!categ_add_button!!
	</div>";

$categ_add_button = "
	<input id='button_add_categ' type='button' class='bouton_small' value='+' onClick=\"add_categ();\"/>";

$form_categ_parent = "
	<div id='el0Child_2' class='row' movable='yes' title=\"".htmlentities($msg['categ_parent'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label class='etiquette' for='form_categparent'>".htmlentities($msg['categ_parent'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80emr' id='category_parent' name='category_parent' value=\"!!parent_libelle!!\"
                completion='categories_mul' autfield='category_parent_id' autocomplete='off' autexclude='!!id_noeud!!' att_id_filter='!!id_thes!!'/>
			<input type='button' class='bouton_small' onclick=\"openPopUp('./select.php?what=categorie&caller=categ_form&p1=category_parent_id&p2=category_parent&keep_tilde=1&parent=!!parent!!&id2='+document.categ_form.category_parent_id.value, 'selector_category')\" title='$msg[157]' value='$msg[parcourir]' />
			<input type='button' class='bouton_small' value='$msg[raz]' onclick=\"this.form.category_parent.value=''; this.form.category_parent_id.value='0'; \" />
			<input type='hidden' id='category_parent_id' name='category_parent_id' value='!!parent_value!!' />
		</div>
	</div>";

$form_renvoivoir = "
	<div id='el0Child_3' class='row' movable='yes' title=\"".htmlentities($msg['categ_renvoi'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label class='etiquette' for='form_renvoivoir'>".htmlentities($msg['categ_renvoi'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80emr' id='category_voir' name='category_voir' size='48' value=\"!!voir_libelle!!\"
                completion='categories_mul' autfield='category_voir_id' autocomplete='off' />
			<input type='button' class='bouton_small' onclick=\"openPopUp('./select.php?what=categorie&caller=categ_form&p1=category_voir_id&p2=category_voir&parent=!!parent!!&id2='+document.categ_form.category_voir_id.value, 'selector_category')\" title='$msg[157]' value='$msg[parcourir]' />
			<input type='button' class='bouton_small' value='$msg[raz]' onclick=\"this.form.category_voir.value=''; this.form.category_voir_id.value='0'; \" />
			<input type='hidden' id='category_voir_id' name='category_voir_id' value='!!voir_value!!' />
		</div>
	</div>";

$form_renvoivoiraussi = "
	<div id='el0Child_4' class='row' movable='yes' title=\"".htmlentities($msg['renvoi_voir_aussi'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label class='etiquette' for='form_renvoivoir'>".$msg['renvoi_voir_aussi'].$msg['renvoi_reciproque']."</label>
		</div>
		!!renvoi_voir_aussi!!
	</div>";

$form_num_aut = "
	<input type='text' class='saisie-20em' id='num_aut' name='num_aut' value=\"!!num_aut!!\" />";
	
// $categ_replace_content_form : form remplacement categorie
$categ_replace_content_form = "
<div class='row'>
	<label class='etiquette' for='par'>".htmlentities($msg[160], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-80emr' name='by_libelle' id='by_libelle' value=\"\" completion=\"categories_mul\" autfield=\"by\" />
	<input type='button' class='bouton_small' onclick=\"openPopUp('./select.php?what=categorie&caller=categ_replace&p1=by&p2=by_libelle&keep_tilde=1&parent=0&deb_rech='+".pmb_escape()."(this.form.by_libelle.value), 'selector_category')\" value='$msg[parcourir]' />
	<input type='button' class='bouton_small' value='$msg[raz]' onclick=\"this.form.by_libelle.value=''; this.form.by.value='0'; \" />
	<input type='hidden' name='by' id='by' value='0'>
</div>
<div class='row'>		
	<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
</div>
";

$categories_liaison_tpl = "
<div id='el0Child_7' class='row' movable='yes' title=\"".htmlentities($msg['categ_links'], ENT_QUOTES, $charset)."\">
	<div id='el1Parent' class='parent' >
		<h3>
	    	<img src='".get_url_icon('minus.gif')."' class='img_plus align_bottom' name='imEx' id='el1Img' title='".$msg['categ_links']."' border='0' onClick=\"expandBase('el1', true); return false;\" />
	    	".$msg['categ_links']."
	    </h3>
	</div>
	<div id='el1Child' class='child'>
	    <!-- categ_child -->
	    <!-- categ_renvoivoir -->
	    <!-- categ_renvoivoiraussi -->
	</div>
	<div class='row'>&nbsp;</div>
</div>";

$traduction_na_tpl = "
<br />
<label class='etiquette'>(!!lang_value!!) </label>
<br />
<textarea class='saisie-50em' id='category_na' name='category_na[!!lang!!]'  cols='40' rows='2' wrap='virtual'>!!note_application!!</textarea>";

$traduction_cm_tpl = "
<br />
<label class='etiquette'>(!!lang_value!!) </label>
<br />
<textarea class='saisie-50em' id='category_cm' name='category_cm[!!lang!!]'  cols='40' rows='2' wrap='virtual'>!!commentaire!!</textarea>";
   
?>