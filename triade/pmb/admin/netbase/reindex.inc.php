<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex.inc.php,v 1.37 2017-11-22 11:07:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/notice.class.php");
require_once("$class_path/stemming.class.php");
require_once("$class_path/double_metaphone.class.php");
require_once($class_path."/authperso.class.php");
require_once($class_path."/custom_parametres_perso.class.php");

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if (!isset($start)) $start=0;

$v_state=urldecode($v_state);

// on commence par :
if (!isset($index_quoi)) $index_quoi='NOTICES';
if (!isset($count)) $count = 0;
	
switch ($index_quoi) {
	case 'NOTICES':
	
		if (!$count) {
			$notices = pmb_mysql_query("SELECT count(1) FROM notices", $dbh);
			$count = pmb_mysql_result($notices, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_notices"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT notice_id FROM notices LIMIT $start, $lot");
		if(pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				notice::majNotices($row->notice_id);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'NOTICES', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
				$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_notices"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_notices"], ENT_QUOTES, $charset);
				print netbase::get_current_state_form($v_state, $spec, 'AUTEURS');
		}
	
		break ;
	
	case 'AUTEURS':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM authors", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_authors"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT author_id as id,concat(author_name,' ',author_rejete,' ', author_lieu, ' ',author_ville,' ',author_pays,' ',author_numero,' ',author_subdivision) as auteur from authors LIMIT $start, $lot", $dbh);
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_chars($row->auteur); 
				$req_update = "UPDATE authors ";
				$req_update .= " SET index_author=' ${ind_elt} '";
				$req_update .= " WHERE author_id=$row->id ";
				$update = pmb_mysql_query($req_update, $dbh);
				}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'AUTEURS', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_authors"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_authors"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'EDITEURS');
		}
		break ;
	
	case 'EDITEURS':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM publishers", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_publishers"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT ed_id as id, ed_name as publisher, ed_ville, ed_pays from publishers LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_chars($row->publisher." ".$row->ed_ville." ".$row->ed_pays); 
				$req_update = "UPDATE publishers ";
				$req_update .= " SET index_publisher=' ${ind_elt} '";
				$req_update .= " WHERE ed_id=$row->id ";
				$update = pmb_mysql_query($req_update);
				}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'EDITEURS', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_publishers"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_publishers"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'CATEGORIES');
		}
		break ;
	
	case 'CATEGORIES':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM categories", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_categories"], ENT_QUOTES, $charset)."</h2>";
		
		$req = "select num_noeud, langue, libelle_categorie from categories limit $start, $lot ";
		$query = pmb_mysql_query($req, $dbh);
		 
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while($row = pmb_mysql_fetch_object($query)) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->libelle_categorie, $row->langue); 
				
				$req_update = "UPDATE categories ";
				$req_update.= "SET index_categorie=' ${ind_elt} '";
				$req_update.= "WHERE num_noeud='".$row->num_noeud."' and langue='".$row->langue."' ";
				$update = pmb_mysql_query($req_update);
				
				
				//ajout des mots des termes dans la table words pour l autoindexation
				$t_words = array();
				$i = 0;
				$t_row = explode(' ',$ind_elt);
				if( is_array($t_row) && count($t_row) ) {
					$t_row = array_unique($t_row);
					foreach($t_row as $w) {
						if($w) {
							$t_words[$i]['word'] = $w;
							$t_words[$i]['lang'] = $row->langue;
							$i++;
						}
					}
				}
				if(count($t_words)) {
					//calcul de stem et double_metaphone
					foreach ($t_words as $i=>$w) {
						$q1 = "select id_word from words where word='".addslashes($w['word'])."' and lang='".addslashes($w['lang'])."' limit 1";
						$r1 = pmb_mysql_query($q1, $dbh);
						if(pmb_mysql_num_rows($r1)) {
							//le mot existe
							$t_words[$i]['allready_exists']=1;
						} else {
							//le mot n'existe pas
							$dmeta = new DoubleMetaPhone($w['word']);
							if($dmeta->primary || $dmeta->secondary){
								$t_words[$i]['double_metaphone'] = $dmeta->primary." ".$dmeta->secondary;
							}
							if($w['lang']=='fr_FR') {
								$stemming = new stemming($w['word']);
								$t_words[$i]['stem']=$stemming->stem;
							} else {
								$t_words[$i]['stem']='';
							}
						}
					}
					foreach($t_words as $i=>$w) {
						if (!$w['allready_exists']) {
							$q2 = "insert ignore into words (word, lang, double_metaphone, stem) values ('".$w['word']."', '".$w['lang']."', '".$w['double_metaphone']."', '".$w['stem']."') ";
							pmb_mysql_query($q2,$dbh);
						}
					}
				}
				
				
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
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM collections", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_collections"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT collection_id as id, collection_name as collection, collection_issn from collections LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->collection); 
				if($tmp = $row->collection_issn){
					$ind_elt .= " ".strip_empty_words($tmp); 
				}
				
				$req_update = "UPDATE collections ";
				$req_update .= " SET index_coll=' ${ind_elt} '";
				$req_update .= " WHERE collection_id=$row->id ";
				$update = pmb_mysql_query($req_update);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'COLLECTIONS', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_collections"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_collections"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'SOUSCOLLECTIONS');
		}
		break ;
	
	case 'SOUSCOLLECTIONS':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM sub_collections", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_sub_collections"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT sub_coll_id as id, sub_coll_name as sub_collection, sub_coll_issn from sub_collections LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->sub_collection); 
				if($tmp = $row->sub_coll_issn){
					$ind_elt .= " ".strip_empty_words($tmp); 
				}
				$req_update = "UPDATE sub_collections ";
				$req_update .= " SET index_sub_coll=' ${ind_elt} '";
				$req_update .= " WHERE sub_coll_id=$row->id ";
				$update = pmb_mysql_query($req_update);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'SOUSCOLLECTIONS', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_sub_collections"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_sub_collections"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'SERIES');
		}
		break ;
	
	case 'SERIES':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM series", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_series"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT serie_id as id, serie_name from series LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->serie_name); 
				
				$req_update = "UPDATE series ";
				$req_update .= " SET serie_index=' ${ind_elt} '";
				$req_update .= " WHERE serie_id=$row->id ";
				$update = pmb_mysql_query($req_update);
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
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM indexint", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_indexint"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT indexint_id as id, concat(indexint_name,' ',indexint_comment) as index_indexint from indexint LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->index_indexint); 
				
				$req_update = "UPDATE indexint ";
				$req_update .= " SET index_indexint=' ${ind_elt} '";
				$req_update .= " WHERE indexint_id=$row->id ";
				$update = pmb_mysql_query($req_update);
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
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM titres_uniformes", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_tu"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT tu_id from titres_uniformes ORDER BY 1 LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
					
			while(($row = pmb_mysql_fetch_object($query))) {
				
				titre_uniforme::update_index_tu($row->tu_id);

			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'TITRES_UNIFORMES', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_tu"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_tu"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'FRAIS_ANNEXES');
		}
		break ;
	
	case 'FRAIS_ANNEXES':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM frais", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_frais_annexes"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT id_frais as id, libelle from frais LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while(($row = pmb_mysql_fetch_object($query))) {
				// constitution des pseudo-indexes
				$ind_elt = strip_empty_words($row->libelle); 
				
				$req_update = "UPDATE frais ";
				$req_update .= " SET index_libelle=' ${ind_elt} '";
				$req_update .= " WHERE id_frais=$row->id ";
				$update = pmb_mysql_query($req_update);
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'FRAIS_ANNEXES', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_frais_annexes"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_frais_annexes"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'AUTHPERSO');
		}
		break ;

	case 'AUTHPERSO':
		if (!$count) {
			$elts = pmb_mysql_query("SELECT count(1) FROM authperso_authorities", $dbh);
			$count = pmb_mysql_result($elts, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_authperso"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT id_authperso_authority as id, authperso_authority_authperso_num from authperso_authorities ORDER BY authperso_authority_authperso_num LIMIT $start, $lot");
		if (pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			$id_authperso = 0;
			while(($row = pmb_mysql_fetch_object($query))) {
				if(!$id_authperso || ($id_authperso != $row->authperso_authority_authperso_num)) {
					$authperso = new authperso($row->authperso_authority_authperso_num);
					$p_perso=new custom_parametres_perso("authperso","authperso",$row->authperso_authority_authperso_num);
				}
				$mots_perso=$p_perso->get_fields_recherche($row->id);
				if($mots_perso) {
					$infos_global = $mots_perso.' ';
					$infos_global_index = strip_empty_words($mots_perso).' ';
				} else {
					$infos_global = '';
					$infos_global_index = '';
				}
				pmb_mysql_query("update authperso_authorities set authperso_infos_global='".addslashes($infos_global)."', authperso_index_infos_global='".addslashes(' '.$infos_global_index)."' where id_authperso_authority=".$row->id);
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
		$spec = $spec - INDEX_NOTICES;
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
