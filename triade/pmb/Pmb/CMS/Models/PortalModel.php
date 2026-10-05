<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PortalModel.php,v 1.24 2024/01/17 13:23:05 qvarin Exp $

namespace Pmb\CMS\Models;

use Pmb\Common\Helper\Helper;
use Pmb\CMS\Orm\VersionOrm;
use Pmb\CMS\Factories\PageFactory;
use Pmb\CMS\Orm\PortalOrm;
use Pmb\Common\Helper\Portal;
use Pmb\CMS\Semantics\HtmlSemantic;
use Pmb\CMS\Library\ParserXML\{
	Container,
	Zone,
	Frame,
	TreeElement
};
use Pmb\CMS\Library\ParserCMS\{
    Container as ContainerCMS,
    Zone as ZoneCMS,
    Frame as FrameCMS,
    TreeElement as TreeElementCMS
};

class PortalModel extends PortalRootModel
{
    /**
     *
     * @var string
     */
    protected $name = "";

    /**
     *
     * @var int
     */
    protected $idVersion = 0;

    /**
     *
     * @var null|bool
     */
    protected $isDefault = null;

    /**
     *
     * @var null|VersionOrm
     */
    protected $version = null;

    /**
     *
     * @var null|VersionOrm[]
     */
    protected $versions = null;

    /**
     *
     * @var PagePortalModel|PageFRBRModel[]
     */
    protected $pages = null;

    /**
     *
     * @var null|GabaritLayoutModel[]
     */
    protected $gabaritLayouts = null;

    public function __construct(int $idVersion = 0)
    {
        $this->idVersion = $idVersion;
        if (VersionOrm::exist($idVersion)) {
            $this->fetchData($idVersion);
        } else {
            $this->initNewPortal();
        }
    }

    protected function fetchData($idVersion)
    {
        $this->version = VersionOrm::findById($idVersion);
        $orm = $this->version->portal[0];

        $reflect = new \ReflectionClass($orm);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        foreach ($props as $prop) {
            if (! $prop->isStatic()) {
                if ($prop->getName() != "version" && ! method_exists($this, Helper::camelize("fetch_" . $prop->getName()))) {
                    $this->{Helper::camelize($prop->getName())} = $orm->{$prop->getName()};
                }
            }
        }

        if ("{" == substr($this->version->properties, 0, 1)) {
            $json = $this->version->properties;
        } else {
            $json = gzuncompress($this->version->properties);
        }

        $properties = json_decode($json, true);
        if (empty($properties)) {
            throw new \Exception("Version properties empty! (".json_last_error_msg().")");
        }

        LayoutElementModel::$classements = $properties["framesClassements"] ?? [];
        unset($properties["framesClassements"]);

        // On met les gabaritLayouts en premier
        uksort($properties, function ($a, $b) {
            return $b == "gabaritLayouts" ? 1 : -1;
        });

        $this->version->properties = $properties;
        $this->unserialize($this->version->properties, $this);
    }

    /**
     *
     * @return PagePortalModel|PageFRBRModel[]
     */
    public function getPages()
    {
        if (! isset($this->pages)) {
            if (! empty($this->version) && ! empty($this->version->properties['pages'])) {
                foreach ($this->version->properties['pages'] as $page) {
                    $this->pages[] = new $page['class']($page, $this);
                }
            }
        }
        return $this->pages;
    }

    /**
     *
     * @return GabaritLayoutModel[]
     */
    public function getGabaritLayouts()
    {
        if (! isset($this->gabaritLayouts)) {
            if (! empty($this->version) && ! empty($this->version->properties['gabaritLayouts'])) {
                foreach ($this->version->properties['gabaritLayouts'] as $gabaritLayout) {
                    $this->gabaritLayouts[] = new $gabaritLayout['class']($gabaritLayout, $this);
                }
            }
        }
        return $this->gabaritLayouts;
    }

    /**
     *
     * @param int $portal
     * @return VersionOrm[]
     */
    public static function getVersions(int $portal = 0): array
    {
        return VersionOrm::find("portal_num", $portal);
    }

    /**
     *
     * @return int
     */
    public static function getCurrentPortal(): int
    {
        $portalsOrm = PortalOrm::findAll();
        $index = count($portalsOrm);
        for ($i = 0; $i < $index; $i ++) {
            if ($portalsOrm[$i]->is_default == 1) {
                return $portalsOrm[$i]->id;
            }
        }
        return 0;
    }

    /**
     *
     * @return PortalModel[]
     */
    public static function getPortals(): array
    {
        $portals = [];
        $portalsOrm = PortalOrm::findAll();
        $index = count($portalsOrm);
        for ($i = 0; $i < $index; $i ++) {
            $portals[] = static::getPortal(0, $portalsOrm[$i]->id);
        }
        return $portals;
    }

