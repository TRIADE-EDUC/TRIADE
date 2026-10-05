<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EventDiffusion.php,v 1.2 2023/03/28 14:16:49 rtigero Exp $
namespace Pmb\DSI\Models;

use Pmb\Common\Models\Model;
use Pmb\Common\Helper\Helper;

class EventDiffusion extends Model implements CRUD
{

	protected $ormName = "Pmb\DSI\Orm\EventDiffusionOrm";

	protected $num_event = 0;
	
	protected $num_diffusion = 0;

	public function __construct(int $num_event = 0, int $num_diffusion = 0)
	{
		$this->num_event = $num_event;
		$this->num_diffusion = $num_diffusion;
		$this->read();
	}

	public function create()
	{
		$orm = new $this->ormName();
		$orm->num_event = $this->num_event;
		$orm->num_diffusion = $this->num_diffusion;

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
		$orm = new $this->ormName(["num_event" => $this->num_event, "num_diffusion" => $this->num_diffusion]);
		$orm->num_event = $this->num_event;
		$orm->num_diffusion = $this->num_diffusion;

		$orm->save();
	}

	public function delete()
	{
		try {
			$orm = new $this->ormName(["num_event" => $this->num_event, "num_diffusion" => $this->num_diffusion]);
			$orm->delete();
		} catch(\Exception $e) {
			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}
		
		$this->num_diffusion = 0;
		$this->num_event = 0;
		
		return [
			'error' => false,
			'errorMessage' => ''
		];
	}
}