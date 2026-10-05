<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: HarvestModel.php,v 1.13 2024/01/09 08:15:40 qvarin Exp $

namespace Pmb\Harvest\Models;

use search;
use parametres_perso;
use connecteurs;

class HarvestModel
{
    protected const HARVEST_XML_FILE = "/harvest/harvest_fields.xml";

    /* Tableau des groupes de champs */
    protected static $groups = null;

    protected $sources = null;

    protected $searchFields = null;

    protected $searchFieldIds = [];

    protected $formatedNoticeCustomFields = null;

    protected $noticeCustomFields = null;

    protected $id = 0;

    public function __construct(int $id = 0)
    {
        $this->id = intval($id);
        $this->getGroups();
    }

    /**
     * Récupère la liste complète des groupes de champs triée par id croissant
     *
     * @return array
     */
    public function getGroups()
    {
        global $include_path, $msg;
		if (! is_null(static::$groups)) {
            return static::$groups;
        }
        static::$groups = [];
        $nomfichier = $include_path . static::HARVEST_XML_FILE;

        if (file_exists($nomfichier)) {
            $fp = fopen($nomfichier, "r");
            if ($fp) {
                $xml = fread($fp, filesize($nomfichier));
                fclose($fp);
                $param = _parser_text_no_function_($xml, "HARVEST");
                $groups = $param["FIELD"];
            }
        }

        foreach ($groups as $group) {
            static::$groups[$group["UNIMARCFIELD"]] = [
                "id" => $group["ID"],
                "name" => $msg[$group["NAME"]],
                "ufield" => $group["UNIMARCFIELD"],
                "repeatable" => $group["REPEATABLE"]
            ];
        }

        foreach ($this->formatNoticeCustomFields() as $customField) {
            static::$groups[$customField["ufield"]] = $customField;
        }

        uasort(static::$groups, function ($a, $b) {
            return $a["id"] > $b["id"];
        });

        return static::$groups;
    }

    /**
     * Récupère les champs d'un profil d'import pour chaque groupe
     *
     * @param array $profile
     * @return void|false Retourne false si aucun identifiant
     */
    protected function fetchProfileFieldsForeachGroup(array &$profile)
    {
        if (!$this->id) {
            return false;
        }

        if (empty($profile['groups'])) {
            $profile['groups'] = $this->getGroups();
        }

        $fieldsQuery = "SELECT harvest_profil_name, harvest_field_first_flag, harvest_field_order,
            harvest_field_ufield, harvest_src_ufield, num_source, harvest_src_prec_flag, harvest_src_order, harvest_field_xml_id
            FROM harvest_src
            JOIN harvest_field ON num_harvest_field = id_harvest_field
            JOIN harvest_profil ON id_harvest_profil = num_harvest_profil
            WHERE id_harvest_profil = " . intval($this->id) . "
            ORDER BY harvest_field_xml_id, harvest_src_order ASC";

        $fieldsResult = pmb_mysql_query($fieldsQuery);
        while ($field = pmb_mysql_fetch_assoc($fieldsResult)) {
            if (empty($profile['name'])) {
                $profile['name'] = $field['harvest_profil_name'];
            }

            $currentGroup = &$profile["groups"][$field['harvest_field_ufield']] ?? null;
            if (null === $currentGroup) {
                // Groupe inexistant ou supprime dans le xml 'harvest_fields.xml'
                continue;
            }

            // Définition du flag associé au premier champ
            if (!isset($currentGroup["firstFlag"])) {
                $currentGroup["firstFlag"] = $field["harvest_field_first_flag"];
            }

            // Définition de l'ordre associé au premier champ
            if (!isset($currentGroup["order"])) {
                $currentGroup["order"] = $field["harvest_field_order"];
            }

            //Récupération des données du champ
            if (!isset($currentGroup['fields'])) {
                $currentGroup['fields'] = [];
            }
            $currentGroup["fields"][] = [
                "ufield" => $field["harvest_src_ufield"],
                "source" => $field["num_source"],
                "precFlag" => $field["harvest_src_prec_flag"],
                "order" => $field["harvest_src_order"]
            ];
        }
    }

