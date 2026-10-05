<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perso.inc.php,v 1.8.10.1 2025/03/12 17:27:47 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}
global $class_path;

require_once $class_path . "/parametres_perso.class.php";

$option_visibilite = array();
$option_visibilite["multiple"] = "block";
$option_visibilite["obligatoire"] = "block";
$option_visibilite["search"] = "block";
$option_visibilite["export"] = "block";
$option_visibilite["import"] = "block";
$option_visibilite["filters"] = "none";
$option_visibilite["exclusion"] = "block";
$option_visibilite["opac_sort"] = "block";

$p_perso = new parametres_perso("notices", "./admin.php?categ=notices&sub=perso", $option_visibilite);

$p_perso->proceed();
