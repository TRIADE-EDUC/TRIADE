<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SourceSubscriberList.php,v 1.16.2.1.2.1 2025/03/31 14:48:27 rtigero Exp $
namespace Pmb\DSI\Models\SubscriberList;

use encoding_normalize;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\SubscriberList\Subscribers\Subscriber;
use Pmb\DSI\Orm\SubscriberListContentOrm;

class SourceSubscriberList extends RootSubscriberList
{

	protected const EXCLUDED_PROPERTIES = [
		"idSubscriberList",
		'numModel',
		'model',
		'parentSubscriberList'
	];

	protected $ormName = "Pmb\DSI\Orm\SubscriberListOrm";

	public const TAG_TYPE = 4;

	public $idSubscriberList = 0;

	public $name = "";

	public $settings = null;

	public $model = false;

	public $source = null;

	protected $numParent = 0;

	protected $parentSubscriberList = null;

	public $numModel = 0;

	protected $subscriberListModel = null;

	public $subscribers = null;

	protected $subscriberListContent = null;

	public $tags = null;

	public function __construct(int $id = 0)
	{
		$this->id = intval($id);
		$this->read();
	}

	/**
	 * Retourne les donnees du selecteur
	 *
	 * @return array
	 */
	public function getSelectorData()
	{
		$source = $this->getSource();
		return ! empty($source) ? $source->getData() : [];
	}

	public function create()
	{
		$orm = new $this->ormName();
		$orm->name = $this->name;
		$orm->settings = json_encode($this->settings);
		$orm->model = $this->model;
		$orm->num_model = $this->numModel;
		$orm->save();

		$this->id = $orm->{$this->ormName::$idTableName};
		$this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
	}

	public function update()
	{
		$orm = new $this->ormName($this->id);
		$orm->name = $this->name;
		$orm->settings = json_encode($this->settings);
		$orm->model = $this->model;
		$orm->num_model = $this->numModel;
		$orm->save();
	}

	public function delete()
	{
		try {
			if (! $this->checkBeforeDelete()) {
				return [
					'error' => true,
					'errorMessage' => "msg:model_check_use"
				];
			}
			$orm = new $this->ormName($this->id);
			$this->removeEntityTags();
			$this->removeSubscribers();
			$orm->delete();
		} catch (\Exception $e) {
			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}
	}

	public function setFromForm(object $data)
	{
		$this->name = $data->name;
		$this->settings = $data->settings;
		$this->model = $data->model;
		$this->numModel = $data->numModel;
		$this->subscribers = $data->subscribers;
	}

	public function read()
	{
		$this->fetchData();
		$this->getSubscribers();
	}

	protected function getSubscribers()
	{
		if (isset($this->subscribers)) {
			return $this->subscribers;
		}
		$this->subscribers = [];
		$this->subscribers = $this->getSelectorData();
	}

	public function getSubscribersFromDatabase()
	{
		$subscribers = SubscriberListContentOrm::finds([
			"num_subscriber_list" => $this->id
		]);
		$formatedSubscribers = new \stdClass();
		$formatedSubscribers->subscribers = [];
		foreach ($subscribers as $subscriber) {
			$subscriberInstance = Subscriber::getInstance(Subscriber::FROM_SUBSCRIBER_LIST, $subscriber->num_subscriber);
			$subscriberInstance->setEntity($this->id);
			$formatedSubscribers->subscribers[] = $subscriberInstance;
		}
		return $formatedSubscribers;
	}

	/**
	 * Retire de la source les abonnes desinscrits
	 *
	 * @param LocalSubscriberList $list
	 */
	public function filterSource($list)
	{
		$filteredLocalList = array_filter($list->subscribers, function ($a) {
			return $a->updateType != 0;
		});
		foreach ($filteredLocalList as $subscriber) {
			$j = array_search($subscriber->name, array_column($this->subscribers, 'name'));
			if ($j !== false && static::isSameSubscriber($subscriber, $this->subscribers[$j])) {
				array_splice($this->subscribers, $j, 1);
			}
		}
	}

	public function getFormatedSubscribers($list = [])
	{
		$this->getSubscribers();
		$this->filterSource($list);
		return $this->subscribers;
	}

	public function getSubscribersToSend()
	{
		return $this->subscribers;
	}

	/**
	 * Retourne la source utilise par la list
	 *
	 * @return null|\Pmb\DSI\Models\Selector\RootSelector
	 */
	public function getSource()
	{
		if (isset($this->source)) {
			return $this->source;
		}

		if (! isset($this->settings) || ! isset($this->settings->subscriberListSource->subscriberListSelector->namespace)) {
			return null;
		}

		$namespace = $this->settings->subscriberListSource->namespace;
		if (class_exists($namespace)) {
			$this->source = new $namespace($this->settings->subscriberListSource->subscriberListSelector);
		}
		return $this->source;
	}

	protected function removeSubscribers()
	{
		foreach ($this->subscribers as $subscriber) {
			if ($subscriber->id != 0) {
				$subscriber->delete();
			}
		}
	}

	/**
	 * Retourne les listes d'abonnes verouillees associees a un modele
	 * @param int $idSubscriberList id du modele
	 */
	public function getLockedListsFromModel($idSubscriberList = 0)
	{
		$lockedLists = array();
		$lists = $this->ormName::finds([
			"num_model" => $idSubscriberList,
		]);

		foreach ($lists as $list) {
			if ($list->settings != "") {
				$settings = encoding_normalize::json_decode($list->settings);
				if (isset($settings->locked) && true == $settings->locked) {
					$lockedLists[] = $list;
				}
			}
		}

		return $lockedLists;
	}

	/**
	 * Réinitialise la source
	 */
	public function reset()
	{
		$emptyModel = new static();
		$this->settings = $emptyModel->settings;
		$this->source = $emptyModel->source;
		$this->subscriberListContent = $emptyModel->subscriberListContent;
		$this->subscribers = array();

		$this->update();
	}
}
