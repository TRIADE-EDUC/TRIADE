<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Container.php,v 1.2 2023/11/28 15:21:07 qvarin Exp $

namespace Pmb\CMS\Library\ParserCMS;

use Pmb\CMS\Library\ParserXML\{
    Container as ParserXMLContainer,
    TreeElement as XMLTreeElement,
    Zone as XMLZone
};

class Container
{
    /**
     *
     * @var Zone|null
     */
    public $zone = null;

    /**
     * Nom du portail
     *
     * @var string
     */
    public $cmsName = "";

    /**
     * Id du portail
     *
     * @var int
     */
    public $cmsId = 0;

    /**
     *
     * @var ParserCMS|null
     */
    public $parserCMS = null;

    /**
     *
     * @var ParserXMLContainer|null
     */
    protected $parserXMLContainer = null;

    private $index = array();

    public function __construct()
    {
        $this->parserXMLContainer = new ParserXMLContainer();
        $this->fetchCMS();
        $this->parseCMS();
    }

    private function parseCMS()
    {
        $this->parserCMS = new ParserCMS($this->cmsId);
        $this->zone = $this->parseElement($this->parserCMS->zone);
        $this->checkRequirementElement($this->parserXMLContainer->zone);
    }

    private function parseElement(array $element)
    {
        if (strpos($element['build_obj'], 'cms_module_') === 0) {
            return $this->createFrame($element);
        }

        $xmlElement = $this->parserXMLContainer->getElementById($element['build_obj']);
        if (is_a($xmlElement, \Pmb\CMS\Library\ParserXML\Frame::class)) {
            return $this->createFrame($element);
        }

        if (!$this->parserXMLContainer->getElementById($element['build_obj']) && empty($element['children'])) {
            return $this->createFrame($element);
        }

        $zone = $this->createZone($element);
        foreach ($element['children'] as $child) {
            $zone->appendChild($this->parseElement($child));
        }

        return $zone;
    }

    /**
     *
     * @param array $element
     * @return Zone
     */
    public function createZone(array $data): Zone
    {
        if (empty($data['build_obj'])) {
            throw new \InvalidArgumentException("build_obj not found on data");
        }

        $zone = new Zone($data, $this);
        $this->index[$data["build_obj"]] = $zone;
        return $zone;
    }

    /**
     *
     * @param array $element
     * @return Frame
     */
    public function createFrame(array $data): Frame
    {
        if (empty($data['build_obj'])) {
            throw new \InvalidArgumentException("build_obj not found on data");
        }

        $frame = new Frame($data, $this);
        $this->index[$data["build_obj"]] = $frame;
        return $frame;
    }

    /**
     *
     * @param string $id
     * @return TreeElement
     */
    public function getElementById(string $id)
    {
        return isset($this->index[$id]) ? $this->index[$id] : null;
    }


    /**
     * @return int|false
     */
    private function fetchCMS()
    {
        $result = pmb_mysql_query(
            "SELECT id_cms, cms_name FROM cms
            WHERE cms_opac_default=1
            ORDER BY id_cms ASC
            LIMIT 1"
        );

        if (pmb_mysql_num_rows($result)) {
            $this->cmsId = intval(pmb_mysql_result($result, 0, 0));
            $this->cmsName = pmb_mysql_result($result, 0, 1);
        } else {
            throw new \Exception("CMS not found !");
        }
    }

    private function checkRequirementElement(XMLTreeElement $element)
    {
        if (null === $this->getElementById($element->id)) {
            $this->addRequirementElement($element);
        }

        if ($element instanceof XMLZone) {
            $child = $element->getFirstChild();
            while ($child) {
                $this->checkRequirementElement($child);
                $child = $child->getNext();
            }
        }
    }

    private function formatRequirementElement(XMLTreeElement $element)
    {
        $data = [
            'build_obj' => $element->id,
            'isHidden' => true
        ];

        if ($element instanceof XMLZone) {
            $data['children'] = [];
            $child = $element->getFirstChild();
            while ($child) {
                $data['children'][] = $this->formatRequirementElement($child);
                $child = $child->getNext();
            }
        }

        return $data;
    }

    private function addRequirementElement(XMLTreeElement $element)
    {
        if (null !== $this->getElementById($element->id)) {
            throw new \InvalidArgumentException("Element " . $element['build_obj'] . " already exists");
        }

        $idParent = 'container';
        if ($element->getParent()) {
            $idParent = $element->getParent()->id;
        }

        $element = $this->formatRequirementElement($element);
        $element = $this->parseElement($element);

        $parent = $this->getElementById($idParent);
        if ($parent instanceof Zone) {
            $parent->appendChild($element);
        } else {
            throw new \InvalidArgumentException('Parent of element ' . $element->id . ' not found !');
        }
    }
}
