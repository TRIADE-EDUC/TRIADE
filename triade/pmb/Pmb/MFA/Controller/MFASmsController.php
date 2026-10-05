<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFASmsController.php,v 1.4 2022/10/18 13:45:59 jparis

namespace Pmb\MFA\Controller;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Views\VueJsView;

class MFASmsController extends MFAController
{
    protected static $modelNamespace = "Pmb\\MFA\\Models\\MFASms";
    protected function defaultAction()
    {
        $data = $this->getDataList();
        $data["selvars"] = $this->getSelvars();
        
        $view = new VueJsView("mfa/sms", $data);
        print $view->render();

        foreach($data["data"] as $object) {
            $translation = new \translation_ajax($object->id, "mfa_sms");
            print $translation->connect("mfa-form-sms-" . strtolower($object->context));
        }
    }

    public function saveTranslations($id, $context)
    {
        $translation = new \translation_ajax($id, "mfa_sms");
        $translation->set_form_data(Helper::toArray($this->data->formData));
        $translation->update("content", "content_" . strtolower($context), "text");
    }
    
    protected function getSelvars() 
    {
    	return [
    			"gestion" => \mailtpl::get_formatted_options_selvars('users'),
    			"opac" => \mailtpl::get_formatted_options_selvars('readers', ['empr_group_empr', 'empr_group_loc', 'empr_group_misc', 'empr_group_mfa'])
    	];
    }
}