<?php

namespace Pmb\DSI\Controller;

use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\DiffusionProduct;
use Pmb\DSI\Models\Product;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\DSI\Models\SubscriberList\Subscribers\Subscriber;
use Pmb\DSI\Orm\DiffusionProductOrm;
use Pmb\DSI\Orm\SubscribersDiffusionOrm;
use Pmb\DSI\Orm\SubscribersProductOrm;

class SubscribersController extends CommonController
{

	public function delete($entityType = "")
	{
		$subscriber = Subscriber::getInstance($entityType, $this->data->id);
		$result = $subscriber->delete();

		if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}
		$this->ajaxJsonResponse([
			'success' => true
		]);
		exit();
	}

	public function getEntity($entityType = "", $idSubscriber = 0)
	{
		return $this->ajaxJsonResponse(Subscriber::getInstance($entityType, $idSubscriber));
	}

	public function save($entityType, $entityId)
	{
		$this->data->id = intval($this->data->id);
		$subscriber = Subscriber::getInstance($entityType, $this->data->id);
		$subscriber->setFromForm($this->data);
		$subscriber->setEntity($entityId);
		$result = $subscriber->check($this->data);
		if (isset($result['error'])) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}


		if (0 == $this->data->id) {
			$subscriber->create();
		} else {
			$subscriber->update();
		}
		$this->ajaxJsonResponse($subscriber);
		exit();
	}

	/**
	 * Ajoute les subscribers a partir d'une liste contenant une source
	 *
	 * @param number $idSubscriberList
	 */
	public function importSubscribers(string $entityType, int $idEntity = 0)
	{
		$subscribers = array();
		$error = false;
		if (! empty($this->data->subscribers)) {
			foreach ($this->data->subscribers as $subscriber) {
				$subscriberModel = Subscriber::getInstance($entityType);
				$subscriberModel->setFromForm($subscriber);
				$subscriberModel->setEntity($idEntity);
				$result = $subscriberModel->check($subscriber);
				if (isset($result['error'])) {
					$error = $result;
					continue;
				}
				$subscriberModel->create();
				$subscribers[] = $subscriberModel;
			}
		}
		if ($error && count($subscribers) == 0) {
			$this->ajaxJsonResponse($error);
		}
		$this->ajaxJsonResponse($subscribers);
	}

	/**
	 * Desinscrit un abonne issu d'une source
	 * On peut également passer un tableau de subscribers pour une désinscription multiple
	 * @param string $entityType
	 * @param int $entityId
	 */
	public function unsubscribe(string $entityType, int $entityId)
	{
		//Cas ou on a un tableau
		if (isset($this->data->subscribers) && is_array($this->data->subscribers)) {
			foreach ($this->data->subscribers as $subscriber) {
				$newSubscriber = Subscriber::getInstance($entityType, $subscriber->id);
				$newSubscriber->setFromForm($subscriber);
				$newSubscriber->setEntity($entityId);
				$newSubscriber->unsubscribe();

				if (0 == $subscriber->id) {
					$newSubscriber->create();
				} else {
					$newSubscriber->update();
				}
			}
			$this->ajaxJsonResponse($this->data->subscribers);
			exit();
		}

		//Cas d'un subscriber unique
		$subscriber = Subscriber::getInstance($entityType, $this->data->id);
		$subscriber->setFromForm($this->data);
		$subscriber->setEntity($entityId);
		$subscriber->unsubscribe();

		if (0 == $this->data->id) {
			$subscriber->create();
		} else {
			$subscriber->update();
		}
		$this->ajaxJsonResponse($subscriber);
		exit();
	}

	/**
	 * Reinscrit un abonne desinscrit
	 * @param string $entityType
	 * @param int $entityId
	 */
	public function subscribe(string $entityType, int $entityId)
	{
		$subscriber = Subscriber::getInstance($entityType, $this->data->id);
		$result = $subscriber->subscribe();

		$this->ajaxJsonResponse($result);
		exit();
	}

	/**
	 * Inscription d'un abonné depuis l'OPAC
	 * @param string $entityType
	 * @param int $entityId
	 * @param bool $ajax
	 */
	public function subscribeFromOpac(string $entityType, int $entityId, bool $ajax = true)
	{
		//On vérifie si on n'est pas sur un réabonnement
		if ($entityType == "diffusions") {
			//Si on est sur une diffusion on vérifie les produits associés
			$diffusionProducts = DiffusionProductOrm::finds([
				"num_diffusion" => $entityId,
			]);
			foreach ($diffusionProducts as $diffusionProduct) {
				$this->subscribeFromOpac("products", $diffusionProduct->num_product, false);
			}
		}
		$idSubscriber = 0;
		switch ($entityType) {
			case "diffusions":
				$searchSubscriber = SubscribersDiffusionOrm::finds([
					"num_diffusion" => $entityId,
					'settings' => [
						"value" => '%"idEmpr":' . $this->data->settings->idEmpr . '%',
						"operator" => "LIKE",
						"inter" => "AND"
					]
				]);
				if (count($searchSubscriber) == 1) {
					$idSubscriber = $searchSubscriber[0]->id_subscriber_diffusion;
				}
				break;
			case "products":
				$searchSubscriber = SubscribersProductOrm::finds([
					"num_product" => $entityId,
					'settings' => [
						"value" => '%"idEmpr":' . $this->data->settings->idEmpr . '%',
						"operator" => "LIKE",
						"inter" => "AND"
					]
				]);
				if (count($searchSubscriber) == 1) {
					$idSubscriber = $searchSubscriber[0]->id_subscriber_product;
				}
				break;
		}

		$subscriber = Subscriber::getInstance($entityType, $idSubscriber);
		$subscriber->setFromForm($this->data);
		$subscriber->setEntity($entityId);
		if ($idSubscriber == 0) {
			//Nouvelle inscription ? Alors on met en manuel
			$subscriber->type = RootSubscriberList::SUBSCRIBER_TYPE_MANUAL;
			$subscriber->create();
		}

		if (!$ajax) {
			return $subscriber->subscribe();
		}

		$this->ajaxJsonResponse($subscriber->subscribe());
	}

	/**
	 * Désinscription d'un abonné depuis l'OPAC
	 * @param string $entityType
	 * @param int $entityId
	 * @param bool $ajax
	 */
	public function unsubscribeFromOpac(string $entityType, int $entityId, bool $ajax = true)
	{
		$idEmpr = $this->data->settings->idEmpr;
		if ($entityType == "diffusions") {
			//Si on est sur une diffusion on vérifie les produits associés
			$diffusionProducts = DiffusionProductOrm::finds([
				"num_diffusion" => $entityId,
			]);
			foreach ($diffusionProducts as $diffusionProduct) {
				$this->unsubscribeFromOpac("products", $diffusionProduct->num_product, false);
			}
		}

		switch ($entityType) {
			case "diffusions":
				$entity = new Diffusion($entityId);
				$entity->fetchSubscriberList();
				break;
			case "products":
				$entity = new Product($entityId);
				$entity->fetchSubscriberList();
				break;
		}
		//On regarde si l'abonné fait partie de la source
		foreach ($entity->subscriberList->source->subscribers as $subscriber) {
			if ($subscriber->getIdEmpr() == $idEmpr) {
				//Alors on le désinscrit de la source
				$type = RootSubscriberList::SUBSCRIBER_TYPE_SOURCE;
				$subscriber = Subscriber::getInstance($entityType, $this->data->id);
				$subscriber->type = $type;
				$subscriber->setFromForm($this->data);
				$subscriber->setEntity($entityId);
				$subscriber->unsubscribeFromSubscriber();
				//On ajoute donc une entree en base pour desinscrire d'une source
				if (0 == $this->data->id) {
					$subscriber->create();
				} else {
					$subscriber->update();
				}

				if (!$ajax) {
					return $subscriber;
				}

				$this->ajaxJsonResponse($subscriber);
				exit();
			}
		}
		foreach ($entity->subscriberList->lists->subscribers as $subscriber) {
			if ($subscriber->getIdEmpr() == $idEmpr) {
				//On a récupéré l'entrée en base donc on change les propriétés
				//Pour désinscrire
				$subscriber->unsubscribeFromSubscriber();

				if (0 == $subscriber->id) {
					$subscriber->create();
				} else {
					$subscriber->update();
				}

				if (!$ajax) {
					return $subscriber;
				}

				$this->ajaxJsonResponse($subscriber);
				exit();
			}
		}
	}

	/**
	 * Supprime tous les subscribers d'une entite en base
	 */
	public function empty()
	{
		$subscriber = Subscriber::getInstance($this->data->entityType);
		$subscriber->setEntity($this->data->entityId);
		$empty = $subscriber->emptySubscribers();
		return $this->ajaxJsonResponse($empty);
	}
}
