<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: LdapQuery.php,v 1.3.4.1 2025/03/04 16:26:11 dbellamy Exp $

namespace Pmb\Authentication\Models\Sources\Ldap;

use Pmb\Authentication\Common\AbstractQuery;
use Pmb\Authentication\Interfaces\AuthenticationQueryInterface;
use Pmb\Authentication\Models\AuthenticationHandler;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class LdapQuery extends AbstractQuery implements AuthenticationQueryInterface
{

    // Parametres
    protected $login_modes = [
        'submit'
    ];

    protected $charset = 'utf-8';

    protected $host = 'ldap://localhost:389';

    protected $user = null;

    protected $pwd = null;

    protected $base_dn = '';

    protected $filter = '(&(objectclass=*)(sn=*))';

    protected $login_attr = 'samaccountname';

    protected $attrs = [];

    protected $one_level = 0;

    protected $ldap_opt_protocol_version = 3;

    protected $ldap_opt_referalls = 0;

    protected $ldap_opt_debug_level = 0;

    protected $size_limit = 2000;

    protected $time_limit = 0;

    protected $deref = LDAP_DEREF_NEVER;

    protected $controls = [];

    protected $use_pagination = 1;

    // Variables internes
    protected $conn = false;

    protected $bound = false;

    protected $result = [];

    /**
     * A traiter *
     */
    protected $filter_pattern = '(&(objectclass=*)(sn=PATTERN*))';

    protected $pagesize = 100;

    protected $use_pattern = 0;

    /**
     * AuthenticationQuery implementation *
     */

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

                switch ($p_name) {
                    case ('attrs'):
                        $p_value = str_replace([
                            "\t",
                            "\n",
                            "\r",
                            " "
                        ], '', $p_value);
                        $p_value = explode(',', $p_value);
                        break;
                    default:
                        break;
                }
                $this->{$p_name} = $p_value;
                $valid_params[$p_name] = $p_value;
            }
        }
        static::$logger->debug(__METHOD__ . " >> " . print_r($valid_params, true));
    }

    /**
     * Lancement authentification (mode submit)
     *
     * @param AuthenticationHandler $caller
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function runExternalLoginSubmit(AuthenticationHandler $caller, string $username, string $password)
    {
        static::$logger->debug(__METHOD__ . " >> {$this->login_attr}={$username} / ***");

        $this->caller = $caller;

        if (empty($username) || empty($password)) {
            static::$logger->debug(__METHOD__ . " >> KO");
            return false;
        }
        $this->bind();
        if (! $this->bound) {
            static::$logger->debug(__METHOD__ . " >> KO");
            $this->close();
            return false;
        }

        $old_one_level = $this->one_level;
        $this->one_level = 0;

        $done = false;
        $filter = "({$this->login_attr}={$username})";
        if (is_string($this->filter) && (false !== stripos($this->filter, "{{FILTER}}"))) {
            $filter = str_replace("{{FILTER}}", $filter, $this->filter);
        }
        $tab_base_dn = (is_array($this->base_dn) ? $this->base_dn : [
            $this->base_dn
        ]);
        while (! $done && count($tab_base_dn)) {

            $base_dn = array_shift($tab_base_dn);
            $this->search($base_dn, $filter, $this->attrs, 1);

            $sr = $this->getResult();
            if (count($sr)) {
                $done = true;
            }
        }

        $this->one_level = $old_one_level;

        if (! count($sr)) {
            static::$logger->debug(__METHOD__ . " >> KO");
            $this->close();
            return false;
        }

        $this->bound = false;
        $this->bind($sr[0]['dn'], $password);
        if (! $this->bound) {
            static::$logger->debug(__METHOD__ . " >> KO");
            $this->close();
            return false;
        }

        $this->external_user = $username;
        $this->external_attributes = [];
        $this->external_attributes['dn'] = $sr[0]['dn'];
        for ($i = 0; $i < $sr[0]['count']; $i ++) {
            $attr_name = $sr[0][$i];
            if ($sr[0][$attr_name]['count'] == 1) {
                $this->external_attributes[$attr_name] = $sr[0][$attr_name][0];
            } else {
                $this->external_attributes[$attr_name] = $sr[0][$attr_name];
                unset($this->external_attributes[$attr_name]['count']);
            }
        }
        $this->close();
        static::$logger->debug(__METHOD__ . " >> OK ");
        return true;
    }

    /**
     * DataQuery implementation *
     */
    public function run()
    {
        static::$logger->debug(__METHOD__);

        $done = false;
        
        if ($this->use_pagination) {

            $this->searchPaginated($this->base_dn, $this->filter, $this->attrs);
            $done = true;
        }

        if (! $done && ! $this->use_pattern) {

            $srg_l = array(); // resultat global
            $this->search($this->base_dn, $this->filter, $this->attrs, $this->size_limit);
            static::$logger->debug(print_r($this->result, true));
            $this->formatResult();
            $done = true;
        }

        if (! $done && $this->use_pattern) {

            $srg_l = array(); // resultat global

            $tab_i0 = array(
                'a',
                'b',
                'c',
                'd',
                'e',
                'f',
                'g',
                'h',
                'i',
                'j',
                'k',
                'l',
                'm',
                'n',
                'o',
                'p',
                'q',
                'r',
                's',
                't',
                'u',
                'v',
                'w',
                'x',
                'y',
                'z'
            );
            $tab_i1 = array(
                ' ',
                '\'',
                'a',
                'b',
                'c',
                'd',
                'e',
                'f',
                'g',
                'h',
                'i',
                'j',
                'k',
                'l',
                'm',
                'n',
                'o',
                'p',
                'q',
                'r',
                's',
                't',
                'u',
                'v',
                'w',
                'x',
                'y',
                'z'
            );

            foreach ($tab_i0 as $i0) {
                foreach ($tab_i1 as $i1) {
                    $srp_l = array();
                    $tmp_filter = str_replace('PATTERN', $i0 . $i1, $this->filter_pattern);
                    $this->search($this->base_dn, $tmp_filter, $this->attrs, $this->size_limit);
                    static::$logger->debug('partiel');
                    static::$logger->debug(print_r($this->result, true));
                    $srp_l = $this->result;
                    if (count($srp_l)) {
                        $srg_l = array_merge($srg_l, $srp_l);
                    }
                    unset($srp_l);
                }
            }
            $this->result = $srg_l;
            $done = true;
        }

        $this->formatResult();
    }

    public function getResult()
    {
        static::$logger->debug(__METHOD__);
        return $this->result;
    }

    public function formatResult()
    {
        static::$logger->debug(__METHOD__);

        if (! is_array($this->result)) {
            $this->result = array();
        }
        if (count($this->result)) {
            foreach ($this->result as $k0 => $v0) {
                if (isset($this->result[$k0]['count'])) {
                    for ($i = 0; $i < $this->result[$k0]['count']; $i ++) {
                        unset($this->result[$k0][$i]);
                    }
                    unset($this->result[$k0]['count']);
                }
                if (isset($this->result[$k0]['dn'])) {
                    $this->result[$k0]['dn'] = array(
                        0 => $this->result[$k0]['dn']
                    );
                }
                foreach ($v0 as $k1 => $v1) {
                    if (isset($this->result[$k0][$k1]['count'])) {
                        unset($this->result[$k0][$k1]['count']);
                    }
                }
            }
        }
    }

    /**
     * Setter magique
     *
     * @param string $p_name
     * @param mixed $p_value
     *
     * @return void
     */
    public function __set($p_name = '', $p_value = '')
    {
        if (property_exists($this, $p_name)) {
            $this->$p_name = $p_value;
            static::$logger->debug(__METHOD__ . " >> {$p_name} = " . print_r($p_value, true));
            return;
        }
        static::$logger->error(__METHOD__ . " >> {$p_name} is undefined");
    }

    /**
     * Getter magique
     *
     * @param string $p_name
     *
     * @return mixed
     */
    public function __get($p_name = '')
    {
        if (property_exists($this, $p_name)) {
            static::$logger->debug(__METHOD__ . " >> {$p_name} = " . print_r($this->{$p_name}, true));
            return $this->$p_name;
        }
        static::$logger->error(__METHOD__ . " >> {$p_name} is undefined");
        return '';
    }

    /**
     * Connexion LDAP
     *
     * @return void
     */
    protected function connect()
    {
        if ($this->conn) {
            static::$logger->debug(__METHOD__ . " >> OK");
            return;
        }

        $this->conn = ldap_connect($this->host);
        if (! $this->conn) {
            static::$error = true;
            static::$logger->debug(__METHOD__ . " >> KO");
            return;
        }

        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_opt_protocol_version);
        ldap_set_option($this->conn, LDAP_OPT_REFERRALS, $this->ldap_opt_referalls);
        ldap_set_option(null, LDAP_OPT_DEBUG_LEVEL, $this->ldap_opt_debug_level);
        putenv('TLS_REQCERT=never');
    }

    /**
     * Bind LDAP (Authentification)
     *
     * @param string $user
     * @param string $pwd
     *
     * @return void
     */
    protected function bind($user = '', $pwd = '')
    {
        if (! $user) {
            $user = $this->user;
        }
        if (! $pwd) {
            $pwd = $this->pwd;
        }
        static::$logger->debug(__METHOD__ . " >> $user / ***");

        $this->connect();
        if (! $this->conn) {
            static::$error = true;
            static::$logger->debug(__METHOD__ . " >> KO");
            return;
        }

        $this->bound = ldap_bind($this->conn, (($user) ? $user : null), (($pwd) ? $pwd : null));
        if (! $this->bound) {
            static::$error = true;
            static::$logger->debug(__METHOD__ . " >> KO");
        }
        static::$logger->debug(__METHOD__ . " >> OK");
    }

    /**
     * Fermeture connexion LDAP
     *
     * @return void
     */
    protected function close()
    {
        @ldap_close($this->conn);
        $this->bound = false;
        $this->conn = false;
        static::$logger->debug(__METHOD__);
    }

    /**
     * Recherche simple
     *
     * @param string $base_dn
     * @param string $filter
     * @param string $attrs
     * @param number $size_limit
     *
     * @return void
     */
    protected function search($base_dn = null, $filter = null, $attrs = null, $size_limit = null)
    {
        static::$logger->debug(__METHOD__);

        if (is_null($base_dn) || ! is_string($base_dn)) {
            $base_dn = $this->base_dn;
        }
        if (is_null($filter) || ! is_string($filter)) {
            $filter = $this->filter;
        }

        if (is_null($attrs) || ! is_array($attrs)) {
            $attrs = $this->attrs;
        }
        if (is_null($size_limit) || ! is_integer($size_limit)) {
            $size_limit = $this->size_limit;
        }

        $time_limit = $this->time_limit;
        $deref = $this->deref;
        $controls = $this->controls;

        $this->result = [];

        $this->bind();
        if (! $this->bound) {
            static::$logger->debug(__METHOD__ . " >> KO");
            $this->close();
            return;
        }

        static::$logger->debug(__METHOD__ . " >> Base dn = $base_dn");
        static::$logger->debug(__METHOD__ . " >> Filter : $filter");
        static::$logger->debug(__METHOD__ . " >> Attrs : " . print_r($attrs, true));

        $lce = 0;
        $lge = array();
        if ($this->one_level === 0) {
            $sr = ldap_search($this->conn, $base_dn, $filter, $attrs, 0, $size_limit, $time_limit, $deref, $controls);
        } else {
            $sr = ldap_list($this->conn, $base_dn, $filter, $attrs, 0, $size_limit, $time_limit, $deref, $controls);
        }

        if ($sr) {
            $lce = ldap_count_entries($this->conn, $sr);
        }
        if ($sr && $lce) {
            $lge = ldap_get_entries($this->conn, $sr);
            unset($lge['count']);
            $this->result = array_merge($this->result, $lge);
        }
        static::$logger->debug(__METHOD__ . " >> $lce entries found");
    }

    /**
     * Recherche paginee
     *
     * @param string $base_dn
     * @param string $filter
     * @param string $attrs
     * @param number $size_limit
     *
     * @return void
     */
    protected function searchPaginated($base_dn = null, $filter = null, $attrs = null, $size_limit = null)
    {
        static::$logger->debug(__METHOD__);

        if (is_null($base_dn) || ! is_string($base_dn)) {
            $base_dn = $this->base_dn;
        }
        if (is_null($filter) || ! is_string($filter)) {
            $filter = $this->filter;
        }
        if (is_null($attrs) || ! is_array($attrs)) {
            $attrs = $this->attrs;
        }
        if (is_null($size_limit) || ! is_integer($size_limit)) {
            $size_limit = $this->size_limit;
        }

        $time_limit = $this->time_limit;
        $deref = $this->deref;
        $controls = $this->controls;
        if (is_null($controls)) {
            $controls = [];
        }
        $cookie = '';
        $controls[] = [
            'oid' => LDAP_CONTROL_PAGEDRESULTS,
            'value' => [
                'size' => $this->pagesize,
                'cookie' => $cookie
            ]
        ];

        $this->result = [];
        $this->connect();
        if (! $this->conn) {
            static::$logger->debug();
            $this->close();
            return;
        }
        $this->bind();
        if (! $this->bound) {
            static::$logger->debug();
            $this->close();
            return;
        }

        do {
            $lce = 0;
            $lge = array();

            if ($this->one_level === 0) {
                $sr = ldap_search($this->conn, $base_dn, $filter, $attrs, 0, $size_limit, $time_limit, $deref, $controls);
            } else {
                $sr = ldap_list($this->conn, $base_dn, $filter, $attrs, 0, $size_limit, $time_limit, $deref, $controls);
            }
            if ($sr) {
                $lce = ldap_count_entries($this->conn, $sr);
            }
            if ($sr && $lce) {
                $lge = ldap_get_entries($this->conn, $sr);
                unset($lge['count']);
                $this->result = array_merge($this->result, $lge);
            }
            static::$logger->debug("LDAP Base dn : $base_dn");
            static::$logger->debug("LDAP Filter : $filter");
            static::$logger->debug("$lce entries found");

            if (isset($controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'])) {
                $cookie = $controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'];
            } else {
                $cookie = '';
            }
        } while (! empty($cookie));

        $this->close();
    }
}
