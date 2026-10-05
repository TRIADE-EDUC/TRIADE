<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UrlBuilder_6_601.php,v 1.5 2023/02/24 11:05:39 qvarin Exp $
namespace Pmb\CMS\Library\UrlBuilder;

class UrlBuilder_6_601 extends EntityUrlBuilder
{

    public const LVL = "notice_display";

    public static $entitiesIds = null;

    public function getEntityType()
    {
        return TYPE_NOTICE;
    }
    
    protected function getQuery(): string
    {
        global $gestion_acces_active, $gestion_acces_empr_notice;
        
        $acces_join = "";
        if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
            $dom_2 = static::getAccesDomain(2);
            $id_empr = !empty($_SESSION['id_empr_session']) ? intval($_SESSION['id_empr_session']) : 0;
            $acces_join = $dom_2->getJoin($id_empr, 16, 'notice_id');
        }
        
        return "SELECT notice_id AS 'entity_id' FROM notices {$acces_join} WHERE niveau_biblio NOT IN('b', 'a', 's')";
    }
}