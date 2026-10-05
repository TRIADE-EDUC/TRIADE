<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldMarclistModel.php,v 1.4 2020/09/30 12:56:11 btafforeau Exp $
namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldMarclistModel extends Model
{
    public static function findMarclistValues($customField)
    {
        $marclistValues = [];
        if (empty($customField['VALUES'])) {
            $marclistValues[] = [
                'displayLabel' => '',
                'value' => ''
            ];
            if ($customField['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'yes') {
                $marclistValues[0]['dndId'] = 0;
            }
        } else {
            if ($customField['OPTIONS'][0]['AUTORITE'][0]['value'] == 'yes') {
                $i = 0;
                $listValues = \marc_list_collection::get_instance($customField['OPTIONS'][0]['DATA_TYPE'][0]['value']);
                foreach ($customField['VALUES'] as $customValue) {
                    $marclistValues[] = [
                        'displayLabel' => $listValues->table[strtoupper($customValue)],
                        'value' => $customValue
                    ];
                    if ($customField['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'yes') {
                        $marclistValues[$i]['dndId'] = $i;
                    }
                }
            } else {
                $value = $customField['VALUES'];
                if ($customField['OPTIONS'][0]['MULTIPLE'][0]['value'] != 'yes' && is_array($value)) {
                    $value = $customField['VALUES'][0];
                }
                $marclistValues[] = [
                    'value' => $value
                ];
            }
        }

        return $marclistValues;
    }

    public static function getMarclistGlobalValue($customValues)
    {
        $globalValue = [];
        if (! is_array($customValues[0]->value)) {
            foreach ($customValues as $customValue) {
                $globalValue[] = $customValue->value;
            }
        } else {
            $globalValue = $customValues[0]->value;
        }

        return $globalValue;
    }

    public static function getMarclistInformations($customField)
    {
        $list = \marc_list_collection::get_instance($customField['OPTIONS'][0]['DATA_TYPE'][0]['value']);

        $customField['LIST_VALUES'] = self::toArray($list);

        switch ($customField['OPTIONS'][0]['DATA_TYPE'][0]['value']) {
            case 'lang':
                $customField['OPTIONS'][0]['DATA_TYPE'][0]['value'] = 'langue';
                break;
            case 'function':
                $customField['OPTIONS'][0]['DATA_TYPE'][0]['value'] = 'fonction';
                break;
            default:
                break;
        }
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}