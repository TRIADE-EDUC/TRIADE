<?php
namespace Pmb\DSI\Models;

use Pmb\Common\Models\Model;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Orm\EntitiesTagsOrm;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\DSI\Orm\TagOrm;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\Event\RootEvent;
use Pmb\DSI\Models\Item\RootItem;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Models\SubscriberList\SourceSubscriberList;
use Pmb\DSI\Orm\DiffusionOrm;
use Pmb\DSI\Orm\ProductOrm;

class Tag extends Model implements CRUD
{

	protected $ormName = "Pmb\DSI\Orm\TagOrm";

	protected $idTag = 0;

	public $name = "";

	public function __construct(int $id = 0)
	{
		$this->id = $id;
		if($this->id) {
			$this->read();
		}
	}

	public function create()
	{
		$orm = new $this->ormName();
		$orm->name = $this->name;
		$orm->save();

		$this->id = $orm->{$this->ormName::$idTableName};
		$this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
	}

	public function update()
	{
		$orm = new $this->ormName($this->id);
		$orm->name = $this->name;

		$orm->save();
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
				'errorMessage' => 'msg:tag_duplicated'
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
	}

	public function read()
	{
		$this->fetchData();
	}

	public function delete()
	{
		try {
			$orm = new $this->ormName($this->id);
			//Suppression des liens
			$links = EntitiesTagsOrm::finds([
				'num_tag' => $this->id
			]);

			foreach ($links as $link) {
				$link->delete();
			}

			$orm->delete();
		} catch (\Exception $e) {
			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}
	}

	public function getRelatedEntities($numTag)
	{
		$relatedLinks = EntitiesTagsOrm::finds([
			"num_tag" => $numTag
		]);
		return self::getEntityFromTag($relatedLinks);
	}

	public static function getEntityFromTag(array $links)
	{
		global $msg;
		$result = array();
		$result['diffusion'] = [
			"label" => "dsi_diffusions",
			"entities" => array()
		];
		$result['product'] = [
			"label" => "dsi_products",
			"entities" => array()
		];
		foreach ($links as $relatedLink) {
			switch ($relatedLink->type) {
				case SourceSubscriberList::TAG_TYPE:
					$list = RootSubscriberList::getSourceSubscriberList($relatedLink->num_entity);
					if (empty($list->name)) {
						$products = ProductOrm::find("num_subscriber_list", $list->id);
						foreach ($products as $product) {
							$result['product']['entities'][] = $product->name . ' (' . $msg['dsi_subscriber_list'] . ')';
						}
						$result['diffusion']['entities'] = $result['diffusion']['entities'] = array_merge($result['diffusion']['entities'], self::getLabelFromParents($list->id, 'num_subscriber_list', $msg['dsi_subscriber_list']));
					} else {
						if (! isset($result['subscriberList'])) {
							$result['subscriberList'] = [
								"label" => "dsi_subscriber_list",
								"entities" => array()
							];
						}
						$result['subscriberList']['entities'][] = $list->name;
					}
					break;
				case RootChannel::TAG_TYPE:
					$list = RootChannel::getInstance($relatedLink->num_entity);
					if (empty($list->name)) {
						$result['diffusion']['entities'] = array_merge($result['diffusion']['entities'], self::getLabelFromParents($list->id, 'num_channel', $msg['dsi_channels']));
					} else {
						if (! isset($result['channel'])) {
							$result['channel'] = [
								"label" => "dsi_channels",
								"entities" => array()
							];
						}
						$result['channel']['entities'][] = $list->name;
					}
					break;
				case RootEvent::TAG_TYPE:
					$list = RootEvent::getInstance($relatedLink->num_entity);
					if (empty($list->name)) {
						$result['diffusion']['entities'] = array_merge($result['diffusion']['entities'], self::getLabelFromParents($list->id, 'num_event', $msg['dsi_triggers']));
					} else {
						if (! isset($result['event'])) {
							$result['event'] = [
								"label" => "dsi_triggers",
								"entities" => array()
							];
						}
						$result['event']['entities'][] = $list->name;
					}
					break;
				case RootItem::TAG_TYPE:
					$list = RootItem::getInstance($relatedLink->num_entity);
					if (empty($list->name)) {
						$result['diffusion']['entities'] = array_merge($result['diffusion']['entities'], self::getLabelFromParents($list->id, 'num_item', $msg['dsi_items']));
					} else {
						if (! isset($result['item'])) {
							$result['item'] = [
								"label" => "dsi_items",
								"entities" => array()
							];
						}
						$result['item']['entities'][] = $list->name;
					}
					break;
				case RootView::TAG_TYPE:
					$list = RootView::getInstance($relatedLink->num_entity);
					if (empty($list->name)) {
						$result['diffusion']['entities'] = array_merge($result['diffusion']['entities'], self::getLabelFromParents($list->id, 'num_view', $msg['dsi_views']));
					} else {
						if (! isset($result['view'])) {
							$result['view'] = [
								"label" => "dsi_views",
								"entities" => array()
							];
						}
						$result['view']['entities'][] = $list->name;
					}
					break;
				case Diffusion::TAG_TYPE:
					if (! isset($result['diffusion'])) {
						$result['diffusion'] = [
							"label" => "dsi_diffusions",
							"entities" => array()
						];
					}
					$diffusion = new Diffusion($relatedLink->num_entity);
					$result['diffusion']['entities'][] = $diffusion->name;
					break;
				case Product::TAG_TYPE:
					if (! isset($result['product'])) {
						$result['product'] = [
							"label" => "dsi_products",
							"entities" => array()
						];
					}
					$product = new Product($relatedLink->num_entity);
					$result['product']['entities'][] = $product->name;
					break;
			}
		}
		//On doit unset les diffusions et produits si vides car on doit les set avant le traitement
		if (! count($result['diffusion']['entities'])) {
			unset($result['diffusion']);
		}
		if (! count($result['product']['entities'])) {
			unset($result['product']);
		}
		return $result;
	}

	public static function getLabelFromParents($id, $foreignKey, $type)
	{
		global $msg;
		$result = array();
		$diffusions = DiffusionOrm::find($foreignKey, $id);
		foreach ($diffusions as $diffusion) {
			$result[] = $diffusion->name . ' (' . $type . ')';
		}
		return $result;
	}

	public function getTags()
	{
		$result = array();
		$tags = TagOrm::findAll();
		foreach ($tags as $tag) {
			$result[] = new self($tag->id_tag);
		}
		return $result;
	}
}

