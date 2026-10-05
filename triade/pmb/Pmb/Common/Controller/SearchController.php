<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchController.php,v 1.6.10.1 2025/06/04 12:39:33 qvarin Exp $

namespace Pmb\Common\Controller;

use Pmb\Animations\Models\AnimationSearchModel;

class SearchController
{
    public function proceed($action = "", $data = [])
    {
        switch ($action) {
            case "search":
                return $this->searchAction($data);
            default:
                throw new \Exception("action required");
        }
    }

    public function searchAction(array $data)
    {
        switch ($data['what']) {
            case 'animations':
                $searchModel = new AnimationSearchModel();

                if (isset($data['filter'])) {
                    $searchModel->setFilter($data['filter']);
                }
                if (isset($data['page'])) {
                    $searchModel->setPage($data['page']);
                }

                return $searchModel->makeSearch($data['globalsSearch'], $data['labelId'], 'search_fields_animations');
            default:
                throw new \Exception("what required");
        }
    }
}