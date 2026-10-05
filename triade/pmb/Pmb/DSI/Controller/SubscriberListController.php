<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubscriberListController.php,v 1.34.2.2.2.2 2025/05/21 12:31:40 rtigero Exp $
namespace Pmb\DSI\Controller;

use Pmb\DSI\Models\SubscriberList\DiffusionSubscriberList;
use Pmb\DSI\Models\SubscriberList\ProductSubscriberList;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\DSI\Models\DSIParserDirectory;
use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Models\SubscriberList\Subscribers\Subscriber;
use Pmb\DSI\Models\SubscriberList\SubscriberListContent;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\SubscriberList\Subscribers\SubscriberListSubscriber;
use Pmb\DSI\Orm\SubscriberListContentOrm;
use Pmb\DSI\Orm\SubscriberListOrm;

class SubscriberListController extends CommonController
{

	protected const VUE_NAME = "dsi/subscriberList";

	/**
	 *
	 * {@inheritdoc}
	 * @see \Pmb\DSI\Controller\CommonController::getBreadcrumb()
	 */
	protected function getBreadcrumb()
	{
		global $msg, $action, $id;
		$breadcrum = "{$msg['dsi_menu']} {$msg['menu_separator']} {$msg['dsi_subscriber_list']}";

		if (isset($action) && $action == "edit" && isset($id)) {
			if (SubscriberListOrm::exist($id)) {
				$subscriberList = SubscriberListOrm::findById($id);
				$breadcrum .= " {$msg['menu_separator']} {$subscriberList->name}";
			}
		}

		return $breadcrum;
	}

	protected function defaultAction()
	{
		$data = array();
		$data['list'] = $this->fetchModels();
		print $this->render($data);
	}

	protected function addAction()
	{
		$data['subscriberList'] = RootSubscriberList::getSubscriberList();
		$data["types"] = HelperEntities::get_subscriber_entities();
		print $this->render($data);
	}

	protected function editAction()
	{
		global $id;

		$id = intval($id);
		if (SubscriberListOrm::exist($id)) {
			$data['subscriberList'] = RootSubscriberList::getSubscriberList($id);
			$data["types"] = HelperEntities::get_subscriber_entities();
			$this->render($data);
		} else {
			global $msg;
			$this->notFound(
				sprintf($msg['subscriber_list_not_found'], strval($id)),
				"./dsi.php?categ=subscriber_list"
			);
		}
	}

	public function delete()
	{
		$subscriberList = RootSubscriberList::getSubscriberList($this->data->source->id);
		$result = RootSubscriberList::deleteSubscriberList($subscriberList);

		if (! empty($result) && $result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}
		$this->ajaxJsonResponse([
			'success' => true
		]);
		exit();
	}

	private function getManifestById($id = 0) {}

	public function getSources($id = 0)
	{
		$this->ajaxJsonResponse(RootSubscriberList::getSources($id));
	}

	public function getSelectors($namespace = "")
	{
		$data = array();

		$namespace = str_replace("-", "\\", $namespace);

		$compatibility = DSIParserDirectory::getInstance()->getCompatibility($namespace);
		$selectors = $compatibility['selector'];

		foreach ($selectors as $selector) {
			$message = $selector::getMessages();
			$manifest = DSIParserDirectory::getInstance()->getManifestByNamespace($selector);
			$allowedInModels = isset($manifest->allowedInModels) ? intval($manifest->allowedInModels) : 1;
			$data[] = [
				"namespace" => $selector,
				"name" => $message['name'],
				"allowedInModels" => $allowedInModels
			];
		}
		$this->ajaxJsonResponse($data);
	}

	public function save()
	{
		//Selon qu'on enregistre uniquement la source ou la source avec sa liste
		if (isset($this->data->lists) && isset($this->data->source)) {
			$source = $this->data->source;
		} else {
			$source = $this->data;
		}
		$subscriberList = RootSubscriberList::getSubscriberList($source->id ?? 0);
		$result = $subscriberList->source->check($source);
		if (isset($result['error'])) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}

