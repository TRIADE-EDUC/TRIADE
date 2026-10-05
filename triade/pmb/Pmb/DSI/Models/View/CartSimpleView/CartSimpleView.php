<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CartSimpleView.php,v 1.1.4.2 2024/10/01 08:24:23 jparis Exp $
namespace Pmb\DSI\Models\View\CartSimpleView;

use Pmb\DSI\Models\Channel\Cart\CartChannel;
use Pmb\DSI\Models\Item\Entities\Record\RecordListItem\RecordListItem;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\RootView;

class CartSimpleView extends RootView
{
	public const CART_TYPES_LIST = [
		"NOTI"
	];

	public function getFormData()
	{
		$data = parent::getFormData();
		foreach (CartChannel::CART_TYPES as $cartType) {
			if(!in_array($cartType, self::CART_TYPES_LIST)) {
				continue;
			}

			$caddie = \caddie_root::get_instance_from_object_type($cartType);
			$data['carts'][$cartType] = $caddie::get_cart_list();
		}

		return $data;
	}

	public function render(Item $item, int $entityId, int $limit, string $context)
	{
		$data = array();
		$data['settings'] = $this->settings;

		$itemData = $this->getDataFromContext($item, $context);
        if (empty($itemData)) {
            return "";
        }

		$this->filterData($itemData, $entityId);
		$this->limitData($itemData, $limit);

		switch (true) {
			case $item instanceof RecordListItem:
				$data['cart']['NOTI'] = array_keys($itemData);
				break;
			//TODO authorities
			default:
				break;
		}

		return $data;
	}
}