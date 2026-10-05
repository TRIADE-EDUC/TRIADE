<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldQueryListModel.php,v 1.2 2020/10/02 08:12:50 btafforeau Exp $

namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldQueryListModel extends Model
{
    public static function findQueryListValues($customField)
    {
        $queryListValues = [];
        
        if (empty($customField['VALUES'])) {
            $displayLabel = '';
            $value = '';
            if ($customField['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'yes' && $customField['OPTIONS'][0]['AUTORITE'][0]['value'] != 'yes') {
                $value = [];
            }
            
            if (! empty($customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'])) {
                $value = $customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'];
                if ($customField['OPTIONS'][0]['AUTORITE'][0]['value'] == 'yes') {
                    $displayLabel = $customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['value'];
                } else {
                    if (is_array($value)) {
                        $value = [$customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE']];
                    }
                }
            }
            
            $queryListValues[] = [
                'displayLabel' => $displayLabel,
                'value' => $value
            ];
        } else {
            if ($customField['OPTIONS'][0]['AUTORITE'][0]['value'] == 'yes') {
                $result = pmb_mysql_query($customField['OPTIONS'][0]['QUERY'][0]['value']);
                $listValues = [];
                if (pmb_mysql_num_rows($result)) {
                    while ($row = pmb_mysql_fetch_array($result)) {
                        $listValues[$row[0]] = $row[1];
                    }
                }
            }
            foreach ($customField['VALUES'] as $customValue) {
                if ($customField['OPTIONS'][0]['AUTORITE'][0]['value'] == 'yes') {
                    $queryListValues[] = [
                        'value' => $customValue,
                        'displayLabel' => $listValues[$customValue]
                    ];
                } else {
                    $queryListValues[] = [
                        'value' => $customValue
                    ];
                }
            }
        }
        
        return $queryListValues;
    }
    
    public static function getQueryListGlobalValue($customValues)
    {
        $listValue = [];
        if (is_object($customValues[0])) {
            foreach ($customValues as $customValue) {
                if (!empty($customValue->value)) {
                    $listValue[] = $customValue->value;
                }
            }
        } else {
            $listValue = $customValues[0]->value;
        }
        
        return $listValue;
    }
    
    public static function getQueryListInformations($customField)
    {
        global $lang;
        
        // On rajoute la langue si besoin dans le requête
        $query = str_replace('$lang', $lang, $customField['OPTIONS'][0]['QUERY'][0]['value']);
        
        if (! empty($query)) {
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_array($result)) {
                    $customField['LIST_VALUES'][$row[0]] = $row[1];
                }
            }
        }
        
        $customField['OPTIONS'][0]['TYPE_LIST'] = '';
        if ($customField['OPTIONS'][0]['CHECKBOX'][0]['value'] == 'yes') {
            $customField['OPTIONS'][0]['TYPE_LIST'] = 'radio';
            if ($customField['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'yes') {
                $customField['OPTIONS'][0]['TYPE_LIST'] = 'checkbox';
            }
        }
        
        if (empty($customField['OPTIONS'][0]['CHECKBOX_NB_ON_LINE'][0]['value'])) {
            $customField['OPTIONS'][0]['CHECKBOX_NB_ON_LINE'][0]['value'] = 4;
        }
        
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}