		$subscriberList->source->setFromForm($source);
		if (0 == $source->id) {
			$subscriberList->source->create();
			if ($subscriberList->source->id && $subscriberList->source->model && isset($this->data->lists)) {
				//On est sur une création de modele, on récupère les désinscrits
				foreach ($this->data->lists->subscribers as $subscriber) {
					$newSubscriber = new SubscriberListSubscriber();
					$newSubscriber->setFromForm($subscriber);
					$newSubscriber->setEntity($subscriberList->source->id);
					$newSubscriber->create();
				}
			}
		} else {
			$subscriberList->source->update();
		}
		//mise a jour des listes verrouillees si c'est un modele
		if ($subscriberList->source->model) {
			$diffusionList = new DiffusionSubscriberList();
			$diffusionList->updateLockedListsFromModel($subscriberList->source->id);

			$productList = new ProductSubscriberList();
			$productList->updateLockedListsFromModel($subscriberList->source->id);
		}
		$this->ajaxJsonResponse($subscriberList->source);
	}

	public function getEntity($idSubscriberList = 0)
	{
		$this->ajaxJsonResponse(RootSubscriberList::getSubscriberList($idSubscriberList));
	}

	public function getTypes()
	{
		return $this->ajaxJsonResponse(HelperEntities::get_subscriber_entities());
	}

	public function getModels()
	{
		return $this->ajaxJsonResponse($this->fetchModels());
	}

	protected function fetchModels()
	{
		$subscriberLists = RootSubscriberList::getAllSubscriberLists();
		$subscriberListModels = array();
		foreach ($subscriberLists as $list) {
			if ($list->source->model) {
				$subscriberListModels[] = $list->source;
			}
		}
		return $subscriberListModels;
	}

	public function getModel($idModel)
	{
		return $this->ajaxJsonResponse(RootSubscriberList::getSourceSubscriberList($idModel));
	}

	public function addSubscriber($idSubscriberList = 0)
	{
		$this->data->id = intval($this->data->id);
		$subscriber = Subscriber::getInstance($this->data->id, $this->data->type);
		$subscriber->setFromForm($this->data);
		//Gestion de la jointure
		$subscriberListContent = new SubscriberListContent($subscriber->idSubscriber, $idSubscriberList);
		if ((isset($subscriber->idSubscriber) && 0 == $idSubscriberList) || (isset($idSubscriberList) && 0 == $subscriber->idSubscriber)) {
			$subscriberListContent->create();
		} else {
			$subscriberListContent->update();
		}
		$this->ajaxJsonResponse($subscriber);
		exit();
	}

	/**
	 * Suppression du lien entre le subscriber et la liste
	 *
	 * @param int $idSubscriberList
	 */
	public function removeSubscriberFromList($idSubscriberList = 0)
	{
		$this->data->id = intval($this->data->id);
		if (!SubscriberListContentOrm::exist($this->data->id)) {
			http_response_code(404);
			$this->ajaxError("Lien introuvable");
			exit();
		}
		$subscriberListContent = new SubscriberListContent($this->data->id, $idSubscriberList);

		$result = $subscriberListContent->delete();

		if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}
		$this->ajaxJsonResponse([
			'success' => true
		]);
		exit();
	}

	/**
	 * Retourne une les donnees du selecteur a partir d'un formulaire de subscriberlist
	 */
	public function getSubscribersFromList()
	{
		if (empty($this->data->settings->subscriberListSource->subscriberListSelector->data)) {
			return $this->ajaxJsonResponse(array());
		}
		$subscriberListModel = RootSubscriberList::getSourceSubscriberList();
		$subscriberListModel->setFromForm($this->data);
		$this->ajaxJsonResponse($subscriberListModel->getSelectorData());
	}

	public function filterSubscribers($idSubscriberList, $channelType)
	{
		$subscriberList = RootSubscriberList::getSourceSubscriberList($idSubscriberList);
		$channelNamespace = array_search($channelType, RootChannel::IDS_TYPE);
		if ($channelNamespace === false) {
			return $this->ajaxJsonResponse($subscriberList->subscribers);
		}
		$subscriberList->filterList($channelNamespace::CHANNEL_REQUIREMENTS['subscribers']);
		return $this->ajaxJsonResponse($subscriberList->subscribers);
	}

	/**
	 * relie un tag a l'entite
	 */
	public function unlinkTag()
	{
		$subscriberList = RootSubscriberList::getSourceSubscriberList();
		$delete = $subscriberList->unlinkTag($this->data->numTag, $this->data->numEntity);
		return $this->ajaxJsonResponse($delete);
	}

	/**
	 * Supprime le lien entre un tag et une entite
	 */
	public function linkTag()
	{
		$subscriberList = RootSubscriberList::getSourceSubscriberList();
		$link = $subscriberList->linkTag($this->data->numTag, $this->data->numEntity);
		return $this->ajaxJsonResponse($link);
	}

	/**
	 * Retourne la liste des subscribers en base d'une subscriberList
	 *
	 * @param int $idSubscriberList
	 */
	public function getSubscribers(int $idSubscriberList)
	{
		$subscriberList = RootSubscriberList::getSourceSubscriberList($idSubscriberList);
		$this->ajaxJsonResponse($subscriberList->getSubscribersFromDatabase());
	}

	public function duplicate()
	{
		if ($this->data->id != 0) {
			$subscriberListToDuplicate = RootSubscriberList::getSubscriberList($this->data->id);
			$newSubscriberList = RootSubscriberList::getSubscriberList();
			$newSubscriberList->source = $subscriberListToDuplicate->source->duplicate();

			$newSubscriberList->lists->subscribers = array();
			foreach ($subscriberListToDuplicate->lists->subscribers as $subscriber) {
				$newSubscriberList->lists->subscribers[] = $subscriber->duplicate($newSubscriberList->source->id);
			}
			$newSubscriberList->source->source->selector->getSearchInput();
			$newSubscriberList->nbSubscribers = RootSubscriberList::getNbSubscribers($newSubscriberList);
			$this->ajaxJsonResponse($newSubscriberList);
		}
	}

	/**
	 * met a jour les listes verrouilles derivees de modeles
	 */
	public function updateLockedListsFromModel()
	{
		switch ($this->data->entityType) {
			case "diffusions":
				$diffusionList = new DiffusionSubscriberList();
				$updatedSubscribers = $diffusionList->updateLockedListsFromModel($this->data->idModel);
				break;
			case "products":
				$productList = new ProductSubscriberList();
				$updatedSubscribers = $productList->updateLockedListsFromModel($this->data->idModel);
				break;
			default:
				$this->ajaxError("Unknown Type");
				break;
		}
		$this->ajaxJsonResponse($updatedSubscribers);
	}

	public function deleteAll()
	{
		foreach ($this->data->ids as $id) {
			$subscriberList = RootSubscriberList::getSubscriberList($id);
			$result = RootSubscriberList::deleteSubscriberList($subscriberList);
			if ($result["error"]) {
				$this->ajaxError($result['errorMessage']);
			}
		}
		$this->ajaxJsonResponse(['success' => true]);
	}

	public function exportModel($idModel = 0)
	{
		$idModel = intval($idModel);

		if ($idModel != 0) {
			RootSubscriberList::exportSubscriberList($idModel);
		}
	}

	public function importModel()
	{
		global $msg;

		if (isset($this->data->file) && !empty($this->data->file)) {
			$model = @unserialize($this->data->file);
			if ($model && $model->source instanceof RootSubscriberList) {
				$model->source->create();

				foreach ($model->lists->subscribers as $subscriber) {
					$model->lists->subscribers[] = $subscriber->duplicate($model->source->id);
				}

				$model->source->source->selector->getSearchInput();

				$this->ajaxJsonResponse($model);
			}
		}

		$this->ajaxError($msg["dsi_model_import_error"]);
	}

	public function importModelTags()
	{
		$subscriberList = RootSubscriberList::getSourceSubscriberList($this->data->numEntity);
		$subscriberList->importModelTags();
		$this->ajaxJsonResponse($subscriberList->tags);
	}

	/**
	 * Réinitialise la liste d'abonnés
	 */
	public function empty()
	{
		switch ($this->data->entityType) {
			case "diffusions":
				$subscriberList = RootSubscriberList::getDiffusionSubscribers($this->data->entityId, $this->data->idSubscriberList);
				break;
			case "products":
				$subscriberList = RootSubscriberList::getProductSubscribers($this->data->entityId, $this->data->idSubscriberList);
				break;
		}
		//On réinitialise la source
		$subscriberList->source->reset();

		//On vide les subscribers
		$subscriber = Subscriber::getInstance($this->data->entityType);
		$subscriber->setEntity($this->data->entityId);
		$subscriber->emptySubscribers();
		$subscriberList->lists->subscribers = array();

		$this->ajaxJsonResponse($subscriberList);
	}
}
