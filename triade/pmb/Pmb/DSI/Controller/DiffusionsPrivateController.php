<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionsPrivateController.php,v 1.13.2.3 2024/11/12 14:15:35 jparis Exp $
namespace Pmb\DSI\Controller;

use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\Event\RootEvent;
use Pmb\DSI\Models\EventDiffusion;
use Pmb\DSI\Models\Item\RootItem;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\DSI\Orm\DiffusionOrm;

class DiffusionsPrivateController extends CommonController
{

	protected const VUE_NAME = "dsi/diffusionsPrivate";

	//Pour l'instant que des RMC de notices pour les alertes privées
	const DSI_PRIVATE_SELECTOR_NAMESPACE = "Pmb\\DSI\\Models\\Selector\\Item\\Entities\\Record\\RMC\\RecordRMCSelector";

	const DSI_PRIVATE_EMPR_SELECTOR_NAMESPACE = "Pmb\\DSI\\Models\\Selector\\Subscriber\\Empr\\SearchById\\SearchByIdSelector";

	const EMPR_TYPE = 1;

	/**
	 *
	 * {@inheritdoc}
	 * @see \Pmb\DSI\Controller\CommonController::getBreadcrumb()
	 */
	protected function getBreadcrumb()
	{
		global $msg;
		return "{$msg['dsi_menu']} {$msg['menu_separator']} {$msg['dsi_private']}";
	}

	protected function defaultAction()
	{
		$diffusionModel = new Diffusion();

		$diffusions = $diffusionModel->getFilteredList();
		$formData = array();
		$formData["diffusionModel"] = "";
		$formData["selectedItem"] = 0;
		$formData["nbMaxResults"] = 0;
		$diffusionPrivateModel = Diffusion::getDiffusionPrivateModel();
		if (! empty($diffusionPrivateModel)) {
			$formData["diffusionModel"] = $diffusionPrivateModel->idDiffusion;
			$formData["selectedItem"] = $diffusionPrivateModel->settings->selectedItem;
			$formData["nbMaxResults"] = $diffusionPrivateModel->settings->nbMaxResults ?? 0;
		}
		print $this->render([
			"diffusions" => $diffusions,
			"formData" => $formData
		]);
	}

	public function save()
	{
		if (intval($this->data->diffusionModel) != 0) {
			$diffusionPrivateModel = Diffusion::getDiffusionPrivateModel();

			//On ne peut avoir qu'une diffusion modele alors on retire le paramétrage si on en trouve un
			if (! empty($diffusionPrivateModel)) {
				$idDiffusionModel = $diffusionPrivateModel->idDiffusion;
				if ($this->data->diffusionModel != $idDiffusionModel) {
					//On détruit tout le paramétrage
					unset($diffusionPrivateModel->settings->diffusionModel);
					unset($diffusionPrivateModel->settings->selectedItem);
					unset($diffusionPrivateModel->settings->nbMaxResults);
					$diffusionPrivateModel->update();
				}
			}
			$diffusion = new Diffusion($this->data->diffusionModel);
			$diffusion->settings->diffusionModel = true;

			//Gestion de l'item a remplacer par la bannette privée
			if (intval($this->data->selectedItem) != 0) {
				$diffusion->settings->selectedItem = intval($this->data->selectedItem);
			}

			//Gestion du nombre max de resultats
			if (intval($this->data->nbMaxResults) != 0) {
				$diffusion->settings->nbMaxResults = intval($this->data->nbMaxResults);
			}

			//On met à jour avec le nouveau paramétrage
			$diffusion->update();

			$this->ajaxJsonResponse(array(
				"diffusionModel" => $diffusion->idDiffusion,
				"selectedItem" => $diffusion->settings->selectedItem,
				"nbMaxResults" => $diffusion->settings->nbMaxResults
			));
		}
	}

	public function getDiffusionItems(int $idDiffusion)
	{
		if (!DiffusionOrm::exist($idDiffusion)) {
			http_response_code(404);
			$this->ajaxError("Diffusion items not found");
		}
		$diffusion = new Diffusion($idDiffusion);
		$diffusion->fetchItem();
		//On ne récupère que les items de type notice pour le moment
		$items = $diffusion->item->getItemsFromType(TYPE_NOTICE);

		$this->ajaxJsonResponse($items);
	}

	/**
	 * Création d'une diffusion depuis l'opac
	 */
	public function saveFromOpac()
	{
		$diffusionPrivateModel = Diffusion::getDiffusionPrivateModel();
		if (! empty($diffusionPrivateModel)) {
			$diffusionPrivateModel->fetchView();
			$item = RootItem::getInstance($diffusionPrivateModel->settings->selectedItem);
			$searchData = new \stdClass();
			$searchData->search_serialize = $this->data->serializedSearch;
			$searchData->human_query = $this->data->humanQuery;
			$searchData->search = $this->data->search;
			//On met de coté les données originelles de l'item
			$originalData = $item->settings->selector->data;
			$originalNamespace = $item->settings->selector->namespace;
			//On change les données de l'item
			$item->settings->selector->data = $searchData;
			$item->settings->selector->namespace = static::DSI_PRIVATE_SELECTOR_NAMESPACE;
			$item->settings->userModifiedItem = true;

			$item->update();
			//On duplique la diffusion
			//On récupère l'item modifié avant de dupliquer
			$diffusionPrivateModel->fetchItem();

			//On duplique
			$toDuplicate = array(
				"view",
				"item",
				"channel"
			);
			$diffusionPrivate = $diffusionPrivateModel->duplicate($toDuplicate);

			$this->updateNewDiffusionPrivate($diffusionPrivate);
			$this->updateChannel($diffusionPrivate, $diffusionPrivateModel);
			$this->updateView($diffusionPrivate, $diffusionPrivateModel);
			$this->createEvent($diffusionPrivate);
			$this->createSubscriberList($diffusionPrivate);
			$diffusionPrivate->update();

			//On remet l'item à son état d'origine
			$item->settings->selector->data = $originalData;
			$item->settings->selector->namespace = $originalNamespace;
			unset($item->settings->userModifiedItem);
			$item->update();

			$this->ajaxJsonResponse($diffusionPrivate);
		}
	}

