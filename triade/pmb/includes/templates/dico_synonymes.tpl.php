<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dico_synonymes.tpl.php,v 1.8 2019-05-27 14:55:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $select_prop, $aff_liste_mots, $msg, $current_module, $aff_modif_mot, $mot_js, $aff_mot_lie;

// template pour le form de dictionnaire des synonymes

$select_prop = "scrollbars=yes, toolbar=no, dependent=yes, resizable=yes";

$aff_liste_mots="
<script type='text/javascript'>
<!--
	function test_form(form)
	{
		if(form.word_search.value.length == 0)
			{
				alert('Le champ de recherche est vide.');
				return false;
			} else return true;
	}
-->
</script>
<h1>".$msg["semantique"]." : ".$msg["dico_syn"]."</h1>
<div class='row'>
	<form class='form-$current_module' name='search_mots' method='post' action='./autorites.php?categ=semantique&sub=synonyms&action=search' onSubmit='if (test_form(search_mots)) return true; else return false;'>
	<h3>".$msg["357"]." : ".$msg["dico_syn"]."</h3>\n
	<div class='form-contenu'>
		<input type='text' class='saisie-30em' name='word_search' value=''>
	</div>
	<div class='row'>
	<div class='left'><input type='submit' class='bouton' value='".$msg["142"]."'>\n
	&nbsp;<input type='button' class='bouton' value='".$msg["word_create"]."' onclick=\"document.location='./autorites.php?categ=semantique&sub=synonyms&action=view';\"></div>\n
	!!see_last_words!!
	</div>
	<div class='row'></div>	
	</form>
	</div>\n
<div class='row'>&nbsp;</div>\n
<div class='row'><h3>".$msg["search_words_libelle"]." !!cle!!</h3></div>\n
!!lettres!!\n
<div class='row'>&nbsp;</div>\n
!!liste_mots!!\n
<script> document.search_mots.word_search.focus(); </script>
";

//template pour le form ajout/modification d'un mot
$aff_modif_mot="
<script src='javascript/ajax.js'></script>
!!mots_js!!\n
<h1>".$msg["semantique"]." : ".$msg["dico_syn"]."</h1>
<div class='row'>&nbsp;</div>
<form class='form-$current_module' id='words' name='words' method='post' action='!!action!!&action=modif'>\n
<h3><div class='left'>".$msg["syn_word"]."</div></h3><div class='row'></div><hr class='spacer' />\n
<div class='form-contenu'>
".$msg["word_selected"]." : <input type='text' class='saisie-20em' name='word_selected' value=\"!!mot_original!!\">\n
<input type='hidden' name='word_code_selected' value='!!id_mot!!'>
<input type='hidden' id='max_word' name='max_word' value=\"!!max_word!!\" />
<div class='row'>&nbsp;</div>
<b>".$msg["word_syn"]." :</b><div class='row'>&nbsp;</div>
!!mots_lie!!
<div id='addword'/>
</div>\n
</div><div class='row'><hr class='spacer' />
<div class='left'><input type='button' class='bouton' value='".$msg["76"]."' onClick=\"history.back(-1);\">&nbsp;<input type='button' class='bouton' value='".$msg["77"]."' onClick='document.words.submit();'></div>
!!supprimer!!
</div><div class='row'></div></form>\n
<script> 
ajax_pack_element(document.words.f_word0);
document.words.word_selected.focus(); 
</script>";

//fonctions ajax ajout de zones de texte
$mot_js="
<script>
	function fonction_selecteur_word() {
        name=this.getAttribute('id').substring(4);
		name_id = name.substr(0,6)+'_code'+name.substr(6);
        openPopUp('./select.php?what=synonyms&caller=words&p1='+name_id+'&p2='+name, 'selector');
    }
    function fonction_raz_word() {
        name=this.getAttribute('id').substring(4);
		name_id = name.substr(0,6)+'_code'+name.substr(6);
        document.getElementById(name).value='';
		document.getElementById(name_id).value='';
    }
    function add_word() {
    	templates.add_completion_selection_field('f_word', 'f_word_code', 'synonyms', fonction_selecteur_word);
    }
</script>";

//template de zone de texte pour chaque mot lié				
$aff_mot_lie="
<div class='row'>
<input type='text' class='saisie-30emr' id='f_word!!iword!!' name='f_word!!iword!!' value=\"!!word!!\" autfield='f_word_code!!iword!!' completion=\"synonyms\" />
<input type='hidden' id='f_word_code!!iword!!' name='f_word_code!!iword!!' value='!!id_word!!'>
<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=synonyms&caller=words&p1=f_word_code!!iword!!&p2=f_word!!iword!!&deb_rech='+".pmb_escape()."(this.form.f_word!!iword!!.value), 'selector')\" />
<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_word!!iword!!.value='';this.form.f_word_code!!iword!!.value=''; \" />
!!bouton_ajouter!!
</div>\n";
?>
