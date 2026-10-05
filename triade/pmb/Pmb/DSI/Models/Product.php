<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Product.php,v 1.18.2.1.2.1 2025/05/26 12:13:31 rtigero Exp $
namespace Pmb\DSI\Models;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Root;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\DSI\Orm\EventProductOrm;
use Pmb\DSI\Models\Event\RootEvent;
use Pmb\DSI\Orm\DiffusionHistoryOrm;

class Product extends Root implements CRUD
{

	public const TAG_TYPE = 7;

	protected $ormName = "Pmb\DSI\Orm\ProductOrm";

	public $name = "";
	public $settings = "";
	public $status = "";

	public $subscriberList = null;
	public $numSubscriberList = 0;
	public $tags = null;

	public $diffusions = null;

	protected $subscribers = null;
	protected $idProduct = 0;
	protected $numStatus = 0;
	public $events = null;

	public $productDiffusions = array();

	protected $lastDiffusion;

	public function __construct(int $id = 0)
	{
		$this->id = $id;
		$this->read();
		$this->fetchUserDefaultStatus();
	}

	public function create()
	{
		$orm = new $this->ormName();
		$orm->name = $this->name;
		$orm->settings = json_encode($this->settings);
		$orm->num_status = $this->status;
		$orm->num_subscriber_list = isset($this->numSubscriberList) ? $this->numSubscriberList : 0;
		$orm->save();
		$this->id = $orm->{$this->ormName::$idTableName};
		$this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
	}

	public function check(object $data)
	{
		if (empty($data->name) || ! is_string($data->name)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:data_errors'
			];
		}

		$fields = [
			'name' => $data->name
		];
		if (! empty($data->id)) {
			$fields[$this->ormName::$idTableName] = [
				'value' => $data->id,
				'operator' => '!='
			];
		}

