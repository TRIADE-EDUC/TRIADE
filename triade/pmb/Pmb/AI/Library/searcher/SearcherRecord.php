<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearcherRecord.php,v 1.2 2024/03/28 10:39:14 qvarin Exp $

namespace Pmb\AI\Library\searcher;

use sort;
use search;
use searcher;
use filter_results;

class SearcherRecord extends SearcherRoot
{
    /**
     * Clé primaire de la table
     */
    public const PRIMARY_KEY = "notice_id";

    /**
     * Nombre d'exemplaires
     *
     * @var integer
     */
    public $nbExplnum = 0;

    /**
     * Retourne l'instance du tri
     *
     * @return sort
     */
    protected function _get_sort_instance()
    {
        return new sort("notices", "session");
    }

    /**
     * Retourne l'instance de recherche
     *
     * @return search
     */
    protected function _get_search_instance()
    {
        return new search("search_fields");
    }

    /**
     * Filtre les resultats
     *
     * @return void
     */
    protected function _filter_results()
    {
        if (!empty($this->objects)) {
            $filter = new filter_results(implode(',', $this->objects));
            $idsAllowed = explode(',', $filter->get_results());

            $this->objects = array_filter($this->objects, function ($id) use ($idsAllowed) {
                return in_array($id, $idsAllowed);
            });
            $this->pert = array_filter($this->pert, function ($id) use ($idsAllowed) {
                return in_array($id, $idsAllowed);
            }, ARRAY_FILTER_USE_KEY);
        }
    }

    /**
     * Retourne le nombre d'exemplaires
     *
     * @param integer $limit_one
     * @return integer
     */
    public function get_nb_explnums($limit_one = 1)
    {
        if (! $this->objects) {
            $this->get_result();
        }

        $this->nbExplnum = 0;
        if ($this->objects != "") {
            $this->nbExplnum = searcher::get_nb_explnums_from_notices_ids(
                implode(',', $this->objects),
                $limit_one
            );
        }
        return $this->nbExplnum;
    }
}
