<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_controller.class.php,v 1.7.10.1 2025/01/30 13:55:10 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path;
global $lvl, $sub;

require_once $class_path . '/pnb/pnb.class.php';

class pnb_controller
{

    public function proceed()
    {
        global $lvl;
        global $sub;
        $empr_id = intval($_SESSION['id_empr_session']);
        if (! $empr_id) {
            return;
        }

        $pnb = new pnb();

        switch ($lvl) {

            case 'all':
            case 'pnb_loan_list':
                print $pnb->get_empr_loans_list($empr_id);
                break;

            case 'pnb_devices':
                switch ($sub) {

                    default:
                        print $pnb->get_devices_list($empr_id);
                        break;

                    case 'save':
                        $pnb->save_devices_list($empr_id);
                        print $pnb->get_devices_list($empr_id);
                        break;
                }
                break;

            case 'pnb_parameters':

                switch ($sub) {

                    default:
                        print $pnb->get_parameters($empr_id);
                        break;

                    case 'save':
                        $pnb->save_parameters($empr_id);
                        print $pnb->get_parameters($empr_id);
                        break;
                }
                break;
        }
    }

    public function proceed_ajax($action)
    {
        global $msg, $allow_pnb;

        $pnb = new pnb();
        $response = ['status' => false, 'message' => '', 'infos' => ''];

        if (! $allow_pnb) {
            $response = ['status' => false, $msg['pnb_not_allowed'], 'infos' => 'pnb_not_allowed'];
            echo encoding_normalize::json_encode($response);
            return;
        }

        switch ($action) {

            case 'loan':

                global $empr_pnb_device, $notice_id;
                $notice_id = isset($notice_id) ? intval($notice_id) : 0;
                $empr_pnb_device = (isset($empr_pnb_device) && is_string($empr_pnb_device)) ? $empr_pnb_device : '';
                if (! $notice_id) {
                    echo encoding_normalize::json_encode($response);
                }
                $pnb->loan_book($empr_pnb_device, $notice_id);
                break;

            case 'get_loan_form':

                global $notice_id;
                $notice_id = isset($notice_id) ? intval($notice_id) : 0;
                $pnb->get_loan_form($notice_id);
                break;

            case 'post_loan_info':

                global $empr_pnb_device, $notice_id, $pass, $hint_pass;
                $empr_pnb_device = (isset($empr_pnb_device) && is_string($empr_pnb_device)) ? $empr_pnb_device : '';
                $notice_id = isset($notice_id) ? intval($notice_id) : 0;
                $pass = (isset($pass) && is_string($pass)) ? $pass : '';
                $hint_pass = (isset($hint_pass) && is_string($hint_pass)) ? $hint_pass : '';
                $pnb->loan_book($empr_pnb_device, $notice_id, $pass, $hint_pass);
                break;

            case 'returnLoan':

                global $id_empr, $expl_id, $fromPortal, $drm;
                $id_empr = isset($id_empr) ? intval($id_empr) : 0;
                $expl_id = isset($expl_id) ? intval($expl_id) : 0;
                $fromPortal = isset($fromPortal) ? intval($fromPortal) : 0;
                $drm = (isset($drm) && is_string($drm)) ? $drm : '';
                if ($id_empr && $expl_id) {
                    $response = $pnb->return_book($id_empr, $expl_id, $fromPortal, $drm);
                }
                echo encoding_normalize::json_encode($response);
                break;

            case 'extendLoan':

                global $id_empr, $expl_id, $fromPortal, $drm;
                $id_empr = isset($id_empr) ? intval($id_empr) : 0;
                $expl_id = isset($expl_id) ? intval($expl_id) : 0;
                $fromPortal = isset($fromPortal) ? intval($fromPortal) : 0;
                $drm = (isset($drm) && is_string($drm)) ? $drm : '';
                if ($id_empr && $expl_id) {
                    $response = $pnb->extend_loan($id_empr, $expl_id, $fromPortal, $drm);
                }
                echo encoding_normalize::json_encode($response);
                break;

            case 'get_empr_devices_list':

                global $notice_id;
                $notice_id = isset($notice_id) ? intval($notice_id) : 0;
                echo encoding_normalize::json_encode($pnb->get_empr_devices_list($notice_id));
                break;
        }
    }
}
