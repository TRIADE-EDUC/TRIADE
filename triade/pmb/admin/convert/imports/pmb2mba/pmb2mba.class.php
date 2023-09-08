<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb2mba.class.php,v 1.2 2018-08-10 12:52:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/serial_display.class.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($base_path."/admin/convert/convert.class.php");

class pmb2mba extends convert {

	protected static function mba_isbd($obj){
		global $dbh, $base_path;
		global $msg;
		global $tdoc;
		global $fonction_auteur;
		global $charset;
		global $thesaurus_mode_pmb, $thesaurus_categories_categ_in_line, $pmb_keyword_sep, $thesaurus_categories_affichage_ordre;
		global $load_tablist_js;
		global $lang;
		global $categories_memo,$libelle_thesaurus_memo;
		global $categories_top,$use_opac_url_base,$thesaurus_categories_show_only_last;
		global $categ;
		global $id_empr;
		global $pmb_show_notice_id;
		global $sort_children;
		global $pmb_resa_planning;
		global $pmb_etat_collections_localise,$pmb_droits_explr_localises,$explr_visible_mod ;
	
		if($obj->notice->niveau_biblio == "m"){
	
			// constitution de la mention de titre
			if($obj->tit_serie) {
				if ($obj->print_mode) $display_isbd = $obj->tit_serie;
					else $display_isbd = $obj->tit_serie_lien_gestion;
				if($obj->notice->tnvol)
					$display_isbd .= ',&nbsp;'.$obj->notice->tnvol;
			}
			$display_isbd ? $display_isbd .= '.&nbsp;'.$obj->tit1 : $display_isbd = $obj->tit1;
	
			$tit2 = $obj->notice->tit2;
			$tit3 = $obj->notice->tit3;
			$tit4 = $obj->notice->tit4;
			if($tit3) $display_isbd .= "&nbsp;= $tit3";
			if($tit4) $display_isbd .= "&nbsp;: $tit4";
			if($tit2) $display_isbd .= "&nbsp;; $tit2";
			$display_isbd .= ' ['.$tdoc->table[$obj->notice->typdoc].']';
	
			$mention_resp = array() ;
	
			// constitution de la mention de responsabilité
			//$obj->responsabilites
			$as = array_search ("0", $obj->responsabilites["responsabilites"]) ;
			if ($as!== FALSE && $as!== NULL) {
				$auteur_0 = $obj->responsabilites["auteurs"][$as] ;
				$auteur = new auteur($auteur_0["id"]);
				if ($obj->print_mode) $mention_resp_lib = $auteur->get_isbd();
				else $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
				if (!$obj->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
	//			if ($auteur_0["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_0["fonction"]];
				$mention_resp[] = $mention_resp_lib ;
			}
	
			$as = array_keys ($obj->responsabilites["responsabilites"], "1" ) ;
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $obj->responsabilites["auteurs"][$indice] ;
				$auteur = new auteur($auteur_1["id"]);
				if ($obj->print_mode) $mention_resp_lib = $auteur->get_isbd();
				else $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
				if (!$obj->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
	//			if ($auteur_1["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_1["fonction"]];
				$mention_resp[] = $mention_resp_lib ;
			}
	
			$as = array_keys ($obj->responsabilites["responsabilites"], "2" ) ;
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_2 = $obj->responsabilites["auteurs"][$indice] ;
				$auteur = new auteur($auteur_2["id"]);
				if ($obj->print_mode) $mention_resp_lib = $auteur->get_isbd();
				else $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
				if (!$obj->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
	//			if ($auteur_2["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_2["fonction"]];
				$mention_resp[] = $mention_resp_lib ;
			}
	
			$libelle_mention_resp = implode ("; ",$mention_resp) ;
			if($libelle_mention_resp) $display_isbd .= "&nbsp;/ $libelle_mention_resp" ;
	
			// mention d'édition
			if($obj->notice->mention_edition) $display_isbd .= ".&nbsp;-&nbsp;".$obj->notice->mention_edition;
	
			// zone de l'adresse
			// on récupère la collection au passage, si besoin est
			if($obj->notice->subcoll_id) {
				$collection = new subcollection($obj->notice->subcoll_id);
				$ed_obj = new editeur($collection->editeur) ;
				if ($obj->print_mode) {
					$editeurs .= $ed_obj->get_isbd();
					$collections = $collection->get_isbd();
				} else {
					$editeurs .= $ed_obj->isbd_entry_lien_gestion;
					$collections = $collection->isbd_entry_lien_gestion;
				}
			} elseif ($obj->notice->coll_id) {
				$collection = new collection($obj->notice->coll_id);
				$ed_obj = new editeur($collection->parent) ;
				if ($obj->print_mode) {
					$editeurs .= $ed_obj->get_isbd();
					$collections = $collection->get_isbd();
				} else {
					$editeurs .= $ed_obj->isbd_entry_lien_gestion;
					$collections = $collection->isbd_entry_lien_gestion;
				}
			} elseif ($obj->notice->ed1_id) {
				$editeur = new editeur($obj->notice->ed1_id);
				if ($obj->print_mode) $editeurs .= $editeur->get_isbd();
				else $editeurs .= $editeur->isbd_entry_lien_gestion;
			}
	
			if($obj->notice->ed2_id) {
				$editeur = new editeur($obj->notice->ed2_id);
				if ($obj->print_mode) $ed_isbd=$editeur->get_isbd();
				else $ed_isbd=$editeur->isbd_entry_lien_gestion;
				$editeurs ? $editeurs .= '&nbsp;; '.$ed_isbd : $editeurs = $ed_isbd;
				}
	
			if($obj->notice->year) $editeurs ? $editeurs .= ', '.$obj->notice->year : $editeurs = $obj->notice->year;
			elseif ($obj->notice->niveau_biblio!='b') $editeurs ? $editeurs .= ', [s.d.]' : $editeurs = "[s.d.]";
	
	
			if ($editeurs) $display_isbd .= ".&nbsp;-&nbsp;$editeurs";
	
	
	// 		// zone de la collation (ne concerne que a2)
	// 		if($obj->notice->npages)
	// 			$collation = $obj->notice->npages;
	// 		if($obj->notice->ill)
	// 			$collation .= ': '.$obj->notice->ill;
	// 		if($obj->notice->size)
	// 			$collation .= '; '.$obj->notice->size;
	// 		if($obj->notice->accomp)
	// 			$collation .= '+ '.$obj->notice->accomp;
	
	// 		if($collation)
	// 			$display_isbd .= ".&nbsp;-&nbsp;$collation";
	
	
			if($collections) {
				if($obj->notice->nocoll) $collections .= '; '.$obj->notice->nocoll;
				$display_isbd .= ".&nbsp;-&nbsp;($collections)".' ';
				}
			if(substr(trim($display_isbd), -1) != "."){
				$display_isbd .= '.';
			}
	
	
		//	// note générale
		//	if($obj->notice->n_gen)
		// 		$zoneNote = nl2br(htmlentities($obj->notice->n_gen,ENT_QUOTES, $charset)).' ';
		//
		//	// ISBN ou NO. commercial
		//	if($obj->notice->code) {
		//		if(isISBN($obj->notice->code)) {
		//			if ($zoneNote) {
		//				$zoneNote .= '.&nbsp;-&nbsp;ISBN ';
		//			} else {
		//				$zoneNote = 'ISBN ';
		//			}
		//		} else {
		//			if($zoneNote) $zoneNote .= '.&nbsp;-&nbsp;';
		//		}
		//		$zoneNote .= $obj->notice->code;
		//	}
	
	// 		demande de retrait le 5/2/13
	// 		if($obj->notice->prix) {
	// 			if($obj->notice->code) {$zoneNote .= '&nbsp;: '.$obj->notice->prix;}
	// 			else {
	// 				if ($zoneNote) 	{ $zoneNote .= '&nbsp; '.$obj->notice->prix;}
	// 				else	{ $zoneNote = $obj->notice->prix;}
	// 			}
	// 		}
	// 		if($zoneNote) $display_isbd .= "<br /><br />$zoneNote.";
	
	// 		//In
			//Recherche des notices parentes
			if (!$obj->no_link) {
				$notice_relations = notice_relations_collection::get_object_instance($obj->notice_id);
				$display_isbd.= $notice_relations->get_display_links('parents', $obj->print_mode, $obj->show_explnum, $obj->show_statut, $obj->show_opac_hidden_fields);
			}
	
			// niveau 1
			if($obj->level == 1) {
				if(!$obj->print_mode) $display_isbd .= "<!-- !!bouton_modif!! -->";
				if ($obj->expl) {
					$display_isbd .= "<br /><b>${msg[285]}</b>";
					$display_isbd .= $obj->show_expl_per_notice($obj->notice->notice_id, $obj->link_expl);
					if ($obj->show_explnum) {
						$explnum_assoc = show_explnum_per_notice($obj->notice->notice_id, 0,$obj->link_explnum);
						if ($explnum_assoc) $display_isbd .= "<b>$msg[explnum_docs_associes]</b>".$explnum_assoc;
					}
				}
				if($obj->show_resa) {
					$aff_resa=resa_list ($obj->notice_id, 0, 0) ;
					if ($aff_resa) $display_isbd .= "<b>$msg[resas]</b>".$aff_resa;
				}
				if($obj->show_planning && $pmb_resa_planning) {
					$aff_resa_planning=planning_list(0,$obj->notice_id) ;
					if ($aff_resa_planning)	$display_isbd .= "<b>$msg[resas_planning]</b>".$aff_resa_planning;
				}
			}
		}else{
			//pour le reste...
			$display_isbd = $obj->notice->tit1;
	
			// constitution de la mention de titre
			$tit3 = $obj->notice->tit3;
			$tit4 = $obj->notice->tit4;
			if($tit3) $display_isbd .= "&nbsp;= $tit3";
			if($tit4) $display_isbd .= "&nbsp;: $tit4";
			$display_isbd .= ' ['.$tdoc->table[$obj->notice->typdoc].']';
			// constitution de la mention de responsabilité
	
			$mention_resp = array() ;
	
			// constitution de la mention de responsabilité
			//$obj->responsabilites
			$as = array_search ("0", $obj->responsabilites["responsabilites"]) ;
			if ($as!== FALSE && $as!== NULL) {
				$auteur_0 = $obj->responsabilites["auteurs"][$as] ;
				$auteur = new auteur($auteur_0["id"]);
				if ($obj->print_mode)
					$mention_resp_lib = $auteur->get_isbd();
				else
					$mention_resp_lib = $auteur->isbd_entry_lien_gestion;
				if (!$obj->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
	//			if ($auteur_0["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_0["fonction"]];
				$mention_resp[] = $mention_resp_lib ;
			}
	
			$as = array_keys ($obj->responsabilites["responsabilites"], "1" ) ;
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $obj->responsabilites["auteurs"][$indice] ;
				$auteur = new auteur($auteur_1["id"]);
				if ($obj->print_mode)
					$mention_resp_lib = $auteur->get_isbd();
				else
					$mention_resp_lib = $auteur->isbd_entry_lien_gestion;
				if (!$obj->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
	//			if ($auteur_1["fonction"]) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_1["fonction"]];
				$mention_resp[] = $mention_resp_lib ;
			}
	
			$as = array_keys ($obj->responsabilites["responsabilites"], "2" ) ;
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_2 = $obj->responsabilites["auteurs"][$indice] ;
				$auteur = new auteur($auteur_2["id"]);
				if ($obj->print_mode)
					$mention_resp_lib = $auteur->get_isbd();
				else
					$mention_resp_lib = $auteur->isbd_entry_lien_gestion;
				if (!$obj->print_mode) $mention_resp_lib .= $auteur->author_web_link ;
	//			if ($auteur_2["fonction"])
	//				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_2["fonction"]];
				$mention_resp[] = $mention_resp_lib ;
			}
	
			$libelle_mention_resp = implode ("; ",$mention_resp) ;
			if($libelle_mention_resp)
				$display_isbd .= "&nbsp;/ ". $libelle_mention_resp ." " ;
	
			// zone de l'adresse (ne concerne que s1)
			if ($obj->notice->niveau_biblio == 's' && $obj->notice->niveau_hierar == 1) {
				if($obj->notice->ed1_id) {
					$editeur = new editeur($obj->notice->ed1_id);
					if ($obj->print_mode)
						$editeurs .= $editeur->get_isbd();
					else
						$editeurs .= $editeur->isbd_entry_lien_gestion;
				}
				if($obj->notice->ed2_id) {
					$editeur = new editeur($obj->notice->ed2_id);
					if ($obj->print_mode) $ed_isbd=$editeur->get_isbd(); else $ed_isbd=$editeur->isbd_entry_lien_gestion;
					if($editeurs)
						$editeurs .= '&nbsp;; '.$ed_isbd;
					else
						$editeurs .= $ed_isbd;
				}
	
				if($obj->notice->year)
					$editeurs ? $editeurs .= ', '.$obj->notice->year : $editeurs = $obj->notice->year;
				//else
					//$editeurs ? $editeurs .= ', [s.d.]' : $editeurs = "[s.d.]";
	
				if($editeurs)
					$display_isbd .= ".&nbsp;-&nbsp;$editeurs";
					// code ici pour la gestion des éditeurs
			}
	
			// zone de la collation (ne concerne que a2, mention de pagination)
			// pour les périodiques, on rebascule en zone de note
			// avec la mention du périodique parent
			if($obj->notice->niveau_biblio == 'a' && $obj->notice->niveau_hierar == 2) {
	
				$bulletin = $obj->parent_title;
				if($obj->parent_numero) {
					$bulletin .= ' '.$obj->parent_numero;
				}
				// affichage de la mention de date utile : mention_date si existe, sinon date_date
				if ($obj->parent_date)
					$date_affichee = " (".$obj->parent_date.")";
				else if ($obj->parent_date_date)
					$date_affichee .= " [".formatdate($obj->parent_date_date)."]";
				else
					$date_affichee="" ;
				$bulletin .= $date_affichee;
	
				if($obj->action_bulletin) {
					$obj->action_bulletin = str_replace('!!id!!', $obj->bul_id, $obj->action_bulletin);
					$bulletin = "<a href=\"".$obj->action_bulletin."\">".htmlentities($bulletin,ENT_QUOTES, $charset)."</a>";
				}
				$mention_parent = "in <b>$bulletin</b>";
			}
	
			if($mention_parent) {
				$display_isbd .= "<br />$mention_parent";
				$pagination = htmlentities($obj->notice->npages,ENT_QUOTES, $charset);
				if($pagination)
					$display_isbd .= ".&nbsp;-&nbsp;$pagination";
			}
	
			//In
			//Recherche des notices parentes
			if (!$obj->no_link) {
				$notice_relations = notice_relations_collection::get_object_instance($obj->notice_id);
				$display_isbd.= $notice_relations->get_display_links('parents', $obj->print_mode, $obj->show_explnum, $obj->show_statut, $obj->show_opac_hidden_fields);
			}
	
			// fin du niveau 1
			if($obj->level == 1) {
				if ($obj->show_explnum) {
					$explnum = show_explnum_per_notice($obj->notice_id, 0, $obj->lien_explnum);
					if ($explnum) $display_isbd .= "<br /><b>$msg[explnum_docs_associes]</b><br />".$explnum ;
					if ($obj->notice->niveau_biblio == 'a' && $obj->notice->niveau_hierar == '2' && (SESSrights & CATALOGAGE_AUTH) && $obj->bouton_explnum) $display_isbd .= "<br /><input type='button' class='bouton' value=' $msg[explnum_ajouter_doc] ' onClick=\"document.location='".$base_path."/catalog.php?categ=serials&analysis_id=$obj->notice_id&sub=analysis&action=explnum_form&bul_id=$obj->bul_id'\">" ;
				}
			}
		}
		return $display_isbd;
	}

	public static function _export_notice_($id,$keep_expl=0,$params=array()) {
		global $charset,$msg;
		if(!$id) return;
		$requete = "select * from notices where notice_id=".$id;
		$resultat = pmb_mysql_query($requete);
		$res = pmb_mysql_fetch_object($resultat);
	
		$environement["short"] = 1;
		$environement["ex"] = 0;
		$environement["exnum"] = 0;
		$environement["link"] = "" ;
		$environement["link_analysis"] = "" ;
		$environement["link_explnum"] = "" ;
		$environement["link_bulletin"] = "" ;
	
		if($res->niveau_biblio != 's' && $res->niveau_biblio != 'a') {
			$display = new mono_display($id, $environement["short"], $environement["link"], $environement["ex"], $environement["link_expl"], '', $environement["link_explnum"],0,1);
			//récup des infos bulletins: bulletin_cb
			$requete = "select * from bulletins where num_notice=".$id;
			$resultat_bul = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($resultat_bul)){
				$res_bul = pmb_mysql_fetch_object($resultat_bul);
				$bulletin_cb=$res_bul->bulletin_cb;
			}
		} else {
			// on a affaire à un périodique
			$display = new serial_display($id, $environement["short"], $environement["link_serial"], $environement["link_analysis"], $environement["link_bulletin"], "", $environement["link_explnum"], 0, 0, 1, 1, true, 1);
		}
	
		//Champs personalisés
		$p_perso=new parametres_perso("notices");
		$perso_aff = $titre = $loc = $etablissement = $date = "" ;
		if (!$p_perso->no_special_fields) {
			$perso_=$p_perso->show_fields($id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]) {
					if($p["NAME"] == "t_d_f_titre")$titre=$p["AFF"];
					elseif($p["NAME"] == "t_d_f_lieu_etabl")$loc=$p["AFF"];
					elseif($p["NAME"] == "t_d_f_etablissement")$etablissement=$p["AFF"];
					elseif($p["NAME"] == "t_d_f_date")$date=$p["AFF"];
	
				}
			}
		}
	
		if($titre)$perso_aff=$titre;
		if($perso_aff && $loc){
			$loc = explode("/",$loc);
			$perso_aff.=" ";
			$perso_aff.=$loc[0];
		}
		if($perso_aff && $date)$perso_aff.=", ";
		$perso_aff.=$date;
		if ($perso_aff) {
			$titre_de_forme = $msg["n_titre_de_forme"]."[".$perso_aff."] " ;
		}
	
		// langues
		$langues="";
		if(count($display->langues)) {
			$langues = $msg[537]." : ".construit_liste_langues($display->langues);
		}
	//	if(count($display->languesorg)) {
	//		$langues .= $msg[711]." : ".construit_liste_langues($display->languesorg);
	//	}
		if($langues)	$langues="\n".$langues;
	
	
		$notice="<notice>\n";
	
		//notice (ID)
		$notice.="<ID>$id</ID>\n";
	
		//isbn (ISBN)
		if ($display->isbn) {
			$notice.="<ISBN>".htmlspecialchars($display->isbn,ENT_QUOTES,$charset)."</ISBN>\n";
		} elseif($bulletin_cb){
			$notice.="<ISBN>".htmlspecialchars($bulletin_cb,ENT_QUOTES,$charset)."</ISBN>\n";
		}
		//Année publication(YEAR)
		if ($display->notice->year) {
			$notice.="<YEAR>".htmlspecialchars($display->notice->year,ENT_QUOTES,$charset)."</YEAR>\n";
		}
		//isbd(ISBD)
		$display_isbd = static::mba_isbd($display);
		//if ($display->isbd) {
		//	$isbd=str_replace("<br />","\n",$titre_de_forme.$display->isbd.$langues);
		$isbd=str_replace("<br />"," ",$titre_de_forme.$display_isbd);
		if($display->notice->lien) {
			$isbd.= " ".$display->notice->lien;
		}
		$isbd=strip_tags($isbd);
		$notice.="<ISBD>".htmlspecialchars(html_entity_decode($isbd,ENT_QUOTES,$charset),ENT_QUOTES,$charset)."</ISBD>\n";
		//}
	
		$notice.="</notice>\n";
	
		return $notice;
	}
}
