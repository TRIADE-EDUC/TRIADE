<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Event.php,v 1.3 2023/03/03 13:50:43 rtigero Exp $

namespace Pmb\DSI\Models\Event;

interface Event
{
	public function trigger();
}

