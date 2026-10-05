<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_affichage.inc.php,v 1.69.2.1 2024/05/22 10:22:59 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Affichage d'une notice

require_once($class_path."/notice_affichage.class.php");
require_once($class_path."/notice_affichage.ext.class.php");
require_once($class_path."/serial_affichage_unimarc.class.php");
require_once($class_path."/notice_onglet.class.php");
require_once($class_path."/record_display.class.php");
require_once($class_path."/record_display_modes.class.php");

//afin d'inclure les fichiers contenant les fonctions particulières d'affichage
// require_once($include_path."/func_phototheque.inc.php"); EST REMPLACE PAR LE CODE CI-DESSOUS
global $opac_notice_groupe_fonction;
if ($opac_notice_groupe_fonction) {
	$couples_type_not_fonction=explode(";",$opac_notice_groupe_fonction);
	for ($i=0; $i<count($couples_type_not_fonction); $i++) {
		$couples_type_not_fonction_c=explode(" ",trim($couples_type_not_fonction[$i]));
		$groupe_fonction_temp=trim($couples_type_not_fonction_c[1]);
		if ($groupe_fonction_temp!="aff_notice") {
			require_once($include_path."/".$groupe_fonction_temp.".inc.php");
		}
	}
}

function get_aff_function() {
	global $l_typdoc;
	global $opac_notice_groupe_fonction;
	global $aff_notice_fonction;
	global $class_path,$include_path;
	global $is_aff_notice_fonction;
	global $opac_notices_display_modes;

	if (!$is_aff_notice_fonction) {

		//on utilise le système de choix des modes d'affichage
		$recordmodes = record_display_modes::get_instance();
		if ($opac_notices_display_modes && $recordmodes) {
			if ($mode_id=$recordmodes->get_current_mode()) {
				$aff_notice_fonction = $recordmodes->get_aff_function($mode_id);

				if ($aff_notice_fonction) {
					$is_aff_notice_fonction = true;
					return $aff_notice_fonction;
				}
			}
		}

		$couples=explode(";", $opac_notice_groupe_fonction ?? "");
		for ($i=0; $i<count($couples); $i++) {
			$c = explode(" ", trim($couples[$i] ?? ""));
			$t_typdoc_o[] = explode(",", trim($c[0] ?? ""));

			// Tri du tableau
			$fonction[] = (isset($c[1]) ? trim($c[1]) : '');
		}

		$t_typdoc = explode(",", $l_typdoc ?? "");
		// Pour chaque t_typdoc, recherche des éléments qui le contienne
		for ($i=0; $i<count($t_typdoc); $i++) {
			for ($j=0; $j<count($t_typdoc_o); $j++) {
				$as=array_search($t_typdoc[$i],$t_typdoc_o[$j]);
				if ($as===false) {
					for ($k=$j+1; $k<count($t_typdoc_o); $k++) {
						$t_typdoc_o[$k-1]=$t_typdoc_o[$k];
						$fonction[$k-1]=$fonction[$k];
					}
					unset($t_typdoc_o[count($t_typdoc_o)-1]);
					unset($fonction[count($t_typdoc_o)]);
					$j--;
				}
			}
		}
		if ((count($t_typdoc_o))&&($fonction[0])) {
			$aff_notice_fonction=$fonction[0];
		}
		if ($aff_notice_fonction && $aff_notice_fonction!="aff_notice") {
			require_once($include_path."/".$aff_notice_fonction.".inc.php");
			$is_aff_notice_fonction=true;
		} else {
			$aff_notice_fonction="";
		}
	}
	return $aff_notice_fonction;
}

