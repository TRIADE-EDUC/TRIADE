<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc.inc.php,v 1.119 2019-06-11 06:53:05 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once "$include_path/apache_functions.inc.php";
require_once "$class_path/semantique.class.php";
require_once "$class_path/parse_format.class.php";
require_once "$class_path/curl.class.php";

//ajout des mots vides calculés
$add_empty_words=semantique::add_empty_words();
if ($add_empty_words) eval($add_empty_words);

if (!function_exists('is_countable')) {
	function is_countable($var) {
		return (is_array($var) || $var instanceof Countable);
	}
}

//Fonction pour gérer les images demandés par PMB
function getimage_cache($notice_id=0, $etagere_id=0, $authority_id=0, $vigurl=0, $noticecode=0, $url_image=0){
	global $pmb_notice_img_folder_id, $pmb_authority_img_folder_id, $opac_url_base, $dbh;

	global $opac_img_cache_folder;

	$img_cache_folder = $opac_img_cache_folder;

	$stop = false;
	$hash = $location = $hash_location = $hash_location_empty = "";

	$imgpmb_name=$imgpmb_test="";
	if($notice_id){
		$imgpmb_name="img_".$notice_id;
		$imgpmb_test=$pmb_notice_img_folder_id;
	}elseif($etagere_id){
		$imgpmb_name="img_etag_".$etagere_id;
		$imgpmb_test=$pmb_notice_img_folder_id;
	}elseif($authority_id){
		$imgpmb_name="img_authority_".$authority_id;
		$imgpmb_test=$pmb_authority_img_folder_id;
	}

	if(!$stop && $imgpmb_name && $imgpmb_test){
		$req = "select repertoire_path from upload_repertoire where repertoire_id ='".$imgpmb_test."'";
		$res = pmb_mysql_query($req,$dbh);
		if(pmb_mysql_num_rows($res)){
			$rep = pmb_mysql_fetch_array($res,PMB_MYSQL_NUM);
			$location = $rep[0].$imgpmb_name;
			if($img_cache_folder && file_exists($location)){
				$hash = md5($opac_url_base.$location);
				$hash_location = $img_cache_folder.$hash.".png";
				if(file_exists($hash_location)){
					$location = $hash_location;
					$hash_location = "";
				}
			}else{
				//Gestion de l'existance du fichier non géré, comme c'était le cas avant
			}
			$stop = true;
		}
	}

	if(!$stop && $img_cache_folder){
		$hash_image="";
		if($vigurl){
			$hash_image.=$vigurl;
		}
		if($noticecode){
			$hash_image.=$noticecode;
		}
		if($url_image){
			$hash_image.=$url_image;
		}

		if($hash_image){
			$hash=md5($hash_image);
			$image_rep_cache=$img_cache_folder.$hash.".png";
			//Pour les images vides elles peuvent changer entre les PMB, la gestion et l'Opac
			$hash_img_empty=md5($hash_image.$opac_url_base.'1');
			$image_empty_rep_cache=$img_cache_folder.$hash_img_empty.".png";
			if(file_exists($image_empty_rep_cache)){
				$location = $image_empty_rep_cache;
			}elseif(file_exists($image_rep_cache)){
				$location = $image_rep_cache;
			}else{
				//on teste l'existence de répertoire de cache pour éviter les erreurs et les liens cassés
				if (file_exists($img_cache_folder)) {
					$hash_location = $image_rep_cache;
					$hash_location_empty = $image_empty_rep_cache;
				}
			}
		}
	}

	$tmp = array("hash" => $hash, "location" => $location, "hash_location" => $hash_location, "hash_location_empty" => $hash_location_empty);
	return $tmp;
}

function getimage_url($code = "", $vigurl = "", $no_cache=false) {
	global $opac_url_base, $opac_book_pics_url, $pmb_opac_url, $pmb_url_base;
	global $pmb_img_cache_folder, $pmb_img_cache_url, $opac_img_cache_folder, $opac_img_cache_url;
	global $use_opac_url_base;
	
	$url_return = $notice_id = $etagere_id = $authority_id = $noticecode = $url_image = "" ;

	$url_image = $opac_book_pics_url;
	$prefix=$opac_url_base;
	$img_cache_folder = $opac_img_cache_folder;
	$img_cache_url = $opac_img_cache_url;
	$cached_in_opac = 1;

	if($code){
		$noticecode = pmb_preg_replace('/-|\.| /', '', $code);
	}else{
		$noticecode = "";
	}

	$for_cut="";
	$out = array();
	if (($vigurl) && (preg_match('#^(.+)?getimage\.php(.+)?$#',$vigurl,$out))) {
		if(isset($out[1]) && trim($out[1])){
			$contruct_url = trim($out[1]);
			if(($contruct_url == "./") || ($contruct_url == $opac_url_base) || ($contruct_url == $pmb_opac_url) || ($contruct_url == $pmb_url_base)){
				//Je peux tenter de trouve une URL statique
				if(isset($out[2])){
					$for_cut = trim($out[2]);
				}
			}/*else{
			//Impossible on vient d'un autre PMB, on prend l'URL telque
			}*/
		}elseif(isset($out[1]) && !trim($out[1])){//L'url de la vignette de la notice commence par getimage sans rien devant
			//Je peux tenter de trouve une URL statique
			if(isset($out[2])){
				$for_cut = trim($out[2]);
			}
		}

		if($for_cut){
			$out2=array();
			if(preg_match("#(notice_id|etagere_id|authority_id)=([0-9]+)#",$for_cut,$out2)){
				switch ($out2[1]) {
					case "notice_id":
						$notice_id = $out2[2];
						$url_return = $prefix."getimage.php?notice_id=".$notice_id;
						break;
					case "etagere_id":
						$etagere_id = $out2[2];
						$url_return = $prefix."getimage.php?etagere_id=".$etagere_id;
						break;
					case "authority_id":
						$authority_id = $out2[2];
						$url_return = $prefix."getimage.php?authority_id=".$authority_id;
						break;
				}
			}
		}
	}

	if((strpos($vigurl,'data:image',0) === 0) || (strpos($vigurl,"vig_num.php") !== FALSE ) || (strpos($vigurl,"vign_middle.php") !== FALSE )){
		$url_return = $vigurl;
	}elseif(!$no_cache && $img_cache_url && $img_cache_folder && empty($use_opac_url_base)){
		$manag_cache=getimage_cache($notice_id, $etagere_id, $authority_id, $vigurl, $noticecode, $url_image);
		$out=array();
		if($manag_cache["location"] && preg_match("#^".$img_cache_folder."(.+)$#",$manag_cache["location"],$out)){
			$url_return = $img_cache_url.$out[1];
		}
	}

	if(!$url_return){
		$url_return = $prefix."getimage.php?url_image=".urlencode($url_image)."&amp;noticecode=!!noticecode!!&amp;vigurl=".urlencode($vigurl) ;
		$url_return = str_replace("!!noticecode!!", $noticecode, $url_return) ;
	}
	return $url_return;
}

//Fonction de récupération d'une URL vignette
function get_vignette($notice_id, $no_cache=false) {
	global $opac_book_pics_url, $opac_show_book_pics;
	global $opac_url_base;
	$url_image_ok = "";
	$requete="select code,thumbnail_url from notices where notice_id=$notice_id";
	$res=pmb_mysql_query($requete);
	if ($res) {
		$notice=pmb_mysql_fetch_object($res);
		if ($notice->code || $notice->thumbnail_url) {
			if ($opac_show_book_pics=='1' && ($opac_book_pics_url || $notice->thumbnail_url)) {
				$url_image_ok = getimage_url($notice->code, $notice->thumbnail_url, $no_cache);
			}
		}
	}
	if(!$url_image_ok){
		$url_image_ok = get_url_icon("vide.png", 1);
	}
	return $url_image_ok;
}

// ----------------------------------------------------------------------------
//	fonctions de formatage de chaîne
// ----------------------------------------------------------------------------
// reg_diacrit : fonction pour traiter les caractères accentués en recherche avec regex


function reg_diacrit($chaine) {
	$chaine = convert_diacrit($chaine);
	$tab = pmb_split('/\s/', $chaine);
	// mise en forme de la chaine pour les alternatives
	// on fonctionne avec OU (pour l'instant)
	if(sizeof($tab) > 1) {
		$mots = array();
		foreach($tab as $dummykey=>$word) {
			if($word) $mots[] = "($word)";
		}
		return join('|', $mots);
	} else {
		return $chaine;
	}
}

