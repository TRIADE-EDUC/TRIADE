<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldDateIntervalModel.php,v 1.3 2020/10/02 08:12:50 btafforeau Exp $
namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldDateIntervalModel extends Model
{

    public static function findDateIntervalValues($customField)
    {
        $dateValues = [];
        if (empty($customField['VALUES'])) {
            $value = '';
            if ($customField['OPTIONS'][0]['DEFAULT_TODAY'][0]['value'] != 'yes') {
                $dt = new \DateTime('now');
                $value = $dt->format('Y-m-d');
            }
            $dateValues[] = [
                'value' => $value,
                'valueTime' => '',
                'value1' => $value,
                'value1Time' => ''
            ];
            
            if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                $dateValues[0]['dndId'] = 0;
            }
        } else {
            $i = 0;
            foreach ($customField['VALUES'] as $customValue) {
                $splittedValues = explode('|', $customValue);

                $dt = new \DateTime();
                $dt->setTimestamp($splittedValues[0]);
                $value = $dt->format('Y-m-d');
                $valueTime = $dt->format('H:i');
                $et = new \DateTime();
                $et->setTimestamp($splittedValues[1]);
                $value1 = $et->format('Y-m-d');
                $value1Time = $et->format('H:i');

                $dateValues[] = [
                    'value' => $value,
                    'valueTime' => $valueTime,
                    'value1' => $value1,
                    'value1Time' => $value1Time
                ];

                if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                    $dateValues[$i]['dndId'] = $i;
                }
                $i ++;
            }
        }

        return $dateValues;
    }

    public static function getDateIntervalGlobalValue($customValues)
    {
        $globalValue = [];
        foreach ($customValues as $customValue) {
            if (empty($customValue->valueTime)) {
                $customValue->valueTime = '00:00';
            }
            if (empty($customValue->value1Time)) {
                $customValue->value1Time = '23:59';
            }
            
            $formatDt = '';
            if (!empty($customValue->value)) {
                $dt = new \DateTime("$customValue->value $customValue->valueTime");
                $formatDt = $dt->format('U');
            }
            
            $formatEt = '';
            if (!empty($customValue->value1)) {
                $et = new \DateTime("$customValue->value1 $customValue->value1Time");
                $formatEt = $et->format('U');
            }
            
            if (!empty($formatDt) && !empty($formatEt)) {
                $globalValue[] = $formatDt . '|' . $formatEt;
            }
        }

        return $globalValue;
    }

    public static function getDateIntervalInformations($customField)
    {
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}