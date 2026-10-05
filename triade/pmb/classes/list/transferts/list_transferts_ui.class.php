<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_ui.class.php,v 1.42.2.1.2.1 2025/02/20 09:18:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once($class_path.'/transfert.class.php');
require_once($include_path.'/templates/list/transferts/list_transferts_ui.tpl.php');
require_once ($class_path."/mono_display.class.php");
require_once ($class_path."/serial_display.class.php");
require_once ($class_path."/lender.class.php");
require_once ($class_path."/docs_statut.class.php");
require_once ($class_path."/docs_location.class.php");
require_once ($base_path."/circ/transferts/affichage.inc.php");

class list_transferts_ui extends list_ui {
	
	protected $cp;
	
	protected $displayed_cp;
	
	protected $override_environment_filters;
	
	protected function _get_query_base() {
		$query = 'select id_transfert from transferts
			INNER JOIN transferts_demande ON id_transfert=num_transfert
			INNER JOIN exemplaires ON num_expl=expl_id
			INNER JOIN docs_section ON expl_section=idsection
			INNER JOIN docs_location AS locd ON num_location_dest=locd.idlocation
			INNER JOIN docs_location AS loco ON num_location_source=loco.idlocation
			INNER JOIN lenders ON expl_owner=idlender
			INNER JOIN docs_statut ON expl_statut=idstatut
            LEFT JOIN resa ON resa_trans=id_resa
			LEFT JOIN empr ON resa_idempr=id_empr
			LEFT JOIN pret ON pret_idexpl=num_expl';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new transfert($row->id_transfert);
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $deflt_docs_location;
		
		$this->override_environment_filters = array();
		if(isset($filters['etat_demande'])) {
			$this->override_environment_filters['etat_demande'] = $filters['etat_demande'];
		}
		if(empty($this->filters)) {
			$this->filters = array(
					'site_origine' => $deflt_docs_location,
					'site_destination' => 0
			);
		}
		$this->filters['f_etat_date'] = '';
		$this->filters['f_etat_dispo'] = '';
		/**
		 * etat_transfert => (0 = non fini)
		 * etat_demande => (0 = non validée, 1 = validée, 2 = envoyée, 3 = aller fini, 4 = refus)
		 * type_transfert => (1 = aller-retour)
		 */
		$this->filters['etat_transfert'] = -1;
		$this->filters['etat_demande'] = -1;
		$this->filters['type_transfert'] = '';
		
		$this->filters['sens_transfert'] = -1;
		$this->filters['cb'] = '';
		parent::init_filters($filters);
	}
	
