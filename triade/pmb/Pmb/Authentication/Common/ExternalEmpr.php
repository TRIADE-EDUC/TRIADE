<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ExternalEmpr.php,v 1.2 2023/06/23 12:38:09 dbellamy Exp $

namespace Pmb\Authentication\Common;

use Pmb\Authentication\Common\AbstractLogger;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

global $base_path, $num_carte_auto;


if (!class_exists('emprunteur')) {
    require_once __DIR__.'/../../classes/emprunteur.class.php';
}


class ExternalEmpr extends AbstractLogger
{

    protected $id_empr = 0;
    protected $empr_cb = '';
    protected $empr_nom = '';
    protected $empr_prenom = '';
    protected $empr_adr1 = '';
    protected $empr_adr2 = '';
    protected $empr_cp = '';
    protected $empr_ville = '';
    protected $empr_pays = '';
    protected $empr_mail = '';
    protected $empr_tel1 = '';
    protected $empr_tel2 = '';
    protected $empr_prof = '';
    protected $empr_year = '';
    protected $empr_categ = 0;
    protected $empr_codestat = 0;
    protected $empr_creation = '';
    protected $empr_modif = '';
    protected $empr_sexe = 0;
    protected $empr_login = '';
    protected $empr_password = '';
    protected $empr_date_adhesion = '';
    protected $empr_date_expiration = '';
    protected $empr_msg = '';
    protected $empr_lang = 'fr_FR';
    protected $empr_ldap = 0;
    protected $type_abt = 0;
    protected $last_loan_date = '';
    protected $empr_location = 0;
    protected $date_fin_blocage = '';
    protected $total_loans = 0;
    protected $empr_statut = 0;
    protected $cle_validation = '';
    protected $empr_sms = 0;
    protected $duree_adhesion = 365;
    protected $default_empr_categ = 1;
    protected $default_empr_codestat = 1;
    protected $default_empr_statut = 1;
    protected $default_empr_location = 1;
    protected $cps = array();
    protected $dont_update = array();
    protected $ldap_logon_attr = 'uid';
    protected $search_result = array();

    protected $is_new = false;

