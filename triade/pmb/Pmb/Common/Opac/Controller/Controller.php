<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Controller.php,v 1.2 2024/01/31 10:47:42 qvarin Exp $

namespace Pmb\Common\Opac\Controller;

use Pmb\Common\Controller\Controller as ControllerBase;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
	die("no access");
}

class Controller extends ControllerBase
{

    protected function check_captcha(string $value)
    {
        global $base_path, $msg;

        require_once ($base_path . "/includes/securimage/securimage.php");

        $flag = true;
        $message = "";

        // Captcha
        $securimage = new \Securimage();
        if (! $securimage->check($value)) {
            $flag = false;
            $message = $msg['animation_registration_verifcode_mandatory'];
        }

        // Remove random value
        $_SESSION['image_random_value'] = '';

        return array(
            "success" => $flag,
            "message" => $message
        );
    }
}
