<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_templates.tpl.php,v 1.48 2020/02/05 08:09:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $include_path, $search_ed, $search_indexint, $search_tu, $search_authperso, $search_subject, $search_indexint_id, $thesaurus_concepts_active, $pmb_rfid_activate;
global $pmb_show_notice_id, $param_rfid_activate, $pmb_rfid_serveur_url, $rfid_tpl, $rfid_js_header, $NOTICE_author_query, $msg, $current_module, $search_form_categ, $charset;
global $browser, $search_form_editeur, $browser_editeur, $search_form_titre_uniforme, $browser_titre_uniforme, $search_form_authperso, $browser_authperso, $search_form_map;

require_once($include_path."/rfid_config.inc.php");

if(!isset($search_ed)) $search_ed = '';
if(!isset($search_indexint)) $search_indexint = '';
if(!isset($search_tu)) $search_tu = '';
if(!isset($search_authperso)) $search_authperso = '';
if(!isset($search_subject)) $search_subject = '';
if(!isset($search_indexint_id)) $search_indexint_id = 0;
if(!isset($thesaurus_concepts_active)) $thesaurus_concepts_active = 0;
if(!isset($pmb_rfid_activate)) $pmb_rfid_activate = 0;
if(!isset($pmb_show_notice_id)) $pmb_show_notice_id = 0;
if(!isset($param_rfid_activate)) $param_rfid_activate = '';

// page de switch recherche notice
//	Auteur/Titre

