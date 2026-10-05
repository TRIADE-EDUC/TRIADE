<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldQualifiedTextI18nModel.php,v 1.3 2020/10/02 08:12:50 btafforeau Exp $
namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldQualifiedTextI18nModel extends Model
{

    public static function findQualifiedTextI18nValues($customField)
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

            $qualifiedValue = '';
            if (! empty($customField['OPTIONS'][0]['DEFAULT_VALUE'][0]['value'])) {
                $qualifiedValue = $customField['OPTIONS'][0]['DEFAULT_VALUE'][0]['value'];
            } elseif ($customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'] != '') {
                $qualifiedValue = $customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'];
            }

            $textValues[] = [
                'displayLang' => $displayLang,
                'lang' => $defaultLang,
                'qualifiedValue' => $qualifiedValue,
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
                    'qualifiedValue' => $splittedValues[2],
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

    public static function getQualifiedTextI18nGlobalValue($customValues)
    {
        $globalValue = [];
        foreach ($customValues as $customValue) {
            if (!empty($customValue->value) && !empty($customValue->lang)) {
                $globalValue[] = $customValue->value . '|||' . $customValue->lang . '|||' . $customValue->qualifiedValue;
            }
        }

        return $globalValue;
    }

    public static function getQualifiedTextI18nInformations($customField, $prefix, $champ)
    {
        global $msg;

        $langueDoc = get_langue_doc();
        if (! empty($customField['OPTIONS'][0]['DEFAULT_LANG'][0]['value'])) {
            $customField['OPTIONS'][0]['DEFAULT_LANG'][0]['DISPLAY_LABEL'] = $langueDoc[$customField['OPTIONS'][0]['DEFAULT_LANG'][0]['value']];
        }

        $customField['LIST_VALUES'] = array();
        $unselectedLabel = $customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['value'];
        if (! empty($unselectedLabel)) {
            $unselectedId = $customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'];
            $customField['LIST_VALUES'][$unselectedId] = $unselectedLabel;
        }

        $requete = "SELECT " . $prefix . "_custom_list_value, " . $prefix . "_custom_list_lib FROM " . $prefix . "_custom_lists WHERE " . $prefix . "_custom_champ = $champ ORDER BY ordre";
        $resultat = pmb_mysql_query($requete);
        if ($resultat) {
            $i = 0;
            while ($row = pmb_mysql_fetch_array($resultat)) {
                $customField['LIST_VALUES'][$row[$prefix . "_custom_list_value"]] = $row[$prefix . "_custom_list_lib"];
                $i ++;
            }
        }

        $customField['MSG']['LANG_SELECT'] = $msg['param_perso_lang_select'];

        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}