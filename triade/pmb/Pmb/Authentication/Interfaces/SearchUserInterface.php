<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchUserInterface.php,v 1.3 2023/07/03 15:09:59 dbellamy Exp $

namespace Pmb\Authentication\Interfaces;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

interface SearchUserInterface
{
    /**
     * Recherche utilisateur
     *
     * retourne identifiant utilisateur si trouvé, 0 sinon
     * peuple $this->search_result avec tableau [userid, username]
     *
     * @param array $args
     *
     * @return int : identifiant utilisateur si trouvé, 0 sinon
     */
    public function search(array $args = []);

    /**
     * Liste des arguments a fournir
     *
     * @return array
     */
    public function getArgs();

}
