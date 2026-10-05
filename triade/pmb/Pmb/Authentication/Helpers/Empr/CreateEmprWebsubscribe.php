<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CreateEmprWebsubscribe.php,v 1.2 2023/07/13 13:14:58 dbellamy Exp $

namespace Pmb\Authentication\Helpers\Empr;

use Pmb\Authentication\Interfaces\CreateEmprInterface;
use Pmb\Authentication\Common\AbstractLogger;
use Pmb\Common\Helper\GlobalContext;
use Pmb\Common\Models\EmprModel;
use Pmb\Common\Models\EmprCategModel;
use Pmb\Common\Orm\EmprCategOrm;

class CreateEmprWebsubscribe extends AbstractLogger implements CreateEmprInterface
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
        global $opac_url_base;
        static::$logger->debug(__METHOD__);
        static::$logger->debug('$args = '.print_r($args, true));

        $ret = 0;

        // Générer URL de callback
        if( !empty($args['empr_mail']) ) {
            $tmp_mail = $args['empr_mail'];
            unset ($args['empr_mail']);
            $args['empr_mail'][0] = $tmp_mail;
        }
        $ext_auth_args = base64_encode(serialize($args));
        $callback_url = $opac_url_base.'/subscribe.php?ext_auth_args='.$ext_auth_args;
        // Definition contexte
        $caller->defineContext($callback_url);

        // Deconnexion
        if( method_exists($caller, 'runEmprExternalLogout') ) {
            $caller->runEmprExternalLogout();
        }
        return $ret;
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

