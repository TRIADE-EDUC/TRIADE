<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PaymentsAPIController.php,v 1.2 2024/01/03 11:24:14 gneveu Exp $

namespace Pmb\Payments\Opac\Controller;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use DateTime;
use Pmb\Common\Controller\Controller;
use Pmb\Payments\Opac\Models\PaymentsModel;
use Pmb\Payments\Orm\AccountsOrm;
use Pmb\Payments\Orm\TransactionsOrm;
use Pmb\Payments\Orm\TransactionComptePaymentsOrm;

class PaymentsAPIController extends Controller
{
    
    // TODO : Toutes les informations suivantes seront a reporter dans la classe payfip
    // et gerer la mechanique
    
    /**
     * « P » payée CB
     */
    public const PAYMENT_PAID = "P";

    /**
     * « R » refusée CB
     */
    public const PAYMENT_REFUSED = "R";

    /**
     * « A » abandon CB
     */
    public const PAYMENT_ABANDONED = "A";

    /**
     * « V » PayéePrélèvement
     */
    public const PAYMENT_PAID_DEBIT = "V";

    /**
     * « Z » Refusée prélèvement
     */
    public const PAYMENT_REFUSED_DEBIT = "Z";

    /**
     * Moyen de paiement
     */
    public const MODE_PAYMENT = "payfip";

    /**
     * The function `returnPayment` handles different payment scenarios and updates the transaction
     * status accordingly, and then redirects the user to the payments list page.
     *
     * @return The function does not return any value.
     */

    public function returnPayment()
    {
        // on utilise pas tout mais cela pourrait servir
        global $numcli, $exer, $refdet;
        global $objet, $montant, $mel;
        global $saisie, $resultrans, $numauto;
        global $dattrans, $heurtrans; // 01012023 1800
        global $opac_url_base;

        $payments = TransactionComptePaymentsOrm::find("transaction_num", $refdet);
        $idsAccount = [];
        foreach ($payments as $payment) {
            $idsAccount[] = $payment->compte_num;
        }

        if (!$this->checkMontant($idsAccount, $montant)) {
            // Il faut gerer si cela n'est pas bon ou payment partiel
            return false;
        }

        $paymentModel = new PaymentsModel();
        switch (true) {
            case self::PAYMENT_PAID == $resultrans:
            case self::PAYMENT_PAID_DEBIT == $resultrans:
                $this->consolidatedAccount($idsAccount, intval($refdet));
                $paymentModel->updateTransactionPayment(intval($refdet), $resultrans, PaymentsModel::TRANSACTION_FINISH);
                break;
            case self::PAYMENT_REFUSED == $resultrans:
            case self::PAYMENT_ABANDONED == $resultrans:
            case self::PAYMENT_REFUSED_DEBIT == $resultrans:
            default:
                $this->unfreezeAccount($idsAccount, intval($refdet));
                $paymentModel->updateTransactionPayment(intval($refdet), $resultrans, PaymentsModel::TRANSACTION_ABANDONED);
                break;
        }
        header('Location: ' . $opac_url_base . '/empr.php?tab=payments&lvl=payments_list');
        exit();
    }

    /**
     * The function checks if the sum of the balances of multiple accounts is equal to zero.
     *
     * @param
     *            array idsAccount An array of account IDs.
     * @param
     *            int montant The parameter "montant" is an integer representing the amount of money.
     *
     * @return bool a boolean value. It returns true if the sum of the balances of the accounts
     *         specified in the array is equal to 0, and false otherwise.
     */

    private function checkMontant(array $idsAccount, $montant): bool
    {
        foreach ($idsAccount as $id) {
            $accountOrm = new AccountsOrm(intval($id));
            $montant += $accountOrm->solde;
        }

        if (0 == $montant) {
            return true;
        }

        return false;
    }

    /**
     * The unfreezeAccount function takes an array of account IDs, retrieves the corresponding account
     * objects, sets their "droits" property to null, and saves the changes.
     *
     * @param
     *            array idsAccount An array of account IDs that need to be unfrozen.
     */

    private function unfreezeAccount(array $idsAccount): void
    {
        foreach ($idsAccount as $id) {
            $accountOrm = new AccountsOrm(intval($id));
            $accountOrm->droits = null;
            $accountOrm->save();
        }
    }


    /**
     * The function `consolidatedAccount` updates the balance of multiple accounts to zero and creates
     * a transaction record for each account with the updated balance.
     *
     * @param array idsAccount An array of account IDs.
     * @param int transactNumber The `transactNumber` parameter is an integer that represents the
     * transaction number. It is used to generate a comment for the transaction.
     */

    private function consolidatedAccount(array $idsAccount, int $transactNumber): void
    {
        // TODO : on utilise pas tout mais cela pourrait servir
        // A reporter dans la classe payfip
        
        global $numcli, $exer, $refdet;
        global $objet, $montant, $mel;
        global $saisie, $resultrans, $numauto;
        global $dattrans, $heurtrans; // 01012023 1800

        $date = new DateTime();
        $dateFormat = $date->createFromFormat("dmY Hi", $dattrans . " " . $heurtrans);

        $commentaire = "$numauto - " . self::MODE_PAYMENT . " - " . $transactNumber;

        foreach ($idsAccount as $id) {
            $accountOrm = new AccountsOrm(intval($id));
            // On recupere le sold pour gerer la ligne de transaction
            $soldAccount = $accountOrm->solde;

            $accountOrm->solde = 0;
            $accountOrm->droits = null;
            $accountOrm->save();

            $transactionsOrm = new TransactionsOrm();
            $transactionsOrm->compte_id = intval($id);
            $transactionsOrm->date_enrgt = $dateFormat->format("Y-m-d H:i:s");
            $transactionsOrm->date_prevue = $date->format("Y-m-d");
            $transactionsOrm->date_effective = $date->format("Y-m-d");
            $transactionsOrm->montant = intval(abs($soldAccount));
            $transactionsOrm->sens = 1;
            $transactionsOrm->commentaire = $commentaire;
            $transactionsOrm->encaissement = 1;
            $transactionsOrm->realisee = 1;
            $transactionsOrm->save();
        }
    }
}
