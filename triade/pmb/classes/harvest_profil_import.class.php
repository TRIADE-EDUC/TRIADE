<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: harvest_profil_import.class.php,v 1.11 2024/01/05 11:37:04 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $charset;

class harvest_profil_import {
    
    public $id=0;
    
    public function __construct($id = 0)
    {
        $this->id=intval($id);
    }
    
    /**
     * Retourne un selecteur html de profil d'import
     *
     * @param string $sel_name
     * @param number $sel_id
     *
     * @return string
     */
    public static function getSelector($sel_name = '', $sel_id = 0 )
    {
        global $charset;
        
        $list = static::getList();
        if( !count($list)) {
            return '';
        }
        $tpl = "<select name='$sel_name' >";
        foreach($list as $id => $name) {
            $tpl .= "<option value='".$id."' ". (($id==$sel_id) ? "selected" : "") . ">" . htmlentities($name, ENT_QUOTES, $charset) ."</option>";
        }
        $tpl.= "</select>";
        return $tpl;
    }
    
    
    /**
     * Retourne la liste des profils d'import
     *
     * @return array
     */
    public static function getList()
    {
        $q = "select id_harvest_profil_import, harvest_profil_import_name from harvest_profil_import order by harvest_profil_import_name";
        $r = pmb_mysql_query($q);
        
        if ( !pmb_mysql_num_rows($r) ) {
            return [];
        }
        
        $list = [];
        while($row = pmb_mysql_fetch_assoc($r)) {
            $list[$row['id_harvest_profil_import']] = $row['harvest_profil_import_name'];
        }
        return $list;
    }
    
}
