<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldListModel.php,v 1.6 2023/02/15 15:05:02 qvarin Exp $
namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldListModel extends Model
{

    protected $ormName = "\Pmb\Animations\Orm\CustomFieldListOrm";

    public static function findListValues($customField, $prefix, $idCustomField)
    {
        $queryListValues = [];

        if (empty($customField['VALUES'])) {
            $displayLabel = '';
            $value = '';
            if ($customField['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'yes') {
                $value = [];
            }

            if (! empty($customField['OPTIONS'][0]['DEFAULT_VALUE'][0]['value'])) {
                $defaultValueId = 0;
                if (! empty($customField['OPTIONS'][0]['DEFAULT_VALUE'][0]['value'])) {
                    $query = "SELECT " . $prefix . "_custom_list_value AS id, " . $prefix . "_custom_list_lib AS libelle FROM " . $prefix . "_custom_lists WHERE " . $prefix . "_custom_champ = $idCustomField AND " . $prefix . "_custom_list_value = '" . addslashes($customField['OPTIONS'][0]['DEFAULT_VALUE'][0]['value']) . "' ORDER BY ordre";
                    $result = pmb_mysql_query($query);
                    if (pmb_mysql_num_rows($result)) {
                        while ($row = pmb_mysql_fetch_object($result)) {
                            $defaultValueId = $row->id;
                            $defaultValue = $row->libelle;
                        }
                    }
                }
                $value = $defaultValueId;
                if ($customField['OPTIONS'][0]['AUTORITE'][0]['value'] == 'yes') {
                    $displayLabel = $defaultValue;
                } else {
                    if (is_array($value)) {
                        $value = [
                            $defaultValueId
                        ];
                    }
                }
            } elseif (! empty($customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'])) {
                $value = $customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'];
                if ($customField['OPTIONS'][0]['AUTORITE'][0]['value'] == 'yes') {
                    $displayLabel = $customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['value'];
                } else {
                    if (is_array($value)) {
                        $value = [
                            $customField['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE']
                        ];
                    }
                }
            }

            $queryListValues[] = [
                'displayLabel' => $displayLabel,
                'value' => $value
            ];
            if ($customField['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'yes' && $customField['OPTIONS'][0]['AUTORITE'][0]['value'] == 'yes') {
                $queryListValues[0]['dndId'] = 0;
            }
        } else {
            if ($customField['OPTIONS'][0]['AUTORITE'][0]['value'] == 'yes') {
                $query = "SELECT " . $prefix . "_custom_list_value AS id, " . $prefix . "_custom_list_lib AS libelle FROM " . $prefix . "_custom_lists WHERE " . $prefix . "_custom_champ = $idCustomField ORDER BY ordre";
                $result = pmb_mysql_query($query);
                $listValues = [];
                if (pmb_mysql_num_rows($result)) {
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $listValues[$row->id] = $row->libelle;
                    }
                }
            }
            $i = 0;
            foreach ($customField['VALUES'] as $customValue) {
                if ($customField['OPTIONS'][0]['AUTORITE'][0]['value'] == 'yes') {
                    $queryListValues[] = [
                        'value' => $customValue,
                        'displayLabel' => $listValues[$customValue]
                    ];
                } else {
                    if ($customField['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'yes') {
                        $queryListValues[] = [
                            'value' => $customField['VALUES']
                        ];
                        break;
                    } else {
                        $queryListValues[] = [
                            'value' => $customValue
                        ];
                    }
                }

                if ($customField['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'yes' && $customField['OPTIONS'][0]['AUTORITE'][0]['value'] == 'yes') {
                    $queryListValues[$i]['dndId'] = $i;
                }
                $i ++;
            }
        }

        return $queryListValues;
    }

    public static function getListGlobalValue($customValues)
    {
        $listValue = [];
        if (! is_array($customValues[0]->value)) {
            foreach ($customValues as $customValue) {
                $listValue[] = $customValue->value;
            }
        } else {
            $listValue = $customValues[0]->value;
        }
        return $listValue;
    }

    public static function getListInformations($customField, $prefix, $champ)
    {
        $prefix = addslashes($prefix);
        $champ = intval($champ);

        $query = "SELECT "
            . "{$prefix}_custom_list_value AS id, "
            . "{$prefix}_custom_list_lib AS libelle, "
            . "ordre "
            . "FROM {$prefix}_custom_lists "
            . "WHERE {$prefix}_custom_champ = {$champ} ORDER BY ordre";

        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $customField['LIST_VALUES'][$row->id] = $row->libelle;
                $customField['LIST_ORDER'][$row->id] = $row->ordre;
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