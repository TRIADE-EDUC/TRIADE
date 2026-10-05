<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArticleFilter.php,v 1.1.6.1 2025/05/21 08:03:10 rtigero Exp $

namespace Pmb\DSI\Models\Filter\Entities\Article;

use Pmb\DSI\Models\Filter\RootFilter;

class ArticleFilter extends RootFilter
{
	protected function __construct(array $data, int $entityId)
	{
		parent::__construct($data, $entityId);
	}
}
