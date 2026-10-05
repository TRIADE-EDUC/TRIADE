<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: GabaritLayoutModel.php,v 1.15 2023/11/28 15:21:06 qvarin Exp $

namespace Pmb\CMS\Models;

use Pmb\CMS\Library\ParserXML\Container;

class GabaritLayoutModel extends LayoutModel
{
    protected $name;

    protected $default = 0;

    protected $classement = "";

    public function isDefault()
    {
        return $this->default == 1;
    }

    public function getClassement()
    {
        return $this->classement;
    }

    public function setClassement(string $classement)
    {
        $this->classement = $classement;
    }

    /**
     *
     * @return \Pmb\CMS\Models\LayoutModel
     */
    public function resetLayout(string $layout)
    {
        if ("children" == $layout) {
            $this->children = [];
            $this->init();
        } else {
            $this->removeLayout($layout);
        }
    }

    /**
     *
     * @param array $data
     * @param GabaritLayoutModel $legacyLayout
     * @throws \Exception
     * @return boolean
     */
    public function setDataFromForm(array $data, $legacyLayout = null)
    {
        if (!empty($data['name'])) {
            $this->name = trim($data['name']);
        } else {
            throw new \Exception("GabaritLayoutModel can't have an empty name");
        }

        if (!empty($data['default']) && intval($data['default']) == 1) {
            if ($this->portal->getDefaultGabarit()) {
                $this->portal->getDefaultGabarit()->default = 0;
            }
            $this->default = 1;
        }

        if (!empty($legacyLayout) && $legacyLayout instanceof GabaritLayoutModel) {
            $this->legacyLayout = $legacyLayout;
            $this->initLegacy();
        } else {
            $this->legacyLayout = null;
        }

        return true;
    }

    public function associatedPages(array $pages)
    {
        $portalPages = $this->portal->getPages();
        $index = count($portalPages);
        for ($i = 0; $i < $index; $i++) {
            if (in_array($portalPages[$i]->getId(), $pages)) {
                $portalPages[$i]->setGabarit($this);
            }
        }
    }

    public function disassociatedPages()
    {
        $portalPages = $this->portal->getPages();
        $index = count($portalPages);
        for ($i = 0; $i < $index; $i++) {
            if (!empty($portalPages[$i]->getGabaritLayout()) && $portalPages[$i]->getGabaritLayout()->getId() == $this->getId()) {
                $portalPages[$i]->unsetGabarit();
            }
        }
    }

    public function init()
    {
        if (empty($this->children)) {
            $container = new Container();
            $this->children = $this->portal->fetchTree($container->zone);
        }
    }
}
