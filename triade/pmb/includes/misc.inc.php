<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc.inc.php,v 1.209 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once "$include_path/apache_functions.inc.php";
require_once "$class_path/curl.class.php";

if (!function_exists('is_countable')) {
	function is_countable($var) {
		return (is_array($var) || $var instanceof Countable);
	}
}

//Fonction pour gérer les images demandés par PMB
function getimage_cache($notice_id=0, $etagere_id=0, $authority_id=0, $vigurl=0, $noticecode=0, $url_image=0, $empr_pic=0, $cached_in_opac = 0){
	global $pmb_notice_img_folder_id, $pmb_authority_img_folder_id, $opac_url_base,$empr_pics_max_size, $dbh;

	global $pmb_img_cache_folder, $opac_img_cache_folder;

	if(!$cached_in_opac){
		$img_cache_folder = $pmb_img_cache_folder;
	}else{
		$img_cache_folder = $opac_img_cache_folder;
	}

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
		if($empr_pic && $empr_pics_max_size){
			$hash_image.=$empr_pics_max_size;
		}
		if($hash_image){
			$hash=md5($hash_image);
			$image_rep_cache=$img_cache_folder.$hash.".png";
			//Pour les images vides elles peuvent changer entre les PMB, la gestion et l'Opac
			$hash_img_empty=md5($hash_image.$opac_url_base.$cached_in_opac);
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

function getimage_url($code = "", $vigurl = "", $empr_pic = 0, $no_cache=false) {
	global $opac_url_base, $opac_book_pics_url, $pmb_book_pics_url, $pmb_opac_url, $pmb_url_base, $prefix_url_image;
	global $pmb_img_cache_folder, $pmb_img_cache_url, $opac_img_cache_folder, $opac_img_cache_url;
	global $use_opac_url_base;
	
	$url_return = $notice_id = $etagere_id = $authority_id = $noticecode = $url_image = "" ;

	if($empr_pic){
		$code = pmb_preg_replace('/ /', '', $code);
		$vigurl = str_replace("!!num_carte!!", $code, $vigurl) ;
		$url_image = "";
		$code = "";
	}

	if(isset($prefix_url_image) && $prefix_url_image && ($prefix_url_image != $pmb_opac_url) && ($prefix_url_image != $opac_url_base)){
		$url_image = $pmb_book_pics_url;
		$prefix = $prefix_url_image;
		$img_cache_folder = $pmb_img_cache_folder;
		$img_cache_url = $pmb_img_cache_url;
		$cached_in_opac = 0;
	}else{
		$url_image = $opac_book_pics_url;
		$prefix = $opac_url_base;
		$img_cache_folder = $opac_img_cache_folder;
		$img_cache_url = $opac_img_cache_url;
		$cached_in_opac = 1;
	}

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
		$manag_cache=getimage_cache($notice_id, $etagere_id, $authority_id, $vigurl, $noticecode, $url_image, $empr_pic, $cached_in_opac);
		$out=array();
		if($manag_cache["location"] && preg_match("#^".$img_cache_folder."(.+)$#",$manag_cache["location"],$out)){
			$url_return = $img_cache_url.$out[1];
		}
	}

	if(!$url_return){
		$url_return = $prefix."getimage.php?url_image=".urlencode($url_image)."&amp;noticecode=!!noticecode!!&amp;vigurl=".urlencode($vigurl) ;
		if(isset($empr_pic) && $empr_pic){
			$url_return .="&amp;empr_pic=1";
		}
		$url_return = str_replace("!!noticecode!!", $noticecode, $url_return) ;
	}
	return $url_return;
}

//Fonction de récupération d'une URL vignette
function get_vignette($notice_id, $no_cache=false) {
	global $opac_book_pics_url, $opac_show_book_pics;
	global $opac_url_base;

	$requete="select code,thumbnail_url from notices where notice_id=$notice_id";
	$res=pmb_mysql_query($requete);

	$url_image_ok=$opac_url_base."images/vide.png";

	if ($res) {
		$notice=pmb_mysql_fetch_object($res);
		if ($notice->code || $notice->thumbnail_url) {
			if ($opac_show_book_pics && ($opac_book_pics_url || $notice->thumbnail_url)) {
				$url_image_ok = getimage_url($notice->code, $notice->thumbnail_url, 0, $no_cache);
			}
		}
	}
	return $url_image_ok;
}

// ----------------------------------------------------------------------------
//	fonctions de formatage de chaine
// ----------------------------------------------------------------------------
// reg_diacrit : fonction pour traiter les caracteres accentues en recherche avec regex

// choix de la classe à utiliser pour envoi en pdf
if (!isset($fpdf)) {
	if ($charset != 'utf-8') $fpdf = 'FPDF'; else $fpdf = 'UFPDF';
}

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


//strip_empty_chars : enleve tout ce qui n'est pas alphabetique ou numerique d'une chaine
function strip_empty_chars($string) {
	// traitement des diacritiques
	$string = convert_diacrit($string);

	// Mis en commentaire : qu'en est-il des caracteres non latins ???
	// SUPPRIME DU COMMENTAIRE : ER : 12/05/2004 : ça fait tout merder...
	// RECH_14 : Attention : ici suppression des eventuels "
	//          les " ne sont plus supprimes
	$string = stripslashes($string) ;
	$string = pmb_alphabetic('^a-z0-9\s', ' ',pmb_strtolower($string));

	// remplacement espace  insécable 0xA0:	&nbsp;  	Non-breaking space
	$string = clean_nbsp($string);

	$string = pmb_preg_replace_spaces($string);

	return $string;
}

function get_empty_words($lg = 0) {
	global $got_empty_word;
	global $pmb_indexation_lang;
	//	global $lang;
	global $include_path;

	if(!isset($got_empty_word[$lg]) || !$got_empty_word[$lg]) {
		$got_empty_word[$lg] = array();
		if (!$lg || $lg == $pmb_indexation_lang) {
			global $empty_word;
			$got_empty_word[$lg] = $empty_word;
		} else {
			include($include_path."/marc_tables/".$lg."/empty_words");
			$got_empty_word[$lg] = $empty_word;
		}
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

// strip_empty_words : fonction enlevant les mots vides d'une chaine
function strip_empty_words($string, $lg = 0) {

	// on inclut le tableau des mots-vides pour la langue par defaut si elle n'est pas precisee
	// c'est normalement la langue de catalogage...
	// sinon on inclut le tableau des mots vides pour la langue precisee
	// si apres nettoyage des mots vide la chaine est vide alors on garde la chaine telle quelle (sans les accents)
	$empty_word = get_empty_words($lg);

	// nettoyage de l'entree

	// traitement des diacritiques
	$string = convert_diacrit($string);

	// Mis en commentaire : qu'en est-il des caracteres non latins ???
	// SUPPRIME DU COMMENTAIRE : ER : 12/05/2004 : ça fait tout merder...
	// RECH_14 : Attention : ici suppression des eventuels "
	//          les " ne sont plus supprimes
	$string = stripslashes($string) ;
	$string = pmb_alphabetic('^a-z0-9\s', ' ',pmb_strtolower($string));

	// remplacement espace  insécable 0xA0:	&nbsp;  	Non-breaking space
	$string = clean_nbsp($string);

    //$string = pmb_preg_replace_spaces($string);

	$string_avant_mots_vides = $string ;
	// suppression des mots vides
	if(is_array($empty_word)) {
		global $empty_word_converted;
		if(!isset($empty_word_converted)) {
			$empty_word_converted = array();
			foreach($empty_word as $dummykey=>$word) {
				$empty_word_converted[$dummykey] = convert_diacrit($word);
			}
            $empty_word_converted = implode("|",$empty_word_converted);
		}
        // AR-AP : \b => word boundary bien plus efficace
        $string = pmb_preg_replace("/\b(".$empty_word_converted.")\b/im", '', $string);
	}


	// re nettoyage des espaces generes
	$string = pmb_preg_replace_spaces($string);

	if (!$string) {
		$string = $string_avant_mots_vides ;
		// re nettoyage des espaces generes
		$string = pmb_preg_replace_spaces($string);
	}

	return $string;
}

// clean_string() : fonction de nettoyage d'une chaÓne
function clean_string($string) {
	global $charset;
	global $clean_string_matches;
	global $clean_string_replaces;
	// on supprime les caractËres non-imprimables
	$string = pmb_preg_replace("/\\x0|[\x01-\x1f]/U","",$string);

	// suppression des caractËres de ponctuation indesirables
	// $string = pmb_preg_replace('/[\{\}\"]/', '', $string);

	if(!isset($clean_string_matches) || !isset($clean_string_replaces)) {
		$clean_string_matches = array();
		$clean_string_replaces = array();
		// supression du point et des espaces de fin
		$clean_string_matches[] = '/\s+\.$|\s+$/';
		$clean_string_replaces[] = '';

		// nettoyage des espaces autour des parenthËses
		$clean_string_matches[] = '/\(\s+/';
		$clean_string_replaces[] = '(';
		$clean_string_matches[] = '/\s+\)/';
		$clean_string_replaces[] = ')';

		// idem pour les crochets
		$clean_string_matches[] = '/\[\s+/';
		$clean_string_replaces[] = '[';
		$clean_string_matches[] = '/\s+\]/';
		$clean_string_replaces[] = ']';

		// petit point de detail sur les apostrophes
		//$string = pmb_preg_replace('/\'\s+/', "'", $string);

		// 'trim' par regex
		$clean_string_matches[] = '/^\s+|\s+$/';
		$clean_string_replaces[] = '';

		// suppression des espaces doubles
		$clean_string_matches[] = '/\s+/';
		$clean_string_replaces[] = ' ';

		if($charset == 'utf-8') {
			foreach ($clean_string_matches as $key=>$matches) {
				$clean_string_matches[$key] = $matches.'u';
			}
		}
	}
	$string = preg_replace($clean_string_matches, $clean_string_replaces, $string);

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
//	fonctions sur les dates
// ----------------------------------------------------------------------------
// today() : retourne la date du jour au format MySQL-DATE
function today() {
	$jour = date('Y-m-d');
	return $jour;
}

// formatdate() : retourne une date formatee comme il faut
function formatdate($date_a_convertir, $with_hour=0) {
	global $msg;
	global $dbh;
	pmb_load_messages();
	if ($with_hour) $resultatdate=pmb_mysql_query("select date_format('".$date_a_convertir."', '".$msg["format_date_heure"]."') as date_conv ");
		else $resultatdate=pmb_mysql_query("select date_format('".$date_a_convertir."', '".$msg["format_date"]."') as date_conv ");
	$date_conv=pmb_mysql_result($resultatdate,0,0);
	return $date_conv ;
}

// formatdate_input() : retourne une date formatee comme il faut
function formatdate_input($date_a_convertir, $with_hour=0) {
	global $msg;
	global $dbh;

	if ($with_hour) $resultatdate=pmb_mysql_query("select date_format('".$date_a_convertir."', '".$msg["format_date_heure"]."') as date_conv ");
	else $resultatdate=pmb_mysql_query("select date_format('".$date_a_convertir."', '".$msg["format_date_input_model"]."') as date_conv ");
	$date_conv=pmb_mysql_result($resultatdate,0,0);
	return $date_conv ;
}

// extraitdate() : retourne une date formatee comme il faut
function extraitdate($date_a_convertir) {
	global $msg;

	$date_a_convertir = str_replace ("-","/",$date_a_convertir);
	$date_a_convertir = str_replace (".","/",$date_a_convertir);
	$date_a_convertir = str_replace ("\\","/",$date_a_convertir);

	$format_local = str_replace ("%","",$msg["format_date_input_model"]);
	$format_local = str_replace ("-","",$format_local);
	$format_local = str_replace ("/","",$format_local);
	$format_local = str_replace ("\\","",$format_local);
	$format_local = str_replace (".","",$format_local);
	$format_local = str_replace (" ","",$format_local);
	$format_local = str_replace ($msg["format_date_input_separator"],"",$format_local);
	list($date[substr($format_local,0,1)],$date[substr($format_local,1,1)],$date[substr($format_local,2,1)]) = sscanf($date_a_convertir,$msg["format_date_input"]) ;
	if ($date['Y'] && $date['m'] && $date['d']){
		 //$date_a_convertir = $date['Y']."-".$date['m']."-".$date['d'] ;
		 $date_a_convertir = sprintf("%04d-%02d-%02d",$date['Y'],$date['m'],$date['d']);
	} else {
		$date_a_convertir="";
	}
	return $date_a_convertir ;
}

function detectFormatDate($date_a_convertir,$compl="01"){
	global $msg;

	if(preg_match("#\d{4}-\d{2}-\d{2}#",$date_a_convertir)){
		$date = $date_a_convertir;
	}else if(preg_match("#\d{4}.\d{2}.\d{2}#",$date_a_convertir)){
		$date = str_replace('.', '-', $date_a_convertir);
	}else if(preg_match(getDatePattern(),$date_a_convertir)){
		$date = extraitdate($date_a_convertir);
	}elseif(preg_match(getDatePattern("short"),$date_a_convertir)){
		$format = str_replace ("%","",$msg["format_date_short"]);
		$format = str_replace ("-","",$format);
		$format = str_replace ("/","",$format);
		$format = str_replace ("\\","",$format);
		$format = str_replace (".","",$format);
		$format = str_replace (" ","",$format);
		$format = str_replace ($msg["format_date_input_separator"],"",$format);
		if (!empty(substr($format,0,1)) && !empty(substr($format,1,1)) && !empty(substr($format,2,1))) {		    
		    list($date[substr($format,0,1)],$date[substr($format,1,1)],$date[substr($format,2,1)]) = sscanf($date_a_convertir,$msg["format_date_short_input"]);
		} elseif (!empty(substr($format,0,1)) && !empty(substr($format,1,1))) {		 
		    list($date[substr($format,0,1)],$date[substr($format,1,1)]) = sscanf($date_a_convertir,$msg["format_date_short_input"]);
		} elseif (!empty(substr($format,0,1)) && !empty(substr($format,1,1))) {
		    list($date[substr($format,0,1)]) = sscanf($date_a_convertir,$msg["format_date_short_input"]);
		}
		if ($date['Y'] && $date['m']){
			if ($compl == "min") {
				$date = sprintf("%04d-%02d-%02s",$date['Y'],$date['m'],"01");
			} elseif ($compl == "max") {
				$date = sprintf("%04d-%02d-%02s",$date['Y'],$date['m'],date("t",mktime( 0, 0, 0, $date['m'], 1, $date['Y'] )));
			} else{
				 $date = sprintf("%04d-%02d-%02s",$date['Y'],$date['m'],$compl);
			}
		}else{
			$date = "0000-00-00";
		}
	}elseif(preg_match(getDatePattern("year"),$date_a_convertir,$matches)){
		if ($compl == "min") {
			$date = $matches[0]."-01-01";
		} elseif ($compl == "max") {
			$date = $matches[0]."-12-31";
		} else{
			$date = $matches[0]."-".$compl."-".$compl;
		}
	}else{
		$format = str_replace ("%",".",$msg["format_date"]);
		$format = str_replace ("-","",$format);
		$format = str_replace ("/","",$format);
		$format = str_replace ("\\","",$format);
		$format = str_replace (".","",$format);
		$format = str_replace (" ","",$format);
		$pattern= array();
		for($i=0 ; $i< strlen($format) ; $i++){
			switch($format[$i]){
				case "m" :
				case "d" :
					$pattern[$i] =  '\d{1,2}';
					break;
				case "Y" :
					$pattern[$i] =  '(\d{2})';
					break;
			}
		}
		if(preg_match("#".implode($pattern,".")."#", $date_a_convertir,$matches)){
			if(substr(date("Y"),2,2) < $matches['1']){
				$correct_year = ((substr(date("Y"),0,2)*1)-1).$matches[1];
			}else{
				$correct_year = substr(date("Y"),0,2).$matches[1];
			}
			if(substr($format,-1) == "Y"){
				$date = detectFormatDate(substr($date_a_convertir,0,-2).$correct_year,$compl);
			}
		}else{
			$date = "0000-00-00";
		}
	}

	return $date;
}

function getDatePattern($format="long"){
	global $msg;
	switch($format){
		case "long" :
			$format_date = str_replace ("%","",$msg["format_date"]);
			break;
		case "short" :
			$format_date = str_replace ("%","",$msg["format_date_short"]);
			break;
		case "year":
			$format_date = "Y";
			break;
	}
	$format_date = str_replace ("-"," ",$format_date);
	$format_date = str_replace ("/"," ",$format_date);
	$format_date = str_replace ("\\"," ",$format_date);
	$format_date = str_replace ("."," ",$format_date);
	$format_date=explode(" ",$format_date);
	$pattern = array();
	for($i=0;$i<count($format_date);$i++){
		switch($format_date[$i]){
			case "m" :
			case "d" :
				$pattern[$i] =  '\d{1,2}';
			break;
			case "Y" :
				$pattern[$i] =  '\d{4}';
			break;
		}
	}
	return "#".implode($pattern,".")."#";
}

function getDojoPattern($date) {
	$formatted_date = str_replace (array("%d", "%m", "%y", "%D", "%M", "%Y"),array("dd","MM","yy","DD","MMMM","yyyy"),$date);
	if(strpos($formatted_date, '%') !== false) {
		return '';
	} else {
		return $formatted_date;
	}
}

// construitdateheuremysql($date) : retourne une date formatee MySQL à partir de "YYYYmmddHHMMSS"
function construitdateheuremysql($date_a_convertir) {
	global $msg;
	$date_a_convertir = str_replace('-', '', $date_a_convertir);
	$date_a_convertir = str_replace('/', '', $date_a_convertir );
	$date_a_convertir = str_replace(' ', '', $date_a_convertir );
	$date_a_convertir = str_replace('#', '', $date_a_convertir );
	$date_a_convertir = str_replace(':', '', $date_a_convertir );
	$date_a_convertir = str_replace('.', '', $date_a_convertir );
	$date_a_convertir = str_replace('@', '', $date_a_convertir );
	$date_a_convertir = str_replace('\\', '', $date_a_convertir );
	$date_a_convertir = str_replace('%', '', $date_a_convertir );
	$date_a_convertir = str_replace($msg["format_date_input_separator"], '', $date_a_convertir );

	$dateconv = substr($date_a_convertir,0,4) ;
	$dateconv.= "-" ;
	$dateconv.= substr($date_a_convertir,4,2) ;
	$dateconv.= "-" ;
	$dateconv.= substr($date_a_convertir,6,2) ;
	if (substr($date_a_convertir,8,2)) {
		$dateconv.= " " ;
		$dateconv.= substr($date_a_convertir,8,2) ;
		$dateconv.= ":" ;
		$dateconv.= substr($date_a_convertir,10,2) ;
		if (substr($date_a_convertir,12,2)) {
			$dateconv.= ":" ;
			$dateconv.= substr($date_a_convertir,12,2) ;
		}
	}
	return $dateconv ;
}

// ----------------------------------------------------------------------------
//	fonctions qui retourne le nom de la page courante (SANS L'EXTENSION .php) !
// ----------------------------------------------------------------------------
function current_page() {
	return str_replace("/", "", preg_replace("#\/.*\/(.*\.php)$#", "\\1", $_SERVER["PHP_SELF"]));
}

// ----------------------------------------------------------------------------
//	fonction gen_liste qui genere des combo_box a partir d'une requete
// ----------------------------------------------------------------------------
/*
 $requete :					requete sql pour generer la liste (retourne $champ_code, $champ_info)
 $champ_code :				valeur
 $champ_info :				libelle
 $nom :						id et name
 $on_change :				fonction a appeler sur changement
 $selected :				valeur affichee par defaut
 $liste_vide_code : 		valeur renvoyee si liste vide
 $liste_vide_info :			libelle affiche si liste vide
 $option_premier_code :     valeur en tete de liste
 $option_premier_info :     libelle en tete de liste
 $multiple :				selecteur multiple si 1
 $attr						attributs de la liste
*/
function gen_liste ($requete, $champ_code, $champ_info, $nom, $on_change, $selected, $liste_vide_code, $liste_vide_info,$option_premier_code,$option_premier_info,$multiple=0,$attr='') {

	global $dbh, $charset ;

	$resultat_liste=pmb_mysql_query($requete, $dbh) or die ($requete);
	$renvoi="<select name=\"$nom\" id=\"$nom\" onChange=\"$on_change\" ";
	if ($multiple) $renvoi.="multiple ";
	if ($attr) $renvoi.="$attr ";
	$renvoi.=">\n";
	$nb_liste=pmb_mysql_num_rows($resultat_liste);
	if ($nb_liste==0) {
		$renvoi.="<option value=\"$liste_vide_code\">".htmlentities($liste_vide_info, ENT_QUOTES, $charset)."</option>\n";
	} else {
		if ($option_premier_info!="") {
			$renvoi.="<option value=\"$option_premier_code\" ";
			if ($selected==$option_premier_code) $renvoi.="selected=\"selected\"";
			$renvoi.=">".htmlentities($option_premier_info, ENT_QUOTES, $charset)."</option>\n";
		}
		$i=0;
		while ($i<$nb_liste) {
			$renvoi.="<option value=\"".pmb_mysql_result($resultat_liste,$i,$champ_code)."\" ";
			if ($selected==pmb_mysql_result($resultat_liste,$i,$champ_code)) $renvoi.="selected=\"selected\"";
			$renvoi.=">".htmlentities(pmb_mysql_result($resultat_liste,$i,$champ_info),ENT_QUOTES, $charset)."</option>\n";
			$i++;
		}
	}
	$renvoi.="</select>\n";
	return $renvoi;
}


// ----------------------------------------------------------------------------
//	fonction gen_liste_multiple qui genere des combo_box super sympas avec selection multiple
// ----------------------------------------------------------------------------
function gen_liste_multiple ($requete, $champ_code, $champ_info, $champ_selected, $nom, $on_change, $selected, $liste_vide_code, $liste_vide_info,$option_premier_code,$option_premier_info,$multiple=0) {
	$resultat_liste=pmb_mysql_query($requete) or die (pmb_mysql_error());
	$nb_liste=pmb_mysql_num_rows($resultat_liste);
	if ($multiple && $nb_liste) {
		if ($nb_liste < $multiple) $size = $nb_liste+1;
			else $size = $multiple;
		} else $size = 1 ;
	$renvoi="<select size='$size' name='$nom' data-form-name='$nom' id='$nom' onChange=\"$on_change\"";
	if ($multiple) $renvoi.=" multiple";
	$renvoi.=">\n";
	if ($nb_liste==0) {
		$renvoi.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n";
	} else {
		if ($option_premier_info!="") {
			$renvoi.="<option value=\"$option_premier_code\" ";
			if ($selected==$option_premier_code) $renvoi.="selected=\"selected\"";
			$renvoi.=">$option_premier_info</option>\n";
		}
		$i=0;
		while ($i<$nb_liste) {
			$renvoi.="<option value=\"".pmb_mysql_result($resultat_liste,$i,$champ_code)."\" ";
			if ($selected==pmb_mysql_result($resultat_liste,$i,$champ_selected)) $renvoi.="selected=\"selected\"";
			$renvoi.=">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n";
			$i++;
		}
	}
	$renvoi.="</select>\n";
	return $renvoi;
}

// ----------------------------------------------------------------------------
//	fonction do_selector qui genere des combo_box avec tout ce qu'il faut
// ----------------------------------------------------------------------------
function do_selector($table, $name='mySelector', $value=0) {

	global $dbh;
 	global $charset;

	$defltvar="deflt_".$table;

	global ${$defltvar};

	if ($value==0) $value= ${$defltvar} ;

	if(!$table)
		return '';

	$requete = "SELECT * FROM $table order by 2";
	$result = @pmb_mysql_query($requete, $dbh);

	$nbr_lignes = pmb_mysql_num_rows($result);

	if(!$nbr_lignes)
		return '';

	$selector = "<select name='$name' id='$name'>";
	while($line = pmb_mysql_fetch_row($result)) {
		$selector .= "<option value='${line[0]}'";
		$line[0] == $value ? $selector .= ' selected=\'selected\'>' : $selector .= '>';
 		$selector .= htmlentities($line[1],ENT_QUOTES, $charset).'</option>';
	}
	$selector .= '</select>';

	return $selector;
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

// ----------------------------------------------------------------------------
//	fonction de pagination
// ----------------------------------------------------------------------------

function aff_pagination ($url_base="", $nbr_lignes=0, $nb_per_page=0, $page=0, $etendue=10, $aff_nb_per_page=false, $aff_extr=false ) {

	global $msg,$charset;
	global $pmb_items_pagination_custom;
	if(!$nb_per_page) $nb_per_page=1;
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
		$nav_bar .= "<a class='pagination_first' data-type-link='pagination' id='premiere' href='".$url_base."&page=1&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page."' ><img src='".get_url_icon('first.gif')."' border='0' alt='".$msg['first_page']."' hspace='6' class='align_middle' title='".$msg['first_page']."' /></a>";
	}

	// affichage du lien precedent si necessaire
	if($precedente > 0) {
		$nav_bar .= "<a class='pagination_left' data-type-link='pagination' id='precedente' href='".$url_base."&page=".$precedente."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page."' ><img src='".get_url_icon('left.gif')."' border='0' alt='".$msg[48]."' hspace='6' class='align_middle' title='".$msg[48]."' /></a>";
	}

	for ($i = $deb; ($i <= $nbepages) && ($i<=$page+$etendue) ; $i++) {
		if($i==$page) {
			$nav_bar .= "<strong>".$i."</strong>";
		} else {
			$nav_bar .= "<a class='pagination_page' data-type-link='pagination' href='".$url_base."&page=".$i."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page."' >".$i."</a>";
		}
		if($i<$nbepages) $nav_bar .= " ";
	}


	if ($suivante<=$nbepages) {
		$nav_bar .= "<a class='pagination_right' data-type-link='pagination' id='suivante' href='".$url_base."&page=".$suivante."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page."' ><img src='".get_url_icon('right.gif')."' border='0' alt='".$msg[49]."' hspace='6' class='align_middle' title='".$msg[49]."' /></a>";
	}

	if($aff_extr && (($page+$etendue)<$nbepages) ) {
		$nav_bar .= "<a class='pagination_last' data-type-link='pagination' id='derniere' href='".$url_base."&page=".$nbepages."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page."' ><img src='".get_url_icon('last.gif')."' border='0' alt='".$msg['last_page']."' hspace='6' class='align_middle' title='".$msg['last_page']."' /></a>";
	}

	$start_in_page = ((($page-1)*$nb_per_page)+1);
	if(($start_in_page + $nb_per_page) > $nbr_lignes) {
		$end_in_page = $nbr_lignes;
	} else {
		$end_in_page = ((($page-1)*$nb_per_page)+$nb_per_page);
	}
	$nav_bar .= " (".$start_in_page." - ".$end_in_page." / ".$nbr_lignes.")";

	$pagination_nav_bar = "";
	if($pmb_items_pagination_custom) {
		$pagination_custom = explode(',', $pmb_items_pagination_custom);
		if(count($pagination_custom)) {
			$max_nb_elements = 0;
			$nb_first_custom_element = $pagination_custom[0];
			foreach ($pagination_custom as $nb_elements) {
			    $nb_elements = (int) trim($nb_elements);
				if($nb_first_custom_element <= $nbr_lignes) {
					if($nb_elements == $nb_per_page) $pagination_nav_bar .= "<b>";
					$pagination_nav_bar .= " <a class='pagination_custom' data-type-link='pagination' href='".$url_base."&page=1&nbr_lignes=".$nbr_lignes."&nb_per_page_custom=".$nb_elements."' >".$nb_elements."</a> ";
					if($nb_elements == $nb_per_page) $pagination_nav_bar .= "</b>";
				}
				if($nb_elements > $max_nb_elements) {
					$max_nb_elements = $nb_elements;
				}
			}
			if(($max_nb_elements > $nbr_lignes) && ($nb_per_page < $nbr_lignes)) {
				$pagination_nav_bar .= " <a class='pagination_custom' data-type-link='pagination' href='".$url_base."&page=1&nbr_lignes=".$nbr_lignes."&nb_per_page_custom=".$nbr_lignes."' >".$msg['tout_afficher']."</a> ";
			}
			if($pagination_nav_bar) {
				$pagination_nav_bar = "<span style='float:right;'> ".$msg['per_page']." ".$pagination_nav_bar."</span>";
			}
		}
	}
	$nav_bar = "<div class='center'>".$nav_bar.$pagination_nav_bar."</div>";
	return $nav_bar ;
}

// ----------------------------------------------------------------------------
//	fonction de selection des sous-onglets
// ---------------------------------------------------------------------------
//exemple d'entree : categ=caddie&sub=gestion&quoi=panier
function ongletSelect($urlPart){
	$returnSelection="";
	$items=explode("&",$urlPart);
	foreach($items as $item){
		$item=explode("=",$item);
		global ${$item[0]};
		if(!isset($item[1])) $item[1] = "";
		if (${$item[0]}==$item[1]){
			$returnSelection=" class=\"selected\"";
		} else {
			$returnSelection="";
			break;
		}
	}
	return $returnSelection;
}


// ----------------------------------------------------------------------------
//	fonction generant une alerte javascript
// ----------------------------------------------------------------------------
function alert_jscript ($message="") {
global $charset;
$ret = "
<script type='text/javascript'>
<!--
alert(\"".$message."\");
-->
</script>" ;
return $ret;
}

// ---------------------------------------------------------------------------------
//	function called to clean marc fields from garbage in some italian z39.50 server
// ---------------------------------------------------------------------------------
function del_more_garbage($string) {

// delete the "<<"   and    ">>" symbols
// con l'apostrofo niente spazio
$string = preg_replace('/<<(\w*[\'])\s*>>\s*/', '$1',$string );
//senza apostrofo uno spazio
$string = preg_replace('/<<(\w*)\s*>>\s*/', '$1 ',$string );

// delete the "* " symbol
$string = preg_replace('/\*/', '',$string );

// delete the ","  at the beginnin or at the end of the string
$string= preg_replace('/^\,|\,$/', '', $string);

return $string;
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
//  pmb_preg_replace_space($regex,$replace,$chaine) : remplacement d'une regex par une autre
// ------------------------------------------------------------------
function pmb_preg_replace_spaces($chaine) {
	// 1 - espaces en debut et fin
	// 2 - espaces en double
	return preg_replace(array('/^\s+|\s+$/','/\s+/'), array('',' '), $chaine);
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
//  pmb_split($separateur,$string) : separe un chaine de caractere selon un separateur
// ------------------------------------------------------------------
function pmb_split($separateur,$chaine) {
	global $charset;
	if ($charset != 'utf-8') {
		return explode($separateur,$chaine);
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
 * Hebreu
 * \x{05D0}-\x{05EA}
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
				.'\x{10A0}-\x{10C5}\x{10D0}-\x{10FC}\x{2D00}-\x{2D25}\x{05D0}-\x{05EA}'
				.']/u', ' ', $string);*/
		return preg_replace('/['.$regex.'\p{L}]/u', $replace, $string);//MB 28/10/14: http://www.regular-expressions.info/unicode.html
	}
}

// ------------------------------------------------------------------
//  pmb_strlen($string) : calcule la longueur d'une chaine pour utf-8 il s'agit du nombre de caracteres.
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
		if ($longueur == 0){
			return mb_substr($chaine,$depart,mb_strlen($chaine),$charset);
		}else
			return mb_substr($chaine,$depart,$longueur,$charset);
	}
}

// ------------------------------------------------------------------
//  pmb_strtolower($string) : passage d'une chaine de caractere en minuscule
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
//  pmb_strtoupper($string) : passage d'une chaine de caractere en majuscule
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
//  pmb_bidi($string) : renvoi la chaine de caractere en gerant les problemes
//  d'affichage droite gauche des parentheses
// ------------------------------------------------------------------
function pmb_bidi($string) {
	global $charset;
	global $lang;
	if ($charset != 'utf-8' or $lang == 'ar') {
		// utf-8 obligatoire pour l'arabe
		return $string;
	}
	else {
		//\x{0600}-\x{06FF}\x{0750}-\x{077F} : Arabic
		//x{0590}-\x{05FF} : hebrew
		if (preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{0590}-\x{05FF}]/u', $string)) {

			// 1 - j'entoure les caracteres arabes + espace ou parenthese ou chiffre de <span dir=rtl>'
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
//  des mails envoyes par PMB (resa, prets)
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

//---------------------------------------------------------------------
//Affiche un bloc avec +
//---------------------------------------------------------------------
function gen_plus($id, $titre, $contenu, $maximise=0, $script_before='', $script_after='', $class_parent='notice-parent', $class_child='notice-child') {
	global $msg;
	if($maximise) $max=" startOpen=\"Yes\""; else $max='';
	return "
	<div class='row'></div>
	<div id='$id' class='".$class_parent."'>
		<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='$id"."Img' title='".$msg['plus_detail']."' border='0' onClick=\" $script_before expandBase('$id', true); $script_after return false;\" hspace='3'>
		<span class='notice-heada'>
			$titre
		</span>
	</div>
	<div id='$id"."Child' class='".$class_child."' style='margin-bottom:6px;display:none;width:94%' $max>
		$contenu
	</div>
	";
}


//---------------------------------------------------------------------
//Affiche un bloc avec +
//---------------------------------------------------------------------
function gen_plus_titre($id,$titre,$contenu,$maximise=0,$script_before='', $script_after='') {
	global $msg;
	if($maximise) $max=" startOpen=\"Yes\""; else $max='';
	return "
	<div class='row'></div>
	<div id='$id'  style='cursor: pointer;'  class='notice-parent' onClick=\" $script_before expandBase('$id', true); $script_after return false;\" >
		<span class='notice-heada'>
			$titre
		</span>
	</div>
	<div id='$id"."Child' class='notice-child' style='margin-bottom:6px;display:none;width:auto' $max>
		$contenu
	</div>
	";
}
//---------------------------------------------------------------------
// teste une requete et retourne false si problematique, sinon true
//---------------------------------------------------------------------
function explain_requete($requete) {

if (strtolower(substr(trim($requete),0,6))!='select') return true;

	global $dbh,$erreur_explain_rqt;
	$requete = "explain ".$requete;
	$result = @pmb_mysql_query($requete, $dbh);
	if(!$result) return false;
	$nbr_lignes = pmb_mysql_num_rows($result);

	if (!$nbr_lignes) return false;
	/*
	echo "<table><tr>";
	echo "<td>id           </td>";
	echo "<td>select_type  </td>";
	echo "<td>table        </td>";
	echo "<td>type         </td>";
	echo "<td>possible_keys</td>";
	echo "<td>key          </td>";
	echo "<td>key_len      </td>";
	echo "<td>ref          </td>";
	echo "<td>rows         </td>";
	echo "<td>Extra        </td>";
	echo "</tr>";
	*/
	$numligne=0;
	$erreur_explain_rqt="";
	$table_davant="";
	while ($ligne = pmb_mysql_fetch_object($result)) {
		$numligne++;
		/*
		echo "<tr>";
		echo "<td>".$ligne->id           ."</td>";
		echo "<td>".$ligne->select_type  ."</td>";
		echo "<td>".$ligne->table        ."</td>";
		echo "<td>".$ligne->type         ."</td>";
		echo "<td>".$ligne->possible_keys."</td>";
		echo "<td>".$ligne->key          ."</td>";
		echo "<td>".$ligne->key_len      ."</td>";
		echo "<td>".$ligne->ref          ."</td>";
		echo "<td>".$ligne->rows         ."</td>";
		echo "<td>".$ligne->Extra        ."</td>";
		echo "</tr>";
		*/
		if ($numligne>1) {
			if ($ligne->possible_keys=='' && $ligne->ref=='' && $ligne->select_type=="SIMPLE") {
				$erreur_explain_rqt = " ERROR: ".$table_davant." - ".$ligne->table. " ";
				return false;
			}
		}
		$table_davant=$ligne->table;
	}
	// echo "</table>";
	return true;
}

function clean_tags($tags) {
	global $pmb_keyword_sep;
	$liste = explode($pmb_keyword_sep,$tags);
	$clean_liste=array();
	for($i=0; $i<count($liste); $i++) {
		if($tmp=trim($liste[$i])){
			if(!in_array($tmp,$clean_liste)){
				$clean_liste[]=$tmp;
			}
		}
	}
	if (count($clean_liste)) {
		return implode($pmb_keyword_sep,$clean_liste);
	}
	return '';
}

//---------------------------------
//CONFIGURATION DU PROXY POUR CURL
//---------------------------------

function configurer_proxy_curl(&$curl,$url_asked=''){
	global $pmb_curl_proxy,$curl_addon_array_options,$curl_addon_array_exclude_proxy;

	/*
	 * petit hack pour définir des options supplémentaires à curl
	 * les deux tableaux suivants peuvent être définis dans un fichier pmb/includes/config_local.inc.php (attention, à reporter en opac 'opac_config_local.inc.php')
	 *
	 * Exemple $curl_addon_array_options
	 *
	 * $curl_addon_array_options = array(
	 * 		CURLOPT_POST => 1,
	 * 		CURLOPT_HEADER => false,
	 * 		CURLOPT_POSTFIELDS => $data,
	 *      CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4 // Pour forcer la résolution en IPV4
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
		if($pmb_curl_proxy!=''){
			$param_proxy = explode(',',$pmb_curl_proxy);
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

// permet d'éviter une déconnection mysql
function mysql_set_wait_timeout($val_second=120) {
	$sql = "set wait_timeout = $val_second";
	pmb_mysql_query($sql);
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

function alert_sound_script(){
	global $param_sounds, $alert_sound_list;
	if (!$param_sounds) return;
	if (!count($alert_sound_list)) return;

	// Parfois ceci bloque le focus sur Firefox 3.5. pb de temps réel dans la gestion des evenements.
	//$script="<embed src='!!sound_file!!' height='0' width='0' autostart='true' loop='false' BORDER='0'>";

/*
$script=
  "<embed src='!!sound_file!!' autostart='true' height=0/>
   <script type='text/javascript'>
   var obj='';
   if(document.getElementById('form_cb_expl')){
   	obj='form_cb_expl';
   }
   if(document.getElementById('cb_doc')){
   	obj='cb_doc';
   }
   	if(obj){
		setTimeout(\"document.getElementById('\"+obj+\"').blur(); document.getElementById('\"+obj+\"').focus(); \",1200);
	}
   </script>
   ";
  */
	// En HTML5:
$script="
	<audio id='sound_to_play'  >
		<source src='!!sound_file!!' type='audio/ogg'>
	</audio>

	<script type='text/javascript'>
   		myAudio=document.getElementById('sound_to_play');
   		myAudio.play();
   </script>
";
	if(in_array("critique",$alert_sound_list))	$sound="sounds/boing.ogg";
	elseif(in_array("question",$alert_sound_list))$sound="sounds/boing.ogg";
	elseif(in_array("application",$alert_sound_list))$sound="sounds/boing.ogg";
	elseif(in_array("information",$alert_sound_list))$sound="sounds/waou.ogg";

	$script=str_replace("!!sound_file!!", $sound, $script) ;

	return $script;
}

function console_log($msg_to_log){
	print "<script type='text/javascript'>if(typeof console != 'undefined') {console.log('".addslashes($msg_to_log)."');}</script>";
}

function clean_string_to_base($string){
	return str_replace(" ","_",strip_empty_chars($string));
}

function go_first_tab(){
	global $value_deflt_module;

	if(!SESSrights){
		print "<SCRIPT>document.location='taberror.php';</SCRIPT>";
		exit;
	}
	switch($value_deflt_module){
		case "circu" :
			if(SESSrights & CIRCULATION_AUTH){
				print "<SCRIPT>document.location='circ.php';</SCRIPT>";
				exit;
			}
			break;
		case "catal" :
			if(SESSrights & CATALOGAGE_AUTH){
				print "<SCRIPT>document.location='catalog.php';</SCRIPT>";
				exit;
			}
			break;
		case "autor" :
			if(SESSrights & AUTORITES_AUTH){
				print "<SCRIPT>document.location='autorites.php';</SCRIPT>";
				exit;
			}
			break;
		case "edit" :
			if(SESSrights & EDIT_AUTH){
				print "<SCRIPT>document.location='edit.php';</SCRIPT>";
				exit;
			}
			break;
		case "dsi" :
			if(SESSrights & DSI_AUTH){
				print "<SCRIPT>document.location='dsi.php';</SCRIPT>";
				exit;
			}
			break;
		case "acquis" :
			if(SESSrights & ACQUISITION_AUTH){
				print "<SCRIPT>document.location='acquisition.php';</SCRIPT>";
				exit;
			}
			break;
		case "admin" :
			if(SESSrights & ADMINISTRATION_AUTH){
				print "<SCRIPT>document.location='admin.php';</SCRIPT>";
				exit;
			}
			break;
		case "exten" :
			if(SESSrights & EXTENSION_AUTH){
				print "<SCRIPT>document.location='exten.php';</SCRIPT>";
				exit;
			}
			break;
		case "cms" :
			if(SESSrights & CMS_AUTH){
				print "<SCRIPT>document.location='cms.php';</SCRIPT>";
				exit;
			}
			break;
		case "account" :
			if(SESSrights & PREF_AUTH){
				print "<SCRIPT>document.location='account.php';</SCRIPT>";
				exit;
			}
			break;
		case "fiches" :
			if(SESSrights & FICHES_AUTH){
				print "<SCRIPT>document.location='fichier.php';</SCRIPT>";
				exit;
			}
			break;
		case "dashboard" :
		default :
			print "<SCRIPT>document.location='dashboard.php';</SCRIPT>";
			exit;
	}
	print "<SCRIPT>document.location='taberror.php';</SCRIPT>";
	exit;
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
		$elem = utf8_encode($elem);
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

function display_notification($message, $params = array()) {
	$params = array_merge(array(
			'sticky' => false,
			'duration' => 5000,
			'channel' => 'info',
			'title' => ''
	), $params);
	return '<script type="text/javascript">
				require(["dojo/topic", "dojo/ready"], function(topic, ready){
					ready(function() {
						topic.publish("dGrowl", "'.$message.'", '.json_encode($params).');
					});
				});
			</script>';
}

function get_upload_max_filesize(){
	$upload_max_filesize = ini_get('upload_max_filesize');
	if (!$upload_max_filesize) {
		$upload_max_filesize = 50000;
	} else {
		$upload_max_filesize = trim($upload_max_filesize);
		$last = strtolower($upload_max_filesize[strlen($upload_max_filesize)-1]);
		$upload_max_filesize = rtrim($upload_max_filesize,"GMKgmk");
		switch($last) {

			// Le modifieur 'G' est disponible depuis PHP 5.1.0
			case 'g':
				$upload_max_filesize *= 1024;
			case 'm':
				$upload_max_filesize *= 1024;
			case 'k':
				$upload_max_filesize *= 1024;
		}
	}
	//On retourne le résultat en kbytes
	return $upload_max_filesize;
}

function get_url_icon($icon, $use_opac_url_base=0) {
	global $base_path;
	global $opac_url_base;
	global $stylesheet;

	if($use_opac_url_base) $url_base = $opac_url_base;
	else $url_base = $base_path."/";

	$icon_name = str_replace(array('.svg', '.png', '.jpg', '.gif'), '', $icon);

	if($url = search_url_icon_type("styles/".$stylesheet."/images/".$icon_name)){
		return $url_base.$url;
	}
	if($url = search_url_icon_type("styles/common/images/".$icon_name)){
		return $url_base.$url;
	}
	if($url = search_url_icon_type("images/".$icon_name)){
		return $url_base.$url;
	}
	if($url = "$url_base/images/$icon") {
		if (file_exists($url)) return $url;
		return '';
	}
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

    global $pmb_curl_available, $base_path, $opac_url_base ;
	//Calcul des URLs subst
	$url_subst=str_replace(".xml","_subst.xml",$url);
	$filename_subst=str_replace(".xml","_subst.xml",$filename);

	$file_copied =false;
	$subst_file_copied = false;

	//Si CURL est disponible en gestion
	if($pmb_curl_available) {
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
        $file_path = "$base_path/opac_css/".str_replace($opac_url_base, '', $url);
		if(file_exists($file_path)) {
			$file_copied = copy($file_path, $filename);
		}
	}

	if(!$file_copied) {
		return false;
	}

	if(!$subst_file_copied) {
        $subst_file_path = "$base_path/opac_css/".str_replace($opac_url_base, '', $url_subst);
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

function get_input_date_time_inter($name, $id = '', $date_begin = '', $time_begin = '', $date_end = '', $time_end = '', $required = false, $onchange='') {
    global $msg;

    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
        $version = get_browser_version($_SERVER['HTTP_USER_AGENT']);
        if (!$version || ((int) $version < 57)) {
            if ($required) {
                $required = 'true';
            } else {
                $required = 'false';
            }
            $fields_date = "
					<label>".$msg['resa_planning_date_debut']."</label>
					<input type='text' id='" . $id . "_date_begin' name='" . $name . "[date_begin]' value='" . $date_begin . "' data-dojo-type='dijit/form/DateTextBox'/>
					<input type='text' id='" . $id . "_time_begin' name='" . $name . "[time_begin]' value='" . $time_begin . "' data-dojo-type='dijit/form/TimeTextBox' data-dojo-props=\"constraints:{timePattern:'HH:mm',clickableIncrement:'T00:15:00', visibleIncrement: 'T01:00:00',visibleRange: 'T01:00:00'}\"/>
					<label>" . $msg['resa_planning_date_fin'] . "</label>
					<input type='text' id='" . $id . "_date_end' name='" . $name . "[date_end]' value='" . $date_end . "' data-dojo-type='dijit/form/DateTextBox'/>
					<input type='text' id='" . $id . "_time_end' name='" . $name . "[time_end]' value='" . $time_end . "' data-dojo-type='dijit/form/TimeTextBox' data-dojo-props=\"constraints:{timePattern:'HH:mm',clickableIncrement:'T00:15:00', visibleIncrement: 'T01:00:00',visibleRange: 'T01:00:00'}\"/>
					<input class='bouton' type='button' value='X' onClick='empty_dojo_calendar_by_id(\"" . $id . "_date_begin\"); empty_dojo_calendar_by_id(\"" . $id . "_time_begin\"); empty_dojo_calendar_by_id(\"" . $id . "_date_end\"); empty_dojo_calendar_by_id(\"" . $id . "_time_end\");'/>
    		        <script>use_dojo_calendar = 1</script>
            ";
            return $fields_date;
        }
    }
    if ($required) {
        $required = 'required';
    } else {
        $required = '';
    }
    $time_begin = str_replace('T', '', $time_begin);
    $time_end = str_replace('T', '', $time_end);
    $fields_date = "
		<label>".$msg['resa_planning_date_debut']."</label>
        <input type='date' id='" . $id . "_date_begin' name='" . $name . "[date_begin]' value='" . $date_begin . "' onchange='" . $onchange . "' " . $required . " />
		<input type='time' id='" . $id . "_time_begin' name='" . $name . "[time_begin]' value='" . $time_begin . "' onchange='" . $onchange . "' " . $required . " />
		<label>" . $msg['resa_planning_date_fin'] . "</label>
		<input type='date' id='" . $id . "_date_end' name='" . $name . "[date_end]' value='" . $date_end . "' onchange='" . $onchange . "' " . $required . " />
		<input type='time' id='" . $id . "_time_end' name='" . $name . "[time_end]' value='" . $time_end . "' onchange='" . $onchange . "' " . $required . " />
		<input class='bouton' type='button' value='X' onClick='document.getElementById(\"" . $id . "_date_begin\").value=\"\";document.getElementById(\"" . $id . "_time_begin\").value=\"\"; document.getElementById(\"" . $id . "_date_end\").value=\"\";document.getElementById(\"" . $id . "_time_end\").value=\"\";'/>
	   <script>use_dojo_calendar = 0</script>";
    return $fields_date;
}

function get_input_date($name, $id = '', $value='', $required = false, $onchange='') {
    global $msg;

    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
        $version = get_browser_version($_SERVER['HTTP_USER_AGENT']);
        if (!$version || ((int) $version < 57)) {
            if ($required) {
                $required = 'true';
            } else {
                $required = 'false';
            }
            $input_date = "
                    <input type='text'
                    name='" . $name . "'
                    id='" . $id . "'
                    value='" . $value . "'
                    onchange='" . $onchange . "'
                    data-form-name='" . $name . "'
                    data-dojo-type='dijit/form/DateTextBox'
                    required='" . $required . "'
                    constraints=\"{datePattern:'" . getDojoPattern($msg['format_date']) . "'}\" />
                    <input class='bouton' type='button' value='X' onClick='empty_dojo_calendar_by_id(\"".$id."\"); '/>
    		        <script>use_dojo_calendar = 1</script>
            ";
            return $input_date;
        }
    }
    if ($required) {
        $required = 'required';
    } else {
        $required = '';
    }
    $input_date = "
        <input type='date'
        name='" . $name . "'
        id='" . $id . "'
        value='" . $value . "'
        onchange='" . $onchange . "'
        " . $required . " />
		<input class='bouton' type='button' value='X' onClick='document.getElementById(\"".$id."\").value=\"\";'/>
	   <script>use_dojo_calendar = 0</script>";
    return $input_date;
}

function get_browser_version($u_agent, $ub = "Firefox") {

    $matches = array();
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        return '';
    }
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    return $version;
}

// PB de chargement de messages dans certains appel du WS
// Stratégie de contournement en attendant mieux !
function pmb_load_messages(){
    global $msg;
    global $include_path;
    global $lang;
    if(empty($msg) && file_exists("$include_path/messages/$lang.xml")){
        $messages = new XMLlist("$include_path/messages/$lang.xml", 0);
        $messages->analyser();
        $msg = $messages->table;
    }
}
