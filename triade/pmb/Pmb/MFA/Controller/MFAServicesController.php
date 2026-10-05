<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFAServicesController.php,v 1.4 2022/10/18 13:45:59 jparis

namespace Pmb\MFA\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Common\Helper\Helper;
use Pmb\Common\Views\VueJsView;

class MFAServicesController extends MFAController
{
    protected static $modelNamespace = "Pmb\\MFA\\Models\\MFAServices";
    protected function defaultAction()
    {
        $data = $this->getDataList();
        
        $view = new VueJsView("mfa/services", $data);
        print $view->render();

        foreach($data["data"] as $object) {
            $translation = new \translation_ajax($object->id, "mfa_services");
            print $translation->connect("mfa-form-services-" . strtolower($object->context));
        }
    }

    public function saveTranslations($id, $context)
    {
        $translation = new \translation_ajax($id, "mfa_services");
        $translation->set_form_data(Helper::toArray($this->data->formData));
        $translation->update("suggest_message", "suggest_message_" . strtolower($context), "text");
    }
}