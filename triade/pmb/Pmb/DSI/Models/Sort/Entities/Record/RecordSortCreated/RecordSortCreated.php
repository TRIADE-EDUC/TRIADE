<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordSortCreated.php,v 1.1.4.2 2025/05/21 09:06:39 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Record\RecordSortCreated;

use Pmb\DSI\Models\Sort\RootSort;

class RecordSortCreated extends RootSort
{

	protected $direction;

	protected $field = "create_date";

	protected $fieldType = "datetime";

	public function __construct($data = null)
	{
		$this->type = static::TYPE_QUERY;
		if (in_array($data->direction, static::DIRECTIONS)) {
			$this->direction = $data->direction;
		}
	}
}
