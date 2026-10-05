<?php

namespace Pmb\DSI\Models\View\RssView\EntitiesTemplates;

use Pmb\Common\Helper\Directory;
use Pmb\Common\Helper\HelperEntities;

class RootTemplate implements TemplateInterface
{
    public const CONST_TYPE = 0;

    protected $entityNamespace = null;

    public const TITLE_TEMPLATE = "";

    public const DESCRIPTION_TEMPLATE = "";

    public function getTitle($tplTitle)
    {
        $h2o = \H2o_collection::get_instance($this->getTemplatePath($tplTitle, static::TITLE_TEMPLATE));
        return $h2o->render([
            $this->getEntityNamespace() => $this->instance,
        ]);
    }

    public function getPubDate()
    {
        return "";
    }

    public function getLink($tplLink)
    {
        return "";
    }

    public function getDescription($tplDescription)
    {
        $h2o = \H2o_collection::get_instance($this->getTemplatePath($tplDescription, static::DESCRIPTION_TEMPLATE));
        return $h2o->render([
            $this->getEntityNamespace() => $this->instance,
        ]);
    }

    protected function getTemplatePath(string $tplDir, string $tplName)
    {
        $templates = static::getTemplateDirectories();

        if (defined('GESTION')) {
            $path = "./opac_css/includes/templates/dsi";
        } else {
            $path = "./includes/templates/dsi";
        }

        return "$path/{$this->getEntityNamespace()}/{$templates[$tplDir]}/{$tplName}";
    }

    public static function getTemplateDirectories()
    {
        $entitiesNamespace = HelperEntities::get_entities_namespace();
        $entitiesNamespace = array_map("strtolower", $entitiesNamespace);

        if (empty($entitiesNamespace) || empty($entitiesNamespace[static::CONST_TYPE])) {
            return [];
        }

        $dirName = $entitiesNamespace[static::CONST_TYPE];

        if (defined('GESTION')) {
            $path = "./opac_css/includes/templates/dsi/{$dirName}";
        } else {
            $path = "./includes/templates/dsi/{$dirName}";
        }

        $dirs = Directory::getNameDirectories($path);
        return $dirs ?? [];
    }

    protected function getEntityNamespace()
    {
        if (null === $this->entityNamespace) {
            $entitiesNamespace = HelperEntities::get_entities_namespace();
            $entitiesNamespace = array_map("strtolower", $entitiesNamespace);
            $this->entityNamespace = $entitiesNamespace[static::CONST_TYPE];
        }
        return $this->entityNamespace;
    }

    public static function getTemplates()
    {
        global $msg;

        return [
            "tplTitle" => static::getTemplateDirectories(),
            "tplDescription" => static::getTemplateDirectories()
        ];
    }
}