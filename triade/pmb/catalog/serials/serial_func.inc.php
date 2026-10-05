<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_func.inc.php,v 1.111 2023/07/26 15:07:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path;
require_once("$class_path/docs_location.class.php");
require_once ($include_path."/avis_notice.inc.php");
require_once($include_path.'/h2o/pmb_h2o.inc.php');
require_once($class_path.'/records_tabs.class.php');
require_once($class_path.'/notice.class.php');
require_once ($class_path."/caddie/caddie_controller.class.php");
require_once($class_path."/abts_modeles.class.php");
require_once($class_path."/abts_abonnements.class.php");

// résultat de recherche pour gestion des périodiques
function show_serial_info($serial_id, $page, $nbr_lignes) {
	global $serial_action_bar;
	global $msg;
	global $nb_per_page_a_search;
	global $charset;
	global $deflt_collstate_location,$deflt_bulletinage_location,$location;
	global $pmb_etat_collections_localise,$pmb_droits_explr_localises,$explr_invisible,$explr_visible_unmod;
	// barre de restriction des bulletins affichés
	global $aff_bulletins_restrict_numero, $aff_bulletins_restrict_date, $aff_bulletins_restrict_periode ;
	global $sort_children;
	global $pmb_url_base, $categ, $sub, $quoi, $view, $tab_page, $tab_nb_per_page;
	global $bull_date_start,$bull_date_end;
	global $current;
	
	
	if($view == "collstate"){
	    if ($pmb_etat_collections_localise) {
	        global $id;
	        if((isset($id) && $id) && $deflt_collstate_location === "0"){//Affiche tous les états de collection après création/modification
	            $location=$deflt_collstate_location;
	        }else{
	            $location=((string)$location==""?$deflt_collstate_location:$location);
	        }
	    }
	}else{
        $location=((string)$location==""?$deflt_bulletinage_location:$location);
    }
	
	$url_suffix = "";
	if ($quoi) $url_suffix .= "&quoi=".$quoi;
	if ($tab_page) $url_suffix .= "&tab_page=".$tab_page."&tab_nb_per_page=".$tab_nb_per_page;
	// lien d'ajout d'une notice mère à un caddie
	$cart_click_noti = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=!!item!!', 'cart')\"";
	$cart_link = "<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click_noti>";
	
	if ($current!==false) {
		$print_action = "&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&notice_id=".$serial_id."&action_print=print_prepare','print'); w.focus(); return false;\"><img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
	}
	
	$visualise_click_notice="
	<script type=\"text/javascript\" src='./javascript/select.js'></script>
	
	<a href='#' onClick='show_frame(\"".$pmb_url_base."opac_css/notice_view.php?id=$serial_id\")'><img src='".get_url_icon('search.gif')."' class='align_middle' title=\"{$msg["noti_see_gestion"]}\" style='border:0px' /></a>";
	 
	$base_url = serial::get_permalink($serial_id);
	$serial_action_bar = str_replace('!!serial_id!!', $serial_id, $serial_action_bar);
	if ($serial_id) $myQuery = pmb_mysql_query("SELECT * FROM notices WHERE notice_id=$serial_id ");
	
	if ($serial_id && pmb_mysql_num_rows($myQuery)) {
		//Bulletins
		$myPerio = pmb_mysql_fetch_object($myQuery);
		// function serial_display ($id, $level='1', $action_serial='', $action_analysis='', $action_bulletin='', $lien_suppr_cart="", $lien_explnum="", $bouton_explnum=1,$print=0,$show_explnum=1, $show_statut=0, $show_opac_hidden_fields=true ) {
		$isbd = new serial_display($myPerio, 5, "",                      "",                 "",                  "",                  "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!");
		$perio_header = $isbd->header;
	
		// isbd du périodique
		$perio_isbd = $isbd->isbd;
		$isbd->get_etat_periodique();
		$perio_isbd.=$isbd->print_etat_periodique();
		
		global $avis_quoifaire,$valid_id_avis;
		$perio_isbd = str_replace('<!-- !!avis_notice!! -->', avis_notice($serial_id,$avis_quoifaire,$valid_id_avis), $perio_isbd);
	
		$perio_isbd = str_replace('<!-- !!caddies_notice!! -->', caddie_controller::get_display_list_from_item('display', 'NOTI', $serial_id), $perio_isbd);
		
		if (!$page) $page=1;
		$debut = ($page-1)*$nb_per_page_a_search;
		switch ($view) {
			case "abon":
				$base_url = serial::get_permalink($serial_id)."&view=abon".$url_suffix;
				
				$abonnements=new abts_abonnements($serial_id,$location);
				
				$bulletins= "<form action='".$base_url."' method='post' name='filter_form'><input type='hidden' name='location' value='$location'/>" ;
				$bulletins.=$abonnements->show_list();
				$bulletins.= "</form>" ;
				break;
			case "modele":
				$modeles=new abts_modeles($serial_id);
				$bulletins=$modeles->show_list();
				break;
			case "collstate":
				$base_url = serial::get_permalink($serial_id)."&view=collstate".$url_suffix;
				
				if($pmb_etat_collections_localise) {
					$list_collstate_ui = new list_collstate_ui(array('serial_id' => $serial_id, 'bulletin_id' => 0, 'location' => $location));
				} else {
					$list_collstate_ui = new list_collstate_ui(array('serial_id' => $serial_id, 'bulletin_id' => 0));
				}
				$bulletins = $list_collstate_ui->get_display_list();
				
				$pages_display = $list_collstate_ui->get_collstate_pagination();
				break;				
			default:
				// barre de restriction des bulletins affichés
				$clause="";
				if ($aff_bulletins_restrict_numero) {
					$clause = " and bulletin_numero like '%".str_replace("*","%",$aff_bulletins_restrict_numero)."%' ";
					$base_url .= "&aff_bulletins_restrict_numero=".urlencode($aff_bulletins_restrict_numero) ;
				}			
				if ($aff_bulletins_restrict_date) {
					$aff_bulletins_restrict_date_traite = str_replace("*","%",$aff_bulletins_restrict_date) ;
					$tab_bulletins_restrict_date = explode ($msg['format_date_input_separator'],$aff_bulletins_restrict_date_traite) ;
					if(count($tab_bulletins_restrict_date)==3)$aff_bulletins_restrict_date_traite = $tab_bulletins_restrict_date[2]."-".$tab_bulletins_restrict_date[1]."-".$tab_bulletins_restrict_date[0];
					if(count($tab_bulletins_restrict_date)==2)$aff_bulletins_restrict_date_traite = $tab_bulletins_restrict_date[1]."-".$tab_bulletins_restrict_date[0];
					if(count($tab_bulletins_restrict_date)==1)$aff_bulletins_restrict_date_traite = $tab_bulletins_restrict_date[0];
					$clause .= " and date_date like '%".$aff_bulletins_restrict_date_traite."%'" ;
					$base_url .= "&aff_bulletins_restrict_date=".urlencode($aff_bulletins_restrict_date) ;
				}
				if ($aff_bulletins_restrict_periode) {
					$aff_bulletins_restrict_periode_traite = str_replace("*","%",$aff_bulletins_restrict_periode) ;
					$clause .= " and mention_date like '%".$aff_bulletins_restrict_periode_traite."%'" ;
					$base_url .= "&aff_bulletins_restrict_periode=".urlencode($aff_bulletins_restrict_periode) ;
				}
				$base_url .= $url_suffix;

				$filter_date=compare_date($bull_date_start,$bull_date_end);
				//On compte les expl de la localisation
// 				$rqt="SELECT COUNT(1) FROM bulletins ".($location?", exemplaires":"")." WHERE ".($location?"(expl_bulletin=bulletin_id and expl_location='$location' or expl_location is null) and ":"")." bulletin_notice='$serial_id' $filter_date ";
// 				$myQuery = pmb_mysql_query($rqt);
// 				$nb_expl_loc = pmb_mysql_result($myQuery,0,0);
		
				//On compte les bulletinsà afficher
				$rqt="SELECT count(distinct bulletin_id) FROM bulletins ".($location?", exemplaires":"")." WHERE ".($location?"(expl_bulletin=bulletin_id and expl_location='$location' or expl_location is null) and ":"")." bulletin_notice='$serial_id' $clause $filter_date ";
				$myQuery = pmb_mysql_query($rqt);
				$nbr_lignes = pmb_mysql_result($myQuery,0,0);
				
				require_once("views/view_bulletins.inc.php");
				break;
		}
		
		// Gestion de la supression de la notice si les droits de modification des exemplaires sont localisés.  	
		$flag_no_delete_notice=0;
		//visibilité des exemplaires
		if ($pmb_droits_explr_localises) {
			global $explr_visible_mod;
			$explr_tab_modif=explode(",",$explr_visible_mod);			
			$requete = "SELECT expl_location from exemplaires, bulletins,notices where
				expl_bulletin=bulletin_id and bulletin_notice=notice_id and notice_id= $serial_id";			
			$execute_query=pmb_mysql_query($requete);
			if ($execute_query&&pmb_mysql_num_rows($execute_query)) {
				while ($r=pmb_mysql_fetch_object($execute_query)) {
					if(!in_array ($r->expl_location,$explr_tab_modif )) $flag_no_delete_notice=1;
				}			
			}
		}
		if(!$flag_no_delete_notice)$serial_action_bar = str_replace('!!delete_serial_button!!', "<input type='button' class='bouton' onclick=\"confirm_serial_delete();\" value='$msg[63]' />", $serial_action_bar);
		else $serial_action_bar=str_replace('!!delete_serial_button!!', "", $serial_action_bar);
		$serial_action_bar = str_replace('!!issn!!', $myPerio->code, $serial_action_bar);
	  	
		// action_bar : serials.tpl.php...
	  	// mise à jour des info du javascript	  	
	  	$serial_action_bar = str_replace('!!nb_bulletins!!', $isbd->serial_nb_bulletins, $serial_action_bar);
	  	$serial_action_bar = str_replace('!!nb_articles!!', $isbd->serial_nb_articles, $serial_action_bar);
	  	$serial_action_bar = str_replace('!!nb_expl!!', $isbd->serial_nb_exemplaires, $serial_action_bar);
	  	$serial_action_bar = str_replace('!!nb_etat_coll!!', $isbd->serial_nb_etats_collection, $serial_action_bar);
	  	$serial_action_bar = str_replace('!!nb_abo!!', $isbd->serial_nb_abo_actif, $serial_action_bar);

	  	// Titre de la page
	  	print '<script type="text/javascript">document.title = "'.addslashes(pmb_bidi(strip_tags($isbd->notice->tit1))).'";</script>';
	  	
	    // titre général du périodique
	  	print pmb_bidi("
	  			<div class='row'>
	  				<div class='notice-perio'>
						<h3 style='display: inline;' class='notice-perio-title'>".$isbd->aff_statut.str_replace('!!item!!', $serial_id, $cart_link).$print_action.$visualise_click_notice." ".$perio_header."</h3>
        				<div class='row'>".$perio_isbd."</div>
        				<hr />
        				<div class='row'>".$serial_action_bar."</div>
	        		</div>
	        	</div>");
		
		// bulletinage
		$onglets = "
		<div id='content_onglet_perio'>
			<span class='".((!$view)?"onglet-perio-selected'>":"onglets-perio'>")."<a href=\"#\" onClick=\"document.location='".serial::get_permalink($serial_id).$url_suffix."'\">".$msg["abts_onglet_bull"]."</a></span>
			<span class='".(($view=="abon")?"onglet-perio-selected'>":"onglets-perio'>")."<a href=\"#\" onClick=\"document.location='".serial::get_permalink($serial_id)."&view=abon".$url_suffix."'\">".$msg["abts_onglet_abt"]."</a></span>
			<span class='".(($view=="modele")?"onglet-perio-selected'>":"onglets-perio'>")."<a href=\"#\"  onClick=\"document.location='".serial::get_permalink($serial_id)."&view=modele".$url_suffix."'\">".$msg["abts_onglet_modele"]."</a></span>
			<span class='".(($view=="collstate")?"onglet-perio-selected'>":"onglets-perio'>")."<a href=\"#\"  onClick=\"document.location='".serial::get_permalink($serial_id)."&view=collstate".$url_suffix."'\">".$msg["abts_onglet_collstate"]."</a></span>
		</div>
		";
		print $onglets;
		
		$totaux_loc="";
		$temp_location=0;
		$list_locs="";
		
		switch($view) {
			case "modele":
				$list_locs="";
				$link_bulletinage = "";
				break;
			case "abon":
				if ($location) $temp_location=$location;
				$list_locs=docs_location::gen_combo_box_empr($temp_location,1,"document.filter_form.location.value=this.options[this.selectedIndex].value; document.filter_form.submit();");
				$link_bulletinage = "<a href='./catalog.php?categ=serials&sub=pointage&serial_id=$serial_id&location_view=$location'>".$msg["link_notice_to_bulletinage"]."</a>"; 				
				break;
			case "collstate":
				if($pmb_etat_collections_localise) {
					if (($location)) $temp_location=$location;
					$list_locs=docs_location::gen_combo_box_empr($temp_location,1,"document.filter_form.location.value=this.options[this.selectedIndex].value; document.filter_form.submit();");
				}				
				$link_bulletinage = "<input type='button' class='bouton' value='".$msg["collstate_add_collstate"]."' 
				onClick=\"document.location='./catalog.php?categ=serials&sub=collstate_form&serial_id=$serial_id&id=';\">";				
				break;
			default:
				$link_bulletinage = "";
				/*if($nb_bull_loc) {
					if($temp_location && $list_locs) {
						$totaux_loc="<strong>$nb_bull_loc</strong> ".$msg["serial_nb_bulletin"]."
						<strong>$nb_expl_loc</strong> ".$msg["bulletin_nb_ex"];
					}
				}*/
				break;			
		}	
		if(empty($view)) {
			print pmb_bidi("
			<div class='bulletins-perio'>
				<div class='row'>
					<div class='center'>
						".$pages_display."
					</div>
				</div>
				<div class='row'>
					$bulletins
				</div>
			</div>");
		} else {
			print pmb_bidi("
			<div class='bulletins-perio'>
				<div class='row'>
					<h3>".($view=="abon"?$msg["perio_abts_title"]:($view=="modele"?$msg["perio_modeles_title"]:($view=="collstate"?$msg["abts_onglet_collstate"]:"")))."&nbsp;$list_locs
					$link_bulletinage
					</h3>
					$totaux_loc
				</div>
				<div class='row'>
					<div class='center'>
						".($view!="abon" && $view!="modele" ? $pages_display : "")."
					</div>
				</div>
				<div class='row'>
					$bulletins
				</div>
			</div>");
		}
		$template_path_serial_tabs =  "./includes/templates/records/records_elements_tabs.html";
		if(file_exists("./includes/templates/records/records_elements_tabs_subst.html")){
			$template_path_serial_tabs =  "./includes/templates/records/records_elements_tabs_subst.html";
		}
		if(file_exists($template_path_serial_tabs)){
			$h2o_serial_tabs = H2o_collection::get_instance($template_path_serial_tabs);
			$records_tabs = new records_tabs(new notice($isbd->notice_id));
			$records_list_ui = $records_tabs->get_record()->get_records_list_ui();
			if ($records_list_ui) $records_list_ui->set_current_url($pmb_url_base.'catalog.php?categ='.$categ.'&sub='.$sub.'&serial_id='.$isbd->notice_id.'&quoi='.$quoi.($view ? '&view='.$view : ''));
			print $h2o_serial_tabs->render(array('records_tabs' => $records_tabs));
		}
	}
}

function compare_date($date_debut="", $date_fin="") {
	$restrict = '';
	if($date_debut && $date_fin) {
		if($date_fin<$date_debut) {
			$restrict = " and date_date between '".$date_fin."' and '".$date_debut."' ";
		} else if($date_fin == $date_debut) {
			$restrict = " and date_date='".$date_debut."' ";
		} else {
			$restrict = " and date_date between '".$date_debut."' and '".$date_fin."' ";
		}
	} else if($date_debut) {
		$restrict = " and date_date >='".$date_debut."' ";
	} else if($date_fin) {
		$restrict = " and date_date <='".$date_fin."' ";
	}
	return $restrict;
}

// affichage de la liste utilisateurs pour sélection
function list_serial($cb, $serial_list, $nav_bar) {
	global $serial_list_tmpl;
	$serial_list_tmpl = str_replace("!!cle!!", $cb, $serial_list_tmpl);
	$serial_list_tmpl = str_replace("!!list!!", $serial_list, $serial_list_tmpl);
	$serial_list_tmpl = str_replace("!!nav_bar!!", $nav_bar, $serial_list_tmpl);
	print pmb_bidi($serial_list_tmpl);
}