		$result = $this->ormName::finds($fields);
		if (! empty($result)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:product_duplicated'
			];
		}

		return [
			'error' => false,
			'errorMessage' => ''
		];
	}

	public function setFromForm(object $data)
	{
		$this->name = $data->name;
		$this->settings = $data->settings;
		$this->numStatus = intval($data->numStatus);
		$this->numSubscriberList = $data->numSubscriberList;
	}

	public function read()
	{
		$this->fetchData();
		$this->fetchRelations();
		$this->tags = $this->getEntityTags();
	}

	public function update()
	{
		$orm = new $this->ormName($this->id);
		$orm->name = $this->name;
		$orm->settings = json_encode($this->settings);
		$orm->num_status = $this->numStatus;
		$orm->num_subscriber_list = $this->numSubscriberList;
		$orm->save();
	}

	public function delete()
	{
		try {
			$orm = new $this->ormName($this->id);

			//Suppression des liens
			if ($orm->num_subscriber_list != 0) {
				$subscriberList = RootSubscriberList::getProductSubscribers($this->id, $orm->num_subscriber_list);
				$subscriberList->lists->delete();
			}

			foreach ($orm->events as $productEvent) {
				$event = RootEvent::getInstance($productEvent->num_event);
				$event->delete();
			}

			$this->removeEntityTags();

			$orm->delete();
		} catch (\Exception $e) {
			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}

		$this->id = 0;
		$this->{Helper::camelize($orm::$idTableName)} = 0;
		$this->settings = "";
		$this->name = "";
		$this->status = "";
		$this->numStatus = "";
		$this->tags = null;
		$this->productDiffusions = array();

		return [
			'error' => false,
			'errorMessage' => ""
		];
	}

	public function fetchRelations()
	{
		$this->fetchSubscriberList();
		$this->fetchEvents();
		$this->fetchDiffusions();
	}

	public function fetchSubscriberList()
	{
		if (!isset($this->subscriberList)) {
			$this->subscriberList = RootSubscriberList::getProductSubscribers($this->id, $this->numSubscriberList);
		}
	}

	public function fetchEvents()
	{
		$events = EventProductOrm::finds(["num_product" => $this->id]);
		$this->events = array();
		foreach ($events as $event) {
			$instance = RootEvent::getInstance($event->num_event);
			$instance->callerType = "product";
			$this->events[] = $instance;
		}
	}

	public function fetchDiffusions()
	{
		$this->diffusions = array();

		foreach ($this->productDiffusions as $productDiffusion) {
			$this->diffusions[] = new Diffusion($productDiffusion->num_diffusion);
		}
	}

	public function getLastDiffusion()
	{
		$productDiffusions = end($this->productDiffusions);

		if (empty($productDiffusions) || !$productDiffusions) {
			$this->lastDiffusion = GlobalContext::msg('dsi_diffusion_never_send');
			return $this->lastDiffusion;
		}

		$timestamp = strtotime($productDiffusions->last_diffusion);
		if ($timestamp < 0) {
			$this->lastDiffusion = GlobalContext::msg('dsi_diffusion_never_send');
			return $this->lastDiffusion;
		}

		$date = new \DateTime($productDiffusions->last_diffusion);

		$this->lastDiffusion = $date->format(GlobalContext::msg('dsi_format_date'));
		return $this->lastDiffusion;
	}

	public function fetchUserDefaultStatus()
	{
		//Si on n'a pas de status, on va recuperer le parametrage utilisateur
		//Normalement le parametrage utilisateur par defaut est cable avec le statut par defaut
		//Donc on est cense avoir toujours une valeur ici
		if ($this->numStatus == 0) {
			global $PMBuserid;
			$query = "SELECT deflt_dsi_product_default_status FROM users WHERE userid = '$PMBuserid'";
			$this->numStatus = pmb_mysql_result(pmb_mysql_query($query), 0, 0);
		}
	}

	/**
	 * Teste les declencheurs + envoi.
	 * Utilise dans le planificateur (pmbesDSI_diffuseBannette)
	 *
	 * @return boolean
	 */
	public function trigger()
	{
		global $msg;

		$this->fetchEvents();


		if (! empty($this->events)) {
			$toSend = false;
			foreach ($this->events as $event) {
				if ($event->trigger()) {
					$toSend = true;
				}
			}
			if (! $toSend) {
				return $msg['dsi_diffusion_no_triggers_activated'];
			}
		}

		//On peut diffuser
		$this->fetchDiffusions();
		$this->fetchSubscriberList();
		foreach ($this->diffusions as $diffusion) {
			if (! $this->isDiffusionActive($diffusion->id)) {
				continue;
			}
			$diffusion->fetchEvents();
			$diffusion->fetchItem();
			$diffusion->fetchView();
			$canTrigger = true;
			//On teste les conditions d'affichage de la diffusion
			foreach ($diffusion->events as $event) {
				if (! $event->canTrigger($diffusion->view, $diffusion->item)) {
					$canTrigger = false;
					break;
				}
				//Si le produit n'a pas de trigger, on teste celui de la diffusion
				if (empty($this->events) && ! $event->trigger()) {
					$canTrigger = false;
					break;
				}
			}

			if (! $canTrigger) {
				continue;
			}

			//On prend la liste d'abonnés du produit sauf si aucun
			if (! empty($this->subscriberList->source->subscribers) || ! empty($this->subscriberList->lists->subscribers)) {
				$diffusion->subscriberList = $this->subscriberList;
			} else {
				$diffusion->fetchSubscriberList();
			}

			$send = $diffusion->send();
			if ($send["error"] == false && isset($send["idHistory"])) {
				//La diffusion s'est bien passée on met à jour la date de diffusion
				$history = DiffusionHistoryOrm::findById($send["idHistory"]);
				foreach ($this->productDiffusions as $productDiffusion) {
					if ($productDiffusion->num_diffusion == $diffusion->id && $productDiffusion->num_product == $this->id) {
						$productDiffusion->last_diffusion = $history->date;
						$productDiffusion->save();
						break;
					}
				}
			}
		}
		return true;
	}

	/**
	 * Teste si une diffusion est active dans le produit
	 *
	 * @param int $idDiffusion
	 */
	public function isDiffusionActive($idDiffusion)
	{
		foreach ($this->productDiffusions as $productDiffusion) {
			if ($productDiffusion->num_diffusion == $idDiffusion) {
				if ($productDiffusion->active) {
					return true;
				}
				break;
			}
		}
		return false;
	}
}
