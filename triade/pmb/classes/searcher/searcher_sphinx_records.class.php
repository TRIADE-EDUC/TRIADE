<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_records.class.php,v 1.22.6.2 2024/11/12 12:49:30 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_records_list_ui.class.php');

class searcher_sphinx_records extends searcher_sphinx {
	protected $index_name = 'records';

	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/notices/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'records';
		$this->id_key = 'notice_id';
 	}


	protected function get_full_raw_query() {
	    if (!empty($this->fields_restrict)) {
	        $parts = explode('_', $this->fields_restrict[0]);
	        $code_champ = $parts[1];
	        $code_ss_champ = $parts[2];
	        return "SELECT DISTINCT id_notice AS id, 100 AS weight FROM notices_fields_global_index WHERE code_champ = $code_champ AND code_ss_champ = $code_ss_champ";
	    }
		return 'SELECT notice_id AS id, 100 AS weight FROM notices';
	}

	protected function _filter_results(){
		if($this->objects_ids!='') {
			$fr = new filter_results($this->objects_ids);
			$this->objects_ids = $fr->get_results();
			$query = 'delete from '.$this->get_tempo_tablename();
			if($this->objects_ids != ''){
				$query.=' where notice_id not in ('.$this->objects_ids.')' ;
			}
			pmb_mysql_query($query);
		}
	}

	public function get_full_query(){
		$this->get_result();
		$query =  'select notice_id, pert from '.$this->get_tempo_tablename();
		return $query;
	}
	public function get_nb_results(){
		$this->get_result();
		if (empty($this->objects_ids)) {
		    return 0;
		}
		return count(explode(',', $this->objects_ids));
	}

	public function get_sorted_result($tri = "default",$start=0,$number=20){
		$this->tri = $tri;
		$this->get_result();
		$sort = new sort("notices","session");
		//$query = $sort->appliquer_tri_from_tmp_table($this->tri,$this->get_tempo_tablename(),'notice_id',$start,$number);
		$query = $sort->appliquer_tri($this->tri, $this->get_raw_query(), 'notice_id', $start, $number);
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			$this->result=array();
			while($row = pmb_mysql_fetch_object($res)){
				$this->result[] = $row->notice_id;
			}
		}
		return $this->result;
	}

	public function explain($display,$mode,$mini=false){
		print '<div style="margin-left:10px;width:49%;overflow:hidden;float:left">';
		print '<h1>Recherche SPHINX</h1>';
		print '<p>QUERY : '.$this->sphinx_query.'</p>';
		$start = microtime(true);
 		print '<p>Nombre de resultats trouves: '.$this->get_nb_results().'</p>';
 		if(!$mini){
	 		$result = $this->get_sorted_result();
	 		if($this->get_nb_results()>0 && $result){
		 		$inter = microtime(true);
			 	print '<p>Temps de calcul (en seconde) : '.($inter - $start).'</p>';
			 	if ($display) {
			 		$elements_records_list_ui = new elements_records_list_ui($result, count($result), false);
			 		print $elements_records_list_ui->get_elements_list();
			 		print '<p>Temps de gen page (en seconde) : '.(microtime(true) - $inter).'</p>';
			 	}
	 		}
 		}
 		print '<p>Temps Total (en seconde) : '.(microtime(true) - $start).'</p></div>';
	}

	public function init_fields_restrict($mode) {
	    global $multi_crit_indexation_oeuvre_title;

		$this->fields_restrict = array();
		switch ($mode) {
			case 'title':
				$this->fields_restrict[] = 'f_001_00';
				$this->fields_restrict[] = 'f_002_00';
				$this->fields_restrict[] = 'f_003_00';
				$this->fields_restrict[] = 'f_004_00';
				$this->fields_restrict[] = 'f_006_00';
				$this->fields_restrict[] = 'f_023_01';
				if ($multi_crit_indexation_oeuvre_title == 1) {
					$this->fields_restrict[]= 'f_026_01';
				}
				break;
			case 'authors':
				$this->fields_restrict[] = 'f_027_01';
				$this->fields_restrict[] = 'f_027_02';
				$this->fields_restrict[] = 'f_027_03';
				$this->fields_restrict[] = 'f_027_04';
				$this->fields_restrict[] = 'f_028_01';
				$this->fields_restrict[] = 'f_028_02';
				$this->fields_restrict[] = 'f_028_03';
				$this->fields_restrict[] = 'f_028_04';
				$this->fields_restrict[] = 'f_029_01';
				$this->fields_restrict[] = 'f_029_02';
				$this->fields_restrict[] = 'f_029_03';
				$this->fields_restrict[] = 'f_029_04';
				$this->fields_restrict[] = 'f_127_01';
				$this->fields_restrict[] = 'f_127_02';
				$this->fields_restrict[] = 'f_127_03';
				$this->fields_restrict[] = 'f_127_04';
				$this->fields_restrict[] = 'f_128_01';
				$this->fields_restrict[] = 'f_128_02';
				$this->fields_restrict[] = 'f_128_03';
				$this->fields_restrict[] = 'f_128_04';
				break;
			case 'categories':
				$this->fields_restrict[] = 'f_025_01';
				break;
			case 'concepts':
				$this->fields_restrict[] = 'f_036_01';
				$this->fields_restrict[] = 'f_126_01';
				break;
			case 'map_equinoxe':
				$this->fields_restrict[] = 'f_041_00';
				break;
			case 'titres_uniformes':
			    $this->fields_restrict = $this->sphinx_base->get_datatype_indexes_from_mode($mode);
				break;
			default:
				global $pmb_search_exclude_fields;

				$indexes = $this->sphinx_base->getIndexes();
				$excludes = explode(',',$pmb_search_exclude_fields);
				$nb_excludes = count($excludes);
				for ($i = 0; $i < $nb_excludes; $i++) {
				    $field_partkey = 'f_' . str_pad($excludes[$i], 3, "0", STR_PAD_LEFT);
				    $nb_fields = count($indexes['records']['fields']);
				    for ($j = 0; $j < $nb_fields; $j++) {
				        if (strpos($indexes['records']['fields'][$j], $field_partkey) === 0) {
				            $this->fields_ignore[] = $indexes['records']['fields'][$j];
				        }
				    }
				}
				break;
		}
		$this->mode = $mode;
	}

	protected function get_filters() {
		$filters = parent::get_filters();
		global $statut_query, $typdoc_query, $date_parution_start_query, $date_parution_end_query, $date_parution_exact_query;

		if (!empty($typdoc_query)) {
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée!
			// par contre, on peut avoir un tableau avec une valeur vide...
		    if (!is_array($typdoc_query) || (is_array($typdoc_query) && $typdoc_query[0] !== '')) {
    			$filters[] = array(
    				'name'=> 'typdoc',
    				'values' => $typdoc_query
    			);
		    }
		}

		if (!empty($statut_query)) {
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée!
			// par contre, on peut avoir un tableau avec une valeur vide...
		    if (!is_array($statut_query) || (is_array($statut_query) && $statut_query[0] !== '')) {
		        $filters[] = array(
    				'name'=> 'statut',
    				'values' => $statut_query
    			);
		    }
		}

		if (!empty($date_parution_start_query)) {
		    $date_parution_start = detectFormatDate($date_parution_start_query);
		} elseif (!empty($date_parution_end_query)) {
		    $result = pmb_mysql_query("SELECT min(date_parution) FROM notices");
		    $date_parution_start = pmb_mysql_result($result, 0, 0);
		}

		if (!empty($date_parution_end_query)) {
		    $date_parution_end = detectFormatDate($date_parution_end_query);
		} elseif (!empty($date_parution_start_query)) {
		    $result = pmb_mysql_query("SELECT max(date_parution) FROM notices");
		    $date_parution_end = pmb_mysql_result($result, 0, 0);
		}

		if (!empty($date_parution_start_query) && $date_parution_exact_query) {
		    $dt = new DateTime($date_parution_start);
		    $val = $dt->format('U');

		    $val_min = $dt->format('U');
		    $val_max = $dt->format('U');
		    switch (true) {
		        case (strlen($date_parution_start_query) == 4):
		            // On a juste une année, on recherche donc entre le premier et dernier jour de l'année
		            $dt_min = new DateTime(date('Y-01-01', $val));
		            $val_min = $dt_min->format('U');
		            $dt_max = new DateTime(date('Y-12-31', $val));
		            $val_max = $dt_max->format('U');
		            break;
		        case (strlen($date_parution_start_query) == 7):
		            // On a un mois et une année, on recherche donc entre le premier et dernier jour du mois
		            $dt_min = new DateTime(date('Y-m-d', $val));
		            $val_min = $dt_min->format('U');
		            $dt_max = new DateTime(date('Y-m-t', $val));
		            $val_max = $dt_max->format('U');
		            break;
		        case (strlen($date_parution_start_query) == 10):
		            // On a une date complète, on recherche donc uniquement sur ce jour
		            $dt_min = new DateTime(date('Y-m-d 00:00:00', $val));
		            $val_min = $dt_min->format('U');
		            $dt_max = new DateTime(date('Y-m-d 23:59:59', $val));
		            $val_max = $dt_max->format('U');
		            break;
		    }

		    $filters[] = array(
		        'name' => 'date_parution',
		        'values' => [
		            'min' => $val_min,
		            'max' => $val_max
		        ],
		        'range' => true
		    );
		} elseif (!empty($date_parution_start)) {
	        $dt_min = new DateTime($date_parution_start);
	        $val_min = $dt_min->format('U');
	        $dt_max = new DateTime($date_parution_end);
	        $val_max = $dt_max->format('U');

		    $filters[] = array(
		        'name' => 'date_parution',
		        'values' => [
		            'min' => $val_min,
		            'max' => $val_max
		        ],
		        'range' => true
		    );
		}
		return $filters;
	}

	protected function _get_objects_ids() {
	    global $sphinx_indexes_prefix;

		if (isset($this->objects_ids)) {
			return $this->objects_ids;
		}

 		if ($this->mode != 'explnum') {
 		    global $docnum_query, $multi_crit_indexation_docnum_allfields;

	 		parent::_get_objects_ids();

	 		if (!empty($docnum_query) && $this->mode != 'all_fields') {
	 		    $docnum_query = '';
	 		}
	 		if ($multi_crit_indexation_docnum_allfields == 1) {
	 		    $docnum_query = 'on';
	 		}

	 		if ($this->sphinx_query == '*' || empty($docnum_query)) {
 		        return $this->objects_ids;
	 		}
 		} else {
 			$this->objects_ids = '';
 			// La table tempo n'a pas été créée par le parent
 			$this->_build_tmp_table();
 		}

		$already_found = explode(',', $this->objects_ids);
		$this->sc->SetGroupBy('num_record', SPH_GROUPBY_ATTR);
		$this->sc->SetSelect("id, num_record");
		$nb = 0;
		$matches = array();
		do {
			$this->sc->SetLimits($nb, $this->bypass);
			$result = $this->sc->Query($this->sphinx_query, $sphinx_indexes_prefix.'records_explnums');
			if((! is_array($result)) || (is_array($result) && ! array_key_exists('matches', $result))){
				return $this->objects_ids;
			}
			for($i = 0 ; $i<count($result['matches']) ; $i++){
				if (in_array($result['matches'][$i]['attrs']['num_record'], $already_found)) {
					continue;
				}
				if($this->objects_ids){
					$this->objects_ids.= ',';
				}
				$this->objects_ids.= $result['matches'][$i]['attrs']['num_record'];
				$matches[] = array(
						'id' => $result['matches'][$i]['attrs']['num_record'],
						'weight' => $result['matches'][$i]['weight']
				);
 				$this->nb_result++;
			}
			$nb+= count($result['matches']);
			$this->insert_in_tmp_table($matches);
		} while ($nb < $result['total_found']);
		return $this->objects_ids;
	}
}