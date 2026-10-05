<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldModel.php,v 1.21 2023/02/15 15:05:02 qvarin Exp $
namespace Pmb\Common\Models;

use Pmb\Common\Models\CustomFieldTypes\CustomFieldQueryListModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldListModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldTextModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldQueryAuthModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldDateBoxModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldCommentModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldURLModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldMarclistModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldTextI18nModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldQualifiedTextI18nModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldDateIntervalModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldDateFlotModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldResolveModel;
use Pmb\Common\Models\CustomFieldTypes\CustomFieldHTMLModel;

class CustomFieldModel extends Model
{

    protected $ormName = "\Pmb\Animations\Orm\CustomFieldOrm";

    /**
     * Permet d'aller chercher les champs perso pour un prefix donnee
     *
     * @param string $prefix
     * @param number $idObject
     * @return array
     */
    public static function getAllCustomFields($prefix, $idObject = 0, $formatValue = false)
    {
        $pperso = new \parametres_perso($prefix);
        $pperso->get_values($idObject);
        return CustomFieldModel::formatField($pperso, $idObject, $formatValue);
    }

    /**
     * Permet d'aller chercher les champs perso pour un prefix donnee
     *
     * @param string $prefix
     * @param int $idObject
     * @return array
     */
    public static function getAllCustomsFieldPriceType($prefix, int $idObject = 0, $formatValue = false)
    {
        $pperso = new \animations_pricetype_parametres_perso($idObject);
        return CustomFieldModel::formatField($pperso, $idObject, $formatValue);
    }

    public static function updateCustomFields($customFields, $idObject, $prefix)
    {
        $pperso = new \parametres_perso($prefix);
        self::setGlobalsCustomFields($customFields);
        $pperso->rec_fields_perso($idObject);
        self::unsetGlobalsCustomFields($customFields);
    }

    public static function updateCustomFieldsPriceType($customFields, $idObject, $numPriceType)
    {
        $pperso = new \animations_pricetype_parametres_perso($numPriceType);
        self::setGlobalsCustomFields($customFields);
        $pperso->rec_fields_perso($idObject);
        self::unsetGlobalsCustomFields($customFields);
    }

    public static function setGlobalsCustomFields($customFields)
    {
        foreach ($customFields as $field) {
            $name = $field->customField->name;
            switch ($field->customField->type) {
                case 'text':
                    $value = CustomFieldTextModel::getTextGlobalValue($field->customValues);
                    break;
                case 'list':
                    $value = CustomFieldListModel::getListGlobalValue($field->customValues);
                    break;
                case 'query_list':
                    $value = CustomFieldQueryListModel::getQueryListGlobalValue($field->customValues);
                    break;
                case 'query_auth':
                    $value = CustomFieldQueryAuthModel::getQueryAuthGlobalValue($field->customValues);
                    break;
                case 'date_box':
                    $value = CustomFieldDateBoxModel::getDateBoxGlobalValue($field->customValues);
                    break;
                case 'comment':
                    $value = CustomFieldCommentModel::getCommentGlobalValue($field->customValues);
                    break;
                case 'url':
                    $value = CustomFieldURLModel::getURLGlobalValue($field->customValues);
                    break;
                case 'marclist':
                    $value = CustomFieldMarclistModel::getMarclistGlobalValue($field->customValues);
                    break;
                case 'text_i18n':
                    $value = CustomFieldTextI18nModel::getTextI18nGlobalValue($field->customValues);
                    break;
                case 'q_txt_i18n':
                    $value = CustomFieldQualifiedTextI18nModel::getQualifiedTextI18nGlobalValue($field->customValues);
                    break;
                case 'date_inter':
                    $value = CustomFieldDateIntervalModel::getDateIntervalGlobalValue($field->customValues);
                    break;
                case 'date_flot':
                    $value = CustomFieldDateFlotModel::getDateFlotGlobalValue($field->customValues);
                    break;
                case 'resolve':
                    $value = CustomFieldResolveModel::getResolveGlobalValue($field->customValues);
                    break;
                case 'html':
                    $value = CustomFieldHTMLModel::getHTMLGlobalValue($field->customValues);
                    break;
                default:
                    $value = [];
                    break;
            }
            global ${$name};
            ${$name} = $value;
        }
    }

    public static function unsetGlobalsCustomFields($customFields)
    {
        foreach ($customFields as $field) {
            $name = $field->customField->name;
            global ${$name};
            unset(${$name});
        }
    }