	protected function createEvent($diffusionPrivate)
	{
		$newEvent = RootEvent::getInstance();
		$newEvent->type = RootEvent::IDS_TYPE['Pmb\DSI\Models\Event\Periodical\PeriodicalEvent'];
		$newEvent->settings->periodical = "daily";
		$newEvent->settings->periodical_data = new \stdClass();
		$newEvent->settings->periodical_data->nbDays = intval($this->data->diffusionPrivatePeriodicity);

		$now = new \DateTime("now");
		$newEvent->settings->periodical_start = $now->format("Y-m-d");
		$newEvent->settings->periodical_time = $this->data->diffusionPrivateTime;
		$newEvent->settings->periodical_data->custom_dates = new \stdClass();
		$newEvent->settings->periodical_data->custom_dates->added_dates = array();
		$newEvent->settings->periodical_data->custom_dates->removed_dates = array();

		$newEvent->create();

		//on fait le lien avec la diffusion
		$eventDiffusionModel = new EventDiffusion($newEvent->idEvent, $diffusionPrivate->idDiffusion);
		$eventDiffusionModel->create();
	}

	protected function updateNewDiffusionPrivate(&$diffusionPrivate)
	{
		$diffusionPrivate->name = $this->data->diffusionPrivateName;
		$diffusionPrivate->settings->opacName = $this->data->diffusionPrivateName;
		//Ne doit pas être visible pour tous à l'opac
		$diffusionPrivate->settings->opacVisibility = false;
		$diffusionPrivate->settings->isPrivate = true;
		//On met un petit flag fonction du type d'emprunteur pour retrouver
		//Les diffusions privées propres à l'abonné
		switch ($this->data->emprType) {
			case "pmb":
				$diffusionPrivate->settings->idEmpr = intval($this->data->idEmpr);
				break;
			default:
				break;
		}
		$diffusionPrivate->automatic = 1;
		unset($diffusionPrivate->settings->diffusionModel);
		unset($diffusionPrivate->settings->selectedItem);
		unset($diffusionPrivate->settings->nbMaxResults);
	}

	protected function createSubscriberList(&$diffusionPrivate)
	{
		switch ($this->data->emprType) {
			case "pmb":
				$subscriberList = RootSubscriberList::getDiffusionSubscribers($diffusionPrivate->idDiffusion, 0);
				$emprList = RootSubscriberList::getSources(self::EMPR_TYPE);
				if (count($emprList) == 1) {
					$subscriberList->source->settings->subscriberListSource = new \stdClass();
					$subscriberList->source->settings->subscriberListSource->id = $emprList[0]["id"];
					$subscriberList->source->settings->subscriberListSource->name = $emprList[0]["name"];
					$subscriberList->source->settings->subscriberListSource->namespace = $emprList[0]["namespace"];
					$subscriberList->source->settings->subscriberListSource->subscriberListSelector = array(
						"data" => $this->data->idEmpr,
						"namespace" => static::DSI_PRIVATE_EMPR_SELECTOR_NAMESPACE
					);
					$subscriberList->source->create();
					$diffusionPrivate->numSubscriberList = $subscriberList->source->idSubscriberList;
				}
				break;
			default:
				break;
		}
	}

	protected function updateChannel(&$diffusionPrivate, $diffusionPrivateModel)
	{
		$channel = $diffusionPrivate->channel;
		//On verrouille le canal pour reprendre les évolution du paramétrage du modèle
		$channel->settings->locked = true;
		$channel->numModel = $diffusionPrivateModel->numChannel;
		$channel->update();
	}

	protected function updateView(&$diffusionPrivate, $diffusionPrivateModel)
	{
		global $dsi_private_bannette_nb_notices;
		$limit = intval($dsi_private_bannette_nb_notices);

		$view = $diffusionPrivate->view;

		//Limite : on prend la limite la plus basse entre le paramètre des alertes privées et le paramètre pmb
		if ($diffusionPrivateModel->settings->nbMaxResults < $limit) {
			$limit = $diffusionPrivateModel->settings->nbMaxResults;
		}

		$view->settings->limit = $limit;
		$view->update();
	}

	public function deleteFromOpac()
	{
		global $id_empr, $msg;

		if (!DiffusionOrm::exist($this->data->diffusionPrivate->id)) {
			http_response_code(404);
			$this->ajaxError($msg["diffusion_private_error_delete"]);
		}
		$diffusionPrivate = new Diffusion($this->data->diffusionPrivate->id);
		//Pour les petits malins qui voudraient supprimer d'autres diffusions que les leurs ;)
		if ($diffusionPrivate->settings->isPrivate && ($diffusionPrivate->settings->idEmpr == $id_empr)) {
			$result = $diffusionPrivate->delete();
			$this->ajaxJsonResponse($result);
			exit();
		}

		$this->ajaxError($msg["diffusion_private_error_delete"]);
		exit();
	}
}
