<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldCommentModel.php,v 1.2 2020/09/23 15:23:24 gneveu Exp $

namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldCommentModel extends Model
{
    public static function findCommentValues($customField)
    {
        $commentValues = [];
        if (empty($customField['VALUES'])) {
            $commentValues[] = [
                'value' => ''
            ];
            if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                $commentValues[0]['dndId'] = 0;
            }
        } else {
            $i = 0;
            foreach ($customField['VALUES'] as $customValue) {
                $commentValues[] = [
                    'value' => $customValue
                ];
                if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                    $commentValues[$i]['dndId'] = $i;
                }
                $i ++;
            }
        }
        return $commentValues;
    }
    
    public static function getCommentGlobalValue($customValues)
    {
        $globalValue = [];
        foreach ($customValues as $customValue) {
            $globalValue[] = $customValue->value;
        }
        return $globalValue;
    }
    
    public static function getCommentInformations($customField)
    {
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}