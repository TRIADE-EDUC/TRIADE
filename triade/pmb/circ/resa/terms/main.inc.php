<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.15 2017/10/18 14:55:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch recherche sujets

// inclusions principales
require_once("$class_path/thesaurus.class.php");


//recuperation du thesaurus session 
if(!isset($id_thes) || !$id_thes) {
	$id_thes = thesaurus::getSessionThesaurusId();
}

$search_form_term = "
<form class='form-$current_module' name='term_search_form' method='post' action='./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&mode=1&unq=$unq&mode=5#search_frame'>
<h3>".$msg["search_by_terms"]."</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne'>
				<!-- sel_thesaurus -->		
				<input type='text' class='saisie-50em' name='search_term' value='".(isset($search_term) ? htmlentities(stripslashes($search_term),ENT_QUOTES,$charset) : '')."'>
			</div>
		</div>
		<div class='row'>
			<span class='saisie-contenu'>
				$msg[155]&nbsp;<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
				</span>
			</div>
		</div>
	<!--	Bouton Rechercher -->
	<div class='row'>
		<input type='submit' class='bouton' value='$msg[142]' />
		</div>
	</form>
	<script type='text/javascript'>
		document.forms['term_search_form'].elements['search_term'].focus();
		</script>
	<br />
	";
	
	
//affichage du selectionneur de thesaurus et du lien vers les thésaurus
$lien_thesaurus = '';

if ($thesaurus_mode_pmb != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
	$lien_thesaurus = "<a href='./autorites.php?categ=categories&sub=thes'>".$msg['thes_lien']."</a>";

}	
$search_form_term=str_replace("<!-- sel_thesaurus -->",thesaurus::getSelector($id_thes, './circ.php?categ=resa&mode=5&id_empr='.$id_empr.'&groupID='.$groupID.'&unq='.$unq),$search_form_term);
$search_form_term=str_replace("<!-- lien_thesaurus -->",$lien_thesaurus,$search_form_term);


//affichage du choix de langue pour la recherche
//$sel_langue = '';
//$sel_langue = "<div class='row'>";
//$sel_langue.= "<input type='checkbox' name='lg_search' id='lg_search' value='1' />&nbsp;".htmlentities($msg['thes_sel_langue'],ENT_QUOTES,$charset);
//$sel_langue.= "</div><br />";
//$search_form_term=str_replace("<!-- sel_langue -->",$sel_langue,$search_form_term);


echo $search_form_term;


echo "
<a name='search_frame'/>
<div class='row'>
	<iframe name='term_search' src='".$base_path."/circ/resa/terms/term_browse.php?id_empr=$id_empr&groupID=$groupID&mode=1&unq=$unq&search_term=".(isset($search_term) ? rawurlencode(stripslashes($search_term)) : '')."&id_thes=".$id_thes."' width=100% height=600>
</div>";
