<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concepts.tpl.php,v 1.3 2019/05/27 14:50:51 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $aut_concepts_menu, $msg;

// Menu concepts
$aut_concepts_menu = "
<h1>".$msg['ontology_skos_menu'] ."<span> &gt; !!menu_sous_rub!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=concepts&sub=conceptscheme").">
		<a title='".$msg['ontology_skos_conceptscheme']."' href='./autorites.php?categ=concepts&sub=conceptscheme&action='>".
		$msg['ontology_skos_conceptscheme']."
		</a>
	</span>
	<span".ongletSelect("categ=concepts&sub=concept").">
		<a title='".$msg['ontology_skos_concept']."' href='./autorites.php?categ=concepts&sub=concept&action='>".
		$msg['ontology_skos_concept']."
		</a>
	</span>
		<span".ongletSelect("categ=concepts&sub=collection").">
		<a title='".$msg['ontology_skos_collection']."' href='./autorites.php?categ=concepts&sub=collection&action='>".
		$msg['ontology_skos_collection']."
		</a>
	</span>
		<span".ongletSelect("categ=concepts&sub=orderedcollection").">
		<a title='".$msg['ontology_skos_orderedcollection']."' href='./autorites.php?categ=concepts&sub=orderedcollection&action='>".
		$msg['ontology_skos_orderedcollection']."
		</a>
	</span>
</div>
";

// $aut_concepts_list="

// ";


// // $schemes_search_form : template de recherche des concepts
// $concept_search_form = "
// <form class='form-".$current_module."' id='ontology_search_form' name='ontology_search_form' method='post' action='!!action_url!!'>
// 	<h3>".$msg['aut_schemes_search_form_title']."</h3>
// 	<div class='form-contenu'>

// 		<!-- zone de recherche -->
// 		<div class='row'>
// 			<input type='text' class='saisie-80em' id='schemes_search_input' name='schemes_search_input' value=\"!!schemes_search_input!!\" />
// 		</div>

// 		<!-- selecteur schema -->
// 		!!scheme_sel!!
		
// 		<!-- selecteur collection -->
// 		!!collection_sel!!
		
// 		<!-- selecteur langue -->
// 		!!lang_sel!! 
		
// 		<!-- selecteur affichage -->
// 		!!view_sel!!
		
// 	</div>


// 	<!--	boutons	-->
// 	<div class='row'>
// 		<div class='left'>
// 			<input type='submit' class='bouton' value='".$msg[142]."' onClick=\"return test_form(this.form)\" />
// 		</div>
// 	</div>
// 	<div class='row'>
// 	</div>	

// </form>

// <script type='text/javascript'>
// 	document.forms['schemes_search_form'].elements['schemes_search_input'].focus();
// </script>
// ";
