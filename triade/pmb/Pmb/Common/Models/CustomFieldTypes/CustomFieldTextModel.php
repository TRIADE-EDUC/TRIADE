<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldTextModel.php,v 1.3 2020/09/23 10:07:26 gneveu Exp $

namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldTextModel extends Model
{
    public static function findTextValues($customField)
    {
        $textValues = [];
        if (empty($customField['VALUES'])) {
            $textValues[] = [
                'value' => ''
            ];
            if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                $textValues[0]['dndId'] = 0;
            }
        } else {
            $i = 0;
            foreach ($customField['VALUES'] as $customValue) {
                $textValues[] = [
                    'value' => $customValue
                ];
                if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                    $textValues[$i]['dndId'] = $i;
                }
                $i++;
            }
        }
        
        return $textValues;
    }
    
    public static function getTextGlobalValue($customValues)
    {
        $globalValue = [];
        foreach ($customValues as $customValue) {
            $globalValue[] = $customValue->value;
        }
        
        return $globalValue;
    }
    
    public static function getTextInformations($customField)
    {
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}