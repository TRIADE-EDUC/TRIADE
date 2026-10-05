<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_datanodes.class.php,v 1.3 2019/06/13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/frbr/cataloging/frbr_cataloging_datanode.class.php");

class frbr_cataloging_datanodes {
	
	protected $num_category;
	
	protected $title;
	
	protected $num_parent;
	
	/**
	 * Liste des jeux de données
	 */
	protected $datanodes;
	
	protected $children;
	
	/**
	 * Constructeur
	 */
	public function __construct($num_category=0) {
	    $this->num_category = (int) $num_category;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $PMBuserid;
		
		if ($this->num_category) {
			$query = "select cataloging_category_title, cataloging_category_num_parent from frbr_cataloging_categories where id_cataloging_category=".$this->num_category;
			$result = pmb_mysql_query($query);
			if ($row = pmb_mysql_fetch_object($result)) {
				$this->title = $row->cataloging_category_title;
				$this->num_parent = $row->cataloging_category_num_parent;
			}
		} else {
			$this->title = "Racine";
			$this->num_parent = -1;
		}
		$this->datanodes = array();
		$query = "select id_cataloging_datanode from frbr_cataloging_datanodes where cataloging_datanode_num_category =".$this->num_category;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$frbr_cataloging_datanode = new frbr_cataloging_datanode($row->id_cataloging_datanode);
				//Gestion des droits utilisateurs (on affiche uniquement les veilles paramétrées pour le current user)
				if(in_array(SESSuserid,$frbr_cataloging_datanode->get_allowed_users()) || ($PMBuserid==1)){
					$this->datanodes[] = $frbr_cataloging_datanode->get_informations();
				}
				
			}
		}
		$this->children = array();
		$query = "select id_cataloging_category from frbr_cataloging_categories where cataloging_category_num_parent=".$this->num_category;
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)) {
			$frbr_cataloging_datanodes = new frbr_cataloging_datanodes($row->id_cataloging_category);
			$this->children[] = $frbr_cataloging_datanodes->get_format_data();
		}
	}
	
	public function get_format_data() {
		$format_data = array(
				'id' => $this->num_category,
				'type' => 'category',
				'title' => $this->title,
				'num_parent' => $this->num_parent,
				'children' => $this->children,
				'datanodes' => $this->datanodes
		);
		return $format_data;
	}
}