if ($param_rfid_activate && $pmb_rfid_activate && $pmb_rfid_serveur_url ) {	
	get_rfid_js_header();
	$rfid_tpl = "
		$rfid_js_header
		<script type='text/javascript'>
			var cb_lu =new Array();
			setTimeout(\"init_rfid_read_cb(0,f_expl);\",0);
		
			function f_expl(cb) {
				// il y a une ou plusieurs étiquette rfid
				if( cb.length>0) {			
					document.getElementById('ex_query').value=cb[0];
					document.NOTICE_author_query.submit();
				}
			}
		
		</script>
	";
}else $rfid_tpl="";
$NOTICE_author_query = "
$rfid_tpl
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	function test_form(form) {
		if ((form.ex_query.value.length == 0) && (form.title_query.value.length == 0) && (form.author_query.value.length == 0) && (form.categ_query.value.length == 0) && (form.all_query.value.length == 0) && ((form.concept_query && form.concept_query.value.length == 0) || (!form.concept_query))) {
			form.all_query.value='*';
			return true;
		}
		if ((form.ex_query.value.length != 0) && ((form.title_query.value.length != 0) || (form.author_query.value.length != 0)  || (form.categ_query.value.length != 0) || (form.all_query.value.length != 0) || (form.concept_query && form.concept_query.value.length != 0))) {
			if (confirm('$msg[1917]')) {
	            form.title_query.value = '';
	            form.all_query.value = '';
	            form.author_query.value = '';
	            form.categ_query.value = '';
				return true;
			} else {
	        	return false;
	       	}
		}
		return true;
	}
	function switch_input(field_label) {
		document.getElementById(field_label).setAttribute('class','saisie-80emr');	
	}
	function reset_input(field_label) {
		if(document.getElementById(field_label+'_id').value){
			document.getElementById(field_label+'_id').value = 0;
			document.getElementById(field_label).setAttribute('class','saisie-80em');
		}		
	}
</script>
<form class='form-$current_module' id='NOTICE_author_query' name='NOTICE_author_query' method='post' action='!!base_url!!' onSubmit='return test_form(this)'>
<h3>$msg[354]</h3>
<div class='form-contenu'>
<div class='row'>
	<label class='etiquette' for='all_query'>".$msg['global_search']."</label>
	</div>
	<div class='colonne'>
		<div class='row'>
			<input class='saisie-80em' type='text' value='!!all_query!!' name='all_query' id='all_query' />
		</div>
	</div>
	!!docnum_query!!
<div class='row'>
	<label class='etiquette' for='title_query'>$msg[233]</label>
</div>
<div class='row'>
	<input class='saisie-80em' type='text' value='!!title_query!!' name='title_query' id='title_query' />
</div>

<div class='row'>
	<label class='etiquette' for='author_query'>$msg[234]</label>
</div>
<div class='colonne'>
	<input class='saisie-80em' id='author_query' type='text' value='!!author_query!!' size='36' name='author_query' autfield='author_query_id' completion='authors' autocomplete='off' onkeyup='reset_input(this.id);' callback='switch_input' />
	<input type='hidden' id='author_query_id' name='author_query_id' value='!!author_query_id!!' />
</div>

<div class='row'>
	<label class='etiquette' for='categ_query'>".$msg["search_categorie_title"]."</label>
</div>
<div class='colonne'>
	<div class='row'>
		<input class='saisie-80em' id='categ_query' type='text' value='!!categ_query!!' size='36' name='categ_query' autfield='categ_query_id' completion='categories_mul' autocomplete='off' onkeyup='reset_input(this.id);' callback='switch_input' />
		<input type='hidden' id='categ_query_id' name='categ_query_id' value='!!categ_query_id!!' />
	</div> 
</div>
!!auto_postage!!
";
if($thesaurus_concepts_active){
	$NOTICE_author_query.= "
	<div class='row'>
		<label class='etiquette' for='concept_query'>".$msg["search_concept_title"]."</label>
	</div>
	<div class='colonne'>
		<div class='row'>
			<input class='saisie-80em' id='concept_query' type='text' param1='-1' value='!!concept_query!!' size='36' name='concept_query' autfield='concept_query_id' completion='onto' autocomplete='off' att_id_filter='http://www.w3.org/2004/02/skos/core#Concept' onkeyup='reset_input(this.id);' callback='switch_input' />
			<input type='hidden' id='concept_query_id' name='concept_query_id' value='!!concept_query_id!!' />
		</div> 
	</div>
	!!concepts_autopostage!!
	";
}
$NOTICE_author_query.= "
<div class='row'>
	<span class='saisie-contenu'>
		$msg[155]&nbsp;<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
	</span>
</div>
<div class='colonne2'>
	<div class='row'>
		<label for='typdoc-query'>$msg[17]$msg[1901]</label>
	</div>
	<select id='typdoc-query' name='typdoc_query[]' multiple>
		!!typdocfield!!
	</select>
</div>
<div class='colonne_suite'>
	<div class='row'>
		<label for='statut-query'>$msg[noti_statut_noti]</label>
	</div>
	<select id='statut-query' name='statut_query[]' multiple>
		!!statutfield!!
	</select>
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='row'>
		<label>".$msg['search_date_parution']."</label> 					
	</div>
	<div class='row'>
		
		<input type='radio' name='date_parution_exact_query' id='date_parution_exact_query' value='1' !!date_parution_exact_checked!!
				onclick=\"document.getElementById('date_parution_end_query').disabled = this.checked;\"/>		
		<label for='date_parution_exact_query'>".$msg['search_exact_date']."</label>		
		<input type='radio' name='date_parution_exact_query' id='date_parution_no_exact_query' value='0' !!date_parution_no_exact_checked!!
				onclick=\"document.getElementById('date_parution_end_query').disabled = false;\"/>			
		<label for='date_parution_no_exact_query'>".$msg['search_date_parution_start']."</label>
		<input type='text' name='date_parution_start_query' id='date_parution_start_query' value='!!date_parution_start!!' 
			title='".$msg['format_date_input_text_placeholder']."' alt='".$msg['format_date_input_text_placeholder']."' placeholder='".$msg['format_date_input_text_placeholder']."'/>	
		<label>".$msg['search_date_parution_end']."</label>
		<input type='text' name='date_parution_end_query' id='date_parution_end_query' value='!!date_parution_end!!' !!date_parution_end_disabled!!
			title='".$msg['format_date_input_text_placeholder']."' alt='".$msg['format_date_input_text_placeholder']."' placeholder='".$msg['format_date_input_text_placeholder']."'/>	
		
	</div>
</div>
<div class='row'>&nbsp;</div>
<div class='colonne2'>
	<div class='row'>
		<label class='etiquette' for='ex_query'>$msg[940]</label>
	</div>
	<div class='row'>
		<input class='saisie-80em' type='text' name='ex_query' id='ex_query' value='!!ex_query!!'/>
	</div>
</div>";
if($pmb_show_notice_id) {
	$NOTICE_author_query .= "
		<div class='colonne_suite'>
			<div class='row'>
				<label class='etiquette' for='f_notice_id'>".$msg['notice_id_libelle']."</label>
			</div>
			<div class='row'>
				<input class='saisie-30em' type='text' name='f_notice_id' id='f_notice_id' />
			</div>
		</div>";
}
$NOTICE_author_query .= "
		<div class='row'>&nbsp;</div>
	</div>";
$NOTICE_author_query .= "
<!--	Bouton Rechercher	-->
<div class='row'>
	<input type='submit' class='bouton' value='$msg[142]' />
	</div>
<input type='hidden' name='etat' value='first_search'/>
</form>

<script type='text/javascript'>
      document.forms['NOTICE_author_query'].elements['all_query'].focus();
      ajax_parse_dom();
</script>
";

//Index/Sujet
$search_form_categ = "
<form class='form-$current_module' name='subject_search_form' method='post' action='!!base_url!!'>
<h3>$msg[355]</h3>
	<div class='form-contenu'>

		<div class='row'>
			<div class='colonne'>
				<!-- sel_thesaurus -->		
				<input type='text' class='saisie-50em' name='search_subject' value='".htmlentities(stripslashes($search_subject),ENT_QUOTES,$charset)."' />
			</div>	
		</div>

		<div class='row'>
			<span class='saisie-contenu'>
				$msg[155]&nbsp;<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
			</span>
		</div>

		<!-- sel_langue -->

		<!--	Indexation interne	-->
		<div class='row'>
			<label for='f_indexint' class='etiquette'>".$msg['indexint_catal_title']."</label>
			</div>
		<div class='row'>
			<input type='text' class='saisie-50em' id='search_indexint' name='search_indexint' completion='indexint' autocomplete='off' autfield='search_indexint_id' value=\"".htmlentities(stripslashes($search_indexint),ENT_QUOTES,$charset)."\" size='54' onKeyUp=\"this.form.search_indexint_id.value='0';\"/>
			<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=indexint&caller=subject_search_form&param1=search_indexint_id&param2=search_indexint&parent=0&bt_ajouter=no', 'selector')\" />
			<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.search_indexint.value=''; this.form.search_indexint_id.value='0'; \" />
			<input type='hidden' id='search_indexint_id' name='search_indexint_id' value='".htmlentities(stripslashes($search_indexint_id),ENT_QUOTES,$charset)."' />
		</div>
	</div>

	<!--	Bouton Rechercher -->
	<div class='row'>
		<input type='submit' class='bouton' value='$msg[142]' />
		</div>
		<input type='hidden' name='etat' value='first_search'/>
	</form>
	<script type='text/javascript' src='".$base_path."/javascript/ajax.js'></script>
	<script type='text/javascript'>
		document.forms['subject_search_form'].elements['search_subject'].focus();
		ajax_parse_dom();
		</script>
	<br />";
	$browser="
	<div class='row'>
		<iframe name=\"collection_browser\" frameborder=\"0\" scrolling=\"yes\" width=\"100%\" height=\"300\" src=\"!!browser_url!!\">
	</div>";
	
	//Editeur collection
	$search_form_editeur = "
	<form class='form-$current_module' name='ed_search_form' method='post' action='!!base_url!!'>
	<h3>$msg[356]</h3>
	<div class='form-contenu'>
		<div class='row'>
			<input type='text' class='saisie-50em' name='search_ed' value='".htmlentities(stripslashes($search_ed),ENT_QUOTES,$charset)."'>
			</div>
		<div class='row'>
			<span class='saisie-contenu'>
				$msg[155]&nbsp;<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
				</span>
			</div>
		</div>
	<!--	Bouton Rechercher	-->
	<div class='row'>
		<input type='submit' class='bouton' value='$msg[142]' />
		</div>
		<input type='hidden' name='etat' value='first_search'/>
	</form>
	<script type='text/javascript'>
		document.forms['ed_search_form'].elements['search_ed'].focus()
		</script>
	<br />";
	$browser_editeur="<iframe name=\"collection_browser\" frameborder=\"0\" scrolling=\"yes\" width=\"100%\" height=\"300\" src=\"!!browser_url!!\">
	";
	
	//titre uniforme
	$search_form_titre_uniforme = "
	<form class='form-$current_module' name='tu_search_form' method='post' action='!!base_url!!'>
	<h3>".$msg["search_by_titre_uniforme"]."</h3>
	<div class='form-contenu'>
		<div class='row'>
			<input type='text' class='saisie-50em' name='search_tu' value='".htmlentities(stripslashes($search_tu),ENT_QUOTES,$charset)."'>
			</div>
		<div class='row'>
			<span class='saisie-contenu'>
				$msg[155]&nbsp;<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
				</span>
			</div>
		</div>
	<!--	Bouton Rechercher	-->
	<div class='row'>
		<input type='submit' class='bouton' value='$msg[142]' />
		</div>
		<input type='hidden' name='etat' value='first_search'/>
	</form>
	<script type='text/javascript'>
		document.forms['tu_search_form'].elements['search_tu'].focus()
		</script>
	<br />";
	$browser_titre_uniforme="<iframe name=\"titre_uniforme_browser\" frameborder=\"0\" scrolling=\"yes\" width=\"100%\" height=\"300\" src=\"!!browser_url!!\">";
	
	//Authperso
	$search_form_authperso = "
	<form class='form-$current_module' name='authperso_search_form' method='post' action='!!base_url!!'>
	<h3>!!authperso_search_title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<input type='text' class='saisie-50em' name='search_authperso' value='".htmlentities(stripslashes($search_authperso),ENT_QUOTES,$charset)."'>
			</div>
		<div class='row'>
			<span class='saisie-contenu'>
				$msg[155]&nbsp;<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
				</span>
			</div>
		</div>
	<!--	Bouton Rechercher	-->
	<div class='row'>
		<input type='submit' class='bouton' value='$msg[142]' />
		</div>
		<input type='hidden' name='etat' value='first_search'/>
	</form>
	<script type='text/javascript'>
		document.forms['authperso_search_form'].elements['search_authperso'].focus()
		</script>
	<br />";
	$browser_authperso="<iframe name=\"authperso_browser\" frameborder=\"0\" scrolling=\"yes\" width=\"100%\" height=\"300\" src=\"!!browser_url!!\">";
	
	//Géolocalisation
	$search_form_map = "
	<script src='javascript/ajax.js'></script>
	<script type='text/javascript'>
	function test_form(form) {
	
		if ((form.categ_query.value.length == 0) && (form.all_query.value.length == 0) && ((form.concept_query && form.concept_query.value.length == 0) || (!form.concept_query)) ) {
		//	form.all_query.value='*';
			return true;
		}
	}
	</script>
	<form class='form-$current_module' id='search_form_map' name='search_form_map' method='post' action='!!base_url!!' onSubmit='return test_form(this)'>
	<h3>".$msg["search_by_map"]."</h3>
	<div class='form-contenu'>
	
	<table class='map_search'><tr><td>
	
	<div class='row'>
		<label class='etiquette' for='all_query'>$msg[global_search]</label>
	</div>
	<div class='colonne'>
		<div class='row'>
			<input class='saisie-80em' type='text' value='!!all_query!!' name='all_query' id='all_query' />
		</div>
	</div>
	!!docnum_query!!
	
	<div class='row'>
		<label class='etiquette' for='categ_query'>".$msg["search_categorie_title"]."</label>
	</div>
	<div class='colonne'>
		<div class='row'>
			<input class='saisie-80em' id='categ_query' type='text' value='!!categ_query!!' size='36' name='categ_query' autfield='categ_query' completion='categories_mul' autocomplete='off' />
		</div>
	</div>
	!!auto_postage!!
	";
	if($thesaurus_concepts_active){
		$search_form_map .= "
		<div class='row'>
			<label class='etiquette' for='concept_query'>".$msg["search_concept_title"]."</label>
		</div>
		<div class='colonne'>
			<div class='row'>
				<input class='saisie-80em' id='concept_query' type='text' param1='-1' value='!!concept_query!!' size='36' name='concept_query' autfield='concept_query' completion='onto' autocomplete='off' att_id_filter='http://www.w3.org/2004/02/skos/core#Concept' />
			</div>
		</div>
		!!concepts_autopostage!!";
	}
	$search_form_map .= "
	<div class='row'>
		<label class='etiquette' for='map_echelle_query'>".$msg["map_echelle"]."</label>
	</div>
	<div class='row'>
		!!map_echelle_list!!
	</div>
	<div class='row'>
		<label class='etiquette' for='map_projection_query'>".$msg["map_projection"]."</label>
	</div>
	<div class='row'>
		!!map_projection_list!!
	</div>		
	<div class='row'>
		<label class='etiquette' for='map_ref_query'>".$msg["map_ref"]."</label>
	</div>
	<div class='row'>
		!!map_ref_list!!
	</div>				
	<div class='row'>
		<label class='etiquette' for='map_equinoxe_query'>".$msg["map_equinoxe"]."</label>
	</div>
	<div class='row'>
		<input id='map_equinoxe_query' class='saisie-80em' type='text' value='!!map_equinoxe_value!!' name='map_equinoxe_query'>
	</div>
	<div class='row'>
		<span class='saisie-contenu'>
			$msg[155]&nbsp;<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
		</span>
	</div>
	<div class='colonne2'>
		<div class='row'>
			<label for='typdoc-query'>$msg[17]$msg[1901]</label>
		</div>
		<select id='typdoc-query' name='typdoc_query[]' multiple>
		!!typdocfield!!
		</select>
	</div>
	<div class='colonne_suite'>
		<div class='row'>
			<label for='statut-query'>$msg[noti_statut_noti]</label>
		</div>
		<select id='statut-query' name='statut_query[]' multiple>
		!!statutfield!!
		</select>
	</div>
	</td>
	<td>
	<div class='row'>
		<label class='etiquette'>".$msg["map_search"]."</label>
	</div>
	<div class='row'>
		!!map!!
	</div>
	</td>
	</tr>
	</table>
	<div class='row'>&nbsp;</div>
	</div>
	<!--	Bouton Rechercher	-->
	<div class='row'>
		<input type='submit' class='bouton' value='$msg[142]' />
	</div>
	<input type='hidden' name='etat' value='first_search'/>
	</form>
	
	<script type='text/javascript'>
	document.forms['search_form_map'].elements['all_query'].focus();
	ajax_parse_dom();
	</script>
	";
?>