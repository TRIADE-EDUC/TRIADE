<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_ui.class.php,v 1.8 2019-03-04 10:13:22 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");
require_once($class_path."/analyse_query.class.php");
require_once($include_path."/templates/list/accounting/list_accounting_ui.tpl.php");

class list_accounting_ui extends list_ui {

	protected $type_acte;

	protected $analyse_query;

	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}

	public function get_form_title() {
		global $msg, $charset;

		return htmlentities($msg['recherche'].' : '.$msg['acquisition_ach_'.$this->get_initial_name()], ENT_QUOTES, $charset);
	}

	protected function _get_query_base() {
		if(!$this->filters['user_input']) {
			$query = "
				SELECT actes.id_acte as id, actes.*, date_ech_calc, raison_sociale, actes2.numero as num_acte_parent
				FROM (actes ";
		} else {
			$members_actes = $this->get_analyse_query()->get_query_members("actes","actes.numero","actes.index_acte", "actes.id_acte");
			$members_lignes = $this->get_analyse_query()->get_query_members("lignes_actes","lignes_actes.code","lignes_actes.index_ligne", "lignes_actes.id_ligne");
			$query = "
				select distinct(actes.id_acte), actes.id_acte as id, actes.*, date_ech_calc, actes2.numero as num_acte_parent, raison_sociale, max(".$members_actes["select"]."+".$members_lignes["select"].") as pert
				from (actes left join lignes_actes on num_acte=id_acte ";
		}
		$query .= "
			LEFT JOIN (SELECT MIN((DATE_FORMAT(date_ech, '%Y%m%d'))) AS date_ech_calc, num_acte FROM lignes_actes WHERE (('2' & statut) = '0') GROUP BY num_acte) dl ON dl.num_acte=actes.id_acte)
			LEFT JOIN entites ON entites.id_entite=actes.num_fournisseur
			LEFT JOIN liens_actes ON num_acte_lie=actes.id_acte
			LEFT JOIN actes actes2 ON actes2.id_acte=liens_actes.num_acte ";
// 		$query .= "group by actes.id_acte ";
// 		if(trim($order)){
// 			$q.=$order;
// 		} else{
// 			$q.= "order by pert desc";
// 		}
		return $query;
	}

	protected function add_object($row) {
		$this->objects[] = $row;
	}

	protected function get_filter_entite() {
		if(!$this->filters['entite']) {
			$query = entites::list_biblio(SESSuserid);
			$result = pmb_mysql_query($query);
			$this->filters['entite'] = pmb_mysql_result($result, 0, 'id_entite');
		}
		return $this->filters['entite'];
	}

	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $id_bibli;
		//Paramètres utilisateur
		global $deflt3bibli;
		$status = "deflt3".$this->get_initial_name()."_statut";
		global ${$status};
		$this->filters = array(
				'type_acte' => $this->get_type_acte(),
				'user_input' => '',
				'entite' => ($id_bibli ? $id_bibli : ($deflt3bibli ? $deflt3bibli : $this->get_filter_entite())),
				'exercice' => '',
				'status' => ${$status}
		);
		parent::init_filters($filters);
	}

	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		global $nb_per_page_acq;
		$this->pager = array(
				'page' => 1,
				'nb_per_page' => ($nb_per_page_acq ? $nb_per_page_acq : 10),
				'nb_results' => 0,
				'nb_page' => 1
		);
	}

	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		if($this->filters['user_input']) {
			$this->applied_sort = array(
					'by' => 'pert',
					'asc_desc' => 'asc'
			);
		} else {
			$this->applied_sort = array(
					'by' => 'id',
					'asc_desc' => 'desc'
			);
		}
	}

	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {

		if($this->applied_sort['by']) {
			$order = '';
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				default :
					$order .= $sort_by;
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
		$user_input = $this->objects_type.'_user_input';
		global ${$user_input};
		if(isset(${$user_input})) {
			$this->filters['user_input'] = ${$user_input};
		}
		$entite = $this->objects_type.'_entite';
		global ${$entite};
		if(isset(${$entite})) {
			$this->filters['entite'] = ${$entite};
		}
		$exercice = $this->objects_type.'_exercice';
		global ${$exercice};
		if(isset(${$exercice})) {
			$this->filters['exercice'] = ${$exercice};
		}
		$status = $this->objects_type.'_status';
		global ${$status};
		if(isset(${$status})) {
			$this->filters['status'] = ${$status};
		}
		parent::set_filters_from_form();
	}

	protected function get_entites_selector() {
		global $msg, $charset;

		$selector = "<select name='".$this->objects_type."_entite' class='saisie-50em' onchange=\"submit();\">";
		//Recherche des etablissements auxquels a acces l'utilisateur
		$query = entites::list_biblio(SESSuserid);
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_object($result)) {
			$selector .= "<option value='".$row->id_entite."' ".($row->id_entite == $this->filters['entite'] ? "selected='selected'" : "").">";
			$selector .= $row->raison_sociale."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}

	protected function get_exercices_selector() {
		$selector = exercices::getHtmlSelect($this->filters['entite'], $this->filters['exercice'], true, array('id'=>$this->objects_type.'_exercice','name'=>$this->objects_type.'_exercice','onchange'=>'submit();'));
		return $selector;
	}

	protected function get_status_selector() {
		global $charset;

		$selector = "<select class='saisie-25em' id='".$this->objects_type."_status' name='".$this->objects_type."_status' onchange=\"submit();\">";
		$list_statut = actes::getStatelist($this->get_type_acte());
		foreach($list_statut as $key => $value){
			$selector .="<option value='".$key."' ".($this->filters['status'] == $key ? "selected='selected'" : "").">".htmlentities($value, ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}

	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $msg;
		global $pmb_lecteurs_localises;
		global $list_accounting_ui_search_filters_form_tpl;

		$search_filters_form = $list_accounting_ui_search_filters_form_tpl;
		$search_filters_form = str_replace('!!user_input!!', $this->filters['user_input'], $search_filters_form);
		$search_filters_form = str_replace('!!exercices_selector!!', $this->get_exercices_selector(), $search_filters_form);
		$search_filters_form = str_replace('!!status_selector!!', $this->get_status_selector(), $search_filters_form);
		$search_filters_form = str_replace('!!entites_selector!!', $this->get_entites_selector(), $search_filters_form);
		$search_filters_form = str_replace('!!objects_type!!', $this->objects_type, $search_filters_form);
		return $search_filters_form;
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
		$filters[] = "actes.type_acte = '".$this->filters['type_acte']."'";
		if($this->filters['user_input']) {
			$isbn = '';
			$t_codes = array();
			if (isEAN($this->filters['user_input'])) {
				// la saisie est un EAN -> on tente de le formater en ISBN
				$isbn = EANtoISBN($this->filters['user_input']);
				// si échec, on prend l'EAN comme il vient
				if($isbn) {
					$t_codes[] = $isbn;
					$t_codes[] = formatISBN($isbn,10);
				}
			} elseif (isISBN($this->filters['user_input'])) {
				// si la saisie est un ISBN
				$isbn = formatISBN($this->filters['user_input']);
				if($isbn) {
					$t_codes[] = $isbn ;
					$t_codes[] = formatISBN($isbn,13);
				}
			} elseif (isISSN($this->filters['user_input'])) {
				$t_codes[] = $this->filters['user_input'] ;
			}
			if (count($t_codes)) {
				$codes_query = array();
				foreach ($t_codes as $v) {
					$codes_query [] = "lignes_actes.code like '%".$v."%' ";
				}
				$filters[] = "(".implode(' or ', $codes_query).")";
			} else {
				$members_actes = $this->get_analyse_query()->get_query_members("actes","actes.numero","actes.index_acte", "actes.id_acte");
				$members_lignes = $this->get_analyse_query()->get_query_members("lignes_actes","lignes_actes.code","lignes_actes.index_ligne", "lignes_actes.id_ligne");
				$filters[] = "(".$members_actes["where"]." or ".$members_lignes["where"].")";
			}
		}
		if($this->filters['entite']) {
			$filters[] = "actes.num_entite = '".$this->filters['entite']."'";
		}
		if($this->filters['exercice']) {
			$filters[] = "actes.num_exercice = '".$this->filters['exercice']."'";
		}
		if($this->filters['status']) {
			if ($this->filters['status'] != '-1') {
				if ($this->filters['status'] == 32) {
					$filters[] = "((actes.statut & 32) = 32) ";
				} else {
					$filters[] = "((actes.statut & 32) = 0) and ((actes.statut & ".$this->filters['status'].") = '".$this->filters['status']."') ";
				}
			}
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}

	protected function get_link_action($action, $act) {
		global $msg;

		return array(
				'href' => static::get_controller_url_base()."&action=".$action."&id_bibli=".$this->filters['entite'],
				'confirm' => $msg['acquisition_'.$this->get_initial_name().'list_'.$act]
		);
	}

	protected function add_column_print($pdfdoc) {
		global $base_path;
		global $msg, $charset;

		$this->columns[] = array(
				'property' => '',
				'label' => "",
				'html' => "
					<a href=# onclick=\"openPopUp('".$base_path."/pdf.php?pdfdoc=".$pdfdoc."&id_".$this->get_initial_name()."=!!id!!' ,'print_PDF');\" >
						<img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt='".htmlentities($msg['imprimer'],ENT_QUOTES, $charset)."' title='".htmlentities($msg['imprimer'],ENT_QUOTES, $charset)."' />
					</a>
				"
		);
	}

	protected function get_display_content_cell_print_mail($object) {
		global $base_path;
		global $sub;
		global $msg, $charset;
		global $acquisition_pdfcde_by_mail;

		$bib_coord = pmb_mysql_fetch_object(entites::get_coordonnees($this->filters['entite'],1));

		$display = "
		<a href=# onclick=\"document.location='".$base_path."/acquisition.php?categ=ach&sub=".$sub."&action=print&id_bibli=".$this->filters['entite']."&id_".$this->get_initial_name()."=".$object->id_acte."&page=".$this->pager['page']."&by_mail=0'\" >
			<img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt='".htmlentities($msg['imprimer'],ENT_QUOTES, $charset)."' title='".htmlentities($msg['imprimer'],ENT_QUOTES, $charset)."' />
		</a>";

		$parameter_name = 'acquisition_pdf'.$this->get_initial_name().'_by_mail';
		global ${$parameter_name};
		if (((($object->statut & ~STA_ACT_ARC) == STA_ACT_ENC) && ${$parameter_name} && strpos($bib_coord->email,'@'))) {
			$display .= "
			<a href=# onclick=\"document.location='".$base_path."/acquisition.php?categ=ach&sub=".$sub."&action=print&id_bibli=".$this->filters['entite']."&id_".$this->get_initial_name()."=".$object->id_acte."&page=".$this->pager['page']."&by_mail=1'\" >
				<img src='".get_url_icon('mail.png')."' style='border:0px' class='center' alt='".htmlentities($msg['58'],ENT_QUOTES, $charset)."' title='".htmlentities($msg['58'],ENT_QUOTES, $charset)."' />
			</a>";
		}
		return $display;
	}

	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'ach', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}

	protected function get_cell_content($object, $property) {
		global $msg, $charset;

		$content = '';
		switch($property) {
			case 'num_fournisseur':
				$entites = new entites($object->num_fournisseur);
				$content .= $entites->raison_sociale;
				break;
			case 'date_acte':
				$content .= formatdate($object->date_acte);
				break;
			case 'statut':
				$st = (($object->statut) & ~(STA_ACT_ARC));
				switch ($st) {
					case STA_ACT_ENC :
						$statut = htmlentities($msg['acquisition_'.$this->get_initial_name().'_enc'], ENT_QUOTES, $charset);
						break;
					case STA_ACT_REC :
						$statut = htmlentities($msg['acquisition_'.$this->get_initial_name().'_rec'], ENT_QUOTES, $charset);
						break;
					case STA_ACT_PAY :
						$statut = htmlentities($msg['acquisition_'.$this->get_initial_name().'_pay'], ENT_QUOTES, $charset);
						break;
					default :
						if(isset($msg['acquisition_'.$this->get_initial_name().'_enc'])) {
							$statut = htmlentities($msg['acquisition_'.$this->get_initial_name().'_enc'], ENT_QUOTES, $charset);
						} else {
							$statut = '';
						}
				}
				if(($object->statut & STA_ACT_ARC) == STA_ACT_ARC) {
					$content .= '<s>'.$statut.'</s>';
				} else {
					$content .= $statut;
				}
				break;
			case 'print_mail':
				$content .= $this->get_display_content_cell_print_mail($object);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function _get_cell_header($name, $label = '') {
		global $msg, $charset;

		if($name == 'print_mail') {
			return "<th>".$this->_get_label_cell_header($label)."</th>";
		} else {
			return parent::_get_cell_header($name, $label);
		}
	}

	protected function get_display_cell($object, $property) {
		if($property == 'print_mail') {
			$display = "<td>".$this->get_cell_content($object, $property)."</td>";
		} else {
			$display = "<td onclick=\"window.location='".static::get_controller_url_base()."&action=modif&id_bibli=".$object->num_entite."&id_exercice=".$object->num_exercice."&id_".$this->get_initial_name()."=".$object->id_acte."'\" style='cursor:pointer;'><i>".$this->get_cell_content($object, $property)."</i></td>";
		}
		return $display;
	}

	protected function _get_query_human() {
		global $msg, $charset;

		$humans = array();
		if($this->filters['entite']) {
			$entites = new entites($this->filters['entite']);
			$humans[] = $this->_get_label_query_human($msg['acquisition_coord_lib'], $entites->raison_sociale);
		}
		if($this->filters['exercice']) {
			$exercices = new exercices($this->filters['exercice']);
			$humans[] = $this->_get_label_query_human($msg['acquisition_budg_exer'], $exercices->libelle);
		}
		if($this->filters['status']) {
			$list_statut = actes::getStatelist($this->get_type_acte());
			$humans[] = $this->_get_label_query_human($msg['acquisition_statut'], $list_statut[$this->filters['status']]);
		}
		return $this->get_display_query_human($humans);
	}

	public function get_analyse_query() {
		global $msg;

		if(!isset($this->analyse_query)) {
			$this->analyse_query = new analyse_query(stripslashes($this->filters['user_input']),0,0,0,0);
			if ($this->analyse_query->error) {
				error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$this->analyse_query->current_car,$this->analyse_query->input_html,$this->analyse_query->error_message));
				exit;
			}
		}
		return $this->analyse_query;
	}

	public function get_export_icons() {
		return '';
	}

	public static function get_controller_url_base() {
		global $base_path;
		global $categ, $sub;

		return $base_path.'/acquisition.php?categ='.$categ.'&sub='.$sub;
	}

	public static function run_action_list($action='') {
		$selected_objects = static::get_selected_objects();
		if(count($selected_objects)) {
			foreach ($selected_objects as $id) {
				$actes = new actes($id);
				switch ($action) {
					case 'valid':
						static::run_valid_object($actes);
						break;
					case 'arc':
						static::run_arc_object($actes);
						break;
					case 'sold':
						static::run_sold_object($actes);
						break;
					case 'rec':
						static::run_rec_object($actes);
						break;
					case 'pay':
						static::run_pay_object($actes);
						break;
					case 'delete':
						static::run_delete_object($actes);
						break;
				}
			}
		}
	}
}