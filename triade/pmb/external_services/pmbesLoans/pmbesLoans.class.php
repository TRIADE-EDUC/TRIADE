<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesLoans.class.php,v 1.15 2023/08/28 14:01:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");

define('LOAN_ALL_ACTIONS','1');
define('LOAN_PRINT_MAIL','2');
define('LOAN_CSV_MAIL','3');

class pmbesLoans extends external_services_api_class {

	//ex: "empr","empr_list","b,n,c,g","b,n,c,g".$localisation.",cs","n,g"
	// correspondance : ./includes/filter_list/empr/empr_list.xml
	// les 2 premiers params doivent-ils plutôt être forcées ??
	public function filterLoansReaders($filter_name, $filter_source = '', $display = '', $filter = '', $sort = '', $parameters = []) {
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
					"empr_nom" => encoding_normalize::utf8_normalize($row["empr_nom"]),
					"empr_prenom" => encoding_normalize::utf8_normalize($row["empr_prenom"]),
					"categ_libelle" => encoding_normalize::utf8_normalize($row["libelle"]),
					"group_name" => encoding_normalize::utf8_normalize($row["group_name"]),
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
			array_walk($t_empr, function(&$a) {$a = intval($a);}); //Soyons sûr de ne stocker que des entiers dans le tableau.
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
			array_walk($t_empr, function(&$a) {$a = intval($a);}); //Soyons sûr de ne stocker que des entiers dans le tableau.
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
		global $msg, $pmb_lecteurs_localises;

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

			$res = pmb_mysql_query($sql);
			if (!$res) {
				return false;
	//			throw new Exception("Not found: Error");
			}

			while ($row = pmb_mysql_fetch_assoc($res)) {
				$result = array(
					"aff_pret_date" => encoding_normalize::utf8_normalize($row["aff_pret_date"]),
					"aff_pret_retour" => encoding_normalize::utf8_normalize($row["aff_pret_retour"]),
					"retard" => encoding_normalize::utf8_normalize($row["retard"]),
					"id_empr" => $row["id_empr"],
					"empr_nom" => encoding_normalize::utf8_normalize($row["empr_nom"]),
					"empr_prenom" => encoding_normalize::utf8_normalize($row["empr_prenom"]),
					"empr_mail" => encoding_normalize::utf8_normalize($row["empr_mail"]),
					"empr_cb" => $row["empr_cb"],
					"expl_cote" => encoding_normalize::utf8_normalize($row["expl_cote"]),
					"expl_cb" => encoding_normalize::utf8_normalize($row["expl_cb"]),
					"expl_notice" => encoding_normalize::utf8_normalize($row["expl_notice"]),
					"expl_bulletin" => encoding_normalize::utf8_normalize($row["expl_bulletin"]),
					"idnot" => encoding_normalize::utf8_normalize($row["idnot"]),
				    "tit" => encoding_normalize::utf8_normalize($row["tit"]),
				    'pnb_flag' => $row['pret_pnb_flag'],
				);
				$results[] = $result;
			}

			return $results;
		} else {
			return array();
		}
	}

	public function listLoansGroups($loan_type=0, $limite_mysql=0, $limite_page=0) {
		global $msg;

		$loan_type = intval($loan_type);
		$limite_mysql = intval($limite_mysql);
		$limite_page = intval($limite_page);
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
			$res = pmb_mysql_query($sql);

			if (!$res)
				throw new Exception("Not found: Error");

			while ($row = pmb_mysql_fetch_assoc($res)) {
				$result = array(
					"id_groupe" => encoding_normalize::utf8_normalize($row["id_groupe"]),
					"libelle_groupe" => encoding_normalize::utf8_normalize($row["libelle_groupe"]),
					"resp_groupe" => encoding_normalize::utf8_normalize($row["resp_groupe"]),
					"id_empr" => $row["id_empr"],
					"empr_cb" => $row["empr_cb"],
					"empr_nom" => encoding_normalize::utf8_normalize($row["empr_nom"]),
					"empr_prenom" => encoding_normalize::utf8_normalize($row["empr_prenom"]),
					"empr_mail" => encoding_normalize::utf8_normalize($row["empr_mail"]),
					"pret_idexpl" => encoding_normalize::utf8_normalize($row["pret_idexpl"]),
					"pret_date" => encoding_normalize::utf8_normalize($row["pret_date"]),
					"pret_retour" => encoding_normalize::utf8_normalize($row["pret_retour"]),
					"expl_cote" => encoding_normalize::utf8_normalize($row["expl_cote"]),
					"expl_id" => encoding_normalize::utf8_normalize($row["expl_id"]),
					"expl_cb" => encoding_normalize::utf8_normalize($row["expl_cb"]),
					"aff_pret_date" => encoding_normalize::utf8_normalize($row["aff_pret_date"]),
					"aff_pret_retour" => encoding_normalize::utf8_normalize($row["aff_pret_retour"]),
					"retard" => encoding_normalize::utf8_normalize($row["retard"]),
					"expl_notice" => encoding_normalize::utf8_normalize($row["expl_notice"]),
					"expl_bulletin" => encoding_normalize::utf8_normalize($row["expl_bulletin"]),
					"idnot" => encoding_normalize::utf8_normalize($row["idnot"]),
				    "tit" => encoding_normalize::utf8_normalize($row["tit"]),
				    'pnb_flag' => $row['pret_pnb_flag'],
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
}

?>