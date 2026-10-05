<?php

// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PriceController.php,v 1.15.8.1 2024/09/05 08:44:06 gneveu Exp $

namespace Pmb\Animations\Controller;

use Pmb\Common\Views\VueJsView;
use Pmb\Animations\Models\PriceTypeModel;
use Pmb\Animations\Models\PriceModel;
use Pmb\Animations\Orm\PriceOrm;
use Pmb\Animations\Orm\PriceTypeOrm;

class PriceController
{
    /**
     *
     * @var string
     */
    public $action = "list";

    public function proceed(string $action = "", $data = null)
    {
        global $id;

        $this->action = $action;
        switch ($action) {
            default:
            case "list":
                return $this->listTypesAction();
                break;
            case "delete":
                return $this->deleteTypeAction(intval($data->id));
                break;
            case "save":
                return $this->saveTypeAction($data);
                break;
            case "add":
                return $this->addTypeAction();
                break;
            case "edit":
                return $this->updateTypeAction(intval($id));
                break;
            case "check":
                return $this->checkTypeAction(intval($data->id));
                break;
        }
    }

    public function listTypesAction()
    {
        $pricesType = PriceTypeModel::getPricesTypeList();
        $newVue = new VueJsView("animations/priceTypes", [
            "priceTypes" => $pricesType,
            "action" => $this->action
        ]);
        print $newVue->render();
    }

    public function saveTypeAction(object $data)
    {
        if (!empty($data->id)) {
            PriceTypeModel::updatePriceType($data->id, $data);
        } else {
            PriceTypeModel::addPriceType($data);
        }
    }

    public function deleteTypeAction(int $id)
    {
        if($id && (1 !== $id)) {
            $newListPricesType = PriceTypeModel::deletePriceType($id);
        }
        return $newListPricesType;
    }

    public function addTypeAction()
    {
        $priceType = new PriceTypeModel();
        $newVue = new VueJsView("animations/priceTypes", [
            "priceTypes" =>  $priceType->getEditAddData(),
            "action" => $this->action,
            'img' => [
                'plus' => get_url_icon('plus.gif'),
                'minus' => get_url_icon('minus.gif'),
                'expandAll' => get_url_icon('expand_all'),
                'collapseAll' => get_url_icon('collapse_all'),
                'tick' => get_url_icon('tick.gif'),
                'error' => get_url_icon('error.png'),
                'patience' => get_url_icon('patience.gif'),
                'sort' => get_url_icon('sort.png'),
                'iconeDragNotice' => get_url_icon('icone_drag_notice.png')
            ]
        ]);
        print $newVue->render();
    }

    public function updateTypeAction(int $id)
    {
        if ($id == 0 || !PriceTypeOrm::exist($id)) {
            http_response_code(404);
            $this->action = "list";
            return $this->listTypesAction();
        }

        try {
            $priceType = new PriceTypeModel($id);
        } catch (\Exception $e) {
            $this->action = "list";
            return $this->listTypesAction();
        }

        $newVue = new VueJsView("animations/priceTypes", [
            "priceTypes" => $priceType->getEditAddData(),
            "action" => $this->action,
            'img' => [
                'plus' => get_url_icon('plus.gif'),
                'minus' => get_url_icon('minus.gif'),
                'expandAll' => get_url_icon('expand_all'),
                'collapseAll' => get_url_icon('collapse_all'),
                'tick' => get_url_icon('tick.gif'),
                'error' => get_url_icon('error.png'),
                'patience' => get_url_icon('patience.gif'),
                'sort' => get_url_icon('sort.png'),
                'iconeDragNotice' => get_url_icon('icone_drag_notice.png')
            ]
        ]);
        print $newVue->render();
    }

    public function checkTypeAction(int $id)
    {
        return PriceTypeModel::checkPriceTypeUse($id);
    }
}
