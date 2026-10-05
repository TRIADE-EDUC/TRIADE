<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubscriberEmpr.php,v 1.3 2023/10/19 09:32:49 rtigero Exp $
namespace Pmb\DSI\Models\SubscriberList\Subscribers;

class SubscriberEmpr extends Subscriber
{

	public const DEDUPLICATION_FIELDS = [
		"name",
		"idEmpr",
		"email"
	];

	public $name = "";

	public $type;

	public $settings = null;

	public function __construct()
	{
		if (! isset($this->settings)) {
			$this->settings = json_decode("{}");
		}
	}

	public function getName()
	{
		return $this->name;
	}

	public function getIdEmpr()
	{
		if(! empty($this->settings->idEmpr)) {
			return intval($this->settings->idEmpr);
		}
		return 0;
	}
}