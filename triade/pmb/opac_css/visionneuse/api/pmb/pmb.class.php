<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb.class.php,v 1.72.2.2.2.1 2025/01/30 09:08:07 tsamson Exp $

global $class_path, $include_path, $opac_search_other_function;
require_once("$include_path/notice_affichage.inc.php");
require_once("$include_path/bulletin_affichage.inc.php");
require_once("$class_path/upload_folder.class.php");
require_once("$class_path/search.class.php");
require_once("$class_path/searcher.class.php");
require_once("$class_path/searcher/searcher_factory.class.php");
require_once($class_path."/auth_popup.class.php");
require_once($class_path."/search_universes/search_segment_searcher_extended.class.php");
if($opac_search_other_function){
	require_once($include_path."/".$opac_search_other_function);
}

class pmb extends base_params implements params {
	public $listeDocs = array();		//tableau de documents
	public $current = 0;				//position courante dans le tableau
	public $currentDoc = array();			//tableau décrivant le document courant
	public $params;					//tableau de paramètres utiles pour la recontructions des requetes...et même voir plus
	public $listeBulls = array();
	public $listeNotices = array();
	public $watermark = array();			//Url du watermark si défini  + transparence
	public $bibliInfos = array();

    public function __construct($params,$visionneuse_path) {
    	global $opac_photo_mean_size_x,$opac_photo_mean_size_y;
    	$this->params = $params;

    	$this->driver_name = "pmb";
    	$this->params["maxX"] = $opac_photo_mean_size_x;
    	$this->params["maxY"] = $opac_photo_mean_size_y;
    	$this->visionneuse_path = $visionneuse_path;
    	if($this->params["lvl"] != "afficheur")
	    	$this->recupListDocNum();
	    if($this->params["lvl"] != "afficheur" && $this->params["explnum"] !== 0)
	    	$this->getDocById($this->params["explnum"]);
    }

 	public function getDocById($id){
		$this->getExplnums($id);
 	}

 	public function recupListDocNum(){
 		global $gestion_acces_active,$gestion_acces_empr_notice;
 		global $opac_photo_filtre_mimetype; //filtre des mimetypes;

 		//droits d'acces emprunteur/notice
		$acces_j='';
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1){
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
		}
		if($acces_j){
			$statut_j='';
			$statut_r='';
		}else{
			$statut_j=',notice_statut';
			$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
		}

     	//on reconstruit la requete...
     	$this->listeBulls =array();
     	$this->listeNotices =array();
		$requete_noti = "";
		$requete_bull = "";
		$requete_explnum = "";
	 	$searcher = false;