function convert_diacrit($string) {
	global $tdiac;
	global $charset;
	global $include_path;
	global $tdiac_diacritique, $tdiac_replace;
	if(!$string) return;
	if (!$tdiac) {
		$tdiac = new XMLlist($include_path."/messages/diacritique".$charset.".xml");
		$tdiac->analyser();
	}
	if (!is_countable($tdiac_diacritique) || !is_countable($tdiac_replace) || !count($tdiac_diacritique) || !count($tdiac_replace)) {
		$tdiac_diacritique = array();
		$tdiac_replace = array();
		foreach($tdiac->table as $wreplace => $wdiacritique) {
			$wdiacritique = str_replace(array('(', ')'), "", $wdiacritique);
			foreach (explode('|', $wdiacritique) as $wdiac) {
				$tdiac_diacritique[] = $wdiac;
				$tdiac_replace[] = $wreplace;
			}

		}
	}
	$string = str_replace($tdiac_diacritique,$tdiac_replace,$string);
	return $string;
}

//strip_empty_chars : enlêve tout ce qui n'est pas alphabétique ou numérique d'une chaine
function strip_empty_chars($string) {
	// traitement des diacritiques
	$string = convert_diacrit($string);
	// Mis en commentaire : qu'en est-il des caractères non latins ???
	// SUPPRIME DU COMMENTAIRE : ER : 12/05/2004 : ça fait tout merder...
	// RECH_14 : Attention : ici suppression des éventuels "
	//          les " ne sont plus supprimés
	$string = stripslashes($string) ;
	$string = pmb_alphabetic('^a-z0-9\s', ' ',pmb_strtolower($string));

	// remplacement espace  insécable 0xA0:	&nbsp;  	Non-breaking space
	$string = clean_nbsp($string);
	// espaces en début et fin
	$string = pmb_preg_replace('/^\s+|\s+$/', '', $string);

	// espaces en double
	$string = pmb_preg_replace('/\s+/', ' ', $string);

	return $string;
}

function get_empty_words($lg = 0) {
	global $got_empty_word;

	if(!isset($got_empty_word[$lg]) || !$got_empty_word[$lg]) {
		$got_empty_word[$lg] = array();
		global $empty_word;
		$got_empty_word[$lg] = $empty_word;
		$mots = array();
		$query = "select mot from mots join linked_mots on mots.id_mot = linked_mots.num_mot where type_lien = 4";
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$mots[] = $row->mot;
			}
			$got_empty_word[$lg] = array_diff($got_empty_word[$lg], $mots);
		}
	}
	return $got_empty_word[$lg];
}

// strip_empty_words : fonction enlevant les mots vides d'une chaîne
function strip_empty_words($string) {

	// on inclut le tableau des mots-vides pour la langue par defaut
	// c'est normalement la langue de catalogage...
	// si après nettoyage des mots vide la chaine est vide alors on garde la chaine telle quelle (sans les accents)

	$empty_word = get_empty_words();
	// nettoyage de l'entrée

	// traitement des diacritiques
	$string = convert_diacrit($string);

	// Mis en commentaire : qu'en est-il des caractères non latins ???
	// SUPPRIME DU COMMENTAIRE : ER : 12/05/2004 : ça fait tout merder...
	// RECH_14 : Attention : ici suppression des éventuels "
	//          les " ne sont plus supprimés
	$string = stripslashes($string) ;
	$string = pmb_alphabetic('^a-z0-9\s', ' ',pmb_strtolower($string));

	// remplacement espace  insécable 0xA0:	&nbsp;  	Non-breaking space
	$string = clean_nbsp($string);

	// espaces en début et fin
	$string = pmb_preg_replace('/^\s+|\s+$/', '', $string);

	// espaces en double
	$string = pmb_preg_replace('/\s+/', ' ', $string);

	$string_avant_mots_vides = $string ;
	// suppression des mots vides
	if(is_array($empty_word)) {
		foreach($empty_word as $dummykey=>$word) {
			$word = convert_diacrit($word);
			$string = pmb_preg_replace("/^${word}$|^${word}\s|\s${word}\s|\s${word}\$/i", ' ', $string);
			// RECH_14 : suppression des mots vides collés à des guillemets
			if (pmb_preg_match("/\"${word}\s/i",$string)) $string = pmb_preg_replace("/\"${word}\s/i", '"', $string);
			if (pmb_preg_match("/\s${word}\"/i",$string)) $string = pmb_preg_replace("/\s${word}\"/i", '"', $string);
			}
		}


	// re nettoyage des espaces générés
	// espaces en dÈbut et fin
	$string = pmb_preg_replace('/^\s+|\s+$/', '', $string);
	// espaces en double
	$string = pmb_preg_replace('/\s+/', ' ', $string);

	if (!$string) {
		$string = $string_avant_mots_vides ;
		// re nettoyage des espaces générés
		// espaces en dÈbut et fin
		$string = pmb_preg_replace('/^\s+|\s+$/', '', $string);
		// espaces en double
		$string = pmb_preg_replace('/\s+/', ' ', $string);
		}

	return $string;
	}

// clean_string() : fonction de nettoyage d'une chaîne
function clean_string($string) {

	// on supprime les caractËres non-imprimables
	$string = pmb_preg_replace("/\\x0|[\x01-\x1f]/U","",$string);

	// suppression des caractËres de ponctuation indÈsirables
	// $string = pmb_preg_replace('/[\{\}\"]/', '', $string);

	// supression du point et des espaces de fin
	$string = pmb_preg_replace('/\s+\.$|\s+$/', '', $string);

	// nettoyage des espaces autour des parenthËses
	$string = pmb_preg_replace('/\(\s+/', '(', $string);
	$string = pmb_preg_replace('/\s+\)/', ')', $string);

	// idem pour les crochets
	$string = pmb_preg_replace('/\[\s+/', '[', $string);
	$string = pmb_preg_replace('/\s+\]/', ']', $string);

	// petit point de détail sur les apostrophes
	$string = pmb_preg_replace('/\'\s+/', "'", $string);

	// 'trim' par regex
	$string = pmb_preg_replace('/^\s+|\s+$/', '', $string);

	// suppression des espaces doubles
	$string = pmb_preg_replace('/\s+/', ' ', $string);

	return $string;
	}

//Corrections des caractères bizarres (voir pourris) de M$
function cp1252Toiso88591($str){
	$cp1252_map = array(
		"\x80" => "EUR", /* EURO SIGN */
		"\x82" => "\xab", /* SINGLE LOW-9 QUOTATION MARK */
		"\x83" => "\x66",     /* LATIN SMALL LETTER F WITH HOOK */
		"\x84" => "\xab", /* DOUBLE LOW-9 QUOTATION MARK */
		"\x85" => "...", /* HORIZONTAL ELLIPSIS */
		"\x86" => "?", /* DAGGER */
		"\x87" => "?", /* DOUBLE DAGGER */
		"\x88" => "?",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
		"\x89" => "?", /* PER MILLE SIGN */
		"\x8a" => "S",   /* LATIN CAPITAL LETTER S WITH CARON */
		"\x8b" => "\x3c", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
		"\x8c" => "OE",   /* LATIN CAPITAL LIGATURE OE */
		"\x8e" => "Z",   /* LATIN CAPITAL LETTER Z WITH CARON */
		"\x91" => "\x27", /* LEFT SINGLE QUOTATION MARK */
		"\x92" => "\x27", /* RIGHT SINGLE QUOTATION MARK */
		"\x93" => "\x22", /* LEFT DOUBLE QUOTATION MARK */
		"\x94" => "\x22", /* RIGHT DOUBLE QUOTATION MARK */
		"\x95" => "\b7", /* BULLET */
		"\x96" => "\x20", /* EN DASH */
		"\x97" => "\x20\x20", /* EM DASH */
		"\x98" => "\x7e",   /* SMALL TILDE */
		"\x99" => "?", /* TRADE MARK SIGN */
		"\x9a" => "S",   /* LATIN SMALL LETTER S WITH CARON */
		"\x9b" => "\x3e;", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
		"\x9c" => "oe",   /* LATIN SMALL LIGATURE OE */
		"\x9e" => "Z",   /* LATIN SMALL LETTER Z WITH CARON */
		"\x9f" => "Y"    /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
	);
	$str = strtr($str, $cp1252_map);
	return $str;
}

