<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: modelling.tpl.php,v 1.2 2019-05-27 10:34:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $modelling_menu, $msg, $charset, $modelling_layout, $current_module, $modelling_layout_end;

require("cms/cms.tpl.php");

$modelling_menu = "
	<div id='menu'>";
if(SESSrights & MODELLING_AUTH) {
	$modelling_menu .= "
		<h3 onclick='menuHide(this,event)'>".htmlentities($msg["modelling_tab_title"],ENT_QUOTES,$charset)."</h3>
		<ul>
			<li><a href='./modelling.php?categ=ontologies'>".htmlentities($msg["ontologies"],ENT_QUOTES,$charset)."</a></li>
		</ul>";
}

$modelling_layout = "<div id='conteneur' class='$current_module'>
	$modelling_menu
	<div id='contenu'>
	!!menu_contextuel!!
";

$modelling_layout_end = "
		</div>
	</div>
";
