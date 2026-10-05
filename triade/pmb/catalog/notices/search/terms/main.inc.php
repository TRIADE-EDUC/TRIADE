<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.24 2019/06/07 08:05:39 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id_thes, $search_term, $recalled, $page_search, $term_click, $class_path, $search_form_term, $current_module, $msg, $charset, $lien_thesaurus;
global $thesaurus_mode_pmb, $base_path;

if(!isset($id_thes)) $id_thes = 0;
if(!isset($search_term)) $search_term = '';
if(!isset($recalled)) $recalled = '';
if(!isset($page_search)) $page_search = '';
if(!isset($term_click)) $term_click = '';

// page de switch recherche sujets

// inclusions principales
require_once("$class_path/thesaurus.class.php");


//recuperation du thesaurus session 
if(!$id_thes) {
$id_thes = thesaurus::getSessionThesaurusId();
} else {
	thesaurus::setSessionThesaurusId($id_thes);
}


$search_form_term = "
<form class='form-$current_module' name='term_search_form' method='post' action='./catalog.php?categ=search&mode=5'>
<h3>".$msg["search_by_terms"]."</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne'>
				<!-- sel_thesaurus -->		
				<input type='text' class='saisie-50em' name='search_term' value='".htmlentities(stripslashes($search_term),ENT_QUOTES,$charset)."' />
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
$search_form_term=str_replace("<!-- sel_thesaurus -->",thesaurus::getSelector($id_thes, './catalog.php?categ=search&mode=5'),$search_form_term);
$search_form_term=str_replace("<!-- lien_thesaurus -->",$lien_thesaurus,$search_form_term);


//affichage du choix de langue pour la recherche
//$sel_langue = '';
//$sel_langue = "<div class='row'>";
//$sel_langue.= "<input type='checkbox' name='lg_search' id='lg_search' value='1' />&nbsp;".htmlentities(addslashes($msg['thes_sel_langue']),ENT_QUOTES,$charset);
//$sel_langue.= "</div><br />";
//$search_form_term=str_replace("<!-- sel_langue -->",$sel_langue,$search_form_term);


echo $search_form_term;

if (!isset($_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["SEARCH_TYPE"])) {
    $_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["SEARCH_TYPE"] = "";
}

//Nouvelle recherche
if (($search_term)&&(!$recalled)) {
	$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]="./catalog.php?categ=search&mode=5&id_thes=".$id_thes;
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["SEARCH_TYPE"]="term_search";
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]=$_POST;
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]["recalled"]=1;
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["GET"]=$_GET;
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]="<b>".$msg["histo_term"]."</b> ".htmlentities(stripslashes($search_term),ENT_QUOTES,$charset);
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY_START"]="<b>".$msg["histo_term"]."</b> ".htmlentities(stripslashes($search_term),ENT_QUOTES,$charset);
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$msg["search_by_terms"];
} else if ((!$search_term) && (!$recalled) && (isset($_SESSION["session_history"])) && ($_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["SEARCH_TYPE"]=="term_search") && ($_SESSION["CURRENT"]!==false)) {  
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["SEARCH_TYPE"]="";
} else if (($recalled)&&($_SESSION["CURRENT"]!==false)) {
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["SEARCH_TYPE"]="term_search";
}
echo "
<a name='search_frame'/>
<div class='row'>
	<iframe name='term_search' src='".$base_path."/catalog/notices/search/terms/term_browse.php?search_term=".rawurlencode(stripslashes($search_term))."&page_search=$page_search&term_click=".rawurlencode(stripslashes($term_click))."&id_thes=".$id_thes."'width=100% height=600></iframe>
</div>";
