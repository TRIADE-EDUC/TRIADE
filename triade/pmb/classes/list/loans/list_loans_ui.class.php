<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_loans_ui.class.php,v 1.2 2019-02-08 09:33:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($class_path."/emprunteur.class.php");
require_once($class_path."/pret.class.php");
require_once($class_path."/expl.class.php");

class list_loans_ui extends list_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		/* Conservation des anciens éléments du select
		 	date_format(pret_date, '".$msg['format_date']."') as aff_pret_date, ";
			$sql .= "date_format(pret_retour, '".$msg['format_date']."') as aff_pret_retour, ";
			$sql .= "IF(pret_retour>=CURDATE(),0,1) as retard, ";
			$sql .= "id_empr, empr_nom, empr_prenom, empr_mail, empr_cb, expl_cote, expl_cb, expl_notice, expl_bulletin, notices_m.notice_id as idnot, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, tdoc_libelle, ";
			$sql .= "short_loan_flag
		 */
		$query = 'select pret_idempr, pret_idexpl 
			FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id )
				LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id)
				LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				JOIN pret ON pret_idexpl = expl_id
				JOIN empr ON empr.id_empr = pret.pret_idempr
				JOIN docs_type ON expl_typdoc = idtyp_doc 	
				';
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = new pret($row->pret_idempr, $row->pret_idexpl);
	}
		
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $pmb_lecteurs_localises;
		
		$this->available_filters =
		array('main_fields' =>
				array(
						'doc_location' => 'editions_filter_docs_location',
						'empr_categorie' => 'editions_filter_empr_categ',
						'empr_codestat_one' => 'editions_filter_empr_codestat',
				)
		);
		if($pmb_lecteurs_localises) {
			$this->available_filters['main_fields']['empr_location'] = 'editions_filter_empr_location';
		}
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $pmb_lecteurs_localises;
		global $deflt2docs_location;
		
		$this->filters = array(
				'empr_location_id' => ($pmb_lecteurs_localises ? $deflt2docs_location : 0),
				'docs_location_id' => '',
				'empr_categ_filter' => '',
				'empr_codestat_filter' => '',
				'pret_date_start' => '',
				'pret_date_end' => '',
				'pret_retour_start' => '',
				'pret_retour_end' => '',
				'short_loan_flag' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		global $pmb_lecteurs_localises;
		if($pmb_lecteurs_localises) {
			$this->add_selected_filter('empr_location');
		}
		$this->add_selected_filter('doc_location');
		$this->add_empty_selected_filter();
		$this->add_selected_filter('empr_categorie');
		$this->add_selected_filter('empr_codestat_one');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'cb_expl' => '4014',
						'cote' => '4016',
						'typdoc' => '294',
						'record' => '233',
						'author' => '234',
						'empr' => 'empr_nom_prenom',
						'pret_date' => 'circ_date_emprunt',
						'pret_retour' => 'circ_date_retour',
						'late_letter' => '369'
				)
		);
		
		$this->available_columns['custom_fields'] = array();
