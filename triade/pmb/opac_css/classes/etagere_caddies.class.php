<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere_caddies.class.php,v 1.8 2023/11/30 10:43:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/acces.class.php");

class etagere_caddies {
	// propriétés
	public $idetagere ;
	public $etagere;
	public $caddies;
	public $restricts;
	
	// constructeur
	public function __construct($etagere_id=0) {
		$this->idetagere = intval($etagere_id);
		$this->getData();
	}
	
	public function getData() {
		$this->etagere = new etagere($this->idetagere);
		$this->caddies = array();
		if($this->idetagere) {
			$query = "SELECT caddie_id, etagere_caddie_filters FROM etagere_caddie where etagere_id='".$this->idetagere."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_object($result)) {
					$this->caddies[$row->caddie_id] = array(
							'id' => $row->caddie_id,
							'filters' => encoding_normalize::json_decode($row->etagere_caddie_filters, true)
					);
				}
			}
		}
	}
	
	public function set_properties_from_form() {
		
	}
	
	// ajout d'un item panier
	public function add_panier($item=0, $filters=array()) {
		if (!$item) return 0 ;
		$requete_compte = "select count(1) from etagere_caddie where etagere_id='".$this->idetagere."' and caddie_id='".$item."' ";
		$result_compte = pmb_mysql_query($requete_compte);
		$deja_item=pmb_mysql_result($result_compte, 0, 0);
		if (!$deja_item) {
			$requete = "insert into etagere_caddie set etagere_id='".$this->idetagere."', caddie_id='".$item."', etagere_caddie_filters='".encoding_normalize::json_encode($filters)."' ";
			pmb_mysql_query($requete);
		} else {
			$requete = "update etagere_caddie set etagere_caddie_filters='".encoding_normalize::json_encode($filters)."' where etagere_id='".$this->idetagere."' and caddie_id='".$item."' ";
			pmb_mysql_query($requete);
			return 0;
		}
		return 1 ;
	}
	
	// suppression d'un item panier
	public function del_item($item=0) {
		$requete = "delete FROM etagere_caddie where etagere_id='".$this->idcaddie."' and caddie_id='".$item."' ";
		pmb_mysql_query($requete);
	}
	
	// get_cart() : ouvre une étagère et récupère le contenu
	public function constitution($modif=1) {
		global $PMBuserid ;
		global $msg ;
		
		$liste = caddie::get_cart_list('NOTI');
		if (!empty($liste)) {
			$myCart = null;
			$print_cart = array();
			$parity = array();
			$ret = pmb_bidi("<div class='row'><a href='javascript:expandAll()'><img src='".get_url_icon('expand_all.gif')."' id='expandall' style='border:0px'></a>
				<a href='javascript:collapseAll()'><img src='".get_url_icon('collapse_all.gif')."' id='collapseall' style='border:0px'></a></div>");
			foreach ($liste as $valeur) {
				$rqt_autorisation=explode(" ",$valeur['autorisations']);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
					if(!is_object($myCart))$myCart = new caddie(0);
					$myCart->type=$valeur['type'];
					$print_cart[$myCart->type]["titre"]="<b>".$msg["caddie_de_".$myCart->type]."</b><br />";
					if(!trim($valeur["caddie_classement"])){
						$valeur["caddie_classement"]=classementGen::getDefaultLibelle();
					}
					$parity[$myCart->type]=1-(isset($parity[$myCart->type]) ? $parity[$myCart->type] : 0);
					if ($parity[$myCart->type]) $pair_impair = "even";
					else $pair_impair = "odd";
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
					
					$rowPrint= pmb_bidi("<tr class='$pair_impair' $tr_javascript >");
					$rowPrint.= pmb_bidi("<td style='text-align:right;'><input type=checkbox name=idcaddie[] value='".$valeur['idcaddie']."' class='checkbox' ");
					if (!empty($this->caddies[$valeur['idcaddie']])) $rowPrint .= pmb_bidi(" checked ");
					if (!$modif) $rowPrint .= pmb_bidi(" disabled='disabled' ");
					$rowPrint .= pmb_bidi(" />&nbsp;</td>");
					$rowPrint.= pmb_bidi("<td><a href='catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&idcaddie=".$valeur['idcaddie']."' target='_blank'/><span ".($valeur['favorite_color'] != '#000000' ? "style='color:".$valeur['favorite_color']."'" : "").">".$valeur['name']."</span>");
					$rowPrint.= pmb_bidi("</a></td>");
					$rowPrint.= pmb_bidi("<td>");
					$checked_elt_flag = ($this->get_filter($valeur['idcaddie'], 'elt_flag') ? "checked='checked'" : "");
					$rowPrint.= pmb_bidi("<input type='checkbox' name='filters[".$valeur['idcaddie']."][elt_flag]' value='1' class='checkbox' ".$checked_elt_flag."/> ".$msg['caddie_item_marque']);
					$checked_elt_no_flag = ($this->get_filter($valeur['idcaddie'], 'elt_no_flag') ? "checked='checked'" : "");
					$rowPrint.= pmb_bidi("&nbsp;<input type='checkbox' name='filters[".$valeur['idcaddie']."][elt_no_flag]' value='1' class='checkbox' ".$checked_elt_no_flag."/> ".$msg['caddie_item_NonMarque']);
					$rowPrint.= pmb_bidi("</td>");
					$rowPrint.=  pmb_bidi("</tr>");
			
					$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["titre"] = stripslashes($valeur["caddie_classement"]);
					if(!isset($print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"])) {
						$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] = '';
					}
					$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] .= $rowPrint;
				}
			}
	
			//Tri des classements
			foreach($print_cart as $key => $cart_type) {
				ksort($print_cart[$key]["classement_list"]);
			}
			// affichage des paniers par type
			foreach($print_cart as $key => $cart_type) {
				//on remplace les clés à cause des accents
				$cart_type["classement_list"]=array_values($cart_type["classement_list"]);
				$contenu="";
				foreach($cart_type["classement_list"] as $keyBis => $cart_typeBis) {
					$contenu.=gen_plus($key.$keyBis,$cart_typeBis["titre"],"<table style='border:0px; border-spacing: 0px; width: 100%' class='classementGen_tableau'><tr><th style='text-align:right;' class='classement20'>".$msg['etagere_caddie_inclus']."</th><th class='classement40'>".$msg['caddie_name']."</th><th>".$msg['etagere_caddie_filters']."</th></tr>".$cart_typeBis["cart_list"]."</table>",1);
				}
				$ret .= gen_plus($key,$cart_type["titre"],$contenu,1);
			}
		} else {
			$ret = $msg['398'];
		}
		
		return $ret;
	}
	
	public function get_caddies() {
		return $this->caddies;
	}
	
	public function get_filter($idcaddie, $name) {
		if(!empty($this->caddies[$idcaddie]['filters'][$name])) {
			return $this->caddies[$idcaddie]['filters'][$name];
		}
		return '';
	}
		
	public function is_visible_element($caddie_id, $flag=NULL) {
		$elt_flag = $this->get_filter($caddie_id, 'elt_flag');
		$elt_no_flag = $this->get_filter($caddie_id, 'elt_no_flag');
		//Est-ce qu'il y a une règle de filtrage ?
		if($elt_flag && !$elt_no_flag) {
			//Seulement les pointés
			if(empty($flag)) {
				return false;
			}
		} elseif($elt_no_flag && !$elt_flag) {
			//Seulement les non pointés
			if(!empty($flag)) {
				return false;
			}
		}
		return true;
	}
	
	public function init_restricts() {
		global $gestion_acces_active, $gestion_acces_empr_notice;
		
		if(!isset($this->restricts)) {
			$this->restricts = array();
			$this->restricts['acces_j']='';
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
				$this->restricts['acces_j'] = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
			}
			if($this->restricts['acces_j']) {
				$this->restricts['statut_j']='';
				$this->restricts['statut_r']='';
			} else {
				$this->restricts['statut_j']=',notice_statut';
				$this->restricts['statut_r']="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			}
			if(!empty($_SESSION["opac_view"]) && $_SESSION["opac_view"]  && !empty($_SESSION["opac_view_query"]) && $_SESSION["opac_view_query"] ){
				$opac_view_restrict=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
				$this->restricts['statut_r'].=" and ".$opac_view_restrict;
			}
		}
		return $this->restricts;
	}
	
	public function get_sort_query($query='', $sort_name="notices") {
		global $opac_etagere_notices_order;
		
		if (isset($_SESSION["last_sort$sort_name"]) && $_SESSION["last_sort$sort_name"]!=="") {
			$sort = new sort($sort_name, 'session');
			$query = $sort->appliquer_tri($_SESSION["last_sort$sort_name"], $query, "notice_id");
		} elseif(!empty($this->etagere->id_tri)) {
		    $sort = new sort($sort_name, 'base');
		    $query = $sort->appliquer_tri($this->etagere->id_tri, $query, "notice_id");
		} else {
		    if ($opac_etagere_notices_order) {
		        $query .= "order by ".$opac_etagere_notices_order;
		    } else {
		        $query .= "order by index_serie, tit1";
		    }
		}
		return $query;
	}
	
	public function get_sorted_filtered_notices($notices) {
		$query = "select notice_id from notices where notice_id IN (".implode(',', $notices).")";
		$query = $this->get_sort_query($query);
		$result = pmb_mysql_query($query);
		$sorted_filtered_notices = array();
		if(pmb_mysql_num_rows($result)) {
			while(($obj=pmb_mysql_fetch_object($result))) {
				$sorted_filtered_notices[] = $obj->notice_id;
			}
		}
		return $sorted_filtered_notices;
	}
	
	public function get_notices($start=0, $nb_per_page=0) {
		$notices = array();
		$this->init_restricts();
		$query = "select distinct notice_id, caddie_content.caddie_id, caddie_content.flag from caddie_content, etagere_caddie, notices ".$this->restricts['acces_j']." ".$this->restricts['statut_j']." ";
		$query.= "where etagere_id=".$this->idetagere." and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id ".$this->restricts['statut_r']." ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while(($obj=pmb_mysql_fetch_object($result))) {
				//Est-ce qu'il y a une règle de filtrage ?
				if($this->is_visible_element($obj->caddie_id, $obj->flag)) {
					$notices[] = $obj->notice_id;
				}
			}
		}
		if(count($notices)) {
			$notices = $this->get_sorted_filtered_notices($notices);
		}
		if($nb_per_page) {
			$notices = array_slice($notices, $start, $nb_per_page);
		}
		return $notices;
	}
	
	public function get_notices_count() {
		$this->init_restricts();
		$notices = $this->get_notices();
		return count($notices);
	}
	
	public function get_typdocs() {
		$typdocs = array();
		$this->init_restricts();
		$requete = "select distinct typdoc, caddie_content.caddie_id, caddie_content.flag FROM caddie_content, etagere_caddie, notices ".$this->restricts['acces_j']." ".$this->restricts['statut_j']." ";
		$requete.= "where etagere_id=".$this->idetagere." and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id ".$this->restricts['statut_r']." ";
		$res = pmb_mysql_query($requete);
		if ($res) {
			while ($tpd=pmb_mysql_fetch_object($res)) {
				if($this->is_visible_element($tpd->caddie_id, $tpd->flag)) {
					$typdocs[]=$tpd->typdoc;
				}
			}
		}
		return $typdocs;
	}
	
	public function get_notices_from_query($query, $with_filters=false) {
		$notices = '';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$tab_notices=array();
			while($row=pmb_mysql_fetch_object($result)) {
				if($this->is_visible_element($row->caddie_id, $row->flag)) {
					$tab_notices[]=$row->notice_id;
				}
			}
			$notices=implode(',',$tab_notices);
		}
		if($notices && $with_filters) {
			$fr = new filter_results($notices);
			$notices = $fr->get_results();
		}
		return $notices;
	}
	
	public function show_tris($sort_name="notices") {
		global $msg;
		global $opac_nb_max_tri;
		
		$display = '';
		$notices_count = $this->get_notices_count();
		if ($notices_count<=$opac_nb_max_tri) {
			$end_html = '';
			if((isset($_SESSION["last_sort$sort_name"]) && $_SESSION["last_sort$sort_name"]!=="") || !empty($this->etagere->id_tri)) { //Encapsulation des éléments de tri dans un container pour faciliter le style
				$display.= '<span class="triContainer">';
				$end_html = '</span>';
			}
			$display.=  sort::get_display_tris_selector($notices_count, $sort_name);
			if (isset($_SESSION["last_sort$sort_name"]) && $_SESSION["last_sort$sort_name"]!=="") {
				$sort = new sort($sort_name, 'session');
				$display.=  "<span class='sort'>".$msg['tri_par']." ".$sort->descriptionTriParId($_SESSION["last_sort$sort_name"])."<span class=\"espaceResultSearch\">&nbsp;</span></span>";
			} elseif(!empty($this->etagere->id_tri)) {
				$sort = new sort($sort_name, 'base');
				$display.= "<span class='sort'>".$msg['tri_par']." ".$sort->descriptionTriParId($this->etagere->id_tri)."<span class=\"espaceResultSearch\">&nbsp;</span></span>";
			}
			$display.= $end_html;
		} else {
			$display.=  "<span class=\"espaceResultSearch\">&nbsp;</span>";
		}
		return $display;
	}
}
 