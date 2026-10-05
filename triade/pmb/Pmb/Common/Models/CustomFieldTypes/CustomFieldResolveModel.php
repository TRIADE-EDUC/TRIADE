<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldResolveModel.php,v 1.2 2020/10/02 08:12:50 btafforeau Exp $

namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldResolveModel extends Model
{
    public static function findResolveValues($customField)
    {
        $resolveValues = [];
        if (empty($customField['VALUES'])) {
            $selectValue = '';
            if (isset($customField['OPTIONS'][0]['RESOLVE'])) {
                $selectValue = $customField['OPTIONS'][0]['RESOLVE'][0]['ID'];
            }
            $resolveValues[] = [
                'value' => '',
                'selectValue' => $selectValue
            ];
        } else {
            foreach ($customField['VALUES'] as $customValue) {
                $splittedValues = explode('|', $customValue);
                $resolveValues[] = [
                    'value' => $splittedValues[0],
                    'selectValue' => $splittedValues[1]
                ];
            }
        }
        return $resolveValues;
    }
    
    public static function getResolveGlobalValue($customValues)
    {
        $globalValue = [];
        foreach ($customValues as $customValue) {
            if (!empty($customValue->value) && !empty($customValue->selectValue)) {
                $globalValue[] = $customValue->value . '|' . $customValue->selectValue;
            }
        }
        
        return $globalValue;
    }
    
    public static function getResolveInformations($customField)
    {
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}