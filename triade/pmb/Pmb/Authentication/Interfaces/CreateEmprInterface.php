<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CreateEmprInterface.php,v 1.4 2023/07/13 13:14:57 dbellamy Exp $

namespace Pmb\Authentication\Interfaces;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

interface CreateEmprInterface
{
    /**
     * Creation lecteur lors de l'authentification
     * Retourne identifiant lecteur si ok, 0 sinon
     * Peuple $this->empr_login
     *
     * @param array $args
     *
     * @return int : Identifiant lecteur si ok, 0 sinon
     *
     */
    public function onAuthenticationCreate($caller = null, array $args = []);

    /**
     * Modification lecteur lors de l'authentification
     *
     * @param array $args
     *
     * @return int : Identifiant lecteur si ok, 0 sinon
     */
    public function onAuthenticationUpdate($caller = null, array $args = []);

    /**
     * Liste des arguments a fournir
     *
     * @return array
     */
    public function getArgs();
}