function aff_notice($id, $nocart=0, $gen_header=1, $use_cache=0, $mode_aff_notice="", $depliable="", $nodocnum=0, $enrichment=1, $recherche_ajax_mode=0, $show_map=1, $template_directory = "") {

	global $liens_opac;
	global $opac_notices_format;
	global $opac_notices_depliable;
	global $opac_cart_allow;
	global $opac_cart_only_for_subscriber;
	global $opac_notice_affichage_class;
	global $opac_notice_enrichment;
	global $opac_recherche_ajax_mode;
	global $opac_notices_format_onglets;
	global $lvl,$search_type_asked;
	global $record_css_already_included; // Pour pas inclure la css 10 fois
	global $opac_rgaa_active;
	
	$retour_aff = "";
	if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) {
		$cart=1; 
	} else {
		$cart=0;
	}
	if ($nocart) $cart=0;
	$id = intval($id);
	//Recherche des fonctions d'affichage
	$entete='';
	$recordmodes = record_display_modes::get_instance();
	if($recordmodes && $id==-1){
		$entete.=$recordmodes->show_mode_selector();
	}
	
	$f=get_aff_function();
	if ($f) return $entete.$f($id,$cart,$gen_header,$use_cache,$mode_aff_notice,$depliable,$nodocnum,$enrichment,$recherche_ajax_mode,$show_map,$recordmodes);
	
	if ($id>0) {
		if ($mode_aff_notice !== "") $type_aff=$mode_aff_notice;
		else $type_aff=$opac_notices_format;
		
		if ($depliable === "") $depliable=$opac_notices_depliable;

		if ($type_aff != AFF_ETA_NOTICES_TEMPLATE_DJANGO) {
			$header_only=0;
			if($recherche_ajax_mode && $opac_recherche_ajax_mode){
				//Si ajax, on ne charge pas tout
				$header_only=1;
			}
			$class_affichage_to_call = $opac_notice_affichage_class;
			if (!$class_affichage_to_call) $class_affichage_to_call="notice_affichage";
			$current = new $class_affichage_to_call($id,$liens_opac,$cart,0,$header_only,!$gen_header, $show_map);
			if($nodocnum) $current->docnum_allowed = 0;
			
			if ($gen_header) $current->do_header();
		
			if(!$current->visu_notice){
				return "";
			}
		}
		if($opac_recherche_ajax_mode && $recherche_ajax_mode && $depliable && $type_aff!=AFF_ETA_NOTICES_REDUIT && $type_aff!=AFF_ETA_NOTICES_TEMPLATE_DJANGO){
			$current->genere_ajax($type_aff,0) ;
			$retour_aff .= $current->result ;
		}else{
			$flag_no_onglet_perso = 0;
			switch ($type_aff) {
				case AFF_ETA_NOTICES_REDUIT :
					$retour_aff .= $current->notice_header_with_link."<br />";
					break;
				case AFF_ETA_NOTICES_ISBD :
					$current->do_isbd();
					$current->genere_simple($depliable, 'ISBD') ;
					$retour_aff .= $current->result ;
					break;
				case AFF_ETA_NOTICES_PUBLIC :
					$current->do_public();
					$current->genere_simple($depliable, 'PUBLIC') ;
					$retour_aff .= $current->result ;
					break;
				case AFF_ETA_NOTICES_BOTH :
					$current->do_isbd();
					$current->do_public();
					$current->genere_double($depliable, 'PUBLIC') ;
					$retour_aff .= $current->result ;
					break ;
				case AFF_ETA_NOTICES_BOTH_ISBD_FIRST :
					$current->do_isbd();
					$current->do_public();
					$current->genere_double($depliable, 'ISBD') ;
					$retour_aff .= $current->result ;
					break ;
				case AFF_ETA_NOTICES_TEMPLATE_DJANGO :
					global $include_path;
					global $opac_notices_format_django_directory;
					
					if (!$opac_notices_format_django_directory) $opac_notices_format_django_directory = "common";
					
					if (!$record_css_already_included) {
						if (file_exists($include_path."/templates/record/".$opac_notices_format_django_directory."/styles/style.css")) {
							$retour_aff .= "<link type='text/css' href='./includes/templates/record/".$opac_notices_format_django_directory."/styles/style.css' rel='stylesheet'></link>";
						}
						$record_css_already_included = true;
					}
					// Si on l'appelle par le tag {% etagere_see %}, on veut tout le temps le réduit...
					$trace = debug_backtrace();
					$count_trace = count($trace);
					$from_etagesee_tag = false;
					$from_recordsee_tag = false;
					for ($i = 2; $i < $count_trace; $i++) {
					    if (isset($trace[$i]['object']) && get_class($trace[$i]['object']) === "Etageresee_Tag") {
					        $from_etagesee_tag=true;
					    }
					    if (isset($trace[$i]['object']) && get_class($trace[$i]['object']) === "Recordsee_Tag") {
					        $from_recordsee_tag=true;
					    }
					}
					if($from_etagesee_tag === true){
					    $retour_aff .= record_display::get_display_in_result($id, $template_directory);
					}elseif($from_recordsee_tag === true){
					    $retour_aff .= record_display::get_display_in_result($id, $template_directory);
					}else{
    					switch ($lvl) {
    						case 'notice_display' :
    						    $from_serial_analysis = false;
    						    if(!empty($_GET['id']) && $_GET['id'] != $id) {
    						        if(notice::get_niveau_biblio($_GET['id']) == 's' && notice::get_niveau_biblio($id) == 'a') {
    						            $from_serial_analysis = true;
    						        }
    						    }
    						    if($from_serial_analysis) {
    						        $retour_aff .= record_display::get_display_in_result($id, $template_directory);
    						    } else {
    						        $retour_aff .= record_display::get_display_extended($id, $template_directory);
    						    }
    						    
    						    break;
    						case 'bulletin_display' :
    						case 'resa' :
    							$retour_aff .= record_display::get_display_extended($id, $template_directory);
    							break;
    						case 'more_result' :
    						default :
    							if($search_type_asked=='perio_a2z'){
    								$retour_aff .= record_display::get_display_extended($id, $template_directory);
    							} else {
    								$retour_aff .= record_display::get_display_in_result($id, $template_directory);
    							}
    							break;
    					}
					}
					break;
				default:
					$current->do_isbd();
					$current->do_public();					
					$current->genere_double($depliable, 'autre') ;
					$retour_aff .= $current->result ;
					$flag_no_onglet_perso=1;
					break ;
					
			}
		
	/*			
			$onglets_title="";
			$onglets_content="";
			if($opac_notices_format_onglets){
				$onglets=explode(",", $opac_notices_format_onglets);
				foreach($onglets as $id_tpl){
					$notice_onglet=new notice_onglet($id_tpl);
					$onglets_title.="
					<li id='onglet_tpl_".$id_tpl."_".$id."'  class='isbd_public_inactive'>
						<a href='#' title=\"".$notice_onglet->get_onglet_header()."\" onclick=\"show_what('tpl_".$id_tpl."_', '$id'); return false;\">".$notice_onglet->get_onglet_header()."</a>
					</li>";
		
					$onglets_content.="
					<div id='div_tpl_".$id_tpl."_".$id."' class='onglet_tpl' style='display:none;'>
					".$notice_onglet->get_onglet_content($id)."
					</div>";
				}
			}	
			$retour_aff=str_replace('<!-- onglets_perso_list -->', $onglets_title, $retour_aff);
			$retour_aff=str_replace('<!-- onglets_perso_content -->', $onglets_content, $retour_aff);
				
	*/			
			if(!$flag_no_onglet_perso){
				$onglet_perso=new notice_onglets();
				$retour_aff=$onglet_perso->insert_onglets($id,$retour_aff);
			}
			if(!$depliable && $opac_notice_enrichment && $enrichment==1){
				$retour_aff.="<script>getEnrichment('$id');</script>";
			}
		}
		if($opac_rgaa_active){
			if(strpos($retour_aff, '<article>') === false){
				$retour_aff ="<article>". $retour_aff."</article>";
			}
		}
	}	
	return $entete.$retour_aff;
}

