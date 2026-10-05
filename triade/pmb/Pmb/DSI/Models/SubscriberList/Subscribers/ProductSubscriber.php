<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ProductSubscriber.php,v 1.8.4.1 2025/05/26 12:13:32 rtigero Exp $
namespace Pmb\DSI\Models\SubscriberList\Subscribers;

use Pmb\Common\Helper\Helper;

class ProductSubscriber extends Subscriber implements SubscriberDiffusion
{

	public const DEDUPLICATION_FIELDS = [
		"email"
	];

	protected $ormName = "Pmb\DSI\Orm\SubscribersProductOrm";

	public $numProduct = 0;

	public function create()
	{
		$orm = new $this->ormName();
		$orm->name = $this->name;
		$orm->settings = json_encode($this->settings);
		$orm->type = $this->type;
		$orm->update_type = $this->updateType;
		$orm->num_product = $this->numProduct;
		$orm->save();

		$this->id = $orm->{$this->ormName::$idTableName};
		$this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
	}

	public function setFromForm($data)
	{
		$this->name = $data->name;
		$this->settings = $data->settings;
		$this->updateType = $data->updateType ?? "";
		$this->type = $data->type;
	}

	public function update()
	{
		$orm = new $this->ormName($this->id);
		$orm->name = $this->name;
		$orm->settings = json_encode($this->settings);
		$orm->type = $this->type;
		$orm->update_type = $this->updateType;
		$orm->num_product = $this->numProduct;
		$orm->save();
	}

	public function setEntity(int $entityId)
	{
		$this->numProduct = $entityId;
	}

	public function emptySubscribers()
	{
		$subscribers = $this->ormName::finds([
			"num_product" => $this->numProduct
		]);
		foreach ($subscribers as $subscriber) {
			$subscriber->delete();
		}
		return true;
	}

	public function check($data)
	{
		if ($this->id != 0) {
			return true;
		}
		$fields = array();
		$fields['num_product'] = [
			"value" => $this->numProduct,
			"operator" => "=",
			"inter" => "AND"
		];
		foreach (static::DEDUPLICATION_FIELDS as $field) {
			switch (true) {
				case isset($data->$field):
					$fields[$field] = [
						"value" => $data->$field,
						"operator" => "=",
						"inter" => "AND"
					];
					break;
				case isset($data->settings->$field):
					if (! isset($fields["settings"])) {
						$fields['settings'] = array();
					}
					$fields['settings'][] = [
						"value" => '%"' . $field . '":"' . $data->settings->$field . '"%',
						"operator" => "LIKE",
						"inter" => "AND"
					];
					break;
				default:
					break;
			}
		}
		if (! count($fields)) {
			return true;
		}

		$result = $this->ormName::finds($fields);

		if (! empty($result)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:subscriber_duplicated'
			];
		}
	}

	public function getIdEmpr()
	{
		if (! empty($this->settings->idEmpr)) {
			return intval($this->settings->idEmpr);
		}
		return 0;
	}
}