<?php
namespace Pmb\DSI\Models\SubscriberList\Subscribers;

use Pmb\Common\Helper\Helper;

class SubscriberExt extends Subscriber
{

	public const DEDUPLICATION_FIELDS = [
		"email"
	];
	
	public $idSubscriber;

	public $name;

	public $settings;

	public $updateType;

	public $numTag;

	protected function __construct(int $id)
	{
		$this->type = static::TYPE_EXT;
		$this->id = $id;
		$this->read();
	}

	public function read()
	{
		$this->fetchData();
		if ($this->settings != "") {
			$this->settings = json_decode($this->settings);
		} else {
			$this->settings = json_decode("{}");
		}
	}

	public function check($data)
	{
		if (isset($data->settings->email)) {
			if (! Helper::isValidMail($data->settings->email)) {
				return [
					'error' => true,
					'errorMessage' => 'msg:data_errors'
				];
			}
		}
		if (empty($data->name)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:data_errors'
			];
		}
		
		return parent::check($data);
	}

	public function update()
	{
		$orm = new $this->ormName($this->id);
		$orm->name = $this->name;
		$orm->settings = json_encode($this->settings);
		$orm->type = $this->type;
		$orm->update_type = $this->updateType;
		$orm->num_tag = $this->numTag;
		$orm->save();
	}
}

