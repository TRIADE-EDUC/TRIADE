<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rss_func.inc.php,v 1.10 2017-10-19 14:06:21 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// --------- funtion affichage RSS, reçoit fichier XML
function affiche_rss($id_rss=0) {

	$req_rss = "select lien, eformat from notices where notice_id='$id_rss' " ;
	$res_rss = pmb_mysql_query($req_rss);
	$rss = pmb_mysql_fetch_object($res_rss);
	
	$rss_lien = $rss->lien;
	$rss_lu = explode(' ', $rss->eformat) ;
	$rss_time = $rss_lu[1] ;

	if ($rss_time=='0' || !$rss_time) return affiche_rss_from_url($rss->lien) ;
	else {
		$req_content = "select if(sysdate()<date_add(rss_last, interval $rss_time minute), rss_content, null) as contenu, if(sysdate()<date_add(rss_last, interval $rss_time minute), rss_content_parse, null) as contenu_parse from rss_content where rss_id='$id_rss' " ;
		$res_content = pmb_mysql_query($req_content);
		if ($content = pmb_mysql_fetch_object($res_content)) {
			// on a trouvé un truc dans la table
			if ($content->contenu) {
				$etat_cache_rss = 1 ;
			} else {
				// truc trouvé mais périmé
				$etat_cache_rss = 2 ;
			}
		} else {
			// même pas trouvé
			$etat_cache_rss = 0 ; 
		}
		switch ($etat_cache_rss) {
			case 1 :
				// return affiche_rss_from_fichier($content->contenu) ;
				return $content->contenu_parse ;
				break ;
			case 2 :
				$fichier = lit_fichier_rss($rss_lien) ;
				$contenu_parse = affiche_rss_from_fichier($fichier);
				$rq = "update rss_content set rss_content='".addslashes($fichier)."', rss_content_parse='".addslashes($contenu_parse)."' where rss_id=$id_rss ";
				pmb_mysql_query($rq);
				return $contenu_parse ;
				break ;
			case 0 :
				$fichier = lit_fichier_rss($rss_lien) ;
				$contenu_parse = affiche_rss_from_fichier($fichier);
				$rq = "insert into rss_content set rss_id=$id_rss, rss_content='".addslashes($fichier)."', rss_content_parse='".addslashes($contenu_parse)."' ";
				pmb_mysql_query($rq);
				return $contenu_parse ;
				break ;
		}
	}	
}

function lit_fichier_rss($url_fichier) {
	global $opac_curl_available, $pmb_curl_timeout;
	
	$res="";
	if ($opac_curl_available) {
		$timeout=($pmb_curl_timeout*1 ? $pmb_curl_timeout*1 : 5);
		$ch = curl_init($url_fichier);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		configurer_proxy_curl($ch,$url_fichier);
		$res=curl_exec($ch);
		curl_close($ch);
	} else {
		$fp=fopen($url_fichier,"r");
		if ($fp) {
			while (!feof($fp)) $res.=fread($fp,2048);
			fclose($fp);
		}
	}

	return $res;
}

// --------- funtion affichage RSS, reçoit URL fichier XML
function affiche_rss_from_url($url_fichier="") {

	$fp=lit_fichier_rss($url_fichier) ;
	if ($fp) {
		$red=true;
		$content=str_replace("&nbsp;"," ",$fp);
		//Parse du fichier
		$param=_parser_text_no_function_($content);
		if (is_array($param)) {
			list($forme,$val)=each($param);
			$param=$val[0];
			for ($j=0; $j<count($param["CHANNEL"]); $j++) {
				$current=$param["CHANNEL"][$j];
				$articles.="<div class='row'>";
				if ($current["IMAGE"][0]) $articles.="<a href='".$current["IMAGE"][0]["LINK"][0]["value"]."' target='_blank'><img src='".$current["IMAGE"][0]["URL"][0]["value"]."' border='0' alt='".$current["IMAGE"][0]["TITLE"][0]["value"]."' title='".$current["IMAGE"][0]["TITLE"][0]["value"]."' class='center'></a>&nbsp;";
				$articles.="<b>".$current["TITLE"][0]["value"]."</b>";
				if (strpos($forme,"RDF")!==false) $current=$param;
				$articles.="<ul class='rss_section'>";
				for ($k=0; $k<count($current["ITEM"]); $k++) {
					$articles.="<li class='rss_articles'>";
					$item=$current["ITEM"][$k];
					$articles.="<p><i><a href='".$item["LINK"][0]["value"]."' target='_blank'>".$item["TITLE"][0]["value"]."</a></i></p><div class='rss_descriptions'>".$item["DESCRIPTION"][0]["value"]."</div>";
					$articles.="</li>";
				}
				$articles.="<div style='clear:both;'></div></ul>";
				$articles.="</div>";
			}
		}
	}
	return $articles;		
}

// --------- funtion affichage RSS, reçoit fichier XML
function affiche_rss_from_fichier($fichier="") {

	$content = $fichier ;
	$content=str_replace("&nbsp;"," ",$content);
	//Parse du fichier
	$param=_parser_text_no_function_($content);
	if (is_array($param)) {
		list($forme,$val)=each($param);
		$param=$val[0];
		for ($j=0; $j<count($param["CHANNEL"]); $j++) {
			$current=$param["CHANNEL"][$j];
			$articles.="<div class='row'>";
			if ($current["IMAGE"][0]) $articles.="<a href='".$current["IMAGE"][0]["LINK"][0]["value"]."' target='_blank'><img src='".$current["IMAGE"][0]["URL"][0]["value"]."' border='0' alt='".$current["IMAGE"][0]["TITLE"][0]["value"]."' title='".$current["IMAGE"][0]["TITLE"][0]["value"]."' class='center'></a>&nbsp;";
			$articles.="<b>".$current["TITLE"][0]["value"]."</b>";
			if (strpos($forme,"RDF")!==false) $current=$param;
			$articles.="<ul class='rss_section'>";
			for ($k=0; $k<count($current["ITEM"]); $k++) {
				$articles.="<li class='rss_articles'>";
				$item=$current["ITEM"][$k];
				$articles.="<p><i><a href='".$item["LINK"][0]["value"]."' target='_blank'>".$item["TITLE"][0]["value"]."</a></i></p><div class='rss_descriptions'>".$item["DESCRIPTION"][0]["value"]."</div>";
				$articles.="</li>";
			}
			$articles.="<div style='clear:both;'></div></ul>";
			$articles.="</div>";
		}
	}
	return $articles;		
}

