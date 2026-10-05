<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ComptesModel.php,v 1.1 2023/03/21 13:35:34 gneveu Exp $
namespace Pmb\Common\Models;

use Pmb\Common\Orm\EmprOrm;

class ComptesModel extends Model
{
    protected $ormName = "\Pmb\Common\Orm\ComptesOrm";

    public const ACCOUNT_ANIMATION = 22;
    public const ACCOUNT_SENS_DEBIT = -1;
    public const ACCOUNT_SENS_CREDIT = 1;

    public static function accountDebit(int $idEmpr, float $total, int $type, string $comment, int $sens)
    {
        global $PMBuserid, $PMBusername;

        if (empty($PMBuserid) && empty($PMBusername)) {
            // C'est moche mais bon cela n'est pas gérer pour que une autre personne puisse faire des lignes de comptes
            $PMBuserid = 1;
            $PMBusername = "admin";
        }

        // Sinon a pas deja de compte... C'est la methode pour en créer un...
        $idCompte = \comptes::get_compte_id_from_empr($idEmpr, $type);
        $compte = new \comptes($idCompte);
        $idTransac = $compte->record_transaction(date("Y-m-d"), $total, $sens, $comment);

        if (false !== $idTransac) {
            $compte->validate_transaction($idTransac);
        }
    }

}