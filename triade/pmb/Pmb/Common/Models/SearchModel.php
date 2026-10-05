<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchModel.php,v 1.7.6.2 2025/06/04 12:39:33 qvarin Exp $

namespace Pmb\Common\Models;

class SearchModel extends Model
{
    public const NB_PER_PAGE = 20;

    /**
     * page
     *
     * @var null|int
     */
    protected $page = null;

    /**
     * filter
     *
     * @var null|array
     */
    protected $filter = null;

    public function __construct()
    {}

    /**
     * Exemple de tableau
     *
     * $globalsSearch = [
     *      'f_1' => [
     *          'BOOLEAN' => 'afri*'
     *      ]
     * ];
     *
     */
    private function setGlobalsSearch(array $globalsSearch)
    {
        global $search;

        $i = 0;
        foreach ($globalsSearch as $searchCode => $searchValues) {
            $search[] = $searchCode;
            foreach ($searchValues as $searchOperator => $searchValue) {
                $op = "op_" . $i . "_" . $searchCode;
                global ${$op};
                ${$op} = $searchOperator;
                if (is_array($searchValue) && $searchOperator == 'BETWEEN') {
                    // Cas ou on a besoin de !!p!! et !!p1!!
                    $field_ = "field_" . $i . "_" . $searchCode;
                    global ${$field_};
                    ${$field_} = [$searchValue[0]];

                    $field1_ = "field_" . $i . "_" . $searchCode . "_1";
                    global ${$field1_};
                    ${$field1_} = [$searchValue[1]];
                } else {
                    // Cas classique
                    $field_ = "field_" . $i . "_" . $searchCode;
                    global ${$field_};
                    if (!is_array($searchValue)) {
                        $searchValue = [$searchValue];
                    }
                    ${$field_} = $searchValue;
                }
            }
            $i++;
        }
    }

    /**
     * Defini la page a recuperer
     *
     * @param integer $page
     * @return void
     */
    public function setPage(int $page)
    {
        $this->page = $page;
    }

    /**
     * Defini les filtres
     *
     * @param array $filter
     * @return void
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Add Filter
     *
     * @param string $query
     * @param string $labelId
     * @return string
     */
    protected function addFilter(string $query, $labelId)
    {
        return $query;
    }

    /**
     * Fait une recherche
     *
     * @param array $globalsSearch
     * @param string $labelId
     * @param string $search_fields
     * @param string $table
     * @return array
     */
    public function makeSearch(array $globalsSearch, string $labelId, string $search_fields = 'search_fields', &$table = "")
    {
        $this->setGlobalsSearch($globalsSearch);

        $searcher = new \search(true, $search_fields);
        $table = $searcher->make_search();

        if (null === $this->page) {
            return $this->fetchAll($table, $labelId);
        }

        return $this->pagination($table, $labelId, $this->page);
    }

    /**
     * Fetch All
     *
     * @param string $table
     * @param string $labelId
     * @return array
     */
    protected function fetchAll(string $table, string $labelId)
    {
        $selectField = $table .".".$labelId;
        $query = $this->addFilter("SELECT ". $selectField ." FROM " . $table, $selectField);
        $result = pmb_mysql_query($query);

        $identifiers = [];
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $identifiers[] = $row->{$labelId};
            }
            pmb_mysql_free_result($result);
        }

        return $identifiers;
    }

    /**
     * Pagination
     *
     * @param string $table
     * @param string $labelId
     * @param integer $page
     * @param integer $max (default self::NB_PER_PAGE)
     * @return array
     */
    protected function pagination(string $table, string $labelId, int $page, int $max = self::NB_PER_PAGE)
    {
        $selectField = $table .".".$labelId;
        $query = $this->addFilter("SELECT count(". $selectField .") AS total FROM " . $table, $selectField);
        $result = pmb_mysql_query($query);

        $pagination = [
            'currentPage' => $page + 1,
            'total' => 0,
            'nbPerPage' => $max,
            'result' => [],
        ];

        if (pmb_mysql_num_rows($result)) {
            $pagination['total'] = intval(pmb_mysql_result($result, 0));
            $maxPage = ceil($pagination['total'] / $max);
            if ($page > $maxPage) {
                $page = $maxPage;
            }

            $page = $page * $max;

            $query = $this->addFilter("SELECT ". $selectField ." FROM " . $table, $selectField);
            $query .= " LIMIT $page, $max";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    $pagination['result'][] = $row->{$labelId};
                }
                pmb_mysql_free_result($result);
            }
        }

        return $pagination;
    }
}
