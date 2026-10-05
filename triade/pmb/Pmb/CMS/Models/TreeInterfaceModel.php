<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TreeInterfaceModel.php,v 1.2 2023/11/28 15:21:06 qvarin Exp $
namespace Pmb\CMS\Models;

interface TreeInterfaceModel
{

    /**
     *
     * @return LayoutContainerModel[]|LayoutElementModel[]
     */
    public function getChildren(): array;
    
    /**
     *
     * @param int $index
     * @param LayoutContainerModel|LayoutElementModel $child
     */
    public function replaceChild(int $index, $child);
    
    /**
     *
     * @param LayoutContainerModel|LayoutElementModel $child
     */
    public function appendChild($child);
}