<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EventProduct.php,v 1.1.4.1 2024/10/01 08:24:23 jparis Exp $
namespace Pmb\DSI\Models;

use Pmb\Common\Models\Model;
use Pmb\Common\Helper\Helper;

class EventProduct extends Model implements CRUD
{

	protected $ormName = "Pmb\DSI\Orm\EventProductOrm";

	protected $num_event = 0;
	
	protected $num_product = 0;

	public function __construct(int $num_event = 0, int $num_product = 0)
	{
		$this->num_event = $num_event;
		$this->num_product = $num_product;
		$this->read();
	}

	public function create()
	{
		$orm = new $this->ormName();
		$orm->num_event = $this->num_event;
		$orm->num_product = $this->num_product;

		$orm->save();
	}

	public function check($data)
	{
		return true;
	}

	public function read()
	{
		$this->fetchData();
	}

	public function update()
	{
		$orm = new $this->ormName(["num_event" => $this->num_event, "num_product" => $this->num_product]);
		$orm->num_event = $this->num_event;
		$orm->num_product = $this->num_product;

		$orm->save();
	}

	public function delete()
	{
		try {
			$orm = new $this->ormName(["num_event" => $this->num_event, "num_product" => $this->num_product]);
			$orm->delete();
		} catch(\Exception $e) {
			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}
		
		$this->num_product = 0;
		$this->num_event = 0;
		
		return [
			'error' => false,
			'errorMessage' => ''
		];
	}
}