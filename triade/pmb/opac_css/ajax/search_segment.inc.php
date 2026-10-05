<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment.inc.php,v 1.1 2020/03/25 13:36:42 jlaurent Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $action, $segment_sort, $num_segment;

switch ($action):
    case 'add_session_currentSegment':
        require_once($class_path."/search_universes/search_segment_sort.class.php");
        $search_segment_sort = new search_segment_sort($num_segment);
        $search_segment_sort->add_session_currentSegment($segment_sort);
        break;
    default:
        break;
endswitch;