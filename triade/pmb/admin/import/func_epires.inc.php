<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_epires.inc.php,v 1.26 2022/05/25 07:21:57 dgoron Exp $

// DEBUT paramétrage propre à la base de données d'importation :
global $class_path; //Nécessaire pour certaines inclusions
require_once($class_path."/serials.class.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/categories.class.php");
require_once($class_path."/noeuds.class.php");

function recup_noticeunimarc_suite($notice) {
	global $info_464,$info_200,$info_676;
	global $info_900,$info_901,$info_902,$info_903,$info_904,$info_910;
	global $rs,$bl,$dt;

	$info_464="";
	$info_900="";
	$info_901="";
	$info_902="";
	$info_903="";
	$info_904="";
	$info_910="";
	$info_200="";
	$info_676="";

	$record = new iso2709_record($notice, AUTO_UPDATE);
	$rs=$record->inner_guide["rs"];
	$bl=$record->inner_guide["bl"];
	$dt=$record->inner_guide["dt"];

	for ($i=0;$i<count($record->inner_directory);$i++) {
		$cle=$record->inner_directory[$i]['label'];
		switch($cle) {
			case "464":
				//C'est un périodique donc un dépouillement ou une notice objet
				$info_464=$record->get_subfield($cle,"t","v","p","d","z","e","u");
				break;
			case "200":
				$info_200=$record->get_subfield($cle,"a");
			default:
				break;

		} /* end of switch */

	} /* end of for */

	$info_900=$record->get_subfield("900","a");
	$info_901=$record->get_subfield("901","a");
	$info_902=$record->get_subfield("902","a");
	$info_903=$record->get_subfield("903","a");
	$info_910=$record->get_subfield("910","a");
	$info_904=$record->get_subfield_array_array("904","a");
	$info_676=$record->get_subfield("676","a");

} // fin recup_noticeunimarc_suite = fin récupération des variables propres à la bretagne

//trouve un champ perso et renvoi so id
function trouve_champ_perso($nom) {
	$rqt = "SELECT idchamp FROM notices_custom WHERE name='" . addslashes($nom) . "'";
	$res = pmb_mysql_query($rqt);

	if (pmb_mysql_num_rows($res)>0)
		return pmb_mysql_result($res,0);
	else
		return 0;
}

//trouve le thesaurus avec le code et renvoi son id
function trouve_thesaurus($code) {
	$rqt = "SELECT num_thesaurus FROM noeuds WHERE autorite='" . $code . "'";
	$res = pmb_mysql_query($rqt);

	if (pmb_mysql_num_rows($res)>0)
		return pmb_mysql_result($res,0);
	else
		return 0;
}