// 		$this->add_custom_fields_available_columns('notices');
		$this->add_custom_fields_available_columns('expl', 'id_expl');
		$this->add_custom_fields_available_columns('empr', 'id_empr');
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
	
		if($this->applied_sort['by']) {
			$order = '';
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				case 'record' :
					break;
				case 'author' :
					break;
				case 'empr' :
					$order .= 'empr_nom, empr_prenom';
					break;
				case 'pret_retour_empr' :
					$order .= 'pret_retour, empr_nom, empr_prenom';
					break;
				case 'cote':
					$order .= 'expl_cote';
					break;
				case 'typdoc':
					$order .= 'expl_typdoc';
					break;
				case 'pret_date':
					$order .= 'pret_date';
					break;
				case 'pret_retour':
					$order .= 'pret_retour';
					break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				if($this->applied_sort['asc_desc'] == 'desc' && strpos($order, ',')) {
					$cols = explode(',', $order);
					$query_order = " order by ";
					foreach ($cols as $i=>$col) {
						if($i) {
							$query_order .= ","; 
						}
						$query_order .= " ".$col." ".$this->applied_sort['asc_desc'];
					}
					return $query_order;
				} else {
					return " order by ".$order." ".$this->applied_sort['asc_desc'];
				}
			} else {
				return "";
			}
		}
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $empr_location_id;
		global $docs_location_id;
		global $empr_categ_filter;
		global $empr_codestat_filter;
		
		if(isset($empr_location_id)) {
			$this->filters['empr_location_id'] = $empr_location_id*1;
		}
		if(isset($docs_location_id)) {
			$this->filters['docs_location_id'] = $docs_location_id*1;
		}
		if(isset($empr_categ_filter)) {
			$this->filters['empr_categ_filter'] = $empr_categ_filter*1;
		}
		if(isset($empr_codestat_filter)) {
			$this->filters['empr_codestat_filter'] = $empr_codestat_filter*1;
		}
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_empr_location() {
		return docs_location::gen_combo_box_empr($this->filters['empr_location_id']);
	}
	
	protected function get_search_filter_doc_location() {
		return docs_location::gen_combo_box_docs($this->filters['docs_location_id']);
	}
	
	protected function get_search_filter_empr_categorie() {
		return emprunteur::gen_combo_box_categ($this->filters['empr_categ_filter']);
	}
	
	protected function get_search_filter_empr_codestat_one() {
		return emprunteur::gen_combo_box_codestat($this->filters['empr_codestat_filter']);
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base(), $search_form);
		return $search_form;
	}
		
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['empr_location_id']) {
			$filters [] = 'empr_location = "'.$this->filters['empr_location_id'].'"';
		}
		if($this->filters['docs_location_id']) {
			$filters [] = 'expl_location = "'.$this->filters['docs_location_id'].'"';
		}
		if($this->filters['empr_categ_filter']) {
			$filters [] = 'empr_categ = "'.$this->filters['empr_categ_filter'].'"';
		}
		if($this->filters['empr_codestat_filter']) {
			$filters [] = 'empr_codestat = "'.$this->filters['empr_codestat_filter'].'"';
		}
		if($this->filters['pret_date_start']) {
			$filters [] = 'pret_date >= "'.$this->filters['pret_date_start'].'"';
		}
		if($this->filters['pret_date_end']) {
			$filters [] = 'pret_date < "'.$this->filters['pret_date_end'].'"';
		}
		if($this->filters['pret_retour_start']) {
			$filters [] = 'pret_retour >= "'.$this->filters['pret_retour_start'].'"';
		}
		if($this->filters['pret_retour_end']) {
			$filters [] = 'pret_retour < "'.$this->filters['pret_retour_end'].'"';
		}
		if($this->filters['short_loan_flag']) {
			$filters [] = 'short_loan_flag = "'.$this->filters['short_loan_flag'].'"';
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
				case 'cote':
				case 'typdoc':
					return strcmp($a->get_exemplaire()->{$sort_by}, $b->get_exemplaire()->{$sort_by});
					break;
				case 'record' :
					return strcmp($a->get_exemplaire()->get_notice_title(), $b->get_exemplaire()->get_notice_title());
					break;
				case 'author':
					return strcmp(gen_authors_header(get_notice_authors($a->get_exemplaire()->id_notice)), gen_authors_header(get_notice_authors($b->get_exemplaire()->id_notice)));
					break;
				case 'empr':
					return strcmp(emprunteur::get_name($a->id_empr), emprunteur::get_name($b->id_empr));
					break;
				case 'pret_date':
					return strcmp($a->pret_date, $b->pret_date);
					break;
				case 'pret_retour':
					return strcmp($a->pret_retour, $b->pret_retour);
					break;
				case 'late_letter':
					return '';
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
		$display = str_replace('!!categ!!', 'expl', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $base_path;
		global $empr_show_caddie;
		global $pmb_short_loan_management;
		global $biblio_email;
		
		$content = '';
		switch($property) {
			case 'cb_expl':
				$content .= "<b>".exemplaire::get_cb_link($object->{$property})."</b>";
				break;
			case 'cote':
				$content .= $object->get_exemplaire()->cote;
				break;
			case 'typdoc':
				$content .= $object->get_exemplaire()->typdoc;
				break;
			case 'record':
				$content .= "<b>";
				if (SESSrights & CATALOGAGE_AUTH) {
					if ($object->get_exemplaire()->id_notice) {
						$query = "select tit1 as title from notices where notice_id = ".$object->get_exemplaire()->id_notice;
						$result = pmb_mysql_query($query);
						$content .= "<a href='./catalog.php?categ=isbd&id=".$object->get_exemplaire()->id_notice."' ".($object->retard ? "style='color:RED'" : "").">".pmb_mysql_result($result, 0, 'title')."</a>"; // notice de monographie
					} elseif ($object->get_exemplaire()->id_bulletin) {
						$query = "select notices_s.tit1 as title, bulletin_numero, mention_date from bulletins
								LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id
								where bulletin_id = ".$object->get_exemplaire()->id_bulletin;
						$result = pmb_mysql_query($query);
						$row = pmb_mysql_fetch_object($result);
						$record_title = $row->title;
						if($row->bulletin_numero) {
							$record_title .= ' '.$row->bulletin_numero;
						}
						if($row->mention_date) {
							$record_title .= ' ('.$row->mention_date.')';
						}
						$content .= "<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".$object->get_exemplaire()->id_bulletin."' ".($object->retard ? "style='color:RED'" : "").">".$record_title."</a>"; // notice de bulletin
					} else {
						$content .= $object->get_exemplaire()->get_notice_title();
					}
				} else {
					$content .= $object->get_exemplaire()->get_notice_title();
				}
				$content .= "</b>";
				break;
			case 'author':
				$content .= "<span ".($object->retard ? "style='color:RED'" : "").">".gen_authors_header(get_notice_authors($object->get_exemplaire()->id_notice))."</span>";
				break;
			case 'empr':
				if ($empr_show_caddie) {
					$content .= "<img src='".get_url_icon('basket_empr.gif')."' class='align_middle' alt='basket' title=\"".$msg[400]."\" onClick=\"openPopUp('./cart.php?object_type=EMPR&item=".$object->id_empr."', 'cart')\">&nbsp;";
				}
				$content .= "<a href='".$base_path."/circ.php?categ=pret&form_cb=".rawurlencode(emprunteur::get_cb_empr($object->id_empr))."'>".emprunteur::get_name($object->id_empr)."</a>";
				break;
			case 'pret_date':
				$content .= $object->date_pret_display;
				if($pmb_short_loan_management && $this->filters['short_loan_flag']) {
					$content .= "&nbsp;<img src='".get_url_icon('chrono.png')."' alt='".$msg['short_loan']."' title='".$msg['short_loan']."'/>";
				}
				break;
			case 'pret_retour':
				$content .= "<span ".($object->retard ? "style='color:RED'" : "")."><b>".$object->date_retour_display."</b></span>";
				break;
			case 'late_letter':
				if ($object->retard) {
					$imprime_click = "onclick=\"openPopUp('./pdf.php?pdfdoc=lettre_retard&cb_doc=".$object->id_expl."&id_empr=".$object->id_empr."', 'lettre'); return(false) \"";
					$mail_click = "onclick=\"if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_retard&cb_doc=".$object->id_expl."&id_empr=".$object->id_empr."', 'mail');} return(false) \"";
					$content .= "<a href=\"#\" ".$imprime_click."><img src='".get_url_icon('new.gif')."' title=\"".$msg["lettre_retard"]."\" alt=\"".$msg['lettre_retard']."\" border=\"0\"></a>";
					if ((emprunteur::get_mail_empr($object->id_empr))&&($biblio_email)) {
						$content .= "<a href=\"#\" ".$mail_click."><img src='".get_url_icon('mail.png')."' title=\"".$msg['mail_retard']."\" alt=\"".$msg['mail_retard']."\" border=\"0\"></a>";
					}
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!id!!', $object->id_empr.'_'.$object->id_expl, $value);
		$display = "<td class='center'>".$value."</td>";
		return $display;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		
		$humans = array();
		if($this->filters['empr_location_id']) {
			$docs_location = new docs_location($this->filters['empr_location_id']);
			$humans[] = $this->_get_label_query_human($msg['editions_filter_empr_location'], $docs_location->libelle);
		}
		if($this->filters['docs_location_id']) {
			$docs_location = new docs_location($this->filters['docs_location_id']);
			$humans[] = $this->_get_label_query_human($msg['editions_filter_docs_location'], $docs_location->libelle);
		}
		if($this->filters['empr_categ_filter']) {
			$query = "select libelle from empr_categ where id_categ_empr = ".$this->filters['empr_categ_filter'];
			$humans[] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_categ'], $query);
		}
		if($this->filters['empr_codestat_filter']) {
			$query = "select libelle from empr_codestat where idcode = ".$this->filters['empr_codestat_filter'];
			$humans[] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_codestat'], $query);
		}
		return $this->get_display_query_human($humans);;
	}
	
	protected function get_infos() {
		$infos['in_progress'] = 0;
		$infos['late'] = 0;
		$query = "select IF(pret_retour>=CURDATE(),'0','1') as retard, count(pret_idexpl) as combien
			FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id )
			LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id)
			LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
			JOIN pret ON pret_idexpl = expl_id
			JOIN empr ON empr.id_empr = pret.pret_idempr
			JOIN docs_type ON expl_typdoc = idtyp_doc 	
		";
		$query .= $this->_get_query_filters();
		$query.= " group by retard ";
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)) {
			if($row->retard) {
				$infos['late'] += $row->combien;
			}
			$infos['in_progress'] += $row->combien;
		}
		return $infos;
	}
	
	public function get_display_late() {
		global $msg;
		// construction du message ## prêts en retard sur un total de ##
		$display = $msg['n_retards_sur_total_de'];
		$infos = $this->get_infos();
		$display = str_replace ("!!nb_retards!!", $infos['late'], $display);
		$display = str_replace ("!!nb_total!!", $infos['in_progress'], $display);
		return $display;
	}	
}