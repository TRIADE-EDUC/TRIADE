<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EmprList.php,v 1.4 2023/04/04 13:13:48 rtigero Exp $

namespace Pmb\DSI\Models\Source\Subscriber\Entities\Empr;

use Pmb\DSI\Models\Source\Subscriber\SubscribersSource;

class EmprList extends SubscribersSource
{
	public function getData()
	{
		if(! empty($this->selector)) {
			return $this->selector->getData();
		}
		return array();
	}
}