    /**
     * Retourne la liste des champs perso formate
     *
     * @param \parametres_perso $pperso
     * @param int $idObject
     * @return array
     */
    protected static function formatField($pperso, int $idObject, $formatValue = false)
    {
        $i = 0;
        $customFieldsTab = [];

        foreach ($pperso->t_fields as $idCustomField => $customField) {

            $customField['ID'] = $idCustomField;
            $customField['VALUES'] = (isset($pperso->values[$idCustomField])) ? $pperso->values[$idCustomField] : [];

            if (! isset($customField['OPTIONS'][0]['FOR'])) {
                $i ++;
                continue;
            }

            switch ($customField['OPTIONS'][0]['FOR']) {
                case 'text':
                    $customFieldsTab[$i]['customField'] = CustomFieldTextModel::getTextInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldTextModel::findTextValues($customField);
                    break;
                case 'list':
                    $customFieldsTab[$i]['customField'] = CustomFieldListModel::getListInformations($customField, $pperso->prefix, $idCustomField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldListModel::findListValues($customField, $pperso->prefix, $idCustomField);
                    break;
                case 'query_list':
                    $customFieldsTab[$i]['customField'] = CustomFieldQueryListModel::getQueryListInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldQueryListModel::findQueryListValues($customField);
                    break;
                case 'query_auth':
                    $customFieldsTab[$i]['customField'] = CustomFieldQueryAuthModel::getQueryAuthInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldQueryAuthModel::findQueryAuthValues($customField);
                    break;
                case 'date_box':
                    $customFieldsTab[$i]['customField'] = CustomFieldDateBoxModel::getDateBoxInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldDateBoxModel::findDateBoxValues($customField);
                    break;
                case 'comment':
                    $customFieldsTab[$i]['customField'] = CustomFieldCommentModel::getCommentInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldCommentModel::findCommentValues($customField);
                    break;
                case 'url':
                    $customFieldsTab[$i]['customField'] = CustomFieldURLModel::getURLInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldURLModel::findURLValues($customField);
                    break;
                case 'marclist':
                    $customFieldsTab[$i]['customField'] = CustomFieldMarclistModel::getMarclistInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldMarclistModel::findMarclistValues($customField);
                    break;
                case 'text_i18n':
                    $customFieldsTab[$i]['customField'] = CustomFieldTextI18nModel::getTextI18nInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldTextI18nModel::findTextI18NValues($customField);
                    break;
                case 'q_txt_i18n':
                    $customFieldsTab[$i]['customField'] = CustomFieldQualifiedTextI18nModel::getQualifiedTextI18nInformations($customField, $pperso->prefix, $idCustomField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldQualifiedTextI18nModel::findQualifiedTextI18NValues($customField);
                    break;
                case 'date_inter':
                    $customFieldsTab[$i]['customField'] = CustomFieldDateIntervalModel::getDateIntervalInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldDateIntervalModel::findDateIntervalValues($customField);
                    break;
                case 'date_flot':
                    $customFieldsTab[$i]['customField'] = CustomFieldDateFlotModel::getDateFlotInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldDateFlotModel::findDateFlotValues($customField);
                    break;
                case 'resolve':
                    $customFieldsTab[$i]['customField'] = CustomFieldResolveModel::getResolveInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldResolveModel::findResolveValues($customField);
                    break;
                case 'html':
                    // TODO : Le v-model n'est pas fonctionnel, ainsi que la duplication !
                    $customFieldsTab[$i]['customField'] = CustomFieldHTMLModel::getHTMLInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldHTMLModel::findHTMLValues($customField);
                    break;
                case 'external':
                    // TODO : N'est pas fonctionnel !
                    $customFieldsTab[$i]['customField'] = CustomFieldValueModel::getExternalInformations($customField);
                    $customFieldsTab[$i]['customValues'] = CustomFieldValueModel::findExternalValues($customField, $pperso->prefix, $idCustomField, $idObject);
                    break;
                default:
                    break;
            }

            if ($formatValue) {
                $customFieldsTab[$i]['customFormatValues'] = CustomFieldModel::formatValues($customField, $customFieldsTab[$i], $pperso->prefix);
            }

            $i ++;
        }

        return $customFieldsTab;
    }

    /**
     * Retourne un tableau formate pour l'affichage
     *
     * @param array $customField
     * @param array $customFormatedField
     * @param string $prefix
     * @return array
     */
    public static function formatValues($customField, $customFormatedField, $prefix)
    {
        $result = [
            "label" => $customField['TITRE'],
            "id" => $customField['ID'],
            "values" => []
        ];

        foreach ($customField['VALUES'] as $value) {
            $order = 0;
            if (! empty($customFormatedField['customField']['listOrder']) && isset($customFormatedField['customField']['listOrder'][$value])) {
                $order = $customFormatedField['customField']['listOrder'][$value];
            }

            $result['values'][] = [
                "value" => $value,
                "format_value" => CustomFieldModel::getFormattedOutput($customField, $customFormatedField, $prefix),
                "order" => $order,
                "details" => CustomFieldModel::getDetails($customField, $value)
            ];
        }
        return $result;
    }

    /**
     * Permet de calculer le text  pour l'affichage
     *
     * @param array $customField
     * @param array $customFormatedField
     * @param string $prefix
     * @return string
     */
    protected static function getFormattedOutput($customField, $customFormatedField, $prefix)
    {
        global $val_list_empr;
        $function = $val_list_empr[$customField['TYPE']];

        $field = array();
        $field["ID"] = $customField['ID'];
        $field["NAME"] = $customField["NAME"];
        $field["COMMENT"] = $customField["COMMENT"];
        $field["MANDATORY"] = $customField["MANDATORY"];
        $field["OPAC_SORT"] = $customField["OPAC_SORT"];
        $field["ALIAS"] = $customField["TITRE"];
        $field["DATATYPE"] = $customField["DATATYPE"];
        $field["OPTIONS"] = $customField["OPTIONS"];
        $field["VALUES"] = $customField['VALUES'];
        $field["PREFIX"] = $prefix;

        $result = call_user_func($function, $field, $customField['VALUES']);
        if (!empty($result)) {
            if (is_array($result)) {
                return isset($result['value']) ? $result['value'] : $result['withoutHTML'];
            }
            return $result;
        }
        return "";
    }

    /**
     * Permet d'aller chercher les informations sur la valeur définis
     *
     * @param array $customField
     * @param mixed $value
     * @return mixed
     */
    protected static function getDetails($customField, $value)
    {
        if (!empty($customField["TYPE"]) && $customField["TYPE"] == "query_auth" && !empty($value)) {
            // On est en selection d'authorité, on retourne son instance
            return get_authority_details_from_field($customField, $value);
        }
        return "";
    }
}