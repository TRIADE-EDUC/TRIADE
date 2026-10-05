<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: surligner.inc.php,v 1.34 2023/08/17 09:47:54 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "inc.php")) die("no access");

global $class_path, $include_path;
global $pmb_indexation_lang;
global $user_query, $opac_stemming_active;
global $inclure_recherche;

require_once($class_path."/analyse_query.class.php");
require_once($class_path."/XMLlist.class.php");
require_once("$include_path/marc_tables/$pmb_indexation_lang/empty_words");

$carac_spec = new XMLlist("$include_path/messages/diacritiqueiso-8859-1.xml");
$carac_spec->analyser();
$carac = $carac_spec->table;
		
reset($carac_spec->table);

//Nettoyage de la chaine recherchée
function nettoyer_chaine($tree = array(), &$tableau = [], &$tableau_l = [], $aq = null, $not = 1) {
	global $empty_word,$charset;
	
	if (empty($aq)) {
	    global $user_query, $opac_stemming_active;
	    $aq = new analyse_query(stripslashes($user_query), 0, 0, 1, 0, $opac_stemming_active);
	}
	if (empty($tree)) {
	    $tree = $aq->tree;
	}
	
	if (is_countable($tree)) {

    	for ($i=0; $i<count($tree); $i++) {
    		$mot = "";
    		if ($tree[$i]->not) {
    		    $mul = -1;
    		} else {
    		    $mul = 1;
    		}

    		if ($tree[$i]->sub == null) {
    			if (($not * $mul) == 1) {
    				$mot = str_replace("*","\w*",$tree[$i]->word);
    				$mot = str_replace("+","\w+",$mot);
    				if ($tree[$i]->literal) {
    					$mot=pmb_strtolower(convert_diacrit($mot));
    					if ($mot && !in_array($mot,$tableau_l) && !in_array($mot,$tableau)) {
    						$tableau_l[]= $mot;
    					}
    				} else {
    				    if (strlen($tree[$i]->word)<=1) {
    						$mot = "";
    				    }
    				    if ($mot && !in_array($mot,$tableau) && !in_array($mot,$tableau_l)) {
    						$tableau[]= $mot;
    				    }
    				}
    			}
    		} else {
    			$not = $not*$mul;
    			nettoyer_chaine($tree[$i]->sub, $tableau, $tableau_l, $aq, $not);
    		}
    	}
	}
}

$tableau=array();
$tableau_l=array();
if (!empty($user_query) && (trim($user_query) !== "*")) {
	$aq=new analyse_query(stripslashes($user_query),0,0,1,0,$opac_stemming_active);
	if (!$aq->error) {
		nettoyer_chaine("",$tableau,$tableau_l,$aq,1);
	}
}

//On calcule des variables de session qui seront utilisées dans surligner.js.php
$_SESSION['surligner_tableau'] = strip_tags(implode("','",$tableau));
$_SESSION['surligner_tableau_l'] = strip_tags(implode("','",addslashes_array($tableau_l)));

$_SESSION['surligner_codes'] = "window.codes = {};\n";
$j=0;
foreach($carac_spec->table as $key=>$val) {
	$values=explode("|",substr($val,1,strlen($val)-2));

	$i=0;
	$temp="[";
	if(!isset($values[$i])) $values[$i] = '';
	while ($values[$i]!="") {
		$temp .=$values[$i];
		$i++;
		if(!isset($values[$i])) $values[$i] = '';
	}
	$temp .= "]";
	$_SESSION['surligner_codes'] .= "codes['".$key."']='".$temp."';\n";
	$j++;
}

$_SESSION['surligner_key_carac'] = "";
foreach($carac_spec->table as $key=>$val) {
	$_SESSION['surligner_key_carac'] .= "	reg=new RegExp(codes['".$key."'], 'g');\n";
	$_SESSION['surligner_key_carac'] .= "	chaine=chaine.replace(reg, '".$key."');\n";
}		

$inclure_recherche = "<script src='./includes/javascript/misc.js'></script>";
$inclure_recherche .= "<script>
	var terms=new Array('".(isset($_SESSION['surligner_tableau'])?$_SESSION['surligner_tableau']:'')."');
	var terms_litteraux=new Array('".(isset($_SESSION['surligner_tableau_l'])?$_SESSION['surligner_tableau_l']:'')."');
	if (!codes) {
		var codes = {};
	}

	function remplacer_carac(mot) {
		var x;
		var reg;				
		var chaine=mot;
		".(isset($_SESSION['surligner_key_carac'])?$_SESSION['surligner_key_carac']:'')."		
		return(chaine);		
	}
</script>";

// Ne pas déplacer l'inclusion - Ce fichier la variable de session $_SESSION['surligner_codes']
require_once("$include_path/javascript/surligner.js.php");
//$inclure_recherche .= "<script src='./includes/javascript/surligner.js.php'></script>";
$inclure_recherche .= "<script src='./temp/surligner_codes.js'></script>";
$inclure_recherche .= "<script src='./includes/javascript/surligner.js'></script>";
?>