	protected function init_override_filters() {
		global $deflt_docs_location;
		
		if($this->filters['site_origine'] == static::FILTER_USER_PREFERENCE) {
			$this->filters['site_origine'] = $deflt_docs_location;
		}
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'id' => '1601',
					'record' => '233',
					'cb' => '232',
					'cote' => '296',
					'location' => '298',
					'statut' => '297',
					'empr' => 'transferts_circ_empr',
					'source' => 'transferts_circ_source',
					'destination' => 'transferts_circ_destination',
					'expl_owner' => '651',
					'formatted_date_creation' => 'transferts_circ_date_creation',
					'formatted_date_envoyee' => 'transferts_circ_date_envoi',
					'formatted_date_refus' => 'transferts_circ_date_refus',
					'formatted_date_reception' => 'transferts_circ_date_reception',
					'formatted_date_retour' => 'transferts_circ_date_retour',
					'formatted_date_acceptee' => 'transferts_circ_date_validation',
					'motif' => 'transferts_circ_motif',
					'motif_refus' => 'transferts_circ_motif_refus',
					'transfert_ask_user_num' => 'transferts_edition_ask_user',
					'transfert_send_user_num' => 'transferts_edition_send_user',
                    'transfert_ask_formatted_date' => 'transferts_popup_ask_date'
			)
		);
		
		$this->available_columns['custom_fields'] = array();
		$this->add_custom_fields_available_columns('notices', 'num_notice');
		$this->add_custom_fields_available_columns('expl', 'num_exemplaire');
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		global $transferts_tableau_nb_lignes;
		parent::init_default_pager();
		$this->pager['nb_per_page'] = $transferts_tableau_nb_lignes;
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('formatted_date_creation', 'desc');
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id':
	            return 'id_transfert';
	        case 'record' :
	            return '';
	        case 'section':
	            return 'section_libelle';
	        case 'cote':
	            return 'expl_cote';
	        case 'location':
	            return 'expl_location';
	        case 'cb':
	            return 'expl_cb';
	        case 'statut':
	            return 'statut_libelle';
	        case 'empr':
	            return "concat(empr_nom,' ',empr_prenom)";
	        case 'expl_owner':
	            return "lender_libelle";
	        case 'source':
	            return "loco.location_libelle";
	        case 'destination':
	            return "locd.location_libelle";
	        case 'formatted_date_creation':
	            if(static::class == 'list_transferts_demandes_ui') {
	                return "transferts_demande.date_creation";
	            } else {
	                return "transferts.date_creation";
	            }
	            return '';
	        case 'formatted_date_reception':
	            return "date_reception";
	        case 'formatted_date_envoyee':
	            return "date_envoyee";
	        case 'formatted_date_refus':
	            return "date_visualisee";
	        case 'formatted_date_acceptee':
	            return "date_visualisee";
	        case 'motif_refus':
	            return "motif_refus";
	        case 'transfert_ask_formatted_date':
	            return "transfert_ask_date";
	        case 'formatted_bt_date_retour':
	        case 'date_retour':
	            return "date_retour";
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	protected function set_filter_environment_from_form() {
		global $sub, $action;
		
		if(!isset($this->override_environment_filters)) {
			$this->override_environment_filters = array();
		}
		if(!empty($action)) {
			switch ($sub) {
				case 'valid':
					if($action == 'aff_refus') {
						// get environment validation
						$this->filters = array_merge($_SESSION['list_transferts_validation_ui_filter'], $this->override_environment_filters);
					}
					break;
				case 'envoi':
					if($action == 'aff_refus') {
						// get environment envoi
						$this->filters = array_merge($_SESSION['list_transferts_envoi_ui_filter'], $this->override_environment_filters);
					}
					break;
				case 'departs' :
					if($action == 'aff_refus') {
						// get environment envoi
						$this->filters = array_merge($_SESSION['list_transferts_validation_ui_filter'], $this->override_environment_filters);
					}
					break;
			}
		}
	}
	
	protected function set_filter_selection_from_form() {
		global $sub, $action;
		
		$numeros = '';
		$transferts_ui_selection = $this->objects_type.'_selection';
		if(!empty($action)) {
			switch ($sub) {
				case 'valid':
					if($action == 'aff_refus') {
						if(!empty($GLOBALS['transferts_validation_ui_selection'])) {
							$transferts_ui_selection = 'transferts_validation_ui_selection';
						}
					}
					break;
				case 'envoi':
					if($action == 'aff_refus') {
						if(!empty($GLOBALS['transferts_envoi_ui_selection'])) {
							$transferts_ui_selection = 'transferts_envoi_ui_selection';
						}
					}
					break;
				case 'departs' :
					if($action == 'aff_refus') {
						if(!empty($GLOBALS['transferts_validation_ui_selection'])) {
							$transferts_ui_selection = 'transferts_validation_ui_selection';
						} elseif(!empty($GLOBALS['transferts_envoi_ui_selection'])) {
							$transferts_ui_selection = 'transferts_envoi_ui_selection';
						}
					}
					break;
			}
		}
		global ${$transferts_ui_selection};
		if(!empty(${$transferts_ui_selection})) {
			$numeros = implode(',', ${$transferts_ui_selection});
		}
		$this->filters['ids'] = '';
		if($numeros) {
			$this->filters['ids'] = $numeros;
		}
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_environment_from_form();
		$this->set_filter_selection_from_form();
		$this->set_filter_from_form('site_origine', 'integer');
		$this->set_filter_from_form('site_destination', 'integer');
		$this->set_filter_from_form('f_etat_date', 'integer');
		$this->set_filter_from_form('f_etat_dispo', 'integer');
		$this->set_filter_from_form('cb');
		parent::set_filters_from_form();
	}
	
	protected function get_search_options_locations($loc_select,$tous = true) {
		global $msg;
	
		$options = '';
		$query = "SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle ";
		$result = pmb_mysql_query($query);
		if ($tous) {
			$options .= "<option value='0'>".$msg["all_location"]."</option>";
		}
		while ($row = pmb_mysql_fetch_object($result)) {
			$options .= "<option value='".$row->idlocation."' ".($row->idlocation==$loc_select ? "selected='selected'" : "").">";
			$options .= $row->location_libelle."</option>";
		}
		return $options;
	}
	
	protected function get_search_filter_site_origine() {
		global $msg;
		$query = "SELECT idlocation as id, location_libelle as label FROM docs_location ORDER BY label";
		$query = $this->_get_query_search_override_filter('site_origine', $query);
		return $this->get_search_filter_simple_selection($query, 'site_origine', $msg["all_location"]);
	}
	
	protected function get_search_filter_site_destination() {
		global $msg;
		$query = "SELECT idlocation as id, location_libelle as label FROM docs_location ORDER BY label";
		return $this->get_search_filter_simple_selection($query, 'site_destination', $msg["all_location"]);
	}
	
	protected function get_search_filter_f_etat_date() {
		global $msg;
	
		$options = array(
				0 => $msg["transferts_circ_retour_filtre_etat_tous"],
				1 => $msg["transferts_circ_retour_filtre_etat_proche"],
				2 => $msg["transferts_circ_retour_filtre_etat_depasse"]
		);
		return $this->get_search_filter_simple_selection('', 'f_etat_date', '', $options);
	}
	
	protected function get_search_filter_f_etat_dispo() {
		global $msg;
	
		$options = array(
				1 => $msg["transferts_circ_retour_filtre_dispo"],
				2 => $msg["transferts_circ_retour_filtre_circ"],
				0 => $msg["transferts_circ_retour_filtre_etat_tous"]
		);
		return $this->get_search_filter_simple_selection('', 'f_etat_dispo', '', $options);
	}
	
	protected function get_search_filter_cb() {
		global $charset;
		return "<input type='text' name='".$this->objects_type."_cb' value='".htmlentities($this->filters['cb'], ENT_QUOTES, $charset)."' />"; 	
	}
	
	protected function _add_query_filters() {
		global $transferts_nb_jours_alerte;
		
		$this->_add_query_filter_simple_restriction('site_origine', 'num_location_source', 'integer');
		$this->_add_query_filter_simple_restriction('site_destination', 'num_location_dest', 'integer');
		if($this->filters['f_etat_date']) {
			switch ($this->filters['f_etat_date']) {
				case "1":
					$this->query_filters [] = "(DATEDIFF(DATE_ADD(date_retour,INTERVAL -" . $transferts_nb_jours_alerte . " DAY),CURDATE())<=0
							AND DATEDIFF(date_retour,CURDATE())>=0)";
					break;
				case "2":
					$this->query_filters [] = "DATEDIFF(date_retour,CURDATE())<0";
					break;
			}
		}
		if($this->filters['f_etat_dispo']) {
			switch ($this->filters['f_etat_dispo']) {
				case 1 : // pas en pret et non réservé
					$this->query_filters [] = "if(id_resa, resa_confirmee=0, 1) and if(pret_idexpl,0 ,1) ";
					break;
				case 2 : // en pret et réservé seulement
					$this->query_filters [] = "( if(id_resa, resa_confirmee=1, 0) OR if(pret_idexpl,1 ,0) ) ";
					break;
			}
		}
		if($this->filters['etat_transfert'] !== '' && $this->filters['etat_transfert'] !== -1) {
			$this->query_filters [] = 'etat_transfert = "'.$this->filters['etat_transfert'].'"';
		}
		if(is_array($this->filters['etat_demande'])) {
			$this->query_filters [] = 'etat_demande IN ('.implode(',', $this->filters['etat_demande']).')';
		} elseif($this->filters['etat_demande'] !== '' && $this->filters['etat_demande'] !== -1) {
			$this->query_filters [] = 'etat_demande = "'.$this->filters['etat_demande'].'"';
		}
		if($this->filters['type_transfert'] !== '' && $this->filters['type_transfert'] !== -1) {
			$this->query_filters [] = 'type_transfert = "'.$this->filters['type_transfert'].'"';
		}
		if($this->filters['ids']) {
			$this->query_filters [] = 'id_transfert IN ('.$this->filters['ids'].')';
		}
		$this->_add_query_filter_simple_restriction('cb', 'exemplaires.expl_cb');
	}
	
	/**
	 * Fonction de callback
	 * @param object $a
	 * @param object $b
	 * * @param number $index
	 */
	protected function _compare_objects($a, $b, $index=0) {
	    if($this->applied_sort[$index]['by']) {
	        $sort_by = $this->applied_sort[$index]['by'];
			switch($sort_by) {
				case 'formatted_date_creation':
					return strcmp($a->get_date_creation(), $b->get_date_creation());
					break;
				case 'formatted_date_reception':
					return strcmp($a->get_transfert_demande()->get_date_reception(), $b->get_transfert_demande()->get_date_reception());
					break;
				case 'formatted_date_envoyee':
					return strcmp($a->get_transfert_demande()->get_date_envoyee(), $b->get_transfert_demande()->get_date_envoyee());
					break;
				case 'formatted_date_refus':
					return strcmp($a->get_transfert_demande()->get_date_visualisee(), $b->get_transfert_demande()->get_date_visualisee());
					break;
				case 'formatted_date_acceptee':
					return strcmp($a->get_transfert_demande()->get_date_visualisee(), $b->get_transfert_demande()->get_date_visualisee());
					break;
				case 'transfert_ask_formatted_date':
					return strcmp($a->get_transfert_ask_date(), $b->get_transfert_ask_date());
					break;
				case 'transfert_ask_user_num':
				case 'transfert_send_user_num':
					return strcmp(user::get_param(call_user_func_array(array($a, "get_".$sort_by), array()), 'username'), user::get_param(call_user_func_array(array($b, "get_".$sort_by), array()), 'username'));
					break;
				case 'transfert_bt_relancer':
					return '';
					break;
				case 'formatted_bt_date_retour':
					return strcmp($a->get_date_retour(), $b->get_date_retour());
					break;
				default :
				    return parent::_compare_objects($a, $b, $index);
					break;
			}
		}
	}
	
	protected function _get_object_property_record($object) {
		if($object->get_num_notice()) {
			return aff_titre($object->get_num_notice(), 0);
		} else {
			return aff_titre(0, $object->get_num_bulletin());
		}
	}
	
	protected function _get_object_property_section($object) {
		return $object->get_exemplaire()->section;
	}
	
	protected function _get_object_property_cote($object) {
		return $object->get_exemplaire()->cote;
	}
	
	protected function _get_object_property_location($object) {
		return $object->get_exemplaire()->location;
	}
	
	protected function _get_object_property_cb($object) {
		return $object->get_exemplaire()->cb;
	}
	
	protected function _get_object_property_statut($object) {
		$docs_statut = new docs_statut($object->get_exemplaire()->statut_id);
		return $docs_statut->libelle;
	}
	
	protected function _get_object_property_empr($object) {
		$id_resa = $object->get_transfert_demande()->get_resa_trans();
		if($id_resa) {
			$query = "select id_empr, empr_cb from empr join resa on id_empr = resa_idempr where id_resa = ".$id_resa;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result) == 1) {
				$row = pmb_mysql_fetch_object($result);
				return emprunteur::get_name($row->id_empr);
			}
		}
		return '';
	}
	
	protected function _get_object_property_expl_owner($object) {
		$lender = new lender($object->get_exemplaire()->owner_id);
		return $lender->lender_libelle;
	}
	
	protected function _get_object_property_source($object) {
		$docs_location = new docs_location($object->get_transfert_demande()->get_num_location_source());
		return $docs_location->libelle;
	}
	
	protected function _get_object_property_destination($object) {
		$docs_location = new docs_location($object->get_transfert_demande()->get_num_location_dest());
		return $docs_location->libelle;
	}
	
	protected function _get_object_property_motif_refus($object) {
		return $object->get_transfert_demande()->get_motif_refus();
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		global $base_path;
		
		$is_cp_property = false;
		if(isset($this->displayed_cp) && is_array($this->displayed_cp)) {
			$is_cp_property = array_search($property, $this->displayed_cp);
		}
		if($is_cp_property) {
			$this->cp->get_values($object->get_exemplaire()->expl_id);
			if(isset($this->cp->values[$is_cp_property])) {
				$values = $this->cp->values[$is_cp_property];
			} else {
				$values = array();
			}
			$aff_column=$this->cp->get_formatted_output($values, $is_cp_property);
			if (!$aff_column) $aff_column="&nbsp;";
			return $aff_column;
		} else {
			switch($property) {
				case 'record':
					return $this->_get_object_property_record($object); // conservation du HTML
				case 'cb':
					return aff_exemplaire($object->get_exemplaire()->{$property});
				case 'statut':
					$docs_statut = new docs_statut($object->get_exemplaire()->statut_id);
					return aff_statut_exemplaire($docs_statut->libelle.'###'.$object->get_exemplaire()->expl_id);
				case 'empr':
					$content = '';
					$id_resa = $object->get_transfert_demande()->get_resa_trans();
					if($id_resa) {
						$query = "select id_empr, empr_cb from empr join resa on id_empr = resa_idempr where id_resa = ".$id_resa;
						$result = pmb_mysql_query($query);
						if(pmb_mysql_num_rows($result) == 1) {
							$row = pmb_mysql_fetch_object($result);
							if (SESSrights & CIRCULATION_AUTH) {
								$content = "<a href='./circ.php?categ=pret&form_cb=".$row->empr_cb."'>";
								$content .= emprunteur::get_name($row->id_empr);
								$content .= "</a>";
							} else {
								$content .= emprunteur::get_name($row->id_empr);
							}
						}
					}
					return $content;
				case 'formatted_date_reception':
					return $object->get_transfert_demande()->get_formatted_date_reception();
				case 'formatted_date_envoyee':
					return $object->get_transfert_demande()->get_formatted_date_envoyee();
				case 'formatted_date_refus':
					return $object->get_transfert_demande()->get_formatted_date_visualisee();
				case 'formatted_date_acceptee':
					return $object->get_transfert_demande()->get_formatted_date_visualisee();
				case 'transfert_ask_user_num':
				case 'transfert_send_user_num':
					return user::get_param(call_user_func_array(array($object, "get_".$property), array()), 'username');
				case 'transfert_bt_relancer':
					return $this->get_interface_button($msg["transferts_circ_btRelancer"], ['location' => "./circ.php?categ=trans&sub=refus&action=aff_redem&transid=".$object->get_id()]);
				case 'formatted_bt_date_retour':
					return "<input type='button' class='bouton' name='bt_date_retour_".$object->get_id()."' value='".$object->get_formatted_date_retour()."' onClick=\"var reg=new RegExp('(-)', 'g'); openPopUp('".$base_path."/select.php?what=calendrier&caller=".$this->get_form_name()."&date_caller='+".$this->get_form_name().".date_retour_".$object->get_id().".value.replace(reg,'')+'&param1=date_retour_".$object->get_id()."&param2=bt_date_retour_".$object->get_id()."&auto_submit=NO&date_anterieure=YES&after=chgDate%28id_value,".$object->get_id()."%29', 'calendar')\" />
						<input type='hidden' name='date_retour_".$object->get_id()."' value='".$object->get_date_retour()."' />";
				default :
					return parent::get_cell_content($object, $property);
			}	
		}
	}
	
	protected function get_edition_link() {
		global $msg;
		global $sub;
		$edition_link = '';
		//le lien pour l'édition si on a le droit ...
		if (SESSrights & EDIT_AUTH) {
			$sub_url = $sub;
			if($sub == 'departs' || $sub == 'valid' || $sub == 'retour') {
			    switch (static::class) {
					case 'list_transferts_envoi_ui':
						$sub_url = 'envoi';
						break;
					case 'list_transferts_retours_ui':
						$sub_url = 'retours';
						break;
					case 'list_transferts_validation_ui':
						$sub_url = 'validation';
						break;
				}
			}
			$url_edition = "./edit.php?categ=transferts&sub=".$sub_url;
			//on applique la seletion du filtre
			if ($this->filters['site_origine']) {
				$url_edition .= "&site_origine=" .$this->filters['site_origine'];
			}
			if ($this->filters['site_destination']) {
				$url_edition .= "&site_destination=" .$this->filters['site_destination'];
			}
			$edition_link = "<a href='" . $url_edition . "'>".$msg[1100]."</a>";
		}
		return $edition_link;
	}
	
	/**
	 * Affichage des éléments de recherche
	 */
	public function get_search_content() {
	    global $msg;
		global $list_transferts_ui_parcours_search_content_form_tpl;
		global $action;
		
		$content_form = $list_transferts_ui_parcours_search_content_form_tpl;
		
		$content_form = str_replace('!!json_filters!!', json_encode($this->filters), $content_form);
		$content_form = str_replace('!!json_selected_columns!!', json_encode($this->selected_columns), $content_form);
		$content_form = str_replace('!!json_settings!!', json_encode($this->settings), $content_form);
		$content_form = str_replace('!!json_applied_group!!', json_encode($this->applied_group), $content_form);
		$content_form = str_replace('!!json_applied_sort!!', json_encode($this->applied_sort), $content_form);
		$content_form = str_replace('!!page!!', $this->pager['page'], $content_form);
		$content_form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $content_form);
		$content_form = str_replace('!!pager!!', json_encode($this->pager), $content_form);
		$content_form = str_replace('!!selected_filters!!', json_encode($this->selected_filters), $content_form);
		$content_form = str_replace('!!ancre!!', (!empty($this->ancre) ? $this->ancre : ''), $content_form);
		$content_form = str_replace('!!go_directly_to_ancre!!', '', $content_form);
		$content_form = str_replace('!!messages!!', $this->get_messages(), $content_form);
		$content_form = str_replace('!!objects_type!!', $this->objects_type, $content_form);
		$content_form = str_replace('!!export_icons!!', $this->get_export_icons(), $content_form);
		$content_form = str_replace('!!list_button_add!!', $this->get_button_add(), $content_form);
		$content_form = str_replace('!!list_search_content_form_tpl!!', $this->get_search_content_form(), $content_form);
		if($this->get_setting('display', 'search_form', 'unfolded_filters')) {
		    $content_form = str_replace('!!expandable_icon!!', get_url_icon('minus.gif'), $content_form);
		    $content_form = str_replace('!!unfolded_filters!!', 'block', $content_form);
		} else {
		    $content_form = str_replace('!!expandable_icon!!', get_url_icon('plus.gif'), $content_form);
		    $content_form = str_replace('!!unfolded_filters!!', 'none', $content_form);
		}
		if((!empty($this->is_displayed_options_block) || $this->get_setting('display', 'search_form', 'options')) && isset($this->available_columns)) {
		    $content_form = str_replace('!!list_options_content_form_tpl!!', $this->get_options_content_form(), $content_form);
		    if((!empty($this->is_displayed_datasets_block) || $this->get_setting('display', 'search_form', 'datasets')) && $action != 'dataset_apply' && $action != 'dataset_save') {
		        $content_form = str_replace('!!list_button_save!!', "<input type='button' id='".$this->objects_type."_button_save' class='bouton' value='".$msg['77']."' onclick=\"this.form.action = '".static::get_controller_url_base()."&action=dataset_edit&id=0'; this.form.submit();\" />", $content_form);
		    } else {
		        $content_form = str_replace('!!list_button_save!!', "", $content_form);
		    }
		} else {
		    $content_form = str_replace('!!list_options_content_form_tpl!!', '', $content_form);
		    $content_form = str_replace('!!list_button_save!!', '', $content_form);
		}
		
		if($this->is_session_values()) {
		    $content_form = str_replace('!!list_button_initialization!!', "<input type='button' id='".$this->objects_type."_button_initialization' class='bouton' value='".$msg['list_ui_initialization']."' onclick=\"this.form.".$this->objects_type."_initialization.value = 'reset'; this.form.submit();\" />", $content_form);
		} else {
		    $content_form = str_replace('!!list_button_initialization!!', '', $content_form);
		}
		$content_form = str_replace('!!list_buttons_extension!!', $this->get_search_buttons_extension(), $content_form);
		if(count($this->get_datasets()['my'])) {
		    $content_form = str_replace('!!list_datasets_my_content_form_tpl!!', $this->get_datasets_content_form('my'), $content_form);
		} else {
		    $content_form = str_replace('!!list_datasets_my_content_form_tpl!!', '', $content_form);
		}
		if(count($this->get_datasets()['shared'])) {
		    $content_form = str_replace('!!list_datasets_shared_content_form_tpl!!', $this->get_datasets_content_form('shared'), $content_form);
		} else {
		    $content_form = str_replace('!!list_datasets_shared_content_form_tpl!!', '', $content_form);
		}
		
		$content_form = str_replace('!!nb_res!!', $this->pager['nb_per_page'], $content_form);
