<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFAManagementController.php,v 1.2 2023/07/13 13:17:02 jparis Exp $
namespace Pmb\MFA\Opac\Controller;

use mfa_root;
use Pmb\Common\Opac\Controller\Controller;
use Pmb\MFA\Controller\MFAOtpController;
use Pmb\MFA\Controller\MFAServicesController;
use Pmb\MFA\Opac\Views\MFAView;

class MFAManagementController extends Controller
{
    protected $action;

    public function proceed($action = "")
    {
        global $opac_url_base, $opac_biblio_name;

        $this->action = $action;

        $emprData = $this->getEmprData();
        $mfaService = (new MFAServicesController())->getData("OPAC");
        
        $data = [
            "url_base" => $opac_url_base,
            "action" => $this->action,
            "context" => "opac",
            "sms_activate" => $mfaService->sms,
            "empr" => [
                "id" => $this->data->empr_id,
                "mfa_secret_code" => $emprData["mfa_secret_code"] ?? "",
                "mfa_favorite" => $emprData["mfa_favorite"] ?? "",
                "empr_tel1" => $emprData["empr_tel1"] ?? "",
                "empr_sms" => $emprData["empr_sms"] ?? "",
                "empr_mail" => $emprData["empr_mail"] ?? ""
            ]
        ];
            
        if(empty($emprData["mfa_secret_code"])) {
            $mfaRoot = new mfa_root();
            $mfaOtp = (new MFAOtpController())->getData("OPAC");

            $data["suggest_message"] = nl2br($mfaService->getTranslatedSuggestMessage());

            $data["empr"]["temp_secret_code"] = $mfaRoot->generate_secret_code(7);
            $data["empr"]["temp_base32_secret_code"] = base32_upper_encode($data["empr"]["temp_secret_code"]);
            $data["empr"]["url_qr_code"] = $mfaRoot->get_qr_code_url('totp', 'opac', $data["empr"]["temp_base32_secret_code"],
				[
                    'algorithm' => $mfaOtp->hashMethod,
                    'digits' => $mfaOtp->lengthCode, 
                    'period' => $mfaOtp->lifetime,
                    'issuer' => $opac_biblio_name
                ]);
        }

        $view = new MFAView("mfa/management", $data);
        print $view->render();
    }

    private function getEmprData() {
        $query = "SELECT mfa_secret_code, mfa_favorite, empr_tel1, empr_sms, empr_mail FROM empr WHERE id_empr = " . $this->data->empr_id;
        $result = pmb_mysql_query($query);

        return pmb_mysql_fetch_assoc($result);
    }
}