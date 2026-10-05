<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perso.inc.php,v 1.1 2015/06/26 13:15:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/pret_parametres_perso.class.php");

$option_visibilite=array();
$option_visibilite["multiple"]="none";
$option_visibilite["obligatoire"]="block";
$option_visibilite["search"]="none";
$option_visibilite["export"]="none";
$option_visibilite["filters"]="block";
$option_visibilite["exclusion"]="none";
$option_visibilite["opac_sort"]="none";

$p_perso=new pret_parametres_perso("pret","./admin.php?categ=loans&sub=perso",$option_visibilite);

$p_perso->proceed();

?>