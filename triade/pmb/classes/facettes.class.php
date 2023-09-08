<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes.class.php,v 1.15 2019-05-16 12:54:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/acces.class.php");
require_once($class_path."/facettes_root.class.php");
require_once($class_path."/notice.class.php");

class facettes extends facettes_root {
	
	/**
	 * Nom de la table bdd
	 * @var string
	 */
	public static $table_name = 'facettes';
	
	/**
	 * Mode d'affichage (extended/external)
	 * @var string
	 */
	public $mode = 'extended';
	
	/**
	 * Nom de la classe de comparaison
	 */
	protected static $compare_class_name = 'facette_search_compare';
	
	public function __construct($objects_ids = ''){
		parent::__construct($objects_ids);
	}
		
	protected function get_query_by_facette($id_critere, $id_ss_critere) {
		global $lang;
		
		$query = "select value ,count(distinct id_notice) as nb_result from (SELECT value,id_notice FROM notices_fields_global_index
					WHERE id_notice IN (".$this->objects_ids.")
					AND code_champ = ".($id_critere+0)."
					AND code_ss_champ = ".($id_ss_critere+0)."
					AND lang in ('','".$lang."','".substr($lang,0,2)."')) as sub
					GROUP BY value
					ORDER BY ";
		return $query;
	}
	
	public static function get_facette_wrapper(){
		$script = parent::get_facette_wrapper();
		$script .= "
		<script type='text/javascript'>
			function valid_facettes_multi(){
				//on bloque si aucune case cochée
				var form = document.facettes_multi;
				for (i=0, n=form.elements.length; i<n; i++){
					if ((form.elements[i].checked == true)) {
						if(document.getElementById('filtre_compare_facette')) {
							document.getElementById('filtre_compare_facette').value='filter';
						}
						if(document.getElementById('filtre_compare_form_values')) {
							document.getElementById('filtre_compare_form_values').value='filter';
						}
						form.submit();
						return true;
					}
				}
				return false;
			}
		</script>";
		return $script;
	}
	
	public static function make_facette_search_env() {
		global $search;
		global $op_0_s_1;
		global $field_0_s_1;
		
		//historique des recherches
		if(empty($search)) {
			$search = array();
		}
		$search[] = "s_1";
		$op_0_s_1 = "EQ";
		$field_0_s_1[] = $_SESSION['last_query']+0; 
		
		//creation des globales => parametres de recherche
		if ($_SESSION['facette']) {
			for ($i=0;$i<count($_SESSION['facette']);$i++) {
				$search[] = "s_3";
		    	$field = "field_".($i+1)."_s_3";
		    	$field_=array();
    			$field_ = $_SESSION['facette'][$i];
    			global ${$field};
    			${$field} = $field_;
    			
		    	$op = "op_".($i+1)."_s_3";
		    	$op_ = "EQ";
    			global ${$op};
    			${$op}=$op_;
    		    
    			$inter = "inter_".($i+1)."_s_3";
    			$inter_ = "and";
    			global ${$inter};
    			${$inter} = $inter_;
			}
		}
	}

	protected static function get_link_delete_clicked($id, $facettes_nb_applied) {
// 		if ($facettes_nb_applied==1) {
// 			$link = "document.location=\"".static::format_url('lvl=more_results&get_last_query=1&reinit_facette=1')."\";";
// 		} else {
// 			$link = "document.location=\"".static::format_url('lvl=more_results&mode=extended&facette_test=1&param_delete_facette='.$id)."\";";
// 		}
		return $link;
	}
	
	protected static function get_link_not_clicked($name, $label, $code_champ, $code_ss_champ, $id, $nb_result) {
// 		$link =  "document.location=\"".static::format_url("lvl=more_results&mode=extended&facette_test=1");
// 		$link .= "&name=".rawurlencode($name)."&value=".rawurlencode($label)."&champ=".$code_champ."&ss_champ=".$code_ss_champ."\";";
		return $link;
	}
	
	protected static function get_link_reinit_facettes() {
// 		$link =  "document.location=\"".static::format_url("lvl=more_results&get_last_query=1&reinit_facette=1")."\";";
		return $link;
	}
	
	protected static function get_link_back($reinit_compare=false) {
//		$link =  "document.location.href=\"".static::format_url("lvl=more_results&get_last_query=1".($reinit_compare ? "&reinit_compare=1" : ""))."\"";
		return $link;
	}
	
	public static function get_session_values() {
		if(!isset($_SESSION['facette'])) $_SESSION['facette'] = array();
		return $_SESSION['facette'];
	}
	
	public static function set_session_values($session_values) {
		$_SESSION['facette'] = $session_values;
	}
	
	public static function delete_session_value($param_delete_facette) {
		if(isset($_SESSION['facette'][$param_delete_facette])){
			unset($_SESSION['facette'][$param_delete_facette]);
			$_SESSION['facette'] = array_values($_SESSION['facette']);
		}
	}
	
	public static function expl_voisin($id_notice=0){
		global $charset,$msg;
		$data=array();
		$notices_list = facettes::get_expl_voisin($id_notice);
		$display=static::aff_notices_list($notices_list);
		$data['aff']="";
		if($display)$data['aff']= "<h3 class='avis_detail'>".$msg['expl_voisin_search']."</h3>".$display;
		if ($charset!="utf-8") $data['aff']= utf8_encode($data['aff']);
		$data['id']=$id_notice;
		return $data;
	}	
		
	public static function get_expl_voisin($id_notice=0){
		global $dbh;
		global $opac_nb_notices_similaires;
		
		$id_notice+=0;
		$notice_list=array();	
		$req = "select expl_cote from exemplaires where expl_notice=$id_notice";
		$res = @pmb_mysql_query($req,$dbh);
		
		$nb_result = $opac_nb_notices_similaires;
		if($nb_result>6 || $nb_result<0 || !(isset($opac_nb_notices_similaires))){
			$nb_result=6;
		}
		$nb_asc="";
		$nb_desc="";
		if(($nb_result%2)==0){
			$nb_asc = $nb_result/2;
			$nb_desc = $nb_asc;
		} else {
			$nb_desc = $nb_result%2;
			$nb_asc = $nb_result-$nb_desc;
		}		
		
		if($res && pmb_mysql_num_rows($res)){
			$r=pmb_mysql_fetch_object($res);
			$cote=$r->expl_cote;			
			$query = "
			(select distinct expl_notice from exemplaires where expl_notice!=0 and expl_cote >= '".$cote."' and expl_notice!=$id_notice order by expl_cote asc limit ".$nb_asc.")
				union 
			(select distinct expl_notice from exemplaires where expl_notice!=0 and expl_cote < '".$cote."' and expl_notice!=$id_notice  order by expl_cote desc limit ".$nb_desc.")" ;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result) > 0){				
				while($row = pmb_mysql_fetch_object($result)){
					$notice_list[] = $row->expl_notice;
				}
			}			
		}	
		return $notice_list;
	}	
			
	
	public static function similitude($id_notice=0){
		global $charset,$msg;
		$data=array();
		$notices_list = facettes::get_similitude_notice($id_notice);
		$display= static::aff_notices_list($notices_list);		
		$data['aff']="";
		if($display)$data['aff']= "<h3 class='avis_detail'>".$msg['simili_search']."</h3>".$display;
		if ($charset!="utf-8") $data['aff']= utf8_encode($data['aff']);
		$data['id']=$id_notice;
		return $data;
	}
	
	public static function get_similitude_notice($id_notice=0){
		global $dbh;
		global $opac_nb_notices_similaires;
		global $gestion_acces_active,$gestion_acces_empr_notice;
		
		$id_notice+=0;
		$req="select distinct code_champ, code_ss_champ, num_word from notices_mots_global_index where	(
				code_champ in(1,17,19,20,25) 
 			)and
			id_notice=$id_notice";
		/*27,28,29
 				or (code_champ=90 and code_ss_champ=2)
				or (code_champ=90 and code_ss_champ=3)
	 			or (code_champ=90 and code_ss_champ=4) 
		 */
		// 7337 43421
		
		$res=pmb_mysql_query($req,$dbh);
		$where_mots="";
		$notice_list=array();
		if($res && pmb_mysql_num_rows($res)){
			while($r=pmb_mysql_fetch_object($res)){
				if($where_mots)$where_mots.=" or ";
				$where_mots.="(code_champ =".$r->code_champ." AND code_ss_champ =".$r->code_ss_champ." AND num_word =".$r->num_word." and id_notice != ".$id_notice.")";
			}
		}
		if($where_mots){
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
			}
			$nb_result = $opac_nb_notices_similaires;
			if($nb_result>6 || $nb_result<0 || !(isset($opac_nb_notices_similaires))){
				$nb_result=6;
			}
			$req = "select id_notice, sum(pond) as s from notices_mots_global_index where $where_mots group by id_notice order by s desc limit ".$nb_result;
			$res = @pmb_mysql_query($req,$dbh);		
			if($res && pmb_mysql_num_rows($res)){
				while($r=pmb_mysql_fetch_object($res)){
					if($r->s >80){
						$acces_v=TRUE;
						if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
							$acces_v = $dom_2->getRights($_SESSION['id_empr_session'],$r->id_notice,4);
						} else {
							$requete = "SELECT notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$r->id_notice."' and id_notice_statut=statut ";
							$myQuery = pmb_mysql_query($requete, $dbh);
							if($myQuery && pmb_mysql_num_rows($myQuery)) {
								$statut_temp = pmb_mysql_fetch_object($myQuery);
								if(!$statut_temp->notice_visible_opac)	$acces_v=FALSE;
								if($statut_temp->notice_visible_opac_abon && !$_SESSION['id_empr_session'])	$acces_v=FALSE;
							} else 	$acces_v=FALSE;
						}
						if($acces_v){
							$notice_list[] = $r->id_notice;
						}
					}
				}
			}
		}
		return $notice_list;
	}
	
	protected static function aff_notices_list($notices_list){
		global $dbh,$charset;
		global $opac_show_book_pics,$opac_book_pics_url,$opac_book_pics_msg,$opac_url_base;
		global $opac_notice_affichage_class,$gestion_acces_active,$gestion_acces_empr_notice;
		global $opac_notice_reduit_format_similaire ;
		
		$img_list = "";
		$title_list = "";
		
		$tabNotice = array();
		
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {				
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
		}
		$i = 0;
		foreach($notices_list as $notice_id){		
			$acces_v=TRUE;	
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {	
				$acces_v = $dom_2->getRights($_SESSION['id_empr_session'],$notice_id,4);
			} else {
				$requete = "SELECT notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$notice_id."' and id_notice_statut=statut ";
				$myQuery = pmb_mysql_query($requete, $dbh);
				if($myQuery && pmb_mysql_num_rows($myQuery)) {
					$statut_temp = pmb_mysql_fetch_object($myQuery);
					if(!$statut_temp->notice_visible_opac)	$acces_v=FALSE;
					if($statut_temp->notice_visible_opac_abon && !$_SESSION['id_empr_session'])	$acces_v=FALSE;
				} else 	$acces_v=FALSE;
			}
			if(!$acces_v) continue;
			
			$req = "select * from notices where notice_id=$notice_id";			
			$res = @pmb_mysql_query($req,$dbh);
			if($r=pmb_mysql_fetch_object($res)){
				$image="";
				
				if (substr($opac_notice_reduit_format_similaire,0,1)!="H" && $opac_show_book_pics=='1') {
					$image="<a href='".$opac_url_base."index.php?lvl=notice_display&id=".$notice_id."'>"."<img class='vignetteimg_simili' src='".notice::get_picture_url_no_image($r->niveau_biblio, $r->typdoc)."' hspace='4' vspace='2'></a>";
					
					$url_image_ok="";
					if ($r->thumbnail_url || ($r->code && $opac_book_pics_url)) {
						$url_image_ok=getimage_url($r->code, $r->thumbnail_url);
					}
					
					if ($r->thumbnail_url) {
						$title_image_ok="";
						$image = "<a href='".$opac_url_base."index.php?lvl=notice_display&id=".$notice_id."'>"."<img class='vignetteimg_simili' src='".$url_image_ok."' title=\"".$title_image_ok."\" hspace='4' vspace='2'>"."</a>";
					} elseif($r->code && $opac_book_pics_url){
						$title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
						$image = "<a href='".$opac_url_base."index.php?lvl=notice_display&id=".$notice_id."'>"."<img class='vignetteimg_simili' src='".$url_image_ok."' title=\"".$title_image_ok."\" hspace='4' vspace='2'>"."</a>";
					}				
				}		
				$notice = new $opac_notice_affichage_class($notice_id, "", 0,0,1);	
				$notice->do_header_similaire();				
				$notice_header= "<a href='".$opac_url_base."index.php?lvl=notice_display&id=".$notice_id."'>".$notice->notice_header."</a>";		
				$i++;
			}			
			
			// affichage du titre et de l'image dans la même cellule
			if($image!=""){
				$img_list.="<td class='center'>".$image."<br />".$notice_header."</td>";
			} else {
				$img_list.="<td class='center'>".$notice_header."</td>";
			}		
			
		}
		if(!$i)return"";		
		$display="<table style='table-layout:fixed;width:100%'><tr>".$img_list."</tr></table>";		
		
		return $display;
	}
	
	/**
	 * Retourne le template de facettes
	 * @param string $query
	 */
	public static function get_display_list_from_query($query) {
		global $opac_facettes_ajax;
		
		$display = '';
		$objects = '';
		$result = pmb_mysql_query($query);
		if($result) {
			while($row = pmb_mysql_fetch_object($result)){
				if($objects){
					$objects.=",";
				}
				$objects.= $row->notice_id;
			}
		}
		if(!$opac_facettes_ajax){
			$display .= facettes::make_facette($objects);
		}else{
			$_SESSION['tab_result']=$objects;
			$display .= static::call_ajax_facettes();
		}
		//Formulaire "FACTICE" pour l'application du comparateur et du filtre multiple...
		if($display) {
			$display.= '
			<form name="form_values" style="display:none;" method="post" action="'.static::format_url('lvl=more_results&mode=extended').'">
				<input type="hidden" name="from_see" value="1" />
				'.facette_search_compare::form_write_facette_compare().'
			</form>';
		}
		return $display;
	}
	
	public static function get_formatted_value($id_critere, $id_ss_critere, $value) {
		return get_msg_to_display($value);
	}
}// end class
