<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootItem.php,v 1.38.2.1.2.2 2025/05/21 08:03:12 rtigero Exp $
namespace Pmb\DSI\Models\Item;

use encoding_normalize;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Helper\SubscriberHelper;
use Pmb\DSI\Models\DSIParserDirectory;
use Pmb\DSI\Models\Item\Aggregator\AggregatorItem;
use Pmb\DSI\Models\Root;
use Pmb\DSI\Orm\ItemOrm;

class RootItem extends Root implements Item
{

	protected const EXCLUDED_PROPERTIES = [
		"idItem",
		'numModel',
		'model'
	];

	public const TAG_TYPE = 6;

	protected $ormName = "Pmb\DSI\Orm\ItemOrm";

	public $idItem = 0;

	public $name = "";

	public $type = "";

	public $model = false;

	public $numModel = 0;

	public $settings = "";

	public $childs = [];

	public $tags = [];

	// ORM props
	public $numParent = 0;

	public $itemSource = "";

	public $removed = [];

	public $results;

	public $data;

	protected $modifiedType = null;

	public function __construct(int $id = 0)
	{
		$this->id = $id;
		if ($this->id) {
			$this->read();
		}
	}

	public static function getInstance(int $id = 0)
	{
		$orm = new ItemOrm($id);
		$checkId = $id;
		$settings = encoding_normalize::json_decode($orm->settings);
		if (isset($settings->locked) && $settings->locked) {
			$checkId = $orm->num_model;
		}
		if (empty($orm->type)) {
			$childs = ItemOrm::find("num_parent", $checkId);
			if (!empty($childs)) {
				return new AggregatorItem($id);
			}
		}
		return SimpleItem::getInstance($id);
	}

	public function read()
	{
		$this->fetchData();
		$this->fetchChilds();

		if (isset($this->settings->namespace) && class_exists($this->settings->namespace)) {
			$this->itemSource = new $this->settings->namespace($this->settings);
		}
	}

	public function check($data): array
	{
		if (!is_string($data->name)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:data_errors'
			];
		}

		/*
		 * if(!empty($data->name)) {
		 * $fields = ['name' => $data->name, 'model' => $data->model];
		 * if (!empty($data->id)) {
		 * $fields[$this->ormName::$idTableName] = [
		 * 'value' => $data->id,
		 * 'operator' => '!='
		 * ];
		 * }
		 * $result = $this->ormName::finds($fields);
		 * if (!empty($result)) {
		 * return [
		 * 'error' => true,
		 * 'errorMessage' => 'msg:item_duplicated'
		 * ];
		 * }
		 * }
		 */

