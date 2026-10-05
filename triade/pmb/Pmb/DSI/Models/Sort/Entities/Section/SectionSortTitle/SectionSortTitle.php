<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SectionSortTitle.php,v 1.1.4.2 2025/03/26 08:26:02 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Section\SectionSortTitle;

use Pmb\DSI\Models\Sort\RootSort;

class SectionSortTitle extends RootSort
{

	protected $field = "section_title";

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