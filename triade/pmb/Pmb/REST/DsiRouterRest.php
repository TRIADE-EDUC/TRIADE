<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DsiRouterRest.php,v 1.76.2.3.2.2 2025/05/26 12:13:32 rtigero Exp $

namespace Pmb\REST;

use Pmb\Common\Helper\Helper;

class DsiRouterRest extends RouterRest
{
    protected const CONTROLLER = "\\Pmb\\DSI\\Controller\\DsiApiController";

    protected function generateRoutes()
    {
        // Diffusion
        $this->post('{controller}/save', 'save');
        $this->post('{controller}/delete', 'delete');
        $this->post('{controller}/deleteProductDiffusion', 'deleteProductDiffusion');
        $this->post('{controller}/deleteEventDiffusion', 'deleteEventDiffusion');
        $this->post('{controller}/deleteEventProduct', 'deleteEventProduct');
        $this->post('{controller}/search', 'search');
        $this->post('{controller}/addSubscriber/{idSubscriberList}', 'addSubscriber');
        $this->post('{controller}/removeSubscriberFromList/{idSubscriberList}', 'removeSubscriberFromList');
        $this->post('{controller}/{entityType}/importSubscribers/{entityId}', 'importSubscribers');
        $this->post('{controller}/getSubscribersFromList', 'getSubscribersFromList');
        $this->post('{controller}/unlinkTag', 'unlinkTag');
        $this->post('{controller}/linkTag', 'linkTag');
        $this->post('{controller}/duplicate', 'duplicate');
        $this->post('{controller}/createModelFromDiffusion', 'createModelFromDiffusion');

        $this->get('{controller}/getSourceList/{id_type}', 'getSourceList')->with("id_type", static::LIMIT_NUMBER);
        $this->get('{controller}/getSelectorList/{namespace}', 'getSelectorList');
        $this->get('{controller}/getMailList/', 'getMailList');
        $this->get('{controller}/getEntitiesDefaultTemplates/{stripTags}', 'getEntitiesDefaultTemplates');
        $this->get('{controller}/getEntitiesDefaultTemplates', 'getEntitiesDefaultTemplates');
        $this->get('{controller}/getEntityTree/{id_type}', 'getEntityTree');
        $this->post('{controller}/getCustomizableFieldTree', 'getCustomizableFieldTree');
        $this->get('{controller}/getTemplateDirectories/{viewType}/{entityType}', 'getTemplateDirectories');
        $this->get('{controller}/getSources/{type}', 'getSources');
        $this->get('{controller}/getSelectors/{type}', 'getSelectors');
        $this->get('{controller}/haveSubSelector/{namespace}', 'haveSubSelector');
        $this->get('{controller}/getCompatibility/{type}', 'getCompatibility');

        $this->get('{controller}/getEntityList/', 'getEntityList');
        $this->get('{controller}/getTypeListAjax/', 'getTypeListAjax');

        $this->get('{controller}/getEntity/{idEntity}', 'getEntity');
        $this->get('{controller}/getEntity', 'getEntity');
        $this->get('{controller}/getTypes/', 'getTypes');
        $this->get('{controller}/getModels/', 'getModels');
        $this->get('{controller}/getItems/', 'getItems');
        $this->get('{controller}/getViews/', 'getViews');

        $this->get('{controller}/getModel/{idModel}', 'getModel');
        $this->get('{controller}/preview/{idEntity}', 'previewView');
        $this->get('{controller}/preview/{idEntity}/{selectedAttachment}', 'previewView');
        $this->get('{controller}/render/{idEntity}', 'renderView');
        $this->get('{controller}/render/{idView}/{idItem}/{idEntity}/{limit}/{context}', 'renderView');

        $this->get('{controller}/availableFilters/{idItem}', 'availableFilters');

        $this->get('{controller}/getEmptyInstance/', 'getEmptyInstance');
        $this->get('{controller}/getInstance/{id}', 'getInstance');
        $this->get('{controller}/requirements/{type}', 'getRequirements');
        $this->get('{controller}/requirements', 'getRequirements');
        $this->get('{controller}/filterSubscribers/{idSubscriberList}/{channelType}', 'filterSubscribers');
        $this->get('{controller}/tags', 'getTags');
        $this->get('{controller}/getRelatedEntities/{numTag}', 'getRelatedEntities');
        $this->get('{controller}/{entityType}/getEntity', 'getEntity');
        $this->get('{controller}/subscribers/{idSubscriberList}', 'getSubscribers');
        $this->post('{controller}/empty', 'empty');
        $this->post('{controller}/{entityType}/{idEntity}/save', 'save');
        $this->post('{controller}/{entityType}/delete', 'delete');
        $this->post('{controller}/{entityType}/unsubscribe/{idEntity}', 'unsubscribe');
        $this->post('{controller}/{entityType}/subscribe/{idEntity}', 'subscribe');
        $this->post('{controller}/updateLockedLists', 'updateLockedListsFromModel');
        $this->post('{controller}/updateHistoryState/{idState}/{idEntity}', 'updateHistoryState');

        $this->get('{controller}/getSectionList', 'getSectionList');
        $this->get('{controller}/getWatchList', 'getWatchList');
        $this->get('{controller}/portal/url/{idDiffusion}/{idHistory}', 'getPortalChannelOpacURL');

        $this->get('{controller}/humhub/containers/{idChannel}', 'getHumHubContainers');
        $this->get('{controller}/getAllDiffusions', 'getAllDiffusions');
        $this->post('{controller}/saveContentBuffer/{idHistory}/{contentType}', 'saveContentBuffer');
        $this->post('{controller}/resetContentBuffer/{idHistory}/{contentType}', 'resetContentBuffer');

        $this->post('{controller}/getItemsListLabel', 'getItemsListLabel');
        $this->post('{controller}/getItemsFromList', 'getItemsFromList');
        $this->get('{controller}/contentHistoryTypes', 'getContentHistoryTypes');

        $this->get('{controller}/contentBuffer/{id}', 'getContentBuffer');

        $this->post('{controller}/deleteAll', 'deleteAll');

        $this->get('{controller}/form/data/{type}/{idModel}', 'getAdditionnalData');
        $this->get('{controller}/{id}/send/{idHistory}', 'send');

        $this->get('cms/pages', 'cmsPages');
        $this->get('input/patterns', 'patterns');
        $this->get('group/form/data/{type}', 'getGroupAdditionnalData');
        $this->get('group/compatibility/{viewType}/{itemType}', 'getGroupCompatibility');
        $this->get('{controller}/messages/{moduleName}', 'getAdditionnalMessages');
        $this->get('{controller}/filters/options/{filterNamespace}', 'getFilterOptions');

        $this->get('{controller}/items/{idDiffusion}', 'getDiffusionItems');
        $this->get('{controller}/export/{id}', 'exportModel');
        $this->post('{controller}/import', 'importModel');
        $this->get('{controller}/getSelectorSorts/{sortNamespace}', 'getSelectorSorts');
        $this->get('{controller}/record/caddies', 'getRecordCaddies');

        $this->post('{controller}/importModelTags', 'importModelTags');
        $this->get('{controller}/getItemEntityTree/{idItem}', 'getItemEntityTree');

        $this->get('{controller}/getlevels', 'getLevels');
        $this->post('{controller}/addPortalDiffusion', 'addPortalDiffusion');

        $this->get('{controller}/getDataInProgressDiffusion/{id}', 'getDataInProgressDiffusion');
        $this->get('{controller}/getDataInProgressAllDiffusions', 'getDataInProgressAllDiffusions');

        $this->get('{controller}/getParameters', 'getParameters');
        $this->post('{controller}/upload', 'upload');
    }

    /**
     *
     * @param RouteRest $route
     * @return mixed
     */
    protected function call(RouteRest $route)
    {
        global $data;
        $data = \encoding_normalize::json_decode(stripslashes($data ?? ''));
        if (empty($data) || !is_object($data)) {
            $data = new \stdClass();
        }

        $args = $route->getArguments();
        $className = $this->foundController($route);
        if (false === $className) {
            $className = static::CONTROLLER;
        } elseif (count($args) > 0) {
            array_splice($args, 0, 1);
        }

        $callback = [
            new $className($data),
            $route->getMethod(),
        ];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $args);
        }
    }

    private function foundController(RouteRest $route)
    {
        $args = $route->getArguments();
        $controller = $args[0] ?? "";

        $namespace = "Pmb\\DSI\\Controller\\" . Helper::pascalize("{$controller}_controller");
        if (class_exists($namespace)) {
            return $namespace;
        }
        return false;
    }
}