    /**
     * Retourne un profil
     *
     * @return array Profil
     */
    public function getProfile()
    {
        $profile = array();
        $profile["id"] = $this->id;
        $profile["name"] = "";
        $profile["sources"] = $this->getSearchFieldsBySource();
        $profile["groups"] = static::$groups;

        // Recupèration des champs d'un profil d'import pour chaque groupe
        $this->fetchProfileFieldsForeachGroup($profile);

        // Définition des champs obligatoires pour chaque groupe
        $order = 0;
        foreach ($profile["groups"] as $ufield => $group) {
            if (empty($group['fields'])) {
                $profile["groups"][$ufield]['fields'] = [
                    $this->getEmptyField($ufield)
                ];
            }
            if (!isset($group['firstFlag'])) {
                $profile["groups"][$ufield]['firstFlag'] = 0;
            }
            if (!isset($group['order'])) {
                $profile["groups"][$ufield]['order'] = $order;
            }
            $order++;
        }

        return $profile;
    }

    /**
     * Retourne un champ vide formaté
     *
     * @param string $ufield
     * @return array
     */
    private function getEmptyField($ufield)
    {
        return [
            "ufield" => $ufield,
            "source" => 0,
            "precFlag" => 0,
            "order" => 0
        ];
    }

    /**
     * Récupère la liste des sources
     *
     * @return array
     */
    protected function getSources()
    {
        if (!is_null($this->sources)) {
            return $this->sources;
        }
        $sourcesData = array();
        $connectors = connecteurs::get_instance();
        $sources = $connectors->getSearchableSourceList();
        if ($this->id) {
            $query = "SELECT num_source, num_field FROM harvest_search_field WHERE num_harvest_profil = " . intval($this->id);
            $result = pmb_mysql_query($query);
            $sourcesData = pmb_mysql_fetch_all($result);
        }
        foreach ($sources as $source) {
            $foundKey = array_search($source["source_id"], array_column($sourcesData, 0));
            $this->sources[] = [
                "name" => $source["name"],
                "id" => $source["source_id"],
                "idConnector" => $source["id_connector"],
                "field" => $foundKey !== false ? $sourcesData[$foundKey][1] : 0
            ];
        }
        return $this->sources;
    }

    /**
     * Retourne un profil d'import
     *
     * @return array Profil
     */
    public function getImportProfile()
    {
        $profile = array();
        $profile["name"] = "";
        $profile["groups"] = static::$groups;

        $i = 0;
        foreach ($profile["groups"] as $ufield => $group) {
            $profile["groups"][$ufield]["flag"] = 0;
            $profile["groups"][$ufield]["order"] = $i;
            $i++;
        }

        if ($this->id) {
            $query =
                "SELECT harvest_profil_import_name, harvest_profil_import_field_xml_id, harvest_profil_import_field_flag, harvest_profil_import_field_order
                FROM harvest_profil_import
                JOIN harvest_profil_import_field ON id_harvest_profil_import = num_harvest_profil_import
                WHERE id_harvest_profil_import = " . intval($this->id) . "
                ORDER BY harvest_profil_import_field_order";
            $result = pmb_mysql_query($query);
            while ($row = pmb_mysql_fetch_assoc($result)) {
                if (empty($profile["name"])) {
                    $profile["name"] = $row["harvest_profil_import_name"];
                }

                $group = $this->getGroup($row["harvest_profil_import_field_xml_id"]);
                $group["flag"] = $row["harvest_profil_import_field_flag"];
                $group["order"] = $row["harvest_profil_import_field_order"];
                $profile["groups"][$group["ufield"]] = $group;
            }
            $profile["id"] = $this->id;
        }

        return $profile;
    }

