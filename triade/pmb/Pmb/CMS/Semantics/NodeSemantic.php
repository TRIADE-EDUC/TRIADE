<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: NodeSemantic.php,v 1.2 2022/02/14 15:36:52 qvarin Exp $
namespace Pmb\CMS\Semantics;

use Pmb\CMS\Library\Build\PortalDocument;

interface NodeSemantic
{

    /**
     *
     * @return \DomNode
     */
    public function getContainerNode(): \DomNode;

    /**
     *
     * @return \DOMNode
     */
    public function getNode(): \DomNode;
}