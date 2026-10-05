<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_bretagne.inc.php,v 1.35.2.1 2024/12/17 13:50:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// DEBUT paramétrage propre à la base de données d'importation :
global $class_path; //Nécessaire pour certaines inclusions
require_once($class_path."/serials.class.php");
require_once($class_path."/categories.class.php");

function recup_noticeunimarc_suite($notice) {
	global $info_464,$index_sujets,$info_610;
	global $info_900,$info_901,$info_902,$info_903,$info_904,$info_905,$info_906,$info_606_a;


	$info_464="";
	$info_900="";
	$info_901="";
	$info_902="";
	$info_903="";
	$info_904="";
	$info_905="";
	$info_906="";
	$info_610=$index_sujets;
	$index_sujets=array();

	$record = new iso2709_record($notice, AUTO_UPDATE);
	for ($i=0;$i<count($record->inner_directory);$i++) {
		$cle=$record->inner_directory[$i]['label'];
		switch($cle) {
			case "464":
				//C'est un périodique donc un dépouillement ou une notice objet
				$info_464=$record->get_subfield($cle,"t","v","p","d","z","e");
				break;
			default:
				break;

		} /* end of switch */

	} /* end of for */

	$info_606_a=$record->get_subfield_array_array("606","a");
	$info_900=$record->get_subfield_array_array("900","a");
	$info_901=$record->get_subfield_array_array("901","a");
	$info_902=$record->get_subfield_array_array("902","a");
	$info_903=$record->get_subfield("903","a");
	$info_904=$record->get_subfield("904","a");
	$info_905=$record->get_subfield("905","a");
	$info_906=$record->get_subfield_array_array("906","a");

} // fin recup_noticeunimarc_suite = fin récupération des variables propres à la bretagne

