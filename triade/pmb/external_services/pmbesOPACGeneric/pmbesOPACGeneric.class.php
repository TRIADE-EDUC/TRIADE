<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesOPACGeneric.class.php,v 1.21 2023/08/28 14:01:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
global $charset, $opac_url_base;
global $opac_etagere_order ;
global $gestion_acces_active, $gestion_acces_empr_notice;
global $opac_autres_lectures_tri;
global $opac_autres_lectures_nb_mini_emprunts;
global $opac_autres_lectures_nb_maxi;
global $opac_autres_lectures_nb_jours_maxi;
global $opac_autres_lectures;

require_once $class_path."/external_services.class.php";


class pmbesOPACGeneric extends external_services_api_class
{

    //Types de selecteurs pris en charge
    const SELECTOR_TYPES_AVAILABLE = [
        'categories',
        'authors',
        'authors_person',
        'congres_name',
        'collectivite_name',
        'publishers',
        'titres_uniformes',
        'collections',
        'subcollections',
        'indexint',
        'serie'
    ];
    
    //Nombre max de resultats retournes
    const SELECTOR_MAX_RESULTS_DEFAULT = 20;
    
    
    protected $max_results = 20;
    

	/**
	 * 
	 * @param integer $OPACUserId : Id emprunteur
	 * @param integer $select : Filtre etageres 
	 * @return array
	 */
	public function list_shelves($OPACUserId, $filter = 0) {
		
		global $opac_etagere_order ;
		
		$tableau_etagere = [] ;

		//Par defaut, recuperation des etageres valides et visibles en page d'accueil (retro-compatibilite)
		$where_clause = "where visible_accueil=1 and ( (validite_date_deb<=sysdate() and validite_date_fin>=sysdate()) or validite=1 )";
		$filter = intval($filter);
		switch($filter) {
			case 1 :	
				//Recuperation des etageres valides 
				$where_clause = "where ( (validite_date_deb<=sysdate() and validite_date_fin>=sysdate()) or validite=1 )";
				break;
			case 2 :
				//Recuperation de toutes les etageres
				$where_clause = "";
				break;
			default :
				break;
		}

		$order_clause = "name";
		if ($opac_etagere_order) {
			$order_clause = "order by $opac_etagere_order";
		}
		
		$query = "select idetagere, name, comment from etagere $where_clause $order_clause";
		$result = pmb_mysql_query($query);

		if (pmb_mysql_num_rows($result)) {
			while ($etagere=pmb_mysql_fetch_assoc($result)) {
				$tableau_etagere[] = [
						'id' => $etagere['idetagere'],
						'name' => encoding_normalize::utf8_normalize($etagere['name']),
						'comment' => encoding_normalize::utf8_normalize($etagere['comment'])
						];
			}
		}
		return $tableau_etagere;
	}
	