    /**
     * Retourne la liste des profils
     *
     * @return array liste des profils
     */
    public function getProfiles()
    {
        $result = pmb_mysql_query("SELECT * FROM harvest_profil");
        $profiles = array();
        while ($row = pmb_mysql_fetch_assoc($result)) {
            $profiles[] = [
                "id" => $row["id_harvest_profil"],
                "name" => $row["harvest_profil_name"]
            ];
        }
        return $profiles;
    }

    /**
     * Retourne la liste des profils d'import
     *
     * @return array liste des profils d'import
     */
    public function getImportProfiles()
    {
        $result = pmb_mysql_query("SELECT * FROM harvest_profil_import");
        $profiles = array();
        while ($row = pmb_mysql_fetch_assoc($result)) {
            $profiles[] = [
                "id" => $row["id_harvest_profil_import"],
                "name" => $row["harvest_profil_import_name"]
            ];
        }
        return $profiles;
    }

    /**
     * Sauvegarde un profil
     *
     * @param \stdClass $data
     *        	Données du formulaire
     * @return int ID du profil
     */
    public function saveProfile($data)
    {
        //MAJ de harvest_profil
        if ($this->id != 0) {
            //On met à jour le nom
            $query = "UPDATE harvest_profil
                SET harvest_profil_name = '" . addslashes($data->name) . "'
                WHERE id_harvest_profil = " . intval($this->id);
            pmb_mysql_query($query);
            //Reset des données
            $query = "DELETE harvest_src, harvest_field
                FROM harvest_src JOIN harvest_field ON num_harvest_field = id_harvest_field
                WHERE harvest_field.num_harvest_profil = " . intval($this->id);
            pmb_mysql_query($query);
        } else {
            $query = "INSERT INTO harvest_profil (harvest_profil_name) VALUES ('". addslashes($data->name) ."')";
            pmb_mysql_query($query);
            $this->id = pmb_mysql_insert_id();
        }

        //MAJ de harvest_field
        foreach ($data->groups as $group) {

            if (empty($group->id)) {
                // Groupe sans identifiant, il a ete supprimer dans le xml 'harvest_fields.xml'
                continue;
            }

            $query = "INSERT INTO harvest_field (num_harvest_profil, harvest_field_xml_id, harvest_field_ufield, harvest_field_first_flag, harvest_field_order)
                VALUES (". intval($this->id) . ", ". intval($group->id) . ", '". addslashes($group->ufield) ."', " . intval($group->firstFlag) . ", ". intval($group->order) .")";
            pmb_mysql_query($query);
            $harvestFieldId = intval(pmb_mysql_insert_id());
            if ($harvestFieldId) {
                //MAJ de harvest_src
                $query = "INSERT INTO harvest_src (num_harvest_field, num_source, harvest_src_ufield, harvest_src_prec_flag, harvest_src_order) VALUES ";
                $insertClause = "";

                $order = 0;
                foreach ($group->fields as $field) {
                    if (empty($field->source)) {
                        continue;
                    }
                    $insertClause .= "({$harvestFieldId}, '" . addslashes($field->source) ."', '" . addslashes($field->ufield) ."', " . intval($field->precFlag) . ", " . intval($order) . "),";
                    $order++;
                }

                if (!empty($insertClause)) {
                    $query .= substr($insertClause, 0, -1);
                    pmb_mysql_query($query);
                }
            }
        }

        //MAJ de harvest_search_field
        $query = "DELETE FROM harvest_search_field WHERE num_harvest_profil = " . intval($this->id);
        pmb_mysql_query($query);

        $query = "INSERT INTO harvest_search_field (num_harvest_profil, num_source, num_field) VALUES ";
        $insertClause = "";
        foreach ($data->sources as $source) {
            if ($source->field) {
                $insertClause .= "(" . intval($this->id) . ", " . intval($source->id) . ", '" . addslashes($source->field) . "'),";
            }
        }
        if (!empty($insertClause)) {
            $query .= substr($insertClause, 0, -1);
            pmb_mysql_query($query);
        }
        return $this->id;
    }

