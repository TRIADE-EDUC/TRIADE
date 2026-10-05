<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionHistory.php,v 1.38.2.4.2.4 2025/05/26 12:13:31 rtigero Exp $
namespace Pmb\DSI\Models;

use Pmb\Common\Models\Model;
use Pmb\DSI\Helper\LookupHelper;
use Pmb\DSI\Helper\SubscriberHelper;
use Pmb\DSI\Models\CRUD;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Orm\ContentHistoryOrm;
use Pmb\DSI\Orm\ContentBufferOrm;
use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\DSI\Models\Item\RootItem;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Models\Item\SimpleItem;
use Pmb\Common\Helper\HTML;
use Pmb\DSI\Models\View\WYSIWYGView\WYSIWYGView;
use Pmb\DSI\Orm\SendQueueOrm;

class DiffusionHistory extends Model implements CRUD
{

	public static $instances = array();

	/**
	 * Diffusion a initialiser (Dans le cas d'un déclencheur manuel)
	 * Aucune donnee n'est enregistrer
	 */
	public const INITIALIZED = 0;

	/**
	 * Diffusion a valider, les donnee sont enregistrees
	 */
	public const TO_VALIDATE = 1;

	/**
	 * Diffusion valider (modifie au besoin), pret a l'envoi
	 */
	public const VALIDATED = 2;

	/**
	 * Diffusion envoyee
	 */
	public const SENT = 3;

	/**
	 * Diffusion envoyee
	 */
	public const RESET = 4;

	/**
	 * Diffusion sans donnée dans l'historique
	 */
	public const NODATA = 5;

	public const ITEM_SAVE_IDS = true;

	protected $ormName = "Pmb\DSI\Orm\DiffusionHistoryOrm";

	public $idDiffusionHistory = 0;

	public $numDiffusion = 0;

	public $diffusion = null;

	public $date = null;

	public $formatedDate = null;

	public $totalRecipients = 0;

	public $state = 0;

	public $contentHistory = [];

	public $contentBuffer = [];

	public function __construct(int $id = 0, ?Diffusion $diffusion = null)
	{
		$this->id = $id;

		if (! empty($diffusion)) {
			$this->setDiffusion($diffusion);
		}

		$this->read();
	}

	public function read()
	{
		$this->fetchData();
		$this->fetchRelations();
		$this->formatedDate = $this->getFormatedDate();
	}

	/**
	 * Pour la creation, voir la fonction init()
	 *
	 * @return void
	 */
	public function create($buffer = true)
	{
		$orm = new $this->ormName();
		$orm->num_diffusion = $this->numDiffusion;
		$orm->date = $this->date;
		$orm->total_recipients = $this->totalRecipients;
		$orm->state = $this->state;

		$orm->save();
		$this->id = $orm->{$this->ormName::$idTableName};
		$this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
		$this->saveContentBuffer();
	}

	/**
	 * Pour l'update, voir les fonctions toValidate(), validate(), sent()
	 *
	 * @return void
	 */
	public function update()
	{
		$orm = new $this->ormName($this->id);
		$orm->num_diffusion = $this->numDiffusion;
		$orm->date = $this->date;
		$orm->total_recipients = $this->totalRecipients;
		$orm->state = $this->state;

		$orm->save();
		$this->saveContentBuffer();
	}

	public function delete()
	{
		$this->cleanBuffer();

		$this->fetchContents();
		if (! empty($this->contentHistory)) {
			foreach ($this->contentHistory as $contentHistoryList) {
				array_walk($contentHistoryList, function ($contentHistory) {
					$contentHistory->delete();
				});
			}
		}

		$orm = new $this->ormName($this->id);
		$orm->delete();
	}

	protected function fetchRelations()
	{
		$this->fetchDiffusion();
		$this->fetchContents();
		$this->fetchContentBuffer();
	}

	private function fetchDiffusion()
	{
		if ((! isset($this->diffusion) && $this->numDiffusion) || (isset($this->diffusion) && $this->diffusion->id != $this->numDiffusion)) {
			$this->diffusion = Diffusion::getInstance($this->numDiffusion);
		}
	}

