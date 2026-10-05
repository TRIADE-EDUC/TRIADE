<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordSortPublicationYear.php,v 1.3 2024/03/18 13:30:29 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Record\RecordSortPublicationYear;

use Pmb\DSI\Models\Sort\RootSort;

class RecordSortPublicationYear extends RootSort
{

	protected $field = "year";

	protected $fieldType = "integer";

	protected $direction;

	public function __construct($data = null)
	{
		$this->type = static::TYPE_QUERY;
		if (isset($data->direction) && in_array($data->direction, static::DIRECTIONS)) {
			$this->direction = $data->direction;
		}
	}
}
