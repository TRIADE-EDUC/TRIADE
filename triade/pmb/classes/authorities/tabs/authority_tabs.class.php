<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_tabs.class.php,v 1.33 2021/12/28 08:46:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/elements_list_tab.class.php');
require_once($class_path.'/skos/skos_concept.class.php');
require_once($class_path.'/indexation.class.php');
require_once($class_path.'/authorities/tabs/authority_tabs_parser.class.php');
require_once($class_path.'/filter_results.class.php');
require_once($class_path.'/encoding_normalize.class.php');

class authority_tabs {
	
	/**
	 * Instance de la classe authority associée
	 * @var authority
	 */
	protected $authority;
	
	/**
	 * Tableau des onglets de l'autorité
	 * @var elements_list_tab Tableau des onglets
	 */
	protected $tabs;
	
	/**
	 * Constructeur
	 * @param authority $authority Instance de la classe authority associée
	 */
	public function __construct($authority){
		$this->authority = $authority;
		$parser = new authority_tabs_parser();
		$this->tabs = $parser->get_tabs_for($authority->get_string_type_object());
		$this->init_tabs_contents();
	}
	
	/**
	 * Retourne le tableau des onglets
	 * @return authority_tab Tableau d'authority_tab
	 */
	public function get_tabs(){
		return $this->tabs;
	}
	
	/**
	 * Ajoute un onglet au tableau
	 * @param authority_tab $tab Onglet à ajouter
	 */
	protected function add_tab($tab) {
		if ($tab) {
			$this->tabs[] = $tab;
		}
	}
	
	/**
	 * Retourne la portion de requête pour la limite des résultats
	 * @return string Portion de la requête
	 */
	protected static function get_limit($elements_ids){
		global $pmb_nb_elems_per_tab;
		global $tab_page;
		global $tab_nb_per_page;
		
		if (!$tab_nb_per_page) {
			$tab_nb_per_page = $pmb_nb_elems_per_tab;
		}
		if($tab_page){
			return array_slice($elements_ids, (($tab_page-1) * ($tab_nb_per_page*1)), ($tab_nb_per_page*1));
		}
		return array_slice($elements_ids, 0,($tab_nb_per_page*1));
	}
	
	public static function _sort_groups_by_label($a, $b) {
		if (strtolower($a['label']) == strtolower($b['label'])) {
			return 0;
		}
		return (strtolower($a['label']) < strtolower($b['label'])) ? -1 : 1;
	}
	
