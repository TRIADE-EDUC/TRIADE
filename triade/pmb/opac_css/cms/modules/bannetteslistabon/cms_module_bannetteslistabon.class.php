<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannetteslistabon.class.php,v 1.2 2024/01/18 15:41:28 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $include_path;

class cms_module_bannetteslistabon extends cms_module_common_module {

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
        global $bannette_abon;

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
                                if ( is_array($bannette_abon) ) {

                                    $bannette_ids = array_keys($bannette_abon);
                                    array_walk($bannette_ids, function(&$a) { $a = intval($a);});

                                    foreach ($bannette_ids as $bannette_id) {
                                        if($bannette_id) {
                                            pmb_mysql_query("INSERT ignore INTO bannette_abon SET num_bannette=" . $bannette_id . ", num_empr=" . $id_empr);
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
