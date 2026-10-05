<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ViewsController.php,v 1.43.2.1.2.2 2025/05/21 12:31:40 rtigero Exp $

namespace Pmb\DSI\Controller;

use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Models\View\CustomizableView;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Models\DSIParserDirectory;
use Pmb\DSI\Models\Item\RootItem;
use Pmb\DSI\Orm\ViewOrm;

class ViewsController extends CommonController
{
    protected const VUE_NAME = "dsi/views";

    /**
     *
     * {@inheritDoc}
     * @see \Pmb\DSI\Controller\CommonController::getBreadcrumb()
     */
    protected function getBreadcrumb()
    {
        global $msg, $action, $id;
        $breadcrum = "{$msg['dsi_menu']} {$msg['menu_separator']} {$msg['dsi_views']}";

        if (isset($action) && $action == "edit" && isset($id)) {
            if (ViewOrm::exist($id)) {
                $view = ViewOrm::findById($id);
                $breadcrum .= " {$msg['menu_separator']} {$view->name}";
            }
        }

        return $breadcrum;
    }

    protected function defaultAction()
    {
        $view = RootView::getInstance();
        print $this->render([
            "list" => $view->getList(["model" => "1"]),
            "types" => $this->getTypeList(),
        ]);
    }

    /**
     * Ajout diffusion
     */
    protected function addAction()
    {
        print $this->render($this->getFormData());
    }

    /**
     * Edition diffusion
     */
    protected function editAction()
    {
        global $id;
        $id = intval($id);

        print $this->render($this->getFormData($id));
    }

    public function save()
    {
        $this->data->id = intval($this->data->id);
        $view = RootView::getInstance($this->data->id);
        $result = $view->check($this->data);
        if ($result['error']) {
            $this->ajaxError($result['errorMessage']);
            exit();
        }
        $view->setFromForm($this->data);

        if (0 == $this->data->id) {
            $view->create();
        } else {
            $view->update();
        }
        $view->saveChilds();

        $this->ajaxJsonResponse($view);
        exit();
    }

    public function delete()
    {
        $view = RootView::getInstance($this->data->id);
        $view->deleteChilds();

        $result = $view->delete();
        if ($result['error']) {
            $this->ajaxJsonResponse($result);
            exit();
        }
        $this->ajaxJsonResponse([
            'success' => true,
        ]);
        exit();
    }

    /**
     * Recuperation donnees formulaire ajout/edition
     *
     * @param number $id
     * @return array[]
     */
    protected function getFormData($id = 0)
    {
        $data = [];
        $data["view"] = new RootView($id);
        $data["types"] = $this->getTypeList();
        $data["entities"] = HelperEntities::get_entities_labels();
        return $data;
    }

    public function getTypeList()
    {
        $viewTypeList = [];
        $manifests = DSIParserDirectory::getInstance()->getManifests("Pmb/DSI/Models/View/");
        foreach ($manifests as $manifest) {
            $message = $manifest->namespace::getMessages();
            $viewTypeList[] = [
                "id" => RootView::IDS_TYPE[$manifest->namespace] ?? 0,
                "namespace" => $manifest->namespace,
                "name" => $message['name'] ?? $manifest->namespace,
                "compatibility" => $manifest->compatibility,
                "levels" => $manifest->levels,
                "limitable" => isset($manifest->limitable) && $manifest->limitable == "true" ? true : false,
                "previewable" => isset($manifest->previewable) && $manifest->previewable == "true" ? true : false,
                "customizable" => isset($manifest->customizable) && $manifest->customizable == "true" ? true : false,
                "default_model_image" => isset($manifest->defaultModelImage) ? $manifest->defaultModelImage : "",
            ];
        }

        return $viewTypeList;
    }

    public function getTypeListAjax()
    {
        $this->ajaxJsonResponse($this->getTypeList());
    }

    public function getEntitiesDefaultTemplates($stripTags = 0)
    {
        $data = HelperEntities::get_entities_default_templates();
        if ($stripTags) {
            $data = array_map("strip_tags", $data);
        }
        $this->ajaxJsonResponse(array_map("trim", $data));
    }

    public function getEntityTree($type = 0)
    {
        $parseDirectory = DSIParserDirectory::getInstance();
        $manifests = $parseDirectory->getManifests(__DIR__ . "/../Models/Item/Entities");
        //On considère que si le type est vide, on recupere tous les arbres non liés à la donnée dynamique
        if ($type == 0) {
            $itemModel = new RootItem();
            $this->ajaxJsonResponse($itemModel->getTree());
            exit;
        }
        if (empty($manifests)) {
            $this->ajaxJsonResponse([]);
        }

        $manifestFinds = array_filter($manifests, function ($manifest) use ($type) {
            return $manifest->namespace::TYPE == $type;
        });
        if (empty($manifestFinds)) {
            $this->ajaxJsonResponse([]);
        }

        $manifest = current($manifestFinds);
        if (empty($manifest)) {
            $this->ajaxJsonResponse([]);
        }

        $item = new $manifest->namespace();
        $this->ajaxJsonResponse($item->getTree());
    }

    public function getCustomizableFieldTree()
    {
        if ($this->data->fields) {
            $customizableView = new CustomizableView();
            $structureData = $customizableView->getDefaultStructureData();

            foreach ($this->data->fields as $field) {
                $method = "getStructureData" . ucfirst($field->type);

                if (method_exists($customizableView, $method)) {
                    $structureData["children"][] = $customizableView->$method($field);
                }
            }

            $this->ajaxJsonResponse($structureData);
        }
    }