    /**
     * Sauvegarde un profil d'import
     *
     * @param \stdClass $data
     *        	Données du formulaire
     * @return int ID du profil d'import
     */
    public function saveImportProfile($data)
    {
        if ($this->id != 0) {
            //On reset les champs
            $query = "DELETE FROM harvest_profil_import_field WHERE num_harvest_profil_import= " . intval($this->id);
            pmb_mysql_query($query);

            //On met à jour le nom
            $query = "UPDATE harvest_profil_import SET harvest_profil_import_name = '" . addslashes($data->name) . "' WHERE id_harvest_profil_import = " . intval($this->id);
            pmb_mysql_query($query);
        } else {
            $query = "INSERT INTO harvest_profil_import (harvest_profil_import_name) VALUES ('" . addslashes($data->name) . "')";
            pmb_mysql_query($query);
            $this->id = intval(pmb_mysql_insert_id());
        }

        $query = "INSERT INTO harvest_profil_import_field VALUES ";
        $insertClause = "";
        foreach ($data->groups as $group) {
            $insertClause .= "(". intval($this->id) .", ". intval($group->id) .", ". intval($group->flag) .", ". intval($group->order) ."),";
        }
        $query .= substr($insertClause, 0, -1);
        pmb_mysql_query($query);

        return $this->id;
    }

    /**
     * Supprime un profil et toutes les données associées
     *
     * @return bool
     */
    public function deleteProfile()
    {
        $query = "DELETE harvest_profil, harvest_field, harvest_src, harvest_search_field
            FROM harvest_profil
            LEFT JOIN harvest_search_field ON harvest_search_field.num_harvest_profil = harvest_profil.id_harvest_profil
            LEFT JOIN harvest_field ON harvest_profil.id_harvest_profil = harvest_field.num_harvest_profil
            LEFT JOIN harvest_src ON harvest_field.id_harvest_field = harvest_src.num_harvest_field
            WHERE id_harvest_profil = " . intval($this->id);
        return pmb_mysql_query($query);
    }

    /**
     * Supprime un profil d'import et toutes les données associées
     *
     * @return bool
     */
    public function deleteImportProfile()
    {
        $query = "DELETE harvest_profil_import, harvest_profil_import_field
            FROM harvest_profil_import
            LEFT JOIN harvest_profil_import_field ON harvest_profil_import_field.num_harvest_profil_import = harvest_profil_import.id_harvest_profil_import
            WHERE harvest_profil_import.id_harvest_profil_import = " . intval($this->id);
        return pmb_mysql_query($query);
    }

    /**
     * Recupere la liste des champs de recherche externe
     *
     * @return []
     */
    protected function getSearchFields()
    {
        if (!is_null($this->searchFields)) {
            return $this->searchFields;
        }

        $sc = new search(false, "search_fields_unimarc");

        $this->searchFields = [];
        $this->searchFieldIds = [];
        foreach ($sc->fixedfields as $fixed_field) {
            if (empty($fixed_field["UNIMARCFIELD"]) || ('FORBIDDEN' == $fixed_field["UNIMARCFIELD"]) || (! $fixed_field["VISIBLE"])) {
                continue;
            }
            $key = $fixed_field["UNIMARCFIELD"];
            $id = $fixed_field["ID"];
            $this->searchFields[$key] = $fixed_field["TITLE"];
            $this->searchFieldIds[$key] = $id;
        }

        return $this->searchFields;
    }