    /**
     *
     * @param int $portal
     * @return int
     */
    public static function getIdVersion(int $portal): int
    {
        return PortalOrm::findById($portal)->version_num;
    }

    /**
     *
     * @param int $version
     * @param int $portal
     * @return PortalModel
     */
    public static function getPortal(int $version = 0, int $portal = 0): PortalModel
    {
        if ($version != 0 && ! VersionOrm::exist($version)) {
            $version = 0;
        }

        if (empty($version) && empty($portal)) {
            $portal = static::getCurrentPortal();
            $version = static::getIdVersion($portal);
        }

        if (empty($version) && ! empty($portal)) {
            $version = static::getIdVersion($portal);
        }

        if (empty($version)) {
            return new PortalModel();
        }

        return new PortalModel($version);
    }

    /**
     *
     * @return PagePortalModel|PageFRBRModel
     */
    public function getCurrentPage()
    {
        if (empty($this->getPages())) {
            return $this->generateDefaultPage();
        }
        $currentPage = PageFactory::getCurrentPage($this->getPages());
        return !empty($currentPage) ? $currentPage : $this->generateDefaultPage();
    }

    public function serialize()
    {
        $serialize = [
            "pages" => [],
            "gabaritLayouts" => [],
            "framesClassements" => LayoutElementModel::$classements,
        ];

        if (! empty($this->getGabaritLayouts())) {
            foreach ($this->getGabaritLayouts() as $gabaritLayout) {
                $serialize['gabaritLayouts'][] = $gabaritLayout->serialize();
            }
        }

        if (! empty($this->getPages())) {
            foreach ($this->getPages() as $page) {
                $serialize['pages'][] = $page->serialize();
            }
        }
        return $serialize;
    }

    /**
     * Initialise un nouveau portail
     */
    private function initNewPortal()
    {
        global $msg;
        $container = new Container();
        $this->gabaritLayouts[] = new GabaritLayoutModel($this, [
            "id" => 1,
            "name" => $msg['portal_default_gabarit_label'],
            "default" => 1,
            "class" => "Pmb\CMS\Models\GabaritLayoutModel",
            "children" => $this->fetchTree($container->zone)
        ]);
        $this->name = $msg['portal_default_label'];
        $this->isDefault = 1;
        $this->save();
    }

    /**
     * Recupère les enfants à tous les étages d'un TreeElement
     *
     * @param TreeElement|TreeElementCMS $element
     * @return array
     */
    public function fetchTree($element): array
    {
        if (!($element instanceof TreeElement) && !($element instanceof TreeElementCMS)) {
            throw new \InvalidArgumentException("element not a instance of TreeElement or TreeElementCMS");
        }

        $children = [];
        do {
            // On va chercher les enfants de l'element
            $child = $this->treeElementToArray($element);
            if (($element instanceof Zone) || ($element instanceof ZoneCMS)) {
                $firstChild = $element->getFirstChild();
                if (! empty($firstChild)) {
                    // Si il a un suivant, on le parcourt recursivement pour recuperer ses enfants
                    $child['children'] = $this->fetchTree($firstChild);
                }
            }
            $children[] = $child;
        } while ($element = $element->getNext());

        return $children;
    }

    /**
     * Format un TreeElement en tableau
     *
     * @param TreeElement|TreeElementCMS $element
     * @return array
     */
    private function treeElementToArray($element): array
    {
        if (!($element instanceof TreeElement) && !($element instanceof TreeElementCMS)) {
            throw new \InvalidArgumentException("element not a instance of TreeElement or TreeElementCMS");
        }

        $array = [
            'name' => $element->id,
            'class' => $this->getClassNameOfTreeElement($element),
            'isHidden' => $element->isHidden,
            'semantic' => [
                "class" => "Pmb\CMS\Semantics\HtmlSemantic",
                "id_tag" => $element->id,
                "tag" => "div",
                "classes" => []
            ],
        ];

        if (FrameOpacModel::class == $array['class']) {
            $array['classement'] = "OPAC";
        }

        return $array;
    }

    /**
     * Récupère la classe du model d'un TreeElement
     *
     * @param TreeElement|TreeElementCMS $element
     * @return string
     */
    private function getClassNameOfTreeElement($element): string
    {
        if (!($element instanceof TreeElement) && !($element instanceof TreeElementCMS)) {
            throw new \InvalidArgumentException("element not a instance of TreeElement or TreeElementCMS");
        }

        $classname = null;
        switch (true) {
            case $element instanceof Frame:
                $classname = \Pmb\CMS\Models\FrameOpacModel::class;
                break;

            case $element instanceof FrameCMS:
                if (strpos($element->id, 'cms_') !== false) {
                    $classname = \Pmb\CMS\Models\FrameCMSModel::class;
                } else {
                    $classname = \Pmb\CMS\Models\FrameOpacModel::class;
                }
                break;

            case $element instanceof Zone:
            case $element instanceof ZoneCMS:
                $classname = \Pmb\CMS\Models\ZoneOpacModel::class;
                break;

            default:
                throw new \InvalidArgumentException("element not a instance unkonwn TreeElement or TreeElementCMS");
        }

        return $classname;
    }

