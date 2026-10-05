<?php

namespace Pmb\CMS\Library\ParserXML;

class Zone extends TreeElement
{
    /**
     *
     * @var TreeElement|null
     */
    private $firstChild = null;

    /**
     *
     * @param TreeElement $treeElement
     */
    protected function setFirstChild(TreeElement $treeElement)
    {
        $this->firstChild = $treeElement;
    }

    /**
     *
     * @return TreeElement|null
     */
    public function getFirstChild()
    {
        return $this->firstChild;
    }

    public function unsetFirstChild()
    {
        $this->firstChild = null;
    }

    /**
     *
     * @param TreeElement $treeElement
     */
    public function appendChild(TreeElement $treeElement)
    {
        if (! isset($this->firstChild)) {
            $this->setFirstChild($treeElement);
        } else {
            $lastTreeElement = $this->getFirstChild();
            while (! empty($lastTreeElement->getNext())) {
                $lastTreeElement = $lastTreeElement->getNext();
            }
            $this->insertAfter($treeElement, $lastTreeElement->id);
        }
        $treeElement->setParent($this);
    }

    /**
     *
     * @param TreeElement $treeElement
     * @throws \Exception
     */
    public function removeChild(TreeElement $treeElement)
    {
        if (empty($treeElement->getParent()) || $treeElement->getParent()->id != $this->id) {
            throw new \InvalidArgumentException("TreeElement not children of zone(id={$this->id})");
        }

        $previous = $treeElement->getPrevious();
        $next = $treeElement->getNext();
        if (! empty($previous) || ! empty($next)) {
            if (empty($previous) && ! empty($next)) {
                // On supprime le premier enfant
                $next->unsetBefore();
                $this->setFirstChild($next);
            } elseif (! empty($previous) && empty($next)) {
                // On supprime le dernier enfant
                $previous->unsetAfter();
            } else {
                // On supprime enfant dans la chaine
                $previous->setAfter($next);
                $next->setBefore($previous);
            }
        } else {
            // On avait qu'un seul enfant
            $this->unsetFirstChild();
        }

        // On retire treeElement de notre chainage
        $treeElement->unsetAfter();
        $treeElement->unsetBefore();
        $treeElement->unsetParent();
    }

    /**
     *
     * @param TreeElement $treeElement
     *            Element que l'on place
     * @param string $idAfter
     *            Element qui se trouve après celui qu'on place
     */
    public function insertBefore(TreeElement $treeElement, string $idAfter)
    {
        $afterElement = $this->document->getElementById($idAfter);
        $previous = $afterElement->getPrevious();

        if (empty($previous)) {
            // On placement un TreeElement avant le premier enfant
            $treeElement->unsetBefore();
            $treeElement->setAfter($afterElement);
            $afterElement->setBefore($treeElement);
            $this->setFirstChild($treeElement);
        } else {
            $previous->setAfter($treeElement);
            $treeElement->setBefore($previous);
            $treeElement->setAfter($afterElement);
            $afterElement->setBefore($treeElement);
        }
        $treeElement->setParent($this);
    }

    /**
     *
     * @param TreeElement $treeElement
     *            Element que l'on place
     * @param string $idBefore
     *            Element qui se trouve avant celui qu'on place
     */
    public function insertAfter(TreeElement $treeElement, string $idBefore)
    {
        $previousElement = $this->document->getElementById($idBefore);
        $next = $previousElement->getNext();

        if (empty($next)) {
            // On placement un TreeElement en dernier
            $previousElement->setAfter($treeElement);
            $treeElement->setBefore($previousElement);
            $treeElement->unsetAfter();
        } else {
            $next->setBefore($treeElement);
            $treeElement->setAfter($next);
            $treeElement->setBefore($previousElement);
            $previousElement->setAfter($treeElement);
        }
        $treeElement->setParent($this);
    }

    public function getChildren()
    {
        $children = [];
        $child = $this->getFirstChild();

        while (! empty($child)) {
            $children[] = $child;
            $child = $child->getNext();
        }

        return $children;
    }
}
