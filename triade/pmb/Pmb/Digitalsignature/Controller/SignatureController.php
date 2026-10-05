<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SignatureController.php,v 1.5.8.1 2024/09/05 08:44:06 gneveu Exp $

namespace Pmb\Digitalsignature\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Digitalsignature\Models\SignatureModel;
use Pmb\Common\Views\VueJsView;
use Pmb\Digitalsignature\Models\CertificateModel;
use Pmb\Digitalsignature\Models\DocnumCertifiedFields;
use Pmb\Digitalsignature\Models\DocnumCertifier;
use Pmb\Digitalsignature\Orm\SignatureOrm;

class SignatureController extends Controller
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
            case "getdata":
                return $this->proceedDataAction($data);
            case "save":
                return $this->saveAction($data);
            case "delete":
                return $this->deleteAction(intval($data->id));
            case "deleteDocSign":
                return $this->deleteDocSign($data);
            case "check":
                return $this->checkAction($data);
            case "list":
            default:
                return $this->listAction();
        }
    }

    public function listAction()
    {
        $newVue = new VueJsView("digitalsignature/signature", [
            "action" => $this->action,
            "list" => SignatureModel::getSignatureList(),
            "certificates" => CertificateModel::getCertificateList(),
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
        if (!SignatureOrm::exist($id)) {
            http_response_code(404);
            return $this->listAction();
        }

        $newVue = new VueJsView("digitalsignature/signature", [
            "action" => $this->action,
            "signdata" => SignatureModel::getFormData($id),
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
        SignatureModel::updateSignature($data);
    }

    public function deleteAction($id)
    {
        SignatureModel::deleteSignature($id);
        return $this->listAction();
    }

    public function proceedDataAction($data)
    {
        switch ($data->type) {
            case "docnum":
                return DocnumCertifiedFields::getData();
        }
        return [];
    }

    public function checkAction($data)
    {
        switch ($data->type) {
            case "docnum":
                $explnum = new \explnum($data->id);
                $certifier = new DocnumCertifier($explnum);
                return ["check" => $certifier->check()];
        }
        return [];
    }

    public function deleteDocSign($data)
    {
        switch ($data->type) {
            case "docnum":
                $explnum = new \explnum($data->id);
                $docnumCertifier = New DocnumCertifier($explnum);
                $docnumCertifier->removeFiles();
        }
        return [];
    }
}
