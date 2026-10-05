<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SectionSortOrder.php,v 1.1.4.2 2025/03/26 08:26:01 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Section\SectionSortOrder;

use Pmb\DSI\Models\Sort\RootSort;

class SectionSortOrder extends RootSort
{

	protected $field = "section_order";

	protected $fieldType = "integer";

	protected $direction;

	public function __construct($data = null)
	{
		$this->type = static::TYPE_QUERY;
		if (in_array($data->direction, static::DIRECTIONS)) {
			$this->direction = $data->direction;
		}
	}
}