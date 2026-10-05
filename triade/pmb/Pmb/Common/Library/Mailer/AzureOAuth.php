<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AzureOAuth.php,v 1.3 2023/10/13 12:40:41 dbellamy Exp $

namespace Pmb\Common\Library\Mailer;

use League\OAuth2\Client\Token\AccessToken;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use \Pmb\Common\Helper\DateHelper;

class AzureOAuth extends OAuth
{

    /**
     * Duree de validite refresh token Azure (en jours)
     */
    const REFRESH_TOKEN_DURATION = 90;

    protected $options = [];

    protected $context = '';


    public function __construct($provider, $options)
    {
        $this->provider = $provider;
        $this->oauthUserEmail = $options['user'];
        $this->oauthClientSecret = $options['xoauth2_secret_value'];
        $this->oauthClientId = $options['xoauth2_client_id'];
        $this->oauthRefreshToken = $options['xoauth2_refresh_token'];

        $this->options = $options;
        $this->options['xoauth2_refresh_token_validity'] = $options['xoauth2_refresh_token_validity'] ?? '';
        $this->context = (defined('GESTION')) ? 'pmb' : 'opac';
        $this->options['id'] = $options['id'] ?? $this->context;
    }

    /**
     * Mise a jour du refresh token dans le parametre PMB
     *
     * @return void
     */
    protected function updateRefreshToken()
    {
        $dt_now = new \DateTime('now');

        try {
            $xoauth2_refresh_token_validity = pmb_preg_replace("[^0-9]", '', $this->options['xoauth2_refresh_token_validity']);
            $dt_token = new \DateTime($xoauth2_refresh_token_validity);
            $diff = ceil(DateHelper::getDiffInSeconds($dt_now, $dt_token) / 86400);
            if($diff > 10 ) {
                return;
            }
        } catch (\Exception $e) {
        }
        $dt_token->add(new \DateInterval('P'.AzureOAuth::REFRESH_TOKEN_DURATION.'D'));

        $this->options['xoauth2_refresh_token'] = $this->oauthToken->getRefreshToken();
        $this->options['xoauth2_refresh_token_validity'] = $dt_token->format('Y-m-d');
        $params = '';
        $origin = $this->options['id'];
        unset($this->options['id']);

        foreach($this->options as $k=>$v) {
            $params.= $k."=".$v.";".PHP_EOL;
        }
        switch (true) {
            case ( 'opac'  == $origin) :
                \parameter::update('opac', 'mail_methode', $params);
                break;
            case ( 'pmb' ==$origin) :
                \parameter::update('pmb', 'mail_methode', $params);
                break;
            default :
                $mail_configuration = new \mail_configuration($origin);
                $domain_configuration = $mail_configuration->get_domain();

                if($domain_configuration->is_allowed_authentification_override()) {
                    $mail_configuration->update_xoauth2_refresh_token($this->options['xoauth2_refresh_token'], $this->options['xoauth2_refresh_token_validity']);
                    $mail_configuration->save();
                } else {
                    $domain_configuration->update_xoauth2_refresh_token($this->options['xoauth2_refresh_token'], $this->options['xoauth2_refresh_token_validity']);
                    $domain_configuration->save();
                }
                break;
        }
    }

    /**
     * fetch a new token if it's not available or has expired
     *
     * @return AccessToken
     */
    protected function fetchOauthToken()
    {
        if (null === $this->oauthToken || $this->oauthToken->hasExpired()) {
            $this->oauthToken = $this->getToken();
        }

        $this->updateRefreshToken();
        return $this->oauthToken;
    }

    /**
     * Generate a base64-encoded OAuth token.
     *
     * @return string
     */
    public function getOauth64()
    {
        return base64_encode(
            'user=' .
            $this->oauthUserEmail .
            "\001auth=Bearer " .
            $this->fetchOauthToken() .
            "\001\001"
            );
    }
}

