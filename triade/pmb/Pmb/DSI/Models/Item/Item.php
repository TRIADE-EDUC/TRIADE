<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Item.php,v 1.4 2023/07/21 07:53:36 rtigero Exp $

namespace Pmb\DSI\Models\Item;

interface Item
{
    public function getData();

    /**
     * Retourne la human query du ou des selecteurs associés à l'item
     * @return string
     */
    public function getSearchInput();

    /**
     * Retourne le nombre de resultats de l'item
     */
    public function getNbResults();

    /**
     * Retourne la liste des résultats
     */
    public function getResults();
}

