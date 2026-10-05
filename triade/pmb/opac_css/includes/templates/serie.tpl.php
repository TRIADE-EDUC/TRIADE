<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serie.tpl.php,v 1.4 2019/05/29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// ce fichier contient des templates indiquant comment doit s'afficher un éditeur

if ( ! defined( 'SERIE_TMPL' ) ) {
  define( 'SERIE_TMPL', 1 );

//	----------------------------------
//	$serie_display : écran d'info pour une série
// !!id!!        identifiant de l'éditeur
// !!name!!      nom de l'éditeur

global $serie_level2_display, $msg;
  
// level 2 : affichage général
$serie_level2_display = "
<div class=serielevel2>
<h3>".sprintf($msg["serie_details_serie"],"!!name!!")."</h3>
</div>
";

} # fin de définition
