<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DocsLocation.php,v 1.1 2024/02/14 09:21:08 dbellamy Exp $

namespace Pmb\Common\Helper;

class DocsLocation
{

    static $shortList = null;

    /**
     * Liste des localisations
     *
     * @return ['id', 'label'];
     */
    public static function getShortList()
    {
        $shortList = [];
        if( is_null(static::$shortList) ) {
            static::$shortList = [];
            $q = "select idlocation as id, location_libelle as label from docs_location order by label";
            $r = pmb_mysql_query($q);
            if(pmb_mysql_num_rows($r)) {

                while($row = pmb_mysql_fetch_assoc($r)) {
                    static::$shortList[$row['id']] = $row['label'];
                }
            }
        }
        $shortList = static::$shortList;
        return $shortList;
    }
}