    public function getTemplateDirectories(int $viewType, int $entityType = 0)
    {
        $namespace = array_search($viewType, RootView::IDS_TYPE, true);
        if ($namespace === false) {
            $namespace = RootView::class;
        }

        $modelView = new $namespace();
        $this->ajaxJsonResponse($modelView->getTemplateDirectories($entityType));
    }

    public function getModels()
    {
        return $this->ajaxJsonResponse($this->fetchModels());
    }

    protected function fetchModels()
    {
        return (new RootView())->getList(["model" => "1"]);
    }

    public function getModel($idModel)
    {
        return $this->ajaxJsonResponse(RootView::getInstance($idModel));
    }

    public function getCompatibility($type)
    {
        $result = [];
        $typeNamespace = array_search($type, RootView::IDS_TYPE);
        $compatibility = DSIParserDirectory::getInstance()->getCompatibility($typeNamespace);
        if (isset($compatibility['item'])) {
            foreach ($compatibility['item'] as $item) {
                $result[] = $item::TYPE;
            }
        }
        return $this->ajaxJsonResponse($result);
    }

    /**
     * relie un tag a l'entite
     * @return array
     */
    public function unlinkTag()
    {
        $view = RootView::getInstance();
        $delete = $view->unlinkTag($this->data->numTag, $this->data->numEntity);
        return $this->ajaxJsonResponse($delete);
    }

    /**
     * Supprime le lien entre un tag et une entite
     * @return array
     */
    public function linkTag()
    {
        $view = RootView::getInstance();
        $link = $view->linkTag($this->data->numTag, $this->data->numEntity);
        return $this->ajaxJsonResponse($link);
    }

    /**
     * Retourne en AJAX la liste de toutes les vues
     */
    public function getViews()
    {
        $this->ajaxJsonResponse(RootView::getInstance()->getList());
    }

    /**
     * Retourne en AJAX le rendu de la vue
     */
    public function renderView(int $idView, int $idItem, int $idEntity, int $limit, string $context)
    {
        $view = RootView::getInstance($idView);
        $item = RootItem::getInstance($idItem);

        $this->ajaxJsonResponse($view->render($item, $idEntity, $limit, $context));
    }

    /**
     * Retourne en AJAX la prévisualisation de la vue
     */
    public function previewView(int $idView, int $idItem = 0, int $idEntity = 0, int $limit = 0, string $context = "model")
    {
        $view = RootView::getInstance($idView);
        $item = RootItem::getInstance($idItem);

        if ($context == "model") {
            $this->ajaxResponse($view->preview($item, $idEntity, $limit, $context), "text/html");
            exit;
        }

        $this->ajaxJsonResponse($view->preview($item, $idEntity, $limit, $context));
    }

    /**
     * Retourne en AJAX un instance vide d'un RootView
     */
    public function getEmptyInstance()
    {
        $this->ajaxJsonResponse(RootView::getInstance());
    }

    /**
     * Retourne en AJAX un instance d'un RootView
     */
    public function getInstance(int $id)
    {
        $this->ajaxJsonResponse(RootView::getInstance($id));
    }

    public function duplicate()
    {
        if ($this->data->id != 0) {
            $viewToDuplicate = RootView::getInstance($this->data->id);
            $newView = $viewToDuplicate->duplicate();
            $this->ajaxJsonResponse($newView);
        }
    }

    /**
     * Permet de recupere des donnees pour le formulaire
     *
     * @param integer $type
     * @param integer|null $id
     * @return void
     */
    public function getAdditionnalData(int $type, ?int $id = 0)
    {
        $namespace = array_search($type, RootView::IDS_TYPE, true);
        if ($namespace === false) {
            $namespace = RootView::class;
        }

        $modelView = new $namespace($id);
        $this->ajaxJsonResponse($modelView->getFormData());
    }
    public function deleteAll()
    {
        foreach ($this->data->ids as $id) {
            $view = RootView::getInstance($id);
            $result = $view->delete();
            if ($result["error"]) {
                $this->ajaxError($result['errorMessage']);
            }
        }
        $this->ajaxJsonResponse(['success' => true]);
    }

    public function exportModel($idModel = 0)
    {
        $idModel = intval($idModel);

        if ($idModel != 0) {
            $modelToExport = RootView::getInstance($idModel);
            $modelToExport->export();
        }
    }

    public function importModel()
    {
        global $msg;

        if (isset($this->data->file) && !empty($this->data->file)) {
            $model = @unserialize($this->data->file);

            if ($model && $model instanceof RootView) {
                $model->create();
                $this->ajaxJsonResponse($model);
            }
        }

        $this->ajaxError($msg["dsi_model_import_error"]);
    }

    public function importModelTags()
    {
        $view = RootView::getInstance($this->data->numEntity);
        $view->importModelTags();
        $this->ajaxJsonResponse($view->tags);
    }

    public function getLevels()
    {
        $this->ajaxJsonResponse(RootView::getInstance()->getLevels());
    }

    public function createModelFromDiffusion()
    {
        $this->data->id = intval($this->data->id);
        $view = RootView::getInstance($this->data->id);

        $result = $view->check($this->data);
        if ($result['error']) {
            $this->ajaxError($result['errorMessage']);
            exit();
        }

        $view->setFromForm($this->data);

        // On reset les childs sinon on récupère des stdclass avec le setFromForm
        $view->childs = [];
        $view->fetchChilds();

        $viewName = $view->name;

        $newView = $view->duplicate(null, false);
        if ($newView) {

            // On remet le nom de la vue
            $newView->name = $viewName;

            // On force la création d'un modèle
            $newView->model = true;
            $newView->update();
        }

        $this->ajaxJsonResponse($newView);
        exit();
    }
}
