<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ExportView.php,v 1.3.2.1 2024/10/01 08:24:24 jparis Exp $

namespace Pmb\DSI\Models\View\ExportView;

use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\RootView;

class ExportView extends RootView
{
    protected static $count = 0;

    protected static $exports = null;

    protected static $output = [];

    public const EXPORT_LINKS = [
        "mere" => false,
        "fille" => false,
        "horizontale" => false,
        "notice_mere" => false,
        "notice_fille" => false,
        "notice_horizontale" => false,
    ];

    public const EXPORT_LINKS_SERIES = [
        "bull_link" => true,
        "art_link" => true,
        "perio_link" => true,
        "bulletinage" => false,
        "notice_perio" => false,
        "notice_art" => false,
    ];

    public function preview(Item $item, int $entityId, int $limit, string $context)
    {
        return $this->render($item, $entityId, $limit, $context);
    }

    public function render(Item $item, int $entityId, int $limit, string $context)
    {
        if (! isset($this->settings->entityType)) {
            return [
                'nomfichier' => $this->makeFilename(),
                'contenu' => "",
            ];
        }

        $data = $this->getDataFromContext($item, $context);
        if (empty($data)) {
            return [
                'nomfichier' => $this->makeFilename(),
                'contenu' => "",
            ];
        }

        $this->fetchCurrentParametersExport();
        return [
            'nomfichier' => $this->makeFilename(),
            'minetype' => static::$output['MIMETYPE'] ?? "",
            'contenu' => $this->makeExport($data ?? []),
        ];
    }

    /**
     * Permet de fournir des donnees pour le formulaire
     *
     * @return array
     */
    public function getFormData()
    {
        $this->parseAllItem();
        return array_merge(parent::getFormData(), [
            "exports" => static::$exports,
        ]);
    }

    protected function makeFilename()
    {
        $this->fetchCurrentParametersExport();
        $origine = str_replace([" ", "0."], "", microtime());
        return "export_{$origine}." . static::$output['SUFFIX'];
    }

    protected function makeExport($data)
    {
        $item = $this->getCurrentItemExport();
        return cree_export_notices(
            array_keys($data),
            \start_export::get_id_by_path($item['params']['PATH']),
            1,
            $this->iniParameters()
        );
    }

    protected function iniParameters()
    {
        $parameters = [
            'genere_lien' => $this->getSetting('exportGenerateLink', false),
            'map' => false,
        ];

        $exportLinks = $this->getSetting('exportLinks', Helper::toObject(static::EXPORT_LINKS));
        $exportLinksSeries = $this->getSetting('exportLinksSeries', Helper::toObject(static::EXPORT_LINKS_SERIES));

        foreach (array_keys(static::EXPORT_LINKS) as $exportLink) {
            $parameters[$exportLink] = $exportLinks->{$exportLink};
        }

        foreach (array_keys(static::EXPORT_LINKS_SERIES) as $exportLink) {
            $parameters[$exportLink] = $exportLinksSeries->{$exportLink};
        }

        return $parameters;
    }

    protected function getCatalogPath()
    {
        global $base_path;

        $file = "{$base_path}/admin/convert/imports/catalog.xml";
        if (is_file("{$base_path}/admin/convert/imports/catalog_subst.xml")) {
            $file = "{$base_path}/admin/convert/imports/catalog_subst.xml";
        }
        return $file;
    }

    protected function parseAllItem()
    {
        if (null === static::$exports) {
            static::$exports = [];
            _parser_($this->getCatalogPath(), [
                "ITEM" => static::class . "::parseItem",
            ], "CATALOG");
        }
    }

    /**
     * Retourne l'item a utlise du fichier catalog.xml
     *
     * @param array $param
     */
    protected function getCurrentItemExport()
    {
        $this->parseAllItem();
        $index = array_search($this->getSetting('exportFormat'), array_column(static::$exports, 'index'), true);
        return static::$exports[$index] ?? null;
    }

    /**
     * Parse la liste des items contenue dans le fichier catalog.xml
     *
     * @param array $param
     */
    public static function parseItem($param)
    {
        $t['name'] = $param['EXPORTNAME'] ?? '';
        $t['index'] = static::$count;
        $t['params'] = $param;

        static::$count ++;
        if (isset($param['EXPORT']) && $param['EXPORT'] == "yes") {
            static::$exports[] = $t;
        }
    }

    protected function fetchCurrentParametersExport()
    {
        global $base_path;

        $item = $this->getCurrentItemExport();
        if (null !== $item) {
            _parser_("{$base_path}/admin/convert/imports/{$item['params']['PATH']}/params.xml", [
                "OUTPUT" => static::class . "::parseOutput",
            ], "PARAMS");
        }
    }

    public static function parseOutput($param)
    {
        static::$output = $param;
    }

    public function getParameterExportByPath($path)
    {
        $this->parseAllItem();

        foreach (static::$exports as $export) {
            if ($export['params']['PATH'] == $path) {
                return $export;
            }
        }

        return 0;
    }
}
