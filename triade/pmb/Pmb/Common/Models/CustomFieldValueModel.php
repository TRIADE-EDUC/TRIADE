<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldValueModel.php,v 1.12 2020/09/30 10:04:10 qvarin Exp $

namespace Pmb\Common\Models;

use Pmb\Common\Helper\Helper;

class CustomFieldValueModel extends Model
{
    protected $ormName = "\Pmb\Animations\Orm\CustomFieldValueOrm";
    
    public static function findValue($prefix, $champ, $origine)
    {
        $query = "SELECT * FROM " . $prefix . "_custom_values WHERE " . $prefix . "_custom_champ = $champ AND " . $prefix . "_custom_origine = $origine ORDER BY " . $prefix . "_custom_order";
        $result = pmb_mysql_query($query);
        $customFieldValues = [];
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $customFieldValues[] = $row;
            }
        }
        return $customFieldValues;
    }
    
    public static function findAllValue($prefix, $origine)
    {
        $query = "SELECT * FROM " . $prefix . "_custom_values WHERE " . $prefix . "_custom_origine = $origine ORDER BY " . $prefix . "_custom_order";
        $result = pmb_mysql_query($query);
        $customFieldValues = [];
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $customFieldValues[] = $row;
            }
        }
        return $customFieldValues;
    }
    
    public static function getExternalInformations($customField)
    {
        global $msg;
        if (empty($customField['OPTIONS'][0]['BUTTONTEXT'][0]['value'])) {
            $customField['OPTIONS'][0]['BUTTONTEXT'][0]['value'] = $msg['parperso_external_browse'];
        }
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }

    public static function findExternalValues($customField, $prefix, $idCustomField, $idObject)
    {
        return [[
            'value' => ''
        ]];
    }
}