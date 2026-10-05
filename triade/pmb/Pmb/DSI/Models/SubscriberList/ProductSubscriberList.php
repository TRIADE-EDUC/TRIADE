<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ProductSubscriberList.php,v 1.9 2023/04/05 13:43:30 rtigero Exp $
namespace Pmb\DSI\Models\SubscriberList;

use Pmb\DSI\Models\SubscriberList\Subscribers\Subscriber;

class ProductSubscriberList extends LocalSubscriberList
{

	protected const SUBSCRIBER_TYPE = Subscriber::FROM_PRODUCT;
	
	protected $ormName = "Pmb\DSI\Orm\SubscribersProductOrm";

	protected $entityOrmName = "Pmb\DSI\Orm\ProductOrm";

	public $numProduct = 0;

	public function __construct($idProduct = 0)
	{
		$this->numProduct = $idProduct;
		parent::__construct($idProduct);
	}

	protected function getSubscribers()
	{
		$this->subscribers = array();
		$lists = $this->ormName::finds([
			'num_product' => $this->numEntity
		]);
		foreach ($lists as $list) {
			$this->subscribers[] = Subscriber::getInstance("products", $list->{$this->ormName::$idTableName});
		}
		$this->filterSubscribers();

		return $this->subscribers;
	}

	public function getFormatedSubscribers($list = array())
	{
		if (! empty($this->subscribers)) {
			return $this->subscribers;
		}
		return $this->getSubscribers();
	}
}