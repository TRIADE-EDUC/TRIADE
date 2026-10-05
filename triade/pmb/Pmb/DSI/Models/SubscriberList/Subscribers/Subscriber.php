<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Subscriber.php,v 1.10 2023/10/11 14:29:54 rtigero Exp $
namespace Pmb\DSI\Models\SubscriberList\Subscribers;

use Pmb\Common\Models\Model;
use Pmb\DSI\Models\CRUD;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;

class Subscriber extends Model implements CRUD
{
	public const PREFIX_PATTERN = "subscriber";

	public const FROM_DIFFUSION = "diffusions";

	public const FROM_PRODUCT = "products";

	public const FROM_SUBSCRIBER_LIST = "subscriber_list";

	public const FROM_PENDING_DIFFUSION = "diffusions_pending";

	public const UPDATE_TYPE_SUBSCRIBER = 0;

	//Désinscription depuis la gestion
	public const UPDATE_TYPE_UNSUBSCRIBER = 1;
	//Désinscription par l'emprunteur
	public const UPDATE_TYPE_SELF_UNSUBSCRIBER = 2;

	public const DEDUPLICATION_FIELDS = array();

	public $idSubscriber = 0;

	public $name;

	public $settings;

	public $type;

	public $updateType = 0;

	public static function getInstance(string $type = "", int $id = 0)
	{
		switch ($type) {
			case self::FROM_DIFFUSION:
				return new DiffusionSubscriber($id);
			case self::FROM_PRODUCT:
				return new ProductSubscriber($id);
			case self::FROM_SUBSCRIBER_LIST:
			case self::FROM_PENDING_DIFFUSION:
				return new SubscriberListSubscriber($id);
			default:
				return new self($id);
		}
	}

	public function __construct(int $id = 0)
	{
		$this->id = $id;
		$this->read();
	}

	public function create()
	{}

	public function update()
	{}

	public function read()
	{
		$this->fetchData();
		if ($this->settings != "") {
			$this->settings = json_decode($this->settings);
		} else {
			$this->settings = json_decode("{}");
		}
	}

	public function delete()
	{
		try {
			$orm = new $this->ormName($this->id);
			$orm->delete();
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

	public function save()
	{}

	/**
	 * Verification avant insertion en base
	 *
	 * @param object $data
	 */
	public function check($data)
	{
		//Derivate
	}

	public function unsubscribe()
	{
		$this->updateType = self::UPDATE_TYPE_UNSUBSCRIBER;
	}

	public function unsubscribeFromSubscriber()
	{
		$this->updateType = self::UPDATE_TYPE_SELF_UNSUBSCRIBER;
	}

	public function subscribe()
	{
		$subscriber = clone $this;
		if($subscriber->type == RootSubscriberList::SUBSCRIBER_TYPE_SOURCE) {
			if ($this->id) {
				$delete = $this->delete();
				if (! $delete['error']) {
					$subscriber->id = 0;
					$subscriber->updateType = self::UPDATE_TYPE_SUBSCRIBER;
					return $subscriber;
				}
			}
		} else {
			$this->updateType = self::UPDATE_TYPE_SUBSCRIBER;
			$this->update();
			return $this;
		}
		return [
			'error' => true,
			'errorMessage' => 'msg:subscriber_duplicated'
		];
	}

	public function getIdSubscriber()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getStatus()
	{
		return $this->updateType;
	}

	public function getIdEmpr()
	{
		return 0;
	}
}

