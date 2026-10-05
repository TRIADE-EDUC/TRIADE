<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perso.inc.php,v 1.5 2020/08/04 12:06:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/parametres_perso.class.php");

$option_visibilite=array();
$option_visibilite["multiple"]="block";
$option_visibilite["obligatoire"]="block";
$option_visibilite["search"]="block";
$option_visibilite["export"]="none";
$option_visibilite["filters"]="none";
$option_visibilite["exclusion"]="none";
$option_visibilite["opac_sort"]="block";

$p_perso=new parametres_perso($type_field,"./admin.php?categ=authorities&sub=perso&type_field=$type_field",$option_visibilite);

$p_perso->proceed();

?>