// ----------------------------------------------------------------------------
//	test_title_query() : nouvelle version analyse d'une rech. sur titre
// ----------------------------------------------------------------------------
function test_title_query($query, $operator=TRUE, $force_regexp=FALSE) {
	// Armelle : a priori utilise uniquement dans édition des périodique. Changer la-bas.
	// fonction d'analyse d'une recherche sur titre
	// la fonction retourne un tableau :
	$query_result = array(  'type' => 0,
	                        'restr' => '',
	                        'order' => '',
	                        'nbr_rows' => 0);

	// FORCAGE ER 12/05/2004 : le match against avec la troncature* ne fonctionne pas...
	$force_regexp = TRUE ;

	// $query_result['type'] = type de la requête :
	// 0 : rien (problème)
	// 1: match/against
	// 2: regexp
	// 3: regexp pure sans traitement
	// $query_result['restr'] = critères de restriction
	// $query_result['order'] = critères de tri
	// $query_result['indice'] = façon d'obtenir un indice de pertinence
	// $query_result['nbr_rows'] = nombre de lignes qui matchent

	// si operator TRUE La recherche est booléenne AND
	// si operator FALSE La recherche est booléenne OR
	// si force_regexp : la recherche est forcée en mode regexp

	$stopwords = FALSE;
	global $dbh;

	// initialisation opérateur
	$operator ? $dopt = 'AND' : $dopt = 'OR';

	$query = strtolower($query);

	// espaces en début et fin
	$query = preg_replace('/^\s+|\s+$/', '', $query);

	// espaces en double
	$query = preg_replace('/\s+/', ' ', $query);


	// traitement des caractères accentués
	$query = convert_diacrit($query);

	// contrôle de la requete
	if(!$query)
		return $query_result;

	// déterminer si la requête est une regexp
	// si c'est le cas, on utilise la saisie utilisateur sans modification
	// (on part du principe qu'il sait ce qu'il fait)

	if(preg_match('/\^|\$|\[|\]|\.|\*|\{|\}|\|/', $query)) {
		// regexp pure : pas de modif de la saisie utilisateur
		$query_result['type'] = 3;
		$query_result['restr'] =  "index_serie REGEXP '$query'";
		$query_result['restr'] .= " OR tit1 REGEXP '$query'";
		$query_result['restr'] .= " OR tit2 REGEXP '$query'";
		$query_result['restr'] .= " OR tit3 REGEXP '$query'";
		$query_result['restr'] .= " OR tit4 REGEXP '$query'";
	       	$query_result['order'] = "index_serie ASC, tnvol ASC, tit1 ASC";
		} else {
	 		// nettoyage de la chaîne
	 		$query = preg_replace("/[\(\)\,\;\'\!\-\+]/", ' ', $query);

	 		// on supprime les mots vides
	 		$query = strip_empty_words($query);

	 		// contrôle de la requete
	 		if(!$query) return $query_result;

			// la saisie est splitée en un tableau
			$tab = preg_split('/\s+/', $query);

			// on cherche à détecter les mots de moins de 4 caractères (stop words)
			// si il y des mots remplissant cette condition, c'est la méthode regexp qui sera employée
			foreach($tab as $dummykey=>$word) {
				if(strlen($word) < 4) {
					$stopwords = TRUE;
					break;
					}
				}

			if($stopwords || $force_regexp) {
				// méthode REGEXP
				$query_result['type'] = 2;
				 // constitution du membre restricteur
				// premier mot
				$query_result['restr'] = "(index_sew REGEXP '${tab[0]} ) '";
				for ($i = 1; $i < sizeof($tab); $i++) {
					$query_result['restr'] .= " $dopt (index_sew REGEXP '${tab[$i]}' )";
					}
				// contitution de la clause de tri
				$query_result['order'] = "index_serie ASC, tnvol ASC, tit1 ASC";
				} else {
					// méthode FULLTEXT
					$query_result['type'] = 1;
					// membre restricteur
					$query_result['restr'] = "MATCH (index_wew) AGAINST ('*${tab[0]}*')";
					for ($i = 1; $i < sizeof($tab); $i++) {
						$query_result['restr'] .= " $dopt MATCH";
						$query_result['restr'] .= " (index_wew)";
						$query_result['restr'] .= " AGAINST ('*${tab[$i]}*')";
						}
					// membre de tri
					$query_result['order'] = "index_serie DESC, tnvol ASC, index_sew ASC";
					}
			}

	// récupération du nombre de lignes
	$rws = "SELECT count(1) FROM notices WHERE ${query_result['restr']}";
	$result = @pmb_mysql_query($rws, $dbh);
	$query_result['nbr_rows'] = @pmb_mysql_result($result, 0, 0);

	return $query_result;
	}

//Fonction de préparation des chaines pour regexp sans match against
function analyze_query($query) {
	// Armelle - a priori plus utilisé
	// déterminer si la requête est une regexp
	// si c'est le cas, on utilise la saisie utilisateur sans modification
	// (on part du principe qu'il sait ce qu'il fait)
	if(preg_match('/\^|\$|\[|\]|\.|\*|\{|\}|\|\+/', $query)) {
		// traitement des caractères accentués
		$query = preg_replace('/[àáâãäåÀÁÂÃÄÅ]/'	, 'a', $query);
		$query = preg_replace('/[éèêëÈÉÊË]/'		, 'e', $query);
		$query = preg_replace('/[ìíîïÌÍÎÏ]/'		, 'i', $query);
		$query = preg_replace('/[òóôõöÒÓÔÕÖ]/'		, 'o', $query);
		$query = preg_replace('/[ùúûüÙÚÛÜ]/'		, 'u', $query);
		$query = preg_replace('/[çÇ]/m'				, 'c', $query);
		return $query;
	} else {
		return reg_diacrit($query);
	}
}

// ----------------------------------------------------------------------------
//	fonction sur les dates
// ----------------------------------------------------------------------------
// today() : retourne la date du jour au format MySQL-DATE
// penser à mettre à jour les classes concernées
function today() {
	$jour = date('Y-m-d');
	return $jour;
	}

// ----------------------------------------------------------------------------
//	fonction qui retourne le nom de la page courante (SANS L'EXTENSION .php) !
// ----------------------------------------------------------------------------
function current_page() {
	return str_replace("/", "", preg_replace("#\/.*\/(.*\.php)$#", "\\1", $_SERVER["PHP_SELF"]));
	}

// ----------------------------------------------------------------------------
//	fonction gen_liste qui génère des combo_box super sympas
// ----------------------------------------------------------------------------
function gen_liste ($requete, $champ_code, $champ_info, $nom, $on_change, $selected, $liste_vide_code, $liste_vide_info,$option_premier_code,$option_premier_info) {
	$resultat_liste=pmb_mysql_query($requete);
	$renvoi="<select name=\"$nom\"  id=\"$nom\" onChange=\"$on_change\">\n";
	$nb_liste=pmb_mysql_num_rows($resultat_liste);
	if ($nb_liste==0) {
		$renvoi.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n";
		} else {
			if ($option_premier_info!="") {
				$renvoi.="<option value=\"$option_premier_code\" ";
				if ($selected==$option_premier_code) $renvoi.="selected='selected'";
				$renvoi.=">$option_premier_info</option>\n";
				}
			$i=0;
			while ($i<$nb_liste) {
				$renvoi.="<option value=\"".pmb_mysql_result($resultat_liste,$i,$champ_code)."\" ";
				if ($selected==pmb_mysql_result($resultat_liste,$i,$champ_code)) $renvoi.="selected";
				$renvoi.=">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n";
				$i++;
				}
			}
	$renvoi.="</select>\n";
	return $renvoi;
	}

// ----------------------------------------------------------------------------
//	fonction qui retourne le nom de la page courante (SANS L'EXTENSION .php) !
// ----------------------------------------------------------------------------
function inslink($texte="", $lien="",$param="") {
	if ($lien) return "<a href='$lien' $param>$texte</a>" ;
	else return "$texte" ;
}

