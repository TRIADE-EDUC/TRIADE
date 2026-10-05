<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionsPendingController.php,v 1.17.2.2.2.2 2025/03/25 10:38:53 rtigero Exp $

namespace Pmb\DSI\Controller;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\ContentBuffer;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\DiffusionHistory;
use Pmb\DSI\Models\DSIParserDirectory;
use Pmb\DSI\Helper\Filters;
use Pmb\DSI\Models\SendQueue;
use Pmb\DSI\Orm\DiffusionHistoryOrm;
use Pmb\DSI\Orm\DiffusionOrm;

class DiffusionsPendingController extends CommonController
{
    protected const VUE_NAME = "dsi/diffusionsPending";

    /**
     *
     * {@inheritDoc}
     * @see \Pmb\DSI\Controller\CommonController::getBreadcrumb()
     */
    protected function getBreadcrumb()
    {
        global $msg;
        return "{$msg['dsi_menu']} {$msg['menu_separator']} {$msg['dsi_sending_pending']}";
    }

    protected function defaultAction()
    {
        $entities = HelperEntities::get_entities_labels();
        array_walk($entities, function (&$item, $key) {
            $item = ["value" => $key, "label" => $item];
        });
        $entities = array_values($entities);

        $channels = [];
        $manifests = DSIParserDirectory::getInstance()->getManifests("Pmb/DSI/Models/Channel/");
        foreach ($manifests as $manifest) {
            $manifest->manually = intval($manifest->manually);
            $message = $manifest->namespace::getMessages();
            $channels[] = [
                "value" => RootChannel::IDS_TYPE[$manifest->namespace],
                "label" => $message['name'],
            ];
        }

        $diffusionInstance = new Diffusion();
        foreach ($diffusionInstance->getList() as $diffusion) {
            $diffusion->init();
        }

        $history = new DiffusionHistory();
        $list = $history->getPendingList();

        print $this->render([
            "list" => $list,
            "subscribers" => [],
            "subscriberTypes" => HelperEntities::get_subscriber_entities(),
            "entities" => Filters::getEntityOptions(),
            "filters" => Filters::getFilters(),
        ]);
    }

    public function updateHistoryState($state, $idHistory) {
        if (!DiffusionHistoryOrm::exist($idHistory)) {
            http_response_code(404);
            $this->ajaxError("Upate Diffusion not found");
        }
        try {
            $history = new DiffusionHistory($idHistory);
            $history->state($state);
            $this->ajaxJsonResponse(Helper::toArray($history));
        } catch (\InvalidArgumentException $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    public function saveContentBuffer($idHistory, $contentType) {
        if (!DiffusionHistoryOrm::exist($idHistory)) {
            http_response_code(404);
            $this->ajaxError("Save Diffusion History not found");
        }
        $diffusionHistory = new DiffusionHistory($idHistory);
        foreach(Helper::toArray($this->data->data, "") as $key => $content) {
            $diffusionHistory->contentBuffer[$contentType][$key]->modified = true;
            $diffusionHistory->contentBuffer[$contentType][$key]->content = $content["content"];
        }
        $diffusionHistory->saveContentBuffer();

        $this->ajaxJsonResponse(['success' => true]);
    }

    public function resetContentBuffer($idHistory, $contentType) {
        if (!DiffusionHistoryOrm::exist($idHistory)) {
            http_response_code(404);
            $this->ajaxError("Diffusion History conntent buffer not found");
        }
        $diffusionHistory = new DiffusionHistory($idHistory);
        $diffusionHistory->contentBuffer[$contentType] = [];

        switch($contentType) {
            case ContentBuffer::CONTENT_TYPES_SUBSCRIBER:
                $diffusionHistory->diffusion->fetchChannel();
                $diffusionHistory->diffusion->fetchSubscriberList();
                $diffusionHistory->addContentSubscriberList($diffusionHistory->diffusion->subscriberList);
                break;
            case ContentBuffer::CONTENT_TYPES_ITEM:
                $diffusionHistory->diffusion->fetchItem();
                $diffusionHistory->addContentItem($diffusionHistory->diffusion->item);
                break;
            case ContentBuffer::CONTENT_TYPES_VIEW:
                $diffusionHistory->diffusion->fetchView();
                $diffusionHistory->addContentView($diffusionHistory->diffusion->view);
                break;
        }
        $diffusionHistory->saveContentBuffer();
        $this->ajaxJsonResponse($diffusionHistory->contentBuffer[$contentType]);
    }

    public function getContentBuffer($id)
    {
        if (!DiffusionHistoryOrm::exist($id)) {
            http_response_code(404);
            $this->ajaxError("Diffusion History Buffer not found");
        }
        $diffusionHistory = new DiffusionHistory($id);
        $this->ajaxJsonResponse($diffusionHistory->contentBuffer);
    }

    /**
     * Récupère les données d'une diffusion en attente en cours d'envoi.
     */
    public function getDataInProgressDiffusion($idHistory)
    {
        if (!DiffusionHistoryOrm::exist($idHistory)) {
            http_response_code(404);
            $this->ajaxError("Diffusion History data in progress not found");
        }
        $diffusionHistory = new DiffusionHistory(intval($idHistory));

        $data = [
            "inProgress" => false,
            "remainingElements" => $diffusionHistory->totalRecipients,
            "totalElements" => $diffusionHistory->totalRecipients,
        ];

        $remainingElements = SendQueue::getRemaining($diffusionHistory->idDiffusionHistory);
        if (!empty($remainingElements)) {
            $data['inProgress'] = true;
            $data['remainingElements'] = count($remainingElements);
            $data['totalElements'] = count(SendQueue::getAll($diffusionHistory->idDiffusionHistory));
        }

        $this->ajaxJsonResponse($data);
    }

    /**
     * Récupère les données des diffusions en attente en cours d'envoi.
     */
    public function getDataInProgressAllDiffusions()
    {
        $diffusionHistory = new DiffusionHistory();

        $diffusionHistories = $diffusionHistory->getList([
            "state" => 2
        ]);

        $list = [
            "list" => [],
            "nbPerPass" => SendQueue::NB_PER_PASS,
        ];
        foreach($diffusionHistories as $diffusionHistory) {
            $data = [
                "inProgress" => false,
                "remainingElements" => $diffusionHistory->totalRecipients,
                "totalElements" => $diffusionHistory->totalRecipients,
            ];

            $remainingElements = SendQueue::getRemaining($diffusionHistory->idDiffusionHistory);
            if (!empty($remainingElements)) {
                $data['inProgress'] = true;
                $data['remainingElements'] = count($remainingElements);
                $data['totalElements'] = count(SendQueue::getAll($diffusionHistory->idDiffusionHistory));
            }

            $list["list"][$diffusionHistory->idDiffusionHistory] = $data;
        }

        $this->ajaxJsonResponse($list);
    }
}
