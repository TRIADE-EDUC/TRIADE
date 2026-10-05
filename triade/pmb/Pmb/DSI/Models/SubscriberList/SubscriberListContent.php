<?php
namespace Pmb\DSI\Models\SubscriberList;

use Pmb\Common\Models\Model;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\CRUD;

class SubscriberListContent extends Model implements CRUD
{

	protected $ormName = "Pmb\DSI\Orm\SubscriberListContentOrm";

	protected $numSubscriber = 0;

	protected $numSubscriberList = 0;

	public function __construct(int $numSubscriber = 0, int $numSubscriberList = 0)
	{
		$this->numSubscriber = $numSubscriber;
		$this->numSubscriberList = $numSubscriberList;
		$this->read();
	}

	public function create()
	{
		$orm = new $this->ormName([ 
			"num_subscriber" => $this->numSubscriber,
			"num_subscriber_list" => $this->numSubscriberList
		]);
		$orm->num_subscriber = $this->numSubscriber;
		$orm->num_subscriber_list = $this->numSubscriberList;
		$orm->save();
	}

	public function check($data)
	{
		return true;
	}

	public function setFromForm(object $data)
	{}

	public function read()
	{
		$this->fetchData();
	}

	public function update()
	{
		$orm = new $this->ormName([
			"num_subscriber" => $this->numSubscriber,
			"num_subscriber_list" => $this->numSubscriberList
		]);
		$orm->num_subscriber = $this->numSubscriber;
		$orm->num_subscriber_list = $this->numSubscriberList;
		$orm->save();
	}

	public function delete()
	{
		try {
			$orm = new $this->ormName([
				"num_subscriber" => $this->numSubscriber,
				"num_subscriber_list" => $this->numSubscriberList
			]);
			$orm->delete();
		} catch (\Exception $e) {
			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}

		$this->numSubscriber = 0;
		$this->numSubscriberList = 0;

		return [
			'error' => false,
			'errorMessage' => ''
		];
	}
}

