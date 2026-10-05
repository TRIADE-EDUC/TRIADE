<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_browser_1.php,v 1.5 2019/06/07 08:05:39 btafforeau Exp $

global $base_path, $base_auth, $base_title, $include_path;

// affichage du browser de catégories

// définition du minimum nécéssaire
$base_path="../../../..";
$base_auth = "CATALOGAGE_AUTH";
$base_title = "\$msg[6]";
require_once ("$base_path/includes/init.inc.php");

include("$include_path/tree.inc.php");
print "
	<div id='contenu-frame'>";
tree("window.parent.document.location='../../../../catalog.php?categ=search&mode=1&categ_id=!!id!!'; return(false);");
print "
	</div>";