    /**
     * Format la liste des champs persos de notice
     *
     * @return []
     */
    protected function formatNoticeCustomFields()
    {
        if (!is_null($this->formatedNoticeCustomFields)) {
        	return $this->formatedNoticeCustomFields;
        }
        $this->formatedNoticeCustomFields = [];

        foreach ($this->getNoticeCustomFields() as $id => $t_field) {
            if ($t_field['EXPORT'] == '1') {
            	$options = array_shift($t_field['OPTIONS']) ?? [];

            	$isRepeatable = false;
            	if ($options['MULTIPLE'][0]['value'] == 'yes' || $options['REPEATABLE'][0]['value'] == '1') {
            		$isRepeatable = true;
            	}

                $this->formatedNoticeCustomFields[] = [
                    'id' => 900 + intval($id),
                    'name' => $t_field['TITRE'],
                    'ufield' => '900$' . $id,
                	'repeatable' => $isRepeatable ? 'yes' : 'no',
                ];
            }
        }
        return $this->formatedNoticeCustomFields;
    }

    /**
     * Recupere la liste des champs persos de notice
     *
     * @return []
     */
    public function getNoticeCustomFields()
    {
    	if (!is_null($this->noticeCustomFields)) {
    		return $this->noticeCustomFields;
    	}

    	$this->noticeCustomFields = [];
    	$pp = new parametres_perso('notices');
    	$this->noticeCustomFields = $pp->t_fields;

    	return $this->noticeCustomFields;
    }

    /**
     * Recupere la liste des champs interrogeables par source
     *
     * @return []
     */
    public function getSearchFieldsBySource()
    {
        global $base_path;

        if (!is_null($this->sources)) {
            return $this->sources;
        }
        $this->getSources();
        $this->getSearchFields();

        $unimarc_search_fields = array();
        foreach ($this->searchFields as $key => $value) {
            $unimarc_search_fields[] = [
                "value" => $key,
                "label" => $value
            ];
        }
        $already_seen_classes = [];
        foreach ($this->sources as $k => $source) {
            $class = $source['idConnector'];
            $class_filepath = $base_path . '/admin/connecteurs/in/' . $class . '/' . $class . '.class.php';
            if (array_key_exists($class, $already_seen_classes)) {
                $this->sources[$k]['searchFields'] = $already_seen_classes[$class];
                continue;
            }
            // Recuperation champs de recherche definis pour la source
            if (file_exists($class_filepath)) {
                require_once $class_filepath;
                $source_search_fields = $unimarc_search_fields;
                $specific_unimarc_search_fields = $class::getSpecificUnimarcSearchFields();
                if (!empty($specific_unimarc_search_fields)) {
                    $source_search_fields = $specific_unimarc_search_fields;

                    // Elimination des champs de recherche de la source non pris en charge
                    for ($i = count($source_search_fields); $i >= 0; $i--) {
                        if (!array_key_exists($source_search_fields[$i], $this->searchFields)) {
                            unset($source_search_fields[$i]);
                        } else {
                            $source_search_fields[$i] = [
                                "value" => $source_search_fields[$i],
                                "label" => $this->searchFields[$source_search_fields[$i]]
                            ];
                        }
                    }
                }

                $this->sources[$k]['searchFields'] = $source_search_fields;
                $already_seen_classes[$class] = $source_search_fields;
            }
        }
        return $this->sources;
    }

    /**
     * Retourne infos source a partir identifiant
     *
     * @param number $sourceId : id source
     * @return array
     */
    public function getSourceById($sourceId = 0)
    {
        foreach ($this->sources as $source) {
            if ($source['id'] == $sourceId) {
                return $source;
            }
        }
        return [];
    }



    /**
     * Retourne un groupe en fonction de l'identifiant passe en parametre
     * @param int $groupId identifiant du groupe
     * @return array | null
     */
    public function getGroup($groupId = 0)
    {
        $this->getGroups();
        foreach (static::$groups as $group) {
            if ($group["id"] == $groupId) {
                return $group;
            }
        }
        return null;
    }


    /**
     * Retourne un tableau unimarcfield => id champ xml
     *
     * @return array
     */
    public function getSearchFieldIds()
    {
        $this->getSearchFields();
        return $this->searchFieldIds;
    }
}