    public function __set($name = '', $value = '')
    {
        static::$logger->debug(__METHOD__ . '(' . $name . ', '.$value.')');

        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        }
    }

    public function __get($name = '')
    {
        static::$logger->debug(__METHOD__ . '(' . $name . ')');

        if (property_exists($this, $name)) {
            return ($this->{$name});
        }
        return false;
    }

    public function setDureeAdhesion()
    {
        static::$logger->debug(__METHOD__);

        if (! $this->empr_categ) {
            $this->setEmprCateg();
        }

        if ($this->empr_categ) {
            $q = 'select duree_adhesion from empr_categ where id_categ_empr=' . $this->empr_categ;
            static::$logger->debug($q);
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $this->duree_adhesion = pmb_mysql_result($r, 0, 0);
            }
        }
        static::$logger->debug(' => Durée adhésion =' . $this->duree_adhesion);
    }

    public function setDefaultEmprLocation($empr_location = 0)
    {
        static::$logger->debug(__METHOD__);

        $empr_location = intval($empr_location);
        if ($empr_location) {
            $q = 'select idlocation from docs_location where idlocation=' . $empr_location;
            static::$logger->debug($q);

            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $this->default_empr_location = $empr_location;
                static::$logger->debug('Id localisation par défaut = ' . $empr_location);
                return;
            }
        }
        static::$error = true;
        static::$logger->error('Id localisation par défaut = ' . $empr_location . ' inexistant');
    }

    public function setEmprLocation($empr_location = 0)
    {
        static::$logger->debug(__METHOD__);

        $empr_location = intval($empr_location);
        if (! $empr_location) {
            $empr_location = $this->empr_location;
        }
        if (! $empr_location) {
            $empr_location = $this->default_empr_location;
        }

        $q = 'select idlocation from docs_location where idlocation=' . $empr_location;
        static::$logger->debug($q);

        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $this->empr_location = $empr_location;
        } else {
            $this->empr_location = $this->default_empr_location;
        }

        static::$logger->debug('=> Id localisation = ' . $this->empr_location);
    }

    public function setDefaultEmprStatut($empr_statut = 0)
    {
        static::$logger->debug(__METHOD__);

        $empr_statut = intval($empr_statut);
        if ($empr_statut) {
            $q = 'select idstatut from empr_statut where idstatut=' . $empr_statut;
            static::$logger->debug($q);

            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $this->default_empr_statut = $empr_statut;
                static::$logger->debug('Id statut par défaut = ' . $empr_statut);
                return;
            }
        }
        static::$error = true;
        static::$logger->error('Id statut par défaut = ' . $empr_statut . ' inexistant');
    }

    public function setEmprStatut($empr_statut = 0)
    {
        static::$logger->debug(__METHOD__);

        $empr_statut = intval($empr_statut);
        if (! $empr_statut) {
            $empr_statut = $this->empr_statut;
        }
        if (! $empr_statut) {
            $empr_statut = $this->default_empr_statut;
        }

        $q = 'select idstatut from empr_statut where idstatut=' . $empr_statut;
        static::$logger->debug($q);

        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $this->empr_statut = $empr_statut;
        } else {
            $this->empr_statut = $this->default_empr_statut;
        }

        static::$logger->debug(' => Id statut =' . $this->empr_statut);
    }

    public static function setAllEmprStatut($empr_statut = 0)
    {
        static::$logger->debug(__METHOD__);

        $empr_statut = intval($empr_statut);
        if (! $empr_statut) {
            static::$error = true;
            static::$logger->error('Id statut =' . $empr_statut . 'inexistant');
            return;
        }

        $q = 'select idstatut from empr_statut where idstatut=' . $empr_statut;
        static::$logger->debug($q);
        $r = pmb_mysql_query($q);
        if (! pmb_mysql_num_rows($r)) {
            static::$error = true;
            static::$logger->error('Id statut =' . $empr_statut . 'inexistant');
            return;
        }
        $q = "update empr set empr_statut={$empr_statut}";
        static::$logger->debug($q);
        pmb_mysql_query($q);
        if (pmb_mysql_errno()) {
            static::$error = true;
            static::$logger->error(pmb_mysql_error());
            return;
        }
        static::$logger->debug(' => Id statut =' . $empr_statut);
    }

    public function setDefaultEmprCodestat($empr_codestat = 0)
    {
        static::$logger->debug(__METHOD__);

        $empr_codestat = intval($empr_codestat);
        if ($empr_codestat) {
            $q = 'select idcode from empr_codestat where idcode=' . $empr_codestat;
            static::$logger->debug($q);

            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $this->default_empr_codestat = $empr_codestat;
                static::$logger->debug('Id code statistique par défaut = ' . $empr_codestat);
                return;
            }
        }
        static::$error = true;
        static::$logger->error('Id code statistique par défaut = ' . $empr_codestat . ' inexistant');
    }

    public function setEmprCodestat($empr_codestat = 0)
    {
        static::$logger->debug(__METHOD__);

        $empr_codestat = intval($empr_codestat);
        if (! $empr_codestat) {
            $empr_codestat = $this->empr_codestat;
        }
        if (! $empr_codestat) {
            $empr_codestat = $this->default_empr_codestat;
        }

        $q = 'select idcode from empr_codestat where idcode=' . $empr_codestat;
        static::$logger->debug($q);

        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $this->empr_codestat = $empr_codestat;
        } else {
            $this->empr_codestat = $this->default_empr_codestat;
        }

        static::$logger->debug(' => Id code statistique = ' . $this->empr_codestat);
    }

    public function setDefaultEmprCateg($empr_categ = 0)
    {
        static::$logger->debug(__METHOD__);

        $empr_categ = intval($empr_categ);
        if ($empr_categ) {
            $q = 'select id_categ_empr from empr_categ where id_categ_empr=' . $empr_categ;
            static::$logger->debug($q);

            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $this->default_empr_categ = $empr_categ;
                static::$logger->debug('Id catégorie par défaut = ' . $empr_categ);
                return;
            }
        }
        static::$error = true;
        static::$logger->error('Id catégorie par défaut = ' . $empr_categ . ' inexistant');
    }

    public function setEmprCateg($empr_categ = 0)
    {
        static::$logger->debug(__METHOD__);

        $empr_categ = intval($empr_categ);
        if (! $empr_categ) {
            $empr_categ = $this->empr_categ;
        }
        if (! $empr_categ) {
            $empr_categ = $this->default_empr_categ;
        }

        $q = 'select id_categ_empr from empr_categ where id_categ_empr=' . $empr_categ;
        static::$logger->debug($q);

        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $this->empr_categ = $empr_categ;
        } else {
            $this->empr_categ = $this->default_empr_categ;
        }

        static::$logger->debug('=> Id catégorie = ' . $this->empr_categ);
    }

    public function save(&$nb = 0)
    {
        static::$logger->debug(__METHOD__);

        if ($this->empr_cb && $this->empr_nom) {

            $this->verify();

            $q_empr = "insert into empr set ";
            $q_empr .= "empr_cb='" . addslashes($this->empr_cb) . "', ";
            $q_empr .= "empr_nom='" . addslashes($this->empr_nom) . "', ";
            $q_empr .= "empr_prenom='" . addslashes($this->empr_prenom) . "', ";
            $q_empr .= "empr_adr1='" . addslashes($this->empr_adr1) . "', ";
            $q_empr .= "empr_adr2='" . addslashes($this->empr_adr2) . "', ";
            $q_empr .= "empr_cp='" . addslashes($this->empr_cp) . "', ";
            $q_empr .= "empr_ville='" . addslashes($this->empr_ville) . "', ";
            $q_empr .= "empr_pays='" . addslashes($this->empr_pays) . "', ";
            $q_empr .= "empr_mail='" . addslashes($this->empr_mail) . "', ";
            $q_empr .= "empr_tel1='" . addslashes($this->empr_tel1) . "', ";
            $q_empr .= "empr_tel2='" . addslashes($this->empr_tel2) . "', ";
            $q_empr .= "empr_prof='" . addslashes($this->empr_prof) . "', ";
            $q_empr .= "empr_year='" . addslashes($this->empr_year) . "', ";
            $q_empr .= "empr_categ='" . $this->empr_categ . "',";
            $q_empr .= "empr_codestat='" . $this->empr_codestat . "',";
            $q_empr .= "empr_creation=now(), ";
            $q_empr .= "empr_modif=now(), ";
            $q_empr .= "empr_sexe='" . $this->empr_sexe . "', ";
            $q_empr .= "empr_login='" . addslashes($this->empr_login) . "', ";
            $q_empr .= "empr_password='" . addslashes($this->empr_password) . "', ";
            $q_empr .= "empr_date_adhesion=" . (($this->empr_date_adhesion) ? "'" . $this->empr_date_adhesion . "'" : "now()") . ", ";
            $q_empr .= "empr_date_expiration=" . (($this->empr_date_expiration) ? "'" . $this->empr_date_expiration . "'" : 'adddate(curdate(), interval ' . $this->duree_adhesion . ' day )') . ", ";
            $q_empr .= "empr_msg='" . addslashes($this->empr_msg) . "', ";
            $q_empr .= "empr_lang='" . $this->empr_lang . "', ";
            $q_empr .= "empr_ldap='" . $this->empr_ldap . "', ";
            $q_empr .= "type_abt='" . $this->type_abt . "', ";
            $q_empr .= "last_loan_date='" . $this->last_loan_date . "', ";
            $q_empr .= "empr_location='" . $this->empr_location . "', ";
            $q_empr .= "date_fin_blocage='" . $this->date_fin_blocage . "', ";
            $q_empr .= "total_loans='" . $this->total_loans . "', ";
            $q_empr .= "empr_statut='" . $this->empr_statut . "', ";
            $q_empr .= "cle_validation='" . $this->cle_validation . "', ";
            $q_empr .= "empr_sms='" . $this->empr_sms . "' ";

            static::$logger->debug($q_empr);

            $r = pmb_mysql_query($q_empr);
            if ($r) {

                $this->id_empr = pmb_mysql_insert_id();
                \emprunteur::update_digest($this->empr_login, $this->empr_password);
                \emprunteur::hash_password($this->empr_login, $this->empr_password);
                $this->is_new = true;
                $nb ++;
                static::$logger->info("Ajout Compte " . $this->empr_cb . " (" . $this->empr_prenom . (($this->empr_prenom) ? " " : "") . $this->empr_nom . ")");
            } else {

                static::$logger->error("Ajout Compte " . $this->empr_cb . " (" . $this->empr_prenom . (($this->empr_prenom) ? " " : "") . $this->empr_nom . ")");
            }
        } else {

            static::$logger->error(" =>  empr_cb=" . $this->empr_cb . " -- empr_nom=" . $this->empr_nom);
        }
    }

    public function update(&$nb = 0)
    {
        static::$logger->debug(__METHOD__);

        if ($this->id_empr &&
            ($this->dont_update['empr_cb'] || $this->empr_cb) &&
            ($this->dont_update['empr_nom'] || $this->empr_nom)) {

            $this->verify();

            $q_empr = "update empr set ";
            if (! $this->dont_update['empr_cb']) {
                $q_empr .= "empr_cb='" . addslashes($this->empr_cb) . "', ";
            }
            if (! $this->dont_update['empr_nom']) {
                $q_empr .= "empr_nom='" . addslashes($this->empr_nom) . "', ";
            }
            if (! $this->dont_update['empr_prenom']) {
                $q_empr .= "empr_prenom='" . addslashes($this->empr_prenom) . "', ";
            }
            if (! $this->dont_update['empr_adr1']) {
                $q_empr .= "empr_adr1='" . addslashes($this->empr_adr1) . "', ";
            }
            if (! $this->dont_update['empr_adr2']) {
                $q_empr .= "empr_adr2='" . addslashes($this->empr_adr2) . "', ";
            }
            if (! $this->dont_update['empr_cp']) {
                $q_empr .= "empr_cp='" . addslashes($this->empr_cp) . "', ";
            }
            if (! $this->dont_update['empr_ville']) {
                $q_empr .= "empr_ville='" . addslashes($this->empr_ville) . "', ";
            }
            if (! $this->dont_update['empr_pays']) {
                $q_empr .= "empr_pays='" . addslashes($this->empr_pays) . "', ";
            }
            if (! $this->dont_update['empr_mail']) {
                $q_empr .= "empr_mail='" . addslashes($this->empr_mail) . "', ";
            }
            if (! $this->dont_update['empr_tel1']) {
                $q_empr .= "empr_tel1='" . addslashes($this->empr_tel1) . "', ";
            }
            if (! $this->dont_update['empr_tel2']) {
                $q_empr .= "empr_tel2='" . addslashes($this->empr_tel2) . "', ";
            }
            if (! $this->dont_update['empr_prof']) {
                $q_empr .= "empr_prof='" . addslashes($this->empr_prof) . "', ";
            }
            if (! $this->dont_update['empr_year']) {
                $q_empr .= "empr_year='" . addslashes($this->empr_year) . "', ";
            }
            if (! $this->dont_update['empr_categ']) {
                $q_empr .= "empr_categ='" . $this->empr_categ . "', ";
            }
            if (! $this->dont_update['empr_codestat']) {
                $q_empr .= "empr_codestat='" . $this->empr_codestat . "', ";
            }
            $q_empr .= "empr_modif=now(), ";
            if (! $this->dont_update['empr_sexe']) {
                $q_empr .= "empr_sexe='" . $this->empr_sexe . "', ";
            }
            if (! $this->dont_update['empr_login']) {
                $q_empr .= "empr_login='" . addslashes($this->empr_login) . "', ";
            }
            if (! $this->dont_update['empr_password']) {
                $q_empr .= "empr_password='" . addslashes($this->empr_password) . "', ";
            }
            if (! $this->dont_update['empr_date_adhesion'] && $this->empr_date_adhesion) {
                $q_empr .= "empr_date_adhesion='" . addslashes($this->empr_date_adhesion) . "', ";
            }
            if (! $this->dont_update['empr_date_expiration']) {
                $q_empr .= "empr_date_expiration=" . (($this->empr_date_expiration) ? "'" . $this->empr_date_expiration . "'" : 'adddate(curdate(), interval ' . $this->duree_adhesion . ' day )') . ", ";
            }
            if (! $this->dont_update['empr_msg']) {
                $q_empr .= "empr_msg='" . addslashes($this->empr_msg) . "', ";
            }
            if (! $this->dont_update['empr_lang']) {
                $q_empr .= "empr_lang='" . $this->empr_lang . "', ";
            }
            if (! $this->dont_update['type_abt']) {
                $q_empr .= "type_abt='" . $this->type_abt . "', ";
            }
            if (! $this->dont_update['empr_location']) {
                $q_empr .= "empr_location='" . $this->empr_location . "', ";
            }
            if (! $this->dont_update['empr_statut']) {
                $q_empr .= "empr_statut='" . $this->empr_statut . "', ";
            }
            if (! $this->dont_update['empr_sms']) {
                $q_empr .= "empr_sms='" . $this->empr_sms . "', ";
            }
            $q_empr .= "empr_ldap='" . $this->empr_ldap . "' ";
            $q_empr .= "where id_empr='" . $this->id_empr . "' ";

            static::$logger->debug($q_empr);

            $r = pmb_mysql_query($q_empr);
            if ($r) {
                if (! $this->dont_update['empr_password']) {
                    \emprunteur::update_digest($this->empr_login, $this->empr_password);
                    \emprunteur::hash_password($this->empr_login, $this->empr_password);
                }
                $nb ++;
                static::$logger->info("Mise à jour Compte " . $this->empr_cb . " (" . $this->empr_prenom . (($this->empr_prenom) ? " " : "") . $this->empr_nom . ")");
            } else {
                static::$logger->error("Mise à jour Compte " . $this->empr_cb . " (" . $this->empr_prenom . (($this->empr_prenom) ? " " : "") . $this->empr_nom . ")");
            }
        } else {
            static::$logger->error(" => id_empr=" . $this->id_empr . " -- empr_cb=" . $this->empr_cb . " -- empr_nom=" . $this->empr_nom);
        }
    }

    public function delete(&$nb_deleted = 0, &$nb_to_delete = 0)
    {
        static::$logger->debug(__METHOD__);

        if (! $this->id_empr) {
            static::$logger->error(" => id_empr=0");
            return;
        }
        $deleted = \emprunteur::del_empr($this->id_empr);
        if ($deleted) {
            $nb_deleted ++;
            return true;
        }
        $nb_to_delete ++;
        return false;
    }

    public function verify()
    {
        static::$logger->debug(__METHOD__);

        if (! $this->empr_categ) {
            $this->empr_categ = $this->default_empr_categ;
        }
        if (! $this->empr_codestat) {
            $this->empr_codestat = $this->default_empr_codestat;
        }
        if (! $this->empr_statut) {
            $this->empr_statut = $this->default_empr_statut;
        }
        if (! $this->empr_location) {
            $this->empr_location = $this->default_empr_location;
        }
    }

    public function addToCaddie($caddie_name = '', $is_new = 0)
    {
        static::$logger->debug(__METHOD__);

        $caddie_name = trim($caddie_name);

        $id_caddie = 0;

        if ($is_new != 1) {
            $is_new = 0;
        }

        if ($caddie_name && $this->id_empr && (! $is_new || ($is_new && $this->is_new))) {
            $q = "select idemprcaddie from empr_caddie where name='" . addslashes($caddie_name) . "' ";
            static::$logger->debug($q);
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $id_caddie = pmb_mysql_result($r, 0, 0);
            } else {
                $q1 = "insert into empr_caddie (name,autorisations) values ('" . addslashes($caddie_name) . "',' 1 ') ";
                static::$logger->debug($q1);
                pmb_mysql_query($q1);
                $id_caddie = pmb_mysql_insert_id();
            }
            if ($id_caddie) {
                $q2 = "insert ignore into empr_caddie_content (empr_caddie_id,object_id,flag) values ('" . $id_caddie . "', '" . $this->id_empr . "',NULL)";
                static::$logger->debug($q2);
                pmb_mysql_query($q2);
            }
        }
    }

    public function razCaddie($caddie_name = '')
    {
        static::$logger->debug(__METHOD__);

        $caddie_name = trim($caddie_name);
        $id_caddie = 0;

        if ($caddie_name) {
            $q = "select idemprcaddie from empr_caddie where name='" . addslashes($caddie_name) . "' ";
            static::$logger->debug($q);
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $id_caddie = pmb_mysql_result($r, 0, 0);
            }
            if ($id_caddie) {
                $q2 = "delete from empr_caddie_content where empr_caddie_id='" . $id_caddie . "'";
                static::$logger->debug($q2);
                pmb_mysql_query($q2);
            }
        }
    }

    function verifyCp($cp, $type = '', $datatype = '')
    {
        static::$logger->debug(__METHOD__);

        $cp = trim($cp);
        $type = trim($type);
        $datatype = trim($datatype);
        $idcp = 0;
        if (array_key_exists($cp, $this->cps)) {
            return $this->cps[$cp];
        }

        $q_idcp = 'select idchamp from empr_custom where name="' . addslashes($cp) . '" ';
        if ($type && $datatype) {
            $q_idcp .= 'and type="' . addslashes($type) . '" and datatype="' . addslashes($datatype) . '" ';
        }
        static::$logger->debug($q_idcp);
        $r_idcp = pmb_mysql_query($q_idcp);

        if (pmb_mysql_num_rows($r_idcp) != 1) {
            static::$logger->error('Champ perso ' . $cp . ' inexistant');
            return $idcp;
        }

        $idcp = pmb_mysql_result($r_idcp, 0, 0);
        $this->cps[$cp] = $idcp;
        return $idcp;
    }

    public function delCp($cp = '')
    {
        static::$logger->debug(__METHOD__);

        $cp = trim($cp);

        $idcp = $this->verifyCp($cp);
        if (! $idcp) {
            return;
        }

        if (! $this->id_empr) {
            static::$logger->error('Id lecteur non fourni');
            return;
        }

        $q_del = "delete from empr_custom_values where empr_custom_origine='" . $this->id_empr . "' and empr_custom_champ='" . $this->cps[$cp] . "' ";
        static::$logger->debug($q_del);
        pmb_mysql_query($q_del);
    }

    public function setCpTextSmalltext($cp = '', $val = '')
    {
        static::$logger->debug(__METHOD__);

        $cp = trim($cp);
        $val = trim($val);

        $idcp = $this->verifyCp($cp, 'text', 'small_text');
        if (! $idcp) {
            return;
        }

        if (! $this->id_empr) {
            static::$logger->error('Id lecteur non fourni');
            return;
        }

        $q_val = "insert into empr_custom_values set empr_custom_origine='" . $this->id_empr . "', empr_custom_champ='" . $this->cps[$cp] . "', empr_custom_small_text='" . addslashes($val) . "' ";
        static::$logger->debug($q_val);
        pmb_mysql_query($q_val);
    }

    public function setCpTextText($cp = '', $val = '')
    {
        static::$logger->debug(__METHOD__);

        $cp = trim($cp);
        $val = trim($val);

        $idcp = $this->verifyCp($cp, 'text', 'text');
        if (! $idcp) {
            return;
        }

        if (! $this->id_empr) {
            static::$logger->error('Id lecteur non fourni');
            return;
        }

        $q_val = "insert into empr_custom_values set empr_custom_origine='" . $this->id_empr . "', empr_custom_champ='" . $this->cps[$cp] . "', empr_custom_text='" . addslashes($val) . "' ";
        static::$logger->debug($q_val);
        pmb_mysql_query($q_val);
    }

    public function setCpListInteger($cp = '', $label = '')
    {
        static::$logger->debug(__METHOD__);

        $cp = trim($cp);
        $label = trim($label);

        $idcp = $this->verifyCp($cp, 'list', 'integer');
        if (! $idcp) {
            return;
        }

        if (! $this->id_empr) {
            static::$logger->error('Id lecteur non fourni');
            return;
        }

        if ($label) {

            $q_val = "select empr_custom_list_value from empr_custom_lists where empr_custom_champ='" . $this->cps[$cp] . "' and empr_custom_list_lib='" . addslashes($label) . "' ";
            static::$logger->debug($q_val);
            $r_val = pmb_mysql_query($q_val);
            if (pmb_mysql_num_rows($r_val)) {
                $val = pmb_mysql_result($r_val, 0, 0);
            } else {
                $q_val1 = "select ifnull(max(empr_custom_list_value*1)+1,1) from empr_custom_lists where empr_custom_champ='" . $this->cps[$cp] . "' ";
                static::$logger->debug($q_val1);
                $r_val1 = pmb_mysql_query($q_val1);
                $val = pmb_mysql_result($r_val1, 0, 0);

                $q_val2 = "insert into empr_custom_lists set empr_custom_champ='" . $this->cps[$cp] . "', empr_custom_list_value='" . $val . "', empr_custom_list_lib='" . addslashes($label) . "' ";
                static::$logger->debug($q_val2);
                pmb_mysql_query($q_val2);
            }
            $q_val3 = "insert into empr_custom_values set empr_custom_origine='" . $this->id_empr . "', empr_custom_champ='" . $this->cps[$cp] . "', empr_custom_integer='" . $val . "' ";
            static::$logger->debug($q_val3);
            pmb_mysql_query($q_val3);
        }
    }

    public function setCpListSmalltext($cp = '', $value = '', $label = '')
    {
        static::$logger->debug(__METHOD__);

        $cp = trim($cp);
        $value = trim($value);
        $label = trim($label);

        $idcp = $this->verifyCp($cp, 'list', 'small_text');
        if (! $idcp) {
            return;
        }

        if (! $this->id_empr) {
            static::$logger->error('Id lecteur non fourni');
            return;
        }

        if ($label) {

            $q_val = "select empr_custom_list_value from empr_custom_lists where empr_custom_champ='" . $this->cps[$cp] . "' and empr_custom_list_value='" . addslashes($value) . "' ";
            static::$logger->debug($q_val);
            $r_val = pmb_mysql_query($q_val);
            if (! pmb_mysql_num_rows($r_val)) {
                $q_val2 = "insert into empr_custom_lists set empr_custom_champ='" . $this->cps[$cp] . "', empr_custom_list_value='" . addslashes($value) . "', empr_custom_list_lib='" . addslashes($label) . "' ";
                static::$logger->debug($q_val2);
                pmb_mysql_query($q_val2);
            } else {
                $q_val2 = "update empr_custom_lists set empr_custom_list_lib='" . addslashes($label) . "' where empr_custom_champ='" . $this->cps[$cp] . "' and empr_custom_list_value='" . addslashes($value) . "' ";
                static::$logger->debug($q_val2);
                pmb_mysql_query($q_val2);
            }
            $q_val3 = "insert into empr_custom_values set empr_custom_origine='" . $this->id_empr . "', empr_custom_champ='" . $this->cps[$cp] . "', empr_custom_small_text='" . addslashes($value) . "' ";
            static::$logger->debug($q_val3);
            pmb_mysql_query($q_val3);
        }
    }

    public function cleanCpListSmalltext($cp = '')
    {
        static::$logger->debug(__METHOD__);

        $cp = trim($cp);

        $idcp = $this->verifyCp($cp, 'list', 'small_text');
        if (! $idcp) {
            return;
        }

        $q_id = "select idchamp from empr_custom where name='" . $cp . "' ";
        static::$logger->debug($q_id);
        $r_id = pmb_mysql_query($q_id);
        if (pmb_mysql_num_rows($r_id)) {
            $idchamp = pmb_mysql_result($r_id, 0, 0);
            $q_clean = "delete from empr_custom_lists where empr_custom_champ='" . $idchamp . "' and empr_custom_list_value not in (select distinct empr_custom_small_text from empr_custom_values where empr_custom_champ='" . $idchamp . "') ";
            static::$logger->debug($q_clean);
            pmb_mysql_query($q_clean);
        }
    }

    public function notInLdap()
    {
        static::$logger->debug('not_in_ldap()');

        $ret = 0;

        $q = 'select count(*) from empr where empr_ldap=0';
        static::$logger->debug($q);
        $r = pmb_mysql_query($q);
        $ret = pmb_mysql_result($r, 0, 0);

        static::$logger->debug('=>' . $ret);
        return $ret;
    }

    public function notInLdapToCaddie($caddie_name = '')
    {
        static::$logger->debug(__METHOD__);

        if (! $caddie_name) {
            $caddie_name = 'Lecteurs hors SYNCHRO';
        }

        $id_caddie = 0;
        $q = "select idemprcaddie from empr_caddie where name='" . addslashes($caddie_name) . "' ";
        static::$logger->debug($q);

        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $id_caddie = pmb_mysql_result($r, 0, 0);
        } else {
            $q1 = "insert into empr_caddie (name,autorisations) values ('" . addslashes($caddie_name) . "',' 1 ') ";
            static::$logger->debug($q1);
            pmb_mysql_query($q1);
            $id_caddie = pmb_mysql_insert_id();
        }
        if ($id_caddie) {
            $q3 = "insert ignore into empr_caddie_content (select '" . $id_caddie . "', id_empr,NULL from empr where empr_ldap=0 )";
            static::$logger->debug($q3);
            pmb_mysql_query($q3);
        }
    }

    public function inLdapToCaddie($caddie_name = '')
    {
        static::$logger->debug(__METHOD__);

        if (! $caddie_name) {
            $caddie_name = 'Lecteurs hors SYNCHRO';
        }

        $id_caddie = 0;
        $q = "select idemprcaddie from empr_caddie where name='" . addslashes($caddie_name) . "' ";
        static::$logger->debug($q);

        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $id_caddie = pmb_mysql_result($r, 0, 0);
        } else {
            $q1 = "insert into empr_caddie (name,autorisations) values ('" . addslashes($caddie_name) . "',' 1 ') ";
            static::$logger->debug($q1);
            pmb_mysql_query($q1);
            $id_caddie = pmb_mysql_insert_id();
        }
        if ($id_caddie) {
            $q3 = "insert ignore into empr_caddie_content (select '" . $id_caddie . "', id_empr,NULL from empr where empr_ldap=1 )";
            static::$logger->debug($q3);
            pmb_mysql_query($q3);
        }
    }

    public function razLdapFlag()
    {
        static::$logger->debug(__METHOD__);

        $q = 'update empr set empr_ldap=0 where empr_ldap=1';
        static::$logger->debug($q);
        pmb_mysql_query($q);
        if (pmb_mysql_errno()) {
            static::$logger->error(pmb_mysql_error());
        }
    }


    /**
     * Recherche lecteur à partir d'un code-barres
     * retourne identifiant lecteur si trouvé, 0 sinon
     * peuple $this->search_result avec tableau [id_empr, empr_login]
     *
     * @param string|array $empr_cb
     *
     * @return int
     */
    public function searchCb($empr_cb = '')
    {
        static::$logger->debug(__METHOD__." >> empr_cb = ".print_r($empr_cb, true));

        if( is_array($empr_cb) ) {
            $empr_cb = array_shift($empr_cb);
        }
        $empr_cb = trim($empr_cb);

        $ret = 0;
        $this->search_result = [];

        if ($empr_cb) {
            $q = "select id_empr, empr_login from empr where empr_cb='" . addslashes($empr_cb) . "'";
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $this->search_result = pmb_mysql_fetch_assoc($r);
                $ret = $this->search_result['id_empr'];
            }
        }
        static::$logger->debug(__METHOD__." >> $ret");
        return $ret;
    }


    /**
     * Recherche lecteur à partir d'un login
     * retourne identifiant lecteur si unique, 0 sinon
     * peuple $this->search_result avec tableau [id_empr, empr_login]
     *
     * @param string|array $empr_login
     *
     * @return int
     */
    public function searchLogin($empr_login = '')
    {
        static::$logger->debug(__METHOD__." >> empr_login = ".print_r($empr_login, true));

        if( is_array($empr_login) ) {
            $empr_login = array_shift($empr_login);
        }
        $empr_login = trim($empr_login);

        $ret = 0;
        $this->search_result = [];

        if ($empr_login) {
            $q = "select id_empr, empr_login from empr where empr_login='" . addslashes($empr_login) . "'";
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r) == 1) {
                $this->search_result = pmb_mysql_fetch_assoc($r);
                $ret = $this->search_result['id_empr'];
            }
        }
        static::$logger->debug(__METHOD__." >> id_empr = $ret");
        return $ret;
    }


    /**
     * Recherche lecteur à partir d'un nom et d'un prénom
     * retourne identifiant lecteur si unique, 0 sinon
     * peuple $this->search_result avec tableau [id_empr, empr_login]
     *
     * @param string|array $empr_login
     *
     * @return int
     */
    public function searchName($empr_nom = '', $empr_prenom = '')
    {
        static::$logger->debug(__METHOD__." >> empr_nom = ".print_r($empr_nom, true));
        static::$logger->debug(__METHOD__." >> empr_prenom = ".print_r($empr_prenom, true));

        if( is_array($empr_nom) ) {
            $empr_nom = array_shift($empr_nom);
        }
        $empr_nom = trim($empr_nom);
        if( is_array($empr_prenom) ) {
            $empr_prenom = array_shift($empr_prenom);
        }
        $empr_prenom = trim($empr_prenom);

        $ret = 0;
        $this->search_result = [];

        if ($empr_nom) {
            $q = "select id_empr, empr_login from empr where empr_nom='" . addslashes($empr_nom) . "' and empr_prenom='" . addslashes($empr_prenom) . "' ";
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r) == 1) {
                $this->search_result = pmb_mysql_fetch_assoc($r);
                $ret = $this->search_result['id_empr'];
            }
        }
        static::$logger->debug(__METHOD__." >> id_empr = $ret");
        return $ret;
    }


    /**
     * Recherche lecteur à partir d'un mail
     * retourne identifiant lecteur si unique, 0 sinon
     * peuple $this->search_result avec tableau [id_empr, empr_login]
     *
     * @param string|array $empr_login
     *
     * @return int
     */
    public function searchMail($empr_mail = '')
    {
        static::$logger->debug(__METHOD__." >> empr_mail = ".print_r($empr_mail, true));

        if( is_array($empr_mail) ) {
            $empr_mail = array_shift($empr_mail);
        }
        $empr_mail = trim($empr_mail);

        $ret = 0;
        $this->search_result = [];

        if ($empr_mail) {
            $q = "select id_empr, empr_login from empr where empr_mail='" . addslashes($empr_mail) . "' ";
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r) == 1) {
                $this->search_result = pmb_mysql_fetch_assoc($r);
                $ret = $this->search_result['id_empr'];
            }
        }
        static::$logger->debug(__METHOD__." >> id_empr = $ret");
        return $ret;
    }


    public function searchLoginWithMail($empr_mail = '')
    {
        static::$logger->debug(__METHOD__." >> mail = $empr_mail");

        $ret = 0;
        $empr_mail = trim($empr_mail);
        $this->search_result = [];

        if ($empr_mail) {
            $q = "select id_empr, empr_login from empr where empr_mail='" . addslashes($empr_mail) . "' ";
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r) == 1) {
                $this->search_result = pmb_mysql_fetch_assoc($r);
                $ret = $this->search_result['empr_login'];
            }
        }

        static::$logger->debug(__METHOD__." >> empr_login = $ret");
        return $ret;
    }

    public function searchCpTextSmalltext($cp = '', $val = '')
    {
        static::$logger->debug(__METHOD__);

        $cp = trim($cp);
        $val = trim($val);
        $ret = 0;

        $q_idcp = 'select idchamp from empr_custom where name="' . addslashes($cp) . '" and type="text" and datatype="small_text" ';
        static::$logger->debug($q_idcp);
        $r_idcp = pmb_mysql_query($q_idcp);

        if (pmb_mysql_num_rows($r_idcp) != 1) {
            static::$logger->error('Champ perso ' . $cp . ' inexistant');
            return $ret;
        }

        $idcp = pmb_mysql_result($r_idcp, 0, 0);
        $q_s = 'select distinct(empr_custom_origine) as id_empr, empr_login from empr_custom_values ';
        $q_s .= 'join empr on empr_custom_origine=id_empr where empr_custom_champ=' . $idcp . ' and empr_custom_small_text="' . addslashes($val) . '" ';
        static::$logger->debug($q_s);
        $r_s = pmb_mysql_query($q_s);
        if (pmb_mysql_num_rows($r_s) == 1) {
            $ret = pmb_mysql_result($r_s, 0, 0);
            $this->search_result = pmb_mysql_fetch_assoc($r_s);
            $ret = $this->search_result['id_empr'];
        }
        static::$logger->debug('id_empr=' . $ret);
        return $ret;
    }

    public function setCpDateBoxDate($cp = '', $val = '')
    {
        static::$logger->debug(__METHOD__);

        $cp = trim($cp);
        $val = trim($val);

        $date = \DateTime::createFromFormat("Y-m-d", $val);
        if(!$date) {
            return;
        }

        $idcp = $this->verifyCp($cp, 'date_box', 'date');
        if (!$idcp) {
            return;
        }

        if (!$this->id_empr) {
            static::$logger->error('Id lecteur non fourni');
            return;
        }

        $q_val = "insert into empr_custom_values set empr_custom_origine='" . $this->id_empr . "', empr_custom_champ='" . $this->cps[$cp] . "', empr_custom_date='" . addslashes($val) . "' ";
        static::$logger->debug($q_val);
        pmb_mysql_query($q_val);
    }


    public function deleteOldGroups()
    {
        static::$logger->debug(__METHOD__);

        $q = 'delete from groupe where id_groupe not in (select groupe_id from empr_groupe)';
        static::$logger->debug($q);
        pmb_mysql_query($q);
    }

    public function removeFromGroups()
    {
        static::$logger->debug(__METHOD__);

        if ($this->id_empr) {
            $q = 'delete from empr_groupe where empr_id=' . $this->id_empr;
            static::$logger->debug($q);
            pmb_mysql_query($q);
        }
    }

    public function addToGroupId($id_group = 0)
    {
        static::$logger->debug(__METHOD__);

        $id_group = intval($id_group);
        $qi = '';

        if ($this->id_empr && $id_group) {
            $qs = 'select id_groupe from groupe where id_groupe=' . $id_group;
            static::$logger->debug($qs);
            $rs = pmb_mysql_query($qs);
            if (pmb_mysql_num_rows($rs)) {
                $qi = 'insert ignore into empr_groupe (empr_id,groupe_id) values(' . $this->id_empr . ',' . $id_group . ')';
                static::$logger->debug($qi);
                pmb_mysql_query($qi);
            }
        }
    }

    public function addToGroupName($group_name = '', $create = 1)
    {
        static::$logger->debug(__METHOD__);

        $tab_group_name = array();
        if (is_string($group_name) && $group_name) {
            $tab_group_name = [
                $group_name
            ];
        }
        if (is_array($group_name)) {
            $tab_group_name = $group_name;
        }
        if (! count($tab_group_name)) {
            return;
        }

        foreach ($tab_group_name as $group_name) {
            $group_name = trim($group_name);
            $id_group = 0;

            if ($this->id_empr && $group_name) {
                $qs = 'select id_groupe from groupe where libelle_groupe="' . addslashes($group_name) . '" limit 1 ';
                static::$logger->debug($qs);
                $rs = pmb_mysql_query($qs);
                if (pmb_mysql_num_rows($rs)) {
                    $id_group = pmb_mysql_result($rs, 0, 0);
                }
                if ($create && pmb_mysql_num_rows($rs) == 0) {
                    $qc = 'insert into groupe (libelle_groupe) values ("' . addslashes($group_name) . '")';
                    static::$logger->debug($qc);
                    pmb_mysql_query($qc);
                    if (! pmb_mysql_error()) {
                        $id_group = pmb_mysql_insert_id();
                    }
                }
                if ($id_group) {
                    $qi = 'insert ignore into empr_groupe (empr_id,groupe_id) values(' . $this->id_empr . ',' . $id_group . ')';
                    static::$logger->debug($qi);
                    pmb_mysql_query($qi);
                }
            }
        }
    }

    public function genEmprBarcode()
    {
        global $pmb_num_carte_auto;
        global $base_path;

        static::$logger->debug(__METHOD__);

        $pmb_num_carte_auto_array = explode(',', $pmb_num_carte_auto);

        if ($pmb_num_carte_auto_array[0] == '1') {

            $q1 = 'delete from empr_temp where sess not in (select SESSID from sessions)';
            static::$logger->debug($q1);
            pmb_mysql_query($q1);
            $q2 = "select max(empr_cb+1) as max_cb FROM (select empr_cb from empr UNION select cb FROM empr_temp WHERE sess <>'" . SESSid . "') tmp";
            static::$logger->debug($q2);
            $r2 = pmb_mysql_query($q2);
            $cb_initial = pmb_mysql_fetch_object($r2);
            $cb_a_creer = (string) $cb_initial->max_cb;
            $q3 = "INSERT INTO empr_temp (cb ,sess) VALUES ('" . addslashes($cb_a_creer) . "','" . SESSid . "')";
            static::$logger->debug($q3);
            pmb_mysql_query($q3);
        } elseif ($pmb_num_carte_auto_array[0] == '2') {

            $q1 = 'delete from empr_temp where sess not in (select SESSID from sessions)';
            static::$logger->debug($q1);
            pmb_mysql_query($q1);

            $long_prefixe = $pmb_num_carte_auto_array[1];
            $nb_chiffres = $pmb_num_carte_auto_array[2];
            $prefix = $pmb_num_carte_auto_array[3];

            $q2 = "SELECT CAST(SUBSTRING(empr_cb," . ($long_prefixe + 1) . ") AS UNSIGNED) AS max_cb, SUBSTRING(empr_cb,1," . ($long_prefixe * 1) . ") AS prefixdb FROM (select empr_cb from empr UNION select cb FROM empr_temp WHERE sess <>'" . SESSid . "') tmp ORDER BY max_cb DESC limit 0,1"; // modif f cerovetti pour sortir dernier code barre tri par ASCII
            static::$logger->debug($q2);
            $r2 = pmb_mysql_query($q2);
            $cb_initial = pmb_mysql_fetch_object($r2);
            $cb_a_creer = ($cb_initial->max_cb * 1) + 1;
            if (! $nb_chiffres) {
                $nb_chiffres = strlen($cb_a_creer);
            }
            if (! $prefix) {
                $prefix = $cb_initial->prefixdb;
            }

            $cb_a_creer = $prefix . substr((string) str_pad($cb_a_creer, $nb_chiffres, "0", STR_PAD_LEFT), - $nb_chiffres);
            $q3 = "INSERT INTO empr_temp (cb ,sess) VALUES ('" . addslashes($cb_a_creer) . "','" . SESSid . "')";
            static::$logger->debug($q3);
            pmb_mysql_query($q3);
        } elseif ($pmb_num_carte_auto_array[0] == '3') {

            $num_carte_auto_filename = $base_path . '/circ/empr/' . trim($pmb_num_carte_auto_array[1]) . '.inc.php';
            $num_carte_auto_fctname = trim($pmb_num_carte_auto_array[1]);
            if (file_exists($num_carte_auto_filename)) {
                require_once ($num_carte_auto_filename);
                if (function_exists($num_carte_auto_fctname)) {
                    $cb_a_creer = $num_carte_auto_fctname();
                }
            }
        }

        static::$logger->debug('barcode = ' . $cb_a_creer);

        return $cb_a_creer;
    }

    public function createEmprCodestat($name = '')
    {
        static::$logger->debug(__METHOD__);

        $ret = 0;

        $name = trim($name);

        if ($name) {
            $qs = 'select idcode from empr_codestat where libelle="' . addslashes($name) . '" ';
            static::$logger->debug($qs);
            $rs = pmb_mysql_query($qs);
            if (pmb_mysql_num_rows($rs)) {
                $ret = pmb_mysql_result($rs, 0, 0);
            } else {
                $qc = 'insert into empr_codestat (libelle) values ("' . addslashes($name) . '") ';
                static::$logger->debug($qc);
                $rc = pmb_mysql_query($qc);
                if ($rc) {
                    $ret = pmb_mysql_insert_id();
                }
            }
        }
        static::$logger->debug('idcodestat = ' . $ret);

        return $ret;
    }

    public function setDontUpdate($dont_update = array())
    {
        static::$logger->debug(__METHOD__);

        if (is_array($dont_update) && count($dont_update)) {
            $this->dont_update = array();
            foreach ($dont_update as $v) {
                $this->dont_update[trim($v)] = 1;
            }
        }
    }


    public function addJpegImage($directory = '', $raw_image = '')
    {
        static::$logger->debug(__METHOD__);

        $directory = trim($directory);
        if (! $raw_image) {
            static::$logger->error('Image vide.');
            return;
        }
        if (! is_dir($directory) || ! is_writeable($directory)) {
            static::$logger->error('Répertoire de stockage inexistant ou inaccessible.');
            return;
        }

        $directory = SynchroCommons::addTrailingSlash($directory);

        $image_filename = $directory . $this->empr_cb . '.jpg';
        @file_put_contents($image_filename, $raw_image);
    }
}
