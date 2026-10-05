<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordSortCustomField.php,v 1.2.4.1 2025/05/21 10:24:04 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Record\RecordSortCustomField;

use Pmb\DSI\Models\Sort\RootSort;

class RecordSortCustomField extends RootSort
{

	public static $fields = [
		"customField" => [
			"type" => "select",
			"required" => true,
			"options" => [],
			"callback" => "getOptions"
		]
	];

	protected $direction;

	protected $customField;

	protected $field = "notices_custom_values.notices_custom_";

	public function __construct($data = null)
	{
		$this->type = static::TYPE_QUERY;
		if (in_array($data->direction, static::DIRECTIONS)) {
			$this->direction = $data->direction;
		}
		if (! empty($data->customField)) {
			$customField = new \parametres_perso("notices");
			if (! empty($customField->t_fields[$data->customField])) {
				$this->customField = $data->customField;
				$this->field .= $customField->t_fields[$this->customField]['DATATYPE'];
			}
		}
	}

	public static function getOptionsCustomField(&$field)
	{
		$pp = new \parametres_perso("notices");
		foreach ($pp->t_fields as $id => $t_field) {
			$field["options"][] = [
				"value" => $id,
				"label" => $t_field["TITRE"] ?? $t_field["NAME"]
			];
		}
	}

	public function getJoinClause()
	{
		if (! empty($this->customField)) {
			return "LEFT JOIN notices_custom_values ON notices_custom_values.notices_custom_origine = notices.notice_id AND notices_custom_champ = '" . $this->customField . "'";
		}
		return "";
	}
}
