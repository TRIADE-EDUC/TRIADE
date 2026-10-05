<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationOpacController.php,v 1.3 2024/04/10 09:43:12 tsamson Exp $
namespace Pmb\Authentication\Controller;

use Pmb\Common\Controller\Controller;

class AuthenticationOpacController extends Controller
{

    public function proceed()
    {
        global $action;

        switch ($action) {
            case "logout":
                $this->logout();
                break;
            case 'check_auth':
            case 'get_form':
            default :
                $this->getAuthForm();
                break;
        }
    }

    protected function getAuthForm()
    {
        global $base_path, $callback_func, $popup_mode;
        $popup_mode = \auth_popup::MODE_ONLY_LOGIN;

        $callback_func = "auth_callback";
        $opac_password_forgotten_show = 0;
        $opac_websubscribe_show = 0;

        $authPopup = new \auth_popup();
        return $authPopup->process();
    }

    protected function logout()
    {
        global $mobile_app;
        unset($_SESSION['user_code']);
        unset($_SESSION["ws_sess_id"]);
        session_destroy();
        logout();
        
        if ($mobile_app) {
            $this->getAuthForm();
        }
    }
}
