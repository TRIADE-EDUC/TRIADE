<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autorites.tpl.php,v 1.54 2019-05-27 16:04:40 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $autorites_menu, $msg, $pmb_use_uniform_title, $thesaurus_concepts_active, $plugins, $autorites_menu_panier_gestion, $autorites_menu_panier_collecte, $autorites_menu_panier_pointage;
global $autorites_menu_panier_action, $autorites_layout, $current_module, $autorites_layout_end, $user_query, $categ, $autorites_forcing_form, $autorites_unlocking_request;

// $autorites_menu : menu page autorités
$autorites_menu = "
<div id='menu'>
<h3 onclick='menuHide(this,event)'>".$msg['search']."</h3>
<ul>
	<li><a href='./autorites.php?categ=search'>".$msg["search_authorities"]."</a></li>
	<li><a href='./autorites.php?categ=search_perso'>".$msg["search_perso_menu"]."</a></li>
</ul>
<h3 onclick='menuHide(this,event)'>$msg[132]</h3>
<ul>
	<li><a href='./autorites.php?categ=auteurs&sub=&id='>$msg[133]</a></li>";
if (SESSrights & THESAURUS_AUTH) {
	$autorites_menu .= "<li><a href='./autorites.php?categ=categories&sub=&parent=0&id=0'>$msg[134]</a></li>";
}
$autorites_menu .= "<li><a href='./autorites.php?categ=editeurs&sub=&id='>".$msg[135]."</a></li>
	<li><a href='./autorites.php?categ=collections&sub=&id='>".$msg[136]."</a></li>
	<li><a href='./autorites.php?categ=souscollections&sub=&id='>".$msg[137]."</a></li>
	<li><a href='./autorites.php?categ=series&sub=&id='>".$msg[333]."</a></li>";
if ($pmb_use_uniform_title) {
	$autorites_menu .= "<li><a href='./autorites.php?categ=titres_uniformes&sub=&id='>".$msg['aut_menu_titre_uniforme']."</a></li>";
}
$autorites_menu .= "<li><a href='./autorites.php?categ=indexint&sub=&id='>".$msg['indexint_menu']."</a></li>";

if ($thesaurus_concepts_active==true && (SESSrights & CONCEPTS_AUTH)) {
	$autorites_menu .= "
	<li><a href='./autorites.php?categ=concepts&sub=&id='>".$msg['ontology_skos_menu']."</a></li>";
}
$autorites_menu .= "
	!!authpersos!!
</ul>
<h3 onclick='menuHide(this,event)'>".$msg['caddie_menu']."</h3>
<ul>
	<li><a href='./autorites.php?categ=caddie'>".$msg['caddie_menu_gestion']."</a></li>
	<li><a href='./autorites.php?categ=caddie&sub=collecte'>".$msg['caddie_menu_collecte']."</a></li>
	<li><a href='./autorites.php?categ=caddie&sub=pointage'>".$msg['caddie_menu_pointage']."</a></li>
	<li><a href='./autorites.php?categ=caddie&sub=action'>".$msg['caddie_menu_action']."</a></li>
</ul>";
if (SESSrights & THESAURUS_AUTH) {
	$autorites_menu .= "
<h3 onclick='menuHide(this,event)'>".$msg['semantique']."</h3>
<ul>
	<li><a title='".$msg['word_syn_menu']."' href='./autorites.php?categ=semantique&sub=synonyms'>".$msg['word_syn_menu']."</a></li>
	<li><a title='".$msg['empty_words_libelle']."' href='./autorites.php?categ=semantique&sub=empty_words'>".$msg['empty_words_libelle']."</a></li>
</ul>";
}
$autorites_menu .= "
<h3 onclick='menuHide(this,event)'>".$msg['authorities_gest']."</h3>
<ul>
	<li><a title='".$msg['authorities_import']."' href='./autorites.php?categ=import&sub='>".$msg['authorities_import']."</a></li>
</ul>";
$plugins = plugins::get_instance();
$autorites_menu .= $plugins->get_menu('autorites')."</div>
";

// ---------------------------------------------------------------------------
//		Menus horizontaux : sous-onglets
// ---------------------------------------------------------------------------
// $autorites_menu_panier_gestion : menu gestion des paniers en autorites
$autorites_menu_panier_gestion = "
<h1>$msg[caddie_menu] <span> $msg[caddie_menu_gestion] > <!--!!sous_menu_choisi!! --></span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=panier").">
		<a title='$msg[caddie_menu_gestion_panier]' href='./autorites.php?categ=caddie&sub=gestion&quoi=panier'>
			$msg[caddie_menu_gestion_panier]
		</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=procs").">
		<a title='$msg[caddie_menu_gestion_procs]' href='./autorites.php?categ=caddie&sub=gestion&quoi=procs'>
			$msg[caddie_menu_gestion_procs]
		</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=classementGen").">
		<a title='$msg[classementGen_list_libelle]' href='./autorites.php?categ=caddie&sub=gestion&quoi=classementGen'>
			$msg[classementGen_list_libelle]
		</a>
	</span>
</div>
";

// $autorites_menu_panier_collecte : menu collecte des contenus de paniers
$autorites_menu_panier_collecte = "
<h1>$msg[caddie_menu] <span>> $msg[caddie_menu_collecte] > <!--!!sous_menu_choisi!! --></span></h1>
<div class='hmenu'>
	<span".ongletSelect("caddie&sub=collecte&moyen=selection").">
		<a title='$msg[caddie_menu_collecte_selection]' href='./autorites.php?categ=caddie&sub=collecte&moyen=selection'>
			$msg[caddie_menu_collecte_selection]
		</a>
	</span>
