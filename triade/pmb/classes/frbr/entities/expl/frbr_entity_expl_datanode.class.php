<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_expl_datanode.class.php,v 1.1 2019/08/19 09:27:30 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_expl_datanode extends frbr_entity_common_entity_datanode {
    
    public function __construct($id=0){
        parent::__construct($id);
    }
}