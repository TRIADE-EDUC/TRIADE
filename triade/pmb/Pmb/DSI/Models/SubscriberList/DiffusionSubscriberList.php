<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionSubscriberList.php,v 1.9 2023/04/05 13:43:30 rtigero Exp $

namespace Pmb\DSI\Models\SubscriberList;

use Pmb\DSI\Models\SubscriberList\Subscribers\Subscriber;
use Pmb\DSI\Models\Channel\RootChannel;

class DiffusionSubscriberList extends LocalSubscriberList
{
	protected const SUBSCRIBER_TYPE = Subscriber::FROM_DIFFUSION;
	
	protected $ormName = "Pmb\DSI\Orm\SubscribersDiffusionOrm";

	protected $entityOrmName = "Pmb\DSI\Orm\DiffusionOrm";

	protected $numDiffusion = 0;

	public function __construct($idDiffusion = 0)
	{
		$this->numDiffusion = $idDiffusion;
		parent::__construct($idDiffusion);
	}

	protected function getSubscribers()
	{
		$this->subscribers = array();
		$lists = $this->ormName::finds(['num_diffusion' => $this->numEntity]);
		foreach ($lists as $list) {
			$this->subscribers[] = Subscriber::getInstance("diffusions", $list->{$this->ormName::$idTableName});
		}

		$this->filterSubscribers();

		return $this->subscribers;
	}

	public function getFormatedSubscribers($list = array())
	{
		if (!empty($this->subscribers)) {
			return $this->subscribers;
		}
		return $this->getSubscribers();
	}

	protected function filterSubscribers()
	{
		//Recuperation des contraintes du channel
		$entity = $this->entityOrmName::findById($this->numEntity);
		if (!$entity->num_channel) {
			return array();
		}
		$channel = RootChannel::getInstance($entity->num_channel);
		if (!$channel::CHANNEL_REQUIREMENTS || !isset($channel::CHANNEL_REQUIREMENTS['subscribers'])) {
			return array();
		}
		$requirements = array_keys($channel::CHANNEL_REQUIREMENTS['subscribers']);
		for ($i = 0; $i < count($this->subscribers); $i++) {
			//On ne filtre pas les unsubscribers
			if ($this->subscribers[$i]->updateType == Subscriber::UPDATE_TYPE_UNSUBSCRIBER) {
				continue;
			}
			$validSettings = 0;
			foreach ($this->subscribers[$i]->settings as $setting => $value) {
				if ((in_array($setting, $requirements)) && !empty($value)) {
					$validSettings++;
				}
			}
			if ($validSettings < count($requirements)) {
				array_splice($this->subscribers, $i, 1);
			}
		}
	}
}