function aff_notice_unimarc($id,$nocart=0, $entrepots_localisations=array(), $mode_aff_notice = "", $template_directory = "") {

	global $opac_notices_format;
	global $opac_notices_depliable;
	global $opac_cart_allow;
	global $opac_cart_only_for_subscriber;
	global $msg;
	global $record_css_already_included;
	global $opac_rgaa_active;

	$retour_aff = '';
	if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) $cart=1; else $cart=0;
	if ($nocart) $cart=0;
	
	if ($mode_aff_notice !== "") $type_aff=$mode_aff_notice;
	else $type_aff=$opac_notices_format;
	//Recherche des fonctions d'affichage
	//$f=get_aff_function();
	//if ($f) return $f($id,$cart);
	if ($id) {
	    if ($type_aff != AFF_ETA_NOTICES_TEMPLATE_DJANGO) {
    		$current = new notice_affichage_unimarc($id,"",$cart,0, $entrepots_localisations);
    		$depliable=$opac_notices_depliable;
    		$current->do_header(); 
    		
    		if($current->notice_header == ""){
    			$current->notice_header = sprintf($msg['cart_notice_expired'],$id);
    			$current->notice_expired = true;
    		}
	    }
		switch ($type_aff) {
			case AFF_ETA_NOTICES_REDUIT :
				$retour_aff .= $current->notice_header." ";
				break;
			case AFF_ETA_NOTICES_ISBD :	
				$current->do_isbd();
				$current->genere_simple($depliable, 'ISBD') ;
				$retour_aff .= $current->result ;
				break;
			case AFF_ETA_NOTICES_PUBLIC :
				$current->do_public();
				$current->genere_simple($depliable, 'PUBLIC') ;
				$retour_aff .= $current->result ;
				break;
			case AFF_ETA_NOTICES_BOTH :
				$current->do_isbd();
				$current->do_public();
				$current->genere_double($depliable, 'PUBLIC') ;
				$retour_aff .= $current->result ;
				break ;
			case AFF_ETA_NOTICES_BOTH_ISBD_FIRST :
				$current->do_isbd();
				$current->do_public();
				$current->genere_double($depliable, 'ISBD') ;
				$retour_aff .= $current->result ;
				break ;
			case AFF_ETA_NOTICES_TEMPLATE_DJANGO :
			    global $include_path;
			    global $opac_notices_format_django_directory;
			    
			    if (!$opac_notices_format_django_directory) $opac_notices_format_django_directory = "common";
			    
			    if (!$record_css_already_included) {
			        if (file_exists($include_path."/templates/record/".$opac_notices_format_django_directory."/styles/style.css")) {
			            $retour_aff .= "<link type='text/css' href='./includes/templates/record/".$opac_notices_format_django_directory."/styles/style.css' rel='stylesheet'></link>";
			        }
			        $record_css_already_included = true;
			    }
			    
			    $retour_aff .= record_display::get_display_unimarc_in_result($id, $template_directory, $entrepots_localisations);
			    
			    break;
			default:
				$current->do_isbd();
				$current->do_public();					
				$current->genere_double($depliable, 'autre') ;
				$retour_aff .= $current->result ;
				break ;
		}
		if($opac_rgaa_active){
			if(strpos($retour_aff, '<article>') === false){
				$retour_aff ="<article>". $retour_aff."</article>";
			}
		}
	}
	
	return $retour_aff;
}

