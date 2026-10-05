<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchByIdSelector.php,v 1.1 2023/10/25 15:10:09 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Subscriber\Empr\SearchById;

use Pmb\DSI\Models\Selector\SubSelector;
use Pmb\DSI\Models\SubscriberList\Subscribers\SubscriberEmpr;

class SearchByIdSelector extends SubSelector
{
    public const TYPE_SELECTOR = 2;

    public $idEmpr;

    public function __construct($idEmpr = 0)
    {
        $this->idEmpr = intval($idEmpr);
    }
    
    public function getData()
    {
        $query = "SELECT id_empr, empr_cb, empr_mail, empr_prenom, empr_nom FROM empr WHERE id_empr = '" .$this->idEmpr."'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $emprs[] = $this->getSubscriberFromQuery($row);
            }
        }
        return $emprs;
    }

    /**
     * Cree et remplit les donnees d'un subcriber a partir d'une ligne de requete
     * @param object $row
     * @return \Pmb\DSI\Models\SubscriberList\Subscribers\SubscriberEmpr
     */
    protected function getSubscriberFromQuery($row)
    {
        $empr = new SubscriberEmpr();
        $empr->settings->idEmpr = $row->id_empr;
        $empr->settings->cb = $row->empr_cb;
        $empr->settings->email = $row->empr_mail;
        $empr->name = $row->empr_prenom . ' ' . $row->empr_nom;
        $empr->type = self::TYPE_SELECTOR;

        return $empr;
    }

    /**
     * Retourne la recherche effectuer pour l'affichage.
     *
     * @return string
     */
    public function getSearchInput(): string
    {
        return $this->idEmpr ?? 0;
    }
}
