<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionsPrivateController.php,v 1.2.2.1 2024/12/24 11:19:35 rtigero Exp $
namespace Pmb\DSI\Opac\Controller;

use Pmb\Common\Opac\Controller\Controller;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Opac\Views\DiffusionsPrivateView;

class DiffusionsPrivateController extends Controller
{
    protected $action;

    public function proceed($action = "")
    {
        global $msg;
        switch ($action) {
            case "bannette_creer":
                global $search, $serialized_search;


                $model = Diffusion::getDiffusionPrivateModel();
                if (empty($model)) {
                    print $msg["diffusion_private_unavailable"];
                    break;
                }
                $data = array();

                $s = new \search();

                if ($serialized_search) {
                    $serializedSearch = stripslashes($serialized_search);
                    $s->unserialize_search($serializedSearch);
                } else {
                    $s->unhistorize_search();
                    $s->strip_slashes();
                    $serializedSearch = $s->serialize_search();
                }

                $data["formData"] = array();
                $data["formData"]["serializedSearch"] = $serializedSearch;
                $data["formData"]["humanQuery"] = $s->make_serialized_human_query($serializedSearch);
                $data["formData"]["search"] = $search;
                $data["formData"]["idEmpr"] = $this->data->id;
                $data["formData"]["emprType"] = "pmb";
                $data["formData"]["diffusionPrivateName"] = "";
                $data["formData"]["diffusionPrivatePeriodicity"] = 0;
                $data["formData"]["diffusionPrivateTime"] = "00:00";
                $view = new DiffusionsPrivateView("dsi/diffusionsPrivate", $data);
                print $view->render();
                break;
        }
    }
}
