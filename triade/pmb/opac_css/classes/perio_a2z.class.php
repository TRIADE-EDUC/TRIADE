<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perio_a2z.class.php,v 1.97.2.1.2.1 2025/02/10 14:15:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des perio a2z
if ( ! defined( 'PERIO_CLASS' ) ) {
  define( 'PERIO_CLASS', 1 );
  
  global $base_path, $class_path, $include_path;
  
require_once($base_path."/includes/templates/perio_a2z.tpl.php");
require_once($base_path."/classes/notice_info.class.php");
require_once($base_path.'/classes/notice_affichage.class.php');


require_once($base_path.'/includes/templates/notice_display.tpl.php');
require_once($base_path.'/includes/explnum.inc.php');
require_once($base_path.'/classes/notice_affichage.class.php');
require_once($base_path.'/includes/bul_list_func.inc.php');
require_once($base_path.'/classes/upload_folder.class.php');


require_once($base_path.'/includes/notice_affichage.inc.php');
require_once($base_path.'/includes/navbar.inc.php');

require_once($include_path."/resa_func.inc.php"); 
require_once($base_path.'/classes/notice.class.php');
require_once($base_path."/includes/templates/avis.tpl.php");
require_once("$class_path/acces.class.php");
require_once($base_path."/includes/notice_affichage.inc.php");
require_once($base_path."/includes/templates/notice.tpl.php");

require_once($include_path."/notice_affichage.inc.php");


class perio_a2z {
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $tab_alpha_notice = array();
	public $onglets_contens = array();
	public $onglets_sub_contens = array();
	public $max_per_onglet = 12;
	public $location = 0;
	public $surlocation = 0;
	public $titles = array(); //Liste des titres de périodiques
	public $titles_filter = array(); //Liste des titres de périodiques filtrés
	
	protected $start = "";//Pour filtrer par début de titre
	protected $start_len = 0;//Pour filtrer par début de titre
	
	// ---------------------------------------------------------------
	//		perio_a2z : constructeur
	// ---------------------------------------------------------------
	public function __construct($bull_id=0,$abecedaire=0,$max_per_onglet=12,$start=""){
		// A MODIFIER!!
		global $abt_actif, $opac_perio_a2z_default_active_subscription_filter;
		
		$this->start = strip_empty_words($start);
		$this->start_len = pmb_strlen($this->start);

		if(($abt_actif=='') && $opac_perio_a2z_default_active_subscription_filter){
			$abt_actif = 1;
		}
		
		$bull_id = intval($bull_id);
		if($max_per_onglet){
			$this->max_per_onglet=$max_per_onglet;
		}
		if(!$bull_id){
			if(!$abecedaire){
				$this->getData();
			}else{
				$this->getDataAbc();
			}
		}
	}
	
	public function getData() {
		global $filtre_select;
		
		$this->titles=array();
		$this->titles_filter=array();
		
		$ongletInc=0;
		$req=$this->getQuery();
		$resultat=pmb_mysql_query($req);
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
				$index_sew_trim=trim($r->index_sew);
				if(pmb_substr($r->notice_id,0,pmb_strlen("es)"))=="es" &&  $filtre_select!=2){
					// si cataloguée, on ne l'affiche pas. sauf si on filtre par fonds externe ($filtre_select!=2)
					if ($this->get_doublon_ex($this->extract_external_id($r->notice_id))) continue;
				}
				$letter=pmb_substr(pmb_strtolower($index_sew_trim),0,1);
				if(is_numeric($letter)){
					$letter="0";
				}
				
				if(!$ongletInc || $qtOngletCours==$this->max_per_onglet || (isset($lastLetter) && $letter!=$lastLetter && (is_numeric($lastLetter) || $lastLetter==" "))){
					$ongletInc++;
					$qtOngletCours=0;
					$this->onglets_contens[$ongletInc]["first_label"]=pmb_strtoupper($index_sew_trim);
					$this->onglets_contens[$ongletInc]["letter"]=$letter;
				}
				
				$lastLetter=$letter;
				
				$qtOngletCours++;
				$this->onglets_contens[$ongletInc]["last_label"]=pmb_strtoupper($index_sew_trim);
				
				//Sous-onglet (uniquement pour compatibilité avec affichage abécédaire
				$this->onglets_sub_contens[$ongletInc][1]["id"][]=$r->notice_id;
				
				//On mémorise le couple onglet/sous-onglet pour la recherche ajax
				$t=array();
				$t["onglet"]=$ongletInc.'_1';
				$t["label"]=pmb_strtoupper($index_sew_trim);
				$t["title"]=$r->tit1;
				$t["id"]=$r->notice_id;
				if(pmb_substr($r->notice_id,0,pmb_strlen("es)"))=="es"){
					//$this->titles[]="xxxxxxxxx";
					//print $r->notice_id;
				}else{
					$this->titles[]=$t;
					if ($this->start && (pmb_substr($index_sew_trim,0,$this->start_len) == $this->start)) {
						$this->titles_filter[]=$t;
					}
				}
			}
			//On transforme les labels
			foreach($this->onglets_contens as $onglet=>$myOnglet){
				$lastOnglet=$onglet;
				if($onglet==1){
					$this->onglets_contens[$onglet]["first_label"]=pmb_substr($this->onglets_contens[$onglet]["first_label"],0,1);
				}else{
					$mesTermes=$this->difference_label($this->onglets_contens[$onglet-1]["last_label"],$this->onglets_contens[$onglet]["first_label"]);
					$this->onglets_contens[$onglet-1]["last_label"]=$mesTermes[0];
					$this->onglets_contens[$onglet]["first_label"]=$mesTermes[1];
				}
			}
			//On retravaille le dernier
			$mesTermes=$this->difference_label($this->onglets_contens[$lastOnglet]["first_label"],$this->onglets_contens[$lastOnglet]["last_label"]);
			$this->onglets_contens[$lastOnglet]["last_label"]=$mesTermes[1];
			foreach($this->onglets_contens as $onglet=>$myOnglet){
				$this->onglets_contens[$onglet]["label"]=$this->onglets_contens[$onglet]["first_label"]." - ".$this->onglets_contens[$onglet]["last_label"];
				//Cas particulier des inclassables et numériques
				if($this->onglets_contens[$onglet]["letter"]==" "){
					$this->onglets_contens[$onglet]["label"]=" # ";
				}elseif(is_numeric($this->onglets_contens[$onglet]["letter"])){
					$this->onglets_contens[$onglet]["label"]="0 - 9";
				}
			}
		}
	}
	
	public function getDataAbc() {
		global $filtre_select;
	
		$this->titles=array();
		$this->titles_filter=array();
		
		$tbCorrespondance= array();
		$ongletInc=0;
		
		$req=$this->getQuery();
		$resultat=pmb_mysql_query($req);
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
				$index_sew_trim=trim($r->index_sew);
				if(pmb_substr($r->notice_id,0,pmb_strlen("es)"))=="es" && $filtre_select!=2){
					// si cataloguée, on ne l'affiche pas. sauf si on filtre par fonds externe ($filtre_select!=2)
					if ($this->get_doublon_ex($this->extract_external_id($r->notice_id))) continue;
				}
				$letter=pmb_substr(trim(pmb_strtolower($r->index_sew)),0,1);
				//On classe selon la première lettre
				if(is_numeric($letter)){
					$letter="0";
				}
				if(isset($tbCorrespondance[$letter])){
					$tbCorrespondance[$letter]["qt"]++;
					$tbCorrespondance[$letter]["lastTitle"]=$index_sew_trim;
					$onglet=$tbCorrespondance[$letter]["onglet"];
				}else{
					$ongletInc++;
					$ongletSubInc=1;
					$tbCorrespondance[$letter]["qt"]=1;
					$tbCorrespondance[$letter]["onglet"]=$ongletInc;
					if(is_numeric($letter)){
						$this->onglets_contens[$ongletInc]["label"]="0 - 9";
					}elseif($letter==" "){// les inclassables
						$this->onglets_contens[$ongletInc]["label"]=" # ";
					}else{
						$this->onglets_contens[$ongletInc]["label"]=" ".pmb_strtoupper($letter)." ";
					}
					$this->onglets_contens[$ongletInc]["letter"][]=$letter;
					$onglet=$ongletInc;
				}
				//Sous-onglet
				if(!isset($this->onglets_sub_contens[$onglet][$ongletSubInc])){
					$this->onglets_sub_contens[$onglet][$ongletSubInc]["label"]=pmb_strtoupper($index_sew_trim);
				}
				$this->onglets_sub_contens[$onglet][$ongletSubInc]["id"][]=$r->notice_id;
				$this->onglets_sub_contens[$onglet][$ongletSubInc]["last_label"]=pmb_strtoupper($index_sew_trim);
				if(count($this->onglets_sub_contens[$onglet][$ongletSubInc]["id"])==$this->max_per_onglet){
					$ongletSubInc++;
				}
				//On mémorise le couple onglet/sous-onglet pour la recherche ajax
				$t=array();
				$t["onglet"]=$onglet.'_'.$ongletSubInc;
				$t["label"]=pmb_strtoupper($index_sew_trim);
				$t["title"]=$r->tit1;
				$t["id"]=$r->notice_id;
				if(pmb_substr($r->notice_id,0,pmb_strlen("es)"))=="es"){
	// 				$this->titles[]="xxxxxxxxx";
	// 				print $r->notice_id;
				}else{
					$this->titles[]=$t;
					if ($this->start && (pmb_substr($index_sew_trim,0,$this->start_len) == $this->start)) {
						$this->titles_filter[]=$t;
					}
				}
				
			}
			//On transforme les labels
			foreach($this->onglets_sub_contens as $onglet=>$myOnglet){			
				foreach($myOnglet as $ongletSub=>$myOngletSub){
					$lastOngletSub=$ongletSub;
					if($ongletSub==1){
						$this->onglets_sub_contens[$onglet][$ongletSub]["label"]=pmb_substr($this->onglets_sub_contens[$onglet][$ongletSub]["label"],0,1);
					}else{
						$mesTermes=$this->difference_label($this->onglets_sub_contens[$onglet][$ongletSub-1]["last_label"],$this->onglets_sub_contens[$onglet][$ongletSub]["label"]);
						$this->onglets_sub_contens[$onglet][$ongletSub-1]["last_label"]=$mesTermes[0];
						$this->onglets_sub_contens[$onglet][$ongletSub]["label"]=$mesTermes[1];
					}
				}
				//On retravaille le dernier
				$mesTermes=$this->difference_label($this->onglets_sub_contens[$onglet][$lastOngletSub]["label"],$this->onglets_sub_contens[$onglet][$lastOngletSub]["last_label"]);
				$this->onglets_sub_contens[$onglet][$lastOngletSub]["last_label"]=$mesTermes[1];
			}
		}
	}
	
	public function difference_label($label1,$label2){
		$retour=array();
		$terme1=$terme2="";
		for($i=0; $i<pmb_strlen($label1);$i++){
			$terme1=pmb_substr($label1,0,$i+1);
			$terme2=pmb_substr($label2,0,$i+1);
			if($terme1 !=  $terme2){			
				break;
			}
		}
		$retour[0]=$terme1;
		$retour[1]=$terme2;
		return $retour;
	}
	
	public function getQuery() {
		global $location;
		global $surloc;
		global $abt_actif;
		global $gestion_acces_active, $gestion_acces_empr_notice;
		global $filtre_select;
	
		$this->location = intval($location);
		$this->surlocation = intval($surloc);
		
		if($abt_actif){
			$from_abt_actif = " ,abts_abts ";
			$where_abt_actif = "  and num_notice=notice_id and date_fin >= CURDATE() ";
		} else {
			$from_abt_actif = "";
			$where_abt_actif = "";
		}
		$opac_view_restrict=" and !(opac_visible_bulletinage&0x10) ";
		if(isset($_SESSION["opac_view"]) && $_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
			$opac_view_restrict.=" and notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
		}
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
			$statut_j=',notice_statut ';
			$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
		}
		if($this->location){
			$req="
			SELECT distinct serial_id as notice_id, index_sew, tit1 FROM (
				(
					SELECT DISTINCT bulletin_notice as serial_id ,index_sew, tit1 FROM notices $acces_j, bulletins, exemplaires $from_abt_actif $statut_j
					WHERE notice_id=bulletin_notice and bulletin_id = expl_bulletin  and expl_location=".$this->location."  $opac_view_restrict $where_abt_actif $statut_r
				)union( 
					SELECT DISTINCT id_serial as serial_id ,index_sew, tit1 from notices $acces_j, collections_state $from_abt_actif $statut_j
					WHERE notice_id=id_serial and location_id=".$this->location."  $opac_view_restrict $where_abt_actif $statut_r
				)union(
					SELECT DISTINCT bulletin_notice as serial_id ,index_sew, tit1 FROM notices $acces_j, bulletins, explnum, explnum_location $from_abt_actif $statut_j
					WHERE notice_id=bulletin_notice and bulletin_id = explnum_bulletin AND num_explnum=explnum_id and num_location=".$this->location." $opac_view_restrict $where_abt_actif $statut_r
				)
			) AS sub order by index_sew	
			";		
			
		} elseif($this->surlocation) {
			$req="
			SELECT distinct serial_id as notice_id, index_sew, tit1 FROM (
				(
					SELECT DISTINCT bulletin_notice as serial_id ,index_sew, tit1 FROM notices $acces_j, bulletins, exemplaires $from_abt_actif $statut_j
					WHERE notice_id=bulletin_notice and bulletin_id = expl_bulletin AND expl_location in( select idlocation from  docs_location where surloc_num = ".$this->surlocation.") $opac_view_restrict $where_abt_actif $statut_r
				)union( 
					SELECT DISTINCT id_serial as serial_id ,index_sew, tit1 from notices $acces_j, collections_state $from_abt_actif $statut_j
					WHERE notice_id=id_serial and location_id in( select idlocation from  docs_location where surloc_num = ".$this->surlocation.") $opac_view_restrict $where_abt_actif $statut_r
				)union(
					SELECT DISTINCT notice_id as serial_id ,index_sew, tit1 FROM notices $acces_j, bulletins, explnum, explnum_location $from_abt_actif $statut_j
					WHERE notice_id=bulletin_notice and bulletin_id = explnum_bulletin AND num_explnum=explnum_id and num_location in( select idlocation from docs_location  where surloc_num = ".$this->surlocation.") $opac_view_restrict $where_abt_actif $statut_r
				)union(
					SELECT DISTINCT notice_id as serial_id ,index_sew, tit1 FROM notices $acces_j, bulletins, explnum, explnum_location, analysis $from_abt_actif $statut_j
					WHERE notice_id=bulletin_notice and bulletin_id = explnum_notice and analysis_bulletin=bulletin_id AND num_explnum=explnum_id and num_location in( select idlocation from docs_location  where surloc_num = ".$this->surlocation.") $opac_view_restrict $where_abt_actif $statut_r
				)
			) AS sub order by index_sew	
			";
			
		} else {
			$req="SELECT DISTINCT notice_id, index_sew, tit1 from notices $acces_j $from_abt_actif $statut_j where niveau_biblio='s' $opac_view_restrict  $where_abt_actif $statut_r order by index_sew";
		}
		$sources_list=$this->get_external_sources_list();	
		if(count($sources_list) && $filtre_select!=1){
			if($filtre_select==2){
				$sources_queries =array();
				foreach($sources_list as $conn => $sources_ids){
					foreach($sources_ids as $source_id){
						$sources_queries[]= "
						select * from ((
							SELECT concat('es_".$conn."_',entrepot3.recid) as id, entrepot3.i_value as index_sew, entrepot3.value as tit1
							from entrepot_source_".$source_id." as entrepot1 
							join entrepot_source_".$source_id." as entrepot2 on entrepot1.recid = entrepot2.recid 
							join entrepot_source_".$source_id." as entrepot3 on entrepot1.recid = entrepot3.recid
							where entrepot1.ufield='bl' and entrepot1.value='s' and entrepot2.ufield='hl' and entrepot2.value='1' and entrepot3.ufield='200' and entrepot3.usubfield='a' group by 1
						) union (
							SELECT concat('es_".$conn."_',entrepot3.recid) as id, entrepot3.i_value as index_sew, entrepot3.value as tit1 
							FROM entrepot_source_".$source_id." as entrepot1 
							join entrepot_source_".$source_id." as entrepot2 on entrepot1.recid = entrepot2.recid 
							join entrepot_source_".$source_id." as entrepot3 on entrepot3.recid = entrepot2.recid 
							WHERE entrepot1.ufield = 'bl' and entrepot1.value='a' and entrepot2.ufield = 'hl' and entrepot2.value = '2' and entrepot3.ufield = '461' and entrepot3.usubfield='t' and entrepot3.value != '' group by entrepot3.value 
						)) as sub".$source_id." group by tit1";
					}
				}			
				$req1 = "select id as notice_id, tit1, index_sew from ((".implode(") union (",$sources_queries).")) as sousrequte group by tit1 order by index_sew";
			}else{
				$sources_queries =array();
				foreach($sources_list as $conn => $sources_ids){
					foreach($sources_ids as $source_id){
						$sources_queries[]= "
						select * from ((
							SELECT concat('es_".$conn."_',entrepot3.recid) as id, entrepot3.i_value as index_sew, entrepot3.value as tit1
							from entrepot_source_".$source_id." as entrepot1 
							join entrepot_source_".$source_id." as entrepot2 on entrepot1.recid = entrepot2.recid 
							join entrepot_source_".$source_id." as entrepot3 on entrepot1.recid = entrepot3.recid
							where entrepot1.ufield='bl' and entrepot1.value='s' and entrepot2.ufield='hl' and entrepot2.value='1' and entrepot3.ufield='200' and entrepot3.usubfield='a' group by 1
						) union (
							SELECT concat('es_".$conn."_',entrepot3.recid) as id, entrepot3.i_value as index_sew, entrepot3.value as tit1 
							FROM entrepot_source_".$source_id." as entrepot1 
							join entrepot_source_".$source_id." as entrepot2 on entrepot1.recid = entrepot2.recid 
							join entrepot_source_".$source_id." as entrepot3 on entrepot3.recid = entrepot2.recid 
							WHERE entrepot1.ufield = 'bl' and entrepot1.value='a' and entrepot2.ufield = 'hl' and entrepot2.value = '2' and entrepot3.ufield = '461' and entrepot3.usubfield='t' and entrepot3.value != '' group by entrepot3.value 
						)) as sub".$source_id." group by tit1";
					}
				}
				$req1="
					SELECT id as notice_id, index_sew, tit1 FROM (
						(
							SELECT notice_id as id , index_sew, tit1 from notices $acces_j $from_abt_actif $statut_j
							where niveau_biblio='s' $opac_view_restrict  $where_abt_actif $statut_r
						) union 
						(".implode(") union (",$sources_queries).")
					)AS sub order by trim(index_sew)";
			}
			return $req1;
		}
		return $req;
	}
	/* MB - 28/12/2018: Plus utilisé, tout passe par filterSearch
	public function startwith($elt) {
		if (pmb_substr(strip_empty_words($elt["title"]),0,pmb_strlen(strip_empty_words($this->start)))==strip_empty_words($this->start)) {
			return true;
		} else return false;
	}
	
	public function filter() {
		if($this->start){
			$titles = $this->titles_filter;
		}else{
			$titles = $this->titles;
		}
		return $titles;
	}*/
	
	public function filterSearch($datas) {
		//commence par
		if($this->start){
			$titles = $this->titles_filter;
		}else{
			$titles = $this->titles;
		}
		
		//on complète si besoin
		if((count($titles)<20) && $this->start && count($this->titles)){
			//Liste des identifiants de périodiques disponibles
			$listeId="";
			$title_tmp=array();
			foreach($this->titles as $title){
				if ($listeId) $listeId.= ",";
				// présence id notice externe es_...
				$listeId.= "'" . $title["id"] . "'";
				$title_tmp[$title["id"]] = $title;
			}
			
			$aq=new analyse_query(stripslashes($datas."*"));
			$query=$aq->get_query("notices", "index_sew", "index_sew", "notice_id", "niveau_biblio='s' AND niveau_hierar='1' AND notice_id IN (".$listeId.")");
			$result = pmb_mysql_query($query);
			if ($result) {
				while ($row = pmb_mysql_fetch_object($result)) {
					if($title_tmp[$row->notice_id] && (!in_array($title_tmp[$row->notice_id],$titles))){
						$titles[]=$title_tmp[$row->notice_id];
					}
				}
			}
		}
		return $titles;
	}
	
	public function compose_label($titre1,$titre2) {
		$therme1=$therme2="";
		for($i=0; $i<pmb_strlen($titre1);$i++){
			$therme1=pmb_substr($titre1,0,$i+1);
			$therme2=pmb_substr($titre2,0,$i+1);
			if($therme1 !=  $therme2){			
				break;
			}
		}
		$label=pmb_substr($therme1,0,5)." - ".pmb_substr($therme2,0,5);
		return($label);
	}
		
	public function get_form($onglet_sel='1_1',$flag_empty=0,$flag_ajax=0){
	    global $msg;
		global $a2z_perio_display,$onglet_a2z,$ongletSub_a2z,$ongletSubList_a2z, $a2z_perio,$a2z_tpl;
		global $abt_actif;
		global $avis_tpl_form_script;
		global $filtre_select;
		
		$a2z_tpl = common::format_title($msg["a2z_title"]).$a2z_tpl;
		
		$myArray = explode("_",$onglet_sel);
		$onglet_sel = $myArray[0];
		$ongletSub_sel = 0;
		if(!empty($myArray[1])) $ongletSub_sel = $myArray[1];
	
		if(!$this->onglets_contens){
		    if($flag_ajax) {
		        $form = $a2z_tpl;
		    } else {
		        $form="<div id='perio_a2z'>\n".$a2z_tpl."</div>";
		    }
			$form = str_replace('!!perio_display!!',"", $form);
			if($abt_actif) {
			    $check_abt_actif=" checked='checked' ";
			} else {
			    $check_abt_actif="";
			}
		
			$form = str_replace('!!check_abt_actif!!',$check_abt_actif, $form);
			$form = str_replace('!!onglet_sel!!',"", $form);
			$form = str_replace('!!location!!',$this->location, $form);
			$form = str_replace('!!surlocation!!',$this->surlocation, $form);
			$form = str_replace('!!perio_display!!',"", $form);
			$form=str_replace('!!a2z_onglets_list!!',"", $form);
			$form=str_replace('!!a2z_onglets_sublist!!',"", $form);
			$form=str_replace('!!perio_id_list!!',"", $form);
			$form=str_replace('!!a2z_perio_list!!',"", $form);
			$form = str_replace('!!filtre!!',"", $form);
			return $form;
		}
		if($flag_ajax) {
		    $form=$avis_tpl_form_script.$a2z_tpl;
		} else {
		    $form=$avis_tpl_form_script."<div id='perio_a2z'>\n".$a2z_tpl."</div>";
		}
		$form_list="";
		$form_sublist="";
		$perio_id_list = '';
		$perio_list = '';
		foreach($this->onglets_contens as $onglet_num => $onglet){
			$line=$onglet_a2z;
			$line = str_replace('!!onglet_num!!',$onglet_num, $line);
			$line = str_replace('!!onglet_label!!',$onglet["label"], $line);
					
			$lineSub = $ongletSub_a2z;
			$lineSub = str_replace('!!onglet_num!!',$onglet_num, $lineSub);
			$subList="";
			if(count($this->onglets_sub_contens[$onglet_num])>1){
				foreach($this->onglets_sub_contens[$onglet_num] as $ongletSub_num => $ongletSub){
					$lineSubList = $ongletSubList_a2z;
					$lineSubList = str_replace('!!onglet_num!!',$onglet_num, $lineSubList);
					$lineSubList = str_replace('!!ongletSub_num!!',$ongletSub_num, $lineSubList);
					$lineSubList = str_replace('!!ongletSub_label!!',$ongletSub["label"]." - ".$ongletSub["last_label"], $lineSubList);
					$subList.=$lineSubList;
				}
			}
			$lineSub = str_replace('!!ongletSub_list!!',$subList, $lineSub);
			
			if($onglet_num==$onglet_sel && !$flag_empty){
				foreach($this->onglets_sub_contens[$onglet_num] as $ongletSub_num => $ongletSub){
					if($ongletSub_num==$ongletSub_sel){
						// liste des périodiques
						$perio_list="";
						$view=0;
						$perio_id_list="";
						if (is_array($ongletSub["id"])) {
							//tri de la liste par titre
							$serials_ids = "";
							$es_queries = array();
							foreach($ongletSub["id"] as $id){
								if(strpos($id,"es")!==false){
									// si cataloguée, on ne l'affiche pas. sauf si on filtre par fonds externe ($filtre_select!=2)
									if ($filtre_select==2 || !$this->get_doublon_ex($this->extract_external_id($id))){
										$elems = explode("_",$id);
										$query = "select * from external_count where rid =".$elems[2];
										$result = pmb_mysql_query($query);
										if(pmb_mysql_num_rows($result)){
											while($row = pmb_mysql_fetch_object($result)){
												$es_queries[]="(select '".$id."' as notice_id,entrepot_source_".$row->source_id.".value as tit1,entrepot_source_".$row->source_id.".i_value as index_sew from external_count join entrepot_source_".$row->source_id." on entrepot_source_".$row->source_id.".recid = external_count.rid where external_count.rid = '".$elems[2]."' and entrepot_source_".$row->source_id.".ufield = '200' and entrepot_source_".$row->source_id.".usubfield='a')";
											}
										}
									}
								}else{
									if($serials_ids) $serials_ids.= ",";
									$serials_ids.= $id;
								}
							}
							$es_query = $int_query = "";
							if(count($es_queries)){
								$es_query = "select * from (".implode(" union ",$es_queries).") as sub";
							}
							if($serials_ids){
								$int_query = "select notice_id,tit1 from notices where notice_id in(".$serials_ids.")";
							}
							
							if($int_query && $es_query){
								$query = "select * from ((".$int_query.") union (".$es_query.")) as sub order by index_sew";
							}else if ($es_query){
								$query = $es_query." order by index_sew";
							}else if ($int_query){ 
								$query = $int_query." order by index_sew";
							}
							$result = pmb_mysql_query($query);
							if ($result) {
								while ($notice = pmb_mysql_fetch_object($result)) {
									if(!$perio_id_list)$perio_id_list="'".$notice->notice_id."'";else	$perio_id_list.=",'".$notice->notice_id."'";
									$perio = $a2z_perio;
									
									$perio = str_replace('!!id!!',$notice->notice_id, $perio);	
									$perio = str_replace('!!perio_title!!',$notice->tit1, $perio);
									
									if(strpos($notice->notice_id,"es")!==false){
										$perio = str_replace('!!abt_actif!!',$this->get_external_icon($notice->notice_id), $perio);
									}else{
										$req = "select abt_id from abts_abts  where num_notice=".$notice->notice_id." and date_fin >= CURDATE() ";
										$res = pmb_mysql_query($req);
										if (pmb_mysql_num_rows($res)) {
											$perio = str_replace('!!abt_actif!!',"<img src='".get_url_icon('check.png')."'>", $perio);									
										}else{
											$perio = str_replace('!!abt_actif!!',"", $perio);	
										}
									}
									$perio_list.= $perio;
									if(!$view){
										$view++;
										$form = str_replace('!!perio_display!!',$this->get_perio($notice->notice_id), $form);	
										$perio_list = str_replace('!!perio_btn_pressed!!','true', $perio_list);	
										$perio_list = str_replace('!!perio_active!!','perio_active', $perio_list);				
									}else{
										$perio_list = str_replace('!!perio_btn_pressed!!','false', $perio_list);		
										$perio_list = str_replace('!!perio_active!!','perio_inactive', $perio_list);		
									}
								}
							}
						}
						$line = str_replace('!!onglet_class!!',"isbd_public_active", $line);
						$line = str_replace('!!onglet_btn_pressed!!',"true", $line);
						$lineSub = str_replace('!!ongletSub_display!!','block', $lineSub);
					}
				}
			}else{
				$line = str_replace('!!onglet_class!!',"isbd_public_inactive", $line);
				$line = str_replace('!!onglet_btn_pressed!!',"false", $line);
				$lineSub = str_replace('!!ongletSub_display!!','none', $lineSub);	
			}
			
			$form_list.=$line;
			$form_sublist.=$lineSub;
		}	
		if($abt_actif) $check_abt_actif=" checked='checked' ";
		else $check_abt_actif="";
			
		$form = str_replace('!!check_abt_actif!!',$check_abt_actif, $form);	
		$form = str_replace('!!onglet_sel!!',$onglet_sel, $form);	
		$form = str_replace('!!location!!',$this->location, $form);	
		$form = str_replace('!!surlocation!!',$this->surlocation, $form);	
		$form = str_replace('!!perio_display!!',"", $form);		
		$form=str_replace('!!a2z_onglets_list!!',$form_list, $form);
		$form=str_replace('!!a2z_onglets_sublist!!',$form_sublist, $form);	
		$form=str_replace('!!perio_id_list!!',$perio_id_list, $form);	
		$form=str_replace('!!a2z_perio_list!!',$perio_list, $form);	
		$form = str_replace('!!filtre!!',$this->get_filtre_form(), $form);	
	
		return $form;
	}
	
	public function get_filtre_form(){
		global $msg, $filtre_select;
		
		if(!count($this->get_external_sources_list())) return "";		
		$filtre_select = intval($filtre_select);
		$selected = array();
		$selected[0] = '';
		$selected[1] = '';
		$selected[2] = '';
		$selected[$filtre_select]= " selected='selected' ";
		return $msg["perio_a2z_filtre"]."
			<select class='saisie-25em' id='filtre_select' name='filtre_select' onchange=\"memo_onglet=new Array(); reload_all(); \">
				<option value='0' $selected[0] >".$msg["perio_a2z_filtre_all"]."</option>
				<option value='1' $selected[1] >".$msg["perio_a2z_fonds_propre"]."</option>
				<option value='2' $selected[2] >".$msg["perio_a2z_fonds_externe"]."</option>
			</select>
		";		
	}	
	
	public function get_onglet($onglet_sel='1_1'){
		global $msg,$charset,$base_path;
		global $a2z_perio_display,$onglet_a2z, $a2z_perio,$a2z_tpl_ajax;
		global $filtre_select;
		$myArray = explode("_",$onglet_sel);
		$onglet_sel = $myArray[0];
		$ongletSub_sel = 0;
		if(!empty($myArray[1])) $ongletSub_sel = $myArray[1];
		
		$form=$a2z_tpl_ajax;
		$form_list="";
		$line = '';
		if(is_countable($this->onglets_sub_contens[$onglet_sel]) && count($this->onglets_sub_contens[$onglet_sel])){
			foreach($this->onglets_sub_contens[$onglet_sel] as $onglet_num => $onglet){
				if($onglet_num==$ongletSub_sel){
					// onglet actif
					$line = str_replace('!!onglet_class!!',"isbd_public_active", $line);
					// liste des périodiques
					$perio_list="";
					$view=0;
					$perio_id_list="";
					if (is_array($onglet["id"])) {
						//tri de la liste par titre
						$serials_ids = implode(",", $onglet["id"]);
						foreach($onglet["id"] as $id_brute){
							if(pmb_substr($id_brute,0,2)=="es"){
								//notice externe					
								if( $filtre_select!=2){
									// si cataloguée, on ne l'affiche pas. sauf si on filtre par fonds externe ($filtre_select!=2)
									if ($this->get_doublon_ex($this->extract_external_id($id_brute))) continue;
								}
								$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->extract_external_id($id_brute));
								$myQuery = pmb_mysql_query($requete);
								$source_id = pmb_mysql_result($myQuery, 0, 0);		
								$query="select ufield,value from entrepot_source_$source_id where recid='".addslashes($this->extract_external_id($id_brute))."' and ((ufield='200' and usubfield='a') or (ufield='461' and usubfield='t') or (ufield='bl') or (ufield='hl')) ";						
								$result = pmb_mysql_query($query);
								if ($result) {
									while($row= pmb_mysql_fetch_object($result)){
										$infos[$row->ufield] = $row->value;
									}
									switch($infos['bl'].$infos['hl']){
										case "a2" :
										case "s2" :
											$tit1 = $infos['461'];
											break;
										default :
											$tit1 = $infos['200'];
											break;
											
									}
									
									$perio = $a2z_perio;						
									$perio = str_replace('!!id!!',$id_brute, $perio);
									$perio = str_replace('!!perio_title!!',$tit1, $perio);										
									$perio = str_replace('!!abt_actif!!',$this->get_external_icon($id_brute), $perio);								
									$perio_list.= $perio;
									if(!$view){
										$view++;
										$form = str_replace('!!perio_display!!',$this->get_perio($id_brute), $form);
									}						
									
								}	
							}else{
								$query = "select notice_id,tit1 from notices where notice_id =$id_brute";
								//print $query."<br>";
								$result = pmb_mysql_query($query);
								if ($result) {
									if ($notice = pmb_mysql_fetch_object($result)) {
										$perio = $a2z_perio;
								
										$perio = str_replace('!!id!!',$notice->notice_id, $perio);
										$perio = str_replace('!!perio_title!!',$notice->tit1, $perio);
								
										$req = "select abt_id from abts_abts  where num_notice=".$notice->notice_id." and date_fin >= CURDATE() ";
										$res = pmb_mysql_query($req);
										if (pmb_mysql_num_rows($res)) {
											$perio = str_replace('!!abt_actif!!',"<img src='".get_url_icon('check.png')."'>", $perio);									
										}else{
											$perio = str_replace('!!abt_actif!!',"", $perio);
										}
										$perio_list.= $perio;
										if(!$view){
											$view++;
											$form = str_replace('!!perio_display!!',$this->get_perio($notice->notice_id), $form);
										}
									}
								}
							}
						}
					}
				}
				$form_list.=$line;
			}	
		}else{
			$perio_list=$msg["a2z_abt_actif_filter_no_result"];
			$form = str_replace('!!perio_display!!',"", $form);
		}
		$form=str_replace('!!perio_id_list!!',$perio_id_list, $form);	
		$form=str_replace('!!a2z_perio_list!!',$perio_list, $form);	
	
		return $form;
	}
	
	
	public function get_doublon_ex($id){
		$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($id);
		$myQuery = pmb_mysql_query($requete);
		$source_id = pmb_mysql_result($myQuery, 0, 0);		
		
		$rqt = "select value from entrepot_source_".$source_id." where recid='".addslashes($id)."' and ufield='011' and usubfield='a'";
		$res=pmb_mysql_query($rqt);
	
		if(pmb_mysql_num_rows($res)){
			if($r = pmb_mysql_fetch_object($res)) {			
				$code=$r->value;
				$rqt = "select notice_id from notices where code='$code'";
				$res_notice=pmb_mysql_query($rqt);
				
				if(pmb_mysql_num_rows($res_notice)){
					if($r_notice = pmb_mysql_fetch_object($res_notice)) {
						return $r_notice->notice_id;					
					}
				}
			}
		}
		return 0;
	}
	
	public function get_perio_ex($id){
		if(strpos($id,"es") === false){
			return $this->get_perio($id);
	 	}
		global $opac_notices_depliable;
		$opac_notices_depliable=0;
		return aff_serial_unimarc($this->extract_external_id($id));	
	}
	
	public function get_bulletin_retard($serial_id){
		global $opac_sur_location_activate,$msg;
		
		$bulletin_retard_form="	
		<h3><span class='bulletin_retard'>".$msg["bulletin_retard_title"]."</span></h3>
		<table class='bulletin_retard'>
			<tr>
				<th class='expl_header_location_libelle'>".$msg["bulletin_retard_location"]."</th>
				<th class='expl_header_location_libelle'>".$msg["bulletin_retard_date_parution"]."</th>
				<th class='expl_header_location_libelle'>".$msg["bulletin_retard_libelle_numero"]."</th>
				<th class='expl_header_location_libelle'>".$msg["bulletin_retard_comment"]."</th>
			</tr>
			!!bulletin_retard_list!!
		</table> 
		";
		$bulletin_retard_line="
			<tr class='!!tr_class!!' onmouseout=\"this.className='!!tr_class!!'\" onmouseover=\"this.className='surbrillance'\">
				<td>!!location_libelle!!</td>
				<td>!!date_parution!!</td>
				<td>!!libelle_numero!!</td>
				<td>!!comment_opac!!</td>		
			</tr>
		";
		$tpl="";
		$req="SELECT surloc_num, location_id,location_libelle, rel_date_parution,rel_libelle_numero, rel_comment_opac 
			from perio_relance, abts_abts, docs_location
			where  location_id=idlocation and rel_abt_num=abt_id and num_notice=$serial_id and rel_comment_opac!='' group by rel_abt_num,rel_date_parution,	rel_libelle_numero order by rel_nb desc";		
	
		$result = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($result)){
			$tr_class="";
			while($r = pmb_mysql_fetch_object($result)) {	
				$surloc_libelle="";
				if($opac_sur_location_activate && $r->surloc_num ){
					$req="select surloc_libelle from sur_location where surloc_id = ".$r->surloc_num;
					$res_surloc = pmb_mysql_query($req);
					if(pmb_mysql_num_rows($res_surloc)){
						$surloc= pmb_mysql_fetch_object($res_surloc);
						$surloc_libelle=$surloc->surloc_libelle." / ";
					}
				}			
				$line=$bulletin_retard_line;
				
				$line=str_replace("!!location_libelle!!", $surloc_libelle.$r->location_libelle , $line);	
				$line=str_replace("!!date_parution!!", $r->rel_date_parution, $line);	
				$line=str_replace("!!libelle_numero!!", $r->rel_libelle_numero, $line);		
				$line=str_replace("!!comment_opac!!", $r->rel_comment_opac, $line);	
				if($tr_class=='even')$tr_class="odd"; else $tr_class='even';
				$line=str_replace("!!tr_class!!",$tr_class, $line);	
				$lines.=$line	;	
			}
			$tpl=$bulletin_retard_form;
			$tpl=gen_plus("bulletin_retard",$msg["bulletin_retard_title"],str_replace("!!bulletin_retard_list!!", $lines, $tpl));		
		}
		return $tpl;		
	}	
	
	public function get_bulletins_search_form($id) {
	    global $msg, $charset;
	    global $page;
	    global $bull_num_deb, $bull_date_start, $bull_date_end;
	    global $opac_rgaa_active;
	    global $opac_bull_results_per_page;
	    
	    if(!$page) $page=1;
	    
	    $bull_results_per_page = $opac_bull_results_per_page ?? 12;
	    if (isset($this->max_per_onglet)) {
	        $bull_results_per_page = $this->max_per_onglet;
	    }

	    //Recherche par numéro
	    $num_field_start = "
		<input type='hidden' name='f_bull_deb_id' id='f_bull_deb_id' />
		<input id='bull_num_deb' name='bull_num_deb' type='text' size='" . ($opac_rgaa_active ? '2' : '10') ."' value='".$bull_num_deb."' onkeypress='if (event.keyCode==13){ show_perio($id);}' />";
                 
	    $search_form = "
       <form name=\"form_values\" action=\"./index.php?lvl=notice_display&id=$id\" >\n
			<input type=\"hidden\" name=\"premier\" value=\"\">\n
			<input type=\"hidden\" id='bull_page' name=\"page\" value=\"".htmlentities($page, ENT_QUOTES, $charset)."\">\n
			<input type=\"hidden\" id='bull_nb_per_page' name=\"nb_per_page_custom\" value=\"".htmlentities($bull_results_per_page, ENT_QUOTES, $charset)."\">\n";
	    if($opac_rgaa_active) {
	        $search_form .= "
            <fieldset>
                <legend class='visually-hidden'>".$msg["search_bull"]."</legend>
                <div class='bulletin_search_filter d-flex flex-wrap'>
                    <label for='bull_num_deb'>".$msg["search_per_bull_num"]." : ".$msg["search_bull_exact"]."</label>
                    ".$num_field_start."
                </div>
                <div class='bulletin_search_filter d-flex flex-wrap'>
                    <div class='inputs_bull_date inputs_bull_date_start d-flex flex-wrap'>
                        <label for='bull_date_start'>".$msg["search_per_bull_date"]." : ".$msg["search_bull_start"]."</label>
                        ".get_input_date('bull_date_start', 'bull_date_start', $bull_date_start, false, '')."
                    </div>
                    <div class='inputs_bull_date inputs_bull_date_end d-flex flex-wrap'>
                        <label for='bull_date_end'>".$msg["search_bull_end"]."</label>
                        ".get_input_date('bull_date_end', 'bull_date_end', $bull_date_end, false, '')."
                    </div>
                </div>
                <div class='bulletin_search_button'>
                    <input type='button' class='boutonrechercher' value='".$msg["142"]."' onclick='show_perio($id);' />
                </div>
            </fieldset>";
	    } else {
	        $search_form .= "
	        <table role='presentation'>
				<tr>
					<td>
                        <label for='bull_num_deb'><strong>".$msg["search_per_bull_num"]." : ".$msg["search_bull_exact"]."</strong></label>
					</td>
					<td>$num_field_start</td>
				</tr>
				<tr>
					<td>
                        <label for='bull_date_start'>
                            <strong>".$msg["search_per_bull_date"]." : ".$msg["search_bull_start"]."</strong>
                        </label>
                    </td>
					<td>
                        <div id='inputs_bull_date_start' class='inputs_bull_date_start'>
                            " . get_input_date('bull_date_start', 'bull_date_start', $bull_date_start, false, '') . "
                        </div>
                    </td>
					<td>
                        <label for='bull_date_end'>
                            <strong>".$msg["search_bull_end"]."</strong>
                        </label>
                    </td>
					<td>
                        <div id='inputs_bull_date_end' class='inputs_bull_date_end'>
                            " . get_input_date('bull_date_end', 'bull_date_end', $bull_date_end, false, '') . "
                        </div>
                    </td>
				</tr>
				<tr>
					<td colspan='4'><input type='button' class='boutonrechercher' value='".$msg["142"]."' onclick='show_perio($id);' /></td>
				</tr>
			</table>";
	    }
	    $search_form .= "
		</form>";
	    
	    return $search_form;
	}
	
	public function get_perio($id) {
		//on simplifie les appels..
		if(strpos($id,"es") !== false){
			return $this->get_perio_ex($id);
		}
			
		global $msg;
		global $f_bull_deb_id,$opac_bull_results_per_page,$page,$opac_fonction_affichage_liste_bull,$bull_date_start,$bull_date_end;
		global $bull_num_deb;
		global $flag_no_get_bulletin;
		global $recherche_ajax_mode;
		
		//on surcharge pour l'affichage des périos en affichage django
		global $lvl;
		global $opac_notices_format;
		
		$bull_results_per_page = $opac_bull_results_per_page ?? 12;
		if (isset($this->max_per_onglet)) {
		    $bull_results_per_page = $this->max_per_onglet;
		}

		if($opac_notices_format==AFF_ETA_NOTICES_TEMPLATE_DJANGO){
			$lvl='notice_display';
		}
	
		$flag_no_get_bulletin=1;
		$opac_notices_depliable=0;
		$resultat_aff = aff_notice($id, 0, 1, 0, "", 0, 0, 1, $recherche_ajax_mode);
		$form = "";
		/*
		$notice = new notice_affichage($id) ;
		$notice->do_header();
		$notice->do_public();
		//$notice->do_isbd();
		$notice->genere_simple(0, 'PUBLIC') ;					
		$resultat_aff .= $notice->result;	
		*/
		$requete = "SELECT notice_id, niveau_biblio,typdoc,opac_visible_bulletinage FROM notices WHERE notice_id='$id'  and (opac_visible_bulletinage&0x1) LIMIT 1";	
		$res = pmb_mysql_query($requete);
		if (($obj=pmb_mysql_fetch_object($res))) {
			//Recherche dans les numéros	
			$start_num = $bull_num_deb;
			$restrict_num = "";
			$restrict_date = "";
			if($f_bull_deb_id){
				$restrict_num = $this->compare_date($f_bull_deb_id);
			} else if($start_num){
				$restrict_num = " and bulletin_numero like '%".$start_num."%' ";
			}
			
			// Recherche dans les dates et libellés de période
			if(!$restrict_num) 
				$restrict_date = $this->compare_date($bull_date_start,$bull_date_end);
												
			// nombre de références par pages (12 par défaut)
			if(!$page) {
			    $page=1;
			}
			$debut =($page-1)*$bull_results_per_page;
			$limiter = " LIMIT $debut,$bull_results_per_page";
			
			$bulletin_retard=$this->get_bulletin_retard($id);			
			$tableau = "		
			<a id='tab_bulletins_serial_".$id."' name='tab_bulletin'></a>
			<h3><span class='titre_exemplaires'>".$msg["a2z_perio_list_bulletins"]."</span></h3>
			<div id='form_search_bull'>
				<script src='./includes/javascript/ajax.js'></script>
				".$this->get_bulletins_search_form($id)."
				<div class='row'></div><br />
			</div>\n";
			$resultat_aff.= $tableau;
			
			
	//		$resultat_aff.= "<script>ajax_parse_dom();</script>";	
			$resultat_aff.=$bulletin_retard;
    	      if(!empty($opac_fonction_affichage_liste_bull) && $opac_fonction_affichage_liste_bull == 'affichage_liste_bulletins_tableau') {
			    $filters = [];
			    $filters['serial_id'] = $id;
			    $filters['bulletin_numero'] = $bull_num_deb;
			    $filters['date_date_start'] = $bull_date_start;
			    $filters['date_date_end'] = $bull_date_end;
			    $pager = [];
			    if(!empty($page)) {
			        $pager['page'] = $page;
			        $pager['nb_per_page'] = $bull_results_per_page;
			    }
			    $resultat_aff.= list_opac_bulletins_a2z_ui::get_instance($filters, $pager)->get_display_list();
			    $resultat_aff.= "<br /><br />";
			} else {
			    $record_datas = record_display::get_record_datas($id);
			    $requete = $record_datas->get_query_bulletins_list($restrict_num, $restrict_date);
			    $rescount1=pmb_mysql_query($requete);
    			$count1=pmb_mysql_num_rows($rescount1);
    						
    			//si on recherche par date ou par numéro, le résultat sera trié par ordre croissant
    			if (($restrict_num)||($restrict_date)) $requete.=" ORDER BY date_date, bulletin_numero*1 ";
    			else $requete.=" ORDER BY date_date DESC, bulletin_numero*1 DESC";
    			$requete.=$limiter;
    			$res = @pmb_mysql_query($requete);
    			$count=pmb_mysql_num_rows($res);
    			if ($count) {
    				ob_start();
    				if ($opac_fonction_affichage_liste_bull) {
    				    eval("\$opac_fonction_affichage_liste_bull (\$res);");
    				} else {
    				    affichage_liste_bulletins_normale($res);
    				}
    				$resultat_aff.=ob_get_contents();
    				ob_end_clean();
    			} else {
    			    $resultat_aff.= "<strong>".$msg["bull_no_found"]."</strong>";
    			}
    			//$resultat_aff.= "<br />";		
    			
    			// constitution des liens
    			if (!$count1) $count1=$count;
    			$nbepages = ceil($count1/$bull_results_per_page);
    			$action = "show_perio($id);return false;";
    			$url_page = "javascript:changepage(!!page!!,$id, this)";
    			if ($nbepages>1) {
    			    $navBar = getNavbar($page, $count1, $bull_results_per_page, $url_page, '', '#');
    			    $navBar->setOnsubmit($action);
    			    $form = $navBar->getPaginatorPerio();
    			}
			}
		}
		return $resultat_aff.$form;
	}
	
	
	public function compare_date($date_debut="",$date_fin=""){
		$restrict = "";
		if($date_debut && $date_fin){
			if($date_fin<$date_debut){
				$restrict = " and date_date between '".$date_fin."' and '".$date_debut."' ";
			} else if($date_fin == $date_debut) {
				$restrict = " and date_date='".$date_debut."' ";
			} else {
				$restrict = " and date_date between '".$date_debut."' and '".$date_fin."' ";
			}
		} else if($date_debut){
			$restrict = " and date_date >='".$date_debut."' ";
		} else if($date_fin){
			$restrict = " and date_date <='".$date_fin."' ";
		}
		return $restrict;
	}
	
	public function get_external_sources_list(){
		$es_list = array();
		$query = "select source_id,parameters from connectors_sources where id_connector = 'es_list'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$source_params = unserialize($row->parameters);
				if($source_params['use_in_a2z']){
					$es_list[$row->source_id]=$source_params['es_selected'];
				}
			}
		}
		return $es_list;
	}
	
	public function extract_external_id($id){
		return substr($id,strrpos($id,"_")+1);
	}
	
	public function get_external_icon($id){
		$infos = explode("_",$id);
		$query = "select ico_notice,parameters from connectors_sources where source_id = ".$infos[1];
		$icon = "";
		$result =pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row =pmb_mysql_fetch_object($result);
			$params = unserialize($row->parameters);
			if($params['source_as_origine'] == 0){
				$icon = "<img src='./images/".$row->ico_notice."'/>";
			}else{
				$query="select ico_notice,name from connectors_sources join external_count on connectors_sources.source_id = external_count.source_id where rid = ".$infos[2];
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$row = pmb_mysql_fetch_object($result);
					$icon = "<img src='./images/".$row->ico_notice."'/>";
				}
			}
		}
		return $icon;
	}

} # fin de définition de la classe 


} # fin de déclaration