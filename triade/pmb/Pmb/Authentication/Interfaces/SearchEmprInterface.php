<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchEmprInterface.php,v 1.3 2023/07/03 15:09:59 dbellamy Exp $

namespace Pmb\Authentication\Interfaces;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

interface SearchEmprInterface
{
    /**
     * Recherche lecteur
     *
     * retourne identifiant lecteur si unique, 0 sinon
     * peuple $this->search_result avec tableau [id_empr, empr_login]
     *
     * @param array $args
     *
     * @return int : identifiant lecteur si unique, 0 sinon
     */
    public function search(array $args = []);

    /**
     * Liste des arguments a fournir
     *
     * @return array
     */
    public function getArgs();

}