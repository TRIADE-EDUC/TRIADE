<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_used_in_record_qualification.class.php,v 1.2 2021/01/28 14:41:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class frbr_entity_common_datasource_used_in_record_qualification extends frbr_entity_common_datasource_used_in_qualification {
	
    public function __construct($id=0){
        $this->vedette_type = [TYPE_NOTICE_RESPONSABILITY_PRINCIPAL, TYPE_NOTICE_RESPONSABILITY_AUTRE, TYPE_NOTICE_RESPONSABILITY_SECONDAIRE];
        $this->entity_type = "records";
        parent::__construct($id);
    }
    
    /*
     * Récupération des données de la source...
     */
    public function get_datas($datas=array()){
        $query = "SELECT R.responsability_notice AS id, VO.object_id AS parent
                FROM vedette V
                JOIN vedette_object VO ON V.id_vedette = VO.num_vedette
                JOIN vedette_link VL ON VL.num_vedette = VO.num_vedette
                JOIN responsability R ON R.id_responsability = VL.num_object
                WHERE VO.object_id IN (".implode(',', $datas).")
                AND VO.object_type = ".$this->get_type_from_entity_type($this->get_parent_type())."
                AND VL.type_object IN (".implode(',', $this->vedette_type).")
                AND V.grammar = 'notice_authors'";
        $datas = $this->get_datas_from_query($query);
        return $datas;
    }
}