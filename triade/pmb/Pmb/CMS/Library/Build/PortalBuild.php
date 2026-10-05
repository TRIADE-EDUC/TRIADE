<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PortalBuild.php,v 1.13.2.1.2.3 2025/06/03 06:32:09 dgoron Exp $

namespace Pmb\CMS\Library\Build;

use Pmb\CMS\Models\PageModel;
use Pmb\CMS\Models\PortalModel;
use Pmb\CMS\Models\LayoutContainerModel;
use Pmb\CMS\Models\LayoutElementModel;

class PortalBuild
{
    /**
     *
     * @var PageModel|null
     */
    public $pageModel = null;

    /**
     *
     * @var PortalModel|null
     */
    private $portalModel = null;

    /**
     *
     * @var PortalDocument|null
     */
    private $portalDocument = null;

    /**
     *
     * @var array
     */
    private $headers = [
        'add' => [],
        'replace' => []
    ];

    public function __construct()
    {
        $this->setPortalModel(PortalModel::getPortal());
    }

    /**
     *
     * @param string $html
     * @return string
     */
    public function transformHTML(string $html)
    {
        $this->portalDocument = new PortalDocument();
        $this->portalDocument->loadHTML($html);

        $page = $this->portalModel->getCurrentPage();

        $layoutContainer = $page->generateTree();
        $zone = ZoneBuild::getInstance($layoutContainer, $this->portalDocument);
        $node = $zone->buildNode();
        if (is_object($node)) {
            $node->setIdAttribute("id", true);
        }

        $body = $this->portalDocument->getBody();
        if (is_object($body)) {
            if (is_object($node)) {
                if ($body->hasChildNodes()) {
                    $body->insertBefore($node, $body->firstChild);
                } else {
                    $body->appendChild($node);
                }
            }
            if($page->name){
                $body->setAttribute("data-cms-page", $page->name);
            }
            if($page->gabaritLayout->name){
                $body->setAttribute("data-cms-model", $page->gabaritLayout->name);
            }
        }

        $this->parse($layoutContainer);
        $this->insertHeaders();

        return $this->portalDocument->saveHTML();
    }

    /**
     *
     * @param LayoutContainerModel $layoutContainer
     * @return null
     */
    protected function parse(LayoutContainerModel $layoutContainer)
    {
        $children = $layoutContainer->getChildren();
        if (empty($children)) {
            return;
        }

        $idPrevious = '';
        for ($i = 0; $i < count($children); $i ++) {
            $element = $this->buildElementFromLayout($children[$i]);
            $node = $element->buildNode();
            if (empty($node) || false === $node) {
                continue;
            }

            $node->setIdAttribute("id", true);
            $node->setAttribute("data-name", addslashes($children[$i]->name));
            $this->portalDocument->insertDomNode($node, $layoutContainer->getSemantic()->getIdTag(), $idPrevious);

            if ($children[$i] instanceof LayoutContainerModel) {
                $this->parse($children[$i]);
            }

            if ($children[$i]->isHidden) {
                $this->portalDocument->isDeleted($children[$i]->getSemantic()->getIdTag());
            }

            $this->addHeaders($element->getHeaders());
            $idPrevious = $children[$i]->getSemantic()->getIdTag();
        }
    }

    /**
     * Ajout des headers des cadres dans la page
     */
    protected function insertHeaders()
    {
        if (count($this->headers['add'])) {
            foreach ($this->headers['add'] as $header) {
                if (empty($header)) {
                    continue;
                }
                $this->portalDocument->addHeader($header);
            }
        }

        if (count($this->headers['replace'])) {
            foreach ($this->headers['replace'] as $header) {
                if (empty($header)) {
                    continue;
                }
                $this->portalDocument->replaceHeader($header);
            }
        }
    }

    /**
     *
     * @return \Pmb\CMS\Models\PortalModel
     */
    public function getPortalModel()
    {
        return $this->portalModel;
    }

    /**
     *
     * @param PortalModel $portalModel
     * @return \Pmb\CMS\Models\PortalModel
     */
    public function setPortalModel(PortalModel $portalModel)
    {
        $this->portalModel = $portalModel;
        return $this->portalModel;
    }

    /**
     *
     * @return \Pmb\CMS\Library\Build\PortalDocument
     */
    public function getPortalDocument()
    {
        if (null !== $this->portalDocument) {
            return $this->portalDocument;
        }
    }

    /**
     *
     * @param PortalDocument $portalDocument
     * @return \Pmb\CMS\Library\Build\PortalDocument
     */
    public function setPortalDocument(PortalDocument $portalDocument)
    {
        $this->portalDocument = $portalDocument;
        return $this->portalDocument;
    }

    /**
     * Transformation d'un layout en Frame ou Zone
     *
     * @param LayoutContainerModel|LayoutElementModel $layout
     * @throws \InvalidArgumentException
     * @return FrameBuild|ZoneBuild
     */
    private function buildElementFromLayout($layout)
    {
        if ($layout instanceof LayoutContainerModel) {
            $element = ZoneBuild::getInstance($layout, $this->portalDocument);
        } elseif ($layout instanceof LayoutElementModel) {
            $element = FrameBuild::getInstance($layout, $this->portalDocument);
        } else {
            throw new \InvalidArgumentException("Invalid child instances");
        }

        return $element;
    }

    /**
     * Ajout des headers
     *
     * @param array $headers
     * @return void
     */
    private function addHeaders(array $headers)
    {
        $this->headers['add'] = array_unique(array_merge(
            $this->headers['add'],
            $headers['add'] ?? []
        ));

        $this->headers['replace'] = array_unique(array_merge(
            $this->headers['replace'],
            $headers['replace'] ?? []
        ));
    }
}
