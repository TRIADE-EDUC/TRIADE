<?php

namespace Pmb\DSI\Models\SubscriberList\Subscribers;

use Pmb\Common\Helper\Helper;

class SubscriberListSubscriber extends Subscriber
{

	protected $ormName = "Pmb\DSI\Orm\SubscribersOrm";

	protected $linkOrmName = "Pmb\DSI\Orm\SubscriberListContentOrm";

	protected $numSubscriberList = 0;

	protected $subscriberLists;

	public function create()
	{
		$orm = new $this->ormName();
		$orm->name = $this->name;
		$orm->settings = json_encode($this->settings);
		$orm->type = $this->type;
		$orm->update_type = $this->updateType;
		$orm->save();

		$this->id = $orm->{$this->ormName::$idTableName};
		$this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
		$this->linkSubscriberList();
	}

	public function setFromForm($data)
	{
		$this->name = $data->name;
		$this->settings = $data->settings;
		$this->updateType = $data->updateType ?? "";
		$this->type = $data->type;
	}

	public function setEntity(int $idEntity)
	{
		$this->numSubscriberList = $idEntity;
	}

	public function linkSubscriberList()
	{
		if ($this->id == 0 || $this->numSubscriberList == 0) {
			return false;
		}
		$keys = ['num_subscriber' => $this->id, 'num_subscriber_list' => $this->numSubscriberList];
		if ($this->linkOrmName::exist($keys)) {
			return false;
		}
		$orm = new $this->linkOrmName();
		$orm->num_subscriber = $this->id;
		$orm->num_subscriber_list = $this->numSubscriberList;
		$orm->save();
		return true;
	}

	public function unlinkSubscriberList()
	{
		$keys = ['num_subscriber' => $this->id, 'num_subscriber_list' => $this->numSubscriberList];
		if (! $this->linkOrmName::exist($keys)) {
			return false;
		}
		$orm = new $this->linkOrmName($keys);
		$orm->delete();
		return true;
	}

	protected function removeAllSubscriberLinks()
	{
		$links = $this->linkOrmName::find('num_subscriber', $this->id);
		foreach ($links as $link) {
			$link->delete();
		}
	}

	public function delete()
	{
		try {
			$orm = new $this->ormName($this->id);
			$orm->delete();
			$this->removeAllSubscriberLinks();
		} catch (\Exception $e) {
			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}

		$this->id = 0;
		$this->{Helper::camelize($orm::$idTableName)} = 0;
		$this->name = "";
		$this->updateType = 0;

		return [
			'error' => false,
			'errorMessage' => ''
		];
	}

	/**
	 * @param mixed $param id de la liste d'abonnes
	 */
	public function duplicate($param = null)
	{
		$newSubscriber = clone $this;
		$newSubscriber->numSubscriberList = $param;
		$newSubscriber->id = 0;
		$newSubscriber->create();
	}

	public function emptySubscribers()
	{
		$subscribersLinks = $this->linkOrmName::finds([
			"num_subscriber_list" => $this->numSubscriberList
		]);
		foreach ($subscribersLinks as $link) {
			$link->delete();
		}
		return true;
	}
}