		switch($this->params['mode']){
			//nouvelle méthode...
			case "title" :
				$searcher = searcher_factory::get_searcher('records', 'title', stripslashes($this->params['user_query']));
				break;
			case "tous" :
				$searcher = searcher_factory::get_searcher('records', 'all_fields', stripslashes($this->params['user_query']));
				break;
			case "keyword" :
				$searcher = searcher_factory::get_searcher('records', 'keywords', stripslashes($this->params['user_query']));
				break;
			case "extended" :
				if($this->params['serialized_search']){
					$searcher = searcher_factory::get_searcher('records', 'extended', stripslashes($this->params['serialized_search']));
				}else{
					$searcher = searcher_factory::get_searcher('records', 'extended', stripslashes($this->params['search']));
				}
				break;
			case "abstract" :
				$searcher = searcher_factory::get_searcher('records', 'abstract', stripslashes($this->params['user_query']));
				break;
			case "authperso_see" :
				$requete_noti = "SELECT notice_id FROM notices_authperso, notices ".$acces_j." ".$statut_j." where notice_authperso_authority_num= ".$this->params["idautorite"]." and notice_authperso_notice_num = notice_id ".$statut_r." ";
				break;
			//autorités , pas optimal, faudra y revenir...
			case "author_see":
				//recup des auteurs associés...
				$rqt_auteurs = "select author_id as aut from authors where author_see='".$this->params["idautorite"]."' and author_id!=0 ";
				$rqt_auteurs .= "union select author_see as aut from authors where author_id='".$this->params["idautorite"]."' and author_see!=0 " ;
				$res_auteurs = pmb_mysql_query($rqt_auteurs);
				$clause_auteurs = "responsability_author in('".$this->params["idautorite"]."' ";
				while(($id_aut=pmb_mysql_fetch_object($res_auteurs))) {
					$clause_auteurs .= ", '".$id_aut->aut."' ";
					$rqt_auteursuite = "select author_id as aut from authors where author_see='$id_aut->aut' and author_id!=0 ";
					$res_auteursuite = pmb_mysql_query($rqt_auteursuite);
					while(($id_autsuite=pmb_mysql_fetch_object($res_auteursuite))) $clause_auteurs .= ", '".$id_autsuite->aut."' ";
				}
				$clause_auteurs .= ")";

				// on lance la vraie requête
				$requete_noti = "SELECT distinct notices.notice_id,niveau_biblio,niveau_hierar FROM notices $acces_j, responsability $statut_j ";
				$requete_noti.= "where $clause_auteurs and notice_id=responsability_notice $statut_r ";
				$requete_noti.= "ORDER BY index_serie,tnvol,index_sew";
				break;
			case "congres_see" :
				//on récup les auteurs associés
				$rqt_auteurs = "select author_id as aut from authors where author_see='".$this->params["idautorite"]."' and author_id!=0 ";
				$rqt_auteurs .= "union select author_see as aut from authors where author_id='".$this->params["idautorite"]."' and author_see!=0 " ;
				$res_auteurs = pmb_mysql_query($rqt_auteurs);
				$clause_auteurs = "responsability_author in('".$this->params["idautorite"]."' ";
				while(($id_aut=pmb_mysql_fetch_object($res_auteurs))) {
					$clause_auteurs .= ", '".$id_aut->aut."' ";
					$rqt_auteursuite = "select author_id as aut from authors where author_see='$id_aut->aut' and author_id!=0 ";
					$res_auteursuite = pmb_mysql_query($rqt_auteursuite);
					while(($id_autsuite=pmb_mysql_fetch_object($res_auteursuite))) $clause_auteurs .= ", '".$id_autsuite->aut."' ";
				}
				$clause_auteurs .= ")" ;

				//on peut lancer la vrai requete maintenant...
				$requete_noti = "SELECT distinct notices.notice_id,niveau_biblio,niveau_hierar FROM notices $acces_j, responsability $statut_j ";
				$requete_noti.= "where $clause_auteurs and notice_id=responsability_notice $statut_r ";
				$requete_noti.= "ORDER BY index_serie,tnvol,index_sew";

				break;
			case "categ_see":
				global $opac_auto_postage_nb_descendant,$opac_auto_postage_nb_montant;
				global $opac_auto_postage_descendant,$opac_auto_postage_montant,$opac_auto_postage_etendre_recherche;

				//auto-postage...
				$nb_level_descendant=$opac_auto_postage_nb_descendant;
				$nb_level_montant=$opac_auto_postage_nb_montant;

				$q = "select path from noeuds where id_noeud = '".$this->params["idautorite"]."' ";
				$r = pmb_mysql_query($q);
				$path=pmb_mysql_result($r, 0, 0);
				$nb_pere=substr_count($path,'/');

				// Si un path est renseigné et le paramètrage activé
				if ($path && ($opac_auto_postage_descendant || $opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){

					//Recherche des fils
					if(($opac_auto_postage_descendant || $opac_auto_postage_etendre_recherche)&& $nb_level_descendant) {
						if($nb_level_descendant != '*' && is_numeric($nb_level_descendant))
							$liste_fils=" path regexp '^$path(\\/[0-9]*){0,$nb_level_descendant}$' ";
						else
							//$liste_fils=" path regexp '^$path(\\/[0-9]*)*' ";
							$liste_fils=" path like '$path/%' or  path = '$path' ";
					} else {
						$liste_fils=" id_noeud='".$this->params["idautorite"]."' ";
					}

					// recherche des pères
					if(($opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && $nb_level_montant ) {

						$id_list_pere=explode('/',$path);
						$stop_pere=0;
						if($nb_level_montant != '*' && is_numeric($nb_level_montant)) $stop_pere = $nb_pere - intval($nb_level_montant);
						if($stop_pere<0) $stop_pere=0;
						for($i=$nb_pere;$i>=$stop_pere; $i--) {
							$liste_pere.= " or id_noeud='".$id_list_pere[$i]."' ";
						}
					}
					$suite_req = " FROM noeuds join notices_categories on id_noeud=num_noeud join notices on notcateg_notice=notice_id  $acces_j $statut_j ";
					$suite_req.= "WHERE ($liste_fils $liste_pere) $statut_r ";

				} else {
					// cas normal d'avant
					//$suite_req=" FROM notices_categories, notices, notice_statut WHERE (notices_categories.num_noeud = '".$id."' and notices_categories.notcateg_notice = notices.notice_id) and (notices.statut = notice_statut.id_notice_statut and ((notice_statut.notice_visible_opac = 1 and notice_statut.notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_statut.notice_visible_opac_abon=1 and notice_statut.notice_visible_opac = 1)":"").")) ";
					$suite_req = " FROM notices_categories join notices on notcateg_notice=notice_id $acces_j $statut_j ";
					$suite_req.= "WHERE num_noeud=".$this->params["idautorite"]." $statut_r ";
				}
				//on a ce qu'il nous faut, on peut lancer la recherche...
				$requete_noti ="SELECT distinct notices.notice_id, notices.niveau_biblio, notices.niveau_hierar $suite_req";
				break;
			case "indexint_see":
				$requete_noti = "SELECT notice_id,niveau_biblio,niveau_hierar FROM notices, notice_statut WHERE indexint='".$this->params["idautorite"]."' and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (notice_visible_opac_abon=1 and notice_visible_opac=1)" : "").")" ;
				break;
			case "coll_see":
				$requete_noti= "SELECT notices.notice_id,niveau_biblio,niveau_hierar FROM notices $acces_j $statut_j WHERE coll_id='".$this->params["idautorite"]."' $statut_r ";
				break;
			case "publisher_see":
				$requete_noti  = "SELECT notice_id,niveau_biblio,niveau_hierar FROM notices $acces_j $statut_j WHERE (ed1_id='".$this->params["idautorite"]."' or ed2_id='".$this->params["idautorite"]."') $statut_r ";
				break;
			case "titre_uniforme_see" :
				$requete_noti = "SELECT notice_id,niveau_biblio,niveau_hierar FROM notices $acces_j $statut_j ,notices_titres_uniformes ";
				$requete_noti.= "WHERE ntu_num_notice=notice_id and ntu_num_tu='".$this->params["idautorite"]."' $statut_r ";
				break;
			case "serie_see":
				$requete_noti  = "SELECT distinct notice_id,niveau_biblio,niveau_hierar FROM notices, notice_statut WHERE tparent_id='".$this->params["idautorite"]."' and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (notice_visible_opac_abon=1 and notice_visible_opac=1)" : "").")" ;
				break;
			case "subcoll_see":
				$requete_noti = "SELECT notice_id,niveau_biblio,niveau_hierar FROM notices $acces_j $statut_j WHERE subcoll_id='".$this->params["idautorite"]."'  $statut_r ";
				break;
			case "perio_bulletin":
				//TODO : droits sur les bulletins et dépouillements
				$requete_bull = "SELECT bulletin_id FROM bulletins WHERE bulletin_notice='".$this->params["idperio"]."'";
				if($this->params['bull_only']!=1){
					//on récupère aussi les articles associés aux bulletins
					$requete_noti ="select analysis_notice as notice_id from analysis join bulletins on analysis_bulletin = bulletin_id AND bulletin_notice='".$this->params["idperio"]."'";
				}
				break;
			case "docnum":
				//cas assez particulier, on va pas rechercher toutes les notices et bulletins pour retrouver les explnum le tout en partant des explnums....
				$requete1 = "select explnum_id,explnum_notice,explnum_bulletin,explnum_nom,explnum_mimetype,explnum_url,explnum_extfichier,explnum_nomfichier,explnum_repertoire,explnum_path, notice_id, ".stripslashes($this->params["pert"])." from explnum, notices $statut_j $acces_j ".stripslashes($this->params["clause"])." ";
				$requete2 = "select explnum_id,explnum_notice,explnum_bulletin,explnum_nom,explnum_mimetype,explnum_url,explnum_extfichier,explnum_nomfichier,explnum_repertoire,explnum_path, notice_id, ".stripslashes($this->params["pert"])." from bulletins, explnum, notices $statut_j $acces_j ".stripslashes($this->params["clause_bull"])." ";
				$requete3 = "select explnum_id,explnum_notice,explnum_bulletin,explnum_nom,explnum_mimetype,explnum_url,explnum_extfichier,explnum_nomfichier,explnum_repertoire,explnum_path, notice_id, ".stripslashes($this->params["pert"])." from bulletins, explnum, notices $statut_j $acces_j ".stripslashes($this->params["clause_bull_num_notice"])." ";
				$requete_explnum = "select explnum_id,explnum_notice,explnum_bulletin,explnum_nom,explnum_mimetype,explnum_url,explnum_extfichier,explnum_nomfichier,explnum_repertoire,explnum_path from ($requete1 UNION $requete2 UNION $requete3) as uni join notices n on uni.notice_id=n.notice_id  ".stripslashes($this->params["tri"]);
				break;
			case "scan_request":
				$requete_base = "SELECT explnum_id, scan_request_explnum_num_notice as explnum_notice, scan_request_explnum_num_bulletin as explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut, explnum_repertoire, explnum_path
					FROM explnum
					JOIN scan_request_explnum ON scan_request_explnum.scan_request_explnum_num_explnum = explnum.explnum_id";

				$requete1 = $requete_base." JOIN bulletins ON bulletins.bulletin_id = explnum_bulletin AND explnum_bulletin <> 0";
				$requete1.=  " WHERE scan_request_explnum_num_request = '".$this->params['id']."'";

				$requete2 = $requete_base." JOIN notices ON notices.notice_id = explnum_notice AND explnum_notice <> 0";
				$requete2.= " ".$statut_j." ".$acces_j;
				$requete2.=  " WHERE scan_request_explnum_num_request = '".$this->params['id']."'";
				$requete2.= $statut_r;

				$requete_explnum = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut, explnum_repertoire, explnum_path from (".$requete1." union ".$requete2.") as uni";
				break;
			case "segment" :
			    $searcher = new search_segment_searcher_extended(stripslashes($this->params['serialized_search']));
			    //On récupère depuis l'historique la recherche dans l'univers
			    $tmp = unserialize(stripslashes($this->params['serialized_search']));
			    if(!empty($tmp[1]['FIELD'][0])){
			        $this->params['user_query'] = $tmp[1]['FIELD'][0];
			    }
			    //Mais ca dépanne sur Documation
			    break;
			case "ai_search" :
				$list_notices = $_SESSION['ai_search_result'][$this->params["id"]] ?? [];
			    $requete_noti = "SELECT notice_id, niveau_biblio, niveau_hierar FROM notices $acces_j $statut_j ";
			    $requete_noti.= "WHERE notice_id in (".implode(",", $list_notices).") $statut_r ";
			    break;
			case "concept_see" :
			    // ici : idautorite correspond à l'identifant du concept et non à l'id d'autorité
			    $concept = new concept($this->params["idautorite"]);
			    $list_notices = $concept->get_indexed_notices();
			    $requete_noti = "SELECT notice_id, niveau_biblio, niveau_hierar FROM notices $acces_j $statut_j ";
			    $requete_noti.= "WHERE notice_id in (".implode(",", $list_notices).") $statut_r ";
			    break;
			default :
				//on ne peut avoir que l'id de l'exemplaire
				$requete_noti = "select explnum_notice as notice_id from explnum where explnum_notice != 0 and explnum_id = ".$this->params["explnum_id"];
				$requete_bull = "select explnum_bulletin as bulletin_id from explnum where explnum_bulletin != 0 and explnum_id = ".$this->params["explnum_id"];
				break;

		}