// ----------------------------------------------------------------------------
//	fonction qui insère l'entrée $entree dans un table si image possible avec le $code
// ----------------------------------------------------------------------------
function do_image(&$entree, $code, $depliable ) {
	global $charset;
	global $opac_show_book_pics ;
	global $opac_book_pics_url ;
	global $opac_book_pics_msg;
	global $opac_url_base ;
	$image = "" ;
	if ($code <> "") {
		if ($opac_show_book_pics=='1' && $opac_book_pics_url) {
			$url_image = getimage_url($code, "");
			$title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
			if ($depliable) {
				$image = "<img src='".get_url_icon('vide.png', 1)."' title='".$title_image_ok."' class='align_right' hspace='4' vspace='2' vigurl='".$url_image."' >";
			} else {
				$image = "<img src='".$url_image."' title=\"".$title_image_ok."\" class='align_right' hspace='4' vspace='2'>";
			}
		}
	}

	if ($image) {
		$entree = "<table  style='width:100%'><tr><td>$entree</td><td style='vertical-align:top' class='align_right'>$image</td></tr></table>" ;
	} else {
		$entree = "<table  style='width:100%'><tr><td>$entree</td></tr></table>" ;
	}
}

// ------------------------------------------------------------------
//  pmb_preg_match($regex,$chaine) : recherche d'une regex
// ------------------------------------------------------------------
function pmb_preg_match($regex,$chaine) {
	global $charset;
	if ($charset != 'utf-8') {
		return preg_match($regex,$chaine);
	}
	else {
		return preg_match($regex.'u',$chaine);
	}
}

// ------------------------------------------------------------------
//  pmb_preg_grep($regex,$chaine) : recherche d'une regex
// ------------------------------------------------------------------
function pmb_preg_grep($regex,$chaine) {
	global $charset;
	if ($charset != 'utf-8') {
		return preg_grep($regex,$chaine);
	}
	else {
		return preg_grep($regex.'u',$chaine);
	}
}

// ------------------------------------------------------------------
//  pmb_preg_replace($regex,$replace,$chaine) : remplacement d'une regex par une autre
// ------------------------------------------------------------------
function pmb_preg_replace($regex,$replace,$chaine) {
	global $charset;
	if ($charset != 'utf-8') {
		return preg_replace($regex,$replace,$chaine);
	}
	else {
		return preg_replace($regex.'u',$replace,$chaine);
	}
}

// ------------------------------------------------------------------
//  pmb_str_replace($toreplace,$replace,$chaine) : remplacement d'une chaine par une autre
// ------------------------------------------------------------------
function pmb_str_replace($toreplace,$replace,$chaine) {
	global $charset;
	if ($charset != 'utf-8') {
		return str_replace($toreplace,$replace,$chaine);
	}
	else {
		return preg_replace("/".$toreplace."/u",$replace,$chaine);
	}
}

// ------------------------------------------------------------------
//  pmb_split($separateur,$string) : sépare un chaine de caractère selon un separateur
// ------------------------------------------------------------------
function pmb_split($separateur,$chaine) {
	global $charset;
	if ($charset != 'utf-8') {
		return preg_split($separateur,$chaine);
	}
	else {
		return mb_split($separateur,$chaine);
	}
}

/*
 * ------------------------------------------------------------------
 * pmb_alphabetic($regex,$replace,$string) : enleve les caracteres non alphabetique. Equivalent de [a-z0-9]
 *
 * Pour les caracteres latins;
 * Pour l'instant pour les caracteres non latins:
 * Armenien :
 * \x{0531}-\x{0587}\x{fb13}-\x{fb17}
 * Arabe :
 * \x{0621}-\x{0669}\x{066E}-\x{06D3}\x{06D5}-\x{06FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}
 * Cyrillique :
 * \x{0400}-\x{0486}\x{0488}-\x{0513}
 * Chinois :
 * \x{4E00}-\x{9BFF}
 * Japonais (Hiragana - Katakana - Suppl. phonetique katakana - Katakana demi-chasse) :
 * \x{3040}-\x{309F}\x{30A0}-\x{30FF}\x{31F0}-\x{31FF}\x{FF00}-\x{FFEF}
 * Grec :
 * \x{0386}\x{0388}-\x{038A}\x{038C}\x{038E}-\x{03A1}\x{03A3}-\x{03CE}\x{03D0}\x{03FF}\x{1F00}-\x{1F15}\x{1F18}-\x{1F1D}\x{1F20}-\x{1F45}\x{1F48}-\x{1F4D}\x{1F50}-\x{1F57}\x{1F59}\x{1F5B}\x{1F5D}\x{1F5F}-\x{1F7D}\x{1F80}-\x{1FB4}\x{1FB6}-\x{1FBC}\x{1FC2}-\x{1FC4}\x{1FC6}-\x{1FCC}\x{1FD0}-\x{1FD3}\x{1FD6}-\x{1FDB}\x{1FE0}-\x{1FEC}\x{1FF2}-\x{1FF4}\x{1FF6}-\x{1FFC}
 * Géorgien
 * \x{10A0}-\x{10C5}\x{10D0}-\x{10FC}\x{2D00}-\x{2D25}
 * ------------------------------------------------------------------
 */

function pmb_alphabetic($regex,$replace,$string) {
	global $charset;

	if ($charset != 'utf-8') {
		return preg_replace('/['.$regex.']/', $replace, $string);
	} else {
		/*return preg_replace('/['.$regex
				.'\x{0531}-\x{0587}\x{fb13}-\x{fb17}'
				.'\x{0621}-\x{0669}\x{066E}-\x{06D3}\x{06D5}-\x{06FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}'
				.'\x{0400}-\x{0486}\x{0488}-\x{0513}'
				.'\x{4E00}-\x{9BFF}'
				.'\x{3040}-\x{309F}\x{30A0}-\x{30FF}\x{31F0}-\x{31FF}\x{FF00}-\x{FFEF}'
				.'\x{0386}\x{0388}-\x{038A}\x{038C}\x{038E}-\x{03A1}\x{03A3}-\x{03CE}\x{03D0}\x{03FF}\x{1F00}-\x{1F15}\x{1F18}-\x{1F1D}\x{1F20}-\x{1F45}\x{1F48}-\x{1F4D}\x{1F50}-\x{1F57}\x{1F59}\x{1F5B}\x{1F5D}\x{1F5F}-\x{1F7D}\x{1F80}-\x{1FB4}\x{1FB6}-\x{1FBC}\x{1FC2}-\x{1FC4}\x{1FC6}-\x{1FCC}\x{1FD0}-\x{1FD3}\x{1FD6}-\x{1FDB}\x{1FE0}-\x{1FEC}\x{1FF2}-\x{1FF4}\x{1FF6}-\x{1FFC}'
				.'\x{10A0}-\x{10C5}\x{10D0}-\x{10FC}\x{2D00}-\x{2D25}'
				.']/u', ' ', $string);*/
		return preg_replace('/['.$regex.'\p{L}]/u', $replace, $string);//MB 28/10/14: http://www.regular-expressions.info/unicode.html
	}
}

// ------------------------------------------------------------------
//  pmb_strlen($string) : calcule la longueur d'une chaine pour utf-8 il s'agit du nombre de caractères.
// ------------------------------------------------------------------
function pmb_strlen($string) {
	global $charset;

	if ($charset != 'utf-8')
		return strlen($string);
	else {
		return mb_strlen($string,$charset);
	}
}

// ------------------------------------------------------------------
//  pmb_getcar($currentcar,$string) : recupere le caractere $cuurentcar de la chaine
// ------------------------------------------------------------------
function pmb_getcar($currentcar,$string) {
	global $charset;

	if (!isset($string[$currentcar])) return '';
	if ($charset != 'utf-8')
		return $string[$currentcar];
	else {
		return mb_substr($string,$currentcar, 1,$charset);
	}
}

// ------------------------------------------------------------------
//  pmb_substr($chaine,$depart,$longueur) : recupere n caracteres
// ------------------------------------------------------------------
function pmb_substr($chaine,$depart,$longueur=0) {
	global $charset;

	if ($charset != 'utf-8') {
		if ($longueur == 0)
			return substr($chaine,$depart);
		else
			return substr($chaine,$depart,$longueur);
	}
	else {
		if ($longueur == 0)
			return mb_substr($chaine,$depart,$charset);
		else
			return mb_substr($chaine,$depart,$longueur,$charset);
	}
}

