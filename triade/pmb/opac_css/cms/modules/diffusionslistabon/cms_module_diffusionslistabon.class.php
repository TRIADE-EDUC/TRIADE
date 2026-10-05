<?php

// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_diffusionslistabon.class.php,v 1.1.4.2 2025/04/11 10:10:09 jparis Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

use Pmb\DSI\Controller\SubscribersController;
use Pmb\DSI\Opac\Controller\DiffusionsController;

class cms_module_diffusionslistabon extends cms_module_common_module
{
    public function __construct($id = 0)
    {
        $this->module_path = str_replace(basename(__FILE__), "", __FILE__);
        parent::__construct($id);
    }

    public function execute_ajax()
    {
        global $do;
        global $f_verifcode;
        global $f_login;
        global $diffusion_abon;

        $response = [
            'content' => 'error',
            'content-type' => 'text/html',
        ];

        if ( empty($do) || !in_array($do, ['connect', 'subscribe'])) {
            return $response;
        }

        switch ($do) {

            case "connect":

                $log_ok = connexion_empr();
                if ($log_ok) {
                    $response['content'] = 'ok';
                }
                break;

            case "subscribe":

                if( empty($f_verifcode) || !is_string($f_verifcode) ) {
                    $f_verifcode = '';
                }
                if('' == $f_verifcode) {
                    break;
                }
                $securimage = new Securimage();
                if( $securimage->check($f_verifcode) ) {

                    $_SESSION['image_is_logged_in'] = true;
                    $_SESSION['image_random_value'] = '';

                    global $include_path;
                    require $include_path . '/websubscribe.inc.php';
                    $verif = verif_validite_compte();

                    switch($verif[0]) {
                        case 0 :
                            $res = pmb_mysql_query("SELECT id_empr FROM empr WHERE empr_login='" . addslashes($f_login) . "'");
                            if ($res && pmb_mysql_num_rows($res)) {
                                $row = pmb_mysql_fetch_assoc($res);
                                $id_empr = $row['id_empr'];
                                //Abonnement aux bannettes sur inscription
                                if ( is_array($diffusion_abon) ) {

                                    $diffusion_ids = array_keys($diffusion_abon);
                                    array_walk($diffusion_ids, function(&$a) { $a = intval($a);});

                                    //Nouvelle DSI
                                    $controller_data = new \stdClass();
                                    $controller_data->id = (int) $id_empr;
                                    //On ne gère que les listes d'emprunteurs
                                    //Pour le moment ...
                                    $controller_data->emprType = "pmb";
                                    $diffusion_controller = new DiffusionsController($controller_data);

                                    foreach ($diffusion_ids as $diffusion_id) {
                                        $diffusion_id = intval($diffusion_id);
                                        if($diffusion_id) {
                                            $subscriber = $diffusion_controller->getSubscriberFromType();
                                            $subscriber_controller = new SubscribersController($subscriber);
                                            $subscriber_controller->subscribeFromOpac("diffusions", $diffusion_id, false);
                                        }
                                    }
                                }
                                $response['content'] = 'ok';
                            }
                            break;
                        default :
                            $response['content'] = $verif[2];
                            break;
                        }

                } else {

                    $response['content'] = 'error_code';
                    $_SESSION['image_is_logged_in'] = false;
                    $_SESSION['image_random_value'] = '';
                }

                break;
            default :
                break;
        }

        return $response;
    }
}