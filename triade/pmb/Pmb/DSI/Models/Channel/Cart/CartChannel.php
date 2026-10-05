<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CartChannel.php,v 1.1.4.1 2025/01/14 14:18:50 jparis Exp $
namespace Pmb\DSI\Models\Channel\Cart;

use Pmb\DSI\Models\Channel\RootChannel;

class CartChannel extends RootChannel
{

	public const CART_TYPES = [
		"MIXED",
		"NOTI"
	];

	public function send($subscriberList, $renderedView, $diffusion = null)
	{
		foreach (self::CART_TYPES as $cartType) {
			if (empty($renderedView['settings']->cart->{$cartType})) {
				continue;
			}
			$caddie = \caddie_root::get_instance_from_object_type($cartType, $renderedView['settings']->cart->$cartType);
			if ($this->settings->emptyCart) {
				$this->emptyCart($caddie);
			}
			foreach ($renderedView['cart'][$cartType] as $id) {
				$caddie->add_item($id);
			}
		}

		return true;
	}

	/**
	 * Vide le panier
	 *
	 * @param mixed $caddie
	 */
	private function emptyCart($caddie)
	{
		$caddie->del_item_flag();
		$caddie->del_item_no_flag();
	}
}