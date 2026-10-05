<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EmprModel.php,v 1.10 2023/09/21 14:04:14 gneveu Exp $
namespace Pmb\Common\Models;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Common\Orm\EmprOrm;
use Pmb\Common\Helper\GlobalContext;

class EmprModel extends Model
{

    protected $ormName = "\Pmb\Common\Orm\EmprOrm";

    public $idEmpr;

    public $emprCb;

    public $emprNom;

    public $emprPrenom;

    public $emprAdr1;

    public $emprAdr2;

    public $emprCp;

    public $emprVille;

    public $emprPays;

    public $emprMail;

    public $emprTel1;

    public $emprTel2;

    public $emprProf;

    public $emprYear;

    public $emprCateg;

    public $emprCodestat;

    public $emprCreation;

    public $emprModif;

    public $emprSexe;

    public $emprLogin;

    public $emprPassword;

    public $emprPasswordIsEncrypted;

    public $emprDigest;

    public $emprDateAdhesion;

    public $emprDateExpiration;

    public $emprMsg;

    public $emprLang;

    public $emprLdap;

    public $typeAbt;

    public $lastLoanDate;

    public $emprLocation;

    public $dateFinBlocage;

    public $totalLoans;

    public $emprStatut;

    public $cleValidation;

    public $emprSms;

    public $emprSubscriptionAction;

    public $emprPnbPassword;

    public $emprPnbPasswordHint;

    public $affEmprDateAdhesion;

    public $affEmprDateExpiration;

    public $nbDaysBeforeExpiration;

    public $affEmprDayDate;

    public $affLastLoanDate;

    public static function getEmprByCB(string $cb)
    {
        $emprOrmInstances = EmprOrm::find('empr_cb', stripslashes($cb));
        if (! empty($emprOrmInstances)) {
            $id_empr = $emprOrmInstances[0]->id_empr;
            if (! empty($id_empr)) {
                return new EmprModel($id_empr);
            }
        }
        return new EmprModel(0);
    }

    public static function ValidBarcode(string $barcode)
    {
        if (empty($barcode)) {
            return false;
        }

        $emprOrmInstances = EmprOrm::find('empr_cb', stripslashes($barcode));
        return ! empty($emprOrmInstances);
    }

    /**
     * Redefinition du code-barres lecteur lors de l'inscription en ligne
     *
     * @param int $id_empr : id lecteur
     * @return string : code-barres
     */
    public static function redefineBarcodeOnWebRegistration(int $id_empr = 0)
    {
        // Recuperation des parametres necessaires
        $base_path = GlobalContext::get('base_path');
        $opac_websubscribe_num_carte_auto = GlobalContext::get('opac_websubscribe_num_carte_auto');
        $opac_websubscribe_num_carte_auto = explode(',', $opac_websubscribe_num_carte_auto);

        if (! $id_empr) {
            return '';
        }
        $pe_emprcb = 'www' . $id_empr;

        switch ($opac_websubscribe_num_carte_auto[0]) {

            // Préfixe + Nb digits
            case 2:

                $long_prefixe = $opac_websubscribe_num_carte_auto[1];
                $nb_chiffres = $opac_websubscribe_num_carte_auto[2];
                $prefix = empty($opac_websubscribe_num_carte_auto[3]) ? '' : ($opac_websubscribe_num_carte_auto[3]);

                $query = "SELECT CAST(SUBSTRING(empr_cb," . ($long_prefixe + 1) . ") AS UNSIGNED) AS max_cb, ";
                $query .= "SUBSTRING(empr_cb,1," . ($long_prefixe * 1) . ") AS prefixdb ";
                $query .= "FROM empr ORDER BY max_cb DESC limit 0,1";
                $result = pmb_mysql_query($query);
                $cb_initial = pmb_mysql_fetch_object($result);

                $pe_emprcb = ($cb_initial->max_cb * 1) + 1;

                if (! $nb_chiffres) {
                    $nb_chiffres = strlen($pe_emprcb);
                }

                if (! $prefix) {
                    $prefix = $cb_initial->prefixdb;
                }

                $pe_emprcb = $prefix . substr((string) str_pad($pe_emprcb, $nb_chiffres, "0", STR_PAD_LEFT), - $nb_chiffres);

                break;

            // Fonction dans opac_css/circ/empr/
            case 3:

                $num_carte_auto_fctname = trim($opac_websubscribe_num_carte_auto[1]);
                $num_carte_auto_filename = $base_path . '/circ/empr/' . $num_carte_auto_fctname . '.inc.php';
                if (file_exists($num_carte_auto_filename)) {
                    require_once ($num_carte_auto_filename);
                    if (function_exists($num_carte_auto_fctname)) {
                        $pe_emprcb = $num_carte_auto_fctname();
                    }
                }
                break;
        }
        return $pe_emprcb;
    }

    public function save()
    {
        $empr_orm = new EmprOrm();
        $now = new \DateTime("now");

        $empr_orm->empr_cb = $this->emprCb;
        $empr_orm->empr_nom = $this->emprNom;
        $empr_orm->empr_prenom = $this->emprPrenom;
        $empr_orm->empr_adr1 = $this->emprAdr1;
        $empr_orm->empr_adr2 = $this->emprAdr2;
        $empr_orm->empr_cp = $this->emprCp;
        $empr_orm->empr_ville = $this->emprVille;
        $empr_orm->empr_pays = $this->emprPays;
        $empr_orm->empr_mail = $this->emprMail;
        $empr_orm->empr_tel1 = $this->emprTel1;
        $empr_orm->empr_tel2 = $this->emprTel2;
        $empr_orm->empr_prof = $this->emprProf;
        $empr_orm->empr_year = $this->emprYear;
        $empr_orm->empr_categ = $this->emprCateg;
        $empr_orm->empr_codestat = $this->emprCodestat;
        $empr_orm->empr_creation = $now->format("Y-m-d H:i:s");
        $empr_orm->empr_modif = $now->format("Y-m-d");
        $empr_orm->empr_sexe = $this->emprSexe;
        $empr_orm->empr_login = $this->emprLogin;
        $empr_orm->empr_password = $this->emprPassword;
        $empr_orm->empr_password_is_encrypted = $this->emprPasswordIsEncrypted;
        $empr_orm->empr_digest = $this->emprDigest;
        $empr_orm->empr_date_adhesion = $this->emprDateAdhesion;
        $empr_orm->empr_date_expiration = $this->emprDateExpiration;
        $empr_orm->empr_msg = $this->emprMsg;
        $empr_orm->empr_lang = $this->emprLang;
        $empr_orm->empr_ldap = $this->emprLdap;
        $empr_orm->type_abt = $this->typeAbt;
        $empr_orm->last_loan_date = $this->lastLoanDate;
        $empr_orm->empr_location = $this->emprLocation;
        $empr_orm->date_fin_blocage = $this->dateFinBlocage;
        $empr_orm->total_loans = $this->totalLoans;
        $empr_orm->empr_statut = $this->emprStatut;
        $empr_orm->cle_validation = $this->cleValidation;
        $empr_orm->empr_sms = $this->emprSms;
        $empr_orm->empr_subscription_action = $this->emprSubscriptionAction;
        $empr_orm->empr_pnb_password = $this->emprPnbPassword;
        $empr_orm->empr_pnb_password_hint = $this->emprPnbPasswordHint;
        $empr_orm->save();
        $this->idEmpr = $empr_orm->id_empr;

        return $this->idEmpr;
    }

    public static function getBarcode($idEmpr)
    {
        $empr = EmprOrm::find('id_empr', $idEmpr);
        return $empr[0]->empr_cb;
    }
}
