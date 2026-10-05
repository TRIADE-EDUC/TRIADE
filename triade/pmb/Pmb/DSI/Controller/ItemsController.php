<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ItemsController.php,v 1.38.4.2 2025/05/21 12:31:40 rtigero Exp $
namespace Pmb\DSI\Controller;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\DSIParserDirectory;
use Pmb\DSI\Models\Item\RootItem;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Orm\ItemOrm;

class ItemsController extends CommonController
{

	protected const VUE_NAME = "dsi/items";

	/**
	 *
	 * {@inheritdoc}
	 * @see CommonController::getBreadcrumb
	 */
	protected function getBreadcrumb(): string
	{
		global $msg, $action, $id;
		$breadcrum = "{$msg['dsi_menu']} {$msg['menu_separator']} {$msg['dsi_items']}";

		if (isset($action) && $action == "edit" && isset($id)) {
			if (ItemOrm::exist($id)) {
				$item = ItemOrm::findById($id);
				$breadcrum .= " {$msg['menu_separator']} {$item->name}";
			}
		}

		return $breadcrum;
	}

	/**
	 * Affichage de la liste d'item
	 */
	protected function defaultAction()
	{
		$data = array();
		$item = RootItem::getInstance();
		$data['list'] = $item->getList([
			"model" => "1"
		]);
		$data["types"] = HelperEntities::get_entities_labels();

		$this->render($data);
	}

	/**
	 * Affichage de création d'un item
	 */
	protected function addAction()
	{
		$this->render($this->getFormData());
	}

	/**
	 * Affichage d'édition d'un item
	 */
	protected function editAction()
	{
		global $id;

		$id = intval($id);

			$this->render($this->getFormData($id));
		}

	/**
	 * Récupération donnees formulaire ajout/édition
	 *
	 * @param int $id
	 * @return array
	 */
	protected function getFormData(int $id = 0): array
	{
		$data = array();
		$data["types"] = HelperEntities::get_entities_labels();
		$data["item"] = RootItem::getInstance($id);

		return $data;
	}

	/**
	 * Sauvegarde un item
	 */
	public function save()
	{
		$this->data->id = intval($this->data->id);

		$item = RootItem::getInstance($this->data->id);
		$result = $item->check($this->data);
		if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}

		$item->setFromForm($this->data);
		if (0 == $this->data->id) {
			$item->create();
		} else {
			$item->update();
		}

		$item->saveChilds();

