<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordDocnumThumbnailSource.php,v 1.9.2.2 2024/12/10 08:51:05 tsamson Exp $
namespace Pmb\Thumbnail\Models\Sources\Entities\Record\Docnum;

use Pmb\Thumbnail\Models\Sources\RootThumbnailSource;
use Pmb\Common\Models\CustomFieldModel;
use Pmb\Common\Helper\GlobalContext;

class RecordDocnumThumbnailSource extends RootThumbnailSource
{
    /**
     * champs perso autorises
     * @var array
     */
    protected const ALLOWED_CUSTOM_FIELDS = array(
        "list",
        "query_list",
        "query_auth"
    );
    
    /**
     * valeurs par defaut
     * @var array
     */
    protected const DEFAULT_VALUES = array(
        "active_found_first_thumbnail" => 1,
        "disable_custom_field_selection" => 0,
        "custom_field" => ""
    );

    /**
     * prefixe des champs perso
     * @var string
     */
    protected const PREFIX = "explnum";

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getImage()
     */
    public function getImage(int $object_id) : string
    {
        if (! $object_id) {
            return '';
        }

        if (! isset($this->settings['active_found_first_thumbnail'])) {
            $this->settings['active_found_first_thumbnail'] = 1;
        }
        if (! isset($this->settings['disable_custom_field_selection'])) {
            $this->settings['disable_custom_field_selection'] = '';
        }

        /**
         *
         * @var \record_datas $record
         */
        $record = \record_datas::get_instance($object_id);
        $explnumsDatas = $record->get_explnums_datas();
        if ($explnumsDatas['nb_explnums'] == 0) {
            return '';
        }

        if (intval($this->settings['disable_custom_field_selection']) == 1 && ! empty($this->settings['custom_field'])) {
            return $this->getDocnumThumbnailFromCustomField($object_id, $explnumsDatas);
        } elseif (intval($this->settings['active_found_first_thumbnail']) == 1) {
            return $this->getFirstDocnumThumbnail($object_id, $explnumsDatas);
        } else {
            return "";
        }
    }

    /**
     * Retrourne la vignette du premier document numerique de l'entite
     *
     * @param int $object_id
     * @param array $explnums_datas
     * @return string
     */
    private function getFirstDocnumThumbnail($object_id, $explnums_datas) : string
    {
        if (! empty($explnums_datas) && $explnums_datas["nb_explnums"] > 0) {
            for ($i = 0; $i < $explnums_datas["nb_explnums"]; $i ++) {
                $explnum = $explnums_datas['explnums'][$i];
                if (! empty($explnum) && \explnum::has_acces_vignette($explnum['id'], $object_id) && $explnum['has_vignette'] && ! empty($explnum['thumbnail_url'])) {
                    // 146841 : En attente d'une solution pour que le serveur puisse s'appele tout seul (certificat)
                    // return $this->loadImageWithCurl($explnum['thumbnail_url']);
                    $url = GlobalContext::get("pmb_url_internal") . "vig_num.php?explnum_id=" . $explnum['id'] ."&database=".LOCATION;
                    return $this->loadImageWithCurl($url);
                }
            }
        }
        return '';
    }

    /**
     * Retourne la vignette du document ayant un champ perso rempli comme le parametrage de la source demande
     *
     * @param int $object_id
     * @param array $explnums_datas
     * @return string
     */
    private function getDocnumThumbnailFromCustomField($object_id, $explnums_datas) : string
    {
        foreach ($explnums_datas['explnums'] as $explnum) {
            $customField = $this->getCustomFields($explnum['p_perso']);
            if (empty($customField)) {
                continue;
            }
            if (! $this->isMatchingCustomFieldValue($customField)) {
                continue;
            }
            if (\explnum::has_acces_vignette($explnum['id'], $object_id) && $explnum['has_vignette'] && ! empty($explnum['thumbnail_url'])) {
                //utilisation de l'url interne pour eviter l'authentification et ne pas bloquer curl
                $url = GlobalContext::get("pmb_url_internal") . "vig_num.php?explnum_id=" . $explnum['id'] ."&database=".LOCATION;
                return $this->loadImageWithCurl($url);
            }
        }
        return '';
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::getParameters()
     */
    public function getParameters() : array
    {
        if (empty($this->settings)) {
            $this->settings = self::DEFAULT_VALUES;
        }

        if (! isset($this->settings['active_found_first_thumbnail'])) {
            $this->settings['active_found_first_thumbnail'] = 1;
        }

        $this->settings['docnum_custom_fields'] = $this->getDocnumCustomFields();
        if (! empty($this->settings['custom_field'])) {
            foreach ($this->settings['docnum_custom_fields'] as $paramPerso) {
                if ($paramPerso['customField']['name'] == $this->settings['custom_field']) {
                    $this->settings['custom_field_value']['customField'] = $paramPerso['customField'];
                    break;
                }
            }
        }
        return $this->settings;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Sources\RootThumbnailSource::setParameters()
     */
    public function setParameters($settings) : void
    {
        $this->settings = [
            "active_found_first_thumbnail" => $settings->active_found_first_thumbnail ?? 1,
            "disable_custom_field_selection" => $settings->disable_custom_field_selection ?? 0,
            "custom_field" => $settings->custom_field ?? "",
            "custom_field_value" => [
                "customValues" => $settings->custom_field_value->customValues ?? []
            ]
        ];
    }

    /**
     * Retourne un booleen indiquant si la valeur du champ perso passee en parametre
     * est identique a la valeur enregistree dans les parametres
     *
     * @param array $pperso
     * @return boolean
     */
    private function isMatchingCustomFieldValue(array $pperso) : bool
    {
        $values = $this->settings['custom_field_value']['customValues'];
        foreach ($values as $value) {
            switch (gettype($value['value'])) {
                case "array":
                    if ((count(array_diff($value['value'], $pperso['VALUES'])) == 0) && count($value['value']) == count($pperso['VALUES'])) {
                        return true;
                    }
                    break;
                case "string":
                case "integer":
                    if (in_array($value['value'], $pperso['VALUES'])) {
                        return true;
                    }
                    break;
            }
        }
        return false;
    }

    /**
     * Retourne un tableau des champs perso de l'entite
     *
     * @return array
     */
    private function getDocnumCustomFields() : array
    {
        $customFields = array();
        $explnumCustomFields = CustomFieldModel::getAllCustomFields(self::PREFIX);

        if (! count($explnumCustomFields)) {
            return $customFields;
        }

        foreach ($explnumCustomFields as $field) {
            if (in_array($field['customField']['type'], self::ALLOWED_CUSTOM_FIELDS)) {
                $customFields[] = $field;
            }
        }
        return $customFields;
    }

    /**
     * Retourne le champ perso qui correspond aux donnees sauvegardees
     *
     * @param array|\parametres_perso $paramPerso
     * @return \parametres_perso|NULL
     */
    private function getCustomFields($paramPerso)
    {
        if ($paramPerso instanceof \parametres_perso) {
            foreach ($paramPerso->t_fields as $key => $field) {
                if ($field['NAME'] === $this->settings['custom_field'] && isset($paramPerso->values)) {
                    return [
                        'VALUES' => $paramPerso->values[$key] ?? array()
                    ];
                }
            }
        } else {
            return $paramPerso[$this->settings['custom_field']] ?? [
                'VALUES' => array()
            ];
        }
        return null;
    }
}