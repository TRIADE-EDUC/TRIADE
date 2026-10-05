<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldTextI18nModel.php,v 1.3 2020/10/02 08:12:50 btafforeau Exp $
namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldTextI18nModel extends Model
{

    public static function findTextI18nValues($customField)
    {
        $textValues = [];
        $langueDoc = get_langue_doc();
        if (empty($customField['VALUES'])) {
            $defaultLang = '';
            $displayLang = '';
            if (! empty($customField['OPTIONS'][0]['DEFAULT_LANG'][0]['value'])) {
                $defaultLang = $customField['OPTIONS'][0]['DEFAULT_LANG'][0]['value'];
                $displayLang = $langueDoc[$defaultLang];
            }
            $textValues[] = [
                'displayLang' => $displayLang,
                'lang' => $defaultLang,
                'value' => ''
            ];

            if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                $textValues[0]['dndId'] = 0;
            }
        } else {
            $i = 0;
            foreach ($customField['VALUES'] as $customValue) {
                $splittedValues = explode('|||', $customValue);
                $textValues[] = [
                    'displayLang' => $langueDoc[$splittedValues[1]],
                    'lang' => $splittedValues[1],
                    'value' => $splittedValues[0]
                ];
                if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                    $textValues[$i]['dndId'] = $i;
                }
                $i ++;
            }
        }

        return $textValues;
    }

    public static function getTextI18nGlobalValue($customValues)
    {
        $globalValue = [];
        foreach ($customValues as $customValue) {
            if (!empty($customValue->value) && !empty($customValue->lang)) {
                $globalValue[] = $customValue->value . '|||' . $customValue->lang;
            }
        }

        return $globalValue;
    }

    public static function getTextI18nInformations($customField)
    {
        global $msg;

        $langueDoc = get_langue_doc();
        if (! empty($customField['OPTIONS'][0]['DEFAULT_LANG'][0]['value'])) {
            $customField['OPTIONS'][0]['DEFAULT_LANG'][0]['DISPLAY_LABEL'] = $langueDoc[$customField['OPTIONS'][0]['DEFAULT_LANG'][0]['value']];
        }
        $customField['MSG']['LANG_SELECT'] = $msg['param_perso_lang_select'];
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}