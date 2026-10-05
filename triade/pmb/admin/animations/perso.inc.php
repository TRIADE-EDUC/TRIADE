<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perso.inc.php,v 1.3 2020/10/05 12:43:50 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/animations/animations_pricetype_parametres_perso.class.php");

$option_visibilite=array();
$option_visibilite["multiple"]="block";
$option_visibilite["obligatoire"]="block";
$option_visibilite["search"]="block";
$option_visibilite["export"]="none";
$option_visibilite["filters"]="none";
$option_visibilite["exclusion"]="none";
$option_visibilite["opac_sort"]="block";

global $type_field;

switch ($type_field){
    case "anim_price_type":
        global $numPriceType;
        $p_perso=new animations_pricetype_parametres_perso($numPriceType,"./admin.php?categ=animations&sub=priceTypesPerso&type_field=$type_field&numPriceType=$numPriceType",$option_visibilite);
        break;
    case "anim_animation":
        $p_perso=new parametres_perso($type_field,"./admin.php?categ=animations&sub=perso&type_field=$type_field",$option_visibilite);
        break;
}

$p_perso->proceed();

?>