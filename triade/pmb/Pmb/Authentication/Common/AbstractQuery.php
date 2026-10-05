<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AbstractQuery.php,v 1.2 2023/07/11 08:49:01 dbellamy Exp $

namespace Pmb\Authentication\Common;

use Pmb\Authentication\Interfaces\AuthenticationQueryInterface;
use Pmb\Common\Helper\ParserMessage;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

abstract class AbstractQuery extends AbstractLogger implements AuthenticationQueryInterface
{

    use ParserMessage;

    protected $login_modes = [
        'redirect'
    ];

    protected $charset = 'utf-8';

    protected $caller = null;

    // Attributs externes
    protected $external_attributes = [];

    // Identifiant externe
    protected $external_user = '';

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\Authentication\Interfaces\AuthenticationQueryInterface::setParams()
     */
    public function setParams($params = array())
    {
        $valid_params = [];
        if (! is_array($params) || empty($params)) {
            static::$logger->debug(__METHOD__ . " >> " . print_r($valid_params, true));
            return;
        }

        foreach ($params as $p_name => $p_value) {

            if (property_exists($this, $p_name)) {
                $this->{$p_name} = $p_value;
                $valid_params[$p_name] = $p_value;
            }
        }
        static::$logger->debug(__METHOD__ . " >> " . print_r($valid_params, true));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\Authentication\Interfaces\AuthenticationQueryInterface::getCharset()
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\Authentication\Interfaces\AuthenticationQueryInterface::getLoginModes()
     */
    public function getLoginModes()
    {
        static::$logger->debug(__METHOD__ . " >> " . print_r($this->login_modes, true));
        return $this->login_modes;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\Authentication\Interfaces\AuthenticationQueryInterface::getUser()
     */
    public function getUser()
    {
        static::$logger->debug(__METHOD__ . " >> " . $this->external_user);
        return $this->external_user;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\Authentication\Interfaces\AuthenticationQueryInterface::getAttributes()
     */
    public function getAttributes()
    {
        static::$logger->debug(__METHOD__ . " >> " . print_r($this->external_attributes, true));
        return $this->external_attributes;
    }
}
