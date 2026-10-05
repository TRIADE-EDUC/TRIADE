<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFAOtpController.php,v 1.4 2022/10/18 13:45:59 jparis

namespace Pmb\MFA\Controller;

use Pmb\Common\Views\VueJsView;

class MFAOtpController extends MFAController
{
    protected static $modelNamespace = "Pmb\\MFA\\Models\\MFAOtp";
    protected function defaultAction()
    {
        $data = $this->getDataList();
        $data["methods"] = \mfa_totp::$hash_methods;

        $view = new VueJsView("mfa/otp", $data);
        print $view->render();
    }
}