// ------------------------------------------------------------------
//  pmb_strtolower($string) : passage d'une chaine de caractère en minuscule
// ------------------------------------------------------------------
function pmb_strtolower($string) {
	global $charset;
	if ($charset != 'utf-8') {
		return strtolower($string);
	}
	else {
		return mb_strtolower($string,$charset);
	}
}

// ------------------------------------------------------------------
//  pmb_strtoupper($string) : passage d'une chaine de caractère en majuscule
// ------------------------------------------------------------------
function pmb_strtoupper($string) {
	global $charset;
	if ($charset != 'utf-8') {
		return strtoupper($string);
	}
	else {
		return mb_strtoupper($string,$charset);
	}
}

// ------------------------------------------------------------------
//   pmb_substr_replace($string,$replacement,$start,$length=null) : remplace un segment de la chaîne string par la chaîne replacement. Le segment est délimité par start et éventuellement par length
// ------------------------------------------------------------------
function pmb_substr_replace($string,$replacement,$start,$length=null) {
	global $charset;
	if($length === null){
		$length=pmb_strlen($string);
	}
	if ($charset != 'utf-8'){
		return substr_replace($string, $replacement, $start,$length);
	}else{
		$result  = mb_substr ($string, 0, $start, $charset);
	    $result .= $replacement;
	    if ($length > 0)
	    {
	        $result .= mb_substr($string, ($start + $length), null, $charset);
	    }
	    return $result;
	}
}

// ------------------------------------------------------------------
//  pmb_escape() : renvoi la bonne fonction javascript en fonction du charset
// ------------------------------------------------------------------
function pmb_escape() {
	global $charset;
	if ($charset != 'utf-8') {
		return "escape";
	}
	else {
		return "encodeURIComponent";
	}
}

// ------------------------------------------------------------------
//  pmb_bidi($string) : renvoi la chaine de caractere en gérant les problemes
//  d'affichage droite gauche des parenthèses
// ------------------------------------------------------------------
function pmb_bidi($string) {
	global $charset;
	global $lang;

	return $string;

	if ($charset != 'utf-8' or $lang == 'ar') {
		// utf-8 obligatoire pour l'arabe
		return $string;
	}
	else {
		//\x{0600}-\x{06FF}\x{0750}-\x{077F} : Arabic
		//x{0590}-\x{05FF} : hebrew
		if (preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{0590}-\x{05FF}]/u', $string)) {

			// 1 - j'entoure les caractères arabes + espace ou parenthese ou chiffre de <span dir=rtl>'
			 $string = preg_replace("/([\s*(&nbsp;)*(&amp;)*\-*\(*0-9*]*[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{0590}-\x{05FF}]+([,*\s*(&nbsp;)*(&amp;)*\-*\(*0-9*]*[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{0590}-\x{05FF}]*[,*\s*(&nbsp;)*(&amp;)*\-*\)*0-9*]*)*)/u","<span dir='rtl'>\\1</span>",$string);
			 // 2 - j'enleve les span dans les 'value' ca marche pas dans les ecrans de saisie
			 $string = preg_replace('/value=[\'\"]<span dir=\'rtl\'>(.*?)<\/span>[\'\"]/u','value=\'\\1\'',$string);
			 // 3 - j'enleve les span dans les 'title'
			 $string = preg_replace('/title=[\'\"]<span dir=\'rtl[\'\"]>(.*?)<\/span>/u','title=\'\\1',$string);
			 // 4 - j'enleve les span dans les 'alt'
			 $string = preg_replace('/alt=[\'\"]<span dir=\'rtl[\'\"]>(.*?)<\/span>/u','alt=\'\\1',$string);
			 // 4 - j'enleve les span sont entre cote, c'est que c'est dans une valeur.
			 $string = preg_replace('/[\'\"]<span dir=\'rtl[\'\"]>(.*?)<\/span>\'/u','\'\\1\'',$string);
			 // 4 - j'enleve les span dans les textarea.
			 //preg_match('/<textarea(.*?)><span dir=\'rtl[\'\"](.*?)<\/span>/u',$string,$toto);
			 //printr($toto);
			 $string = preg_replace('/<textarea(.*?)><span dir=\'rtl[\'\"](.*?)<\/span>/u','<textarea \\1 \\2',$string);
			 return $string;
		}
		else {
			return $string;
		}

	}
}

function gen_plus_form($id, $titre, $contenu,$startopen=false) {
	global $msg;
	return "
		<div class='row'></div>
		<div id='$id' class='notice-parent'>
			<img src='./getgif.php?nomgif=plus' name='imEx' id='$id" . "Img' title='".addslashes($msg['plus_detail'])."' style='border:0px' onClick=\"expandBase('$id', true); return false;\" hspace='3'>
			<span class='notice-heada'>
				$titre
			</span>
		</div>
		<div id='$id" . "Child' class='notice-child' ".($startopen?"startOpen='Yes' ":"")."style='margin-bottom:6px;display:none;width:94%'>
			$contenu
		</div>
		";
}

// ------------------------------------------------------------------
//  pmb_sql_value($string) : renvoie la valeur de l'unique colonne (ou uniquement de la premiere) de la requete $rqt
// ------------------------------------------------------------------
function pmb_sql_value($rqt) {
	if($result=pmb_mysql_query($rqt))
		if($row = pmb_mysql_fetch_row($result))	return $row[0];
	return '';
}

// ------------------------------------------------------------------
//  mail_bloc_adresse() : renvoie un code HTML contenant le bloc d'adresse à mettre en bas
//  des mails envoyés par PMB (résa, prêts)
// ------------------------------------------------------------------
function mail_bloc_adresse() {
	global $msg ;
	global $biblio_name, $biblio_email,$biblio_website ;
	global $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_phone ;
	$ret = $biblio_name ;
	if ($biblio_adr1) $ret .= "<br />".$biblio_adr1 ;
	if ($biblio_adr2) $ret .= "<br />".$biblio_adr2 ;
	if ($biblio_cp && $biblio_town) $ret .= "<br />".$biblio_cp." ".$biblio_town ;
	elseif ($biblio_town) $ret .= "<br />".$biblio_cp." ".$biblio_town ;
	if ($biblio_phone) $ret .= "<br />".$msg['location_details_phone']." ".$biblio_phone ;
	if ($biblio_email) $ret .= "<br />".$msg['location_details_email']." ".$biblio_email ;
	if ($biblio_website) $ret .= "<br />".$msg['location_details_website']." <a href='".$biblio_website."'>".$biblio_website."</a>" ;

	return $ret ;
}

//---------------------------------
//CONFIGURATION DU PROXY POUR CURL
//---------------------------------

function configurer_proxy_curl(&$curl,$url_asked=''){
	global $opac_curl_proxy,$curl_addon_array_options,$curl_addon_array_exclude_proxy;

	/*
	* petit hack pour définir des options supplémentaires à curl
	* les deux tableaux suivants peuvent être définis dans un fichier pmb/opac_css/includes/opac_config_local.inc.php (attention, à reporter en gestion 'config_local.inc.php')
	*
	* Exemple $curl_addon_array_options
	*
	* $curl_addon_array_options = array(
	* 		CURLOPT_POST => 1,
	* 		CURLOPT_HEADER => false,
	* 		CURLOPT_POSTFIELDS => $data,
	*       CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4 // Pour forcer la résolution en IPV4
	* );
	*
	* Exemple $curl_addon_array_exclude_proxy
	*
	* $curl_addon_array_exclude_proxy = array(
	* 		"domain1.com",
	* 		"domain2.com"
	* );
	*
	*/

	if(is_array($curl_addon_array_options) && count($curl_addon_array_options)){
		curl_setopt_array($curl, $curl_addon_array_options);
	}

	$use_proxy = true;
	if(trim($url_asked) && is_array($curl_addon_array_exclude_proxy) && count($curl_addon_array_exclude_proxy)){
		foreach($curl_addon_array_exclude_proxy as $domain){
			$domain = str_replace('.','\.',$domain);
			$domain = str_replace('/','\/',$domain);
			if(preg_match('`'.$domain.'`', $url_asked)){
				$use_proxy = false;
				break;
			}
		}
	}

	if($use_proxy){
		if($opac_curl_proxy!=''){
			$param_proxy = explode(',',$opac_curl_proxy);
			$adresse_proxy = $param_proxy[0];
			$port_proxy = $param_proxy[1];
			$user_proxy = $param_proxy[2];
			$pwd_proxy = $param_proxy[3];

			curl_setopt($curl, CURLOPT_PROXY, $adresse_proxy);
			curl_setopt($curl, CURLOPT_PROXYPORT, $port_proxy);
			curl_setopt($curl, CURLOPT_PROXYUSERPWD, "$user_proxy:$pwd_proxy");
		}
	}

}

