<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SourceSelector.php,v 1.3 2023/04/05 09:55:15 qvarin Exp $

namespace Pmb\DSI\Models\Selector;

class SourceSelector extends RootSelector
{
    /**
	 * Cette methode doit etre remplacee dans les sous-classes.
     * Retourne les donnée de la recherche effectuer
     *
     * @return array
     */
    public function getData()
    {
        return [];
    }

    /**
	 * Cette methode doit etre remplacee dans les sous-classes.
     * Retourne les identifiants de la recherche effectuer
     *
     * @return array
     */
    public function getResults()
    {
        return [];
    }
}

