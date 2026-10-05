<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldDateBoxModel.php,v 1.2 2020/09/23 15:23:24 gneveu Exp $
namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldDateBoxModel extends Model
{

    public static function findDateBoxValues($customField)
    {
        $dateValues = [];
        if (empty($customField['VALUES'])) {
            $value = '';
            if ($customField['OPTIONS'][0]['DEFAULT_TODAY'][0]['value'] != 'yes') {
                $dt = new \DateTime('now');
                $value = $dt->format('Y-m-d');
            }
            $dateValues[] = [
                'value' => $value
            ];
            if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                $dateValues[0]['dndId'] = 0;
            }
        } else {
            $i = 0;
            foreach ($customField['VALUES'] as $customValue) {
                $dt = new \DateTime($customValue);
                $value = $dt->format('Y-m-d');
                $dateValues[] = [
                    'value' => $value
                ];
                if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                    $dateValues[0]['dndId'] = 0;
                }
                $i ++;
            }
        }

        return $dateValues;
    }

    public static function getDateBoxGlobalValue($customValues)
    {
        $globalValue = [];
        foreach ($customValues as $customValue) {
            $globalValue[] = $customValue->value;
        }

        return $globalValue;
    }

    public static function getDateBoxInformations($customField)
    {
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}