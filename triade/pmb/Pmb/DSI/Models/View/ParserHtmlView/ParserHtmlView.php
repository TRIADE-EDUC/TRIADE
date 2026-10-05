<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ParserHtmlView.php,v 1.4 2023/10/11 14:54:08 jparis Exp $

namespace Pmb\DSI\Models\View\ParserHtmlView;

use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\RootView;

class ParserHtmlView extends RootView
{
    public const DEFAULT_PARAMETERS_TEMPLATE = [
        'level' => 6,
        'expl' => 0,
        'show_explnum' => 1,
    ];

    protected $html;

    protected $templates;

    public function render($item, int $entityId, int $limit, string $context)
    {
        $data = $this->getDataFromContext($item, $context);
        if (empty($data)) {
            return "";
        }

        global $use_opac_url_base, $use_dsi_diff_mode;
        $use_opac_url_base = 1;
        $use_dsi_diff_mode = 1;

        $this->filterData($data, $entityId);
        $this->limitData($data, $limit);

        $this->html = "";
        foreach ($data as $id => $value) {
            $id = intval($id);
            if (empty($this->settings->template)) {
                $this->html .= $this->generateDefaultTemplate($id);
            } else {
                $noticeTemplateGen = new \notice_tpl_gen($this->settings->template);
                $this->html .= $noticeTemplateGen->build_notice($id);
            }
        }
        return $this->html;
    }

    public function preview($item, int $entityId, int $limit, string $context)
    {
        return $this->formatHTMLPreview($this->render($item, $entityId, $limit, $context));
    }

    private function generateDefaultTemplate(int $noticeId)
    {
        $query = "SELECT notice_id, niveau_biblio FROM notices WHERE notice_id = {$noticeId}";
        $result = pmb_mysql_query($query);

        $template = "";
        if ($result && pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_assoc($result);

            global $opac_url_base;
            $noticeLink = $opac_url_base."index.php?database=".DATA_BASE."&lvl=notice_display&id={$row['notice_id']}";

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
        return $template."\r\n";
    }

    /**
     * Permet de fournir des donnees pour le formulaire
     *
     * @return array
     */
    public function getFormData()
    {
        $this->fetchTemplates();
        return array_merge(
            parent::getFormData(),
            [
                "templates" => $this->templates,
            ]
        );
    }

    /**
     * Recupere les templates de notices definis en Editions
     *
     * @return void
     */
    protected function fetchTemplates()
    {
        if (isset($this->templates)) {
            return false;
        }


        $this->templates = [];
        $query = "SELECT notpl_id, if (notpl_comment != '', concat(notpl_name,'. ',notpl_comment), notpl_name) AS nom
                FROM notice_tpl ORDER BY notpl_name";
        $result = pmb_mysql_query($query);

        if ($result && pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $this->templates[] = [
                    "value" => intval($row['notpl_id']),
                    "label" => $row['nom'],
                ];
            }
        }
    }
}
