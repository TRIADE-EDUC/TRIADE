<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UrlBuilder_5_503.php,v 1.2 2022/04/04 11:53:51 qvarin Exp $
namespace Pmb\CMS\Library\UrlBuilder;

class UrlBuilder_5_503 extends EntityUrlBuilder
{

    public const LVL = "categ_see";
    
    public static $entitiesIds = null;
    
    public function getEntityType()
    {
        return TYPE_CATEGORY;
    }
    
    protected function getQuery(): string
    {
        return "SELECT num_noeud AS 'entity_id' FROM categories";
    }
}