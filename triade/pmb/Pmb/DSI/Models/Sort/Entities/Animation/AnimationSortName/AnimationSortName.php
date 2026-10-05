<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationSortName.php,v 1.1.4.2 2025/05/21 08:03:12 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Animation\AnimationSortName;

use Pmb\DSI\Models\Sort\RootSort;

class AnimationSortName extends RootSort
{

	protected $field = "name";

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
