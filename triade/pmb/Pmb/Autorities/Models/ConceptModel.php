<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ConceptModel.php,v 1.5 2023/03/14 15:16:11 gneveu Exp $

namespace Pmb\Autorities\Models;

use Pmb\Common\Models\Model;

class ConceptModel extends Model
{
    protected $ormName = "\Pmb\Autorities\Orm\ConceptOrm";
    
    public static function getConcept($conceptId)
    {
        $concept = new \concept($conceptId);
        $conceptInfos = [];
        $conceptInfos['id'] = $concept->get_id();
        $conceptInfos['displayLabel'] = $concept->get_isbd();
        return $conceptInfos;
    }
    
    public static function updateAnimationConcepts($concepts, $animationId)
    {
        $ordre = 0;
        $rqtDel = "DELETE FROM index_concept WHERE num_object = '$animationId' AND type_object = '" . TYPE_ANIMATION . "'";
        pmb_mysql_query($rqtDel);
        $rqtIns = "INSERT INTO index_concept (num_object, type_object, num_concept, order_concept, comment, comment_visible_opac) VALUES";
        foreach ($concepts as $concept) {
            if (!empty($concept->id)) {
                $rqt = "$rqtIns ('$animationId', '" . TYPE_ANIMATION . "', '$concept->id', $ordre, '', 0)";
                pmb_mysql_query($rqt);
                $ordre++;
            }
        }
    }
}