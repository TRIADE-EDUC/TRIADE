<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_ui.class.php,v 1.48.2.1 2024/05/21 13:55:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");
require_once($class_path."/analyse_query.class.php");
require_once($class_path."/actes.class.php");
require_once($class_path."/rubriques.class.php");
require_once($class_path."/budgets.class.php");
require_once($include_path."/templates/list/accounting/list_accounting_ui.tpl.php");

class list_accounting_ui extends list_ui {

	protected $type_acte;

	protected $analyse_query;

	protected function get_form_title() {
		global $msg, $charset;

		return htmlentities($msg['recherche'].' : '.$msg['acquisition_ach_'.$this->get_initial_name()], ENT_QUOTES, $charset);
	}

	public function get_dataset_title() {
		global $msg, $charset;

		if(isset($msg['acquisition_ach_'.$this->get_initial_name()])) {
			return htmlentities($msg['acquisition_ach_'.$this->get_initial_name()], ENT_QUOTES, $charset);
		}
	}

	protected function _get_query_base() {
	    $this->set_filter_from_form('user_input');
		if(!$this->filters['user_input']) {
			$query = "
				SELECT actes.id_acte as id, actes.*, date_ech_calc, raison_sociale, actes2.numero as num_acte_parent
				FROM (actes ";
		} else {
			$members_actes = $this->get_analyse_query()->get_query_members("actes","actes.numero","actes.index_acte", "actes.id_acte");
			$members_lignes = $this->get_analyse_query()->get_query_members("lignes_actes","lignes_actes.code","lignes_actes.index_ligne", "lignes_actes.id_ligne");
			$query = "
				select actes.id_acte as id, actes.*, date_ech_calc, actes2.numero as num_acte_parent, raison_sociale, max(".$members_actes["select"]."+".$members_lignes["select"].") as pert
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

	protected function fetch_data() {
	    $this->objects = array();
	    $query = $this->_get_query();
	    $result = pmb_mysql_query($query);
	    //Particularité ici : On s'assure qu'il n'existe pas qu'une ligne avec des valeurs NULLES
	    while($row = pmb_mysql_fetch_object($result)) {
    	    if($row->id) {
    	        $this->add_object($row);
    	    }
        }
        if(!count($this->objects)) {
            $this->pager['nb_results'] = 0;
        } elseif($this->applied_sort_type != "SQL"){
            $this->pager['nb_results'] = pmb_mysql_num_rows($result);
        }
	    $this->messages = "";
	}

	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'global_search' => 'global_search',
						'status' => 'acquisition_statut',
						'entity' => 'acquisition_coord_lib',
						'rubrique' => 'acquisition_rub',
						'applicants' => 'acquisition_applicants',
						'lgstats' => 'acquisition_lgstat',
						'suppliers' => 'acquisition_ach_fou'
				)
		);
		if(static::class != 'list_accounting_devis_ui') {
			$this->available_filters['main_fields']['exercice'] = 'acquisition_budg_exer';
		}
		$this->available_filters['custom_fields'] = array();
	}

	protected function init_default_selected_filters() {
		$this->add_selected_filter('global_search');
		if(static::class != 'list_accounting_devis_ui') {
			$this->add_selected_filter('exercice');
		} else {
			$this->add_empty_selected_filter();
		}
		$this->add_empty_selected_filter();
		$this->add_selected_filter('status');
		$this->add_selected_filter('entity');
		$this->add_empty_selected_filter();
		$this->add_selected_filter('rubrique');
		//$this->add_selected_filter('applicants');
		//$this->add_selected_filter('suppliers');
	}

