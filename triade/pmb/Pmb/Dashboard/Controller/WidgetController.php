<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: WidgetController.php,v 1.10 2024/03/04 15:15:43 jparis Exp $
namespace Pmb\Dashboard\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Dashboard\Models\WidgetModel;

class WidgetController extends Controller
{
    
    /**
     * Sauvegarde les données et renvoie une réponse JSON.
     * 
     * @return void
     */
    public function save() {
        global $PMBuserid;

        $this->data->idWidget = intval($this->data->idWidget);

        $widget = new WidgetModel($this->data->idWidget);

        if(!$this->data->idWidget) {
            $this->data->numUser = $PMBuserid;
        }

        $check = $widget->check($this->data);
        if ($check['error']) {
            $this->ajaxError($check['errorMessage']);
            exit();
        }

        $widget->setFromForm($this->data);

        if($widget->idWidget) {
            $widget->update();
        } else {
            $widget->create();
        }

        $this->ajaxJsonResponse($widget);
    }

    /**
     * Supprime un widget de la base de données.
     * 
     * @return void
     */
    public function delete() {
        $this->data->idWidget = intval($this->data->idWidget);
        
        $widget = new WidgetModel($this->data->idWidget);
        $result = $widget->delete();

        if ($result['error']) {
            $this->ajaxError($result['errorMessage']);
            exit();
        }
        $this->ajaxJsonResponse([
			'success' => true
        ]);
    }

    /**
     * Duplique un widget de la base de données.
     *
     * @return void
     */
    public function duplicate()
    {
        global $PMBuserid;

        $this->data->idWidget = intval($this->data->idWidget);

        $widget = new WidgetModel($this->data->idWidget);
        $widget->idWidget = 0;
        $widget->numUser = intval($PMBuserid);
        $widget->widgetName = "Copy of " . $widget->widgetName;

        $widget->create();

        $this->ajaxJsonResponse([
            'success' => true
        ]);
    }

    /**
     * Récupère la liste des widgets
     *
     * @return void
     */
    public function getList()
    {
        $widget = new WidgetModel();
        $this->ajaxJsonResponse($widget->getListByCurrentUserId());
    }

    public function getConfiguration() {
        if(!empty($this->data->source)) {
            if(class_exists($this->data->source) && method_exists($this->data->source, 'getConfiguration')) {
                $source = new $this->data->source();
                $this->ajaxJsonResponse($source->getConfiguration());
                exit();
            }
        }

        $this->ajaxError("error");
    }

    public function getData() {
        if(!empty($this->data->source)) {
            if(class_exists($this->data->source) && method_exists($this->data->source, 'getData')) {
                $params = $this->data->params ?? [];
    
                $source = new $this->data->source();
                $this->ajaxJsonResponse($source->getData($params));
                exit();
            }
        }

        $this->ajaxError("error");
    }

    public function updateData() {
        global $PMBuserid;

        $this->data->idWidget = intval($this->data->idWidget);
        $widget = new WidgetModel($this->data->idWidget);

        // Si l'utilisateur peut modifier le widget
        if ($widget->widgetEditable || $widget->numUser == $PMBuserid) {
            if(!empty($this->data->source)) {
                if(class_exists($this->data->source) && method_exists($this->data->source, 'updateData')) {
                    $source = new $this->data->source();

                    if($source->updateData($this->data->params)) {
                        $this->ajaxJsonResponse([
                            'success' => true
                        ]);
                        exit();
                    }
                }
            }
        }

        $this->ajaxError("error");
    }

    public function getConditions() {
        if(!empty($this->data->source)) {
            if(class_exists($this->data->source) && method_exists($this->data->source, 'getConditions')) {
                $params = $this->data->params ?? [];

                $source = new $this->data->source();
                $this->ajaxJsonResponse($source->getConditions($params));
                exit();
            }
        }

        $this->ajaxError("error");
    }
}
