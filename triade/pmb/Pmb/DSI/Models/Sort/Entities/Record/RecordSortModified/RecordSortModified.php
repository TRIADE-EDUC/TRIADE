<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordSortModified.php,v 1.1.4.2 2025/05/21 09:06:39 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Record\RecordSortModified;

use Pmb\DSI\Models\Sort\RootSort;

class RecordSortModified extends RootSort
{

	protected $direction;

	protected $field = "update_date";

	protected $fieldType = "datetime";

	public function __construct($data = null)
	{
		$this->type = static::TYPE_QUERY;
		if (in_array($data->direction, static::DIRECTIONS)) {
			$this->direction = $data->direction;
		}
	}
}
