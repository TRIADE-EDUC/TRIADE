<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_caddie_content_ui.class.php,v 1.3.6.2.2.1 2025/03/04 16:38:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_authorities_caddie_content_ui extends list_caddie_content_root_ui {
	
	protected function _get_query_caddie_content() {
		$query = "SELECT authorities_caddie_content.object_id FROM authorities_caddie_content";
		$query .= $this->_get_query_filters_caddie_content();
		$query .= " AND caddie_id='".static::$id_caddie."'";
		return $query;
	}
	
	protected function fetch_data() {
	    if (static::$object_type == 'CONCEPTS') {
    	    $this->objects = array();
	        $query = $this->_get_query_caddie_content();
    	    $result = pmb_mysql_query($query);
    	    if (pmb_mysql_num_rows($result)) {
    	        while($row = pmb_mysql_fetch_object($result)) {
	                $aut = new authority($row->object_id);
    	            $this->add_object($aut->get_object_instance());
    	        }
    	        if($this->applied_sort_type != "SQL"){
    	            $this->pager['nb_results'] = pmb_mysql_num_rows($result);
    	        }
    	    }
    	    $this->messages = "";
	    } else {
	        parent::fetch_data();
	    }
	}
	
	protected function _get_query_base() {
		switch (static::$object_type) {
			case 'AUTHORS':
				$query = "SELECT authorities.id_authority AS id, authors.* 
				          FROM authors 
                          JOIN authorities ON authorities.num_object = authors.author_id 
                          AND authorities.type_object = " . AUT_TABLE_AUTHORS;
				break;
			case 'CATEGORIES':
				$query = "SELECT authorities.id_authority AS id, categories.* 
                          FROM categories 
                          JOIN authorities ON authorities.num_object = categories.num_noeud 
                          AND authorities.type_object = " . AUT_TABLE_CATEG;
				break;
			case 'PUBLISHERS':
				$query = "SELECT authorities.id_authority AS id, publishers.* 
                          FROM publishers 
                          JOIN authorities ON authorities.num_object = publishers.ed_id 
                          AND authorities.type_object = " . AUT_TABLE_PUBLISHERS;
				break;
			case 'COLLECTIONS':
				$query = "SELECT authorities.id_authority AS id, collections.* 
                          FROM collections 
                          JOIN authorities ON authorities.num_object = collections.collection_id 
                          AND authorities.type_object = " . AUT_TABLE_COLLECTIONS;
				break;
			case 'SUBCOLLECTIONS':
				$query = "SELECT authorities.id_authority AS id, sub_collections.* 
                          FROM sub_collections 
                          JOIN authorities ON authorities.num_object = sub_collections.sub_coll_id 
                          AND authorities.type_object = " . AUT_TABLE_SUB_COLLECTIONS;
				break;
			case 'SERIES':
				$query = "SELECT authorities.id_authority AS id, series.* 
                          FROM series 
                          JOIN authorities ON authorities.num_object = series.serie_id 
                          AND authorities.type_object = " . AUT_TABLE_SERIES;
				break;
			case 'TITRES_UNIFORMES':
				$query = "SELECT authorities.id_authority AS id, titres_uniformes.* 
				          FROM titres_uniformes 
			              JOIN authorities ON authorities.num_object = titres_uniformes.tu_id 
				          AND authorities.type_object = " . AUT_TABLE_TITRES_UNIFORMES;
				break;
			case 'INDEXINT':
				$query = "SELECT authorities.id_authority AS id, indexint.*
				          FROM indexint
			              JOIN authorities ON authorities.num_object = indexint.indexint_id
				          AND authorities.type_object = " . AUT_TABLE_INDEXINT;
				break;
			case 'AUTHPERSO':
			    $query = "SELECT authorities.id_authority AS id, authperso.authperso_name, authperso_authorities.* 
			              FROM authperso_authorities 
		                  JOIN authperso ON authperso.id_authperso = authperso_authorities.authperso_authority_authperso_num 
			              JOIN authorities ON authorities.num_object = authperso_authorities.id_authperso_authority 
			              AND authorities.type_object = " . AUT_TABLE_AUTHPERSO;
			    break;
			case 'MIXED':
			    $query = "SELECT authorities.id_authority AS id, authorities_statuts.authorities_statut_label, authorities.* 
			              FROM authorities
			              JOIN authorities_statuts ON authorities_statuts.id_authorities_statut = authorities.num_statut";
			    break;
			default:
			    $query = "";
			    break;
		}
		if ($query) {
			$query .= " where authorities.id_authority IN (".$this->_get_query_caddie_content().")";
		}
		return $query;
	}
	
	protected function get_exclude_fields() {
		switch (static::$object_type) {
			case 'AUTHORS':
				return array(
					'author_see',
					'index_author',
					'author_import_denied'
				);
				break;
			case 'CATEGORIES':
				return array(
					'index_categorie',
					'index_path_word_categ'
				);
				break;
			case 'PUBLISHERS':
				return array(
					'index_publisher',
					'ed_num_entite',
				);
				break;
			case 'COLLECTIONS':
				return array(
					'index_coll',
				);
				break;
			case 'SUBCOLLECTIONS':
				return array(
					'index_sub_coll',
				);
				break;
			case 'SERIES':
				return array(
					'serie_index',
				);
				break;
			case 'TITRES_UNIFORMES':
				return array(
					'index_tu',
					'tu_import_denied',
				);
				break;
			case 'INDEXINT':
				return array(
					'index_indexint',
					'num_pclass',
				);
				break;
			case 'AUTHPERSO':
				break;
			case 'MIXED':
				break;
			case 'CONCEPTS':
				break;
			default:
				break;
		}
		return parent::get_exclude_fields();
	}
	
	protected function get_main_fields() {
	    global $msg;
		switch (static::$object_type) {
		    case 'AUTHPERSO':
		        $type_object = 'authperso_authorities';
		        break;
			case 'AUTHORS':
			    return array_merge(
					$this->get_describe_fields('authors', 'authors', 'authors'),
			    	array('author_concepts' => $this->get_describe_field($msg['list_author_concept'], 'titres_uniformes', 'aut')),
			    	array('aut_link' => $msg['aut_link'])
				);
				break;
			case 'SUBCOLLECTIONS':
				return array_merge(
					$this->get_describe_fields('sub_collections', 'sub_collections', 'sub_collections'),
					array('aut_link' => $msg['aut_link'])
				);
				break;
			case 'TITRES_UNIFORMES':
			    return array_merge(
				    $this->get_describe_fields('titres_uniformes', 'titres_uniformes', 'titres_uniformes'),
				    array('tu_performers' => $this->get_describe_field($msg['list_titre_uniforme_interpreter_function'], 'titres_uniformes', 'titres_uniformes')),
				    array('tu_authors' => $this->get_describe_field($msg['list_titre_uniforme_author_function'], 'titres_uniformes', 'titres_uniformes')),
				    array('tu_concepts' => $this->get_describe_field($msg['list_titre_uniforme_concept'], 'titres_uniformes', 'titres_uniformes')),
				    array('aut_link' => $msg['aut_link']),
				    array('notices_titres_uniformes_ids' => $msg['notice_relations']." (Ids)")
			    );
			    break;
			case 'MIXED':
			    return array();
			case 'CONCEPTS':
			    $props = skos_concept::get_properties();
			    return $props;		
			default:
			    $type_object = static::$object_type;
				break;
		}
		return array_merge(
		    $this->get_describe_fields(strtolower($type_object), strtolower($type_object), strtolower($type_object)),
			array('aut_link' => $msg['aut_link'])
	    );
	}
	
	protected function get_describe_field($fieldname, $datasource_name, $prefix) {
		global $msg;
		
		if(isset($this->get_editions_datasource($datasource_name)->struct_format[$prefix.'_'.$fieldname])) {
			return $this->get_editions_datasource($datasource_name)->struct_format[$prefix.'_'.$fieldname]['label'];
		}
		$field_from_rmc = '';
		switch (static::$object_type) {
			case 'PUBLISHERS':
				$field_from_rmc = str_replace('ed', 'publisher', $fieldname);
				break;
			case 'SUBCOLLECTIONS':
				$field_from_rmc = str_replace('sub_coll', 'subcollection', $fieldname);
				break;
			case 'SERIES':
				if($fieldname == 'serie_name') {
					$fieldname = 'serie_label';
				}
				break;
			case 'TITRES_UNIFORMES':
				$field_from_rmc = str_replace('tu', 'titre_uniforme', $fieldname);
		    	break;
			case 'INDEXINT':
				if($fieldname == 'indexint_name') {
					$fieldname = 'indexint_label';
				}
				break;
		    default:
		    	
		    	break;
		}if($field_from_rmc && isset($msg['search_extended_'.$field_from_rmc])) {
			return $msg['search_extended_'.$field_from_rmc];
		} elseif(isset($msg['search_extended_'.$fieldname])) {
			return $msg['search_extended_'.$fieldname];
		} elseif(substr($fieldname, strlen($fieldname)-2) == 'id') {
			return $msg['1601'];
		}else {
			return $fieldname;
		}
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		switch (static::$object_type) {
			case 'AUTHORS':
				$this->add_custom_fields_available_columns('author', 'author_id');
				break;
			case 'CATEGORIES':
				$this->add_custom_fields_available_columns('categ', 'num_noeud');
				break;
			case 'PUBLISHERS':
			    $this->add_custom_fields_available_columns('publisher', 'ed_id');
			    break;
			case 'COLLECTIONS':
			    $this->add_custom_fields_available_columns('collection', 'collection_id');
			    break;
			case 'SUBCOLLECTIONS':
			    $this->add_custom_fields_available_columns('subcollection', 'sub_coll_id');
			    break;
			case 'SERIES':
			    $this->add_custom_fields_available_columns('serie', 'serie_id');
			    break;
			case 'TITRES_UNIFORMES':
			    $this->add_custom_fields_available_columns('tu', 'tu_id');
			    break;
			case 'INDEXINT':
				$this->add_custom_fields_available_columns('indexint', 'indexint_id');
				break;
			case 'AUTHPERSO':
			    $this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_authperso_available_columns());
			    $this->add_custom_fields_available_columns('authperso', 'id_authperso_authority');
			    break;
			case 'MIXED':
			    $this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_mixed_available_columns());
			    break;
			case 'CONCEPTS':
			    $this->add_custom_fields_available_columns('skos', 'id');
			    break;
			default:
			    break;
		}
		$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_creator_available_columns());
	}
	
	protected function get_exclude_default_columns() {
	    return array(
	        'creator',
	        'notices_titres_uniformes_ids'
	    );
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		switch (static::$object_type) {
			case 'AUTHORS':
				$sort_by = 'author_name';
				break;
			case 'CATEGORIES':
				$sort_by = 'libelle_categorie';
				break;
			case 'PUBLISHERS':
				$sort_by = 'ed_name';
				break;
			case 'COLLECTIONS':
				$sort_by = 'collection_name';
				break;
			case 'SUBCOLLECTIONS':
				$sort_by = 'sub_coll_name';
				break;
			case 'SERIES':
				$sort_by = 'serie_name';
				break;
			case 'TITRES_UNIFORMES':
				$sort_by = 'tu_name';
				break;
			case 'INDEXINT':
				$sort_by = 'indexint_name';
				break;
			case 'AUTHPERSO':
			    $sort_by = 'id_authperso_authority';
			    break;
			case 'MIXED':
			    $sort_by = 'id_authority';
			    break;
			case 'CONCEPTS':
			    $sort_by='id';
			    break;
			default:
			    $sort_by = '';
			    break;
		}
		$this->add_applied_sort($sort_by);
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/autorites.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&object_type='.static::$object_type.'&idcaddie='.static::$id_caddie.'&item=0';
	}
	
	protected function _get_query_order() {
	    if (static::$object_type == 'CONCEPTS'){
	        $this->applied_sort_type = 'OBJECTS';
	        return '';
	    }
	    if ($this->applied_sort[0]['by']) {
	        $sort_by = $this->applied_sort[0]['by'];
	        switch($sort_by) {
	            case 'isbd_authority':
	            case 'tu_authors':
	            case 'tu_performers':
	            case 'tu_concepts':
	            case 'author_concepts':
	            case 'notices_titres_uniformes_ids':
	                $this->applied_sort_type = 'OBJECTS';
	                return '';
	            default :
	                return parent::_get_query_order();
	        }
	    }
	}
	
	protected function add_authperso_available_columns() {
	    return array(
	        'authperso_name' => 'search_by_authperso_title'
	    );
	}
	
	protected function add_mixed_available_columns() {
	    return array(
	        'id_authority' => 'cms_authority_format_data_id',
	        'num_object' => 'cms_authority_format_data_db_id',
	        'type_object' => 'include_option_type_donnees',
	        'isbd_authority' => 'cms_authority_format_data_isbd',
	        'authorities_statut_label' => 'search_extended_common_statut',
	        'thumbnail_url' => 'explnum_vignette',
	    );
	}
	
	protected function add_creator_available_columns() {
	    global $msg;
	    
	    $code = '';
	    switch (static::$object_type) {
	        case 'AUTHORS':
	            $code = 'search_author_creator';
	            break;
	        case 'CATEGORIES':
	            $code = 'search_category_creator';
	            break;
	        case 'PUBLISHERS':
	            $code = 'search_publisher_creator';
	            break;
	        case 'COLLECTIONS':
	            $code = 'search_coll_creator';
	            break;
	        case 'SUBCOLLECTIONS':
	            $code = 'search_subcoll_creator';
	            break;
	        case 'SERIES':
	            $code = 'search_serie_creator';
	            break;
	        case 'TITRES_UNIFORMES':
	            $code = 'search_tu_creator';
	            break;
	        case 'INDEXINT':
	            $code = 'search_indexint_creator';
	            break;
	        case 'AUTHPERSO':
	            break;
	        case 'MIXED':
	            break;
	        case 'CONCEPTS':
	            break;
	            break;
	    }
	    if (!empty($code)) {
	        return array('creator' => $msg[$code]);
	    }
	    return array();
	}
	
	protected function _get_object_property_aut_link($object) {
		$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, $object->id);
		$aut_link = $authority->get_aut_link();
		if(!empty($aut_link) && is_object($aut_link)) {
			return $authority->get_aut_link()->get_display();
		}
		return '';
	}
	
	protected function _get_object_property_creator($object) {
	    $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, $object->id);
	    $query = "SELECT CONCAT(nom, ' ', prenom,' (',username,')') AS creator_name
            FROM users 
            JOIN audit ON audit.user_id = users.userid
            WHERE audit.object_id = ".$authority->get_num_object()." AND audit.type_obj=".$authority->get_audit_type()." AND audit.type_modif=1 ";
	    $result = pmb_mysql_query($query);
	    return pmb_mysql_result($result, 0, 'creator_name');
	}
	
	protected function get_cell_content($object, $property) {
	    $content = '';
	    switch($property) {
	        case 'type_object':
	            return authority::get_type_label_from_type_id($object->{$property});
	        case 'isbd_authority':
	            $authority = new authority($object->id_authority);
	            return $authority->get_isbd();
	        case 'aut_link':
	            return $this->_get_object_property_aut_link($object); //Interpretation du HTML
	    }
	    switch (static::$object_type){
	        case 'CONCEPTS':
	            $content = $object->{$property};
	            if($content !== null){
	                if(is_array($content)){
	                    $content = implode("<br>",$content);
	                }
	                return $content;
	            }
	            break;
	        case 'AUTHORS':
	            $authors = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $object->author_id);
	            switch ($property){
	                case 'author_concepts':
	                    $datas = $authors->get_concepts();
	                    return $this->get_format_cell_content($datas, 'CONCEPTS');
	            }
                break;
	        case 'TITRES_UNIFORMES':
	            $content = '';
	            switch ($property){
	                case 'tu_authors':
	                    $titres_uniformes = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $object->tu_id);
	                    $responsabilities = $titres_uniformes->get_sorted_responsabilities();
	                    $datas = $responsabilities['authors'];
	                    return $this->get_format_cell_content($datas);
	                case 'tu_performers':
	                    $titres_uniformes = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $object->tu_id);
	                    $responsabilities = $titres_uniformes->get_sorted_responsabilities();
	                    $datas = $responsabilities['performers'];
	                    return $this->get_format_cell_content($datas);
	                case 'tu_concepts':
	                    $titres_uniformes = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $object->tu_id);
	                    $datas = $titres_uniformes->get_concepts();
	                    return $this->get_format_cell_content($datas, 'CONCEPTS');
	                case 'notices_titres_uniformes_ids':
	                    $titres_uniformes = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $object->tu_id);
	                    return implode(' / ', $titres_uniformes->get_linked_records_id());
	            }
	            
	    }
	    return parent::get_cell_content($object, $property);
	}
	
	protected function get_format_cell_content($datas, $type=NULL){
	    $content = '';
	    if ($type === 'CONCEPTS') {
	        foreach ($datas as $data){
	            if ($content != '') {
	                $content .= "<br><br>";
	            }
	            $content .= $data->get_display_label();
	        }
	        return $content;
	    }
	    //Si on est encore c'est que l'on a des auteurs/interpretes
	    foreach ($datas as $data){
	        $content_name = '';
	        $content_function = '';
	        if ($content != '') {
	            $content_name .= "<br><br>";
	        }
	        $content_name .= $data['objet']->display;
	        for ($i = 0; $i < count($data['attributes']); $i++) {
	            if ($content_function != '') {
	                $content_function .= ", ";
	            }
	            $content_function .= $data['attributes'][$i]['fonction_label'];
	        }
	        $content .= $content_name." (".$content_function.")";
	    }
	    return $content;
	}
}