function import_new_notice_suite() {
	global $notice_id ;

	global $info_464,$info_676 ;
	global $info_606_a,$info_606_x;
	global $info_900,$info_901,$info_902, $info_200,$info_903,$info_904,$info_910;
	global $rs,$bl,$dt;
	global $bulletin_ex;
	global $m_thess;

	//si on est en multi-thesaurus
	if (!$m_thess) {
		$rqt = "SELECT count(1) FROM thesaurus WHERE active=1";
	 	$m_thess = pmb_mysql_result(pmb_mysql_query($rqt),0,0);
	}

	//Cas des périodiques
	if (is_array($info_464) && trim($info_464[0]['t'])) {
		$requete="SELECT * FROM notices WHERE notice_id=$notice_id";
		$resultat=pmb_mysql_query($requete);
		$r=pmb_mysql_fetch_object($resultat);
		//Notice chapeau existe-t-elle ?
		$requete="SELECT notice_id FROM notices WHERE tit1='".addslashes($info_464[0]['t'])."' and niveau_hierar='1' and niveau_biblio='s'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			//Si oui, récupération id
			$chapeau_id=pmb_mysql_result($resultat,0,0);

			//Mise à jour du champ commentaire de gestion si nécessaire
			if ($info_903[0]) {
				$requete="UPDATE notices SET commentaire_gestion=concat(commentaire_gestion,' ','".addslashes($info_903[0])."') WHERE notice_id=$chapeau_id";
				pmb_mysql_query($requete);
			}

			//Bulletin existe-t-il ?
			$requete="SELECT bulletin_id FROM bulletins WHERE bulletin_numero='".addslashes($info_464[0]['v'])."' AND mention_date='".addslashes($info_464[0]['d'])."' AND bulletin_notice=$chapeau_id";
			$resultat=pmb_mysql_query($requete);
			//On lance une autre requête de test pour vérifier l'existence du bulletin (espace insécable dans le numéro ?)
			//#125734 : contenu du fichier PRISME en erreur
			$requete_clean_nbsp="SELECT bulletin_id FROM bulletins WHERE bulletin_numero='".addslashes(clean_nbsp($info_464[0]['v']))."' AND mention_date='".addslashes($info_464[0]['d'])."' AND bulletin_notice=$chapeau_id";
			$resultat_clean_nbsp=pmb_mysql_query($requete_clean_nbsp);
			
			if (pmb_mysql_num_rows($resultat)) {
				//Si oui, récupération id bulletin
				$bulletin_id=pmb_mysql_result($resultat,0,0);
			} elseif (pmb_mysql_num_rows($resultat_clean_nbsp)) {
				//Si oui, récupération id bulletin
				$bulletin_id=pmb_mysql_result($resultat_clean_nbsp,0,0);
			} else {
				//Si non, création bulletin
				$info=array();
				$bulletin=new bulletinage("",$chapeau_id);
				if ($info_464[0]['u'])
					$info['bul_titre'] = addslashes($info_464[0]['u']);
				else $info['bul_titre'] = addslashes("Bulletin ".$info_464[0]['v']);

				$info['bul_no'] = addslashes($info_464[0]['v']);
				$info['bul_date'] = addslashes($info_464[0]['d']);
				if (!$info_464[0]['e']) {
					if ($info_902[0])
						$info['date_date']=$info_902[0];
				} else
					$info['date_date']=$info_464[0]['e'];
				$bulletin_id=$bulletin->update($info);
			}
		} else {
			//Si non, création notice chapeau et bulletin
			$chapeau=new serial();
			$info=array();
			$info['tit1']=addslashes($info_464[0]['t']);
			$info['niveau_biblio']='s';
			$info['niveau_hierar']='1';
			$info['typdoc']=$r->typdoc;

			$chapeau->update($info);
			$chapeau_id=$chapeau->id;

			//Mise à jour du champ commentaire de gestion si nécessaire
			if ($info_903[0]) {
				$requete="UPDATE notices SET commentaire_gestion=concat(commentaire_gestion,' ','".addslashes($info_903[0])."') WHERE notice_id=$chapeau_id";
				pmb_mysql_query($requete);
			}

			$bulletin=new bulletinage("",$chapeau_id);
			$info=array();
			if ($info_464[0]['u'])
				$info['bul_titre']=addslashes($info_464[0]['u']);
			else $info['bul_titre']=addslashes("Bulletin ".$info_464[0]['v']);

			$info['bul_no']=addslashes($info_464[0]['v']);
			$info['bul_date']=addslashes($info_464[0]['d']);
			if (!$info_464[0]['e']) {
				if ($info_902[0])
					$info['date_date']=$info_902[0];
			} else
				$info['date_date']=$info_464[0]['e'];
			$bulletin_id=$bulletin->update($info);
		}

		//Passage de la notice en article
		$requete="UPDATE notices SET niveau_biblio='a', niveau_hierar='2', code='', npages='".addslashes($info_464[0]['p'])."' WHERE notice_id=$notice_id";
		pmb_mysql_query($requete);
		$requete="INSERT INTO analysis (analysis_bulletin,analysis_notice) VALUES($bulletin_id,$notice_id)";
		pmb_mysql_query($requete);
		$bulletin_ex=$bulletin_id;
	} else
		$bulletin_ex=0;


	//Traitement du thésaurus
	if ($m_thess>1) {
		//on est en multi-thesaurus
		for ($i=0; $i<count($info_606_a); $i++) {
			for ($j=0; $j<count($info_606_a[$i]); $j++) {
				$descripteur_tete=$info_606_a[$i][$j];
				$descripteur_fils=$info_606_x[$i][$j];

				//Recherche du thésaurus
				$thes_id=trouve_thesaurus($descripteur_tete);
				//Recherche du terme fils
				if ($thes_id>0) {
					$categ_id_fils=categories::searchLibelle(addslashes(trim($descripteur_fils)),$thes_id,"fr_FR");
					if (!$categ_id_fils) {
						//Création
						$new_thes=$thes_id==1?4:$thes_id;	//Choix du thesaurus Candidats descripteurs si descripteur inexistant
						$categ_id_fils=categories::searchLibelle(addslashes(trim($descripteur_fils)),$new_thes,"fr_FR");
						if (!$categ_id_fils) {
							$noeud=new noeuds();
							$noeud->num_thesaurus=($new_thes);
							$thesau=new thesaurus($new_thes);
							$noeud->num_parent=$thesau->num_noeud_racine;
							$noeud->save();
							$categ_id_fils=$noeud->id_noeud;
							//Création du libellé
							$categ=new categories($noeud->id_noeud,'fr_FR');
							$categ->libelle_categorie=$descripteur_fils;
							$categ->index_categorie=" ".strip_empty_words($descripteur_fils)." ";
							$categ->save();
						}
					}
					$requete="INSERT INTO notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ($notice_id,$categ_id_fils, ".($i+1).")";
					pmb_mysql_query($requete);
				}
			}
		} //for($i
	} else {
		//Traitement du thésaurus unique
		for ($i=0; $i<count($info_606_a); $i++) {
			for ($j=0; $j<count($info_606_a[$i]); $j++) {
				$descripteur_tete=$info_606_a[$i][$j];
				$descripteur_fils=$info_606_x[$i][$j];

				//Recherche du terme de tête
				//$requete="SELECT num_noeud FROM categories WHERE libelle_categorie='".addslashes($descripteur_tete)."' AND langue='fr_FR'";
				$requete="SELECT id_noeud FROM noeuds WHERE autorite='".addslashes($descripteur_tete)."'";
				$resultat=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($resultat)) {
					//la tête existe !
					$categ_id_tete=pmb_mysql_result($resultat,0,0);
				} else {
					//Création de la tête
					//Nouveau Noeud !
					$th=new thesaurus(1);
					$noeud=new noeuds();
					$noeud->num_thesaurus=$th->id_thesaurus;
					$noeud->num_parent=$th->num_noeud_racine;
					$noeud->autorite=$descripteur_tete;
					$noeud->save();
					$categ_id_tete=$noeud->id_noeud;
					//Création du libellé
					$categ=new categories($noeud->id_noeud,'fr_FR');
					$categ->libelle_categorie=$descripteur_tete;
					$categ->index_categorie=" ".strip_empty_words($descripteur_tete)." ";
					$categ->save();
				}
				//Recherche du terme fils
				$categ_id_fils=categories::searchLibelle(addslashes($descripteur_fils),1,"fr_FR");
				if (!$categ_id_fils) {
					//Création
					$noeud=new noeuds();
					$noeud->num_thesaurus=1;
					$noeud->num_parent=$categ_id_tete;
					$noeud->save();
					$categ_id_fils=$noeud->id_noeud;
					//Création du libellé
					$categ=new categories($noeud->id_noeud,'fr_FR');
					$categ->libelle_categorie=$descripteur_fils;
					$categ->index_categorie=" ".strip_empty_words($descripteur_fils)." ";
					$categ->save();
				}
				$requete="INSERT INTO notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ($notice_id, $categ_id_fils, ".($i+1).")";
				pmb_mysql_query($requete);
			}
		}
	}

	//Indexation décimale
	if (!empty($info_676[0])) {
		$requete="select indexint_id from indexint where indexint_name='".addslashes($info_676[0])."'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$indexint=pmb_mysql_result($resultat,0,0);
		} else {
			$requete="insert into indexint (indexint_name) values('".addslashes($info_676[0])."')";
			pmb_mysql_query($requete);
			$indexint=pmb_mysql_insert_id();
		}
		$requete="update notices set indexint=".$indexint." where notice_id=".$notice_id;
		pmb_mysql_query($requete);
	}

	//Organisme
	if(isset($info_900[0])) {
		import_records::insert_list_integer_value_custom_field_from_name('op', $notice_id, $info_900[0]);
	}

	//Genre
	if(isset($info_901[0])) {
		import_records::insert_list_integer_value_custom_field_from_name('gen', $notice_id, $info_901[0]);
	}

	//Type de texte
	if (count($info_904)) {
		$no_champ = trouve_champ_perso("typtext");
		if ($no_champ>0) {
			for ($i=0; $i<count($info_904); $i++) {
				for ($j=0; $j<count($info_904[$i]); $j++) {
					$requete="SELECT max(notices_custom_list_value*1) FROM notices_custom_lists WHERE notices_custom_champ=".$no_champ;
					$resultat=pmb_mysql_query($requete);
					$max=@pmb_mysql_result($resultat,0,0);
					$n=$max+1;
					$requete="SELECT notices_custom_list_value FROM notices_custom_lists WHERE notices_custom_list_lib='".addslashes($info_904[$i][$j])."' AND notices_custom_champ=".$no_champ;
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="INSERT INTO notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) VALUES($no_champ,$n,'".addslashes($info_904[$i][$j])."')";
						pmb_mysql_query($requete);
						$value=$n;
						$n++;
					}
					$requete="INSERT INTO notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_integer) VALUES($no_champ,$notice_id,$value)";
					pmb_mysql_query($requete);
				}
			}
		}
	}

	//Date de saisie
	if ($info_902[0]) {
		$no_champ = trouve_champ_perso("ds");
		if ($no_champ>0) {
			$requete="INSERT INTO notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_date) VALUES($no_champ,$notice_id,'".str_replace(".","-",$info_902[0])."')";
			pmb_mysql_query($requete);
		}
	}

	//N° de lot
	if ($info_903[0]) {
		$requete="UPDATE notices SET commentaire_gestion='".addslashes($info_903[0])."' WHERE notice_id=$notice_id";
		pmb_mysql_query($requete);
	}

	//Cas de la mise à jour des périodiques ou du champ bord (notices chapeau)
	if ($bl=="s") {
		//Si c'est un périodique
		if ($dt=="a") {
			//Passage de la notice en notice chapeau
			$requete="UPDATE notices SET niveau_biblio='s', niveau_hierar='1' WHERE notice_id=$notice_id";
			pmb_mysql_query($requete);
			//Recherche si la notice existe déjà par rapport au titre
			$requete="select notice_id FROM notices WHERE ucase(tit1)='".addslashes(strtoupper($info_200[0]))."' AND niveau_biblio='s' AND niveau_hierar='1' AND notice_id!=$notice_id";
			$resultat=pmb_mysql_query($requete);
			$update=false;
			if (pmb_mysql_num_rows($resultat)) {
				$update=true;
				$n_update=pmb_mysql_result($resultat,0,0);
				//Mise à jour de tous les bulletins
				$requete="UPDATE bulletins SET bulletin_notice=".$notice_id." WHERE bulletin_notice=".$n_update;
				pmb_mysql_query($requete);
				//Suppression de l'ancienne notice
				$requete="DELETE FROM notices WHERE notice_id=$n_update";
				pmb_mysql_query($requete);
				$requete="DELETE FROM notices_categories WHERE notcateg_notice=".$n_update;
				pmb_mysql_query($requete);
				$requete="DELETE FROM notices_custom_values WHERE notices_custom_origine=".$n_update;
				pmb_mysql_query($requete);
				$requete="DELETE FROM responsability WHERE responsability_author=".$n_update;
				pmb_mysql_query($requete);
			}

			if ((!$update)&&($rs!="n")) {
				//Si il n'y a pas de création, on supprime la notice
				$requete="DELETE FROM notices WHERE notice_id=$notice_id";
				pmb_mysql_query($requete);
				$requete="DELETE FROM notices_categories WHERE notcateg_notice=".$notice_id;
				pmb_mysql_query($requete);
				$requete="DELETE FROM notices_custom_values WHERE notices_custom_origine=".$notice_id;
				pmb_mysql_query($requete);
				$requete="DELETE FROM responsability WHERE responsability_author=".$notice_id;
				pmb_mysql_query($requete);
			}
		} else if ($dt=="l") {
			//Recherche si la notice existe déjà par rapport au titre
			$requete="select notice_id FROM notices WHERE ucase(tit1)='".addslashes(strtoupper($info_200[0]))."' AND typdoc='l' AND notice_id!=$notice_id";
			$resultat=pmb_mysql_query($requete);
			$update=false;
			if (pmb_mysql_num_rows($resultat)) {
				$update=true;
				$n_update=pmb_mysql_result($resultat,0,0);
				//Suppression de l'ancienne notice
				$requete="DELETE FROM notices WHERE notice_id=$n_update";
				pmb_mysql_query($requete);
				$requete="DELETE FROM notices_categories WHERE notcateg_notice=".$n_update;
				pmb_mysql_query($requete);
				$requete="DELETE FROM notices_custom_values WHERE notices_custom_origine=".$n_update;
				pmb_mysql_query($requete);
				$requete="DELETE FROM responsability WHERE responsability_author=".$n_update;
				pmb_mysql_query($requete);
			}
		} else if ($dt=="r") {
			//Mise à jour du champ bord
			if ($info_910[0]) {
				$no_champ = trouve_champ_perso("bord");
				if ($no_champ>0) {
					//Recherche si la notice existe déjà par rapport au titre
					$requete="SELECT notice_id FROM notices WHERE ucase(tit1)='".addslashes(strtoupper($info_200[0]))."' AND niveau_biblio='s' AND niveau_hierar='1' AND notice_id!=$notice_id";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$notice_update=pmb_mysql_result($resultat,0,0);
						$requete="UPDATE notices_custom_values SET notices_custom_text='".addslashes(str_replace("##","\n",$info_910[0]))."' WHERE notices_custom_champ=$no_champ AND notices_custom_origine=".$notice_update;
						pmb_mysql_query($requete);
						if (!pmb_mysql_affected_rows()) {
							$requete="INSERT INTO notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_text) VALUES($no_champ,$notice_update,'".addslashes(str_replace("##","\n",$info_910[0]))."')";
							pmb_mysql_query($requete);
						}
					}
				}
			}
			//Suppression de la nouvelle notice
			$requete="DELETE FROM notices WHERE notice_id=".$notice_id;
			pmb_mysql_query($requete);
		}
	}

} // fin import_new_notice_suite

// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	global $prix, $notice_id, $info_995, $tdoc_codage, $book_lender_id,
		$sdoc_codage, $book_statut_id, $locdoc_codage, $statisdoc_codage,
		$cote_mandatory, $nb_expl_ignores;

	global $bulletin_ex;

	// lu en 010$d de la notice
	$price = $prix[0];

	$nb_infos_995 = count($info_995);
	// la zone 995 est répétable
	for ($nb_expl = 0; $nb_expl < $nb_infos_995; $nb_expl++) {
		/* RAZ expl */
		$expl = array();

		/* préparation du tableau à passer à la méthode */
		$expl['cb'] 	    = $info_995[$nb_expl]['f'];
		$unique=false;
		$cb=$expl['cb'];
		$cb1=$cb;
		$n_cb=2;
		while (!$unique) {
			$requete="select 1 from exemplaires where expl_cb='".addslashes($cb1)."'";
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$cb1=$cb." ".$n_cb;
				$n_cb++;
			} else $unique=true;
		}
		$expl['cb']=$cb1;
		if ($bulletin_ex) {
			$expl['bulletin']=$bulletin_ex;
			$expl['notice']=0;
		} else {
			$expl['notice']     = $notice_id ;
			$expl['bulletin']=0;
		}
		// $expl['typdoc']     = $info_995[$nb_expl]['r']; à chercher dans docs_typdoc
		$data_doc=array();
		//$data_doc['tdoc_libelle'] = $info_995[$nb_expl]['r']." -Type doc importé (".$book_lender_id.")";
		//$data_doc['tdoc_libelle'] = $typdoc_995[$info_995[$nb_expl]['r']];
		//if (!$data_doc['tdoc_libelle']) $data_doc['tdoc_libelle'] = "\$r non conforme -".$info_995[$nb_expl]['r']."-" ;
		$data_doc['duree_pret'] = 0 ; /* valeur par défaut */
		$data_doc['tdoc_codage_import'] = $info_995[$nb_expl]['r'] ;
		$data_doc['tdoc_libelle']=$info_995[$nb_expl]['r'] ;
		if ($tdoc_codage) $data_doc['tdoc_owner'] = $book_lender_id ;
			else $data_doc['tdoc_owner'] = 0 ;
		$expl['typdoc'] = docs_type::import($data_doc);

		$expl['cote'] = $info_995[$nb_expl]['k'];

        if (!trim($expl['cote'])) $expl['cote']="SC";

		// $expl['section']    = $info_995[$nb_expl]['q']; à chercher dans docs_section
		$data_doc=array();
		if (!$info_995[$nb_expl]['t'])
			$info_995[$nb_expl]['t'] = "inconnu";
		$data_doc['section_libelle'] = $info_995[$nb_expl]['t'] ;
		$data_doc['sdoc_codage_import'] = $info_995[$nb_expl]['t'] ;
		if ($sdoc_codage) $data_doc['sdoc_owner'] = $book_lender_id ;
			else $data_doc['sdoc_owner'] = 0 ;
		$expl['section'] = docs_section::import($data_doc);

		$expl['statut'] = $book_statut_id;

		// $expl['location']   = $info_995[$nb_expl]['']; à fixer par combo_box
		// figé dans le code ici pour l'instant :
		//$info_995[$nb_expl]['localisation']="Bib princip"; /* biblio principale */
		$data_doc=array();
		$data_doc['location_libelle'] = "inconnu";
		if ($info_995[$nb_expl]['a']) {
			$data_doc['location_libelle'] = $info_995[$nb_expl]['a'];
			$data_doc['locdoc_codage_import'] = $info_995[$nb_expl]['a'];
		} else
			$data_doc['locdoc_codage_import']="Centre de documentation";

		if ($locdoc_codage)
			$data_doc['locdoc_owner'] = $book_lender_id ;
		else
			$data_doc['locdoc_owner'] = 0 ;
		$expl['location'] = docs_location::import($data_doc);

		// $expl['codestat']   = $info_995[$nb_expl]['q']; 'q' utilisé, éventuellement à fixer par combo_box
		$data_doc=array();
		//$data_doc['codestat_libelle'] = $info_995[$nb_expl]['q']." -Pub visé importé (".$book_lender_id.")";
		if (!$info_995[$nb_expl]['q'])
			$info_995[$nb_expl]['q'] = "inconnu";
		$data_doc['codestat_libelle'] = $info_995[$nb_expl]['q'] ;
		$data_doc['statisdoc_codage_import'] = $info_995[$nb_expl]['q'] ;
		if ($statisdoc_codage) $data_doc['statisdoc_owner'] = $book_lender_id ;
			else $data_doc['statisdoc_owner'] = 0 ;
		$expl['codestat'] = docs_codestat::import($data_doc);


		// $expl['creation']   = $info_995[$nb_expl]['']; à préciser
		// $expl['modif']      = $info_995[$nb_expl]['']; à préciser

		$expl['note']       = $info_995[$nb_expl]['u'];
		$expl['prix']       = $price;
		$expl['expl_owner'] = $book_lender_id ;
		$expl['cote_mandatory'] = $cote_mandatory ;

		if (!empty($info_995[$nb_expl]['m'])) {
			$expl['date_depot'] = substr($info_995[$nb_expl]['m'],0,4)."-".substr($info_995[$nb_expl]['m'],4,2)."-".substr($info_995[$nb_expl]['m'],6,2) ;
		}
		if (!empty($info_995[$nb_expl]['n'])) {
			$expl['date_retour'] = substr($info_995[$nb_expl]['n'],0,4)."-".substr($info_995[$nb_expl]['n'],4,2)."-".substr($info_995[$nb_expl]['n'],6,2) ;
		}
		
		$expl_id = exemplaire::import($expl);

		if ($expl_id == 0)
			$nb_expl_ignores++;

	} // fin for
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}