function import_new_notice_suite() {
	global $charset;
	global $notice_id;

	global $info_464;
	global $info_606_a;
	global $info_900,$info_901,$info_902,$info_903,$info_904,$info_905,$info_906,$info_610;

	global $pmb_keyword_sep;

	global $bulletin_ex;

	// param : l'article hérite-t-il de l'URL de la vignette de la notice chapeau
	global $pmb_serial_thumbnail_url_article;
	// param : l'article hérite-t-il de l'URL de la vignette de la notice bulletin
	global $pmb_bulletin_thumbnail_url_article;

	//Cas des périodiques
	if (is_array($info_464)) {
		$requete="select * from notices where notice_id=$notice_id";
		$resultat=pmb_mysql_query($requete);
		$r=pmb_mysql_fetch_object($resultat);
		//Notice chapeau existe-t-elle ?
			$requete="select notice_id from notices where tit1='".addslashes($info_464[0]['t'])."' and niveau_hierar='1' and niveau_biblio='s'";
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				//Si oui, récupération id
				$chapeau_id=pmb_mysql_result($resultat,0,0);
				//Bulletin existe-t-il ?
				$requete="select bulletin_id from bulletins where bulletin_numero='".addslashes($info_464[0]['v'])."' and  mention_date='".addslashes($info_464[0]['d'])."' and bulletin_notice=$chapeau_id";
				//$requete="select bulletin_id from bulletins where bulletin_numero='".addslashes($info_464[0]['v'])."' and bulletin_notice=$chapeau_id";
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)) {
					//Si oui, récupération id bulletin
					$bulletin_id=pmb_mysql_result($resultat,0,0);
				} else {
					//Si non, création bulletin
					$info=array();
					$bulletin=new bulletinage("",$chapeau_id);
					$bul_titre_txt='Bulletin N°';
					if ($charset=='utf-8') {
						$bul_titre_txt=encoding_normalize::utf8_normalize($bul_titre_txt);
					}
					$info['bul_titre']=addslashes($bul_titre_txt.$info_464[0]['v']);
					$info['bul_no']=addslashes($info_464[0]['v']);
					$info['bul_date']=addslashes($info_464[0]['d']);
					if (!$info_464[0]['e']) {
						$date_date=explode("/",$info_464[0]['d']);
						if (count($date_date)) {
							if (count($date_date)==1) $info['date_date']=$date_date[0]."-01-01";
							if (count($date_date)==2) $info['date_date']=$date_date[1]."-".$date_date[0]."-01";
							if (count($date_date)==3) $info['date_date']=$date_date[2]."-".$date_date[1]."-".$date_date[0];
						} else {
							if ($info_904[0]) {
								$info['date_date']=$info_904[0];
							}
						}
					} else {
						$info['date_date']=$info_464[0]['e'];
					}
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

				$bulletin=new bulletinage("",$chapeau_id);
				$info=array();
				$bul_titre_txt='Bulletin N°';
				if ($charset=='utf-8') {
					$bul_titre_txt=encoding_normalize::utf8_normalize($bul_titre_txt);
				}
				$info['bul_titre']=addslashes($bul_titre_txt.$info_464[0]['v']);
				$info['bul_no']=addslashes($info_464[0]['v']);
				$info['bul_date']=addslashes($info_464[0]['d']);
				if (!$info_464[0]['e']) {
					$date_date=explode("/",$info_464[0]['d']);
					if (count($date_date)) {
						if (count($date_date)==1) $info['date_date']=$date_date[0]."-01-01";
						if (count($date_date)==2) $info['date_date']=$date_date[1]."-".$date_date[0]."-01";
						if (count($date_date)==3) $info['date_date']=$date_date[2]."-".$date_date[1]."-".$date_date[0];
					} else {
						if ($info_904[0]) {
							$info['date_date']=$info_904[0];
						}
					}
				} else {
					$info['date_date']=$info_464[0]['e'];
				}
				$bulletin_id=$bulletin->update($info);
			}
			//Notice objet ?
			if ($info_464[0]['z']=='objet') {
				//Supression de la notice
				$requete="delete from notices where notice_id=$notice_id";
				pmb_mysql_query($requete);
				$bulletin_ex=$bulletin_id;
			} else {
				$annee=$info_464[0]['d'];
				$date_parution=(!empty($info['date_date']) ? $info['date_date'] : '');
				//Récupère la date du bulletin (non disponible autrement si il existe déjà) pour l'article
				$select="SELECT date_date FROM bulletins WHERE bulletin_id='".$bulletin_id."'";
				$res_date=pmb_mysql_query($select);
				if($res_date && pmb_mysql_num_rows($res_date)){
					$date_parution=pmb_mysql_result($res_date,0,0);
					$annee=substr($date_parution,0,4);
				}
				//Dédoublonnage : vérifie si un article avec ce titre n'existe pas déjà pour le bulletin
				$requete="SELECT old.notice_id,old.tit1 FROM notices new, notices old JOIN analysis ON analysis_notice=old.notice_id WHERE new.notice_id='".addslashes($notice_id)."' AND new.notice_id!=old.notice_id AND analysis_bulletin='".addslashes($bulletin_id)."' AND new.tit1=old.tit1";
				$res_doubl=pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($res_doubl)){
					notice::del_notice($notice_id);
					pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".addslashes("La notice existe d&eacute;j&agrave; : ".pmb_mysql_result($res_doubl,0,1)." ")."') ") ;
					$notice_id=pmb_mysql_result($res_doubl,0,0);
				} else {
					//Passage de la notice en article
					$requete="update notices set niveau_biblio='a', niveau_hierar='2', code='', year='".addslashes($annee)."', npages='".addslashes($info_464[0]['p'])."', date_parution='".addslashes($date_parution)."' where notice_id=$notice_id";
					pmb_mysql_query($requete);
					$requete="insert into analysis (analysis_bulletin,analysis_notice) values($bulletin_id,$notice_id)";
					pmb_mysql_query($requete);
					//Copie de la vignette de la notice bulletin en article
					if ($pmb_bulletin_thumbnail_url_article) {
						$requete="UPDATE notices AS na, notices AS nb, analysis, bulletins SET na.thumbnail_url=nb.thumbnail_url WHERE LENGTH(nb.thumbnail_url)!=0 AND analysis_notice=$notice_id AND analysis_bulletin=bulletin_id AND num_notice=nb.notice_id AND na.notice_id=analysis_notice";
						pmb_mysql_query($requete);
					}
					//Copie de la vignette de la notice chapeau en article
					if ($pmb_serial_thumbnail_url_article) {
						$requete="UPDATE notices AS na, notices AS nc, analysis, bulletins SET na.thumbnail_url=nc.thumbnail_url WHERE LENGTH(nc.thumbnail_url)!=0 AND LENGTH(na.thumbnail_url)=0 AND analysis_notice=$notice_id AND analysis_bulletin=bulletin_id AND bulletin_notice=nc.notice_id AND na.notice_id=analysis_notice";
						pmb_mysql_query($requete);
					}
					$bulletin_ex=$bulletin_id;
				}
			}
	} else {
	    $bulletin_ex=0;
	}

	//Traitement du thésaurus
	$unknown_desc=array();
	$ordre_categ = 0;
	if (!empty($info_606_a)) {
    	for ($i=0; $i<count($info_606_a); $i++) {
    		for ($j=0; $j<count($info_606_a[$i]); $j++) {
    			$descripteur=trim($info_606_a[$i][$j]);
    			//Recherche du terme
    			//dans le thesaurus par defaut et dans la langue de l'interface
    			$libelle = addslashes($descripteur);
    			$categ_id = categories::searchLibelle($libelle);
    
    			if ($categ_id) {
    				$requete = "INSERT INTO notices_categories (notcateg_notice,num_noeud,ordre_categorie) VALUES($notice_id,$categ_id,$ordre_categ)";
    				pmb_mysql_query($requete);
    				$ordre_categ++;
    			} else {
    				$unknown_desc[]=$descripteur;
    			}
    		}
    	}
	}

	if(!empty($info_610) && count($info_610)){
		for ($i=0; $i<count($info_610); $i++) {
			$tmp=explode($pmb_keyword_sep,$info_610[$i]);
			if(count($tmp)){
				foreach ( $tmp as $value ) {
       				if($libelle=addslashes(trim($value))){
       					$categ_id = categories::searchLibelle($libelle);
						if ($categ_id) {
							$requete = "INSERT INTO notices_categories (notcateg_notice,num_noeud,ordre_categorie) VALUES($notice_id,$categ_id,$ordre_categ)";
							pmb_mysql_query($requete);
							$ordre_categ++;
						} else {
							$unknown_desc[]=trim($value);
						}
       				}
				}
			}
		}
	}

	if ($unknown_desc) {
		$mots_cles=implode($pmb_keyword_sep,$unknown_desc);
		$requete="update notices set index_l='".addslashes($mots_cles)."', index_matieres=' ".addslashes(strip_empty_words($mots_cles))." ' where notice_id=$notice_id";
		pmb_mysql_query($requete);
	}

	//Thème
	import_records::insert_list_integer_values_custom_field(1, $notice_id, $info_900);

	//Genres
	import_records::insert_list_integer_values_custom_field(2, $notice_id, $info_901);

	//Discipline
	import_records::insert_list_integer_values_custom_field(3, $notice_id, $info_902);

	//Type de nature
	if (!empty($info_905[0])) {
		$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=6";
		$resultat=pmb_mysql_query($requete);
		$max=@pmb_mysql_result($resultat,0,0);
		$n=$max+1;
		$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($info_905[0])."' and notices_custom_champ=6";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$value=pmb_mysql_result($resultat,0,0);
		} else {
			$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values(6,$n,'".addslashes($info_905[0])."')";
			pmb_mysql_query($requete);
			$value=$n;
			$n++;
		}
		$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_integer) values(6,$notice_id,$value)";
		pmb_mysql_query($requete);
	}

	//Niveau
	import_records::insert_list_integer_values_custom_field(7, $notice_id, $info_906);

	//Année de péremption
	if (!empty($info_903[0])) {
		$requete="select notices_custom_integer from notices_custom_values where notices_custom_integer='".addslashes($info_903[0])."' and notices_custom_champ=4 and notices_custom_origine='".$notice_id."'";
		$resultat=pmb_mysql_query($requete);
		if (!pmb_mysql_num_rows($resultat)) {
			$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_integer) values(4,$notice_id,'".addslashes($info_903[0])."')";
			pmb_mysql_query($requete);
		}
	}

	//Date de saisie
	if (!empty($info_904[0])) {
		$requete="select notices_custom_date from notices_custom_values where notices_custom_date='".addslashes($info_904[0])."' and notices_custom_champ=5 and notices_custom_origine='".$notice_id."'";
		$resultat=pmb_mysql_query($requete);
		if (!pmb_mysql_num_rows($resultat)) {
			$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_date) values(5,$notice_id,'".$info_904[0]."')";
			pmb_mysql_query($requete);
		}
	}

} // fin import_new_notice_suite

// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	global $prix, $notice_id, $info_995, $tdoc_codage, $book_lender_id,
		$sdoc_codage, $book_statut_id, $locdoc_codage, $statisdoc_codage,
		$cote_mandatory,$info_464, $nb_expl_ignores;

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
		if (($bulletin_ex)&&(is_array($info_464))) {
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

        if (!trim($expl['cote'])) $expl['cote']="ARCHIVES";

		// $expl['section']    = $info_995[$nb_expl]['q']; à chercher dans docs_section
		$data_doc=array();
		if (!$info_995[$nb_expl]['t'])
			$info_995[$nb_expl]['t'] = "inconnu";
		$data_doc['section_libelle'] = $info_995[$nb_expl]['t'] ;
		$data_doc['sdoc_codage_import'] = $info_995[$nb_expl]['t'] ;
		if ($sdoc_codage) $data_doc['sdoc_owner'] = $book_lender_id ;
			else $data_doc['sdoc_owner'] = 0 ;
		$expl['section'] = docs_section::import($data_doc);

		/* $expl['statut']     à chercher dans docs_statut */
		/* TOUT EST COMMENTE ICI, le statut est maintenant choisi lors de l'import
		if ($info_995[$nb_expl]['o']=="") $info_995[$nb_expl]['o'] = "e";
		$data_doc=array();
		$data_doc['statut_libelle'] = $info_995[$nb_expl]['o']." -Statut importé (".$book_lender_id.")";
		$data_doc['pret_flag'] = 1 ;
		$data_doc['statusdoc_codage_import'] = $info_995[$nb_expl]['o'] ;
		$data_doc['statusdoc_owner'] = $book_lender_id ;
		$expl['statut'] = docs_statut::import($data_doc);
		FIN TOUT COMMENTE */

		$expl['statut'] = $book_statut_id;

		// $expl['location']   = $info_995[$nb_expl]['']; à fixer par combo_box
		// figé dans le code ici pour l'instant :
		//$info_995[$nb_expl]['localisation']="Bib princip"; /* biblio principale */
		$data_doc=array();
		$data_doc['location_libelle'] = "inconnu";
		if ($info_995[$nb_expl]['a']) {
			$data_doc['location_libelle'] = $info_995[$nb_expl]['a'];
			$data_doc['locdoc_codage_import'] = $info_995[$nb_expl]['a'];
		} else {
			$data_doc['locdoc_codage_import']="cdi";
		}
		if ($locdoc_codage) $data_doc['locdoc_owner'] = $book_lender_id ;
			else $data_doc['locdoc_owner'] = 0 ;
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
		if ($expl_id == 0) {
			$nb_expl_ignores++;
		}

		//debug : affichage zone 995
		/*
		echo "995\$a =".$info_995[$nb_expl]['a']."<br />";
		echo "995\$b =".$info_995[$nb_expl]['b']."<br />";
		echo "995\$c =".$info_995[$nb_expl]['c']."<br />";
		echo "995\$d =".$info_995[$nb_expl]['d']."<br />";
		echo "995\$f =".$info_995[$nb_expl]['f']."<br />";
		echo "995\$k =".$info_995[$nb_expl]['k']."<br />";
		echo "995\$m =".$info_995[$nb_expl]['m']."<br />";
		echo "995\$n =".$info_995[$nb_expl]['n']."<br />";
		echo "995\$o =".$info_995[$nb_expl]['o']."<br />";
		echo "995\$q =".$info_995[$nb_expl]['q']."<br />";
		echo "995\$r =".$info_995[$nb_expl]['r']."<br />";
		echo "995\$u =".$info_995[$nb_expl]['u']."<br /><br />";
		*/
		} // fin for
	} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}