<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesLoans.class.php,v 1.9 2019-04-26 15:59:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");

define('LOAN_ALL_ACTIONS','1');
define('LOAN_PRINT_MAIL','2');
define('LOAN_CSV_MAIL','3');

class pmbesLoans extends external_services_api_class {
	
	public function restore_general_config() {
		
	}
	
	public function form_general_config() {
		return false;
	}
	
	public function save_general_config() {
		
	}
	
	//ex: "empr","empr_list","b,n,c,g","b,n,c,g".$localisation.",cs","n,g"
	// correspondance : ./includes/filter_list/empr/empr_list.xml
	// les 2 premiers params doivent-ils plutôt être forcées ??
	public function filterLoansReaders($filter_name,$filter_source="",$display,$filter,$sort,$parameters) {
		global $empr_sort_rows, $empr_show_rows, $empr_filter_rows,$pmb_lecteurs_localises;

		if (SESSrights & CIRCULATION_AUTH) {
			if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
				if ($pmb_lecteurs_localises) $localisation=",l";
				else $localisation="";
				$filter=new filter_list($filter_name,$filter_source,$display,$filter.$localisation,$sort);
	
				$t_filters = explode(",",$filter->filtercolumns);
				foreach ($t_filters as $f) {
					$filters_selectors="f".$filter->fixedfields[$f]["ID"];
					if ($parameters[$filters_selectors]) {
						$tableau=array();
						foreach ($parameters[$filters_selectors] as $categ) {
							$tableau[$categ] = $categ;
						}
						global ${$filters_selectors};
						${$filters_selectors} = $tableau;
					}
				}
				$t_sort = explode(",",$filter->sortablecolumns);
				for ($j=0;$j<=count($t_sort)-1;$j++) {
	    			$sort_selector="sort_list_".$j;
	    			if ($parameters[$sort_selector]) {
						global ${$sort_selector};
	    				${$sort_selector} = $parameters[$sort_selector];    				
					}
	    		}
				$filter->activate_filters();
				$requete = $filter->query;						
			}
	
			$resultat=pmb_mysql_query($requete);
							
			$result = array();
			while ($row=pmb_mysql_fetch_assoc($resultat)) {
				$result = array(
					"id_empr" => $row["id_empr"],
					"empr_cb" => $row["empr_cb"],
					"empr_nom" => utf8_normalize($row["empr_nom"]),
					"empr_prenom" => utf8_normalize($row["empr_prenom"]),
					"categ_libelle" => utf8_normalize($row["libelle"]),
					"group_name" => utf8_normalize($row["group_name"]),
				);
			}
			return $result;
		} else {
			return array();
		}
	}
	
	/*Dépend du paramétrage PMB
	 * Retourne un chiffre >= 1 si des relances n'ont pas été envoyées par mail*/
	public function relanceLoansReaders($t_empr) {

		if (SESSrights & CIRCULATION_AUTH) {
			$requete = "select id_empr from empr, pret, exemplaires where 1 ";
			$requete.=" and id_empr in (".implode(",",$t_empr).") ";
			//$requete.= $loc_filter;
			$requete.= "and pret_retour<now() and pret_idempr=id_empr and pret_idexpl=expl_id group by id_empr";
			$resultat=pmb_mysql_query($requete);
			$not_all_mail=0;
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$amende=new amende($r->id_empr);
				$level=$amende->get_max_level();
				$niveau_min=$level["level_min"];
				$printed=$level["printed"];
				if ((!$printed)&&($niveau_min)) {
					$not_all_mail+=print_relance($r->id_empr);		
				}
			}
			return $not_all_mail;
		} else {
			return 0;
		}
	}
	
	public function exportCSV($t_empr) {
		
		if (SESSrights & CIRCULATION_AUTH) {
			$req="TRUNCATE TABLE cache_amendes";
			pmb_mysql_query($req);
			$requete = "select id_empr from empr, pret, exemplaires where 1 ";
			if (!isset($t_empr)) $t_empr[] = "0";
			$requete.=" and id_empr in (".implode(",",$t_empr).") ";
			//$requete.= $loc_filter;
			$requete.= "and pret_retour<now() and pret_idempr=id_empr and pret_idexpl=expl_id group by id_empr";
	
			$resultat=pmb_mysql_query($requete);
			$not_all_mail=0;
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$amende=new amende($r->id_empr);
				$level=$amende->get_max_level();
				$niveau_min=$level["level_min"];
				$printed=$level["printed"];
				if ((!$printed)&&($niveau_min)) {
					$not_all_mail+=print_relance($r->id_empr);		
				}
			}
			
			$req ="select id_empr  from empr, pret, exemplaires, empr_categ where 1 ";
			$req.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id and id_categ_empr=empr_categ group by id_empr";
			$res=pmb_mysql_query($req);
			while ($r=pmb_mysql_fetch_object($res)) {
				$relance_liste.=get_relance($r->id_empr);
			}
	
			//modification du template importé
			//possiblité de l'appeler sans le mot global
			//(juste pour noté qu'elle n'est pas valorisée ici)
			global $export_relance_tpl;
			$export_relance_tpl = str_replace("!!relance_liste!!",$relance_liste,$export_relance_tpl);
			
			return $export_relance_tpl;
		} else {
			return 0;
		}
	}
	
	//pour valider une action ...
	public function commitActionEmpr($id_empr, $cb, $last_level_commit,$next_level) {
		
	}

	public function listLoansReaders($loan_type=0, $f_loc=0,$f_categ=0,$f_group=0,$f_codestat=0,$sort_by=0,$limite_mysql='',$limite_page='') {
		global $dbh, $msg, $pmb_lecteurs_localises;
		
		if (SESSrights & CIRCULATION_AUTH) {
//			$empr = new emprunteur($empr_id);
		
			if ($loan_type) {
				switch ($loan_type) {
					case LIST_LOAN_LATE:
						break;
					case LIST_LOAN_CURRENT:
						break;
				}
			}
					
			$results = array();
			
			//REQUETE SQL
			$sql = "SELECT date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
			$sql .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
			$sql .= " IF(pret_retour>=CURDATE(),0,1) as retard, " ;
			$sql .= " id_empr, empr_nom, empr_prenom, empr_mail, id_empr, empr_cb, expl_cote, expl_cb, expl_notice, expl_bulletin, notices_m.notice_id as idnot, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit ";
			$sql .= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
			$sql .= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
			$sql .= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
			$sql .= "        docs_type , pret, empr, empr_groupe ";
			$sql .= "WHERE ";
			if ($pmb_lecteurs_localises) {
				if ($f_loc) 
					$sql.= "empr_location in (".trim($f_loc,",").") AND "; 
			}
			if ($f_categ) {
				$sql .= "empr_categ in (".trim($f_categ,",").") AND ";
			}
			if ($f_group) {
				$sql .= "id_empr=empr_id and groupe_id in (".trim($f_group,",").") AND ";
			}
			if ($f_codestat) {
				$sql .= "empr_codestat in (".trim($f_codestat,",").") AND ";
			}
			$order = "";
			if ($sort_by) {
				$t_sort_by = explode(",",$sort_by);
				foreach ($t_sort_by as $v_sort_by) {
					if ($v_sort_by == "n") {
						$order .= "empr_nom, empr_prenom,";
					}
					if ($v_sort_by == "g") {
						$order .= "groupe_id,";
					}
				}
			}
		
			$sql.= "expl_typdoc = idtyp_doc and pret_idexpl = expl_id  and empr.id_empr = pret.pret_idempr ";
			if ($order != '') {
				$sql .= "order by ".trim($order,",");			
			}
			if ($limite_mysql && $limite_page) {
				$sql = $sql." LIMIT ".$limite_mysql.", ".$limite_page; 
			}
								
			$res = pmb_mysql_query($sql, $dbh);
			if (!$res) {
				return false;
	//			throw new Exception("Not found: Error");	
			}

			while ($row = pmb_mysql_fetch_assoc($res)) {
				$result = array(
					"aff_pret_date" => utf8_normalize($row["aff_pret_date"]),
					"aff_pret_retour" => utf8_normalize($row["aff_pret_retour"]),
					"retard" => utf8_normalize($row["retard"]),
					"id_empr" => $row["id_empr"],
					"empr_nom" => utf8_normalize($row["empr_nom"]),
					"empr_prenom" => utf8_normalize($row["empr_prenom"]),
					"empr_mail" => utf8_normalize($row["empr_mail"]),
					"empr_cb" => $row["empr_cb"],
					"expl_cote" => utf8_normalize($row["expl_cote"]),
					"expl_cb" => utf8_normalize($row["expl_cb"]),
					"expl_notice" => utf8_normalize($row["expl_notice"]),
					"expl_bulletin" => utf8_normalize($row["expl_bulletin"]),
					"idnot" => utf8_normalize($row["idnot"]),
					"tit" => utf8_normalize($row["tit"]),
				);
				$results[] = $result;
			}
		
			return $results;
		} else {
			return array();
		}
	}
		
	public function listLoansGroups($loan_type=0, $limite_mysql='', $limite_page='') {
		global $dbh, $msg;
		
		if (SESSrights & CIRCULATION_AUTH) {
			$results = array();
		
			$critere_requete = "";
			if ($loan_type) {
				switch ($loan_type) {
					case LIST_LOAN_LATE:
						$critere_requete .= "And pret_retour < curdate()";	
						break;
					case LIST_LOAN_CURRENT:
						$critere_requete .= "";
						break;
				}
			}
		
			//REQUETE SQL
			$sql = "SELECT id_groupe, libelle_groupe, resp_groupe, ";
			$sql .= "id_empr, empr_cb, empr_nom, empr_prenom, empr_mail, ";
			$sql .= "pret_idexpl, pret_date, pret_retour, ";
			$sql .= "expl_cote, expl_id, expl_cb, ";
			$sql .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
			$sql .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
			$sql .= " IF(pret_retour>=curdate(),0,1) as retard, " ; 
			$sql .= " expl_notice, expl_bulletin, notices_m.notice_id as idnot, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit ";
			$sql .= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
			$sql.= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
			$sql.= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), " ;
			$sql.= "        empr,pret,empr_groupe, groupe "; 
			$sql .= "WHERE pret.pret_idempr = empr.id_empr AND pret.pret_idexpl = exemplaires.expl_id AND empr_groupe.empr_id = empr.id_empr AND groupe.id_groupe = empr_groupe.groupe_id ";
			$sql .= $critere_requete; 
			if ($limite_mysql && $limite_page) {
				$sql = $sql." LIMIT ".$limite_mysql.", ".$limite_page; 
			} 
			// on lance la requête (mysql_query)  
			$res = pmb_mysql_query($sql, $dbh);
	
			if (!$res)
				throw new Exception("Not found: Error");
			
			while ($row = pmb_mysql_fetch_assoc($res)) {
				$result = array(
					"id_groupe" => utf8_normalize($row["id_groupe"]),
					"libelle_groupe" => utf8_normalize($row["libelle_groupe"]),
					"resp_groupe" => utf8_normalize($row["resp_groupe"]),
					"id_empr" => $row["id_empr"],
					"empr_cb" => $row["empr_cb"],
					"empr_nom" => utf8_normalize($row["empr_nom"]),
					"empr_prenom" => utf8_normalize($row["empr_prenom"]),
					"empr_mail" => utf8_normalize($row["empr_mail"]),
					"pret_idexpl" => utf8_normalize($row["pret_idexpl"]),
					"pret_date" => utf8_normalize($row["pret_date"]),
					"pret_retour" => utf8_normalize($row["pret_retour"]),
					"expl_cote" => utf8_normalize($row["expl_cote"]),
					"expl_id" => utf8_normalize($row["expl_id"]),
					"expl_cb" => utf8_normalize($row["expl_cb"]),
					"aff_pret_date" => utf8_normalize($row["aff_pret_date"]),
					"aff_pret_retour" => utf8_normalize($row["aff_pret_retour"]),
					"retard" => utf8_normalize($row["retard"]),
					"expl_notice" => utf8_normalize($row["expl_notice"]),
					"expl_bulletin" => utf8_normalize($row["expl_bulletin"]),
					"idnot" => utf8_normalize($row["idnot"]),
					"tit" => utf8_normalize($row["tit"]),
				);
				$results[] = $result;
			}
			return $results;
		} else {
			return array();
		}
	}

	public function buildPdfLoansDelayReaders($t_empr, $f_loc=0, $niveau_relance=0) {
			
	}
	
	
	public function buildPdfLoansRunningGroup($id_groupe='') {
		
	}
	
	public function buildPdfLoansDelayGroup ($groupe_id) {
		
	}
	
	public function buildPdfLoansRunningReader($id_empr, $location_biblio) {
		
	}
	
	
	public function buildPdfLoansDelayReader($id_empr, $biblio_location=0, $niveau_relance=0) {
		
	}
	
	/**
	 * 
	 * Envoi de mail auto
	 * @param $type_send READER=1,GROUP=2
	 * @param $ident
	 */
	public function sendMailLoansRunning($type_send, $ident, $location_biblio) {
	
	}
	
	/**
	 * 
	 * Envoi de mail auto
	 * @param $type_send READER=1,GROUP=2
	 * @param $ident
	 */
	public function sendMailLoansDelay($type_send, $ident) {
		/*Quasi-identique à sendMailLoansRunning */
		
		return "";
	}
	
	public function get_texts($relance) {
		global $fdp, $after_list,$before_recouvrement,$after_recouvrement,$limite_after_list, $before_list;
		global $madame_monsieur, $nb_1ere_page, $nb_par_page, $taille_bloc_expl, $debut_expl_1er_page,$debut_expl_page;
		global $marge_page_gauche, $marge_page_droite, $largeur_page, $hauteur_page,$format_page;
		global $biblio_name, $biblio_email,$biblio_phone;
//		global $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_country,$biblio_website;
//		global $biblio_logo, $txt_biblio_info,$biblio_state ;
		global $pmb_lecteurs_localises;

		$var = "pdflettreretard_".$relance."fdp";
		global ${$var};
		eval ("\$fdp=\"".${$var}."\";");
	
		// le texte après la liste des ouvrages en retard
		$var = "pdflettreretard_".$relance."after_list";
		global ${$var};
		eval ("\$after_list=\"".${$var}."\";");
		
		// Le texte avant la liste des ouvrages qui passeront en recouvrement
		$var = "pdflettreretard_".$relance."before_recouvrement";
		global ${$var};
		eval ("\$before_recouvrement=\"".${$var}."\";");
		
		// Le texte après la liste des ouvrages qui passeront en recouvrement
		$var = "pdflettreretard_".$relance."after_recouvrement";
		global ${$var};
		eval ("\$after_recouvrement=\"".${$var}."\";");
			
		
		// la position verticale limite du texte after_liste (si >, saut de page et impression)
		$var = "pdflettreretard_".$relance."limite_after_list";
		global ${$var};
		$limite_after_list = ${$var};
				
		// le texte avant la liste des ouvrges en retard
		$var = "pdflettreretard_".$relance."before_list";
		global ${$var};
		eval ("\$before_list=\"".${$var}."\";");
		
		// le "Madame, Monsieur," ou tout autre truc du genre "Cher adhérent,"
		$var = "pdflettreretard_".$relance."madame_monsieur";
		global ${$var};
		eval ("\$madame_monsieur=\"".${$var}."\";");
		
		// le nombre de blocs expl à imprimer sur la première page
		$var = "pdflettreretard_".$relance."nb_1ere_page";
		global ${$var};
		$nb_1ere_page = ${$var};
		
		// le nombre de blocs expl à imprimer sur les pages suivantes
		$var = "pdflettreretard_".$relance."nb_par_page";
		global ${$var};
		$nb_par_page = ${$var};
		
		// la taille d'un bloc expl en retard affiché
		$var = "pdflettreretard_".$relance."taille_bloc_expl";
		global ${$var};
		$taille_bloc_expl = ${$var};
		
		// la position verticale du premier bloc expl sur la première page
		$var = "pdflettreretard_".$relance."debut_expl_1er_page";
		global ${$var};
		$debut_expl_1er_page = ${$var};
		
		// la position verticale du premier bloc expl sur les pages suivantes
		$var = "pdflettreretard_".$relance."debut_expl_page";
		global ${$var};
		$debut_expl_page = ${$var};
		
		// la marge gauche des pages
		$var = "pdflettreretard_".$relance."marge_page_gauche";
		global ${$var};
		$marge_page_gauche = ${$var};
		
		// la marge droite des pages
		$var = "pdflettreretard_".$relance."marge_page_droite";
		global ${$var};
		$marge_page_droite = ${$var};
		
		// la largeur des pages
		$var = "pdflettreretard_1largeur_page";
		global ${$var};
		$largeur_page = ${$var};
		
		// la hauteur des pages
		$var = "pdflettreretard_1hauteur_page";
		global ${$var};
		$hauteur_page = ${$var};
		
		// le format des pages
		$var = "pdflettreretard_1format_page";
		global ${$var};
		$format_page = ${$var};
	}
	
	public function infos_biblio($location_biblio=0) {
		global $dbh,$pmb_lecteurs_localises;
		global $biblio_name, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email,$biblio_website;
		global $biblio_logo;
		
		if ($pmb_lecteurs_localises) {
			if (!$location_biblio) {
				global $deflt2docs_location;
				$location_biblio = $deflt2docs_location;
			}
			$query = "select name, adr1,adr2,cp,town,state,country,phone,email,website,logo from docs_location where idlocation=".$location_biblio;
			$res = pmb_mysql_query($query,$dbh);
			if (pmb_mysql_num_rows($res) == 1) {
				$row = pmb_mysql_fetch_object($res);
				$biblio_name = $row->name;
				$biblio_adr1 = $row->adr1;
				$biblio_adr2 = $row->adr2;
				$biblio_cp = $row->cp;
				$biblio_town = $row->town;
				$biblio_state = $row->state;
				$biblio_country = $row->country;
				$biblio_phone = $row->phone;
				$biblio_email = $row->email;
				$biblio_website = $row->website;
				$biblio_logo = $row->logo;
			}	
		} else {
			/*** Informations provenant des paramètres généraux - on ne parle donc pas de multi-localisations **/
			// nom de la structure
			$var = "opac_biblio_name";
			global ${$var};
			eval ("\$biblio_name=\"".${$var}."\";");
		
			// logo de la structure
			$var = "opac_logo";
			global ${$var};
			eval ("\$biblio_logo=\"".${$var}."\";");
		
			// adresse principale
			$var = "opac_biblio_adr1";
			global ${$var};
			eval ("\$biblio_adr1=\"".${$var}."\";");
			
			// adresse secondaire
			$var = "opac_biblio_adr2";
			global ${$var};
			eval ("\$biblio_adr2=\"".${$var}."\";");
			
			// code postal
			$var = "opac_biblio_cp";
			global ${$var};
			eval ("\$biblio_cp=\"".${$var}."\";");
			
			// ville
			$var = "opac_biblio_town";
			global ${$var};
			eval ("\$biblio_town=\"".${$var}."\";");
			
//			// Etat
			$var = "opac_biblio_state";
			global ${$var};
			eval ("\$biblio_state=\"".${$var}."\";");
			
			// pays
			$var = "opac_biblio_country";
			global ${$var};
			eval ("\$biblio_country=\"".${$var}."\";");
			
			// telephone
			$var = "opac_biblio_phone";
			global ${$var};
			eval ("\$biblio_phone=\"".${$var}."\";");
			
			// adresse mail
			$var = "opac_biblio_email";
			global ${$var};
			eval ("\$biblio_email=\"".${$var}."\";");
			
			//site web
			$var = "opac_biblio_website";
			global ${$var};
			eval ("\$biblio_website=\"".${$var}."\";");
		}
	}
}

?>