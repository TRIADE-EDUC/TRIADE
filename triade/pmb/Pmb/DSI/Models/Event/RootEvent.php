<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootEvent.php,v 1.20.2.1.2.1 2025/05/26 12:13:32 rtigero Exp $

namespace Pmb\DSI\Models\Event;

use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\EventDiffusion;
use Pmb\DSI\Models\Root;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Orm\EventOrm as OrmEventOrm;
use Pmb\DSI\Orm\EventProductOrm;
use Pmb\DSI\Orm\EventDiffusionOrm;

class RootEvent extends Root implements Event
{
	protected const EXCLUDED_PROPERTIES = array(
		"idEvent",
		'numModel',
		'model',
		'eventModel',
	);

	public const TAG_TYPE = 5;

	public const IDS_TYPE = [
        "Pmb\DSI\Models\Event\Periodical\PeriodicalEvent" => 1,
    ];

    protected $ormName = "Pmb\DSI\Orm\EventOrm";

    public $id = 0;
    public $name = "";
    public $type = 0;
    public $model = false;
    public $settings = "";
    public $tags = null;
	//Type d'entité qui utilise ce déclencheur (diffusion ou produit)
	public $callerType = "";

    // ORM props
    protected $idEvent = 0;

    public $numModel = 0;

    protected $eventModel = null;

    public function __construct(int $id = 0) {
        $this->id = intval($id);
		if($this->id) {
			$this->read();
		} else {
			$this->settings = new \stdClass();
		}
    }

    public static function getInstance(int $id = 0) {
        if(!empty($id)) {
            $event = OrmEventOrm::findById($id);
            if(!empty($event)) {
                foreach(self::IDS_TYPE as $key => $type) {
                    if(self::IDS_TYPE[$key] == $event->type) {
                        return new $key($id);
                    }
                }
            }
        }
        return new RootEvent($id);
    }

    public function read()
	{
		$this->fetchData();
	}

    public function check(object $data) {
		if (!is_string($data->name)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:data_errors'
			];
		}

// 		if(!empty($data->name)) {
// 			$fields = ['name' => $data->name, 'model' => $data->model];
// 			if (!empty($data->id)) {
// 				$fields[$this->ormName::$idTableName] = [
// 					'value' =>  $data->id,
// 					'operator' => '!='
// 				];
// 			}
// 			$result = $this->ormName::finds($fields);
// 			if (!empty($result)) {
// 				return [
// 					'error' => true,
// 					'errorMessage' => 'msg:item_duplicated'
// 				];
// 			}
// 		}

		return [
			'error' => false,
			'errorMessage' => ''
		];
	}

    public function create()
	{

        $orm = new $this->ormName();
		$orm->name = $this->name;
		$orm->type = $this->type;
		$orm->model = $this->model;
		$orm->settings = json_encode($this->settings);
		$orm->num_model = $this->numModel;
		$orm->save();

		$this->id = $orm->{$this->ormName::$idTableName};
		$this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
    }

	public function update()
	{
		$orm = new $this->ormName($this->id);
		$orm->name = $this->name;
		$orm->type = $this->type;
		$orm->model = $this->model;
		$orm->settings = json_encode($this->settings);
		$orm->num_model = $this->numModel;
		$orm->save();
	}

	public function delete()
	{
        try {
            if(!$this->checkBeforeDelete()) {
                return [
                    'error' => true,
                    'errorMessage' => "msg:model_check_use"
                ];
            }
            $orm = new $this->ormName($this->id);
            $productEvents = EventProductOrm::finds(["num_event" => $orm->id_event]);

    		foreach ($productEvents as $productEvent) {
    	      $productEvent->delete();
    		}

    		$diffusionEvents = EventDiffusionOrm::finds(["num_event" => $orm->id_event]);

    		foreach ($diffusionEvents as $diffusionEvent) {
    		    $diffusionEvent->delete();
    		}
		} catch (\Exception $e) {
		    return [
		        'error' => true,
		        'errorMessage' => $e->getMessage()
		    ];
		}

		$this->removeEntityTags();

		$orm->delete();

		$this->id = 0;
		$this->{Helper::camelize($orm::$idTableName)} = 0;
		$this->name = '';
		$this->type = '';
		$this->model = false;
		$this->settings = "";
		$this->numModel = null;

		return [
			'error' => false,
			'errorMessage' => ''
		];
	}

    public function setFromForm(object $data)
	{
		$this->name = $data->name;
		$this->type = intval($data->type);
		$this->model = $data->model;
		$this->settings = $data->settings;
		$this->numModel = $data->numModel;
	}

	public function trigger()
	{
		return false;
	}

	/**
	 * @param mixed $param Id de la diffusion
	 */
	public function duplicate($param = null)
	{
		$newEntity = self::getInstance($this->id);
		$newEntity->name = $this->getDuplicateName($this->name);
		$newEntity->id = 0;
		$newEntity->create();

		if($newEntity->id != 0) {
			if(isset($param)) {
				$eventDiffusionModel = new EventDiffusion($newEntity->id, $param);
				$eventDiffusionModel->create();
			}
			return $newEntity;
		}
		return false;
	}

	/**
     * Retourne un parametre en fonction de son nom
     *
     * @param string $settingName
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $settingName, $default = null)
    {
        if (empty($this->settings)) {
            return $default;
        }
        return $this->settings->{$settingName} ?? $default;
    }

	/**
	 * Vérifie si la diffusion peut être déclenchée.
	 *
	 * @param mixed $view
	 * @param mixed $item
	 * @return bool Renvoie true si la fonction peut être déclenchée, false sinon
	 */
	public function canTrigger($view = null, $item = null)
	{
		if(!empty($view) && !empty($item)) {
			if (isset($this->settings->conditions) && !empty($this->settings->conditions->emptyAssociatedItem)) {
				foreach ($this->settings->conditions->emptyAssociatedItem->views as $idView) {
					$accociatedView = RootView::getInstance($idView);
					$accociatedItem = $view->getAssociatedItemOfView($accociatedView, $item);

					if (!empty($accociatedItem) && !empty($accociatedView)) {
						if (empty($accociatedView->getFilteredData($accociatedItem, $view->entityId, $view->context))) {
							return false;
						}
					}
				}
			}
		}

		return true;
	}
}

