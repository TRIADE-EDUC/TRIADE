<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.tpl.php,v 1.6 2019/05/27 13:35:33 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// template pour la page d'accueil
// $main : contenu de la page d'accueil
global $main_layout, $main_layout_end;

// $main_layout : layout page main
$main_layout = "
	<div id='conteneur'>
		<div id='contenu'>
	";

//	----------------------------------
// $main_layout_end : layout page main (fin)
$main_layout_end = "
	</div>
	</div>
	";

