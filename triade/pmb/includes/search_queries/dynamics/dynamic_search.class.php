<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dynamic_search.class.php,v 1.5 2021/08/17 10:11:27 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class dynamic_search {
	public $id;
	public $xml_prefix;
	public $prefix;
	public $n_ligne;
	public $params;
	public $search;

	//Constructeur
    public function __construct($id,$xml_prefix,$n_ligne,$params,&$search) {
    	$this->id=$id;
    	$this->xml_prefix = $xml_prefix;
    	switch ($xml_prefix) {
    		case 'd' :
    			$this->prefix = 'notices';
    			break;
    		case 'e' :
    			$this->prefix = 'expl';
    			break;
    		case 'en' :
    			$this->prefix = 'explnum';
    			break;
    		case 'a' :
    			$this->prefix = 'authperso';
    			break;
    		default :
    			$this->prefix = $xml_prefix;
    			break;
    	}
    	$this->n_ligne=$n_ligne;
    	$this->params=$params;
    	$this->search=&$search;
    }
    
    protected function get_join_query() {
    	$join_query = "";
    	switch ($this->search->tableName) {
    		case 'authorities' :
    			switch($this->prefix) {
    				case 'author' :
    					$join_query .= " join authors on authors.author_id=authorities.num_object and authorities.type_object = 1 join author_custom_values on author_custom_origine=author_id ";
    					break;
    				case 'categ' :
    					$join_query .= " join noeuds on noeuds.id_noeud=authorities.num_object and authorities.type_object = 2 join categ_custom_values on categ_custom_origine=id_noeud ";
    					break;
    				case 'publisher' :
    					$join_query .= " join publishers on publishers.ed_id=authorities.num_object and authorities.type_object = 3 join publisher_custom_values on publisher_custom_origine=ed_id ";
    					break;
    				case 'collection' :
    					$join_query .= " join collections on collections.collection_id=authorities.num_object and authorities.type_object = 4 join collection_custom_values on collection_custom_origine=collection_id ";
    					break;
    				case 'subcollection' :
    					$join_query .= " join sub_collections on sub_collections.sub_coll_id=authorities.num_object and authorities.type_object = 5 join subcollection_custom_values on subcollection_custom_origine=sub_coll_id ";
    					break;
    				case 'serie' :
    					$join_query .= " join series on series.serie_id=authorities.num_object and authorities.type_object = 6 join serie_custom_values on serie_custom_origine=serie_id ";
    					break;
    				case 'tu' :
    					$join_query .= " join titres_uniformes on titres_uniformes.tu_id=authorities.num_object and authorities.type_object = 7 join tu_custom_values on tu_custom_origine=tu_id ";
    					break;
    				case 'indexint' :
    					$join_query .= " join indexint on indexint.indexint_id=authorities.num_object and authorities.type_object = 8 join indexint_custom_values on indexint_custom_origine=indexint_id ";
    					break;
    				case 'authperso' :
    					$join_query .= " join authperso_custom_values on authperso_custom_values.authperso_custom_origine=authorities.num_object and authorities.type_object = 9 ";
    					break;
    			}
    			break;
    		case 'notices' :
    			switch($this->prefix) {
    				case 'author' :
    					$join_query .= " join responsability on responsability.responsability_notice=notices.notice_id join author_custom_values on author_custom_origine=responsability.responsability_author ";
    					break;
    				case 'categ' :
    					$join_query .= " join notices_categories on notices_categories.notcateg_notice=notices.notice_id join categ_custom_values on categ_custom_origine=notices_categories.num_noeud ";
    					break;
    				case 'publisher' :
    					$join_query .= " join publisher_custom_values on publisher_custom_origine=notices.ed1_id ";
    					break;
    				case 'collection' :
    					$join_query .= " join collection_custom_values on collection_custom_origine=notices.coll_id ";
    					break;
    				case 'subcollection' :
    					$join_query .= " join subcollection_custom_values on subcollection_custom_origine=notices.subcoll_id ";
    					break;
    				case 'serie' :
    					$join_query .= " join serie_custom_values on serie_custom_origine=notices.tparent_id ";
    					break;
    				case 'tu' :
    					$join_query .= " join notices_titres_uniformes on notices_titres_uniformes.ntu_num_notice = notices.notice_id join titres_uniformes on titres_uniformes.tu_id = notices_titres_uniformes.ntu_num_tu join tu_custom_values on tu_custom_origine=tu_id ";
    					break;
    				case 'indexint' :
    					$join_query .= " join indexint_custom_values on indexint_custom_origine=notices.indexint ";
    					break;
    				case 'authperso' :
    					$join_query .= " join notices_authperso on notices_authperso.notice_authperso_notice_num=notices.notice_id join authperso_custom_values on authperso_custom_values.authperso_custom_origine=notices_authperso.notice_authperso_authority_num ";
    					break;
    			}
    			break;
    	}
    	return $join_query;
    }
    
    protected function get_restrict_query_with_operator($field, $operator) {
    	
    	$restrict_query =  "";
    	switch ($operator) {
    		case 'CONTAINS_AT_LEAST' :
    			$restrict_query .= $field." like '%!!p!!%'";
    			break;
    		case 'CONTAINS_ALL' :
    			$restrict_query .= $field." like '%!!p!!%'";
    			break;
    		case 'STARTWITH' :
    			$restrict_query .= $field." like '!!p!!%'";
    			break;
    		case 'ENDWITH' :
    			$restrict_query .= $field." like '%!!p!!'";
    			break;
    		case 'EXACT' :
    			$restrict_query .= $field." like '!!p!!'";
    			break;
    		case 'EQ' :
    			$restrict_query .= $field." = '%!!p!!'";
    			break;
    		case 'LT' :
    			$restrict_query .= $field." < '%!!p!!'";
    			break;
    		case 'GT' :
    			$restrict_query .= $field." > '%!!p!!'";
    			break;
    		case 'LTEQ' :
    			$restrict_query .= $field." <= '%!!p!!'";
    			break;
    		case 'GTEQ' :
    			$restrict_query .= $field." >= '%!!p!!'";
    			break;
    		case 'ISEMPTY' :
    			$restrict_query .= $field." = '' or ".$field." is null";
    			break;
    		case 'ISNOTEMPTY' :
    			$restrict_query .= $field." != ''";
    			break;
    	}
    	return $restrict_query;
    }
    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query($field = array()) {
    	return "";    
    }
    
    public function make_unimarc_query($field = array()) {
    	return "";
    }
    
    public function get_query($field = array()) {
    	return "";
    }
}