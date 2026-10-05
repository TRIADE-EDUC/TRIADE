<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: semantic.tpl.php,v 1.3 2019/05/27 12:59:00 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $semantic_layout, $semantic_layout_end, $msg;

$semantic_menu = "
	<div id='menu'>
		<h3 onclick='menuHide(this,event)'>".$msg['ontologies']."</h3>
		<ul>
			!!ontologies_menu!!
		</ul>";
$plugins = plugins::get_instance();
$semantic_menu.= $plugins->get_menu("semantic")."
	</div>";

$semantic_layout = "
<div id='conteneur' class='semantic'>
$semantic_menu
	<div id='contenu'>";

$semantic_layout_end = "
	</div>
</div>";