<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CreateEmprDefault.php,v 1.3 2023/07/13 13:14:57 dbellamy Exp $

namespace Pmb\Authentication\Helpers\Empr;

use Pmb\Authentication\Interfaces\CreateEmprInterface;
use Pmb\Authentication\Common\AbstractLogger;
use Pmb\Common\Helper\GlobalContext;
use Pmb\Common\Models\EmprModel;
use Pmb\Common\Models\EmprCategModel;
use Pmb\Common\Orm\EmprCategOrm;

class CreateEmprDefault extends AbstractLogger implements CreateEmprInterface
{
    const ARGS = [
        'id_empr',
        'empr_cb',
        'empr_nom',
        'empr_prenom',
        'empr_login',
        'empr_mail',
    ];

    public $id_empr = 0;
    public $empr_login = '';

    /**
     *
     * {@inheritdoc}
     * @see CreateEmprInterface::onAuthenticationCreate()
     */
    public function onAuthenticationCreate($caller = null, array $args = [])
    {
        static::$logger->debug(__METHOD__);
        static::$logger->debug('$args = '.print_r($args, true));

        $ret = 0;

        if ( empty($args['empr_cb'] ) || !is_string($args['empr_cb']) ) {
            $args['empr_cb'] = '';
        }
        $args['empr_cb'] = trim($args['empr_cb']);

        if ( empty($args['empr_nom']) || !is_string($args['empr_nom']) ) {
            return $ret;
        }
        $args['empr_nom'] = trim($args['empr_nom']);
        if(empty($args['empr_nom'])) {
            return $ret;
        }

        if ( empty($args['empr_login']) || !is_string($args['empr_login']) ) {
            return $ret;
        }
        $args['empr_login'] = trim($args['empr_login']);
        if(empty($args['empr_login']) ) {
            return $ret;
        }

        //Login deja existant
        if( ! \emprunteur::check_login_uniqueness($args['empr_login'])) {
            return $ret;
        }

        if ( empty($args['empr_prenom'] ) || !is_string($args['empr_prenom']) ) {
            $args['empr_prenom'] = '';
        }
        $args['empr_prenom'] = trim($args['empr_prenom']);

        if ( empty($args['empr_mail'] ) || !is_string($args['empr_mail']) ) {
            $args['empr_mail'] = '';
        }
        $args['empr_mail'] = trim($args['empr_mail']);

        $empr_location = GlobalContext::get('opac_websubscribe_empr_location');
        if(!$empr_location) {
            return $ret;
        }
        $empr_categ = GlobalContext::get('opac_websubscribe_empr_categ');
        if(!$empr_categ) {
            return $ret;
        }
        $empr_codestat = GlobalContext::get('opac_websubscribe_empr_stat');
        if(!$empr_codestat) {
            return $ret;
        }
        $empr_statut = explode(',', GlobalContext::get('opac_websubscribe_empr_status'))[0];
        if(!$empr_statut) {
            return $ret;
        }
        $default_lang = GlobalContext::get('opac_default_lang');

        $lang = '';
        if( !empty($_COOKIE['PhpMyBibli-LANG']) && preg_match("#/^([a-z]{2}_[A-Z]{2}|[a-z]{2})$/g#", )) {
            $lang = $_COOKIE['PhpMyBibli-LANG'];
        }
        if( !$lang && !empty($default_lang) ) {
            $lang = $default_lang;
        }
        if( !preg_match("#/^([a-z]{2}_[A-Z]{2}|[a-z]{2})$/g#", $lang) ) {
            $lang = 'fr_FR';
        }

        $empr_date_adhesion = new \DateTime("now");
        try {
            $empr_categ_data = EmprCategOrm::findById($empr_categ);
        } catch (\Exception $e) {
            return $ret;
        }
        $empr_categ_duree_adhesion = $empr_categ_data->duree_adhesion;
        $empr_date_expiration = clone $empr_date_adhesion;
        $empr_date_expiration->add(new \DateInterval("P".$empr_categ_duree_adhesion."D"));

        $empr_model = new EmprModel();
        $empr_model->emprCb = empty($args['empr_cb']) ? ('wwwtmp'.rand(0,100000)) : $args['empr_cb'] ;
        $empr_model->emprNom = $args['empr_nom'];
        $empr_model->emprPrenom = $args['empr_prenom'];
        $empr_model->emprLogin = $args['empr_login'];
        $empr_model->emprMail = $args['empr_mail'];
        $empr_model->emprLocation = $empr_location;
        $empr_model->emprCateg = $empr_categ;
        $empr_model->emprCodestat = $empr_codestat;
        $empr_model->emprStatut = $empr_statut;
        $empr_model->emprLang = $lang;
        $empr_model->emprDateAdhesion = $empr_date_adhesion->format("Y-m-d H:i:s");
        $empr_model->emprDateExpiration = $empr_date_expiration->format("Y-m-d H:i:s");

        $this->id_empr = $empr_model->save();
        if($this->id_empr) {
            $this->empr_login = $empr_model->emprLogin;
        }
        return $this->id_empr;
    }

    /**
     *
     * {@inheritdoc}
     * @see CreateEmprInterface::onAuthenticationUpdate()
     */
    public function onAuthenticationUpdate($caller = null, array $args = [])
    {
        $ret = 0;
        return $ret;
    }

    public function getArgs() {
        return static::ARGS;
    }
}

