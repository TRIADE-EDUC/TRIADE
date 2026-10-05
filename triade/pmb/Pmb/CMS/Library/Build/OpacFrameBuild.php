<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OpacFrameBuild.php,v 1.5 2023/01/31 11:36:53 qvarin Exp $
namespace Pmb\CMS\Library\Build;

class OpacFrameBuild extends FrameBuild
{

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\CMS\Library\Build\FrameBuild::buildNode()
     */
    public function buildNode()
    {
        $semantic = $this->layoutElement->getSemantic();
        $elementNode = $this->portalDocument->getElementById($semantic->getIdTag()) ?? null;

        if (! empty($elementNode)) {
            $newElementNode = $this->portalDocument->importNode($semantic->getNode(), true);
            $containerNode = $semantic->getContainerNode();

            if (! empty($containerNode) && $containerNode->getAttribute('id') != $newElementNode->getAttribute('id')) {
                // On importe le noeud container et on récupère tout les enfants du cadre OPAC
                $containerNode = $this->portalDocument->importNode($containerNode, true);
                $this->portalDocument->switchParent($elementNode, $containerNode);
                $newElementNode->appendChild($containerNode);
                // Pour l'élément $semantic->getIdTag() on stock le noeud container
                $this->portalDocument->elementNodeContainer[$semantic->getIdTag()] = $containerNode;
            } else {
                $this->portalDocument->switchParent($elementNode, $newElementNode);
            }

            $parentNode = $elementNode->parentNode;
            $parentNode->removeChild($elementNode);
            $elementNode = $parentNode->appendChild($newElementNode);
        }
        return $elementNode;
    }
}