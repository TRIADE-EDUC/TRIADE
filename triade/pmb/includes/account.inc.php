<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: account.inc.php,v 1.15 2021/05/11 07:47:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $styles_path, $include_path;


function get_account_info($user) {
	if(!$user) {
		return 0;
	}
	$requete = "SELECT * FROM users WHERE username='".addslashes($user)."' LIMIT 1";
	$result = pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($result)) {
		$values = pmb_mysql_fetch_object($result);
		return $values;
	} 
	return 0;
}

function get_styles() {
	// où $rep = répertoire de stockage des feuilles
	// retourne un tableau indexé avec les noms des CSS disponibles
	
	// mise en forme du répertoire
	global $styles_path;
	
	if($styles_path) {
		$rep = $styles_path;
	} else {
		$rep = './styles/';
	}
	
	if( '/' != substr($rep,-1) ) {
		$rep .= '/';
	}
	
	$handle = @opendir($rep);
	
	if(!$handle) {
		$result = array();
		return $result;
	}
	
	while($css = readdir($handle)) {
		if(is_dir($rep.$css) && !preg_match('/\.|cvs|CVS|common|affichage_arabe|rtl|dsi|images/', $css) ) {
			$result[] = $css;
		}
	}
	
	closedir($handle);
	
	sort($result);
	return $result;
}

function make_user_lang_combo($lang='') {
	// retourne le combo des langues avec la langue $lang selectionnée
	// nécessite l'inclusion de XMLlist.class.php (normalement c'est déjà le cas partout
	global $include_path;
	global $charset;
	
	// langue par défaut
	if(!$lang) $lang="fr_FR";
	
	$langues = new XMLlist("$include_path/messages/languages.xml");
	$langues->analyser();
	$clang = $langues->table;
	$combo = "<select name='user_lang' id='user_lang' class='saisie-20em'>";
	foreach ($clang as $cle => $value) {
		// arabe seulement si on est en utf-8
		if (($charset != 'utf-8' && $lang != 'ar') || ($charset == 'utf-8')) {
			if(strcmp($cle, $lang) != 0) {
				$combo .= "<option value='$cle'>$value ($cle)</option>";
			} else {
				$combo .= "<option value='$cle' selected >$value ($cle)</option>";
			}
		}
	}
	$combo .= "</select>";
	return $combo;
}

function make_user_style_combo($dstyle='') {
	// retourne le combo des styles avec le style $style selectionné
	$style = get_styles();
	$combo = "<select name='form_style' id='form_style' class='saisie-20em'>";
	foreach ($style as $valeur) {
        $libelle = $valeur; 
        if(strcmp($valeur, $dstyle) == 0) {
        	$combo .= "<option value=\"$valeur\" selected >$libelle</option>";
        } else {
        	$combo .= "<option value=\"$valeur\">$libelle</option>";
        }
    }
    $combo .= "</select>";
	return $combo;
}

function make_user_tdoc_combo($typdoc=0) {
	$requete = "SELECT idtyp_doc, tdoc_libelle FROM docs_type order by 2";
	$result = pmb_mysql_query($requete);
	$combo = "<select name='form_deflt_tdoc' id='form_deflt_tdoc' class='saisie-30em'>";
	while($tdoc = pmb_mysql_fetch_object($result)) {
		if($tdoc->idtyp_doc != $typdoc) {
			$combo .= "<option value='".$tdoc->idtyp_doc."'>".$tdoc->tdoc_libelle."</option>";
		} else {
			$combo .= "<option value='".$tdoc->idtyp_doc."' selected >".$tdoc->tdoc_libelle."</option>";
		}
	}
	$combo .= "</select>";
	return $combo;
}