	public function retrieve_shelf_content($shelf_id, $OPACUserId) {

		$shelf_id = intval($shelf_id);
		if (!$shelf_id)
			return array();

		//droits d'acces emprunteur/notice
		$acces_j='';
		global $gestion_acces_active, $gestion_acces_empr_notice;
		if ($OPACUserId != -1 && $gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$acces_j = $dom_2->getJoin($OPACUserId,4,'notice_id');
		}

		if($acces_j) {
			$statut_j='';
			$statut_r='';
		} else {
			$statut_j=',notice_statut';
			$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0) or (notice_visible_opac_abon=1 and notice_visible_opac=1)) ";
		}

		$sql = "SELECT object_id FROM etagere LEFT JOIN etagere_caddie ON (etagere_id = idetagere) LEFT JOIN caddie_content ON (caddie_content.caddie_id = etagere_caddie.caddie_id) LEFT JOIN notices ON (object_id = notice_id) $acces_j $statut_j WHERE etagere_id = ".$shelf_id." AND object_id $statut_r GROUP BY object_id";
		$res = pmb_mysql_query($sql);
		$results = array();
		while($row = pmb_mysql_fetch_row($res)) {
			$results[] = $row[0];
		}
		return $results;
	}
	
	public function list_locations() {

		$results = array();
		$sql = "SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac = 1";
		$res = pmb_mysql_query($sql);
		while($row = pmb_mysql_fetch_assoc($res)) {
			$results[] = array(
				"location_id" => $row["idlocation"],
				"location_caption" => encoding_normalize::utf8_normalize($row["location_libelle"])
			);
		}

		return $results;
	}
	
	public function list_sections($location) {
		
		global $opac_url_base;
		$results = [];
		
		$location = intval($location);
		$requete="select idsection, section_libelle, section_pic from docs_section, exemplaires where expl_location=$location and section_visible_opac=1 and expl_section=idsection group by idsection order by section_libelle ";
		$resultat=pmb_mysql_query($requete);
		while ($r=pmb_mysql_fetch_object($resultat)) {
			$aresult = array();
			$aresult["section_id"] = $r->idsection;
			$aresult["section_location"] = $location;
			$aresult["section_caption"] = encoding_normalize::utf8_normalize($r->section_libelle);
			$aresult["section_image"] = $opac_url_base.($r->section_pic ? encoding_normalize::utf8_normalize($r->section_pic) : "images/rayonnage-small.png");
			$results[] = $aresult;
		}
		return $results;
	}
	
	public function is_also_borrowed_enabled() {
		global $opac_autres_lectures_tri;
		return $opac_autres_lectures_tri ? true : false;
	}
	
	public function also_borrowed ($notice_id=0,$bulletin_id=0) {

		global $opac_autres_lectures_tri;
		global $opac_autres_lectures_nb_mini_emprunts;
		global $opac_autres_lectures_nb_maxi;
		global $opac_autres_lectures_nb_jours_maxi;
		global $opac_autres_lectures;
		global $gestion_acces_active,$gestion_acces_empr_notice;
		
		$results = array();
		
		if (!$opac_autres_lectures || (!$notice_id && !$bulletin_id)) return $results;
	
		if (!$opac_autres_lectures_nb_maxi) $opac_autres_lectures_nb_maxi = 999999 ;
		if ($opac_autres_lectures_nb_jours_maxi) $restrict_date=" date_add(oal.arc_fin, INTERVAL $opac_autres_lectures_nb_jours_maxi day)>=sysdate() AND ";
		if ($notice_id) $pas_notice = " oal.arc_expl_notice!=$notice_id AND ";
		if ($bulletin_id) $pas_bulletin = " oal.arc_expl_bulletin!=$bulletin_id AND ";
		// Ajout ici de la liste des notices lues par les lecteurs de cette notice
		$rqt_autres_lectures = "SELECT oal.arc_expl_notice, oal.arc_expl_bulletin, count(*) AS total_prets,
					trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '%d/%m/%Y'),')') ,'')))) as tit, if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id 
				FROM ((((pret_archive AS oal JOIN
					(SELECT distinct arc_id_empr FROM pret_archive nbec where (nbec.arc_expl_notice='".$notice_id."' AND nbec.arc_expl_bulletin='".$bulletin_id."') AND nbec.arc_id_empr !=0) as nbec
					ON (oal.arc_id_empr=nbec.arc_id_empr and oal.arc_id_empr!=0 and nbec.arc_id_empr!=0))
					LEFT JOIN notices AS notices_m ON arc_expl_notice = notices_m.notice_id )
					LEFT JOIN bulletins ON arc_expl_bulletin = bulletins.bulletin_id) 
					LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				WHERE $restrict_date $pas_notice $pas_bulletin oal.arc_id_empr !=0
				GROUP BY oal.arc_expl_notice, oal.arc_expl_bulletin
				HAVING total_prets>=$opac_autres_lectures_nb_mini_emprunts 
				ORDER BY $opac_autres_lectures_tri 
				"; 
	
		$res_autres_lectures = pmb_mysql_query($rqt_autres_lectures); 
		if (!$res_autres_lectures)
			return $results;
		if (pmb_mysql_num_rows($res_autres_lectures)) {
			
			$inotvisible=0;
			$aresult = array();
	
			//droits d'acces emprunteur/notice
			$acces_j='';
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
				$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
			}
				
			if($acces_j) {
				$statut_j='';
				$statut_r='';
			} else {
				$statut_j=',notice_statut';
				$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			}
			
			while (($data=pmb_mysql_fetch_array($res_autres_lectures))) { // $inotvisible<=$opac_autres_lectures_nb_maxi
				$requete = "SELECT  1  ";
				$requete .= " FROM notices $acces_j $statut_j  WHERE notice_id='".$data['not_id']."' $statut_r ";
				$myQuery = pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($myQuery) && $inotvisible<=$opac_autres_lectures_nb_maxi) { // pmb_mysql_num_rows($myQuery)
					$inotvisible++;
					$titre = $data['tit'];
					// **********
					$responsab = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
					$responsab = get_notice_authors($data['not_id']) ;
					$as = array_search ("0", $responsab["responsabilites"]) ;
					if ($as!== FALSE && $as!== NULL) {
						$auteur_0 = $responsab["auteurs"][$as] ;
						$auteur = new auteur($auteur_0["id"]);
						$mention_resp = $auteur->get_isbd();
					} else {
						$aut1_libelle = array();
						$as = array_keys ($responsab["responsabilites"], "1" ) ;
						for ($i = 0 ; $i < count($as) ; $i++) {
							$indice = $as[$i] ;
							$auteur_1 = $responsab["auteurs"][$indice] ;
							$auteur = new auteur($auteur_1["id"]);
							$aut1_libelle[]= $auteur->get_isbd();
						}
						$mention_resp = implode (", ",$aut1_libelle) ;
					}
					$mention_resp ? $auteur = $mention_resp : $auteur="";
				
					$aresult["notice_id"] = $data['not_id'];
					$aresult["notice_title"] = $titre;
					$aresult["notice_author"] = $auteur;
					$results[] = $aresult;
				}
			}
		};
		
		return $results;
		}
	
	public function get_location_information($location_id) {
		$result = array();
		
		$location_id = intval($location_id);
		if (!$location_id)
			throw new Exception("Missing parameter: location_id");
		
		$sql = "SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac = 1 AND idlocation = ".$location_id;
		$res = pmb_mysql_query($sql);
		if ($row = pmb_mysql_fetch_assoc($res))
			$result = array(
				"location_id" => $row["idlocation"],
				"location_caption" => encoding_normalize::utf8_normalize($row["location_libelle"])
			);

		return $result;
	}
	
	public function get_location_information_and_sections($location_id) {
		return array(
			"location" => $this->get_location_information($location_id),
			"sections" => $this->list_sections($location_id)
		);
	}
	
	public function get_section_information($section_id) {

		global $opac_url_base;
		
		$result = [];
		$section_id = intval($section_id);
		if (!$section_id) {
			throw new Exception("Missing parameter: section_id");
		}
		$requete="select idsection, section_libelle, section_pic, expl_location from docs_section, exemplaires where idsection = ".$section_id." and section_visible_opac=1 and expl_section=idsection group by idsection order by section_libelle ";
		$resultat=pmb_mysql_query($requete);
		if ($r=pmb_mysql_fetch_object($resultat)) {
			$result["section_id"] = $r->idsection;
			$result["section_location"] = $r->expl_location;
			$result["section_caption"] = encoding_normalize::utf8_normalize($r->section_libelle);
			$result["section_image"] = $opac_url_base.($r->section_pic ? encoding_normalize::utf8_normalize($r->section_pic) : "images/rayonnage-small.png");
		}
		return $result;
	}
	
	public function get_all_locations_and_sections() {
		
		global $opac_url_base;
		
		$results = [];
		$sql = "SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac = 1";
		$res = pmb_mysql_query($sql);
		while($row = pmb_mysql_fetch_assoc($res)) {
			$aresult = array(
				'location' => array(
					"location_id" => $row["idlocation"],
					"location_caption" => encoding_normalize::utf8_normalize($row["location_libelle"])
				),
				'sections' => array(),
			);
			
			$sql2="select idsection, section_libelle, section_pic from docs_section, exemplaires where expl_location=".($row["idlocation"])." and section_visible_opac=1 and expl_section=idsection group by idsection order by section_libelle ";
			$res2=pmb_mysql_query($sql2);
			while ($r=pmb_mysql_fetch_object($res2)) {
				$asection = array();
				$asection["section_id"] = $r->idsection;
				$asection["section_location"] = $row["idlocation"];
				$asection["section_caption"] = encoding_normalize::utf8_normalize($r->section_libelle);
				$asection["section_image"] = $opac_url_base.($r->section_pic ? encoding_normalize::utf8_normalize($r->section_pic) : "images/rayonnage-small.png");
				$aresult['sections'][] = $asection;
			}
			
			$results[] = $aresult;
		}

		return $results;
	}
	
	public function get_infopage($infopage_id, $js_subst = "", $encoding ="") {
		global $charset, $opac_url_base;
		
		$requete = "SELECT content_infopage FROM infopages WHERE id_infopage = $infopage_id";
		$result = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($result)) {
			$infopage = pmb_mysql_result($result, 0, 0);
			if (!empty($js_subst)) {
				$infopage = str_replace($opac_url_base."index.php?lvl=infopages&amp;pagesid=", "!!INFOPAGE_URL!!", $infopage);
				preg_match_all("/!!INFOPAGE_URL!!([0-9]+)/", $infopage, $tab);
				$nb_tabs = count($tab[0]);
				for ($i = 0; $i < $nb_tabs; $i++) {
					$infopage = preg_replace("/".$tab[0][$i]."/", "#\" onclick=\"".str_replace("!!id!!", $tab[1][$i], $js_subst).";return false;", $infopage);	
				}
			}
			if ($encoding == "utf-8" && $charset != "utf-8") {
			    return encoding_normalize::utf8_normalize($infopage);
			} elseif ($encoding != "utf-8" && $charset == "utf-8") {
			    return encoding_normalize::utf8_decode($infopage);
			} else {
			    return $infopage;
			}
		}
	}
	
	public function get_marc_table($type){
		global $charset;
		$marc_list = new marc_list($type);
		if ($charset != "utf-8"){
			foreach($marc_list->table as $key => $value){
				$marc_list->table[$key] = encoding_normalize::utf8_normalize($value);
			}
		}
		return $marc_list->table;
	}	
	
	/**
	 * Point d'entree selector
	 * @param string $type : type de selecteur
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude : filtre d'exclusion
	 * @return array [id, label]
	 */
	public function selector($type, $search, $filter = '' , $exclude = '')
	{
	    $response = [];
	    //nettoyage $type
	    if( !in_array($type, self::SELECTOR_TYPES_AVAILABLE) ) {
	        return encoding_normalize::utf8_normalize($response);
	    }
	    
	    $this->max_results = self::SELECTOR_MAX_RESULTS_DEFAULT;
	    
	    switch($type) {
	        
	        case 'categories' :
	            $response = $this->getCategories($search, $filter, $exclude);
	            break;
	        case 'authors' :
	            $response = $this->getAuthors($search, $filter, $exclude);
	            break;
	        case 'authors_person' :
	            $response = $this->getAuthorsPerson($search, $filter, $exclude);
	            break;
	        case 'congres_name' :
	            $response = $this->getCongresName($search, $filter, $exclude);
	            break;
	        case 'collectivite_name' :
	            $response = $this->getCollectiviteName($search, $filter, $exclude);
	            break;
	        case 'publishers' :
	            $response = $this->getPublishers($search, $filter, $exclude);
	            break;
	        case 'titres_uniformes' :
	            $response = $this->getTitresUniformes($search, $filter, $exclude);
	            break;
	        case 'collections' :
	            $response = $this->getCollections($search, $filter, $exclude);
	            break;
	        case 'subcollections' :
	            $response = $this->getSubCollections($search, $filter, $exclude);
	            break;
	        case 'indexint' :
	            $response = $this->getIndexint($search, $filter, $exclude);
	            break;
	        case 'serie' :
	            $response = $this->getSerie($search, $filter, $exclude);
	            break;
	    }
	    
	    return encoding_normalize::utf8_normalize($response);
	}
	
	
	/**
	 * Selecteur serie
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label]
	 */
	protected function getSerie($search, $filter, $exclude)
	{
	    $response = [];
	    
	    //nettoyage $filter = inutilise
	    $filter = intval($filter);
	    
	    //nettoyage $exclude = ids series a exclure
	    $tab_exclude = explode(',', $exclude);
	    $tab_autexclude = [];
	    foreach($tab_exclude as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_autexclude[] =  $v;
	        }
	    }
	    $autexclude = implode(',', $tab_autexclude);
	    unset($tab_autexclude);
	    
	    $param1 = '';
	    $equation_filters = search_authorities::get_join_and_clause_from_equation(AUT_TABLE_SERIES, $param1);
	    
	    $restrict = '';
	    if ($autexclude) {
	        $restrict = " AND serie_id not in ($autexclude) ";
	    }
	    
	    //Recherche "commence par"
	    $start=stripslashes($search);
	    $start = str_replace("*","%",$start);
	    
	    $requete = "select serie_id as id, ";
	    $requete.= "serie_name as label ";
	    $requete.= "from series ".$equation_filters['join']." where serie_name like '".addslashes($start)."%' ".$restrict." ".$equation_filters['clause']." ";
	    $requete.= "order by label limit ".$this->max_results;
	    
	    $res = pmb_mysql_query($requete);
	    
	    if(pmb_mysql_num_rows($res)) {
	        while(($row = pmb_mysql_fetch_object($res)) ) {
	            $response[] = [
	                'id' => $row->id,
	                'label' => $row->label,
	            ];
	        }
	    }
	    return $response;
	}
	
	
	/**
	 * Selecteur indexation
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label]
	 */
	protected function getIndexint($search, $filter, $exclude)
	{
	    $response = [];
	    
	    //nettoyage $filter = id plan de classement
	    $filter = intval($filter);
	    
	    //nettoyage $exclude = ids indexations a exclure
	    $tab_exclude = explode(',', $exclude);
	    $tab_autexclude = [];
	    foreach($tab_exclude as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_autexclude[] =  $v;
	        }
	    }
	    $autexclude = implode(',', $tab_autexclude);
	    unset($tab_autexclude);
	    
	    $param1 = '';
	    $equation_filters = search_authorities::get_join_and_clause_from_equation(AUT_TABLE_INDEXINT, $param1);
	    
	    $restrict = '';
	    if ($autexclude) {
	        $restrict = " AND indexint_id not in ($autexclude) ";
	    }
	    if ($filter) {
	        $restrict.= " AND num_pclass = ".$filter." ";
	    }
	    
	    //Recherche "commence par"
	    $start=stripslashes($search);
	    $start = str_replace("*","%",$start);
	    $requete = "select indexint_id as id, ";
	    $requete.= "if(indexint_comment is not null and indexint_comment!='',concat(indexint_name,' : ',indexint_comment), indexint_name) as label ";
	    $requete.= "from indexint ".$equation_filters['join']." ";
	    $requete.= "where if(indexint_comment is not null and indexint_comment!='',concat(indexint_name,' - ',indexint_comment),indexint_name) like '".addslashes($start)."%' ".$restrict." ".$equation_filters['clause']." ";
	    $requete.= "order by label limit ".$this->max_results;
	    
	    $res = pmb_mysql_query($requete);
	    
	    if(pmb_mysql_num_rows($res)) {
	        while(($row = pmb_mysql_fetch_object($res)) ) {
	            $response[] = [
	                'id' => $row->id,
	                'label' => $row->label,
	            ];
	        }
	    }
	    return $response;
	}
	
	
	/**
	 * Selecteur sous-collections
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label]
	 */
	protected function getSubCollections($search, $filter, $exclude)
	{
	    $response = [];
	    
	    //nettoyage $filter = id collection parent
	    $filter = intval($filter);
	    
	    //nettoyage $exclude = ids sous-collections a exclure
	    $tab_exclude = explode(',', $exclude);
	    $tab_autexclude = [];
	    foreach($tab_exclude as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_autexclude[] =  $v;
	        }
	    }
	    $autexclude = implode(',', $tab_autexclude);
	    unset($tab_autexclude);
	    
	    $param1 = '';
	    $equation_filters = search_authorities::get_join_and_clause_from_equation(AUT_TABLE_SUB_COLLECTIONS, $param1);
	    
	    $restrict = '';
	    if ($autexclude) {
	        $restrict = " AND sub_coll_id not in ($autexclude) ";
	    }
	    if ($filter) {
	        $restrict .= " AND sub_coll_parent = ".$filter;
	    }
	    
	    //Recherche "commence par"
	    $start=stripslashes($search);
	    $start = str_replace("*","%",$start);
	    $requete = "select sub_coll_id as id, ";
	    $requete.= "if(sub_coll_issn is not null and sub_coll_issn!='',concat(sub_coll_name,', ',sub_coll_issn),sub_coll_name) as label ";
	    $requete.= "from sub_collections ".$equation_filters['join']." ";
	    $requete.= "where if(sub_coll_issn is not null and sub_coll_issn!='',concat(sub_coll_name,', ',sub_coll_issn),sub_coll_name) like '".addslashes($start)."%' ".$restrict." ".$equation_filters['clause']." ";
	    $requete.= "order by label limit ".$this->max_results;
	    
	    $res = pmb_mysql_query($requete);
	    
	    if(pmb_mysql_num_rows($res)) {
	        while(($row = pmb_mysql_fetch_object($res)) ) {
	            $response[] = [
	                'id' => $row->id,
	                'label' => $row->label,
	            ];
	        }
	    }
	    return $response;
	}
	
	
	/**
	 * Selecteur collections
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label]
	 */
	protected function getCollections($search, $filter, $exclude)
	{
	    $response = [];
	    
	    //nettoyage $filter = id collection parent
	    $filter = intval($filter);
	    
	    //nettoyage $exclude = ids collections a exclure
	    $tab_exclude = explode(',', $exclude);
	    $tab_autexclude = [];
	    foreach($tab_exclude as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_autexclude[] =  $v;
	        }
	    }
	    $autexclude = implode(',', $tab_autexclude);
	    unset($tab_autexclude);
	    
	    $param1 = '';
	    $equation_filters = search_authorities::get_join_and_clause_from_equation(AUT_TABLE_COLLECTIONS, $param1);
	    
	    $restrict = '';
	    if ($autexclude) {
	        $restrict = " AND collection_id not in ($autexclude) ";
	    }
	    if ($filter) {
	        $restrict .= " AND collection_parent = ".$filter;
	    }
	    
	    //Recherche "commence par"
	    $start=stripslashes($search);
	    $start = str_replace("*","%",$start);
	    $requete = "select collection_id as id, ";
	    $requete.= "if(collection_issn is not null and collection_issn!='', concat(collection_name,', ',collection_issn),collection_name) as label ";
	    $requete.= "from collections ".$equation_filters['join']." ";
	    $requete.= "where if(collection_issn is not null and collection_issn!='', concat(collection_name,', ',collection_issn),collection_name) like '".addslashes($start)."%' ".$restrict." ".$equation_filters['clause']." ";
	    $requete.= "order by index_coll limit ".$this->max_results;
	    
	    $res = pmb_mysql_query($requete);
	    
	    if(pmb_mysql_num_rows($res)) {
	        while(($row = pmb_mysql_fetch_object($res)) ) {
	            $response[] = [
	                'id' => $row->id,
	                'label' => $row->label,
	            ];
	        }
	    }
	    return $response;
	}
	
	
	/**
	 * Selecteur titres uniformes
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label]
	 */
	protected function getTitresUniformes($search, $filter, $exclude)
	{
	    $response = [];
	    
	    //nettoyage $filter = inutilise
	    $filter = '';
	    
	    //nettoyage $exclude = ids titres uniformes a exclure
	    $tab_exclude = explode(',', $exclude);
	    $tab_autexclude = [];
	    foreach($tab_exclude as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_autexclude[] =  $v;
	        }
	    }
	    $autexclude = implode(',', $tab_autexclude);
	    unset($tab_autexclude);
	    
	    $param1 = '';
	    $equation_filters = search_authorities::get_join_and_clause_from_equation(AUT_TABLE_TITRES_UNIFORMES, $param1);
	    
	    $restrict = '';
	    if ($autexclude) {
	        $restrict = " AND tu_id not in ($autexclude) ";
	    }
	    
	    //Recherche "commence par"
	    $start=stripslashes($search);
	    $start = str_replace("*","%",$start);
	    $requete = "select tu_id as id, ";
	    $requete.= "if(tu_comment is not null and tu_comment!='', concat(tu_name,' : ',tu_comment),tu_name) as label ";
	    $requete.= "from titres_uniformes ".$equation_filters['join']." ";
	    $requete.= "where if(tu_comment is not null and tu_comment!='',concat(tu_name,' - ',tu_comment),tu_name) like '".addslashes($start)."%' ".$restrict." ".$equation_filters['clause']." ";
	    $requete.= "order by label limit ".$this->max_results;
	    
	    $res = pmb_mysql_query($requete);
	    
	    if(pmb_mysql_num_rows($res)) {
	        while(($row = pmb_mysql_fetch_object($res)) ) {
	            $response[] = [
	                'id' => $row->id,
	                'label' => $row->label,
	            ];
	        }
	    }
	    return $response;
	}
	
	
	/**
	 * Selecteur editeurs
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label]
	 */
	protected function getPublishers($search, $filter, $exclude)
	{
	    $response = [];
	    
	    //nettoyage $filter = inutilise
	    $filter = '';
	    
	    //nettoyage $exclude = id editeurs a exclure
	    $tab_exclude = explode(',', $exclude);
	    $tab_autexclude = [];
	    foreach($tab_exclude as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_autexclude[] =  $v;
	        }
	    }
	    $autexclude = implode(',', $tab_autexclude);
	    unset($tab_autexclude);
	    
	    $param1 = '';
	    $equation_filters = search_authorities::get_join_and_clause_from_equation(AUT_TABLE_PUBLISHERS, $param1);
	    
	    $restrict = '';
	    if ($autexclude) {
	        $restrict = " AND ed_id not in ($autexclude) ";
	    }
	    
	    //Recherche "commence par"
	    $start=stripslashes($search);
	    $start = str_replace("*","%",$start);
	    $requete="select ed_id as id, ";
	    $requete.= "concat(
            ed_name,
            if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),' (',''),
            if(ed_ville is not null and ed_ville!='',ed_ville,''),
            if(ed_ville is not null and ed_ville!='' and ed_pays is not null and ed_pays!='',' - ',''),
            if(ed_pays is not null and ed_pays!='',ed_pays,''),
            if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),')','')
            ) as label ";
	    $requete.= "from publishers ".$equation_filters["join"]." ";
	    $requete.= "where concat(
            ed_name,
            if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),' (',''),
            if(ed_ville is not null and ed_ville!='',ed_ville,''),
            if(ed_ville is not null and ed_ville!='' and ed_pays is not null and ed_pays!='',' - ',''),
            if(ed_pays is not null and ed_pays!='',ed_pays,''),
            if((ed_ville is not null and ed_ville!='') or (ed_pays is not null and ed_pays!=''),')','')
            ) like '".addslashes($start)."%' ".$restrict." ".$equation_filters["clause"]." ";
	    $requete.= "order by label limit ".$this->max_results;
	    
	    $res = pmb_mysql_query($requete);
	    
	    if(pmb_mysql_num_rows($res)) {
	        while(($row = pmb_mysql_fetch_object($res)) ) {
	            $response[] = [
	                'id' => $row->id,
	                'label' => $row->label,
	            ];
	        }
	    }
	    return $response;
	}
	
	
	/**
	 * Selecteur auteurs de type collectivite
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label]
	 */
	protected function getCollectiviteName($search, $filter, $exclude)
	{
	    $response = [];
	    
	    //nettoyage $filter = inutilise
	    $filter = '';
	    
	    //nettoyage $exclude = id auteurs a exclure
	    $tab_exclude = explode(',', $exclude);
	    $tab_autexclude = [];
	    foreach($tab_exclude as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_autexclude[] =  $v;
	        }
	    }
	    $autexclude = implode(',', $tab_autexclude);
	    unset($tab_autexclude);
	    
	    $restrict = '';
	    if ($autexclude) {
	        $restrict = " AND author_id not in ($autexclude) ";
	    }
	    
	    //Recherche "commence par"
	    $start=stripslashes($search);
	    $start = str_replace("*","%",$start);
	    $requete = "select author_id as id, ";
	    $requete.= "author_name as label ";
	    $requete.= "from authors ";
	    $requete.= "where author_type='71' and author_name like '".addslashes($start)."%' $restrict ";
	    $requete.= "order by label limit ".$this->max_results;
	    
	    $res = pmb_mysql_query($requete);
	    
	    if(pmb_mysql_num_rows($res)) {
	        while(($row = pmb_mysql_fetch_object($res)) ) {
	            $response[] = [
	                'id' => $row->id,
	                'label' => $row->label,
	            ];
	        }
	    }
	    return $response;
	}
	
	
	/**
	 * Selecteur auteurs de type congres
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label]
	 */
	protected function getCongresName($search, $filter, $exclude)
	{
	    $response = [];
	    
	    //nettoyage $filter = inutilise
	    $filter = '';
	    
	    //nettoyage $exclude = id auteurs a exclure
	    $tab_exclude = explode(',', $exclude);
	    $tab_autexclude = [];
	    foreach($tab_exclude as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_autexclude[] =  $v;
	        }
	    }
	    $autexclude = implode(',', $tab_autexclude);
	    unset($tab_autexclude);
	    
	    $restrict = '';
	    if ($autexclude) {
	        $restrict = " AND author_id not in ($autexclude) ";
	    }
	    
	    //Recherche "commence par"
	    $start=stripslashes($search);
	    $start = str_replace("*","%",$start);
	    $requete = "select author_id as id, ";
	    $requete.= "author_name as label ";
	    $requete.= "from authors ";
	    $requete.= "where author_type='72' and author_name like '".addslashes($start)."%' $restrict ";
	    $requete.= "order by label limit ".$this->max_results;
	    
	    $res = pmb_mysql_query($requete);
	    
	    if(pmb_mysql_num_rows($res)) {
	        while(($row = pmb_mysql_fetch_object($res)) ) {
	            $response[] = [
	                'id' => $row->id,
	                'label' => $row->label,
	            ];
	        }
	    }
	    return $response;
	}
	
	
	/**
	 * Selecteur auteurs de type personne
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label]
	 */
	protected function getAuthorsPerson($search, $filter, $exclude)
	{
	    $response = [];
	    
	    //nettoyage $filter = inutilise
	    $filter = '';
	    
	    //nettoyage $exclude = id auteurs a exclure
	    $tab_exclude = explode(',', $exclude);
	    $tab_autexclude = [];
	    foreach($tab_exclude as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_autexclude[] =  $v;
	        }
	    }
	    $autexclude = implode(',', $tab_autexclude);
	    unset($tab_autexclude);
	    
	    $restrict = '';
	    if ($autexclude) {
	        $restrict = " AND author_id not in ($autexclude) ";
	    }
	    
	    //Recherche "commence par"
	    $start=stripslashes($search);
	    $start = str_replace("*","%",$start);
	    $requete = "select author_id as id, ";
	    $requete.= "if(author_date!='',concat(if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name),' (',author_date,')'),if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name)) as label ";
	    $requete.= "from authors ";
	    $requete.= "where author_type='70' and if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name) like '".addslashes($start)."%' $restrict ";
	    $requete.= "order by label limit ".$this->max_results;
	    
	    $res = pmb_mysql_query($requete);
	    
	    if(pmb_mysql_num_rows($res)) {
	        while(($row = pmb_mysql_fetch_object($res)) ) {
	            $response[] = [
	                'id' => $row->id,
	                'label' => $row->label,
	            ];
	        }
	    }
	    return $response;
	}
	
	
	/**
	 * Selecteur auteurs
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label]
	 */
	protected function getAuthors($search, $filter, $exclude)
	{
	    $response = [];
	    
	    //nettoyage $filter = inutilise
	    $filter = '';
	    
	    //nettoyage $exclude = id auteurs a exclure
	    $tab_exclude = explode(',', $exclude);
	    $tab_autexclude = [];
	    foreach($tab_exclude as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_autexclude[] =  $v;
	        }
	    }
	    $autexclude = implode(',', $tab_autexclude);
	    unset($tab_autexclude);
	    
	    $param1 = '';
	    $equation_filters=search_authorities::get_join_and_clause_from_equation(AUT_TABLE_AUTHORS, $param1);
	    
	    $restrict = '';
	    if ($autexclude) {
	        $restrict = " AND author_id not in ($autexclude) ";
	    }
	    
	    //Recherche "commence par"
	    $start=stripslashes($search);
	    $start = str_replace("*","%",$start);
	    $requete = "select author_id as id, ";
	    $requete.= "if(author_date!='', concat(if(author_rejete is not null and author_rejete!='', concat(author_name,', ',author_rejete),author_name),' (',author_date,')'), if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name)) as label ";
	    $requete.= "from authors ".$equation_filters['join']." ";
	    $requete.= "where if(author_rejete is not null and author_rejete!='',concat(author_name,', ',author_rejete),author_name) like '".addslashes($start)."%' ".$restrict.$equation_filters['clause']." ";
	    $requete.= "order by label limit ".$this->max_results;
	    
	    $res = pmb_mysql_query($requete);
	    
	    if(pmb_mysql_num_rows($res)) {
	        while(($row = pmb_mysql_fetch_object($res)) ) {
	            $response[] = [
	                'id' => $row->id,
	                'label' => $row->label,
	            ];
	        }
	    }
	    return $response;
	}
	
	
	/**
	 * Selecteur categories
	 *
	 * @param string $search : recherche
	 * @param string $filter : filtre de recherche
	 * @param string $exclude  : filtre d'exclusion
	 * @return array [id, label, short_label]
	 */
	protected function getCategories($search, $filter, $exclude)
	{
	    global $opac_thesaurus, $opac_thesaurus_defaut, $opac_categories_show_only_last, $thesaurus_liste_trad;
	    global $lang;
	    
	    $response = [];
	    
	    //Liste des ids de categories deja traites
	    $tab_categ_ids = [];
	    
	    //nettoyage $filter = ids des thesaurus a explorer
	    $tab_filter = explode(',', $filter);
	    $tab_thesaurus_filter = [];
	    foreach($tab_filter as $v) {
	        $v = intval($v);
	        if($v) {
	            $tab_thesaurus_filter[] =  $v;
	        }
	    }
	    unset($tab_filter);
	    
	    //nettoyage $exclude = id categorie a exclure
	    $autexclude = intval($exclude);
	    
	    $param1 = '';
	    $equation_filters = search_authorities::get_join_and_clause_from_equation(AUT_TABLE_CATEG, $param1);
	    
	    // mono / multi thesaurus
	    $multi_thesaurus = true;
	    $thesaurus_where_clause = "";
	    $tab_thesaurus = [];
	    $id_thesaurus = 0;
	    
	    if (!$opac_thesaurus) {
	        $multi_thesaurus = false;
	        $id_thesaurus = $opac_thesaurus_defaut;
	        $thesaurus_where_clause = "id_thesaurus = $id_thesaurus and ";
	        $tab_thesaurus[$id_thesaurus] = new thesaurus($id_thesaurus);
	    }
	    
	    //en mode multi thesaurus, on peut definir la liste des thesaurus a explorer
	    $nb_thesaurus = 1;
	    if($multi_thesaurus) {
	        $nb_thesaurus = count($tab_thesaurus_filter);
	        if($nb_thesaurus == 1) {
	            $id_thesaurus = $tab_thesaurus_filter[0];
	            $thesaurus_where_clause = "id_thesaurus = $id_thesaurus and ";
	            $multi_thesaurus = false;
	            $tab_thesaurus[$id_thesaurus] = new thesaurus($id_thesaurus);
	        }
	        if($nb_thesaurus > 1 ) {
	            $thesaurus_where_clause = "id_thesaurus in (".implode(',', $tab_thesaurus_filter).") and ";
	        }
	    }
	    
	    //liste des langues definies pour les thesaurus
	    $tab_thesaurus_translation_list = explode(',', $thesaurus_liste_trad);
	    
	    //Recherche "commence par"
	    $start = stripslashes($search);
	    $start = str_replace("*","%",$start);
	    $aq=new analyse_query($start);
	    $members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
	    $members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");
	    
	    $requete = "SELECT noeuds.id_noeud AS categ_id, noeuds.num_renvoi_voir as categ_see, noeuds.num_thesaurus, noeuds.not_use_in_indexation, ";
	    //1 seul thesaurus / 1 seule langue
	    if( $id_thesaurus && ( ($lang == $tab_thesaurus[$id_thesaurus]->langue_defaut) || !in_array($lang, $tab_thesaurus_translation_list)  ) ) {
	        $requete.= "catdef.langue as langue, ";
	        $requete.= "catdef.libelle_categorie as categ_libelle, ";
	        $requete.= "catdef.index_categorie as index_categorie, ";
	        $requete.= "(".$members_catdef["select"].") as pert ";
	        $requete.= "FROM noeuds ";
	        $requete.= "JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND catdef.langue = '".$tab_thesaurus[$id_thesaurus]->langue_defaut."' ".$equation_filters['join']." ";
	        $requete.= "WHERE noeuds.num_thesaurus = ".$id_thesaurus." and catdef.libelle_categorie like '".addslashes($start)."%' ";
	        $requete.= $equation_filters['clause']." ";
	        if ($autexclude) {
	            $requete.= "AND noeuds.id_noeud != $autexclude AND (noeuds.path NOT LIKE '$autexclude/%' AND noeuds.path NOT LIKE '%/$autexclude/%' AND noeuds.path NOT LIKE '%/$autexclude') ";
	        }
	        $requete.= "order by pert desc, num_thesaurus, categ_libelle ";
	        //+sieurs thesaurus / +sieurs langues
	    } else {
	        $requete.= "if (catlg.num_noeud is null, catdef.langue , catlg.langue) as langue, ";
	        $requete.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as categ_libelle, ";
	        $requete.= "if (catlg.num_noeud is null, catdef.index_categorie , catlg.index_categorie ) as index_categorie, ";
	        $requete.= "if (catlg.num_noeud is null, ".$members_catdef["select"].", ".$members_catlg["select"].") as pert ";
	        $requete.= "FROM thesaurus ";
	        $requete.= "JOIN noeuds ON thesaurus.id_thesaurus = noeuds.num_thesaurus ".$equation_filters['join']." ";
	        $requete.= "LEFT JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND catdef.langue=thesaurus.langue_defaut ";
	        $requete.= "LEFT JOIN categories as catlg on catdef.num_noeud=catlg.num_noeud and catlg.langue = '".$lang."' ";
	        $requete.= "WHERE $thesaurus_where_clause if(catlg.num_noeud is null, catdef.libelle_categorie like '".addslashes($start)."%', catlg.libelle_categorie like '".addslashes($start)."%') ";
	        $requete.= $equation_filters['clause']." ";
	        if ($autexclude) {
	            $requete.= "AND noeuds.id_noeud != $autexclude AND (noeuds.path NOT LIKE '$autexclude/%' AND noeuds.path NOT LIKE '%/$autexclude/%' AND noeuds.path NOT LIKE '%/$autexclude') ";
	        }
	        $requete.= "order by pert desc, thesaurus_order, num_thesaurus, categ_libelle ";
	    }
	    
	    $res = pmb_mysql_query($requete);
	    
	    $n = $this->max_results;
	    
	    if(pmb_mysql_num_rows($res)) {
	        
	        while( $n && ($categ = pmb_mysql_fetch_object($res)) ) {
	            
	            if( !in_array($categ->num_noeud, $tab_categ_ids) ) {
	                
	                $thesaurus_label = '';
	                $categ_id = $categ->categ_id;
	                
	                $categ_label = $categ->categ_libelle;
	                $short_categ_label = $categ->categ_libelle;
	                $categ_see_label = '';
	                $short_categ_see_label = '';
	                $categ_path = '';
	                $categ_not_use_in_indexation = $categ->not_use_in_indexation;
	                
	                //Libelle thesaurus en mode multi_thesaurus
	                if( $multi_thesaurus ) {
	                    if (!array_key_exists($categ->num_thesaurus, $tab_thesaurus) ) {
	                        $tab_thesaurus[$categ->num_thesaurus] = new thesaurus($categ->num_thesaurus);
	                    }
	                    $thesaurus_label = $tab_thesaurus[$categ->num_thesaurus]->libelle_thesaurus;
	                }
	                
	                //categorie renvoyee
	                if( $categ->categ_see ) {
	                    $categ_id = $categ->categ_see;
	                    $category = new category($categ_id);
	                    $categ_not_use_in_indexation = $category->not_use_in_indexation;
	                    $temp = new categories($categ->categ_see, $categ->langue);
	                    $categ_see_label = $temp->libelle_categorie;
	                    $short_categ_see_label = $temp->libelle_categorie;
	                    $categ_path = categories::listAncestorNames($categ->categ_see, $categ->langue);
	                    if( !$opac_categories_show_only_last) {
	                        $categ_see_label = $categ_path;
	                    }
	                } else {
	                    $categ_path = categories::listAncestorNames($categ_id, $categ->langue);
	                    if( !$opac_categories_show_only_last) {
	                        $categ_label = $categ_path;
	                    }
	                }
	                
	                if( !in_array($categ_id, $tab_categ_ids) && !$categ_not_use_in_indexation &&  !preg_match("#:~|^~#i",$categ_path) ) {
	                    
	                    $final_categ_label = !empty($thesaurus_label) ? '['.$thesaurus_label.'] ' : '';
	                    $short_final_categ_label = '';
	                    if( empty($categ_see_label) ) {
	                        $final_categ_label.= $categ_label;
	                        $short_final_categ_label = $short_categ_label;
	                    } else {
	                        $final_categ_label.= $categ_label.' -> '.$categ_see_label .'@';
	                        $short_final_categ_label = $short_categ_label.' -> '.$short_categ_see_label .'@';
	                    }
	                    
	                    $response[] = [
	                        'id' => $categ_id,
	                        'label' => $final_categ_label,
	                        'short_label' => $short_final_categ_label,
	                    ];
	                    
	                    $tab_categ_ids[] = $categ_id;
	                    $n--;
	                }
	            }
	        }
	    }
	    
	    //Si pas assez de resultats, on complete
	    if( count($response) < $this->max_results ) {
	        
	        //Recherche "contient"
	        $aq = new analyse_query(stripslashes($search)."*");
	        $members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
	        $members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");
	        $requete = '';
	        
	        if (!$aq->error) {
	            $requete = "SELECT noeuds.id_noeud AS categ_id, noeuds.num_renvoi_voir as categ_see, noeuds.num_thesaurus, noeuds.not_use_in_indexation, ";
	            //1 seul thesaurus / 1 seule langue
	            if( $id_thesaurus && ( ($lang == $tab_thesaurus[$id_thesaurus]->langue_defaut) || !in_array($lang, $tab_thesaurus_translation_list)  ) ) {
	                $requete.= "catdef.langue as langue, ";
	                $requete.= "catdef.libelle_categorie as categ_libelle, ";
	                $requete.= "catdef.index_categorie as index_categorie, ";
	                $requete.= "(".$members_catdef["select"].") as pert ";
	                $requete.= "FROM noeuds ";
	                $requete.= "JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND  catdef.langue = '".$tab_thesaurus[$id_thesaurus]->langue_defaut."' ".$equation_filters['join']." ";
	                $requete.= "WHERE noeuds.num_thesaurus='".$id_thesaurus."' and catdef.libelle_categorie not like '~%' and ".$members_catdef["where"]." ";
	                $requete.= $equation_filters['clause']." ";
	                if ($autexclude) {
	                    $requete.= "AND noeuds.id_noeud != $autexclude AND (noeuds.path NOT LIKE '$autexclude/%' AND noeuds.path NOT LIKE '%/$autexclude/%' AND noeuds.path NOT LIKE '%/$autexclude') ";
	                }
	                $requete.= "order by pert desc, num_thesaurus, categ_libelle";
	                //+sieurs thesaurus / +sieurs langues
	            } else {
	                $requete.= "if (catlg.num_noeud is null, catdef.langue , catlg.langue) as langue, ";
	                $requete.= "if (catlg.num_noeud is null, catdef.libelle_categorie , catlg.libelle_categorie ) as categ_libelle, ";
	                $requete.= "if (catlg.num_noeud is null, catdef.index_categorie , catlg.index_categorie ) as index_categorie, ";
	                $requete.= "if (catlg.num_noeud is null, ".$members_catdef["select"].", ".$members_catlg["select"].") as pert ";
	                $requete.= "FROM thesaurus ";
	                $requete.= "JOIN noeuds ON thesaurus.id_thesaurus = noeuds.num_thesaurus ".$equation_filters['join']." ";
	                $requete.= "LEFT JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND catdef.langue=thesaurus.langue_defaut ";
	                $requete.= "LEFT JOIN categories as catlg on catdef.num_noeud=catlg.num_noeud and catlg.langue = '".$lang."' ";
	                $requete.= "WHERE $thesaurus_where_clause if(catlg.num_noeud is null, ".$members_catdef["where"].", ".$members_catlg["where"].") ";
	                $requete.= $equation_filters['clause']." ";
	                if ($autexclude) {
	                    $requete.= "AND noeuds.id_noeud != $autexclude AND (noeuds.path NOT LIKE '$autexclude/%' AND noeuds.path NOT LIKE '%/$autexclude/%' AND noeuds.path NOT LIKE '%/$autexclude') ";
	                }
	                $requete.= "order by pert desc, thesaurus_order, num_thesaurus, categ_libelle";
	            }
	            
	        }
	        
	        if (!$requete) {
	            return $response;
	        }
	        
	        $res = pmb_mysql_query($requete);
	        
	        if(pmb_mysql_num_rows($res)) {
	            
	            while( $n && ($categ = pmb_mysql_fetch_object($res)) ) {
	                
	                if( !in_array($categ->num_noeud, $tab_categ_ids) ) {
	                    
	                    $thesaurus_label = '';
	                    $categ_id = $categ->categ_id;
	                    $categ_label = $categ->categ_libelle;
	                    $short_categ_label = $categ->categ_libelle;
	                    $categ_see_label = '';
	                    $short_categ_see_label = '';
	                    $categ_path = '';
	                    $categ_not_use_in_indexation = $categ->not_use_in_indexation;
	                    
	                    //Libelle thesaurus en mode multi_thesaurus
	                    if( $multi_thesaurus ) {
	                        if (!array_key_exists($categ->num_thesaurus, $tab_thesaurus) ) {
	                            $tab_thesaurus[$categ->num_thesaurus] = new thesaurus($categ->num_thesaurus);
	                        }
	                        $thesaurus_label = $tab_thesaurus[$categ->num_thesaurus]->libelle_thesaurus;
	                    }
	                    
	                    //categorie renvoyee
	                    if( $categ->categ_see ) {
	                        $categ_id = $categ->categ_see;
	                        $category = new category($categ_id);
	                        $categ_not_use_in_indexation = $category->not_use_in_indexation;
	                        $temp = new categories($categ->categ_see, $categ->langue);
	                        $categ_see_label = $temp->libelle_categorie;
	                        $short_categ_see_label = $temp->libelle_categorie;
	                        $categ_path = categories::listAncestorNames($categ->categ_see, $categ->langue);
	                        if( !$opac_categories_show_only_last) {
	                            $categ_see_label = $categ_path;
	                        }
	                    } else {
	                        $categ_path = categories::listAncestorNames($categ->categ_id, $categ->langue);
	                        if( !$opac_categories_show_only_last) {
	                            $categ_label = $categ_path;
	                        }
	                    }
	                    
	                    if( !in_array($categ_id, $tab_categ_ids) && !$categ_not_use_in_indexation &&  !preg_match("#:~|^~#i",$categ_path) ) {
	                        
	                        $final_categ_label = !empty($thesaurus_label) ? '['.$thesaurus_label.'] ' : '';
	                        $short_final_categ_label = '';
	                        if( empty($categ_see_label) ) {
	                            $final_categ_label.= $categ_label;
	                            $short_final_categ_label = $short_categ_label;
	                        } else {
	                            $final_categ_label.= $categ_label.' -> '.$categ_see_label .'@';
	                            $short_final_categ_label = $short_categ_label.' -> '.$short_categ_see_label .'@';
	                        }
	                        
	                        $response[] = [
	                            'id' => $categ_id,
	                            'label' => $final_categ_label,
	                            'short_label' => $short_final_categ_label,
	                        ];
	                        
	                        $tab_categ_ids[] = $categ_id;
	                        $n--;
	                    }
	                }
	            }
	        }
	    }
	    return $response;
	    
	}
	
}
