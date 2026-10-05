<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PortalDocument.php,v 1.19.2.1 2025/01/08 09:51:43 jparis Exp $

namespace Pmb\CMS\Library\Build;

use Pmb\Common\Helper\HTML;

class PortalDocument extends \DOMDocument
{
    private const ROOT_CONTAINER_ID = "container";

    /**
     *
     * @var \DOMElement|null
     */
    public $substituteContainer = null;

    public $elementNodeContainer = array();

    protected $deleted = array();

    protected $firstChildByZone = array();

    /**
     *
     * @param string $version
     * @param string $encoding
     */
    public function __construct(string $version = "1.0", string $encoding = "")
    {
        global $charset;
        if (empty($encoding)) {
            $encoding = $charset;
        }
        parent::__construct($version, $encoding);
    }

    /**
     *
     * @param \DomNode $node
     * @param string $idParent
     * @param string $idPrevious
     * @return \DomNode
     */
    public function insertDomNode(\DomNode $node, string $idParent = '', string $idPrevious = ''): \DomNode
    {
        if ($idParent == self::ROOT_CONTAINER_ID) {
            $elementParent = $this->substituteContainer;
        } else {
            $elementParent = $this->getElementById($idParent);
            if (empty($elementParent)) {
                throw new \InvalidArgumentException("Parent element not found");
            }
        }

        if (! empty($this->elementNodeContainer[$idParent])) {
            $elementParent = $this->elementNodeContainer[$idParent];
        }

        if ($idParent == self::ROOT_CONTAINER_ID && empty($idPrevious)) {
            $elementPrevious = $elementParent->firstChild;
        } else {
            $elementPrevious = $this->getElementById($idPrevious);
        }

        $node = $elementParent->appendChild($node);
        if (! empty($elementPrevious)) {
            $node = $elementParent->insertBefore($elementPrevious, $node);
        } else {
            $this->firstChildByZone[$idParent] = $node->getAttribute('id');
        }

        return $node;
    }

    /**
     *
     * {@inheritdoc}
     * @see \DOMDocument::loadHTML()
     * @throws \InvalidArgumentException
     */
    public function loadHTML($source, $options = null): bool
    {
        $success = @parent::loadHTML(HTML::cleanHTML($source, $this->encoding), $options);
        if (!$success) {
            throw new \InvalidArgumentException("HTML could not be loaded");
        }

        $this->substituteContainer = $this->createElement('div');
        $this->substituteContainer = $this->getBody()->appendChild($this->substituteContainer);

        return $success;
    }

    /**
     *
     * @return \DomElement
     */
    public function getBody()
    {
        return $this->getElementsByTagName('body')->item(0);
    }

    /**
     *
     * @return \DOMNode
     */
    public function getHead()
    {
        return $this->getElementsByTagName('head')->item(0);
    }

    /**
     *
     * {@inheritdoc}
     * @see \DOMDocument::saveHTML()
     */
    #[\ReturnTypeWillChange]
    public function saveHTML($node = null)
    {
        $rootContainerNode = $this->getElementById(self::ROOT_CONTAINER_ID);
        $this->switchParent($this->substituteContainer, $rootContainerNode);
        $this->substituteContainer->parentNode->removeChild($this->substituteContainer);

        foreach ($this->deleted as $index => $id) {
            $element = $this->getElementById($id);
            if (!empty($element)) {
                // On évite de perdre le javascript
                $this->moveScript($element, $this->getBody());
                $element->parentNode->removeChild($element);
            }
        }

        /**
         * On ajoute l'attribut "defer" pour les scripts VueJS
         * Pour qu'il trouve leur <div> (Noeud DOM racine)
         *
         * Et on deplace les scripts[src="*"] pour les mettres dans la balise head
         *
         * @var \DOMNodeList $DomNodeList
         */
        $domNodeList = $this->getBody()->getElementsByTagName("script");

        $moveBefore = [];
        $index = count($domNodeList);
        for ($i = 0; $i < $index; $i++) {
            /**
             * @var \DomElement $domNode
             */
            $domNode = $domNodeList->item($i);

            if ($domNode->hasAttribute('src') && !$domNode->hasAttribute('defer')) {
                $value = $domNode->getAttribute('src');
                if (strpos($value, "vuejs") !== false) {
                    $domNode->setAttribute('defer', 'defer');
                } else {
                    $moveBefore[] = $domNode;
                }
            }
        }

        foreach ($moveBefore as $domNode) {
            $this->getHead()->appendChild($domNode);
        }

        return parent::saveHTML($node);
    }

