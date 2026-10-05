<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchEmprByMail.php,v 1.2 2023/07/03 15:10:00 dbellamy Exp $

namespace Pmb\Authentication\Helpers\Empr;

use Pmb\Authentication\Interfaces\SearchEmprInterface;
use Pmb\Authentication\Common\AbstractLogger;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class SearchEmprByMail extends AbstractLogger implements SearchEmprInterface
{

    const ARGS = ['empr_mail'];

    public $search_result = [];

    /**
     * Recherche lecteur a partir d'un mail
     *
     * retourne identifiant lecteur si unique, 0 sinon
     * peuple $this->search_result avec tableau [id_empr, empr_login]
     *
     * @param array $args
     *
     * @return int : identifiant lecteur si unique, 0 sinon
     */
    public function search(array $args = [])
    {
        static::$logger->debug(__METHOD__);
        static::$logger->debug(__METHOD__.' >> $args = '.print_r($args, true));

        $ret = 0;
        $this->search_result = [];

        if ( empty($args['empr_mail'] ) || !is_string($args['empr_mail']) ) {
            return $ret;
        }
        $empr_mail = trim($args['empr_mail']);

        if ($empr_mail) {
            $q = "select id_empr, empr_login from empr where empr_mail='" . addslashes($empr_mail) . "' ";
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r) == 1) {
                $this->search_result = pmb_mysql_fetch_assoc($r);
                $ret = $this->search_result['id_empr'];
            }
        }
        return $ret;
    }

    /**
     *
     * {@inheritDoc}
     * @see \Pmb\Authentication\Interfaces\SearchEmprInterface::getArgs()
     */
    public function getArgs()
    {
        static::$logger->debug(__METHOD__);

        return static::ARGS;
    }
}
