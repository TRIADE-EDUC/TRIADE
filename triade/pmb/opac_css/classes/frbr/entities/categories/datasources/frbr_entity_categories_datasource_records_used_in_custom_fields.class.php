<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_categories_datasource_records_used_in_custom_fields.class.php,v 1.2 2019/10/21 13:47:50 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_categories_datasource_records_used_in_custom_fields extends frbr_entity_common_datasource_used_in_custom_fields {
    
    public function __construct($id=0) {
        $this->entity_type = "records";
        $this->origin_entity = "categories";
        parent::__construct($id);
        $this->prefix = 'notices';
    }
}