	protected function init_tabs_contents() {
		global $quoi, $class_path;
		
		foreach ($this->tabs as $tab) {
			$callable = $tab->get_callable();
			if (count($callable)) {
				require_once($class_path.'/authorities/tabs/'.$callable['class'].'.class.php');
				call_user_func_array(array($callable['class'], $callable['method']), array($tab, $this->authority));
			} else {
				$query_elements = $tab->get_query_elements();
				$nb_result = 0;
				pmb_mysql_query('set session group_concat_max_len = 16777216');
				if (($query_elements['getconcepts'] != 'true') || (($query_elements['getconcepts'] == 'true') && count($this->authority->get_concepts_ids()))) {
					// Si on a besoin des concepts composés, et qu'aucun n'est trouvé, ça ne sert à rien d'aller plus loin
					$query_clauses = $this->get_query_clauses($query_elements);
					$query = 'select group_concat(distinct '.$query_elements['table'].'.'.$query_elements['select'].' separator ",")';
					$query.= $query_clauses['from'];
					$query.= $query_clauses['where'];
					$filtered_results = self::get_filtered_results(pmb_mysql_result(pmb_mysql_query($query), 0, 0), $tab);
					if($filtered_results){
						$nb_result = (substr_count($filtered_results,",") + 1);
					}
				}
				
				$tab->set_nb_results($nb_result);
				if (!$quoi && $nb_result) {
					// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
					$quoi = $tab->get_name();
				}
				$elements_ids = array();
				if ($nb_result && ($quoi == $tab->get_name())) {
					$filtered_elements = $this->get_tab_filters($tab);
					
					if (!$tab->has_filters_values() || ($tab->has_filters_values() && count($filtered_elements))) {
						// On n'a pas de filtre coché ou on a au moins un résultat après filtrage, sinon on ne fait rien
						$tab->set_nb_filtered_results(count($filtered_elements));
						$query = 'select group_concat(distinct '.$query_elements['table'].'.'.$query_elements['select'].' '.$query_clauses['order'].' separator ",") as elements_id';
						$query.= $query_clauses['from'].$query_clauses['order_from'];
						$query.= $query_clauses['where'];
						if (count($filtered_elements)) {
							$query.= ' and '.$query_elements['table'].'.'.$query_elements['select'].' in ('.implode(',', $filtered_elements).')';
						}
						
						//$query.= $query_clauses['order']; non fonctionnel, mis dans le group_concat
						
						$result = pmb_mysql_query($query);
						if($result && pmb_mysql_num_rows($result)){
							$filtered_result = explode(',', self::get_filtered_results(pmb_mysql_result($result, 0, 0),$tab));
							$filtered_result = self::get_limit($filtered_result);
							foreach($filtered_result as $element_id){
								if ($tab->get_content_type() == 'authorities') {
								    $authority =  authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => ($element_id*1), 'type_object' => $tab->get_content_authority_type()]);
									$elements_ids[] = $authority->get_id();
								} else {
									$elements_ids[] = $element_id;
								}
							}
						}
					}
				}
				$tab->set_contents($elements_ids);
			}
		}
	}
	
	/**
	 * 
	 * @param elements_list_tab $tab
	 * @return multitype:NULL
	 */
	protected function get_tab_filters($tab) {
		global $msg;
			
		$query_elements = $tab->get_query_elements();
		$query_clauses = $this->get_query_clauses($query_elements);
		$filters = $tab->get_filters();
		$elements_ids = array();
		
		foreach ($filters as $filter) {
			$result_ids = array();
			
			if ($filter['type'] == 'callable') {
				// Si c'est un filtre de type callable, on appelle la méthode
				$result_ids = call_user_func_array(array($filter['class'], $filter['method']), array($tab, $filter, $this->authority->get_num_object()));
			} else {
				// Sinon on construit les requêtes qui vont bien
				if ($filter['type'] == 'marc_list') {
					$marc_list = marc_list_collection::get_instance($filter['marcname']);
				}
				$groups = array();
				
				$query = 'select group_concat('.$query_elements['table'].'.'.$query_elements['select'].' separator ",") as elements_ids, '.$query_elements['table'].'.'.$filter['field'].' as group_id';
				$query.= $query_clauses['from'];
				$query.= $query_clauses['where'];
				$query.= ' group by '.$query_elements['table'].'.'.$filter['field'];
				
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						if(!$row->group_id){
							$row->group_id = '__';
						}
						if(!isset($groups[$row->group_id])){
							$label = '';
							if ($filter['type'] == 'marc_list') {
								if ($filter['marcname'] != 'oeuvre_link') {
									$label = (!empty($marc_list->table[$row->group_id]) ? $marc_list->table[$row->group_id] : (!empty($msg['authority_marc_list_empty_filter_'.$filter['marcname']]) ? $msg['authority_marc_list_empty_filter_'.$filter['marcname']] : $msg['authority_marc_list_empty_filter']));
								} else {
									// Dans le cas d'oeuvre_link.xml on a un étage de plus...
									foreach ($marc_list->table as $link_type) {
										if (isset($link_type[$row->group_id])) {
											$label = $link_type[$row->group_id];
											break;
										}
									}
								}
							}
							$filtered_results = self::get_filtered_results($row->elements_ids, $tab);
							$nb_results = (substr_count($filtered_results,",") + 1);
							$groups[$row->group_id] = array(
									'label' => $label,
									'nb_results' => $nb_results
							);
						}
					}
				}
				if (count($groups)) {
					// On trie le tableau
					uasort($groups, array('authority_tabs', '_sort_groups_by_label'));
					$tab->add_groups($filter['name'], array(
							'label' => $filter['label'],
							'elements' => $groups
					));

					$filter_values = $tab->get_filter_values($filter['name']);
					
					/**
					 * Petit hack permettant de s'en sortir avec les fonctions d'auteurs non valorisées
					 * Dans les colonnes en base elles sont soit à 0, soit à chaine vide.
					 * On passe le filtre à '__' pour pouvoir le traiter dans les formulaires
					 * 	-> Le 0 n'est pas pris en compte dans les values des input checkbox
					 * 	-> On construit un message spécifique "Sans valeur" paramétrable suivant le type de filtre
					 * 		authority_marc_list_empty_filter -> Message standard
					 * 		authority_marc_list_empty_filter_nom_marclist -> Message personnalisé
					 */
					$array_search_result = array_search('__', $filter_values);
					if($array_search_result !== false){
						$filter_values[$array_search_result] = '';
						$filter_values[] = 0;
					}
					
					//Si on a des résultats; on passe à la suite
					if($filter_values && count($filter_values)){
						$query = 'select group_concat(distinct '.$query_elements['table'].'.'.$query_elements['select'].' separator ",") as elements_ids';
						$query.= $query_clauses['from'];
						$query.= $query_clauses['where'];
						$query.= ' and '.$query_elements['table'].'.'.$filter['field'].' in ("'.implode('","', $filter_values).'")';
						
						$result = pmb_mysql_query($query);
						if(pmb_mysql_num_rows($result)){
							$row = pmb_mysql_fetch_object($result);
							$filtered_results = self::get_filtered_results($row->elements_ids, $tab);
							$result_ids = explode(',', $filtered_results);
						}
					}
				}
			}
			if (count($elements_ids) && count($result_ids)) {
				$elements_ids = array_intersect($elements_ids, $result_ids);
			} else if (count($result_ids)) {
				$elements_ids = $result_ids;
			}
		}
		return $elements_ids;
	}
	
	protected function get_query_clauses($query_elements) {
		$query_clauses = array();
		$tables = array();
		
		// Clause from
		$query_clauses['from'] = ' from '.$query_elements['table'];
		$tables[] = $query_elements['table'];
		foreach ($query_elements['join'] as $join) {
			$query_clauses['from'].= ' join '.$join['table'];
			$tables[] = $join['table'];
			$query_clauses['from'].= ' on '.$query_elements['table'].'.'.$join['referencefield'].' = '.$join['table'].'.'.$join['externalfield'];
			if ($join['condition']) {
				$query_clauses['from'].= ' and '.$join['condition'];
			}
		}
		
		// Clause where
		$query_clauses['where'] = '';
		foreach ($query_elements['elementfield'] as $elementfield) {
			if (!$query_clauses['where']) {
				$query_clauses['where'].= ' where (';
			} else {
				$query_clauses['where'].= ' or';
			}
			$query_clauses['where'].= ' '.$query_elements['table'].'.'.$elementfield.' = '.$this->authority->get_num_object();
		}
		if ($query_clauses['where']) $query_clauses['where'].= ')';
		foreach ($query_elements['condition'] as $condition) {
			if (!$query_clauses['where']) {
				$query_clauses['where'].= ' where';
			} else {
				$query_clauses['where'].= ' and';
			}
			$query_clauses['where'].= ' '.$condition;
		}
		if ($query_elements['getconcepts'] == 'true') {
			$concepts_ids = $this->authority->get_concepts_ids();
			if (count($concepts_ids)) {
				if (!$query_clauses['where']) {
					$query_clauses['where'].= ' where';
				} else {
					$query_clauses['where'].= ' and';
				}
				$query_clauses['where'].= ' '.$query_elements['conceptfield'].' in ('.implode(',', $concepts_ids).')';
			}
		}
		
		// Clause order
		$query_clauses['order'] = '';
		$query_clauses['order_from'] = '';
		if ($query_elements['order']) {
			// On commence par faire une jointure si nécessaire
			if ($query_elements['order']['table'] && !in_array($query_elements['order']['table'], $tables)) {
				if ($query_elements['order']['joinclause']) {
					$query_clauses['order_from'].= ' '.$query_elements['order']['joinclause'];
				} else {
					$query_clauses['order_from'].= ' join '.$query_elements['order']['table'];
					$query_clauses['order_from'].= ' on '.$query_elements['table'].'.'.$query_elements['order']['referencefield'].' = '.$query_elements['order']['table'].'.'.$query_elements['order']['externalfield'];
				}
			}
			$query_clauses['order'].= ' order by '.($query_elements['order']['table'] ? $query_elements['order']['table'] : $query_elements['table']).'.'.$query_elements['order']['field'];
		}
		return $query_clauses;
	}
	
	/**
	 * Méthode permettant de récupérer les autorités indexées avec un concept utilisant cette autorité
	 * @param elements_list_tab $tab
	 * @param authority $authority
	 */
	protected static function get_tab_authorities_indexed_with_authority($tab, $authority){
		$concepts_ids = $authority->get_concepts_ids();
		self::get_tab_authorities_indexed_with_concepts($tab, $authority, $concepts_ids);
	}
	
	protected static function get_nb_authorities_indexed_with_concepts($concepts_ids, $types_needed){
		if (count($concepts_ids)) {
			$query = 'select count(distinct num_object, type_object) from index_concept where num_concept in ('.implode(',', $concepts_ids).') and type_object in ('.implode(',', $types_needed).')';
			return pmb_mysql_result(pmb_mysql_query($query), 0, 0);
		}
		return 0;
	}
	
	/**
	 * Méthode permettant de récupérer les autorités indexées avec les concepts dont les ids sont passés en paramètres
	 * @param elements_list_tab $tab
	 * @param authority_tabs $authority_tabs
	 * @param array $concepts_ids
	 */
	protected static function get_tab_authorities_indexed_with_concepts($tab, $authority, $concepts_ids){
		global $msg;
		global $quoi;
		
		$types_needed = array(TYPE_AUTHOR, TYPE_CATEGORY, TYPE_PUBLISHER, TYPE_COLLECTION, TYPE_SUBCOLLECTION, TYPE_SERIE, TYPE_TITRE_UNIFORME, TYPE_INDEXINT, TYPE_AUTHPERSO);
		
		$nb_result = 0;
		
		$nb_result = self::get_nb_authorities_indexed_with_concepts($concepts_ids, $types_needed);
		
		$tab->set_nb_results($nb_result);
		if (!$quoi && $nb_result) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}
		if ($nb_result && ($quoi == $tab->get_name())) {
			// On définit les filtres
			$filter = array(
					'name' => 'common_indexed_authorities_by_types',
					'label' => $msg['authority_tabs_common_indexed_authorities_by_types']
			);
			$tab->set_filters(array($filter));
			$groups = array();
			$query = 'select count(distinct num_object) as nb, type_object, id_authperso, authperso_name from index_concept left join authperso_authorities on num_object = id_authperso_authority and type_object = '.TYPE_AUTHPERSO.' left join authperso on id_authperso = authperso_authority_authperso_num where num_concept in ('.implode(',', $concepts_ids).') and type_object in ('.implode(',', $types_needed).') group by type_object, id_authperso';
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					if (($row->type_object == TYPE_AUTHPERSO) && !isset($groups[1000 + $row->id_authperso])) {
						$groups[1000 + $row->id_authperso] = array(
								'label' => $row->authperso_name,
								'nb_results' => $row->nb
						);
					} else if (!isset($groups[$row->type_object])){
						$groups[$row->type_object] = array(
								'label' => authority::get_type_label_from_type_id(index_concept::get_aut_table_type_from_type($row->type_object)),
								'nb_results' => $row->nb
						);
					}
				}
			}
			if (count($groups)) {
				// On trie le tableau
				uasort($groups, array('authority_tabs', '_sort_groups_by_label'));
				$tab->add_groups($filter['name'], array(
						'label' => $filter['label'],
						'elements' => $groups
				));
				
				$filter_values = $tab->get_filter_values($filter['name']);

				$authpersos_needed = array();
				if ($filter_values && count($filter_values)) {
					$types_needed = array();
					foreach ($filter_values as $value) {
						if ($value > 1000) {
							if (!in_array(TYPE_AUTHPERSO, $types_needed)) {
								$types_needed[] = TYPE_AUTHPERSO;
							}
							$authpersos_needed[] = $value - 1000;
						} else {
							$types_needed[] = $value;
						}
					}
					
				}
				
				$query = 'select SQL_CALC_FOUND_ROWS distinct num_object, type_object, authperso_authority_authperso_num';
				$query.= ' from index_concept left join authperso_authorities on num_object = id_authperso_authority and type_object = '.TYPE_AUTHPERSO;
				$query.= ' where num_concept in ('.implode(',', $concepts_ids).') and type_object in ('.implode(',', $types_needed).')';
				// si on a des filtres sur des authorités persos
				if (count($authpersos_needed)) {
					$query.= ' and (authperso_authority_authperso_num is null or authperso_authority_authperso_num in ('.implode(',', $authpersos_needed).'))';
				}
				$query.= authority_tabs::get_limit_concept();
				// on lance la requête
				$result = pmb_mysql_query($query);
				$records_ids = array();
				if($result && pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
					    $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $row->num_object, 'type_object' => index_concept::get_aut_table_type_from_type($row->type_object)]);
						$records_ids[] = $authority->get_id();
					}
				}
				$nb_filtered_results = pmb_mysql_result(pmb_mysql_query('select FOUND_ROWS()'), 0, 0);
				$tab->set_nb_filtered_results($nb_filtered_results);
				$tab->set_contents($records_ids);
			}
		}
	}
	
	/**
	 * Retourne la portion de requête pour la limite des résultats des requetes concepts
	 * @return string Portion de la requête
	 */
	protected static function get_limit_concept(){
		global $pmb_nb_elems_per_tab;
		global $tab_page;
		global $tab_nb_per_page;
	
		if (!$tab_nb_per_page) {
			$tab_nb_per_page = $pmb_nb_elems_per_tab;
		}
		if($tab_page){
			return ' limit '.(($tab_page-1) * ($tab_nb_per_page*1)).', '.($tab_nb_per_page*1).' ';
		}
		return ' limit '.($tab_nb_per_page*1).' ';
	}
	
	
	/**
	 * Méthode permettant de filtrer les resultats 
	 * @param $elements_ids String id d'element séparés par des virgules
	 * @param $tab elements_list_tab Instance d'onglet courante
	 * 
	 * Nous pourrons implémenter les fitlres sur les droits autorités quand ils auront été développés
	 */
	protected static function get_filtered_results($elements_ids, $tab){
		switch($tab->get_content_type()){
			case 'records':
				$filter_results = new filter_results($elements_ids);
				$filtered_result = $filter_results->get_results();
				if($filtered_result){
					return $filtered_result;
				}
				return 0;
			default:
				return $elements_ids;

		}
		return 0;
	}
	
	/**
	 * 
	 * @param array $elements_ids
	 * @param elements_list_tab $tab
	 * @return array
	 */
	protected static function get_sorted_results($elements_ids, $tab){
		switch($tab->get_content_type()){
			case 'records':
				$sorted_ids = array();
				if (is_array($elements_ids) && count($elements_ids)) {
					$query = '
							SELECT notice_id
							FROM notices
							WHERE notice_id IN ('.implode(',',$elements_ids).')
							ORDER BY index_sew';
					$result = pmb_mysql_query($query);
				
					if (pmb_mysql_num_rows($result)) {
						while ($row = pmb_mysql_fetch_assoc($result)) {
							$sorted_ids[] = $row['notice_id'];
						}
					}
				}
				break;
			default:
				$sorted_ids = array();
				break;		
		}
		return $sorted_ids;
	}

	protected function get_tab_composed_concepts($tab, $authority) {
		global $quoi;
		$concepts_ids = $authority->get_concepts_ids();
		$nb_result = count($concepts_ids);
	
		$tab->set_nb_results($nb_result);
		if (!$quoi && $nb_result) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}
		if ($nb_result && ($quoi == $tab->get_name())) {
			$elements_ids = array();
			foreach ($concepts_ids as $element_id) {
			    $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $element_id, 'type_object' => $tab->get_content_authority_type()]);
				$elements_ids[] = $authority->get_id();
			}
			$tab->set_contents($elements_ids);
		}
	}

	protected function get_tab_composed_records($tab, $authority) {
		global $quoi;
		
		$ids = $authority->get_records_ids();
		$nb_result = count($ids);
	
		$tab->set_nb_results($nb_result);
		if (!$quoi && $nb_result) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}
		$elements_ids = array();
		if ($nb_result && ($quoi == $tab->get_name())) {
			foreach ($ids as $element_id) {
				$query = "select responsability_notice from responsability where id_responsability=".$element_id;
				$result = pmb_mysql_query($query);				
				if($result && pmb_mysql_num_rows($result)){
					$row = pmb_mysql_fetch_object($result);
					$elements_ids[] = $row->responsability_notice;
				}
			}
			$tab->set_contents($elements_ids);
		}
	}

	protected function get_tab_composed_tus($tab, $authority) {
		global $quoi;
		
		$ids = $authority->get_tus_ids();
		$nb_result = count($ids);
	
		$tab->set_nb_results($nb_result);
		if (!$quoi && $nb_result) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}
		$elements_ids = array();
		if ($nb_result && ($quoi == $tab->get_name())) {
			foreach ($ids as $element_id) {
				$query = "select responsability_tu_num from responsability_tu where id_responsability_tu=".$element_id;
				$result = pmb_mysql_query($query);				
				if($result && pmb_mysql_num_rows($result)){
					$row = pmb_mysql_fetch_object($result);
					$authority =  authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $row->responsability_tu_num, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
					$elements_ids[] = $authority->get_id();
				}
			}
			$tab->set_contents($elements_ids);
		}
	}
	
	protected static function get_tab_used_in_pperso_authorities($tab, $authority){
		global $msg, $quoi;
		
		$authority->get_used_in_pperso_authorities();
		$groups = array();
		$elements_ids=array();
		
		$author_elements_ids=$authority->get_used_in_pperso_authorities_ids('author');				
		if($author_nb_result=count($author_elements_ids)){
			$groups[AUT_TABLE_AUTHORS] = array(
					'label' => $msg[133],
					'nb_results' => $author_nb_result,
					'elements_group_ids' => $author_elements_ids
			);
		}	
		$publisher_elements_ids=$authority->get_used_in_pperso_authorities_ids('publisher');
		if($publisher_nb_result=count($publisher_elements_ids)){
			$groups[AUT_TABLE_PUBLISHERS] = array(
					'label' => $msg[135],
					'nb_results' => $publisher_nb_result,
					'elements_group_ids' => $publisher_elements_ids
			);
		}	
		
		$categ_elements_ids=$authority->get_used_in_pperso_authorities_ids('categ');
		if($categ_nb_result=count($categ_elements_ids)){
			$groups[AUT_TABLE_CATEG] = array(
					'label' => $msg[134],
					'nb_results' => $categ_nb_result,
					'elements_group_ids' => $categ_elements_ids
			);
		}			
		$collection_elements_ids=$authority->get_used_in_pperso_authorities_ids('collection');
		if($collection_nb_result=count($collection_elements_ids)){
			$groups[AUT_TABLE_COLLECTIONS] = array(
					'label' => $msg[136],
					'nb_results' => $collection_nb_result,
					'elements_group_ids' => $collection_elements_ids
			);
		}	
		$subcollection_elements_ids=$authority->get_used_in_pperso_authorities_ids('subcollection');
		if($subcollection_nb_result=count($subcollection_elements_ids)){
			$groups[AUT_TABLE_SUB_COLLECTIONS] = array(
					'label' => $msg[137],
					'nb_results' => $subcollection_nb_result,
					'elements_group_ids' => $subcollection_elements_ids
			);
		}	
		
		$indexint_elements_ids=$authority->get_used_in_pperso_authorities_ids('indexint');
		if($indexint_nb_result=count($indexint_elements_ids)){
			$groups[AUT_TABLE_INDEXINT] = array(
					'label' => $msg['indexint_menu'],
					'nb_results' => $indexint_nb_result,
					'elements_group_ids' => $indexint_elements_ids
			);
		}	
		
		$serie_elements_ids=$authority->get_used_in_pperso_authorities_ids('serie');
		if($serie_nb_result=count($serie_elements_ids)){
			$groups[AUT_TABLE_SERIES] = array(
					'label' => $msg[333],
					'nb_results' => $serie_nb_result,
					'elements_group_ids' => $serie_elements_ids
			);
		}	
		
		$tu_elements_ids=$authority->get_used_in_pperso_authorities_ids('tu');
		if($tu_nb_result=count($tu_elements_ids)){
			$groups[AUT_TABLE_TITRES_UNIFORMES] = array(
					'label' => $msg['aut_menu_titre_uniforme'],
					'nb_results' => $tu_nb_result,
					'elements_group_ids' => $tu_elements_ids
			);
		}	
		
		$authperso_elements_ids=$authority->get_used_in_pperso_authorities_ids('authperso');
		if($authperso_nb_result=count($authperso_elements_ids)){
			$groups[AUT_TABLE_AUTHPERSO] = array(
					'label' => $msg['admin_menu_authperso'],
					'nb_results' => $authperso_nb_result,
					'elements_group_ids' => $authperso_elements_ids
			);
		}	
		
		$nb_result= $author_nb_result+$publisher_nb_result+$categ_nb_result+$collection_nb_result+$subcollection_nb_result+
			$indexint_nb_result+$serie_nb_result+$tu_nb_result+$authperso_nb_result;
				
		if(!$nb_result) return;	
		
		$tab->set_nb_results($nb_result);
		if (!$quoi && $nb_result) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}
		$filter=array();
		$filter['name']='pp_authorities';
		$filter['label']=$msg[132];
		
		$elements_ids=array_merge($author_elements_ids,$publisher_elements_ids,$categ_elements_ids,$collection_elements_ids,$subcollection_elements_ids,
				$indexint_elements_ids,$serie_elements_ids,$tu_elements_ids,$authperso_elements_ids);

		if (count($groups)) {
			uasort($groups, array('authority_tabs', '_sort_groups_by_label'));
			$tab->add_groups($filter['name'], array(
					'label' => $filter['label'],
					'elements' => $groups
			));
			$list_filters = $tab->get_filter_values($filter['name']);	
			if(count($list_filters)){
				$flag_filter=0;
				foreach ($list_filters as $filter_aut_table){
					if(count($groups[$filter_aut_table]['elements_group_ids'])){
						$flag_filter=1; // au moins un filtre correspond a un groupe présent
					}
				}
				if($flag_filter){
					$elements_ids=array();
					foreach ($list_filters as $filter_aut_table){
						if(count($groups[$filter_aut_table]['elements_group_ids'])){
							$elements_ids=array_merge($elements_ids,$groups[$filter_aut_table]['elements_group_ids']);
						}
					}
					$tab->set_nb_filtered_results(count($elements_ids));					
				}else {
					// on garde tout les $elements_ids
				}	
			}
		}
		$tab->set_contents(self::get_limit($elements_ids));
	}
	
	protected static function get_tab_used_in_pperso_records($tab, $authority){
		global $quoi;

		$authority->get_used_in_pperso_authorities();
		$elements_ids=$authority->get_used_in_pperso_authorities_ids('notices');
		if(!$nb_result=count($elements_ids)) return;
		
		$tab->set_nb_results($nb_result);
		if (!$quoi && $nb_result) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}
		$tab->set_contents( self::get_limit($elements_ids) );		
	}		
	
	protected static function get_tab_used_in_pperso_cms_editorial_sections($tab, $authority){
		global $quoi;		

		$authority->get_used_in_pperso_authorities();
		$elements_ids=$authority->get_used_in_pperso_authorities_ids('section');
		if(!$nb_result=count($elements_ids)) return;
	
		$tab->set_nb_results($nb_result);
		if (!$quoi && $nb_result) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}		
		$tab->set_contents( self::get_limit($elements_ids) );
	}

	protected static function get_tab_used_in_pperso_cms_editorial_articles($tab, $authority){
		global $quoi;
	
		$authority->get_used_in_pperso_authorities();
		$elements_ids=$authority->get_used_in_pperso_authorities_ids('article');
		if(!$nb_result=count($elements_ids)) return;
	
		$tab->set_nb_results($nb_result);
		if (!$quoi && $nb_result) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}		
		$tab->set_contents( self::get_limit($elements_ids) );
	}
	
	protected static function get_tab_entities_graphed($tab, $authority){
		global $pmb_entity_graph_recursion_lvl;
		global $pmb_entity_graph_activate, $quoi;
		
		if($pmb_entity_graph_activate && $quoi == $tab->get_name()) {
			$entity_graph = entity_graph::get_entity_graph($authority, 'authority');
			$entity_graph->get_recursive_graph($pmb_entity_graph_recursion_lvl);
			$tab->set_contents($entity_graph->get_json_entities_graphed());
		}
		$tab->set_nb_results(0);
	}

	/**
	 * Méthode permettant de récupérer les exemplaires indexées avec les concepts dont les ids sont passés en paramètres
	 * @param elements_list_tab $tab
	 * @param authority $authority
	 */
	protected static function get_tab_exemplaire_indexed_with_concepts($tab, $authority){
	    global $quoi;
	    $expl_tab_ids = exemplaire::get_expl_from_concept($authority);
	    $tab->set_nb_results(count($expl_tab_ids));
	    if($quoi == $tab->get_name()) {
    	    $tab->set_contents($expl_tab_ids);
	    }
	}

	/**
	 * Méthode permettant de récupérer les rubriques du cms indexées avec les concepts dont les ids sont passés en paramètres
	 * @param elements_list_tab $tab
	 * @param authority $authority
	 */
	
	protected static function get_tab_cms_section_indexed_with_concepts($tab, $authority){
	    global $quoi;
	    $cms_section_tab_ids = cms_editorial_data::get_cms_section_from_concept($authority);
	    $tab->set_nb_results(count($cms_section_tab_ids));
	    if($quoi == $tab->get_name()) {
    	    $tab->set_contents($cms_section_tab_ids);
	    }
	}

	/**
	 * Méthode permettant de récupérer les articles du cms indexées avec les concepts dont les ids sont passés en paramètres
	 * @param elements_list_tab $tab
	 * @param authority $authority
	 */
	
	protected static function get_tab_cms_article_indexed_with_concepts($tab, $authority){
	    global $quoi;
	    $cms_article_tab_ids = cms_editorial_data::get_cms_article_from_concept($authority);
	    $tab->set_nb_results(count($cms_article_tab_ids));
	    if($quoi == $tab->get_name()) {
	        $tab->set_contents($cms_article_tab_ids);
	    }
	}
}