    public function fetchVersions()
    {
        if (! isset($this->versions)) {
            $this->versions = VersionOrm::find("portal_num", $this->id);
        }
        return $this->versions;
    }

    public function getDefaultGabarit()
    {
        foreach ($this->getGabaritLayouts() as $gabarit) {
            if ($gabarit->isDefault()) {
                return $gabarit;
            }
        }
        return null;
    }

    public function addPage($page)
    {
        $this->pages[] = $page;
    }

    public function removePage($id): bool
    {
        foreach ($this->pages as $key => $page) {
            if ($page->id == $id) {
                array_splice($this->pages, $key, 1);
                return true;
            }
        }
        return false;
    }

    public function addGabarit($gabarit)
    {
        $this->gabaritLayouts[] = $gabarit;
    }

    public function removeGabarit($id)
    {
        if (GabaritLayoutModel::exist($id)) {
            foreach ($this->gabaritLayouts as $key => $gabarit) {
                if ($gabarit->id == $id) {
                    array_splice($this->gabaritLayouts, $key, 1);
                    return true;
                }
            }
        }
        return false;
    }

    public function save()
    {
        $properties_serialised = \encoding_normalize::json_encode($this->serialize());
        if (empty($properties_serialised)) {
            throw new \Exception("Properties cannot be encoded in json (Error :".json_last_error_msg().")");
        }

        $newVersion = new VersionOrm();
        $newVersion->name = $this->name;
        $newVersion->last_version_num = !empty($this->version) ? $this->version->id : 0;
        $newVersion->create_at = (new \DateTime())->format("Y-m-d H:i:s");
        $newVersion->properties = gzcompress($properties_serialised);

        $portalOrm = new PortalOrm($this->getId());
        $portalOrm->name = $this->name;
        $portalOrm->is_default = $this->isDefault;
        $portalOrm->save();

        $this->id = $portalOrm->id;
        $newVersion->portal_num = $this->id;
        $newVersion->save();

        $portalOrm->version_num = $newVersion->id;
        $portalOrm->save();

        $this->version = $newVersion;
        return $newVersion->id;
    }

    /**
     *
     * @param string $type
     * @param string $subtype
     * @return \Pmb\CMS\Models\PagePortalModel
     */
    private function generateDefaultPage(string $type = "", string $subtype = "")
    {
        if (empty($type)) {
            $type = Portal::getTypePage();
        }
        if (empty($subtype)) {
            $subtype = Portal::getSubTypePage();
        }

        $page = new PagePortalModel($this, [
            "name" => Portal::getLabel($subtype),
            "type" => $type,
            "sub_type" => $subtype,
            "class" => "Pmb\CMS\Models\PagePortalModel",
            "parent_page" => [],
            "gabarit_layout" => [
                "class" => "Pmb\CMS\Models\GabaritLayoutModel",
                "id" => $this->getDefaultGabarit()->getId()
            ],
            "page_layout" => [],
            "conditions" => []
        ]);

        $this->pages[] = $page;
        $this->save();

        return $page;
    }

    /**
     *
     * @param array $list
     * @return string[]
     */
    public function getFrameList(): array
    {
        $listFrame = [];

        /**
         * On vas cherher les cadres OPAC
         */
        foreach ($this->getDefaultGabarit()->getAllFrames() as $frame) {
            if ($frame instanceof FrameOpacModel) {
                $listFrame[] = $frame->serialize();
            }
        }

        /**
         * On vas chercher les cadres portail
         */
        $parser = new \cms_modules_parser();
        foreach ($parser->get_cadres_list() as $cadre) {
            $idTag = $cadre->cadre_object . "_" . $cadre->id_cadre;

            $frameCms = new FrameCMSModel($this, [
                "name" => $cadre->cadre_name
            ], $this);
            $frameCms->setSemantic(new HtmlSemantic($this, [
                "idTag" => $idTag,
                "tag" => "div"
            ], $this));
            $listFrame[] = $frameCms->serialize();
        }
        return $listFrame;
    }

    public function getName()
    {
        return $this->name;
    }

    public function migration()
    {
        global $msg;

        $defaultGabarit = $this->getDefaultGabarit();
        if (null !== $defaultGabarit) {
            $defaultGabarit->default = 0;
        }

        $container = new ContainerCMS();
        $this->gabaritLayouts[] = new GabaritLayoutModel($this, [
            "name" => sprintf($msg['portal_migration'], "{$container->cmsName} - {$container->parserCMS->cmsVersion}"),
            "default" => 1,
            "class" => GabaritLayoutModel::class,
            "children" => $this->fetchTree($container->zone)
        ]);

        return $this->save();
    }
}
