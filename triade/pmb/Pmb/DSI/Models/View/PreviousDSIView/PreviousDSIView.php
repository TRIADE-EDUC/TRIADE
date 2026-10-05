<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PreviousDSIView.php,v 1.9.4.2 2024/10/01 08:24:23 jparis Exp $

namespace Pmb\DSI\Models\View\PreviousDSIView;

use bannette_tpl;
use encoding_normalize;
use H2o;
use H2o_collection;
use notice_tpl_gen;
use Pmb\DSI\Helper\LookupHelper;
use Pmb\DSI\Helper\SubscriberHelper;
use Pmb\DSI\Models\View\GroupView\GroupView;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Models\View\SummaryView\SummaryView;
use Pmb\DSI\Orm\ViewOrm;
use XMLlist;

class PreviousDSIView extends SummaryView
{
    public const DEFAULT_PARAMETERS_TEMPLATE = [
        'level' => 6,
        'expl' => 0,
        'show_explnum' => 1,
    ];

    protected static $lang_messages = array();

    protected $html = "";

    private $noticeTemplates;
    private $groupedView = false;
    private $formatedData = array();
    private $records = array();

    protected function fetchLinkedView()
    {
        parent::fetchLinkedView();
        if ($this->linkedView->id != 0) {
            $this->groupedView = true;
        }
    }

    /**
     * Reproduit l'affichage par défaut des templates de bannette pour les bannettes avec groupement
     */
    protected const BANNETTE_DEFAULT_TPL_WITH_SUMMARY = "
        {{ info.header }}
        <a name=\"SUMMARY\"></a>
        <div class=\"summary\">
            <br />
            {% for sommaire in sommaires %}
                {% if sommaire.level == 1 %}
                    <a class=\"summary_elt\" href=\"#{{ loop.counter }}\">
                        {{ loop.counter }} - {{ sommaire.title }}
                    </a>
                    <br />
                {% endif %}
            {% endfor %}
        </div>
        {% for sommaire in sommaires %}
            <h{{ sommaire.level }} id=\"{{ loop.counter }}\" class=\"dsi_rang_{{ sommaire.level }}\">{{ sommaire.title }}</h{{ sommaire.level }}>
            {% for record in sommaire.records %}
                {% if record.render %}
                    {{ record.render }}
                {% endif %}
            {% endfor %}
            <br />
        {% endfor %}
        {{ info.footer }}
    ";

    /**
     * Reproduit l'affichage par défaut des templates de bannette pour les bannettes sans groupement
     */
    protected const BANNETTE_DEFAULT_TPL_WITHOUT_SUMMARY = "
        {{ info.header }}
        {% for record in records %}
            {% if record.render %}
                {{ record.render }}
            {% endif %}
        {% endfor %}
        <br />
        {{ info.footer }}
    ";