	private function fetchContents()
	{
		$this->contentHistory = [];

		$contentHistoryOrm = ContentHistoryOrm::find("num_diffusion_history", $this->id);
		while (count($contentHistoryOrm) > 0) {
			$history = array_shift($contentHistoryOrm);
			$this->contentHistory[$history->type][] = new ContentHistory($history->id_content_history);
		}
	}

	private function fetchContentBuffer()
	{
		$this->contentBuffer = [];

		$contentBufferOrm = ContentBufferOrm::find("num_diffusion_history", $this->id);
		while (count($contentBufferOrm) > 0) {
			$buffer = array_shift($contentBufferOrm);
			$this->contentBuffer[$buffer->type][] = new ContentBuffer($buffer->id_content_buffer);
		}
		$this->setupViewsBuffer();
	}

	/**
	 * La diffusion a bien été diffuser
	 * On vide le buffer et on remplit notre historique par le contenu du buffer si il est active
	 *
	 * @return void
	 */
	public function saveContentHistory()
	{
		$this->clearBuffer();

		foreach ($this->contentBuffer as $contentBuffer) {
			array_walk($contentBuffer, function ($contentBuffer) {
				$contentHistory = new ContentHistory();
				$contentHistory->id = 0;
				$contentHistory->type = $contentBuffer->type;
				$contentHistory->content = $contentBuffer->content;
				$contentHistory->numDiffusionHistory = $this->id;
				$contentHistory->create();
			});
		}
	}

	public function saveContentBuffer()
	{
		$this->clearBuffer();
		foreach ($this->contentBuffer as $contentBuffer) {
			foreach($contentBuffer as $buffer) {
				$buffer->id = 0;
				$buffer->numDiffusionHistory = $this->id;
				if($buffer->type == ContentBuffer::CONTENT_TYPES_VIEW) {
					// if(! empty($buffer->content["childs"])) {
					// 	$buffer->content["childs"] = [];
					// }
				}
				$buffer->create();
			}
		}
	}

	public function getFormatedDate()
	{
		if (empty($this->date)) {
			return "";
		}

		if (! $this->date instanceof \DateTime) {
			$date = new \DateTime($this->date);
		} else {
			$date = $this->date;
		}

		return $date->format(GlobalContext::msg('dsi_format_date'));
	}

	/**
	 * Creer une DiffusionHistory pour les envois manuels
	 * Aucune donnees n'est sauvegarder
	 *
	 * @return void
	 */
	public function init()
	{
		$this->updateDate();
		$this->state = DiffusionHistory::INITIALIZED;
		$this->create(false);
	}

	/**
	 * Passe la DiffusionHistory a "a valider", puis on fait les sauvegardes
	 *
	 * @return void
	 */
	public function toValidate()
	{
		$this->updateDate();
		$this->state = DiffusionHistory::TO_VALIDATE;

		$this->diffusion->fetchView();
		$this->diffusion->fetchItem();
		$this->diffusion->fetchChannel();
		$this->diffusion->fetchSubscriberList();
		$this->diffusion->fetchEvents();
		$this->diffusion->fetchDiffusionDescriptors();

		$subscriberList = $this->diffusion->getSubscribers();
		$subscribers = RootSubscriberList::getSubscriberListToSend($subscriberList, $this->diffusion->channel::CHANNEL_REQUIREMENTS["subscribers"]) ?? [];

		$this->totalRecipients = count($subscribers) ?? 0;

		$this->contentBuffer = [];

		$this->addContentItem($this->diffusion->item);
		$this->addContentSubscriberList($subscriberList);
		$this->addContentView($this->diffusion->view);
		$this->addContentChannel($this->diffusion->channel);

		if (! $this->id) {
			$this->create();
		} else {
			$this->update();
		}
	}

	/**
	 * Remet tout à zero via la diffusion donnée
	 *
	 * @return void
	 */
	public function reset()
	{
		if ($this->state != DiffusionHistory::TO_VALIDATE) {
			// On doit etre en statut "a valider" pour faire un reset des donnees
			throw new \Exception("Invalid state change to state " . DiffusionHistory::TO_VALIDATE);
		}

		$this->toValidate();
	}

