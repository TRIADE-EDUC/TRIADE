<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: LocalSubscriberList.php,v 1.9.4.2 2025/03/31 14:48:27 rtigero Exp $
namespace Pmb\DSI\Models\SubscriberList;

use Pmb\DSI\Models\SubscriberList\Subscribers\Subscriber;

class LocalSubscriberList extends RootSubscriberList
{

	public $numEntity = 0;

	public $subscribers = array();

	public function __construct(int $numEntity = 0)
	{
		$this->numEntity = $numEntity;
		$this->read();
	}

	public function read()
	{
		$this->getSubscribers();
	}

	protected function getSubscribers()
	{
		//Derivate
		return array();
	}

	public function getUnsubscribers(SourceSubscriberList $source)
	{
		$result = array();
		foreach ($this->subscribers as $subscriber) {
			if ($subscriber->type == static::SUBSCRIBER_TYPE_SOURCE && $subscriber->updateType == Subscriber::UPDATE_TYPE_UNSUBSCRIBER) {
				$result[] = $subscriber;
			}
		}
		return $result;
	}

	/**
	 * Filtre les abonnes
	 */
	protected function filterSubscribers()
	{
		//Derivate
	}

	public function getSubscribersToSend()
	{
		$filteredSubscribers = array_filter($this->subscribers, function ($a) {
			return $a->updateType != Subscriber::UPDATE_TYPE_UNSUBSCRIBER && $a->updateType != Subscriber::UPDATE_TYPE_SELF_UNSUBSCRIBER;
		});

		return $filteredSubscribers;
	}

	/**
	 *
	 * @param mixed $param
	 *        	Id de la diffusion
	 */
	public function duplicate($param = null)
	{
		if (empty($param)) {
			return false;
		}
		$newList = clone $this;
		foreach ($newList->subscribers as $subscriber) {
			$newSubscriber = clone $subscriber;
			$newSubscriber->id = 0;
			$newSubscriber->setEntity($param);

			$newSubscriber->create();
		}
		return $newList;
	}

	public function delete()
	{
		foreach ($this->subscribers as $subscriber) {
			if ($subscriber->id != 0) {
				$subscriber->delete();
			}
		}
	}

	/**
	 * Met a jour les abonnements et desabonnements des listes verrouillees a un modele
	 * @param int $idSubscriberList id du modele
	 * @return array liste des nouveaux abonnes
	 */
	public function updateLockedListsFromModel($idSubscriberList = 0)
	{
		$result = array();
		$sources = new SourceSubscriberList($idSubscriberList);
		$sourceSubscribers = $sources->getSubscribersFromDatabase();
		$sourcesList = $sources->getLockedListsFromModel($idSubscriberList);

		foreach($sourcesList as $source) {
			$entity = $this->entityOrmName::find("num_subscriber_list", $source->id_subscriber_list);
			if(empty($entity)) {
				continue;
			}
			//Normalement on a qu'une diffusion liee a une subscriber list
			$idEntity = $entity[0]->{$entity[0]::$idTableName};
			$entitySubscriberList = new static($idEntity);
			//On vide d'abord tous les subscribers
			if(! empty($entitySubscriberList->subscribers)) {
				$entitySubscriberList->subscribers[0]->emptySubscribers();
			}
			//On set les subscribers du model
			foreach($sourceSubscribers->subscribers as $subscriber) {
				$newSubscriber = Subscriber::getInstance($entitySubscriberList::SUBSCRIBER_TYPE);
				$newSubscriber->setEntity($idEntity);
				$newSubscriber->setFromForm($subscriber);
				$newSubscriber->create();
				$result[] = $newSubscriber;
			}
		}
		if(!empty($result)) {
			return $result;
		}
		return $sourceSubscribers->subscribers;
	}
}

