<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_searcher_extended.class.php,v 1.2 2022/09/22 09:22:05 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once $class_path.'/searcher.class.php';

class search_segment_searcher_extended extends searcher_extended {

    protected function get_search_instance(){
        return search::get_instance('search_fields_gestion');
    }
}