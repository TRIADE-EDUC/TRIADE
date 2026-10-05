<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionSubscriber.php,v 1.14 2023/12/01 09:32:50 rtigero Exp $
namespace Pmb\DSI\Models\SubscriberList\Subscribers;

use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\DSI\Orm\DiffusionOrm;

class DiffusionSubscriber extends Subscriber implements SubscriberDiffusion
{

	public $ormName = "Pmb\DSI\Orm\SubscribersDiffusionOrm";

	public $idSubscriberDiffusion = 0;

	public $numDiffusion = 0;

	private $requirements = array();

	private $entityOrm = null;

	public function create()
	{
		$orm = new $this->ormName();
		$orm->name = $this->name;
		$orm->settings = json_encode($this->settings);
		$orm->type = $this->type;
		$orm->update_type = $this->updateType;
		$orm->num_diffusion = $this->numDiffusion;
		$orm->save();

		$this->id = $orm->{$this->ormName::$idTableName};
		$this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
	}

	public function setFromForm($data)
	{
		$this->name = $data->name;
		if (! empty($data->settings->idEmpr)) {
			$data->settings->idEmpr = intval($data->settings->idEmpr);
		}
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
		$orm->num_diffusion = $this->numDiffusion;
		$orm->save();
	}

	public function setEntity(int $entityId)
	{
		$this->numDiffusion = $entityId;
		$this->entityOrm = DiffusionOrm::findById($this->numDiffusion);
	}

	public function emptySubscribers()
	{
		$subscribers = $this->ormName::finds([
			"num_diffusion" => $this->numDiffusion
		]);
		foreach ($subscribers as $subscriber) {
			$subscriber->delete();
		}
		return true;
	}

	public function check($data)
	{
		if($this->checkDiffusionSubscriberDuplication($data) || $this->checkSourceSubscriberDuplication($data)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:subscriber_duplicated'
			];
		}
		return true;
	}

	public function getIdEmpr()
	{
		if (! empty($this->settings->idEmpr)) {
			return intval($this->settings->idEmpr);
		}
		return 0;
	}

	/**
	 * Récupère les  prérequis du canal
	 */
	private function getChannelRequirements()
	{
		if (empty($this->requirements)) {
			if ($this->entityOrm && $this->entityOrm->num_channel) {
				$channel = RootChannel::getInstance($this->entityOrm->num_channel);
				$this->requirements = array_keys($channel::CHANNEL_REQUIREMENTS["subscribers"]);
			}
		}
	}

	/**
	 * Verifie les doublons dans la liste de diffusion
	 * @param \stdClass $subscriber
	 * @return bool
	 */
	private function checkDiffusionSubscriberDuplication($subscriber)
	{
		$this->getChannelRequirements();
		$fields = array();
		$fields['num_diffusion'] = [
			"value" => $this->numDiffusion,
			"operator" => "=",
			"inter" => "AND"
		];
		//Vérification de doublons en base
		foreach ($this->requirements as $field) {
			switch (true) {
				case isset($data->$field):
					$fields[$field] = [
						"value" => $subscriber->$field,
						"operator" => "=",
						"inter" => "AND"
					];
					break;
				case isset($subscriber->settings->$field):
					if (! isset($fields["settings"])) {
						$fields['settings'] = array();
					}
					$fields['settings'][] = [
						"value" => '%"' . $field . '":"' . $subscriber->settings->$field . '"%',
						"operator" => "LIKE",
						"inter" => "AND"
					];
					break;
				default:
					break;
			}
		}

		if (count($fields)) {
			$result = $this->ormName::finds($fields);
			if (! empty($result)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Verifie les doublons dans la source de diffusion
	 * @param \stdClass $subscriber
	 * @return bool
	 */
	private function checkSourceSubscriberDuplication($subscriber)
	{
		$this->getChannelRequirements();
		//Vérification de la duplication dans la source
		if($this->entityOrm && $this->entityOrm->num_subscriber_list) {
			$subscriberList = RootSubscriberList::getSourceSubscriberList($this->entityOrm->num_subscriber_list);
			foreach($this->requirements as $requirement) {
				foreach($subscriberList->subscribers as $sourceSubscriber) {
					if($sourceSubscriber->settings->$requirement == $subscriber->settings->$requirement) {
						return true;
					}
				}
			}
		}
		return false;
	}
}