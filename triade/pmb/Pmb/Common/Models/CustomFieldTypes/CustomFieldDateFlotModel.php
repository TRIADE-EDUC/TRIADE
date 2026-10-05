<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldDateFlotModel.php,v 1.3 2020/10/02 14:05:19 btafforeau Exp $

namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldDateFlotModel extends Model
{
    public static function findDateFlotValues($customField)
    {
        $dateValues = [];
        if (empty($customField['VALUES'])) {
            $value = '';
            if ($customField['OPTIONS'][0]['DEFAULT_TODAY'][0]['value'] !== 'yes') {
                $dt = new \DateTime('now');
                $value = $dt->format('Y-m-d');
            }
            $dateValues[] = [
                'type' => 0,
                'value' => $value,
                'value1' => '',
                'comment' => ''
            ];
        } else {
            foreach ($customField['VALUES'] as $customValue) {
                $splittedValues = explode('|||', $customValue);
                $dateValues[] = [
                    'type' => $splittedValues[0],
                    'value' => $splittedValues[1],
                    'value1' => $splittedValues[2],
                    'comment' => $splittedValues[3]
                ];
            }
        }
        return $dateValues;
    }
    
    public static function getDateFlotGlobalValue($customValues)
    {
        $globalValue = [];
        foreach ($customValues as $customValue) {
            if (!empty($customValue->value)) {
                $globalValue[] = $customValue->type . '|||' . $customValue->value . '|||' . $customValue->value1 . '|||' . $customValue->comment;
            }
        }
        
        return $globalValue;
    }
    
    public static function getDateFlotInformations($customField)
    {
        global $msg;
        
        for ($i = 0; $i < 5; $i++) {
            $customField['LIST_VALUES'][] = $msg["parperso_option_duration_type$i"];
        }
        
        $customField['MSG'] = [
            'duration_begin' => $msg['parperso_option_duration_begin'],
            'duration_end' => $msg['parperso_option_duration_end'],
            'duration_comment' => $msg['parperso_option_duration_comment']
        ];
        
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}