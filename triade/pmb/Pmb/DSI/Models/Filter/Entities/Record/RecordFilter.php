<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordFilter.php,v 1.2.4.1 2025/05/21 08:03:11 rtigero Exp $

namespace Pmb\DSI\Models\Filter\Entities\Record;

use Pmb\DSI\Models\Filter\RootFilter;

class RecordFilter extends RootFilter
{
	protected function __construct(array $data, int $entityId)
	{
		parent::__construct($data, $entityId);
	}
}