//remplacement espace insécable 0xA0: &nbsp; Non-breaking space => problème lié à certaine version de navigateur
function clean_nbsp($input) {
    global $charset;
    //if($charset=="iso-8859-1")$input = str_replace(chr(0xa0), ' ', $input);
    $input = html_entity_decode(str_replace('&nbsp;',' ',htmlentities($input,ENT_QUOTES,$charset)),ENT_QUOTES,$charset);
    return $input;
}

function addslashes_array($input_arr){
    if(is_array($input_arr)){
        $tmp = array();
        foreach ($input_arr as $key1 => $val){
            $tmp[$key1] = addslashes_array($val);
        }
        return $tmp;
    }
    else {
    	if (is_string($input_arr))
        	return addslashes($input_arr);
        else
        	return $input_arr;
    }
}

function stripslashes_array($input_arr){
    if(is_array($input_arr)){
        $tmp = array();
        foreach ($input_arr as $key1 => $val){
            $tmp[$key1] = stripslashes_array($val);
        }
        return $tmp;
    }
    else {
    	if (is_string($input_arr))
        	return stripslashes($input_arr);
        else
        	return $input_arr;
    }
}

function console_log($msg_to_log){
	print "<script type='text/javascript'>if(typeof console != 'undefined') {console.log('".addslashes($msg_to_log)."');}</script>";
}

function parseHTML($buffer){
	$htmlparser=new parse_format("inhtml.inc.php");
	$htmlparser->cmd = $buffer;
	return $htmlparser->exec_cmd(true);
}

function gen_plus($id, $titre, $contenu, $maximise=0, $script_before='', $script_after='', $class_parent='notice-parent', $class_child='notice-child') {
	global $msg;
	if($maximise) $max=" startOpen=\"Yes\""; else $max='';
	return "
	<div class='row'></div>
	<div id='$id' class='".$class_parent."'>
		<img src='./getgif.php?nomgif=plus' class='img_plus' name='imEx' id='$id"."Img' title='".$msg['plus_detail']."' style='border:0px' onClick=\" $script_before expandBase('$id', true); $script_after return false;\" hspace='3'>
		<span class='notice-heada'>
			$titre
		</span>
	</div>
	<div id='$id"."Child' class='".$class_child."' style='margin-bottom:6px;display:none;width:94%' $max>
		$contenu
	</div>
	";
}

function pmb_utf8_decode($elem){
	if(is_array($elem)){
		foreach ($elem as $key =>$value){
			$elem[$key] = pmb_utf8_decode($value);
		}
	}else if(is_object($elem)){
		$elem = pmb_obj2array($elem);
		$elem = pmb_utf8_decode($elem);
	}elseif(function_exists("mb_convert_encoding")){
		$elem = mb_convert_encoding($elem,"Windows-1252","UTF-8");
	}else{
		$elem = utf8_decode($elem);
	}
	return $elem;
}

function pmb_utf8_encode($elem){
	if(is_array($elem)){
		foreach ($elem as $key =>$value){
			$elem[$key] = pmb_utf8_encode($value);
		}
	}else if(is_object($elem)){
		$elem = pmb_obj2array($elem);
		$elem = pmb_utf8_encode($elem);
	}elseif(function_exists("mb_convert_encoding")){
		$elem = mb_convert_encoding($elem,"UTF-8","Windows-1252");
	}else{
		$elem=utf8_encode($elem);
	}

	return $elem;
}

function pmb_utf8_array_encode($elem){
	global $charset;
	if($charset != "utf-8"){
		return pmb_utf8_encode($elem);
	}else{
		return $elem;
	}
}

function pmb_utf8_array_decode($elem){
	global $charset;
	if($charset != "utf-8"){
		return pmb_utf8_decode($elem);
	}else{
		return $elem;
	}
}

function pmb_obj2array($obj){
	$array = array();
	if(is_object($obj)){
		foreach($obj as $key => $value){
			if(is_object($value)){
				$value = pmb_obj2array($value);
			}
			$array[$key] = $value;
		}
	}else{
		$array = $obj;
	}
	return $array;
}

//------like print_r but more readable--for debugging purposes
function printr($arr,$filter="",$name="") {
	//array_shift($args) ;
	print "<pre>\n" ;
	if ($name) {
		print "Printing content of array <b>$name:</b>\n";
	}
	if ($filter == "" || ! is_array($arr) ) {
		print_r($arr) ;
	} else {
		if (is_array($arr)) {
				ksort($arr);
				foreach($arr as $key => $val) {
					if (preg_match("#$filter#", $key) || preg_match("#$filter#", $val) ) {
						print "[" . $key . "] => " . $val ."\n" ;
					}
				}
		}
	}

	print "</pre>";
	return ;
}

function get_msg_to_display($message) {
	global $msg;

	if (substr($message, 0, 4) == "msg:") {
		if(isset($msg[substr($message, 4)])){
			return $msg[substr($message, 4)];
		}
	}
	return $message;
}

/**
 * Enregistre dans la session
 */
function add_value_session($code,$value, $in_array = true) {
	$session_none = false;
	if ((function_exists('session_status') && session_status() == 1) || (session_id() === '')) {
		$session_none = true;
		session_start();
	}
	if ($in_array) {
		if (is_array($_SESSION[$code]) && count($_SESSION[$code])) {
			if(!in_array($value, $_SESSION[$code])) {
				$_SESSION[$code][] = $value;
			}
		} else {
			$_SESSION[$code] = array($value);
		}
	} else {
		$_SESSION[$code] = $value;
	}
	if ($session_none) {
		session_write_close();
	}
}

/**
 * Génération d'un log
 */
function generate_log() {
	global $dbh,$log, $infos_notice, $infos_expl;

	if($_SESSION['user_code']) {
		$res=pmb_mysql_query($log->get_empr_query());
		if($res){
			$empr_carac = pmb_mysql_fetch_array($res);
			$log->add_log('empr',$empr_carac);
		}
	}

	$log->add_log('num_session',session_id());
	$log->add_log('expl',$infos_expl);
	$log->add_log('docs',$infos_notice);

	$log->save();
}


/**
 * Charge et lance la compression d'un fichier CSS
 */
function loadandcompresscss($file){
	global $opac_default_style;
	$relocate = true;
	if(strpos($file,"?") && strpos($file,"./styles/".$opac_default_style."/") === false && strpos($file,"./styles/common/") === false){
		$aCurl = new Curl();
		$content = $aCurl->get($file);
		$content=$content->body;
		$relocate = false;
	}else{
		if(strpos($file,"?")){
			$file = substr($file,0,strpos($file,"?"));
		}
		$content = file_get_contents($file);
	}
	return compresscss($content, $file,$relocate);

}

/**
 * Compression d'un fichier CSS
 */
function compresscss($content,$file,$relocate=true){
	if(preg_match_all("!@import\s+url\(['\"]([^'^\"]*)['\"]\);!",$content,$matches)){
		$css_filepath = dirname($file);
		for($i=0 ; $i< count($matches[0]) ; $i++){
			$content = str_replace($matches[0][$i],loadandcompresscss($css_filepath."/".$matches[1][$i]),$content);
		}
	}


	if($relocate && preg_match_all("!url\(['\"]?([^'^\")]+)['\"]?\)!",$content,$matches)){
		for($i=0 ; $i< count($matches[0]) ; $i++){
			$target = $matches[1][$i];
			$target = ".".dirname($file)."/".str_replace(".".dirname($file)."/","",$target);
			$content = str_replace($matches[0][$i],"url('".$target."')",$content);
		}
	}
	$content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
	// Remove space after colons
	$content = preg_replace('!\s?:\s?!', ':', $content);
	// Remove whitespace
	$content = str_replace(array("\r", "\n\n", "\t",), '', $content);
	return $content;
}

