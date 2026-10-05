<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PageFactory.php,v 1.3 2022/02/22 15:32:29 jparis Exp $
namespace Pmb\CMS\Factories;

use Pmb\CMS\Models\PageFRBRModel;
use Pmb\CMS\Models\PagePortalModel;
use Pmb\Common\Helper\Portal as PortalHelper;

class PageFactory
{

    /**
     * Retourne l'instance de la page courrante selon une liste de page
     *
     * @param PagePortalModel|PageFRBRModel[] $pages
     * @return PagePortalModel|PageFRBRModel|NULL
     */
    public static function getCurrentPage(array $pages)
    {
        $type = PortalHelper::getTypePage();
        $subType = PortalHelper::getSubTypePage();
        return static::getMatchPage($pages, $type, $subType);
    }

    /**
     * Retourne la page qui matche
     *
     * @param PagePortalModel|PageFRBRModel[] $pages
     * @param string $type
     * @param string $subType
     * @return PagePortalModel|PageFRBRModel|NULL
     */
    private static function getMatchPage(array $pages, string $type, string $subType = "")
    {
        $pagesMatches = [];
        foreach ($pages as $page) {
            if ($page->type == $type && $page->subType == $subType) {
                $pagesMatches[] = $page;
            }
        }

        if (! empty($pagesMatches)) {
            $page = static::pageMeetsRequirements($pagesMatches);
            if (! is_null($page)) {
                return $page;
            }
        }
        return null;
    }

    /**
     * Retourne la première page qui remplis toutes les conditions
     *
     * @param array $pagesMatches
     * @return NULL|PagePortalModel|PageFRBRModel
     */
    private static function pageMeetsRequirements($pagesMatches)
    {
        $page = null;

        /**
         * On tris les pages.
         * Elle qui a le plus de conditions à elle qui en a le moins
         */
        usort($pagesMatches, function ($pageA, $pageB) {
            $indexA = count($pageA->getConditions());
            $indexB = count($pageB->getConditions());

            if ($indexA == $indexB) {
                return 0;
            }
            return ($indexA > $indexB) ? - 1 : 1;
        });

        $index = count($pagesMatches);
        for ($i = 0; $i < $index; $i ++) {
            $valid = true;
            $pageModel = $pagesMatches[$i];
            foreach ($pageModel->getConditions() as $condition) {
                if (! empty($condition) && ! $condition->check()) {
                    $valid = false;
                    break;
                }
            }

            if ($valid) {
                $page = $pageModel;
                break;
            }
        }

        return $page;
    }
}