		//avec le nouveau mode de recherche on utilise un nouveau mode de visionneuse...
		if(is_object($searcher)){
			if(isset($_SESSION["last_sortnotices"]) && $_SESSION["last_sortnotices"]!==""){
				$explnums = $searcher->get_explnums($_SESSION["last_sortnotices"]);
			}else{
				$explnums = $searcher->get_explnums("default");
			}
			$this->listeDocs = array();
			if(count($explnums)){
				for ($i=0 ; $i<count($explnums) ; $i++){
					$rqt = " select explnum_id,explnum_notice,explnum_bulletin,explnum_nom,explnum_mimetype,explnum_url,explnum_extfichier,explnum_nomfichier,explnum_repertoire,explnum_path from explnum where explnum_id =".$explnums[$i]." and explnum_mimetype in (".$opac_photo_filtre_mimetype.")";
					$res = pmb_mysql_query($rqt);
					if(pmb_mysql_num_rows($res)){
						while($row = pmb_mysql_fetch_object($res)){
							$this->listeDocs[] = $row;
						}
					}
				}
			}
			$this->checkCurrentExplnumId();
		}else{
			if ($requete_explnum != ""){
				$res_explnum = pmb_mysql_query($requete_explnum);
				$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
				while(($expl = pmb_mysql_fetch_object($res_explnum))){
					if($expl->explnum_mimetype && (in_array($expl->explnum_mimetype,$allowed_mimetype))){//Si le mimetype du document n'est pas connu il ne peut pas être affiché dans la visionneuse.
						$this->listeDocs[] = $expl;
					}
				}
				$this->current = 0;
				$this->checkCurrentExplnumId();

			}else{
				if($requete_noti){
					$res_notice = pmb_mysql_query($requete_noti);
					if(pmb_mysql_num_rows($res_notice)){
						while(($not_ids = pmb_mysql_fetch_object($res_notice))){
							//cas d'une notice de bulletin, le docnum peut etre rattaché au bulletin
							//donc on va le chercher et le rajoute à la liste...
						    if(isset($not_ids->niveau_biblio) && $not_ids->niveau_biblio == "b" && isset($not_ids->niveau_hierar) && $not_ids->niveau_hierar == "2"){
								$req = "select bulletin_id from bulletins where num_notice = ".$not_ids->notice_id." LIMIT 1";
								$res_notibull = pmb_mysql_query($req);
								if(pmb_mysql_num_rows($res_notibull))
									$this->listeBulls[] = pmb_mysql_result($res_notibull,0,0);
							}else{
								$this->listeNotices[] = $not_ids->notice_id;
							}
						}
					}
				}

				if($requete_bull){
					$res_bull = pmb_mysql_query($requete_bull);
					if(pmb_mysql_num_rows($res_bull)){
						while(($bull_ids = pmb_mysql_fetch_object($res_bull))){
							$this->listeBulls[]= $bull_ids->bulletin_id;
						}
					}
				}

				if($this->listeNotices || $this->listeBulls)
					$this->getExplnums();
			}
 		}
  	}

 	//recupére les documents numériques associés
 	public function getExplnums($id=0){
		global $opac_photo_filtre_mimetype; //filtre des mimetypes
		global $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;
		global $opac_explnum_order,$opac_show_links_invisible_docnums;

		if(empty($this->listeDocs)){
			$requete = "select explnum_id,explnum_notice,explnum_bulletin,explnum_nom,explnum_mimetype,explnum_url,explnum_extfichier,explnum_nomfichier,explnum_repertoire,explnum_path from explnum ";
			if($id !=0){
			    $id = intval($id);
				$requete .= "where explnum_id = $id";
				$this->current = 0;
			}else {
				if(is_countable($this->listeNotices) && sizeof($this->listeNotices) && empty($this->listeBulls)){
					$requete .= "where (explnum_notice in ('".implode("','",$this->listeNotices)."') and explnum_bulletin = 0 ) ";
				}else if(is_countable($this->listeBulls) && sizeof($this->listeBulls) && empty($this->listeNotices)){
					$requete .= "where (explnum_bulletin in ('".implode("','",$this->listeBulls)."') and explnum_notice = 0)";
				}else {
					$requete .= "where ((explnum_notice in ('".implode("','",$this->listeNotices)."') and explnum_bulletin = 0) or (explnum_bulletin in ('".implode("','",$this->listeBulls)."') and explnum_notice = 0))";
				}
			}
			$requete .= " and explnum_mimetype in ($opac_photo_filtre_mimetype)";
			if($opac_explnum_order){
				$requete.=" order by ".$opac_explnum_order;
			}else{
				$requete .= " order by explnum_mimetype, explnum_nom, explnum_id ";
			}
			$res = pmb_mysql_query($requete);
			if($res && pmb_mysql_num_rows($res)){
				while(($expl = pmb_mysql_fetch_object($res))){
					$docnum_visible = true;
					if ($expl->explnum_notice) {
						$id_for_right = $expl->explnum_notice;
					} else {
						if($expl->explnum_bulletin){
							$query = "select num_notice,bulletin_notice from bulletins where bulletin_id = ".$expl->explnum_bulletin;
							$result = pmb_mysql_query($query);
							if(pmb_mysql_num_rows($result)){
								$infos = pmb_mysql_fetch_object($result);
								if($infos->num_notice){
									$id_for_right = $infos->num_notice;
								}else{
									$id_for_right = $infos->bulletin_notice;
								}
							}
						}
					}
					if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
						$ac= new acces();
						$dom_2= $ac->setDomain(2);
						$docnum_visible = $dom_2->getRights($_SESSION['id_empr_session'],$id_for_right,16);
					} else {
						$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$id_for_right."' and id_notice_statut=statut ";
						$myQuery = pmb_mysql_query($requete);
						if(pmb_mysql_num_rows($myQuery)) {
							$statut_temp = pmb_mysql_fetch_object($myQuery);
							if(!$statut_temp->explnum_visible_opac)	$docnum_visible=false;
							if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session']) $docnum_visible=false;
						} else $docnum_visible=false;
					}
					if ($docnum_visible || $opac_show_links_invisible_docnums) {
						$explnum_docnum_visible = true;
						if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
							$ac= new acces();
							$dom_3= $ac->setDomain(3);
							$explnum_docnum_visible = $dom_3->getRights($_SESSION['id_empr_session'],$expl->explnum_id,16);
						} else {
							$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM explnum, explnum_statut WHERE explnum_id ='".$expl->explnum_id."' and id_explnum_statut=explnum_docnum_statut ";
							$myQuery = pmb_mysql_query($requete);
							if(pmb_mysql_num_rows($myQuery)) {
								$statut_temp = pmb_mysql_fetch_object($myQuery);
								if(!$statut_temp->explnum_visible_opac)	{
									$explnum_docnum_visible=false;
								}
								if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$explnum_docnum_visible=false;
							} else {
								$explnum_docnum_visible=false;
							}
						}
						if ($explnum_docnum_visible ||  $opac_show_links_invisible_docnums) {
							$this->listeDocs[] = $expl;
						}
					}
				}
			}
			$this->checkCurrentExplnumId();
		}
	}

	public function checkCurrentExplnumId(){
		if($this->params["explnum_id"] != 0 && $this->params["start"] && is_countable($this->listeDocs)) {
			for ($i=0;$i<sizeof($this->listeDocs);$i++){
				if($this->params["explnum_id"] == $this->listeDocs[$i]->explnum_id){
					$this->current = $i;
					break;
				}
			}
		}else $this->current = $this->params["position"];
	}

	public function getCurrentDoc(){
	    if(empty($this->listeDocs[$this->current])) {
	       return array();
	    }
		$this->currentDoc = array();
		//on peut récup déjà un certain nombre d'infos...
		$this->currentDoc["id"] = $this->listeDocs[$this->current]->explnum_id;
		$this->params["explnum_id"] = $this->listeDocs[$this->current]->explnum_id;
		$this->currentDoc["titre"] = $this->listeDocs[$this->current]->explnum_nom;
		$nbSearchInExplnum = 0;
		if(!empty($this->listeDocs[$this->current]->explnum_id)) {
		    $req_expl = "select explnum_id from explnum ";
		    $req_expl.= "where explnum_id = ".$this->listeDocs[$this->current]->explnum_id." and ";
		    $terms = explode(" ",$this->params["user_query"]);
		    if(is_countable($terms) && sizeof($terms)){
		        $req_expl.="(";
		        $search = '';
		        for ($i=0 ; $i<sizeof($terms) ; $i++){
		            if( $search != "") $search .= " or ";
		            $search .= "explnum_index_sew LIKE '%".$terms[$i]."%'";
		        }
		        if( $search != "") $req_expl .= $search;
		        $req_expl.=")";
		    }
		    $searchInExplnum = pmb_mysql_query($req_expl);
		    $nbSearchInExplnum = pmb_mysql_num_rows($searchInExplnum);
		}
		if($nbSearchInExplnum == 0){
			$this->currentDoc["searchterms"]  ="";
		}else{
			$this->currentDoc["searchterms"]  =$this->params["user_query"];
		}

		//on récupère le chemin
		if($this->listeDocs[$this->current]->explnum_url != ""){
			//c'est une url
			$this->currentDoc["path"] = $this->listeDocs[$this->current]->explnum_url ;
		}elseif($this->listeDocs[$this->current]->explnum_repertoire != 0){
			//il est en répertoire d'upload
			$rep = new upload_folder($this->listeDocs[$this->current]->explnum_repertoire);
			$this->currentDoc["path"] = $rep->repertoire_path."/".$this->listeDocs[$this->current]->explnum_nomfichier;
			$this->currentDoc["path"] = str_replace("//", "/", $rep->repertoire_path . $this->listeDocs[$this->current]->explnum_path . $this->listeDocs[$this->current]->explnum_nomfichier);
			$this->currentDoc["path"] = $rep->encoder_chaine($this->currentDoc["path"]);
		}else{
			//il est en base
		    $this->setInCache($this->listeDocs[$this->current]->explnum_id, $this->openCurrentDoc());
			$this->currentDoc["path"] = "";
		}

		//dans le cadre d'une URL, on doit récup le mimetype...
		if ($this->listeDocs[$this->current]->explnum_url){

			$src = fopen( $this->listeDocs[$this->current]->explnum_url, 'r');
			if ($src) {
				$meta = stream_get_meta_data($src);
				foreach($meta['wrapper_data'] as $header) {
					$data = explode(':', $header);
					if(trim(strtolower($data[0])) == 'content-type') {
						$this->currentDoc["mimetype"] = trim($data[1]);
						break;
					}
				}
			}
		}else{
		//sinon il a déjà été détecté et est présent en base...
			$this->currentDoc["mimetype"] =$this->listeDocs[$this->current]->explnum_mimetype;
		}

		//pour la conversion on y ajoute l'extension
		$ext='';
		$ext=$this->listeDocs[$this->current]->explnum_extfichier;
		if (!$ext && $this->listeDocs[$this->current]->explnum_nomfichier) $ext=substr($this->listeDocs[$this->current]->explnum_nomfichier,strrpos($this->listeDocs[$this->current]->explnum_nomfichier,'.')*1+1);
		if (!$ext && $this->listeDocs[$this->current]->explnum_url) $ext=substr($this->listeDocs[$this->current]->explnum_url,strrpos($this->listeDocs[$this->current]->explnum_url,'.')*1+1);
		$this->currentDoc['extension'] = $ext;

		if(empty($this->params['nodesc'])){
			//on récup la notice associée...
		    if($this->listeDocs[$this->current]->explnum_notice) {
		        $customDisplay = $this->getCustomRecordDisplay(intval($this->listeDocs[$this->current]->explnum_notice));
               if(!empty($customDisplay)) {
                   $this->currentDoc["desc"] = $customDisplay;
               } else {
	               $this->currentDoc["desc"] = aff_notice($this->listeDocs[$this->current]->explnum_notice, 1, 1, 0, "", 0, 1);
               }
               
		    } else {
                $this->currentDoc["desc"] = bulletin_affichage($this->listeDocs[$this->current]->explnum_bulletin, "visionneuse");
		    }
				
			$lop = array();
			preg_match_all("/(<a href=[\"'][^#][^>]*>)(.*?)<\/a>/",$this->currentDoc["desc"],$lop);
			for ($i = 0 ; $i <sizeof($lop[0]) ; $i++){
				$plop = explode ($lop[0][$i],$this->currentDoc["desc"]);
				$this->currentDoc["desc"] = implode($lop[2][$i],$plop);
			}
		}
		return $this->currentDoc;
	}
	
	public function getCustomRecordDisplay(int $id_record = 0)
	{
	    global $base_path, $opac_notices_format_django_directory;
	    
	    if ($id_record === 0) {
	        return "";
	    }
	    
	    $django_directory = !empty($opac_notices_format_django_directory) ? $opac_notices_format_django_directory : "common";
	    $template_path = "$base_path/includes/templates/record/$django_directory/record_in_visionneuse_display.tpl.html";
	    if (file_exists($template_path)) {
	        return record_display::render($id_record, $template_path);
	    }
	    
	    $common_template_path = "$base_path/includes/templates/record/common/record_in_visionneuse_display.tpl.html";
	    if (file_exists($common_template_path)) {
	        return record_display::render($id_record, $common_template_path);
	    }
	    
	    return "";
	}

	public function getCurrentBiblioInfos(){
		global $msg;

		$current = $this->listeDocs[$this->current]->explnum_id;
		if(!isset($this->biblioInfos[$current])){
			$query = "select explnum_notice,explnum_bulletin from explnum where explnum_id = ".$current;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				if($row->explnum_notice){
					$query = "select notice_id, tit1, year from notices where notice_id = ".$row->explnum_notice;
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$this->biblioInfos[$current]['title']['value'] = $row->tit1;
						$this->biblioInfos[$current]['date']['value'] = $row->year;
						$this->biblioInfos[$current]['permalink']['value'] = "./index.php?lvl=notice_display&id=".$row->notice_id;
						$aut_query = "select responsability_author from responsability where responsability_notice = ".$row->notice_id." order by responsability_type asc, responsability_ordre asc limit 1";
					}
				}else{
					$query = "select bulletin_id, bulletin_titre,mention_date,date_date,notices.tit1,perio.tit1 as perio_title, notices.notice_id, perio.notice_id as serial_id from bulletins join notices as perio on bulletin_notice = perio.notice_id left join notices on num_notice = notices.notice_id where bulletin_id = ".$row->explnum_bulletin;
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$titre = $row->tit1;
						if(!$titre) $titre = $row->bulletin_titre;
						$this->biblioInfos[$current]['title']['value'] = $row->perio_title.", ".$titre;
						$this->biblioInfos[$current]['date']['value'] = ($row->mention_date ? $row->mention_date : format_date($row->date_date));
						$this->biblioInfos[$current]['permalink']['value'] = "./index.php?lvl=bulletin_display&id=".$row->bulletin_id;
						$aut_query = "select responsability_author from responsability where responsability_notice = ".($row->notice_id ? $row->notice_id:$row->serial_id)." order by responsability_type asc, responsability_ordre asc limit 1";
					}
				}
				$result = pmb_mysql_query($aut_query);
				if(pmb_mysql_num_rows($result)){
					$author_id = pmb_mysql_result($result,0,0);
					$author= new auteur($author_id);
					$this->biblioInfos[$current]['author']['value'] =$author->get_isbd();
				}
				$this->biblioInfos[$current]['title']['label'] = $msg['title'];
				$this->biblioInfos[$current]['date']['label'] = $msg['serialcirc_ask_date'];
				$this->biblioInfos[$current]['permalink']['label'] = $msg['location_more_info'];
				$this->biblioInfos[$current]['author']['label'] = $msg['author_search'];
				$this->biblioInfos[$current]['explnum_licence'] = explnum_licence::get_explnum_licence_tooltip($current);
			}

		}
		return $this->biblioInfos[$current];
	}

