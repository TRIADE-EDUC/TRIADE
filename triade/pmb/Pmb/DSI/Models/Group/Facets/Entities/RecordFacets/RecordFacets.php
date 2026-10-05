<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordFacets.php,v 1.3 2024/03/15 14:10:23 rtigero Exp $

namespace Pmb\DSI\Models\Group\Facets\Entities\RecordFacets;

use Pmb\DSI\Models\Group\Facets\FacetsGroup;

class RecordFacets extends FacetsGroup
{

    public const COMPONENT = "RecordFacets";

    protected $fields = null;

    /**
     * Recupere et formate les champs base pour le formulaire
     *
     * @return array
     */
    public function getFields()
    {
        global $msg;

        $this->fetchFields();
        $fields = $this->formatFields($this->fields['FIELD']);

        // On tris les label par ordre alpha
        usort($fields, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $fields;
    }

    /**
     * Permet de recuperer les champs base
     *
     * @return boolean
     */
    protected function fetchFields()
    {
        global $include_path;

        if (isset($this->fields)) {
            return true;
        }

        $file = $include_path . "/indexation/notices/champs_base_subst.xml";
        if (!is_file($file)) {
            $file = $include_path . "/indexation/notices/champs_base.xml";
        }

        $ressource = fopen($file, "r");
        if ($ressource) {
            $xml = fread($ressource, filesize($file));
            fclose($ressource);

            $this->fields = _parser_text_no_function_($xml, "INDEXATION", $file);
            return true;
        }
        return false;
    }

    protected function getSubFields(int $fieldID)
    {
        global $msg;

        $this->fetchFields();
        $subFields = [];

        $key = array_search($fieldID, array_column($this->fields['FIELD'], 'ID'));
        if (false === $key) {
            return $subFields;
        }

        if (isset($this->fields['FIELD'][$key]['TABLE'][0]['TABLEFIELD'])) {
            $subFields = array_merge(
                $subFields,
                $this->formatFields($this->fields['FIELD'][$key]['TABLE'][0]['TABLEFIELD'])
            );
        }

        if (isset($this->fields['FIELD'][$key]['CALLABLE'])) {
            $subFields = array_merge(
                $subFields,
                $this->formatFields($this->fields['FIELD'][$key]['CALLABLE'])
            );
        }

        if (isset($this->fields['FIELD'][$key]['ISBD'])) {
            $id = intval($this->fields['FIELD'][$key]['ISBD']['ID'] ?? 0);
            if ($id) {
                $subFields[] = [
                    "value" => $id,
                    "label" => $msg['facette_isbd'],
                    "subFields" => $this->getSubFields($id)
                ];
            }
        }

        return $subFields;
    }

    /**
     * Permet de formater les champs base
     *
     * @param array $subFields
     * @return array
     */
    protected function formatFields($fields)
    {
        global $msg;

        $result = [];
        foreach ($fields as $field) {
            $id = intval($field['ID'] ?? 0);
            $label = isset($field['NAME']) ? ($msg[$field['NAME']] ?? $field['NAME']) : false;

            if ($id && $label) {
                $result[] = [
                    "value" => $id,
                    "label" => $label,
                    "subFields" => $this->getSubFields($id)
                ];
            }
        }

        return $result;
    }

    /**
     * Permet de grouper les items
     *
     * @return array
     */
    public function group()
    {
        $query = $this->buildQuery();
        $entitiesNotGrouped = $this->entities;
        $groups = [];

        if ($query !== false) {
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    if (!isset($groups[$row['group']])) {
                        $groups[$row['group']] = [
                            static::RESULT_KEY => []
                        ];
                    }

                    $id = intval($row['id']);
                    $groups[$row['group']][static::RESULT_KEY][] = $id;
                    $entitiesNotGrouped = array_filter($entitiesNotGrouped, function ($entityId) use ($id) {
                        return $entityId != $id;
                    });
                }
            }
        }

        if (!empty($entitiesNotGrouped)) {
            $groups[static::EMPTY_GROUP_KEY] = [static::RESULT_KEY => $entitiesNotGrouped];
        }

        if (isset($this->subGroup)) {
            foreach ($groups as $group => $result) {
                $this->subGroup->addItems($this->entityType, $result[static::RESULT_KEY]);
                $groups[$group] = $this->subGroup->group();
            }
        }

        return $groups;
    }

    /**
     * Construit la requete sql
     *
     * @return string|boolean
     */
    protected function buildQuery()
    {
        global $lang, $msg, $dsi_bannette_notices_order;

        if (empty($this->entities)) {
            return false;
        }

        $ids = array_map("intval", $this->entities);
        $ids = implode(',', $ids);

        $sort = $this->getSetting('sort', FacetsGroup::SORT_ALPHA);
        $order = $this->getSetting('order', FacetsGroup::ORDER_DESC);

        $criteria = $this->getSetting('criteria', false);
        if ($criteria === false) {
            return false;
        }

        $critere = $criteria[0] ?? "";
        $ssCritere = $criteria[1] ?? "";

        switch ($sort) {
            case FacetsGroup::SORT_DATE:
                $queryOrder = 'ORDER BY STR_TO_DATE(value, "' . $msg['format_date'] . '")';
                break;

            case FacetsGroup::SORT_INTEGER:
                $queryOrder = 'ORDER BY value*1';
                break;

            case FacetsGroup::SORT_ALPHA:
            default:
                $queryOrder = 'ORDER BY value';
                break;
        }

        if ($order === FacetsGroup::ORDER_ASC) {
            $queryOrder .= " asc";
        } else {
            $queryOrder .= " desc";
        }

        if ($dsi_bannette_notices_order) {
            $query = "SELECT value AS 'group', id_notice AS 'id' FROM notices_fields_global_index
                LEFT JOIN notices on (id_notice=notice_id)
			    WHERE id_notice IN ({$ids})
                AND code_champ = '{$critere}'
                AND code_ss_champ = '{$ssCritere}'
                AND lang in ('', '{$lang}')
                {$queryOrder}, {$dsi_bannette_notices_order}";
        } else {
            $query = "SELECT value AS 'group', id_notice AS 'id' FROM notices_fields_global_index
                WHERE id_notice IN ({$ids})
                AND code_champ = '{$critere}'
                AND code_ss_champ = '{$ssCritere}'
                AND lang in ('', '{$lang}')
                {$queryOrder}";
        }

        return $query;
    }
}
