<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PaymentsModel.php,v 1.2 2024/01/03 11:24:11 gneveu Exp $

namespace Pmb\Payments\Opac\Models;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Common\Models\Model;
use Pmb\Payments\Orm\TransactionsOrm;
use Pmb\Payments\Orm\TransactionPaymentsOrm;

class PaymentsModel extends Model
{
    protected $ormName = "\Pmb\Payments\Orm\TransactionPaymentsOrm";

    public const TRANSACTION_IN_PROGRESS = 1;

    public const TRANSACTION_FINISH = 2;

    public const TRANSACTION_ABANDONED = 3;



    /**
     * The function inserts a new transaction into the database and returns the transaction number.
     *
     * @param int numUser The parameter "numUser" represents the number of the user associated with the
     * transaction. It is of type integer.
     * @param int organization The "organization" parameter is an integer that represents the number of
     * the organization associated with the transaction.
     *
     * @return int an integer value, which is the transaction number.
     */

    public function insertTransaction(int $numUser, int $organization): int
    {
        try {
            $transactionNumber = $this->getTransactionNumber();
            $date = new \DateTime();

            $transactionPaymentOrm = new TransactionPaymentsOrm();
            $transactionPaymentOrm->order_number = $transactionNumber;
            $transactionPaymentOrm->payment_date = $date->format('Y-m-d H:i:s');
            $transactionPaymentOrm->payment_status = self::TRANSACTION_IN_PROGRESS;
            $transactionPaymentOrm->num_user = $numUser;
            $transactionPaymentOrm->num_organization = $organization;
            $transactionPaymentOrm->save();

            return $transactionNumber;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * The function "getTransactionNumber" returns a generated transaction number as an integer.
     *
     * @return int An integer value is being returned.
     */

    public function getTransactionNumber(): int
    {
        return $this->generateTransactionNumber();
    }


    /**
     * The function generates a unique transaction number by checking the last entry order number,
     * appending the current year and month, and incrementing the number if it already exists.
     *
     * @return int an integer value, which is the generated transaction number.
     */

    protected function generateTransactionNumber(): int
    {
        $transactionNumber = TransactionPaymentsOrm::getLastEntryOrderNumber();

        $year = date('Y');
        $month = date('m');

        if (substr("$transactionNumber", 0, 6) == ($year . $month)) {
            $transactionNumber = substr("$transactionNumber", 6) + 1;
        } else {
            $transactionNumber =  1;
        }

        $transactionNumber = intval($year . $month . $transactionNumber);

        if (!empty(TransactionPaymentsOrm::find("order_number", $transactionNumber))) {
            return $this->generateTransactionNumber();
        }
        return $transactionNumber;
    }

    /**
     * The function updates the payment details of a transaction in the database.
     *
     * @param int transactionNumber The transaction number is an integer that uniquely identifies a
     * transaction. It is used to find the corresponding transaction payment record in the database.
     * @param string resultrans The parameter "resultrans" is a string that represents the result of
     * the transaction payment. It could be a success message, an error message, or any other relevant
     * information about the payment result.
     * @param int paymentStatus The paymentStatus parameter is an integer that represents the status of
     * the payment. It could have different values depending on the specific implementation, but
     * typically it could be used to indicate whether the payment was successful or failed. For
     * example, a value of 1 could represent a successful payment, while a value of
     */

    public function updateTransactionPayment(int $transactionNumber, string $resultrans, int $paymentStatus): void
    {
        $transactionPaymentOrm = TransactionPaymentsOrm::find("order_number", $transactionNumber);
        $transactionPaymentOrm = new TransactionPaymentsOrm($transactionPaymentOrm[0]->id);

        $date = new \DateTime();

        $transactionPaymentOrm->payment_date = $date->format('Y-m-d H:i:s');
        $transactionPaymentOrm->payment_organization_status = $resultrans;
        $transactionPaymentOrm->payment_status = $paymentStatus;
        $transactionPaymentOrm->save();
    }


    /**
     * The function `getTransactionList` retrieves a list of transactions for a given employer ID and
     * returns an array of payment objects.
     *
     * @param int emprId The parameter "emprId" is an integer representing the ID of the user for whom
     * you want to retrieve the transaction list.
     *
     * @return array an array of TransactionPaymentsOrm objects.
     */

    public static function getTransactionList(int $emprId): array
    {
        $paymentTab = [];

        $transactionTab = TransactionPaymentsOrm::find("num_user", $emprId);
        foreach ($transactionTab as $transaction) {
            $date = new \DateTime($transaction->payment_date);
            $transaction->getOrganization();
            $transaction->unsetStructure();
            $transaction->payment_date = $date->format("d/m/Y H:i:s");
            $paymentTab[] = $transaction;
        }
        return $paymentTab;
    }
}
