<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_controller.class.php,v 1.9.4.3 2025/04/10 14:39:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/caddie/caddie_root_controller.class.php");
require_once($class_path."/etagere_caddies.class.php");

class caddie_controller extends caddie_root_controller {

	protected static $model_class_name = 'caddie';

	protected static $procs_class_name = 'caddie_procs';

	public static function proceed($id=0) {
		global $lvl;
		global $mode;

		$notices = '';
		$message = '';
		switch ($lvl) {
			case "more_results":
				//changement de plan !
				switch ($mode) {
					case "tous" :
						$notices = static::get_notices_from_searcher_class('searcher_all_fields');
						break;
					case "title":
					case "titre":
						$notices = static::get_notices_from_searcher_class('searcher_title');
						break;
					case "keyword":
						$notices = static::get_notices_from_searcher_class('searcher_keywords');
						break;
					case "abstract":
						$notices = static::get_notices_from_searcher_class('searcher_abstract');
						break;
					case "extended":
						$notices = static::get_notices_from_searcher_class('searcher_extended');
						break;
					case "external":
						if ($_SESSION["ext_type"]=="multi") $es=new search("search_fields_unimarc"); else $es=new search("search_simple_fields_unimarc");
						$table=$es->make_search();
						$requete="select concat('es', notice_id) as notice_id from $table where 1;";
						$message=cart::add_query($requete);
						break;
					case 'docnum' :
						//droits d'acces emprunteur/notice
						$acces_j='';
						global $class_path;
						global $gestion_acces_active, $gestion_acces_empr_notice;
						global $clause, $clause_bull, $clause_bull_num_notice;

						if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
							require_once("$class_path/acces.class.php");
							$ac= new acces();
							$dom_2= $ac->setDomain(2);
							$acces_j= $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
						}
						if ($acces_j) {
							$statut_j='';
						} else {
							$statut_j=',notice_statut';
						}
						$q_noti = "select notice_id from explnum, notices $statut_j $acces_j ".stripslashes($clause).' ';
						$q_bull  = "select notice_id from bulletins, explnum, notices $statut_j $acces_j ".stripslashes($clause_bull).' ';
						$q_bull_num_notice  = "select notice_id from bulletins, explnum, notices $statut_j $acces_j ".stripslashes($clause_bull_num_notice).' ';
						$q = "select uni.notice_id from ($q_noti UNION $q_bull UNION $q_bull_num_notice) as uni";

						$notices = static::get_notices_from_query($q);
						break;
				}
				break;
			case "author_see":
				$notices = static::get_notices_from_authority_page('author', $id);
				break;
			case "categ_see":
				global $opac_auto_postage_etendre_recherche, $opac_auto_postage_nb_descendant, $opac_auto_postage_nb_montant;
				global $nb_level_enfants, $nb_level_descendant, $nb_level_parents, $nb_level_montant;

				//LISTE DES NOTICES ASSOCIEES
				//Lire le champ path du noeud pour étendre la recherche éventuellement au fils et aux père de la catégorie
				// lien Etendre auto_postage
				if (empty($nb_level_enfants)) {
					// non defini, prise des valeurs par défaut
					if (isset($_SESSION["nb_level_enfants"]) && $opac_auto_postage_etendre_recherche) $nb_level_descendant=$_SESSION["nb_level_enfants"];
					else $nb_level_descendant=$opac_auto_postage_nb_descendant;
				} else {
					$nb_level_descendant=$nb_level_enfants;
				}

				// lien Etendre auto_postage
				if(empty($nb_level_parents)) {
					// non defini, prise des valeurs par défaut
					if(isset($_SESSION["nb_level_parents"]) && $opac_auto_postage_etendre_recherche) $nb_level_montant=$_SESSION["nb_level_parents"];
					else $nb_level_montant=$opac_auto_postage_nb_montant;
				} else {
					$nb_level_montant=$nb_level_parents;
				}
				$notices = static::get_notices_from_authority_page('category', $id);
				break;
			case "indexint_see":
				$notices = static::get_notices_from_authority_page('indexint', $id);
				break;
			case "coll_see":
				$notices = static::get_notices_from_authority_page('collection', $id);
				break;
			case "publisher_see":
				$notices = static::get_notices_from_authority_page('publisher', $id);
				break;
			case "serie_see":
				$notices = static::get_notices_from_authority_page('serie', $id);
				break;
			case "subcoll_see":
				$notices = static::get_notices_from_authority_page('subcollection', $id);
				break;
			case "etagere_see":
				$etagere_caddies = new etagere_caddies($id);
				$q = "select distinct object_id as notice_id, caddie_content.caddie_id, caddie_content.flag from caddie_content join etagere_caddie on caddie_content.caddie_id=etagere_caddie.caddie_id where etagere_id='$id'";
				$notices = $etagere_caddies->get_notices_from_query($q, true);
				break;
			case "dsi":
				$q = "select distinct num_notice as notice_id from bannette_contenu where num_bannette='$id' " ;
				$notices = static::get_notices_from_query($q, true);
				break;
			case "analysis":
				$q = "select distinct analysis_notice as notice_id from analysis where analysis_bulletin='$id' " ;
				$notices = static::get_notices_from_query($q, true);
				break;
			case "loans_all":
				global $id_empr;
				$sql = "SELECT if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as notice_id ";
				$sql.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
				$sql.= "LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
				$sql.= "LEFT JOIN notices AS notices_s ON num_notice = notices_s.notice_id), pret ";
				$sql.= "WHERE pret_idexpl = expl_id AND pret_idempr='$id_empr' ";
				$sql.= "AND (notices_m.notice_id<>0 OR notices_s.notice_id<>0)";

				$notices = static::get_notices_from_query($sql);
				break;
			case "loans_old":
				global $id_empr;
				global $opac_empr_hist_nb_max, $opac_empr_hist_nb_jour_max;

				$limit = '';
				$restrict_date = '';
				if ($opac_empr_hist_nb_max) {
					$limit=" LIMIT 0, $opac_empr_hist_nb_max ";
				}
				if ($opac_empr_hist_nb_jour_max) {
					$restrict_date=" date_add(pret_archive.arc_fin, INTERVAL $opac_empr_hist_nb_jour_max day)>=sysdate() AND ";
				}
				$sql = "SELECT if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as notice_id ";
				$sql.= "FROM (((pret_archive LEFT JOIN notices AS notices_m ON arc_expl_notice = notices_m.notice_id ) ";
				$sql.= "LEFT JOIN bulletins ON arc_expl_bulletin = bulletins.bulletin_id) ";
				$sql.= "LEFT JOIN notices AS notices_s ON num_notice = notices_s.notice_id), empr ";
				$sql.= "WHERE $restrict_date empr.id_empr = arc_id_empr and arc_id_empr='$id_empr' ";
				$sql.= " and arc_fin < '".date("Y-m-d H:i:s")."'";
				$sql.= " group by notice_id ";
				$sql.= " having notice_id<>0";
				$sql.= $limit;

				$notices = static::get_notices_from_query($sql);
				break;
			case "search_segment":
   			    $notices = static::get_notices_from_search_segment($id, $mode);
			    break;
			case "ai_search":
   			    $notices = static::get_notices_from_ai_search($id, $mode);
			    break;
		}
		if($notices) {
			$message = cart::add_entities($notices);
		}
		return $message;
	}

	public static function get_notices_from_searcher_class($searcher_class) {
		global $opac_max_cart_items;

		$searcher_instance = new $searcher_class(static::$user_query);
		if(isset($_SESSION["last_sortnotices"]) && $_SESSION["last_sortnotices"] !==""){
			$cart_sort=$_SESSION["last_sortnotices"];
		}else{
			$cart_sort="default";
		}
		$notices = $searcher_instance->get_sorted_cart_result($cart_sort,0,$opac_max_cart_items);
		if(is_countable($notices) && count($notices)){
			$notices = implode(",",$notices);
		}
		return $notices;
	}

	public static function get_notices_from_query($query, $with_filters=false) {
		$notices = '';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$tab_notices=array();
			while($row=pmb_mysql_fetch_object($result)) {
				$tab_notices[]=$row->notice_id;
			}
			$notices=implode(',',$tab_notices);
		}
		if($notices && $with_filters) {
			$fr = new filter_results($notices);
			$notices = $fr->get_results();
		}
		return $notices;
	}

	public static function get_notices_from_authority_page($authority_type, $authority_id) {
		global $class_path;

		$notices = '';
		$authority_page_class = "authority_page_".$authority_type;
		$authority_page = new $authority_page_class($authority_id);
		$records_ids = $authority_page->get_records_ids();
		if(count($records_ids)) {
			$notices = implode(',', $records_ids);
		}
		return $notices;
	}

	public static function get_notices_from_search_segment($id, $mode ='') {
	    $num_notices = $_SESSION['segment_result'][$id];
	    //on ajoute 'es' dans le cas des notices externes
	    if (!empty($mode) && $mode == TYPE_EXTERNAL){
    	    $num_notices_array = array();
    	    $num_notices_array = explode(',', $num_notices);
    	    foreach ($num_notices_array as $key=>$num){
    	        $num_notices_array[$key] = 'es'.$num;
    	    }
    	    $num_notices = implode(',', $num_notices_array);
	    }
	    return $num_notices;
	}

	public static function get_notices_from_ai_search($id, $mode ='') {
		return implode(',', $_SESSION['ai_search_result'][$id] ?? []);
	}

}
