<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CartView.php,v 1.1 2023/07/18 09:18:53 rtigero Exp $
namespace Pmb\DSI\Models\View\CartView;

use Pmb\DSI\Models\Channel\Cart\CartChannel;
use Pmb\DSI\Models\Item\Entities\Record\RecordListItem\RecordListItem;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\RootView;

class CartView extends RootView
{

	public function getFormData()
	{
		$data = parent::getFormData();
		foreach (CartChannel::CART_TYPES as $cartType) {
			$caddie = \caddie_root::get_instance_from_object_type($cartType);
			$data['carts'][$cartType] = $caddie::get_cart_list();
		}

		return $data;
	}

	public function render(Item $item, int $entityId, int $limit, string $context)
	{
		$data = array();
		$data['settings'] = $this->settings;

		foreach ($item->childs as $child) {
			switch (true) {
				case $child instanceof RecordListItem:
					$data['cart']['NOTI'] = $child->getResults();
					break;
				//TODO authorities
				default:
					continue 2;
			}
			if (count($child->childs)) {
				$childData = $this->render($child, $entityId, $limit, $context);
				$data['cart']["NOTI"] = array_merge($data['cart']['NOTI'], $childData['cart']['NOTI']);
				$data['cart']["MIXED"] = array_merge($data['cart']['MIXED'], $childData['cart']['MIXED']);
			}
		}
		return $data;
	}
}