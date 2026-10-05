<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_searcher_ontologies.class.php,v 1.1 2023/02/07 15:31:39 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once $class_path . '/searcher/searcher_authorities_extended.class.php';

// un jour ca sera utile
class search_segment_searcher_ontologies extends searcher_ontologies_extended
{

    const PREFIX_TEMPO = 'segment_onto';

    public $class_id;

    public function __construct($class_id = "")
    {
        parent::__construct();
        $this->class_id = $class_id;
        global $user_query, $universe_query;
        $this->user_query = $user_query;

        if (empty($user_query) && ! empty($universe_query)) {
            $this->user_query = $universe_query;
        }
    }

}