</div>
";

// $autorites_menu_panier_pointage : menu pointage des contenus de paniers
$autorites_menu_panier_pointage = "
<h1>$msg[caddie_menu] <span>> $msg[caddie_menu_pointage] > <!--!!sous_menu_choisi!! --></span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=caddie&sub=pointage&moyen=selection").">
		<a title='$msg[caddie_menu_pointage_selection]' href='./autorites.php?categ=caddie&sub=pointage&moyen=selection'>
			$msg[caddie_menu_pointage_selection]
		</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=pointage&moyen=panier").">
		<a title='$msg[caddie_menu_pointage_panier]' href='./autorites.php?categ=caddie&sub=pointage&moyen=panier'>
			$msg[caddie_menu_pointage_panier]
		</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=pointage&moyen=raz").">
		<a title='$msg[caddie_menu_pointage_raz]' href='./autorites.php?categ=caddie&sub=pointage&moyen=raz'>
			$msg[caddie_menu_pointage_raz]
		</a>
	</span>
</div>
";

// $autorites_menu_panier_action : menu action des contenus de paniers
$autorites_menu_panier_action = "
<h1>$msg[caddie_menu] <span>> $msg[caddie_menu_action] > <!--!!sous_menu_choisi!! --></span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=caddie&sub=action&quelle=supprpanier").">
		<a title='$msg[caddie_menu_action_suppr_panier]' href='./autorites.php?categ=caddie&sub=action&quelle=supprpanier'>
			$msg[caddie_menu_action_suppr_panier]
		</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=action&quelle=edition").">
		<a title='$msg[caddie_menu_action_edition]' href='./autorites.php?categ=caddie&sub=action&quelle=edition'>
			$msg[caddie_menu_action_edition]
		</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=action&quelle=selection").">
		<a title=\"".$msg['caddie_menu_action_selection']."\" href='./autorites.php?categ=caddie&sub=action&quelle=selection'>
			$msg[caddie_menu_action_selection]
		</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=action&quelle=supprbase").">
		<a title='$msg[caddie_menu_action_suppr_base]' href='./autorites.php?categ=caddie&sub=action&quelle=supprbase'>
			$msg[caddie_menu_action_suppr_base]
		</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=action&quelle=reindex").">
		<a title='$msg[caddie_menu_action_reindex]' href='./autorites.php?categ=caddie&sub=action&quelle=reindex'>
			$msg[caddie_menu_action_reindex]
		</a>
	</span>
</div>
";
		
//	----------------------------------

// $autorites_layout : layout page autorités
$autorites_layout = "
<div id='conteneur' class='$current_module'>
$autorites_menu
<div id='contenu'>
<!--<h1>$msg[132]</h1>-->
<!--!!menu_contextuel!! -->
";


// $autorites_layout_end : layout page circulation (fin)
$autorites_layout_end = '
</div>
</div>
';

// $user_query : form de recherche
$user_query = "
<script type='text/javascript'>
<!--
	function test_form(form)
	{
		if(form.user_input.value.length == 0)
			{
				alert(\"$msg[141]\");
				return false;
			}
		return true;
	}
-->
</script>
<form class='form-$current_module' name='search' method='post' action='!!action!!'>
<h3>!!user_query_title!!</h3>
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne'>
			<!-- sel_pclassement -->
			<!-- sel_thesaurus -->
			<!-- sel_autorites -->
			<!-- sel_authority_statuts -->
			<input type='text' class='saisie-50em' name='user_input' value='!!user_input!!'/>
		</div>
		<div class='right'></div>
		<div class='row'></div>
	</div>
</div>
<!-- sel_langue -->
";

if ($categ=="indexint") $user_query.="
	<div class='row'>
		<input type='radio' name='exact' id='exact1' value='1' !!checked_index!!/>
		<label class='etiquette' for='exact1'>&nbsp;".$msg["indexint_search_index"]."</label>&nbsp;
		<input type='radio' name='exact' id='exact0' value='0' !!checked_comment!!/>
		<label for='exact0' class='etiquette'>&nbsp;".$msg["indexint_search_comment"]."</label>
	</div>";
$user_query.="	
<div class='row'>
	<div class='left'>
		<input type='submit' class='bouton' value='$msg[142]' onClick=\"return test_form(this.form)\" />
		<input class='bouton' type='button' value='!!add_auth_msg!!' onClick=\"document.location='!!add_auth_act!!'\" />
	</div>
	<div class='right'>
		<!-- lien_classement --><!-- lien_derniers --><!-- lien_thesaurus --><!-- imprimer_thesaurus -->
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['search'].elements['user_input'].focus();
</script>
<div class='row'></div>
";


$autorites_forcing_form = "
<form class='form-$current_module' name='search' method='post' action='!!action!!'>
<h3>".$msg['entity_currently_locked']."</h3>
<div class='form-contenu'>
	<div class='row'>
		<p>!!entity_is_locked_by!!</p>
        <p>!!entity_force_edition!!</p>
	</div>
</div>
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value='".$msg['654']."'  />
	</div>
	<div class='right'>
		<input type='submit' class='bouton' value='".$msg['142']."' />
	</div>
<div class='row'></div>
</form>
";

$autorites_unlocking_request= "
<script type='text/javascript'>
    require(['dojo/ready', 'dojo/xhr', 'dojo/on'], function(ready){
        ready(function(){
            var oldBefore = window.onbeforeunload;
            window.addEventListener('beforeunload', function(e){
                /* DO SOME MAGIC MY LITTLE FELLOW */
                oldBefore(e);
            });
        });
    });
</script>
";


?>