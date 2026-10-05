<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TransfoInterface.php,v 1.2 2023/07/03 15:09:59 dbellamy Exp $

namespace Pmb\Authentication\Interfaces;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

interface TransfoInterface
{
    /**
     * Transformation attribut
     *
     * @param array $args
     *
     * @return mixed
     */
    public function transfo(array $args = []);

    /**
     * Liste des arguments a fournir
     *
     * @return array
     */
   public function getArgs();
}
