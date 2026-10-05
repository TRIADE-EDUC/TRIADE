<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: catalog.tpl.php,v 1.67 2023/12/08 08:48:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $categ, $mode, $sub, $quoi, $msg, $current_module;
global $biblio_query, $saisie_cb_form, $search_bar, $type, $external_type, $action;

// Valeurs pour l'affichage de la page par defaut 
// (selection de l'onglet)
// note : l'autre solution serait de faire un menu général (voir en admin) 
//plutôt que d'afficher un sous menu par défaut.
if(!$categ){
	$categ="search";
	$mode=0;
} elseif($categ=="caddie" and !$sub){
	//Paniers > Gestion : selection de "Gestion des paniers par defaut"
	$sub="gestion";
	$quoi="panier";	
}

// $biblio_query : form de recherche : semble ne plus être utilisé.....
$biblio_query = "
<script type='text/javascript'>
	function test_form(form)
	{
		if((form.ex_query.value.replace(/^\s+|\s+$/g, '').length == 0) && (form.ISBN_query.value.replace(/^\s+|\s+$/g, '').length == 0) && (form.title_query.value.replace(/^\s+|\s+$/g, '').length == 0) && (form.author_query.value.replace(/^\s+|\s+$/g, '').length == 0))
		{
			alert(\"{$msg[348]}\");
			return false;
		}

		return true;
	}
</script>
<h1>".$msg["235"]."</h1>
<form class='form-$current_module' id='biblio_query' name='biblio_query' method='post' action='./catalog.php?categ=search' onSubmit='return test_form(this)'>
<div class='form-contenu'>
<div class='row'>
	<label class='etiquette' for='ex_query'>".$msg["232"]."</label>
	</div>
<div class='row'>
	<input type='text' class='saisie-50em' name='ex_query' id='ex_query' />
	</div>
<div class='row'>
	<label class='etiquette' for='ISBN_query'>".$msg["231"]."</label>
	</div>
<div class='row'>
	<input type='text' class='saisie-50em' name='ISBN_query' id='ISBN_query' />
	</div>
<div class='row'>
	<label class='etiquette' for='title_query'>".$msg["233"]."</label>
	</div>
<div class='row'>
	<input type='text' class='saisie-50em' value='' size='36' name='title_query' id='title_query' />
	</div>
<div class='row'>
	<label class='etiquette' for='author_query'>".$msg["234"]."</label>
	</div>
<div class='row'>
	<input type='text' class='saisie-50em' value='' name='author_query' id='author_query' />
	</div>
<div class='row'>
	<span>".$msg["155"]." <a href='./help.php?whatis=regex' onclick='aide_regex();return false;'></a></span>
	</div>
</div>
<div class='row'>
	<input type='submit' class='bouton' value='".$msg["142"]."' />
	</div>
</form>
<script type='text/javascript'>	document.forms['biblio_query'].elements['ex_query'].focus();
</script>
";

//  $saisie_cb_form: form de saisie code barre
$saisie_cb_form = "
<h1>".$msg["270"]."</h1>
<form class='form-$current_module' id='saisie_cb' name='saisie_cb' method='post' action='./catalog.php?categ=create_form&id=0'>
<div class='form-contenu'>
<div class='row'>
	<label class='etiquette' for='saisieISBN'>".$msg["255"]."</label>
	</div>
<div class='row'>
	<input class='saisie-20em' type='text' id='saisieISBN' name='saisieISBN' value='' />
	</div>
</div>
<div class='row'>
	<input class='bouton' type='submit' value=' ".$msg["502"]." ' />
	</div>
</form>
<script type='text/javascript'>document.forms['saisie_cb'].elements['saisieISBN'].focus();</script>
";

//  $search_bar: code qui fait la barre de ranking en résultat recherche
// si vous changez la taille, il faut mettre à jour $lengtha et $lengthb
// dans classes/notice_display.class.php
$search_bar = "
<table style='border:0px; border-spacing: 0px' class=\"result-bar\" width=\"25\" role='presentation'>
  <tr>
    <td class=\"bar-left\"><img src=\"".get_url_icon('bar_spacer.gif')."\" width=\"!!la!!\" height=\"3\" alt=\"rank: !!indice!!%\"></td>
    <td class=\"bar-right\"><img src=\"".get_url_icon('bar_spacer.gif')."\" width=\"!!lb!!\" height=\"3\"></td>
  </tr>
</table>
";