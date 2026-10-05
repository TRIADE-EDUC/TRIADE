<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchUserByLogin.php,v 1.2 2023/07/03 15:09:59 dbellamy Exp $

namespace Pmb\Authentication\Helpers\User;

use Pmb\Authentication\Interfaces\SearchUserInterface;
use Pmb\Authentication\Common\AbstractLogger;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class SearchUserByLogin extends AbstractLogger implements SearchUserInterface
{

    const ARGS = ['username'];
    public $search_result = [];

    /**
     * Recherche utilisateur a partir d'un login
     *
     * retourne identifiant utilisateur si trouvé, 0 sinon
     * peuple $this->search_result avec tableau [userid, username]
     *
     * @param array $args
     *
     * @return int : identifiant utilisateur si trouvé, 0 sinon
     */
    public function search(array $args = [])
    {
        $ret = 0;
        $this->search_result = [];

        if ( empty($args['username'] ) || !is_string($args['username']) ) {
            return $ret;
        }
        $username = trim($args['username']);

        if ($username) {
            $q = 'select userid, username from users where username="' . addslashes($username) . '" limit 1';
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $this->search_result = pmb_mysql_fetch_assoc($r);
                $ret = $this->search_result['userid'];
            }
        }
        return $ret;
    }

    /**
     *
     * {@inheritDoc}
     * @see \Pmb\Authentication\Interfaces\SearchUserInterface::getArgs()
     */
    public function getArgs()
    {
        return static::ARGS;
    }
}