	/**
	 * Passe la DiffusionHistory a "valider" (pret a l'envoi)
	 *
	 * @param Diffusion $diffusion
	 * @return void
	 */
	public function validate()
	{
		if (DiffusionHistory::TO_VALIDATE != $this->state) {
			throw new \Exception("Invalid state change to state " . DiffusionHistory::TO_VALIDATE);
		}

		$this->updateDate();
		$this->state = DiffusionHistory::VALIDATED;
		$this->update();
	}

	/**
	 * DiffusionHistory envoye, ont fait le menage
	 *
	 * @param Diffusion $diffusion
	 */
	public function sent()
	{
		global $dsi_send_automatically;

		if (DiffusionHistory::VALIDATED != $this->state) {
			throw new \Exception("Invalid state change to state " . DiffusionHistory::VALIDATED);
		}

		$diffusionHistoryParser = new DiffusionHistoryParser();
		$item = $diffusionHistoryParser->parseItem($this->contentBuffer[ContentHistory::CONTENT_TYPES_ITEM]);
		$view = $diffusionHistoryParser->parseView($this->contentBuffer[ContentHistory::CONTENT_TYPES_VIEW]);
		$subscribers = $diffusionHistoryParser->parseSubscribers($this->contentBuffer[ContentHistory::CONTENT_TYPES_SUBSCRIBER]);
		$channel = $diffusionHistoryParser->parseChannel($this->contentBuffer[ContentHistory::CONTENT_TYPES_CHANNEL]);

		//On reset les donnees qui vont bien
		$this->diffusion->item = $item;
		$this->diffusion->view = $view;
		$this->diffusion->subscriberList = $subscribers;
		$this->diffusion->channel = $channel;

		$sent = true;
		$render = $view->render($item, $this->diffusion->id, $view->settings->limit ?? 0, "DiffusionPending");
		if ($channel->sendManually()) {

			if (empty($dsi_send_automatically)) {
				$sendQueueOrm = SendQueueOrm::find("num_diffusion_history", $this->id);
				if (empty($sendQueueOrm)) {

					$emprIds = [];
					$subscriberIds = [];
					foreach ($subscribers as $subscriber) {
						if (!empty($subscriber->id)) {
							$subscriberIds[] = $subscriber->id;
							continue;
						}

						$emprIds[] = $subscriber->settings->idEmpr;
					}

					SendQueue::fillQueue($subscriberIds, $emprIds, $this->id, $channel->type);
				}

				$nextQueueElements = SendQueue::getNext($this->id);

				$nextSubscribers = [];
				foreach ($nextQueueElements as $element) {
					foreach ($subscribers as $subscriber) {

						if (
							$element->numSubscriberDiffusion != 0 && isset($subscriber->id) &&
							$subscriber->id == $element->numSubscriberDiffusion
						) {
							$nextSubscribers[] = $subscriber;
							continue;
						}

						if (
							$element->numSubscriberEmpr != 0 && isset($subscriber->settings->idEmpr) &&
							$subscriber->settings->idEmpr == $element->numSubscriberEmpr
						) {
							$nextSubscribers[] = $subscriber;
						}
					}
				}

				$this->diffusion->currentHistory = $this;
			} else {
				$nextSubscribers = $subscribers;
			}


			$channel->setTitle($this->diffusion->name);
			$sent = $channel->send($nextSubscribers, $render, $this->diffusion);

			if (!$sent) {
				return false;
			}

			if (empty($dsi_send_automatically)) {
				SendQueue::flagNext($this->id);
				SendQueue::cleanQueue($this->id);

				$remainingElements = SendQueue::getRemaining($this->id);
				if (!empty($remainingElements)) {
					return true;
				}
			}
		}
		//Pour conserver les données issues des lookups H2o dans le cas des DSI privées
		//Peut mieux faire mais ça fait le taff
		if (is_string($render)) {
			if (isset($this->diffusion->settings->isPrivate) && $this->diffusion->settings->isPrivate) {
				$render = LookupHelper::format(SubscriberHelper::format($render, $subscribers[0], false, $this->diffusion), $this->diffusion);
			} else {
				$render = LookupHelper::format($render, $this->diffusion, false);
			}
		}

		$this->addContentRenderView($render);
		$this->cleanContentItem();
		$this->cleanContentSubscriberList();

		// On update le channel pour les stats
		$this->cleanContentChannel();
		$this->addContentChannel($this->diffusion->channel);

		$this->updateDate();
		$this->state = DiffusionHistory::SENT;

		$this->update();

		// On enregistre l'historique
		$this->saveContentHistory();
		$this->diffusion->checkCountHistorySaved();

		return true;
	}

