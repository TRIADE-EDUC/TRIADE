<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationQueryInterface.php,v 1.2 2023/06/23 12:38:10 dbellamy Exp $

namespace Pmb\Authentication\Interfaces;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

interface AuthenticationQueryInterface
{

    /**
     * Definition parametres
     *
     * @params array $params
     *
     * @return void
     */
    public function setParams($params = []);

    /**
     * Retourne le charset externe
     *
     * @return string
     */
    public function getCharset();

    /**
     * Retourne les modes d'authentification possibles
     *
     * @return array : ['submit', 'redirect']
     */
    public function getLoginModes();

    /**
     * Retourne le login de l'utilisateur
     *
     * @return string
     */
    public function getUser();

    /**
     * Retourne les attributs de l'utilisateur
     *
     * @return array
     */
    public function getAttributes();
}
