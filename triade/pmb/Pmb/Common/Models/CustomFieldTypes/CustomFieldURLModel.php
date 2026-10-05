<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldURLModel.php,v 1.3 2020/10/02 08:12:50 btafforeau Exp $
namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldURLModel extends Model
{

    public static function findURLValues($customField)
    {
        $URLValues = [];

        if (empty($customField['VALUES'])) {
            $URLValues[] = [
                'displayLabel' => '',
                'img' => '',
                'linkTarget' => '',
                'value' => ''
            ];
            if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                $URLValues[0]['dndId'] = 0;
            }
        } else {
            $i = 0;
            foreach ($customField['VALUES'] as $customValue) {
                $splittedValues = explode('|', $customValue);
                $newTab = false;
                if ($splittedValues[2] == '1') {
                    $newTab = true;
                }
                $URLValues[] = [
                    'displayLabel' => $splittedValues[1],
                    'img' => '',
                    'linkTarget' => $newTab,
                    'value' => $splittedValues[0]
                ];
                if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                    $URLValues[$i]['dndId'] = $i;
                }
                $i ++;
            }
        }

        return $URLValues;
    }

    public static function getURLGlobalValue($customValues)
    {
        $globalValue = [];
        foreach ($customValues as $customValue) {
            $newTab = 0;
            if ($customValue->linkTarget) {
                $newTab = 1;
            }
            if (!empty($customValue->value) && !empty($customValue->displayLabel)) {
                $globalValue[] = $customValue->value . '|' . $customValue->displayLabel . '|' . $newTab;
            }
        }
        return $globalValue;
    }

    public static function getURLInformations($customField)
    {
        global $msg;
        if (empty($customField['OPTIONS'][0]['BUTTONTEXT'][0]['value'])) {
            $customField['MSG']['URL_CHECK'] = $msg['persofield_url_check'];
            $customField['MSG']['URL_LINK_TARGET'] = $msg['persofield_url_linktarget'];
            $customField['MSG']['URL_LINK_LABEL'] = $msg['persofield_url_linklabel'];
        }
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}