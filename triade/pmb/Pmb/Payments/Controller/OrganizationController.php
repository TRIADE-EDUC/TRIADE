<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OrganizationController.php,v 1.2 2024/01/03 11:24:14 gneveu Exp $

namespace Pmb\Payments\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Common\Views\VueJsView;
use Pmb\Payments\Models\OrganizationModel;
use Pmb\Payments\Orm\PaymentOrganizationOrm;

class OrganizationController extends Controller
{
    /**
     *
     * @var string
     */
    public $action = "list";

    public function proceed(string $action = "")
    {
        $this->action = $action;
        switch ($action) {
            case "add":
                $this->addOrganisation();
                break;
            case "edit":
                $this->editOrganisation();
                break;
            case "save":
                $this->saveOrganisation();
                break;
            case "list":
            default:
                $this->organizationList();
                break;
        }
    }

    /**
     * The function "organizationList" creates a new view object and renders it, passing in two arrays
     * of organization data.
     */

    public function organizationList()
    {
        $newVue = new VueJsView("payments/paymentsOrganization", [
            "action" => $this->action,
            "organizationlist" => OrganizationModel::getOrganizationList(),
            "organizationlistavaible" => OrganizationModel::getOrganizationListAvaible(),
        ]);
        print $newVue->render();
    }

    /**
     * The function "addOrganisation" creates a new instance of the PaymentsOrganizationView class and
     * renders the view with the organization list and available organization list as data.
     */

    public function addOrganisation()
    {
        $newVue = new VueJsView("payments/paymentsOrganization", [
            "action" => $this->action,
            "organizationlistavaible" => OrganizationModel::getOrganizationListAvaible(),
        ]);
        print $newVue->render();
    }

    /**
     * The function saves an organization's data into the database using the PaymentOrganizationOrm
     * model in PHP.
     */
    
    public function saveOrganisation()
    {
        $organization = PaymentOrganizationOrm::find("name", $this->data->organization->name);
        if (!empty($organization)) {
            $paymentOrganizationOrm = new PaymentOrganizationOrm($organization[0]->id);
        } else {
            $paymentOrganizationOrm = new PaymentOrganizationOrm();
            $paymentOrganizationOrm->name = ($this->data->organization->name);
        }
        $paymentOrganizationOrm->data = json_encode($this->data->organization->data);
        $paymentOrganizationOrm->save();
        $this->organizationList();
    }

    /**
     * The setData function sets the value of the data property in a PHP class.
     *
     * @param data The "data" parameter is a variable that represents the data that you want to set for
     * the object. It can be any type of data, such as a string, integer, array, or object.
     */
    
    public function setData($data)
    {
        $this->data = $data;
    }

    public function editOrganisation()
    {
        $newVue = new VueJsView("payments/paymentsOrganization", [
            "action" => $this->action,
            "organization" => PaymentOrganizationOrm::findById(intval($this->data)),
            "organizationlistavaible" => OrganizationModel::getOrganizationListAvaible(),
        ]);
        print $newVue->render();
    }
}