		$this->ajaxJsonResponse($item);
		exit();
	}

	/**
	 * Supprime un item
	 */
	public function delete()
	{
		$item = new RootItem($this->data->id);
		$result = $item->delete();

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
	 * Retourne en AJAX la liste des sources selon le type d'un item
	 *
	 * @param number $id_type
	 */
	public function getSourceList($id_type)
	{
		$entities = HelperEntities::get_dsi_entities_namespace()[$id_type];
		$manifests = DSIParserDirectory::getInstance()->getManifests("./Pmb/DSI/Models/Source/Item/Entities/" . $entities . "/");
		$data = [];
		foreach ($manifests as $manifest) {
			$message = $manifest->namespace::getMessages();
			$data[] = [
				"namespace" => $manifest->namespace,
				"name" => $message['name']
			];
		}

		$this->ajaxJsonResponse($data);
	}

	/**
	 * Retourne en AJAX la liste des sélecteurs selon le namespace d'un type d'item
	 *
	 * @param string $namespace
	 */
	public function getSelectorList(string $namespace)
	{
		$namespace = str_replace("-", "\\", $namespace);

		$compatibility = DSIParserDirectory::getInstance()->getCompatibility($namespace);
		$selectors = array();
		if (isset($compatibility['selector'])) $selectors = $compatibility['selector'];

		$data = [];
		foreach ($selectors as $selector) {
			$message = $selector::getMessages();
			$data[] = [
				"namespace" => $selector,
				"name" => $message['name'],
				"messages" => $message
			];
		}

		$this->ajaxJsonResponse($data);
	}

	public function haveSubSelector(string $namespace)
	{
		$namespace = str_replace("-", "\\", $namespace);

		$compatibility = DSIParserDirectory::getInstance()->getCompatibility($namespace);
		$selectors = $compatibility["selectors"] ?? array();
		$this->ajaxJsonResponse(! (count($selectors) === 0));
	}

	/**
	 * Retourne en AJAX les données d'un item
	 *
	 * @param int $id
	 */
	public function getData(int $id = 0)
	{
		$item = RootItem::getInstance($id);
		$this->ajaxJsonResponse($item->getData());
	}

	/**
	 * Retourne en AJAX la liste des modèles d'items
	 */
	public function getModels()
	{
		$this->ajaxJsonResponse($this->fetchModels());
	}

	/**
	 * Retourne la liste des modèles d'items
	 *
	 * @return array[RootItem]
	 */
	protected function fetchModels(): array
	{
		return (new RootItem())->getList(["model" => "1"]);
	}

	/**
	 * Retourne en AJAX l'instance d'un model d'item
	 *
	 * @param int $idModel
	 */
	public function getModel(int $idModel = 0)
	{
		$this->ajaxJsonResponse(RootItem::getInstance($idModel));
	}

	/**
	 * Retourne en AJAX la liste de tout les items
	 */
	public function getItems()
	{
		$this->ajaxJsonResponse(RootItem::getInstance()->getList());
	}

	/**
	 * Retourne en AJAX la liste des vues compatibles selon un type d'item
	 *
	 * @param number $type
	 */
	public function getCompatibility($type)
	{
		$result = array();
		$typeNamespace = HelperEntities::get_item_from_type($type);
		if (empty($typeNamespace)) {
			$this->ajaxJsonResponse($result);
		}
		$compatibility = DSIParserDirectory::getInstance()->getCompatibility($typeNamespace);
		if (isset($compatibility['view'])) {
			foreach ($compatibility['view'] as $view) {
				$result[] = RootView::IDS_TYPE[$view];
			}
		}
		$this->ajaxJsonResponse($result);
	}

	/**
	 * relie un tag a l'entite
	 */
	public function unlinkTag()
	{
		$item = RootItem::getInstance();
		$delete = $item->unlinkTag($this->data->numTag, $this->data->numEntity);
		$this->ajaxJsonResponse($delete);
	}

	/**
	 * Supprime le lien entre un tag et une entite
	 */
	public function linkTag()
	{
		$item = RootItem::getInstance();
		$link = $item->linkTag($this->data->numTag, $this->data->numEntity);
		$this->ajaxJsonResponse($link);
	}

	/**
	 * Retourne en AJAX un instance vide d'un RootItem
	 */
	public function getEmptyInstance()
	{
		$this->ajaxJsonResponse(RootItem::getInstance());
	}

	/**
	 * Retourne en AJAX un instance d'un RootItem
	 */
	public function getInstance(int $id)
	{
		$this->ajaxJsonResponse(RootItem::getInstance($id));
	}

	/**
	 * Retourne la liste des filtres disponibles selon le type d'item
	 *
	 * @param string $type
	 * @return array
	 */
	public function availableFilters($type)
	{
		$typeNamespace = HelperEntities::get_item_from_type($type);
		$compatibility = DSIParserDirectory::getInstance()->getCompatibility($typeNamespace);
		$filters = array();
		if (isset($compatibility["filter"])) {
			foreach ($compatibility['filter'] as $filterNamespace) {
				if (! $filterNamespace::selfCheck()) {
					continue;
				}
				$message = $filterNamespace::getMessages();
				$filters[] = [
					"namespace" => $filterNamespace,
					"name" => $message['name'],
					"fields" => $filterNamespace::$fields,
					"messages" => $message
				];
			}
		}
		$this->ajaxJsonResponse($filters);
	}

	public function getSectionList()
	{
		$this->ajaxJsonResponse($this->getRecursiveSectionList());
	}

	protected function getRecursiveSectionList(int $parent = 0, int $lvl = 0): array
	{
		global $charset;
		$sectionList = [];

		$rqt = "select id_section, section_title from cms_sections where section_num_parent = '" . $parent . "' ORDER BY section_order";
		$res = pmb_mysql_query($rqt);
		if (pmb_mysql_num_rows($res)) {
			while ($row = pmb_mysql_fetch_object($res)) {
				$sectionList[] = [
					"label" => str_repeat("&nbsp;&nbsp;", $lvl) . htmlentities($row->section_title, ENT_QUOTES, $charset),
					"value" => $row->id_section
				];
				$sectionList = array_merge($sectionList, $this->getRecursiveSectionList($row->id_section, $lvl + 1));
			}
		}

		return $sectionList;
	}

	public function getWatchList()
	{
		$watches = [];

		$query = "SELECT id_watch, watch_title FROM docwatch_watches";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$watches[$row['id_watch']] = $row['watch_title'];
			}
		}

		$this->ajaxJsonResponse($watches);
	}

	public function getAllDiffusions()
	{
		$diffusion = new Diffusion();
		$this->ajaxJsonResponse($diffusion->getList());
	}

	public function duplicate()
	{
		if ($this->data->id != 0) {
			$itemToDuplicate = RootItem::getInstance($this->data->id);
			$newItem = $itemToDuplicate->duplicate();
			$this->ajaxJsonResponse($newItem);
		}
	}

	public function getItemsListLabel()
	{
		$item = new $this->data->namespace();
		$ids = $this->data->ids;
		if (is_a($ids, "stdClass")) {
			$ids = (array) $ids;
			$ids = array_keys($ids);
		}
		$this->ajaxJsonResponse($item->getLabels($ids));
	}

	/**
	 * Retourne une les donnees du selecteur a partir d'un formulaire d'item
	 */
	public function getItemsFromList()
	{
		if (empty($this->data->settings->selector->data)) {
			return $this->ajaxJsonResponse(array());
		}

		$selector = new $this->data->settings->selector->namespace($this->data->settings->selector);
		$this->ajaxJsonResponse($selector->getData());
	}

	public function deleteAll()
	{
		foreach ($this->data->ids as $id) {
			$item = RootItem::getInstance($id);
			$result = $item->delete();
			if ($result["error"]) {
				$this->ajaxError($result['errorMessage']);
			}
		}
		$this->ajaxJsonResponse([
			'success' => true
		]);
	}

	public function getFilterOptions(string $field)
	{
		$filtersManifests = DSIParserDirectory::getInstance()->getManifests("Pmb/DSI/Models/Filter/");
		foreach ($filtersManifests as $manifest) {
			$fields = $manifest->namespace::$fields;
			if (array_key_exists($field, $fields) && method_exists($manifest->namespace, "getOptions")) {
				$this->ajaxJsonResponse($manifest->namespace::getOptions());
				return;
			}
		}
		$this->ajaxJsonResponse([]);
	}

	public function exportModel($idModel = 0)
	{
		$idModel = intval($idModel);

		if ($idModel != 0) {
			$modelToExport = RootItem::getInstance($idModel);
			$modelToExport->export();
		}
	}

	public function importModel()
	{
		global $msg;

		if (isset($this->data->file) && !empty($this->data->file)) {
			$model = @unserialize($this->data->file);

			if ($model && $model instanceof RootItem) {
				$model->create();

				$this->cloneRecursiveChilds($model->childs, $model->id);

				$this->ajaxJsonResponse($model);
			}
		}

		$this->ajaxError($msg["dsi_model_import_error"]);
	}

	private function cloneRecursiveChilds($childs, $numParent)
	{
		foreach ($childs as $child) {
			$child->id = 0;
			$child->numParent = $numParent;

			$child->create();

			if (!empty($child->childs)) {
				$this->cloneRecursiveChilds($child->childs, $child->id);
				$child->saveChilds();
			}
		}

		return $childs;
	}

	public function getSelectorSorts($selectorNamespace)
	{
		$selectorNamespace = str_replace("-", "\\", $selectorNamespace);

		$compatibility = DSIParserDirectory::getInstance()->getCompatibility($selectorNamespace);
		$sorts = array();
		if (isset($compatibility["sort"])) {
			foreach ($compatibility["sort"] as $filterNamespace) {
				$message = $filterNamespace::getMessages();
				$fields = $filterNamespace::$fields;
				foreach ($fields as $fieldName => &$field) {
					if (! empty($field['callback'])) {
						$method = Helper::camelize($field['callback'] . " " . $fieldName);
						if (method_exists($filterNamespace, $method)) {
							$filterNamespace::$method($field);
						}
					}
				}
				$sorts[] = [
					"namespace" => $filterNamespace,
					"name" => $message['name'],
					"fields" => $fields,
					"messages" => $message
				];
			}
		}
		$this->ajaxJsonResponse($sorts);
	}

	public function getRecordCaddies()
	{
		$carts = \caddie::get_cart_list('NOTI');
		$this->ajaxJsonResponse($carts);
	}

	public function importModelTags()
	{
		$item = RootItem::getInstance($this->data->numEntity);
		$item->importModelTags();
		$this->ajaxJsonResponse($item->tags);
	}

	public function getItemEntityTree($idItem)
	{
		$item = RootItem::getInstance($idItem);
		$this->ajaxJsonResponse($item->getEntitiesTree());
	}
}
