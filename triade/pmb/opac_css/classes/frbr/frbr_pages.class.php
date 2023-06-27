<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_pages.class.php,v 1.2 2017-05-24 15:24:48 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// require_once($class_path."/frbr/frbr_entities_parser.class.php");

class frbr_pages {
	
	protected $entity;
	
	/**
	 * Liste des pages
	 */
	protected $pages;
	
	/**
	 * Constructeur
	 */
	public function __construct($entity='') {
		$this->entity = $entity;
		$this->fetch_data();
	}
	
	/**
	 * Données
	 */
	protected function fetch_data() {
		
		$this->pages = array();
		$query = 'select id_page, page_entity from frbr_pages ';
// 		if($this->entity) {
			$query .= 'where page_entity = "'.$this->entity.'" ';
// 		}
		$query .= 'order by page_order, page_name';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
// 				$this->pages[$row->page_entity][] = new frbr_entity_common_entity_page($row->id_page);
				$this->pages[] = new frbr_entity_common_entity_page($row->id_page);
			}
		}
	}
	
	public function get_pages() {
		return $this->pages;
	}
}