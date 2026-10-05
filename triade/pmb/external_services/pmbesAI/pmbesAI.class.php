<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesAI.class.php,v 1.3 2024/01/25 15:07:56 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path . "/search_perso.class.php");

class pmbesAI extends external_services_api_class
{
    /**
     * Nombre de docnums a recuperer par passe
     */
    protected const NB_PER_PASS = 1000;

    /**
     * Recupere le contenu des docnums depuis la base de donnees.
     *
     * @param int $fromId Identifiant d'exemplaire numerique a partir duquel commencer le tableau
     * @return array Le contenu des docnums
     */
    public function getDocnumsContent(int $fromId = 0, int $id_search_perso = 0)
    {
        $contents = array();
        $queryTemp = $this->getSearchPersoQuery($id_search_perso);

        $query = "SELECT explnum_id, explnum_notice, explnum_index_wew FROM explnum
            WHERE explnum_index_wew != ''
            AND explnum_notice IN ($queryTemp)";
        if ($fromId) {
            $query .= " AND explnum_id > '$fromId'";
        }
        $query .= " ORDER BY explnum_id LIMIT " . static::NB_PER_PASS;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $contents = encoding_normalize::utf8_normalize(pmb_mysql_fetch_all($result));
        }

        return $contents;
    }

    /**
     * Recupere le contenu des resumes des notices depuis la base de donnees.
     *
     * @param int $fromId Identifiant de notice a partir duquel commencer le tableau
     * @return array Le contenu des resumes
     */
    public function getSummariesContent(int $fromId = 0, int $id_search_perso = 0)
    {
        $contents = array();
        $queryTemp = $this->getSearchPersoQuery($id_search_perso);

        $query = "SELECT notice_id, n_resume FROM notices WHERE n_resume != '' AND notice_id IN ($queryTemp)";
        if ($fromId) {
            $query .= " AND notice_id > '$fromId'";
        }
        $query .= " ORDER BY notice_id LIMIT " . static::NB_PER_PASS;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $contents = encoding_normalize::utf8_normalize(pmb_mysql_fetch_all($result));
        }

        return $contents;
    }

    /**
     * Retourne la requete de recherche de la recherche perso
     *
     * @param int $id_search_perso
     * @return string
     */
    protected function getSearchPersoQuery(int $id_search_perso = 0)
    {
        $searchPerso = new search_perso($id_search_perso);
        $search = $searchPerso->get_instance_search();
        $search->unserialize_search($searchPerso->query);
        $tempTable = $search->make_search();

        return "SELECT notice_id FROM $tempTable";
    }
}