    /**
     *
     * @param \DomNode $parentOld
     * @param \DomNode $newParent
     */
    public function switchParent(\DomNode $parentOld, \DomNode $newParent)
    {
        $newParent = $this->mergeDomNodeAttributes($parentOld, $newParent);
        while ($childNode = $parentOld->childNodes->item(0)) {
            $newParent->appendChild($childNode);
        }
    }

    /**
     *
     * @param \DomElement $nodeOld
     * @param \DomElement $nodeNew
     * @return \DomElement
     */
    public function mergeDomNodeAttributes(\DomNode $nodeOld, \DomNode $nodeNew): \DomNode
    {
        $index = count($nodeOld->attributes);
        for ($i = 0; $i < $index; $i ++) {
            /**
             * @var \DOMAttr $domAttr
             */
            $domAttr = $nodeOld->attributes->item($i);
            if ($domAttr->name == "id") {
                continue;
            }

            $value = $domAttr->value;
            if ($nodeNew->hasAttributes() && $nodeNew->hasAttribute($domAttr->name)) {
                $newValue = $nodeNew->getAttributeNode($domAttr->name)->value;
                switch ($domAttr->name) {
                    case "class":
                        if ($newValue != $value) {
                            $values = array_merge(explode(" ", $newValue), explode(" ", $domAttr->value));
                            $value = implode(" ", array_unique($values));
                        }
                        break;

                    default:
                        $value = $newValue;
                        break;
                }
            }
            $nodeNew->setAttribute($domAttr->name, $value);
        }
        return $nodeNew;
    }

    public function isDeleted($id_tag)
    {
        $this->deleted[] = $id_tag;
    }

    public function addHeader(string $header)
    {
        global $charset;
        if ($charset == "utf-8") {
            $header = "<?xml version='1.0' encoding='$charset'>" . $header;
        }

        $domDocument = new \domDocument();
        if (! @$domDocument->loadHTML($header)) {
            return false;
        }

        $head = $domDocument->getElementsByTagName("head")->item(0);
        for ($i = 0; $i < $head->childNodes->length; $i++) {
            $node = $this->importNode($head->childNodes->item($i), true);
            $this->getHead()->appendChild($node);
        }
    }

    public function replaceHeader(string $header)
    {
        global $charset;
        if ($charset == "utf-8") {
            $header = "<?xml version='1.0' encoding='$charset'>" . $header;
        }

        $domDocument = new \domDocument();
        if (! @$domDocument->loadHTML($header)) {
            return false;
        }

        $head = $domDocument->getElementsByTagName("head")->item(0);
        for ($i = 0; $i < $head->childNodes->length; $i++) {
            $newNode = $head->childNodes->item($i);
            $similarNode = $this->foundSimilarNode($newNode);

            if (!empty($similarNode)) {
                $similarNode->parentNode->removeChild($similarNode);
            }
            if (!empty($newNode)) {
                $this->getHead()->appendChild($this->importNode($newNode, true));
            }
        }
    }

    public function foundSimilarNode(\DomNode $node)
    {
        $elements = $this->getElementsByTagName($node->nodeName);

        if ($elements->length == 0) {
            return null;
        }

        $attibutes = $node->attributes ?? new \DOMNamedNodeMap();

        for ($i = 0; $i < $elements->length; $i++) {
            /**
             * @var \DomElement $element
             */
            $element = $elements->item($i);

            $match = true;

            if(count($attibutes)){
                for ($j = 0; $j < $attibutes->length; $j++) {
                    /**
                     * @var \DOMAttr $attibute
                     */
                    $attibute = $attibutes->item($j);
                    if ($attibute->name == "content" || $attibute->name == "value") {
                        continue;
                    }

                    if (
                        !$element->hasAttribute($attibute->name) ||
                        $element->getAttribute($attibute->name) != $attibute->value
                    ) {
                        $match = false;
                        break;
                    }
                }
            }

            if ($match) {
                return $element;
            }
        }

        return null;
    }

    /**
     * Permet de deplacer les scripts dans un nouveau conteneur
     *
     * @param \DOMElement $element
     * @param \DOMElement $parent
     * @return void
     */
    protected function moveScript(\DOMElement $element, \DOMElement $parent)
    {
        $domNodeList = $element->getElementsByTagName("script");
        for ($i=0; $i < $domNodeList->length; $i++) {
            $node = $domNodeList->item($i);
            if ($node) {
                $parent->appendChild($node);
            }
        }

        if ($element->hasChildNodes()) {
            for ($i = 0; $i < $element->childNodes->length; $i++) {
                $childNode = $element->childNodes->item($i);
                if ($childNode instanceof \DOMElement) {
                    $this->moveScript($childNode, $parent);
                }
            }
        }
    }
}
