<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_datasource_authperso_link.class.php,v 1.1 2019/09/04 13:20:56 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_records_datasource_authperso_link extends frbr_entity_common_datasource {
    
    public function __construct($id=0) {
        $this->entity_type = "authperso";
        $this->origin_entity = "records";
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
	    $query = "
            SELECT DISTINCT notice_authperso_authority_num as id, notice_authperso_notice_num as parent 
            FROM notices_authperso";
	    if (!empty($this->parameters->authperso_id)) {
            $query .= " JOIN authperso_authorities ON id_authperso_authority = notice_authperso_authority_num 
                AND authperso_authority_authperso_num = '".$this->parameters->authperso_id."'";
	    }
	    $query .= " WHERE notice_authperso_notice_num IN (".implode(',', $datas).")";
	    $datas = $this->get_datas_from_query($query);
	    $datas = parent::get_datas($datas);
	    return $datas;
	}
}