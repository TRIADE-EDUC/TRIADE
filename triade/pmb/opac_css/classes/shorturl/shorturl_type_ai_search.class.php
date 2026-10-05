<?php

// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_ai_search.class.php,v 1.3 2024/03/07 12:50:17 qvarin Exp $

use Pmb\AI\Models\AiSessionSemanticModel;
use Pmb\AI\Orm\AiSessionSemanticOrm;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class shorturl_type_ai_search extends shorturl_type
{
    protected function rss()
    {
		global $opac_short_url_mode, $opac_short_url_rss_records_format;
		global $opac_search_results_per_page, $opac_url_base, $charset;

		if (!is_array($this->context)) {
            $context = unserialize($this->context);
        }

        $context['ai_session'] = intval($context['ai_session']);
        if (!empty($context['ai_session']) && AiSessionSemanticOrm::exist($context['ai_session'])) {
            $sessionShared = new AiSessionSemanticModel($context['ai_session']);
        } else {
            global $opac_url_base;

            // L'historique n'existe pas, on redirige vers l'accueil
            $location = "{$opac_url_base}index.php";
            header("Location: {$location}", true, 301);
            exit;
        }

		$notices_list = [];
        if (!empty($sessionShared->aiSessionSemantiqueNumObjects[$context['index_question']])) {
			foreach ($sessionShared->aiSessionSemantiqueNumObjects[$context['index_question']] as $row) {
				$notices_list[]= $row['id'];
			}
        }

        if ($opac_short_url_mode) {

            $flux = new records_flux(0);
            $rssRecordsFormat = substr($opac_short_url_rss_records_format, 0, 1);
            $flux->setRssRecordsFormat($rssRecordsFormat);
            if ($rssRecordsFormat == 'H') {
                $flux->setIdTpl(substr($opac_short_url_rss_records_format, 2));
            }

            $flux->set_limit($opac_search_results_per_page);
            $params = explode(',', $opac_short_url_mode);
            if (is_array($params) && count($params) > 1) { //Une limite est définie
                $flux->set_limit($params[1]);
            }

        } else {
            $flux = new newrecords_flux(0) ;
        }

        $flux->setRecords($notices_list);
        $flux->setLink($opac_url_base."s.php?h=$this->hash");

        $human_query = AiSessionSemanticModel::create_human_query($sessionShared->aiSessionSemantiqueQuestions[$context['index_question']] ?? '');
        $flux->setDescription(strip_tags($human_query));
        $flux->xmlfile();

        if (!$flux->envoi) {
            return;
        }

        @header('Content-type: text/xml; charset='.$charset);
        print $flux->envoi;
    }

    protected function permalink()
    {
        if (!is_array($this->context)) {
            $context = unserialize($this->context);
        }

        $context['ai_session'] = intval($context['ai_session']);
        if (!empty($context['ai_session']) && AiSessionSemanticOrm::exist($context['ai_session'])) {
            $sessionShared = new AiSessionSemanticModel($context['ai_session']);
        } else {
            global $opac_url_base;

            // L'historique n'existe pas, on redirige vers l'accueil
            $location = "{$opac_url_base}index.php";
            header("Location: {$location}", true, 301);
            exit;
        }

        // On l'enregistre dans l'historique
        global $user_query;
        $user_query = $sessionShared->aiSessionSemantiqueQuestions[$context['index_question']];
        AiSessionSemanticModel::rec_history();

        // On récupère l'index de l'historique
        $n = $_SESSION["nb_queries"];
        $_SESSION["ai_search_history_{$n}"]['ai_session'] = intval($_SESSION["ai_search_history_{$n}"]['ai_session']);
        $index_question = $_SESSION["ai_search_history_{$n}"]["index_question"];

        // On ajoute les résultats et la réponse générée
        $session = new AiSessionSemanticModel($_SESSION["ai_search_history_{$n}"]['ai_session']);
        $session->addResults($index_question, $sessionShared->aiSessionSemantiqueNumObjects[$context['index_question']]);
        $session->addResponse($index_question, $sessionShared->aiSessionSemantiqueReponses[$context['index_question']]);

        // On redirige sur la recherche
        global $opac_url_base;
        $location = "{$opac_url_base}index.php?";
        $location .= http_build_query([
            'lvl' => 'search_result',
            'get_query' => $n
        ]);

        header("Location: {$location}", true, 301);
    }

    public function generate_hash($action, $context = array())
    {
		$nb_queries = $_SESSION["nb_queries"];
        if (empty($_SESSION["ai_search_history_{$nb_queries}"])) {
			return '';
		}

		$index_question = $_SESSION["ai_search_history_{$nb_queries}"]["index_question"];
		$ai_session = $_SESSION["ai_search_history_{$nb_queries}"]["ai_session"];

        $new_context = array();
        $new_context["ai_session"] = $ai_session;
        $new_context["index_question"] = $index_question;

        $hash = '';
        if (method_exists($this, $action)) {
            $hash = self::create_hash('ai_search', $action, $new_context);
        }
        return $hash;
    }
}
