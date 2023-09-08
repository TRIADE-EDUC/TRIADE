<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_searcher.class.php,v 1.1 2015-01-13 10:11:25 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/searcher/searcher_generic.class.php");

class cms_editorial_searcher extends searcher_generic {


		function __construct($user_query,$type_obj='article'){
                parent::__construct($user_query);
				$this->type_obj = $type_obj;
				$this->field_restrict[] = array(
					'field' => 'type',
					'values' => $this->type_obj,
					'op' => "and",
					'not' => false,
				);
		        $this->object_table = 'cms_'.$this->type_obj.'s';
                $this->object_key = 'id_'.$this->type_obj;
                $this->object_index_key= "num_obj";
                $this->object_words_table = "cms_editorial_words_global_index";
                $this->object_fields_table = "cms_editorial_fields_global_index";
        }

        protected function _get_search_type(){
                return "editorial_all_fields";
        }

        protected function _get_search_query(){
        	$this->_calc_query_env();
        	if($this->user_query !== "*"){
        		$query = $this->aq->get_query_mot($this->object_index_key,$this->object_words_table,$this->object_words_value,$this->object_fields_table,$this->object_fields_value,$this->field_restrict);
        	}else{
        		$query =" select ".$this->object_key." from cms_".$this->object_table;
        	}
        	return $query;
        }
        
        
}