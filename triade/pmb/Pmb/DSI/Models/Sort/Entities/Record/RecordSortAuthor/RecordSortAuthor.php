<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordSortAuthor.php,v 1.2 2023/11/10 13:49:06 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Record\RecordSortAuthor;

use Pmb\DSI\Models\Sort\RootSort;

class RecordSortAuthor extends RootSort
{

	protected const AUTHOR_TYPE = "070";

	protected $direction;

	protected $field = "authors.author_name";

	protected $fieldType = "string";

	public function __construct($data = null)
	{
		$this->type = static::TYPE_QUERY;
		if (in_array($data->direction, static::DIRECTIONS)) {
			$this->direction = $data->direction;
		}
	}

	public function getJoinClause()
	{
		return "LEFT JOIN responsability ON notices.notice_id = responsability.responsability_notice AND responsability.responsability_fonction = '" . static::AUTHOR_TYPE . "' LEFT JOIN authors ON responsability.responsability_author = authors.author_id";
	}
}