function get_url_icon($icon, $use_opac_url_base=0) {
	global $base_path;
	global $opac_url_base, $css;

	if($use_opac_url_base) $url_base = $opac_url_base;
	else $url_base = $base_path."/";

	$icon_name = str_replace(array('.svg', '.png', '.jpg', '.gif'), '', $icon);

	// on cherche celle du style, du common, sinon celle par défaut
	if($url = search_url_icon_type("styles/".$css."/images/".$icon_name)){
		return $url_base.$url;
	}
	if($url = search_url_icon_type("styles/common/images/".$icon_name)){
		return $url_base.$url;
	}
	if($url = search_url_icon_type("images/".$icon_name)){
		return $url_base.$url;
	}
	return $url_base."images/".$icon;

}

function search_url_icon_type($icon) {
	global $base_path;

	if(file_exists($base_path.'/'.$icon.'.svg')) {
		return $icon.'.svg';
	}
	if(file_exists($base_path.'/'.$icon.'.png')) {
		return $icon.'.png';
	}
	if(file_exists($base_path.'/'.$icon.'.jpg')) {
		return $icon.'.jpg';
	}
	if(file_exists($base_path.'/'.$icon.'.gif')) {
		return $icon.'.gif';
	}
	return '';
}

function gen_where_in($field, $elts, &$table_tempo_name=''){
	global $dbh;
	global $memo_tempo_table_to_rebuild;

	if(!isset($memo_tempo_table_to_rebuild)) $memo_tempo_table_to_rebuild = array();

	if(!is_array($elts)) {
		$elts = str_replace("'", '', $elts);
		$elts = str_replace('"', '', $elts);
		$elts = explode(',', $elts);
	}
	if(!count($elts)) $elts = array();
	if(!$table_tempo_name) $table_tempo_name = 'where_in_table'.md5(uniqid("",true));
	$field_id = 'where_in_id';

	$rqt = 'create temporary table IF NOT EXISTS '.$table_tempo_name.' ('.$field_id.' int, index using btree('.$field_id.')) engine=memory ';
	pmb_mysql_query($rqt,$dbh);
	$memo_tempo_table_to_rebuild[] = $rqt;
	if(count($elts)) {
		$rqt = 'INSERT INTO '.$table_tempo_name.' ('.$field_id.') VALUES ('.implode('),(',$elts).')';
		$memo_tempo_table_to_rebuild[] = $rqt;
		pmb_mysql_query($rqt,$dbh);
	}
	$field_id = $table_tempo_name.'.'.$field_id;
	return ' join '.$table_tempo_name.' on '.$field.'='.$field_id.' ';
}

function gen_where_in_string($field, $elts){

	if(!$elts) return '';
	if(!is_array($elts)) {
		$elts = str_replace("'", '', $elts);
		$elts = str_replace('"', '', $elts);
		$elts = explode(',', $elts);
		if(!count($elts)) return '';
	}

	$prefix = str_replace('.', '', $field);

	$query = " inner join (select '".$elts[0]."' as ".$prefix."x_";

	for($i=1; $i<count($elts); $i++) {
		$query.= " union all select '".$elts[$i]."'";
	}
	return $query.") as ".$prefix."x_where_in on ".$field." = ".$prefix."x_where_in.".$prefix."x_ ";
}

function aff_pagination ($url_base="", $nbr_lignes=0, $nb_per_page=0, $page=0, $etendue=10, $aff_nb_per_page=false, $aff_extr=false ) {

	global $msg,$charset, $base_path;
	global $opac_items_pagination_custom;

	$nbepages = ceil($nbr_lignes/$nb_per_page);
	$suivante = $page+1;
	$precedente = $page-1;
	$deb = $page - $etendue ;
	if ($deb<1) $deb=1;
	$fin = $page + $etendue ;
	if($fin>$nbepages)$fin=$nbepages;

	$nav_bar = "";

	if ($aff_nb_per_page) {
		$nav_bar = "<div class='left' ><input type='text' name='nb_per_page' id='nb_per_page' class='saisie-2em' value='".$nb_per_page."' />&nbsp;".htmlentities($msg['1905'], ENT_QUOTES, $charset)."&nbsp;";
		$nav_bar.= "<input type='button' class='bouton' value='".$msg['actualiser']."' ";
		$nav_bar.="onclick=\"try{
			var page=".$page.";
			var old_nb_per_page=".$nb_per_page.";
			var nbr_lignes=".$nbr_lignes.";
			var new_nb_per_page=document.getElementById('nb_per_page').value;
			var new_nbepages=Math.ceil(nbr_lignes/new_nb_per_page);
			if(page>new_nbepages) page=new_nbepages;
			document.location='".$url_base."&page='+page+'&nbr_lignes=".$nbr_lignes."&nb_per_page='+new_nb_per_page;
		}catch(e){}; \" /></div>";
	}

	if($aff_extr && (($page-$etendue)>1) ) {
		$nav_bar .= "<a class='pagination_first' id='premiere' href='".$url_base."&page=1&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page."' ><img src='".get_url_icon('first.gif')."' style='border:0px' alt='".$msg['first_page']."' hspace='6' class='align_middle' title='".$msg['first_page']."' /></a>";
	}

	// affichage du lien precedent si necessaire
	if($precedente > 0) {
		$nav_bar .= "<a class='pagination_left' id='precedente' href='".$url_base."&page=".$precedente."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page."' ><img src='".get_url_icon('left.gif')."' style='border:0px' alt='".$msg[48]."' hspace='6' class='align_middle' title='".$msg[48]."' /></a>";
	}

	for ($i = $deb; ($i <= $nbepages) && ($i<=$page+$etendue) ; $i++) {
		if($i==$page) {
			$nav_bar .= "<strong>".$i."</strong>";
		} else {
			$nav_bar .= "<a class='pagination_page' href='".$url_base."&page=".$i."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page."' >".$i."</a>";
		}
		if($i<$nbepages) $nav_bar .= " ";
	}


	if ($suivante<=$nbepages) {
		$nav_bar .= "<a class='pagination_right' href='".$url_base."&page=".$suivante."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page."' ><img src='".get_url_icon('right.gif')."' style='border:0px' alt='".$msg[49]."' hspace='6' class='align_middle' title='".$msg[49]."' /></a>";
	}

	if($aff_extr && (($page+$etendue)<$nbepages) ) {
		$nav_bar .= "<a class='pagination_last' id='derniere' href='".$url_base."&page=".$nbepages."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page."' ><img src='".get_url_icon('last.gif')."' style='border:0px' alt='".$msg['last_page']."' hspace='6' class='align_middle' title='".$msg['last_page']."' /></a>";
	}

	$start_in_page = ((($page-1)*$nb_per_page)+1);
	if(($start_in_page + $nb_per_page) > $nbr_lignes) {
		$end_in_page = $nbr_lignes;
	} else {
		$end_in_page = ((($page-1)*$nb_per_page)+$nb_per_page);
	}
	$nav_bar .= " (".$start_in_page." - ".$end_in_page." / ".$nbr_lignes.")";

	$pagination_nav_bar = "";
	if($opac_items_pagination_custom) {
		$pagination_custom = explode(',', $opac_items_pagination_custom);
		if(count($pagination_custom)) {
			$max_nb_elements = 0;
			$nb_first_custom_element = $pagination_custom[0];
			foreach ($pagination_custom as $nb_elements) {
				$nb_elements = trim($nb_elements)+0;
				if($nb_first_custom_element <= $nbr_lignes) {
					if($nb_elements == $nb_per_page) $pagination_nav_bar .= "<b>";
					$pagination_nav_bar .= " <a class='pagination_custom' id='pagination_custom' href='".$url_base."&page=1&nbr_lignes=".$nbr_lignes."&nb_per_page_custom=".$nb_elements."' >".$nb_elements."</a> ";
					if($nb_elements == $nb_per_page) $pagination_nav_bar .= "</b>";
				}
				if($nb_elements > $max_nb_elements) {
					$max_nb_elements = $nb_elements;
				}
			}
			if(($max_nb_elements > $nbr_lignes) && ($nb_per_page < $nbr_lignes)) {
				$pagination_nav_bar .= " <a class='pagination_custom' id='pagination_custom' href='".$url_base."&page=1&nbr_lignes=".$nbr_lignes."&nb_per_page_custom=".$nbr_lignes."' >".$msg['tout_afficher']."</a> ";
			}
			if($pagination_nav_bar) {
				$pagination_nav_bar = "<span style='float:right;'> ".$msg['per_page']." ".$pagination_nav_bar."</span>";
			}
		}
	}
	$nav_bar = "<div class='center'>".$nav_bar.$pagination_nav_bar."</div>";
	return $nav_bar ;
}

