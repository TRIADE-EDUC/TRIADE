<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordSortTypdoc.php,v 1.2 2023/11/10 13:49:06 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Record\RecordSortTypdoc;

use Pmb\DSI\Models\Sort\RootSort;

class RecordSortTypdoc extends RootSort
{

	protected $field = "typdoc";

	protected $fieldType = "string";

	protected $direction;

	public function __construct($data = null)
	{
		$this->type = static::TYPE_QUERY;
		if (in_array($data->direction, static::DIRECTIONS)) {
			$this->direction = $data->direction;
		}
	}
}