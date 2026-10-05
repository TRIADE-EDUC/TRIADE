<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EmprOrm.php,v 1.5 2023/07/13 13:14:57 dbellamy Exp $

namespace Pmb\Common\Orm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class EmprOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "empr";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_empr";

    /**
     *
     * @var integer
     */
    protected $id_empr = 0;

    /**
     *
     * @var string
     */
    protected $empr_cb = "";

    /**
     *
     * @var string
     */
    protected $empr_nom = "";

    /**
     *
     * @var string
     */
    protected $empr_prenom = "";

    /**
     *
     * @var string
     */
    protected $empr_adr1 = "";

    /**
     *
     * @var string
     */
    protected $empr_adr2 = "";

    /**
     *
     * @var string
     */
    protected $empr_cp = "";

    /**
     *
     * @var string
     */
    protected $empr_ville = "";

    /**
     *
     * @var string
     */
    protected $empr_pays = "";

    /**
     *
     * @var string
     */
    protected $empr_mail = "";

    /**
     *
     * @var string
     */
    protected $empr_tel1 = "";

    /**
     *
     * @var string
     */
    protected $empr_tel2 = "";

    /**
     *
     * @var string
     */
    protected $empr_prof = "";

    /**
     *
     * @var integer
     */
    protected $empr_year = 0;

    /**
     *
     * @var integer
     */
    protected $empr_categ = 0;

    /**
     *
     * @var integer
     */
    protected $empr_codestat = 0;

    /**
     *
     * @var \DateTime
     */
    protected $empr_creation = "";

    /**
     *
     * @var \DateTime
     */
    protected $empr_modif = "";

    /**
     *
     * @var integer
     */
    protected $empr_sexe = 0;

    /**
     *
     * @var string
     */
    protected $empr_login = "";

    /**
     *
     * @var string
     */
    protected $empr_password = "";

    /**
     *
     * @var integer
     */
    protected $empr_password_is_encrypted = 0;

    /**
     *
     * @var string
     */
    protected $empr_digest = "";

    /**
     *
     * @var \DateTime
     */
    protected $empr_date_adhesion = "";

    /**
     *
     * @var \DateTime
     */
    protected $empr_date_expiration = "";

    /**
     *
     * @var string
     */
    protected $empr_msg = "";

    /**
     *
     * @var string
     */
    protected $empr_lang = "";

    /**
     *
     * @var integer
     */
    protected $empr_ldap = 0;

    /**
     *
     * @var integer
     */
    protected $type_abt = 0;

    /**
     *
     * @var \DateTime
     */
    protected $last_loan_date = "";

    /**
     *
     * @var integer
     */
    protected $empr_location = 0;

    /**
     *
     * @var \DateTime
     */
    protected $date_fin_blocage = "";

    /**
     *
     * @var integer
     */
    protected $total_loans = 0;

    /**
     *
     * @var integer
     */
    protected $empr_statut = 0;

    /**
     *
     * @var string
     */
    protected $cle_validation = "";

    /**
     *
     * @var integer
     */
    protected $empr_sms = 0;

    /**
     *
     * @var string
     */
    protected $empr_subscription_action = "";

    /**
     *
     * @var string
     */
    protected $empr_pnb_password = "";

    /**
     *
     * @var string
     */
    protected $empr_pnb_password_hint = "";

    /**
     *
     * @var string
     */
    protected $mfa_secret_code = "";

    /**
     *
     * @var string
     */
    protected $mfa_favorite = "";

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;
}