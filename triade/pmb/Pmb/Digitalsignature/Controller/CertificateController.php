<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CertificateController.php,v 1.1.8.1 2024/09/05 08:44:06 gneveu Exp $

namespace Pmb\Digitalsignature\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Digitalsignature\Models\CertificateModel;
use Pmb\Common\Views\VueJsView;
use Pmb\Digitalsignature\Orm\CertificateOrm;

class CertificateController extends Controller
{
    private $action;

    /**
     *
     * @param string $action
     * @return
     */
    public function proceed($action = "", $data = null)
    {
        $this->action = $action;
        switch ($action) {
            case "edit":
                return $this->editAction(intval($data->id));
            case "delete":
                return $this->deleteAction(intval($data->id));
            case "save":
                return $this->saveAction($data);
            case "list":
            default:
                return $this->listAction();
        }
    }

    public function listAction()
    {
        $newVue = new VueJsView("digitalsignature/certificate", [
            "action" => $this->action,
            "list" => CertificateModel::getCertificateList(),
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

    public function editAction($id)
    {
        if (!CertificateOrm::exist($id)) {
            http_response_code(404);
            return $this->listAction();
        }

        $certificate = new CertificateModel($id);

        $newVue = new VueJsView("digitalsignature/certificate", [
            "action" => $this->action,
            "certificate" => $certificate,
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

    public function saveAction($data)
    {
        CertificateModel::updateCertificate($data);
    }

    public function deleteAction($id)
    {
        CertificateModel::deleteCertificate($id);
        $this->listAction();
    }
}
