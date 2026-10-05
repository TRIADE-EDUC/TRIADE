<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: HTML5Renderer.php,v 1.17.2.3 2024/11/12 14:15:35 jparis Exp $
namespace Pmb\DSI\Models\View\WYSIWYGView\Render;

use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\Item\RootItem;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Models\View\WYSIWYGView\WYSIWYGView;

class HTML5Renderer
{

	public const CONTAINER_ELEMENT_TEMPLATE = '<div style="!!style!!">!!content!!</div>';

	public const BLOCK_TYPE = 1;

	public const TEXT_TYPE = 2;

	public const IMAGE_TYPE = 3;

	public const VIDEO_TYPE = 4;

	public const LIST_TYPE = 5;

	public const RICH_TEXT_TYPE = 6;

	public const VIEW_TYPE = 7;

	public const IMPORT_VIEW_TYPE = 8;

	protected $view;

	protected $item;

	public function __construct(WYSIWYGView $view, Item $item)
	{
		$this->view = $view;
		$this->item = $item;
	}

	public function render($currentElement): string
	{
		$html = "";

		if (!$this->canDisplay($currentElement)) {
			return $html;
		}

		switch ($currentElement->type) {
			case static::BLOCK_TYPE:
				$html .= $this->renderBlockElement($currentElement);
				break;

			case static::TEXT_TYPE:
				$html .= $this->renderTextElement($currentElement);
				break;

			case static::IMAGE_TYPE:
				$html .= $this->renderImageElement($currentElement);
				break;

			case static::VIDEO_TYPE:
				$html .= $this->renderVideoElement($currentElement);
				break;

			case static::LIST_TYPE:
				$html .= $this->renderListElement($currentElement);
				break;

			case static::RICH_TEXT_TYPE:
				$html .= $this->renderRichTextElement($currentElement);
				break;

			case static::VIEW_TYPE:
				$html .= $this->renderViewElement($currentElement);
				break;

			case static::IMPORT_VIEW_TYPE:
				$html .= $this->renderImportViewElement($currentElement);
				break;

			default:
				$html .= "<!-- unknown block type -->";
				break;
		}

		if(empty(static::CONTAINER_ELEMENT_TEMPLATE)) {
			return $html;
		}

		return str_replace([
			'!!style!!',
			'!!content!!'
		], [
			$this->getStyleString($currentElement->style),
			$html
		], static::CONTAINER_ELEMENT_TEMPLATE);
	}

	protected function renderBlockElement($currentElement)
	{
		$html = "";

		foreach ($currentElement->blocks as $block) {
			$html .= $this->render($block);
		}

		return $html;
	}

	protected function renderTextElement($currentElement)
	{
		global $charset;

		return sprintf('<p style="%s">%s</p>', $this->getStyleString($currentElement->text->style), htmlentities($currentElement->content, ENT_QUOTES, $charset));
	}

	protected function renderImageElement($currentElement)
	{
		if (!empty($currentElement->redirect)) {
			return sprintf('<a href="%s"><img alt="%s" style="%s" src="%s"/></a>', $currentElement->redirect, $currentElement->alt, $this->getMultimediaStyleString($currentElement->style->image, $currentElement->keepRatio), $currentElement->content);
		}
		return sprintf('<img alt="%s" style="%s" src="%s"/>', $currentElement->alt, $this->getMultimediaStyleString($currentElement->style->image, $currentElement->keepRatio), $currentElement->content);
	}