	public function addContentItem(RootItem $item)
	{
		if (!($item instanceof RootItem)) {
			return;
		}

		$childs = [];
		if (! empty($item->childs)) {
			$childs = $item->childs;
			$item->childs = [];
		}

		SimpleItem::$ignoreResultsToArray = false;
		$content = Helper::toArray($item, "");
		SimpleItem::$ignoreResultsToArray = true;

		$content['removed'] = [];

		$contentBuffer = new ContentBuffer();
		$contentBuffer->setContent($content);
		$contentBuffer->type = ContentBuffer::CONTENT_TYPES_ITEM;
		$this->contentBuffer[ContentBuffer::CONTENT_TYPES_ITEM][] = $contentBuffer;

		foreach ($childs as $itemChild) {
			$this->addContentItem($itemChild);
		}
	}

	public function addContentSubscriberList($subscriberList)
	{
		$subscribers = RootSubscriberList::getSubscriberListToSend($subscriberList, $this->diffusion->channel::CHANNEL_REQUIREMENTS["subscribers"]);
		$contentBuffer = new ContentBuffer();
		$contentBuffer->setContent(Helper::toArray($subscribers));
		$contentBuffer->type = ContentBuffer::CONTENT_TYPES_SUBSCRIBER;
		$this->contentBuffer[ContentBuffer::CONTENT_TYPES_SUBSCRIBER][] = $contentBuffer;
	}

	public function addContentChannel(RootChannel $channel)
	{
		if (!($channel instanceof RootChannel)) {
			return;
		}

		$contentBuffer = new ContentBuffer();
		$contentBuffer->setContent(Helper::toArray($channel));
		$contentBuffer->type = ContentBuffer::CONTENT_TYPES_CHANNEL;
		$this->contentBuffer[ContentBuffer::CONTENT_TYPES_CHANNEL][] = $contentBuffer;
	}

	public function addContentView(RootView $view)
	{
		if (!($view instanceof RootView)) {
			return;
		}

		$childs = [];
		if (! empty($view->childs)) {
			$childs = $view->childs;
			$view->childs = [];
		}

		$contentBuffer = new ContentBuffer();
		$contentBuffer->setContent(Helper::toArray($view, ""));
		$contentBuffer->type = ContentBuffer::CONTENT_TYPES_VIEW;
		$this->contentBuffer[ContentBuffer::CONTENT_TYPES_VIEW][] = $contentBuffer;

		foreach ($childs as $viewChild) {
			$this->addContentView($viewChild);
		}
	}

	public function addContentRenderView($renderView)
	{
		if (! empty($this->contentBuffer[ContentBuffer::CONTENT_TYPES_RENDER_VIEW])) {
			$this->contentBuffer[ContentBuffer::CONTENT_TYPES_RENDER_VIEW][0]->delete();
		}
		$this->contentBuffer[ContentBuffer::CONTENT_TYPES_RENDER_VIEW] = [];

		$contentBuffer = new ContentBuffer();
		$contentBuffer->setContent([
			"render" => $renderView
		]);
		$contentBuffer->type = ContentBuffer::CONTENT_TYPES_RENDER_VIEW;

		$this->contentBuffer[ContentBuffer::CONTENT_TYPES_RENDER_VIEW][] = $contentBuffer;
	}

	public function cleanContentItem()
	{
		$this->contentBuffer[ContentBuffer::CONTENT_TYPES_ITEM] = [];
	}

	public function cleanContentChannel()
	{
		$this->contentBuffer[ContentBuffer::CONTENT_TYPES_CHANNEL] = [];
	}

	public function cleanContentSubscriberList()
	{
		$props = $this->diffusion->channel::CHANNEL_REQUIREMENTS['subscribers'];
		$props = array_keys($props);

		foreach ($this->contentBuffer[ContentBuffer::CONTENT_TYPES_SUBSCRIBER] as $contentBuffer) {
			array_walk($contentBuffer->content, function (&$subscriber) use ($props) {
				foreach ($subscriber->settings as $prop => $value) {
					if (! in_array($prop, $props)) {
						unset($subscriber->settings->{$prop});
					}
				}
			});
		}
	}