    public function render($item, int $entityId, int $limit, string $context)
    {
        global $use_opac_url_base, $use_dsi_diff_mode, $base_path, $charset, $lang;

        $data = $this->getDataFromContext($item, $context);
        $this->fetchLinkedView();
        $use_opac_url_base = 1;
        $use_dsi_diff_mode = 1;

        $this->formatedData["records"] = array();
        $this->filterData($data, $entityId);
        $this->formatedData["records"]["length_total"] = count($data);
        $this->limitData($data, $limit);
        $this->formatedData["records"]["length"] = count($data);
        $lang_messages = static::get_lang_messages($lang);
        $this->formatedData["dsi_diff_n_notices"] = sprintf($lang_messages["dsi_diff_n_notices"], $this->formatedData["records"]["length"], $this->formatedData["records"]["length_total"]);

        if (empty($data)) {
            return "";
        }

        //On s'occupe du groupement des données
        if ($this->linkedView instanceof GroupView) {
            $this->linkedView->groupData($data, TYPE_NOTICE);
        }
        $this->getFormatedData($data);
        $displayNbNotices = (isset($this->settings->displayNbNotice) && $this->settings->displayNbNotice);

        //On reprend les entêtes de l'ancienne DSI
        $this->html = "<!DOCTYPE html><html lang='" . get_iso_lang_code() . "'><head><meta charset=\"" . $charset . "\" />" . $this->get_css_style() . "</head><body>";

        //Si on a un tpl de bannette et un groupement tout est déjà prêt
        if ($this->settings->bannetteTemplate) {
            if ($displayNbNotices) {
                $this->html .= "<span class=\"dsi_hide_for_emails\"><hr>!!dsi_diff_n_notices!!<hr></span>";
            }
            $this->html .= static::renderBannetteTpl($this->settings->bannetteTemplate, $this->formatedData);
        } else {
            //Sinon on s'occupe du template et du rendu h2o
            $tpl = static::BANNETTE_DEFAULT_TPL_WITHOUT_SUMMARY;
            if ($this->groupedView) {
                $tpl = static::BANNETTE_DEFAULT_TPL_WITH_SUMMARY;
            }
            if ($displayNbNotices) {
                $tpl = str_replace('{{ info.header }}', '{{ info.header }}<span class=\"dsi_hide_for_emails\"><hr>!!dsi_diff_n_notices!!<hr></span>', $tpl);
            }
            $template_path = $base_path . '/temp/' . LOCATION . '_bannette_tpl_' . $this->settings->bannetteTemplate;
            file_put_contents($template_path, $tpl);
            H2o::addLookup([SubscriberHelper::class, 'h2oLookup']);
            H2o::addLookup([LookupHelper::class, 'h2oLookup']);
            $H2o = H2o_collection::get_instance($template_path);
            $this->html .= $H2o->render($this->formatedData);

            if ($charset != "utf-8") {
                $this->html .= encoding_normalize::utf8_decode($this->html);
            }
        }
        //Gestion de l'affichage de la diff de notices
        if ($displayNbNotices) {
            $this->html = str_replace('!!dsi_diff_n_notices!!', $this->formatedData["dsi_diff_n_notices"], $this->html);
        }

        return $this->html;
    }

    public function preview($item, int $entityId, int $limit, string $context)
    {
        return $this->render($item, $entityId, $limit, $context);
    }

    /**
     * Renvoie un template par défaut de notices
     * @param int $noticeId identifiant de la notice
     */
    private function generateDefaultTemplate(int $noticeId)
    {
        $query = "SELECT notice_id, niveau_biblio FROM notices WHERE notice_id = {$noticeId}";
        $result = pmb_mysql_query($query);

        $template = "";
        if ($result && pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_assoc($result);

            global $opac_url_base;
            $noticeLink = $opac_url_base . "index.php?database=" . DATA_BASE . "&lvl=notice_display&id={$row['notice_id']}";

            if (in_array($row['niveau_biblio'], ["m", "b"], true)) {
                $mono = new \mono_display(
                    $row['notice_id'],
                    static::DEFAULT_PARAMETERS_TEMPLATE["level"],
                    "",
                    static::DEFAULT_PARAMETERS_TEMPLATE["expl"],
                    "",
                    "",
                    "",
                    0,
                    1,
                    static::DEFAULT_PARAMETERS_TEMPLATE["show_explnum"],
                    0,
                    "",
                    0,
                    true,
                    false,
                    0,
                    0,
                    1
                );
                $template = "<a href='{$noticeLink}'><b>{$mono->header}</b></a><br /><br />\r\n{$mono->isbd}";
            } elseif (in_array($row['niveau_biblio'], ["s", "a"], true)) {
                $serial = new \serial_display(
                    $row['notice_id'],
                    static::DEFAULT_PARAMETERS_TEMPLATE["level"],
                    "",
                    "",
                    "",
                    "",
                    "",
                    0,
                    1,
                    static::DEFAULT_PARAMETERS_TEMPLATE["show_explnum"],
                    0,
                    false,
                    0,
                    0,
                    '',
                    false,
                    1
                );
                $template = "<a href='{$noticeLink}'><b>{$serial->header}</b></a><br /><br />\r\n{$serial->isbd}";
            }
        }

        global $notice_separator;
        if ($notice_separator) {
            $template .= $notice_separator;
        } else {
            $template .= "<div class='hr'><hr /></div>";
        }
        return $template . "\r\n";
    }

