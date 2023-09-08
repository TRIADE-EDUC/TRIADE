<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_records.class.php,v 1.13 2019-05-27 12:55:59 arenou Exp $

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
		
	
	protected function get_full_raw_query(){
		return 'select notice_id as id, 100 as weight from notices';
	}
	
	protected function _filter_results(){
		if($this->objects_ids!='') {
			$fr = new filter_results($this->objects_ids);
			$this->objects_ids = $fr->get_results();
			$query = 'delete from '.$this->get_tempo_tablename();
			if($this->objects_ids != ''){
				$query.=' where notice_id not in ('.$this->objects_ids.')' ;
			}
			pmb_mysql_query($query) or die(mysql_error());
		}
	}
	
	public function get_full_query(){
		$this->get_result();
		$query =  'select notice_id, pert from '.$this->get_tempo_tablename();
		return $query;
	}
	public function get_nb_results(){
		$this->get_result();
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
	
	public function init_fields_restrict($mode){
		global $mutli_crit_indexation_oeuvre_title;
		$this->fields_restrict = array();
		switch($mode){
			case 'title' :
				$this->fields_restrict[] = 'f_001_00';
				$this->fields_restrict[] = 'f_002_00';
				$this->fields_restrict[] = 'f_003_00';
				$this->fields_restrict[] = 'f_004_00';
				$this->fields_restrict[] = 'f_006_00';
				$this->fields_restrict[] = 'f_023_01';
				if($mutli_crit_indexation_oeuvre_title){
					$this->fields_restrict[]= 'f_026_01';
				}
				break;
			case 'authors' :
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
			case 'categories' :
				$this->fields_restrict[] = 'f_025_01';
				break;	
			case 'concepts' :
				$this->fields_restrict[] = 'f_036_01';
				$this->fields_restrict[] = 'f_126_01';
				break;
			case 'map_equinoxe' :
				$this->fields_restrict[] = 'f_041_00';
				break;
			case 'titres_uniformes' :
			    $this->fields_restrict = $this->sphinx_base->get_datatype_indexes_from_mode($mode);
				break;
			default : 
				global $pmb_search_exclude_fields;
				$indexes = $this->sphinx_base->getIndexes();
				$excludes = explode(',',$pmb_search_exclude_fields);
				for($i=0 ; $i<count($excludes) ; $i++){ 
				    $field_partkey = 'f_'.str_pad($excludes[$i], 3, "0", STR_PAD_LEFT);
				    for($j=0 ; $j<count($indexes['records']['fields']) ; $j++){
				        if(strpos($indexes['records']['fields'][$j],$field_partkey) === 0) {
				            $this->fields_ignore[] = $indexes['records']['fields'][$j];
				        }
				    }
				}
				break;
		}
		$this->mode = $mode;
	}
	
	protected function get_filters(){
		$filters = parent::get_filters();
		global $statut_query,$statut_query;
		if(!empty($typdoc_query)){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée! 
			// par contre, on peut avoir un tableau avec une valeur vide...
		    if(!is_array($typdoc_query) || (is_array($typdoc_query) && $typdoc_query[0] !== '')){
    			$filters[] = array(
    				'name'=> 'typdoc',
    				'values' => $typdoc_query
    			);
		    }
		}
		if(!empty($statut_query)){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée! 
			// par contre, on peut avoir un tableau avec une valeur vide...
		    if(!is_array($statut_query) || (is_array($statut_query) && $statut_query[0] !== '')){
		        $filters[] = array(
    				'name'=> 'statut',
    				'values' => $statut_query
    			);
		    }
		}
		return $filters;
	}
	
	protected function _get_objects_ids() {
	    global $sphinx_indexes_prefix;
	    
		if (isset($this->objects_ids)) {
			return $this->objects_ids;
		}
		
 		if ($this->mode != 'explnum') {
 			global $docnum_query, $mutli_crit_indexation_docnum_allfields;
 			
	 		parent::_get_objects_ids();
			if (($this->sphinx_query == '*') || !($docnum_query || ($mutli_crit_indexation_docnum_allfields*1 > 0))) {
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