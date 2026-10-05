<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ParserCMS.php,v 1.2 2023/11/28 15:21:07 qvarin Exp $

namespace Pmb\CMS\Library\ParserCMS;

class ParserCMS
{
    /**
     * Resultat du parse de la table cms_build
     *
     * @var array
     */
    public $zone = null;

    /**
     * Version du CMS
     *
     * @var int
     */
    public $cmsVersion = 0;

    public function __construct(int $cmsId)
    {
        $this->cmsVersion = $this->fetchLastVersionCMS($cmsId);
        if (!$this->cmsVersion) {
            throw new \InvalidArgumentException("CMS {$cmsId} not found or no version");
        }
        $this->parseCMS();
    }

    /**
     * Retourne la dernier version du CMS par defaut
     *
     * @return int
     */
    private function fetchLastVersionCMS(int $cmsId)
    {
        $result = pmb_mysql_query(
            "SELECT id_version
            FROM cms_version
            WHERE version_cms_num={$cmsId}
            ORDER BY id_version
            DESC LIMIT 1"
        );

        if (pmb_mysql_num_rows($result)) {
            return intval(pmb_mysql_result($result, 0, 0));
        }
        return false;
    }

    /**
     * Parse la table cms_build et defini la propriete zone
     *
     * @return void
     */
    private function parseCMS()
    {
        $this->zone = [
            "build_obj" => "container",
            "children" => array_merge_recursive(
                $this->searchChildrenOfParent("container"),
                $this->searchChildrenOfParent("")
            )
        ];
    }

