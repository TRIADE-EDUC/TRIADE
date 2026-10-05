<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UrlBuilder_6_603.php,v 1.2 2022/04/04 11:53:51 qvarin Exp $
namespace Pmb\CMS\Library\UrlBuilder;

class UrlBuilder_6_603 extends EntityUrlBuilder
{

    public const LVL = "bulletin_display";

    public static $entitiesIds = null;

    public function getEntityType()
    {
        return TYPE_BULLETIN;
    }
    
    protected function getQuery(): string
    {
        return "SELECT bulletin_id AS 'entity_id' FROM bulletins";
    }
}