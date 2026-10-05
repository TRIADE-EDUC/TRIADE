<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.10.2.1 2025/01/16 11:03:35 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion de la recherche spécial "combine"

class navigation_section_search {
	public $id;
	public $n_ligne;
	public $params;
	public $search;

	//Constructeur
    public function __construct($id,$n_ligne,$params,&$search) {
    	$this->id=$id;
    	$this->n_ligne=$n_ligne;
    	$this->params=$params;
    	$this->search=&$search;
    }
    
    //fonction de récupération des opérateurs disponibles pour ce champ spécial (renvoie un tableau d'opérateurs)
    public function get_op() {
    }
    
    //fonction de récupération de l'affichage de la saisie du critère
    public function get_input_box() {
    }
    
    //fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
    public function transform_input() {
    }
    
    //fonction de création de la requête (retourne une table temporaire)
    public function make_search() {
    	global $msg,$gestion_acces_active,$gestion_acces_empr_notice,$class_path;
    	global $default_tmp_storage_engine;

    	
    	$id=intval($_SESSION['last_module_search']['search_id']);
    	$location=intval($_SESSION['last_module_search']['search_location']);
    	$plettreaut=$_SESSION["last_module_search"]["search_plettreaut"];
    	$dcote=$_SESSION["last_module_search"]["search_dcote"];
    	$lcote=$_SESSION["last_module_search"]["search_lcote"];
    	$nc=$_SESSION["last_module_search"]["search_nc"];
    	$ssub=$_SESSION["last_module_search"]["search_ssub"];
    	/*
    	 * récupérer les infos de session
    	 */
    	
    	$requete="SELECT num_pclass FROM docsloc_section WHERE num_location='".$location."' AND num_section='".$id."' ";
    	$res=pmb_mysql_query($requete);
    	$type_aff_navigopac=0;
    	if(pmb_mysql_num_rows($res)){
    		$type_aff_navigopac=pmb_mysql_result($res,0,0);
    	}

    	//droits d'acces emprunteur/notice
    	$acces_j='';
    	if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
    		require_once("$class_path/acces.class.php");
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
    	if($_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
    		$opac_view_restrict=" notice_id in (select opac_view_num_notice from  opac_view_notices_".intval($_SESSION["opac_view"]).") ";
    		$statut_r.=" and ".$opac_view_restrict;
    	}
    	if($type_aff_navigopac == 0){//Pas de navigation
    		//On récupère les notices de monographie avec au moins un exemplaire dans la localisation et la section
			$requete="create temporary table temp_n_id ENGINE={$default_tmp_storage_engine} ( SELECT notice_id FROM notices ".$acces_j." JOIN exemplaires ON expl_section='".$id."' and expl_location='".$location."' and expl_notice=notice_id ".$statut_j." WHERE 1 ".$statut_r." GROUP BY notice_id)";
			pmb_mysql_query($requete);
			//On récupère les notices de périodique avec au moins un exemplaire d'un bulletin dans la localisation et la section
			$requete="INSERT INTO temp_n_id (SELECT notice_id FROM exemplaires JOIN bulletins ON expl_section='".$id."' and expl_location='".$location."' and expl_bulletin=bulletin_id JOIN notices ON notice_id=bulletin_notice ".$acces_j." ".$statut_j." WHERE 1 ".$statut_r." GROUP BY notice_id)";
			pmb_mysql_query($requete);
			pmb_mysql_query("alter table temp_n_id add index(notice_id)");
			$requeteSource = "SELECT notices.notice_id FROM temp_n_id JOIN notices ON notices.notice_id=temp_n_id.notice_id GROUP BY notices.notice_id";
    	}elseif($type_aff_navigopac == -1){//Navigation par auteurs
    		$requete="create temporary table temp_n_id ENGINE={$default_tmp_storage_engine} ( SELECT notice_id FROM notices ".$acces_j." JOIN exemplaires ON expl_section='".$id."' and expl_location='".$location."' and expl_notice=notice_id ".$statut_j." WHERE 1 ".$statut_r." GROUP BY notice_id)";
    		pmb_mysql_query($requete);
    		//On récupère les notices de périodique avec au moins un exemplaire d'un bulletin dans la localisation et la section
    		$requete="INSERT INTO temp_n_id (SELECT notice_id FROM exemplaires JOIN bulletins ON expl_section='".$id."' and expl_location='".$location."' and expl_bulletin=bulletin_id JOIN notices ON notice_id=bulletin_notice ".$acces_j." ".$statut_j." WHERE 1 ".$statut_r." GROUP BY notice_id)";
    		pmb_mysql_query($requete);
    		pmb_mysql_query("alter table temp_n_id add index(notice_id)");
    		//On sait par quoi doit commencer le nom de l'auteur
			if($plettreaut == "num"){
				$requeteSource = "SELECT notices.notice_id FROM temp_n_id JOIN responsability ON responsability_notice=temp_n_id.notice_id JOIN authors ON author_id=responsability_author and trim(index_author) REGEXP '^[0-9]' JOIN notices ON notices.notice_id=temp_n_id.notice_id GROUP BY notices.notice_id";
			}elseif($plettreaut == "vide"){
    			$requeteSource = "SELECT notices.notice_id FROM temp_n_id LEFT JOIN responsability ON responsability_notice=temp_n_id.notice_id LEFT JOIN notices ON notices.notice_id=temp_n_id.notice_id WHERE responsability_author IS NULL GROUP BY notices.notice_id";
			}elseif((@preg_match($plettreaut, null) !== false)){
    			$requeteSource = "SELECT notices.notice_id FROM temp_n_id JOIN responsability ON responsability_notice=temp_n_id.notice_id JOIN authors ON author_id=responsability_author and trim(index_author) REGEXP '^[".$plettreaut."]' JOIN notices ON notices.notice_id=temp_n_id.notice_id GROUP BY notices.notice_id";
    		}else{
    			$requeteSource = "SELECT notices.notice_id FROM temp_n_id JOIN notices ON notices.notice_id=temp_n_id.notice_id GROUP BY notices.notice_id";
    		}
    	}else{//Navigation par un plan de classement
    		if ($ssub) {
    			$t_expl_cote_cond = array();
    			$t_dcote = explode(",", $dcote);
    			for ($i=0; $i<count($t_dcote); $i++) {
    				$t_expl_cote_cond[]="expl_cote regexp '(^".$t_dcote[$i]." )|(^".$t_dcote[$i]."[0-9])|(^".$t_dcote[$i]."$)|(^".$t_dcote[$i].".)'";
    			}
    			$expl_cote_cond="(".implode(" or ",$t_expl_cote_cond).")";
    		}
    	
    		if (!$ssub) {
    			$requete = "SELECT COUNT(distinct notice_id) FROM notices $acces_j ,exemplaires $statut_j ";
    			$requete.= "where expl_location=$location and expl_section=$id and notice_id=expl_notice ";
    			if (strlen($dcote)) {
    				$requete.= "and expl_cote regexp '".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote))."' and expl_cote not regexp '(\\\\.[0-9]*".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")|([^0-9]*[0-9]+\\\\.?[0-9]*.+".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")' ";
    			}
    			$requete.= $statut_r;
    			$res = pmb_mysql_query($requete);
    			$nbr_lignes = pmb_mysql_result($res, 0, 0);

    			$requete2 = "SELECT COUNT(distinct notice_id) FROM notices $acces_j ,exemplaires, bulletins $statut_j ";
    			$requete2.= "where  expl_location=$location and expl_section=$id and notice_id=bulletin_notice and expl_bulletin=bulletin_id ";
    			if (strlen($dcote)) {
    				$requete2.= "and expl_cote regexp '".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote))."' and expl_cote not regexp '(\\\\.[0-9]*".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")|([^0-9]*[0-9]+\\\\.?[0-9]*.+".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")' ";
    			}
    			$requete2.= $statut_r;
    			$res = pmb_mysql_query($requete2);
    			$nbr_lignes += pmb_mysql_result($res, 0, 0);
    	
    		} else {
    			$requete = "select COUNT(distinct notice_id) FROM notices $acces_j ,exemplaires $statut_j ";
    			$requete.= "where expl_location=$location and expl_section=$id and notice_id=expl_notice ";
    			if (strlen($dcote)) {
    				$requete.= " and $expl_cote_cond ";
    			}
    			$requete.= $statut_r;
    			$res = pmb_mysql_query($requete);
    			$nbr_lignes = pmb_mysql_result($res, 0, 0);

    			$requete2 = "SELECT COUNT(distinct notice_id) FROM notices $acces_j ,exemplaires, bulletins $statut_j ";
    			$requete2.= "where  expl_location=$location and expl_section=$id and notice_id=bulletin_notice and expl_bulletin=bulletin_id ";
    			if (strlen($dcote)) {
    				$requete2.= "and $expl_cote_cond ";
    			}
    			$requete2.= $statut_r;
    			$res = pmb_mysql_query($requete2);
    			$nbr_lignes += pmb_mysql_result($res, 0, 0);
   			}
    	
    		if($nbr_lignes) {
    			//Table temporaire de tous les id
    			$requete = "create temporary table temp_n_id ENGINE={$default_tmp_storage_engine} (select notice_id, expl_id FROM notices $acces_j ,exemplaires $statut_j ";
    			$requete.= "WHERE expl_location=$location and expl_section=$id and notice_id=expl_notice ";
    			if (strlen($dcote)) {
    				if (!$ssub) {
    					$requete.= "and expl_cote regexp '".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote))."' and expl_cote not regexp '(\\\\.[0-9]*".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")|([^0-9]*[0-9]+\\\\.?[0-9]*.+".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")' ";
    					$level_ref=strlen($dcote)+1;
    				} else {
    					$requete.= "and $expl_cote_cond ";
    				}
    			}
    			$requete.= "$statut_r ";
    			$requete.= "group by notice_id, expl_id) ";
    			pmb_mysql_query($requete);
    	
    			$requete2 = "insert into temp_n_id (SELECT notice_id, expl_id FROM notices $acces_j ,exemplaires, bulletins $statut_j ";
    			$requete2.= "where  expl_location=$location and expl_section=$id and notice_id=bulletin_notice and expl_bulletin=bulletin_id ";
    			if (strlen($dcote)) {
    				if (!$ssub) {
    					$requete2.= "and expl_cote regexp '".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote))."' and expl_cote not regexp '(\\\\.[0-9]*".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")|([^0-9]*[0-9]+\\\\.?[0-9]*.+".$dcote.str_repeat("[0-9]",$lcote-strlen($dcote)).")' ";
    				} else {
    					$requete2.= "and $expl_cote_cond ";
    				}
    			}
    			$requete2.= "$statut_r ";
    			$requete2.= "group by notice_id, expl_id) ";
    			pmb_mysql_query($requete2);
    			pmb_mysql_query("alter table temp_n_id add index(notice_id, expl_id)");
    			//Calcul du classement
    			if (!$ssub) {
    				$rq1_index="create temporary table union1 ENGINE={$default_tmp_storage_engine} (select distinct expl_cote from exemplaires, temp_n_id where expl_location='".$location."' and expl_section='".$id."' and expl_notice=temp_n_id.notice_id) ";
    				pmb_mysql_query($rq1_index);
    				$rq2_index="create temporary table union2 ENGINE={$default_tmp_storage_engine} (select distinct expl_cote from exemplaires join (select distinct bulletin_id from bulletins join temp_n_id where bulletin_notice=notice_id) as sub on (bulletin_id=expl_bulletin) where expl_location='".$location."' and expl_section='".$id."') ";
    				pmb_mysql_query($rq2_index);
    				$req_index="select distinct expl_cote from union1 union select distinct expl_cote from union2";
    				$res_index=pmb_mysql_query($req_index);
    	
    				if ($level_ref==0) $level_ref=1;
    	
    				// Prepare indexint pre selection - Zend
    				$zendIndexInt = array();
    				//$zendIndexIntCache = array();
    				$zendQ1 = "SELECT indexint_name, indexint_comment FROM indexint WHERE indexint_name NOT REGEXP '^[0-9][0-9][0-9]' AND indexint_comment != '' AND num_pclass='".$type_aff_navigopac."'";
    				$zendRes = pmb_mysql_query($zendQ1);
    				while ($zendRow = pmb_mysql_fetch_assoc($zendRes)) {
    					$zendIndexInt[$zendRow['indexint_name']] = $zendRow['indexint_comment'];
    				}
    				// Zend
    				while ($ct=pmb_mysql_fetch_object($res_index)) {
    					//Je regarde si le début existe dans indexint
    					$lf=5;
    					$t=array();
    					while ($lf>0) {
    						$zendKey = substr($ct->expl_cote, 0, $lf);
    						if ($zendIndexInt[$zendKey]) {
    							if (!$nc) {
    								$t["comment"]=$zendIndexInt[$zendKey];
    								$t["dcote"]=$zendKey;
    								$t["ssub"]=1;
    								$index[$t["dcote"]]=$t;
    								break;
    							} else {
    								$rq_del="select distinct notice_id, expl_id from notices, exemplaires where expl_cote='".$ct->expl_cote."' and expl_notice=notice_id ";
    								$rq_del.=" union select distinct notice_id, expl_id from notices, exemplaires, bulletins where expl_cote='".$ct->expl_cote."' and expl_bulletin=bulletin_id and bulletin_notice=notice_id ";
    								$res_del=pmb_mysql_query($rq_del) ;
    								if (pmb_mysql_num_rows($res_del)) {
										while ($n_id=pmb_mysql_fetch_object($res_del)) {
    										pmb_mysql_query("delete from temp_n_id where notice_id=".$n_id->notice_id." and expl_id=".$n_id->expl_id);
    									}
    								}
    							}
    						}
    						$lf--;
    					}
    					if ($lf==0) {
    						if (preg_match("/[0-9][0-9][0-9]/",$ct->expl_cote,$c)) {
    							$found=false;
    							$lcote=3;
    							$level=$level_ref;
    							while ((!$found)&&($level<=$lcote)) {
    								$cote=substr($c[0],0,$level);
    								$compl=str_repeat("0",$lcote-$level);
    								$rq_index="select indexint_name,indexint_comment from indexint where indexint_name='".$cote.$compl."' and length(indexint_name)>=$lcote and indexint_comment!='' and num_pclass='".$type_aff_navigopac."' order by indexint_name limit 1 ";
    								$res_index_1=pmb_mysql_query($rq_index);
    								if (pmb_mysql_num_rows($res_index_1)) {
    									$name=pmb_mysql_result($res_index_1,0,0);
    									if (!$nc) {
    										if (substr($name,0,$level-1)==$dcote) {
    											$t["comment"]=pmb_mysql_result($res_index_1,0,1);
    											if ($level>1) {
    												$cote_n_1=substr($c[0],0,$level-1);
    												$compl_n_1=str_repeat("0",$lcote-$level+1);
    												if (($cote.$compl)==($cote_n_1.$compl_n_1))
    													$t["comment"]="Généralités";
    											}
    											$t["lcote"]=$lcote;
    											$t["dcote"]=$cote;
    											$index[$name]=$t;
    											$found=true;
    										} else $level++;
    									} else {
    										if (substr($name,0,$level-1)==$dcote) {
    											$rq_del="select distinct notice_id, expl_id from notices, exemplaires where expl_cote='".$ct->expl_cote."' and expl_notice=notice_id ";
    											$rq_del.=" union select distinct notice_id, expl_id from notices, exemplaires, bulletins where expl_cote='".$ct->expl_cote."' and expl_bulletin=bulletin_id and bulletin_notice=notice_id ";
    											$res_del=pmb_mysql_query($rq_del);
    											if (pmb_mysql_num_rows($res_del)) {
													while ($n_id=pmb_mysql_fetch_object($res_del)) {
    													pmb_mysql_query("delete from temp_n_id where notice_id=".$n_id->notice_id." and expl_id=".$n_id->expl_id);
    												}
    											}
    											$found=true;
    										} else $level++;
    									}
    								} else $level++;
    							}
    							if (($level>$lcote)&&($lf==0)) {
    								$t["comment"]=$msg["l_unclassified"];
    								$t["lcote"]=$lcote;
    								$t["dcote"]=$dcote;
    								$index["NC"]=$t;
    							}
    						} else {
    							$t["comment"]=$msg["l_unclassified"];
    							$t["lcote"]=$lcote;
    							$t["dcote"]=$dcote;
    							$index["NC"]=$t;
    						}
    					}
    				}
    			}
    			if ($nc) {
    				$nbr_lignes=pmb_mysql_result(pmb_mysql_query("select count(1) from temp_n_id"),0,0);
    			}
    			if ($nbr_lignes) {
    				$requeteSource = "SELECT DISTINCT notices.notice_id FROM temp_n_id JOIN notices ON notices.notice_id=temp_n_id.notice_id GROUP BY notices.notice_id";
    			}
    		}
    	}

   		pmb_mysql_query("create temporary table t_s_navigation_section (notice_id integer unsigned not null)");
   		$requete="insert into t_s_navigation_section ".$requeteSource;
   		pmb_mysql_query($requete);
		pmb_mysql_query("alter table t_s_navigation_section add primary key(notice_id)");

		return "t_s_navigation_section"; 
    }
    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {  
    }
    
    public function make_unimarc_query() {
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	return "";
    }
    
    
    
	//fonction de vérification du champ saisi ou sélectionné
    public function is_empty($valeur) {
    	if (count($valeur)) {
    		if ($valeur[0]=="") return true;
    			else return ($valeur[0] === false);
    	} else {
    		return true;
    	}	
    }
}
?>