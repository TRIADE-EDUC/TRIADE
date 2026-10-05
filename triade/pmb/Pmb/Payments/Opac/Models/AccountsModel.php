<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AccountsModel.php,v 1.2 2024/01/03 11:24:12 gneveu Exp $

namespace Pmb\Payments\Opac\Models;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Common\Models\Model;
use Pmb\Payments\Orm\AccountsOrm;
use Pmb\Payments\Orm\TransactionsOrm;
use Pmb\Common\Helper\HTML;
use Pmb\Common\Helper\DateHelper;
use Pmb\Common\Orm\EmprOrm;
use Pmb\Common\Helper\Helper;
use Pmb\Payments\Orm\GroupeOrm;
use Pmb\Payments\Orm\EmprGroupeOrm;
use Pmb\Payments\Orm\PaymentOrganizationOrm;
use stats;
use Pmb\Payments\Orm\TransactionComptePaymentsOrm;

class AccountsModel extends Model
{
    protected $ormName = "\Pmb\Payments\Orm\CompteOrm";

    public const ACCOUNT_FROZEN = 666;

    /**
     * Retourne la liste des comptes pour un emprunteur
     *
     * @param int $emprId
     * @return array
     */

    public static function getEmprAccounts(int $emprId)
    {

        $accountsOrm = new AccountsOrm();
        $accountsList = $accountsOrm->find("proprio_id", $emprId);

        $accountsTab = AccountsModel::getListAccounts($accountsList, $emprId);
        $respGroupe = GroupeOrm::find("resp_groupe", $emprId);

        // J'ai la responsablite d'un groupe
        if (!empty($respGroupe)) {
            $idGroupe = $respGroupe[0]->id_groupe;
            // Je vais recupere tous les membres de mon groupe
            $groupMember = EmprGroupeOrm::find("groupe_id", $idGroupe);
            foreach ($groupMember as $member) {
                $accountMemberList = $accountsOrm->find("proprio_id", $member->empr_id);
                // Les comptes du groupe doivent-ils de l'argent
                $memberAccount = AccountsModel::getListAccounts($accountMemberList, $member->empr_id, true);
                // Si oui on les ajoutes
                if (!empty($memberAccount)) {
                    $accountsTab["groupmember"][] = $memberAccount;
                }
            }
        }

        // TODO : Un cas un peu bête que je n'ai pas pensé c'est que je peux ne pas faire parti de mon groupe
        // Hélas je dois m'ajouter, a revoir :(

        return $accountsTab;
    }

    /**
     * Retourne la liste des comptes pour un emprunteur
     *
     * @param int $emprId
     * @return array
     */

    public static function getListAccounts($accountsList, $id, $group = false)
    {
        $accountsTab = array();

        foreach ($accountsList as $key => $account) {
            // Si on gere un groupe on regarde le due du compte pour que l'on puisse payer ces dettes
            if (0 <= $account->solde && $group) {
                continue;
            }

            $accountsTab["accounts"][$key] = [
                "label" => self::get_typ_compte_lib($account->type_compte_id),
                "id" => $account->id_compte,
                "sold" => $account->solde,
                "isFrozen" => intval(($account->droits && self::ACCOUNT_FROZEN == $account->droits)) ,
            ];

            $transacOrm = new TransactionsOrm();
            $transacList = $transacOrm->find("compte_id", $account->id_compte);

            foreach ($transacList as $transac) {
                $date = new \DateTime($transac->date_enrgt);
                $accountsTab["accounts"][$key]["transacash"][] = [
                    "label" => $transac->commentaire,
                    "credit" =>  (1 == $transac->sens) ? true : false ,
                    "sold" => $transac->montant,
                    "date_enrgt" => $date->format("d/m/Y")
                ];
            }
            $accountsTab["accounts"][$key]["empr"] = Helper::toArray(EmprOrm::find("id_empr", $id));

            if (!isset($accountsTab["sold"])) {
                $accountsTab["sold"] = 0;
            }
            $accountsTab["sold"] += $account->solde ?? 0;
        }

        return $accountsTab;

    }

    /**
     * The function "get_typ_compte_lib" returns the label of a type of account based on its ID.
     *
     * @param idTypeAccount The parameter `idTypeAccount` is an integer that represents the type of
     * account. It is used to determine the corresponding account label or description.
     *
     * @return the label (libelle) of a specific type of account based on the given idTypeAccount.
     */

    public static function get_typ_compte_lib($idTypeAccount)
    {
        global $msg;
        $r = "";
        switch ($idTypeAccount) {
            case 1:
                $r = $msg["finance_cmpte_abt"];
                break;
            case 2:
                $r = $msg["finance_cmpte_amendes"];
                break;
            case 3:
                $r = $msg["finance_cmpte_prets"];
                break;
            case 22:
                $r = $msg["finance_cmpte_animation"];
                break;
            default:
                $requete = "select libelle from type_comptes where id_type_compte=" . $idTypeAccount;
                $resultat = pmb_mysql_query($requete);
                if (@pmb_mysql_num_rows($resultat)) {
                    $r = pmb_mysql_result($resultat, 0, 0);
                }
        }
        return $r;
    }

    /**
     * The function updates the status of multiple accounts to "frozen" and inserts a transaction into
     * the payment model.
     *
     * @param array accounts An array of account IDs.
     * @param int emprId The parameter `emprId` is an integer representing the ID of an employee.
     *
     */

    public static function updateStatusAccount(array $accounts, int $emprId)
    {
        $accountsTab = [];

        foreach ($accounts as $account) {
            $accountOrm = new AccountsOrm(intval($account));
            $accountsTab[] = $accountOrm;
            if($accountOrm->droits && self::ACCOUNT_FROZEN == $accountOrm->droits) {
                return false;
            }
        }

        // TODO : alors ce qu'on fait... On va chercher pour l'instant payfip en base...
        // Je connais pas son  ID pour le moment du coup je vais le chercher avec son nom dans la base.
        // Plus tard faudra aller chercher le bon organisme de paiement, mais pour l'instant on n'a que payfip...
        $organization = PaymentOrganizationOrm::find("name", "payfip");

        $paymentModel = new PaymentsModel();
        $transacNumber = $paymentModel->insertTransaction($emprId, $organization[0]->id);

        foreach ($accountsTab as $accountInstance) {
            $accountInstance->droits = self::ACCOUNT_FROZEN;
            $accountInstance->save();

            $transComptePayementOrm = new TransactionComptePaymentsOrm();
            $transComptePayementOrm->transaction_num = $transacNumber;
            $transComptePayementOrm->compte_num = $accountInstance->id_compte;
            $transComptePayementOrm->amount = $accountInstance->solde;
            $transComptePayementOrm->save();
        }

        return $transacNumber;
    }
}
