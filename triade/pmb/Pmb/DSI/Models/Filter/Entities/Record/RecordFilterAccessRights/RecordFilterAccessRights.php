<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordFilterAccessRights.php,v 1.3.4.1 2025/05/21 08:03:11 rtigero Exp $

namespace Pmb\DSI\Models\Filter\Entities\Record\RecordFilterAccessRights;

use Pmb\DSI\Helper\SubscriberHelper;
use Pmb\DSI\Models\Filter\Entities\Record\RecordFilter;

class RecordFilterAccessRights extends RecordFilter
{

	public static $fields = [
		"access_role" => [
			"type" => "select",
			"required" => true,
			"options" => [],
			"ajax" => true
		]
	];

	public function __construct(array $data, int $entityId = 0)
	{
		parent::__construct($data, $entityId);
	}

	public function filter(): array
	{
		$filteredData = [];
		if (empty($this->fieldsValues->access_role)) {
			return $filteredData;
		}
		$ids = array_keys($this->data);
		$ac = new \acces();
		$dom_1 = $ac->setDomain(2);
		$rightsQuery = $dom_1->getFilterQueryByProfile(
			array(
				$this->fieldsValues->access_role
			),
			4,
			"notice_id",
			implode(',', $ids)
		);
		$result = pmb_mysql_query($rightsQuery);
		while ($row = pmb_mysql_fetch_assoc($result)) {
			$filteredData[$row["notice_id"]] = $this->data[$row["notice_id"]];
		}
		return $filteredData;
	}

	/**
	 * Retourne la liste des roles emprunteurs pour le selecteur
	 *
	 * @return array
	 */
	public static function getOptions()
	{
		return SubscriberHelper::get_empr_status();
	}

	public static function selfCheck()
	{
		global $gestion_acces_active, $gestion_acces_empr_notice;

		if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
			return true;
		}

		return false;
	}
}