	protected function get_filter_entite() {
		if(empty($this->filters['entite'])) {
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
		global $deflt3exercice;
		$status = "deflt3".$this->get_initial_name()."_statut";
		global ${$status};
		$this->filters = array(
				'type_acte' => $this->get_type_acte(),
				'user_input' => '',
				'entite' => ($id_bibli ? $id_bibli : ($deflt3bibli ? $deflt3bibli : $this->get_filter_entite())),
				'status' => ${$status},
				'rubrique' => '',
				'applicants' => array(),
				'lgstats' => array(),
				'suppliers' => array()
		);
		if(static::class != 'list_accounting_devis_ui') {
			$this->filters['exercice'] = ($deflt3exercice ? $deflt3exercice : '');
		} else {
			$this->filters['exercice'] = '';
		}
		parent::init_filters($filters);
	}

	protected function init_override_filters() {
		global $id_bibli;
		//Paramètres utilisateur
		global $deflt3bibli;
		global $deflt3exercice;
		$status = "deflt3".$this->get_initial_name()."_statut";
		global ${$status};

		$this->filters['entite'] = ($id_bibli ? $id_bibli : ($deflt3bibli ? $deflt3bibli : $this->get_filter_entite()));
		$this->filters['status'] = ${$status};
		if(static::class != 'list_accounting_devis_ui') {
			$this->filters['exercice'] = ($deflt3exercice ? $deflt3exercice : '');
		} else {
			$this->filters['exercice'] = '';
		}
	}

	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('default', 'text', array('italic' => true));
		$this->set_setting_column('print_mail', 'text', array('italic' => false));
		$this->set_setting_column('date_acte', 'datatype', 'date');
	}

	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		global $nb_per_page_acq;
		parent::init_default_pager();
		$this->pager['nb_per_page'] = ($nb_per_page_acq ? $nb_per_page_acq : 10);
	}

	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		if($this->filters['user_input']) {
		    $this->add_applied_sort('pert');
		} else {
		    $this->add_applied_sort('id', 'desc');
		}
	}

	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    return $sort_by;
	}

	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
	    return " group by actes.id_acte ".parent::_get_query_order();
	}

	/**
	 * Filtre provenant du formulaire
	 */
	public function set_filter_from_form($name, $type='string') {
	    //Particularité pour l'exercice : essayons de maintenir le filtre si le libellé est identique dans un autre établissement
	    if($name == 'exercice') {
	        $field_value = $this->objects_type.'_'.$name;
	        global ${$field_value};
	        if(isset(${$field_value})) {
	            $this->filters[$name] = intval(${$field_value});
	            if($this->filters['entite'] && $this->filters[$name]) {
	                $exercices_instance = new exercices($this->filters[$name]);
	                if($this->filters['entite'] != $exercices_instance->num_entite) {
	                    $exercices = exercices::getExercicesByEntite($this->filters['entite']);
	                    foreach ($exercices as $id_exercice=>$exercice) {
	                        if ($exercice['label'] == $exercices_instance->libelle) {
	                            $this->filters[$name] = $id_exercice;
	                            break;
	                        }
	                    }
	                }
	            }
	        }
	    } else {
	        parent::set_filter_from_form($name, $type);
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('user_input');
		$this->set_filter_from_form('entite', 'integer');
		$this->set_filter_from_form('exercice', 'integer');
		$this->set_filter_from_form('status', 'integer');
		$this->set_filter_from_form('rubrique', 'integer');
		$applicants = $this->objects_type.'_applicants';
		global ${$applicants};
		if(isset(${$applicants})) {
			$this->filters['applicants'] = ${$applicants};
		}
		$this->set_filter_from_form('lgstats', 'integer');
		$suppliers = $this->objects_type.'_suppliers';
		global ${$suppliers};
		if(isset(${$suppliers})) {
			$this->filters['suppliers'] = ${$suppliers};
		}
		parent::set_filters_from_form();
	}

	/**
	 * Retourne l'identifiant du noeud HTML du filtre du formulaire de recherche
	 */
	protected function get_uid_search_filter($property) {
	    switch ($property) {
	        case 'global_search':
	            return parent::get_uid_search_filter('user_input');
	        case 'entity':
	            return parent::get_uid_search_filter('entite');
	        default:
	            return parent::get_uid_search_filter($property);
	    }
	}

	protected function get_search_filter_global_search() {
		return $this->get_search_filter_simple_text('user_input');
	}

	protected function get_search_filter_entity() {
		global $charset;

		$selector = "<select id='".$this->objects_type."_search_filter_entite' name='".$this->objects_type."_entite' class='saisie-50em' onchange=\"submit();\">";
		//Recherche des etablissements auxquels a acces l'utilisateur
		$query = entites::list_biblio(SESSuserid);
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_object($result)) {
			$selector .= "<option value='".$row->id_entite."' ".($row->id_entite == $this->filters['entite'] ? "selected='selected'" : "").">";
			$selector .= htmlentities($row->raison_sociale, ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}

	protected function get_search_filter_exercice() {
		$selector = exercices::getHtmlSelect($this->filters['entite'], $this->filters['exercice'], true, array('id'=>$this->objects_type.'_search_filter_exercice','name'=>$this->objects_type.'_exercice','onchange'=>'submit();'), false);
		return $selector;
	}

	protected function get_search_filter_status() {
		$list_statut = actes::getStatelist($this->get_type_acte());
		return $this->get_search_filter_simple_selection('', 'status', '', $list_statut);
	}

	protected function get_search_filter_rubrique() {
		global $charset;

		$selector = "<select class='saisie-25em' id='".$this->objects_type."_search_filter_rubrique' name='".$this->objects_type."_rubrique' onchange=\"submit();\">";
		$selector .="<option value='0' ".(empty($this->filters['rubrique']) ? "selected='selected'" : "")."></option>";
		if(!empty($this->filters['exercice'])) {
			$result = budgets::listByExercice($this->filters['exercice']);
		} else {
			$result = entites::listBudgetsActifs($this->filters['entite']);
		}
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$query_rubriques = budgets::listRubriques($row->id_budget, 0);
				$result_rubriques = pmb_mysql_query($query_rubriques);
				if(pmb_mysql_num_rows($result_rubriques)) {
					$selector .="<optgroup label='".htmlentities($row->libelle, ENT_QUOTES, $charset)."'>";
					while($row_rubrique = pmb_mysql_fetch_object($result_rubriques)) {
						$selector .="<option value='".$row_rubrique->id_rubrique."' ".($this->filters['rubrique'] == $row_rubrique->id_rubrique ? "selected='selected'" : "").">".htmlentities($row_rubrique->libelle, ENT_QUOTES, $charset)."</option>";
					}
					$selector .="</optgroup>";
				}
			}
		}
		$selector .= "</select>";
		return $selector;
	}

	protected function get_search_filter_applicants() {
		$elements = array();
		if(!empty($this->filters['applicants'])) {
			foreach ($this->filters['applicants'] as $applicant) {
				if($applicant['id']) {
					$name = emprunteur::get_name($applicant['id']);
					$elements[] = array(
							'id' => $applicant['id'],
							'name' => $name
					);
				}
			}
		}
		templates::init_selection_attributes(array(
				array('name' => 'dyn', 'value' => '2'),
		));
		$selector .= templates::get_display_elements_completion_field($elements, $this->get_form_name(), $this->objects_type.'_applicants', $this->objects_type.'_applicants_id', 'emprunteur');
		return $selector;
	}

	protected function get_search_filter_lgstats() {
		$selector=lgstat::getHtmlSelect($this->filters['lgstats'], FALSE, array('id'=> $this->objects_type.'_lgstats', 'name'=> $this->objects_type.'_lgstats[]','multiple'=>'multiple','size'=>'5'));
		return $selector;
	}

	protected function get_search_filter_suppliers() {
		$elements = array();
		if(!empty($this->filters['suppliers'])) {
			foreach ($this->filters['suppliers'] as $supplier) {
				if($supplier['id']) {
					$entites = new entites($supplier['id']);
					$elements[] = array(
							'id' => $supplier['id'],
							'name' => $entites->raison_sociale
					);
				}
			}
		}
		templates::init_selection_attributes(array(
				array('name' => 'dyn', 'value' => '2'),
		));
		$selector .= templates::get_display_elements_completion_field($elements, $this->get_form_name(), $this->objects_type.'_suppliers', $this->objects_type.'_suppliers_id', 'fournisseur');
		return $selector;
	}

	/**
	 * Jointure externes SQL pour les besoins des filtres
	 */
	protected function _get_query_join_filters() {
		if(! array_key_exists("rubrique", $this->filters) && !array_key_exists("applicants", $this->filters) && ! array_key_exists("lgstats", $this->filters)) {
			return "";
		}
		$filter_join_query = '';
		if(!empty($this->filters['rubrique']) || (is_array($this->filters['applicants']) && count($this->filters['applicants'])) || (is_array($this->filters['lgstats']) && count($this->filters['lgstats']))) {
			$filter_join_query .= " LEFT JOIN lignes_actes ON lignes_actes.num_acte=actes.id_acte";
		}
		if(is_array($this->filters['applicants']) && count($this->filters['applicants'])) {
			$filter_join_query .= " LEFT JOIN lignes_actes_applicants ON lignes_actes_applicants.ligne_acte_num=lignes_actes.id_ligne";
		}
		if(is_array($this->filters['lgstats']) && count($this->filters['lgstats'])) {
			$filter_join_query .= " LEFT JOIN lignes_actes_statuts ON lignes_actes_statuts.id_statut=lignes_actes.statut";
		}
		return $filter_join_query;
	}

	protected function _add_query_filters() {
		$this->query_filters [] = "actes.type_acte = '".$this->filters['type_acte']."'";
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
					$codes_query [] = "lignes_actes.code like '%".addslashes($v)."%' ";
				}
				$this->query_filters [] = "(".implode(' or ', $codes_query).")";
			} else {
				$members_actes = $this->get_analyse_query()->get_query_members("actes","actes.numero","actes.index_acte", "actes.id_acte");
				$members_lignes = $this->get_analyse_query()->get_query_members("lignes_actes","lignes_actes.code","lignes_actes.index_ligne", "lignes_actes.id_ligne");
				$this->query_filters [] = "(".$members_actes["where"]." or ".$members_lignes["where"]." or actes.numero like '%".addslashes($this->filters['user_input'])."%')";
			}
		}
		if($this->filters['entite']) {
			$this->query_filters [] = "actes.num_entite = '".$this->filters['entite']."'";
		}
		if($this->filters['exercice']) {
			$this->query_filters [] = "actes.num_exercice = '".$this->filters['exercice']."'";
		}
		if($this->filters['status']) {
			if ($this->filters['status'] != '-1') {
				if ($this->filters['status'] == 32) {
					$this->query_filters [] = "((actes.statut & 32) = 32) ";
				} else {
					$this->query_filters [] = "((actes.statut & 32) = 0) and ((actes.statut & ".$this->filters['status'].") = '".$this->filters['status']."') ";
				}
			}
		}
		if($this->filters['rubrique']) {
			$rubriques_ids = array_keys(rubriques::getChilds($this->filters['rubrique']));
			$rubriques_ids[] = $this->filters['rubrique'];
			$this->query_filters [] = "lignes_actes.num_rubrique IN (".implode(",",$rubriques_ids).")";
		}
		if(count($this->filters['applicants'])) {
			$applicants = array();
			$filtre_empr='';
			$filtre_users='';
			foreach ($this->filters['applicants'] as $applicant) {
				if($applicant['id']) {
					$applicants['empr'][] = $applicant['id'];
				}
			}
			if (is_array($applicants['empr']) && count($applicants['empr'])) {
				$filtre_empr = "lignes_actes_applicants.empr_num in ('".implode("','",$applicants['empr'])."') ";
			}
			if($filtre_empr) {
				$this->query_filters [] = "(".$filtre_empr.")";
			}
		}
		if(count($this->filters['lgstats'])) {
			$this->query_filters [] = "lignes_actes.statut in (".implode(',',$this->filters['lgstats']).")";
		}
		if(count($this->filters['suppliers'])) {
			$suppliers = array();
			foreach ($this->filters['suppliers'] as $supplier) {
				if($supplier['id']) {
					$suppliers[] = $supplier['id'];
				}
			}
			if(count($suppliers)) {
				$this->query_filters [] = "actes.num_fournisseur in (".implode(',',$suppliers).")";
			}
		}
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
				",
                'exportable' => false
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
		if (((($object->statut & ~STA_ACT_ARC) == STA_ACT_ENC) && ${$parameter_name} && !empty($bib_coord->email) && strpos($bib_coord->email,'@'))) {
			$display .= "
			<a href=# onclick=\"if (confirm(pmbDojo.messages.getMessage('acquisition', 'mail_acquisition_confirm'))) {document.location='".$base_path."/acquisition.php?categ=ach&sub=".$sub."&action=print&id_bibli=".$this->filters['entite']."&id_".$this->get_initial_name()."=".$object->id_acte."&page=".$this->pager['page']."&by_mail=1';}\" >
				<img src='".get_url_icon('mail.png')."' style='border:0px' class='center' alt='".htmlentities($msg['58'],ENT_QUOTES, $charset)."' title='".htmlentities($msg['58'],ENT_QUOTES, $charset)."' />
			</a>";
		}
		return $display;
	}

	protected function _get_object_property_num_fournisseur($object) {
		$entites = new entites($object->num_fournisseur);
		return $entites->raison_sociale;
	}

	protected function _get_object_property_statut($object) {
		global $msg;

		$st = (($object->statut) & ~(STA_ACT_ARC));
		switch ($st) {
			case STA_ACT_ENC :
				return $msg['acquisition_'.$this->get_initial_name().'_enc'];
			case STA_ACT_REC :
				return $msg['acquisition_'.$this->get_initial_name().'_rec'];
			case STA_ACT_PAY :
				return $msg['acquisition_'.$this->get_initial_name().'_pay'];
			default :
				if(isset($msg['acquisition_'.$this->get_initial_name().'_enc'])) {
					return $msg['acquisition_'.$this->get_initial_name().'_enc'];
				} else {
					return '';
				}
		}
	}

	protected function get_cell_content($object, $property) {
		global $charset;

		$content = '';
		switch($property) {
			case 'statut':
				$statut = $this->_get_object_property_statut($object);
				if(($object->statut & STA_ACT_ARC) == STA_ACT_ARC) {
					$content .= '<s>'.htmlentities($statut, ENT_QUOTES, $charset).'</s>';
				} else {
					$content .= htmlentities($statut, ENT_QUOTES, $charset);
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
		if($name == 'print_mail') {
			return "<th>".$this->_get_label_cell_header($label)."</th>";
		} else {
			return parent::_get_cell_header($name, $label);
		}
	}

	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		if($property != 'print_mail') {
			$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=modif&id_bibli=".$object->num_entite."&id_exercice=".$object->num_exercice."&id_".$this->get_initial_name()."=".$object->id_acte."\"";
		}
		return $attributes;
	}

	protected function _get_query_human_global_search() {
		return $this->filters['user_input'];
	}

	protected function _get_query_human_exercice() {
		if($this->filters['exercice']) {
			$exercices = new exercices($this->filters['exercice']);
			return $exercices->libelle;
		}
		return '';
	}

	protected function _get_query_human_status() {
	    if(!empty($this->filters['status'])) {
    	    $list_statut = actes::getStatelist($this->get_type_acte());
    		return $list_statut[$this->filters['status']];
	    }
	    return '';
	}

	protected function _get_query_human_rubrique() {
		if(!empty($this->filters['rubrique'])) {
			$rubriques = new rubriques($this->filters['rubrique']);
			$budgets = new budgets($rubriques->num_budget);
			return "[".$budgets->libelle."] ".$rubriques->libelle;
		}
		return '';
	}

	protected function _get_query_human_applicants() {
		$names = array();
		foreach ($this->filters['applicants'] as $applicant) {
			if($applicant['id']) {
				$names[] = emprunteur::get_name($applicant['id']);
			}
		}
		return $names;
	}

	protected function _get_query_human_lgstats() {
		$names = array();
		foreach ($this->filters['lgstats'] as $lgstat) {
			$names[] = lgstat::getLabelFromId($lgstat);
		}
		return $names;
	}

	protected function _get_query_human_suppliers() {
		$names = array();
		foreach ($this->filters['suppliers'] as $supplier) {
			if($supplier['id']) {
				$entites = new entites($supplier['id']);
				$names[] = $entites->raison_sociale;
			}
		}
		return $names;
	}

	protected function _get_query_human() {
		global $msg;

		$humans = $this->_get_query_human_main_fields();
		if($this->filters['entite']) {
			$entites = new entites($this->filters['entite']);
			$humans['entite'] = $this->_get_label_query_human($msg['acquisition_coord_lib'], $entites->raison_sociale);
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