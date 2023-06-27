<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean.php,v 1.41 2019-04-29 11:04:20 dgoron Exp $

$base_path="../..";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "";    
require_once ("$base_path/includes/init.inc.php");  

// les requis par clean.php ou ses sous modules
include_once("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
include_once("$class_path/serie.class.php");
include_once("./params.inc.php");
echo "<div id='contenu-frame'>";

require_once($class_path."/netbase/netbase.class.php");

if(!isset($spec)) $spec = 0;
if(!$spec) {
	$spec += (isset($index_global) ? $index_global : 0);
	$spec += (isset($index_notices) ? $index_notices : 0);
	$spec += (isset($clean_authors) ? $clean_authors : 0);
	$spec += (isset($clean_editeurs) ? $clean_editeurs : 0);
	$spec += (isset($clean_collections) ? $clean_collections : 0);
	$spec += (isset($clean_subcollections) ? $clean_subcollections : 0);
	$spec += (isset($clean_categories) ? $clean_categories : 0);
	$spec += (isset($clean_series) ? $clean_series : 0);
	$spec += (isset($clean_relations) ? $clean_relations : 0);
	$spec += (isset($clean_notices) ? $clean_notices : 0);
	$spec += (isset($index_acquisitions) ? $index_acquisitions : 0);
	$spec += (isset($gen_signature_notice) ? $gen_signature_notice : 0);
	$spec += (isset($nettoyage_clean_tags) ? $nettoyage_clean_tags : 0);
	$spec += (isset($clean_categories_path) ? $clean_categories_path : 0);
	$spec += (isset($gen_date_publication_article) ? $gen_date_publication_article : 0);	
	$spec += (isset($gen_date_tri) ? $gen_date_tri : 0);
	$spec += (isset($reindex_docnum) ? $reindex_docnum : 0);
	$spec += (isset($clean_opac_search_cache) ? $clean_opac_search_cache : 0);
	$spec += (isset($clean_cache_amende) ? $clean_cache_amende : 0);
	$spec += (isset($clean_titres_uniformes) ? $clean_titres_uniformes : 0);
	$spec += (isset($clean_indexint) ? $clean_indexint : 0);
	$spec += (isset($gen_phonetique) ? $gen_phonetique : 0);
	$spec += (isset($index_rdfstore) ? $index_rdfstore : 0);
	$spec += (isset($index_synchrordfstore) ? $index_synchrordfstore : 0);
	$spec += (isset($index_faq) ? $index_faq : 0);
	$spec += (isset($index_cms) ? $index_cms : 0);
	$spec += (isset($index_concept) ? $index_concept : 0);
	$spec += (isset($hash_empr_password) ? $hash_empr_password : 0);
	$spec += (isset($index_authorities) ? $index_authorities : 0);
	$spec += (isset($gen_signature_docnum) ? $gen_signature_docnum : 0);
	$spec += (isset($delete_empr_passwords) ? $delete_empr_passwords : 0);
	$spec += (isset($clean_records_thumbnail) ? $clean_records_thumbnail : 0);
	$spec += (isset($gen_aut_link) ? $gen_aut_link : 0);
	$spec += (isset($clean_cache_temporary_files) ? $clean_cache_temporary_files : 0);
}
if($spec) {
	if($spec & CLEAN_NOTICES) {
		include('./clean_expl.inc.php');
	} elseif($spec & CLEAN_SUBCOLLECTIONS) {
		include('./subcollections.inc.php');
	} elseif($spec & CLEAN_COLLECTIONS) {
		include('./collections.inc.php');
	} elseif($spec & CLEAN_PUBLISHERS) {
		include('./publishers.inc.php');
	} elseif($spec & CLEAN_AUTHORS) {
		if(!isset($pass2) || !$pass2)
			include('./aut_pass1.inc.php'); // 1ère passe : auteurs non utilisés
		elseif ($pass2==1)
			include('./aut_pass2.inc.php'); // 2nde passe : renvois vers auteur inexistant
			elseif ($pass2==2) include('./aut_pass3.inc.php'); // 3eme passe : nettoyage des responsabilités sans notices
			else include('./aut_pass4.inc.php'); // 4eme passe : nettoyage des responsabilités sans auteurs
	} elseif($spec & CLEAN_CATEGORIES) {
		include('./category.inc.php');;
	} elseif($spec & CLEAN_SERIES) {
		include('./series.inc.php');
	} elseif ($spec & CLEAN_TITRES_UNIFORMES) {
		include('./titres_uniformes.inc.php');
	} elseif ($spec & CLEAN_INDEXINT) {
		include('./indexint.inc.php');
	} elseif ($spec & CLEAN_RELATIONS) {
		if(!isset($pass2) || !$pass2) $pass2=1;
		include('./relations'.$pass2.'.inc.php');
	} elseif ($spec & INDEX_ACQUISITIONS) {
		include('./acquisitions.inc.php');
	} elseif ($spec & GEN_SIGNATURE_NOTICE) {
		include('./gen_signature_notice.inc.php');
	} elseif ($spec & GEN_PHONETIQUE) {
		include('./gen_phonetique.inc.php');
	} elseif ($spec & NETTOYAGE_CLEAN_TAGS) {
		include('./nettoyage_clean_tags.inc.php');	
	} elseif ($spec & CLEAN_CATEGORIES_PATH) {
		include('./clean_categories_path.inc.php');	
	} elseif ($spec & GEN_DATE_PUBLICATION_ARTICLE) {
		include('./gen_date_publication_article.inc.php');	
	} elseif ($spec & GEN_DATE_TRI) {
		include('./gen_date_tri.inc.php');
	} elseif($spec & INDEX_NOTICES) {
		include('./reindex.inc.php');
	} elseif($spec & INDEX_GLOBAL) {
		include('./reindex_global.inc.php');
	} elseif ($spec & INDEX_DOCNUM) {
		include('./reindex_docnum.inc.php');
	} elseif ($spec & CLEAN_OPAC_SEARCH_CACHE) {
		include('./clean_opac_search_cache.inc.php');
	} elseif ($spec & CLEAN_CACHE_AMENDE) {
		include('./clean_cache_amende.inc.php');
	}  elseif ($spec & INDEX_RDFSTORE) {
		include('./reindex_rdfstore.inc.php');
	}  elseif ($spec & INDEX_SYNCHRORDFSTORE) {
		include('./reindex_synchrordfstore.inc.php');
	}  elseif ($spec & INDEX_FAQ){
		include('./reindex_faq.inc.php');
	}  elseif ($spec & INDEX_CMS){
		include('./reindex_cms.inc.php');
	}  elseif ($spec & INDEX_CONCEPT){
		include('./reindex_concept.inc.php');
	} elseif ($spec & HASH_EMPR_PASSWORD){
		include('./hash_empr_password.inc.php');
	} elseif ($spec & INDEX_AUTHORITIES){
		include('./reindex_authorities.inc.php');
	} elseif ($spec & GEN_SIGNATURE_DOCNUM){
		include('./gen_signature_docnum.inc.php');
	} elseif ($spec & DELETE_EMPR_PASSWORDS){
		include('./delete_empr_passwords.inc.php');
	} elseif ($spec & CLEAN_RECORDS_THUMBNAIL){
		include('./clean_records_thumbnail.inc.php');
	} elseif ($spec & GEN_AUT_LINK){
	    include('./gen_aut_link.inc.php');
	} elseif ($spec & CLEAN_CACHE_TEMPORARY_FILES) {
		include('./clean_cache_temporary_files.inc.php');
	}
} else {
	if(!isset($v_state)) $v_state = '';
	if($v_state) {
		print "<h2>".htmlentities($msg["nettoyage_termine"], ENT_QUOTES, $charset)."</h2>";
		print urldecode($v_state);
	} else
		include_once('./form.inc.php');
}

// fermeture du lien MySQL

pmb_mysql_close($dbh);
echo "</div>";
print '</body></html>';
