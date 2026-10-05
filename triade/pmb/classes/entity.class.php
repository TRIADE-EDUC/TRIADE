<?php
// +-------------------------------------------------+
// � 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entity.class.php,v 1.2 2023/06/13 13:46:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class entity{
    protected $id;
    
    protected $type;
    
    protected $isbd;
    
    public function __construct($id, $type) {
    	$this->id = intval($id);
    	$this->type = intval($type);
    	$this->fetch_data();
    }
    
    protected function fetch_data() {
    	$query = "SELECT entity_isbd FROM entities WHERE num_entity = '".$this->id."' AND type_entity = '".$this->type."'";
    	$result = pmb_mysql_query($query);
    	if(pmb_mysql_num_rows($result)) {
    		$row = pmb_mysql_fetch_object($result);
    		$this->isbd = $row->entity_isbd;
    	}
    }
    
    protected function get_generated_isbd() {
    	global $lang;
    	
    	switch ($this->type){
    		case TYPE_NOTICE:
    			return "ISBD NOTICE";
    		case TYPE_AUTHOR:
    			$aut= authorities_collection::get_authority(AUT_TABLE_AUTHORS, $this->id);
    			return $aut->get_isbd();
    		case TYPE_PUBLISHER:
    			$aut= authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $this->id);
    			return $aut->get_isbd();
    		case TYPE_INDEXINT:
    			$aut= authorities_collection::get_authority(AUT_TABLE_INDEXINT, $this->id);
    			return $aut->get_isbd();
    		case TYPE_COLLECTION:
    			$aut= authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $this->id);
    			return $aut->get_isbd();
    		case TYPE_SUBCOLLECTION:
    			$aut= authorities_collection::get_authority(AUT_TABLE_SUB_COLLECTIONS, $this->id);
    			return $aut->get_isbd();
    		case TYPE_SERIE:
    			$aut= authorities_collection::get_authority(AUT_TABLE_SERIES, $this->id);
    			return $aut->get_isbd();
    		case TYPE_CATEGORY:
    			$aut= authorities_collection::get_authority(AUT_TABLE_CATEGORIES, $this->id,array('lang'=>$lang,'for_indexation' => true));
    			return $aut->libelle_categorie;
    		case TYPE_TITRE_UNIFORME:
    			$aut= authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $this->id);
    			return $aut->get_isbd();
    		case TYPE_AUTHPERSO:
    			return authperso::get_isbd($this->id);
    	}
    }
    
    public function save() {
    	$query = "INSERT INTO entities SET 
			num_entity= ".$this->id.",
			type_entity = ".$this->type.",
			entity_isbd = '".addslashes($this->isbd)."'
		";
    	pmb_mysql_query($query);
    }
    
	public function get_isbd() {
		if(empty($this->isbd)) {
			$this->isbd = $this->get_generated_isbd();
// 			$this->save();
		}
		return $this->isbd;
	}
}