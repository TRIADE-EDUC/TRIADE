<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acquisitions.inc.php,v 1.8 2017-11-22 11:07:34 dgoron Exp $


if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// la taille d'un paquet a traiter
$lot = ACQUISITION_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if (!isset($start)) $start=0;

// on commence par :
if (!isset($index_quoi)) $index_quoi='SUGGESTIONS';

$v_state=urldecode($v_state);

switch ($index_quoi) {
	
	case 'SUGGESTIONS':
		if (!$count) {
			$actes = pmb_mysql_query("SELECT count(1) FROM suggestions", $dbh);
			$count = pmb_mysql_result($actes, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_sug"], ENT_QUOTES, $charset)."</h2>";
	
		$query = pmb_mysql_query("SELECT id_suggestion, titre, editeur, auteur, code, commentaires FROM suggestions LIMIT ".$start.", ".$lot." ");
		if(pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while($row = pmb_mysql_fetch_object($query)) {
				
				// index acte
				$req_update = "UPDATE suggestions ";
				$req_update.= "SET index_suggestion = ' ".strip_empty_words($row->titre)." ".strip_empty_words($row->editeur)." ".strip_empty_words($row->auteur)." ".$row->code." ".strip_empty_words($row->commentaires)." ' ";
				$req_update.= "WHERE id_suggestion = ".$row->id_suggestion." ";
				$update = pmb_mysql_query($req_update);
				
		
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'SUGGESTIONS', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_sug"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_sug"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'ENTITES');
		}
	
		break ;

	case 'ENTITES' :
		if (!$count) {
			$entites = pmb_mysql_query("SELECT count(1) FROM entites", $dbh);
			$count = pmb_mysql_result($entites, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_ent"], ENT_QUOTES, $charset)."</h2>";
	
		$query = pmb_mysql_query("SELECT id_entite, raison_sociale FROM entites LIMIT ".$start.", ".$lot." ");
		if(pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while($row = pmb_mysql_fetch_object($query)) {
				
				// index acte
				$req_update = "UPDATE entites ";
				$req_update.= "SET index_entite = ' ".strip_empty_words($row->raison_sociale)." ' ";
				$req_update.= "WHERE id_entite = ".$row->id_entite." ";
				$update = pmb_mysql_query($req_update);
				
		
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'ENTITES', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_ent"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_ent"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'ACTES');
		}
	
		break ;

	case 'ACTES':
	
		if (!$count) {
			$actes = pmb_mysql_query("SELECT count(1) FROM actes", $dbh);
			$count = pmb_mysql_result($actes, 0, 0);
		}
		
		print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_act"], ENT_QUOTES, $charset)."</h2>";
		
		$query = pmb_mysql_query("SELECT actes.id_acte, actes.numero, entites.raison_sociale, actes.commentaires, actes.reference FROM actes, entites where num_fournisseur=id_entite LIMIT ".$start.", ".$lot." ");
		if(pmb_mysql_num_rows($query)) {
			print netbase::get_display_progress($start, $count);
			
			while($row = pmb_mysql_fetch_object($query)) {
				
				// index acte
				$req_update = "UPDATE actes ";
				$req_update.= "SET index_acte = ' ".$row->numero." ".strip_empty_words($row->raison_sociale)." ".strip_empty_words($row->commentaires)." ".strip_empty_words($row->reference)." ' ";
				$req_update.= "WHERE id_acte = ".$row->id_acte." ";
				$update = pmb_mysql_query($req_update);
				
				//index lignes_actes
				$query_2 = pmb_mysql_query("SELECT id_ligne, code, libelle FROM lignes_actes where num_acte = '".$row->id_acte."' ");
				if (pmb_mysql_num_rows($query_2)){
					while ($row_2 = pmb_mysql_fetch_object($query_2)) {
						
						$req_update_2 = "UPDATE lignes_actes ";
						$req_update_2.= "SET index_ligne = ' ".strip_empty_words($row_2->libelle)." ' ";
						$req_update_2.= "WHERE id_ligne = ".$row_2->id_ligne." ";
						$update_2 = pmb_mysql_query($req_update_2);
						
					}
					pmb_mysql_free_result($query_2);
				}			
			}
			pmb_mysql_free_result($query);
			$next = $start + $lot;
			print netbase::get_current_state_form($v_state, $spec, 'ACTES', $next, $count);
		} else {
			// mise à jour de l'affichage de la jauge
			print netbase::get_display_final_progress();
			$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_act"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_act"], ENT_QUOTES, $charset);
			print netbase::get_current_state_form($v_state, $spec, 'FINI');
		}
	
		break ;
	
	
	case 'FINI':
		$spec = $spec - INDEX_ACQUISITIONS;
		$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_acq_fini"],ENT_QUOTES,$charset);
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
