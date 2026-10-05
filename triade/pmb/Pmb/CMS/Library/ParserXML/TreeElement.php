<?php

namespace Pmb\CMS\Library\ParserXML;

class TreeElement
{
    public $id;

    public $label = "";

    public $isHidden = false;

    /**
     *
     * @var Container|null
     */
    protected $document = null;

    /**
     *
     * @var TreeElement|null
     */
    private $before = null;

    /**
     *
     * @var TreeElement|null
     */
    private $after = null;

    /**
     *
     * @var Zone|null
     */
    private $parent = null;

    public function __construct(array $data, Container $document)
    {
        if (! isset($data['id'])) {
            throw new \InvalidArgumentException("id not found on data");
        }

        $this->document = $document;
        $this->id = $data["id"];
        $this->label = $data["label"] ?? "";
        if (isset($data["hidden"])) {
            $this->isHidden = $data["hidden"] === true;
        }
    }

    /**
     *
     * @param TreeElement $treeElement
     */
    public function setBefore(TreeElement $treeElement)
    {
        $this->before = $treeElement;
    }

    /**
     *
     * @param TreeElement $treeElement
     */
    public function setAfter(TreeElement $treeElement)
    {
        $this->after = $treeElement;
    }

    /**
     *
     * @param Zone $zone
     */
    public function setParent(Zone $zone)
    {
        $this->parent = $zone;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function unsetParent()
    {
        $this->parent = null;
    }

    public function unsetAfter()
    {
        $this->after = null;
    }

    public function unsetBefore()
    {
        $this->before = null;
    }

    /**
     *
     * @return TreeElement|null
     */
    public function getPrevious()
    {
        if (! isset($this->before)) {
            return null;
        }
        return $this->before;
    }

    /**
     *
     * @return TreeElement|null
     */
    public function getNext()
    {
        if (! isset($this->after)) {
            return null;
        }
        return $this->after;
    }
}
