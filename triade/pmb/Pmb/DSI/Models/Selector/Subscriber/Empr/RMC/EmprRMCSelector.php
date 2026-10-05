<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EmprRMCSelector.php,v 1.16.4.1 2025/03/19 15:53:33 jparis Exp $

namespace Pmb\DSI\Models\Selector\Subscriber\Empr\RMC;

use Pmb\DSI\Models\Selector\SubSelector;
use Pmb\DSI\Models\SubscriberList\Subscribers\SubscriberEmpr;

class EmprRMCSelector extends SubSelector
{
    public const TYPE_SELECTOR = 2;

    protected $search = null;

    protected $humanQuery = null;

    protected $searchSerialize = null;

    protected static $tempTable = "";

    public function __construct(?object $data = null)
    {
        if (isset($data)) {
            $this->search = $data->search ?? "";
            $this->humanQuery = $data->human_query ?? "";
            $this->searchSerialize = $data->search_serialize ?? "";
        }
    }

    public function getData()
    {
        if (empty($this->search)) {
            return [];
        }
        if(defined("GESTION")) {
            $searchInstance = new \search(true, "search_fields_empr");
        } else {
            $searchInstance = new \search("search_fields_empr");
        }
        $searchInstance->unserialize_search($this->searchSerialize);

        self::$tempTable = $searchInstance->make_search();

        $query = "select empr.id_empr, empr.empr_cb, empr.empr_nom, empr.empr_prenom, empr.empr_mail from empr RIGHT JOIN " . self::$tempTable . " ON empr.id_empr = " . self::$tempTable . ".id_empr";
        $result = pmb_mysql_query($query);
        $emprs = [];

        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $emprs[] = $this->getSubscriberFromQuery($row);
            }
        }
        // Souci de table tempo pas encore supprimee -> on force donc la suppression
        if (! empty(self::$tempTable)) {
            $query = "DROP TABLE IF EXISTS " . self::$tempTable;
            pmb_mysql_query($query);
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
        if (isset($this->searchInput)) {
            return $this->searchInput;
        }

        $this->searchInput = $this->humanQuery ?? "";
		return $this->searchInput;
    }
}
