<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DsiApiController.php,v 1.9 2023/11/28 11:29:13 rtigero Exp $
namespace Pmb\DSI\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Models\Channel\Portal\PortalChannel;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\DSIParserDirectory;
use Pmb\DSI\Models\Group\RootGroup;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Helper\SubscriberHelper;
use Pmb\DSI\Helper\LookupHelper;

class DsiApiController extends Controller
{
    public function cmsPages()
    {
        $cmsPages = new \cms_pages();
        $pages = array_map(function ($page) {
            return new \cms_page($page);
        }, $cmsPages->list ?? []);

        $result = [];
        foreach ($pages as $page) {
            $result[] = [
                'value' => $page->id,
                'label' => $page->name,
                'vars' => array_map(function ($var) {
                    return [
                        'value' => $var['id'],
                        'label' => "{$var['name']} - {$var['comment']}",
                    ];
                }, $page->vars ?? [])
            ];
        }

        $this->ajaxJsonResponse($result);
    }

    /**
     * Permet de recupere des donnees pour le formulaire
     *
     * @param integer $type
     * @param integer|null $id
     * @return void
     */
    public function getGroupAdditionnalData(int $type)
    {
        $namespace = array_search($type, RootGroup::IDS_TYPE, true);
        if ($namespace === false) {
            $namespace = RootGroup::class;
        }

        $modelView = new $namespace();
        $this->ajaxJsonResponse($modelView->getFormData());
    }

    /**
     * Retourne la liste des groupes disponibles selon le type d'item/view
     *
     * @param int $viewType
     * @param int $itemType
     * @return void
     */
    public function getGroupCompatibility($viewType, $itemType)
    {
        $viewType = intval($viewType);
        $itemType = intval($itemType);
        if (empty($itemType) || empty($viewType)) {
            $this->ajaxJsonResponse([]);
        }

        $itemNamespace = HelperEntities::get_item_from_type($itemType);
        $viewNamespace = array_search($viewType, RootView::IDS_TYPE, true);
        if (empty($itemNamespace) || empty($viewNamespace)) {
            $this->ajaxJsonResponse([]);
        }

        $itemCompatibility = DSIParserDirectory::getInstance()->getCompatibility($itemNamespace);
        $viewCompatibility = DSIParserDirectory::getInstance()->getCompatibility($viewNamespace);
        $groupCompatibility = array_filter(
            $itemCompatibility['group'] ?? [],
            function ($groupNamespace) use ($viewCompatibility) {
            return in_array($groupNamespace, $viewCompatibility['group'] ?? [], true);
        });

        $groups = array();
        if (!empty($groupCompatibility)) {
            foreach ($groupCompatibility as $groupNamespace) {
                $message = $groupNamespace::getMessages();
                $groups[] = [
                    "id" => RootGroup::IDS_TYPE[$groupNamespace] ?? null,
                    "component" => $groupNamespace::COMPONENT,
                    "name" => $message['name']
                ];
            }
        }

        $this->ajaxJsonResponse($groups);
    }

    /**
     * Retourne la liste des patterns pour les templates
     *
     * @return void
     */
    public function patterns()
    {
        global $msg;
        $response = [
            $msg['label_subscriber_pattern'] => SubscriberHelper::getPatternList(),
            $msg['label_diffusion_pattern'] => LookupHelper::getPatternList(),
            "diffusions" => [
            	"!!portal_diffusion_link!!" => $msg["portal_diffusion_link_desc"]
            ]
        ];

        $portalDiffusions = array();
        $diffusionModel = new Diffusion();
        $diffusions = $diffusionModel->getList();
        
        foreach($diffusions as $diffusion) {
            $diffusion->fetchChannel();
            if($diffusion->channel instanceof PortalChannel) {
                $portalDiffusions[] = [
                    "label" => $diffusion->name,
                    "value" => $diffusion->idDiffusion
                ];
            }
        }
        if(count($portalDiffusions)) {
            $response["diffusions"] = [
                "!!portal_diffusion_link!!" => $msg["portal_diffusion_link_desc"]
            ];
        }
        $response["dynamicGroups"] = array();
        $response["dynamicGroups"]["!!portal_diffusion_link!!"] = [
            "label" => $msg["portal_diffusion_link_label"],
            "type" => "select",
            "options" => $portalDiffusions,
            "defaultOption" => $msg["portal_diffusion_link_default"],
        ];
        $this->ajaxJsonResponse($response);
    }
}