/*******************************************************************
 *  Renvoie le contenu du document brut et gère le cache si besoin  *
 ******************************************************************/
	public function openCurrentDoc(){
		//s'il est en cache, c'est vachement simple
		if($this->isInCache($this->listeDocs[$this->current]->explnum_id)){
			$document = $this->readInCache($this->listeDocs[$this->current]->explnum_id);
		//sinon on va devoir regarder un peu ou ca se passe...
		}elseif($this->listeDocs[$this->current]->explnum_url != ""){
			//on est sur une URL
			$document = file_get_contents($this->listeDocs[$this->current]->explnum_url);
			//on met les documents issues d'une URL en cache, ca évite les problèmes de connexion plus tard...
			$this->setInCache($this->listeDocs[$this->current]->explnum_id,$document);
		}elseif($this->listeDocs[$this->current]->explnum_repertoire != 0){
			//le document est stocké dans un répertoire d'upload
			$rep = new upload_folder($this->listeDocs[$this->current]->explnum_repertoire);
			$filepath = $rep->encoder_chaine(str_replace("//","/",$rep->repertoire_path.$this->listeDocs[$this->current]->explnum_path."/".$this->listeDocs[$this->current]->explnum_nomfichier));
			$document = file_get_contents($filepath);
		}else{
			$requete ="SELECT explnum_data FROM explnum WHERE explnum_id = ".$this->listeDocs[$this->current]->explnum_id;
			$res = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res))
				$document = pmb_mysql_result($res,0,0);
		}
		//on renvoie le contenu du document
		return $document;
	}

	public function is_allowed($explnum_id){
		$docnum_visible = true;
		global $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;

		$query = "select explnum_notice,explnum_bulletin from explnum where explnum_id = ".$explnum_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$infos = pmb_mysql_fetch_object($result);
			if($infos->explnum_notice != 0){
				$id_for_right = $infos->explnum_notice;
			}else {
				$query = "select num_notice,bulletin_notice from bulletins where bulletin_id = ".$infos->explnum_bulletin;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$infos = pmb_mysql_fetch_object($result);
					if($infos->num_notice){
						$id_for_right = $infos->num_notice;
					}else{
						$id_for_right = $infos->bulletin_notice;
					}
				}
			}

		}

		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$docnum_visible = $dom_2->getRights($_SESSION['id_empr_session'],$id_for_right,16);
		} else {
			$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$id_for_right."' and id_notice_statut=statut ";
			$myQuery = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($myQuery)) {
				$statut_temp = pmb_mysql_fetch_object($myQuery);
				if(!$statut_temp->explnum_visible_opac){
					$docnum_visible=false;
				}
				if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session']){
					$docnum_visible=false;
				}
			} else {
				$docnum_visible=false;
			}
		}

		//la notice autorise l'accès au document, on vérifie que le document lui-même est accessible
		if($docnum_visible){
			if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
				$ac= new acces();
				$dom_3= $ac->setDomain(3);
				$docnum_visible = $dom_3->getRights($_SESSION['id_empr_session'],$explnum_id,4);
			}else{
				$query ="select explnum_id from explnum join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_id = ".$explnum_id." and explnum_consult_opac=1 ".(!$_SESSION['id_empr_session'] ? " and explnum_consult_opac_abon = 0":"");
				$result = pmb_mysql_query($query);
				if(!pmb_mysql_num_rows($result)){
					$docnum_visible=false;
				}
			}
		}
		return $docnum_visible;
	}

	public function forbidden_callback(){
		global $opac_show_links_invisible_docnums;

		$display ="";
		if(!$_SESSION['user_code'] && $opac_show_links_invisible_docnums){
			$auth_popup = new auth_popup();
			$display.= "
			<script>
				auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=pmb_visionneuse_refresh');
				function pmb_visionneuse_refresh(){
					window.location.reload();
				}
			</script>";
		}
		return $display;
	}

	public function getBnfClass($mimetype){
		global $base_path,$class_path,$include_path;

		switch($mimetype){
			case "application/bnf" :
				require_once($class_path."/docbnf.class.php");
				$classname = "docbnf";
				break;
			case "application/bnf+zip" :
				require_once($class_path."/docbnf_zip.class.php");
				$classname = "docbnf_zip";
				break;
		}

		return $classname;
	}

	public function getVisionneuseUrl($params){
		global $base_path;

		$url = $base_path."/visionneuse.php";
		if($params){
			$url.= "?".$params;
		}
		return $url;
	}

	public function getDocumentUrl($id){
		global $opac_url_base;
		$evth = events_handler::get_instance();
		$evt = new event_explnum('visionneuse', 'get_url');
		$evt->set_explnum(new explnum($id));
		$evth->send($evt);
		$url = $evt->getIntegrationUrl();
		if($url != ''){
		    return $url;
		}
		return $opac_url_base."doc_num_data.php?explnum_id=".$id;
	}

	public function is_downloadable($explnum_id){
		$docnum_downloadable = true;
		global $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;

		$query = "select explnum_notice,explnum_bulletin from explnum where explnum_id = ".$explnum_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$infos = pmb_mysql_fetch_object($result);
			if($infos->explnum_notice != 0){
				$id_for_right = $infos->explnum_notice;
			}else {
				$query = "select num_notice,bulletin_notice from bulletins where bulletin_id = ".$infos->explnum_bulletin;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$infos = pmb_mysql_fetch_object($result);
					if($infos->num_notice){
						$id_for_right = $infos->num_notice;
					}else{
						$id_for_right = $infos->bulletin_notice;
					}
				}
			}

		}

		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$docnum_downloadable = $dom_2->getRights($_SESSION['id_empr_session'],$id_for_right,16);
		} else {
			$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$id_for_right."' and id_notice_statut=statut ";
			$myQuery = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($myQuery)) {
				$statut_temp = pmb_mysql_fetch_object($myQuery);
				if(!$statut_temp->explnum_visible_opac){
					$docnum_downloadable=false;
				}
				if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session']){
					$docnum_downloadable=false;
				}
			} else {
				$docnum_downloadable=false;
			}
		}

		//la notice autorise l'accès au document, on vérifie que le document lui-même est accessible
		if($docnum_downloadable){
			if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
				$ac= new acces();
				$dom_3= $ac->setDomain(3);
				$docnum_downloadable = $dom_3->getRights($_SESSION['id_empr_session'],$explnum_id,8);
			}else{
				$query ="select explnum_id from explnum join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_id = ".$explnum_id." and explnum_download_opac=1 ".(!$_SESSION['id_empr_session'] ? " and explnum_download_opac_abon = 0":"");
				$result = pmb_mysql_query($query);
				if(!pmb_mysql_num_rows($result)){
					$docnum_downloadable=false;
				}
			}
		}
		return $docnum_downloadable;
	}
}