    /**
     * Recherche les enfants d'un parent
     *
     * @param string $idParent
     * @return array
     */
    private function searchChildrenOfParent(string $idParent)
    {
        $elements = [
            "cadres" => [],
            "fixed" => []
        ];

        $result = pmb_mysql_query(
            "SELECT build_obj, build_fixed, build_child_before, build_child_after
            FROM cms_build
            LEFT JOIN cms_cadres ON build_obj=CONCAT(cadre_object,'_',id_cadre) AND cadre_memo_url=1
            WHERE build_version_num = {$this->cmsVersion} AND build_parent = '". addslashes($idParent) ."'"
        );

        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                if ($row['build_fixed']) {
                    $elements['fixed'][] = $row;
                } else {
                    $elements['cadres'][] = $row;
                }
            }
        }

        $children = $this->addDynamicChildren(
            $this->orderChilren($elements['fixed']),
            $elements['cadres']
        );

        foreach ($children as $key => $child) {
            $children[$key]['children'] = $this->searchChildrenOfParent($child['build_obj']);
        }

        return $children;
    }

    /**
     * Retourne l'enfant suivant en fonction de l'enfant courant
     * Retourne null s'il n'y a pas d'enfant
     *
     * @param array $previousChild
     * @param array $children
     * @return array|null
     */
    private function searchNextChild(array $previousChild, array &$children)
    {
        foreach ($children as $key => $child) {
            if ($child['build_child_before'] == $previousChild['build_obj']) {
                $childFound = $child;
                unset($children[$key]);
                return $childFound;
            }
        }

        foreach ($children as $key => $child) {
            if ($child['build_obj'] == $previousChild['build_child_after']) {
                $childFound = $child;
                unset($children[$key]);
                return $childFound;
            }
        }

        return null;
    }

    /**
     * Trie les enfants fixes en fonction du build_child_before/build_child_after
     *
     * @param array $children
     * @return array
     */
    private function orderChilren(array &$children)
    {
        $firstChild = null;
        foreach ($children as $key => $child) {
            if (empty($child['build_child_before'])) {
                $firstChild = $child;
                unset($children[$key]);
                break;
            }
        }

        if (null === $firstChild) {
            return [];
        }

        $childrenSorted = [$firstChild];
        $currentChild = $firstChild;
        while ($nextChild = $this->searchNextChild($currentChild, $children)) {
            $childrenSorted[] = $nextChild;
            $currentChild = $nextChild;
        }

        return $childrenSorted;
    }

    /**
     * Recherche les enfants positionnés avant l'enfant
     *
     * @param array $previousChild
     * @param array $children
     * @param boolean $recursive
     * @return array
     */
    private function searchBeforeChildren(array $previousChild, array &$children, bool $recursive = true)
    {
        $childrenBeforeFound = [];
        foreach ($children as $key => $child) {
            if ($child['build_child_after'] == $previousChild['build_obj']) {
                $childrenBeforeFound[] = $child;
                unset($children[$key]);
            }
        }

        foreach ($children as $key => $child) {
            if ($child['build_obj'] == $previousChild['build_child_before']) {
                $childrenBeforeFound[] = $child;
                unset($children[$key]);
            }
        }

        if ($recursive) {
            $cloneChildrenBeforeFound = $childrenBeforeFound;
            foreach ($childrenBeforeFound as $childBefore) {
                $this->insertBeforeChildren(
                    $childBefore['build_obj'],
                    $cloneChildrenBeforeFound,
                    $this->searchBeforeChildren($childBefore, $children, $recursive)
                );
            }
            $childrenBeforeFound = $cloneChildrenBeforeFound;
        }

        return $childrenBeforeFound;
    }

    /**
     * Insert les enfants avant l'enfant
     *
     * @param string $buildObj
     * @param array $children
     * @param array $childrenBefore
     * @return void
     */
    private function insertBeforeChildren(string $buildObj, array &$children, array $childrenBefore)
    {
        $index = null;
        foreach ($children as $key => $child) {
            if ($child['build_obj'] == $buildObj) {
                $index = $key;
                break;
            }
        }

        if (null === $index) {
            throw new \InvalidArgumentException("$buildObj not found");
        }

        foreach ($childrenBefore as $childBefore) {
            array_splice($children, $index++, 0, [$childBefore]);
        }
    }

    /**
     * Recherche les enfants positionnés apres l'enfant
     *
     * @param array $previousChild
     * @param array $children
     * @param boolean $recursive
     * @return array
     */
    private function searchAfterChildren(array $previousChild, array &$children, bool $recursive = true)
    {
        $childrenAfterFound = [];
        foreach ($children as $key => $child) {
            if ($child['build_child_before'] == $previousChild['build_obj']) {
                $childrenAfterFound[] = $child;
                unset($children[$key]);
            }
        }

        foreach ($children as $key => $child) {
            if ($child['build_obj'] == $previousChild['build_child_after']) {
                $childrenAfterFound[] = $child;
                unset($children[$key]);
            }
        }

        if ($recursive && !empty($childrenAfterFound)) {
            $cloneChildrenAfterFound = $childrenAfterFound;
            foreach ($childrenAfterFound as $childAfter) {
                $this->insertAfterChildren(
                    $childAfter['build_obj'],
                    $cloneChildrenAfterFound,
                    $this->searchAfterChildren($childAfter, $children, $recursive)
                );
            }
            $childrenAfterFound = $cloneChildrenAfterFound;
        }

        return $childrenAfterFound;
    }

    /**
     * Insert les enfants apres l'enfant
     *
     * @param string $buildObj
     * @param array $children
     * @param array $childrenAfter
     * @return void
     */
    private function insertAfterChildren(string $buildObj, array &$children, array $childrenAfter)
    {
        $index = null;
        foreach ($children as $key => $child) {
            if ($child['build_obj'] == $buildObj) {
                $index = $key;
                break;
            }
        }

        if (null === $index) {
            throw new \InvalidArgumentException("$buildObj not found");
        }

        foreach ($childrenAfter as $childAfter) {
            array_splice($children, $index+1, 0, [$childAfter]);
            $index++;
        }
    }

    /**
     * Recherche l'index de l'enfant
     *
     * @param string $buildObj
     * @param array $children
     * @return int|null
     */
    private function searchIndex(string $buildObj, array $children)
    {
        foreach ($children as $key => $child) {
            if ($child['build_obj'] == $buildObj) {
                return $key;
            }
        }

        return null;
    }

    /**
     * On essai d'inserer les enfants dans le tableau en fonction du premier build_child_before
     * et du dernier build_child_after sinon on les met à la fin
     *
     * @param array $childrenSegmet
     * @param array $children
     * @return void
     */
    private function tryInsertChildren(array $childrenSegmet, array &$children) {
        $startBuildObj = $childrenSegmet[0]['build_child_before'];
        $index = $this->searchIndex($startBuildObj, $children);
        if (null !== $index) {
            $this->insertAfterChildren($children[$index]['build_obj'], $children, $childrenSegmet);
            return;
        }

        $endBuildObj = $childrenSegmet[array_key_last($childrenSegmet)]['build_child_after'];
        $index = $this->searchIndex($endBuildObj, $children);
        if (null !== $index) {
            $this->insertBeforeChildren($children[$index]['build_obj'], $children, $childrenSegmet);
            return;
        }

        // On sait pas ou les mettre, on les met à la fin
        foreach ($childrenSegmet as $child) {
            $children[] = $child;
        }
    }

    /**
     * On fait le chainage en fonction du build_child_before/build_child_after et on applique dans le tableau
     *
     * @param array $currentChild
     * @param array $children
     * @param array $chainedChildren
     * @return void
     */
    private function makeChain(array $currentChild, array &$children, array &$chainedChildren)
    {
        $beforeChildren = $this->searchBeforeChildren($currentChild, $children);
        if ($beforeChildren) {
            // On recupère tous les enfants avant l'enfant courant
            $this->insertBeforeChildren($currentChild['build_obj'], $chainedChildren, $beforeChildren);
        }

        $afterChildren = $this->searchAfterChildren($currentChild, $children);
        if ($afterChildren) {
            // On recupère tous les enfants après l'enfant courant
            $this->insertAfterChildren($currentChild['build_obj'], $chainedChildren, $afterChildren);
        }
    }

    /**
     * Ajoute les enfants dynamiques au tableau des enfants fixes
     *
     * @param array $children
     * @param array $dynamicChildren
     * @return array
     */
    private function addDynamicChildren(array $children, array $dynamicChildren)
    {
        if (empty($children)) {
            $children = $this->orderChilren($dynamicChildren);
        }

        $childrenWithDynamic = $children;
        foreach ($children as $child) {
            $this->makeChain($child, $dynamicChildren, $childrenWithDynamic);
        }

        // Il nous reste des cadres dynamiques, on essaie de les inserer
        if ($dynamicChildren) {
            $child = current($dynamicChildren);
            do {
                $chainedChildren = [$child];
                // On essaie de faire un chainage avec les enfants qui nous reste
                $this->makeChain($child, $dynamicChildren, $chainedChildren);
                //Une fois le chainage fait on les inserts
                $this->tryInsertChildren($chainedChildren, $childrenWithDynamic);
            } while ($child = next($dynamicChildren));
        }

        return $childrenWithDynamic;
    }
}