	public function previewView()
	{
		if (empty($this->contentHistory) || ! isset($this->contentHistory[ContentHistory::CONTENT_TYPES_RENDER_VIEW])) {
			return "";
		}

		$contentHistory = $this->contentHistory[ContentHistory::CONTENT_TYPES_RENDER_VIEW][0] ?? null;
		return $contentHistory ? HTML::formatRender($contentHistory->content->render, $this->diffusion->name ?? "") : "";
	}

	private function updateDate()
	{
		$this->date = (new \DateTime())->format('Y-m-d H:i:s');
		$this->formatedDate = $this->getFormatedDate();
	}

	/**
	 * Permet creer/mettre a jour l'historique en fonction du statut passe
	 *
	 * @param integer $state
	 * @return mixed|void
	 * @throws \InvalidArgumentException
	 */
	public function state(int $state)
	{
		switch ($state) {
			case DiffusionHistory::INITIALIZED:
				if ($this->state != DiffusionHistory::INITIALIZED) {
					// L'historique precedent n'a pas ete envoye
					throw new \InvalidArgumentException("[DiffusionHistory] Invalid sequence state !");
				}
				$this->init();
				break;

			case DiffusionHistory::TO_VALIDATE:

				if ($this->state == DiffusionHistory::VALIDATED) {
					$this->state = DiffusionHistory::TO_VALIDATE;
					$this->update();
					break;
				}

				if ($this->state != DiffusionHistory::INITIALIZED) {
					throw new \InvalidArgumentException("[DiffusionHistory] Invalid sequence state !");
				}
				$this->toValidate();
				break;

			case DiffusionHistory::VALIDATED:
				if ($this->state != DiffusionHistory::TO_VALIDATE) {
					throw new \InvalidArgumentException("[DiffusionHistory] Invalid sequence state !");
				}
				$this->validate();
				break;

			case DiffusionHistory::SENT:
				if ($this->state != DiffusionHistory::VALIDATED) {
					throw new \InvalidArgumentException("[DiffusionHistory] Invalid sequence state !");
				}
				return $this->sent();

			case DiffusionHistory::RESET:
				if ($this->state != DiffusionHistory::TO_VALIDATE) {
					throw new \InvalidArgumentException("[DiffusionHistory] Invalid sequence state !");
				}
				$this->reset();
				break;

			default:
				throw new \InvalidArgumentException("[DiffusionHistory] Invalid state !");
		}
	}

	/**
	 * Permet de refaire l'envoi
	 *
	 * @throws \RuntimeException
	 * @return mixed
	 */
	public function send()
	{
		if (DiffusionHistory::SENT != $this->state) {
			throw new \RuntimeException("Invalid state change to state " . DiffusionHistory::VALIDATED);
		}

		$diffusionHistoryParser = new DiffusionHistoryParser();
		$contentHistoryRenderView = $this->contentHistory[ContentHistory::CONTENT_TYPES_RENDER_VIEW][0];
		$subscribers = $diffusionHistoryParser->parseSubscribers($this->contentHistory[ContentHistory::CONTENT_TYPES_SUBSCRIBER]);
		$channel = $diffusionHistoryParser->parseChannel($this->contentHistory[ContentHistory::CONTENT_TYPES_CHANNEL]);
		$channel->setTitle($this->diffusion->name);

		return $channel->send($subscribers, Helper::toArray($contentHistoryRenderView->content->render), $this->diffusion);
	}

	public function setDiffusion(Diffusion $diffusion)
	{
		$this->diffusion = $diffusion;
		$this->numDiffusion = $diffusion->id;
	}

