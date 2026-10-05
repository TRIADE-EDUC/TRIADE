<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFAMailController.php,v 1.4 2022/10/18 13:45:59 jparis

namespace Pmb\MFA\Controller;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Views\VueJsView;

class MFAMailController extends MFAController
{
    protected static $modelNamespace = "Pmb\\MFA\\Models\\MFAMail";
    protected function defaultAction()
    {
        $data = $this->getDataList();

        $data["mailtpls"] = $this->formatMailTpls();
        $data["selvars"] = $this->getSelvars();
        $data["senders"] = $this->getSenders();

        $view = new VueJsView("mfa/mail", $data);
        print $view->render();

        foreach($data["data"] as $object) {
            $translation = new \translation_ajax($object->id, "mfa_mail");
            print $translation->connect("mfa-form-mail-" . strtolower($object->context));
        }
    }

    protected function formatMailTpls()
    {
        $data = [];
        $mailtpls = new \mailtpls();
        foreach($mailtpls->info as $mailtpl) {
            $data[] = [
                "id" => $mailtpl->info["id"],
                "name" => $mailtpl->info["name"],
                "object" => $mailtpl->info["objet"],
                "tpl" => $mailtpl->info["tpl"]
            ];
        }
        return $data;
    }

    public function saveTranslations($id, $context)
    {
        $translation = new \translation_ajax($id, "mfa_mail");
        $translation->set_form_data(Helper::toArray($this->data->formData));
        $translation->update("content", "content_" . strtolower($context), "text");
        $translation->update("object", "object_" . strtolower($context));
    }

    protected function getSelvars() 
    {
    	return [
    			"gestion" => \mailtpl::get_formatted_options_selvars('users'),
    			"opac" => \mailtpl::get_formatted_options_selvars('readers',
                    ['empr_group_empr', 'empr_group_loc', 'empr_group_misc', 'empr_group_mfa'])
    	];
    }
    
    protected function getSenders() 
    {
        return [
            "gestion" => $this->getSenderLabel((new \mail_user_mfa())->get_setting_value("sender")),
            "opac" => $this->getSenderLabel((new \mail_opac_reader_mfa())->get_setting_value("sender"))
        ];
    }

    private function getSenderLabel($sender) 
    {
        global $msg;

        switch ($sender) {
            case 'docs_location':
                return $msg['location'];
            case 'user':
                return $msg['86'];
            case 'reader':
                return $msg['379'];
            case 'parameter':
                return $msg['opac_view_form_parameters'].' : biblio_name / biblio_email';
        }
    }
}