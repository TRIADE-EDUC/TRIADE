<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_records.class.php,v 1.15.8.3 2024/11/12 12:49:30 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_sphinx_records extends searcher_sphinx {
	protected $index_name = 'records';

	protected $typdocs;

	protected $nb_explnum;

	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/notices/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'records';
		$this->id_key = 'notice_id';
 	}

	protected function get_full_raw_query(){
		return 'select notice_id as id, 100 as weight from notices';
	}

	protected function _filter_results(){
		if($this->objects_ids != '') {
			$fr = new filter_results($this->objects_ids);
			$this->objects_ids = $fr->get_results();
			$this->nb_result = count(explode(',', $this->objects_ids));
			if($this->objects_ids == '') {
				$query = 'truncate '.$this->get_tempo_tablename();
				pmb_mysql_query($query);
			}else{
				$query = 'delete from '.$this->get_tempo_tablename().' where notice_id not in ('.$this->objects_ids.')';
				pmb_mysql_query($query);
			}
			$this->_filter_result_by_custom_search();
		}
	}

	protected function _filter_result_by_custom_search() {
		global $opac_search_other_function;
		if (($this->objects_ids == '') || !$opac_search_other_function) {
			return false;
		}
		$custom_query = '';
		$custom_query = search_other_function_clause();
		if ($custom_query) {
			$query = 'delete from '.$this->get_tempo_tablename().' where notice_id not in ('.$custom_query.')';
			pmb_mysql_query($query);

			$this->objects_ids = '';
			$query = 'select notice_id from '.$this->get_tempo_tablename();
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_assoc($result)) {
					if ($this->objects_ids) {
						$this->objects_ids.= ',';
					}
					$this->objects_ids.= $row['notice_id'];
				}
			}
		}
		return true;
	}

	public function get_full_query(){
		$this->get_result();
		$query =  'select notice_id, pert from '.$this->get_tempo_tablename();
		return $query;
	}

	public function get_nb_results(){
		$this->get_result();
		if(!$this->objects_ids){
			return 0;
		}
		return count(explode(',',$this->objects_ids));
	}

	public function get_sorted_result($tri = "default",$start=0,$number=20){
		$this->tri = $tri;
		$this->get_result();

		$sort = new sort("notices","session");
		$query = $sort->appliquer_tri_from_tmp_table($this->tri,$this->get_tempo_tablename(),'notice_id',$start,$number);

		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			$this->result=array();
			while($row = pmb_mysql_fetch_object($res)){
				$this->result[] = $row->notice_id;
			}
		}
		return $this->result;
	}

	public function explain($display = "",$mode = "",$mini=false){
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
		$this->fields_ignore = array();
		$this->mode = $mode;
		$datatypes = $this->sphinx_base->get_datatypes();
		if (isset($datatypes[$mode])) {
		    $this->fields_restrict = $datatypes[$mode];
		    if ($mode == "title" && $multi_crit_indexation_oeuvre_title == 1) {
		        $this->fields_restrict[] = 'f_026_01';
		    }
		} else {
		    switch ($mode) {
		        case 'title':
		            $this->fields_restrict[] = 'f_001_00';
		            $this->fields_restrict[] = 'f_002_00';
		            $this->fields_restrict[] = 'f_003_00';
		            $this->fields_restrict[] = 'f_004_00';
		            $this->fields_restrict[] = 'f_006_00';
		            $this->fields_restrict[] = 'f_023_01';
		            if ($multi_crit_indexation_oeuvre_title == 1) {
		                $this->fields_restrict[] = 'f_026_01';
		            }
		            break;
    			case 'authors':
    			    if (isset($datatypes['author'])) {
    			        $this->fields_restrict = $datatypes['author'];
    			    } else {
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
    			    }
    				break;
    			case 'categories':
    				$this->fields_restrict[] = 'f_025_01';
    				break;
    			case 'concepts':
    			    if (isset($datatypes['concept'])) {
    			        $this->fields_restrict = $datatypes['concept'];
    			    } else {
    				    $this->fields_restrict[] = 'f_036_01';
    				    $this->fields_restrict[] = 'f_126_01';
    			    }
    				break;
    			case 'titres_uniformes':
    			case 'uniform_title':
    			case 'uniformtitle':
    			    if (isset($datatypes['titres_uniformes'])) {
    			        $this->fields_restrict = $datatypes['titres_uniformes'];
    			    } elseif (isset($datatypes['uniform_title'])) {
    			        $this->fields_restrict = $datatypes['uniform_title'];
    			    } elseif (isset($datatypes['uniformtitle'])) {
    			        $this->fields_restrict = $datatypes['uniformtitle'];
    			    } else {
        				$this->fields_restrict[] = 'f_026_01';
        				$this->fields_restrict[] = 'f_026_02';
        				$this->fields_restrict[] = 'f_026_03';
        				$this->fields_restrict[] = 'f_026_04';
        				$this->fields_restrict[] = 'f_026_05';
        				$this->fields_restrict[] = 'f_026_06';
        				$this->fields_restrict[] = 'f_026_07';
        				$this->fields_restrict[] = 'f_026_09';
        				$this->fields_restrict[] = 'f_026_10';
        				$this->fields_restrict[] = 'f_026_11';
        				$this->fields_restrict[] = 'f_026_12';
        				$this->fields_restrict[] = 'f_026_13';
        				$this->fields_restrict[] = 'f_026_14';
        				$this->fields_restrict[] = 'f_026_15';
        				$this->fields_restrict[] = 'f_026_16';
        				$this->fields_restrict[] = 'f_026_17';
        				$this->fields_restrict[] = 'f_026_18';
        				$this->fields_restrict[] = 'f_026_19';
        				$this->fields_restrict[] = 'f_026_20';
        				$this->fields_restrict[] = 'f_026_21';
        				$this->fields_restrict[] = 'f_026_22';
        				$this->fields_restrict[] = 'f_026_23';
        				$this->fields_restrict[] = 'f_123_01';
        				$this->fields_restrict[] = 'f_124_01';
        				$this->fields_restrict[] = 'f_125_01';
        				$this->fields_restrict[] = 'f_126_01';
        				$this->fields_restrict[] = 'f_127_01';
        				$this->fields_restrict[] = 'f_127_02';
        				$this->fields_restrict[] = 'f_127_03';
        				$this->fields_restrict[] = 'f_127_04';
        				$this->fields_restrict[] = 'f_128_01';
        				$this->fields_restrict[] = 'f_128_02';
        				$this->fields_restrict[] = 'f_128_03';
        				$this->fields_restrict[] = 'f_128_04';
    			    }
    				break;
    			case 'general_note':
    			    if (isset($datatypes['general_note'])) {
    			        $this->fields_restrict = $datatypes['general_note'];
    			    } else {
                        $this->fields_restrict[] = 'f_012_00';
    			    }
    			    break;
    			case 'contents_note':
    			    if (isset($datatypes['contents_note'])) {
        			    $this->fields_restrict = $datatypes['contents_note'];
        			} else {
        			    $this->fields_restrict[] = 'f_013_00';
        			}
			        break;
    			case 'abstract':
    			    if (isset($datatypes['abstract'])) {
        			    $this->fields_restrict = $datatypes['abstract'];
        			} else {
        			    $this->fields_restrict[] = 'f_014_00';
        			}
    			    break;
    			case 'notes':
    			    if (isset($datatypes['notes'])) {
        			    $this->fields_restrict = $datatypes['notes'];
        			} else {
        			    $this->fields_restrict[] = 'f_012_00';
        			    $this->fields_restrict[] = 'f_013_00';
        			    $this->fields_restrict[] = 'f_014_00';
        			}
    			case 'publishers':
    			    if (isset($datatypes['publisher'])) {
    			        $this->fields_restrict = $datatypes['publisher'];
    			    } else {
    			        $this->fields_restrict[] = 'f_017_01';
    			        $this->fields_restrict[] = 'f_017_02';
    			        $this->fields_restrict[] = 'f_017_03';
    			        $this->fields_restrict[] = 'f_017_04';
    			        $this->fields_restrict[] = 'f_017_05';
    			        $this->fields_restrict[] = 'f_017_06';
    			        $this->fields_restrict[] = 'f_017_07';
    			        $this->fields_restrict[] = 'f_017_08';
    			    }
    			    break;
    			case 'keywords':
    			    $this->fields_restrict[] = 'f_017_00';
    			    break;
    			case '':
    			case 'all_fields':
    			    global $opac_exclude_fields;

        			$indexes = $this->sphinx_base->getIndexes();
        			$excludes = explode(',', $opac_exclude_fields);
        			$nb_excludes = count($excludes);
        			for ($i = 0; $i < $nb_excludes; $i++) {
        			    $field_partkey = 'f_' . str_pad($excludes[$i], 3, "0", STR_PAD_LEFT);
        			    $nb_fields = count($indexes['records']['fields']);
        			    for ($j = 0; $j < $nb_fields; $j++) {
        			        if (strpos($indexes['records']['fields'][$j],$field_partkey) === 0) {
        			            $this->fields_ignore[] = $indexes['records']['fields'][$j];
        			        }
        			    }
        			}
    				break;
    			default :
    				break;
    		}
		}
	}

	protected function get_filters() {
	    $filters = parent::get_filters();
	    global $typdoc;

	    if (!empty($typdoc)) {
	        // on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée!
	        // par contre, on peut avoir un tableau avec une valeur vide...
	        if (!is_array($typdoc) || (is_array($typdoc) && $typdoc[0] !== '')) {
	            $filters[] = array(
	                'name'=> 'typdoc',
	                'values' => $typdoc
	            );
	        }
	    }
	    return $filters;
	}

	public function get_typdocs(){
		global $dbh;
		if(!$this->typdocs){
			if(!$this->objects_ids){
				$this->get_result();
			}
			$this->typdocs = array();
			if($this->objects_ids != ""){
				$this->typdocs = searcher::get_typdocs_from_notices_ids($this->objects_ids);
			}
		}
		return $this->typdocs;
	}

	public function get_nb_explnums($limit_one = 1){
		if(!$this->objects_ids){
			$this->get_result();
		}
		$this->nb_explnum = 0;
		if($this->objects_ids != ""){
			$this->nb_explnum = searcher::get_nb_explnums_from_notices_ids($this->objects_ids, $limit_one);
		}
		return $this->nb_explnum;
	}

	protected function _get_objects_ids() {
	    global $sphinx_indexes_prefix;

		if (isset($this->objects_ids)) {
			return $this->objects_ids;
		}
		global $multi_crit_indexation_docnum_allfields, $opac_indexation_docnum_allfields, $dont_check_opac_indexation_docnum_allfields;

		if ($this->mode != 'explnum') {
    		$with_explnum = false;
    		if ($multi_crit_indexation_docnum_allfields) {//On est dans le cas de la recherche mutli-critères
    		    if ($multi_crit_indexation_docnum_allfields > 0) {
    				$with_explnum = true;
    			}
    		}elseif(($opac_indexation_docnum_allfields && !$dont_check_opac_indexation_docnum_allfields)){
    			$with_explnum = true;
    		}
    		parent::_get_objects_ids();
    		if (($this->sphinx_query == '*') || !$with_explnum) {
    			return $this->objects_ids;
    		}
		}else {
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