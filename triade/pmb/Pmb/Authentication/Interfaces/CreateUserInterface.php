<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CreateUserInterface.php,v 1.4.4.1 2025/03/04 16:26:11 dbellamy Exp $

namespace Pmb\Authentication\Interfaces;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

interface CreateUserInterface
{
    /**
     * Creation utilisateur lors de l'authentification
     *
     * @param array $args
     *
     * @return int : Identifiant utilisateur si ok, 0 sinon
     */
    public function onAuthenticationCreate($caller = null, array $args = []);

    /**
     * Modification utilisateur lors de l'authentification
     *
     * @param array $args
     *
     * @return int : Identifiant utilisateur si ok, 0 sinon
     */
    public function onAuthenticationUpdate($caller = null, array $args = []);

    /**
     * Liste des arguments a fournir
     *
     * @return array
     */
    public function getArgs();
}
