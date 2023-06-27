<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: record_display.class.php,v 1.93.2.1 2019-06-18 06:51:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/record_datas.class.php");
require_once($class_path."/parametres_perso.class.php");
include_once($include_path."/templates/demandes.tpl.php");
require_once($class_path."/demandes.class.php");
require_once($class_path.'/scan_request/scan_request.class.php');
require_once($class_path."/serialcirc.class.php");
require_once($class_path."/serialcirc_empr.class.php");
require_once($class_path."/acces.class.php");
require_once($base_path.'/includes/bul_list_func.inc.php');
require_once($base_path.'/includes/notice_affichage.inc.php');
require_once($include_path."/notice_authors.inc.php");
require_once($class_path."/record_datas_unimarc.class.php");

/**
 * Classe d'affichage d'une notice
 *
 */
class record_display {
	
	/**
	 * Tableau d'instances de record_datas
	 * @var record_datas
	 */
	static private $records_datas = array();
	
	/**
	 * Tableau d'instances de notice_affichage_unimarc
	 * @var notice_affichage_unimarc
	 */
	static private $records_unimarc_datas = array();
	static private $special;
	
	public static $linked_permalink;
	
	/**
	 * Retourne une instance de record_datas
	 * @param int $notice_id Identifiant de la notice
	 * @return record_datas
	 */
	static public function get_record_datas($notice_id) {
		if (!isset(self::$records_datas[$notice_id])) {
			self::$records_datas[$notice_id] = new record_datas($notice_id);
		}
		return self::$records_datas[$notice_id];
	}
	
	/**
	 * Ré-initialisation du singleton record_datas
	 * @param int $notice_id Identifiant de la notice
	 * @return record_datas
	 */
	static public function init_record_datas($notice_id) {
		self::$records_datas[$notice_id] = new record_datas($notice_id);
		return self::$records_datas[$notice_id];
	}
	
	static public function lookup($name, $object) {
		$return = null;
		// Si on le nom commence par record. on va chercher les méthodes
		if (substr($name, 0, 8) == ":record.") {
			$attributes = explode('.', $name);
			$notice_id = $object->getVariable('notice_id');
			
			// On va chercher dans record_display
			$return = static::look_for_attribute_in_class("record_display", $attributes[1], array($notice_id));
			
			if (!$return) {
				// On va chercher dans record_datas
			    $unimarc = $object->getVariable('unimarc');
			    if ($unimarc === true) {
			        $record_datas = static::get_record_unimarc_datas($notice_id);
			    } else {
			        $record_datas = static::get_record_datas($notice_id);
			    }
				$return = static::look_for_attribute_in_class($record_datas, $attributes[1]);
			}
			
			// On regarde les attributs enfants recherchés
			if ($return && count($attributes) > 2) {
				for ($i = 2; $i < count($attributes); $i++) {
					// On regarde si c'est un tableau ou un objet
					if (is_array($return)) {
						$return = (isset($return[$attributes[$i]]) ? $return[$attributes[$i]] : '');
					} else if (is_object($return)) {
						$return = static::look_for_attribute_in_class($return, $attributes[$i]);
					} else {
						$return = null;
						break;
					}
				}
			}
		} else {
			$attributes = explode('.', $name);
			// On regarde si on a directement une instance d'objet, dans le cas des boucles for
			if (is_object($obj = $object->getVariable(substr($attributes[0], 1))) && (count($attributes) > 1)) {
				$return = $obj;
				for ($i = 1; $i < count($attributes); $i++) {
					// On regarde si c'est un tableau ou un objet
					if (is_array($return)) {
					    $return = (!empty($return[$attributes[$i]]) ? $return[$attributes[$i]] : null);
					} else if (is_object($return)) {
						$return = static::look_for_attribute_in_class($return, $attributes[$i]);
					} else {
						$return = null;
						break;
					}
				}
			}
		}
		return $return;
	}
	
	static protected function look_for_attribute_in_class($class, $attribute, $parameters = array()) {
		if (is_object($class) && (isset($class->{$attribute}) || method_exists($class, '__get'))) {
			return $class->{$attribute};
		} else if (method_exists($class, $attribute)) {
			return call_user_func_array(array($class, $attribute), $parameters);
		} else if (method_exists($class, "get_".$attribute)) {
			return call_user_func_array(array($class, "get_".$attribute), $parameters);
		} else if (method_exists($class, "is_".$attribute)) {
			return call_user_func_array(array($class, "is_".$attribute), $parameters);
		}
		return null;
	}
	
	static private function render($notice_id, $tpl, $unimarc = false) {
		$h2o = H2o_collection::get_instance($tpl);
		H2o_collection::addLookup("record_display::lookup");
		return $h2o->render(array('notice_id' => $notice_id, 'unimarc' => $unimarc));
	}
	
	/**
	 * Génère le titre nécessaire à Zotéro
	 * @param int $id_notice Identifiant de la notice
	 * @return string
	 */
	static public function get_display_coins_title($notice_id){
		$display = '';
		$record_datas = static::get_record_datas($notice_id);
		$coins = $record_datas->get_coins();
		foreach ($coins as $key=>$value) {
			if(is_array($value)) { //Spécifique rft.aut
				foreach ($value as $sub_value) {
					$display .= "&amp;".$key."=".rawurlencode(encoding_normalize::utf8_normalize($sub_value));
				}
			} else {
				$display .= "&amp;".$key."=".rawurlencode(encoding_normalize::utf8_normalize($value));
			}
		}
		return $display;
	}
	
	/**
	 * Génère le span nécessaire à Zotéro
	 * @param int $id_notice Identifiant de la notice
	 * @return string
	 */
	static public function get_display_coins_span($notice_id){
		// Attention!! Fait pour Zotero qui ne traite pas toute la norme ocoins
		global $charset;
		
		$record_datas = static::get_record_datas($notice_id);
		
		// http://generator.ocoins.info/?sitePage=info/book.html&
		// http://ocoins.info/cobg.html				
		$coins_span="<span class='Z3988' title='ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3A";
		
		switch ($record_datas->get_niveau_biblio()){
			case 's':// periodique
// 				$coins_span.="book";
			break;
			case 'a': // article
				$coins_span.="journal";
			break;
			case 'b': //Bulletin
// 				$coins_span.="book";
			break;
			case 'm':// livre
			default:
				$coins_span.="book";
			break;
		}
		$coins_span.=static::get_display_coins_title($notice_id);
		$coins_span.="'></span>";
		return 	$coins_span;			
	}
	
	static public function get_display_column($label='', $expl=array()) {
		global $msg, $charset;
	
		$column = '';
		if (($label == "location_libelle") && $expl['num_infopage']) {
			if ($expl['surloc_id'] != "0") $param_surloc="&surloc=".$expl['surloc_id'];
			else $param_surloc="";
			$column .="<td class='".$label."'><a href=\"".static::get_parameter_value('url_base')."index.php?lvl=infopages&pagesid=".$expl['num_infopage']."&location=".$expl['expl_location'].$param_surloc."\" title=\"".$msg['location_more_info']."\">".htmlentities($expl[$label], ENT_QUOTES, $charset)."</a></td>";
		} else if ($label=="expl_comment") {
			$column.="<td class='".$label."'>".nl2br(htmlentities($expl[$label],ENT_QUOTES, $charset))."</td>";
		} elseif ($label=="expl_cb") {
			$column.="<td id='expl_" . $expl['expl_id'] . "' class='".$label."'>".htmlentities($expl[$label],ENT_QUOTES, $charset)."</td>";
		} else {
			$column .="<td class='".$label."'>".htmlentities($expl[$label],ENT_QUOTES, $charset)."</td>";
		}
		return $column;
	}
	
	 static public function get_display_situation($expl) {
		global $msg, $charset;
		global $opac_show_empr ;
		global $pmb_transferts_actif, $transferts_statut_transferts;
	
		$situation = "";
		if ($expl['statut_libelle_opac'] != "") $situation .= $expl['statut_libelle_opac']."<br />";
		if ($expl['flag_resa']) {
			$situation .= "<strong>".$msg['expl_reserve']."</strong>";
		} else {
			if ($expl['pret_flag']) {
				if($expl['pret_retour']) { // exemplaire sorti
					if ((($opac_show_empr==1) && ($_SESSION["user_code"])) || ($opac_show_empr==2)) {
						$situation .= $msg['entete_show_empr'].htmlentities(" ".$expl['empr_prenom']." ".$expl['empr_nom'],ENT_QUOTES, $charset)."<br />";
					}
					$situation .= "<strong>".str_replace('!!date!!', formatdate($expl['pret_retour']), $msg['out_until'] )."</strong>";
					// ****** Affichage de l'emprunteur
				} else { // pas sorti
					$situation .= "<strong>".$msg['available']."</strong>";
				}
			} else { // pas prêtable
				// exemplaire pas prêtable, on affiche juste "exclu du pret"
				if (($pmb_transferts_actif=="1") && ("".$expl['expl_statut'].""==$transferts_statut_transferts)) {
					$situation .= "<strong>".$msg['reservation_lib_entransfert']."</strong>";
				} else {
					$situation .= "<strong>".$msg['exclu']."</strong>";
				}
			}
		} // fin if else $flag_resa
		return $situation;
	}
	
