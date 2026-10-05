<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArticleSortOrder.php,v 1.2 2023/11/10 13:49:06 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Article\ArticleSortOrder;

use Pmb\DSI\Models\Sort\RootSort;

class ArticleSortOrder extends RootSort
{

	protected $field = "article_order";

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