<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authors.tpl.php,v 1.63.10.1 2025/02/28 10:39:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $author_content_form, $author_replace_content_form;
global $msg, $charset;
//	----------------------------------
// $author_content_form : form saisie auteur
// champs :
//	author_type : 70/71 (select)
//	author_nom element d'entrée
//	author_rejete element rejeté
//	date1 (text max:4) date 1
//	date2 (text max:4) date 2
//	voir_id (hidden) id de la forme retenue
//	voir_libelle

$author_content_form = "
!!element_author_type!!
<div id='el0Child_1' class='row'>
    !!element_author_nom!!
	!!element_author_rejete!!
</div>
!!element_date!!
!!element_lieu!!
<div id='el0Child_4' class='row'>
	!!element_ville!!
	!!element_pays!!
</div>
<div id='el0Child_5' class='row'>
	!!element_subdivision!!
	!!element_numero!!
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
!!element_isni!!
!!element_author_web!!
!!element_author_comment!!
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
";

// $author_replace_content_form : form remplacement auteur
$author_replace_content_form = "
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
";