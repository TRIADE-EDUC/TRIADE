<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collstate.tpl.php,v 1.14 2023/11/17 09:54:23 dgoron Exp $

// templates pour gestion des autorités collections

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg;
global $tpl_collstate_bulletins_list_page;
global $tpl_collstate_bulletins_list_page_collstate_line;

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