		return [
			'error' => false,
			'errorMessage' => ''
		];
	}

	public function create()
	{
		$orm = new $this->ormName();
		$orm->name = $this->name;
		$orm->model = $this->model;
		$orm->type = intval($this->type);
		$orm->settings = json_encode($this->settings);
		$orm->num_model = $this->numModel;
		$orm->num_parent = $this->numParent;
		$orm->save();

		$this->id = $orm->{$this->ormName::$idTableName};
		$this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
		if (!empty($this->settings->locked)) {
			$this->deleteChilds();
		}
	}

	public function update()
	{
		$orm = new $this->ormName($this->id);
		$orm->name = $this->name;
		$orm->model = $this->model;
		$orm->settings = json_encode($this->settings);
		$orm->type = intval($this->type);
		$orm->num_model = $this->numModel;
		$orm->num_parent = $this->numParent;
		$orm->save();

		//On supprime les enfants si on n'est plus sur un aggregateur
		//Ou si on est sur un modèle verrouillé
		if (intval($this->type) != 0 || !empty($this->settings->locked)) {
			$this->deleteChilds();
		}
	}

	public function delete(): array
	{
		try {
			if (!$this->checkBeforeDelete()) {
				return [
					'error' => true,
					'errorMessage' => "msg:model_check_use"
				];
			}

			$orm = new $this->ormName($this->id);
			$this->deleteChilds();
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
		$this->name = '';
		$this->model = false;
		$this->settings = "";
		$this->type = 0;
		$this->numModel = 0;
		$this->numParent = 0;

		return [
			'error' => false,
			'errorMessage' => ''
		];
	}

	public function setFromForm(object $data)
	{
		$this->name = $data->name;
		$this->type = intval($data->type);
		$this->model = $data->model;
		$this->settings = $data->settings;
		$this->numModel = $data->numModel;
		$this->numParent = $data->numParent ?? 0;
		$this->childs = $data->childs ?? $this->childs;
	}

	public function fetchChilds()
	{
		if (0 != $this->id && empty($this->childs)) {
			$fields["num_parent"] = [
				'value' => $this->id,
				'operator' => '='
			];

			$result = $this->ormName::finds($fields);
			foreach ($result as $child) {
				$this->childs[] = RootItem::getInstance($child->id_item);
			}
		}
	}

	public function saveChilds()
	{
		$this->deleteNotFoundChilds();
		foreach ($this->childs as $child) {
			$childModel = self::getInstance(($child->id !== 0 ? $child->id : 0));

			$child->numParent = $this->id;
			$childModel->setFromForm($child);

			if ($child->id != 0) {
				$childModel->update();
			} else {
				$childModel->create();
				$child->id = $childModel->id;
			}

			$childModel->saveChilds();
		}
	}

	private function findChildById(int $id)
	{
		foreach ($this->childs as $child) {
			if ($id == $child->id) {
				return $child;
			}
		}
		return null;
	}

	public function deleteNotFoundChilds()
	{
		$fields["num_parent"] = [
			'value' => $this->id,
			'operator' => '='
		];
		$result = $this->ormName::finds($fields);
		foreach ($result as $elem) {
			if (empty($this->findChildById($elem->id_item))) {
				$childModel = RootItem::getInstance($elem->id_item);
				$childModel->delete();
			}
		}
	}

	/**
	 * Cette methode doit etre remplacee dans les sous-classes
	 *
	 * @return array
	 */
	public function getData()
	{
		// Do nothing
		return [];
	}

	/**
	 * Cette methode doit etre remplacee dans les sous-classes
	 *
	 * @return array
	 */
	public function getResults()
	{
		// Do nothing
		return [];
	}

	public function getFormatedItem()
	{
		return [
			static::class => array_keys($this->getData() ?? [])
		];
	}

	protected function deleteChilds()
	{
		$childs = $this->ormName::finds([
			"num_parent" => $this->id
		]);

		foreach ($childs as $child) {
			$child->delete();
		}
		$this->childs = [];
	}

	/**
	 *
	 * @param mixed $param
	 *        	Id de l'item parent
	 */
	public function duplicate($param = null)
	{
		$newEntity = static::getInstance($this->id);
		$newEntity->name = $this->getDuplicateName($this->name);
		$newEntity->id = 0;
		if (!is_object($newEntity->settings)) {
			$newEntity->settings = new \stdClass();
		}
		$newEntity->settings->oldId = $this->id;
		if (!empty($param)) {
			$newEntity->numParent = $param;
		}
		$newEntity->create();
		if ($newEntity->id != 0) {
			if (empty($this->settings->locked)) {
				$newEntity->childs = array();
				foreach ($this->childs as $child) {
					$newEntityChild = $child->duplicate($newEntity->id);
					if ($newEntityChild !== false) {
						$newEntity->childs[] = $newEntityChild;
					}
				}
			}
			if (!empty($newEntity->itemSource->selector)) {
				$newEntity->itemSource->selector->getSearchInput();
			}
			return $newEntity;
		}
		return false;
	}

	public function fetchChildById($id)
	{
		foreach ($this->childs as $child) {
			if ($id == $child->id) {
				return $child;
			}

			if (!empty($child->childs)) {
				$childId = $child->fetchChildById($id);
				if (isset($childId)) {
					return $childId;
				}
			}
		}
		return null;
	}

	public function getSearchInput()
	{
		return "";
	}

	public function getNbResults()
	{
		return 0;
	}

	/**
	 * Retourne la liste des items d'un type particulier récursivement
	 *
	 * @param $type int
	 * @return array
	 */
	public function getItemsFromType(int $type)
	{
		$result = array();
		if (count($this->childs)) {
			foreach ($this->childs as $child) {
				$childResults = $child->getItemsFromType($type);
				if (count($childResults)) {
					$result = array_merge($result, $childResults);
				}
			}
		}
		if ($this->type == $type) {
			$result[] = $this;
		}

		return $result;
	}

	public function fetchChildByOldId($oldId)
	{
		foreach ($this->childs as $child) {
			if (!empty($child->settings->oldId)) {
				if ($oldId == $child->settings->oldId) {
					return $child->id;
				}
			}

			if (!empty($child->childs)) {
				$childId = $child->fetchChildByOldId($oldId);
				if (isset($childId)) {
					return $childId;
				}
			}
		}
		return null;
	}
	/**
	 * Retourne l'arbre de données sans entité associée
	 * A dériver dans les classes enfant si besoin de personnaliser l'arbre
	 */
	public function getTree()
	{
		$item = new SimpleItem();
		return $item->getTree();
	}

	/**
	 * Retourne l'arbre de données des entités.
	 *
	 * @param array $entities Le tableau des entités.
	 * @param bool $top Indicateur si c'est l'appel de niveau supérieur.
	 * @return array L'arbre des entités.
	 */
	public function getEntitiesTree($entities = array(), $top = true)
	{
		if (!count($this->childs)) {
			$type = static::TYPE;
			if (empty($entity)) {
				$parseDirectory = DSIParserDirectory::getInstance();
				$manifests = $parseDirectory->getManifests(__DIR__ . "/Entities");
				$manifestFinds = array_filter($manifests, function ($manifest) use ($type) {
					return $manifest->namespace::TYPE == $type;
				});

				$manifest = current($manifestFinds);

				$item = new $manifest->namespace();
				$entity = $item->getTree(false);
			}
			return $entity;
		} else {
			$entity = [
				"var" => preg_replace("( )", "_", $this->name),
				"desc" => $this->name,
				"children" => []
			];

			foreach ($this->childs as $child) {
				$tree = $child->getEntitiesTree($entities, false);
				if (count($tree)) {
					if (!$top) {
						foreach ($tree as &$childTree) {
							$childTree["var"] = $entity["var"] . "." . $childTree["var"];
							$childTree["desc"] = $entity["desc"] . "." . $childTree["desc"];
						}
					}
					$entity["children"] = array_merge($entity["children"], $tree);
				}
			}

			if ($top) {
				//TODO revoir le système d'héritage de data structure
				$defaultVarsModel = new \frbr_entity_common_view_django();
				return array_merge($entity["children"], SubscriberHelper::getTree(), $defaultVarsModel->get_format_data_structure());
			} else {
				$entities[] = $entity;
				return $entities;
			}
		}
	}

	/**
	 * Retourne l'item dupliqué d'un item original
	 * @param integer $oldId
	 * @return Item | NULL
	 */
	public function getItemFromOldId($oldId)
	{
		if (!empty($this->settings->oldId) && ($oldId == $this->settings->oldId)) {
			return $this;
		}
		foreach ($this->childs as $child) {
			$childItem = $child->getItemFromOldId($oldId);
			if (isset($childItem)) {
				return $childItem;
			}
		}
		return null;
	}
}
