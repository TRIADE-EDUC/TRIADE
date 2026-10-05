<?php

// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PaymentsController.php,v 1.5 2024/01/03 11:24:14 gneveu Exp $

namespace Pmb\Payments\Opac\Controller;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Payments\Opac\Models\AccountsModel;
use Pmb\Common\Controller\Controller;
use Pmb\Common\Opac\Views\VueJsView;
use Pmb\Payments\Opac\Models\PaymentsModel;

class PaymentsController extends Controller
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
            case "update_status":
                return $this->updateStatusAccountAction();
                break;
            case "list":
            default:
                $this->listPaymentsAction(intval($this->data->empr_id));
                break;
        }
    }

    /**
     * The function "listPaymentsAction" renders a view with payment information for a given employer
     * ID.
     *
     * @param int emprId The `emprId` parameter is an integer that represents the ID of an employer. It
     * is used to retrieve the accounts associated with that employer.
     */

    public function listPaymentsAction(int $emprId)
    {
        global $pmb_gestion_devise;
        $accounts = AccountsModel::getEmprAccounts($emprId);

        $newVue = new VueJsView("payments/payments", [
            "accounts" => $accounts["accounts"],
            "emprid" => $emprId,
            "sold" => $accounts["sold"],
            "groupmember" => $accounts["groupmember"] ?? [],
            "action" => $this->action,
            "devise" => $pmb_gestion_devise,
            "transactionlist" => PaymentsModel::getTransactionList(intval($emprId)),
        ]);
        print $newVue->render();
    }


    /**
     * The function updates the status of an account and returns a success status and transaction
     * number.
     *
     * @return an array. If the transactionNumber is an integer, the array will have a "success" key
     * set to true and a "transactionNumber" key set to the value of . If the
     * transactionNumber is not an integer, the array will have a "success" key set to false.
     */

    public function updateStatusAccountAction()
    {
        $transactionNumber = AccountsModel::updateStatusAccount($this->data->accounts, intval($this->data->emprId));
        if (is_int($transactionNumber)) {
            return [
                "success" => true,
                "transactionNumber" => $transactionNumber
            ];
        }
        return [
            "success" => false
        ];
    }
}