// 		$content_form = str_replace('!!filters!!', $this->get_search_content_form(), $content_form);
// 		$content_form = str_replace('!!json_filters!!', json_encode($this->filters), $content_form);
// 		$content_form = str_replace('!!page!!', $this->pager['page'], $content_form);
// 		$content_form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $content_form);
// 		$content_form = str_replace('!!pager!!', json_encode($this->pager), $content_form);
// 		$content_form = str_replace('!!objects_type!!', $this->objects_type, $content_form);
		$content_form = str_replace('!!edition_link!!', $this->get_edition_link(), $content_form);
		return $content_form;
	}
	
	public function get_display_list() {
		global $current_module;
		global $list_transferts_ui_script_case_a_cocher;
		
		$display = '';
		if($current_module == 'circ') {
			$display .= "
			<br />
			<form name='".$this->get_form_name()."' class='form-".$current_module."' method='post' action='".static::get_controller_url_base()."'>
				".$this->get_form_title()."
				<div class='form-contenu' >";
			$display .= $this->get_search_content();
			if(count($this->objects)) {
				//Récupération du script JS de tris
				$display .= $this->get_js_sort_script_sort();
				$display .= $this->pager_top();
				//Affichage de la liste des objets
				$display .= "<table id='".$this->objects_type."_list' class='list_ui_list ".$this->objects_type."_list'>";
				$display .= $this->get_display_caption_list();
				$display .= $this->get_display_header_list();
				$display .= $this->get_display_content_list();
				$display .= "</table><br />";
				$display .= $this->get_display_selection_actions();
				$display .= $this->pager_bottom();
			} else {
				$display .= $this->get_display_no_results();
			}
			$display .= "</div>
                <input type='hidden' name='action'>
			    <input type='hidden' id='statut_reception_list' name='statut_reception'>
			    <input type='hidden' id='section_reception_list' name='section_reception'>
			</form>
			".$list_transferts_ui_script_case_a_cocher;
		} else {
			$display .= parent::get_display_list();
		}
		return $display;
	}
	
	public function get_display_valid_list() {
		global $msg;
		global $action;
		global $list_transferts_ui_valid_list_tpl;
		
		$display = $this->get_title();
		$display .= $list_transferts_ui_valid_list_tpl;
		
		$display = str_replace('!!submit_action!!', static::get_controller_url_base()."&action=".str_replace('aff_', '', $action), $display);
		$display = str_replace('!!valid_form_title!!', $this->get_valid_form_title(), $display);
		$display_valid_list = $this->get_display_header_list();
		if(count($this->objects)) {
			$display_valid_list .= $this->get_display_content_list();
		}
		$display = str_replace('!!valid_list!!', $display_valid_list, $display);
		$motif = '';
		if(static::class == 'list_transferts_refus_ui' && $action != 'aff_supp') {
			$motif .= "<hr />".$msg["transferts_circ_validation_refus_motif"]."<br />
					<textarea name='motif_refus' cols=60></textarea>"; 
		}
		$display = str_replace('!!motif!!', $motif, $display);
		$display = str_replace('!!valid_action!!', static::get_controller_url_base(), $display);
		$display = str_replace('!!ids!!', $this->filters['ids'], $display);
		$display = str_replace('!!objects_type!!', $this->objects_type, $display);
		return $display;
	}
	
	protected function _get_query_human_site_origine() {
		$docs_location = new docs_location($this->filters['site_origine']);
		return $docs_location->libelle;
	}
	
	protected function _get_query_human_site_destination() {
		$docs_location = new docs_location($this->filters['site_destination']);
		return $docs_location->libelle;
	}
	
	protected function _get_query_human_f_etat_date() {
		global $msg;
		
		$f_etat_date = '';
		if($this->filters['f_etat_date']) {
			switch ($this->filters['f_etat_date']) {
				case '1':
					$f_etat_date .= $msg["transferts_circ_retour_filtre_etat_proche"];
					break;
				case '2':
					$f_etat_date .= $msg["transferts_circ_retour_filtre_etat_depasse"];
					break;
			}
		}
		return $f_etat_date;
	}
	
	protected function _get_query_human_f_etat_dispo() {
		global $msg;
		
		$f_etat_dispo = '';
		if($this->filters['f_etat_dispo']) {
			switch ($this->filters['f_etat_dispo']) {
				case '1':
					$f_etat_dispo .= $msg["transferts_circ_retour_filtre_dispo"];
					break;
				case '2':
					$f_etat_dispo .= $msg["transferts_circ_retour_filtre_circ"];
					break;
			}
		}
		return $f_etat_dispo;
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
	
	protected function add_event_on_selection_action($action=array()) {
		global $current_module;
		
		if($current_module == 'circ') {
			$display = "
				on(dom.byId('".$this->objects_type."_selection_action_".$action['name']."_link'), 'click', function(event) {
					var selection = new Array();
					query('.".$this->objects_type."_selection:checked').forEach(function(node) {
						selection.push(node.value);
					});
					if(selection.length) {
						var confirm_msg = '".(isset($action['link']['confirm']) ? addslashes($action['link']['confirm']) : '')."';
						if(!confirm_msg || confirm(confirm_msg)) {
							if(document.getElementById('statut_reception') && document.getElementById('statut_reception_list')) {
				                var e = document.getElementById('statut_reception');
				                document.getElementById('statut_reception_list').value = e.options[e.selectedIndex].value;
				            }
				            if(document.getElementById('section_reception') && document.getElementById('section_reception_list')) {
				                var e = document.getElementById('section_reception');
				                document.getElementById('section_reception_list').value = e.options[e.selectedIndex].value;
				            }
							document.".$this->get_form_name().".action.value = 'aff_".$action['name']."';
							document.".$this->get_form_name().".submit();
						}
					} else {
						alert('".addslashes($this->get_error_message_empty_selection($action))."');
						event.preventDefault();
						return false;
					}
				});
			";
		} else {
			$display = parent::add_event_on_selection_action($action);
		}
		return $display;
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=transferts';
	}
}