    /**
     * Permet de fournir des donnees pour le formulaire
     *
     * @return array
     */
    public function getFormData()
    {
        $this->fetchTemplates();
        $groupViews = ViewOrm::finds([
            "type" => 11
        ]);

        $groupViews = array_map(function ($view) {
            return [
                "value" => $view->id_view,
                "label" => $view->name,
            ];
        }, $groupViews);


        return array_merge(
            RootView::getFormData(),
            [
                "bannetteTemplates" => $this->bannetteTemplates,
                "noticeTemplates" => $this->noticeTemplates,
                "groupViews" => $groupViews
            ]
        );
    }

    /**
     * Recupere les templates de notices / bannettes definis en Editions
     *
     * @return void
     */
    protected function fetchTemplates()
    {
        if (isset($this->noticeTemplates) && isset($this->bannetteTemplates)) {
            return false;
        }

        $this->bannetteTemplates = [];
        $query = "SELECT bannettetpl_id, bannettetpl_name FROM bannette_tpl";
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $this->bannetteTemplates[] = [
                    "value" => intval($row['bannettetpl_id']),
                    "label" => $row['bannettetpl_name'],
                ];
            }
        }

        $this->noticeTemplates = [];
        $query = "SELECT notpl_id, if (notpl_comment != '', concat(notpl_name,'. ',notpl_comment), notpl_name) AS nom
                FROM notice_tpl ORDER BY notpl_name";
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $this->noticeTemplates[] = [
                    "value" => intval($row['notpl_id']),
                    "label" => $row['nom'],
                ];
            }
        }
    }

    public function setNoticeTemplates($template)
    {
        $this->noticeTemplates = $template;
    }

    public function setBannetteTemplates($template)
    {
        $this->bannetteTemplates = $template;
    }

    /**
     * Convertit un id de notice en template utilisable pour les bannettes
     * @param int $id Id de notice
     */
    private function getNoticeTpl($id)
    {
        if (empty($this->settings->noticeTemplate)) {
            return array("render" => $this->generateDefaultTemplate($id));
        } else {
            $noticeTemplateGen = notice_tpl_gen::get_instance($this->settings->noticeTemplate);
            return array("render" => $noticeTemplateGen->build_notice($id));
        }
    }

    /**
     * Formatage des données pour les rendre compatibles avec les templates de bannettes
     * @param array $data Données / groupées ou non
     */
    protected function getFormatedData($data)
    {
        global $msg;

        $this->formatedData["sommaires"] = array();
        $this->formatedData["info"] = array();
        $this->formatedData["info"]['header'] = $this->settings->headerTemplate;
        $this->formatedData["info"]['footer']  = $this->settings->footerTemplate;

        $this->formatedData['empr'] = array();
        $this->formatedData['empr']['name'] = '!!empr_name!!';
        $this->formatedData['empr']['first_name'] = '!!empr_first_name!!';
        $this->formatedData['empr']['civ'] = '!!empr_sexe!!';
        $this->formatedData['empr']['cb'] = '!!empr_cb!!';
        $this->formatedData['empr']['login'] = '!!empr_login!!';
        $this->formatedData['empr']['mail'] = '!!empr_mail!!';
        $this->formatedData['empr']['name_and_adress'] = '!!empr_name_and_adress!!';
        $this->formatedData['empr']['all_information'] = '!!empr_all_information!!';
        $this->formatedData['empr']['connect'] = '!!empr_connect!!';
        $this->formatedData['empr']['statut_id'] = '!!empr_statut_id!!';
        $this->formatedData['empr']['statut_lib'] = '!!empr_statut_lib!!';
        $this->formatedData['empr']['categ_id'] = '!!empr_categ_id!!';
        $this->formatedData['empr']['categ_lib'] = '!!empr_categ_lib!!';
        $this->formatedData['empr']['codestat_id'] = '!!empr_codestat_id!!';
        $this->formatedData['empr']['codestat_lib'] = '!!empr_codestat_lib!!';
        $this->formatedData['empr']['langopac_code'] = '!!empr_langopac_code!!';
        $this->formatedData['empr']['langopac_lib'] = '!!empr_langopac_lib!!';

        $this->formatedData['loc'] = array();
        $this->formatedData['loc']['name'] = '!!loc_name!!';
        $this->formatedData['loc']['adr1'] = '!!loc_adr1!!';
        $this->formatedData['loc']['adr2'] = '!!loc_adr2!!';
        $this->formatedData['loc']['cp'] = '!!loc_cp!!';
        $this->formatedData['loc']['town'] = '!!loc_town!!';
        $this->formatedData['loc']['phone'] = '!!loc_phone!!';
        $this->formatedData['loc']['email'] = '!!loc_email!!';
        $this->formatedData['loc']['website'] = '!!loc_website!!';

        if ($this->groupedView) {
            $this->getGroupedData($data);
        } else {
            $this->records = array_map(array($this, 'getNoticeTpl'), array_keys($data));
            $this->formatedData["sommaires"][] = array(
                "title" => $msg["dsi_record_not_classified"],
                "records" => $this->records
            );
        }

        $this->formatedData["records"] = array_merge($this->formatedData["records"], $this->records);
        $this->formatedData = encoding_normalize::utf8_normalize($this->formatedData);
    }


    protected function getGroupedData($data, $level = 1)
    {
        global $msg;

        foreach ($data as $name => $notices) {
            $group = array();
            $group["title"] = $name != "notfound" ? $name : $msg["dsi_record_not_classified"];
            $group["level"] = $level;
            $this->formatedData["sommaires"][$name] = $group;
            if (array_key_exists("values", $notices)) {
                $group["records"] = array_map(array($this, 'getNoticeTpl'), $notices["values"]);
                $this->records = array_merge($this->records, $group["records"]);
                $this->formatedData["sommaires"][$name] = $group;
            } else {
                $this->getGroupedData($notices, $level + 1);
            }
        }
        //return $data;
    }
    protected function get_css_style()
    {
        global $opac_default_style;

        // récupération des fichiers de style commun
        $css = $this->get_css('common');

        // récupération des fichiers de style personnalisé
        $css .= $this->get_css($opac_default_style);
        return $css;
    }

    protected function get_css($directory)
    {
        global $base_path;
        global $opac_url_base;

        $css = '';
        $css_path = $base_path . "/opac_css/styles/" . $directory . "/dsi";
        if (is_dir($css_path)) {
            if (($dh = opendir($css_path))) {
                while (($css_file = readdir($dh)) !== false) {
                    if (filetype($css_path . "/" . $css_file) == 'file') {
                        if (substr($css_file, -4) == ".css") {
                            $css .= "<link rel='stylesheet' type='text/css' href='" . $opac_url_base . "styles/" . $directory . "/dsi/" . $css_file . "' title='lefttoright' />\n";
                        }
                    }
                }
                closedir($dh);
            }
        }
        return $css;
    }

    protected static function get_lang_messages($lang)
    {
        global $include_path;

        if (!isset(static::$lang_messages[$lang])) {
            $messages = new XMLlist($include_path . "/messages/" . $lang . ".xml", 0);
            $messages->analyser();
            static::$lang_messages[$lang] = $messages->table;
        }
        return static::$lang_messages[$lang];
    }

    public static function renderBannetteTpl($id, $data)
    {
        global $charset, $base_path;
        $requete = "SELECT * FROM bannette_tpl WHERE bannettetpl_id='" . $id . "' LIMIT 1 ";
        $result = pmb_mysql_query($requete);
        if (pmb_mysql_num_rows($result)) {
            $temp = pmb_mysql_fetch_object($result);
            $data = encoding_normalize::utf8_normalize($data);
            $temp->bannettetpl_tpl = encoding_normalize::utf8_normalize($temp->bannettetpl_tpl);

            //Remplacement du lien de désinscription pour qu'il soit compatible avec la nouvelle DSI
            $regex = "/<a[^>]*href='[^']*(?:lvl=bannette_unsubscribe)[^']*'>[^<]*<\/a>/";
            $temp->bannettetpl_tpl = preg_replace($regex, '!!subscriber_unsubscribe_link!!', $temp->bannettetpl_tpl);

            $template_path = $base_path . '/temp/' . LOCATION . '_bannette_tpl_' . $id;
            file_put_contents($template_path, $temp->bannettetpl_tpl);
            $H2o = H2o_collection::get_instance($template_path);
            $data_to_return = $H2o->render($data);

            if ($charset != "utf-8") {
                $data_to_return = encoding_normalize::utf8_decode($data_to_return);
            }
            return $data_to_return;
        }
    }
}
