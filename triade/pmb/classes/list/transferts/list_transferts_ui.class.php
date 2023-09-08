<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_ui.class.php,v 1.2 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/list_ui.class.php');
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
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$query = 'select id_transfert from transferts
			INNER JOIN transferts_demande ON id_transfert=num_transfert
			INNER JOIN exemplaires ON num_expl=expl_id
			INNER JOIN docs_section ON expl_section=idsection
			INNER JOIN docs_location AS locd ON num_location_dest=locd.idlocation
			INNER JOIN docs_location AS loco ON num_location_source=loco.idlocation
			INNER JOIN lenders ON expl_owner=idlender
			LEFT JOIN resa ON resa_trans=id_resa
			LEFT JOIN empr ON resa_idempr=id_empr
			LEFT JOIN pret ON pret_idexpl=num_expl';
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = new transfert($row->id_transfert);
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $deflt_docs_location;
		/**
		 * etat_transfert => (0 = non fini)
		 * etat_demande => (0 = non validée, 1 = validée, 2 = envoyée, 3 = aller fini, 4 = refus)
		 * type_transfert => (1 = aller-retour)
		 */
		$this->filters = array(
				'site_origine' => $deflt_docs_location,
				'site_destination' => 0,
				'f_etat_date' => '',
				'f_etat_dispo' => '',
				'etat_transfert' => '',
				'etat_demande' => '',
				'type_transfert' => '',
				'ids' => ''
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'record' => '233',
					'cb' => '232',
					'empr' => 'transferts_circ_empr',
					'source' => 'transferts_circ_source',
					'destination' => 'transferts_circ_destination',
					'expl_owner' => '651',
					'formatted_date_creation' => 'transferts_circ_date_creation',
					'formatted_date_envoyee' => 'transferts_circ_date_envoi',
					'formatted_date_refus' => 'transferts_circ_date_refus',
					'formatted_date_reception' => 'transferts_circ_date_reception',
					'formatted_date_retour' => 'transferts_circ_date_retour',
					'motif' => 'transferts_circ_motif',
					'transfert_ask_user_num' => 'transferts_edition_ask_user',
					'transfert_send_user_num' => 'transferts_edition_send_user',
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
		$this->pager = array(
				'page' => 1,
				'nb_per_page' => $transferts_tableau_nb_lignes,
				'nb_results' => 0,
				'nb_page' => 1
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'formatted_date_creation',
				'asc_desc' => 'desc'
		);
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		
		if($this->applied_sort['by']) {
			$order = '';
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				case 'id':
					$order .= 'id_transfert';
					break;
				case 'record' :
					break;
				case 'section':
					$order .= 'section_libelle';
					break;
				case 'cote':
					$order .= 'expl_cote';
					break;
				case 'cb':
					$order .= 'expl_cb';
					break;
				case 'statut':
					$order .= 'statut_libelle';
					break;
				case 'empr':
					$order .= "concat(empr_nom,' ',empr_prenom)";
					break;
				case 'expl_owner':
					$order .= "lender_libelle";
					break;
				case 'source':
					$order .= "loco.location_libelle";
					break;
				case 'destination':
					$order .= "locd.location_libelle";
					break;
				case 'formatted_date_creation':
					$order .= "transferts.date_creation";
					break;
				case 'formatted_date_reception':
					$order .= "date_reception";
					break;
				case 'formatted_date_envoyee':
					$order .= "date_envoyee";
					break;
				case 'formatted_date_refus':
					$order .= "date_visualisee";
					break;
				case 'motif_refus':
					$order .= "motif_refus";
					break;
				case 'transfert_ask_formatted_date':
					$order .= "transfert_ask_date";
					break;
				case 'formatted_bt_date_retour':
				case 'date_retour':
					$order .= "date_retour";
					break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				return " order by ".$order." ".$this->applied_sort['asc_desc']; 
			} else {
				return "";
			}
		}	
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$site_origine = $this->objects_type.'_site_origine';
		global ${$site_origine};
		if(isset(${$site_origine}) && ${$site_origine} != '') {
			$this->filters['site_origine'] = ${$site_origine}*1;
		}
		$site_destination = $this->objects_type.'_site_destination';
		global ${$site_destination};
		if(isset(${$site_destination}) && ${$site_destination} != '') {
			$this->filters['site_destination'] = ${$site_destination}*1;
		}
		$f_etat_date = $this->objects_type.'_f_etat_date';
		global ${$f_etat_date};
		if(isset(${$f_etat_date})) {
			$this->filters['f_etat_date'] = ${$f_etat_date}*1;
		}
		$f_etat_dispo = $this->objects_type.'_f_etat_dispo';
		global ${$f_etat_dispo};
		if(isset(${$f_etat_dispo})) {
			$this->filters['f_etat_dispo'] = ${$f_etat_dispo}*1;
		}
		$numeros = '';
		foreach ($_REQUEST as $k => $v) {
			//si c'est une case a cocher d'une liste
			if ((substr($k,0,4)=="sel_") && ($v=="1")) {
				//le no de transfert
				$numeros .= substr($k,4,strlen($k)) . ",";
			}
		}
		$this->filters['ids'] = '';
		if($numeros) {
			//on enleve la derniere virgule
			$numeros =  substr($numeros, 0, strlen($numeros)-1);
			$this->filters['ids'] = $numeros;
		}
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
	
	protected function get_search_retour_filtre_etat_selector() {
		global $msg, $charset;
	
		$selector = "<select name='".$this->objects_type."_f_etat_date'>";
		$selector .= "<option value='0' ".($this->filters['f_etat_date'] == 0 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_etat_tous"], ENT_QUOTES, $charset)."</option>";
		$selector .= "<option value='1' ".($this->filters['f_etat_date'] == 1 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_etat_proche"], ENT_QUOTES, $charset)."</option>";
		$selector .= "<option value='2' ".($this->filters['f_etat_date'] == 2 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_etat_depasse"], ENT_QUOTES, $charset)."</option>";
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_search_retour_filtre_etat_dispo_selector() {
		global $msg, $charset;
	
		$selector = "<select name='".$this->objects_type."_f_etat_dispo'>";
		$selector .= "<option value='1' ".($this->filters['f_etat_dispo'] == 1 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_dispo"], ENT_QUOTES, $charset)."</option>";
		$selector .= "<option value='2' ".($this->filters['f_etat_dispo'] == 2 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_circ"], ENT_QUOTES, $charset)."</option>";
		$selector .= "<option value='0' ".($this->filters['f_etat_dispo'] == 0 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_etat_tous"], ENT_QUOTES, $charset)."</option>";
		$selector .= "</select>";
		return $selector;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		global $transferts_nb_jours_alerte;
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['site_origine']) {
			$filters [] = 'num_location_source = "'.$this->filters['site_origine'].'"';
		}
		if($this->filters['site_destination']) {
			$filters [] = 'num_location_dest = "'.$this->filters['site_destination'].'"';
		}
		if($this->filters['f_etat_date']) {
			switch ($this->filters['f_etat_date']) {
				case "1":
					$filters [] = "(DATEDIFF(DATE_ADD(date_retour,INTERVAL -" . $transferts_nb_jours_alerte . " DAY),CURDATE())<=0
							AND DATEDIFF(date_retour,CURDATE())>=0)";
					break;
				case "2":
					$filters [] = "DATEDIFF(date_retour,CURDATE())<0";
					break;
			}
		}
		if($this->filters['f_etat_dispo']) {
			switch ($this->filters['f_etat_dispo']) {
				case 1 : // pas en pret et non réservé
					$filters [] = "if(id_resa, resa_confirmee=0, 1) and if(pret_idexpl,0 ,1) ";
					break;
				case 2 : // en pret et réservé seulement
					$filters [] = "( if(id_resa, resa_confirmee=1, 0) OR if(pret_idexpl,1 ,0) ) ";
					break;
			}
		}
		if($this->filters['etat_transfert'] !== '') {
			$filters [] = 'etat_transfert = "'.$this->filters['etat_transfert'].'"';
		}
		if(is_array($this->filters['etat_demande'])) {
			$filters [] = 'etat_demande IN ('.implode(',', $this->filters['etat_demande']).')';
		} elseif($this->filters['etat_demande'] !== '') {
			$filters [] = 'etat_demande = "'.$this->filters['etat_demande'].'"';
		}
		if($this->filters['type_transfert'] !== '') {
			$filters [] = 'type_transfert = "'.$this->filters['type_transfert'].'"';
		}
		if($this->filters['ids']) {
			$filters [] = 'id_transfert IN ('.$this->filters['ids'].')';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	/**
	 * Fonction de callback
	 * @param account $a
	 * @param account $b
	 */
	protected function _compare_objects($a, $b) {
		if($this->applied_sort['by']) {
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				case 'record' :
					if($a->get_num_notice()) {
						$content_a = aff_titre($a->get_num_notice(), 0);
					} else {
						$content_a = aff_titre(0, $a->get_num_bulletin());
					}
					if($b->get_num_notice()) {
						$content_b = aff_titre($b->get_num_notice(), 0);
					} else {
						$content_b = aff_titre(0, $b->get_num_bulletin());
					}
					return strcmp(strip_tags($content_a), strip_tags($content_b));
					break;
				case 'section':
				case 'cote':
				case 'cb':
					return strcmp($a->get_exemplaire()->{$sort_by}, $b->get_exemplaire()->{$sort_by});
					break;
				case 'statut':
					$docs_statut_a = new docs_statut($a->get_exemplaire()->statut_id);
					$docs_statut_b = new docs_statut($b->get_exemplaire()->statut_id);
					return strcmp($docs_statut_a->libelle, $docs_statut_b->libelle);
					break;
				case 'empr':
					$content_a = '';
					$id_resa = $a->get_transfert_demande()->get_resa_trans();
					if($id_resa) {
						$query = "select id_empr, empr_cb from empr join resa on id_empr = resa_idempr where id_resa = ".$id_resa;
						$result = pmb_mysql_query($query);
						if(pmb_mysql_num_rows($result) == 1) {
							$row = pmb_mysql_fetch_object($result);
							$content_a .= emprunteur::get_name($row->id_empr);
						}
					}
					$content_b = '';
					$id_resa = $b->get_transfert_demande()->get_resa_trans();
					if($id_resa) {
						$query = "select id_empr, empr_cb from empr join resa on id_empr = resa_idempr where id_resa = ".$id_resa;
						$result = pmb_mysql_query($query);
						if(pmb_mysql_num_rows($result) == 1) {
							$row = pmb_mysql_fetch_object($result);
							$content_b .= emprunteur::get_name($row->id_empr);
						}
					}
					return strcmp($content_a, $content_b);
					break;
				case 'expl_owner':
					$lender_a = new lender($a->get_exemplaire()->owner_id);
					$lender_b = new lender($b->get_exemplaire()->owner_id);
					return strcmp($lender_a->lender_libelle, $lender_b->lender_libelle);
					break;
				case 'source':
					$docs_location_a = new docs_location($a->get_transfert_demande()->get_num_location_source());
					$docs_location_b = new docs_location($b->get_transfert_demande()->get_num_location_source());
					return strcmp($docs_location_a->libelle, $docs_location_b->libelle);
					break;
				case 'destination':
					$docs_location_a = new docs_location($a->get_transfert_demande()->get_num_location_dest());
					$docs_location_b = new docs_location($b->get_transfert_demande()->get_num_location_dest());
					return strcmp($docs_location_a->libelle, $docs_location_b->libelle);
					break;
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
				case 'motif_refus':
					return strcmp($a->get_transfert_demande()->get_motif_refus(), $b->get_transfert_demande()->get_motif_refus());
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
					return parent::_compare_objects($a, $b);
					break;
			}
		}
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'transferts', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		global $base_path;
		
		$content = '';
		
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
			$content .= $aff_column;
		} else {
			switch($property) {
				case 'record':
					if($object->get_num_notice()) {
						$content .= aff_titre($object->get_num_notice(), 0);
					} else {
						$content .= aff_titre(0, $object->get_num_bulletin());
					}
					break;
				case 'section':
				case 'cote':
					$content .= $object->get_exemplaire()->{$property};
					break;
				case 'cb':
					$content .= aff_exemplaire($object->get_exemplaire()->{$property});
					break;
				case 'statut':
					$docs_statut = new docs_statut($object->get_exemplaire()->statut_id);
					$content .= aff_statut_exemplaire($docs_statut->libelle.'###'.$object->get_exemplaire()->expl_id);
					break;
				case 'empr':
					//TODO
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
					break;
				case 'expl_owner':
					$lender = new lender($object->get_exemplaire()->owner_id);
					$content .= $lender->lender_libelle;
					break;
				case 'source':
					$docs_location = new docs_location($object->get_transfert_demande()->get_num_location_source());
					$content .= $docs_location->libelle;
					break;
				case 'destination':
					$docs_location = new docs_location($object->get_transfert_demande()->get_num_location_dest());
					$content .= $docs_location->libelle;
					break;
				case 'formatted_date_reception':
					$content .= $object->get_transfert_demande()->get_formatted_date_reception();
					break;
				case 'formatted_date_envoyee':
					$content .= $object->get_transfert_demande()->get_formatted_date_envoyee();
					break;
				case 'formatted_date_refus':
					$content .= $object->get_transfert_demande()->get_formatted_date_visualisee();
					break;
				case 'motif_refus':
					$content .= $object->get_transfert_demande()->get_motif_refus();
					break;
				case 'transfert_ask_user_num':
				case 'transfert_send_user_num':
					$content .= user::get_param(call_user_func_array(array($object, "get_".$property), array()), 'username');
					break;
				case 'transfert_bt_relancer':
					$content .= "<input type='button' class='bouton' value='".$msg["transferts_circ_btRelancer"]."' onclick='document.location=\"./circ.php?categ=trans&sub=refus&action=aff_redem&transid=".$object->get_id()."\"'>";
					break;
				case 'formatted_bt_date_retour':
					$content .= "<input type='button' class='bouton' name='bt_date_retour_".$object->get_id()."' value='".$object->get_formatted_date_retour()."' onClick=\"var reg=new RegExp('(-)', 'g'); openPopUp('".$base_path."/select.php?what=calendrier&caller=".$this->get_form_name()."&date_caller='+".$this->get_form_name().".date_retour_".$object->get_id().".value.replace(reg,'')+'&param1=date_retour_".$object->get_id()."&param2=bt_date_retour_".$object->get_id()."&auto_submit=NO&date_anterieure=YES&after=chgDate%28id_value,".$object->get_id()."%29', 'calendar')\" />
						<input type='hidden' name='date_retour_".$object->get_id()."' value='".$object->get_date_retour()."' />";
					break;
				default :
					$content .= parent::get_cell_content($object, $property);
					break;
			}	
		}
		return $content;
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!id!!', $object->get_id(), $value);
		$display = "<td>".$value."</td>";
		return $display;
	}
	
	protected function get_display_cell($object, $property) {
		$display = "<td>".$this->get_cell_content($object, $property)."</td>";
		return $display;
	}
	
	protected function get_edition_link() {
		global $msg;
		global $sub;
		$edition_link = '';
		//le lien pour l'édition si on a le droit ...
		if (SESSrights & EDIT_AUTH) {
			$sub_url = $sub;
			if($sub == 'departs') {
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
		global $list_transferts_ui_parcours_search_content_form_tpl;
	
		$content_form = $list_transferts_ui_parcours_search_content_form_tpl;
		
		$content_form = str_replace('!!nb_res!!', $this->pager['nb_per_page'], $content_form);
		$content_form = str_replace('!!filters!!', $this->get_search_filters(), $content_form);
		$content_form = str_replace('!!json_filters!!', json_encode($this->filters), $content_form);
		$content_form = str_replace('!!page!!', $this->pager['page'], $content_form);
		$content_form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $content_form);
		$content_form = str_replace('!!pager!!', json_encode($this->pager), $content_form);
		$content_form = str_replace('!!objects_type!!', $this->objects_type, $content_form);
		$content_form = str_replace('!!edition_link!!', $this->get_edition_link(), $content_form);
		return $content_form;
	}
	
	protected function add_column_sel_button() {
		$this->columns[] = array(
				'property' => '',
				'label' => "<div class='center'><input type='button' class='bouton' name='+' onclick='SelAll(document.".$this->get_form_name().");' value='+'></div>",
				'html' => "<div class='center'><input type='checkbox' name='sel_!!id!!' value='1'></div>"
		);
	}
	
	public function get_display_list() {
		global $msg, $charset;
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
				//Affichage de la liste des objets
				$display .= "<table id='".$this->objects_type."_list'>";
				$display .= $this->get_display_header_list();
				$display .= $this->get_display_content_list();
				$display .= "</table><br />";
				$display .= $this->get_display_selection_actions();
				$display .= $this->pager();
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
		global $msg, $charset;
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
		if(static::class == 'list_transferts_refus_ui') {
			$motif .= "<hr />".$msg["transferts_circ_validation_refus_motif"]."<br />
					<textarea name='motif_refus' cols=60></textarea>"; 
		}
		$display = str_replace('!!motif!!', $motif, $display);
		$display = str_replace('!!valid_action!!', static::get_controller_url_base(), $display);
		$display = str_replace('!!ids!!', $this->filters['ids'], $display);
		$display = str_replace('!!objects_type!!', $this->objects_type, $display);
		return $display;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
	
		$humans = array();
		if($this->filters['site_origine']) {
			$docs_location = new docs_location($this->filters['site_origine']);
			$humans[] = $this->_get_label_query_human($msg['transferts_edition_filtre_origine'], $docs_location->libelle);
		}
		if($this->filters['site_destination']) {
			$docs_location = new docs_location($this->filters['site_destination']);
			$humans[] = $this->_get_label_query_human($msg['transferts_edition_filtre_destination'], $docs_location->libelle);
		}
		if($this->filters['f_etat_date']) {
			$option_label = '';
			switch ($this->filters['f_etat_date']) {
				case '1':
					$option_label = $msg["transferts_circ_retour_filtre_etat_proche"];
					break;
				case '2':
					$option_label = $msg["transferts_circ_retour_filtre_etat_depasse"];
					break;
			}
			$humans[] = $this->_get_label_query_human($msg['transferts_circ_retour_filtre_etat'], $option_label);
		}
		if($this->filters['f_etat_dispo']) {
			$option_label = '';
			switch ($this->filters['f_etat_dispo']) {
				case '1':
					$option_label = $msg["transferts_circ_retour_filtre_dispo"];
					break;
				case '2':
					$option_label = $msg["transferts_circ_retour_filtre_circ"];
					break;
			}
			$humans[] = $this->_get_label_query_human($msg['transferts_circ_retour_filtre_dispo_title'], $option_label);
		}
		return $this->get_display_query_human($humans);
	}
	
	public static function get_controller_url_base() {
		global $base_path, $sub;
	
		return $base_path.'/circ.php?categ=trans&sub='. $sub;
	}
}