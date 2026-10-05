<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TriggersController.php,v 1.16.4.2 2025/05/21 12:31:40 rtigero Exp $

namespace Pmb\DSI\Controller;

use Pmb\DSI\Models\DSIParserDirectory;
use Pmb\DSI\Models\Event\RootEvent;
use Pmb\DSI\Models\EventDiffusion;
use Pmb\DSI\Models\EventProduct;
use Pmb\DSI\Orm\EventOrm;

class TriggersController extends CommonController
{

	protected const VUE_NAME = "dsi/triggers";

	/**
	 *
	 * {@inheritDoc}
	 * @see \Pmb\DSI\Controller\CommonController::getBreadcrumb()
	 */
	protected function getBreadcrumb()
	{
	    global $msg, $action, $id;
        $breadcrum = "{$msg['dsi_menu']} {$msg['menu_separator']} {$msg['dsi_triggers']}";

        if (isset($action) && $action == "edit" && isset($id)) {
            if (EventOrm::exist($id)) {
                $event = EventOrm::findById($id);
                $breadcrum .= " {$msg['menu_separator']} {$event->name}";
            }
        }

        return $breadcrum;
	}

	protected function defaultAction()
	{
		$event = RootEvent::getInstance();
	    $this->render([
	        "list" => $event->getList(["model" => "1"]),
            "types" => $this->getTypeList(),
	    ]);
	}


	protected function addAction()
	{
		$this->render($this->getFormData());
	}

	protected function editAction()
	{
		global $id;

		$id = intval($id);
        
			$this->render($this->getFormData($id));
        }

	public function save()
	{
		$this->data->id = intval($this->data->id);

		$event = RootEvent::getInstance($this->data->id);
		$result = $event->check($this->data);
		if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}
		$event->setFromForm($this->data);

		if (0 == $this->data->id) {
			$event->create();
		} else {
			$event->update();
		}

		$this->ajaxJsonResponse($event);
		exit();
	}

	public function delete()
	{
		$event = RootEvent::getInstance($this->data->id);
		$result = $event->delete();

		if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}
		$this->ajaxJsonResponse([
			'success' => true
		]);
		exit();
	}

	public function deleteEventProduct()
	{
        $eventProduct = new EventProduct($this->data->num_event, $this->data->num_product);
        $eventProduct->delete();

        $event = new RootEvent($this->data->num_event);
        $result = $event->delete();


        if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}
		$this->ajaxJsonResponse([
			'success' => true
		]);
		exit();
	}

    public function deleteEventDiffusion()
    {
        $eventDiffusion = new EventDiffusion($this->data->num_event, $this->data->num_diffusion);
        $eventDiffusion->delete();

        $event = new RootEvent($this->data->num_event);
        $result = $event->delete();

        if ($result['error']) {
            $this->ajaxError($result['errorMessage']);
            exit();
        }
        $this->ajaxJsonResponse([
            'success' => true
        ]);
        exit();
    }

	/**
	 * Recuperation donnees formulaire ajout/edition
	 *
	 * @param int $id
	 * @return array[]
	 */
	protected function getFormData($id = 0)
	{
		$data = array();
		$data["event"] = RootEvent::getInstance($id);
		$data["types"] = $this->getTypeList();
		return $data;
	}

	public function getTypeList() {
        $eventTypeList = [];
        $manifests = DSIParserDirectory::getInstance()->getManifests("Pmb/DSI/Models/Event/");
		foreach ($manifests as $manifest) {
			$message = $manifest->namespace::getMessages();
			$eventTypeList[] = [
				"id" => RootEvent::IDS_TYPE[$manifest->namespace],
				"namespace" => $manifest->namespace,
				"name" => $message['name']
			];
		}

        return $eventTypeList;
    }

	public function getTypeListAjax() {
		$this->ajaxJsonResponse($this->getTypeList());
	}

	public function getEmptyInstance() {
		$this->ajaxJsonResponse(new RootEvent());
	}

	public function getModels()
	{
		$this->ajaxJsonResponse($this->fetchModels());
	}

	protected function fetchModels()
	{
		return (new RootEvent())->getList(["model" => "1"]);
	}

	public function getModel($idModel)
	{
		$this->ajaxJsonResponse(RootEvent::getInstance($idModel));
	}

	/**
	 * relie un tag a l'entite
	 */
	public function unlinkTag()
	{
		$event = RootEvent::getInstance();
		$delete = $event->unlinkTag($this->data->numTag, $this->data->numEntity);
		$this->ajaxJsonResponse($delete);
	}

	/**
	 * Supprime le lien entre un tag et une entite
	 */
	public function linkTag()
	{
		$event = RootEvent::getInstance();
		$link = $event->linkTag($this->data->numTag, $this->data->numEntity);
		$this->ajaxJsonResponse($link);
	}

	public function duplicate()
    {
        if($this->data->id != 0) {
            $eventToDuplicate = RootEvent::getInstance($this->data->id);
            $newEvent = $eventToDuplicate->duplicate();
            $this->ajaxJsonResponse($newEvent);
        }
    }

	public function deleteAll()
    {
        foreach($this->data->ids as $id) {
            $event = RootEvent::getInstance($id);
            $result = $event->delete();
            if($result["error"]) {
                $this->ajaxError($result['errorMessage']);
            }
        }
        $this->ajaxJsonResponse([ 'success' => true ]);
    }

	public function exportModel($idModel = 0)
    {
		$idModel = intval($idModel);

        if($idModel != 0) {
            $modelToExport = RootEvent::getInstance($idModel);
			$modelToExport->export();
        }
    }

	public function importModel()
    {
		global $msg;

		if(isset($this->data->file) && !empty($this->data->file)) {
			$model = @unserialize($this->data->file);

			if($model && $model instanceof RootEvent) {
				$model->create();
				$this->ajaxJsonResponse($model);
			}
		}

		$this->ajaxError($msg["dsi_model_import_error"]);
    }

	public function importModelTags()
	{
		$event = RootEvent::getInstance($this->data->numEntity);
		$event->importModelTags();
		$this->ajaxJsonResponse($event->tags);
	}
}

