<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionProduct.php,v 1.4 2023/04/03 14:56:06 rtigero Exp $
namespace Pmb\DSI\Models;

use Pmb\Common\Models\Model;
use Pmb\Common\Helper\Helper;

class DiffusionProduct extends Model implements CRUD
{

	protected $ormName = "Pmb\DSI\Orm\DiffusionProductOrm";

	protected $num_diffusion = 0;

	protected $num_product = 0;

	public $active = false;

	public $last_diffusion = null;

	public function __construct(int $num_diffusion = 0, int $num_product = 0)
	{
		$this->num_diffusion = $num_diffusion;
		$this->num_product = $num_product;
		$this->read();
	}

	public function create()
	{
		$orm = new $this->ormName();
		$orm->num_diffusion = $this->num_diffusion;
		$orm->num_product = $this->num_product;
		$orm->active = $this->active;
		$orm->last_diffusion = $this->last_diffusion;
		$orm->save();
	}
	/**
	 * @return mixed
	 */
	public function check($data)
	{
		return true;
	}

	public function setFromForm(object $data)
	{
		$this->active = $data->active;
		$this->last_diffusion = $data->last_diffusion;
	}

	public function read()
	{
		$this->fetchData();
	}

	public function update()
	{
		$orm = new $this->ormName(["num_diffusion" => $this->num_diffusion, "num_product" => $this->num_product]);
		$orm->num_diffusion = $this->num_diffusion;
		$orm->num_product = $this->num_product;
		$orm->active = $this->active;
		$orm->last_diffusion = $this->last_diffusion;
		$orm->save();
	}

	public function delete()
	{
		try {
			$orm = new $this->ormName(["num_diffusion" => $this->num_diffusion, "num_product" => $this->num_product]);
			$orm->delete();
		} catch(\Exception $e) {
			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}
		
		$this->num_diffusion = 0;
		$this->num_product = 0;
		$this->active = false;
		$this->last_diffusion = '';
		
		return [
			'error' => false,
			'errorMessage' => ''
		];
	}
}