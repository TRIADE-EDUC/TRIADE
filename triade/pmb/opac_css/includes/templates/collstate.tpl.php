<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collstate.tpl.php,v 1.12 2019-05-29 11:23:32 btafforeau Exp $

// templates pour gestion des autorités collections

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg;
global $collstate_list_header;
global $collstate_list_footer;
global $tpl_collstate_liste;
global $tpl_collstate_liste_line;
global $tpl_collstate_surloc_liste;
global $tpl_collstate_surloc_liste_line;
global $tpl_collstate_bulletins_list_th;
global $tpl_collstate_bulletins_list_td;
global $tpl_collstate_bulletins_list_page;
global $tpl_collstate_bulletins_list_page_collstate_line;

$collstate_list_header = "
<table class='exemplaires etatcoll' cellpadding='2' style='width:100%'>
	<tbody>
";

$collstate_list_footer ="
	</tbody>
</table>";

$tpl_collstate_liste[0]="
<table class='exemplaires etatcoll' cellpadding='2' style='width:100%'>
	<tbody>
		<tr>
			<!-- surloc -->
			<th class='collstate_header_emplacement_libelle'>".$msg["collstate_form_emplacement"]."</th>		
			<th class='collstate_header_cote'>".$msg["collstate_form_cote"]."</th>
			<th class='collstate_header_type_libelle'>".$msg["collstate_form_support"]."</th>
			<th class='collstate_header_statut_opac_libelle'>".$msg["collstate_form_statut"]."</th>			
			<th class='collstate_header_origine'>".$msg["collstate_form_origine"]."</th>		
			<th class='collstate_header_state_collections'>".$msg["collstate_form_collections"]."</th>
			<th class='collstate_header_archive'>".$msg["collstate_form_archive"]."</th>
			<th class='collstate_header_lacune'>".$msg["collstate_form_lacune"]."</th>
			!!collstate_bulletins_list_th!!
		</tr>
		!!collstate_liste!!	
	</tbody>	
</table>
";

$tpl_collstate_liste_line[0]="
<tr class='!!pair_impair!!' !!tr_surbrillance!! >
	<!-- surloc -->
	<td class='emplacement_libelle' !!tr_javascript!! >!!emplacement_libelle!!</td>
	<td class='cote' !!tr_javascript!! >!!cote!!</td>
	<td class='type_libelle' !!tr_javascript!! >!!type_libelle!!</td>
	<td class='statut_opac_libelle' !!tr_javascript!! >!!statut_libelle!!</td>	
	<td class='origine' !!tr_javascript!! >!!origine!!</td>
	<td class='state_collections' !!tr_javascript!! >!!state_collections!!</td>
	<td class='archive' !!tr_javascript!! >!!archive!!</td>
	<td class='lacune' !!tr_javascript!! >!!lacune!!</td>
	!!collstate_bulletins_list_td!!
</tr>";

$tpl_collstate_liste[1]="
<table class='exemplaires etatcoll' cellpadding='2' style='width:100%'>
	<tbody>
		<tr>
			<!-- surloc -->
			<th class='collstate_header_location_libelle'>".$msg["collstate_form_localisation"]."</th>		
			<th class='collstate_header_emplacement_libelle'>".$msg["collstate_form_emplacement"]."</th>		
			<th class='collstate_header_cote'>".$msg["collstate_form_cote"]."</th>
			<th class='collstate_header_type_libelle'>".$msg["collstate_form_support"]."</th>
			<th class='collstate_header_statut_opac_libelle'>".$msg["collstate_form_statut"]."</th>		
			<th class='collstate_header_origine'>".$msg["collstate_form_origine"]."</th>		
			<th class='collstate_header_state_collections'>".$msg["collstate_form_collections"]."</th>
			<th class='collstate_header_archive'>".$msg["collstate_form_archive"]."</th>
			<th class='collstate_header_lacune'>".$msg["collstate_form_lacune"]."</th>
			!!collstate_bulletins_list_th!!
		</tr>
		!!collstate_liste!!
	</tbody>	
</table>
";

$tpl_collstate_surloc_liste = "<th class='collstate_header_surloc_libelle'>".$msg["collstate_form_surloc"]."</th>";

$tpl_collstate_liste_line[1]="
<tr class='!!pair_impair!!' !!tr_surbrillance!! >
	<!-- surloc -->
	<td class='localisation' !!tr_javascript!! >!!localisation!!</td>
	<td class='emplacement_libelle' !!tr_javascript!! >!!emplacement_libelle!!</td>
	<td class='cote' !!tr_javascript!! >!!cote!!</td>
	<td class='type_libelle' !!tr_javascript!! >!!type_libelle!!</td>	
	<td class='statut_opac_libelle' !!tr_javascript!! >!!statut_libelle!!</td>
	<td class='origine' !!tr_javascript!! >!!origine!!</td>
	<td class='state_collections' !!tr_javascript!! >!!state_collections!!</td>
	<td class='archive' !!tr_javascript!! >!!archive!!</td>
	<td class='lacune' !!tr_javascript!! >!!lacune!!</td>
	!!collstate_bulletins_list_td!!
</tr>";

$tpl_collstate_surloc_liste_line = "<td class='surloc_libelle' !!tr_javascript!! >!!surloc!!</td>";

$tpl_collstate_bulletins_list_th = "<th class='collstate_header_linked_bulletins_list'>".$msg["collstate_linked_bulletins_list"]."</th>";

$tpl_collstate_bulletins_list_td = "<td class='linked_bulletins_list'><input type='button' class='bouton' value='".$msg["collstate_linked_bulletins_list_link"]."' onclick='!!collstate_bulletins_list_onclick!!'></td>";

$tpl_collstate_bulletins_list_page = "
<div id='collstate_bulletins_list'>
	<h1>".$msg['collstate_linked_bulletins_list_page_title']."</h1>
	<div class='row'>
		<div class='notice-perio'>
	        <div class='row'>
				<table style='width:100%'>
					<tbody>
						!!localisation!!
						!!emplacement_libelle!!
						!!cote!!
						!!type_libelle!!
						!!statut_libelle!!
						!!origine!!
						!!state_collections!!
						!!archive!!
						!!lacune!!
					</tbody>
				</table>
			</div>
		    <hr>
		</div>
	</div>
	<div>
		<div class='row'>
			!!bulletins_list!!
		</div>
	</div>
</div>";

$tpl_collstate_bulletins_list_page_collstate_line = "
<tr>
<td><b>!!label!!</b></td>
<td>!!value!!</td>
</tr>";