function aff_serial_unimarc($id,$nocart=0, $entrepots_localisations=array()) {

	global $opac_notices_format;
	global $opac_notices_depliable;
	global $opac_cart_allow;
	global $opac_cart_only_for_subscriber;
	global $msg;
	global $opac_rgaa_active;

	$retour_aff = '';
	if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) $cart=1; else $cart=0;
	if ($nocart) $cart=0;

	//Recherche des fonctions d'affichage
	//$f=get_aff_function();
	//if ($f) return $f($id,$cart);

	if ($id) {
		$current = new serial_affichage_unimarc($id,"",$cart,0, $entrepots_localisations);
		$depliable=$opac_notices_depliable;
		$current->do_header();

		if($current->notice_header == ""){
			$current->notice_header = sprintf($msg['cart_notice_expired'],$id);
			$current->notice_expired = true;
		}
		switch ($opac_notices_format) {
			case AFF_ETA_NOTICES_REDUIT :
				$retour_aff .= $current->notice_header." ";
				break;
			case AFF_ETA_NOTICES_ISBD :
				$current->do_isbd();
				$current->genere_simple($depliable, 'ISBD') ;
				$retour_aff .= $current->result ;
				break;
			case AFF_ETA_NOTICES_PUBLIC :
				$current->do_public();
				$current->genere_simple($depliable, 'PUBLIC') ;
				$retour_aff .= $current->result ;
				break;
			case AFF_ETA_NOTICES_BOTH :
				$current->do_isbd();
				$current->do_public();
				$current->genere_double($depliable, 'PUBLIC') ;
				$retour_aff .= $current->result ;
				break ;
			case AFF_ETA_NOTICES_BOTH_ISBD_FIRST :
				$current->do_isbd();
				$current->do_public();
				$current->genere_double($depliable, 'ISBD') ;
				$retour_aff .= $current->result ;
				break ;
			default:
				$current->do_isbd();
				$current->do_public();
				$current->genere_double($depliable, 'autre') ;
				$retour_aff .= $current->result ;
				break ;
		}
		if($opac_rgaa_active){
			if(strpos($retour_aff, '<article>') === false){
				$retour_aff ="<article>". $retour_aff."</article>";
			}
		}
	}
	return $retour_aff;
}
?>