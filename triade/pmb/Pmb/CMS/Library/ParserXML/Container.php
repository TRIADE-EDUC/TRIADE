<?php

namespace Pmb\CMS\Library\ParserXML;

class Container
{
    /**
     *
     * @var Zone|null
     */
    public $zone = null;

    /**
     *
     * @var \DOMXPath|null
     */
    private $xml = null;

    private $index = array();

    public function __construct()
    {
        $dom = new \DomDocument();
        $success = $dom->load(__DIR__ . "../../../../../includes/cms/cms_build/cms_build_id.xml");
        if (! $success) {
            throw new \Exception("cms_build_id.xml not found");
        }
        $this->xml = new \DOMXPath($dom);
        $this->parseXML();
    }

    private function parseXML()
    {
        // On va chercher le premier container dans le xml
        $results = $this->xml->query("//cms_object[@container='yes'][1]");
        if (count($results) == 0) {
            throw new \Exception("No root container found");
        }

        // On définis l'élément racine
        $cmsObjectRootElement = $results->item(0);

        $attributes = $this->getAttributesOfDomElement($cmsObjectRootElement);
        $this->zone = $this->createZone($attributes);
        static::searchChildrensOfParentZone($this->zone);
    }

    private function searchChildrensOfParentZone(Zone $parentZone)
    {
        // On va chercher le premier enfants du parent
        $results = $this->xml->query("//cms_object[parent='{$parentZone->id}' and child_before='']");
        if (count($results) == 0) {
            // Aucun enfant
            return;
        }

        $cmsObjectFirstChildren = $results->item(0);
        if (null === $cmsObjectFirstChildren || !$cmsObjectFirstChildren instanceof \DOMElement) {
            // Aucun enfant
            return;
        }

        $attributes = $this->getAttributesOfDomElement($cmsObjectFirstChildren);
        $treeElement = $this->createTreeElement($attributes);
        if ($treeElement instanceof Zone) {
            $this->searchChildrensOfParentZone($treeElement);
        }
        $parentZone->appendChild($treeElement);

        $elements = $cmsObjectFirstChildren->getElementsByTagName("child_after");
        if (count($elements) == 0) {
            throw new \Exception("element with tag name 'child_after' not found");
        }

        $element = $elements->item(0);
        if (empty($element->nodeValue)) {
            // Aucun enfant suivant
            return;
        }

        $results = $this->xml->query("//cms_object[@id='{$element->nodeValue}']");
        if (count($results) == 0) {
            throw new \Exception("cms_object not found (id={$element->nodeValue})");
        }

        $finish = false;
        while (! $finish) {
            $cmsObjectNextChildren = $results->item(0);
            if (null === $cmsObjectNextChildren || !$cmsObjectNextChildren instanceof \DOMElement) {
                // Aucun enfant
                $finish = true;
                continue;
            }

            $attributes = $this->getAttributesOfDomElement($cmsObjectNextChildren);
            $treeElement = $this->createTreeElement($attributes);
            if ($treeElement instanceof Zone) {
                $this->searchChildrensOfParentZone($treeElement);
            }
            $parentZone->appendChild($treeElement);
            $elements = $cmsObjectNextChildren->getElementsByTagName("child_after");
            if (count($elements) == 0) {
                // auncun noeud child_after
                $finish = true;
            }

            $element = $elements->item(0);
            if (empty($element->nodeValue)) {
                // Aucun enfant suivant
                $finish = true;
            } else {
                $results = $this->xml->query("//cms_object[@id='{$element->nodeValue}']");
                if (count($results) == 0) {
                    $finish = true;
                }
            }
        }
    }

    private function getAttributesOfDomElement(\DOMElement $element)
    {
        $attributes = array();
        if ($element->hasAttributes()) {
            foreach ($element->attributes as $attribute) {
                $attributes[$attribute->nodeName] = $attribute->nodeValue;
            }
        }
        return $attributes;
    }

    private function createTreeElement(array $data): TreeElement
    {
        if (! empty($data["zone"]) || ! empty($data["container"])) {
            return $this->createZone($data);
        } else {
            return $this->createFrame($data);
        }
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
     *
     * @param \DOMElement $element
     * @return Zone
     */
    public function createZone(array $data): Zone
    {
        if (empty($data['id'])) {
            throw new \InvalidArgumentException("id not found on data");
        }

        $zone = new Zone($data, $this);
        $this->index[$data["id"]] = $zone;
        return $zone;
    }

    /**
     *
     * @param \DOMElement $element
     * @return Frame
     */
    public function createFrame(array $data): Frame
    {
        if (empty($data['id'])) {
            throw new \InvalidArgumentException("id not found on data");
        }

        $frame = new Frame($data, $this);
        $this->index[$data["id"]] = $frame;
        return $frame;
    }
}
