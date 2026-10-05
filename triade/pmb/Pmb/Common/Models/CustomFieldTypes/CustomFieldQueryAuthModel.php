<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldQueryAuthModel.php,v 1.5 2020/10/02 08:12:50 btafforeau Exp $

namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldQueryAuthModel extends Model
{
    public static function findQueryAuthValues($customField)
    {
        $queryAuthValues = [];
        if (empty($customField['VALUES'])) {
            $queryAuthValues[] = [
                'value' => '',
                'displayLabel' => ''
            ];
            
            if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                $queryAuthValues[0]['dndId'] = 0;
            }
        } else {
            $i = 0;
            foreach ($customField['VALUES'] as $customValue) {
                $queryAuthValues[] = [
                    'value' => $customValue,
                    'displayLabel' => get_authority_isbd_from_field($customField, $customValue)
                ];
                
                if ($customField['OPTIONS'][0]['REPEATABLE'][0]['value'] == '1') {
                    $queryAuthValues[$i]['dndId'] = $i;
                }
                $i++;
            }
        }
        
        return $queryAuthValues;
    }
    
    public static function getQueryAuthGlobalValue($customValues)
    {
        $listValue = [];
        if (is_object($customValues[0])) {
            foreach ($customValues as $customValue) {
                $listValue[] = $customValue->value;
            }
        } else {
            $listValue = $customValues[0]->value;
        }
        
        return $listValue;
    }
    
    public static function getQueryAuthInformations($customField)
    {
        $dataType = $customField["OPTIONS"][0]["DATA_TYPE"]["0"]["value"];
        $customField['AUTH_INFOS'] = get_authority_selection_parameters($dataType);
        $customField['ATT_ID_FILTER'] = '';
        $customField['PARAM1'] = '';
        
        switch ($dataType) {
            case 2:
                // Catégories
                if(isset($customField["OPTIONS"][0]["ID_THES"]["0"]["value"])){
                    $idThesUnique = $customField["OPTIONS"][0]["ID_THES"]["0"]["value"];
                    $customField['ATT_ID_FILTER'] = $idThesUnique;
                }
                break;
            case 9:
                // Concepts
                $conceptSchemes = [];
                $idSchemeConceptArray = $customField['OPTIONS'][0]['ID_SCHEME_CONCEP'];
                if (!empty($idSchemeConceptArray)) {
                    for($i = 0; $i < count($idSchemeConceptArray); $i++) {
                        $conceptSchemes[] = $idSchemeConceptArray[$i]['value'];
                    }
                }
                if (!empty($conceptSchemes[0]) && $conceptSchemes[0] != -1) {
                    $customField['PARAM1'] = implode(',', $conceptSchemes);
                }
                $customField['ATT_ID_FILTER'] = "http://www.w3.org/2004/02/skos/core#Concept";
                break;
        }
        
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}