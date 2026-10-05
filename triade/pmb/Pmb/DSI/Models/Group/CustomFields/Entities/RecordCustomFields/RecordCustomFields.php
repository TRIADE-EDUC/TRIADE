<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordCustomFields.php,v 1.3 2023/11/21 14:19:35 rtigero Exp $
namespace Pmb\DSI\Models\Group\CustomFields\Entities\RecordCustomFields;

use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Group\CustomFields\CustomFieldsGroup;

class RecordCustomFields extends CustomFieldsGroup
{

	public const COMPONENT = "RecordCustomFields";

	public const PREFIX_PARAMETRES_PERSO = "notices";

	protected const CUSTOM_FIELD_CLASSES = [
		"txt_i18n" => "TextI18n",
		"q_txt_i18n" => "QualifiedTextI18n",
		"url" => "URL",
		"html" => "HTML",
		"date_inter" => "DateInterval"
	];

	protected const CUSTOM_FIELDS_MODEL_NAMESPACE = "Pmb\\Common\\Models\\CustomFieldTypes\\";

	/**
	 *
	 * @var \parametres_perso
	 */
	protected $parametresPerso = null;

	public function __construct(?object $settings = null)
	{
		parent::__construct($settings);
		$this->parametresPerso = new \parametres_perso(static::PREFIX_PARAMETRES_PERSO);
	}

	/**
	 * Retourne la liste des champs perso
	 *
	 * @param string $selectEmptyLabel
	 * @return array
	 */
	public function getCustomFields(string $selectEmptyLabel)
	{
		return $this->parametresPerso->get_field_list($selectEmptyLabel);
	}

	/**
	 * Permet de grouper les items
	 *
	 * @return array
	 */
	public function group()
	{
		$entitiesNotGrouped = array_map("intval", $this->entities);
		$groups = [];

		$fieldId = $this->getSetting('criteria', 0);
		if (! empty($fieldId)) {
			foreach ($entitiesNotGrouped as $id) {
				$this->parametresPerso->get_values($id);
				if (empty($this->parametresPerso->values[$fieldId])) {
					continue;
				}

				$values = $this->parametresPerso->values[$fieldId] ?? [];
				$group = $this->getGroupLabel($fieldId);

				if (null === $group) {
					continue;
				}

				if (! isset($groups[$group])) {
					$groups[$group] = [
						static::RESULT_KEY => [],
						"label" => $group
					];
				}
				$optionType = $this->parametresPerso::$st_fields[static::PREFIX_PARAMETRES_PERSO][$fieldId]['TYPE'];
				//Ici pour gerer les cas un peu particuliers des cp:
				switch ($optionType) {
					case "list":
						$requete = "select " . static::PREFIX_PARAMETRES_PERSO . "_custom_list_value, " . static::PREFIX_PARAMETRES_PERSO . "_custom_list_lib, ordre from " . static::PREFIX_PARAMETRES_PERSO . "_custom_lists where " . static::PREFIX_PARAMETRES_PERSO . "_custom_champ=$fieldId order by ordre";
						$result = pmb_mysql_query($requete);
						while ($row = pmb_mysql_fetch_assoc($result)) {
							if ($row[static::PREFIX_PARAMETRES_PERSO . "_custom_list_value"] == $group) {
								$groups[$group]['label'] = $row[static::PREFIX_PARAMETRES_PERSO . "_custom_list_lib"];
								break;
							}
						}
						break;
					default:
						break;
				}
				$groups[$group][static::RESULT_KEY][] = $id;
				$entitiesNotGrouped = array_filter($entitiesNotGrouped, function ($entityId) use ($id) {
					return $entityId != $id;
				});
			}
		}

		if (! empty($entitiesNotGrouped)) {
			$groups[static::EMPTY_GROUP_KEY] = [
				static::RESULT_KEY => $entitiesNotGrouped
			];
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
	 * Récupère le label du groupe pour un champ donné.
	 *
	 * @param int $fieldId
	 *        	L'ID du champ perso.
	 * @return string Le label du groupe pour le champ.
	 */
	protected function getGroupLabel($fieldId)
	{
		global $pmb_perso_sep;

		$label = "";
		$field = $this->parametresPerso::$fields[$this->parametresPerso->prefix][$fieldId];
		$type = $this->parametresPerso::$st_fields[$this->parametresPerso->prefix][$fieldId]["TYPE"];

		if (empty($type) || empty($field) || empty($type)) {
			return $label;
		}

		if (! empty(static::CUSTOM_FIELD_CLASSES[$type])) {
			$customFieldType = static::CUSTOM_FIELD_CLASSES[$type];
		} else {
			$customFieldType = ucfirst(Helper::camelize($type));
		}

		$classname = "CustomField" . $customFieldType . "Model";
		$methodName = "find" . $customFieldType . "Values";
		$calledClass = static::CUSTOM_FIELDS_MODEL_NAMESPACE . $classname;

		if (class_exists($calledClass) && method_exists($calledClass, $methodName)) {
			//Exception pour les listes
			if ($type == "list") {
				$result = call_user_func([
					$calledClass,
					$methodName
				], $field, $this->parametresPerso->prefix, $fieldId);
			} else {
				$result = call_user_func([
					$calledClass,
					$methodName
				], $field);
			}
		}

		switch ($type) {
			case "date_flot":
				foreach ($result as $value) {
					$label .= "{$value["value"]} {$value["value1"]} ({$value["comment"]}) $pmb_perso_sep ";
				}
				break;
			case "date_inter":
				foreach ($result as $value) {
					$label .= "{$value["value"]} {$value["valueTime"]} - {$value["value1"]} {$value["value1Time"]} $pmb_perso_sep ";
				}
				break;
			case "list":
			case "query_list":
			case "query_auth":
				foreach ($result as $value) {
					if (! empty($value["displayLabel"])) {
						$label .= $value["displayLabel"] . "$pmb_perso_sep ";
					} else {
						$label .= $value["value"] . "$pmb_perso_sep ";
					}
				}
				break;
			case "marclist":
				$list = \marc_list_collection::get_instance($field['OPTIONS'][0]['DATA_TYPE'][0]['value']);
				foreach ($result as $value) {
					if (! empty($list->table[$value["value"]])) {
						$label .= $list->table[$value["value"]] . "$pmb_perso_sep ";
					}
				}
				break;
			case "txt_i18n":
			case "q_txt_i18n":
				foreach ($result as $value) {
					if (! empty($value["qualifiedValue"])) {
						$label .= "[{$value["qualifiedValue"]}] ";
					}
					$label .= "{$value["value"]} ({$value["displayLang"]}) $pmb_perso_sep ";
				}
				break;
			default:
				foreach ($result as $value) {
					$label .= $value["value"] . "$pmb_perso_sep ";
				}
				break;
		}
		return substr($label, 0, - (strlen($pmb_perso_sep) + 1));
	}
}