	public function getPendingList()
	{
		$list = $this->getList([
			"state" => [
				"operator" => "not in",
				"value" => [DiffusionHistory::SENT, DiffusionHistory::NODATA],
			]
		]);

		for ($i = 0; $i < count($list); $i++) {

			// Optimisation on enlève les données des views que nous n'utilisons pas pour eviter les dépassements mémoires
			if (is_countable($list[$i]->contentBuffer) && count($list[$i]->contentBuffer) > 0) {
				unset($list[$i]->contentBuffer[ContentBuffer::CONTENT_TYPES_VIEW]);
			}

			if (! empty($list[$i]->diffusion->settings->isPrivate)) {
				array_splice($list, $i, 1);
				$i--;
				continue;
			}
		}
		return $list;
	}

	public function getChannel()
	{
		$diffusionHistoryParser = new DiffusionHistoryParser();
		return $diffusionHistoryParser->parseChannel($this->contentHistory[ContentHistory::CONTENT_TYPES_CHANNEL]);
	}

	public static function getInstance(int $id = 0, ?Diffusion $diffusion = null)
	{
		static::$instances[static::class] = static::$instances[static::class] ?? [];

		if (isset(static::$instances[static::class][$id])) {
			$instance = static::$instances[static::class][$id];
		} else {
			$instance = new static($id, $diffusion);
			static::$instances[static::class][$id] = $instance;
		}

		return $instance;
	}

	/**
	 * Retourne la liste des diffusions publiques
	 *
	 * @return array
	 */
	public function getFilteredList($params)
	{
		$list = $this->getList($params);
		for ($i = 0; $i < count($list); $i++) {

			// Optimisation on enlève les données des views que nous n'utilisons pas pour eviter les dépassements mémoires
			if (is_countable($list[$i]->contentHistory) && count($list[$i]->contentHistory) > 0) {
				unset($list[$i]->contentHistory[ContentHistory::CONTENT_TYPES_VIEW]);
			}

			if (! empty($list[$i]->diffusion->settings->isPrivate)) {
				array_splice($list, $i, 1);
				$i--;
				continue;
			}
		}
		return $list;
	}

	/**
	 * Préparation des vues issues du buffer
	 * @return void
	 */
	protected function setupViewsBuffer()
	{
		if (! is_array($this->contentBuffer) || ! array_key_exists(ContentBuffer::CONTENT_TYPES_VIEW, $this->contentBuffer)) {
			return;
		}

		$viewsBuffer = $this->contentBuffer[ContentBuffer::CONTENT_TYPES_VIEW];
		$views = array();
		if (empty($viewsBuffer)) {
			return;
		}

		//On se fait un petit tableau plus facile à manipuler
		foreach ($viewsBuffer as $viewBuffer) {
			if ($viewBuffer->content && $viewBuffer->content->id) {
				$views[$viewBuffer->content->id] = $viewBuffer->content;
			}
		}

		//On rend son enfant au parent
		foreach ($views as $view) {
			if ($view->numParent && array_key_exists($view->numParent, $views)) {
				if(empty($views[$view->numParent]->childs)) {
					$views[$view->numParent]->childs[] = $view;
					continue;
				}
				$i = array_search($view->id, array_column($views[$view->numParent]->childs, "id"));
				if($i !== false) {
					$view = $views[$view->numParent]->childs[$i];
				}
			}
		}
	}

	protected function updateImportViewBlocks(&$parentBlocks, &$childBlocks, $childId)
	{
		foreach($parentBlocks as $parentBlock) {
			if(!empty($parentBlock->blocks)) {
				$this->updateImportViewBlocks($parentBlock->blocks, $childBlocks, $childId);
			}
			if($parentBlock->type == WYSIWYGView::BLOCK_TYPES["viewImportInput"] && $parentBlock->content->viewId == $childId) {
				$childBlocks = $parentBlock->blocks;
			}
		}
	}

	/**
	 * Vide le buffer mais laisse intact $this->contentBuffer
	 */
	protected function clearBuffer()
	{
		if (empty($this->contentBuffer)) {
			$this->fetchContentBuffer();
		}
		$contentBufferOrm = ContentBufferOrm::find("num_diffusion_history", $this->id);
		while (count($contentBufferOrm) > 0) {
			$buffer = array_shift($contentBufferOrm);
			$buffer->delete();
		}
	}

	/**
	 * Vide le buffer et vide $this->contentBuffer
	 */
	protected function cleanBuffer()
	{
		$this->clearBuffer();
		$this->contentBuffer = [];
	}
}
