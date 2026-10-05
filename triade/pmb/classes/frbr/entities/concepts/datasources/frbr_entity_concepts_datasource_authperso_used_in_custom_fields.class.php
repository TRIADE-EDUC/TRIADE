<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_datasource_authperso_used_in_custom_fields.class.php,v 1.1 2019/09/19 08:36:25 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_concepts_datasource_authperso_used_in_custom_fields extends frbr_entity_common_datasource_used_in_custom_fields {
    
    public function __construct($id=0) {
        $this->entity_type = "authperso";
        $this->origin_entity = "concepts";
        parent::__construct($id);
        $this->prefix = 'authperso';
    }
	
	public function set_authperso_id($id) {
	    $this->parameters->authperso_id = intval($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
	    $query = "SELECT ".$this->parameters->prefix."_custom_origine AS id, ".$this->parameters->prefix."_custom_".$this->parameters->datatype." AS parent FROM ".$this->parameters->prefix."_custom_values WHERE ".$this->parameters->prefix."_custom_champ = ".$this->parameters->id." AND ".$this->parameters->prefix."_custom_".$this->parameters->datatype." IN (".implode(',', $datas).")";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result) == 0) {
	        $datas = array_map(function($id) {
	            return onto_common_uri::get_uri($id);
	        }, $datas);
	            $query = "SELECT ".$this->parameters->prefix."_custom_origine AS id, ".$this->parameters->prefix."_custom_".$this->parameters->datatype." AS parent FROM ".$this->parameters->prefix."_custom_values WHERE ".$this->parameters->prefix."_custom_champ = ".$this->parameters->id." AND ".$this->parameters->prefix."_custom_".$this->parameters->datatype." IN ('".implode("','", $datas)."')";
	    }
	    $datas = $this->get_datas_from_query($query);
	    //$datas = parent::get_datas($datas);
	    return $datas;
	}
}