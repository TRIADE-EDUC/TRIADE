<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_authorities.class.php,v 1.14.2.1.2.3 2025/05/15 08:16:15 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_sphinx_authorities extends searcher_sphinx {

	protected $authority_type;

    protected $object_table = '';

	protected $object_table_key;

	// table temporaire contenant les résultats filtrés triés..;
    public $object_key;

	public function __construct($user_query){
		parent::__construct($user_query);
		$this->id_key = 'id_authority';
		$this->object_key = 'id_authority';
		$this->index_name = 'authors,categories,indexint,series,publishers,collections,subcollections,titres_uniformes,concepts';
 	}

 	protected function get_search_indexes(){
 		global $lang;
 		global $sphinx_indexes_prefix;
 		$indexes = explode(',',$this->index_name);
 		$index = "";
 		foreach($indexes as $index_name){
 			if($index) $index.= ',';
 			$index.= $sphinx_indexes_prefix.trim($index_name).'_'.$lang.','.$sphinx_indexes_prefix.trim($index_name);
 		}
 		if(!$this->authority_type){
	 		$result = pmb_mysql_query('select id_authperso from authperso');
	 		if (pmb_mysql_num_rows($result)) {
	 			while ($row = pmb_mysql_fetch_object($result)) {
	 				if($index) $index.= ',';
	 				$index.= $sphinx_indexes_prefix.'authperso_'.$row->id_authperso.'_'.$lang.','.$sphinx_indexes_prefix.'authperso_'.$row->id_authperso;
	 			}
	 		}
 		}
 		return $index;
 	}

	protected function get_full_raw_query(){
	    global $sub;

		if (!empty($this->authority_type)) {
		    $query = "SELECT id_authority AS id, 100 AS weight FROM authorities WHERE type_object = $this->authority_type ";
		    if (!empty($this->fields_restrict)) {
		        $parts = explode('_', $this->fields_restrict[0]);
		        $code_champ = $parts[1];
		        $code_ss_champ = $parts[2];
		        $query = "SELECT id_authority AS id, 100 AS weight FROM authorities_fields_global_index WHERE type = $this->authority_type AND code_champ = $code_champ AND code_ss_champ = $code_ss_champ ";
		    }

		    if (!empty($sub) && substr($sub, -4) == 'last') {
		        $query .= "ORDER BY id_authority DESC";
		    }
			return $query;
		}
		return 'SELECT id_authority AS id, 100 AS weight FROM authorities';
	}

	protected function _filter_results(){
		if($this->objects_ids!='') {
			global $module_from;
			$this->filter_authorities_forms();
			if (is_string($module_from) && (strpos($module_from, 'catalog') !== false)) {
				$field = 'authorities_statuts_searcher_autority';
				if (strpos($module_from, 'autocomplete') !== false) {
					$field = 'authorities_statuts_autocomplete';
				}
				$query = "
					SELECT num_object
					FROM authorities
					LEFT JOIN authorities_statuts
					ON authorities.num_statut = authorities_statuts.id_authorities_statut
					WHERE num_object IN ($this->objects_ids)
					AND {$field} = 1
					AND authorities.type_object = $this->authority_type";
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					$this->objects_ids = '';
					while ($row = pmb_mysql_fetch_object($result)) {
						if($this->objects_ids != '') {
							$this->objects_ids .= ', ';
						}
						$this->objects_ids .= $row->num_object;
					}
				}
			}
// 			$fr = new filter_results($this->objects_ids);
// 			$this->objects_ids = $fr->get_results();
			$query = 'delete from '.$this->get_tempo_tablename();
			if($this->objects_ids != ''){
				$query.=' where id_authority not in ('.$this->objects_ids.')' ;
			}
			pmb_mysql_query($query) or die(pmb_mysql_error());
		}
	}

	protected function filter_authorities_forms(){
		//A DERIVER
	}

	public function get_full_query(){
		$this->get_result();
		$query =  'select distinct id_authority, pert from '.$this->get_tempo_tablename();
		return $query;
	}

	public function get_sorted_result($tri = "default",$start=0,$number=20){
		$this->tri = $tri;
		$this->get_result();
		$authority_type = $this->get_sort_entity_type();
		$sort = new sort($authority_type,"session");
		$query = $sort->appliquer_tri($this->tri, $this->get_raw_query(), 'id_authority', $start, $number);
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
		    $this->result=array();
		    while($row = pmb_mysql_fetch_object($res)){
		        $this->result[] = $row->id_authority;
		    }
		}
		return $this->result;
	}

	public function explain($display,$mode,$mini=false){
		global $begin_result_liste, $end_result_liste;
		print '<div style="margin-left:10px;width:49%;overflow:hidden;float:left">';
		print '<h1>Recherche SPHINX</h1>';
		print '<p>QUERY : '.$this->sphinx_query.'</p>';
		$start = microtime(true);
 		print '<p>Nombre de resultats trouves: '.$this->get_nb_results().'</p>';
 		$result = $this->get_sorted_result();
 		if($this->get_nb_results()>0 && $result){
	 		$inter = microtime(true);
		 	print '<p>Temps de calcul (en seconde) : '.($inter - $start).'</p>';
		 	$elements_authorities_list_ui = new elements_authorities_list_ui($result, 20, $this->authority_type);
		 	$elements = $elements_authorities_list_ui->get_elements_list();
		 	print $begin_result_liste;
		 	print $elements;
		 	print $end_result_liste;
	 		print '<p>Temps de gen page (en seconde) : '.(microtime(true) - $inter).'</p>';
 		}
 		print '<p>Temps Total (en seconde) : '.(microtime(true) - $start).'</p></div>';
	}

	protected function get_filters(){
		$filters = parent::get_filters();
		global $authority_statut;
		if($authority_statut){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée!
			$filters[] = array(
				'name'=> 'status',
				'values' => $authority_statut*1
			);
		}
		return $filters;
	}

	public function get_authority_tri() {
		// à surcharger si besoin
		return '';
	}

	public function get_object_key() {
		return $this->object_key;
	}

	public function get_object_table() {
		return $this->object_table;
	}

	public function get_object_table_key() {
		return $this->object_table_key;
	}

	public function get_authority_type() {
		return $this->authority_type;
	}

	public function get_human_query() {
		global $msg, $charset;

		$human_query = '';
		$human_queries = $this->_get_human_queries();
		if (count($human_queries)) {
			foreach ($human_queries as $element) {
				if ($human_query) {
					$human_query.= ', ';
				}
				$human_query.= '<b>'.$element['name'].'</b> '.htmlentities($element['value'], ENT_QUOTES, $charset);
			}
		}
		$nb_results = $this->get_nb_results();
		if($nb_results) {
			$human_query.= " => ".sprintf($msg["searcher_results"], $nb_results);
		} else {
			$human_query.= " => ".sprintf($msg['1915'], $nb_results);
		}
		return "<div class='othersearchinfo'>".$human_query."</div>";
	}

	protected function _get_human_queries() {
		global $authority_statut, $msg;

		$human_queries = array();
		if ($this->user_query) {
			$human_queries[] = array(
					'name' => $msg['global_search'],
					'value' => $this->user_query
			);
		}
		if ($authority_statut) {
			$authority_statut_label = pmb_mysql_result(pmb_mysql_query('select authorities_statut_label from authorities_statuts where id_authorities_statut = '.$authority_statut), 0, 0);
			$human_queries[] = array(
					'name' => $msg['authorities_statut_label'],
					'value' => $authority_statut_label
			);
		}

		return $human_queries;
	}

	protected function _get_no_display() {
	    global $no_display;

	    if($no_display) {
	        $authority = new authority(0, $no_display, $this->authority_type);
	        return $authority->get_id();
	    }
	    return 0;
	}
	
	protected function get_sort_entity_type($authority_type = 0) {
	    if ($authority_type === 0) {
	        $authority_type = $this->get_authority_type();
	    }
	    switch ($authority_type) {
	        case AUT_TABLE_CATEG:
	            return "categories";
	        case AUT_TABLE_AUTHPERSO:
	            return "authperso";
	        case AUT_TABLE_CONCEPT:
	            return "concepts";
	        case AUT_TABLE_COLLECTIONS:
	            return "collections";
	        case AUT_TABLE_TITRES_UNIFORMES:
	            return "titres_uniformes";
	        case AUT_TABLE_SUB_COLLECTIONS:
	            return "subcollections";
	        case AUT_TABLE_SERIES:
	            return "series";
	        case AUT_TABLE_PUBLISHERS:
	            return "publishers";
	        case AUT_TABLE_INDEXINT:
	            return "indexint";
	        case AUT_TABLE_AUTHORS:
	            return "authors";
	        default:
	            // Si on n'a rien on regarde dans searcher_authorities_instances
	            if (! empty($this->searcher_authorities_instances) && count($this->searcher_authorities_instances) == 1) {
	                if (! empty($this->searcher_authorities_instances[0])) {
	                    $authority_type = $this->searcher_authorities_instances[0]->get_authority_type();
	                    if ($authority_type != 0) {
	                        return $this->get_sort_entity_type($authority_type);
	                    }
	                }
	            }
	            return "mixed";
	    }
	}
}