<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_diffusionslistabon.class.php,v 1.1.4.2 2025/04/11 10:10:09 jparis Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_diffusionslistabon extends cms_module_common_module
{
    public function __construct($id = 0)
    {
        $this->module_path = str_replace(basename(__FILE__), "", __FILE__);
        parent::__construct($id);
    }
}