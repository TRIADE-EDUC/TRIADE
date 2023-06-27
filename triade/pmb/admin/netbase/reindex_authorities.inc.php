<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_authorities.inc.php,v 1.11 2018-10-03 10:22:52 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/indexation_authority.class.php");
require_once($class_path."/indexation_authperso.class.php");

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if (!isset($start)) {
	$start=0;
	//remise a zero de la table au début
	pmb_mysql_query("TRUNCATE authorities_words_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE authorities_words_global_index DISABLE KEYS",$dbh);

	pmb_mysql_query("TRUNCATE authorities_fields_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE authorities_fields_global_index DISABLE KEYS",$dbh);
}

$v_state=urldecode($v_state);

// on commence par :
if (!isset($index_quoi)) $index_quoi='AUTHORS';

switch ($index_quoi) {	
	case 'AUTHORS':
		if (!isset($count) || !$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM authors", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			//On controle qu'il n'y a pas d'autorité à enlever
			$req="SELECT id_authority FROM authorities LEFT JOIN authors ON num_object=author_id WHERE type_object='".AUT_TABLE_AUTHORS."' AND author_id IS NULL";
			$res = pmb_mysql_query($req,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				while($aut = pmb_mysql_fetch_row($res)){
					$authority = new authority($aut[0]);
					$authority->delete();
				}
			}
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_authors"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT author_id as id from authors LIMIT $start, $lot", $dbh);
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/authors/champs_base.xml", "authorities", AUT_TABLE_AUTHORS);
			$indexation_authority->set_deleted_index(true);
			while($row = pmb_mysql_fetch_object($query)) {
				$indexation_authority->maj($row->id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'AUTHORS', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_authors"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_authors"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'PUBLISHERS');
		}
		break ;
	
	case 'PUBLISHERS':
		if (!isset($count) || !$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM publishers", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			//On controle qu'il n'y a pas d'autorité à enlever
			$req="SELECT id_authority FROM authorities LEFT JOIN publishers ON num_object=ed_id WHERE type_object='".AUT_TABLE_PUBLISHERS."' AND ed_id IS NULL";
			$res = pmb_mysql_query($req,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				while($aut = pmb_mysql_fetch_row($res)){
					$authority = new authority($aut[0]);
					$authority->delete();
				}
			}
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_publishers"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT ed_id as id from publishers LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/publishers/champs_base.xml", "authorities", AUT_TABLE_PUBLISHERS);
			$indexation_authority->set_deleted_index(true);
			while($row = pmb_mysql_fetch_object($query)) {
				$indexation_authority->maj($row->id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'PUBLISHERS', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_publishers"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_publishers"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'CATEGORIES');
		}
		break ;
	
	case 'CATEGORIES':
		if (!isset($count) || !$count) {
			$elts = pmb_mysql_query("SELECT count(distinct num_noeud) FROM categories", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			//On controle qu'il n'y a pas d'autorité à enlever
			$req="SELECT id_authority FROM authorities LEFT JOIN categories ON num_object=num_noeud WHERE type_object='".AUT_TABLE_CATEG."' AND num_noeud IS NULL";
			$res = pmb_mysql_query($req,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				while($aut = pmb_mysql_fetch_row($res)){
					$authority = new authority($aut[0]);
					$authority->delete();
				}
			}
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_categories"], ENT_QUOTES, $charset)."</h2>";
		
		$req = "select distinct num_noeud as id from categories limit $start, $lot ";
		$query = pmb_mysql_query($req, $dbh);
		 
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/categories/champs_base.xml", "authorities", AUT_TABLE_CATEG);
			$indexation_authority->set_deleted_index(true);
			while($row = pmb_mysql_fetch_object($query)) {
				$indexation_authority->maj($row->id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'CATEGORIES', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_categories"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_categories"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'COLLECTIONS');
		}
		break ;
	
	case 'COLLECTIONS':
		if (!isset($count) || !$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM collections", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			//On controle qu'il n'y a pas d'autorité à enlever
			$req="SELECT id_authority FROM authorities LEFT JOIN collections ON num_object=collection_id WHERE type_object='".AUT_TABLE_COLLECTIONS."' AND collection_id IS NULL";
			$res = pmb_mysql_query($req,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				while($aut = pmb_mysql_fetch_row($res)){
					$authority = new authority($aut[0]);
					$authority->delete();
				}
			}
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_collections"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT collection_id as id from collections LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/collections/champs_base.xml", "authorities", AUT_TABLE_COLLECTIONS);
			$indexation_authority->set_deleted_index(true);
			while($row = pmb_mysql_fetch_object($query)) {
				$indexation_authority->maj($row->id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'COLLECTIONS', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_collections"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_collections"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'SUBCOLLECTIONS');
		}
		break ;
	
	case 'SUBCOLLECTIONS':
		if (!isset($count) || !$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM sub_collections", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			//On controle qu'il n'y a pas d'autorité à enlever
			$req="SELECT id_authority FROM authorities LEFT JOIN sub_collections ON num_object=sub_coll_id WHERE type_object='".AUT_TABLE_SUB_COLLECTIONS."' AND sub_coll_id IS NULL";
			$res = pmb_mysql_query($req,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				while($aut = pmb_mysql_fetch_row($res)){
					$authority = new authority($aut[0]);
					$authority->delete();
				}
			}
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_sub_collections"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT sub_coll_id as id from sub_collections LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/subcollections/champs_base.xml", "authorities", AUT_TABLE_SUB_COLLECTIONS);
			$indexation_authority->set_deleted_index(true);
			while($row = pmb_mysql_fetch_object($query)) {
				$indexation_authority->maj($row->id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'SUBCOLLECTIONS', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_sub_collections"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_sub_collections"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'SERIES');
		}
		break ;
	
	case 'SERIES':
		if (!isset($count) || !$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM series", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			//On controle qu'il n'y a pas d'autorité à enlever
			$req="SELECT id_authority FROM authorities LEFT JOIN series ON num_object=serie_id WHERE type_object='".AUT_TABLE_SERIES."' AND serie_id IS NULL";
			$res = pmb_mysql_query($req,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				while($aut = pmb_mysql_fetch_row($res)){
					$authority = new authority($aut[0]);
					$authority->delete();
				}
			}
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_series"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT serie_id as id from series LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/series/champs_base.xml", "authorities", AUT_TABLE_SERIES);
			$indexation_authority->set_deleted_index(true);
			while($row = pmb_mysql_fetch_object($query)) {
				$indexation_authority->maj($row->id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'SERIES', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_series"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_series"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'DEWEY');
		}
		break ;
	
	case 'DEWEY':
		if (!isset($count) || !$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM indexint", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			//On controle qu'il n'y a pas d'autorité à enlever
			$req="SELECT id_authority FROM authorities LEFT JOIN indexint ON num_object=indexint_id WHERE type_object='".AUT_TABLE_INDEXINT."' AND indexint_id IS NULL";
			$res = pmb_mysql_query($req,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				while($aut = pmb_mysql_fetch_row($res)){
					$authority = new authority($aut[0]);
					$authority->delete();
				}
			}
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_indexint"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT indexint_id as id from indexint LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/indexint/champs_base.xml", "authorities", AUT_TABLE_INDEXINT);
			$indexation_authority->set_deleted_index(true);
			while($row = pmb_mysql_fetch_object($query)) {
				$indexation_authority->maj($row->id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'DEWEY', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_indexint"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_indexint"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'TITRES_UNIFORMES');
		}
		break ;
	
	case 'TITRES_UNIFORMES':
		if (!isset($count) || !$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM titres_uniformes", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			//On controle qu'il n'y a pas d'autorité à enlever
			$req="SELECT id_authority FROM authorities LEFT JOIN titres_uniformes ON num_object=tu_id WHERE type_object='".AUT_TABLE_TITRES_UNIFORMES."' AND tu_id IS NULL";
			$res = pmb_mysql_query($req,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				while($aut = pmb_mysql_fetch_row($res)){
					$authority = new authority($aut[0]);
					$authority->delete();
				}
			}
		}
	
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_titres_uniformes"], ENT_QUOTES, $charset)."</h2>";
	
		$query = pmb_mysql_query("SELECT tu_id as id from titres_uniformes LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
				
			$indexation_authority = new indexation_authority($include_path."/indexation/authorities/titres_uniformes/champs_base.xml", "authorities", AUT_TABLE_TITRES_UNIFORMES);
			$indexation_authority->set_deleted_index(true);
			while($row = pmb_mysql_fetch_object($query)) {
				$indexation_authority->maj($row->id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'TITRES_UNIFORMES', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_titres_uniformes"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_titres_uniformes"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'AUTHPERSO');
		}
		break ;
		
	case 'AUTHPERSO':
		if (!isset($count) || !$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM authperso_authorities", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
			//On controle qu'il n'y a pas d'autorité à enlever
			$req="SELECT id_authority FROM authorities LEFT JOIN authperso_authorities ON num_object=id_authperso_authority WHERE type_object ='".AUT_TABLE_AUTHPERSO."' AND id_authperso_authority IS NULL";
			$res = pmb_mysql_query($req,$dbh);
			if($res && pmb_mysql_num_rows($res)){
				while($aut = pmb_mysql_fetch_row($res)){
					$authority = new authority($aut[0]);
					$authority->delete();
				}
			}
		}
	
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_authperso"], ENT_QUOTES, $charset)."</h2>";
	
		$query = pmb_mysql_query("SELECT id_authperso_authority as id, authperso_authority_authperso_num from authperso_authorities ORDER BY authperso_authority_authperso_num LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
	
			$id_authperso = 0;
			while($row = pmb_mysql_fetch_object($query)) {
				if(!$id_authperso || ($id_authperso != $row->authperso_authority_authperso_num)) {
					$indexation_authperso = new indexation_authperso($include_path."/indexation/authorities/authperso/champs_base.xml", "authorities", (1000+$row->authperso_authority_authperso_num), $row->authperso_authority_authperso_num);
					$indexation_authperso->set_deleted_index(true);
					$id_authperso = $row->authperso_authority_authperso_num;
				}				
				$indexation_authperso->maj($row->id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'AUTHPERSO', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_authperso"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_authperso"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'FINI');
		}
		break ;
			
	case 'FINI':
		$spec = $spec - INDEX_AUTHORITIES;
		pmb_mysql_query("ALTER TABLE authorities_words_global_index ENABLE KEYS",$dbh);
		pmb_mysql_query("ALTER TABLE authorities_fields_global_index ENABLE KEYS",$dbh);
		$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_fini"], ENT_QUOTES, $charset);
		print "
			<form class='form-$current_module' name='process_state' action='./clean.php?spec=$spec&start=0' method='post'>
				<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
			</form>
			<script type=\"text/javascript\"><!--
				setTimeout(\"document.forms['process_state'].submit()\",1000);
				-->
			</script>";
		break ;
}