	protected function renderVideoElement($currentElement)
	{
		$controls = $currentElement->video->controls ? "controls " : "";
		$controls .= $currentElement->video->autoplay ? "autoplay " : "";
		$controls .= $currentElement->video->muted ? "muted " : "";
		$controls .= $currentElement->video->loop ? "loop " : "";

		return sprintf('<video style="%s" %s>
                <source src="%s" type="%s"/>
            </video>', $this->getMultimediaStyleString($currentElement->style->video, $currentElement->keepRatio), $controls, $currentElement->content->value, $currentElement->content->mimetype);
	}

	protected function renderListElement($currentElement)
	{
		$elements = "";
		foreach ($currentElement->list->elements as $element) {
			$elements .= sprintf('
                <li style="%s">%s</li>', $this->getStyleString($currentElement->list->style), $element);
		}

		return sprintf('<ul style="%s">%s</ul>', $this->getStyleString($currentElement->style), $elements);
	}

	protected function renderRichTextElement($currentElement)
	{
		return $currentElement->content;
	}

	protected function renderViewElement($currentElement)
	{
		if (!isset($currentElement->viewSelected) || $currentElement->viewSelected == "") {
			return "";
		}
		$viewSelected = RootView::getInstance($currentElement->viewSelected);
		if (!in_array($viewSelected->type, RootView::IDS_TYPE_AGNOSTIC) && $currentElement->itemSelected == 0) {
			return "";
		}
		$itemSelected = $this->item->fetchChildById($currentElement->itemSelected);
		if (!isset($itemSelected)) {
			//On essaie avec le oldid dans le cas des vues wysiwyg importées contenant la vue à remplacer pour les DSI privées
			$itemSelected = $this->item->getItemFromOldId($currentElement->itemSelected);
		}
		if (!isset($itemSelected)) {
			$itemSelected = $this->item;
		}
		//On compare la limite entre la vue wysiwyg et la sous vue
		//On a une limite dans la wysiwyg si elle vient d'une alerte privée
		$limit = $subView->settings->limit ?? 0;
		if (isset($this->view->settings->limit)) {
			if ((intval($this->view->settings->limit) < $limit) || ($limit == 0)) {
				$limit = $this->view->settings->limit;
			}
		}
		return $viewSelected->render($itemSelected, $this->view->entityId, $limit, $this->view->context);
	}

	protected function renderImportViewElement($currentElement)
	{
		if ($currentElement->content && $currentElement->content->viewId) {
			$subView = RootView::getInstance($currentElement->content->viewId);
			$limit = $subView->settings->limit ?? 0;
			if (isset($this->view->settings->limit)) {
				if ((intval($this->view->settings->limit) < $limit) || ($limit == 0)) {
					$limit = $this->view->settings->limit;
				}
			}
			return $subView->render($this->item, $this->view->entityId, $limit, $subView->context);
		}
		if (is_string($currentElement->content)) {
			return $currentElement->content;
		}
		return "";
	}

	protected function getStyleString($style): string
	{
		if (!is_object($style)) {
			return "";
		}

		if (isset($style->block)) {
			$style = $style->block;
		}

		$style = get_object_vars($style);
		array_walk($style, function (&$item, $key) {
			$key = Helper::camelize_to_kebab($key);
			$item = "{$key}:{$item}";
		});

		return implode(';', $style);
	}

	protected function getMultimediaStyleString($style, $keepRatio): string
	{
		if ($keepRatio && isset($style->height)) {
			$style->height = null;
		}
		return $this->getStyleString($style);
	}

	/**
	 * Permet de savoir si on peut afficher un bloc
	 *
	 * @param \stdClass $currentElement
	 * @return boolean
	 */
	public function canDisplay($currentElement)
	{
		if (!empty($this->view) && !empty($this->item)) {
			if (isset($currentElement->conditions) && !empty($currentElement->conditions->emptyAssociatedItem)) {
				foreach ($currentElement->conditions->emptyAssociatedItem->views as $idView) {
					$accociatedView = RootView::getInstance($idView);
					$accociatedItem = $this->view->getAssociatedItemOfView($accociatedView, $this->item);

					if (!empty($accociatedItem) && !empty($accociatedView)) {
						if (empty($accociatedView->getFilteredData($accociatedItem, $this->view->entityId, $this->view->context))) {
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Permet de récupérer un bloc en fonction de son identifiant
	 *
	 * @param string $id
	 * @return \stdClass|boolean
	 */
	protected function getBlockById(string $id, $blocks = null)
	{
		if (null === $blocks) {
			$layer = $this->view->getSetting('layer', new \stdClass());
			$blocks = $layer->blocks ?? [];
		}

		foreach ($blocks as $block) {
			if ($block->id == $id) {
				return $block;
			}

			if (!empty($block->blocks)) {
				$result = $this->getBlockById($id, $block->blocks);
				if ($result !== false) {
					return $result;
				}
			}
		}

		return false;
	}

	/**
	 * Permet de savoir si des items seront vide ou non parmi les sous blocks
	 *
	 * @param \stdClass $currentElement
	 * Bloc qui a les conditions d'affichage
	 * @param array $blocks
	 * Blocs a tester
	 * @return boolean
	 */
	protected function emptyItems($currentElement, $blocks = [])
	{
		if ($currentElement->type == static::BLOCK_TYPE) {
			if (empty($currentElement->condition->items)) {
				return false;
			}
			if (empty($blocks)) {
				$blocks = $currentElement->blocks;
			}
			foreach ($currentElement->condition->items as $item) {
				foreach ($blocks as $block) {
					if (!empty($block->itemSelected) && $item == $block->itemSelected) {
						$view = RootView::getInstance($block->viewSelected);
						$itemSelected = $this->item->fetchChildById($item);
						if ($itemSelected && empty($view->getFilteredData($itemSelected, $this->view->entityId, $this->view->context))) {
							return true;
						}
					}
					if (!empty($block->blocks)) {
						if ($this->emptyItems($currentElement, $block->blocks)) {
							return true;
						}
					}
				}
			}
		}
		return false;
	}
}