function pmb_base64_encode($elem){
	if(is_array($elem)){
		foreach ($elem as $key =>$value){
			$elem[$key] = pmb_base64_encode($value);
		}
	}else if(is_object($elem)){
		$elem = pmb_obj2array($elem);
		$elem = pmb_base64_encode($elem);
	}else{
		$elem = base64_encode($elem);
	}

	return $elem;
}

function pmb_base64_decode($elem){
	if(is_array($elem)){
		foreach ($elem as $key =>$value){
			$elem[$key] = pmb_base64_decode($value);
		}
	}else if(is_object($elem)){
		$elem = pmb_obj2array($elem);
		$elem = pmb_base64_decode($elem);
	}else{
		$elem = base64_decode($elem);
	}
	return $elem;
}

function curl_load_opac_file($url, $filename) {

    global $opac_curl_available, $base_path, $opac_url_base ;
	//Calcul des URLs subst
	$url_subst=str_replace(".xml","_subst.xml",$url);
	$filename_subst=str_replace(".xml","_subst.xml",$filename);

	$file_copied =false;
	$subst_file_copied = false;

	//Si CURL est disponible en OPAC
	if($opac_curl_available) {
		$curl = new Curl();

		// A revoir, devrait etre integre a la fonction "configurer_proxy_curl"
		$curl->set_option('CURLOPT_SSL_VERIFYPEER',  false);

		$curl->set_option('CURLOPT_TIMEOUT',  5);

		$resp = $curl->get($url);
        if( !$curl->error() && $resp->headers['Status-Code'] !== '401' && (stripos($resp->headers['Status'], '401 Unauthorized')=== false)  ) {
			$file_copied = file_put_contents($filename, $resp);
		}

		$resp = $curl->get($url_subst);
		if($resp->headers['Status-Code'] == '404' || (stripos($resp->headers['Status'], '404 not found')!==false) ) {
			$subst_file_copied = true;
        } else if(!$curl->error() && $resp->headers['Status-Code'] !== '401' && (stripos($resp->headers['Status'], '401 Unauthorized')=== false)) {
			$subst_file_copied = file_put_contents($filename_subst, $resp);
		}
	}

	//Copie directe si CURL echoue
	if(!$file_copied) {
        $file_path = "$base_path/".str_replace($opac_url_base, '', $url);
		if(file_exists($file_path)) {
			$file_copied = copy($file_path, $filename);
		}
	}

	if(!$file_copied) {
		return false;
	}

	if(!$subst_file_copied) {
        $subst_file_path = "$base_path/".str_replace($opac_url_base, '', $url_subst);
		if(file_exists($subst_file_path)) {
			$subst_file_copied = copy($subst_file_path, $filename_subst);
		}
	}

	return true;

}

function get_iso_lang_code($l='') {
	global $lang;
	if(!$l) $l = $lang;
	return substr($l, 0, 2);
}

function jscript_unload_question() {
    global $msg;
    return "
	<script type=\"text/javascript\">

	    function unload_off(){
	    	window.onbeforeunload = '';
	    }
	    function unload_on(){
	    	window.onbeforeunload = function(e){
	        	return '".$msg['message_unload_page']."';
	   		}
	    }
	    unload_on();
	</script>";
}

function cms_build_info($params = array()) {
    global $cms_build_activate, $base_path, $pageid, $lvl, $tab, $search_type_asked;    
    global $log, $infos_notice, $infos_expl, $nb_results_tab;
    global $cms_active, $class_path, $opac_compress_css, $charset, $footer, $opac_parse_html;
    
    $cms_build_info = $params;
    $cms_build_info_string = '';
    if ($cms_build_activate == -1) {
        unset($_SESSION["cms_build_activate"]);
    } elseif ($cms_build_activate || $_SESSION["cms_build_activate"]) { // issu de la gestion   
        if (isset($pageid) && $pageid) {
            require_once($base_path."/classes/cms/cms_pages.class.php");
            $cms_page = new cms_page($pageid);
            $cms_build_info['page'] = $cms_page->get_env();
        }
        $cms_build_info['session'] = $_SESSION;
        $cms_build_info['post'] = $_POST;
        $cms_build_info['get'] = $_GET;
        $cms_build_info['lvl'] = $lvl;
        $cms_build_info['tab'] = $tab;
        $cms_build_info['log'] = $log;
        $cms_build_info['infos_notice'] = $infos_notice;
        $cms_build_info['infos_expl'] = $infos_expl;
        $cms_build_info['nb_results_tab'] = $nb_results_tab;
        $cms_build_info['search_type_asked'] = $search_type_asked;
        $cms_build_info_string = "<input type='hidden' id='cms_build_info' name='cms_build_info' value='" . rawurlencode(serialize(pmb_base64_encode($cms_build_info))) . "' />";
        $cms_build_info_string.= "
        	<script type='text/javascript'>
        		if(window.top.window.cms_opac_loaded){
        			window.onload = function() {
        				window.top.window.cms_opac_loaded('" . $_SERVER['REQUEST_URI'] . "');
        			}
        		}
        	</script>
    	";        
        $_SESSION["cms_build_activate"] = "1";
    }	
    
    print str_replace("!!cms_build_info!!", $cms_build_info_string, $footer);	
    
    $htmltoparse = '';
    if($opac_parse_html || $cms_active){
        if($opac_parse_html){
            $htmltoparse= parseHTML(ob_get_contents());
        }else{
            $htmltoparse= ob_get_contents();
        }
        
        ob_end_clean();
        if ($cms_active) {
            require_once($base_path."/classes/cms/cms_build.class.php");
            $cms=new cms_build();
            $htmltoparse = $cms->transform_html($htmltoparse);
        }
        
        //Compression CSS
        if($opac_compress_css == 1 && !$cms_active){
            if($charset=='utf-8') $htmltoparse = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
                '|[\x00-\x7F][\x80-\xBF]+'.
                '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
                '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
                '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/',
                '?', $htmltoparse );
            $compressed_file_exist = file_exists("./temp/full.css");
            require_once($class_path."/curl.class.php");
            $dom = new DOMDocument();
            $dom->encoding = $charset;
            $dom->loadHTML($htmltoparse);
            $css_buffer = "";
            $links = $dom->getElementsByTagName("link");
            $dom_css = array();
            for($i=0 ; $i<$links->length ; $i++){
                $dom_css[] = $links->item($i);
                if(!$compressed_file_exist && $links->item($i)->hasAttribute("type") && $links->item($i)->getAttribute("type") == "text/css"){
                    $css_buffer.= loadandcompresscss(html_entity_decode($links->item($i)->getAttribute("href")));
                }
            }
            $styles = $dom->getElementsByTagName("style");
            for($i=0 ; $i<$styles->length ; $i++){
                $dom_css[] = $styles->item($i);
                if(!$compressed_file_exist){
                    $css_buffer.= compresscss($styles->item($i)->nodeValue,"");
                }
            }
            foreach($dom_css as $link){
                $link->parentNode->removeChild($link);
            }
            if(!$compressed_file_exist){
                file_put_contents("./temp/full.css",$css_buffer);
            }
            $link = $dom->createElement("link");
            $link->setAttribute("href", "./temp/full.css");
            $link->setAttribute("rel", "stylesheet");
            $link->setAttribute("type", "text/css");
            $dom->getElementsByTagName("head")->item(0)->appendChild($link);
            $htmltoparse = $dom->saveHTML();
        }else if (file_exists("./temp/full.css") && !$cms_active){
            unlink("./temp/full.css");
        }
        print $htmltoparse;
    }
}
