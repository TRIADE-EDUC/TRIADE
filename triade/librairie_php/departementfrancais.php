<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/


function departement($nom){
// valeurs pour $nom :
// "tous" : retourne le tableau ci-dessous 
// "select" : retourne un menu déroulant avec les departements
//          : passer l'attribut "name" du select comme deuxieme argument optionnel
//          : passer un numero de departement comme troisieme argument optionnel
// un tableau : traite chaque élément du tableau
// 21       : retourne un tableau contenant la corse (haute et basse)
// un entier: retourne le nom du département associé
// une chaine: retourne le numéro du département associé
// si rien n'est trouvé, retourne une chaîne vide.

$dept = array(
"01" => "Ain",
"02" => "Aisne",
"03" => "Allier",
"04" => "Alpes de Haute Provence",
"05" => "Hautes Alpes",
"06" => "Alpes Maritimes",
"07" => "Ard&egrave;che",
"08" => "Ardennes",
"09" => "Ari&eacute;ge",
"10" => "Aube",
"11" => "Aude",
"12" => "Averyon",
"13" => "Bouche du Rh&ocirc;ne",
"14" => "Calvados",
"15" => "Cantal",
"16" => "Charente",
"17" => "Charente Maritime",
"18" => "Cher",
"19" => "Corr&egrave;ze",
"2a" => "Corse du Sud",
"2b" => "Haute Corse",
"21" => "C&ocirc;te d'Or",
"22" => "C&ocirc;tes d'Armor",
"23" => "Creuse",
"24" => "Dordogne",
"25" => "Doubs",
"26" => "Dr&ocirc;me",
"27" => "Eure",
"28" => "Eure et Loire",
"29" => "Finist&egrave;re",
"30" => "Gard",
"31" => "Haute Garonne",
"32" => "Gers",
"33" => "Gironde",
"34" => "Herault",
"35" => "Ille et Vilaine",
"36" => "Indre",
"37" => "Indre et Loire",
"38" => "Is&egrave;re",
"39" => "Jura",
"40" => "Landes",
"41" => "Loir et Cher",
"42" => "Loire",
"43" => "Haute Loire",
"44" => "Loire Atlantique",
"45" => "Loiret",
"46" => "Lot",
"47" => "Lot et Garonne",
"48" => "Loz&egrave;re",
"49" => "Maine et Loire",
"50" => "Manche",
"51" => "Marne",
"52" => "Haute Marne",
"53" => "Mayenne",
"54" => "Meurthe et Moselle",
"55" => "Meuse",
"56" => "Morbihan",
"57" => "Moselle",
"58" => "Ni&egravevre",
"59" => "Nord",
"60" => "Oise",
"61" => "Orne",
"62" => "Pas de Calais",
"63" => "Puy de D&ocirc;me",
"64" => "Pyren&eacute;es Atlantiques",
"65" => "Hautes Pyren&eacute;es",
"66" => "Pyren&eacute;es orientales",
"67" => "Bas Rhin",
"68" => "Haut Rhin",
"69" => "Rh&ocirc;ne",
"70" => "Haute Sa&ocirc;ne",
"71" => "Sa&ocirc;ne et Loire",
"72" => "Sarthe",
"73" => "Savoie",
"74" => "Haute Savoie",
"75" => "Paris",
"76" => "Seine Maritime",
"77" => "Seine et Marne",
"78" => "Yvelines",
"79" => "Deux S&egrave;vres",
"80" => "Somme",
"81" => "Tarn",
"82" => "Tarn et Garonne",
"83" => "Var",
"84" => "Vaucluse",
"85" => "Vend&eacute;e",
"86" => "Vienne",
"87" => "Haute Vienne",
"88" => "Vosges",
"89" => "Yonne",
"90" => "Territoire de Belfort",
"91" => "Essonne",
"92" => "Hauts de Seine",
"93" => "Seine Saint Denis",
"94" => "Val de Marne",
"95" => "Val d'Oise"
);

if (is_string($nom) && ($nom == "tous")){
	return $dept;
} elseif (is_string($nom) && ($nom == "select")){
	if (func_num_args() >= 2){
	  $sel_nom = func_get_arg(1);
	} else { $sel_nom = "";}
	if (func_num_args() >= 3){
	  $checked = func_get_arg(2);
	} else { $checked = "";}
	$select = '<select name="'.$nom.'">';
	while(list($nb, $nom) = each($dept)){
		if ($nb == $checked){
			$select .= "<option value=\"$nb\" selected>$nom</option>\n";
		} else {
			$select .= "<option value=\"$nb\">$nom</option>\n";
		}
	}
	return $select .= '</select>';
} elseif (is_array($nom)){
  $retour = array();
  foreach ($nom as $n){
    $retour[] = departement($n);
  }
  return $retour;
} elseif (is_int($nom) && ($nom == 21)){
	$retour = array_kslice($dept, "2a", 2);
	$r = array();
	$r["21"] = $retour;
    return $r;
} elseif (is_int($nom) || in_array($nom, array("2a","2b"))) {
	if (isset($dept[$nom])){
		return $dept[$nom];
	}
	return "";
} else {
	$dept = array_flip($dept);
	$nom = ucwords(strtolower($nom));
	if (isset($dept[$nom])){
		return $dept[$nom];
	}
	return "";
}

/****
 * Titre : Extraction associative 
 * Auteur : Damien Seguy 
 * Email : damien.seguy@nexen.net
 * Url : www.nexen.net/
 * Description : Cette fonction est l'équivalent de array_slice, pour les tableaux associatifs.
Il suffit d'indiquer la clé de debut, et le nombre d'entrees a lire.
****/
function array_kslice($array,$key,$length){
$keys = array_keys($array);
  $syek = array_flip($keys);
  $index = $syek[$key];
  
  $keys = array_slice($keys, $index, $length);
  $retour = array();
  foreach ($keys as $k){
    $retour[$k] = $array[$k];
  }
  return $retour;
}

/****
 * Titre : array_slice pour PHP3 
 * Auteur : Cedric Fronteau 
 * Email : charlie@nexen.net
 * Url : 
 * Description : Remplace la fonction array_slice pour les utilisateurs PHP3.
Fonctionne exactement comme décrit dans la doc.
****/
function array_slice($arr,$offset){
if (!is_array($arr))
      return FALSE;
      
   $size = sizeof($arr);
   $t = array();
   
   if ($offset >= $size)
      return $t;

   if (func_num_args() >= 3)
      $length = func_get_arg(2);
   else
      $length = $size;

   if ($offset < 0)
      {
      $offset += $size;
      if ($offset < 0)
         $offset = 0;
      }
   if ($length < 0)
      $length += $size - $offset;
   
   for($i=0;$i<$length && ($i+$offset)<$size;$i++)
      {
      $t[] = $arr[$i+$offset];
      }
   return $t;
}


?>
