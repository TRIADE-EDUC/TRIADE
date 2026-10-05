<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean.php,v 1.52.2.2 2024/10/17 09:49:27 dgoron Exp $

$base_path="../..";
$base_auth = "ADMINISTRATION_AUTH";
$base_title = "";
require_once ("$base_path/includes/init.inc.php");

global $class_path, $include_path, $msg, $charset, $pmb_indexation_lang, $spec, $pass2, $v_state;
global $index_global, $index_notices, $index_acquisitions;
global $clean_authors, $clean_editeurs, $clean_collections, $clean_subcollections, $clean_categories, $clean_series, $clean_relations, $clean_notices;
global $gen_signature_notice, $gen_date_publication_article, $gen_date_tri, $gen_phonetique, $gen_signature_docnum, $gen_aut_link, $gen_ark, $clean_autoload_files;
global $gen_docnum_thumbnail;
global $nettoyage_clean_tags, $clean_categories_path, $clean_opac_search_cache, $clean_cache_amende, $clean_titres_uniformes, $clean_indexint;
global $clean_records_thumbnail, $clean_cache_temporary_files, $clean_cache_apcu, $clean_entities_data, $clean_docnum_thumbnail;
global $reindex_docnum, $index_rdfstore, $index_synchrordfstore, $index_faq, $index_cms, $index_concept, $index_authorities, $index_date_flot;
global $hash_empr_password, $delete_empr_passwords, $ai_indexation;
global $index_sphinx_records, $index_sphinx_authorities, $index_sphinx_concepts;

// les requis par clean.php ou ses sous modules
include_once("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
include_once("./params.inc.php");
echo "<div id='contenu-frame'>";

require_once($class_path."/netbase/netbase.class.php");

if(empty($spec)) $spec = 0;
$pass2 = intval($pass2);

// Liberation de la session pour ne pas penaliser la navigation sur les autres pages
session_write_close();

if(!$spec) {
	$spec += intval($index_global);
	$spec += intval($index_notices);
	$spec += intval($clean_authors);
	$spec += intval($clean_editeurs);
	$spec += intval($clean_collections);
	$spec += intval($clean_subcollections);
	$spec += intval($clean_categories);
	$spec += intval($clean_series);
	$spec += intval($clean_relations);
	$spec += intval($clean_notices);
	$spec += intval($index_acquisitions);
	$spec += intval($gen_signature_notice);
	$spec += intval($nettoyage_clean_tags);
	$spec += intval($clean_categories_path);
	$spec += intval($gen_date_publication_article);
	$spec += intval($gen_date_tri);
	$spec += intval($reindex_docnum);
	$spec += intval($clean_opac_search_cache);
	$spec += intval($clean_cache_amende);
	$spec += intval($clean_titres_uniformes);
	$spec += intval($clean_indexint);
	$spec += intval($gen_phonetique);
	$spec += intval($index_rdfstore);
	$spec += intval($index_synchrordfstore);
	$spec += intval($index_faq);
	$spec += intval($index_cms);
	$spec += intval($index_concept);
	$spec += intval($hash_empr_password);
	$spec += intval($index_authorities);
	$spec += intval($gen_signature_docnum);
	$spec += intval($delete_empr_passwords);
	$spec += intval($clean_records_thumbnail);
	$spec += intval($gen_aut_link);
	$spec += intval($clean_cache_temporary_files);
	$spec += intval($index_date_flot);
	$spec += intval($clean_cache_apcu);
	$spec += intval($clean_entities_data);
	$spec += intval($clean_docnum_thumbnail);
	$spec += intval($gen_ark);
	$spec += intval($clean_autoload_files);
	$spec += intval($gen_docnum_thumbnail);
	$spec += intval($ai_indexation);
	$spec += intval($index_sphinx_records);
	$spec += intval($index_sphinx_authorities);
	$spec += intval($index_sphinx_concepts);
}
$spec = intval($spec);
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
	} elseif($spec & INDEX_SPHINX_RECORDS) {
	    include('./reindex_sphinx_records.inc.php');
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
	} elseif ($spec & INDEX_SPHINX_CONCEPTS){
	    include('./reindex_sphinx_concepts.inc.php');
	} elseif ($spec & HASH_EMPR_PASSWORD){
		include('./hash_empr_password.inc.php');
	} elseif ($spec & INDEX_AUTHORITIES){
		include('./reindex_authorities.inc.php');
	} elseif ($spec & INDEX_SPHINX_AUTHORITIES){
	    include('./reindex_sphinx_authorities.inc.php');
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
	} elseif ($spec & INDEX_DATE_FLOT) {
		include('./reindex_date_flot.inc.php');
	} elseif ($spec & CLEAN_CACHE_APCU) {
		include('./clean_cache_apcu.inc.php');
	} elseif ($spec & CLEAN_ENTITIES_DATA) {
		include('./clean_entities_data.inc.php');
	} elseif ($spec & CLEAN_DOCNUM_THUMBNAIL){
	    include('./clean_docnum_thumbnail.inc.php');
	} elseif ($spec & GEN_ARK){
	    include('./gen_ark.inc.php');
	} elseif ($spec & CLEAN_AUTOLOAD_FILES){
    	include('./clean_autoload_files.inc.php');
	} elseif ($spec & GEN_DOCNUM_THUMBNAIL){
    	include('./gen_docnum_thumbnail.inc.php');
	} elseif ($spec & AI_INDEXATION){
		include('./ai_indexation.inc.php');
	};
} else {
	if(empty($v_state)) $v_state = '';
	if($v_state) {
		print "<h2>".htmlentities($msg["nettoyage_termine"], ENT_QUOTES, $charset)."</h2>";
		print urldecode($v_state);
	} else
		include_once('./form.inc.php');
}

// fermeture du lien MySQL

pmb_mysql_close();
echo "</div>";
print '</body></html>';
