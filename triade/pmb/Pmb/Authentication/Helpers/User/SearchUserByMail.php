<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchUserByMail.php,v 1.2 2023/07/03 15:09:59 dbellamy Exp $

namespace Pmb\Authentication\Helpers\User;

use Pmb\Authentication\Interfaces\SearchUserInterface;
use Pmb\Authentication\Common\AbstractLogger;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class SearchUserByMail extends AbstractLogger implements SearchUserInterface
{

    const ARGS = ['user_email'];
    public $search_result = [];

    /**
     * Recherche utilisateur a partir d'un email
     *
     * retourne identifiant utilisateur si unique, 0 sinon
     * peuple $this->search_result avec tableau [userid, username]
     *
     * @param array $args
     *
     * @return int : identifiant utilisateur si unique, 0 sinon
     */
    public function search(array $args = [])
    {
        $ret = 0;
        $this->search_result = [];

        if ( empty($args['user_email'] ) || !is_string($args['user_email']) ) {
            return $ret;
        }
        $user_email = trim($args['user_email']);


        if ($user_email) {
            $q = "select userid, username from users where user_email='" . addslashes($user_email) . "' ";
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r) == 1) {
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