	/**
	 * Génère la liste des exemplaires
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_expl_list($notice_id) {
		global $msg, $charset;
		global $expl_list_header, $expl_list_footer;
		global $pmb_transferts_actif,$transferts_statut_transferts;
		global $memo_p_perso_expl;
		global $opac_show_empty_items_block ;
		global $opac_show_exemplaires_analysis;
		global $expl_list_header_loc_tpl,$opac_aff_expl_localises;

		$nb_expl_visible = 0;
		$nb_expl_autre_loc=0;
		$nb_perso_aff=0;
        
		$record_datas = static::get_record_datas($notice_id);
		
		$type = $record_datas->get_niveau_biblio();
		$id = $record_datas->get_id();
		$bull = $record_datas->get_bul_info();
		$bull_id = (isset($bull['bulletin_id']) ? $bull['bulletin_id'] : "");
		
		// les dépouillements ou périodiques n'ont pas d'exemplaire
		if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "" ;
		if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
		$header_found_p_perso=0;
	
		$expls_datas = $record_datas->get_expls_datas();
	
		$expl_list_header_deb="";
		if (isset($expls_datas['colonnesarray']) && is_array($expls_datas['colonnesarray'])) {
    		foreach ($expls_datas['colonnesarray'] as $colonne) {
    			$expl_list_header_deb .= "<th class='expl_header_".$colonne."'>".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."</th>";
    		}
		}
		$expl_list_header_deb.="<th class='expl_header_statut'>".$msg['statut']."</th>";
		$expl_liste = "";
		$expl_liste_all = "";
		$header_perso_aff = "";
		
		if(is_array($expls_datas['expls']) && count($expls_datas['expls'])) {
			foreach ($expls_datas['expls'] as $expl) {
				$expl_liste .= "<tr>";
		
				foreach ($expls_datas['colonnesarray'] as $colonne) {
					$expl_liste .= static::get_display_column($colonne, $expl);
				}
				
				if ($expl['flag_resa']) {
					$class_statut = "expl_reserve";
				} else {
					if ($expl['pret_flag']) {
						if($expl['pret_retour']) { // exemplaire sorti
							$class_statut = "expl_out";
						} else { // pas sorti
							$class_statut = "expl_available";
						}
					} else { // pas prêtable
						// exemplaire pas prêtable, on affiche juste "exclu du pret"
						if (($pmb_transferts_actif=="1") && ("".$expl['expl_statut'].""==$transferts_statut_transferts)) {
							$class_statut = "expl_transfert";
						} else {
							$class_statut = "expl_unavailable";
						}
					}
				} // fin if else $flag_resa
				$expl_liste .= "<td class='expl_situation'>".static::get_display_situation($expl)." </td>";
				$expl_liste = str_replace("!!class_statut!!", $class_statut, $expl_liste);
	
				//Champs personalisés
				$perso_aff = "" ;
				if (!$memo_p_perso_expl->no_special_fields) {
					$perso_=$memo_p_perso_expl->show_fields($expl['expl_id']);
					for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
						$p=$perso_["FIELDS"][$i];
						if ($p['OPAC_SHOW'] ) {
							if(!$header_found_p_perso) {
								$header_perso_aff.="<th class='expl_header_tdoc_libelle'>".$p["TITRE_CLEAN"]."</th>";
								$nb_perso_aff++;
							}
							if( $p["AFF"] !== '')	{
								$perso_aff.="<td class='p_perso'>".$p["AFF"]."</td>";
							}
							else $perso_aff.="<td class='p_perso'>&nbsp;</td>";
						}
					}
				}
				$header_found_p_perso=1;
				$expl_liste.=$perso_aff;
	
				$expl_liste .="</tr>";
				$expl_liste_all.=$expl_liste;
	
				if($opac_aff_expl_localises && $_SESSION["empr_location"]) {
					if($expl['expl_location']==$_SESSION["empr_location"]) {
						$expl_liste_loc.=$expl_liste;
					} else $nb_expl_autre_loc++;
				}
				$expl_liste="";
				$nb_expl_visible++;
			
			} // fin foreach
		}
		$expl_list_header_deb="<tr class='thead'>".$expl_list_header_deb;
		//S'il y a des titres de champs perso dans les exemplaires
		if($header_perso_aff) {
			$expl_list_header_deb.=$header_perso_aff;
		}
		$expl_list_header_deb.="</tr>";
	
		if($opac_aff_expl_localises && $_SESSION["empr_location"] && $nb_expl_autre_loc) {
			// affichage avec onglet selon la localisation
			if(!$expl_liste_loc) {
				$expl_liste_loc="<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1+$nb_perso_aff)."'>".$msg["no_expl"]."</td></tr>";
			}
			$expl_liste_all=str_replace("!!EXPL!!",$expl_list_header_deb.$expl_liste_all,$expl_list_header_loc_tpl);
			$expl_liste_all=str_replace("!!EXPL_LOC!!",$expl_list_header_deb.$expl_liste_loc,$expl_liste_all);
			$expl_liste_all=str_replace("!!mylocation!!",$_SESSION["empr_location_libelle"],$expl_liste_all);
			$expl_liste_all=str_replace("!!id!!",$id+$bull_id,$expl_liste_all);
		} else {
			// affichage de la liste d'exemplaires calculée ci-dessus
			if (!$expl_liste_all && $opac_show_empty_items_block==1) {
				$expl_liste_all = $expl_list_header.$expl_list_header_deb."<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1)."'>".$msg["no_expl"]."</td></tr>".$expl_list_footer;
			} elseif (!$expl_liste_all && $opac_show_empty_items_block==0) {
				$expl_liste_all = "";
			} else {
				$expl_liste_all = $expl_list_header.$expl_list_header_deb.$expl_liste_all.$expl_list_footer;
			}
		}	
		$expl_liste_all=str_replace("<!--nb_expl_visible-->",($nb_expl_visible ? " (".$nb_expl_visible.")" : ""),$expl_liste_all);	
		return $expl_liste_all;
	
	} // fin function get_display_expl_list
	
	/**
	 * Génère la liste des exemplaires
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_expl_responsive_list($notice_id) {
		global $dbh;
		global $msg, $charset;
		global $expl_list_header, $expl_list_footer;
		global $opac_expl_data, $opac_expl_order, $opac_url_base;
		global $pmb_transferts_actif,$transferts_statut_transferts;
		global $memo_p_perso_expl;
		global $opac_show_empty_items_block ;
		global $opac_show_exemplaires_analysis;
		global $expl_list_header_loc_tpl,$opac_aff_expl_localises;
		
		$nb_expl_visible = 0; 		
		$nb_expl_autre_loc=0;
		$nb_perso_aff=0;
	
		$record_datas = static::get_record_datas($notice_id);
		if(!$record_datas->is_numeric()){
			$type = $record_datas->get_niveau_biblio();
			$id = $record_datas->get_id();
			$bull = $record_datas->get_bul_info();
			$bull_id = (isset($bull['bulletin_id']) ? $bull['bulletin_id'] : '');
			
			// les dépouillements ou périodiques n'ont pas d'exemplaire
			if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "" ;
			if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
			$header_found_p_perso=0;
			
			$expls_datas = $record_datas->get_expls_datas();
			$expl_list_header_deb="<tr class='thead'>";
			foreach ($expls_datas['colonnesarray'] as $colonne) {
				$expl_list_header_deb .= "<th class='expl_header_".$colonne."'>".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."</th>";
			}
			$expl_list_header_deb.="<th class='expl_header_statut'>".$msg['statut']."</th>";
			$expl_liste="";
			$expl_liste_all="";
			$header_perso_aff="";
			
			if(count($expls_datas['expls'])) {
				$customization_expl_columns = array();
				$special = static::get_special($notice_id);
				if(!empty($special)) {
					$customization_expl_columns = $special->get_customization_expl_columns();
				}
				foreach ($expls_datas['expls'] as $expl) {
					$expl_liste .= "<tr class='item_expl !!class_statut!!'>";
			
					foreach ($expls_datas['colonnesarray'] as $colonne) {
						if(isset($customization_expl_columns[$colonne])) {
							$expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."'>";
							if(isset($customization_expl_columns[$colonne]['htmlentities']) && $customization_expl_columns[$colonne]['htmlentities'] == false) {
								$expl_liste .=strip_tags($expl[$colonne], $customization_expl_columns[$colonne]['keep_tags']);
							} else {
								$expl_liste .=htmlentities($expl[$colonne],ENT_QUOTES, $charset);
							}
							$expl_liste .="</td>";
						} elseif (($colonne == "location_libelle") && $expl['num_infopage']) {
							if ($expl['surloc_id'] != "0") {
								$param_surloc="&surloc=".$expl['surloc_id'];
							} else {
								$param_surloc="";
							}
							$expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."'><a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$expl['num_infopage']."&location=".$expl['expl_location'].$param_surloc."\" title=\"".$msg['location_more_info']."\">".htmlentities($expl[$colonne], ENT_QUOTES, $charset)."</a></td>";
						} elseif($colonne == "expl_cb") {
							$expl_liste .="<td id='expl_" . $expl['expl_id'] . "' class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."'>".htmlentities($expl[$colonne],ENT_QUOTES, $charset)."</td>";
						} else {
							$expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."'>".htmlentities($expl[$colonne],ENT_QUOTES, $charset)."</td>";
						}
			
					}
						
					if ($expl['flag_resa']) {
						$class_statut = "expl_reserve";
					} else {
						if ($expl['pret_flag']) {
							if($expl['pret_retour']) { // exemplaire sorti
								$class_statut = "expl_out";
							} else { // pas sorti
								$class_statut = "expl_available";
							}
						} else { // pas prêtable
							// exemplaire pas prêtable, on affiche juste "exclu du pret"
							if (($pmb_transferts_actif=="1") && ("".$expl['expl_statut'].""==$transferts_statut_transferts)) {
								$class_statut = "expl_transfert";
							} else {
								$class_statut = "expl_unavailable";
							}
						}
					} // fin if else $flag_resa
					$expl_liste .= "<td class='".$msg['statut']."'>".static::get_display_situation($expl)." </td>";
					$expl_liste = str_replace("!!class_statut!!", $class_statut, $expl_liste);
			
					//Champs personalisés
					$perso_aff = "" ;
					if (!$memo_p_perso_expl->no_special_fields) {
						$perso_=$memo_p_perso_expl->show_fields($expl['expl_id']);
						for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
							$p=$perso_["FIELDS"][$i];
							if ($p['OPAC_SHOW'] ) {
								if(!$header_found_p_perso) {
									$header_perso_aff.="<th class='expl_header_tdoc_libelle'>".$p["TITRE_CLEAN"]."</th>";
									$nb_perso_aff++;
								}
								if( $p["AFF"] !== '')	{
									$perso_aff.="<td class='p_perso'>".$p["AFF"]."</td>";
								}
								else $perso_aff.="<td class='p_perso'>&nbsp;</td>";
							}
						}
					}
					$header_found_p_perso=1;
					$expl_liste.=$perso_aff;
			
					$expl_liste .="</tr>";
					$expl_liste_all.= $expl_liste;
			
					if($opac_aff_expl_localises && $_SESSION["empr_location"]) {
						if($expl['expl_location']==$_SESSION["empr_location"]) {
							$expl_liste_loc.=$expl_liste;
						} else {
							$nb_expl_autre_loc++;
						}
					}
					$expl_liste="";
					$nb_expl_visible++;
				} // fin foreach
			}
			//S'il y a des titres de champs perso dans les exemplaires
			if($header_perso_aff) {
				$expl_list_header_deb.=$header_perso_aff;
			}
			$expl_list_header_deb.="</tr>";
			if($opac_aff_expl_localises && $_SESSION["empr_location"] && $nb_expl_autre_loc) {
				// affichage avec onglet selon la localisation
				if(!$expl_liste_loc) {
					$expl_liste_loc="<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1+$nb_perso_aff)."'>".$msg["no_expl"]."</td></tr>";
				}
				$expl_liste_all=str_replace("!!EXPL!!",$expl_list_header_deb.$expl_liste_all,$expl_list_header_loc_tpl);
				$expl_liste_all=str_replace("!!EXPL_LOC!!",$expl_list_header_deb.$expl_liste_loc,$expl_liste_all);
				$expl_liste_all=str_replace("!!mylocation!!",$_SESSION["empr_location_libelle"],$expl_liste_all);
				$expl_liste_all=str_replace("!!id!!",$id+$bull_id,$expl_liste_all);
			} else {
				// affichage de la liste d'exemplaires calculée ci-dessus
				if (!$expl_liste_all && $opac_show_empty_items_block==1) {
					$expl_liste_all = $expl_list_header.$expl_list_header_deb."<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1)."'>".$msg["no_expl"]."</td></tr>".$expl_list_footer;
				} elseif (!$expl_liste_all && $opac_show_empty_items_block==0) {
					$expl_liste_all = "";
				} else {
					$expl_liste_all = $expl_list_header.$expl_list_header_deb.$expl_liste_all.$expl_list_footer;
				}
			}
			$expl_liste_all=str_replace("<!--nb_expl_visible-->",($nb_expl_visible ? " (".$nb_expl_visible.")" : ""),$expl_liste_all);
			return $expl_liste_all;
		}
		return '';
	} // fin function get_display_expl_responsive_list
	
	/**
	 * Fontion qui génère le bloc H3 + table des autres lectures
	 * @param number $notice_id Identifiant de la notice
	 * @param number $bulletin_id Identifiant du bulletin
	 * @return string
	 */
	static public function get_display_other_readings($notice_id) {
		global $dbh, $msg;
		global $opac_autres_lectures_tri;
		global $opac_autres_lectures_nb_mini_emprunts;
		global $opac_autres_lectures_nb_maxi;
		global $opac_autres_lectures_nb_jours_maxi;
		global $opac_autres_lectures;
		global $gestion_acces_active,$gestion_acces_empr_notice;
		
		$record_datas = static::get_record_datas($notice_id);
		$bull = $record_datas->get_bul_info();
		$bulletin_id = (isset($bull['bulletin_id']) ? $bull['bulletin_id'] : '');
		
		if (!$opac_autres_lectures || (!$notice_id && !$bulletin_id)) return "";
	
		if (!$opac_autres_lectures_nb_maxi) $opac_autres_lectures_nb_maxi = 999999 ;
		if ($opac_autres_lectures_nb_jours_maxi) $restrict_date=" date_add(oal.arc_fin, INTERVAL $opac_autres_lectures_nb_jours_maxi day)>=sysdate() AND ";
		else $restrict_date="";
		if ($notice_id) $pas_notice = " oal.arc_expl_notice!=$notice_id AND ";
		else $pas_notice = "";
		if ($bulletin_id) $pas_bulletin = " oal.arc_expl_bulletin!=$bulletin_id AND ";
		else $pas_bulletin = "";
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
	
		$res_autres_lectures = pmb_mysql_query($rqt_autres_lectures) or die ("<br />".pmb_mysql_error()."<br />".$rqt_autres_lectures."<br />");
		if (pmb_mysql_num_rows($res_autres_lectures)) {
			$odd_even=1;
			$inotvisible=0;
			$ret="";
	
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
				$myQuery = pmb_mysql_query($requete, $dbh);
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
				
					// on affiche les résultats 
					if ($odd_even==0) {
						$pair_impair="odd";
						$odd_even=1;
					} else if ($odd_even==1) {
						$pair_impair="even";
						$odd_even=0;
					}
					if ($data['arc_expl_notice']) $tr_javascript=" class='$pair_impair' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./index.php?lvl=notice_display&id=".$data['not_id']."&seule=1';\" style='cursor: pointer' ";
						else $tr_javascript=" class='$pair_impair' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./index.php?lvl=bulletin_display&id=".$data['arc_expl_bulletin']."';\" style='cursor: pointer' ";
					$ret .= "<tr $tr_javascript>";
					$ret .= "<td>".$titre."</td>";    
					$ret .= "<td>".$auteur."</td>";    		
					$ret .= "</tr>\n";
				}
			}
			if ($ret) $ret = "<h3 class='autres_lectures'>".$msg['autres_lectures']."</h3><table style='width:100%;'>".$ret."</table>";
		} else $ret="";
		
	return $ret;
	} // fin autres_lectures ($notice_id=0,$bulletin_id=0)

	/**
	 * Ajoute l'image
	 * @param unknown $notice_id Identifiant de la notice
	 * @param unknown $entree Contenu avant l'ajout
	 * @param unknown $depliable 
	 */
	static public function do_image($notice_id, &$entree,$depliable) {
		global $charset;
		global $msg;
		
		$record_datas = static::get_record_datas($notice_id);
		$image = "";
		if ($record_datas->get_code() || $record_datas->get_thumbnail_url()) {
			if (static::get_parameter_value('show_book_pics')=='1' && (static::get_parameter_value('book_pics_url') || $record_datas->get_thumbnail_url())) {
				$url_image_ok=getimage_url($record_datas->get_code(), $record_datas->get_thumbnail_url());
				$title_image_ok = "";
				if(!$record_datas->get_thumbnail_url()){
					$title_image_ok = htmlentities(static::get_parameter_value('book_pics_msg'), ENT_QUOTES, $charset);
				}
				if(!trim($title_image_ok)){
					$title_image_ok = htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset);
				}
				if ($depliable) {
					$image = "<img class='vignetteimg align_right' src='".static::get_parameter_value('url_base')."images/vide.png' title=\"".$title_image_ok."\" hspace='4' vspace='2' vigurl=\"".$url_image_ok."\"  alt='".$msg["opac_notice_vignette_alt"]."'/>";
				} else {
					$image = "<img class='vignetteimg align_right' src='".$url_image_ok."' title=\"".$title_image_ok."\" hspace='4' vspace='2' alt='".$msg["opac_notice_vignette_alt"]."' />";
				}
			}
		}
		if ($image) {
			$entree = "<table style='width:100%'><tr><td style='vertical-align:top'>$entree</td><td style='vertical-align:top' class='align_right'>$image</td></tr></table>" ;
		} else {
			$entree = "<table style='width:100%'><tr><td>$entree</td></tr></table>" ;
		}
	}
	 /**
	  * Retourne le script des notices similaires
	  * @return string
	  */
	static public function get_display_simili_script($notice_id) {
		switch (static::get_parameter_value('allow_simili_search')) {
			case "0" :
				$script_simili_search = "";
				break;
			case "1" :
				$script_simili_search = "show_simili_search('".$notice_id."');";
				$script_simili_search.= "show_expl_voisin_search('".$notice_id."');";
				break;
			case "2" :
				$script_simili_search = "show_expl_voisin_search('".$notice_id."');";
				break;
			case "3" :
				$script_simili_search = "show_simili_search('".$notice_id."');";
				break;
		}
		return $script_simili_search;
	}
	
	/**
	 * Retourne les notices similaires
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_simili_search($notice_id) {
		$simili_search = "";
		switch(static::get_parameter_value('allow_simili_search')){
			case "1" :
				$simili_search="
					<div id='expl_voisin_search_".$notice_id."' class='expl_voisin_search'></div>
					<div id='simili_search_".$notice_id."' class='simili_search'></div>";
				$simili_search.="
					<script type='text/javascript'>
						".static::get_display_simili_script($notice_id)."
					</script>";
				break;
			case "2" :
				$simili_search="
					<div id='expl_voisin_search_".$notice_id."' class='expl_voisin_search'></div>";
				$simili_search.="
					<script type='text/javascript'>
						".static::get_display_simili_script($notice_id)."
					</script>";
				break;
			case "3" :
				$simili_search="
					<div id='simili_search_".$notice_id."' class='simili_search'></div>";
				$simili_search.="
					<script type='text/javascript'>
						".static::get_display_simili_script($notice_id)."
					</script>";
				break;
		}
		return $simili_search;
	}
	
	/**
	 * Renvoie les états de collections
	 * @param int $notice_id Identifiant de la notice
	 * @return mixed
	 */
	static public function get_display_collstate($notice_id) {
		global $msg;
		global $pmb_etat_collections_localise;
		
		$affichage = "";
		$record_datas = static::get_record_datas($notice_id);
		$collstate = $record_datas->get_collstate();
		if($pmb_etat_collections_localise) {
			$collstate->get_display_list("",0,0,0,1);
		} else { 	
			$collstate->get_display_list("",0,0,0,0);
		}
		if($collstate->nbr) {
			$affichage.= "<h3><span class='titre_exemplaires'>".$msg["perio_etat_coll"]."</span></h3>";
			$affichage.=$collstate->liste;
		}
		return $affichage;
	}
	
	static public function get_lang_list($tableau) {
		$langues = "";
		for ($i = 0 ; $i < sizeof($tableau) ; $i++) {
			if ($langues) $langues.=" ";
			$langues .= $tableau[$i]["langue"]." (<i>".$tableau[$i]["lang_code"]."</i>)";
		}
		return $langues;
	}
	
	/**
	 * Fonction d'affichage des avis
	 * @param int $notice_id Identifiant de la notice
	 */
	static public function get_display_avis($notice_id) {
		$record_datas = static::get_record_datas($notice_id);
		$avis = $record_datas->get_avis();
		return $avis->get_display();
	}
	
	static public function get_display_avis_detail($notice_id) {
		$record_datas = static::get_record_datas($notice_id);
		$avis = $record_datas->get_avis();
		return $avis->get_display_detail();
	}
	
	static public function get_display_avis_only_stars($notice_id) {
		$record_datas = static::get_record_datas($notice_id);
		$avis = $record_datas->get_avis();
		
		return $avis->get_display_only_stars();
	}
	/**
	 * Retourne l'affichage des étoiles
	 * @param float $moyenne
	 */
	
	/**
	 * Fonction d'affichage des suggestions
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_suggestion($notice_id){
		global $msg;
		$do_suggest="<a href='#' title=\"".$msg['suggest_notice_opac']."\" onclick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup&id_notice=".$notice_id."','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\">".$msg['suggest_notice_opac']."</a>";
		return $do_suggest;
	}
	
	/**
	 * Fonction d'affichage des tags
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_tag($notice_id){
		global $msg;
		return "<a href='#' title=\"".$msg['notice_title_tag']."\" onclick=\"open('addtags.php?noticeid=".$notice_id."','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\">".$msg['notice_title_tag']."</a>";
		
	}
	
	/**
	 * Fonction d'affichage des listes de lecture
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_liste_lecture($notice_id){
		global $msg;
		return "
			<script type='text/javascript' src='./includes/javascript/liste_lecture.js'></script>
			<script type='text/javascript'>
				msg_notice_title_liste_lecture_added = '".$msg["notice_title_liste_lecture_added"]."';
				msg_notice_title_liste_lecture_failed = '".$msg["notice_title_liste_lecture_failed"]."';
			</script>
			<a id='liste_lecture_display_tooltip_notice_".$notice_id."'>
				<span class='ExtnotCom imgListeLecture'>
					<img src='".get_url_icon('liste_lecture_w.png')."' align='absmiddle' style='border:0px' title=\"".$msg['notice_title_liste_lecture']."\" alt=\"".$msg['notice_title_liste_lecture']."\" />
				</span>
				<span class='listeLectureN'>
					".$msg['notice_title_liste_lecture']."
				</span>
			</a>
			<div data-dojo-type='dijit/Tooltip' data-dojo-props=\"connectId:'liste_lecture_display_tooltip_notice_".$notice_id."', position:['below']\">
				<div class='row'>
					".$msg['notice_title_liste_lecture']."
				</div>
				<div class='row'>
					".liste_lecture::gen_selector_my_list($notice_id)."
				</div>
			</div>";
	}
	
	/**
	 * Retourne l'affichage étendu d'une notice
	 * @param unknown $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_extended($notice_id, $django_directory = "") {
		global $include_path;
		
		$record_datas = static::get_record_datas($notice_id);
		
		$template = static::get_template("record_extended_display", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);
		
		return static::render($notice_id, $template);
	}
	
	/**
	 * Retourne l'affichage d'une notice dans un résultat de recherche
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_in_result($notice_id, $django_directory = "") {
		global $include_path;
		
		$record_datas = static::get_record_datas($notice_id);
		
		$template = static::get_template("record_in_result_display", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);
		
		return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur imprimante format court
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_printer_short($notice_id, $django_directory = "", $parameters = '') {
		global $include_path;
	
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_for_printer_short", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);
	
		return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur imprimante format long
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_printer_extended($notice_id, $django_directory = "", $parameters = '') {
		global $include_path;
	
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_for_printer_extended", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);
	
		return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur pdf format court
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_pdf_short($notice_id, $django_directory = "", $parameters = '') {
		global $include_path;
	
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_for_pdf_short", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);
	
		return static::render($notice_id, $template);
	}
	
	/**
	 * Retourne l'affichage d'une notice pour impression sur pdf format long
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_pdf_extended($notice_id, $django_directory = "", $parameters = '') {
		global $include_path;
	
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_for_pdf_extended", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);
	
		return static::render($notice_id, $template);
	}
	
	/**
	 * Retourne le bon template
	 * @param string $template_name Nom du template : record_extended ou record_in_result
	 * @param string $niveau_biblio Niveau bibliographique
	 * @param string $typdoc Type de document
	 * @param string $django_directory Répertoire Django à utiliser (paramètre opac_notices_format_django_directory par défaut)
	 * @return string Nom du template à appeler
	 */
	static public function get_template($template_name, $niveau_biblio, $typdoc, $django_directory = "", $connector_id = "", $source_id = "") {
		global $include_path;
		
		if (!$django_directory) $django_directory = static::get_parameter_value('notices_format_django_directory');
		
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$source_id."_".$niveau_biblio.$typdoc.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$source_id."_".$niveau_biblio.$typdoc.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$connector_id."_".$niveau_biblio.$typdoc.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$connector_id."_".$niveau_biblio.$typdoc.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$source_id."_".$niveau_biblio.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$source_id."_".$niveau_biblio.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$connector_id."_".$niveau_biblio.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$connector_id."_".$niveau_biblio.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$source_id.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$source_id.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$connector_id.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$connector_id.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/common/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html")) {
			return $include_path."/templates/record/common/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name."_".$niveau_biblio.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name."_".$niveau_biblio.".tpl.html";
		}
		if (file_exists($include_path."/templates/record/common/".$template_name."_".$niveau_biblio.".tpl.html")) {
			return $include_path."/templates/record/common/".$template_name."_".$niveau_biblio.".tpl.html";
		}		
		if (file_exists($include_path."/templates/record/".$django_directory."/".$template_name.".tpl.html")) {
			return $include_path."/templates/record/".$django_directory."/".$template_name.".tpl.html";
		}
		return $include_path."/templates/record/common/".$template_name.".tpl.html";
	}
	
	static public function get_liens_opac() {
		global $liens_opac;
		
		return $liens_opac;
	}
	
	static public function get_linked_permalink() {
		global $base_path;
		global $use_opac_url_base, $opac_url_base;
	
		$use_opac_url_base += 0;
		if(!isset(static::$linked_permalink[$use_opac_url_base])) {
			if($use_opac_url_base) {
				static::$linked_permalink[$use_opac_url_base] = array(
						'author' => $opac_url_base."index.php?lvl=author_see&id=!!id!!",
						'category' => $opac_url_base."index.php?lvl=categ_see&id=!!id!!",
						'publisher' => $opac_url_base."index.php?lvl=publisher_see&id=!!id!!",
						'collection' => $opac_url_base."index.php?lvl=coll_see&id=!!id!!",
						'subcollection' => $opac_url_base."index.php?lvl=subcoll_see&id=!!id!!",
						'serie' => $opac_url_base."index.php?lvl=serie_see&id=!!id!!",
						'titre_uniforme' => $opac_url_base."index.php?lvl=titre_uniforme_see&id=!!id!!",
						'indexint' => $opac_url_base."index.php?lvl=indexint_see&id=!!id!!",
						'authperso' => $opac_url_base."index.php?lvl=authperso_see&id=!!id!!",
						"concept" => $opac_url_base."index.php?lvl=concept_see&id=!!id!!"
				);
			} else {
				static::$linked_permalink[$use_opac_url_base] = array(
						'author' => $base_path."/index.php?lvl=author_see&id=!!id!!",
						'category' => $base_path."/index.php?lvl=categ_see&id=!!id!!",
						'publisher' => $base_path."/index.php?lvl=publisher_see&id=!!id!!",
						'collection' => $base_path."/index.php?lvl=coll_see&id=!!id!!",
						'subcollection' => $base_path."/index.php?lvl=subcoll_see&id=!!id!!",
						'serie' => $base_path."/index.php?lvl=serie_see&id=!!id!!",
						'titre_uniforme' => $base_path."/index.php?lvl=titre_uniforme_see&id=!!id!!",
						'indexint' => $base_path."/index.php?lvl=indexint_see&id=!!id!!",
						'authperso' => $base_path."/index.php?lvl=authperso_see&id=!!id!!",
						"concept" => $base_path."/index.php?lvl=concept_see&id=!!id!!"
				);
			}
		}
		return static::$linked_permalink[$use_opac_url_base];
	}
	
	/**
	 * Retourne l'affichage des documents numériques
	 * @param int $notice_id Identifiant de la notice
	 * @return string Rendu html des documents numériques
	 */
	static public function get_display_explnums($notice_id) {
		global $include_path;
		global $msg;
		global $nb_explnum_visible;
		
		require_once($include_path."/explnum.inc.php");

		$record_datas = static::get_record_datas($notice_id);
		$bull = $record_datas->get_bul_info();
		$bulletin_id = (isset($bull['bulletin_id']) ? $bull['bulletin_id'] : '');
		
		if ($record_datas->get_niveau_biblio() == "b" && ($explnums = show_explnum_per_notice(0, $bulletin_id, ''))) {
			return "<a name='docnum'><h3><span id='titre_explnum'>".$msg['explnum']." (".$nb_explnum_visible.")</span></h3></a>".$explnums;
		}
		if ($explnums = show_explnum_per_notice($notice_id, 0, '')) {
			return "<a name='docnum'><h3><span id='titre_explnum'>".$msg['explnum']." (".$nb_explnum_visible.")</span></h3></a>".$explnums;
		}
		return "";
	}
	
	static public function get_display_size($notice_id) {
		$record_datas = static::get_record_datas($notice_id);
		
		$size = array();
		if ($record_datas->get_npages()) $size[] = $record_datas->get_npages();
		if ($record_datas->get_ill()) $size[] = $record_datas->get_ill();
		if ($record_datas->get_size()) $size[] = $record_datas->get_size();
		
		return implode(" / ", $size);
	}
	
	static public function get_display_demand($notice_id) {
		global $msg, $charset, $include_path, $form_modif_demande, $form_linked_record, $demandes_active, $opac_demandes_allow_from_record;
		
		if ($demandes_active && $opac_demandes_allow_from_record && $_SESSION['id_empr_session']) {
			$record_datas = static::get_record_datas($notice_id);
			$demande = new demandes();
			$themes = new demandes_themes('demandes_theme','id_theme','libelle_theme',$demande->theme_demande);
			$types = new demandes_types('demandes_type','id_type','libelle_type',$demande->type_demande);
			
			$f_modif_demande = $form_modif_demande;
			$f_modif_demande = str_replace('!!form_title!!',htmlentities($msg['demandes_creation'],ENT_QUOTES,$charset),$f_modif_demande);
			$f_modif_demande = str_replace('!!sujet!!','',$f_modif_demande);
			$f_modif_demande = str_replace('!!progression!!','',$f_modif_demande);
			$f_modif_demande = str_replace('!!empr_txt!!','',$f_modif_demande);
			$f_modif_demande = str_replace('!!idempr!!',$_SESSION['id_empr_session'],$f_modif_demande);
			$f_modif_demande = str_replace('!!iduser!!',"",$f_modif_demande);
			$f_modif_demande = str_replace('!!titre!!','',$f_modif_demande);
				
			$etat=$demande->getStateValue();
			$f_modif_demande = str_replace('!!idetat!!',$etat['id'],$f_modif_demande);
			$f_modif_demande = str_replace('!!value_etat!!',$etat['comment'],$f_modif_demande);
			$f_modif_demande = str_replace('!!select_theme!!',$themes->getListSelector(),$f_modif_demande);
			$f_modif_demande = str_replace('!!select_type!!',$types->getListSelector(),$f_modif_demande);
				
			$date = formatdate(today());
			$date_debut=date("Y-m-d",time());
			$date_dmde = "<input type='button' class='bouton' id='date_debut_btn' name='date_debut_btn' value='!!date_debut_btn!!'
					onClick=\"openPopUp('./select.php?what=calendrier&caller=modif_dmde&date_caller=!!date_debut!!&param1=date_debut&param2=date_debut_btn&auto_submit=NO&date_anterieure=YES', 'date_debut', 250, 300, -2, -2, 'toolbar=no, dependent=yes, resizable=yes')\"/>";
			$f_modif_demande = str_replace('!!date_demande!!',$date_dmde,$f_modif_demande);
				
			$f_modif_demande = str_replace('!!date_fin_btn!!',$date,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_debut_btn!!',$date,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_debut!!',$date_debut,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_fin!!',$date_debut,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_prevue!!',$date_debut,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_prevue_btn!!',$date,$f_modif_demande);
				
			$f_modif_demande = str_replace('!!iddemande!!', '', $f_modif_demande);
			
			$f_modif_demande = str_replace('!!form_linked_record!!', $form_linked_record, $f_modif_demande);
			$f_modif_demande = str_replace('!!linked_record!!', $record_datas->get_tit1(), $f_modif_demande);
			$f_modif_demande = str_replace("!!linked_record_id!!", $notice_id, $f_modif_demande);
			$f_modif_demande = str_replace("!!linked_record_link!!", $record_datas->get_permalink(), $f_modif_demande);
			
			$act_cancel = "demandDialog_".$notice_id.".hide();";
			$act_form = "./empr.php?tab=request&lvl=list_dmde&sub=save_demande";
			
			$f_modif_demande = str_replace('!!form_action!!',$act_form,$f_modif_demande);
			$f_modif_demande = str_replace('!!cancel_action!!',$act_cancel,$f_modif_demande);
			
			// Requires et début de formulaire
			$html = "
					<script type='text/javascript'>
						require(['dojo/parser', 'apps/pmb/PMBDialog']);
						document.body.setAttribute('class', 'tundra');
					</script>
					<div data-dojo-type='apps/pmb/PMBDialog' data-dojo-id='demandDialog_".$notice_id."' title='".$msg['do_demande_on_record']."' style='display:none;width:75%;'>
						".$f_modif_demande."
					</div>
					<a href='#' onClick='demandDialog_".$notice_id.".show();return false;'>
						".$msg['do_demande_on_record']."
					</a>";
			
			return $html;
		}
		return "";
	}
	
	/**
	 * Retourne le rendu html des documents numériques du bulletin parent de la notice d'article
	 * @param int $notice_id Identifiant de la notice
	 * @return string Rendu html des documents numériques du bulletin parent
	 */
	static public function get_display_bull_for_art_expl_num($notice_id) {
		
		$record_datas = static::get_record_datas($notice_id);
		$bul_infos = $record_datas->get_bul_info();
		
		$paramaff["mine_type"]=1;
		$retour = show_explnum_per_notice(0, $bul_infos['bulletin_id'],"",$paramaff);

		return $retour;
	}
	
	static public function get_special($notice_id) {
		global $include_path;
		
		$directory = static::get_parameter_value('notices_format_django_directory');
		if (!$directory) {
			$directory = 'common';
		}
		
		$classpath = $include_path."/templates/record/".$directory."/special/".$directory."_special.class.php";
		$class = "";
		if(file_exists($classpath)){
			require_once $classpath;
			$class = $directory."_special";
		}
		if(!class_exists($class)){
			return null;
		}
		if (!isset(self::$special[$notice_id])) {
			self::$special[$notice_id] = new $class(self::$records_datas[$notice_id]);
		}
		return self::$special[$notice_id];
	}
	
	static public function get_display_scan_request($notice_id) {
		global $msg;
		global $opac_scan_request_activate, $allow_scan_request;
		
		$html = "";
		$record_datas = static::get_record_datas($notice_id);
		if($opac_scan_request_activate && $_SESSION['id_empr_session'] && $allow_scan_request
		    && is_null($record_datas->get_dom_2()) && $record_datas->is_visu_scan_request() && (!$record_datas->is_visu_scan_request_abon() || ($record_datas->is_visu_scan_request_abon() && $_SESSION["user_code"])) || ($record_datas->get_rights() & 32)) {
			$scan_request = new scan_request();
			if($record_datas->get_niveau_biblio() == 'b') {
				$bul_infos = $record_datas->get_bul_info();
// 				$scan_request->add_linked_records(array('bulletins' => array($bul_infos['bulletin_id'])));
				$html = $scan_request->get_link_in_record($bul_infos['bulletin_id'], 'bulletins');
			} else {
// 				$scan_request->add_linked_records(array('notices' => array($notice_id)));
				$html = $scan_request->get_link_in_record($notice_id);
			}
		}
		return $html;
	}
	
	/**
	 * Fonction d'affichage des réseaux sociax
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_social_network($notice_id){
		global $charset;
		
		$record_datas = static::get_record_datas($notice_id);
		if(isset($_SESSION["opac_view"])) {
			$permalink = $record_datas->get_permalink()."&opac_view=".$_SESSION["opac_view"];
		} else {
			$permalink = $record_datas->get_permalink();
		}
		return "
			<div id='el".$notice_id."addthis' class='addthis_toolbox addthis_default_style '
				addthis:url='".static::get_parameter_value('url_base')."fb.php?title=".rawurlencode(strip_tags(($charset != "utf-8" ? utf8_encode($record_datas->get_tit1()) : $record_datas->get_tit1())))."&url=".rawurlencode(($charset != "utf-8" ? utf8_encode($permalink) : $permalink))."'>
			</div>
			<script type='text/javascript'>
				if(param_social_network){
					creeAddthis('el".$notice_id."');
				}else{
					waitingAddthisLoaded('el".$notice_id."');
				}
			</script>";
	
	}
	
	static public function get_display_serialcirc_form_actions($notice_id) {
		global $msg, $charset;
		global $allow_serialcirc;
		
		$html = "";
		$record_datas = static::get_record_datas($notice_id);
		if($_SESSION['id_empr_session'] && static::get_parameter_value('serialcirc_active') && $record_datas->get_opac_serialcirc_demande() && $allow_serialcirc) {
			if($record_datas->get_niveau_biblio() == "s"){
				// pour un pério, on affiche un bouton pour demander l'inscription à un liste de diffusion
				//TODO si le statut le permet...
				$html .= "
					<div class='row'>&nbsp;</div>
					<div class='row'>&nbsp;</div>
					<div class='row'>
						<form method='post' action='empr.php?tab=serialcirc&lvl=ask&action=subscribe'>
							<input type='hidden' name='serial_id' value='".htmlentities($notice_id,ENT_QUOTES,$charset)."'/>
							<input type='submit' class='bouton' value='".htmlentities($msg['serialcirc_ask_subscribtion'],ENT_QUOTES,$charset)."'/>
						</form>
					</div>";
			}else if ($record_datas->get_niveau_biblio() == "b"){
				// pour un bulletin, on regarde s'il est pas en cours d'inscription...
				// récup la circulation si existante...
				$query = "select id_serialcirc from serialcirc join abts_abts on abt_id = num_serialcirc_abt join bulletins on bulletin_notice = abts_abts.num_notice where bulletins.num_notice = ".$notice_id;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$id_serialcirc = pmb_mysql_result($result,0,0);
					$serialcirc = new serialcirc($id_serialcirc);
					if($serialcirc->is_virtual()){
						if($serialcirc->empr_is_subscribe($_SESSION['id_empr_session'])){
							$query ="select num_serialcirc_expl_id from serialcirc_expl where num_serialcirc_expl_serialcirc = ".$id_serialcirc." and serialcirc_expl_start_date = 0";
							$result = pmb_mysql_query($query);
							if(pmb_mysql_num_rows($result)){
								$expl_id = pmb_mysql_result($result,0,0);
								$serialcirc_empr_circ = new serialcirc_empr_circ($_SESSION['id_empr_session'],$id_serialcirc,$expl_id);
								$html.= $serialcirc_empr_circ->get_actions_form();
							}
						}
					}
				}
			}
		}
		return $html;
	}
	
	static public function get_display_bulletins_list($notice_id, $bulletins_id = array()) {
		global $page, $premier, $f_bull_deb_id, $nb_per_page_custom;
		global $bull_date_start, $bull_date_end;
		global $msg;
		global $opac_show_links_invisible_docnums;
		global $gestion_acces_active, $gestion_acces_empr_notice, $gestion_acces_empr_docnum;
		global $opac_bull_results_per_page;
		global $opac_fonction_affichage_liste_bull;
		
		$notice_id = $notice_id*1;
		
		if ($notice_id) {
			$record_datas = static::get_record_datas($notice_id);
			if ($record_datas->get_niveau_biblio() != 's') {
				return null;
			}
		}
		
		global ${"bull_num_deb_".$notice_id};
		$html = '';

		//Recherche dans les numéros
		$start_num = ${"bull_num_deb_".$notice_id};
		$restrict_date = "";
		if($f_bull_deb_id){
			$restrict_num = " and date_date >='".$f_bull_deb_id."' ";
		} else if($start_num){
			$restrict_num = " and bulletin_numero like '%".$start_num."%' ";
		} else {
			$restrict_num = "";
		}

		// Recherche dans les dates et libellés de période
		if(!$restrict_num) {
			if($bull_date_start && $bull_date_end){
				if($bull_date_end < $bull_date_start){
					$restrict_date = " and date_date between '".$bull_date_end."' and '".$bull_date_start."' ";
				} else if($bull_date_end == $bull_date_start) {
					$restrict_date = " and date_date='".$bull_date_start."' ";
				} else {
					$restrict_date = " and date_date between '".$bull_date_start."' and '".$bull_date_end."' ";
				}
			} else if($bull_date_start){
				$restrict_date = " and date_date >='".$bull_date_start."' ";
			} else if($bull_date_end){
				$restrict_date = " and date_date <='".$bull_date_end."' ";
			}
		}

		// nombre de références par pages (12 par défaut)
		if (!isset($opac_bull_results_per_page)) $opac_bull_results_per_page = 12;
		if(!$page) $page = 1;
		$debut = ($page-1)*$opac_bull_results_per_page;
		$limiter = " LIMIT ".$debut.",".$opac_bull_results_per_page;
		
		//Recherche par numéro
		$num_field_start = "
		<input type='hidden' name='f_bull_deb_id' id='f_bull_deb_id' />
		<input id='bull_num_deb_".$notice_id."' name='bull_num_deb_".$notice_id."' type='text' size='10' completion='bull_num' autfield='f_bull_deb_id' value='".$start_num."'>";
		
		//Recherche par date
		$deb_value = str_replace("-","",$bull_date_start);
		$fin_value = str_replace("-","",$bull_date_end);
		$date_deb_value = ($deb_value ? formatdate($deb_value) : '...');
		$date_fin_value = ($fin_value ? formatdate($fin_value) : '...');
		$date_debut = "<div id='inputs_bull_date_start'>
		    <input type='text' style='width: 10em;' name='bull_date_start' id='bull_date_start' 
				data-dojo-type='dijit/form/DateTextBox' required='false' value='".$bull_date_start."' />
			<input type='button' class='bouton' name='del' value='X' onclick=\"empty_dojo_calendar_by_id('bull_date_start');\" />
			</div>
		";
		$date_fin = "<div id='inputs_bull_date_end'>
		    <input type='text' style='width: 10em;' name='bull_date_end' id='bull_date_end' 
				data-dojo-type='dijit/form/DateTextBox' required='false' value='".$bull_date_end."' />
			<input type='button' class='bouton' name='del' value='X' onclick=\"empty_dojo_calendar_by_id('bull_date_end');\" />
			</div>
		";
		
		$tableau = "
		<a name='tab_bulletin'></a>
		<h3>".$msg['perio_list_bulletins']."</h3>
		<div id='form_search_bull'>
			<div class='row'></div>\n
			<script src='./includes/javascript/ajax.js'></script>
			<form name=\"form_values\" action='#tab_bulletin' method=\"post\" onsubmit=\"if (document.getElementById('onglet_isbd".$notice_id."').className=='isbd_public_active') document.form_values.premier.value='ISBD'; else document.form_values.premier.value='PUBLIC';document.form_values.page.value=1;\">\n
				<input type=\"hidden\" name=\"premier\" value=\"\">\n
				<input type=\"hidden\" name=\"page\" value=\"".$page."\">\n
				<input type=\"hidden\" name=\"nb_per_page_custom\" value=\"".$nb_per_page_custom."\">\n
				<table>
					<tr>
						<td class='align_left' rowspan=2><strong>".$msg["search_bull"]."&nbsp;:&nbsp;</strong></td>
						<td class='align_right'><strong>".$msg["search_per_bull_num"]." : ".$msg["search_bull_exact"]."</strong></td>
						<td >".$num_field_start."</td>
						<td >&nbsp;</td>
					</tr>
					<tr>
						<td class='align_right'><strong>".$msg["search_per_bull_date"]." : ".$msg["search_bull_start"]."</strong></td>
						<td>".$date_debut."</td>
						<td><strong>".$msg["search_bull_end"]."</strong> ".$date_fin."</td>
						<td>&nbsp;&nbsp;<input type='button' class='boutonrechercher' value='".$msg["142"]."' onclick='submit();'></td>
					</tr>
				</table>
			</form>
			<div class='row'></div><br />
		</div>\n";
		$html.= $tableau;
		
		//quel affichage de notice il faut utiliser (Public, ISBD) (valeur postée)
		if ($premier) {
			$html.= "<script> show_what('".$premier."','".$notice_id."'); </script>";
		}
		
		$html.= "<script type='text/javascript'>ajax_parse_dom();</script>";
	
		$join_docnum_noti = $join_docnum_bull = "";
		if (($gestion_acces_active == 1) && ($gestion_acces_empr_notice == 1)) {
			$ac = new acces();
			$dom_2= $ac->setDomain(2);
			$join_noti = $dom_2->getJoin($_SESSION["id_empr_session"],4,"bulletins.num_notice");
			$join_bull = $dom_2->getJoin($_SESSION["id_empr_session"],4,"bulletins.bulletin_notice");
			if(!$opac_show_links_invisible_docnums){
				$join_docnum_noti = $dom_2->getJoin($_SESSION["id_empr_session"],16,"bulletins.num_notice");
				$join_docnum_bull = $dom_2->getJoin($_SESSION["id_empr_session"],16,"bulletins.bulletin_notice");
			}
		}else{
			$join_noti = "join notices on bulletins.num_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			$join_bull = "join notices on bulletins.bulletin_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			if(!$opac_show_links_invisible_docnums){
				$join_docnum_noti = "join notices on bulletins.num_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
				$join_docnum_bull = "join notices on bulletins.bulletin_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
			}
		}
		$join_docnum_explnum = "";
		if(!$opac_show_links_invisible_docnums) {
			if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
				$ac = new acces();
				$dom_3= $ac->setDomain(3);
				$join_docnum_explnum = $dom_3->getJoin($_SESSION["id_empr_session"],16,"explnum_id");
			}else{
				$join_docnum_explnum = "join explnum_statut on explnum_docnum_statut=id_explnum_statut and ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
			}
		}
		
		$restriction = " 1";
		if (count($bulletins_id)) {
			$restriction = " bulletins.bulletin_id in (".implode(",", $bulletins_id).")";
		} else if ($notice_id) {
			$restriction = " bulletin_notice = ".$notice_id;
		}
		
		$requete_docnum_noti = "select bulletin_id, count(explnum_id) as nbexplnum from explnum join bulletins on explnum_bulletin = bulletin_id and explnum_notice = 0 ".$join_docnum_explnum." where ".$restriction." and explnum_bulletin in (select bulletin_id from bulletins ".$join_docnum_noti." where ".$restriction.") group by bulletin_id";
		$requete_docnum_bull = "select bulletin_id, count(explnum_id) as nbexplnum from explnum join bulletins on explnum_bulletin = bulletin_id and explnum_notice = 0 ".$join_docnum_explnum." where ".$restriction." and explnum_bulletin in (select bulletin_id from bulletins ".$join_docnum_bull." where ".$restriction.") group by bulletin_id";
		$requete_noti = "select bulletins.*,ifnull(nbexplnum,0) as nbexplnum from bulletins ".$join_noti." left join (".$requete_docnum_noti.") as docnum_noti on bulletins.bulletin_id = docnum_noti.bulletin_id where bulletins.num_notice != 0 and ".$restriction." ".$restrict_num." ".$restrict_date." GROUP BY bulletins.bulletin_id";
		$requete_bull = "select bulletins.*,ifnull(nbexplnum,0) as nbexplnum from bulletins ".$join_bull." left join ($requete_docnum_bull) as docnum_bull on bulletins.bulletin_id = docnum_bull.bulletin_id where bulletins.num_notice = 0 and ".$restriction." ".$restrict_num." ".$restrict_date." GROUP BY bulletins.bulletin_id";
	
		$requete = "select * from (".$requete_noti." union ".$requete_bull.") as uni where 1 ".$restrict_num." ".$restrict_date;
		$rescount1 = pmb_mysql_query($requete);
		$count1 = pmb_mysql_num_rows($rescount1);
	
		//si on recherche par date ou par numéro, le résultat sera trié par ordre croissant
		if ($restrict_num || $restrict_date) {
			$requete.=" ORDER BY date_date, bulletin_numero*1 ";
		} else {
			$requete.= " ORDER BY date_date DESC, bulletin_numero*1 DESC";
		}
		$requete.= $limiter;
		$res = pmb_mysql_query($requete);
		$count = pmb_mysql_num_rows($res);
	
		if ($count) {
			if ($opac_fonction_affichage_liste_bull) {
				$html.= call_user_func_array($opac_fonction_affichage_liste_bull, array($res, false));
			} else {
				affichage_liste_bulletins_normale($res);
			}
		} else {
			$html.= "<br /><strong>".$msg["bull_no_found"]."</strong>";
		}
		$html.= "<br /><br /><div class='row'></div>";
		// constitution des liens
		if (!$count1) $count1 = $count;
		$url_page = "javascript:if (document.getElementById(\"onglet_isbd".$notice_id."\")) if (document.getElementById(\"onglet_isbd".$notice_id."\").className==\"isbd_public_active\") document.form_values.premier.value=\"ISBD\"; else document.form_values.premier.value=\"PUBLIC\"; document.form_values.page.value=!!page!!; document.form_values.submit()";
		$nb_per_page_custom_url = "javascript:document.form_values.nb_per_page_custom.value=!!nb_per_page_custom!!";
		$action = "javascript:if (document.getElementById(\"onglet_isbd".$notice_id."\")) if (document.getElementById(\"onglet_isbd".$notice_id."\").className==\"isbd_public_active\") document.form_values.premier.value=\"ISBD\"; else document.form_values.premier.value=\"PUBLIC\"; document.form_values.page.value=document.form.page.value; document.form_values.submit()";
		
		if ($count) {
		    $html.= pmb_bidi("<div class='row'></div><div id='navbar'><br />\n<div style='text-align:center'>".printnavbar($page, $count1, $opac_bull_results_per_page, $url_page, $nb_per_page_custom_url, $action)."</div></div>");
		}
		return $html;
	}
	
	public static function get_display_isbd_with_link($notice_id = 0, $bulletin_id = 0) {
		if ($notice_id) {
			$display = aff_notice($notice_id, 0, 1, 0, AFF_ETA_NOTICES_REDUIT, '', 1, 0);
		} else {
			$display = "<a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$bulletin_id."'>".bulletin_header($bulletin_id)."</a><br />";
		}
		return $display;
	}
	
	public static function get_record_rights($notice_id = 0, $bulletin_id = 0) {
		global $gestion_acces_active,$gestion_acces_empr_notice;
	
		$rights = array(
				'visible' => false
		);
		$id_for_right = 0;
		if($bulletin_id) {
			$query = "select num_notice,bulletin_notice from bulletins where bulletin_id = '".$bulletin_id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$infos = pmb_mysql_fetch_object($result);
				if($infos->num_notice){
					//notice de bulletin
					$id_for_right = $infos->num_notice;
				}else{
					//notice de pério
					$id_for_right = $infos->bulletin_notice;
				}
			}
		} else {
			$id_for_right = $notice_id;
		}
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac = new acces();
			$dom_2= $ac->setDomain(2);
			if($dom_2->getRights($_SESSION['id_empr_session'],$id_for_right, 4)) {
				$rights['visible'] = true;
			}
		} else {
			$query = "SELECT notice_visible_opac, notice_visible_opac_abon FROM notice_statut JOIN notices ON notices.statut = notice_statut.id_notice_statut WHERE notice_id='".$id_for_right."' ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				if($row->notice_visible_opac && (!$row->notice_visible_opac_abon || ($row->notice_visible_opac_abon && $_SESSION['id_empr_session']))) {
					$rights['visible'] = true;
				}
			}
		}
		return $rights;
	}
	
	static public function get_display_links_for_serials($notice_id) {
		global $msg;
		
		$record_datas = static::get_record_datas($notice_id);
		
		$links_for_serials = '';
		
		if ($record_datas->get_niveau_biblio() == "s") {
			
			$voir_bulletins = '';
			$voir_docnum_bulletins = '';
			$search_in_serial = '';
			
			if ($record_datas->get_bulletins()) {
				$voir_bulletins="<a href='#tab_bulletin'><i>".$msg["see_bull"]."</i></a>";
			}
			if (static::get_parameter_value('visionneuse_allow') && $record_datas->get_opac_visible_bulletinage()) {
				if ($record_datas->get_nb_bulletins_docnums()) {
					$voir_docnum_bulletins="
					<a href='#' onclick=\"open_visionneuse(sendToVisionneusePerio".$notice_id.");return false;\">".$msg["see_docnum_bull"]."</a>
					<script type='text/javascript'>
						function sendToVisionneusePerio".$notice_id."(){
							document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$notice_id."&bull_only=1';
						}
					</script>";
				}
			}
			if ($record_datas->is_open_to_search()) {
				$search_in_serial ="<a href='index.php?lvl=index&search_type_asked=extended_search&search_in_perio=".$notice_id."'><i>".$msg["rechercher_in_serial"]."</i></a>";
			}
			
			if ($voir_bulletins || $voir_docnum_bulletins || $search_in_serial) {
				$links_for_serials = "<div class='links_for_serials'>
						<ul>";
				if ($voir_bulletins) {
					$links_for_serials .= "<li class='see_bull'>".$voir_bulletins."</li>";
				}
				if ($voir_docnum_bulletins) {
					$links_for_serials .= "<li class='see_docsnums'>".$voir_docnum_bulletins."</li>";
				}
				if ($search_in_serial) {
					$links_for_serials .= "<li class='rechercher_in_serial'>".$search_in_serial."</li>";
				}
				$links_for_serials.="
						</ul>
					</div>";
			}
		}

		return $links_for_serials;
	}
	
	static public function get_display_pnb_loan_button($notice_id) {
		global $msg;
		global $charset;
		return '<div id="zone_exemplaires">
					<h3>'.htmlentities($msg['pnb_digital_expl'], ENT_QUOTES, $charset).'</h3> 
					<a id="pnb_loan_book_' . $notice_id . '" href="#" >' . htmlentities($msg['empr_bt_checkout'], ENT_QUOTES, $charset).'</a>
					<script type="text/javascript">
						require(["dojo/dom", "dojo/on", "dojo/_base/lang", "dojox/widget/DialogSimple", "dijit/registry"], function(dom, on, lang, Dialog, registry){
							on(dom.byId("pnb_loan_book_' . $notice_id . '"), "click", lang.hitch(this, function(){
								var dialog = new Dialog({
							        title: "'.htmlentities($msg['empr_bt_checkout'], ENT_QUOTES, $charset).'",
							        href: "./ajax.php?module=ajax&categ=pnb&action=get_loan_form&notice_id='.$notice_id.'",
							        id: "loan_popup",
            						executeScripts: true,
							    });
								dialog.show();
							    var oldHide = dialog.hide;
							    dialog.hide = lang.hitch(dialog, function(){
							    	oldHide();
							       	dialog.destroyRecursive();
					        		var standby = registry.byId("standby_loan");
									if(standby){
					        			standby.destroy();
					        		}
							    });
							}));
						});
					</script>
				</div>';
	}
	
	static protected function get_parameter_value($name) {
		$parameter_name = 'opac_'.$name;
		global ${$parameter_name};
		return ${$parameter_name};
	}
	
	/**
	 * Retourne l'affichage étendu d'une notice externe
	 * @param unknown $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_unimarc($notice_id, $django_directory = "") {
	    global $include_path;
	    
	    $record_unimarc_datas = static::get_record_unimarc_datas($notice_id);
	    
	    $template = static::get_template("record_unimarc_display", $record_unimarc_datas->get_niveau_biblio(), $record_unimarc_datas->get_typdoc(), $django_directory, $record_unimarc_datas->get_connector_id(), $record_unimarc_datas->get_source_id());
	    
	    return static::render($notice_id, $template, true);
	}
	
	/**
	 * Retourne une instance de notice_affichage_unimarc
	 * @param int $notice_id Identifiant de la notice
	 * @return notice_affichage_unimarc
	 */
	static public function get_record_unimarc_datas($notice_id, $entrepots_localisations = array()) {
	    if (!isset(self::$records_unimarc_datas[$notice_id])) {
	        self::$records_unimarc_datas[$notice_id] = new record_datas_unimarc($notice_id, $entrepots_localisations);
	    }
	    return self::$records_unimarc_datas[$notice_id];
	}
	
	/**
	 * Retourne l'affichage d'une notice externe dans un résultat de recherche
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_unimarc_in_result($notice_id, $django_directory = "", $entrepots_localisations = array()) {
	    global $include_path;
	    
	    $record_unimarc_datas = static::get_record_unimarc_datas($notice_id, $entrepots_localisations);
	    
	    $template = static::get_template("record_unimarc_in_result_display", $record_unimarc_datas->get_niveau_biblio(), $record_unimarc_datas->get_typdoc(), $django_directory, $record_unimarc_datas->get_connector_id(), $record_unimarc_datas->get_source_id());
	    
	    return static::render($notice_id, $template, true);
	}
	
	
	/**
	 * Retourne l'affichage d'une notice dans un formulaire de contribution
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_in_contribution($notice_id, $django_directory = "") {
	    $record_datas = static::get_record_datas($notice_id);
	    $template = static::get_template("record_in_contribution_display", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);
	    return static::render($notice_id, $template);
	}
}